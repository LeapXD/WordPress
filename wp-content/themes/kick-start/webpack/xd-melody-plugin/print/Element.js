
/* eslint-disable no-nested-ternary */
import prettier from 'prettier';
const  {
  group, line, hardline, softline, indent, join
} = prettier.doc.builders;
import {
  removeSurroundingWhitespace,
    isInlineElement,
    isOwnlineElement,
    printChildGroups,
    EXPRESSION_NEEDED,
    STRING_NEEDS_QUOTES
} from '../node_modules/@zackad/prettier-plugin-twig/src/util/index.js';



const printOpeningTag = (node, path, print, options) => {

    const groupId = Symbol("opening-tag");

    const opener = "<" + node.name;
    const printedAttributes = printSeparatedList(path, print, "", "attributes");

    const openingTagEnd = [];
    if (node.selfClosing) {
        if (!options.bracketSameLine) {
            openingTagEnd.push(line);
        } else {
            openingTagEnd.push(" ");
        }
        openingTagEnd.push("/>");
    } else {
        if (!options.bracketSameLine) {
            openingTagEnd.push(softline);
        }
        openingTagEnd.push(">");
    }

    const hasAttributes = node.attributes && node.attributes.length > 0;
    if (hasAttributes) {
        return group(
            [opener, indent([line, printedAttributes]), openingTagEnd],
            { id: groupId }
        );
    }

    return group([opener, openingTagEnd], { id: groupId });
};

const printSeparatedList = (path, print, separator, attrName) => {
    const attributes = path.map(print, attrName);
    const node = path.getValue();

    // Check if attributes span multiple lines in the source document
    const spansMultipleLines = node.attributes.some((attr, index, attrs) => {
        if (index === 0) return false; // Skip the first attribute
        return attr.loc.start.line > attrs[index - 1].loc.end.line;
    });

    // Use hardline if attributes span multiple lines in the source document
    const separatorToUse = spansMultipleLines ? hardline : line;

    return join([separator, separatorToUse], attributes);
};

// const printSeparatedList = (path, print, separator, attrName) => {
//     return join([separator, line], path.map(print, attrName));
// };

const printElement = (node, path, print, options) => {
    // Set a flag in case attributes contain, e.g., a FilterExpression
    node[EXPRESSION_NEEDED] = true;
    const openingGroup = group(printOpeningTag(node, path, print, options));
    node[EXPRESSION_NEEDED] = false;
    node[STRING_NEEDS_QUOTES] = false;

    if (node.selfClosing) {
        return openingGroup;
    }

    const groupElement = Symbol("element");
    node.children = removeSurroundingWhitespace(node.children);

    const childGroups = printChildGroups(node, path, print, "children");
    const closingTag = ["</", node.name, ">"];
    const result = [openingGroup];
    const joinedChildren = childGroups;
    if (isOwnlineElement(node) || isInlineElement(node)) {
        const element = [indent([softline, joinedChildren]), softline];
        result.push(element);
    } else {
        const childBlock = [];
        if (childGroups.length > 0) {
            childBlock.push(hardline);
        }
        childBlock.push(joinedChildren);
        result.push(indent(childBlock));
        if (childGroups.length > 0) {
            result.push(hardline);
        }
    }
    result.push(closingTag);

    return group(result, { id: groupElement });
};

export { printElement };
