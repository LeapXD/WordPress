import { Button } from '@wordpress/components';
import { XDIcon } from './icons';

export default function XDButton({
  value, onClick, icon, text, ...props
}) {
  if (icon) {
    return (
      <Button
        icon={<XDIcon icon={icon} />}
        label={text}
        onClick={(event) => {
          event.stopPropagation();
          if (onClick) {
            onClick(value);
          }
        }}
        {...props}
      />
    );
  }

  return (
    <Button
      text={text}
      onClick={(event) => {
        event.stopPropagation();
        if (onClick) {
          onClick(value);
        }
      }}
      {...props}
    />
  );
}
