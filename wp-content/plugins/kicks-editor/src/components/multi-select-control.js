import { FormTokenField } from '@wordpress/components';
import classnames from 'classnames';

export default function XDMultiSelectControl({
  id,
  className,
  options = [],
  onChange,
  value,
  ...props
}) {
  return (
    <FormTokenField
      suggestions={options.map(({ label }) => label)}
      value={value.map((v) => options.find(({ value }) => value === v)?.label || '').filter(Boolean)}
      onChange={(token) => {
        const selectedOptions = token.map((t) => options.find(({ label }) => label.toLowerCase() === t.toLowerCase()));
        onChange(selectedOptions.map(({ value }) => value));
      }}
      className={classnames(`xd-multi-select-control-${id}`, 'xd-multi-select-control', className)}
      saveTransform={(token) => (options.find(({ label }) => label.toLowerCase() === token.toLowerCase()) ? token : '')}
      {...props}
    />
  );
}
