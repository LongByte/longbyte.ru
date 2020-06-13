<?php

namespace Api\Sensors\System;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\System\Table
 */
class Table extends Main\Entity\DataManager {

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'sensors_system';
    }

    public static function getScalarFields() {
        $arFields = array();
        foreach (self::getMap() as $strId => $obField) {
            if ($obField instanceof \Bitrix\Main\Entity\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap() {
        return array(
            'ID' => new Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
                )),
            'ACTIVE' => new Main\Entity\BooleanField('UF_ACTIVE', array(
                'title' => 'Активность',
                )),
            'NAME' => new Main\Entity\StringField('UF_NAME', array(
                'required' => true,
                'title' => 'Название',
                )),
            'TOKEN' => new Main\Entity\StringField('UF_TOKEN', array(
                'required' => true,
                'title' => 'Токен',
                )),
            'EMAIL' => new Main\Entity\StringField('UF_EMAIL', array(
                'required' => true,
                'title' => 'E-mail для уведомлений',
                )),
            'LAST_UPDATE' => new Main\Entity\DateTimeField('UF_LAST_UPDATE', array(
                'required' => true,
                'title' => 'Последнее обновление данных',
                )),
            'LAST_RECEIVE' => new Main\Entity\DateTimeField('UF_LAST_RECEIVE', array(
                'required' => true,
                'title' => 'Последнее получение данных',
                )),
            'USER_ID' => new Main\Entity\IntegerField('UF_USER_ID', array(
                'required' => true,
                'title' => 'Пользователь',
                )),
            'USER' => new Main\Entity\ReferenceField(
                'USER',
                '\Bitrix\Main\User',
                array('this.USER_ID' => 'ref.ID'),
                array('join_type' => 'LEFT')
            ),
        );
    }

}