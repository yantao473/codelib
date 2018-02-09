const merge = require('webpack-merge');
const common = require('./webpack.common.js');
const webpack = require('webpack');

module.exports = merge(common, {
    entry:{
        vendors: ['vue', 'vue-router']
    },
    devtool: 'inline-source-map',
    output: {
        filename: 'js/[name].[hash].bundle.js'
    },
    devServer: {
        contentBase: './public',
        hot: true
    },
    plugins:[
        new webpack.HashedModuleIdsPlugin(),
        new webpack.NamedModulesPlugin(),
        new webpack.HotModuleReplacementPlugin(),
        new webpack.optimize.CommonsChunkPlugin({
            name: 'vendors'
        }),
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('develop')
        })
    ],

    resolve: {
        alias: {
            vue: 'vue/dist/vue.js'
        }
    }

});
