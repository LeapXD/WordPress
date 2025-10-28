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
  const { 'xd/margin': margin = {} } = moduleDefaultSettings;
  if (getBlockSupport(settings, 'xd/margin')) {
    settings.attributes = {
      ...margin,
      ...settings.attributes,
    };
  }
  return settings;
};

const withMarginControls = createHigherOrderComponent((BlockEdit) => function (props) {
  const {
    name, attributes, setAttributes, className,
  } = props;
  const { marginTop, marginBottom } = attributes;

  if (!getBlockSupport(name, 'xd/margin')) {
    return (
      <BlockEdit {...props} />
    );
  }

  if (!getBlockSupport(name, 'xd/margin-controls')) {
    return (
      <BlockEdit {...props} className={classNames(className, marginTop, marginBottom)} />
    );
  }

  return (
    <>
      <BlockEdit {...props} className={classNames(className, marginTop, marginBottom)} />
      <InspectorControls>
        <PanelBody
          title={__('Margin')}
          initialOpen={false}
        >
          <SelectControl
            label="Top"
            value={marginTop}
            options={[
              { label: 'None', value: '' },
              { label: 'Small', value: 'XD-mt--sm' },
              { label: 'Large', value: 'XD-mt--lg' },
            ]}
            onChange={(marginTop) => setAttributes({ marginTop })}
          />
          <SelectControl
            label="Bottom"
            value={marginBottom}
            options={[
              { label: 'None', value: '' },
              { label: 'Small', value: 'XD-mb--sm' },
              { label: 'Large', value: 'XD-mb--lg' },
            ]}
            onChange={(marginBottom) => setAttributes({ marginBottom })}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}, 'withMarginControl');

export default {
  name: 'xd/margin',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-margin-attributes', addAttributes);
    addFilter('editor.BlockEdit', 'xd-module/with-margin-control', withMarginControls);
  },
};
