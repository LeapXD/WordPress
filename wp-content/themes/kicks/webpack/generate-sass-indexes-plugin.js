// Minimal Webpack plugin to (re)generate _index.scss files and
// keep them fresh in watch mode when files are added/removed.

const fs = require('fs');
const path = require('path');
const fg = require('fast-glob');
const chokidar = require('chokidar');

class GenerateSassIndexesPlugin {
  /**
   * @param {Object} options
   * @param {string[]} options.roots  Directories that contain SCSS “packages” (each with subfolders)
   * @param {RegExp}  [options.folderFilter]  Optional: only make indexes for folders matching this
   */
  constructor({ roots = [], folderFilter } = {}) {
    this.roots = roots;
    this.folderFilter = folderFilter;
    this._watcher = null;
    this._queued = false;
  }

  apply(compiler) {
    const runOnce = () => this.generateAll();

    // Run before first build (both normal & watch)
    compiler.hooks.beforeRun.tap('GenerateSassIndexesPlugin', runOnce);
    compiler.hooks.watchRun.tap('GenerateSassIndexesPlugin', runOnce);

    // In watch mode, keep a chokidar watcher to regen on add/unlink
    compiler.hooks.afterCompile.tap('GenerateSassIndexesPlugin', () => {
      if (this._watcher) return;
      if (!compiler.watchMode) return;

      const patterns = this.roots.map(r => path.join(r, '**', '_*.scss'));
      this._watcher = chokidar.watch(patterns, { ignoreInitial: true });

      const schedule = () => {
        if (this._queued) return;
        this._queued = true;
        setTimeout(() => {
          this._queued = false;
          this.generateAll();
        }, 50);
      };

      this._watcher.on('add', schedule).on('unlink', schedule).on('addDir', schedule).on('unlinkDir', schedule);
    });

    compiler.hooks.shutdown.tap('GenerateSassIndexesPlugin', () => {
      if (this._watcher) this._watcher.close();
    });
  }

  generateAll() {
    this.roots.forEach(root => this.generateForRoot(root));
  }

  generateForRoot(root) {
    // Find all subfolders under root that contain _*.scss files (except _index.scss)
    const folders = new Set();
    fg.sync('**/_*.scss', { cwd: root, dot: false }).forEach((rel) => {
      const dir = path.dirname(rel);
      if (this.folderFilter && !this.folderFilter.test(dir)) return;
      folders.add(path.join(root, dir));
    });

    // Also include empty subfolders to ensure an empty _index.scss exists
    fg.sync('**/', { cwd: root, onlyDirectories: true, dot: false }).forEach((rel) => {
      const dir = path.join(root, rel);
      if (this.folderFilter && !this.folderFilter.test(rel.replace(/\/$/, ''))) return;
      folders.add(dir);
    });

    folders.forEach((dir) => this.writeIndex(dir));
  }

  writeIndex(dir) {
    // Collect all _*.scss except _index.scss in this folder
    const files = fg.sync('_*.scss', { cwd: dir, dot: false })
      .filter(f => f !== '_index.scss');

		let lines = [
`/**	
 *	This file is auto-generated.
 *	Do not edit directly.
 */`,
		];

    lines = lines.concat(files
      .map(f => path.basename(f, '.scss').slice(1)) // strip leading underscore
      .sort()
      .map(base => `@forward '${base}';`));

    const outPath = path.join(dir, '_index.scss');
    const next = (lines.join('\n') + (lines.length ? '\n' : ''));

    // Write only if changed (keeps Webpack quiet)
    let prev = null;
    try { prev = fs.readFileSync(outPath, 'utf8'); } catch {}
    if (prev !== next) {
      fs.mkdirSync(dir, { recursive: true });
      fs.writeFileSync(outPath, next, 'utf8');
    }
  }
}

module.exports = GenerateSassIndexesPlugin;
