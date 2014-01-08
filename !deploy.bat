@REM 1.Clear cache production.
@REM 2.ZIP folders, excluding some files
@REM 3.Upload archive via ftp


@ECHO OFF
@REM call #deploy/clear-cache-prod.bat
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump web

ECHO Packing file to ZIP archive...
@REM call #deploy/pack.bat
7z a -r -mx=1 -x!web/.htaccess  -x!app/config/parameters.yml -x!app/config/security.yml -x!app/logs/* -x!.git* -x!app/cache/* -x!vendor offers-prod.zip app src web > NUL

ECHO Uploading ZIP to Hosting...
php #deploy/upload.php

ECHO DONE.
PAUSE