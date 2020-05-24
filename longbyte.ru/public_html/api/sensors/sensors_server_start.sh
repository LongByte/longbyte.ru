/srv/php/php-7.3.2/bin/php sensors_server.php $1 &
rm -f sensors_server.pid
echo $! >>sensors_server.pid
echo $!