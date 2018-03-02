const merge = require('webpack-merge');
const common = require('./webpack.common.js');
const webpack = require('webpack');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');


module.exports = merge(common, {
    devtool: 'source-map',

    output: {
        filename: 'js/[name].[chunkhash:8].bundle.js'
    },
    plugins: [
        new UglifyJSPlugin({
            sourceMap: true,
            uglifyOptions: {
                compress: true,
                warnings: true
            }
        }),
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('production')
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: ['vendors', 'manifest'],
            minChunks: Infinity
        })
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.min.js'
        }
    }
});
