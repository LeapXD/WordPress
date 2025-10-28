/* eslint-disable react/no-array-index-key */
import { getBlockSupport, getBlockType } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup } from '@wordpress/components';
import * as wpComponents from '@wordpress/components';
import * as xdComponents from '../components';
import { BlockControl } from '../components/controls';
import useGetContext from '../hooks/use-get-context';
import mapBlockControl from '../utils/map-block-control';
import cleanObjectDeep from '../utils/clean-object-deep';
import getEffectiveFeatureContext from '../utils/feature-context';

const {
  xd_settings: xdSettings,
} = window;

const {
  block_settings: blockSettings,
  module_settings: moduleSettings,
} = xdSettings;

function BlockControlsWrapper(props) {
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
    blockControls,
  } = custom;

  const modulesSettings = getContext(blockModules) ?? !blockModules?.length;
  const { controls: blockControlsSettings = [] } = getContext(blockControls) ?? {};

  const { allowedModules = modulesSettings } = modulesSettings || {};

  const controlGroups = moduleSettings.map((module) => {
    const { supports, ...panelProps } = module;

    const allowedControls = supports
      .filter((support) => {
        const context = getEffectiveFeatureContext(name, support.name);
        const isAllowed = allowedModules === true
        || (Array.isArray(allowedModules) && allowedModules.includes(support.name));

        return Boolean(context && isAllowed);
      });

    const blockControls = allowedControls.flatMap((support) => {
      const context = getEffectiveFeatureContext(name, support.name);
      const cleanedBlockControls = cleanObjectDeep(support.blockControls, context) || [];

      return cleanedBlockControls
        .filter((control) => components[control.type])
        .map((control) => mapBlockControl(support.name, blockTypeAttributes)(control));
    });

    return {
      ...panelProps,
      blockControls,
    };
  });

  const toolbarGroups = [...blockControlsSettings.map((control) => mapBlockControl('block', blockTypeAttributes)(control)), ...controlGroups
    .reduce((prev, { blockControls }) => [...prev, ...blockControls], [])]
    .reduce((prev, control) => {
      const groupName = control.group ?? 'other';
      const group = prev.find(({ group }) => group === groupName);
      if (group) {
        group.controls.push(control);
      } else {
        prev.push({ group: groupName, controls: [control] });
      }
      return prev;
    }, [])
    .map(({ controls }) => controls
      .map((control, key) => (
        <BlockControl
          {...control}
          block={props}
          key={key}
        />
      )))
    .reduce((prev, control) => [...prev, control], []);

  return (

    <BlockControls>
      { toolbarGroups.map((controls, key) => <ToolbarGroup key={key}>{controls}</ToolbarGroup>) }
    </BlockControls>
  );
}

const withBlockControls = createHigherOrderComponent((BlockEdit) => function (props) {
  return (
    <>
      <BlockControlsWrapper {...props} />
      <BlockEdit {...props} />
    </>
  );
}, 'withCustomControls');

export default withBlockControls;
