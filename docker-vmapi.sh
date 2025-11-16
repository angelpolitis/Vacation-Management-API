#!/bin/bash

docker exec -it vacation-management-api php /var/www/html/src/console.php "$@" 2>NUL