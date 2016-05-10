#!/bin/sh

CURRENT=$(cd $(dirname $0); pwd)
cd "${CURRENT}"

# composer実行ファイルのダウンロード
if [ ! -e "${CURRENT}/composer.phar" ]; then
    php -r "readfile('https://getcomposer.org/installer');" | php
fi

# 必要なライブラリを取得
php composer.phar install

echo "ライブラリの取得が終了しました。"

exit
