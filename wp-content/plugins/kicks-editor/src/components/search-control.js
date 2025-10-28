import { useEffect, useState } from '@wordpress/element';
import { ComboboxControl } from '@wordpress/components';
import classnames from 'classnames';
import uniqueId from 'lodash/uniqueId';

export default function XDSearchControl({
  onChange = () => {},
  onFilterValueChange = () => {},
  placeholder = 'Search',
  className,
  ...props
}) {
  const [remountKey, setRemountKey] = useState(0);
  const [uid] = useState(uniqueId());

  useEffect(() => {
    const input = document.querySelector(`.xd-combobox-control-${uid} input`);
    if (input) {
      input.placeholder = placeholder;
    }
    if (remountKey > 0) {
      input.focus();
    }
  }, [remountKey]);

  return (
    <ComboboxControl
      className={classnames(`xd-combobox-control-${uid}`, className)}
      allowReset={false}
      key={remountKey}
      onFilterValueChange={onFilterValueChange}
      onChange={(value) => {
        onChange(value);
        setRemountKey(remountKey + 1);
      }}
      {...props}
    />
  );
}
