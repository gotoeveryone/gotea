const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = [
    {
        entry: {
            app: './resources/assets/js/app',
            common: './resources/assets/js/common',
        },
        output: {
            path: path.join(__dirname, 'webroot/js'),
            filename: '[name].js',
        },
        resolve: {
            extensions:['.webpack.js', '.vue', '.js'],
            alias: {
                'vue$': 'vue/dist/vue.common',
            },
        },
        module: {
            loaders: [
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
