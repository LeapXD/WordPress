/* eslint-disable import/extensions */
/* eslint-disable import/no-unresolved */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */
import { registerBlockType } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';

import blocks from './blocks/**/index.js';

import '../assets/scss/editor.scss';
import './modules/**/*.js';
import './components/**/*.js';
import blockTransformSettings from './transforms/**/*.js';

const transformsMap = new Map(blockTransformSettings.map(({ name, ...transforms }) => [name, transforms]));

const withTransforms = (settings) => {
  const { name, transforms: originalTransforms } = settings;
  const transforms = transformsMap.get(name);
  if (!transforms) {
    return settings;
  }

  return {
    ...settings,
    transforms:
      {
        to: [...(originalTransforms?.to ?? []), ...(transforms.to ?? [])],
        from: [...(originalTransforms?.from ?? []), ...(transforms.from ?? [])],
      },
  };
};

addFilter('blocks.registerBlockType', 'xd/transforms', withTransforms, 100);

addFilter('xd.editorModules', 'xd/remove-deprecated-plugin-modules', (modules) => modules.filter(
  ({ name }) => ['xd/custom', 'xd/context', 'xd/slot-fills', 'xd/id'].includes(name),
));

addFilter('xd.editorComponents', 'xd/remove-deprecated-plugin-blocks', (blocks) => []);

domReady(() => {
  blocks
    // ensure components export name or metadata
    .filter(({ metadata, name, settings }) => (name || metadata) && settings)
    // prefer metadata over name
    .map(({ metadata, name, settings }) => ({ nameOrMetadata: metadata ?? name, settings }))
    // register the block
    .forEach(({ nameOrMetadata, settings }) => registerBlockType(nameOrMetadata, settings));
});
