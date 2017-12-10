# Gotea (ゴティー)

[CakePHP](http://cakephp.org) 3.x を利用した[Go to Everyone!](https://gotoeveryone.k2ss.info)の情報管理ツールです。

## 前提

以下がインストールされていること。

- php7.1.3+
- composer
- nodejs

## セットアップ

1. `.env.example`を参考にプロジェクトルートに`.env`ファイルを生成します。
2. プロジェクトルートで`composer install`を実行します。
3. 同じくプロジェクトルートで`npm install`を実行します。

### VSCodeを利用する場合

プロジェクトルートに`.vscode/settings.json`を配置し、以下を記載します。

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9000
        },
        {
            "name": "Listen for Docker XDebug",
            "type": "php",
            "request": "launch",
            "port": 9001,
            "log": true,
            "pathMappings": {
                "/usr/share/nginx/html": "${workspaceRoot}"
            }
        }
    ]
}
```

### Dockerを利用する場合

以下コマンドを実行してください。

```
$ cd ./docker
$ LOCAL_IP=<your local ip> docker-compose up
$ # Macの場合
$ LOCAL_IP=$(ipconfig getifaddr en1) docker-compose up
```

### フロントコード

- webpack
- Vue.js
- Sass

最終的なJS・CSSは`webpack`を利用して生成します。  
`npm run dev`を実行するとこれらの変更を監視し、更新時には自動で`webroot`ディレクトリ以下に出力します。

## その他

### ログ出力

環境変数`LOG_DIR`に以下を出力します（未設定もしくはブランクの場合はプロジェクトルートの`logs`ディレクトリ）。

- igoapp-access.log（アクセスログ）
- igoapp-error.log（エラーログ）
- igoapp-cli-debug.log（CLIデバッグログ）
- igoapp-cli-error.log（CLIエラーログ）
