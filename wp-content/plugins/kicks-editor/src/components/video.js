/* eslint-disable jsx-a11y/media-has-caption */
import { MediaUpload } from '@wordpress/block-editor';
import { useEffect, useRef, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { store as coreStore } from '@wordpress/core-data';
import { Button, PanelRow, Notice } from '@wordpress/components';
import themeVersionCompare from '../utils/theme-version-compare';

export default function XDVideo({
  video = {},
  isPreview,
  allowedTypes = ['video/mp4', 'video/webm'],
  onChange = () => {},
}) {
  const {
    id,
    url,
    maxWidth = null,
    maxFileSize = null,
    portrait = false,
  } = video;

  const videoRef = useRef();
  const { createErrorNotice } = useDispatch(noticesStore);

  const [errorMessages, setErrorMessages] = useState([]);
  const [warningMessages, setWarningMessages] = useState([]);

  const checkVideo = () => {
    const { videoWidth, videoHeight } = videoRef.current ?? {};
    if (themeVersionCompare('>=', '2.5.3')) {
      if (portrait && videoWidth > videoHeight) {
        setErrorMessages([...errorMessages, 'This video is in landscape orientation. Please upload a video in portrait orientation.']);
      }
      if (maxWidth && videoWidth > maxWidth) {
        setErrorMessages([...errorMessages, `This video is too wide. Please upload a video that is ${maxWidth}px wide or less.`]);
      }
    }
  };

  useEffect(() => {
    if (videoRef.current) {
      videoRef.current.addEventListener('canplay', checkVideo);
    }
    return () => {
      if (videoRef.current) {
        videoRef.current.removeEventListener('canplay', checkVideo);
      }
    };
  }, [videoRef.current]);

  const mediaItem = useSelect((select) => {
    const { getMediaItems } = select(coreStore);
    return getMediaItems({ include: id });
  });

  useEffect(() => {
    if (mediaItem && !mediaItem.length) {
      if (url) {
        onChange({});
        createErrorNotice(`${url} was removed from the media library. Video removed.`, { type: 'snackbar' });
      }
    }
  }, [mediaItem]);

  const onUploadError = (message) => {
    createErrorNotice(message, { type: 'snackbar' });
  };

  const display = ({ open }) => (
    <>
      <div>
        {(!!id || isPreview) && <video ref={videoRef} src={url} controls />}
      </div>
      {!!warningMessages.length && url && warningMessages.map((message) => <Notice status="warning">{message}</Notice>)}
      {!!errorMessages.length && url && errorMessages.map((message) => <Notice status="error" isDismissible={false}>{message}</Notice>)}
      {!isPreview && (
      <PanelRow>
        {!id && <Button variant="secondary" onClick={open}>Select Video</Button>}
        {!!id && <Button variant="secondary" onClick={open}>Replace Video</Button>}
        {!!id && <Button variant="link" isDestructive onClick={() => { onChange({}); }}>Remove Video</Button>}
      </PanelRow>
      )}
    </>
  );

  return (
    <MediaUpload
      value={video}
      onSelect={({
        id, url, width, height, filesizeInBytes,
      }) => {
        const orientation = width > height ? 'landscape' : 'portrait';
        if (maxFileSize && filesizeInBytes > maxFileSize) {
          const humanReadableMaxFileSize = `${Math.round(maxFileSize / 1000000)}MB`;
          setWarningMessages([...warningMessages, `This video is over ${humanReadableMaxFileSize} and may cause performance issues.`]);
        }
        if (themeVersionCompare('>=', '2.5.3')) {
          onChange({
            id,
            url,
            width,
            height,
            orientation,
          });
        } else {
          onChange({ id, url });
        }
        setErrorMessages([]);
      }}
      allowedTypes={allowedTypes}
      onError={onUploadError}
      render={display}
    />
  );
}
