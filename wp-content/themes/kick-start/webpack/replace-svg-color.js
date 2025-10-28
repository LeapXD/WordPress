// This is a webpack loader.

/**
 * @param {string} source
 */
module.exports = function (source) {
  const callback = this.async();

  const name = this.resourcePath.split('/').pop().split('.')[0];

  callback(null, source
    .replace(/<svg/, `<svg class="xd-svg xd-svg--${name}"`)
    .replace(/fill="(?!none)[^"]+"/g, 'fill="currentColor"')
    .replace(/stroke="(?!none)[^"]+"/g, 'stroke="currentColor"'));
};
