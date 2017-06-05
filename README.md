# 棋戦情報管理システム

[CakePHP](http://cakephp.org) 3.x を利用した囲碁棋戦情報管理システムです。

## 前提
以下がインストールされていること。

- php7+
- composer
- nodejs

## セットアップ

1. プロジェクトのルートディレクトリで`composer install`を実行します。   
2. 同じくルートディレクトリで`npm install`を実行します。

### Dockerを利用する場合

1. `docker`ディレクトリにある`docker-compose.yml`の`<local_ip>`を自端末のIPに変更します。
2. 上記ディレクトリで`docker-compose up`を実行します。

## 注意事項

### フロントコード

- TypeScript
- Angular4
- webpack

最終的なJS・CSSは`webpack`を利用して生成します。  
`npm run dev`を実行するとこれらの変更を監視し、更新時には自動で`webroot`ディレクトリ以下に出力します。

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
