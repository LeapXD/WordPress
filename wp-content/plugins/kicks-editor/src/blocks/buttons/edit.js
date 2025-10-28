import classnames from 'classnames';
import {
  BlockControls,
  useBlockProps,
  InnerBlocks,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import { JustifyContentControl } from './justify-content-control';
import { name as buttonBlockName } from '../button';

const ALLOWED_BLOCKS = [buttonBlockName];
const BUTTONS_TEMPLATE = [['xd/button']];

const layout = {
  type: 'default',
  alignments: [],
};

const __experimentalLayout = layout;

const ButtonsEdit = ({
  attributes: { contentJustification },
  setAttributes,
}) => {
  const blockProps = useBlockProps({
    className: classnames({ [`is-content-justification-${contentJustification}`]: contentJustification }),
  });
  const innerBlocksProps = useInnerBlocksProps(blockProps, {
    allowedBlocks: ALLOWED_BLOCKS,
    template: BUTTONS_TEMPLATE,
    orientation: 'horizontal',
    __experimentalLayout,
    layout,
  });

  return (
    <>
      <BlockControls group="block">
        <JustifyContentControl
          allowedControls={['left', 'center', 'right', 'space-between']}
          value={contentJustification}
          onChange={(value) => setAttributes({ contentJustification: value })}
          popoverProps={{
					  position: 'bottom right',
					  isAlternate: true,
          }}
        />
      </BlockControls>
      <div {...innerBlocksProps}>
        {/* if innerBlocksProps breaks, use this fallback. css will break though */}
        {/* <InnerBlocks
						allowedBlocks={ALLOWED_BLOCKS}
						template={BUTTONS_TEMPLATE}
						orientation={'horizontal'}
						templateInsertUpdatesSelection={true}
				/> */}
      </div>
    </>
  );
};

export default ButtonsEdit;
