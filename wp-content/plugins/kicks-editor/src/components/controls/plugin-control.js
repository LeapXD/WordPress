import { kebabCase } from 'lodash';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import * as wpComponents from '@wordpress/components';
import { store as editorStore } from '@wordpress/editor';
import * as xdComponents from '..';
import getFieldState from './get-field-state';

const {
  xd_settings: xdSettings,
} = window;

export default function pluginControl(props) {
  const {
    activeState,
    name,
    inputType,
    exposeProps,
    method,
    schema,
    value,
    type,
    panel,
    location,
    controlProps,
    ...additionalControlProps
  } = props;

  const { type: fieldType } = schema ?? {};

  const components = {
    RichText: wp.blockEditor.RichText,
    PlainText: wp.blockEditor.PlainText,
    ...wpComponents,
    ...xdComponents,
    ...xdSettings.customComponents,
  };

  const { customHandlers } = xdSettings;

  const { getCurrentPost } = useSelect(editorStore) ?? {};

  const { id, type: postType } = getCurrentPost();
  const { editEntityRecord } = useDispatch(coreStore);

  const { meta = {} } = useSelect(
    (select) => {
      const { getEntityRecord, getEntityRecordEdits } = select(coreStore);
      const post = getEntityRecord('postType', postType, id);
      return post ? {
        ...post,
        ...getEntityRecordEdits('postType', postType, id),
      } : {};
    },
  );

  const saveMeta = (data) => {
    editEntityRecord('postType', postType, id, { meta: data });
  };

  const componentId = kebabCase(`${panel}-${name}`);
  const data = meta;
  const propertyName = name;
  const propertyValue = meta[name] ?? '';
  const setData = saveMeta;
  const setProperty = (value) => saveMeta({ [name]: value });

  const Component = components[type];

  if (!Component) {
    return null;
  }

  if (!name || exposeProps) {
    return (
      <Component {...{
        id: componentId,
        data,
        propertyName,
        propertyValue,
        setData,
        setProperty,
        ...props,
        ...controlProps,
      }}
      />
    );
  }

  const state = getFieldState(activeState, method, fieldType, propertyValue, setProperty);

  if (type === 'XDButton') {
    state.value = value;
    if (fieldType === 'array') {
      state.isPressed = state[activeState].includes(value);
    }
    if (fieldType === 'string') {
      state.isPressed = value === state.activeState;
    }
  }

  if (inputType && type === 'TextControl') {
    controlProps.type = inputType;
  }

  const _customHandlers = Object.entries(controlProps)
    .reduce((acc, [key, value]) => {
      if (key.startsWith('custom:')) {
        const handler = key.replace('custom:', '');
        if (customHandlers[value]) {
          acc[handler] = (e, ...args) => {
            if (e.persist) {
              e.persist();
            }
            customHandlers[value](e, ...args, {
              id,
              data,
              propertyName,
              propertyValue,
              setData,
              setProperty,
              ...state,
              ...controlProps,
              ...additionalControlProps,
            });
          };
        }
        delete controlProps[key];
      }
      return acc;
    }, {});

  return (
    <Component
      id={componentId}
      {...state}
      {...controlProps}
      {...additionalControlProps}
      {..._customHandlers}
    />
  );
}
