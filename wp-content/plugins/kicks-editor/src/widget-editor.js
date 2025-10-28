import { applyFilters, doAction } from '@wordpress/hooks';
import domReady from '@wordpress/dom-ready';
import { registerBlockType } from '@wordpress/blocks';
import themeVersionCompare from './utils/theme-version-compare';
import './core-filters/block-settings';
import './core-filters/default-category';
import './core-filters/inner-blocks';
import './core-filters/icon';

import registerBlocks from './register';

import './scss/editor.scss';
import * as contextModule from './components/context';
import context from './modules/context';
import custom from './modules/custom';
import * as components from './components/widget-components';
import blocks from './blocks';

window.xd_settings.customComponents = {};
window.xd_settings.context = contextModule;
window.xd_settings.components = components;

window.xd_settings.registerComponent = (name, component) => {
  window.xd_settings.customComponents[name] = component;
};

domReady(() => {
  doAction('xd.beforeRegisterWidgetComponents');
  context.register();
  custom.register();
  registerBlocks();

  if (themeVersionCompare('<=', '2.4.3')) {
    addFilter('xd.editorModules', 'xd-module/remove-custom-module', (modules) => modules.filter(({ name }) => !['xd/custom'].includes(name)));

    const editorBlocks = applyFilters('xd.editorComponents', blocks);

    editorBlocks
    // ensure components export name or metadata
      .filter(({ metadata, name, settings }) => (name || metadata) && settings)
    // prefer metadata over name
      .map(({ metadata, name, settings }) => ({ nameOrMetadata: metadata ?? name, settings }))
    // register the block
      .forEach(({ nameOrMetadata, settings }) => registerBlockType(nameOrMetadata, settings));
  }

  doAction('xd.afterRegisterWidgetComponents');
});
