import { BaseControl, CheckboxControl } from '@wordpress/components';

export default function XDCheckboxGroup(props) {
  const {
    value,
    options,
    onChange,
    disabled,
    ...baseControlProps
  } = props;
  return (
    <BaseControl
      {...baseControlProps}
    >
      <div className="components-base-control__checkbox-wrapper">
        {options.map((option) => (
          <CheckboxControl
            disabled={disabled}
            checked={Array.isArray(value) ? value.indexOf(option.value) > -1 : value === option.value}
            key={option.value}
            label={option.label}
            onChange={() => onChange(option.value)}
          />
        ))}
      </div>
    </BaseControl>
  );
}
