<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1591451819_sensors_smena_struktury_bd extends \WS\ReduceMigrations\Scenario\ScriptScenario {

    /**
     * Name of scenario
     * */
    static public function name() {
        return "Sensors. Смена структуры БД.";
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
        return "df75b43fb41447aa852b0d7bbfd8bdcabba35105";
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

        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSystem')) {
                self::hlSystem($arExistHlIblock['ID']);
            }

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                self::hlSensors($arExistHlIblock['ID']);
            }

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsData')) {
                self::hlData($arExistHlIblock['ID']);
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
        $obProp = new \Migration\Builder\UserField('UF_MODE', 'HLBLOCK_' . $iHlBlockId);
        if ($obProp->getId() > 0) {
            $obProp->delete();
        }

        $obProp = new \Migration\Builder\UserField('UF_LAST_RECEIVE', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_DATETIME);
        $obProp->label(array('ru' => 'Последнее получение данных', 'en' => 'Последнее получение данных'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_USER_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Пользователь', 'en' => 'Пользователь'));
        $obProp->save();
    }

    private static function hlSensors($iHlBlockId) {
        $obProp = new \Migration\Builder\UserField('UF_OFF_ALERT', 'HLBLOCK_' . $iHlBlockId);
        if ($obProp->getId() > 0) {
            $obProp->delete();
        }

        $obProp = new \Migration\Builder\UserField('UF_ALERT_ENABLE', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_BOOLEAN);
        $obProp->label(array('ru' => 'Включить оповещение', 'en' => 'Включить оповещение'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_ALERT_MUTE_TILL', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_DATETIME);
        $obProp->label(array('ru' => 'Отключить уведомления до', 'en' => 'Отключить уведомления до'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_IGNORE_LESS', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obProp->label(array('ru' => 'Игнорировать значения меньше', 'en' => 'Игнорировать значения меньше'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_IGNORE_MORE', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obProp->label(array('ru' => 'Игнорировать значения больше', 'en' => 'Игнорировать значения больше'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_LOG_MODE', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Режим логирования', 'en' => 'Режим логирования'));
        $obProp->required(true);
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_MODIFIER', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obProp->label(array('ru' => 'Формула для модификации значения', 'en' => 'Формула для модификации значения'));
        $obProp->save();
    }

    private static function hlData($iHlBlockId) {

        $obProp = new \Migration\Builder\UserField('UF_VALUE', 'HLBLOCK_' . $iHlBlockId);
        if ($obProp->getId() <= 0) {

            \Bitrix\Main\Application::getConnection()->query('ALTER TABLE `sensors_data` 
CHANGE COLUMN `UF_SENSOR_VALUE_MIN` `UF_VALUE_MIN` DOUBLE NULL DEFAULT NULL ,
CHANGE COLUMN `UF_SENSOR_VALUE` `UF_VALUE_AVG` DOUBLE NULL DEFAULT NULL ,
CHANGE COLUMN `UF_SENSOR_VALUE_MAX` `UF_VALUE_MAX` DOUBLE NULL DEFAULT NULL ,
CHANGE COLUMN `UF_SENSOR_VALUES` `UF_VALUES_COUNT` INT(18) NULL DEFAULT NULL ;
');

            \Bitrix\Main\Application::getConnection()->query('UPDATE b_user_field SET FIELD_NAME = \'UF_VALUE_AVG\' WHERE (FIELD_NAME = \'UF_SENSOR_VALUE\');');
            \Bitrix\Main\Application::getConnection()->query('UPDATE b_user_field SET FIELD_NAME = \'UF_VALUE_MAX\' WHERE (FIELD_NAME = \'UF_SENSOR_VALUE_MAX\');');
            \Bitrix\Main\Application::getConnection()->query('UPDATE b_user_field SET FIELD_NAME = \'UF_VALUE_MIN\' WHERE (FIELD_NAME = \'UF_SENSOR_VALUE_MIN\');');
            \Bitrix\Main\Application::getConnection()->query('UPDATE b_user_field SET FIELD_NAME = \'UF_VALUES_COUNT\' WHERE (FIELD_NAME = \'UF_SENSOR_VALUES\');');

            $obProp->type(\Migration\Builder\UserField::TYPE_DOUBLE);
            $obProp->label(array('ru' => 'Текущее/последнее значение', 'en' => 'Текущее/последнее значение'));
            $obProp->save();
        }
    }

}
