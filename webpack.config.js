const fs = require('fs');
const path = require('path');
const glob = require('glob');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const WatchComponentsPlugin = require('./watch-components-plugin');

module.exports = (env) =>
{
	const watch = (env.watch == 'true') ? true : false;

	// Use glob patter to include nested SCSS files to make new composable.css file which includes all components scss for modular pages that require them all
	const componentFiles = glob.sync(path.resolve(__dirname, 'assets/scss/theme/components/**/*.scss').replace(/\\/g, '/'));

	let composableData = '// THIS FILE IS AUTOUPDATED DON\'t PUT ANY CONTENT IN IT\n'; //Note to warn to dont manually edit composable.scss
	composableData = composableData + componentFiles
	.map(file =>
	{
		const relativePath = path.relative(path.resolve(__dirname, 'assets/scss'), file).replace(/\\/g, '/');

		return `@use './${relativePath}';`;
	})
	.join('\n');

	fs.writeFileSync(path.resolve(__dirname, 'assets/scss/composable.scss'), composableData);

	// Use glob pattern to include nested SCSS files for templates wordpress like
	const templateFiles = glob.sync(path.resolve(__dirname, 'assets/scss/theme/**/*.scss').replace(/\\/g, '/'));

	const templateEntries = templateFiles.reduce((entries, file) =>
	{
		// Create relative path for nested files
		const relativePath = path.relative(path.resolve(__dirname, 'assets/scss'), file);
		const name = relativePath.replace(/\.scss$/, '');
		entries[`css/${name}`] = file.replace(/\\/g, '/');

		return entries;
	}, {});

	return {
		mode: env.mode,
		resolve:
		{
			alias: {
				'@rootScss': path.resolve(__dirname, 'assets/scss'),
				'@templatesScss': path.resolve(__dirname, 'assets/scss/theme/templates'),
				'@componentsScss': path.resolve(__dirname, 'assets/scss/theme/components'),
			},
			extensions: [
				'.js',
				'.css',
				'.scss'
			]
		},
		entry: {
			'js/main-compiled': path.resolve(__dirname, 'assets/js/main.js'),
			'css/styles': path.resolve(__dirname, 'assets/scss/styles.scss'),
			'css/composable': path.resolve(__dirname, 'assets/scss/composable.scss'),
			...templateEntries
		},
		output: {
			path: path.resolve(__dirname, 'assets/'),
		},
		plugins: [
			new FixStyleOnlyEntriesPlugin(),
			new MiniCssExtractPlugin({
				filename: '[name].css'
			}),
			new WatchComponentsPlugin(componentFiles) // create composable when any component get compiled
		],
		watch: watch,
		module: {
			rules: [
				{
					test: /\.(scss|css)$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
							options: {
								url: false
							}
						},
						'sass-loader'
					]
				}
			]
		}
	}
}
