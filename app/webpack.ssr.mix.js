const mix = require('laravel-mix');
const webpackNodeExternals = require('webpack-node-externals');
const path = require('path');

mix.js('resources/js/ssr.js', 'public/js')
    .vue({
        version: 3,
        useVueStyleLoader: true,
        options: { optimizeSSR: true },
    })
    .alias({
        '@': 'resources/js',
        ziggy: 'vendor/tightenco/ziggy/dist/index',
    })
    .webpackConfig({
        target: 'node',
        externals: [webpackNodeExternals()],
        resolve: {
            symlinks: false,
            alias: {
                vue: path.resolve("./node_modules/vue"),
            },
        }
    })
    .options({
        legacyNodePolyfills: false
    })
