read PID<sensors_server.pid
if (( $PID > 0 )); then
    echo 'pid found...';
    if ! [ -d /proc/$PID/ ]; then
        echo 'restarting...'
        ./sensors_server_start.sh
    fi
fi
