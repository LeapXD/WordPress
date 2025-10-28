import { createBlock, getBlockType } from '@wordpress/blocks';

export const name = 'xd/container';

export const to = [{
  type: 'block',
  blocks: ['xd/two-tile'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/two-tile').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));

    return createBlock(
      'xd/two-tile',
      newAttributes,
      innerBlocks,
    );
  },
}, {
  type: 'block',
  blocks: ['xd/columns'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/columns').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));

    const newInnerBlocks = [createBlock('xd/column', {}, innerBlocks)];

    return createBlock(
      'xd/columns',
      newAttributes,
      newInnerBlocks,
    );
  },
}, {
  type: 'block',
  blocks: ['xd/cta'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/cta').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));

    return createBlock(
      'xd/cta',
      newAttributes,
      innerBlocks,
    );
  },
}];
