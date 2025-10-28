import { store as blockEditorStore } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';

const { xd_settings: settings } = window;
const { registerComponent, context, components } = settings;
const { useXdBlockContext } = context;
const { XDToolBarButton } = components;

function XDPostCardControls() {
  const blockContext = useXdBlockContext();
  const { clientId, block } = blockContext;
  const { attributes } = block;
  const { postId: id } = attributes;
  const { replaceInnerBlocks } = useDispatch(blockEditorStore);
  const { getBlocks, getBlockParents } = useSelect((select) => select(blockEditorStore), []);
  const parents = getBlockParents(clientId);
  const [parentClientId] = parents.slice(-1);
  const innerBlocks = getBlocks(parentClientId);
  const index = innerBlocks.findIndex((currentCard) => currentCard.clientId === clientId);

  return (
    <>
      <XDToolBarButton
        icon="wp/edit"
        label="Edit"
        onClick={() => {
          window.location = `/wp-admin/post.php?post=${id}&action=edit`;
        }}
      />
      <XDToolBarButton
        icon="wp/close"
        label="Remove Card"
        onClick={() => {
          innerBlocks.splice(index, 1);
          replaceInnerBlocks(parentClientId, innerBlocks);
        }}
      />
    </>
  );
}

registerComponent('XDPostCardControls', XDPostCardControls);
