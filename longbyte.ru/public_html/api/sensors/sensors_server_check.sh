read PID<sensors_server.pid
if (( $PID > 0 )); then
    echo 'pid found...';
    if ! [ -d /proc/$PID/ ]; then
        echo 'restarting...'
        cd ~/web/longbyte.ru/public_html/api/sensors
        ./sensors_server_start.sh
    fi
fi
