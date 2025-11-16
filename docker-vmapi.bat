@echo off

docker exec -it vacation-management-api php /var/www/html/console.php %* 2>NUL