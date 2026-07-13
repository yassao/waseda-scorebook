# フィードバック台帳

`FB` ボタンから送られたフィードバックは、メール送信に加えてサーバー側のJSONL台帳に保存します。

## 保存先

公開サーバー:

```text
/home/cuviu001/cuviu.jp/scorebook-feedback/
```

主なファイル:

- `feedback.jsonl`: 全件の台帳
- `by-category/<category>.jsonl`: 内容カテゴリ別の台帳
- `summary.json`: 件数サマリ
- `attachments/<feedback-id>.json`: 自動添付された試合データ
- `attachments/<feedback-id>.<ext>`: 任意スクリーンショット
- `admin-token.txt`: 外部閲覧用の管理トークン

## ブラウザで見る

管理ページ:

```text
https://cuviu.jp/apps/scorebook/feedback_admin.php
```

試合データJSONとスクリーンショットを扱うため、管理トークンで保護します。トークンは公開ディレクトリ外に保存します。

```zsh
ssh -i ~/.ssh/xserver_cuviu -p 10022 cuviu001@sv16692.xserver.jp \
  "cat /home/cuviu001/cuviu.jp/scorebook-feedback/admin-token.txt"
```

最初にトークン付きURLで開くと、以後はCookieで閲覧できます。

```text
https://cuviu.jp/apps/scorebook/feedback_admin.php?token=ここにトークン
```

Codexへ依頼するときは、管理ページに表示される `FB <feedback-id> を確認して対応してください。` の行をそのまま貼り付ける想定です。

## カテゴリ

コメント内容から以下のカテゴリを自動付与します。複数該当する場合は `categories` にすべて保存し、最初のものを `primaryCategory` にします。

- `bug`
- `runner`
- `input`
- `scorebook`
- `roster`
- `import`
- `share`
- `pdf`
- `save`
- `ads`
- `ui`
- `other`

## 確認例

```zsh
ssh -i ~/.ssh/xserver_cuviu -p 10022 cuviu001@sv16692.xserver.jp \
  "tail -n 20 /home/cuviu001/cuviu.jp/scorebook-feedback/feedback.jsonl"
```

カテゴリ別:

```zsh
ssh -i ~/.ssh/xserver_cuviu -p 10022 cuviu001@sv16692.xserver.jp \
  "ls -la /home/cuviu001/cuviu.jp/scorebook-feedback/by-category"
```
