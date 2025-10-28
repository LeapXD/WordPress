/**
 * Block dependencies
 */
import edit from './edit';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  merge(attributes, attributesToMerge) {
    return {
      text: (attributes.text || '') + (attributesToMerge.text || ''),
    };
  },
  edit,
  save: () => null,
};

export { name, metadata, settings };
