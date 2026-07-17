#!/usr/bin/env zsh
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
LOCAL_FILE="$ROOT_DIR/index.html"
LOCAL_INTRO_FILE="$ROOT_DIR/intro.html"
LOCAL_SUPPORT_THANKS_FILE="$ROOT_DIR/support-thanks.html"
LOCAL_FEEDBACK_FILE="$ROOT_DIR/feedback.php"
LOCAL_FEEDBACK_ADMIN_FILE="$ROOT_DIR/feedback_admin.php"

SSH_KEY="${CUVIU_SSH_KEY:-$HOME/.ssh/xserver_cuviu}"
SSH_HOST="${CUVIU_SSH_HOST:-sv16692.xserver.jp}"
SSH_USER="${CUVIU_SSH_USER:-cuviu001}"
SSH_PORT="${CUVIU_SSH_PORT:-10022}"
REMOTE_DIR="${CUVIU_REMOTE_DIR:-/home/cuviu001/cuviu.jp/public_html/apps/scorebook}"
REMOTE_FILE="$REMOTE_DIR/index.html"
REMOTE_TMP="$REMOTE_DIR/index.html.tmp"
REMOTE_INTRO_FILE="$REMOTE_DIR/intro.html"
REMOTE_INTRO_TMP="$REMOTE_DIR/intro.html.tmp"
REMOTE_SUPPORT_THANKS_FILE="$REMOTE_DIR/support-thanks.html"
REMOTE_SUPPORT_THANKS_TMP="$REMOTE_DIR/support-thanks.html.tmp"
REMOTE_FEEDBACK_FILE="$REMOTE_DIR/feedback.php"
REMOTE_FEEDBACK_TMP="$REMOTE_DIR/feedback.php.tmp"
REMOTE_FEEDBACK_ADMIN_FILE="$REMOTE_DIR/feedback_admin.php"
REMOTE_FEEDBACK_ADMIN_TMP="$REMOTE_DIR/feedback_admin.php.tmp"
REMOTE_FEEDBACK_STORE_DIR="${CUVIU_FEEDBACK_STORE_DIR:-/home/cuviu001/cuviu.jp/scorebook-feedback}"

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

ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mkdir -p '$REMOTE_DIR' '$REMOTE_FEEDBACK_STORE_DIR/attachments' '$REMOTE_FEEDBACK_STORE_DIR/by-category' && chmod 700 '$REMOTE_FEEDBACK_STORE_DIR' '$REMOTE_FEEDBACK_STORE_DIR/attachments' '$REMOTE_FEEDBACK_STORE_DIR/by-category' && if [ ! -s '$REMOTE_FEEDBACK_STORE_DIR/admin-token.txt' ]; then umask 077; php -r 'echo bin2hex(random_bytes(24)), PHP_EOL;' > '$REMOTE_FEEDBACK_STORE_DIR/admin-token.txt'; fi && chmod 600 '$REMOTE_FEEDBACK_STORE_DIR/admin-token.txt'"
scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_TMP"
ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_TMP' '$REMOTE_FILE'"

if [[ -f "$LOCAL_INTRO_FILE" ]]; then
  echo "Deploying $LOCAL_INTRO_FILE"
  echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_INTRO_FILE"
  scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_INTRO_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_INTRO_TMP"
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_INTRO_TMP' '$REMOTE_INTRO_FILE' && chmod 644 '$REMOTE_INTRO_FILE'"
fi

if [[ -f "$LOCAL_SUPPORT_THANKS_FILE" ]]; then
  echo "Deploying $LOCAL_SUPPORT_THANKS_FILE"
  echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_SUPPORT_THANKS_FILE"
  scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_SUPPORT_THANKS_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_SUPPORT_THANKS_TMP"
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_SUPPORT_THANKS_TMP' '$REMOTE_SUPPORT_THANKS_FILE' && chmod 644 '$REMOTE_SUPPORT_THANKS_FILE'"
fi

if [[ -f "$LOCAL_FEEDBACK_FILE" ]]; then
  echo "Deploying $LOCAL_FEEDBACK_FILE"
  echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_FILE"
  scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_FEEDBACK_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_TMP"
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_FEEDBACK_TMP' '$REMOTE_FEEDBACK_FILE' && chmod 644 '$REMOTE_FEEDBACK_FILE'"
fi

if [[ -f "$LOCAL_FEEDBACK_ADMIN_FILE" ]]; then
  echo "Deploying $LOCAL_FEEDBACK_ADMIN_FILE"
  echo "  -> $SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_ADMIN_FILE"
  scp -i "$SSH_KEY" -P "$SSH_PORT" "$LOCAL_FEEDBACK_ADMIN_FILE" "$SSH_USER@$SSH_HOST:$REMOTE_FEEDBACK_ADMIN_TMP"
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "mv '$REMOTE_FEEDBACK_ADMIN_TMP' '$REMOTE_FEEDBACK_ADMIN_FILE' && chmod 644 '$REMOTE_FEEDBACK_ADMIN_FILE'"
fi

echo "Deploy complete:"
echo "https://cuviu.jp/apps/scorebook/"
