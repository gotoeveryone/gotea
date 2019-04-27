#!/bin/bash

set -eu

# Bash の設定を読み出す
source ~/.bash_profile

HOME_DIR="$1"
SLUG="$2"
PROJECT="gotea"

# ディレクトリ決定
WORK_DIR="${HOME_DIR}/release/link/${PROJECT}"
DEPLOY_SEQ=$(date +'%Y%m%d-%H%M%S')
WWW_DIR="${WORK_DIR}/${DEPLOY_SEQ}"
mkdir -p "${WWW_DIR}"

# tar の解凍
cd "${HOME_DIR}/tmp"
TAR_NAME=$(ls -t ${SLUG}-*.tar.gz | head -n1)
tar xzf "${TAR_NAME}" -C ${WWW_DIR}

# ログディレクトリの設定
ln -s "${BASE_DIR}/release/log/${PROJECT}" "${WWW_DIR}/logs"

# .env の設定
ln -s "${BASE_DIR}/release/environment/${PROJECT}/.env" "${WWW_DIR}/.env"

# Composer setup
cd "${WWW_DIR}"
composer install --no-dev

# マイグレーション実行
./bin/cake migrations migrate

# キャッシュ削除
./bin/cake cache clear_all

# リンク張り替え
LINK_PATH=${WWW_DIR}/webroot
TARGET_PATH="${HOME_DIR}/k2ss.info/public_html/${PROJECT}"
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
rm "${HOME_DIR}/tmp/${TAR_NAME}"
