/**
 * Block dependencies
 */
import { heading } from '@wordpress/icons';
import edit from './edit';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  icon: heading,
  merge(attributes, attributesToMerge) {
    return {
      content: (attributes.content || '') + (attributesToMerge.content || ''),
    };
  },
  edit,
  save: () => null,
};

export { name, metadata, settings };
