# Codexへの申し送り（2026-07-05: プロジェクト移動）

## 何が変わったか
このプロジェクトは PC全体のディレクトリ整理により、以下の通り**場所が変わりました**。

- 旧パス: `~/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac`
- 新パス: **`~/dev/waseda-scorebook`**

移動先の`~/dev/`は、CuViu・TTscore2・RS-Technology等すべての開発プロジェクトを集約する新しい標準置き場です。「Documents配下は書類専用、コードは~/dev」という運用に統一しました。

## Codex側で必要な対応
`~/.codex/config.toml` はプロジェクトの信頼設定(`trust_level`)とデスクトップの「開く」設定(`desktop.open-in-target-preferences.perPath`)を**絶対パスで**保持しています。旧パス`~/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac`のエントリはそのまま残っていますが、実体はもう存在しません。

- 新パス`~/dev/waseda-scorebook`でこのプロジェクトを開くと、Codexは初回、**未信頼(untrusted)のディレクトリとして再確認**を求めてくる可能性があります。その際は通常通り信頼を承認してください。
- 旧パスの`config.toml`エントリ（`[projects."/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac"]`と`desktop.open-in-target-preferences.perPath`内の対応行）は、実体不在のまま残る想定です。実害はありませんが、気になる場合は次回`~/.codex/config.toml`を開いたときに手動で削除して構いません。

## 変わらないもの
- git remote: `https://github.com/yassao/waseda-scorebook.git`（変更なし、`git remote -v`で確認済み）
- コミット履歴・ブランチ(`main`)は移動の影響を受けていません。
- 副次的に、`.git/refs/heads/`に紛れ込んでいた壊れた参照ファイル（`main 2`、Time Machine等の同期由来と推定）も今回あわせて削除済みです。`git branch -a`の警告は解消しています。
- 過去のCodexセッション記録（`~/.codex/sessions`内、旧パスを参照する4件）はそのまま履歴として残ります。これらは読み取り専用の記録なので書き換えの必要はありません。

## 次にこのプロジェクトを開くときは
```bash
cd ~/dev/waseda-scorebook
```
から始めてください。
