import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;

const settings = {
  edit,
  save,
};

export { name, metadata, settings };
