/* eslint-disable react/no-array-index-key */
import { Button } from '@wordpress/components';
import classNames from 'classnames';
import uniqueId from 'lodash/uniqueId';
import { FieldGroupControl } from './controls';
import { XDIcon } from './icons';

export default function XDRepeaterField(props) {
  const {
    label, addButtonLabel, className, onChange, value, fields, id = uniqueId('xd-repeater-field-'),
  } = props;

  return (
    <div
      label={label}
      className={classNames(className, 'xd-field-group')}
    >
      <p className="title">{label ?? ''}</p>
      {value.map((val, key) => (
        <div
          key={key}
        >
          <div
            style={{
              padding: '8px',
              border: '1px solid #ddd',
              marginBottom: '10px',
            }}
          >
            <div
              style={{
                display: 'flex', width: '100%', alignItems: 'flex-start', justifyContent: 'flex-end',
              }}
            >
              {key !== 0 && (
              <Button
                icon={<XDIcon icon="wp/chevronUp" />}
                onClick={() => onChange([...value.slice(0, key - 1), value[key], value[key - 1], ...value.slice(key + 1)])}
                size="small"
              />
              )}
              {(value.length - 1) !== key && (
              <Button
                icon={<XDIcon icon="wp/chevronDown" />}
                onClick={() => onChange([...value.slice(0, key), value[key + 1], value[key], ...value.slice(key + 2)])}
                size="small"
              />
              )}
              <Button
                icon={<XDIcon icon="wp/cancelCircleFilled" />}
                onClick={() => onChange([...value.slice(0, key), ...value.slice(key + 1)])}
                size="small"
              />
            </div>
            { fields.map(({
              attribute,
              name,
              activeState = 'value',
              method = 'onChange',
              ...props
            }, fieldKey) => {
              const propertyName = name ?? attribute;
              const vl = val[propertyName] ?? '';
              return (
                <FieldGroupControl
                  {...{
                    method,
                    activeState,
                    value: vl,
                    onChange: (v) => onChange([...value.slice(0, key), { ...val, [propertyName]: v }, ...value.slice(key + 1)]),
                    key: `${key}${fieldKey}`,
                    data: value,
                    setData: onChange,
                    propertyName,
                    ...props,
                    id: `${id}-${key}-${fieldKey}`,
                  }}
                />
              );
            })}
          </div>
          <hr />
        </div>
      ))}
      <div>
        <Button
          icon={<XDIcon icon="wp/plusCircleFilled" />}
          size="small"
          onClick={() => onChange([...value, {}])}
        >
          {`${addButtonLabel ?? 'Add Item'}`}
        </Button>
      </div>
    </div>
  );
}
