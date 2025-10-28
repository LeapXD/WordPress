/* eslint-disable react/no-array-index-key */
import { DropdownMenu, MenuGroup, MenuItem } from '@wordpress/components';
import classnames from 'classnames';
import { XDIcon } from './icons';

export default function XDToolbarDropdownMenu(props) {
  const {
    value, onChange, label, options, icon, ...controlProps
  } = props;

  let toolbarIcon;

  if (!Array.isArray(value) && !icon) {
    toolbarIcon = options.find(({ value: val }) => val === value)?.icon;
  } else {
    toolbarIcon = icon;
  }

  return (
    <div className="components-toolbar">
      <DropdownMenu
        {...controlProps}
        icon={<XDIcon icon={toolbarIcon} />}
        label={label}
      >
        { ({ onClose }) => (
          <MenuGroup>
            {options.map(({
              label, value: val, icon, ...itemProps
            }, key) => {
              const isSelected = Array.isArray(value) ? value.indexOf(val) > -1 : value === val;
              return (
                <MenuItem
                  key={key}
                  icon={(<XDIcon icon={icon} />)}
                  onClick={() => {
                    onChange(val);
                    onClose();
                  }}
                  className={classnames('components-dropdown-menu__menu-item', { 'is-active': isSelected })}
                  {...itemProps}
                >
                  {label}
                </MenuItem>
              );
            })}
          </MenuGroup>
        ) }
      </DropdownMenu>
    </div>
  );
}
