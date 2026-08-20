## v0.4.7 [2026-08-20] 毎回X投稿設定とヘッダーメニュー整理

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.7に更新しました。
> 
> 試合ごとの「毎回X投稿」を追加。オンにすると攻守交代後はX下書きを直接開き、投稿を誤って飛ばしにくくなります。チーム・選手登録と試合設定はヘッダーメニューへ整理しました。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.6 [2026-08-20] AI写真登録の長文貼り付けを安定化

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.6に更新しました。
> 
> AI写真登録で大会・審判情報を含む全文を貼り付けても、両チームの選手名と投手を登録できるよう修正。ChatGPT回答の装飾や不可視改行にも対応しました。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.5 [2026-08-13] 実況入力への直行ボタンとヘッダーメニューを追加

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.5に更新しました。
> 
> 上段に「実況入力」を追加。1打席の音声・自然文入力へすぐ移動できます。表示切替、利き手、守備変更、FB、ヘルプはメニューに整理しました。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.4 [2026-08-10] 投手交代時の球数配分を打順一巡に対応

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.4に更新しました。
> 
> 同じイニングで打順が9番から1番へ一周した時や、打者一巡後に投手交代した時、球数が交代前後の投手へ誤って配分される不具合を修正しました。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.3 [2026-08-10] X投稿画面から三アウト入力を安全に修正

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.3に更新しました。
> 
> スリーアウト後のX投稿画面に「入力を修正」を追加しました。誤って3ストライク目を押した場合は、得点や走者状態を崩さず2ストライクまで戻せます。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.2 [2026-08-10] 登録後の先攻・後攻入れ替えに対応

**変更ファイル:**
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- tests/scorebook-regression.test.js

**X投稿文:**

> スコアブック by CuViuをv0.4.2に更新しました。
> 
> スタメン登録後でも、試合開始前なら先攻・後攻をワンタップで入れ替えられるようになりました。チーム名、選手、背番号、守備位置、先発投手、ベンチ情報をまとめて交換します。
> 
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.4.1 [2026-07-30] Sol運用とリリース履歴・X告知文の保存を開始

**変更ファイル:**
- README.md
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/NEXT_RELEASE_NOTE.txt
- docs/RELEASE_POLICY.md
- docs/RELEASE_POSTS.md
- docs/VERSION
- index.html
- scripts/release_scorebook.sh
- tests/scorebook-regression.test.js

**主な変更:**
- 今後の開発をSolで進める新しい版の起点をv0.4.1に設定。
- TTscore2の運用を参考に、変更履歴を版ごとに保存する方針を明文化。
- リリースごとのX投稿用説明文を保存するファイルと自動生成処理を追加。
- 変更履歴またはX投稿文の更新漏れを回帰テストで検出。

**X投稿文:**

> スコアブック by CuViuをv0.4.1に更新しました。
>
> 自動保存失敗時の緊急書き出し、球数集計の見直し、回帰テスト追加など、試合中の安心感を中心に改善。今後は更新内容も継続してお知らせします。
>
> https://cuviu.jp/apps/scorebook/
> #CuViu #野球スコア

---

## v0.3.95 [2026-07-30] 保存安全性と球数集計の回帰防止

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/VERSION
- index.html
- README.md
- scripts/release_scorebook.sh
- tests/scorebook-regression.test.js

**主な変更:**
- 自動保存に失敗した場合、試合データを即時書き出しできるダイアログを表示。
- 投球記録のない申告敬遠を0球として集計。
- 球数、投手交代後の帰属、手動球数補正、版番号の回帰テストを追加。
- リリース時に `docs/VERSION` とアプリ版が不一致なら処理を中止。
- v0.3.76以降に止まっていたリリース台帳を現行版へ同期。

---

## v0.3.75 [2026-07-24] PDF出力とFc入力を改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.74 [2026-07-24] 投手交代の現在投手判定を安定化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.73 [2026-07-24] v0.3.73: チーム名入力の削除不具合を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.72 [2026-07-24] v0.3.72: 試合前情報と途中経過のX投稿導線を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.71 [2026-07-24] X投稿に投手情報を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.70 [2026-07-24] X投稿を打席ログ形式に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.69 [2026-07-24] 広告代替枠にスポンサー募集を表示

**変更ファイル:**
- docs/VERSION
- docs/baseball-scorebook-wordpress-snippet.html
- index.html

---

