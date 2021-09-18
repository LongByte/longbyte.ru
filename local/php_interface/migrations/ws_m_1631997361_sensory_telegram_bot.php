<?php

/**
 * Class definition update migrations scenario actions
 **/
class ws_m_1631997361_sensory_telegram_bot extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Scenario title
     **/
    public static function name()
    {
        return 'Сенсоры. Телеграм бот';
    }

    /**
     * Priority of scenario
     **/
    public static function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    public static function hash()
    {
        return '8a75115d6914232522a0212748a344f9199f5771';
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
     **/
    public function commit()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            \Bitrix\Highloadblock\HighloadBlockTable::add(array(
                'NAME' => 'SensorsTelegram',
                'TABLE_NAME' => \Api\Sensors\Telegram\Table::getTableName(),
            ));

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsTelegram')) {
                $this->hlSensorsTelegram($arExistHlIblock['ID']);
            }
        }
    }

    private function hlSensorsTelegram($iHlBlockId)
    {
        $obProp = new \Migration\Builder\UserField('UF_ACTIVE', 'HLBLOCK_' . $iHlBlockId);
        $obProp
            ->type(\Migration\Builder\UserField::TYPE_BOOLEAN)
            ->label(array('ru' => 'Активность', 'en' => 'Активность'))
            ->save()
        ;

        $obProp = new \Migration\Builder\UserField('UF_SYSTEM_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp
            ->type(\Migration\Builder\UserField::TYPE_INTEGER)
            ->label(array('ru' => 'ID системы', 'en' => 'ID системы'))
            ->save()
        ;

        $obProp = new \Migration\Builder\UserField('UF_CHAT_ID', 'HLBLOCK_' . $iHlBlockId);
        $obProp
            ->type(\Migration\Builder\UserField::TYPE_STRING)
            ->label(array('ru' => 'ID чата', 'en' => 'ID чата'))
            ->save()
        ;
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data.
     * For printing info into console use object from $this->printer() method.
     **/
    public function rollback()
    {

    }
}
