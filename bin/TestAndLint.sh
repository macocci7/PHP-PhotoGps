#!/usr/bin/bash

# Script to Test and Lint
# requirement:
# - https://github.com/phpenv/phpenv installed
# - PHP versions defined in ../PHP_VERSIONS installed

CMD=phpenv
$CMD -v &> /dev/null
if [ $? -ne 0 ]; then
    echo "command [${CMD}] not found!"
    exit 1
fi
echo "-----------------------------------------------------------"
echo "[composer validate]"
composer validate
if [ $? -ne 0 ]; then
    echo "Operation aborted."
    exit 1
fi

test_and_lint() {
    echo "==========================================================="
    echo "[PHP $1][phpenv global $1]"
    phpenv global $1
    if [ $? -ne 0 ]; then
        echo "Failed to switch version to $1. skipped."
        return 1
    fi
    echo "-----------------------------------------------------------"
    echo "[PHP $1][php -v]"
    php -v
    echo "-----------------------------------------------------------"
    echo "[PHP $1][parallel-lint]"
    ./vendor/bin/parallel-lint src tests examples
    echo "-----------------------------------------------------------"
    echo "[PHP $1][neon-lint]"
    ./vendor/nette/neon/bin/neon-lint conf
    echo "-----------------------------------------------------------"
    echo "[PHP $1][phpcs]"
    ./vendor/bin/phpcs --ignore=vendor \
                        --standard=phpcs.xml \
                        -p \
                        -s \
                        .
    echo "-----------------------------------------------------------"
    echo "[PHP $1][phpstan]"
    ./vendor/bin/phpstan analyze -c phpstan.neon
    echo "-----------------------------------------------------------"
    echo "[PHP $1][phpunit]"
    ./vendor/bin/phpunit ./tests/
    echo "-----------------------------------------------------------"
}

echo "[[TesAndLint.sh]]"

SUPPORTED_PHP_VERSIONS=PHP_VERSIONS
if [ ! -f $SUPPORTED_PHP_VERSIONS ]; then
    echo "file [$SUPPORTED_PHP_VERSIONS] not found."
    echo "operation aborted."
    exit 1
fi
if [ ! -r $SUPPORTED_PHP_VERSIONS ]; then
    echo "cannot read file[$SUPPORTED_PHP_VERSIONS]."
    echo "operation aborted."
    exit 1
fi
STR_CMD=''
while read version ; do
    STR_CMD="$STR_CMD test_and_lint $version;"
done < $SUPPORTED_PHP_VERSIONS
eval $STR_CMD
