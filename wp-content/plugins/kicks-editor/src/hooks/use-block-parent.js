import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as blocksStore } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

export default (clientId) => useSelect((select) => {
  const { getBlockParents, getBlock, getBlockAttributes } = select(blockEditorStore);
  const { getActiveBlockVariation } = select(blocksStore);
  const parents = getBlockParents(clientId);
  const [parent] = parents.slice(-1);
  const parentBlockName = getBlock(parent)?.name;
  const parentBlockAttributes = getBlockAttributes(parent);
  const parentActiveVariation = getActiveBlockVariation(parentBlockName, parentBlockAttributes)?.name ?? parentBlockName;
  return { parent: parentBlockName, parentActiveVariation };
});
