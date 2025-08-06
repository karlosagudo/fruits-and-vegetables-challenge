#!/bin/bash 

PHP_INI=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

if grep ";zend_extension=xdebug.so" $PHP_INI; then
    echo "Activating Xdebug..."
    sed -i 's/;zend_extension=xdebug.so/zend_extension=xdebug.so/' $PHP_INI
    XDEBUG_ENABLED="true"
else
    echo "Deactivating Xdebug..."
    sed -i 's/zend_extension=xdebug.so/;zend_extension=xdebug.so/' $PHP_INI
    XDEBUG_ENABLED="false"
fi

export XDEBUG_ENABLED
