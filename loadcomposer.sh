#!/bin/sh

CURRENT=$(cd $(dirname $0); pwd)
cd "${CURRENT}"

# composer���s�t�@�C���̃_�E�����[�h
if [ -e "${CURRENT}/composer.phar" ]; then
    php -r "readfile('https://getcomposer.org/installer');" | php
fi

# �K�v�ȃ��C�u�������擾
php composer.phar install

echo "���C�u�����̎擾���I�����܂����B"

exit
