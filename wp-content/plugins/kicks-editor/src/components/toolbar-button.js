import { ToolbarButton } from '@wordpress/components';
import { XDIcon } from './icons';

export default function XDToolBarButton({
  value, onClick, icon, text, ...props
}) {
  if (icon) {
    return (
      <ToolbarButton
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
    <ToolbarButton
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
