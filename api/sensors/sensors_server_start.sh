cd ~/www/api/sensors
read PID<sensors_server.pid
if (( $PID > 0 )); then
    echo 'Pid found.';
    if ! [ -d /proc/$PID/ ]; then
        echo 'Restarting...'
        php sensors_server.php $1 &
        rm -f sensors_server.pid
        echo $! >>sensors_server.pid
        echo $!
    else
        echo 'Server already running.'
    fi
else
    echo 'Starting...'
   php sensors_server.php $1 &
    rm -f sensors_server.pid
    echo $! >>sensors_server.pid
    echo $!
fi
