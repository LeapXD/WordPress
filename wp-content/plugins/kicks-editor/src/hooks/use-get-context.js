/* eslint-disable no-restricted-syntax */
import useBlockParent from './use-block-parent';
import useBlockVariation from './use-block-variation';
import themeVersionCompare from '../utils/theme-version-compare';
import { useXdBlockContext } from '../components/context';

const { xd_settings: xdSettings } = window;
const { editor_settings: editorSettings = {} } = xdSettings;
const { post = {} } = editorSettings;
const { post_type: postType } = post ?? {};

export default () => function (blockSettingGroup) {
  const { length } = blockSettingGroup ?? [];

  if (!length) {
    return null;
  }

  const matchedGroup = blockSettingGroup?.map((settings) => {
    const { clientId, block } = useXdBlockContext();
    const { attributes } = block;
    const { parent, parentActiveVariation } = useBlockParent(clientId);
    const { name: activeVariation } = useBlockVariation(clientId);

    const { context = {} } = settings;
    const {
      parent: blockParent,
      parentVariation: blockParentActiveVariation,
      postType: type,
      attributes: blockAttributes,
      variation: blockVariation,
      themeVersion,
      all,
    } = context;
    let score = 0;
    if (blockParent) {
      score = blockParent.includes(parent) ? score + 1 : score - 1;
    }
    if (blockParentActiveVariation) {
      score = blockParentActiveVariation.includes(parentActiveVariation) ? score + 1 : score - 1;
    }
    if (blockVariation) {
      score = blockVariation.includes(activeVariation) ? score + 1 : score - 1;
    }
    if (type) {
      score = type.includes(postType) ? score + 1 : score - 1;
    }
    if (blockAttributes) {
      for (const [key, val] of Object.entries(blockAttributes)) {
        if (attributes[key] === val) {
          score++;
        } else {
        // if any tested attributes are present, and attribute value doesn't match test, disregard current context
          return false;
        }
      }
    }
    if (themeVersion) {
      const { compare, version: thmVer } = themeVersion;
      score = themeVersionCompare(compare, thmVer, false) ? score + 1 : score - 1;
    }
    if (all) {
      score += 1;
    }
    return { ...settings, score };
  })
    .filter((x) => x)
    .reduceRight(
      (prev, settings) => {
        if (typeof prev === 'undefined') {
          return settings;
        }
        return ((settings.score >= (prev.score)) ? settings : prev);
      },
    );
  const defaultGroup = blockSettingGroup?.find(({ isDefault }) => isDefault);
  return defaultGroup ? { ...defaultGroup, ...matchedGroup } : matchedGroup;
};
