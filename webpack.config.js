const fs = require('fs');
const path = require('path');
const glob = require('glob');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const WatchComponentsPlugin = require('./watch-components-plugin');

module.exports = (env) =>
{
	const watch = env.watch === 'true';

	// --- DIRECTORIES ---
	const themeRootFolder = path.resolve(__dirname, 'assets/scss/theme');

	const componentsFolder = path.resolve(__dirname, 'assets/scss/theme/components');
	const composableFile = path.resolve(__dirname, 'assets/scss/composable.scss');

	const templatesFolder = path.resolve(__dirname, 'assets/scss/theme/templates');

	// --- GENERATE COMPOSABLE.SCSS ---
	function generateComposable() {
		const componentFiles = glob.sync(`${componentsFolder}/**/*.scss`).map(f => f.replace(/\\/g, '/'));
		let composableData = '// THIS FILE IS AUTOUPDATED, DON\'T EDIT MANUALLY\n';
		composableData += componentFiles.map(f => {
			const relative = path.relative(path.resolve(__dirname, 'assets/scss'), f).replace(/\\/g, '/');
			return `@use './${relative}';`;
		}).join('\n');
		fs.writeFileSync(composableFile, composableData);
	}
	generateComposable();

	// --- WATCH FOLDERS ---
	if (watch) {
		fs.watch(componentsFolder, { recursive: true }, (eventType, filename) => {
			if (filename && filename.endsWith('.scss')) generateComposable();
		});
		fs.watch(templatesFolder, { recursive: true }, (eventType, filename) => {
			// nothing to generate, entries are dynamic
		});
	}

	// Compile scss files in template root
	function getThemeRootEntries()
	{
		const entries = {};
		const files = glob.sync(`${themeRootFolder}/*.scss`);

		files.forEach(file =>
		{
			const name = path.basename(file, '.scss');
			entries[`css/theme/${name}`] = './' + path.relative(__dirname, file).replace(/\\/g, '/');
		});

		return entries;
	}

	// --- DYNAMIC TEMPLATE ENTRIES ---
	function getTemplateEntries() {
		const entries = {};
		const files = glob.sync(`${templatesFolder}/*.scss`);
		files.forEach(file => {
			const name = path.basename(file, '.scss');
			// <-- IMPORTANT: dynamic path from './'
			entries[`css/theme/templates/${name}`] = './' + path.relative(__dirname, file).replace(/\\/g, '/');
		});
		return entries;
	}

	return {
		mode: env.mode,
		resolve: {
			alias: {
				'@rootScss': path.resolve(__dirname, 'assets/scss'),
				'@templatesScss': templatesFolder,
				'@componentsScss': componentsFolder,
			},
			extensions: ['.js', '.css', '.scss']
		},
		entry: {
			'js/main-compiled': path.resolve(__dirname, 'assets/js/main.js'),
			'css/styles': path.resolve(__dirname, 'assets/scss/styles.scss'),
			'css/composable': composableFile,
			...getThemeRootEntries(),
			...getTemplateEntries() // each template as separated file
		},
		output: {
			path: path.resolve(__dirname, 'assets/')
		},
		plugins: [
			new FixStyleOnlyEntriesPlugin(),
			new MiniCssExtractPlugin({ filename: '[name].css' }),
			new WatchComponentsPlugin([componentsFolder, templatesFolder])
		],
		watch: watch,
		module: {
			rules: [
				{
					test: /\.(scss|css)$/,
					use: [
						MiniCssExtractPlugin.loader,
						{ loader: 'css-loader', options: { url: false } },
						'sass-loader'
					]
				}
			]
		}
	};
};