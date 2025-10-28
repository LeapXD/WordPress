/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

// eslint-disable-line
import { withFilters, withNotices } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import {
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import classNames from 'classnames';

function Card({ className, innerBlocksClassName, innerBlocksSettings } ) {
  const blockClass = classNames('XD-card', className);
  const innerClass = classNames('XD-card__inner', innerBlocksClassName);
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
    template: [['xd/image'], ['core/heading'], ['core/paragraph']],
    templateLock: false,
  });

  return (
    <div {...blockProps}>
      <div {...innerBlocksProps} />
    </div>
  );
}

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(Card);
