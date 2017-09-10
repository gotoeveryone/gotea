# 棋戦情報管理システム

[CakePHP](http://cakephp.org) 3.x を利用した[Go to Everyone!](https://gotoeveryone.k2ss.info)の管理システムです。

## 前提
以下がインストールされていること。

- php7+
- composer
- nodejs

## セットアップ

1. `.env.example`を参考にプロジェクトルートに`.env`ファイルを生成します。
2. プロジェクトルートで`composer install`を実行します。
3. 同じくプロジェクトルートで`npm install`を実行します。

### Dockerを利用する場合

以下コマンドを実行してください。

```
$ cd ./docker
$ LOCAL_IP=<your local ip> docker-compose up
$ # Macの場合
$ LOCAL_IP=$(ipconfig getifaddr en1) docker-compose up
```

## 注意事項

### フロントコード

- webpack
- Vue.js
- Sass

最終的なJS・CSSは`webpack`を利用して生成します。  
`npm run dev`を実行するとこれらの変更を監視し、更新時には自動で`webroot`ディレクトリ以下に出力します。

### ログ出力

環境変数`LOG_DIR`に以下を出力します（未設定もしくはブランクの場合はプロジェクトルートの`logs`ディレクトリ）。

- igoapp-access.log（アクセスログ）
- igoapp-error.log（エラーログ）
- igoapp-cli-debug.log（CLIデバッグログ）
- igoapp-cli-error.log（CLIエラーログ）
