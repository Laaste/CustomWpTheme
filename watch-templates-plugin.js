const fs = require('fs');
const path = require('path');
const glob = require('glob');

class WatchTemplatesPlugin {
	constructor(folder) {
		this.folder = folder;
	}

	apply(compiler) {
		compiler.hooks.thisCompilation.tap('WatchTemplatesPlugin', (compilation) => {
			compilation.hooks.additionalAssets.tap('WatchTemplatesPlugin', () => {
				const files = glob.sync(`${this.folder}/*.scss`);
				files.forEach(file => {
					compilation.fileDependencies.add(file);
				});
			});
		});
	}
}

module.exports = WatchTemplatesPlugin;