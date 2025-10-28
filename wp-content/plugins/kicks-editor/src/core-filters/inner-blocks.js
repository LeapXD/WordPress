import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import useGetContext from '../hooks/use-get-context';

const withInnerBlocksSettings = createHigherOrderComponent((BlockListBlock) => function (props) {
  const getContext = useGetContext();
  const { name, attributes } = props;
  const { xd_settings: xdSettings = {} } = window;
  const { block_settings: blockSettings } = xdSettings;
  const { [name]: settings = {} } = blockSettings;
  const { supports: { custom = {} } = {} } = settings;
  const { innerBlocks = [] } = custom;
  const { layout } = attributes;
  const innerBlocksSettings = getContext(innerBlocks);

  if (!innerBlocks?.length) {
    return (
      <BlockListBlock {...props} />
    );
  }

  return (
    <BlockListBlock
      {...props}
      innerBlocksSettings={{
        __experimentalLayout: layout, layout, ...innerBlocksSettings,
      }}
    />
  );
}, 'withInnerBlocksSettings');

addFilter('xd.innerBlocksSettings', 'xd-module/with-innerblocks-settings', withInnerBlocksSettings);
