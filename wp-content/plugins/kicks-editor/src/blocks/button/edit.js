/**
 * External dependencies
 */
import classnames from 'classnames';

/**
* WordPress dependencies
*/
import { __ } from '@wordpress/i18n';
import {
  useCallback, useEffect, useState, useRef,
} from '@wordpress/element';
import {
  TextControl,
  ToolbarButton,
  Popover,
} from '@wordpress/components';
import {
  BlockControls,
  InspectorAdvancedControls,
  RichText,
  useBlockProps,
  LinkControl,
} from '@wordpress/block-editor';
import { displayShortcut, isKeyboardEvent } from '@wordpress/keycodes';
import { link, linkOff } from '@wordpress/icons';
import { createBlock } from '@wordpress/blocks';

// todo: remove when they remove the expermental status of the LinkControl component
if (!wp.blockEditor.LinkControl && wp.blockEditor.__experimentalLinkControl) {
  wp.blockEditor.LinkControl = wp.blockEditor.__experimentalLinkControl;
}

const NEW_TAB_REL = 'noreferrer noopener';

function ButtonEdit(props) {
  const {
    attributes,
    setAttributes,
    className,
    isSelected,
    onReplace,
    mergeBlocks,
  } = props;
  const {
    linkTarget,
    placeholder,
    rel,
    text,
    url,
    width,
  } = attributes;
  const onSetLinkRel = useCallback(
    (value) => {
      setAttributes({ rel: value });
    },
    [setAttributes],
  );

  function onToggleOpenInNewTab(value) {
    const newLinkTarget = value ? '_blank' : undefined;

    let updatedRel = rel;
    if (newLinkTarget && !rel) {
      updatedRel = NEW_TAB_REL;
    } else if (!newLinkTarget && rel === NEW_TAB_REL) {
      updatedRel = undefined;
    }

    setAttributes({
      linkTarget: newLinkTarget,
      rel: updatedRel,
    });
  }

  function setButtonText(newText) {
    // Remove anchor tags from button text content.
    setAttributes({ text: newText.replace(/<\/?a[^>]*>/g, '') });
  }

  const ref = useRef();
  const richTextRef = useRef();

  const [isEditingURL, setIsEditingURL] = useState(false);
  const isURLSet = !!url;
  const opensInNewTab = linkTarget === '_blank';

  function startEditing(event) {
    event.preventDefault();
    setIsEditingURL(true);
  }

  function unlink() {
    setAttributes({
      url: undefined,
      linkTarget: undefined,
      rel: undefined,
    });
    setIsEditingURL(false);
  }
  function onKeyDown(event) {
    if (isKeyboardEvent.primary(event, 'k')) {
      startEditing(event);
    } else if (isKeyboardEvent.primaryShift(event, 'k')) {
      unlink();
      richTextRef.current?.focus();
    }
  }

  useEffect(() => {
    if (!isSelected) {
      setIsEditingURL(false);
    }
  }, [isSelected]);

  const blockProps = useBlockProps({ ref, onKeyDown });

  return (
    <>
      <div
        {...blockProps}
        className={classnames(blockProps.className, {
          [`has-custom-width wp-block-button__width-${width}`]: width,
          'has-custom-font-size': blockProps.style.fontSize,
        })}
      >
        <RichText
          ref={richTextRef}
          aria-label={__('Button text')}
          placeholder={placeholder || __('Add text…')}
          value={text}
          onChange={(value) => setButtonText(value)}
          withoutInteractiveFormatting
          className={classnames(className, 'wp-block-xd-button__link')}
          onSplit={(value) => createBlock('core/button', {
            ...attributes,
            text: value,
          })}
          onReplace={onReplace}
          onMerge={mergeBlocks}
          identifier="text"
        />
      </div>
      <BlockControls group="block">
        { !isURLSet && (
        <ToolbarButton
          name="link"
          icon={link}
          title={__('Link')}
          shortcut={displayShortcut.primary('k')}
          onClick={(...args) => startEditing(...args)}
        />
        ) }
        { isURLSet && (
        <ToolbarButton
          name="link"
          icon={linkOff}
          title={__('Unlink')}
          shortcut={displayShortcut.primaryShift('k')}
          onClick={(...args) => unlink(...args)}
          isActive
        />
        ) }
      </BlockControls>
      { isSelected && (isEditingURL || isURLSet) && (
      <Popover
        position="bottom center"
        onClose={() => {
          setIsEditingURL(false);
          richTextRef.current?.focus();
        }}
        anchorRef={ref?.current}
        focusOnMount={isEditingURL ? 'firstElement' : false}
      >
        <LinkControl
          className="wp-block-navigation-link__inline-link-input"
          value={{ url, opensInNewTab }}
          onChange={({
            url: newURL = '',
            opensInNewTab: newOpensInNewTab,
          }) => {
            setAttributes({ url: newURL });

            if (opensInNewTab !== newOpensInNewTab) {
              onToggleOpenInNewTab(newOpensInNewTab);
            }
          }}
          onRemove={() => {
            unlink();
            richTextRef.current?.focus();
          }}
          forceIsEditingLink={isEditingURL}
        />
      </Popover>
      ) }
      <InspectorAdvancedControls group="advanced">
        <TextControl
          label={__('Link rel')}
          value={rel || ''}
          onChange={onSetLinkRel}
        />
      </InspectorAdvancedControls>
    </>
  );
}

export default ButtonEdit;
