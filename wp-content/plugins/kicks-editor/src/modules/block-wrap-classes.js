import classNames from 'classnames';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useXdBlockContext } from '../components/context';
import useBlockVariation from '../hooks/use-block-variation';

const {
  xd_settings: xdSettings,
} = window;

const {
  block_settings: blockSettings,
} = xdSettings;

const getClassNames = (schemaProp, blockTypeAttributes, attributes, name) => {
  const { clientId } = useXdBlockContext();
  const variation = useBlockVariation(clientId);
  if (variation.name !== name) {
    Object.entries(variation.classNames ?? []).forEach(([attrName, attrVal]) => {
      if (attrVal[schemaProp]) {
        blockTypeAttributes[attrName][schemaProp] = attrVal[schemaProp];
      }
    });
  }

  return Object.entries(blockTypeAttributes)
    .filter(([attributeName, attributeSchema]) => (attributeSchema[schemaProp]) && attributes[attributeName])
    .map(([attributeName, attributeSchema]) => {
      switch (attributeSchema?.type) {
        case 'string':
        case 'integer':
        case 'number':
        case 'array':
          if (typeof attributeSchema[schemaProp] === 'object') {
            return attributeSchema[schemaProp][attributes[attributeName]];
          }
          return classNames(attributes[attributeName]);
        case 'object':
          if (attributeSchema[schemaProp] === true) {
            return classNames(attributes[attributeName]);
          }
          return classNames(Object.entries(attributes[attributeName]).reduce(
            (prev, [propertyName, value]) => [
              ...prev,
              attributeSchema[schemaProp][propertyName] === true
                ? value
                : { [attributeSchema[schemaProp][propertyName] ?? '']: !!attributes[attributeName][propertyName] }],
            [],
          ));
        case 'boolean':
          return classNames({ [attributeSchema[schemaProp]]: attributes[attributeName] });
        default:
          return '';
      }
    })
    .filter((_) => _);
};

export const withBlockWrapClasses = createHigherOrderComponent((BlockEdit) => function (props) {
  const {
    attributes, name, clientId,
  } = props;
  const { [name]: blockType = {} } = blockSettings;
  const { attributes: blockTypeAttributes = {} } = blockType;

  const variationClass = useBlockVariation(clientId)?.name?.replace('xd/', 'xd-');
  const additionalClasses = getClassNames('className', blockTypeAttributes, attributes, name);
  const innerBlocksClasses = getClassNames('innerBlocksClassName', blockTypeAttributes, attributes, name);
  const innerClasses = getClassNames('innerClassName', blockTypeAttributes, attributes, name);
  const outerClasses = getClassNames('outerClassName', blockTypeAttributes, attributes, name);
  const preInnerBlocksClasses = getClassNames('preInnerBlocksClassName', blockTypeAttributes, attributes, name);
  const postInnerBlocksClasses = getClassNames('postInnerBlocksClassName', blockTypeAttributes, attributes, name);

  return (
    <BlockEdit
      {...props}
      className={classNames(additionalClasses, variationClass)}
      postInnerBlocksClassName={classNames(postInnerBlocksClasses)}
      preInnerBlocksClassName={classNames(preInnerBlocksClasses)}
      innerBlocksClassName={classNames(innerBlocksClasses)}
      innerClassName={classNames(innerClasses)}
      outerClassName={classNames(outerClasses)}
    />
  );
}, 'withBlockWrapClasses');

// Our filter function
export function setBlockCustomClassName(className) {
  return `${className} ${className.replace('wp-block-xd-', 'xd-')}`;
}
