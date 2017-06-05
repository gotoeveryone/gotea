const path = require('path');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = [
    {
        entry: {
            common: './resources/assets/js/common',
            ranking: './resources/assets/ts/ranking',
            ranks: './resources/assets/ts/ranks',
            titles: './resources/assets/ts/titles',
        },
        output: {
            path: path.join(__dirname, 'webroot/js'),
            filename: '[name].min.js',
        },
        resolve: {
            extensions:['.webpack.js', '.tag', '.ts', '.tsx', '.js'],
            alias: {
                'vue$': 'vue/dist/vue.common',
            }
        },
        module: {
            loaders: [
                {
                    test: /\.tsx?$/,
                    loader: 'ts-loader',
                },
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {
                            js: 'buble-loader'
                        }
                    },
                },
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loader: 'buble-loader',
                },
            ],
        },
        plugins: [
            new webpack.DefinePlugin({
                'PRODUCTION': (process.env.NODE_ENV === 'production'),
            }),
        ],
    },
    {
        entry: {
            app: './resources/assets/sass/app.scss',
        },
        output: {
            path: path.join(__dirname, 'webroot/css'),
            filename: '[name].css',
        },
        resolve: {
            extensions: ['.scss', 'css'],
        },
        plugins: [
            new ExtractTextPlugin({
                filename: '[name].css',
                disable: false,
                allChunks: true,
            }),
        ],
        module: {
            loaders: [
                {
                    test: /\.scss$/,
                    exclude: /node_modules/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: 'css-loader!sass-loader',
                    }),
                },
            ],
        },
    },
];
