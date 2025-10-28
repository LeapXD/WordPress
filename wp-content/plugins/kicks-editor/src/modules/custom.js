import { addFilter } from '@wordpress/hooks';
import withBlockBodyControls from './block-body-controls';
import withBlockControls from './block-controls';
import withCssVars from './block-css-vars';
import { withBlockWrapClasses, setBlockCustomClassName } from './block-wrap-classes';
import withInspectorControls from './inspector-controls';

export default {
  name: 'xd/custom',
  register: () => {
    addFilter('editor.BlockEdit', 'xd-module/with-block-body-controls', withBlockBodyControls);
    addFilter('editor.BlockEdit', 'xd-module/with-block-controls', withBlockControls);
    addFilter('editor.BlockEdit', 'xd-module/with-css-vars', withCssVars);
    addFilter('editor.BlockEdit', 'xd-module/with-block-wrap-classes', withBlockWrapClasses);
    addFilter('editor.BlockEdit', 'xd-module/with-inspector-controls', withInspectorControls);
    addFilter('blocks.getBlockDefaultClassName', 'xd-module/set-block-custom-class-name', setBlockCustomClassName);
  },
};