## v0.3.68 [2026-07-24] 試合終了後に支援案内を表示

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.67 [2026-07-23] 試合終了後の表示状態を整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.66 [2026-07-23] X範囲投稿中の重複ボタンを整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.65 [2026-07-23] X範囲投稿の文字単位調整に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.64 [2026-07-23] X範囲投稿を手動起動に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.63 [2026-07-23] X範囲投稿の案内文更新ループを修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.62 [2026-07-23] X Premium設定と範囲投稿モードを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.61 [2026-07-23] X投稿分割機能を一時停止

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.60 [2026-07-23] X下書き表示時の分割パネル無限更新を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.59 [2026-07-23] X分割候補を常時表示に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.58 [2026-07-23] X分割表示のiPhone選択状態を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.57 [2026-07-23] X分割表示の固まりを根本修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.56 [2026-07-23] 短文分割とiPhoneのX起動を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.55 [2026-07-23] 分割X投稿をSafariで開きやすく修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.54 [2026-07-23] X投稿分割表示のフリーズを修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.53 [2026-07-23] X投稿分割に連番ヘッダを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.52 [2026-07-23] X投稿の3分割以上に対応

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.51 [2026-07-23] X投稿分割の文字数判定を改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.50 [2026-07-23] X投稿の自動生成署名を調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.49 [2026-07-23] X投稿に自動生成署名を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.48 [2026-07-22] 犠打と封殺FCの走者更新を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.47 [2026-07-22] 投手背番号編集と写真判定プロンプトを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.46 [2026-07-22] 現地FBの入力UIと記憶機能を改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.45 [2026-07-22] 保存失敗時の書き出し導線を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.44 [2026-07-22] 内部保存データを軽量化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.43 [2026-07-21] 打席と投球の時刻打刻を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.42 [2026-07-21] 野手番号パッドを低く調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.41 [2026-07-21] 入力ポップアップの表示位置を調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.40 [2026-07-21] 支援ページ帰還時のチェンジ再開を追加

**変更ファイル:**
- docs/VERSION
- index.html
- support.html

---

## v0.3.39 [2026-07-21] 支援導線を3択ページ化し検証入口を合言葉化

**変更ファイル:**
- docs/VERSION
- index.html
- scripts/README.md
- scripts/deploy-cuviu.sh
- support.html

---

## v0.3.38 [2026-07-21] 支援者状態表示と検証用切替を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.37 [2026-07-21] Stripe支援リンクを特典別URLに更新

**変更ファイル:**
- docs/VERSION
- docs/baseball-scorebook-wordpress-snippet.html
- index.html

---

## v0.3.36 [2026-07-21] 支援完了ページに合言葉コピーを追加

**変更ファイル:**
- docs/VERSION
- index.html
- support-thanks.html

---

## v0.3.35 [2026-07-21] 支援完了ページを特典別案内に対応

**変更ファイル:**
- docs/VERSION
- index.html
- support-thanks.html

---

## v0.3.34 [2026-07-21] 保存済み試合の現在件数を常時表示

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.33 [2026-07-20] 支援者機能ごとの合言葉解除を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.32 [2026-07-20] 支援者設定に合言葉ゲートと保存上限解除を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.31 [2026-07-20] 保存上限の支援導線をリンク付き表示に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.30 [2026-07-20] X下書きの文字数表示と一言メモ切替を改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.29 [2026-07-20] チーム別スタメン記憶と自動入力を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.28 [2026-07-18] 守備変更差分と攻撃表示マーカーを見やすく調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.27 [2026-07-18] シート変更画面をドラッグと手入力で統合

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.26 [2026-07-18] シート変更のドラッグ入替導線を復旧

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.25 [2026-07-18] 現場FBの試合終了判定と守備変更UIを改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.24 [2026-07-18] 守備ボタンから投手交代を開けるように

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.23 [2026-07-18] 延長回の攻守交代先を補正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.22 [2026-07-17] 支援完了ページの手順文を明確化

**変更ファイル:**
- docs/VERSION
- index.html
- support-thanks.html

---

## v0.3.21 [2026-07-17] 支援完了ページの導線を整理

**変更ファイル:**
- docs/VERSION
- index.html
- support-thanks.html

---

## v0.3.20 [2026-07-17] 延長18回までの記録に対応

**変更ファイル:**
- docs/VERSION
- docs/baseball-scorebook-wordpress-snippet.html
- index.html

