module.exports = {
    entry: './resources/assets/ts/main',
    output: {
        filename: './webroot/js/app.min.js',
    },
    resolve: {
        extensions:['.webpack.js', '.ts', '.tsx', '.js']
    },
    module: {
        loaders: [
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
            }
        ]
    },
};
