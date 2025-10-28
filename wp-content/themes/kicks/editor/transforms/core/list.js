import { createBlock, getBlockType } from '@wordpress/blocks';

export const name = 'core/list';

export const to = [{
  type: 'block',
  blocks: ['xd/accordion'],
  transform: (attributes, innerBlocks) => {
    const allowedAttributes = Object.keys(getBlockType('xd/accordion').attributes ?? {});
    const newAttributes = Object.fromEntries(Object.entries(attributes).filter(([name]) => allowedAttributes.includes(name)));

    const newInnerBlocks = innerBlocks.map(({ innerBlocks, attributes: { content } }) => (
      createBlock('xd/accordionelement', {}, [createBlock('core/paragraph', { content }), ...innerBlocks])
    ));

    return createBlock(
      'xd/accordion',
      newAttributes,
      newInnerBlocks,
    );
  },
}];
