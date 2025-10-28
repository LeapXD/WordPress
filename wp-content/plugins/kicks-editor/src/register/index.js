import {
  registerBlockType, getBlockType, unregisterBlockType,
} from '@wordpress/blocks';
import { compose, useMergeRefs } from '@wordpress/compose';
import { withFilters, withNotices } from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps, store as blockEditorStore } from '@wordpress/block-editor';
import classnames from 'classnames';
import ServerSideRender from '@wordpress/server-side-render';
import { useRef, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { useXdBlockContext } from '../components/context';
import themeVersionCompare from '../utils/theme-version-compare';

function PreInnerBlocks({ preInnerBlocksClasses, isSelected, preInnerBlocks = null }) {
  if (preInnerBlocksClasses) {
    return <div className={classnames('pre-inner-blocks', preInnerBlocksClasses, { 'is-selected': isSelected })}>{preInnerBlocks}</div>;
  }
  return preInnerBlocks;
}
function PostInnerBlocks({ postInnerBlocksClasses, isSelected, postInnerBlocks = null }) {
  if (postInnerBlocksClasses) {
    return <div className={classnames('post-inner-blocks', postInnerBlocksClasses, { 'is-selected': isSelected })}>{postInnerBlocks}</div>;
  }
  return postInnerBlocks;
}

const ConditionalWrapper = ({ condition, wrapper, children }) => (condition ? wrapper(children) : children);

function InnerBlocks({
  serverSideRender,
  name,
  children,
  attributes,
  className,
}) {
  if (serverSideRender) {
    const blockContext = useXdBlockContext();
    const { clientId } = blockContext;
    const blockParents = useSelect((select) => {
      const { getBlockParents } = select(blockEditorStore);
      return getBlockParents(clientId);
    });
    const [blockParent] = blockParents.slice(-1);
    const parentBlock = blockParent
      ? useSelect((select) => {
        const { getBlock } = select(blockEditorStore);
        return getBlock(blockParent);
      }) : null;
    const { attributes: parentAttributes, name: parentName } = parentBlock || {};

    return (
      <>
        {children}
        <ServerSideRender
          attributes={attributes}
          block={name}
          urlQueryArgs={{
            editor: true, parentAttributes, parentName,
          }}
          className={classnames('server-side-render', className)}
        />
      </>
    );
  }
  return children;
}

function getWrapperClassnames(wrapper) {
  let classNames = [];
  let toRemove = [];
  wrapper.forEach((wrap = '') => {
    if (wrap !== false) {
      if (typeof wrap === 'object') {
        classNames = [...classNames, ...wrap.add?.split(' ') ?? []];
        toRemove = [...toRemove, ...wrap.remove?.split(' ') ?? []];
      } else {
        classNames = [...classNames, ...wrap.split(' ')];
      }
    }
  });
  return classnames(classNames.filter((className) => !toRemove.includes(className)));
}

function Edit({
  attributes,
  className,
  innerBlocksClassName,
  innerBlocksSettings = {},
  preInnerBlocks,
  postInnerBlocks,
  preInnerBlocksClassName,
  postInnerBlocksClassName,
  innerClassName,
  outerClassName,
  style: blockStyle,
  context,
  isSelected,
  name,
  ...props
}) {
  const { layout = {}, style } = attributes;
  const { wrap = false, serverSideRender = false, ...innerBlocksSetup } = innerBlocksSettings;
  const settings = { ...innerBlocksSetup, __experimentalLayout: layout, layout };
  const wrapper = typeof wrap === 'object' ? wrap : {};
  const {
    preInnerBlocksInside = false,
    postInnerBlocksInside = false,
  } = wrapper;

  const ref = useRef();

  const { contextRef } = useXdBlockContext();
  const [popoverAnchor, setPopoverAnchor] = useState(null);

  contextRef.current = {
    ref, popoverAnchor,
  };

  const blockRef = {
    ref: useMergeRefs([setPopoverAnchor, ref]),
    onKeyDown: ref.current?.onKeyDown,
  };

  const { className: blockClassName, ...blockProps } = useBlockProps({ style: { ...style, ...blockStyle }, ...blockRef });

  const blockClass = getWrapperClassnames(
    [wrapper.block, wrapper.editorBlock, classnames(className, blockClassName)],
  );
  let outerClass = getWrapperClassnames(
    [wrapper.outer, wrapper.editorOuter, outerClassName],
  );
  let innerClass = getWrapperClassnames(
    [wrapper.inner, wrapper.editorInner, innerClassName],
  );
  const preInnerBlocksClass = getWrapperClassnames(
    [wrapper.preInnerBlocks, wrapper.editorPreInnerBlocks, preInnerBlocksClassName],
  );
  let innerBlocksClass = getWrapperClassnames(
    [wrapper.innerBlocks, wrapper.editorInnerBlocks, innerBlocksClassName],
  );
  const postInnerBlocksClass = getWrapperClassnames(
    [wrapper.postInnerBlocks, wrapper.editorPostInnerBlocks, postInnerBlocksClassName],
  );
  const serverSideRenderBlockClass = wrapper.serverSideRenderBlock;

  if (
    themeVersionCompare('>=', '2.4.3')
    && (outerClass
     || innerClass
     || innerBlocksClass)
  ) {
    if (outerClass && !innerClass) {
      innerClass = outerClass;
      outerClass = false;
    }
    if (innerClass && !innerBlocksClass) {
      innerBlocksClass = innerClass;
      innerClass = false;
    }
    const { children, ...innerBlockProps } = useInnerBlocksProps({
      className: classnames(innerBlocksClass, { 'is-selected': isSelected }),
    }, settings);
    return (
      <div {...blockProps} className={blockClass}>
        <ConditionalWrapper
          condition={outerClass && innerClass && innerBlocksClass}
          wrapper={(children) => <div className={classnames(outerClass, { 'is-selected': isSelected })}>{children}</div>}
        >
          <ConditionalWrapper
            condition={innerClass}
            wrapper={(children) => <div className={classnames(innerClass, { 'is-selected': isSelected })}>{children}</div>}
          >
            {!preInnerBlocksInside && (
            <PreInnerBlocks
              isSelected={isSelected}
              preInnerBlocks={preInnerBlocks}
              preInnerBlocksClasses={preInnerBlocksClass}
            />
            )}
            <InnerBlocks
              className={serverSideRenderBlockClass}
              serverSideRender={serverSideRender}
              name={name}
              attributes={attributes}
            >
              <div {...innerBlockProps}>
                {!!preInnerBlocksInside && (
                <PreInnerBlocks
                  isSelected={isSelected}
                  preInnerBlocks={preInnerBlocks}
                  preInnerBlocksClasses={preInnerBlocksClass}
                />
                )}
                {children}
                {!!postInnerBlocksInside && (
                <PostInnerBlocks
                  isSelected={isSelected}
                  postInnerBlocks={postInnerBlocks}
                  postInnerBlocksClasses={postInnerBlocksClass}
                />
                )}
              </div>
            </InnerBlocks>
            {!postInnerBlocksInside && (
            <PostInnerBlocks
              isSelected={isSelected}
              postInnerBlocks={postInnerBlocks}
              postInnerBlocksClasses={postInnerBlocksClass}
            />
            )}
          </ConditionalWrapper>
        </ConditionalWrapper>
      </div>
    );
  }

  if (wrap === true) {
    const innerBlockProps = useInnerBlocksProps({ className: innerBlocksClass }, settings);
    return (
      <div {...blockProps} className={blockClass}>
        <PreInnerBlocks
          preInnerBlocks={preInnerBlocks}
          preInnerBlocksClasses={preInnerBlocksClass}
        />
        <InnerBlocks
          className={serverSideRenderBlockClass}
          serverSideRender={serverSideRender}
          name={name}
          attributes={attributes}
        >
          <div {...innerBlockProps} />
        </InnerBlocks>
        <PostInnerBlocks
          postInnerBlocks={postInnerBlocks}
          postInnerBlocksClasses={postInnerBlocksClassName}
        />
      </div>
    );
  }

  const { children, ...innerBlockProps } = useInnerBlocksProps({ ...blockProps, className: classnames(blockClass, innerBlocksClass) }, settings);
  return (
    <div {...innerBlockProps}>
      <PreInnerBlocks
        preInnerBlocks={preInnerBlocks}
        preInnerBlocksClasses={preInnerBlocksClass}
      />
      <InnerBlocks
        className={serverSideRenderBlockClass}
        serverSideRender={serverSideRender}
        name={name}
        attributes={attributes}
      >
        {children}
      </InnerBlocks>
      <PostInnerBlocks
        postInnerBlocks={postInnerBlocks}
        postInnerBlocksClasses={postInnerBlocksClass}
      />
    </div>
  );
}

const edit = compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(Edit);

const registerBlocks = () => {
  const { xd_settings: xdSettings = {} } = window;
  const { block_settings: blockSettings } = xdSettings;
  const blockNames = Object.keys(blockSettings);
  const blocks = blockNames
    .map((name) => ({
      name,
      apiVersion: 2,
      ...blockSettings[name],
    }))
    .filter(({ supports: { custom } = {} }) => custom?.register);

  const settings = {
    merge(attributes, attributesToMerge) {
      return {
        content: (attributes.content || '') + (attributesToMerge.content || ''),
      };
    },
    save: () => useInnerBlocksProps?.save()?.children,
    edit,
  };

  blocks.forEach((block) => {
    const { name } = block;
    if (getBlockType(name)) {
      unregisterBlockType(name);
    }
    registerBlockType(block, settings);
  });
};

window.xd_settings.editor_settings.edit = Edit;

export default registerBlocks;
