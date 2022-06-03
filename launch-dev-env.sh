#!/bin/bash

# https://stackoverflow.com/a/48843074/2557030
#Open Docker, only if is not running
if (! docker stats --no-stream ); then
  # On Mac OS this would be the terminal command to launch Docker
  open /Applications/Docker.app
 #Wait until Docker daemon is running and has completed initialisation
while (! docker stats --no-stream ); do
  # Docker takes a few seconds to initialize
  echo "Waiting for Docker to launch..."
  sleep 1
done
docker compose up -d --remove-orphans
sleep 5
fi

# Import the legacy schema used before migrations
echo 'Importing legacy schema into development database...'
mysql --host 127.0.0.1 --port 34107 -umh_user -ppassword --database mhdb < data/latest.sql

echo 'Importing legacy schema into test database...'
mysql --host 127.0.0.1 --port 34106 -umh_test_user -ppassword --database mh_test < data/latest.sql

echo 'Updating configuration files with development defaults'
php init --env=Development --overwrite=All

echo 'Applying newest migrations to development database'
./yii migrate --interactive=0

echo 'Update RBAC rules on development database'
./yii rbac/init

echo 'Applying newest migrations to test database'
./yii_test migrate --interactive=0
echo 'The system is ready to run tests!'