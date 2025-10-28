/* eslint-disable jsx-a11y/alt-text */
/* eslint-disable react/button-has-type */
import { useRef, useState, useEffect } from '@wordpress/element';
import { useDispatch, useSelect, subscribe } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';

import { rawHandler } from '@wordpress/blocks';

import {
  InspectorControls,
  MediaPlaceholder,
  BlockControls,
  MediaReplaceFlow,
  useBlockProps,
} from '@wordpress/block-editor';

import {
  PanelBody,
  Toolbar,
  ToolbarGroup,
  ToolbarButton,
  TextControl,
  withNotices,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { compose } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';

function Image({
  src, size, onSelect,
}) {
  if (src) {
    return (
      <img
        src={src}
        width={size?.width}
        height={size?.height}
      />
    );
  }
  return (
    <MediaPlaceholder
      onSelect={onSelect}
    />
  );
}

let unssubscribe = () => { };
let savedCaption = '';
let updatedCaption = '';

function ImageEdit({ attributes, setAttributes }) {
  const { imageMobile, imageDesktop } = attributes;

  const [mobileUrl, setMobileUrl] = useState('');
  const [mobileSize, setMobileSize] = useState({});
  const [desktopUrl, setDesktopUrl] = useState('');
  const [desktopSize, setDesktopSize] = useState({});
  const [captionState, setCaptionState] = useState(updatedCaption);

  const setUrl = {
    imageMobile: setMobileUrl,
    imageDesktop: setDesktopUrl,
  };

  const setSize = {
    imageMobile: setMobileSize,
    imageDesktop: setDesktopSize,
  };

  const { isSavingPost, isAutosavingPost } = useSelect(editorStore);
  const { saveEntityRecord } = useDispatch(coreStore);

  const updateCaptionsOnSave = () => {
    unssubscribe();
    unssubscribe = subscribe(() => {
      if (isSavingPost() && !isAutosavingPost()) {
        unssubscribe();
        if (savedCaption !== updatedCaption) {
          saveEntityRecord('postType', 'attachment', { id: imageMobile, caption: updatedCaption });
        }
      }
    });
  };

  const getCaption = (caption) => {
    if (typeof caption === 'string') {
      return caption;
    }
    const captionContent = rawHandler({
      HTML: caption.rendered,
    });
    return captionContent[0]?.attributes?.content;
  };

  const updateCaption = (caption = '') => {
    updatedCaption = caption;
    setCaptionState(caption);
    updateCaptionsOnSave();
  };

  const onSelect = (imageKey, image) => {
    if (
      imageKey === 'imageMobile' && (
        (image?.id && imageMobile !== image.id)
        || savedCaption !== getCaption(image.caption))
    ) {
      const caption = getCaption(image.caption);
      updateCaption(caption);
      savedCaption = caption;
    }
    setAttributes({ [imageKey]: image?.id || null });
    setUrl[imageKey](image?.url);
    setSize[imageKey](image?.size);
  };

  const checkSavedImage = (imageKey) => {
    const id = attributes[imageKey];
    if (id) {
      apiFetch({ url: `/wp-json/wp/v2/media/${id}` })
        .then((image) => {
          const { media_details: size } = image;
          onSelect(imageKey, {
            id: image.id, url: image.source_url, caption: image.caption, size,
          });
        })
        .catch(({ code }) => {
          if (code && code === 'rest_post_invalid_id') {
            setAttributes({ [imageKey]: null });
          }
        });
    }
  };

  useEffect(() => {
    checkSavedImage('imageMobile');
    checkSavedImage('imageDesktop');
  }, []);

  return (
    <>
      <div {...useBlockProps()}>
        <Image
          size={mobileSize}
          src={mobileUrl}
          onSelect={(image) => onSelect('imageMobile', image)}
        />
      </div>
      <BlockControls>
        <ToolbarGroup>
          <MediaReplaceFlow
            name="Update"
            mediaId={imageMobile}
            mediaURL={mobileUrl}
            allowedTypes={['image']}
            accept="image/*"
            onSelect={(image) => onSelect('imageMobile', image)}
          />
        </ToolbarGroup>
      </BlockControls>
      <InspectorControls>
        <PanelBody
          title={__('Caption')}
          initialOpen
        >
          <TextControl
            value={captionState}
            onChange={(caption) => updateCaption(caption)}
          />
        </PanelBody>
        <PanelBody
          title={__('Large Image')}
          initialOpen
        >
          {desktopUrl
            && (
              <Toolbar label="Update image">
                <MediaReplaceFlow
                  name="Update"
                  mediaId={imageDesktop}
                  mediaURL={desktopUrl}
                  allowedTypes={['image']}
                  accept="image/*"
                  onSelect={(image) => onSelect('imageDesktop', image)}
                />
                <ToolbarButton
                  label="Remove"
                  onClick={() => onSelect('imageDesktop', null)}
                >
                  Remove
                </ToolbarButton>

              </Toolbar>
            )}
          <Image
            src={desktopUrl}
            size={desktopSize}
            onSelect={(image) => onSelect('imageDesktop', image)}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}

export default compose([
  withNotices,
])(ImageEdit);
