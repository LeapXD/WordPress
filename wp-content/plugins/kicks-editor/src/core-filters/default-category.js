import { addFilter } from '@wordpress/hooks';

addFilter(
  'blocks.registerBlockType',
  'xd/default-category',
  (settings) => {
    const { category, name } = settings;
    if (!category || (name.includes('acf/') && category === 'common')) {
      const { xd_settings: xdSettings } = window;
      const { editor_settings: editorSettings } = xdSettings;
      const { default_category: defaultCategory } = editorSettings;
      if (defaultCategory) {
        settings.category = defaultCategory;
      }
    }
    return settings;
  },
);
