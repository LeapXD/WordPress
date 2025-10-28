import { withFilters, withNotices } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

function TwoTileInner({ className, innerBlocksClassName, innerBlocksSettings }) {
  const blockClass = classNames('XD-two-tile__inner', className, innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlocksProps = useInnerBlocksProps(blockProps, innerBlocksSettings ?? {
    orientation: 'horizontal',
    allowedBlocks: ['xd/image', 'core/heading', 'core/paragraph', 'xd/button'],
    template: [['core/heading', { level: 3 }], ['core/paragraph'], ['xd/button']],
    templateLock: false,
  });

  return (
    <div
      {...innerBlocksProps}
    />
  );
}

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(TwoTileInner);
