# Gotea (ゴティー)

[![CircleCI](https://circleci.com/gh/gotoeveryone/gotea.svg?style=svg)](https://circleci.com/gh/gotoeveryone/gotea)
![PHP from Packagist](https://img.shields.io/packagist/php-v/symfony/symfony.svg)
[![CakePHP Version](https://img.shields.io/badge/cakephp-3.7-0366d6.svg)](https://book.cakephp.org/3.0/ja/index.html)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/gotoeveryone/gotea/blob/master/LICENSE)
[![GitHub version](https://badge.fury.io/gh/gotoeveryone%2Fgotea.svg)](https://badge.fury.io/gh/gotoeveryone%2Fgotea)

囲碁のプロ棋士・棋戦情報を管理します。  
[CakePHP](http://cakephp.org) 3.7.x を利用しています。

## Requirements

- php7.1.3+
- composer
- nodejs
- yarn

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
$ LOCAL_IP=<your local ip> docker-compose up -d
```

### フロントコード

- webpack
- Vue.js
- Sass

最終的な JS・CSS は`webpack`を利用して生成します。  
`yarn run dev`を実行するとこれらの変更を監視し、更新時には自動で`webroot`ディレクトリ以下に出力します。

## その他

### ログ出力

環境変数`LOG_DIR`に以下を出力します（未設定もしくはブランクの場合はプロジェクトルートの`logs`ディレクトリ）。

- gotea-access.log（アクセスログ）
- gotea-error.log（エラーログ）
- gotea-cli-debug.log（CLI デバッグログ）
- gotea-cli-error.log（CLI エラーログ）
