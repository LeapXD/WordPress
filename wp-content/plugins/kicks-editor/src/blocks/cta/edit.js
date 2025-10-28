import { withFilters, withNotices } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

function CTA({ className, innerBlocksClassName, innerBlocksSettings }) {
  const blockClass = classNames('container', className);
  const innerClass = classNames('XD-cta__inner', innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlocksProps = useInnerBlocksProps({ className: innerClass }, innerBlocksSettings ?? {
    allowedBlocks: ['core/heading', 'core/paragraph', 'xd/button'],
    template: [['core/heading'], ['core/paragraph'], ['xd/button', { align: 'center' }]],
  });

  return (
    <div {...blockProps}>
      <div
        {...innerBlocksProps}
      />
    </div>
  );
}

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(CTA);
