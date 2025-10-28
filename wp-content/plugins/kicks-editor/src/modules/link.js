import { __ } from '@wordpress/i18n';
import { useRef, useState, useCallback } from '@wordpress/element';
import {
  ToolbarButton,
  Button,
  ToggleControl,
  TextControl,
  ToolbarGroup,
} from '@wordpress/components';
import { link as linkIcon, close } from '@wordpress/icons';
import {
  URLPopover,
  BlockControls,
  store as blockEditorStore,
} from '@wordpress/block-editor';
import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { useSelect } from '@wordpress/data';

const NEW_TAB_REL = ['noreferrer', 'noopener'];

function URLInputUI({
  onChangeUrl,
  url,
  linkTarget,
  linkClass,
  rel,
}) {
  const [isOpen, setIsOpen] = useState(false);
  const openLinkUI = useCallback(() => {
    setIsOpen(true);
  });

  const [isEditingLink, setIsEditingLink] = useState(false);
  const [urlInput, setUrlInput] = useState();

  const autocompleteRef = useRef(null);

  const startEditLink = useCallback(() => {
    setIsEditingLink(true);
  });

  const stopEditLink = useCallback(() => {
    setIsEditingLink(false);
  });

  const closeLinkUI = useCallback(() => {
    setUrlInput(null);
    stopEditLink();
    setIsOpen(false);
  });

  const getUpdatedLinkTargetSettings = (value) => {
    const newLinkTarget = value ? '_blank' : undefined;

    let updatedRel;
    if (newLinkTarget) {
      const rels = (rel ?? '').split(' ');
      NEW_TAB_REL.forEach((relVal) => {
        if (!rels.includes(relVal)) {
          rels.push(relVal);
        }
      });
      updatedRel = rels.join(' ');
    } else {
      const rels = (rel ?? '')
        .split(' ')
        .filter(
          (relVal) => NEW_TAB_REL.includes(relVal) === false,
        );
      updatedRel = rels.length ? rels.join(' ') : undefined;
    }

    return {
      linkTarget: newLinkTarget,
      rel: updatedRel,
    };
  };

  const onFocusOutside = useCallback(() => (event) => {
    // The autocomplete suggestions list renders in a separate popover (in a portal),
    // so onFocusOutside fails to detect that a click on a suggestion occurred in the
    // LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and
    // return to avoid the popover being closed.
    const autocompleteElement = autocompleteRef.current;
    if (
      autocompleteElement
      && autocompleteElement.contains(event.target)
    ) {
      return;
    }
    setIsOpen(false);
    setUrlInput(null);
    stopEditLink();
  });

  const onSubmitLinkChange = useCallback(() => (event) => {
    if (urlInput) {
      onChangeUrl({ href: urlInput });
    }
    stopEditLink();
    setUrlInput(null);
    event.preventDefault();
  });

  const onLinkRemove = useCallback(() => {
    onChangeUrl({ href: '' });
  });

  const onSetNewTab = (value) => {
    const updatedLinkTarget = getUpdatedLinkTargetSettings(value);
    onChangeUrl(updatedLinkTarget);
  };

  const onSetLinkRel = (value) => {
    onChangeUrl({ rel: value });
  };

  const onSetLinkClass = (value) => {
    onChangeUrl({ linkClass: value });
  };

  const advancedOptions = (
    <>
      <ToggleControl
        label={__('Open in new tab')}
        onChange={onSetNewTab}
        checked={linkTarget === '_blank'}
      />
      <TextControl
        label={__('Link Rel')}
        value={rel ?? ''}
        onChange={onSetLinkRel}
      />
      <TextControl
        label={__('Link CSS Class')}
        value={linkClass || ''}
        onChange={onSetLinkClass}
      />
    </>
  );

  return (
    <>
      <ToolbarButton
        icon={linkIcon}
        className="components-toolbar__control"
        label={url ? __('Edit link') : __('Insert link')}
        aria-expanded={isOpen}
        onClick={openLinkUI}
      />
      {isOpen && (
        <URLPopover
          onFocusOutside={onFocusOutside()}
          onClose={closeLinkUI}
          renderSettings={() => advancedOptions}
        >
          {(!url || isEditingLink) && (
            <URLPopover.LinkEditor
              className="block-editor-format-toolbar__link-container-content"
              value={urlInput}
              onChangeInputValue={setUrlInput}
              onSubmit={onSubmitLinkChange()}
              autocompleteRef={autocompleteRef}
            />
          )}
          {url && !isEditingLink && (
            <>
              <URLPopover.LinkViewer
                className="block-editor-format-toolbar__link-container-content"
                url={url}
                onEditLinkClick={startEditLink}
                urlLabel={url}
              />
              <Button
                icon={close}
                label={__('Remove link')}
                onClick={onLinkRemove}
              />
            </>
          )}
        </URLPopover>
      )}
    </>
  );
}

const addAttributes = (settings) => {
  const { xd_settings: xdSettings = {} } = window;
  const { module_default_settings: moduleDefaultSettings = {} } = xdSettings;
  const { 'xd/link': link = {} } = moduleDefaultSettings;
  if (getBlockSupport(settings, 'xd/link')) {
    settings.attributes = {
      ...link,
      ...settings.attributes,
    };
  }
  return settings;
};

const withLink = createHigherOrderComponent((BlockEdit) => function (props) {
  const {
    clientId,
    name,
    attributes,
    setAttributes,
  } = props;

  const {
    href,
    rel,
    linkClass,
    linkTarget,
  } = attributes;

  const hasParentWithLinkSupport = useSelect((select) => {
    const { getBlockParents, getBlock } = select(blockEditorStore);
    return getBlockParents(clientId)
      .map((id) => getBlock(id))
      .map(({ name }) => getBlockSupport(name, 'xd/link'))
      .filter((_) => _)
      .length > 0;
  });

  if (hasParentWithLinkSupport || !getBlockSupport(name, 'xd/link')) {
    return (
      <BlockEdit {...props} />
    );
  }

  return (
    <>
      <BlockEdit {...props} />
      <BlockControls>
        <ToolbarGroup>
          <URLInputUI
            url={href || ''}
            onChangeUrl={(props) => setAttributes(props)}
            linkTarget={linkTarget}
            linkClass={linkClass}
            rel={rel}
          />
        </ToolbarGroup>
      </BlockControls>
    </>
  );
}, 'withLink');

export default {
  name: 'xd/link',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-link-attributes', addAttributes);
    addFilter('editor.BlockEdit', 'xd-module/with-link', withLink);
  },
};
