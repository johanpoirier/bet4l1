#!/bin/sh

projectDir="$(dirname $0)/../"

target=${@: -1}
targetPath=~/$target

if [ -e $targetPath ]
then
    version=`date '+%Y%m%d%H%M%S'`
    mkdir -p "$targetPath/versions/$version/web/"

    cd "$projectDir"
    cp -r app/* "$targetPath/versions/$version/web/"

    mkdir "$targetPath/versions/$version/logs"
    chmod 775 "$targetPath/versions/$version/logs"

    rm -f "$targetPath/versions/$version/web/lib/config.inc.php"
    ln -s "$targetPath/conf/config.inc.php" "$targetPath/versions/$version/web/lib/config.inc.php"

    rm -f "$targetPath/versions/$version/web/lib/protect/params.inc"
    ln -s "$targetPath/conf/params.inc" "$targetPath/versions/$version/web/lib/protect/params.inc"

    chgrp -R www-data "$targetPath/versions/$version/"
    cd --

    rm -f "$targetPath/current"
    ln -s "$targetPath/versions/$version/" "$targetPath/current"

    echo "Version $version created"
else
    echo "$target does not exist"
fi
