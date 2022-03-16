/* eslint-disable @typescript-eslint/no-var-requires */
const path = require('path');
const EslintWebpackPlugin = require('eslint-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StylelintBarePlugin = require('stylelint-bare-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
/* eslint-enable @typescript-eslint/no-var-requires */

const webrootDir = path.join(__dirname, 'webroot');
const stylesDir = path.join(__dirname, 'resources', 'assets', 'styles');
const scriptsDir = path.join(__dirname, 'resources', 'assets', 'scripts');

module.exports = () => {
  return Promise.all([
    {
      entry: {
        'js/app': path.join(scriptsDir, 'main.ts'),
      },
      output: {
        path: webrootDir,
      },
      performance: {
        hints: false,
      },
      resolve: {
        extensions: ['.ts', '.vue', '.js'],
        alias: {
          vue$: 'vue/dist/vue.esm-bundler',
          vuex$: 'vuex/dist/vuex.mjs',
          '@': scriptsDir,
        },
      },
      stats: 'minimal',
      plugins: [
        new VueLoaderPlugin(),
        new EslintWebpackPlugin({
          extensions: ['.ts', '.js', '.vue'],
        }),
      ],
      module: {
        rules: [
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
              {
                loader: 'css-loader',
                options: {
                  esModule: false,
                },
              },
              {
                loader: 'sass-loader',
                options: {
                  implementation: require('sass'),
                  sassOptions: {
                    fiber: require('fibers'),
                  },
                },
              },
              {
                loader: 'sass-resources-loader',
                options: {
                  resources: [
                    path.join(stylesDir, 'base', '_variables.scss'),
                    path.join(stylesDir, 'base', '_mixin.scss'),
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
        'css/app': path.join(stylesDir, 'app.scss'),
        'css/view': path.join(stylesDir, 'view.scss'),
      },
      output: {
        path: webrootDir,
      },
      performance: {
        hints: false,
      },
      resolve: {
        extensions: ['.scss', 'css'],
      },
      stats: 'minimal',
      plugins: [
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({
          filename: '[name].css',
        }),
        new StylelintBarePlugin({
          files: ['resources/assets/styles/**/*.scss', 'resources/assets/scripts/**/*.vue'],
        }),
      ],
      module: {
        rules: [
          {
            test: /\.(sa|sc|c)ss$/,
            use: [
              MiniCssExtractPlugin.loader,
              {
                loader: 'css-loader',
                options: {
                  esModule: false,
                },
              },
              {
                loader: 'sass-loader',
                options: {
                  implementation: require('sass'),
                  sassOptions: {
                    fiber: require('fibers'),
                  },
                },
              },
            ],
          },
        ],
      },
    },
  ]);
};