---

## v0.3.19 [2026-07-17] 支援導線の金額表記を外す

**変更ファイル:**
- docs/VERSION
- docs/baseball-scorebook-wordpress-snippet.html
- index.html

---

## v0.3.18 [2026-07-17] Stripe支援リンクを任意金額リンクに更新

**変更ファイル:**
- docs/VERSION
- docs/baseball-scorebook-wordpress-snippet.html
- index.html

---

## v0.3.17 [2026-07-17] 紹介HPの正をWordPressページに整理

**変更ファイル:**
- docs/VERSION
- index.html
- intro.html
- scripts/README.md
- scripts/deploy-cuviu.sh

---

## v0.3.16 [2026-07-17] 紹介ページを現状機能に合わせて更新

**変更ファイル:**
- docs/VERSION
- index.html
- intro.html
- scripts/README.md
- scripts/deploy-cuviu.sh

---

## v0.3.15 [2026-07-17] 支援後案内ページと隠し検証入口を調整

**変更ファイル:**
- docs/VERSION
- index.html
- scripts/deploy-cuviu.sh

---

## v0.3.14 [2026-07-17] 支援者設定と検証メニュー導線を分離

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.13 [2026-07-17] 支援者向けPDFフッター編集を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.12 [2026-07-17] 支援表示の文言を簡潔化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.11 [2026-07-17] 検証メニューのボタン修正と広告確認プリセット追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.10 [2026-07-17] 隠し検証メニューとサヨナラ検証プリセットを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.9 [2026-07-17] PDFコメントリンクを控えめな吹き出し表示に変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.8 [2026-07-17] 投球数補正とサヨナラ・広告代替・ファールフライ・PDFコメントリンクを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.7 [2026-07-17] PDFフッターにQRとバージョンを追加し白紙ページを抑制

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.6 [2026-07-16] Xポスト下書きにアプリURL付与を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.5 [2026-07-16] 長押しドラッグで守備位置入れ替えを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.4 [2026-07-16] JSON書き出し名に日時サフィックスを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.3 [2026-07-16] PDF保存名に日時サフィックスを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.2 [2026-07-16] 投手背番号と試合時刻の自動反映を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.1 [2026-07-16] スタメン登録とシート変更に確認画面を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.3.0 [2026-07-16] 現場確認の不具合を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.59 [2026-07-16] トップ表示と簡易スコアを整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.58 [2026-07-16] コールド条件と形式切替UIを改善

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.57 [2026-07-16] コールド基準の表示を整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.56 [2026-07-16] Stripe支援リンクを固定額リンクに差し替え

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.55 [2026-07-16] Stripe支援リンクを有効化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.54 [2026-07-15] Stripe支援導線を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.53 [2026-07-15] 広告スペースの文言を削除

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.52 [2026-07-15] PDF出力を1チーム1ページに調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.51 [2026-07-15] 全体表示ボタンの改行を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.50 [2026-07-15] 打順付きシート変更に対応

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.49 [2026-07-15] シート変更導線をヘッダーに追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.48 [2026-07-15] シート変更入力を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.47 [2026-07-15] 守備位置入れ替えを補強

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.46 [2026-07-15] コールド終了処理を連動

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.45 [2026-07-15] タイブレーク修正時の再計算を補強

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.44 [2026-07-15] タイブレーク走者の表示を強化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.43 [2026-07-15] タイブレーク走者を自動配置

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.42 [2026-07-15] 試合規定を画面と共有文へ反映

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.41 [2026-07-15] 修正後の3アウト位置を整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.40 [2026-07-15] 打席修正時の得点板補正確認を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.39 [2026-07-15] 修正後の後続走者イベントを再計算

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.38 [2026-07-14] 空セル選択時の走者復元を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.37 [2026-07-14] 最終状態修正とライブプレビューを補強

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.36 [2026-07-14] 修正保存のチェンジ判定を抑制

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.35 [2026-07-14] 過去セル修正の走者復元を強化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.34 [2026-07-14] 修正時の得点反映を強化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.33 [2026-07-14] 現地検証の試合規定と得点補正を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.32 [2026-07-13] 広告待機を解除

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.31 [2026-07-13] トリプルプレー記録を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.30 [2026-07-13] X下書きで後続打者の走者アウトを分離

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.29 [2026-07-13] X下書きの走者アウト表現を自然にする

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.28 [2026-07-13] 早稲田式ヒット表示の位置を再調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.27 [2026-07-13] 早稲田式のスコアボックス表示を整える

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.26 [2026-07-13] 慶応式の記号サイズとPDF収まりを再調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.25 [2026-07-13] スコア記号とPDF出力の縮尺を調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.24 [2026-07-13] 慶応式の実験表示を触れる段階へ補強

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.23 [2026-07-13] 慶応式の詳細入力表示を補強

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.22 [2026-07-13] フィードバック台帳ビューアを追加

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/FEEDBACK_DB.md
- docs/VERSION
- feedback_admin.php
- index.html
- scripts/README.md
- scripts/deploy-cuviu.sh

