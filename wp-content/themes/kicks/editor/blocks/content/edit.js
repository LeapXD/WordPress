/* eslint-disable no-case-declarations */
// File: blocks/grid-cell/edit.js
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

import { withFilters } from '@wordpress/components';
import { compose } from '@wordpress/compose';

function Edit({ innerBlocksSettings, className: blockClass }) {
  const { className, ...blockProps } = useBlockProps();

  const { children, ...innerBlocksProps } = useInnerBlocksProps({
    ...blockProps,
    className: `${className} ${blockClass}`,
  }, innerBlocksSettings);

  return (
    <div {...innerBlocksProps}>
      <div className="xd-content__inner">
        {children}
      </div>
    </div>
  );
}

export default compose(
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
)(Edit);
