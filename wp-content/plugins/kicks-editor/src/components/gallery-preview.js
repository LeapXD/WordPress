import { BaseControl } from '@wordpress/components';

export default function XDGalleryPreview({
  className,
  label,
  help,
  gallery = false,
  value = gallery ? [] : {},
  ...props
}) {
  if (!value.length) {
    return null;
  }
  return (
    <BaseControl
      label={label ?? 'Gallery Preview'}
      className={className}
      help={help}
    >
      <div {...props} className="xd-image-gallery-preview">
        { value.map(({ url: imageUrl, id: imageId }) => (
          <div className="xd-image-preview-item" key={imageId}>
            <img src={imageUrl} alt="" />
          </div>
        ))}
      </div>
    </BaseControl>
  );
}
