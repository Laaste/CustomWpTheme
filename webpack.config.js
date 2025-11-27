const fs = require('fs');
const path = require('path');
const glob = require('glob');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const WatchComponentsPlugin = require('./watch-components-plugin');

module.exports = (env) =>
{
	const watch = env.watch == 'true';

	// --- GENERUJ COMPOSABLE.SCSS ---
	const componentsFolder = path.resolve(__dirname, 'assets/scss/theme/components');
	const composableFile = path.resolve(__dirname, 'assets/scss/composable.scss');

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

	// --- GENERUJ TEMPLATES.SCSS ---
	const templatesFolder = path.resolve(__dirname, 'assets/scss/theme/templates');
	const templatesIndex = path.resolve(__dirname, 'assets/scss/templates.scss');

	function generateTemplatesIndex() {
		const templateFiles = glob.sync(`${templatesFolder}/**/*.scss`).map(f => f.replace(/\\/g, '/'));
		let templateContent = '// AUTO GENERATED\n';
		templateContent += templateFiles.map(f => {
			const relative = path.relative(path.resolve(__dirname, 'assets/scss'), f).replace(/\\/g, '/');
			return `@use './${relative}';`;
		}).join('\n');
		fs.writeFileSync(templatesIndex, templateContent);
	}

	generateTemplatesIndex();

	// --- WATCH NA FOLDERACH ---
	if (watch) {
		// Watch components
		fs.watch(componentsFolder, { recursive: true }, (eventType, filename) => {
			if (filename && filename.endsWith('.scss')) generateComposable();
		});
		// Watch templates
		fs.watch(templatesFolder, { recursive: true }, (eventType, filename) => {
			if (filename && filename.endsWith('.scss')) generateTemplatesIndex();
		});
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
			'css/templates': templatesIndex
		},
		output: {
			path: path.resolve(__dirname, 'assets/')
		},
		plugins: [
			new FixStyleOnlyEntriesPlugin(),
			new MiniCssExtractPlugin({ filename: '[name].css' }),
			new WatchComponentsPlugin([componentsFolder])
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
