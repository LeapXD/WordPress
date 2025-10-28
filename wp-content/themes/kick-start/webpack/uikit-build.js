// This is a webpack loader.

const { version } = require('uikit/package.json');

/**
 * @param {string} source
 */
module.exports = function (source) {
  const callback = this.async();

  if (this.resourcePath.match(/\/uikit\/src\/js\/api/)) {
    callback(null, source
      .replace(/LOG/, 0)
      .replace(/VERSION/, `'${version}'`));
  } else {
    callback(null, source);
  }
};
