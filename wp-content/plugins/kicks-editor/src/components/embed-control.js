import { useEffect, useRef, useState } from '@wordpress/element';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import {
  Popover, BaseControl, Button, PanelRow, TextControl, ExternalLink, Card, CardBody,
  __experimentalHStack as HStack,
  TextareaControl,
} from '@wordpress/components';
import { LinkControl } from '@wordpress/block-editor';
import classNames from 'classnames';
import { __ } from '@wordpress/i18n';

export default function XDEmbedControl({
  onChange,
  defaultTitle,
  value,
  label,
  help,
  className = '',
  showLinkTextControl = true,
  ...controlProps
}) {
  const {
    url,
    html,
    title,
    manuallySet = false,
  } = value;
  const [isEditingURL, setIsEditingURL] = useState(false);
  const [newHtml, setNewHtml] = useState(html);

  const basicHtml = url ? `<iframe src="${url}" width="100%" height="100%" frameborder="0"></iframe>` : '';

  const fetchedHtml = useSelect((select) => {
    if (url) {
      const fetchedHtml = select(coreStore).getEmbedPreview(url)?.html;
      return fetchedHtml || basicHtml;
    }
    return '';
  }, [url]);

  useEffect(() => {
    if (manuallySet) {
      return;
    }
    if (fetchedHtml) {
      setNewHtml(fetchedHtml);
    }
  }, [fetchedHtml, url]);

  const linkRef = useRef();

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
      {(!!url && !!newHtml) && (
      <PanelRow>
        <ExternalLink href={url}>
          {title}
        </ExternalLink>
      </PanelRow>
      )}
      {(!!url || !!showLinkTextControl) && (
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
          {url || newHtml ? 'Edit Embed' : 'Insert Embed'}
        </Button>
        {(!!url || !!newHtml)
            && (
            <Button
              variant="link"
              isDestructive
              onClick={() => {
                onChange({});
                setNewHtml('');
              }}
            >
              Remove Embed
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
          value={{ url }}
          onChange={({ url }) => {
            setNewHtml('');
            onChange({ ...value, url, manuallySet: false });
          }}
          onRemove={() => {
            setNewHtml('');
            onChange({
              url: '', html: '', manuallySet: false, title: value.title,
            });
          }}
          placeholder="Paste URL to embed"
          renderControlBottom={() => (
            <Card>
              <CardBody>
                <TextareaControl
                  label="Embed Code"
                  hideLabelFromVision
                  placeholder="Embed Code"
                  onChange={(html) => {
                    setNewHtml(html);
                  }}
                  rows={8}
                  value={newHtml}
                />
                <HStack justify="right">
                  <Button
                    __next40pxDefaultSize
                    variant="secondary"
                    onClick={() => {
                      setIsEditingURL(false);
                    }}
                  >
                    Cancel
                  </Button>
                  <Button
                    __next40pxDefaultSize
                    disabled={html === newHtml}
                    variant="primary"
                    onClick={() => {
                      onChange({ ...value, html: newHtml, manuallySet: newHtml !== fetchedHtml });
                      setIsEditingURL(false);
                    }}
                  >
                    Save Embed Code
                  </Button>
                </HStack>
              </CardBody>
            </Card>
          )}
          settings={[]}
          {...controlProps}
        />

      </Popover>
      )}
    </BaseControl>
  );
}
