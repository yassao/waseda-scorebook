# Scorebook by CuViu リリース管理マニュアル

## 概要

`scripts/release_scorebook.sh` は以下を行います。

1. `docs/VERSION` のバージョン番号を更新
2. `docs/CHANGELOG.md` に1行要約を追記
3. `docs/DEBUG_LOG.md` に作業ログを追記
4. `index.html` の `APP_VERSION` を更新
5. git commit / tag / push

公開サーバーへの反映は別コマンドです。

```zsh
zsh scripts/deploy-cuviu.sh
```

## 基本的な使い方

### パッチバージョン

小さな修正・バグ修正。

```zsh
zsh scripts/release_patch_scorebook.sh "変更内容の1行要約"
```

例:

```zsh
zsh scripts/release_patch_scorebook.sh "X取り込みの一塁セーフ判定を改善"
```

### マイナーバージョン

新機能追加。

```zsh
zsh scripts/release_minor_scorebook.sh "保存JSONの初期実装"
```

### メジャーバージョン

大きな仕様変更や互換性に影響する変更。

```zsh
zsh scripts/release_major_scorebook.sh "データモデルを刷新"
```

## バージョン番号の考え方

```text
v メジャー . マイナー . パッチ
    大きな変更  新機能    小修正
```

一般公開前は `0.x.y` として扱います。

## 保存JSONとの関係

今後の保存データには以下を入れる方針です。

```json
{
  "appVersion": "0.1.0",
  "schemaVersion": 1
}
```

`appVersion` はアプリ側の機能バージョン、`schemaVersion` は保存JSONの互換性管理に使います。
