import { doc } from "prettier";

const { group, line, hardline, indent, join } = doc.builders;

import {
  EXPRESSION_NEEDED,
  wrapExpressionIfNeeded,
} from '../node_modules/@zackad/prettier-plugin-twig/src/util/index.js';

const printObjectExpression = (node, path, print, options) => {
    if (node.properties.length === 0) {
        return "{}";
    }
    node[EXPRESSION_NEEDED] = false;
    const mappedElements = path.map(print, "properties");
      const separator = options.twigAlwaysBreakObjects && node.properties.length > 1 ? hardline : line;
    const indentedContent = [line, join([",", separator], mappedElements)];

    const parts = ["{", indent(indentedContent), separator, "}"];
    wrapExpressionIfNeeded(path, parts, node);

    return group(parts);
};

export { printObjectExpression };
