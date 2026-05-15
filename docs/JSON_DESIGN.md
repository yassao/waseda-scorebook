# 保存JSON設計

Scorebook by CuViu の保存JSONは、早稲田式の見た目そのものではなく、野球の出来事を保存する。  
これにより、早稲田式・慶応式・PDF・X書き出し・クラウド共有へ展開しやすくする。

## 設計方針

- `schemaVersion` を必ず持つ。
- `appVersion` を必ず持つ。
- 完成画像ではなく、試合情報、選手、打席、イベント、コメントを保存する。
- 描画は保存データから再生成する。
- 後から編集できるように、イベント単位で記録する。
- 取り込み元の原文やコメントも残せるようにする。
- 将来の互換性のため、未知の項目を破棄しない方針にする。

## 最小構造

現在の実装では、安全な保存・復元を優先するため、既存アプリ状態を `snapshot` として残している。
そのうえで、将来の共有・PDF・慶応式表示へ移行しやすいように、`game` / `teams` / `plateAppearances` / `events` / `comments` も `event-draft-1` として同梱する。

読み込みは当面 `snapshot` を優先する。
これにより、イベント型JSONの精度を上げている途中でも、既存ユーザーの保存データを壊さない。

### 端末内保存の扱い

- 自動保存は `cuviu_scorebook_current_game_v1` に1件だけ保存する。
- 手動保存は `cuviu_scorebook_saved_games_v1` に複数件の保存スロットとして保存する。
- 手動保存スロットの中身は下記の互換保存JSONそのものを `data` として持つ。
- アプリの復元は、当面どちらも `snapshot` を優先して行う。
- 将来クラウド同期を入れる場合も、保存スロット単位でサーバーへ送れる構造にする。

### 現行の互換保存JSON

```json
{
  "schemaVersion": 1,
  "appVersion": "0.1.7",
  "savedAt": "2026-05-14T12:00:00.000Z",
  "modelVersion": "event-draft-1",
  "game": {},
  "teams": [],
  "plateAppearances": [],
  "events": [],
  "comments": [],
  "snapshot": {
    "state": {},
    "naturalText": "",
    "playPartsHtml": ""
  }
}
```

### 将来のイベント型JSON

将来は `snapshot` に依存せず、下記のイベント型JSONだけから描画できる状態を目指す。

```json
{
  "schemaVersion": 1,
  "appVersion": "0.1.0",
  "savedAt": "2026-05-14T12:00:00+09:00",
  "game": {},
  "teams": [],
  "plateAppearances": [],
  "events": [],
  "comments": []
}
```

## game

試合全体の情報を保存する。

```json
{
  "id": "game_20260514_001",
  "date": "2026-05-14",
  "venue": "",
  "title": "",
  "competition": "",
  "fieldCondition": "",
  "weather": "",
  "wind": "",
  "attendance": null,
  "broadcast": "",
  "scorer": "",
  "startTime": "",
  "endTime": "",
  "durationMinutes": null,
  "umpires": {
    "plate": "",
    "first": "",
    "second": "",
    "third": ""
  },
  "benches": {
    "firstBaseSideTeamId": "team_top",
    "thirdBaseSideTeamId": "team_bottom"
  },
  "topTeamId": "team_top",
  "bottomTeamId": "team_bottom",
  "currentHalf": "top",
  "currentInning": 1,
  "currentBattingOrder": 1,
  "outs": 0,
  "score": {
    "top": [0, 0, 0, 0, 0, 0, 0, 0, 0],
    "bottom": [0, 0, 0, 0, 0, 0, 0, 0, 0]
  }
}
```

### 方針

- `currentHalf` は `top` または `bottom`。
- `score` は将来延長対応のため配列を伸ばせる。
- 0点も表示できるよう、イニング終了時または得点発生時に値を入れる。
- 長崎県高野連資料の紙面上部にある、大会名、年月日、球場、球場状態、天候・風向、観衆、開始・終了・所要時間、審判員、記録者、放送欄を保存できるようにする。
- 一塁側・三塁側のチームは、先攻・後攻と一致しない場合があるため `benches` に分けて保存する。

## teams

チーム情報と打順を保存する。

```json
{
  "id": "team_top",
  "name": "先攻",
  "lineup": [
    {
      "battingOrder": 1,
      "slots": [
        {
          "playerId": "p_top_1_a",
          "name": "選手A",
          "number": "1",
          "position": "8",
          "hand": "",
          "fromInning": 1,
          "toInning": null
        }
      ]
    }
  ]
}
```

### 方針

