import { addFilter } from '@wordpress/hooks';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { dispatch, select } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

const { getBlocks } = select(blockEditorStore);
const { updateBlockAttributes } = dispatch(blockEditorStore);

const withCTAAlignControl = (BlockEdit) => function (props) {
  const { name, attributes, clientId } = props;
  if (name !== 'xd/cta') {
    return <BlockEdit {...props} />;
  }

  const { align } = attributes;
  const innerBlocks = getBlocks(clientId);

  useEffect(() => {
    innerBlocks.forEach((block) => {
      if (block.name === 'core/paragraph') {
        updateBlockAttributes(block.clientId, { align });
      }
      if (block.name === 'core/heading') {
        updateBlockAttributes(block.clientId, { textAlign: align });
      }
      if (block.name === 'xd/buttons') {
        const { layout } = block.attributes;
        if (layout?.justifyContent !== align) {
          updateBlockAttributes(block.clientId, { layout: { ...layout, justifyContent: align } });
        }
      }
    });
  }, [align]);

  return (
    <BlockEdit {...props} />
  );
};

addFilter('editor.BlockEdit', 'xd/cta-with-align-control', withCTAAlignControl);
