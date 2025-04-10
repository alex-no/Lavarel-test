#!/bin/bash

# Change directory to /var/www
cd /var/www || { echo "Failed to change directory to /var/www"; exit 1; }

# Perform git pull to update the repository
git pull || { echo "Failed to perform git pull"; exit 1; }

# Grant write permissions if www_app/storage exists
if [ -d www_app/storage ]; then
   chmod -R a+w www_app/storage
fi

# Copy all files from www_app to the current directory, overwriting existing ones
cp -rf www_app/* ./ || { echo "Failed to copy files from www_app to the current directory"; exit 1; }

# Clear the contents of www_app (keeping the directory itself empty)
rm -rf www_app/* || { echo "Failed to clear the contents of www_app"; exit 1; }

# Print success message
echo "Operations completed successfully."