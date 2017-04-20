module.exports = {
    entry: {
        ranking: './resources/assets/ts/ranking',
        categorize: './resources/assets/ts/categorize',
    },
    output: {
        filename: './webroot/js/[name].min.js',
    },
    resolve: {
        extensions:['.webpack.js', '.ts', '.tsx', '.js']
    },
    module: {
        loaders: [
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
            },
        ],
    },
    plugins: [
    ]
};
