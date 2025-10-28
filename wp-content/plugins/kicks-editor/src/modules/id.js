import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { select, useSelect } from '@wordpress/data';
import { Fragment, useEffect } from '@wordpress/element';
import { InspectorControls, store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import themeVersionCompare from '../utils/theme-version-compare';

const addAttributes = (settings) => {
  if (getBlockSupport(settings, 'xd/id')) {
    settings.attributes = {
      id: {
        type: 'string',
        default: '',
      },
      ...settings.attributes,
    };
  }
  return settings;
};

const withIdControl = createHigherOrderComponent((BlockEdit) => function (props) {
  const isDuplicateId = (blockId, clientId) => {
    const { getClientIdsWithDescendants, getBlockAttributes } = select(blockEditorStore);
    const blocksClientIds = getClientIdsWithDescendants();
    return blocksClientIds.some((_clientId) => {
      const { id: _blockId } = getBlockAttributes(_clientId);
      return clientId !== _clientId && blockId === _blockId;
    });
  };

  const {
    attributes, setAttributes, name, clientId,
  } = props;
  const { id } = attributes;

  if (!getBlockSupport(name, 'xd/id')) {
    return (
      <BlockEdit {...props} />
    );
  }

  if (themeVersionCompare('>', '2.4.3')) {
    useEffect(() => {
      if (!id || isDuplicateId(id, clientId)) {
        setAttributes({ id: `xd-block-${clientId}` });
      }
    }, [id]);

    return (
      <BlockEdit {...props} />
    );
  }

  return (
    <Fragment>
      <BlockEdit {...props} />
      <InspectorControls>
        <PanelBody title="ID" initialOpen={false}>
          <TextControl
            tagName="span"
            placeholder=""
            onChange={(id) => { setAttributes({ id: id.split(' ').join('-') }) }} // eslint-disable-line
            value={id}
            label="ID"
          />
        </PanelBody>
      </InspectorControls>
    </Fragment>
  );
}, 'withIdControl');

export default {
  name: 'xd/id',
  register: () => {
    if (themeVersionCompare('<=', '2.4.3')) {
      addFilter('blocks.registerBlockType', 'xd-module/with-id-attributes', addAttributes);
    }
    addFilter('editor.BlockEdit', 'xd-module/with-id-control', withIdControl);
  },
};
