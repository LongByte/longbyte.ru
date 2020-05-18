<?php

$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

$serverAddress = '127.0.0.1';
$serverPort = 10000;
$maxClients = 16;
$NULL = null;
$arClientSockets = array();

$arControllers = array();

if (($obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($obSocket, $serverAddress, $serverPort) === false) {
    echo "Не удалось выполнить socket_bind(): причина: " . socket_strerror(socket_last_error($obSocket)) . "\n";
}

if (socket_listen($obSocket, 5) === false) {
    echo "Не удалось выполнить socket_listen(): причина: " . socket_strerror(socket_last_error($obSocket)) . "\n";
}

$arSockets = array($obSocket);

do {
    $iNumChanged = socket_select($arSockets, $NULL, $NULL, 0, 10);
    /* Изменилось что-нибудь? */
    if ($iNumChanged) {
        /* Изменился ли главный сокет (новое подключение) */
        if (in_array($obSocket, $arSockets)) {
            if (count($arClientSockets) < $maxClients) {
                $arClientSockets[] = socket_accept($obSocket);
                echo "Принято подключение (" . count($arClientSockets) . " of $maxClients clients)\n";
            }
        }
        /* Цикл по всем клиентам с проверкой изменений в каждом из них */
        foreach ($arClientSockets as $key => $obClient) {
            /* Новые данные в клиентском сокете? Прочитать и ответить */
            if (in_array($obClient, $arSockets)) {
                $rawMessage = socket_read($obClient, 128 * 1024);

                if ($rawMessage === false) {
                    socket_shutdown($obClient);
                    unset($arClientSockets[$key]);
                } else {
                    $rawMessage = trim($rawMessage);

                    $arData = json_decode($rawMessage, true);
                    $strToken = $arData['token'];
                    $arSensors = $arData['data'];
                    $jsonSensors = json_encode($arSensors);

                    if (!array_key_exists($strToken, $arControllers)) {
                        $arControllers[$strToken] = new \Api\Controller\Sensors\Post($strToken);
                    }
                    $obPost = $arControllers[$strToken];

                    $obPost->setPostData($jsonSensors);
                    $response = $obPost->post();
                }
            }
        }
    }

    $arSockets = $arClientSockets;
    $arSockets[] = $obSocket;
} while (true);

