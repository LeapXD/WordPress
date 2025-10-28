import classnames from 'classnames';
import { useBlockProps } from '@wordpress/block-editor';

const { xd_settings: xdSettings } = window;
const { components } = xdSettings;
const { XDImage } = components;

function Edit(props) {
  const {
    attributes,
    setAttributes,
    className,
    style,
  } = props;

  const { imageDesktop } = attributes;

  const blockProps = useBlockProps({
    className: classnames(className, 'xd-image--icon'),
    style,
    onChange: (imageDesktop) => setAttributes({ imageDesktop }),
  });

  return (
    <XDImage
      {...blockProps}
      allowedTypes={['image/svg+xml']}
      value={imageDesktop}
    />
  );
}

export default Edit;
