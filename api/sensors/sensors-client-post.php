<?php

set_time_limit(0);
error_reporting(E_ALL);
ob_implicit_flush();

$token = 'YUHGnhb8iBN49NC';
$sendEverySecond = 1;
$reconnectTimeOut = 5;
$localSensorsServer = 'http://localhost:55555';

$strRemoteServer = 'https://longbyte.ru';
$strRemoteServer = 'http://longbyte.local';

$arGetParams = array(
    'token' => $token,
);

$arEnabledSensors = getSensors($strRemoteServer, $arGetParams, $reconnectTimeOut);
if (!is_array($arEnabledSensors)) {
    echo "Не удалось получить данные сенсоров" . "\n";
    die;
}

$obLastGetSensors = new DateTime();

function getSensors($strRemoteServer, $arGetParams, $reconnectTimeOut): ?array
{
    $obCurl = curl_init($strRemoteServer . '/api/sensors/sensor/?' . http_build_query($arGetParams));
    curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($obCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($obCurl, CURLOPT_HEADER, false);
    curl_setopt($obCurl, CURLOPT_CONNECTTIMEOUT, $reconnectTimeOut);
    $rawJson = curl_exec($obCurl);
    curl_close($obCurl);

    $arEnabledSensors = json_decode($rawJson, true)['data'];
    if (is_array($arEnabledSensors)) {
        return $arEnabledSensors;
    }

    return null;
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
                if ($arEnabledSensor['active']) {
                    $arValues[] = array(
                        'id' => (int) $arEnabledSensor['id'],
                        'value' => $arSensor['SensorValue']
                    );
                }
                continue 2;
            }
        }
        $arValues[] = array(
            'id' => 0,
            'value' => $arSensor['SensorValue'],
            'SensorApp' => $arSensor['SensorApp'],
            'SensorClass' => $arSensor['SensorClass'],
            'SensorName' => $arSensor['SensorName'],
            'SensorUnit' => $arSensor['SensorUnit'],
        );
        $arEnabledSensor[] = array(
            'id' => 0,
            'active' => false,
            'sensor_app' => $arSensor['SensorApp'],
            'sensor_device' => $arSensor['SensorClass'],
            'sensor_name' => $arSensor['SensorName'],
        );
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

    if ((new DateTime())->getTimestamp() > $obLastGetSensors->getTimestamp() + 5 * 60) {
        $arTmpEnabledSensors = getSensors($strRemoteServer, $arGetParams, $reconnectTimeOut);
        if (is_array($arTmpEnabledSensors)) {
            $arEnabledSensors = $arTmpEnabledSensors;
        }
    }
}

