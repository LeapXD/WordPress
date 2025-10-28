import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import {
  ToolbarButton, Popover, Card, CardBody,
} from '@wordpress/components';
import { LinkControl, store as blockEditorStore } from '@wordpress/block-editor';
import { displayShortcut, isKeyboardEvent } from '@wordpress/keycodes';
import { getBlockSupport } from '@wordpress/blocks';
import { link, linkOff } from '@wordpress/icons';
import { dispatch, useSelect } from '@wordpress/data';
import { useXdBlockContext } from './context';

// todo: remove when they remove the experimental status of the LinkControl component
if (!wp.blockEditor.LinkControl && wp.blockEditor.__experimentalLinkControl) {
  wp.blockEditor.LinkControl = wp.blockEditor.__experimentalLinkControl;
}

const NEW_TAB_REL = 'noreferrer noopener';

export default function XDLinkToolBarButton({ block, children, support }) {
  const {
    isSelected,
    attributes,
    setAttributes,
    clientId,
  } = block;

  const {
    target, rel, url, type, entityId,
  } = attributes;

  const editLink = entityId && url?.includes('#') ? `/wp-admin/post.php?post=${entityId}&action=edit` : null;

  function onToggleOpenInNewTab(value) {
    const newLinkTarget = value ? '_blank' : undefined;

    let updatedRel = rel;
    if (newLinkTarget && !rel) {
      updatedRel = NEW_TAB_REL;
    } else if (!newLinkTarget && rel === NEW_TAB_REL) {
      updatedRel = undefined;
    }

    setAttributes({
      target: newLinkTarget,
      rel: updatedRel,
    });
  }

  function onKeyDown(event) {
    if (isKeyboardEvent.primary(event, 'k')) {
      startEditing(event);
    } else if (isKeyboardEvent.primaryShift(event, 'k')) {
      unlink();
      richTextRef?.current?.focus();
    }
  }

  const [isEditingURL, setIsEditingURL] = useState(false);
  const isURLSet = !!url;
  const opensInNewTab = target === '_blank';

  const { contextRef } = useXdBlockContext();
  const blockContext = useXdBlockContext();
  const { state } = blockContext ?? {};

  const { ref, richTextRef, popoverAnchor } = contextRef.current ?? {};

  if (ref?.current) {
    ref.current.onKeyDown = onKeyDown;
  }

  const parentWithLinkSupport = useSelect((select) => {
    const { getBlockParents, getBlock } = select(blockEditorStore);
    const parents = getBlockParents(clientId)
      .map((id) => getBlock(id))
      .filter(({ name }) => getBlockSupport(name, support));

    return parents.length ? parents[0] : null;
  });

  function startEditing(event) {
    event.preventDefault();
    const currentState = state[clientId] ?? {};
    if (parentWithLinkSupport) {
      blockContext.setState({ ...state, [parentWithLinkSupport?.clientId]: { ...currentState, editing: true } });
      dispatch(blockEditorStore).selectBlock(parentWithLinkSupport.clientId);
    } else {
      setIsEditingURL(true);
    }
  }

  function unlink() {
    setAttributes({
      url: undefined,
      target: undefined,
      rel: undefined,
    });
    setIsEditingURL(false);
  }

  useEffect(() => {
    if (state[clientId]?.editing) {
      setIsEditingURL(true);
    }
  }, [state]);

  useEffect(() => {
    if (!isSelected && !state[clientId]?.editing) {
      setIsEditingURL(false);
    }
  }, [isSelected]);

  useEffect(() => {
    const currentState = state[clientId] ?? {};
    if (isEditingURL) {
      blockContext.setState({ ...state, [clientId]: { ...currentState, editing: false } });
    }
  }, [isEditingURL]);

  return (
    <>
      { !isURLSet && (
        <ToolbarButton
          group="group"
          name="link"
          icon={link}
          title={__('Link')}
          shortcut={displayShortcut.primary('k')}
          onClick={startEditing}
        />
      ) }
      { isURLSet && (
        <ToolbarButton
          name="link"
          icon={linkOff}
          title={__('Unlink')}
          shortcut={displayShortcut.primaryShift('k')}
          onClick={unlink}
          isActive
        />
      ) }
      { isSelected && (isEditingURL || isURLSet) && (
      <Popover
        placement="bottom"
        onClose={() => {
          setIsEditingURL(false);
          richTextRef?.current?.focus();
        }}
        anchor={popoverAnchor}
        focusOnMount={isEditingURL ? 'firstElement' : false}
        __unstableSlotName="__unstable-block-tools-after"
        shift
      >
        <LinkControl
          className="wp-block-navigation-link__inline-link-input"
          value={{ url, opensInNewTab }}
          onChange={({
            id: entityId,
            kind: newKind,
            opensInNewTab: newOpensInNewTab,
            title: newTitle,
            type: newType,
            url: newURL = '',
          }) => {
            setAttributes({
              id: entityId,
              kind: newKind,
              opensInNewTab: newOpensInNewTab,
              title: newTitle,
              type: newType,
              url: newURL,
              entityId,
            });

            if (opensInNewTab !== newOpensInNewTab) {
              onToggleOpenInNewTab(newOpensInNewTab);
            }
          }}
          onRemove={() => {
            unlink();
            richTextRef?.current?.focus();
          }}
          forceIsEditingLink={isEditingURL}
          renderControlBottom={() => {
            if (!editLink) {
              return null;
            }
            return (
              <Card>
                <CardBody>
                  <a href={editLink} target="_blank" rel="noreferrer noopener">
                    {__(`Edit ${type}`)}
                  </a>
                </CardBody>
              </Card>
            );
          }}
        />
        {children}
      </Popover>
      ) }
    </>
  );
}
