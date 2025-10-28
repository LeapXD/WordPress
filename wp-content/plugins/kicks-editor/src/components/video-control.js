import { BaseControl } from '@wordpress/components';
import classNames from 'classnames';
import XDVideo from './video';

export default function XDVideoControl({
  onChange, value, label, help, className = '', ...controlProps
}) {
  return (
    <BaseControl
      help={help}
      label={label}
      className={classNames(className, 'xd-video-control')}
    >
      <div className="is-selected">
        <XDVideo
          video={value}
          onChange={onChange}
          {...controlProps}
        />
      </div>
    </BaseControl>
  );
}
