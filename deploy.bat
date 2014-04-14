@REM 1.Clear cache production.
@REM 2.ZIP folders, excluding some files
@REM 3.Upload archive via ftp


@ECHO OFF
@REM call #deploy/clear-cache-prod.bat
@REM php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump web

ECHO Packing file to ZIP archive...
call .deploy/pack-src.bat
call .deploy/pack-vendor.bat

ECHO Uploading ZIP to Hosting...
php -f .deploy/upload.php

ECHO DONE.
PAUSE