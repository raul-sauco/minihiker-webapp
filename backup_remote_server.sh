#!/bin/bash

# Backup Minihiker server data

# Config

user="raul"
host="minihiker.com"

# Functions

function hr(){
  printf '=%.0s' {1..80}
  printf "\n"
}

# Run
hr
printf "Backing up $user@$host\n"
hr

dir="data/db"

# Sync database backups
rsync $user@$host:backups/db/* $dir/
rsync -az $user@$host:backups/server /media/i/MV/mh/code-backups/
rsync -az $user@$host:scripts /media/i/MV/mh/

# Extract the latest copy; keep compressed file; overwrite extracted
xz -kfd $dir/latest.sql.xz && mv $dir/latest.sql data/

# Copy static image files
rsync -az $user@$host:app/static/img/* /home/i/ws/mh/app/static/img/
