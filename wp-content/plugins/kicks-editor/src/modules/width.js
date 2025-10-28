import { InspectorControls } from '@wordpress/block-editor';
import { getBlockSupport } from '@wordpress/blocks';
import { PanelBody, SelectControl } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';

const addAttributes = (settings) => {
  if (getBlockSupport(settings, 'xd/width')) {
    settings.attributes = {
      width: {
        type: 'string',
        default: '',
      },
      ...settings.attributes,
    };
  }
  return settings;
};

const withWidthControls = createHigherOrderComponent((BlockEdit) => function (props) {
  const { attributes, setAttributes, name } = props;
  const { width } = attributes;

  if (!getBlockSupport(name, 'xd/width')) {
    return (
      <BlockEdit {...props} />
    );
  }

  const widthControls = getBlockSupport(name, 'xd/width-controls');

  if (!Array.isArray(widthControls)) {
    return (
      <BlockEdit {...props} />
    );
  }

  return (
    <>
      <BlockEdit {...props} />
      <InspectorControls>
        <PanelBody
          title={__('Container Width')}
          initialOpen={false}
        >
          <SelectControl
            label="Width"
            value={width}
            options={widthControls.map((option) => ({ label: option.name, value: option.width }))}
            onChange={(width) => setAttributes({ width })}
          />
        </PanelBody>
      </InspectorControls>
    </>
  );
}, 'withWidthControl');

const withWidthClass = createHigherOrderComponent((BlockList) => function (props) {
  const { attributes, name, innerBlocksClassName } = props;
  const { width } = attributes;
  if (!getBlockSupport(name, 'xd/width')) {
    return (
      <BlockList {...props} />
    );
  }

  return (
    <BlockList
      {...props}
      innerBlocksClassName={classNames(innerBlocksClassName, width)}
    />
  );
}, 'withWidthClass');

export default {
  name: 'xd/width',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-width-attributes', addAttributes);
    addFilter('editor.BlockEdit', 'xd-module/with-width-control', withWidthControls);
    addFilter('xd.innerBlocksClassName', 'xd-module/with-width-class', withWidthClass);
  },
};
