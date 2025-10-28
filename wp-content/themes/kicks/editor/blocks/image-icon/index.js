/**
 * Block dependencies
 */
import edit from './edit';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || ''),
    };
  },
  edit,
  save: () => null,
};

export { name, metadata, settings };
