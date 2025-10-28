import { BaseControl, Grid } from '@wordpress/components';
import classNames from 'classnames';
import XDButton from './button';

// @todo: remove when they remove the experimental status of the Grid component
if (!wp.components.Grid && wp.components.__experimentalGrid) {
  wp.components.Grid = wp.components.__experimentalGrid;
}

export default function XDButtonGroup(props) {
  const {
    value,
    onChange,
    options,
    gridColumns,
    className,
    ...baseControlProps
  } = props;
  return (
    <BaseControl
      {...baseControlProps}
      className={classNames(className, 'xd-button-group')}
    >
      <div>
        <Grid columns={gridColumns}>
          {options.map(({
            value: val, label, icon, ...props
          }) => (
            <XDButton
              value={val ?? ''}
              isPressed={Array.isArray(value) ? value.indexOf(val) > -1 : value === val}
              key={val}
              text={label}
              onClick={() => onChange(val)}
              icon={icon}
              {...props}
            />
          ))}
        </Grid>
      </div>
    </BaseControl>
  );
}
