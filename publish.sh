#!/bin/bash

# Login and backup the database
# ssh raul@minihiker.com
# mysqldump -u root -p mh > /home/raul/backups/mh_latest.sql

# Backup existing files
# rsync -avzh raul@minihiker.com:minihiker /media/i/HDD/backups/
# rsync -avzh raul@minihiker.com:backups/* /media/i/HDD/backups/mh/

# Publish new work
DEST=raul@minihiker.com:backend

# Publish the Minihiker backend code
# from workspace ==> to minihiker.com/backend
rsync -avzh --exclude-from './excludes.txt' /home/i/ws/mh/app/ $DEST

# If file deletion is needed
# rsync -avzh --delete from to
