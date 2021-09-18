<?php

set_time_limit(0);
error_reporting(E_ALL);
ob_implicit_flush();

$token = '46327846328746873264732';
$sendEverySecond = 1;
$reconnectTimeOut = 5;
$localSensorsServer = 'http://localhost:55555';
$arRemoteSensorsSockets = array(
    'longbyte.ru',
    'svarog.longbyte.ru',
    '127.0.0.1',
);
$remotePort = 56999;


while (true) {

    $obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if ($obSocket === false) {
        echo "Не удалось выполнить socket_create(): причина: " . socket_last_error($obSocket) . "\n";
        die;
    }

    $result = false;
    foreach ($arRemoteSensorsSockets as $remoteSensorsSocket) {
        $remoteSensorsSocketIP = gethostbyname($remoteSensorsSocket);

        echo "Пытаемся соединиться с '$remoteSensorsSocketIP' на порту '$remotePort'...";
        $result = socket_connect($obSocket, $remoteSensorsSocketIP, $remotePort);

        if ($result === false) {
            echo "Не удалось выполнить socket_connect().\nПричина: " . socket_last_error($obSocket) . "\n";
        } else {
            break;
        }
    }
    if ($result === false) {
        echo "Не удалось выполнить socket_connect().\nПричина: " . socket_last_error($obSocket) . "\n";
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
        $dataLength = strlen($jsonData);
        socket_write($obSocket, $jsonData, $dataLength);
        echo "Send {$dataLength} bytes.\n";
//        $rawMessage = socket_read($obSocket, 128 * 1024);
//        $dataLength = strlen($rawMessage);
//        $rawMessage = trim($rawMessage);
//        if (strlen($rawMessage) > 0) {
//            echo "Response: [{$dataLength}] {$rawMessage}.\n";
//        }
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

