#!/bin/bash

NAME="quick-redirect-manager.zip"

if [ "$1" = "zip" ]; then
    if [ -f "$NAME" ]; then
        echo "[INFO] Removing old $NAME file..."
        rm "$NAME"
        echo "[SUCCESS] Removed old $NAME file."
    fi

    echo "[INFO] Install composer..."
    composer install

    echo "[INFO] Zipping the current directory..."
    zip -r "$NAME" .
    echo "[SUCCESS] Zipped the current directory and created $NAME"
fi
