#!/usr/bin/env zsh
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DOCS_DIR="$ROOT_DIR/docs"
VERSION_FILE="$DOCS_DIR/VERSION"
CHANGELOG="$DOCS_DIR/CHANGELOG.md"
DEBUG_LOG="$DOCS_DIR/DEBUG_LOG.md"
INDEX_FILE="$ROOT_DIR/index.html"

BUMP="patch"
while [[ $# -gt 0 && "$1" == -* ]]; do
  case "$1" in
    -m|--minor) BUMP="minor"; shift ;;
    -M|--major) BUMP="major"; shift ;;
    *) echo "不明なオプションです: $1" >&2; exit 1 ;;
  esac
done

SUMMARY="${1:-}"
if [[ -z "$SUMMARY" ]]; then
  echo "変更内容の1行要約を指定してください。" >&2
  echo "例: zsh scripts/release_scorebook.sh \"保存JSONの初期実装\"" >&2
  exit 1
fi

mkdir -p "$DOCS_DIR"
if [[ ! -f "$VERSION_FILE" ]]; then
  echo "0.1.0" > "$VERSION_FILE"
fi

CURRENT_VERSION="$(tr -d '[:space:]' < "$VERSION_FILE")"
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

echo "$NEW_VERSION" > "$VERSION_FILE"

if [[ -f "$INDEX_FILE" ]]; then
  perl -0pi -e "s/const APP_VERSION = \"[^\"]+\";/const APP_VERSION = \"$NEW_VERSION\";/" "$INDEX_FILE"
fi

CHANGED_FILES="$(cd "$ROOT_DIR" && git diff --name-only HEAD | sed '/^$/d' | head -30 || true)"
if [[ -z "$CHANGED_FILES" ]]; then
  CHANGED_FILES="変更なし"
fi

CHANGELOG_ENTRY="$(mktemp)"
cat > "$CHANGELOG_ENTRY" <<EOF
## v$NEW_VERSION [$DATE] $SUMMARY

**変更ファイル:**
$(echo "$CHANGED_FILES" | sed 's/^/- /')

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
git add -A
git commit -m "v$NEW_VERSION: $SUMMARY"
git tag "v$NEW_VERSION"
git push
git push --tags

echo "完了: v$NEW_VERSION $SUMMARY"
