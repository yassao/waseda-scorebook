#!/usr/bin/env zsh
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
zsh "$ROOT_DIR/scripts/release_scorebook.sh" "$@"
