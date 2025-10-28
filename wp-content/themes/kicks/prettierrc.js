const wp = require('@wordpress/prettier-config');

const melodyPluginPath = require.resolve('xd-melody-plugin', {
	paths: [__dirname],
});

module.exports = {
	...wp,
	overrides: [
		...(wp.overrides || []),
		{
			files: ['**/*.twig'],
			options: {
				printWidth: 120,
				singleAttributePerLine: true,
				twigPrintWidth: 512,
				twigAlwaysBreakObjects: true,
				parser: 'twig',
				plugins: [require.resolve('@zackad/prettier-plugin-twig')],
				twigMelodyPlugins: [melodyPluginPath],
				useTabs: false, // Use spaces for Twig files
				tabWidth: 2, // 2 spaces per indent
			},
		},
	],
};
