#!/bin/bash

set -euo pipefail
IFS=$'\n\t'

if [ "$#" -ne 3 ]; then
  echo "Usage: $0 <tar_name> <project> <public_dir>" >&2
  exit 1
fi

TAR_NAME="$1"
PROJECT="$2"
PUBLIC_DIR="$3"

# ローカルに配置した PHP のエイリアスに設定されたバージョンを利用する
PATH="${HOME}/.php:$PATH"

# ディレクトリ決定
WORK_DIR="${HOME}/release/link/${PROJECT}"
DEPLOY_SEQ=$(date +'%Y%m%d-%H%M%S')
WWW_DIR="${WORK_DIR}/${DEPLOY_SEQ}"
CURRENT_PATH="${WORK_DIR}/current"
TARGET_PATH="${PUBLIC_DIR%/}/${PROJECT}"
mkdir -p "${WWW_DIR}"

# tar の解凍
tar xzf "${HOME}/${TAR_NAME}" -C "${WWW_DIR}"

# ログディレクトリの設定
ln -s "${HOME}/release/log/${PROJECT}" "${WWW_DIR}/logs"

# 一時ディレクトリの設定
ln -s "${HOME}/release/tmp/${PROJECT}" "${WWW_DIR}/tmp"

# .env の設定
ln -s "${HOME}/release/environment/${PROJECT}/.env" "${WWW_DIR}/.env"

# Composer setup
cd "${WWW_DIR}"
composer install \
  --no-dev \
  --prefer-dist \
  --no-interaction \
  --no-progress \
  --optimize-autoloader \
  --classmap-authoritative

# マイグレーション実行
./bin/cake migrations migrate

# キャッシュ削除
./bin/cake cache clear_all

# リンク張り替え（ロールバック可能）
LINK_PATH="${WWW_DIR}/webroot"
PREVIOUS_PATH=""
SWITCHED=0

# 公開パスは事前に CURRENT_PATH へのシンボリックリンクであることを要求
if [ ! -L "${TARGET_PATH}" ]; then
  echo "TARGET_PATH がシンボリックリンクではありません: ${TARGET_PATH}" >&2
  exit 1
fi

TARGET_DEST=$(readlink -f "${TARGET_PATH}")
EXPECTED_DEST=$(readlink -f "${CURRENT_PATH}" || true)
if [ -z "${EXPECTED_DEST}" ] || [ "${TARGET_DEST}" != "${EXPECTED_DEST}" ]; then
  echo "TARGET_PATH のリンク先が想定外です: ${TARGET_PATH} -> ${TARGET_DEST}" >&2
  echo "想定: ${TARGET_PATH} -> ${CURRENT_PATH} (${EXPECTED_DEST})" >&2
  exit 1
fi

if [ -L "${CURRENT_PATH}" ]; then
  PREVIOUS_PATH=$(readlink "${CURRENT_PATH}" || true)
fi

rollback() {
  if [ "${SWITCHED}" -eq 1 ] && [ -n "${PREVIOUS_PATH}" ]; then
    ln -sfn "${PREVIOUS_PATH}" "${CURRENT_PATH}"
    echo "デプロイ失敗のためリンクをロールバックしました。${CURRENT_PATH} -> ${PREVIOUS_PATH}"
  fi
}
trap rollback ERR

ln -sfn "${LINK_PATH}" "${CURRENT_PATH}"
SWITCHED=1
echo "リンクを生成しました。${CURRENT_PATH} -> ${LINK_PATH}"
trap - ERR

# 古いディレクトリを削除
# この世代数を保持する
DELETE_COUNT=3

# 指定世代数を超えた古いリリースを削除
mapfile -t RELEASE_DIRS < <(
  find "${WORK_DIR}" \
    -mindepth 1 \
    -maxdepth 1 \
    -type d \
    -regextype posix-extended \
    -regex ".*/[0-9]{8}-[0-9]{6}" \
    -printf '%f\n' | sort -r
)

if [ "${#RELEASE_DIRS[@]}" -gt "${DELETE_COUNT}" ]; then
  for OLD_DIR in "${RELEASE_DIRS[@]:DELETE_COUNT}"; do
    rm -rf -- "${WORK_DIR}/${OLD_DIR}"
    echo "${OLD_DIR}を削除しました。"
  done
fi

# 利用した tar の削除
rm "${HOME}/${TAR_NAME}"
