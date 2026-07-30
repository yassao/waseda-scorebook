# リリース運用

## 起点

- Solで進める開発版は `v0.4.1` から開始する。
- 公開版は `index.html` の `APP_VERSION` と `docs/VERSION` を必ず一致させる。

## リリースごとに残すもの

1. `docs/CHANGELOG.md`
   - バージョン
   - 公開日（`YYYY-MM-DD`形式）
   - 変更内容の1行要約
   - 変更ファイル
   - 必要に応じた主な変更点
2. `docs/DEBUG_LOG.md`
   - 実施したテストと確認内容
3. `docs/RELEASE_POSTS.md`
   - 公開日（`YYYY-MM-DD`形式）
   - そのままXへ投稿できる短い変更説明
4. Git
   - バージョン付きコミット
   - 同じバージョンのタグ
   - GitHubへのプッシュ
5. 本番
   - `https://cuviu.jp/apps/scorebook/`へデプロイ
   - HTTP 200、表示版、ブラウザエラーを確認

## X投稿文

- 利用者に見える改善点を先に書く。
- 実装内部の説明だけにしない。
- 公開URLと `#CuViu` を含める。
- 通常アカウントでも扱いやすい長さを基本にする。
- 作業完了時の報告にも、コピーできる形で添える。

## リリーススクリプト

変更内容を直接渡す場合:

```zsh
zsh scripts/release_patch_scorebook.sh "変更内容の1行要約"
```

`docs/NEXT_RELEASE_NOTE.txt` の先頭行を使う場合:

```zsh
zsh scripts/release_patch_scorebook.sh
```

第2引数を指定すると、X投稿文を個別に上書きできる。

```zsh
zsh scripts/release_patch_scorebook.sh \
  "変更内容の1行要約" \
  $'スコアブック by CuViuを更新しました。\n\n利用者向けの説明\n\nhttps://cuviu.jp/apps/scorebook/\n#CuViu'
```

一時コピーなどで生成結果だけを確認する場合:

```zsh
zsh scripts/release_patch_scorebook.sh --dry-run "変更内容の1行要約"
```
