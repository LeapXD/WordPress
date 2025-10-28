import { MediaUpload } from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { store as coreStore } from '@wordpress/core-data';
import { Button, PanelRow } from '@wordpress/components';
import { XDIcon } from './icons';

export default function XDFileUpload({
  file = {},
  allowedTypes = ['application/pdf'],
  onChange = () => {},
  ...props
}) {
  const { id, title, url } = file;

  const mediaItem = useSelect((select) => {
    const { getMediaItems } = select(coreStore);
    return getMediaItems({ include: id });
  });

  useEffect(() => {
    if (mediaItem && !mediaItem.length) {
      if (url) {
        onChange({});
        createErrorNotice(`${url} was removed from the media library. File removed.`, { type: 'snackbar' });
      }
    }
  }, [mediaItem]);

  const { createErrorNotice } = useDispatch(noticesStore);

  const onUploadError = (message) => {
    createErrorNotice(message, { type: 'snackbar' });
  };

  const display = ({ open }) => (
    <>
      <div>
        {!!id && (
        <Button
          icon={<XDIcon size="16px" icon="wp/external" />}
          className="xd-preview-link"
          iconPosition="right"
          href={url}
          target="_blank"
          variant="link"
        >
          {title}
        </Button>
        )}
      </div>
      <PanelRow>
        {!id && <Button variant="secondary" onClick={open}>Select File</Button>}
        {!!id && <Button variant="secondary" onClick={open}>Replace File</Button>}
        {!!id && <Button variant="link" isDestructive onClick={() => { onChange({}); }}>Remove File</Button>}
      </PanelRow>
    </>
  );

  return (
    <MediaUpload
      {...props}
      value={id}
      onSelect={({ id, url, title }) => { onChange({ id, url, title }); }}
      allowedTypes={allowedTypes}
      onError={onUploadError}
      render={display}
    />
  );
}
