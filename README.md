# Gotea (ゴティー)

[![Build Status](https://travis-ci.org/gotoeveryone/gotea.svg?branch=master)](https://travis-ci.org/cicatrice/travis-test)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/gotoeveryone/gotea/blob/master/LICENSE)
[![GitHub version](https://badge.fury.io/gh/gotoeveryone%2Fgotea.svg)](https://badge.fury.io/gh/gotoeveryone%2Fgotea)

囲碁のプロ棋士・棋戦情報を管理します。  
[CakePHP](http://cakephp.org) 3.x を利用しています。

## Requirements

*   php7.1.3+
*   composer
*   nodejs
*   yarn

## Setup

```console
$ cd <project_root>
$ cp .env.example .env
$ composer install
$ yarn
```

## Run

```console
$ # frontend
$ yarn run dev
$
$ # backend
$ ./bin/cake server
```

### VSCode を利用する場合

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
                "/usr/share/nginx/html": "${workspaceFolder}"
            }
        }
    ]
}
```

### Docker を利用する場合

以下コマンドを実行してください。

```console
$ cd ./docker
$ LOCAL_IP=<your local ip> docker-compose up
$ # Macの場合
$ LOCAL_IP=$(ipconfig getifaddr en1) docker-compose up
```

### フロントコード

*   webpack
*   Vue.js
*   Sass

最終的な JS・CSS は`webpack`を利用して生成します。  
`npm run dev`を実行するとこれらの変更を監視し、更新時には自動で`webroot`ディレクトリ以下に出力します。

## その他

### ログ出力

環境変数`LOG_DIR`に以下を出力します（未設定もしくはブランクの場合はプロジェクトルートの`logs`ディレクトリ）。

*   gotea-access.log（アクセスログ）
*   gotea-error.log（エラーログ）
*   gotea-cli-debug.log（CLI デバッグログ）
*   gotea-cli-error.log（CLI エラーログ）
