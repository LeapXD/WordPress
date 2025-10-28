import { merge } from 'lodash';
import { addFilter } from '@wordpress/hooks';
import { XDIcon } from '../components/icons';

addFilter(
  'blocks.registerBlockType',
  'xd/filter-block-icon',
  (settings) => {
    settings = merge(settings, {
      icon: XDIcon({ icon: settings.icon }),
      variations: (settings.variations ?? []).map(({ icon, ...variation }) => ({ ...variation, icon: XDIcon({ icon }) })),
    });

    return settings;
  },
);
