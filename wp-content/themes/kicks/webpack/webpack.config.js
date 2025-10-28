const GenerateSassIndexesPlugin = require('./generate-sass-indexes-plugin');

/* eslint-disable global-require */
module.exports = async (env) => {
	const Autoprefixer = require('autoprefixer');
	const MiniCssExtractPlugin = require('mini-css-extract-plugin');
	const path = require('path');
	const { merge } = require('webpack-merge');
	const mode = typeof env.NODE_ENV !== 'undefined' ? env.NODE_ENV : 'none';
	const isProduction = typeof env.production !== 'undefined';
	const isBuild = typeof env.build !== 'undefined';
	const isEditor = typeof env.editor !== 'undefined';
	let env_config = {};
	switch (true) {
		case isEditor:
			env_config = await require('./webpack.config.editor')(env);
			break;
		case isBuild || isProduction:
			env_config = await require('./webpack.config.build')(env);
			break;
		default:
			env_config = await require('./webpack.config.watch')(env);
	}
	const config = {
		mode,
		resolve: {
			modules: ['node_modules', './'],
			alias: {
				'uikit-util': 'uikit/src/js/util',
				'uikit/api': 'uikit/src/js/api',
				'uikit/core': 'uikit/src/js/core',
				'uikit/components': 'uikit/src/js/components',
				'uikit/mixin': 'uikit/src/js/mixin',
				'lodash-es': 'lodash',
				'uikit-custom': 'assets/js/uikit-custom',
				'uikit-components': 'assets/js/uikit-components',
			},
		},
		externals: [
			function ({ context, request }, callback) {
				if (/^@wordpress\/.+(?<!icons)$/.test(request)) {
					const [first, ...rest] = request.replace('@wordpress/', 'wp.').split('-');
					return callback(
						null,
						`${first}${rest.map((_) => `${_.slice(0, 1).toUpperCase()}${_.slice(1)}`).join('')}`
					);
				}
				callback();
			},
			{
				lodash: 'lodash',
				// 'uikit-custom': 'UIkit',
				// 'uikit-components': 'uikitComponents',
			},
		],
		plugins: [
			new GenerateSassIndexesPlugin({
      roots: [
        path.resolve(process.cwd(), 'assets/scss'), 
        path.resolve(process.cwd(), 'editor/scss'),
        // path.resolve(process.cwd(), 'editor/scss/components')
      ],
      // Optional: only index feature folders, e.g. /^components|theme|typography/
      // folderFilter: /^(components|theme|typography)(\/|$)/,
    }),
		],
		optimization: {
			// runtimeChunk: 'single',
			splitChunks: {
				chunks: (chunk) => !chunk.name,
				maxInitialRequests: Infinity,
				minSize: 0,
				cacheGroups: {
					uikit: {
						reuseExistingChunk: true,
						test: /[\\/]node_modules\/uikit[\\/]/,
						name(module) {
							if (module.rawRequest.match(/uikit\/(core|component)/g)) {
								return module.rawRequest;
							}
							const file = module.request.replace(/.+\/(.+)\.js/, '$1');
							const context = module.context.replace(/.+\/(.+)/, '$1');
							if (context === 'mixin') {
								return `uikit/mixin/${file}`;
							}
							return `uikit/${context}`;
						},
					},
					jquery: {
						reuseExistingChunk: true,
						test: /jquery/,
						name: 'jquery-migrate',
					},
				},
			},
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					// exclude: /node_modules\/(?!(uikit\/src\/js\/api\/app|uikit\/src\/js\/api\/hooks)).*/,
					use: [
						{
							loader: 'babel-loader',
						},
						{
							loader: path.resolve('webpack/uikit-build.js'),
						},
						{
							loader: 'webpack-import-glob',
						},
					],
				},
				{
					test: /\.s[ac]ss$/i,
					use: [
						isBuild || isProduction || isEditor ? MiniCssExtractPlugin.loader : 'style-loader',
						{
							loader: 'css-loader',
							options: { sourceMap: true, url: true },
						},
						{
							loader: 'postcss-loader',
							options: {
								postcssOptions: {
									plugins: [
										Autoprefixer(),
										require('@tailwindcss/postcss'),
									],
								},
							},
						},
						{ loader: 'resolve-url-loader' },
						{
							loader: 'sass-loader',
							options: {
								implementation: require('sass'),
								sourceMap: true,
								webpackImporter: true,
								sassOptions: {
									loadPaths: [
										path.resolve(process.cwd(), 'assets/scss'),
										path.resolve(process.cwd(), 'node_modules'),
									],
								},
							},
						},
					]
        },
				{
					test: /assets\/icons\/.*\.svg$/,
					use: [
						{
							loader: 'svg-sprite-loader',
							options: {
								extract: true,
								spriteFilename: 'icons/icons.svg',
							},
						},
						{
							loader: 'svgo-loader',
							options: {
								plugins: [
									{
										name: 'removeTitle',
									},
									{
										name: 'convertColors',
										params: {
											shorthex: false,
										},
									},
									{
										name: 'convertPathData',
										active: false,
									},
								],
							},
						},
						{
							loader: path.resolve('webpack/replace-svg-color.js'),
						},
					],
				},
				{
					test: /images\/.*\.(svg|png|jpe?g|gif)$/i,
					loader: 'file-loader',
					exclude: /node_modules\/(?!(uikit\/src\/images)).*/,
					options: {
						name: isBuild || isProduction || isEditor ? 'images/[ext]/[name].[ext]' : '[name].[ext]',
					},
					generator: {
						emit: false,
						filename: '[name][ext]',
						publicPath:
							isBuild || isProduction || isEditor
								? (pathData) => {
										const buildPath = pathData.filename.replace(/assets|[^/]+\.*$/g, '');
										return buildPath;
									}
								: '/',
					},
				},
				{
					test: /fonts\/.*\.(woff2?|ttf|otf|eot|svg)$/,
					type: 'asset/resource',
					generator: {
						filename: 'fonts/[name][ext]',
						publicPath: isBuild || isProduction ? '' : '/',
						outputPath: '',
					},
				},
			],
		},
	};
	return merge(config, env_config);
};
