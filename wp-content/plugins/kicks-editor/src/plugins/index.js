import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import {
  store as editorStore,
  PluginBlockSettingsMenuItem,
  PluginDocumentSettingPanel,
  PluginMoreMenuItem,
  PluginPostPublishPanel,
  PluginPostStatusInfo,
  PluginPrePublishPanel,
  PluginSidebar,
  PluginSidebarMoreMenuItem,
} from '@wordpress/editor';

import { store as coreStore } from '@wordpress/core-data';

import { PluginControl } from '../components/controls';

const pluginPanels = {
  PluginBlockSettingsMenuItem,
  PluginDocumentSettingPanel,
  PluginMoreMenuItem,
  PluginPostPublishPanel,
  PluginPostStatusInfo,
  PluginPrePublishPanel,
  PluginSidebar,
  PluginSidebarMoreMenuItem,
  XDFeaturedImagePanel: ({ children }) => children,
};

const {
  xd_settings: xdSettings,
} = window;

const {
  editor_settings: editorSettings = {},
  plugin_settings: pluginSettings = [],
} = xdSettings;

const {
  post_state: postState,
  page_template: pageTemplate,
} = editorSettings;

const matchField = (compare, operator, value = false) => {
  switch (operator) {
    case '!=':
      return compare !== value;
    case '>':
      return compare > value;
    case '<':
      return compare < value;
    case '>=':
      return compare >= value;
    case '<=':
      return compare <= value;
    case '!!':
      return !!compare;
    case '!':
      return !compare;
    default:
      return compare === value;
  }
};

const getFieldValue = (matches, field) => {
  if (!field || !field.name) return undefined;
  // Always start from matches.meta
  const path = field.name.split('.');
  return path.reduce((obj, key) => (obj ? obj[key] : undefined), matches.meta);
};

const matchLocations = (matches, locations = []) => {
  if (!locations.length) return true;
  return locations.some((andGroup = []) => andGroup.every((condition) => {
    if (condition.param === 'field') {
      const fieldValue = getFieldValue(matches, { name: condition.field });
      return matchField(fieldValue, condition.operator, condition.value);
    }
    return matchField(matches[condition.param], condition.operator, condition.value);
  }));
};

function PluginPanel({
  panel, title, name, fieldGroups = [], locations = [],
}) {
  const { id, type } = useSelect((select) => select(editorStore).getCurrentPost());
  const { template, slug, meta = {} } = useSelect((select) => select(coreStore).getEditedEntityRecord('postType', type, id));

  if (!id) {
    return null;
  }

  const matches = {
    post_type: type,
    post_state: postState,
    page_template: template,
    meta,
    slug,
  };
  const Plugin = pluginPanels[panel];

  // Check this plugin panel has a match for the current location
  if (!matchLocations(matches, locations)) {
    return null;
  }
  // filter out any field groups that don't match the current location
  const groups = fieldGroups.filter(({ locations }) => matchLocations(matches, locations))
  // filter out any field groups that don't contain any fields that match the current location
    .filter(({ fields }) => fields
      .filter(({ locations }) => matchLocations(matches, locations))
      .reduce((prev, curr) => (prev || curr), false));

  if (!groups.length) {
    return null;
  }

  return (
    <Plugin
      Panelname={name}
      title={title}
      name={name}
    >
      {groups.map(({ title, fields }, key) => (
        <div key={key}>
          {title && <h3>{title}</h3>}
          {fields
            .filter(({ locations }) => matchLocations(matches, locations))
            .map(({
              activeState = 'value',
              method = 'onChange',
              exposeProps,
              name,
              location,
              schema,
              type,
              inputType,
              value,
              ...props
            }) => (
              <PluginControl
                {...{
                  activeState,
                  method,
                  exposeProps,
                  name,
                  location,
                  schema,
                  type,
                  inputType,
                  value,
                  panel,
                  controlProps: props,
                }}
                key={name}
              />
            ))}
          {key !== (groups.length - 1)
          && <hr />}
        </div>
      ))}
    </Plugin>
  );
}

export const getPlugins = () => pluginSettings.map(({
  name, panels, icon = null, ...props
}) => panels.map((panel, index) => ({
  plugin: () => (
    <PluginPanel
      panel={panel}
      {...props}
      name={name}
    />
  ),
  panel,
  name: `${name}-${index}`,
  icon,
})));

export const featuredImage = ((Plugin) => createHigherOrderComponent(
  () => function (props) {
    return (
      <Plugin {...props} />
    );
  },
));
