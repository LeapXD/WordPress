import * as wpComponents from '@wordpress/components';
import * as xdComponents from '..';
import getFieldState from './get-field-state';

const xdSettings = window.xd_settings;

export default function XDFieldGroupControl({ type, ...props }) {
  const {
    id,
    method,
    activeState,
    schema,
    value,
    data,
    setData,
    propertyName,
    onChange: setProperty,
    ...controlProps
  } = props;

  const { type: fieldType = 'string', default: fieldDefault = '' } = schema ?? {};

  const propertyValue = value || fieldDefault;

  const components = {
    RichText: wp.blockEditor.RichText,
    PlainText: wp.blockEditor.PlainText,
    ...wpComponents,
    ...xdComponents,
    ...xdSettings.customComponents,
  };

  const { customHandlers, context } = xdSettings;
  const { useXdBlockContext } = context;
  const ctx = useXdBlockContext();
  const { clientId } = ctx;

  const Component = components[type];
  const state = getFieldState(activeState, method, fieldType, propertyValue, setProperty);

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
              schema,
              clientId,
              data,
              propertyName,
              propertyValue,
              setData,
              setProperty,
              ...state,
              ...controlProps,
            });
          };
        }
        delete controlProps[key];
      }
      return acc;
    }, {});

  return (
    <Component id={id} {...state} {...controlProps} {..._customHandlers} />
  );
}
