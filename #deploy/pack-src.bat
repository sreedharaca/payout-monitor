7z a -r -mx=1 ^
 -x!web/.htaccess ^
 -x!app/config/parameters.yml ^
 -x!app/config/security.yml ^
 -x!app/logs/* ^
 -x!.git* ^
 -x!app/cache/* ^
 -x!vendor ^
 src.zip ^
 app ^
 src ^
 web > NUL