/**
 * Block dependencies
 */
import edit from './edit';
import metadata from './block.json';
import save from './save';

const { name } = metadata;

const settings = {
  merge(attributes, attributesToMerge) {
    return {
      text: (attributes.text || '') + (attributesToMerge.text || ''),
    };
  },
  edit,
  save,
};

export { name, metadata, settings };
