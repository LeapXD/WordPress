import classnames from 'classnames';

import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls, MediaPlaceholder } from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';

const { apiFetch } = wp;

const addAttributes = (settings) => {
  if (getBlockSupport(settings, 'xd/background-image')) {
    settings.attributes = {
      ...settings.attributes,
      imageMobile: {
        type: 'object',
        default: {},
      },
      imageDesktop: {
        type: 'object',
        default: {},
      },
      positionFixed: {
        type: 'boolean',
        default: false,
      },
    };
  }
  return settings;
};

function Image({ image, onRemove, onSelect }) {
  if (typeof image !== 'undefined' && image.id) {
    return (
      <div>
        <img src={image.url} />
        <div>
          <button
            className="button button-large"
            onClick={onRemove}
          >
            Remove
          </button>
        </div>
      </div>
    );
  }
  return (
    <div>
      <MediaPlaceholder
        onSelect={onSelect}
      />
    </div>
  );
}

const withBackgroundImageStyle = (settings) => {
  const existingGetEditWrapperProps = settings.getEditWrapperProps;

  settings.getEditWrapperProps = (attributes) => {
    let props = {};

    if (existingGetEditWrapperProps) {
      props = existingGetEditWrapperProps(attributes);
    }

    if (!getBlockSupport(settings.name, 'xd/background-image')) {
      return props;
    }

    const { imageMobile } = attributes;
    const { url } = imageMobile;
    if (url) {
      props.style = {
        background: `linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5)), url(${url})`,
        backgroundSize: 'cover',
        color: 'white',
      };
    }
    return props;
  };

  return settings;
};

const withBackgroundImage = createHigherOrderComponent((BlockEdit) => function (props) {
  if (!getBlockSupport(props.name, 'xd/background-image')) {
    return (
      <BlockEdit {...props} />
    );
  }

  const { attributes, setAttributes, className } = props;
  const { imageMobile, imageDesktop, positionFixed } = attributes;

  const checkSavedImage = (imageKey) => {
    const { id } = attributes[imageKey];
    useEffect(() => {
      if (id) {
        apiFetch({ url: `/wp-json/wp/v2/media/${id}` })
          .catch(({ code }) => {
            if (code && code === 'rest_post_invalid_id') {
              setAttributes({ [imageKey]: {} });
            }
          });
      }
    }, []);
  };

  // Remove the images if they were deleted in the media library.
  checkSavedImage('imageMobile');
  checkSavedImage('imageDesktop');

  return (
    <>
      <BlockEdit {...props} />
      <InspectorControls>
        <PanelBody
          title={__('Background Image')}
          initialOpen={false}
        >
          <h2>Primary</h2>
          <Image
            onRemove={() => setAttributes({ imageMobile: {} })}
            onSelect={(image) => setAttributes({ imageMobile: { id: image.id, url: image.url } })}
            image={imageMobile}
          />
          <h2>Large</h2>
          <Image
            onRemove={() => setAttributes({ imageDesktop: {} })}
            onSelect={(image) => setAttributes({ imageDesktop: { id: image.id, url: image.url } })}
            image={imageDesktop}
          />
          <h2>Position Fixed</h2>
          <CheckboxControl
            checked={positionFixed}
            onChange={(positionFixed) => setAttributes({ positionFixed })}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}, 'withBackgroundImage');

const withBackgroundImageClass = createHigherOrderComponent((BlockListBlock) => function (props) {
  if (!getBlockSupport(props.name, 'xd/background-image')) {
    return (
      <BlockListBlock {...props} />
    );
  }

  const { attributes } = props;
  const { imageMobile } = attributes;
  const { url } = imageMobile;

  return (
    <BlockListBlock {...props} className={classnames(props.className, { 'has-background-image': url })} />
  );
}, 'withBackgroundImage');

export default {
  name: 'xd/background-image',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-image-attributes', addAttributes);
    addFilter('blocks.registerBlockType', 'xd-module/with-image-style', withBackgroundImageStyle);
    addFilter('editor.BlockEdit', 'xd-module/with-backgrond-image', withBackgroundImage);
    addFilter('editor.BlockListBlock', 'xd-module/with-image', withBackgroundImageClass);
  },
};
