/* eslint-disable react/react-in-jsx-scope */
import { useBlockProps, store as editorStore } from '@wordpress/block-editor';
import { useDispatch } from '@wordpress/data';
import { getBlockType } from '@wordpress/blocks';
import { useEffect, useMemo, useState } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import useBlockVariation from '../hooks/use-block-variation';
import themeVersionCompare from '../utils/theme-version-compare';

const withTitle = createHigherOrderComponent((BlockEdit) => function (props) {
  const { clientId, name, style = {} } = props;
  const { title } = useBlockVariation(clientId);

  if (themeVersionCompare('>', '2.4.3')) {
    return (<BlockEdit {...props} style={{ ...style, '--xd-block-title': `'${title}'` }} />);
  }

  if (
    [
      'core/paragraph',
      'core/list',
      'core/heading',
      'core/image',
      'xd/image',
      'xd/button',
      'core/missing',
      'xd/richtext',
    ].includes(name)) {
    return (<BlockEdit {...props} />);
  }

  const { id } = useBlockProps();
  const { selectBlock } = useDispatch(editorStore);
  const onClick = () => {
    selectBlock(clientId);
  };

  const el = document.getElementById(id);
  const link = useMemo(() => {
    const anchor = document.createElement('a');
    anchor.addEventListener('click', onClick);
    anchor.classList.add('wp-block-title');
    anchor.innerHTML = title;
    anchor.setAttribute('href', '#');
    anchor.setAttribute('tabIndex', -1);
    return anchor;
  }, []);
  const [state, setState] = useState(1);
  useEffect(() => {
    if (el) {
      el.prepend(link);
    } else if (state === 1) {
      setState(0);
    }
  }, [el]);
  return <BlockEdit {...props} />;
});
addFilter('editor.BlockEdit', 'xd-filter/with-title', withTitle);
