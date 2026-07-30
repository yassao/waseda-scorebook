#!/usr/bin/env zsh
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DOCS_DIR="$ROOT_DIR/docs"
VERSION_FILE="$DOCS_DIR/VERSION"
CHANGELOG="$DOCS_DIR/CHANGELOG.md"
DEBUG_LOG="$DOCS_DIR/DEBUG_LOG.md"
RELEASE_POSTS="$DOCS_DIR/RELEASE_POSTS.md"
NEXT_RELEASE_NOTE="$DOCS_DIR/NEXT_RELEASE_NOTE.txt"
INDEX_FILE="$ROOT_DIR/index.html"
PUBLIC_URL="https://cuviu.jp/apps/scorebook/"

BUMP="patch"
DRY_RUN=0
while [[ $# -gt 0 && "$1" == -* ]]; do
  case "$1" in
    -m|--minor) BUMP="minor"; shift ;;
    -M|--major) BUMP="major"; shift ;;
    -n|--dry-run) DRY_RUN=1; shift ;;
    *) echo "不明なオプションです: $1" >&2; exit 1 ;;
  esac
done

SUMMARY="${1:-}"
X_POST="${2:-}"
USED_NEXT_RELEASE_NOTE=0
if [[ -z "$SUMMARY" && -f "$NEXT_RELEASE_NOTE" ]]; then
  SUMMARY="$(tr -d '\r' < "$NEXT_RELEASE_NOTE" | sed '/^[[:space:]]*$/d' | head -n 1)"
  USED_NEXT_RELEASE_NOTE=1
fi
if [[ -z "$SUMMARY" ]]; then
  echo "変更内容の1行要約を指定してください。" >&2
  echo "例: zsh scripts/release_scorebook.sh \"保存JSONの初期実装\"" >&2
  echo "または docs/NEXT_RELEASE_NOTE.txt の先頭行に入力してください。" >&2
  exit 1
fi

mkdir -p "$DOCS_DIR"
if [[ ! -f "$VERSION_FILE" ]]; then
  echo "0.1.0" > "$VERSION_FILE"
fi

CURRENT_VERSION="$(tr -d '[:space:]' < "$VERSION_FILE")"
INDEX_VERSION=""
if [[ -f "$INDEX_FILE" ]]; then
  INDEX_VERSION="$(sed -n 's/.*const APP_VERSION = "\([^"]*\)";.*/\1/p' "$INDEX_FILE" | head -1)"
fi
if [[ -n "$INDEX_VERSION" && "$CURRENT_VERSION" != "$INDEX_VERSION" ]]; then
  echo "リリース中止: docs/VERSION ($CURRENT_VERSION) と index.html ($INDEX_VERSION) が一致しません。" >&2
  echo "先に両方を同じ現在版へ揃えてください。" >&2
  exit 1
fi
IFS='.' read -r MAJOR MINOR PATCH <<< "$CURRENT_VERSION"
MAJOR="${MAJOR:-0}"
MINOR="${MINOR:-0}"
PATCH="${PATCH:-0}"

case "$BUMP" in
  major)
    MAJOR=$((MAJOR + 1))
    MINOR=0
    PATCH=0
    ;;
  minor)
    MINOR=$((MINOR + 1))
    PATCH=0
    ;;
  patch)
    PATCH=$((PATCH + 1))
    ;;
esac

NEW_VERSION="$MAJOR.$MINOR.$PATCH"
DATE="$(date '+%Y-%m-%d')"
TIMESTAMP="$(date '+%Y-%m-%d %H:%M:%S')"
if [[ -z "$X_POST" ]]; then
  X_POST="スコアブック by CuViuをv${NEW_VERSION}に更新しました。

$SUMMARY

$PUBLIC_URL
#CuViu #野球スコア"
fi

echo "$NEW_VERSION" > "$VERSION_FILE"
if [[ "$USED_NEXT_RELEASE_NOTE" -eq 1 ]]; then
  : > "$NEXT_RELEASE_NOTE"
fi

if [[ -f "$INDEX_FILE" ]]; then
  perl -0pi -e "s/const APP_VERSION = \"[^\"]+\";/const APP_VERSION = \"$NEW_VERSION\";/" "$INDEX_FILE"
fi

RELEASE_POST_ENTRY="$(mktemp)"
cat > "$RELEASE_POST_ENTRY" <<EOF
## v$NEW_VERSION [$DATE]

$X_POST

---

EOF

if [[ -f "$RELEASE_POSTS" ]]; then
  if head -n 1 "$RELEASE_POSTS" | grep -q '^# '; then
    {
      head -n 4 "$RELEASE_POSTS"
      cat "$RELEASE_POST_ENTRY"
      tail -n +5 "$RELEASE_POSTS"
    } > "$RELEASE_POST_ENTRY.merged"
    mv "$RELEASE_POST_ENTRY.merged" "$RELEASE_POST_ENTRY"
  else
    cat "$RELEASE_POSTS" >> "$RELEASE_POST_ENTRY"
  fi
fi
if [[ ! -f "$RELEASE_POSTS" ]]; then
  {
    echo "# Xリリース投稿文"
    echo
    echo "新しい投稿を上へ追加します。"
    echo
    cat "$RELEASE_POST_ENTRY"
  } > "$RELEASE_POSTS"
  rm "$RELEASE_POST_ENTRY"
else
  mv "$RELEASE_POST_ENTRY" "$RELEASE_POSTS"
fi

CHANGED_FILES="$(cd "$ROOT_DIR" && git status --short --untracked-files=all | cut -c4- | sed '/^$/d' | head -30 || true)"
if [[ -z "$CHANGED_FILES" ]]; then
  CHANGED_FILES="変更なし"
fi

CHANGELOG_ENTRY="$(mktemp)"
cat > "$CHANGELOG_ENTRY" <<EOF
## v$NEW_VERSION [$DATE] $SUMMARY

**変更ファイル:**
$(echo "$CHANGED_FILES" | sed 's/^/- /')

**X投稿文:**

$(echo "$X_POST" | sed 's/^/> /')

---

EOF

if [[ -f "$CHANGELOG" ]]; then
  cat "$CHANGELOG" >> "$CHANGELOG_ENTRY"
fi
mv "$CHANGELOG_ENTRY" "$CHANGELOG"

DEBUG_ENTRY="$(mktemp)"
cat > "$DEBUG_ENTRY" <<EOF
## v$NEW_VERSION [$TIMESTAMP] $SUMMARY

**確認:**
- リリーススクリプトで記録

---

EOF

if [[ -f "$DEBUG_LOG" ]]; then
  cat "$DEBUG_LOG" >> "$DEBUG_ENTRY"
fi
mv "$DEBUG_ENTRY" "$DEBUG_LOG"

cd "$ROOT_DIR"
if [[ "$DRY_RUN" -eq 1 ]]; then
  echo "確認完了: v$NEW_VERSION $SUMMARY"
  echo "dry-runのため、commit・tag・pushは実行していません。"
  exit 0
fi
git add -A
git commit -m "v$NEW_VERSION: $SUMMARY"
git tag "v$NEW_VERSION"
git push
git push --tags

echo "完了: v$NEW_VERSION $SUMMARY"
