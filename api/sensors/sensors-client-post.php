<?php

set_time_limit(0);
error_reporting(E_ALL);
ob_implicit_flush();

$token = 'YUHGnhb8iBN49NC';
$sendEverySecond = 1;
$reconnectTimeOut = 5;
$localSensorsServer = 'http://localhost:55555';

$strRemoteServer = 'http://longbyte.local';

$arGetParams = array(
    'token' => $token,
);

$obCurl = curl_init($strRemoteServer . '/api/sensors/sensor/?' . http_build_query($arGetParams));
curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($obCurl, CURLOPT_HEADER, false);
curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, $reconnectTimeOut);
$rawJson = curl_exec($obCurl);
curl_close($obCurl);

$arEnabledSensors = json_decode($rawJson, true)['data'];
if (!is_array($arEnabledSensors)) {
    echo "Не удалось получить данные сенсоров" . "\n";
    die;
}


while (true) {
    $jsonSensors = file_get_contents($localSensorsServer);
    $arSensors = json_decode($jsonSensors, true);

    $arValues = array();
    foreach ($arSensors as $arSensor) {
        foreach ($arEnabledSensors as $arEnabledSensor) {
            if (
                $arEnabledSensor['sensor_app'] == $arSensor['SensorApp'] &&
                $arEnabledSensor['sensor_device'] == $arSensor['SensorClass'] &&
                $arEnabledSensor['sensor_name'] == $arSensor['SensorName']
            ) {
                $arValues[] = array(
                    'id' => (int) $arEnabledSensor['id'],
                    'value' => $arSensor['SensorValue']
                );
                break;
            }
        }
    }

    $obCurl = curl_init($strRemoteServer . '/api/sensors/post/?' . http_build_query($arGetParams));
    curl_setopt($obCurl, CURLOPT_POST, 1);
    curl_setopt($obCurl, CURLOPT_POSTFIELDS, json_encode($arValues));
    curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($obCurl, CURLOPT_HEADER, false);
    curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, $reconnectTimeOut);
    echo curl_exec($obCurl) . "\n";
    $arInfo = curl_getinfo($obCurl);
    echo 'Send: ' . $arInfo['upload_content_length'] . ' | Receive: ' . $arInfo['download_content_length'] . "\n";
    curl_close($obCurl);

    sleep($sendEverySecond);
}

