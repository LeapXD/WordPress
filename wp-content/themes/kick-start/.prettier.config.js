const wp = require('@wordpress/prettier-config');
const XdTwigPlugin = require('xd-melody-plugin');

const config = {
  ...wp,
  plugins: [
    ...(wp.plugins || []),
    // We don't need the base twig plugin since xd-melody-plugin includes it.
    // require.resolve('@zackad/prettier-plugin-twig'),
    XdTwigPlugin (
      {
        printWidth: 512,
        singleAttributePerLine: true,
        twigAlwaysBreakObjects: true,
        useTabs: false,
        tabWidth: 2,
        bracketSameLine: true,
      } 
    ),
  ]
};

module.exports = config;
