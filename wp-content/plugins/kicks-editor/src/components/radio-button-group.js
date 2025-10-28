import {
  __experimentalToggleGroupControl as ToggleGroupControl,
  __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

export default function XDRadioButtonGroup(props) {
  const {
    options,
    ...controlProps
  } = props;

  return (
    <ToggleGroupControl
      {...controlProps}
    >
      {options.map((option) => (
        <ToggleGroupControlOption
          value={option.value}
          key={option.value}
          label={option.label}
        />
      ))}
    </ToggleGroupControl>
  );
}
