cd ~/web/longbyte.ru/public_html/api/sensors
read PID<sensors_server.pid
echo $PID;
kill -16 $PID
rm -f sensors_server.pid