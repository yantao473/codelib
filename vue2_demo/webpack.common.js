const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');


module.exports = {
    entry: {
        main: './src/main.js'
    },

    output: {
        path: path.resolve(__dirname, 'public')
    },

    plugins: [
        new ExtractTextPlugin('styles.css'),
        new CleanWebpackPlugin(['public']),
        new HtmlWebpackPlugin({
            inject: true,
            filename : 'index.html',
            template:  __dirname + '/src/index.html'
        })
    ],

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader'
                })
            },
            {
                test: /\.(png|svg|jpg|gif)$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        limit: 10000,
                        name: 'images/[name]-[hash:8].[ext]'
                    }
                }]
            },
            {
                test: /\.(eot|woff|woff2|ttf)(\?\S*)?$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        limit: 5000,
                        name: 'font/[name]-[hash:8].[ext]'
                    }
                }]

            },
            {
                test: /\.vue$/,
                use: [
                    'vue-loader'
                ]
            }
        ]
    }
};
