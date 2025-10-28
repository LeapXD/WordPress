/**
 * Block dependencies
 */
import icon from './icon';
import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  icon,
  merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || ''),
    };
  },
  edit,
  save,
};

export { name, metadata, settings };
