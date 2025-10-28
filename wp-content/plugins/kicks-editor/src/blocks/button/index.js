import { button as icon } from '@wordpress/icons';
import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  icon,
  edit,
  save,
  merge: (a, { text = '' }) => ({
    ...a,
    text: (a.text || '') + text,
  }),

};

export { name, metadata, settings };
