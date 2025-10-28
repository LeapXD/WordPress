import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { useRef, useState } from '@wordpress/element';
import { useMergeRefs } from '@wordpress/compose';
import classnames from 'classnames';

const { xd_settings: xdSettings } = window;
const { context: xdBlockContext } = xdSettings;
const { useXdBlockContext } = xdBlockContext;

function ButtonEdit(props) {
  const {
    attributes,
    setAttributes,
    className,
    onReplace,
    mergeBlocks,
    style,
  } = props;
  const {
    placeholder,
    text,
  } = attributes;

  const richTextRef = useRef();
  const ref = useRef();

  const { contextRef } = useXdBlockContext();
  const [popoverAnchor, setPopoverAnchor] = useState(null);

  contextRef.current = {
    ref, popoverAnchor,
  };

  const { className: blockClassName, ...blockProps } = useBlockProps({
    ref: useMergeRefs([setPopoverAnchor, ref]),
    onKeyDown: ref.current?.onKeyDown,
  });

  return (
    <div {...blockProps} className={classnames(blockClassName, className)} style={style}>
      <RichText
        ref={richTextRef}
        aria-label={__('Button text')}
        placeholder={placeholder || __('Add text…')}
        value={text}
        onChange={(value) => setAttributes({ text: value })}
        withoutInteractiveFormatting
        className="xd-button__link"
        // onSplit={(value) => createBlock('xd/button', {
        //   ...attributes,
        //   text: value,
        // })}
        onReplace={onReplace}
        onMerge={mergeBlocks}
        identifier="text"
      />
    </div>

  );
}

export default ButtonEdit;
