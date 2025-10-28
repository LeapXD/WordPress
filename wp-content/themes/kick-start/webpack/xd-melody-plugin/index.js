const {default:basePlugin} = require('@zackad/prettier-plugin-twig');
const { printElement } = require('./print/Element');
const { printObjectExpression } = require('./print/ObjectExpression');

const basePrinter = basePlugin?.printers?.twig;

if (!basePrinter || typeof basePrinter.print !== 'function') {
  throw new Error('xd-melody-plugin: unable to load base melody printer from @zackad/prettier-plugin-twig-melody');
}

const overridePrinters = {
  Element: printElement,
  ObjectExpression: printObjectExpression,
};

const enhancedMelodyPrinter = (options = {}) => ({
  ...basePrinter,
  print(path, _options, print) {

    const opts = { ..._options, ...options };

    const baseDoc = basePrinter.print(path, opts, print);
    const node = typeof path?.getValue === 'function' ? path.getValue() : undefined;
    const nodeType = node?.constructor?.name;
    const override = nodeType ? overridePrinters[nodeType] : undefined;

    if (!override) {
      return baseDoc;
    }

    // Respect prettier-ignore regions which return raw strings from the base printer.
    if (typeof baseDoc === 'string') {
      return baseDoc;
    }

    try {
      return override(node, path, print, opts);
    } catch (error) {
      console.error(`xd-melody-plugin: error in override printer for node type ${nodeType}:`, error);
      // Fall back to the default printer if our override cannot handle the node.
      return baseDoc;
    }
  },
});


module.exports = (options = {}) => ({
  ...basePlugin,
  printers: {
    twig: enhancedMelodyPrinter(options), // Override the melody printer
  },
});