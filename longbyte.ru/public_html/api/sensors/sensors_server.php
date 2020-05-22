<?php

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

$obLog = new \Bitrix\Main\IO\File(\Bitrix\Main\Application::getDocumentRoot() . '/upload/socket.log');
SocketLog($obLog, 'Start server.');

$serverAddress = '194.226.61.252';
$serverPort = 56999;
$maxClients = 16;
$arClientSockets = array();
$arBuffer = array();

$arControllers = array();

if (($obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()) . "\n";
    SocketLog($obLog, "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()));
}

if (socket_bind($obSocket, $serverAddress, $serverPort) === false) {
    echo "Не удалось выполнить socket_bind(): причина: " . socket_strerror(socket_last_error($obSocket)) . "\n";
    SocketLog($obLog, "Не удалось выполнить socket_bind(): причина: " . socket_strerror(socket_last_error($obSocket)));
}

if (socket_listen($obSocket, 5) === false) {
    echo "Не удалось выполнить socket_listen(): причина: " . socket_strerror(socket_last_error($obSocket)) . "\n";
    SocketLog($obLog, "Не удалось выполнить socket_listen(): причина: " . socket_strerror(socket_last_error($obSocket)));
}

$arSockets = array($obSocket);

$_write = null;
$_except = null;
do {
    $iNumChanged = socket_select($arSockets, $_write, $_except, 1, 10);
    /* Изменилось что-нибудь? */
    if ($iNumChanged) {
        /* Изменился ли главный сокет (новое подключение) */
        if (in_array($obSocket, $arSockets)) {
            if (count($arClientSockets) < $maxClients) {
                $arClientSockets[] = socket_accept($obSocket);
                echo "Принято подключение (" . count($arClientSockets) . " of $maxClients clients)\n";
                SocketLog($obLog, "Принято подключение (" . count($arClientSockets) . " of $maxClients clients)");
            }
        }
        /* Цикл по всем клиентам с проверкой изменений в каждом из них */
        foreach ($arClientSockets as $key => $obClient) {
            /* Новые данные в клиентском сокете? Прочитать и ответить */
            if (in_array($obClient, $arSockets)) {
                $rawMessage = socket_read($obClient, 128 * 1024);
                SocketLog($obLog, "Read mesage " . strlen($rawMessage) . " bytes");

                if ($rawMessage === false) {
                    socket_shutdown($obClient);
                    unset($arClientSockets[$key]);
                    SocketLog($obLog, "Client disconnected.");
                } else {
                    $rawMessage = trim($rawMessage);
                    $arBuffer[$key] .= $rawMessage;
                    $arData = json_decode($arBuffer[$key], true);
                    if ($arData) {
                        $arBuffer[$key] = '';
                    } else {
                        continue;
                    }
                    $strToken = $arData['token'];
                    $arSensors = $arData['data'];
                    $jsonSensors = json_encode($arSensors);

                    if (!array_key_exists($strToken, $arControllers)) {
                        $arControllers[$strToken] = new \Api\Controller\Sensors\Post($strToken);
                        SocketLog($obLog, "New controller. Token :{$strToken}");
                    } else {
                        SocketLog($obLog, "Old controller. Token :{$strToken}");
                    }
                    $obPost = $arControllers[$strToken];

                    $obPost->setPostData($jsonSensors);
                    $jsonResponse = $obPost->post();
                    $dataLendth = strlen($jsonResponse);
                    SocketLog($obLog, "Responce: [{$dataLendth}] {$jsonResponse}");
                    socket_write($obClient, $jsonResponse, $dataLendth);
                }
            }
        }
    }

    $arSockets = $arClientSockets;
    $arSockets[] = $obSocket;
} while (true);

function SocketLog($obLog, $str) {
//    $obLog->putContents($str . "\n", \Bitrix\Main\IO\File::APPEND);
}
