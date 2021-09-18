#!/bin/bash

set -eu

TAR_NAME="$1"
PROJECT="$2"

# ローカルに配置した PHP のエイリアスに設定されたバージョンを利用する
PATH="${HOME}/.php:$PATH"

# ディレクトリ決定
WORK_DIR="${HOME}/release/link/${PROJECT}"
DEPLOY_SEQ=$(date +'%Y%m%d-%H%M%S')
WWW_DIR="${WORK_DIR}/${DEPLOY_SEQ}"
mkdir -p "${WWW_DIR}"

# tar の解凍
cd "${HOME}"
tar xzf "${TAR_NAME}" -C ${WWW_DIR}

# ログディレクトリの設定
ln -s "${HOME}/release/log/${PROJECT}" "${WWW_DIR}/logs"

# 一時ディレクトリの設定
ln -s "${HOME}/release/tmp/${PROJECT}" "${WWW_DIR}/tmp"

# .env の設定
ln -s "${HOME}/release/environment/${PROJECT}/.env" "${WWW_DIR}/.env"

# Composer setup
cd "${WWW_DIR}"
composer install --no-dev

# マイグレーション実行
./bin/cake migrations migrate

# キャッシュ削除
./bin/cake cache clear_all

# リンク張り替え
LINK_PATH=${WWW_DIR}/webroot
TARGET_PATH="${HOME}/k2ss.info/public_html/${PROJECT}"
ln -snf ${LINK_PATH} ${TARGET_PATH}
echo "リンクを生成しました。${LINK_PATH} -> ${TARGET_PATH}"

# 古いディレクトリを削除
# この世代数を保持する
DELETE_COUNT=3

# インストールしたディレクトリを除く、指定世代以上古いものを削除
cd "${WORK_DIR}"
ls -t | \
  grep -wv ${DEPLOY_SEQ} | \
  tail -n +${DELETE_COUNT} | \
  xargs -I{} sh -c 'rm -rf {} && echo "{}を削除しました。"'

# 利用した tar の削除
rm "${HOME}/${TAR_NAME}"
