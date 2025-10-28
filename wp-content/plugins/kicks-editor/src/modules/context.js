import { createHigherOrderComponent } from '@wordpress/compose';
import { useRef } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import { XDBlockContextProvider, useXdBlockContext } from '../components/context';

const withContext = createHigherOrderComponent((BlockList) => function (props) {
  const contextRef = useRef({});
  const { clientId } = props;
  const parentBlockContext = useXdBlockContext();
  return (
    <XDBlockContextProvider value={{
      clientId,
      block: props,
      [clientId]: { ...props, ...parentBlockContext },
      contextRef,
    }}
    >
      <BlockList {...props} />
    </XDBlockContextProvider>
  );
}, 'withContext');

export default {
  name: 'xd/context',
  register: () => {
    addFilter('editor.BlockListBlock', 'xd-module/with-context', withContext, 5);
  },
};