- 打順ごとに `slots` を持ち、交代選手を縦3段に表示できるようにする。
- 1人目、2人目、3人目は別スロット。
- 4人目以降は暫定的に3段目へ `/` 区切りで表示してもよい。
- `position` は守備位置、`hand` は左打者などの打ち方、`number` は背番号として紙面左側に再現できるよう保存する。
- 守備位置変更、代打、代走、投手交代は `substitution` イベントにも保存し、どのイニング・どの打席で発生したかを後から復元できるようにする。

## officialSheet

紙のスコアブックで下段・右側に集計する情報を保存する。
初期実装では自動集計できる範囲から埋め、ユーザー修正を受け入れられる構造にする。

```json
{
  "officialSheet": {
    "pitchers": [
      {
        "teamId": "team_top",
        "playerId": "p_top_pitcher_1",
        "throws": "right",
        "name": "",
        "decision": "",
        "save": false,
        "inningsPitched": "0",
        "pitchCountByInning": [],
        "totalPitchCount": 0,
        "battersFaced": 0,
        "atBats": 0,
        "hits": 0,
        "extraBaseHits": 0,
        "sacBunts": 0,
        "sacFlies": 0,
        "walks": 0,
        "hitByPitch": 0,
        "strikeouts": 0,
        "wildPitches": 0,
        "balks": 0,
        "runs": 0,
        "earnedRuns": 0
      }
    ],
    "catchers": [
      {
        "teamId": "team_top",
        "playerId": "p_top_catcher_1",
        "passedBalls": 0,
        "stolenBasesAllowed": 0,
        "caughtStealing": 0
      }
    ],
    "extraBaseHits": {
      "homeRuns": [],
      "triples": [],
      "doubles": []
    },
    "teamTotals": {
      "top": {},
      "bottom": {}
    }
  }
}
```

### 方針

- PDF出力時に紙面下段の投手欄、捕手欄、長打欄へ流し込めるようにする。
- 投球数は、左をイニング投球数、右を合計投球数として保存できるようにする。
- `officialSheet` は自動集計と手入力補正を併用する。記録員が後から修正した値を優先できるよう、将来 `manualOverride` を追加する。

## plateAppearances

打席単位の主要データを保存する。

```json
{
  "id": "pa_0001",
  "teamId": "team_top",
  "inning": 1,
  "half": "top",
  "inningColumn": 1,
  "battingOrder": 1,
  "playerId": "p_top_1_a",
  "sequenceInInning": 1,
  "pitches": [
    {
      "type": "strike_swing",
      "symbol": "×",
      "note": ""
    }
  ],
  "batterResult": {
    "type": "hit",
    "base": 1,
    "fielder": "8",
    "direction": "front",
    "ballType": "liner"
  },
  "outsBefore": 0,
  "outsAfter": 0,
  "runsScored": 0,
  "rbi": 0,
  "rawText": "",
  "source": "manual",
  "commentIds": []
}
```

### 方針

- `inningColumn` はスコアブック上の列番号。
- 打者一巡で同一イニングに複数列が必要になった場合、同じ `inning` でも `inningColumn` を増やす。
- `sequenceInInning` はアウトカウント付与や取り込み順のために使う。
- `source` は `manual`、`x_import`、`nikkan_import`、`natural_language` など。
- 現行の `event-draft-1` では、既存のスコアブックセルから抽出できる範囲を保存する。
- 打者一巡時の完全な入力順、複雑な走者イベントの正規化は今後の改善対象。
- 投球記号は新規データでは資料準拠の `○`、`×`、`・`、`－` を基本にする。旧データやアプリ内部の互換記号は読み込み時に変換できるようにする。
- 得点・残塁・アウトカウントは空欄にせず、紙面上で何らかの状態が分かるよう保存する。

## events

打者・走者・守備・得点・修正などの出来事を保存する。

```json
{
  "id": "ev_0001",
  "plateAppearanceId": "pa_0001",
  "teamId": "team_top",
  "inning": 1,
  "half": "top",
  "sequence": 1,
  "actor": {
    "type": "batter",
    "battingOrder": 1,
    "playerId": "p_top_1_a"
  },
  "type": "advance",
  "fromBase": "home",
  "toBase": "first",
  "reason": "hit",
  "creditedToPlateAppearanceId": "pa_0001",
  "notation": {
    "color": "red",
    "text": "8",
    "fielderSequence": ["8"],
    "direction": "front"
  }
}
```

### 主なイベント種別

| type | 内容 |
|---|---|
| `pitch` | 投球 |
| `batter_result` | 打者結果 |
| `advance` | 走者進塁 |
| `runner_out` | 走者アウト |
| `batter_out` | 打者アウト |
| `run_scored` | 得点 |
| `left_on_base` | 残塁 |
| `error` | 失策 |
| `substitution` | 選手交代 |
| `comment` | コメント |
| `correction` | 後から修正 |

