<?php

$_SERVER["DOCUMENT_ROOT"] = dirname(dirname(__DIR__));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $logEnable;
global $shutdown;
global $obLog;
$logEnable = false;
$shutdown = false;
if (in_array('log', $argv)) {
    $logEnable = true;
}

if (function_exists('pcntl_async_signals')) {
    pcntl_async_signals(true);
    pcntl_signal(SIGTERM, "SIGTERM_handler");
    pcntl_signal(SIGUSR1, "SIGTERM_handler");
}

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
set_time_limit(0);
ob_implicit_flush();

$obLog = new \Bitrix\Main\IO\File(\Bitrix\Main\Application::getDocumentRoot() . '/upload/socket.log');
SocketLog($obLog, 'Start server.');

$serverAddress = '194.226.61.201';
//$serverAddress = '127.0.0.1';
$serverPort = 56999;
$maxClients = 16;
$arClientSockets = array();
$arBuffer = array();

$arControllers = array();

if (($obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    SocketLog($obLog, "Не удалось выполнить socket_create(): причина: " . socket_strerror(socket_last_error()));
}

if (socket_bind($obSocket, $serverAddress, $serverPort) === false) {
    SocketLog($obLog, "Не удалось выполнить socket_bind(): причина: " . socket_strerror(socket_last_error($obSocket)));
}

if (socket_listen($obSocket, 5) === false) {
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
                SocketLog($obLog, "Принято подключение (" . count($arClientSockets) . " of $maxClients clients)");
            }
        }
        /* Цикл по всем клиентам с проверкой изменений в каждом из них */
        foreach ($arClientSockets as $key => $obClient) {
            /* Новые данные в клиентском сокете? Прочитать и ответить */
            if (in_array($obClient, $arSockets)) {
                $rawMessage = socket_read($obClient, 128 * 1024);
                SocketLog($obLog, "Read message " . strlen($rawMessage) . " bytes");

                if ($rawMessage === false || strlen($rawMessage) == 0) {
                    socket_shutdown($obClient);
                    unset($arClientSockets[$key]);
                    SocketLog($obLog, "Client disconnected.");
                    foreach ($arControllers as $obPost) {
                        if (!is_null($obPost)) {
                            $obPost->emergencySave();
                        }
                    }
                } else {
                    if (!array_key_exists($key, $arBuffer)) {
                        $arBuffer[$key] = '';
                    }
                    $arBuffer[$key] .= $rawMessage;
                    if (strpos($arBuffer[$key], '{"token"') > 0) {
                        $arBuffer[$key] = substr(
                            $arBuffer[$key],
                            strpos($arBuffer[$key], '{"token"')
                        );
                    }
                    if (strlen($arBuffer[$key]) > 128 * 1024) {
                        $arBuffer[$key] = '';
                    }
                    $arData = json_decode($arBuffer[$key], true);
                    if ($arData) {
                        $arBuffer[$key] = '';
                    } else {
                        continue;
                    }
                    $strToken = $arData['token'];
                    if (array_key_exists('command', $arData)) {
                        $strCommand = $arData['command'];
                    } else {
                        $strCommand = '';
                    }
                    $arSensors = $arData['data'];
                    $jsonSensors = json_encode($arSensors);

                    if (!array_key_exists($strToken, $arControllers)) {
                        $arControllers[$strToken] = new \Api\Controller\Sensors\Post($strToken);
                        SocketLog($obLog, "New controller. Token :{$strToken}");
                    } else {
                        SocketLog($obLog, "Old controller. Token :{$strToken}");
                    }
                    $obPost = $arControllers[$strToken];

                    switch ($strCommand) {
                        case 'shutdown785423755':
                            $jsonResponse = json_encode(array('Shutdown command received.'));
                            global $shutdown;
                            $shutdown = true;
                            break;

                        case 'getDebug':
                            $jsonResponse = $obPost->getDebug();
                            break;

                        case 'reload':
                            $arControllers[$strToken] = new \Api\Controller\Sensors\Post($strToken);
                            $obPost = $arControllers[$strToken];
                            break;

                        default :
                            $obPost->setPostData($jsonSensors);
                            $jsonResponse = $obPost->post();
                            break;
                    }

                    $dataLength = strlen($jsonResponse);
                    SocketLog($obLog, "Response: [{$dataLength}] {$jsonResponse}");
                    socket_write($obClient, $jsonResponse, $dataLength);
                }
            }
        }
    }

    $arSockets = $arClientSockets;
    $arSockets[] = $obSocket;

    if (date('H:i:s') == '06:00:00') {
        $shutdown = true;
    }
} while (!$shutdown);

foreach ($arControllers as $obPost) {
    if (!is_null($obPost)) {
        $obPost->emergencySave();
    }
}

socket_close($obSocket);
foreach ($arClientSockets as $obClient) {
    socket_close($obClient);
}

function SocketLog($obLog, $str)
{
    global $logEnable;
    if ($logEnable) {
        $obLog->putContents($str . "\n", \Bitrix\Main\IO\File::APPEND);
        echo $str . "\n";
        ob_flush();
    }
}

function SIGTERM_handler($signo)
{
    global $shutdown;
    global $obLog;

    switch ($signo) {
        case SIGTERM:
            $shutdown = true;
            SocketLog($obLog, "SIGTERM recived.");
            break;
        case SIGUSR1:
            $shutdown = true;
            SocketLog($obLog, "SIGUSR1 recived.");
            break;
        default:
            break;
    }
}
