# Gotea (ゴティー)

![Build Status](https://github.com/gotoeveryone/gotea/workflows/Build/badge.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.2-8892bf)
[![CakePHP Version](https://img.shields.io/badge/cakephp-%5E5-red?logo=cakephp)](https://book.cakephp.org/5/ja/index.html)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/gotoeveryone/gotea/blob/master/LICENSE)
[![GitHub version](https://badge.fury.io/gh/gotoeveryone%2Fgotea.svg)](https://badge.fury.io/gh/gotoeveryone%2Fgotea)

囲碁のプロ棋士・棋戦情報を管理します。  
[CakePHP](http://cakephp.org) を利用しています。

## Requirements

- Docker

## Setup

```console
$ cp .env.example .env
```

## Run

```console
$ docker compose up
```

- ホストから接続するためのポートは以下
  - http: 8765
  - データベース (MySQL): 3306

## Migration

```console
$ docker compose exec backend ./bin/cake migrations migrate
```

## Format check

```console
$ # backend
$ docker compose exec backend composer cs-check

$ # frontend
$ docker compose exec frontend npm run lint
```

## Test

```console
$ # backend
$ docker compose exec backend composer test

$ # frontend
$ docker compose exec frontend npm test
```

## Repl

```console
$ docker compose exec backend ./bin/cake console
```
