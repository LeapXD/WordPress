import { createHigherOrderComponent } from '@wordpress/compose';
import { kebabCase } from 'lodash';

const {
  xd_settings: xdSettings,
} = window;

const {
  block_settings: blockSettings,
} = xdSettings;

const getCssVariables = (blockTypeAttributes, attributes) => Object.entries(blockTypeAttributes)
  .filter(([, attributeSchema]) => attributeSchema?.cssVariable)
  .map(([attributeName, attributeSchema]) => {
    switch (attributeSchema?.type) {
      case 'string':
      case 'integer':
      case 'number':
        const propKey = `--xd-${kebabCase(attributeName)}`;
        const value = `${attributes[attributeName]}`;
        if (attributeSchema?.cssTransform) {
          const [[regexp, replace]] = Object.entries(attributeSchema?.cssTransform);
          const re = new RegExp(regexp, 'g');
          const transformedValue = value.replace(re, replace);
          return { [propKey]: transformedValue };
        }
        return { [propKey]: value };
      case 'boolean':
        return ({ [`--xd-${kebabCase(attributeName)}`]: attributes[attributeName] ? attributeSchema.cssVariable : false });
      case 'array':
        return ({ [`--xd-${kebabCase(attributeName)}`]: attributes[attributeName].join(' ') });
      case 'object':
        return Object.entries(attributes[attributeName])
          .reduce((prev, [propertyName, value]) => {
            const propKey = `--xd-${kebabCase(attributeName)}-${kebabCase(propertyName)}`;
            if (attributeSchema?.cssTransform[propertyName]) {
              const [[regexp, replace]] = Object.entries(attributeSchema?.cssTransform[propertyName]);
              const re = new RegExp(regexp, 'g');
              const transformedValue = value.replace(re, replace);
              return { ...prev, [propKey]: transformedValue };
            }
            return { ...prev, [propKey]: value };
          }, {});
      default:
        return {};
    }
  }).reduce((prev, cur) => ({ ...prev, ...cur }), {});

const withCssVars = createHigherOrderComponent((BlockEdit) => function (props) {
  const {
    attributes, name, style,
  } = props;
  const { [name]: blockType = {} } = blockSettings;
  const { attributes: blockTypeAttributes = {} } = blockType;

  const cssVariables = getCssVariables(blockTypeAttributes, attributes);

  return (
    <BlockEdit
      {...props}
      style={{ ...style, ...cssVariables }}
    />
  );
}, 'withCssVars');

export default withCssVars;
