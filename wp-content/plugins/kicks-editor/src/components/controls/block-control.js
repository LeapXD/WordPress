import { compose } from '@wordpress/compose';
import { withFilters } from '@wordpress/components';
import * as wpComponents from '@wordpress/components';
import { kebabCase } from 'lodash';
import * as xdComponents from '..';
import { InspectorControl } from '.';

const {
  xd_settings: xdSettings,
} = window;

function blockControl(props) {
  const {
    attribute,
    attributeType,
    block,
    controlProps,
    exposeProps,
    type,
    support,
    value,
    group,
    activeState,
    method,
    text = '',
    ...additionalControlProps
  } = props;

  if (type === 'XDToolbarDropdownMenu') {
    return <InspectorControl {...{ activeState, method, ...props }} />;
  }

  const {
    attributes,
    setAttributes,
    clientId,
  } = block;

  const components = {
    ...wpComponents,
    ...xdComponents,
    ...xdSettings.customComponents,
  };

  const { customHandlers } = xdSettings;

  const id = `${kebabCase(attribute)}-${clientId}`;
  const data = attributes;
  const propertyName = attribute;
  const propertyValue = attributes[attribute] ?? '';
  const setData = setAttributes;
  const setProperty = (value) => setAttributes({ [attribute]: value });

  const Component = components[type];

  if (!attribute || exposeProps) {
    return (
      <Component {...{
        id,
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

  const attributeValue = attributes[attribute];
  const state = {
    value,
    isPressed: (() => {
      switch (attributeType) {
        case 'number':
        case 'integer':
        case 'string':
          return value === attributes[attribute];
        case 'boolean':
          return attributes[attribute];
        case 'array':
          return attributes[attribute].includes(value);
        case 'object':
          break;
          // @todo: object case.
        default:
      }
    })(),
    onClick: (value) => {
      switch (attributeType) {
        case 'number':
        case 'integer':
          setAttributes({ [attribute]: value * 1 });
          break;
        case 'string':
          setAttributes({ [attribute]: value });
          break;
        case 'boolean':
          setAttributes({ [attribute]: !attributeValue });
          break;
        case 'array':
          const newArray = new Set(attributeValue);
          if (newArray.has(value)) {
            newArray.delete(value);
          } else {
            newArray.add(value);
          }
          break;
        case 'object':
          // @todo: object case.
          break;
        default:
      }
    },
  };

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
              clientId,
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
      {...state}
      {...controlProps}
      {...additionalControlProps}
      {..._customHandlers}
    >
      {text}
    </Component>
  );
}

const XDBlockControl = compose([
  withFilters('xd.blockControl'),
])(blockControl);

export default XDBlockControl;
