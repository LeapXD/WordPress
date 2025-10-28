/**
 * External dependencies
 */
import classnames from 'classnames';

/**
* WordPress dependencies
*/
import { __ } from '@wordpress/i18n';
import {
  AlignmentControl,
  BlockControls,
  RichText,
  useBlockProps,
  store as blockEditorStore,
} from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';
import { store as coreStore } from '@wordpress/core-data';
import { createBlock, getBlockType, createBlocksFromInnerBlocksTemplate } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';
import { addFilter } from '@wordpress/hooks';
import themeVersionCompare from '../utils/theme-version-compare';

function ParagraphBlock({
  attributes,
  mergeBlocks,
  onReplace,
  onRemove,
  setAttributes,
  clientId,
}) {
  const {
    align, content, direction, dropCap, placeholder,
  } = attributes;
  const {
    replaceBlock, selectBlock, insertBlock, removeBlock, moveBlocksToPosition,
  } = useDispatch(blockEditorStore);
  const {
    getBlockParents, getBlock, getBlocks, getSelectionEnd, getSelectionStart,
  } = useSelect(blockEditorStore);
  const { getEditorSettings } = useSelect(editorStore);
  const { createUndoLevel } = useDispatch(editorStore);
  const blockProps = useBlockProps({
    className: classnames({
      'has-drop-cap': dropCap,
      [`has-text-align-${align}`]: align,
    }),
    style: { direction },
  });

  return (
    <>
      <BlockControls group="block">
        <AlignmentControl
          value={align}
          onChange={(newAlign) => setAttributes({ align: newAlign })}
        />
      </BlockControls>
      <RichText
        /* Custom onChange handler.
        Wraps root level paragraphs in container */
        {...blockProps}
        onChange={(newContent) => {
          const newParagraph = createBlock('core/paragraph', { content: newContent });
          const { clientId: newParagraphId } = newParagraph;
          // No parent ? Block probably at root level.
          if (!getBlockParents(clientId).length) {
            // avoid wrapping if user is using quick inserter (/ keystroke)
            if (!newContent || newContent.substr(0, 1) === '/') {
              return;
            }

            const { allowedBlockTypes } = getEditorSettings();

            // make sure there's a container available
            if (!Array.isArray(allowedBlockTypes) || !allowedBlockTypes.find((name) => name === 'xd/container')) {
              setAttributes({ content: newContent });
              return;
            }

            // this action needs to be deferred so that editor doesn't
            // complain about the block getting replaced during a render cycle
            new Promise((resolve) => { resolve(); }).then(() => {
              // create a new paragraph, wrap in container,
              // and select it with caret at end
              // setNewContent(newContent);
              const container = createBlock('xd/container', {}, [newParagraph]);
              replaceBlock(clientId, container);
              selectBlock(newParagraphId, -1);
            });
          }
          setAttributes({ content: newContent });
        }}
        /* Custom onReplace handler
        wraps pasted blocks not allowed at root level in container */
        onReplace={(newBlocks, indexToSelect, initialPosition) => {
          function wrapBlocksInContainer() {
            // Make clones of the new blocks, wrap them in container,
            // get the clientId of the last block, and select it
            // with caret at end.

            const replacementBlocks = createBlocksFromInnerBlocksTemplate(newBlocks);
            const container = createBlock('xd/container', {}, replacementBlocks);
            const { clientId: selectionClientId } = container.innerBlocks.slice(-1);
            onReplace([container]);
            selectBlock(selectionClientId, -1);
          }

          const parentBlocks = getBlockParents(clientId);
          const [parentClientId] = parentBlocks.slice(-1);
          const parentBlock = getBlock(parentClientId);
          const { allowedBlockTypes } = getEditorSettings();

          // if newBlocks is an array of arrays, it's a multi-block selection
          // if (Array.isArray(newBlocks) && newBlocks.length === 1 && Array.isArray(newBlocks[0])) {
          //   // flatten the array
          //   [newBlocks] = newBlocks;
          // }

          // make sure there's a container available
          if (Array.isArray(allowedBlockTypes) && allowedBlockTypes.find((name) => name === 'xd/container')) {
            // No parent ? Block probably at root level.
            if (!parentBlock) {
              // If there is a paragraph in the new blocks, wrap them all in container
              if (newBlocks.find(({ name }) => name === 'core/paragraph')) {
                const emptyParagraphsCheck = newBlocks.filter(({ name }) => name === 'core/paragraph');
                if (emptyParagraphsCheck.length === newBlocks.length && !newBlocks.filter(({ attributes }) => attributes.content?.text).length) {
                  return;
                }
                wrapBlocksInContainer();

                return;
              }
              // allowedBlockTypes are the globally allowed blocks
              // (the ones we set in block-config in theme)
              // rootAllowedBlocks are the allowedBlockTypes that have no
              // parents defined (in block-settings in theme)
              const rootAllowedBlocks = allowedBlockTypes
                .map((name) => getBlockType(name))
                .filter((blockType) => blockType)
                .filter(({ parent }) => !parent || parent?.length === 0)
                .map(({ name }) => name);

              // If any of the new blocks are not allowed at root,
              // wrap in container
              if (newBlocks.filter(({ name }) => rootAllowedBlocks
                .includes(name)).length !== newBlocks.length) {
                wrapBlocksInContainer();
                return;
              }
            }
          }

          if (parentBlock) {
            // when there is no grandParentClientId, get/insertBlocks(null) will do so at the root level
            const [grandParentClientId] = parentBlocks.length > 1 ? parentBlocks.slice(-2) : [];
            const parentSiblingBlocks = getBlocks(grandParentClientId);
            const siblingBlocks = getBlocks(parentClientId);
            const parentIndex = parentSiblingBlocks.findIndex(({ clientId }) => clientId === parentClientId);
            const insertionPoint = siblingBlocks.findIndex(({ clientId: id }) => id === clientId);
            const insertAfter = newBlocks[0].clientId === clientId;
            const { offset: selectionStart } = getSelectionStart();
            const { offset: selectionEnd } = getSelectionEnd();

            // if there are two empty paragraphs together (carriage return was hit twice), split the parent
            // and move the blocks from the split point into a new parent of the same type
            if (selectionStart === selectionEnd
             && newBlocks.filter(({ name }) => name === 'core/paragraph').length === 2
             && insertAfter && newBlocks[0].attributes.content?.text === ''
            ) {
              if (siblingBlocks.length > 1) {
                removeBlock(newBlocks[0].clientId);
                removeBlock(newBlocks[1].clientId);
              }
              const newParent = createBlock(
                parentBlock.name,
                // parentBlock.attributes,
                {},
                [newBlocks[1]],
              );
              if (insertAfter) {
                // createUndoLevel();
                insertBlock(newParent, parentIndex + 1, grandParentClientId)
                  .then(() => new Promise((resolve) => { resolve(); }))
                  .then(() => moveBlocksToPosition(siblingBlocks.slice(insertionPoint + 1)
                    .map(({ clientId }) => clientId), parentClientId, newParent.clientId, 0))
                  // .then(() => removeBlock(newParent.innerBlocks[0]?.clientId, true))
                  .then(() => selectBlock(getBlock(newParent.clientId).innerBlocks[0]?.clientId));
              }
              return;
            }
          }

          // inserted blocks are all allowed at root or we're not at root
          onReplace(newBlocks, indexToSelect, initialPosition);
        }}
        identifier="content"
        tagName="p"
        value={content}
        onKeyUp={(event) => {
          return;
          if (event.key === 'Enter') {
            // if block is empty and and the previous block is empty and there is  a parent block, clone the parent block and insert the new paragraph into the new clone and insert the clone after the parent block
            const parentBlocks = getBlockParents(clientId);
            const [parentClientId] = parentBlocks.slice(-1);
            const parentBlock = getBlock(parentClientId);
            if (parentBlock) {
              // when there is no grandParentClientId, get/insertBlocks(null) will do so at the root level
              const [grandParentClientId] = parentBlocks.length > 1 ? parentBlocks.slice(-2) : [];
              const parentSiblingBlocks = getBlocks(grandParentClientId);
              const siblingBlocks = getBlocks(parentClientId);
              const parentIndex = parentSiblingBlocks.findIndex(({ clientId }) => clientId === parentClientId);
              const insertionPoint = siblingBlocks.findIndex(({ clientId: id }) => id === clientId);
              const previousBlock = siblingBlocks[insertionPoint - 1];
              const insertAfter = previousBlock && previousBlock.attributes.content?.text === '';
              const { offset: selectionStart } = getSelectionStart();
              const { offset: selectionEnd } = getSelectionEnd();

              // if there are two empty paragraphs together (carriage return was hit twice), split the parent
              // and move the blocks from the split point into a new parent of the same type
              if (selectionStart === selectionEnd
               && previousBlock.name === 'core/paragraph'
               && insertAfter
              ) {
                if (siblingBlocks.length > 1) {
                  // removeBlock(newBlocks[0].clientId);
                  // removeBlock(newBlocks[1].clientId);
                }
                const newParent = createBlock(
                  parentBlock.name,
                  // parentBlock.attributes,
                  {},
                  [previousBlock],
                );
                if (insertAfter) {
                  // createUndoLevel();
                  insertBlock(newParent, parentIndex + 1, grandParentClientId)
                    .then(() => new Promise((resolve) => { resolve(); }))
                    .then(() => moveBlocksToPosition(siblingBlocks.slice(insertionPoint + 1)
                      .map(({ clientId }) => clientId), parentClientId, newParent.clientId, 0))
                    // .then(() => removeBlock(newParent.innerBlocks[0]?.clientId, true))
                    .then(() => selectBlock(getBlock(newParent.clientId).innerBlocks[0]?.clientId));
                }
              }
            }
          }
        }}
        onSplit={(value, isOriginal) => {
          let newAttributes;

          if (isOriginal || value) {
            newAttributes = {
              ...attributes,
              content: value,
            };
          }

          const block = createBlock('core/paragraph', newAttributes);

          if (isOriginal) {
            block.clientId = clientId;
          }
          createUndoLevel();
          return block;
        }}
        onMerge={mergeBlocks}
        onRemove={(...args) => {
          const parentBlocks = getBlockParents(clientId);
          const [parentClientId] = parentBlocks.slice(-1);
          if (parentClientId) {
            const blocks = getBlocks(parentClientId);
            if (blocks.length === 1) {
              removeBlock(parentClientId);
            }
          }
          onRemove(...args);
        }}
        aria-label={
					RichText.isEmpty(content) ? __('Empty block; start writing or type forward slash to choose a block') : __('Block: Paragraph')
				}
        data-empty={RichText.isEmpty(content)}
        placeholder={placeholder || __('Type / to choose a block')}
        data-custom-placeholder={placeholder ? true : undefined}
        __unstableEmbedURLOnPaste
        __unstableAllowPrefixTransformations
      />
    </>
  );
}

addFilter(
  'blocks.registerBlockType',
  'xd/add-paragraph-container-wrapper',
  (settings) => {
    if (themeVersionCompare('>=', '2.7.0')) {
      return settings;
    }
    const { name } = settings;
    if (name !== 'core/paragraph') {
      return settings;
    }
    settings.edit = ParagraphBlock;
    return settings;
  },
);
