/* eslint-disable no-case-declarations */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import { BaseControl, RangeControl, TextControl } from '@wordpress/components';
import classNames from 'classnames';
import XDRadioButtonGroup from './radio-button-group';

export default function XDWidthControl({
  onChange, value, className = '', ...controlProps
}) {
  const v = parseFloat(value);
  const width = Number.isNaN(v) ? '' : v;
  const units = value.replace(width, '') || '%';

  return (
    <BaseControl
      {...controlProps}
      className={classNames(className, 'xd-width-control')}
    >
      <div className="xd-width-control__fields">
        <TextControl
          type="number"
          label="Width"
          hideLabelFromVision
          value={width}
          onChange={(width) => {
            onChange(width ? `${width}${units}` : undefined);
          }}
        />
        <XDRadioButtonGroup
          label="Unit"
          hideLabelFromVision
          options={[
            {
              value: 'px',
              label: 'px',
            },
            {
              value: '%',
              label: '%',
            },
          ]}
          value={units}
          onChange={(units) => {
            onChange(width ? `${width}${units}` : undefined);
          }}
        />
      </div>
    </BaseControl>
  );
}
