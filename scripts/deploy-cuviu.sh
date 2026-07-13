#!/usr/bin/env zsh
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
LOCAL_FILE="$ROOT_DIR/index.html"
LOCAL_FEEDBACK_FILE="$ROOT_DIR/feedback.php"

SSH_KEY="${CUVIU_SSH_KEY:-$HOME/.ssh/xserver_cuviu}"
SSH_HOST="${CUVIU_SSH_HOST:-sv16692.xserver.jp}"
SSH_USER="${CUVIU_SSH_USER:-cuviu001}"
SSH_PORT="${CUVIU_SSH_PORT:-10022}"
REMOTE_DIR="${CUVIU_REMOTE_DIR:-/home/cuviu001/cuviu.jp/public_html/apps/scorebook}"
REMOTE_FILE="$REMOTE_DIR/index.html"
REMOTE_TMP="$REMOTE_DIR/index.html.tmp"
REMOTE_FEEDBACK_FILE="$REMOTE_DIR/feedback.php"
REMOTE_FEEDBACK_TMP="$REMOTE_DIR/feedback.php.tmp"

if [[ ! -f "$LOCAL_FILE" ]]; then
  echo "ローカルの index.html が見つかりません: $LOCAL_FILE" >&2
  exit 1
fi

if [[ ! -f "$SSH_KEY" ]]; then
  echo "SSH秘密鍵が見つかりません: $SSH_KEY" >&2
  echo "必要なら CUVIU_SSH_KEY=/path/to/key を指定してください。" >&2
  exit 1
fi

echo "Deploying $LOCAL_FILE"
echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_FILE"

ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mkdir -p '$REMOTE_DIR'"
scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_TMP"
ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_TMP' '$REMOTE_FILE'"

if [[ -f "$LOCAL_FEEDBACK_FILE" ]]; then
  echo "Deploying $LOCAL_FEEDBACK_FILE"
  echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_FILE"
  scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_FEEDBACK_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_TMP"
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_FEEDBACK_TMP' '$REMOTE_FEEDBACK_FILE' && chmod 644 '$REMOTE_FEEDBACK_FILE'"
fi

echo "Deploy complete:"
echo "https://cuviu.jp/apps/scorebook/"
