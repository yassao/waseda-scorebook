# 早稲田式野球スコアブック試作

早稲田式スコアブックをもとにした、Webベースの野球スコア記録アプリの試作です。  
現時点では `1打席の記録` をかなり細かく扱えるところまで進んでいて、あわせて `9人 × 12回` のシート土台も入っています。

GitHub:
[https://github.com/yassao/waseda-scorebook](https://github.com/yassao/waseda-scorebook)

## 目的

- 早稲田式スコアブックをスマホでも扱いやすくする
- 将来的に `PWA` や `iPhoneアプリ化` へつなげる
- 自然言語入力や音声入力で、実況メモからスコアへ反映できるようにする

## 現在の主な機能

- SVGベースのスコアボックス描画
- 投球欄の記録
- 守備位置、打球種別、方向ドット
- 単打 / 二塁打 / 三塁打 / 本塁打の赤斜線
- ゴロ安 / バントヒットの半円
- `BH`、`E`、`A-D` などの補助表記
- `Ⅰ / Ⅱ / Ⅲ / ● / ○ / ℓ` の中央表示
- 自然言語入力の初期辞書
- ユーザー修正内容の `localStorage` 保存
- `9人 × 9回` シートの土台
- 打席保存、選択、次打席への移動
- ミニSVGでのシート反映
- 一部の走者進塁・走者アウト追記
- 盗塁 / 盗塁刺 / 重盗 / 三重盗 / 走塁妨害 / 守備妨害などの補助記号

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

- [index.html](/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac/index.html)
  メインアプリ本体
- [baseball-one-check-01.html](/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac/baseball-one-check-01.html)
- [baseball-one-check-02.html](/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac/baseball-one-check-02.html)
- [baseball-one-check-03.html](/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac/baseball-one-check-03.html)
- [baseball-one-check-04.html](/Users/yasufumi/Documents/Codex/2026-04-25-web-iphone-pwa-python-basic-mac/baseball-one-check-04.html)
  `BASEBALL ONE` 比較用の確認ページ

## 使い方

1. `index.html` をブラウザで開く
2. ボタン入力または自然言語入力で1打席を作る
3. `この打席を保存` でシートへ反映する
4. `次の打席へ` で次セルへ進む

## 現状の注意点

- 走者進塁の完全自動追跡はまだ途中
- 併殺や封殺のケースは一部のみ対応
- `9人 × 12回` シートはまだ表裏分離や交代処理が未実装
- ミニシート表示は簡易SVGで、本体ボックスの完全再現ではない

## 次の優先候補

- 走者進塁と走者アウトの自動追記を強化
- `表 / 裏` と `先攻 / 後攻` を持つ
- スタメン、交代、代打、代走の管理
- シート側のミニSVGを本体表示にさらに近づける
- `localStorage` 保存 / 読み込みの整理
- PWA化

## 開発メモ

- まとまった変更ごとに GitHub へ push してバックアップする運用
- 実装は `HTML / CSS / JavaScript` の単体ファイル中心
- 将来的にデータモデルを分離し、自然言語解析と描画を独立させる想定
