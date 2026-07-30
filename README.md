# スコアブック by CuViu

球場での入力を中心に設計した、Webベースの野球スコア記録アプリです。
早稲田式と慶應式を切り替えられ、試合中の入力から保存、PDF/JPG出力、X投稿用の文章作成までを扱います。

- 公開アプリ: [https://cuviu.jp/apps/scorebook/](https://cuviu.jp/apps/scorebook/)
- 紹介ページ: [https://cuviu.jp/baseball-scorebook/](https://cuviu.jp/baseball-scorebook/)
- GitHub: [https://github.com/yassao/waseda-scorebook](https://github.com/yassao/waseda-scorebook)

## 目的

- 紙のスコアブックの記録力を、スマートフォンで扱いやすくする
- 入力内容を試合データ、PDF、画像、テキスト速報へ再利用する
- 現場で迷いやすい走者処理や守備変更を、確認しながら入力できるようにする

## 現在の主な機能

- 早稲田式 / 慶應式の表示切替
- SVGベースのスコアボックス描画
- 投球、打席結果、走者進塁、走者アウトの記録
- 18回まで伸長できるスコアシート
- スタメン、代打、代走、投手交代、守備位置変更の管理
- ドラッグ操作と一覧入力を連動させたシート変更
- 簡易スコアボード、R/H/E、投手球数の表示
- 端末内の自動保存、複数試合保存、JSON書き出し / 読み込み
- 早稲田式 / 慶應式PDFとJPG、テキスト速報画像の出力
- 試合前情報、イニング経過、試合結果のX投稿文作成
- 通常アカウント向けのX範囲投稿とX Premium向け長文投稿
- 文字認識結果や生成AIの回答を確認してから登録する入力支援
- 一言メモ、打席時刻、試合開始 / 終了時刻の保存

## 対応している入力例

- `ショートゴロ内野安打`
- `センター前ヒット`
- `ライトオーバー二塁打`
- `5-4-3のダブルプレー`
- `二ゴロ`
- `2ゴロ`
- `見三振`
- `空三振`
- `投犠打`
- `右線ライナー2べ`

## 参考にしている主な資料

- [BASEBALL ONE](https://baseball-one.com/blog/archives/274598/)
- [パ・リーグ.com 記事](https://pacificleague.com/news/2023/2/47589)
- [スコアラー 基本プレー](https://bbscorer.com/help/score_basic.html)

基本方針は `BASEBALL ONE` を主軸にしつつ、事例や表記ゆれの補完に `パ・リーグ.com` と `bbscorer` を使っています。

## ファイル構成

- [index.html](index.html): メインアプリ本体
- [support.html](support.html): 開発支援の案内
- [support-thanks.html](support-thanks.html): 支援後の機能案内
- [docs/](docs): 仕様、変更履歴、確認記録
- [tests/](tests): 保存・球数などの回帰テスト
- [scripts/](scripts): リリースとデプロイ

## 使い方

1. チーム、選手、試合設定を登録する
2. `B`、`S`、`打球結果`などからプレーを入力する
3. 走者とアウトを確認して`次打者へ`進む
4. 試合データ、PDF、JPG、X投稿文を必要に応じて出力する

## 動作確認

組み込みJavaScriptの回帰テスト:

```zsh
node --test tests/scorebook-regression.test.js
```

公開前は、iPhone相当幅で主要入力と出力導線も確認します。

## バージョン管理

現在のバージョンは [docs/VERSION](docs/VERSION) と `index.html` の `APP_VERSION` で一致させます。
変更履歴は [docs/CHANGELOG.md](docs/CHANGELOG.md) に残します。リリーススクリプトは両者が不一致の場合に処理を中止します。

小さな修正:

```zsh
zsh scripts/release_patch_scorebook.sh "変更内容の1行要約"
```

新機能:

```zsh
zsh scripts/release_minor_scorebook.sh "変更内容の1行要約"
```

公開サーバーへの反映:

```zsh
zsh scripts/deploy-cuviu.sh
```

## 仕様ノート

公開用マニュアルとは別に、開発中の判断基準を以下に残します。

- [docs/INPUT_POLICY.md](docs/INPUT_POLICY.md)
  入力UI、自然文入力、X/日刊スポーツ取り込み、つぶやきの方針
- [docs/SCOREBOOK_RULES.md](docs/SCOREBOOK_RULES.md)
  早稲田式スコアブックの記号ルールと解釈方針
- [docs/JSON_DESIGN.md](docs/JSON_DESIGN.md)
  保存JSON、イベントログ、慶応式展開に向けたデータ設計
- [docs/IMPLEMENTATION_PLAN.md](docs/IMPLEMENTATION_PLAN.md)
  6月末公開に向けた実装順序とフェーズ計画

## 現状の注意点

- FC、複合プレー、走者だけがアウトになる場面は、リアルタイム表示を確認してユーザーが最終確定する
- 球数は入力された投球記録を基準にし、欠落がある場合は投手欄から手動補正する
- ブラウザの保存容量には端末差があるため、重要な試合はJSONも書き出す

## 次の優先候補

- 実戦データを用いた走者処理・球数・投手交代の回帰テスト拡充
- 写真のタイムスタンプと打席時刻を使った試合経過への自動配置
- チームメンバー共有の安全なデータ設計
- PWA / iPhoneアプリ化

## 開発メモ

- まとまった変更ごとに GitHub へ push してバックアップする運用
- 実装は `HTML / CSS / JavaScript` の単体ファイル中心
- 変更は回帰テスト、iPhone幅の表示確認、実ブラウザ確認を通してから公開する
- 将来的にデータモデルを分離し、自然言語解析と描画を独立させる想定
