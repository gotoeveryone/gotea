const webpack = require('webpack');

module.exports = {
    entry: {
        common: './resources/assets/js/common',
        ranking: './resources/assets/ts/ranking',
        ranks: './resources/assets/ts/ranks',
        titles: './resources/assets/ts/titles',
    },
    output: {
        filename: './webroot/js/[name].min.js',
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
};
