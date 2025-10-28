/* eslint-disable react/jsx-filename-extension */
/* eslint-disable react/jsx-props-no-spreading */
/* eslint-disable react/react-in-jsx-scope */

import { merge } from 'lodash';
import { addFilter } from '@wordpress/hooks';

// This filter allows the block settings to be modified via theme/inc/block-settings.json
/**
 * @todo this potentially can be removed in the future because WordPress core now essentially
 * does the same thing (merge clientside and serverside block settings) via
 * bootstrapServerSideBlockDefinitions (currently unstable__bootstrapServerSideBlockDefinitions)
 * They do a better job of it too since snake_cased property names
 * are converted to camelCased property names.
 * We need to keep an eye on it. The unstable status seems to imply it could be changed or removed.
 *
 * Update: ACF still uses the older registerBlockType( name, metaData ) signature,
 * rather than the newer registerBlockType( metaData, settings ) signature, which means that serverside
 * block type metadata is not applied to ACF blocks. So we have to keep this for now.
 */
addFilter(
  'blocks.registerBlockType',
  'xd/filter-block-settings',
  (settings) => {
    const { xd_settings: xdSettings = {} } = window;
    const { block_settings: blockSettings } = xdSettings;

    if (!blockSettings) {
      return settings;
    }

    if (typeof blockSettings[settings.name] === 'undefined' || settings.name.startsWith('core/')) {
      return settings;
    }

    settings = merge(settings, blockSettings[settings.name]);

    return settings;
  },
);
