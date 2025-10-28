import backgroundImage from './background-image';
import custom from './custom';
import link from './link';
import margin from './margin';
import padding from './padding';
import title from './title';
import width from './width';
import context from './context';
import id from './id';

export default [
  backgroundImage,
  custom,
  link,
  margin,
  padding,
  title,
  width,
  context,
  id,
];

export { default as backgroundImage } from './background-image';
export { default as inspectorControls } from './inspector-controls';
export { default as link } from './link';
export { default as margin } from './margin';
export { default as padding } from './padding';
export { default as title } from './title';
export { default as width } from './width';
export { default as context } from './context';
export { default as id } from './id';
export { default as withBlockBodyControls } from './block-body-controls';
export { default as withBlockControls } from './block-controls';
export { default as withCssVars } from './block-css-vars';
export { withBlockWrapClasses, setBlockCustomClassName } from './block-wrap-classes';
export { default as withInspectorControls } from './inspector-controls';
