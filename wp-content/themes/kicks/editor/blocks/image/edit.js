import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useRef, useState } from '@wordpress/element';
import { compose, useMergeRefs } from '@wordpress/compose';
import classnames from 'classnames';
import { ButtonGroup, createSlotFill, PanelBody, withFilters, withNotices } from '@wordpress/components';

const { xd_settings: xdSettings } = window;
const { context: xdBlockContext, components } = xdSettings;
const { useXdBlockContext } = xdBlockContext;
const { XDImage } = components;

function Edit(props) {
  const {
    attributes,
    setAttributes,
    className,
    clientId,
    style,
    innerBlocksClassName,
    innerBlocksSettings = {},
  } = props;

  const { wrap = false } = innerBlocksSettings;
  const wrapper = typeof wrap === 'object' ? wrap : {};

  function getWrapperClassnames(wrapper) {
    let classNames = [];
    let toRemove = [];
    wrapper.forEach((wrap = '') => {
      if (wrap !== false) {
        if (typeof wrap === 'object') {
          classNames = [...classNames, ...wrap.add?.split(' ') ?? []];
          toRemove = [...toRemove, ...wrap.remove?.split(' ') ?? []];
        } else {
          classNames = [...classNames, ...wrap.split(' ')];
        }
      }
    });
    return classnames(classNames.filter((className) => !toRemove.includes(className)));
  }

  const blockClass = getWrapperClassnames(
    [wrapper.block, wrapper.editorBlock, className],
  );

  const { Slot, Fill } = createSlotFill(`${clientId}-image-desktop-toolbar-image-button`);

  const { imageDesktop } = attributes;

  const [popoverAnchor, setPopoverAnchor] = useState(null);
  const ref = useRef();
  const { contextRef } = useXdBlockContext();
  contextRef.current = { ref, popoverAnchor };

  const blockProps = useBlockProps({
    className: blockClass,
    style,
    ref: useMergeRefs([setPopoverAnchor, ref]),
    onChange: (imageDesktop) => setAttributes({ imageDesktop }),
    onKeyDown: ref.current?.onKeyDown,
  });

  return (
    <XDImage
      {...blockProps}
      value={imageDesktop}
      Fill={Fill}
    />
  );
}

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(Edit);
