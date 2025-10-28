import {
  BaseControl, createSlotFill, PanelRow,
} from '@wordpress/components';
import classNames from 'classnames';
import XDImage from './image';

export default function XDImageControl({
  onChange, value, label, help, id, className = '', ...controlProps
}) {
  const { Slot, Fill } = createSlotFill(`${id}-image-control`);

  return (
    <BaseControl
      label={label}
      help={help}
      className={classNames(className, 'xd-image-control')}
      // __nextHasNoMarginBottom
    >
      <XDImage
        id={id}
        value={value}
        onChange={(image) => onChange(image)}
        Fill={Fill}
        alwaysDisplayButton
        {...controlProps}
      />
      <PanelRow>
        <Slot />
      </PanelRow>
    </BaseControl>
  );
}
