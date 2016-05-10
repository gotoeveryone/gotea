@echo off

set CURRENT=%~dp0
cd /d %CURRENT%

rem composer実行ファイルのダウンロード
IF NOT EXIST "%CURRENT%\composer.phar" (
    php -r "readfile('https://getcomposer.org/installer');" | php
)

rem 必要なライブラリを取得
php composer.phar install

echo ライブラリの取得が終了しました。

pause
