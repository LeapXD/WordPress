import classNames from 'classnames';
import uniqueId from 'lodash/uniqueId';
import { FieldGroupControl } from './controls';

export default function XDFieldGroup(props) {
  const {
    label, className, onChange, value, fields, id = uniqueId('xd-field-group-'),
  } = props;

  return (
    <div
      label={label}
      className={classNames(className, 'xd-field-group')}
    >
      {label && <h4>{label}</h4>}
      {fields.map(({
        attribute,
        name,
        activeState = 'value',
        method = 'onChange',
        ...props
      }, key) => {
        const propertyName = name ?? attribute;
        const val = value[propertyName] ?? '';
        return (
          <FieldGroupControl
            {...{
              method,
              activeState,
              value: val,
              onChange: (val) => onChange({ ...value, ...{ [propertyName]: val } }),
              key,
              data: value,
              setData: onChange,
              propertyName,
              ...props,
              id: `${id}-${key}`,
            }}
          />
        );
      })}
    </div>
  );
}
