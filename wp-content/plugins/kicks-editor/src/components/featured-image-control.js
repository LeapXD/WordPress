import {
  BaseControl, createSlotFill, PanelRow,
} from '@wordpress/components';
import { select, dispatch } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { store as coreStore } from '@wordpress/core-data';
import classNames from 'classnames';
import { XDImage } from '.';

export default function XDFeaturedImageControl({
  onChange, value, label, help, className = '', id: componentId, ...controlProps
}) {
  const { Slot, Fill } = createSlotFill(`${componentId}-image-control`);

  const { getMedia } = select(coreStore);
  const { getEditedPostAttribute } = select(editorStore);
  const featuredImageId = getEditedPostAttribute('featured_media');
  const media = getMedia(featuredImageId, { context: 'view' });
  const { editPost } = dispatch(editorStore);
  const { source_url: url, id } = media ?? {};
  const image = { url, id };

  return (
    <BaseControl
      label={label}
      help={help}
      className={classNames(className, 'xd-image-control', 'xd-featured-image-control')}
      // __nextHasNoMarginBottom
    >
      <XDImage
        onChange={(image) => {
          onChange(image);
          editPost({ featured_media: image?.id ?? 0 });
        }}
        value={image}
        Fill={Fill}
        alwaysDisplayButton
        {...controlProps}
      />
      <PanelRow>
        <Slot />
      </PanelRow>
    </BaseControl>
  );
}
