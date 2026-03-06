# stylelint まわりの npm audit 対応メモ

最終更新: 2026-03-06

## 1. 現状の監査結果

- 実行コマンド: `npm audit --json`
- 結果: `0 vulnerabilities`
- 2026-03-06 時点では監査上の脆弱性は解消済み
- いずれも devDependencies 由来の問題だった

## 2. 実施済みの対応

1. `npm-run-all` を `concurrently` へ置換
2. `npm install` で lockfile を更新
3. `npm audit fix` を実行
4. `npm audit` で `0 vulnerabilities` を確認
5. `npm run lint` の成功を確認
6. `stylelint` 関連パッケージを 16 系中心へ更新
7. `npm run lint:styles` と `npm run build` の成功を確認

## 3. 解消した警告

- ajv (7.0.0-alpha.0 - 8.17.1)
  - 影響: moderate
  - 参照先: https://github.com/advisories/GHSA-2g4f-4pwh-qvx6
  - 解消前の依存経路: `stylelint@14.16.1 -> table@6.8.1 -> ajv@8.11.0`
  - 対応: `npm audit fix` により `ajv@8.18.0` へ更新

- brace-expansion (1.0.0 - 1.1.11, 2.0.0 - 2.0.1)
  - 影響: low
  - 参照先: https://github.com/advisories/GHSA-v6h2-p8h4-qcjw
  - 解消前の依存経路1: `npm-run-all@4.1.5 -> minimatch@3.1.5 -> brace-expansion@1.1.11`
  - 解消前の依存経路2: `@vue/test-utils@2.4.6 -> js-beautify -> minimatch@5.1.9 -> brace-expansion@2.0.1`
  - 対応1: `npm-run-all` を `concurrently` へ置換
  - 対応2: `npm audit fix` により `brace-expansion@1.1.12` / `2.0.2` へ更新

## 4. stylelint 更新で行った修正

1. `stylelint` を 14 系から 16 系へ更新
2. `stylelint-config-recommended-scss` を更新
3. `stylelint-scss` を更新
4. `vite-plugin-stylelint` を更新
5. 未使用だった `stylelint-config-standard` を削除
6. `declaration-property-value-no-unknown` は CSS 専用ルールのため、SCSS を扱うこのリポジトリでは無効化
7. `property-no-deprecated` への対応として `word-wrap` を `overflow-wrap` に変更

## 5. 現時点の整理

- `npm audit` 上の問題は解消済み
- `stylelint` 更新後も `npm run lint` / `npm run lint:styles` / `npm run build` は成功
- `@vue/test-utils` は安定版としては `2.4.6` が最新で、これ以上の更新余地はほぼない
- そのため、stylelint まわりの依存更新はいったん完了とみなせる

## 6. 残っている注意点

1. `npm run build` では Sass の `@import` 非推奨警告が出る
2. これは stylelint 更新の失敗ではないが、将来的には `@use` / `@forward` への移行を検討する
3. Biome はこのリポジトリの `.scss` と `.vue` 内 `lang="scss"` を `stylelint` の代わりとして全面置換できる段階ではない

## 7. 参考: 関連する現行バージョン

- stylelint: `^16.23.1`
- stylelint-config-recommended-scss: `^16.0.0`
- stylelint-config-recommended-vue: `^1.6.1`
- stylelint-scss: `^6.12.1`
- vite-plugin-stylelint: `^6.0.2`
- concurrently: `^9.2.1`
- @vue/test-utils: `^2.4.6`

## 8. stylelint 更新時に確認したポイント

- `stylelint`
- `stylelint-config-recommended-scss`
- `stylelint-config-recommended-vue`
- `stylelint-scss`
- `vite-plugin-stylelint`