---

## v0.2.21 [2026-07-13] フィードバック台帳と二重送信防止を追加

**変更ファイル:**
- .gitignore
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/FEEDBACK_DB.md
- docs/VERSION
- feedback.php
- index.html
- scripts/README.md
- scripts/deploy-cuviu.sh

---

## v0.2.20 [2026-07-13] 匿名フィードバックフォームを追加

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/VERSION
- feedback.php
- index.html
- scripts/README.md
- scripts/deploy-cuviu.sh

---

## v0.2.19 [2026-07-13] 走者イベントと慶応式表示を補強

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.18 [2026-07-13] 走者補正の詳細理由を追加

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/VERSION
- index.html

---

## v0.2.17 [2026-07-12] 走者タップ補正の対象を保持

**変更ファイル:**
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/VERSION
- index.html

---

## v0.2.14 [2026-07-12] X下書きの生還表現を打者側へ寄せる

**変更ファイル:**
- docs/DEBUG_LOG.md
- docs/VERSION
- index.html

---

## v0.2.13 [2026-07-12] X下書きの長打表記を補正

**変更ファイル:**
- docs/DEBUG_LOG.md
- docs/VERSION
- index.html

---

## v0.2.12 [2026-07-11] 慶応式ヒット表示を公式例に寄せる

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.11 [2026-07-08] 忍者AdMax広告タグを差し替え

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.10 [2026-07-04] イニング広告を毎回裏終了時に表示

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.9 [2026-07-04] イニング交代広告に忍者AdMaxタグを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.8 [2026-07-04] 無料版PDFフッターを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.7 [2026-07-04] AdSense審査用コードとプライバシーポリシーリンクを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.6 [2026-06-28] 慶応式の内野安打と得点/残塁記号を調整

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.5 [2026-06-24] ヘルプの改行表示と実験モード表記を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.4 [2026-06-24] ヘルプ表示をベータ版案内へ更新

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.2.3 [2026-06-23] 慶応式安打記号を例12準拠の上段山形へ調整

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.2 [2026-06-23] 慶応式の安打記号を大きな山形表示へ変更

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.2.1 [2026-06-23] 公開リンク向けに表示バージョンを整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.118 [2026-06-18] 慶応式セルを縦長フォーマットへ調整

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.117 [2026-06-16] 打球結果メニューを階層化して整理

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.116 [2026-06-16] 慶応式の振り逃げアウトとバント失敗表示を改善

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.115 [2026-06-16] 慶応式の三振アウト表記を修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.114 [2026-06-16] 慶応式のフライとベースカバー表記を修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.113 [2026-06-16] 慶応式の打者アウト表示を分数形式に修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.112 [2026-06-15] 慶応式の出塁表示を公式寄せ

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.111 [2026-06-15] 慶応式ヘッダーとセル枠を修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.110 [2026-06-15] 慶応式切り替えタップを安定化

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.109 [2026-06-13] 慶応式セルの基本枠を修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.108 [2026-06-11] 慶応式セルの塁配置を公式寄せ

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.107 [2026-06-11] 慶応式セルの非公式表示を削除

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.106 [2026-06-11] 慶応式の投手交代表示を整理

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.105 [2026-06-11] 慶応式の走者イベント表示を分離

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.104 [2026-06-11] 慶応式公式ルール棚卸しと記号修正

**変更ファイル:**
- docs/KEIO_SCOREBOOK_NOTES.md
- docs/VERSION
- index.html

---

