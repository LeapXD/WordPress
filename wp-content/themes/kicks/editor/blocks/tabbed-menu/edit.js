/* eslint-disable no-shadow */
/* eslint-disable react/no-danger */
/* eslint-disable react/react-in-jsx-scope */
/* eslint-disable react/jsx-filename-extension */

import {
  withNotices,
  withFilters,
  Button,
  TextControl,
  PanelBody,
  PanelRow,
  NavigableMenu,
} from '@wordpress/components';
import { compose, useInstanceId } from '@wordpress/compose';
import { useSelect, useDispatch } from '@wordpress/data';
import {
  useInnerBlocksProps,
  useBlockProps,
  store as blockEditorStore,
  InspectorControls,
} from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { useState, useEffect } from '@wordpress/element';
import classnames from 'classnames';
import { partial } from 'lodash';

function TabButton({
  tabId, onClick, children, selected, ...rest
}) {
  return (
    <Button
      role="tab"
      tabIndex={selected ? null : -1}
      aria-selected={selected}
      id={tabId}
      onClick={onClick}
      {...rest}
    >
      {children}
    </Button>
  );
}

function TabPanel({
  className,
  tabs,
  orientation = 'horizontal',
  selected,
  onSelect = () => { },
}) {
  const instanceId = useInstanceId(TabPanel, 'tab-panel');
  const handleClick = (tabKey) => {
    onSelect(tabKey);
  };
  const onNavigate = (childIndex, child) => {
    child.click();
  };

  return (
    <div className={className}>
      <NavigableMenu
        role="tablist"
        orientation={orientation}
        onNavigate={onNavigate}
        className="components-tab-panel__tabs"
      >
        {tabs.map((tab) => (
          <TabButton
            className={classnames(
              'components-tab-panel__tabs-item',
              tab.className,
              {
                'is-active': tab.name === selected,
              },
            )}
            tabId={`${instanceId}-${tab.name}`}
            aria-controls={`${instanceId}-${tab.name}-view`}
            selected={tab.name === selected}
            key={tab.name}
            onClick={partial(handleClick, tab.name)}
          >
            {tab.title}
          </TabButton>
        ))}
      </NavigableMenu>
    </div>
  );
}

export default compose([
  withNotices,
  withFilters('xd.innerBlocksClassName'),
  withFilters('xd.innerBlocksSettings'),
])(({
  clientId,
  attributes,
  setAttributes,
  className,
  innerBlocksClassName,
  innerBlocksSettings,
  preInnerBlocks,
  preInnerBlocksClassName,
  style,
}) => {
  const { tabs } = attributes;

  const blockProps = useBlockProps({ className, style });
  const innerBlocksProps = useInnerBlocksProps({ className: innerBlocksClassName }, {
    ...innerBlocksSettings,
    templateLock: 'all',
    template: null,
  });
  const { allowedBlocks } = innerBlocksSettings;
  const [blockType] = allowedBlocks;
  const blocks = useSelect((select) => {
    const { getBlocks } = select(blockEditorStore);
    return getBlocks(clientId);
  });
  const { replaceInnerBlocks } = useDispatch(blockEditorStore);
  const [title, setTitle] = useState('');
  const [activeTab, setActiveTab] = useState(null);

  useEffect(() => {
    if (tabs.length) {
      setActiveTab(tabs[0].name);
    }
  }, []);

  useEffect(() => {
    if (activeTab !== null && tabs.length !== blocks.length) {
      const { name, title } = tabs.find(({ name }) => activeTab === name);
      const tabIndex = tabs.findIndex(({ name }) => activeTab === name);
      tabs[tabIndex] = { name, title };
      setAttributes({ tabs: [...tabs] });
    }
  }, [blocks, activeTab]);

  useEffect(() => {
    if (blocks.length < tabs.length) {
      replaceInnerBlocks(clientId, [
        ...blocks.map(
          ({ name, attributes, innerBlocks }) => createBlock(name, attributes, innerBlocks),
        ),
        createBlock(blockType)]);
    }
  }, [tabs]);

  return (
    <div {...blockProps}>
      <style dangerouslySetInnerHTML={{
        __html: `
      #block-${clientId} 
        > .block-editor-block-list__layout 
        > .wp-block{
          display:none
        }

      #block-${clientId} 
        > .block-editor-block-list__layout 
        > .wp-block:nth-child(${tabs.findIndex(({ name }) => activeTab === name) + 1}) {
          display:block
        }`,
      }}
      />

      <InspectorControls>
        <PanelBody
          title="Tabs"
        >

          <TextControl
            value={title}
            placeholder="Type tab name"
            onChange={(title) => setTitle(title)}
            autoComplete="off"
          />
          <PanelRow>
            <Button
              variant="primary"
              disabled={!title}
              onClick={() => {
                setTitle('');
                const tabName = title.replace(/[^\S]/g, '-');
                setAttributes({
                  tabs: [...tabs, {
                    name: tabName,
                    title,
                    ids: [],
                  }],
                });
                setActiveTab(tabName);
              }}
            >
              Add New Tab
            </Button>
          </PanelRow>
          <PanelRow>
            <Button
              variant="primary"
              disabled={!title}
              onClick={() => {
                setTitle('');
                const tabIndex = tabs.findIndex(({ name }) => activeTab === name);
                const tabName = title.replace(/[^\S]/g, '-');
                tabs.splice(tabIndex, 1, {
                  name: tabName,
                  title,
                  ids: [],
                });
                setAttributes({
                  tabs: [...tabs],
                });
                setActiveTab(tabName);
              }}
            >
              Rename Tab
            </Button>
          </PanelRow>
          <PanelRow>
            <Button
              variant="secondary"
              onClick={() => {
                setActiveTab(tabs[0]?.name ?? null);
                const tabIndex = tabs.findIndex(({ name }) => activeTab === name);
                tabs.splice(tabIndex, 1);
                blocks.splice(tabIndex, 1);
                setAttributes({ tabs: [...tabs] });
                replaceInnerBlocks(clientId, [
                  ...blocks.map(
                    (
                      { name, attributes, innerBlocks },
                    ) => createBlock(name, attributes, innerBlocks),
                  )]);
              }}
            >
              Delete Tab
            </Button>
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <div className={preInnerBlocksClassName}>
        {preInnerBlocks}
      </div>
      <TabPanel
        tabs={tabs}
        initialTabName={(tabs.find(({ name }) => activeTab === name))?.name}
        onSelect={(tab) => setActiveTab(tab)}
        selected={activeTab}
      >
        {() => null}
      </TabPanel>
      <div {...innerBlocksProps} />
    </div>
  );
});
