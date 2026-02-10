#/bin/bash

# データベースを作成
mysql -u root -e "create database \`${MARIADB_TEST_DATABASE}\` default character set utf8mb4";
