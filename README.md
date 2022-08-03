# Gotea (ゴティー)

![Build Status](https://github.com/gotoeveryone/gotea/workflows/Build/badge.svg)
![PHP from Packagist](https://img.shields.io/packagist/php-v/symfony/symfony.svg)
[![CakePHP Version](https://img.shields.io/badge/cakephp-4.0-0366d6.svg)](https://book.cakephp.org/4.0/ja/index.html)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://github.com/gotoeveryone/gotea/blob/master/LICENSE)
[![GitHub version](https://badge.fury.io/gh/gotoeveryone%2Fgotea.svg)](https://badge.fury.io/gh/gotoeveryone%2Fgotea)

囲碁のプロ棋士・棋戦情報を管理します。  
[CakePHP](http://cakephp.org) 4.x を利用しています。

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
  - データベース (MySQL): 33306

## Migration

```console
$ docker compose exec server ./bin/cake migrations migrate
```

## Test

```console
$ docker compose exec server ./vendor/bin/phpunit
```

## Repl

```console
$ docker compose exec server ./bin/cake console
```
