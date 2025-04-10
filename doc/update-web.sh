#!/bin/bash

# Enable strict error handling
set -e

REPO_DIR="/home/alex/git-content"
TARGET_DIR="/var/www/public"
SOURCE_DIR="$REPO_DIR/www_app/public"

# Logging helper (optional)
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"
}

log "Starting public content update..."

# Pull latest changes
cd "$REPO_DIR"
git pull origin main

# Sync updated files to target directory
#rsync -av --update "$SOURCE_DIR/" "$TARGET_DIR/"
cp -rf "$SOURCE_DIR/" "$TARGET_DIR/" || { echo "Failed to copy files from www_app to the current directory"; exit 1; }

log "Public content update completed."