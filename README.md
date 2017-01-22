# 棋戦情報管理システム

[![Build Status](https://api.travis-ci.org/cakephp/app.png)](https://travis-ci.org/cakephp/app)
[![License](https://poser.pugx.org/cakephp/app/license.svg)](https://packagist.org/packages/cakephp/app)

[CakePHP](http://cakephp.org) 3.x を利用した囲碁棋戦情報管理システムです。

## 前提
以下がインストールされていること。
- php7+
- composer
- nodejs

## セットアップ

1. プロジェクトのルートディレクトリで`composer install`を実行します。   
2. 同じくルートディレクトリで`npm install`を実行します。

## タスクランナー

- gulp-sassを利用しています。
- `gulp watch`を実行すると、SCSSの変更を監視します（JSファイルは未対応です…）。
