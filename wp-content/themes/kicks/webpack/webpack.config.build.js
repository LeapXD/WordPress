/* eslint-disable global-require */
module.exports = (env) => {
  const MiniCssExtractPlugin = require('mini-css-extract-plugin');
  const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');
  const path = require('path');
  const { CleanWebpackPlugin } = require('clean-webpack-plugin');
  const TerserPlugin = require('terser-webpack-plugin');
  const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
  const SpriteLoaderPlugin = require('svg-sprite-loader/plugin');
  const glob = require('glob');
  const { default: ImageminPlugin } = require('imagemin-webpack-plugin');
  // const ImageminWebpWebpackPlugin = require('imagemin-webp-webpack-plugin');
  const { default: ImageminMozjpeg } = require('imagemin-webpack-plugin');
  const isProduction = typeof env.production !== 'undefined';
  const buildFolder = path.resolve('./build');
  const isDevelopment = typeof env.development !== 'undefined';
  const publicPath = isDevelopment ? '/' : buildFolder.replace(/.*\/wp-content\/(.*)/, '/wp-content/$1/');

  const blocks = glob.sync('./editor/blocks/**/view-script.js').reduce((acc, file) => {
    const name = file.match(/editor\/(blocks\/.+)\/view-script.js/)[1];
    return {
      ...acc,
      [name]: {
        import: file,
        dependOn: ['main'],
      },
    };
  }, {});

  const blockStyles = glob.sync('./assets/scss/blocks/**/*.scss').reduce(
    (acc, file) => {
      const name = file.match(/scss\/(blocks\/.+)\.scss/)[1].replace('blocks/_', 'block-styles/');
      return {
        ...acc,
        [name]: file,
      };
    },
    {},
  );

  const entry = {
    editor: './editor/block-editor.js',
    // jquery: './assets/js/jquery.js',
    main: ['./assets/js/main.js', './assets/scss/main.scss'],
    // tailwind: ['./assets/scss/tailwind.scss'],
    blocks: './assets/scss/blocks.scss',
    'icons/icons': glob.sync('./assets/icons/*.svg'),
    'images/images': glob.sync('./assets/images/**/*.*'),
    ...blockStyles,
    ...blocks,
  };

  const config = {
    watch: false,
    entry,
    output: {
      filename: isProduction ? 'js/[name].[chunkhash].min.js' : 'js/[name].js',
      publicPath,
      path: buildFolder,
    },
    devtool: isProduction ? 'nosources-source-map' : 'eval-cheap-module-source-map',
    plugins: [

      new MiniCssExtractPlugin({
        filename: isProduction ? 'css/[name].[chunkhash].min.css' : 'css/[name].css',
      }),
      new IgnoreEmitPlugin([/icons\/icons(.+)?\.js/, /images\/images\.js/, /css\/.+\.js/, /scss\/blocks\/.+\.js/]),
      new CleanWebpackPlugin({
        cleanOnceBeforeBuildPatterns: ['**/*'],
        cleanStaleWebpackAssets: true,
      }),
      new WebpackManifestPlugin({
        filter: (file) => file.path.match(/sprite.svg$|icons\/icons.svg$|js\/(?!images|icons|uikit|block-styles).+\.js$|css\/.+\.css$/),
      }),
      new SpriteLoaderPlugin({
        plainSprite: true,
        spriteAttrs: {
          id: 'svg-sprite',
        },
      }),
      // new ImageminWebpWebpackPlugin({
      // 	config: [{
      // 		test: /\.(jpe?g|png)/,
      // 		options: {
      // 			quality:  100
      // 		}
      // 	}],
      // }),
      new ImageminPlugin({
        optipng: { optimizationLevel: 7 },
        gifsicle: { optimizationLevel: 3 },
        pngquant: { quality: '65-90', speed: 4 },
        svgo: {
          plugins: [
            { removeUnknownsAndDefaults: false },
            { cleanupIDs: false },
          ],
        },
        plugins: [new ImageminMozjpeg({ quality: 75 })],
        disable: false,
      }),

    ].filter((_) => _),
  };

  if (isProduction) {
    config.optimization = {
      minimizer: [new TerserPlugin()],
    };
  }

  return config;
};
