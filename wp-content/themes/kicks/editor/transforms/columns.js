import { createBlock, getBlockType } from '@wordpress/blocks';

export const name = 'xd/columns';

export const to = [{
  type: 'block',
  blocks: ['xd/container'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/container').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));
    const newInnerBlocks = innerBlocks.reduce((prev, cur) => [...prev, ...cur.innerBlocks], []);

    return createBlock(
      'xd/container',
      newAttributes,
      newInnerBlocks,
    );
  },
}];
