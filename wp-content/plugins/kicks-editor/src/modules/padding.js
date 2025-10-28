import classNames from 'classnames';
import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const addAttributes = (settings) => {
  const { xd_settings: xdSettings = {} } = window;
  const { module_default_settings: moduleDefaultSettings = {} } = xdSettings;
  const { 'xd/padding': padding = {} } = moduleDefaultSettings;
  if (getBlockSupport(settings, 'xd/padding')) {
    settings.attributes = {
      ...padding,
      ...settings.attributes,
    };
  }
  return settings;
};

const withPaddingControls = createHigherOrderComponent((BlockEdit) => function (props) {
  const { name, attributes, setAttributes } = props;
  const { paddingTop, paddingBottom } = attributes;

  if (!getBlockSupport(name, 'xd/padding-controls')) {
    return (
      <BlockEdit {...props} />
    );
  }

  return (
    <>
      <BlockEdit {...props} />
      <InspectorControls>
        <PanelBody
          title={__('Padding')}
          initialOpen={false}
        >
          <SelectControl
            label="Top"
            value={paddingTop}
            options={[
              { label: 'None', value: '' },
              { label: 'Small', value: 'XD-pt--sm' },
              { label: 'Large', value: 'XD-pt--lg' },
            ]}
            onChange={(paddingTop) => setAttributes({ paddingTop })}
          />
          <SelectControl
            label="Bottom"
            value={paddingBottom}
            options={[
              { label: 'None', value: '' },
              { label: 'Small', value: 'XD-pb--sm' },
              { label: 'Large', value: 'XD-pb--lg' },
            ]}
            onChange={(paddingBottom) => setAttributes({ paddingBottom })}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}, 'withPaddingControl');

const withPaddingClass = createHigherOrderComponent((BlockList) => function (props) {
  const { attributes, name, innerBlocksClassName } = props;
  const { paddingTop, paddingBottom } = attributes;
  if (!getBlockSupport(name, 'xd/padding')) {
    return (
      <BlockList {...props} />
    );
  }

  return (
    <BlockList
      {...props}
      innerBlocksClassName={classNames(innerBlocksClassName, paddingTop, paddingBottom)}
    />
  );
}, 'withWidthClass');

export default {
  name: 'xd/padding',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-padding-attributes', addAttributes);
    addFilter('editor.BlockEdit', 'xd-module/with-padding-control', withPaddingControls);
    addFilter('xd.innerBlocksClassName', 'xd-module/with-padding-class', withPaddingClass);
  },
};
