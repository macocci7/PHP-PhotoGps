#!/usr/bin/bash

# Script to check
# if the version in composer.json
# is not in git tags

if [ ! -r composer.json ]; then
    echo "composer.json not found."
    echo "operation aborted."
    exit 1
fi
VERSION=$(cat composer.json | grep version | head -1 | grep -Po '\d+\.\d+\.\d+')
echo "version in composer.json: $VERSION"
for tag in `git tag`
do
    if [ $tag = $VERSION ]; then
        # echo "version $VERSION already exists in git tags."
        printf '\033[41mversion %s already exists in git tags.\033[m\n' $VERSION
        exit 1
    fi
done
#echo "[OK.]"
printf '\033[1;102m%s\033[m\n' ' [OK!] '
