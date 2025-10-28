/* eslint-disable no-continue */
/* eslint-disable no-restricted-syntax */
/* eslint-disable no-nested-ternary */
// block-supports-context.js
import { getBlockType } from '@wordpress/blocks';
import { select } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as editorStore } from '@wordpress/editor';
import { useXdBlockContext } from '../components/context';

const isObj = (v) => v !== null && typeof v === 'object' && !Array.isArray(v);

function mergeContexts(base, overlay) {
  if (overlay === undefined) return base;
  if (base === true || overlay === true) return true;

  const normalize = (x) => (x === undefined ? undefined : (typeof x === 'string' || typeof x === 'number') ? [x] : x);

  const a = normalize(base);
  const b = normalize(overlay);

  if (Array.isArray(a) && Array.isArray(b)) {
    return Array.from(new Set([...a, ...b]));
  }
  if (isObj(a) && isObj(b)) {
    const out = { ...a };
    for (const [k, v] of Object.entries(b)) {
      const prev = out[k];
      if (Array.isArray(prev) && Array.isArray(v)) {
        out[k] = Array.from(new Set([...prev, ...v]));
      } else {
        out[k] = v;
      }
    }
    return out;
  }
  return overlay; // different kinds → overlay wins
}

function attrMatches(ruleValue, actual) {
  if (Array.isArray(ruleValue)) {
    return ruleValue.some((v) => (Array.isArray(actual) ? actual.includes(v) : actual === v));
  }
  if (Array.isArray(actual)) return actual.includes(ruleValue);
  return actual === ruleValue;
}

function attributesWhenMatches(whenAttrs, attrs = {}) {
  return Object.entries(whenAttrs).every(([key, expected]) => attrMatches(expected, attrs[key]));
}

const toArr = (v) => (Array.isArray(v) ? v : v == null ? [] : [v]);

function ruleWhenMatches(when, meta) {
  if (!when || typeof when !== 'object') return false;

  if (when.parent !== undefined) {
    if (!meta.parent) return false;
    if (!toArr(when.parent).includes(meta.parent)) return false;
  }
  if (when.ancestor !== undefined) {
    const ancWanted = toArr(when.ancestor);
    if (!Array.isArray(meta.ancestors) || !meta.ancestors.some((a) => ancWanted.includes(a))) {
      return false;
    }
  }
  if (when.postType !== undefined) {
    const typesWanted = toArr(when.postType);
    if (!meta.postType || !typesWanted.includes(meta.postType)) return false;
  }
  if (when.attributes !== undefined) {
    if (!attributesWhenMatches(when.attributes, meta.attributes)) return false;
  }
  return true;
}

function extractBaseFeature(featureDef) {
  if (!isObj(featureDef)) return featureDef; // primitive/array/true/undefined
  const { context, ...rest } = featureDef;
  return Object.keys(rest).length ? rest : undefined;
}

/**
 * Get effective feature context (supports value) for a feature, with mixed `context` entries:
 * - bare overlay (string/number/array/object/true) → always merged
 * - rule object { when, supports } → merged when `when` matches runtime meta
 *
 * @param {string} name   e.g. "xd/content"
 * @param {string} featureName e.g. "xd/horizontal-align"
 * @returns {*} context to feed into matchesCondition / cleanObjectDeep
 */
export default function getEffectiveFeatureContext(name, featureName) {
  const blockType = getBlockType(name);
  if (!blockType) return undefined;

  const featureDef = blockType.supports?.[featureName];
  const base = extractBaseFeature(featureDef);
  const contextItems = Array.isArray(featureDef?.context) ? featureDef.context : [];

  if (base === true) return true;

  const { getBlockParents, getBlockName } = select(blockEditorStore);
  const { getCurrentPostType } = select(editorStore);

  const { clientId, attributes } = useXdBlockContext();

  let parent; let
    ancestors = [];
  if (clientId && getBlockParents && name) {
    const parentIds = getBlockParents(clientId) || [];
    ancestors = parentIds.map((id) => getBlockName(id)).filter(Boolean);
    parent = parentIds.length ? getBlockName(parentIds[parentIds.length - 1]) : undefined;
  }

  const postType = getCurrentPostType();
  const meta = {
    parent, ancestors, postType, attributes,
  };

  // apply base, then each context item in order
  let ctx = base;

  for (const item of contextItems) {
    // unconditional overlay (string/number/array/object/true)
    const isBareOverlay = item === true
      || typeof item === 'string'
      || typeof item === 'number'
      || Array.isArray(item)
      || (isObj(item) && (item.when === undefined || item.supports === undefined));

    if (isBareOverlay && item !== undefined) {
      ctx = mergeContexts(ctx, item);
      continue;
    }

    // conditional rule { when, supports }
    if (isObj(item) && item.when && item.supports !== undefined) {
      if (ruleWhenMatches(item.when, meta)) {
        ctx = mergeContexts(ctx, item.supports);
      }
      continue;
    }

    // ignore unknown shapes silently
  }

  return ctx;
}
