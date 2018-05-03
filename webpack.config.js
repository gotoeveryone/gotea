const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');

module.exports = {
    entry: {
        'js/app.js': path.resolve(__dirname, 'resources/assets/js/app.js'),
        'js/common.js': path.resolve(
            __dirname,
            'resources/assets/js/common.js'
        ),
        'css/app.css': path.resolve(
            __dirname,
            'resources/assets/sass/app.scss'
        ),
        'css/view.css': path.resolve(
            __dirname,
            'resources/assets/sass/view.scss'
        ),
    },
    output: {
        path: path.join(__dirname, 'webroot'),
        filename: '[name]',
    },
    resolve: {
        extensions: ['.webpack.js', '.vue', '.js', '.scss', 'css'],
        alias: {
            vue$: 'vue/dist/vue.common',
        },
    },
    stats: 'minimal',
    plugins: [
        new ExtractTextPlugin({
            filename: '[name]',
            disable: false,
            allChunks: true,
        }),
        new FriendlyErrorsWebpackPlugin(),
    ],
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
                loaders: ['buble-loader', 'eslint-loader'],
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
};
