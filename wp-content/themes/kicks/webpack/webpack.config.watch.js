const portscanner = require('portscanner');
const fs = require('fs');
const glob = require('glob');
const path = require('path');

module.exports = async () => {
  const port = await portscanner.findAPortNotInUse(3000, 4000);

  const [name] = process.cwd().match(/([^/]+)$/g);
  const proxyUrl = `https://${name}.lndo.site`;
  const host = `dev.${name}.lndo.site`;
  const open = !process.argv.includes('no-open');

  const blocks = glob.sync('./editor/blocks/**/view-script.js');
  const main = ['./assets/js/main.js', './assets/scss/main.scss', ...blocks];

  // re-evaluate entry points when files are added / deleted.
  fs.watch('./assets', { recursive: true }, (eventType) => {
    if (eventType === 'rename') {
      main.forEach((entry) => fs.writeFileSync(entry, fs.readFileSync(entry)));
    }
  });

  const scssDir = path.resolve(__dirname, '../assets/scss/blocks');

  const getEntries = () => {
    const blockStyles = glob.sync(path.resolve('./assets/scss/blocks/**/*.scss'))
      .reduce((files, file) => {
        const match = file.match(/scss\/blocks\/_?(.*).scss/);
        return { ...files, [match[1]]: file };
      }, {});
    return {
      main: [
        ...main,
        ...Object.values(blockStyles),
      ],
    };
  };

  const config = {
    target: 'web',
    entry: getEntries,
    output: {
      filename: '[name].js',
      publicPath: '/',
    },
    devtool: 'eval-cheap-module-source-map',
    devServer: {
      watchFiles: {
        paths: [
          './**/*.php',
          './**/*.twig',
          './**/*.json',
        ],
        options: {
          usePolling: false,
        },
      },
      onBeforeSetupMiddleware(devServer) {
        if (!devServer) {
          throw new Error('webpack-dev-server is not defined');
        }

        fs.watch(scssDir, { recursive: true }, (eventType, filename) => {
          if (filename.endsWith('.scss')) {
            if (eventType === 'rename') {
              devServer.sendMessage(devServer.webSocketServer.clients, 'static-changed'); // Soft refresh
            }
          }
        });
      },
      client: {
        progress: true,
      },
      https: true,
      host,
      port,
      open,
      hot: true,
      liveReload: false,
      proxy: [
        {
          context: (url) => url.match(/\.(?!php)|wp-json.*|wp-admin\/admin-ajax.php/g),
          secure: false,
          target: proxyUrl,
          changeOrigin: true,
        },
        {
          context: (url) => url.match(/wp-admin/g),
          target: proxyUrl,
          secure: false,
          changeOrigin: true,
          selfHandleResponse: true,
          onProxyRes(proxyRes, req, res) {
            res.redirect(`${proxyUrl}${req.url}`);
          },
        },
        {
          // pages only - urls that don't contain a . (dot)
          context: (url) => !url.match(/wp-admin.*|\./g),
          target: proxyUrl,
          secure: false,
          changeOrigin: true,
          selfHandleResponse: true,
          headers: {
            'X-ProxiedBy-Webpack': true,
            'X-Webpack-Devserver': `${host}:${port}`,
          },
          onProxyReq(proxyReq, req, res) {
            proxyReq.setHeader('accept-encoding', 'identity');
          },
          onProxyRes(proxyRes, req, res) {
            const url = new URL(proxyUrl);
            const expr = `(?:https?)?\://${url.hostname}(?:\:${url.port})?/`;
            if (proxyRes.statusCode === 301 || proxyRes.statusCode === 302) {
              res.setHeader('Location', proxyRes.headers.location.replace(new RegExp(expr, 'g'), '/'));
              res.statusCode = 302;
              res.end();
            } else {
              let body = [];
              proxyRes.on('data', (chunk) => {
                body.push(chunk);
              });
              proxyRes.on('end', () => {
                body = Buffer.concat(body).toString();
                res.end(body.replace(new RegExp(expr, 'g'), '/'));
              });
            }
          },
        },
        // everything else - images / js files etc are passed through unaltered
      ],
    },
  };

  return config;
};
