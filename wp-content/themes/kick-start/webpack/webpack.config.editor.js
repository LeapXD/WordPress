const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const path = require('path');
const glob = require('glob');

const buildFolder = path.resolve('./build');
const fs = require('fs');

module.exports = () => {
  const editor = './editor/block-editor.js';

  fs.watch('./assets', { recursive: true }, (eventType) => {
    if (eventType === 'rename') {
      fs.writeFileSync(editor, fs.readFileSync(editor));
    }
  });
  fs.watch('./editor', { recursive: true }, (eventType) => {
    if (eventType === 'rename') {
      fs.writeFileSync(editor, fs.readFileSync(editor));
    }
  });

  const blockOverrides = glob.sync('./assets/scss/block-overrides/**/*.scss');

  const getEntries = () => {
    const blockStyles = [
      // ...glob.sync(path.resolve('../kicks/assets/scss/blocks/**/*.scss')),
      ...glob.sync(path.resolve('./assets/scss/blocks/**/*.scss'))].reduce(
      (acc, file) => {
        const name = file.match(/scss\/(blocks\/.+)\.scss/)[1].replace('blocks/_', 'block-styles/');
        return {
          ...acc,
          [name]: file,
        };
      },
      {},
    );
    return {
      editor,
      ...blockStyles,
      ...blockOverrides,
    };
  };

  const config = {
    watch: true,
    entry: getEntries,
    mode: 'development',
    output: {
      filename: 'js/[name].js',
      publicPath: '',
      path: buildFolder,
    },
    devtool: 'eval-cheap-module-source-map',
    plugins: [
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: ['**/editor.*'],
      }),
      new MiniCssExtractPlugin({
        filename: 'css/[name].css',
      }),
    ],
  };

  return config;
};
