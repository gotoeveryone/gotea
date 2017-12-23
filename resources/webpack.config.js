const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = [
    {
        entry: {
            app: path.join(__dirname, 'assets/js/app'),
            common: path.join(__dirname, 'assets/js/common'),
        },
        output: {
            publicPath: 'js/',
            path: path.join(__dirname, '../webroot/js'),
            filename: '[name].js',
        },
        resolve: {
            extensions: ['.webpack.js', '.vue', '.js'],
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
                            js: 'buble-loader',
                        },
                    },
                },
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    loaders: [
                        'buble-loader',
                        'eslint-loader',
                    ],
                },
                {
                    test: /\.css$/,
                    loader: 'style-loader!css-loader',
                },
                {
                    test: /\.(eot|svg|ttf|woff|woff2)(\?\S*)?$/,
                    loader: 'file-loader',
                },
                {
                    test: /\.(png|jpe?g|gif|svg)(\?\S*)?$/,
                    loader: 'file-loader',
                    query: {
                        name: '[name].[ext]?[hash]',
                    },
                },
            ],
        },
    },
    {
        entry: {
            app: path.join(__dirname, 'assets/sass/app.scss'),
        },
        output: {
            path: path.join(__dirname, '../webroot/css'),
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
