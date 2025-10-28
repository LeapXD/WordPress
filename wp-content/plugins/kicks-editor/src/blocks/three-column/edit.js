import { compose } from '@wordpress/compose';
import { withFilters, withNotices } from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

const ThreeColumns = ({ className, innerBlocksClassName, innerBlocksSettings }) => {

  const blockClass = classNames('XD-three-column', className);
  const innerClass = classNames('XD-three-column__content', innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlocksProps = useInnerBlocksProps({ className: innerClass }, innerBlocksSettings ?? {
    orientation: 'horizontal',
    allowedBlocks: ['xd/card'],
    template: [['xd/card'], ['xd/card'], ['xd/card']],
  });

  return (
    <div {...blockProps}>
      <div
        {...innerBlocksProps}
      />
    </div>
  );
};

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(ThreeColumns);
