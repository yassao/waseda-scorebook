# CuViu デプロイ手順

`index.html` / `intro.html` / `support-thanks.html` と、存在する場合はフィードバック受け口の `feedback.php` / 台帳ビューアの `feedback_admin.php` を Xserver の `cuviu.jp/apps/scorebook/` に反映します。
あわせて、フィードバック台帳用の非公開ディレクトリ `/home/cuviu001/cuviu.jp/scorebook-feedback/` を作成します。

```zsh
zsh scripts/deploy-cuviu.sh
```

前提:

- SSH秘密鍵を `~/.ssh/xserver_cuviu` に置いていること
- Xserver の SSH 接続先が `cuviu001@sv16692.xserver.jp:10022` であること

秘密鍵や接続先を変えたい場合は、環境変数で上書きできます。

```zsh
CUVIU_SSH_KEY=~/.ssh/別の鍵 zsh scripts/deploy-cuviu.sh
```
