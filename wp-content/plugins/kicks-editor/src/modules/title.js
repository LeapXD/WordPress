import classnames from 'classnames';

import { getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useBlockProps, PlainText } from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { useState, useEffect, createPortal } from '@wordpress/element';

const addAttributes = (settings) => {
  if (getBlockSupport(settings, 'xd/title')) {
    settings.attributes = {
      title: {
        type: 'string',
        default: '',
      },
      ...settings.attributes,
    };
  }
  return settings;
};

function usePortal({
  children,
  className = 'portal',
  tagName = 'div',
  el,
}) {
  const [container] = useState(() => {
    // eslint-disable-next-line no-shadow
    const el = document.createElement(tagName);
    el.classList.add(className);
    return el;
  });
  useEffect(() => {
    el.prepend(container);
    return () => {
      el.removeChild(container);
    };
  }, []);

  return el ? createPortal(children, container) : null;
}

function Portal(props) {
  const { el } = props;
  return el ? usePortal(props) : null;
}

const withTitle = createHigherOrderComponent((BlockEdit) => function (props) {
  const { name } = props;
  if (!getBlockSupport(name, 'xd/title')) {
    return (
      <BlockEdit {...props} />
    );
  }
  const { id } = useBlockProps();

  const [state, setState] = useState(1);
  useEffect(() => {
    if (state === 1) {
      setState(0);
    }
  }, []);
  const el = document.getElementById(id);
  const { attributes, setAttributes } = props;
  const { title } = attributes;

  return (
    <>
      <Portal el={el}>
        <PlainText
          placeholder={__('Title', 'leap')}
					onChange={(title) => { setAttributes({ title }); }} // eslint-disable-line
          value={title}
          className="h2"
        />
      </Portal>
      <BlockEdit {...props} />
    </>
  );
}, 'withTitle');

export default {
  name: 'xd/title',
  register: () => {
    addFilter('blocks.registerBlockType', 'xd-module/with-title-attributes', addAttributes);
    addFilter('editor.BlockEdit', 'xd-module/with-title', withTitle);
  },
};
