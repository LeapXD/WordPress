import { useEffect, useRef, useState } from '@wordpress/element';
import {
  Popover, BaseControl, Button, PanelRow, TextControl, ExternalLink, Card, CardBody,
} from '@wordpress/components';
import { LinkControl } from '@wordpress/block-editor';
import classNames from 'classnames';
import { __ } from '@wordpress/i18n';

const NEW_TAB_REL = 'noreferrer noopener';

export default function XDLinkControl({
  onChange, defaultTitle, value, label, help, className = '', showLinkTextControl = true, ...controlProps
}) {
  const {
    target,
    rel,
    url,
    title,
    type,
    entityId,
  } = value;
  const opensInNewTab = target === '_blank';
  const [isEditingURL, setIsEditingURL] = useState(false);

  const linkRef = useRef();
  const editLink = entityId && url === '#' ? `/wp-admin/post.php?post=${entityId}&action=edit` : null;

  function onToggleOpenInNewTab(val) {
    const newLinkTarget = val ? '_blank' : undefined;
    let updatedRel = rel;
    if (newLinkTarget && !rel) {
      updatedRel = NEW_TAB_REL;
    } else if (!newLinkTarget && rel === NEW_TAB_REL) {
      updatedRel = undefined;
    }

    onChange({
      ...value,
      target: newLinkTarget,
      rel: updatedRel,
    });
  }

  useEffect(() => {
    if (!isEditingURL) {
      linkRef.current?.focus();
      linkRef.current?.select();
    }
  }, [isEditingURL]);

  return (
    <BaseControl
      help={help}
      label={label}
      className={classNames(className, 'xd-link-control')}
    >
      {(!!url || !!editLink) && (
      <PanelRow>
        <ExternalLink href={editLink || url}>
          {editLink ? `Edit ${type}` : title}
        </ExternalLink>
      </PanelRow>
      )}
      {!!url && !!showLinkTextControl && (
        <TextControl
          label="Link Text"
          value={title ?? ''}
          onChange={(title) => onChange({ ...value, title })}
          ref={linkRef}
        />
      )}
      <PanelRow>
        <Button
          onClick={() => setIsEditingURL(true)}
          variant="secondary"
        >
          {url ? 'Edit Link' : 'Insert Link'}
        </Button>
        {!!url
            && (
            <Button
              variant="link"
              isDestructive
              onClick={() => onChange({})}
            >
              Remove Link
            </Button>
            )}
      </PanelRow>
      {!!isEditingURL && (
      <Popover
        onClose={() => {
          setIsEditingURL(false);
        }}
      >
        <LinkControl
          className="wp-block-navigation-link__inline-link-input"
          value={{ url, opensInNewTab }}
          onChange={({
            id: entityId,
            kind: newKind,
            opensInNewTab: newOpensInNewTab,
            type: newType,
            url: newURL = '',
          }) => {
            onChange({
              id: entityId,
              kind: newKind,
              opensInNewTab: newOpensInNewTab,
              title: showLinkTextControl && title ? title : newURL,
              type: newType,
              url: newURL,
              entityId,
            });
            if (opensInNewTab !== newOpensInNewTab) {
              onToggleOpenInNewTab(newOpensInNewTab);
            }
            setIsEditingURL(false);
          }}
          onRemove={() => {
            onChange({});
          }}
          forceIsEditingLink={isEditingURL}
          renderControlBottom={() => null}
          {...controlProps}
        />
      </Popover>
      )}
    </BaseControl>
  );
}
