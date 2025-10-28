/* eslint-disable no-continue */
/* eslint-disable no-restricted-syntax */

/**
 * Evaluates whether a given `if` condition matches a provided block support context.
 *
 * @param {*} condition - The `if` clause from the module. Can be:
 *   - string → feature key
 *   - array of strings → feature keys or values
 *   - object → feature-value map
 *
 * @param {*} context - The block's support config. Can be:
 *   - `true` → always match
 *   - object → structured block supports
 *   - string or array → flat support values
 *
 * @returns {boolean} Whether the condition is satisfied in the given context.
 *
 * Supported `if` clause matching behavior:
 *
 * 1. `if` is a string:
 *    - context is `true` → always matches
 *    - context is object → matches if context[if] is truthy
 *    - context is string → matches if equal
 *    - context is array  → matches if context includes string
 *
 * 2. `if` is an array of strings:
 *    - context is object → all keys must be truthy in context
 *    - context is string → matches if value is in array
 *    - context is array  → matches if any value in `if` is in context
 *
 * 3. `if` is an object (feature-value map):
 *    - context is object → all keys must match using:
 *        - equality (`===`)
 *        - array includes
 *        - partial overlap for array values
 *
 * Examples:
 *   matchesCondition("align", { align: true })                         → ✅
 *   matchesCondition("left", ["left", "center"])                       → ✅
 *   matchesCondition(["align", "padding"], { align: true })            → ❌
 *   matchesCondition({ align: "left" }, { align: "left" })             → ✅
 *   matchesCondition({ align: ["left", "right"] }, { align: ["center", "right"] }) → ✅
 *   matchesCondition(["left", "center"], "left")                       → ✅
 *   matchesCondition(["left", "center"], "right")                      → ❌
 */
export function matchesCondition(condition, context) {
  // Always allow if context is explicitly `true`
  if (context === true) {
    return true;
  }

  if (typeof context === 'string' || typeof context === 'number') {
    if (typeof condition === typeof context) {
      return context === condition;
    }
    if (Array.isArray(condition)) {
      return condition.includes(context);
    }
    return false;
  }

  if (Array.isArray(context)) {
    if (typeof condition === 'string' || typeof condition === 'number') {
      return context.includes(condition);
    }
    if (Array.isArray(condition)) {
      return condition.some((value) => context.includes(value));
    }
    return false;
  }

  if (typeof context === 'object' && context !== null) {
    // "if" is a string → require the corresponding key to be truthy
    if (typeof condition === 'string') {
      return Boolean(context[condition]);
    }

    // "if" is an array of strings → all keys must be truthy in context
    if (Array.isArray(condition) && condition.every((entry) => typeof entry === 'string')) {
      return condition.every((key) => Boolean(context[key]));
    }

    // "if" is an object → match each key-value pair
    if (typeof condition === 'object') {
      return Object.entries(condition).every(([key, value]) => {
        const contextValue = context[key];

        if (Array.isArray(value)) {
          if (Array.isArray(contextValue)) {
            return value.some((v) => contextValue.includes(v));
          }
          return value.includes(contextValue);
        }

        if (Array.isArray(contextValue)) {
          return contextValue.includes(value);
        }

        return contextValue === value;
      });
    }
  }

  return false;
}

/**
 * Recursively cleans an object by removing properties that don't match the given context.
 *
 * @param {*} input - The input object to clean.
 * @param {*} context - The context to use for matching.
 * @returns {*} The cleaned object, or undefined if it should be excluded.
 *
 * Examples:
 *   cleanObjectDeep({ if: "align", align: "left" }, { align: true }) → { align: "left" }
 *   cleanObjectDeep({ if: "align", align: "left" }, { align: false }) → undefined
 *   cleanObjectDeep({ a: 1, b: { if: "feature", c: 2 } }, { feature: true }) → { a: 1, b: { c: 2 } }
 *   cleanObjectDeep({ a: 1, b: { if: "feature", c: 2 } }, { feature: false }) → { a: 1 }
 */
export default function cleanObjectDeep(node, context) {
  return prune(node, context).node;
}

// Returns { node: prunedNodeOrUndefined, pass: boolean }
// pass === true means: some node in this subtree had an "if" that evaluated TRUE
function prune(node, context) {
  // primitives pass through, do not trigger explicit pass
  if (node == null || typeof node !== 'object') {
    return { node, pass: false };
  }

  // arrays
  if (Array.isArray(node)) {
    const out = [];
    let anyPass = false;
    for (const item of node) {
      const res = prune(item, context);
      if (res.node !== undefined && !isEmptyContainer(res.node)) {
        out.push(res.node);
        anyPass = anyPass || res.pass;
      }
    }
    return out.length ? { node: out, pass: anyPass } : { node: undefined, pass: anyPass };
  }

  // objects
  const hasIf = Object.prototype.hasOwnProperty.call(node, 'if');
  const condition = hasIf ? node.if : undefined;

  // clean children first
  const result = {};
  let anyChildPass = false;
  for (const [k, v] of Object.entries(node)) {
    if (k === 'if') {
      continue; // will be evaluated here
    }
    const res = prune(v, context);
    if (res.node !== undefined && !isEmptyContainer(res.node)) {
      result[k] = res.node;
      anyChildPass = anyChildPass || res.pass;
    }
  }
  const hasContent = Object.keys(result).length > 0;

  if (hasIf) {
    const ok = matchesCondition(condition, context);

    if (ok) {
      // This node explicitly passed an "if"
      return hasContent ? { node: result, pass: true } : { node: undefined, pass: true };
    }

    // Failed own "if": only keep if some DESCENDANT explicitly passed
    if (anyChildPass) {
      return hasContent ? { node: result, pass: true } : { node: undefined, pass: true };
    }

    // No explicit passes below → drop
    return { node: undefined, pass: false };
  }

  // No "if" on this node: keep if it has content; pass flag bubbles from children only
  return hasContent ? { node: result, pass: anyChildPass } : { node: undefined, pass: anyChildPass };
}

function isEmptyContainer(v) {
  if (Array.isArray(v)) return v.length === 0;
  if (v && typeof v === 'object') return Object.keys(v).length === 0;
  return false;
}
