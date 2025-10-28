import * as icons from '@wordpress/icons';
import { camelCase, snakeCase } from 'lodash';
import { Dashicon, Icon } from '@wordpress/components';

const { xd_settings: settings } = window;
const { editor_settings: editorSettings } = settings;
const { theme_dir: dir } = editorSettings;

export const GMIcon = function ({ icon, ...props }) {
  const name = snakeCase(icon);
  return (
    <span {...props} className="material-symbols-outlined">
      {name}
    </span>
  );
};

export function SVGIcon({ icon, ...props }) {
  return (
    <svg className="xd-icon" width={15} height={15} {...props}>
      <title>{`${icon} icon`}</title>
      <use xlinkHref={`${dir}/build/icons/icons.svg#${icon}`} />
    </svg>
  );
}

export const WPIcon = function ({ icon, ...props }) {
  const name = camelCase(icon);
  if (!icons[name]) {
    return null;
  }
  return <Icon {...props} icon={icons[name]} />;
};

export const XDIcon = function ({ icon = '', ...props }) {
  if (typeof icon === 'string') {
    if (icon.startsWith('xd/')) {
      const name = icon.slice(3);
      return (
        <SVGIcon {...props} icon={name} />
      );
    }
    if (icon.startsWith('gm/')) {
      const name = icon.slice(3);
      return (
        <GMIcon {...props} icon={name} />
      );
    }
    if (icon.startsWith('wp/')) {
      const name = icon.slice(3);
      return (
        <WPIcon {...props} icon={name} />
      );
    }
    return <Dashicon {...props} icon={icon} />;
  }
  return icon;
};