## v0.1.103 [2026-06-11] 慶応式で左上プレビューも切替

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.102 [2026-06-11] 慶応式セルの赤進塁線を削除

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.101 [2026-06-10] 慶応式スコアブック実験モードを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.100 [2026-06-08] 内部保存30試合上限の案内を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.99 [2026-06-08] イニング広告のスキップ待機を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.98 [2026-06-08] PDF広告未視聴カウントを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.97 [2026-06-08] 広告ゲートの土台を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.96 [2026-06-08] 内部メモ表示を削除

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.95 [2026-06-08] PDFにテキスト速報ページを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.94 [2026-06-08] 継投多数時のPDF詳細ページを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.93 [2026-06-06] トップ画面にCuViuホームリンクを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.92 [2026-06-06] トップ画面を空色トーンへ刷新

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.91 [2026-06-06] 紹介ページを追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.90 [2026-05-31] PHとPRの表示を明確化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.89 [2026-05-31] 現在打者カードを2行表示へ圧縮

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.88 [2026-05-31] 現在打者カードの選手名省略を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.87 [2026-05-31] 投手交代の青波線をマス上端へ移動

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.86 [2026-05-31] 投手交代ダイアログの長押し起動を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.85 [2026-05-31] AirPrint未選択時のPDF改ページを抑制

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.84 [2026-05-31] リアルタイム得点表示のチーム名幅を拡張

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.83 [2026-05-31] 得点板の生還記録重複カウントを抑制

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.82 [2026-05-31] 保存ホームボタンと犠飛の自動生還を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.81 [2026-05-31] 一言メモ行と選手表示の即時反映を調整

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.80 [2026-05-31] バント失敗の入力項目を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.79 [2026-05-28] PDF出力の余白と改ページを抑制

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.78 [2026-05-28] スマホ入力ブロックの縦余白を調整

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.77 [2026-05-28] 左手時のS選択配置と次打者ボタン状態を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.76 [2026-05-28] S選択ダイアログを片手操作向けに配置

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.75 [2026-05-28] S選択ダイアログのタップ反映を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.74 [2026-05-28] ボールカウント記号を古い表示に統一

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.73 [2026-05-28] 保存済み試合を開く前に自動保存を退避

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.72 [2026-05-28] 画面離脱時の自動保存を即時化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.71 [2026-05-28] 進塁後走者の本塁アウト入力を補強

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.70 [2026-05-28] 走者プレビューと投球数集計を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.69 [2026-05-28] 投球選択と牽制アウト入力を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.68 [2026-05-28] 選手登録導線を試合中向けに改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.67 [2026-05-28] X下書きの打球表現を自然化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.66 [2026-05-28] PDF出力の幅揃えと空白ページ抑制

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.65 [2026-05-28] 投手欄の列ずれを修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.64 [2026-05-28] 三振時カウント表示と日刊取り込み導線を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.63 [2026-05-19] PDF出力の2ページ化を抑制

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.62 [2026-05-19] 不要な一時ファイルを除外

**変更ファイル:**
- .gitignore
- docs/.IMPLEMENTATION_PLAN.md.swp
- docs/VERSION
- index.html

---