### 現行 `event-draft-1` の扱い

- 投球は `pitch` イベントとして保存する。
- 打者結果は `batter_result` イベントとして保存する。
- 走者進塁・走者アウトは、現時点では `runnerContext` と `runnerStatusEvents` を保持する。
- コメントは `comments` に分離し、打席側の `commentIds` と紐づける。
- 後から編集できる本格的なイベント正規化は、保存互換性を維持しながら段階的に進める。

## runner event examples

### 一走が打者安打で三塁へ

```json
{
  "type": "advance",
  "actor": {
    "type": "runner",
    "battingOrder": 1,
    "baseBeforePlay": "first"
  },
  "fromBase": "first",
  "toBase": "third",
  "reason": "batted_ball",
  "creditedToPlateAppearanceId": "pa_0002"
}
```

### 二盗死

```json
{
  "type": "runner_out",
  "actor": {
    "type": "runner",
    "battingOrder": 1,
    "baseBeforePlay": "first"
  },
  "fromBase": "first",
  "toBase": "second",
  "reason": "caught_stealing",
  "outNumber": 1,
  "fielderSequence": ["2", "6"]
}
```

### 封殺 & FC

```json
{
  "type": "runner_out",
  "actor": {
    "type": "runner",
    "battingOrder": 1,
    "baseBeforePlay": "first"
  },
  "fromBase": "first",
  "toBase": "second",
  "reason": "force_out",
  "outNumber": 1,
  "fielderSequence": ["4", "6"]
}
```

```json
{
  "type": "batter_result",
  "actor": {
    "type": "batter",
    "battingOrder": 2
  },
  "reason": "fielders_choice",
  "toBase": "first",
  "fielderSequence": ["6", "3"]
}
```

## comments

つぶやきや取り込み元コメントを保存する。

```json
{
  "id": "comment_0001",
  "target": {
    "type": "plateAppearance",
    "plateAppearanceId": "pa_0001"
  },
  "text": "低いライナーで抜けた",
  "source": "manual",
  "createdAt": "2026-05-14T12:00:00+09:00",
  "updatedAt": "2026-05-14T12:00:00+09:00"
}
```

### 方針

- X取り込みの原文はコメントとして保存する。
- ユーザーのつぶやきもコメントとして保存する。
- スコアブック上には吹き出しで表示する。

## source

データの由来を保存する。

| source | 内容 |
|---|---|
| `manual` | ボタン・GUI入力 |
| `natural_language` | 1打席自然文入力 |
| `x_import` | X / Twitter取り込み |
| `nikkan_import` | 日刊スポーツ取り込み |
| `ai_formatted` | AI整形後の取り込み |

## correction

後から編集した場合は、上書きだけでなく修正履歴を残せるようにする。

```json
{
  "id": "ev_correction_0001",
  "type": "correction",
  "targetEventId": "ev_0001",
  "before": {
    "type": "error"
  },
  "after": {
    "type": "hit"
  },
  "createdAt": "2026-05-14T12:10:00+09:00"
}
```

初期実装では完全な履歴保持は必須ではない。  
ただし将来のため、修正履歴を追加できる設計にしておく。

## localStorage

初期実装ではローカル保存を優先する。

保存キー案:

```text
cuviu_scorebook_current_game_v1
cuviu_scorebook_games_v1
cuviu_scorebook_settings_v1
```

### 必要機能

- 自動保存
- 試合一覧
- JSONエクスポート
- JSONインポート
- 保存データのバージョン確認

## migration

`schemaVersion` が変わった場合は、読み込み時に変換する。

```js
function migrateGameData(data) {
  if (!data.schemaVersion) {
    return migrateLegacyData(data);
  }
  if (data.schemaVersion === 1) {
    return data;
  }
  throw new Error("未対応の保存形式です");
}
```

## 早稲田式・慶応式との関係

保存JSONは表示方式に依存させない。

```text
保存データ: 野球イベント
表示レイヤー: 早稲田式 / 慶応式 / PDF / X書き出し
```

この分離により、入力が固まったあとに慶応式表示を追加しやすくする。

## 5月中旬の実装目標

- `schemaVersion` と `appVersion` を持つ保存データを作る。
- 現在の試合を `localStorage` に自動保存する。
- ページ再読み込み後に復元する。
- JSONエクスポート / インポートを実装する。
- 既存の描画ロジックをすべて置き換えず、まずは保存の入口を作る。

## 未決事項

- 既存 `state` との対応表
- `scoreSheet` の保存粒度
- 完全なイベントログ方式に移行するタイミング
- 過去セル編集時の履歴保持
- クラウド同期時の競合解決
