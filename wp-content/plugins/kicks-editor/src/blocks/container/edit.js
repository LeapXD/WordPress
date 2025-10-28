/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { compose } from '@wordpress/compose';
import { withNotices, withFilters } from '@wordpress/components';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classNames from 'classnames';

const Container = ({ className, innerBlocksClassName, innerBlocksSettings }) => {
  const blockClass = classNames('container', className);
  const innerClass = classNames('XD-container__inner', innerBlocksClassName);
  const blockProps = useBlockProps({ className: blockClass });
  const innerBlocksProps = useInnerBlocksProps({ className: innerClass }, innerBlocksSettings ?? {
    allowedBlocks: [
      'core/paragraph',
      'core/heading',
      'core/list',
      'core/quote',
      'core/cover',
      'xd/button',
      'core/separator',
      'xd/image',
      'xd/accordion',
      'gravityforms/form',
    ],
    template: [['core/paragraph']],
  });

  return (
    <div {...blockProps}>
      <div {...innerBlocksProps} />
    </div>
  );
};

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(Container);
