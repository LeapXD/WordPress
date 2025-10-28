/* eslint-disable react/no-array-index-key */
import { getBlockSupport, getBlockType } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import * as wpComponents from '@wordpress/components';
import * as xdComponents from '../components';
import { InspectorControl } from '../components/controls';
import useGetContext from '../hooks/use-get-context';
import mapControl from '../utils/map-control';

const {
  xd_settings: xdSettings,
} = window;

const {
  block_settings: blockSettings,
  module_settings: moduleSettings,
} = xdSettings;

function BlockBodyControlsWrapper(props) {
  const { name } = props;
  const components = {
    ...wpComponents,
    ...xdComponents,
    ...xdSettings.customComponents,
  };
  const { attributes: blockTypeAttributes } = getBlockType(name);
  const getContext = useGetContext();
  const { [name]: blockType = {} } = blockSettings;
  const { supports: { custom = {} } = {} } = blockType;
  const {
    modules: blockModules,
    preInnerBlocksControls,
    postInnerBlocksControls,
  } = custom;

  const modulesSettings = getContext(blockModules) ?? !blockModules?.length;
  const { controls: preInnerBlocksControlsSettings = [] } = getContext(preInnerBlocksControls) ?? {};
  const { controls: postInnerBlocksControlsSettings = [] } = getContext(postInnerBlocksControls) ?? {};

  const { allowedModules = modulesSettings } = modulesSettings || {};

  const controlGroups = moduleSettings.map((module) => {
    const {
      supports,
      ...panelProps
    } = module;

    const allowedControls = supports
      .filter((support) => getBlockSupport(name, support.name))
      .filter((support) => allowedModules === true || (Array.isArray(allowedModules) && allowedModules?.includes(support.name)));

    const preInnerBlocksControls = allowedControls.reduce((prev, support) => [
      ...prev,
      ...support.preInnerBlocksControls?.map((control) => mapControl(support.name, blockTypeAttributes)(control)) ?? [],
    ], []);

    const postInnerBlocksControls = allowedControls.reduce((prev, support) => [
      ...prev,
      ...support.postInnerBlocksControls?.filter(({ type }) => components[type])
        .map((control) => mapControl(support.name, blockTypeAttributes)(control)) ?? []], []);

    return {
      preInnerBlocksControls,
      postInnerBlocksControls,
      ...panelProps,
    };
  });

  const preInnerBlocks = [...preInnerBlocksControlsSettings.map((control) => mapControl('block', blockTypeAttributes)(control)), ...controlGroups
    .reduce((prev, { preInnerBlocksControls }) => [...prev, ...preInnerBlocksControls], [])];

  const postInnerBlocks = [...postInnerBlocksControlsSettings.map((control) => mapControl('block', blockTypeAttributes)(control)), ...controlGroups
    .reduce((prev, { postInnerBlocksControls }) => [...prev, ...postInnerBlocksControls], [])];

  return (
    {
      preInnerBlocks: preInnerBlocks
        .map((control, key) => (
          <InspectorControl
            {...control}
            key={key}
          />
        )),
      postInnerBlocks: postInnerBlocks
        .map((control, key) => (
          <InspectorControl
            {...control}
            block={props}
            key={key}
          />
        )),
    }
  );
}

const withBlockBodyControls = createHigherOrderComponent((BlockEdit) => function (props) {
  const { preInnerBlocks, postInnerBlocks } = BlockBodyControlsWrapper(props);

  return (
    <BlockEdit
      {...props}
      preInnerBlocks={preInnerBlocks}
      postInnerBlocks={postInnerBlocks}
    />
  );
}, 'withBlockBodyControls');

export default withBlockBodyControls;
