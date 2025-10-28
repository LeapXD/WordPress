import { BaseControl } from '@wordpress/components';
import classNames from 'classnames';

export default function XDMessagePanel({ className, ...props }) {
  return (
    <BaseControl
      {...props}
      className={classNames(className, 'xd-message-panel')}
    />
  );
}
