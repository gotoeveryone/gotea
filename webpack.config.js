/* eslint-disable @typescript-eslint/no-var-requires */
const path = require('path');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StylelintBarePlugin = require('stylelint-bare-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
/* eslint-enable @typescript-eslint/no-var-requires */

module.exports = () => {
  return [
    {
      entry: {
        'js/app': path.resolve(__dirname, 'resources', 'assets', 'ts', 'main.ts'),
      },
      output: {
        path: path.join(__dirname, 'webroot'),
      },
      performance: {
        hints: false,
      },
      resolve: {
        extensions: ['.ts', '.vue', '.js'],
        alias: {
          vue$: 'vue/dist/vue.common',
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
            enforce: 'pre',
            test: /\.(js|vue|ts)$/,
            exclude: /node_modules/,
            use: [
              {
                loader: 'eslint-loader',
                options: {
                  esModule: true,
                  cache: true,
                },
              },
            ],
          },
          {
            test: /\.ts$/,
            exclude: /node_modules|vue\/src/,
            use: [
              {
                loader: 'ts-loader',
                options: {
                  appendTsSuffixTo: [/\.vue$/],
                },
              },
            ],
          },
          {
            test: /\.vue$/,
            exclude: /node_modules/,
            use: [
              {
                loader: 'vue-loader',
                options: {
                  esModule: true,
                },
              },
            ],
          },
          {
            test: /\.(sa|sc|c)ss$/,
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
        'css/app': path.resolve(__dirname, 'resources', 'assets', 'sass', 'app.scss'),
        'css/view': path.resolve(__dirname, 'resources', 'assets', 'sass', 'view.scss'),
      },
      output: {
        path: path.join(__dirname, 'webroot'),
      },
      performance: {
        hints: false,
      },
      resolve: {
        extensions: ['.scss', 'css'],
      },
      stats: 'minimal',
      plugins: [
        new FixStyleOnlyEntriesPlugin(),
        new FriendlyErrorsWebpackPlugin(),
        new MiniCssExtractPlugin({
          filename: '[name].css',
        }),
        new StylelintBarePlugin({
          files: ['resources/assets/sass/**/*.scss', 'resources/assets/vue/**/*.vue'],
        }),
      ],
      module: {
        rules: [
          {
            test: /\.(sa|sc|c)ss$/,
            use: [
              MiniCssExtractPlugin.loader,
              'css-loader',
              'sass-loader',
            ],
          },
        ],
      },
    },
  ];
};
