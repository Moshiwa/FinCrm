#!/bin/sh
# show datetime
dt=$(date '+%d/%m/%Y %H:%M:%S')
echo "[$dt]"

force='false'

# add alias php and composer
php=/usr/bin/php
if [ -f /opt/php81/bin/php ]; then
    php=/opt/php81/bin/php
fi

while getopts 'fp:' flag; do
  case "${flag}" in
    f) force='true' ;;
    p) php="${OPTARG}" ;;
  esac
done

# get current git branch name
branch=`git rev-parse --abbrev-ref HEAD`
echo "branch [$branch]"
prevCommit=`git rev-parse --short HEAD`

if [ $branch = "master" ] || [ $force = "true" ]; then
    # show php version
    $php -v
    # activate maintenance mode
    # php artisan down
    # update source code
    git pull
    # update PHP dependencies
    composerLock=`git --no-pager diff --name-only ${prevCommit} -- ./composer.lock`
    composerJson=`git --no-pager diff --name-only ${prevCommit} -- ./composer.json`
    composer_need_update=false

    if [ $composerLock ]; then
        if [ $composerLock = "composer.lock" ] ; then
            composer_need_update=true
        fi
    fi

    if [ $composerJson ]; then
        if [ $composerJson = "composer.json" ] ; then
            composer_need_update=true
        fi
    fi

    echo "composer_need_update: ${composer_need_update}"

    if [ "$composer_need_update" = true ] || [ $force = "true" ]; then
        $php /usr/local/bin/composer install --no-interaction --no-dev --prefer-dist
    fi
    # --no-interaction Do not ask any interactive question
    # --no-dev  Disables installation of require-dev packages.
    # --prefer-dist  Forces installation from package dist even for dev versions.
    # update database
    $php artisan migrate --force
    # --force  Required to run when in production.
    # run seeders
    $php artisan db:seed --force
    # --force  Required to run when in production.
    # clear config
    $php artisan config:clear
    # restart jobs
    $php artisan queue:restart
    # stop maintenance mode
    # php artisan up
fi
