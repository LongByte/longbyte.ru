<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1609700951_sersonry_aliasy_gruppy extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Scenario title
     * */
    public static function name()
    {
        return 'Серсонры. Алиасы, группы.';
    }

    /**
     * Priority of scenario
     * */
    public static function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    public static function hash()
    {
        return 'b003b766c9494407f9660fdd9c62cb09a761ec00';
    }

    /**
     * @return int approximately time in seconds
     */
    public static function approximatelyTime()
    {
        return 0;
    }

    /**
     * Writes action by apply scenario. Use method `setData` to save needed rollback data.
     * For printing info into console use object from $this->printer() method.
     * */
    public function commit()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                self::hlSensors($arExistHlIblock['ID']);
            }

            \Bitrix\Highloadblock\HighloadBlockTable::add(array(
                'NAME' => 'SensorsGroup',
                'TABLE_NAME' => 'sensors_group',
            ));

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsGroup')) {
                self::hlGroups($arExistHlIblock['ID']);
            }

            \Bitrix\Highloadblock\HighloadBlockTable::add(array(
                'NAME' => 'SensorsGroupSensor',
                'TABLE_NAME' => 'sensors_group_sensor',
            ));

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsGroupSensor')) {
                self::hlGroupSensor($arExistHlIblock['ID']);
            }
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data.
     * For printing info into console use object from $this->printer() method.
     * */
    public function rollback()
    {
// my code
    }

    private static function hlSensors($iHlBlockId)
    {
        $obProp = new \Migration\Builder\UserField('UF_LABEL', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obProp->label(array('ru' => 'Свое название', 'en' => 'Свое название'));
        $obProp->save();
    }

    private static function hlGroups($iHlBlockId)
    {
        $obProp = new \Migration\Builder\UserField('UF_SYSTEM_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Система', 'en' => 'Система'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_SORT', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Сортировка', 'en' => 'Сортировка'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_NAME', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_STRING);
        $obProp->label(array('ru' => 'Название', 'en' => 'Название'));
        $obProp->save();
    }

    private static function hlGroupSensor($iHlBlockId)
    {
        $obProp = new \Migration\Builder\UserField('UF_GROUP_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Группа', 'en' => 'Группа'));
        $obProp->save();

        $obProp = new \Migration\Builder\UserField('UF_SENSOR_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Сенсор', 'en' => 'Сенсор'));
        $obProp->save();
    }

}
