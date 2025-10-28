import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';
import {
  unregisterBlockStyle,
  registerBlockStyle,
  store as blockStore,
} from '@wordpress/blocks';
import { useEffect, useLayoutEffect, useMemo } from '@wordpress/element';

const withStyles = createHigherOrderComponent((BlockEdit) => function (props) {
  const { name, clientId, isSelected } = props;
  const { xd_settings: xdSettings = {} } = window;
  const { block_settings: blockSettings } = xdSettings;
  const { [name]: settings = {} } = blockSettings;
  const { supports: { custom = {} } = {}, styles: blockStyles } = settings;
  const { styles = [] } = custom;
  if (!styles?.length || !blockStyles?.length) {
    return (
      <BlockEdit {...props} />
    );
  }

  const { getBlockStyles } = useSelect(blockStore);

  const currentBlockStyles = useMemo(() => getBlockStyles(name), []);

  const parent = useSelect((select) => {
    const { getBlockParents, getBlock } = select(blockEditorStore);
    const parents = getBlockParents(clientId);
    return getBlock([parents.slice(-1)])?.name;
  });
  const postType = useSelect((select) => {
    const { getCurrentPostType } = select(editorStore);
    return getCurrentPostType();
  });

  const [{ allowedStyles, default: defaultStyle } = {}] = styles.filter(({ context }) => {
    const { parent: blockParent, postType: type, all } = context;
    if (blockParent && type) {
      return blockParent.includes(parent) && type.includes(postType);
    }
    if (blockParent) {
      return blockParent.includes(parent);
    }
    if (type) {
      return type.includes(postType);
    }
    if (all) {
      return true;
    }
    return !blockParent && !type;
  });

  useLayoutEffect(() => {
    if (isSelected) {
      currentBlockStyles.forEach((style) => unregisterBlockStyle(name, style.name));
      if (allowedStyles) {
        blockStyles
          .map(({ name, ...style }) => ({ isDefault: name === defaultStyle, name, ...style }))
          .filter(({ name }) => allowedStyles.includes(name))
          .forEach((style) => registerBlockStyle(name, style));
      } else {
        blockStyles.forEach((style) => registerBlockStyle(name, style));
      }
    }
  }, [isSelected]);

  return (
    <BlockEdit {...props} />
  );
}, 'withStyleSettings');

addFilter('editor.BlockEdit', 'xd-module/with-style-settings', withStyles);
