@echo off

set CURRENT=%~dp0
cd /d %CURRENT%

rem composer���s�t�@�C���̃_�E�����[�h
IF NOT EXIST "%CURRENT%\composer.phar" (
    php -r "readfile('https://getcomposer.org/installer');" | php
)

rem �K�v�ȃ��C�u�������擾
php composer.phar install

echo ���C�u�����̎擾���I�����܂����B

pause
