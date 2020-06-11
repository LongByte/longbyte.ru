<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

echo '<pre>';
$command = $_REQUEST['command'] ?: 'getDebug';
$token = $_REQUEST['token'];

$remoteSensorsSocket = 'longbyte.ru';
$remotePort = 56999;

$remoteSensorsSocketIP = gethostbyname($remoteSensorsSocket);

$obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($obSocket === false) {
    echo "Не удалось выполнить socket_create(): причина: " . socket_last_error($obSocket) . "\n";
} else {

    echo "Пытаемся соединиться с '$remoteSensorsSocketIP' на порту '$remotePort'...";
    $result = socket_connect($obSocket, $remoteSensorsSocketIP, $remotePort);
    if ($result === false) {
        echo "Не удалось выполнить socket_connect().\nПричина: ($result) " . socket_last_error($obSocket) . "\n";
    } else {

        $arSensors = json_decode($jsonSensors, true);
        $arData = array(
            'token' => $token,
            'command' => $command,
        );
        $jsonData = json_encode($arData);
        $dataLendth = strlen($jsonData);
        socket_write($obSocket, $jsonData, $dataLendth);
        echo "Send {$dataLendth} bytes.\n";
        $rawMessage = socket_read($obSocket, 1024 * 1024);
        $dataLendth = strlen($rawMessage);
        $rawMessage = trim($rawMessage);

        if (strlen($rawMessage) > 0) {
            echo "Responce: [{$dataLendth}].\n";

            $arMessage = json_decode($rawMessage, true);
            $arMessage['post_data'] = json_decode($arMessage['post_data'], true);

            print_r($arMessage);
        }
        if (socket_last_error($obSocket)) {
            echo "Ошибка сокета.\nПричина: " . socket_last_error($obSocket) . "\n";
            socket_close($obSocket);
        }
    }
}
echo '</pre>';
