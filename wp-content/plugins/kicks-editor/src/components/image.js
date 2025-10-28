import { MediaPlaceholder } from '@wordpress/block-editor';
import {
  forwardRef, useEffect, useRef, useState,
} from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import classnames from 'classnames';
import { Button, Placeholder } from '@wordpress/components';
import { upload, edit } from '@wordpress/icons';
import { store as noticesStore } from '@wordpress/notices';

export default forwardRef(({
  onChange = () => {},
  Fill,
  className,
  alwaysDisplayButton,
  gallery = false,
  value = gallery ? [] : {},
  accept = 'image/*',
  allowedTypes = ['image'],
  ...props
}, ref) => {
  const { url } = gallery ? {} : value;

  const { createErrorNotice } = useDispatch(noticesStore);

  const onUploadError = (message) => {
    createErrorNotice(message, { type: 'snackbar' });
  };

  const [openGallery, setOpenGallery] = useState({ addToGallery: false, open: false });
  const r = useRef();

  useEffect(() => {
    if (openGallery.open && r.current) {
      r.current();
      setOpenGallery({ open: false });
    }
  }, [openGallery]);

  const placeholder = (content) => (
    <>
      {!!url && (
      <div
        {...props}
        className={classnames(
          className,
          { 'has-image': !!url },
          { 'show-button': !!alwaysDisplayButton },
          'xd-media-placeholder',
          'components-placeholder',
          'xd-image',
        )}
        ref={ref}
      >
        <img src={url} alt="" />
          {content}
      </div>
      )}
      {!url && (
      <div
        {...props}
        className={classnames(className, 'xd-image')}
        ref={ref}
      >
        <Placeholder
          withIllustration
          className={classnames('xd-media-placeholder', { 'show-button': !!alwaysDisplayButton })}
        >
          { content }
        </Placeholder>
      </div>
      )}
    </>
  );

  let pickImageLabel = !url ? 'Select Image' : 'Replace Image';
  if (gallery) {
    pickImageLabel = !value.filter(({ id }) => !!id).length ? 'Select Images' : 'Add to Gallery';
  }

  return (
    <MediaPlaceholder
      onSelect={(selection) => {
        if (gallery) {
          onChange(selection.map(({ url, id }) => ({ url, id })));
        } else {
          const { url, id } = selection;
          onChange({ url, id });
        }
      }}
      accept={accept}
      allowedTypes={allowedTypes}
      value={value}
      onError={onUploadError}
      placeholder={placeholder}
      multiple={gallery}
      gallery={gallery}
      addToGallery={openGallery?.addToGallery}
      mediaLibraryButton={({ open }) => {
        r.current = open;
        return (
          <>
            {Fill && (
            <Fill>
              <div className="xd-image-control__button">
                <Button
                  onClick={() => setOpenGallery({ addToGallery: true, open: true })}
                  variant="secondary"
                >
                  {pickImageLabel}
                </Button>
              </div>
              {url && !gallery
            && (
              <div className="xd-image-control__button">
                <Button
                  variant="link"
                  isDestructive
                  onClick={() => onChange(gallery ? [] : {})}
                >
                  Remove Image
                </Button>
              </div>
            )}
              {!!gallery && !!value.filter(({ id }) => !!id).length
            && (
              <div className="xd-image-control__button">
                <Button
                  variant="secondary"
                  onClick={() => setOpenGallery({ addToGallery: false, open: true })}
                >
                  Edit Gallery
                </Button>
              </div>
            )}
              {gallery && !!value.filter(({ id }) => !!id).length && (
              <div className="xd-image-control__clear">
                <Button
                  variant="link"
                  isDestructive
                  onClick={() => onChange([])}
                >
                  Clear Gallery
                </Button>
              </div>
              )}
            </Fill>
            )}
            <Button
              icon={!url ? upload : edit}
              variant="primary"
              label={gallery ? 'Edit gallery' : 'Select or upload image'}
              showTooltip
              tooltipPosition="top center"
              onClick={() => setOpenGallery({ addToGallery: true, open: true })}
            />
          </>
        );
      }}
    />
  );
});
