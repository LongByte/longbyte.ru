<?php

use Bitrix\Main\Mail\Internal\EventTypeTable;
use Bitrix\Main\Mail\Internal\EventMessageTable;
use Bitrix\Main\Mail\Internal\EventMessageSiteTable;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1583349643_tablitsy_dlya_sensorov extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Таблицы для сенсоров";
    }

    /**
     * Priority of scenario
     * */
    static public function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    static public function hash()
    {
        return "35de6cacb2a37dc2e389f05ab68fb37644d943f0";
    }

    /**
     * @return int approximately time in seconds
     */
    static public function approximatelyTime()
    {
        return 0;
    }

    /**
     * Write action by apply scenario. Use method `setData` for save need rollback data
     * */
    public function commit()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSystem')) {
                self::hlSystem($arExistHlIblock['ID']);
            } else {
                $block = $obHlBuilder->addHLBlock('SensorsSystem', 'sensors_system', function ($block) {

                });

                if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSystem')) {
                    self::hlSystem($arExistHlIblock['ID']);
                }
            }

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                self::hlSensors($arExistHlIblock['ID']);
            } else {
                $block = $obHlBuilder->addHLBlock('SensorsSensors', 'sensors_sensors', function ($block) {

                });

                if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                    self::hlSensors($arExistHlIblock['ID']);
                }
            }

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsData')) {
                self::hlData($arExistHlIblock['ID']);
            } else {
                $block = $obHlBuilder->addHLBlock('SensorsData', 'sensors_data', function ($block) {

                });

                if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsData')) {
                    self::hlData($arExistHlIblock['ID']);
                }
            }
        }

        $eventName = 'SENSORS_ALERT';
        $arHasEvent = EventTypeTable::getList(array(
            'filter' => array('EVENT_NAME' => $eventName),
            'select' => array('ID'),
            'limit' => 1,
        ))->fetch();

        if (!$arHasEvent) {
            $rsResult = EventTypeTable::add(array(
                'LID' => 'ru',
                'EVENT_NAME' => $eventName,
                'NAME' => 'Оповещение системы контроля сенсоров',
                'DESCRIPTION' => '',
                'SORT' => 100,
            ));
            $arHasEvent = $rsResult->isSuccess();
        }

        if ($arHasEvent) {
            $arExistMessage = EventMessageTable::getList(array(
                'filter' => array(
                    'EVENT_NAME' => $eventName,
                ),
            ))->fetch();

            if (!$arExistMessage) {

                $rsResult = EventMessageTable::add(array(
                    'EVENT_NAME' => $eventName,
                    'LID' => 's1',
                    'ACTIVE' => 'Y',
                    'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
                    'EMAIL_TO' => '#EMAIL_TO#',
                    'SUBJECT' => '#SUBJECT#',
                    'MESSAGE' => "#MESSAGE#",
                    'BODY_TYPE' => 'html',
                    'LANGUAGE_ID' => 'ru',
                ));

                if ($rsResult->isSuccess()) {
                    $messageId = $rsResult->getId();

                    EventMessageSiteTable::add(array(
                        'EVENT_MESSAGE_ID' => $messageId,
                        'SITE_ID' => 's1',
                    ));
                }
            }
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback()
    {
        // my code
    }

    private static function hlSystem($iHlBlockId)
    {

        $obActiveProp = new \Migration\Builder\UserField('UF_ACTIVE', 'HLBLOCK_' . $iHlBlockId);
        $obActiveProp->type(\Migration\Builder\UserField::TYPE_BOOLEAN);
        $obActiveProp->label(array('ru' => 'Активность', 'en' => 'Активность'));
        $obActiveProp->save();

        $obNameProp = new \Migration\Builder\UserField('UF_NAME', 'HLBLOCK_' . $iHlBlockId);
        $obNameProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obNameProp->settings(array(
            'SIZE' => 60,
            'ROWS' => 1,
        ));
        $obNameProp->label(array('ru' => 'Название', 'en' => 'Название'));
        $obNameProp->required(true);
        $obNameProp->save();

        $obTokenProp = new \Migration\Builder\UserField('UF_TOKEN', 'HLBLOCK_' . $iHlBlockId);
        $obTokenProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obTokenProp->settings(array(
            'SIZE' => 30,
            'ROWS' => 1,
        ));
        $obTokenProp->label(array('ru' => 'Токен', 'en' => 'Токен'));
        $obTokenProp->required(true);
        $obTokenProp->save();

        $obEmailProp = new \Migration\Builder\UserField('UF_EMAIL', 'HLBLOCK_' . $iHlBlockId);
        $obEmailProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obEmailProp->settings(array(
            'SIZE' => 30,
            'ROWS' => 1,
        ));
        $obEmailProp->label(array('ru' => 'E-mail для уведомлений', 'en' => 'E-mail для уведомлений'));
        $obEmailProp->save();

        $obModeIdProp = new \Migration\Builder\UserField('UF_MODE', 'HLBLOCK_' . $iHlBlockId);
        $obModeIdProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obModeIdProp->label(array('ru' => 'Режим', 'en' => 'Режим'));
        $obModeIdProp->required(true);
        $obModeIdProp->save();
    }

    private static function hlSensors($iHlBlockId)
    {

        $obActiveProp = new \Migration\Builder\UserField('UF_ACTIVE', 'HLBLOCK_' . $iHlBlockId);
        $obActiveProp->type(\Migration\Builder\UserField::TYPE_BOOLEAN);
        $obActiveProp->label(array('ru' => 'Активность', 'en' => 'Активность'));
        $obActiveProp->save();

        $obSystemIdProp = new \Migration\Builder\UserField('UF_SYSTEM_ID', 'HLBLOCK_' . $iHlBlockId);
        $obSystemIdProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obSystemIdProp->label(array('ru' => 'Система', 'en' => 'Система'));
        $obSystemIdProp->save();

        $obSensorAppProp = new \Migration\Builder\UserField('UF_SENSOR_APP', 'HLBLOCK_' . $iHlBlockId);
        $obSensorAppProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obSensorAppProp->label(array('ru' => 'Приложение', 'en' => 'Приложение'));
        $obSensorAppProp->save();

        $obSensorDeviceProp = new \Migration\Builder\UserField('UF_SENSOR_DEVICE', 'HLBLOCK_' . $iHlBlockId);
        $obSensorDeviceProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obSensorDeviceProp->label(array('ru' => 'Устройство', 'en' => 'Устройство'));
        $obSensorDeviceProp->save();

        $obSensorNameProp = new \Migration\Builder\UserField('UF_SENSOR_NAME', 'HLBLOCK_' . $iHlBlockId);
        $obSensorNameProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obSensorNameProp->label(array('ru' => 'Сенсор', 'en' => 'Сенсор'));
        $obSensorNameProp->save();

        $obSensorUnitProp = new \Migration\Builder\UserField('UF_SENSOR_UNIT', 'HLBLOCK_' . $iHlBlockId);
        $obSensorUnitProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obSensorUnitProp->label(array('ru' => 'Единицы измерений', 'en' => 'Единицы измерений'));
        $obSensorUnitProp->save();

        $obSensorValueMinProp = new \Migration\Builder\UserField('UF_ALERT_VALUE_MIN', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMinProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMinProp->label(array('ru' => 'Минимальное допустимое значение', 'en' => 'Минимальное допустимое значение'));
        $obSensorValueMinProp->settings(array('PRECISION' => 4));
        $obSensorValueMinProp->save();

        $obSensorValueMaxProp = new \Migration\Builder\UserField('UF_ALERT_VALUE_MAX', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMaxProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMaxProp->label(array('ru' => 'Максимальное допустимое значение', 'en' => 'Максимальное допустимое значение'));
        $obSensorValueMaxProp->settings(array('PRECISION' => 4));
        $obSensorValueMaxProp->save();
    }

    private static function hlData($iHlBlockId)
    {

        $obSensorIdProp = new \Migration\Builder\UserField('UF_SENSOR_ID', 'HLBLOCK_' . $iHlBlockId);
        $obSensorIdProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obSensorIdProp->label(array('ru' => 'Сенсор', 'en' => 'Сенсор'));
        $obSensorIdProp->save();

        $obDateProp = new \Migration\Builder\UserField('UF_DATE', 'HLBLOCK_' . $iHlBlockId);
        $obDateProp->type(\Migration\Builder\UserField::TYPE_DATETIME);
        $obDateProp->label(array('ru' => 'Дата', 'en' => 'Дата'));
        $obDateProp->save();

        $obSensorValueMinProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE_MIN', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMinProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMinProp->label(array('ru' => 'Минимальное значение', 'en' => 'Минимальное значение'));
        $obSensorValueMinProp->settings(array('PRECISION' => 4));
        $obSensorValueMinProp->save();

        $obSensorValueAvgProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueAvgProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueAvgProp->label(array('ru' => 'Среднее значение', 'en' => 'Среднее значение'));
        $obSensorValueAvgProp->settings(array('PRECISION' => 4));
        $obSensorValueAvgProp->save();

        $obSensorValueMaxProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE_MAX', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMaxProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMaxProp->label(array('ru' => 'Максимальное значение', 'en' => 'Максимальное значение'));
        $obSensorValueMaxProp->settings(array('PRECISION' => 4));
        $obSensorValueMaxProp->save();

        $obSensorValuesCountProp = new \Migration\Builder\UserField('UF_SENSOR_VALUES', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValuesCountProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obSensorValuesCountProp->label(array('ru' => 'Количество значений', 'en' => 'Количество значений'));
        $obSensorValuesCountProp->save();
    }

}
