/**
 * WordPress dependencies
 */
import { buttons as icon } from '@wordpress/icons';
/**
 * Internal dependencies
 */
import transforms from './transforms';
import edit from './edit';
import save from './save';
import variations from './variations';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  icon, edit, save, variations, transforms,
};

export { name, metadata, settings };
