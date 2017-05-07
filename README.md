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

## 注意事項

### タスクランナー

cakephp3-elixirを利用しています。  
`npm run dev`を実行すると、SCSSの変更を監視します（JSファイルは現時点で未対応です）。  

### ログ出力

環境変数`LOG_DIR`に以下を出力します（未設定もしくはブランクの場合はプロジェクトルートの`logs`ディレクトリ）。

- igoapp-access.log（アクセスログ）
- igoapp-error.log（エラーログ）

### DB接続

MySQLにて`igo`スキーマを利用します。  
ユーザ・パスワードは環境変数に以下キーで設定してください。  
※Apache・nginxなどWebサーバーを利用する場合、そちらにも設定が必要です。  

- DB_IGO_USER
- DB_IGO_PASSWORD
