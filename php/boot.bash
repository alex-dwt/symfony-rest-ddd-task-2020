#!/usr/bin/env bash

#sleep 20

#/app/bin/console doctrine:schema:update --force

#ini=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#
#if [ "$ENABLE_XDEBUG" = "1" ]; then
#  echo "zend_extension=xdebug.so" > ${ini}
#  echo "xdebug.remote_connect_back=0" >> ${ini}
#  echo "xdebug.remote_enable=1" >> ${ini}
#  echo "xdebug.remote_autostart=1" >> ${ini}
#  echo "xdebug.remote_host=$PHPSTORM_HOST_IP" >> ${ini}
#  export PHP_IDE_CONFIG="serverName=symfony-rest-ddd-task-2020"
#else
#  rm ${ini} 2>/dev/null
#fi

php-fpm -R
