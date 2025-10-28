import classnames from 'classnames';
import { createBlock } from '@wordpress/blocks';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { useDispatch, select, useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';

const { xd_settings: settings } = window;
const { registerComponent, components, context } = settings;
const { XDImageControl, XDImage, XDGalleryPreview } = components;
const { useXdBlockContext } = context;
const { getBlocks } = select(blockEditorStore);

function useReplaceInnerBlocks() {
  const { replaceInnerBlocks } = useDispatch(blockEditorStore);
  return replaceInnerBlocks;
}

function useGalleryBlockProps() {
  const blockContext = useXdBlockContext();
  const { clientId, block } = blockContext;
  const { isSelected } = block;
  return { clientId, isSelected };
}

function useInnerBlocks() {
  const { clientId } = useGalleryBlockProps();
  const innerBlocks = getBlocks(clientId);
  return innerBlocks;
}

function XDGalleryControl() {
  const replaceInnerBlocks = useReplaceInnerBlocks();
  const innerBlocks = useInnerBlocks();
  const { clientId } = useGalleryBlockProps();
  return (
    <XDImageControl
      gallery
      label="Select Images"
      value={innerBlocks.map(({ attributes }) => ({
        id: attributes.imageDesktop?.id,
        url: attributes.imageDesktop?.url,
      }))}
      onChange={(selection) => {
        replaceInnerBlocks(
          clientId,
          selection.map(({ id, url }) => {
            const rest = innerBlocks.find(({ attributes }) => attributes.imageDesktop?.id === id)?.attributes || {};
            return createBlock('xd/image', { ...rest, imageDesktop: { id, url } });
          }),
        );
      }}
    />
  );
}

function XDGallery() {
  const replaceInnerBlocks = useReplaceInnerBlocks();
  const innerBlocks = useInnerBlocks();
  const { clientId, isSelected } = useGalleryBlockProps();
  return (
    <div className={classnames({ 'is-selected': isSelected }, 'xd-image-picker')}>
      <XDImage
        gallery
        value={innerBlocks.map(({ attributes }) => ({
          id: attributes.imageDesktop?.id,
          url: attributes.imageDesktop?.url,
        }))}
        onChange={(selection) => {
          replaceInnerBlocks(
            clientId,
            selection.map(({ id, url }) => {
              const rest = innerBlocks.find(({ attributes }) => attributes.imageDesktop?.id === id)?.attributes || {};
              return createBlock('xd/image', { ...rest, imageDesktop: { id, url } });
            }),
          );
        }}
      />
    </div>
  );
}

function XDImageGalleryPreview() {
  const { clientId } = useGalleryBlockProps();
  const innerBlocks = useSelect((select) => {
    const { getBlocks } = select(blockEditorStore);
    return getBlocks(clientId);
  });
  return (
    <XDGalleryPreview
      value={innerBlocks.map(({ attributes }) => ({
        id: attributes.imageDesktop?.id,
        url: attributes.imageDesktop?.url,
      }))}
    />
  );
}

registerComponent('XDGalleryControl', XDGalleryControl);
registerComponent('XDGallery', XDGallery);
registerComponent('XDImageGalleryPreview', XDImageGalleryPreview);
