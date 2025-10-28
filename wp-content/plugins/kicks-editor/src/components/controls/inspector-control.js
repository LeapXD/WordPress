import * as wpComponents from '@wordpress/components';
import { HeightControl } from '@wordpress/block-editor';
import isEqual from 'lodash/isEqual';
import { getBlockType } from '@wordpress/blocks';
import * as xdComponents from '..';
import { useXdBlockContext } from '../context';
import getFieldState from './get-field-state';

const { kebabCase } = window.lodash;
const xdSettings = window.xd_settings;

export default function inspectorControl(props) {
  const components = {
    RichText: wp.blockEditor.RichText,
    PlainText: wp.blockEditor.PlainText,
    HeightControl,
    ...wpComponents,
    ...xdComponents,
    ...xdSettings.customComponents,
  };

  const { customHandlers } = xdSettings;

  const {
    attribute,
    attributeType,
    inputType,
    controlProps,
    exposeProps,
    type,
    support,
    method,
    advanced,
    activeState,
    value,
    when,
    block: sourceBlock,
    ...additionalControlProps
  } = props;

  const context = useXdBlockContext();
  const { clientId, block = sourceBlock } = context;
  const { attributes, setAttributes, name } = block;

  const id = `${kebabCase(attribute)}-${clientId}`;
  const data = attributes;
  const propertyName = attribute;
  const propertyValue = attributes[attribute] ?? '';
  const setData = setAttributes;
  const setProperty = (value) => setAttributes({ [attribute]: value });

  const Component = components[type];

  const matchAttribute = (compare, operator, attributeName, value = false) => {
    // Get block name from context
    if (operator === '!default' || operator === 'default') {
      const blockType = getBlockType(name);
      const defaultValue = blockType?.attributes?.[attributeName]?.default;
      const isDefault = isEqual(compare, defaultValue);
      return operator === '!default' ? !isDefault : isDefault;
    }
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

  const matchAttributes = (attributes, orGroups = []) => {
    if (!orGroups.length) return true;
    return orGroups.some((andGroup) => andGroup.every(({ attribute, operator, value }) => matchAttribute(attributes[attribute], operator, attribute, value)));
  };

  if (when && !matchAttributes(attributes || {}, when)) {
    return null;
  }

  if (!attribute || exposeProps) {
    return (
      <Component {...{
        id,
        clientId,
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

  const state = getFieldState(activeState, method, attributeType, propertyValue, setProperty);

  if (type === 'XDButton') {
    if (attributeType === 'array') {
      state.isPressed = state[activeState].includes(value);
    }
    if (attributeType === 'string') {
      state.isPressed = value === state[activeState];
    }
    state.value = value;
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
      id={id}
      {...state}
      {...controlProps}
      {...additionalControlProps}
      {..._customHandlers}
    />
  );
}
