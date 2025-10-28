import { compose } from '@wordpress/compose';
import { withFilters, withNotices } from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

const TwoColumnText = ({ className, innerBlocksClassName, innerBlocksSettings }) => {
  const blockClass = classNames('XD-two-column-text', className, innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlockProps = useInnerBlocksProps(blockProps, innerBlocksSettings ?? {
    orientation: 'horizontal',
    allowedBlocks: ['xd/two-tile-inner'],
    template: [['xd/two-tile-inner'], ['xd/two-tile-inner']],
    templateLock: true,
  });

  return (
    <div {...innerBlockProps} />
  );
};

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(TwoColumnText);
