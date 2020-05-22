<?php

set_time_limit(0);
error_reporting(E_ALL);

$token = '46327846328746873264732';
$sendEverySecond = 1;
$reconnectTimeOut = 5;
$localSensorsServer = 'http://localhost:55555';
$remoteSensorsSocket = 'longbyte.ru';
$remotePort = 56999;



while (true) {

    $remoteSensorsSocketIP = gethostbyname($remoteSensorsSocket);

    $obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if ($obSocket === false) {
        echo "Не удалось выполнить socket_create(): причина: " . socket_last_error($obSocket) . "\n";
        die;
    }

    echo "Пытаемся соединиться с '$remoteSensorsSocketIP' на порту '$remotePort'...";
    $result = socket_connect($obSocket, $remoteSensorsSocketIP, $remotePort);
    if ($result === false) {
        echo "Не удалось выполнить socket_connect().\nПричина: ($result) " . socket_last_error($obSocket) . "\n";
        echo "Переподключение через {$reconnectTimeOut} сек.\n";
        sleep($reconnectTimeOut);
        continue;
    } else {
        echo "OK.\n";
    }

    while (true) {
        $jsonSensors = file_get_contents($localSensorsServer);
        $arSensors = json_decode($jsonSensors, true);
        $arData = array(
            'token' => $token,
            'data' => $arSensors,
        );
        $jsonData = json_encode($arData);
        $dataLendth = strlen($jsonData);
        socket_write($obSocket, $jsonData, $dataLendth);
        echo "Send {$dataLendth} bytes.\n";
        $rawMessage = socket_read($obSocket, 128 * 1024);
        $dataLendth = strlen($rawMessage);
        $rawMessage = trim($rawMessage);
        if (strlen($rawMessage) > 0) {
            echo "Responce: [{$dataLendth}] {$rawMessage}.\n";
        }
        if (socket_last_error($obSocket)) {
            echo "Ошибка сокета.\nПричина: " . socket_last_error($obSocket) . "\n";
            socket_close($obSocket);
            break;
        }
        sleep($sendEverySecond);
    }
}

echo "Закрываем сокет...";
socket_close($obSocket);
echo "OK.\n\n";

