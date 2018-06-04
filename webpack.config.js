const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');

module.exports = {
    entry: {
        'js/app.js': path.resolve(__dirname, 'resources/assets/ts/main.ts'),
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
        extensions: ['.ts', '.vue', '.js', '.scss', 'css'],
        alias: {
            'vue$': 'vue/dist/vue.common',
            '@': path.resolve('resources/assets/ts'),
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
                test: /\.ts$/,
                exclude: /node_modules|vue\/src/,
                loader: 'ts-loader',
                options: {
                    appendTsSuffixTo: [/\.vue$/],
                },
            },
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
                test: /\.css$/,
                loader: 'style-loader!css-loader',
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