## v0.1.61 [2026-05-19] iPhone PDF出力とX下書き表現を補強

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.60 [2026-05-19] PDF出力をA4縦1枚に調整

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.59 [2026-05-19] PDF出力を公式紙面レイアウト化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.58 [2026-05-19] X下書きの打席表現を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.57 [2026-05-19] 閲覧モードPDF出力と球数集計を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.56 [2026-05-18] 入力周辺ボタン名を整理

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.55 [2026-05-18] 一言メモとチーム登録UIを整理

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.54 [2026-05-17] Xで開くボタンの重複を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.53 [2026-05-17] ヘッダーに版表示を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.52 [2026-05-17] X下書き表示の更新確認を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.51 [2026-05-17] X下書きの投稿導線を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.50 [2026-05-17] X下書きコピーとハッシュタグを改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.49 [2026-05-17] 新規試合開始と右上表示を再調整

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.48 [2026-05-17] 新規試合開始のリセットを安定化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.47 [2026-05-17] 入力右上にスコアボードを移動

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.46 [2026-05-17] Xスタメン投稿の一括登録を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.45 [2026-05-16] DeNA型OCRの選手名補正を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.44 [2026-05-16] OCR写真選択導線を整理

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.43 [2026-05-16] OCRのDH登録と名前補正を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.42 [2026-05-16] 縦書きOCRの名前補完を強化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.41 [2026-05-16] 写真文字認識から選手名登録を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.40 [2026-05-16] 併殺連係をPDF式ベース踏みに変換

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.39 [2026-05-16] 走者アウトのPDF表示を調整

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.38 [2026-05-16] 一二三併殺の打者アウト判定を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.37 [2026-05-16] 走者アウト表示をセル中央から分離

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.36 [2026-05-16] アウト走者残像と試合データ書き出しを改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.35 [2026-05-16] 併殺解釈の確認を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.34 [2026-05-16] 打者走者が残る併殺に対応

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.33 [2026-05-16] 共有下書きの三振表記を改善

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.32 [2026-05-16] 三アウト時の共有確認を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.31 [2026-05-16] 入力チーム切替ボタンの配置を修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.30 [2026-05-16] 記録消去後の走者再計算と入力チーム切替追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.29 [2026-05-16] PDF準拠の三振記号へ修正

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.28 [2026-05-16] 投球ごとのつぶやき保存を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.27 [2026-05-16] 投手交代をスコアブックへ反映

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.26 [2026-05-16] JSON表記を試合データへ変更

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.25 [2026-05-16] JSON書き出し成功表示を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.24 [2026-05-16] 新規試合確認にJSON書き出し導線追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.23 [2026-05-16] 投手交代ログを公式欄へ追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.22 [2026-05-15] 残塁と走者イベントをJSON集計へ反映

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.21 [2026-05-15] 投球記号と走者イベント時点をPDF準拠化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.20 [2026-05-15] 現在打者表示と投球時点コメント保存

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.19 [2026-05-15] 片手入力レイアウト切替を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.18 [2026-05-15] 手動詳細入力削除とPDF準拠項目追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.17 [2026-05-15] X取り込みの連結行分割を補強

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.16 [2026-05-15] 日刊スポーツAI整形導線を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.15 [2026-05-15] AI変換ダイアログの前面表示修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.14 [2026-05-15] AI変換ダイアログとX取り込み安定化

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.13 [2026-05-15] AI入力の吹き出しコメント抽出を修正

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.12 [2026-05-15] 1打席AI整形入力を追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.11 [2026-05-15] PDF資料準拠のJSON項目整理

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/JSON_DESIGN.md
- docs/SCOREBOOK_RULES.md
- docs/VERSION
- index.html

---

## v0.1.10 [2026-05-15] 過去セル編集ダイアログを追加

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.9 [2026-05-14] Phase1完了とPhase2開始を計画書に反映

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.8 [2026-05-14] 保存済み試合一覧とトップ復帰導線追加

**変更ファイル:**
- docs/JSON_DESIGN.md
- docs/VERSION
- index.html

---

## v0.1.7 [2026-05-14] 保存JSONにイベント下書きを同梱

**変更ファイル:**
- docs/JSON_DESIGN.md
- docs/VERSION
- index.html

---

## v0.1.6 [2026-05-14] 簡易トップ画面を追加

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.5 [2026-05-14] JSON書き出し導線を下部操作へ移動

**変更ファイル:**
- docs/VERSION
- index.html

---

## v0.1.4 [2026-05-14] 保存JSONと自動保存の初期実装

**変更ファイル:**
- docs/JSON_DESIGN.md
- docs/VERSION
- index.html

---

## v0.1.3 [2026-05-14] 取り込み機能と入口体験の計画を整理

**変更ファイル:**
- docs/IMPLEMENTATION_PLAN.md
- docs/VERSION
- index.html

---

## v0.1.2 [2026-05-14] 6月末公開に向けた実装計画を追加

**変更ファイル:**
- README.md
- docs/VERSION
- index.html

---

## v0.1.1 [2026-05-14] 入力方針と保存JSONの仕様ノートを追加

**変更ファイル:**
- README.md
- docs/VERSION
- index.html

---

# Scorebook by CuViu CHANGELOG

## v0.1.0 [2026-05-14] バージョン管理の開始

**概要:**
- 入力・描画・インポート試作を一般公開前の初期版として整理
- 今後の保存JSONに使う `APP_VERSION` と `DATA_SCHEMA_VERSION` を追加
- 1行要約で変更履歴を残す運用を開始

**変更ファイル:**
- index.html
- docs/VERSION
- docs/CHANGELOG.md
- docs/DEBUG_LOG.md
- docs/RELEASE_MANUAL.md
- scripts/release_scorebook.sh
- scripts/release_patch_scorebook.sh
- scripts/release_minor_scorebook.sh
- scripts/release_major_scorebook.sh

---
