/* eslint-disable no-continue */
/* eslint-disable no-restricted-syntax */
/* eslint-disable react/no-array-index-key */
import { getBlockSupport, getBlockType } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls, HeightControl } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import * as wpComponents from '@wordpress/components';
import * as xdComponents from '../components';
import { InspectorControl } from '../components/controls';
import useGetContext from '../hooks/use-get-context';
import mapControl from '../utils/map-control';
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
  const { name, clientId } = props;
  const components = {
    ...wpComponents,
    ...xdComponents,
    HeightControl,
    ...xdSettings.customComponents,
  };
  const { attributes: blockTypeAttributes } = getBlockType(name);
  const getContext = useGetContext();
  const { [name]: blockType = {} } = blockSettings;
  const { supports: { custom = {} } = {} } = blockType;
  const {
    modules: blockModules,
    inspectorControls,
  } = custom;

  const modulesSettings = getContext(blockModules) ?? !blockModules?.length;
  const { panels: inspectorControlsSettingsPanels = [] } = getContext(inspectorControls) ?? { panels: [] };

  const { allowedModules = modulesSettings } = modulesSettings || {};

  const controlGroupsV1 = moduleSettings.filter(({ version }) => !version || version < 2).map((module) => {
    const {
      supports,
      ...panelProps
    } = module;

    const allowedControls = supports
      .filter((support) => getBlockSupport(name, support.name))
      .filter((support) => allowedModules === true || (Array.isArray(allowedModules) && allowedModules?.includes(support.name)));

    const inspectorControls = allowedControls.reduce((prev, support) => [
      ...prev,
      ...support.inspectorControls?.filter(({ type }) => components[type])
        .map((control) => mapControl(support.name, blockTypeAttributes)(control)) ?? [],
    ], []);

    return {
      inspectorControls: inspectorControls.filter(({ advanced }) => !advanced),
      advancedInspectorControls: inspectorControls.filter(({ advanced }) => advanced),
      ...panelProps,
    };
  });

  const v2Supports = moduleSettings
    .filter(({ version }) => version >= 2)
    .flatMap(({ supports }) => supports
      .filter((support) => support.name !== 'custom')
      .map((support) => {
        const context = getEffectiveFeatureContext(name, support.name, { clientId });
        const isAllowed = allowedModules === true
            || (Array.isArray(allowedModules) && allowedModules.includes(support.name));

        if (!context) {
          return null;
        }

        if (!isAllowed) {
          return null;
        }

        return {
          ...support,
          panels: cleanObjectDeep(support.panels, context),
          inspectorControls: cleanObjectDeep(support.inspectorControls, context),
        };
      })
      .filter((support) => support !== null));

  const controlGroupsV2 = [];
  const advancedControlsV2 = [];

  v2Supports.forEach(({ panels = [] }) => {
    panels.forEach((panel) => {
      if (!controlGroupsV2.some(({ name }) => name === panel.name)) {
        controlGroupsV2.push({
          ...panel,
          inspectorControls: [],
        });
      }
    });
  });

  v2Supports.forEach((support) => {
    const { inspectorControls = [] } = support;
    inspectorControls.filter(({ type }) => components[type])
      .forEach((control) => {
        if (control.panel === 'advanced') {
          advancedControlsV2.push(mapControl(support.name, blockTypeAttributes)(control));
          return;
        }
        const panel = controlGroupsV2.find(({ name }) => name === control.panel);
        if (!panel) {
          return;
        }
        // if (panel.inspectorControls.some(({ name }) => name === control.name)) {
        //   console.warn(`Control with name "${control.name}" already exists in panel "${panel.name}".`);
        //   return;
        // }
        panel.inspectorControls.push(mapControl(support.name, blockTypeAttributes)(control));
      });
  });

  const controlGroups = [...controlGroupsV1, ...controlGroupsV2];

  const inspectorControlGroups = [...inspectorControlsSettingsPanels
    .map(({ controls, ...props }) => ({
      inspectorControls: controls.filter(({ advanced }) => !advanced).map((control) => mapControl('block', blockTypeAttributes)(control)),
      ...props,
    })), ...controlGroups]
    .filter(({ inspectorControls }) => inspectorControls.length);

  const advancedInspectorControls = [...inspectorControlsSettingsPanels
    .map(({ controls, ...props }) => ({
      advancedInspectorControls: controls.filter(({ advanced }) => advanced).map((control) => mapControl('block', blockTypeAttributes)(control)),
      ...props,
    })), ...controlGroupsV1, { advancedInspectorControls: advancedControlsV2 }]
    .map(({ advancedInspectorControls }) => advancedInspectorControls)
    .reduce((prev, inspectorControl) => [...prev, ...inspectorControl], []);

  return (
    <>
      <InspectorControls>
        { inspectorControlGroups.map(({
          title = '',
          inspectorControls = [],
          className = '',
          initialOpen = false,
        }, key) => (
          <PanelBody
            className={className}
            initialOpen={initialOpen}
            title={title}
            key={key}
          >
            {inspectorControls
              .map((control, key) => (
                <InspectorControl
                  {...control}
                  key={key}
                />
              ))}
          </PanelBody>
        )) }
      </InspectorControls>
      <InspectorControls group="advanced">
        { advancedInspectorControls
          .map((control, key) => (
            <InspectorControl
              {...control}
              key={key}
            />
          ))}
      </InspectorControls>
    </>
  );
}

const withInspectorControls = createHigherOrderComponent((BlockEdit) => function (props) {
  return (
    <>
      <BlockControlsWrapper {...props} />
      <BlockEdit {...props} />
    </>
  );
}, 'withInspectorControls');

export default withInspectorControls;
