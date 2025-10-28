import { createBlock, getBlockType } from '@wordpress/blocks';

export const name = 'xd/cta';

export const to = [{
  type: 'block',
  blocks: ['xd/container'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/container').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));

    return createBlock(
      'xd/container',
      newAttributes,
      innerBlocks,
    );
  },
}];
