const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = [
    {
        entry: {
            'js/app.js': path.resolve(__dirname, 'resources/assets/ts/main.ts'),
        },
        output: {
            path: path.join(__dirname, 'webroot'),
            filename: '[name]',
        },
        performance: {
            hints: false,
        },
        resolve: {
            extensions: ['.ts', '.vue', '.js'],
            alias: {
                'vue$': 'vue/dist/vue.common',
                '@': path.resolve('resources/assets/ts'),
            },
        },
        stats: 'minimal',
        plugins: [
            new VueLoaderPlugin(),
            new FriendlyErrorsWebpackPlugin(),
        ],
        module: {
            rules: [
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
                },
                {
                    test: /\.css$/,
                    loader: 'vue-style-loader!css-loader',
                },
                {
                    test: /\.scss$/,
                    exclude: /node_modules/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                        'sass-loader',
                        {
                            loader: 'sass-resources-loader',
                            options: {
                                resources: [
                                    path.resolve(__dirname, 'resources/assets/sass/module/_variables.scss'),
                                    path.resolve(__dirname, './resources/assets/sass/module/_mixin.scss'),
                                ],
                            },
                        },
                    ],
                },
            ],
        },
    },
    {
        entry: {
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
        performance: {
            hints: false,
        },
        resolve: {
            extensions: ['.scss', 'css'],
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
            rules: [
                {
                    test: /\.css$/,
                    loader: 'style-loader!css-loader',
                },
                {
                    test: /\.scss$/,
                    exclude: /node_modules/,
                    use: ExtractTextPlugin.extract({
                        fallback: 'style-loader',
                        use: [
                            'css-loader',
                            'sass-loader',
                        ],
                    }),
                },
            ],
        },
    },
];
