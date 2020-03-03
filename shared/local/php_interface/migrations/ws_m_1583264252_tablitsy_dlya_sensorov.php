<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1583264252_tablitsy_dlya_sensorov extends \WS\ReduceMigrations\Scenario\ScriptScenario {

    /**
     * Name of scenario
     * */
    static public function name() {
        return 'Таблицы для сенсоров';
    }

    /**
     * Priority of scenario
     * */
    static public function priority() {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    static public function hash() {
        return '49091217ec227c59f005c36f8d711e198ae6c387';
    }

    /**
     * @return int approximately time in seconds
     */
    static public function approximatelyTime() {
        return 0;
    }

    /**
     * Write action by apply scenario. Use method `setData` for save need rollback data
     * */
    public function commit() {
        // my code
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
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback() {
        // my code
    }

    private static function hlSystem($iHlBlockId) {


        $obNameProp = new \Migration\Builder\UserField('UF_NAME', 'HLBLOCK_' . $iHlBlockId);

        $obNameProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obNameProp->settings(array(
            'SIZE' => 60,
            'ROWS' => 1
        ));
        $obNameProp->label(array('ru' => 'Название', 'en' => 'Название'));
        $obNameProp->save();

        $oTokenProp = new \Migration\Builder\UserField('UF_TOKEN', 'HLBLOCK_' . $iHlBlockId);
        $oTokenProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $oTokenProp->settings(array(
            'SIZE' => 30,
            'ROWS' => 1
        ));
        $oTokenProp->label(array('ru' => 'Токен', 'en' => 'Токен'));
        $oTokenProp->save();
    }

    private static function hlData($iHlBlockId) {


        $obSystemIdProp = new \Migration\Builder\UserField('UF_SYSTEM_ID', 'HLBLOCK_' . $iHlBlockId);

        $obSystemIdProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obSystemIdProp->label(array('ru' => 'Система', 'en' => 'Система'));
        $obSystemIdProp->save();

        $obDateProp = new \Migration\Builder\UserField('UF_DATE', 'HLBLOCK_' . $iHlBlockId);
        $obDateProp->type(\Migration\Builder\UserField::TYPE_DATE);
        $obDateProp->label(array('ru' => 'Дата', 'en' => 'Дата'));
        $obDateProp->save();

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

        $obSensorValueMinProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE_MIN', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMinProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMinProp->label(array('ru' => 'Минимальное значение', 'en' => 'Минимальное значение'));
        $obSensorValueMinProp->save();

        $obSensorValueAvgProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE_AVG', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueAvgProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueAvgProp->label(array('ru' => 'Среднее значение', 'en' => 'Среднее значение'));
        $obSensorValueAvgProp->save();

        $obSensorValueMaxProp = new \Migration\Builder\UserField('UF_SENSOR_VALUE_MAX', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValueMaxProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obSensorValueMaxProp->label(array('ru' => 'Максимальное значение', 'en' => 'Максимальное значение'));
        $obSensorValueMaxProp->save();

        $obSensorValuesCountProp = new \Migration\Builder\UserField('UF_SENSOR_VALUES', 'HLBLOCK_' . $iHlBlockId);
        $obSensorValuesCountProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obSensorValuesCountProp->label(array('ru' => 'Количество значений', 'en' => 'Количество значений'));
        $obSensorValuesCountProp->save();

        $obSensorUnitProp = new \Migration\Builder\UserField('UF_SENSOR_UNIT', 'HLBLOCK_' . $iHlBlockId);
        $obSensorUnitProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obSensorUnitProp->label(array('ru' => 'Единицы измерений', 'en' => 'Единицы измерений'));
        $obSensorUnitProp->save();
    }

}
