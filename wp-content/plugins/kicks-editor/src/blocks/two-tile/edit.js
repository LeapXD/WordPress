import { compose } from '@wordpress/compose';
import { withFilters, withNotices } from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

const TwoTile = ({className, innerBlocksClassName, innerBlocksSettings}) => {
  const blockClass = classNames('XD-two-tile', className, innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlockProps = useInnerBlocksProps(blockProps, innerBlocksSettings ?? {
    orientation: 'horizontal',
    allowedBlocks: ['xd/image', 'xd/two-two-tile-inner'],
    template: [['xd/image'], ['xd/two-tile-inner']],
    templateLock: 'insert',
  });

  return (
    <div {...innerBlockProps} />
  );
};

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(TwoTile);
