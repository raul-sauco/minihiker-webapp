#!/bin/bash

# Store the destination in a variable
REMOTE="raul@minihiker.com"
DEST=raul@minihiker.com:app

# Clean the dist directory
rm static/css/*

# Compile css from scss
if ! command -v sass &> /dev/null
then
    echo "<sass> could not be found, installing"
    npm install -g sass
fi
sass backend/sass/:static/css/

# Minimize CSS
if ! command -v cleancss &> /dev/null
then
    echo "<cleancss> could not be found, installing"
    npm install clean-css-cli -g
fi
cleancss --batch --batch-suffix '.min' static/css/*.css

# Minimize JS
if ! command -v uglifyjs &> /dev/null
then
    echo "<uglifyjs> could not be found, installing"
    npm install uglify-js -g
fi
rm static/js/*.min.js;
for f in static/js/*.js; do
  short=${f%.js};
  uglifyjs "$f" > "$short".min.js;
done

# Login and backup the database
echo
echo "*** Backing up remote databases in host $REMOTE ***"
echo
ssh -T $REMOTE <<'EOL'
	now="$(date)"
	name="$HOSTNAME"
	up="$(uptime)"
	echo "Server name is $name"
	echo "Server date and time is $now"
	echo "Server uptime: $up"
	cd scripts
	./mysql_backup.sh
EOL

# Backup server database before update and migrations
rsync -avzh $REMOTE:backups/db data/

# Publish new work
rsync -avzh --exclude-from './excludes.txt' . $DEST

echo "Local system name: $HOSTNAME"
echo "Local date and time: $(date)"

# Install new dependencies and apply new migrations
echo
echo "*** Installing dependencies and applying migrations in host $REMOTE ***"
echo
ssh -T $REMOTE <<'EOL'
	now="$(date)"
	name="$HOSTNAME"
	up="$(uptime)"
	echo "Server name is $name"
	echo "Server date and time is $now"
	echo "Server uptime: $up"
	cd app
	php composer.phar install
	./yii migrate --interactive=0
	echo "Server is up to date"
EOL
