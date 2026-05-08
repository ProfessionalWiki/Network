#! /bin/bash

set -ex

MW_BRANCH=$1
EXTENSION_NAME=$2

git clone --depth 1 --branch "$MW_BRANCH" https://github.com/wikimedia/mediawiki.git mediawiki

cd mediawiki

composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader

php maintenance/install.php \
	--dbtype sqlite \
	--dbuser root \
	--dbname mw \
	--dbpath "$(pwd)" \
	--scriptpath="" \
	--pass AdminPassword WikiName AdminUser

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgShowDBErrorBacktrace = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php

echo 'wfLoadExtension( "'$EXTENSION_NAME'" );' >> LocalSettings.php

cat <<EOT >> composer.local.json
{
  	"require": {},
	"extra": {
		"merge-plugin": {
			"merge-dev": true,
			"include": [
				"extensions/$EXTENSION_NAME/composer.json"
			]
		}
	}
}
EOT