import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as blocksStore, getBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

export default (clientId) => useSelect((select) => {
  const { getBlock, getBlockAttributes } = select(blockEditorStore);
  const { getActiveBlockVariation } = select(blocksStore);
  const blockName = getBlock(clientId)?.name;
  const blockAttributes = getBlockAttributes(clientId);
  return getActiveBlockVariation(blockName, blockAttributes) ?? getBlockType(blockName);
});
