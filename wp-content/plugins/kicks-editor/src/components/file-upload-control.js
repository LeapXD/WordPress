import { BaseControl } from '@wordpress/components';
import classNames from 'classnames';
import XDFileUpload from './file-upload';

export default function XDFileUploadControl({
  onChange, value, label, help, className = '', ...controlProps
}) {
  return (
    <BaseControl
      help={help}
      label={label}
      className={classNames(className, 'xd-file-upload-control')}
    >
      <div className="is-selected">
        <XDFileUpload
          file={value}
          onChange={onChange}
          {...controlProps}
        />
      </div>
    </BaseControl>
  );
}
