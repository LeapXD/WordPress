import apiFetch from '@wordpress/api-fetch';
import { registerBlockType } from '@wordpress/blocks';
import { doAction, applyFilters, addFilter } from '@wordpress/hooks';
import { registerPlugin } from '@wordpress/plugins';
import domReady from '@wordpress/dom-ready';

import './i18n';
import './core-filters/block-settings';
import './core-filters/block-styles';
import './core-filters/block-title';
import './core-filters/save-meta';
import './core-filters/paragraph';
import './core-filters/default-category';
import './core-filters/inner-blocks';
import './core-filters/icon';
import './core-filters/taxonomy-dropdown';

import createNoCacheMiddleware from './utils/no-cache-api-middleware';

import modules from './modules';

import registerBlocks from './register';

import blocks from './blocks';

import { getPlugins, featuredImage } from './plugins';

import './scss/editor.scss';

import { context } from './components';
import * as components from './components';
import themeVersionCompare from './utils/theme-version-compare';

window.xd_settings.customComponents = {};
window.xd_settings.customHandlers = {
  __prevent_default: (e) => e.preventDefault(),
  __blur: (e) => e.target.blur(),
  __noop: () => {},
};

window.xd_settings.context = context;
window.xd_settings.components = components;

window.xd_settings.registerComponent = (name, component) => {
  window.xd_settings.customComponents[name] = component;
};

window.xd_settings.registerHandler = (name, handler) => {
  window.xd_settings.customHandlers[name] = handler;
};

domReady(() => {
  apiFetch.use(createNoCacheMiddleware());

  if (themeVersionCompare('<=', '2.4.3')) {
    addFilter('xd.editorModules', 'xd-module/remove-custom-module', (modules) => modules.filter(({ name }) => !['xd/custom'].includes(name)));
  }
  doAction('xd.beforeRegisterEditorComponents');

  const editorModules = applyFilters('xd.editorModules', modules);

  const editorBlocks = applyFilters('xd.editorComponents', blocks);

  editorModules.forEach(({ register }) => {
    if (register) {
      register();
    }
  });

  editorBlocks
    // ensure components export name or metadata
    .filter(({ metadata, name, settings }) => (name || metadata) && settings)
    // prefer metadata over name
    .map(({ metadata, name, settings }) => ({ nameOrMetadata: metadata ?? name, settings }))
    // register the block
    .forEach(({ nameOrMetadata, settings }) => registerBlockType(nameOrMetadata, settings));

  const plugins = applyFilters('xd.editorPlugins', getPlugins());

  plugins.forEach((plugin) => {
    plugin.forEach(({
      name, plugin, panel, icon,
    }) => {
      if (panel === 'XDFeaturedImagePanel') {
        addFilter('editor.PostFeaturedImage', 'xd/filter-featured-image', featuredImage(plugin));
      } else {
        registerPlugin(name, { render: plugin, icon });
      }
    });
  });

  registerBlocks();

  doAction('xd.afterRegisterEditorComponents');
});
