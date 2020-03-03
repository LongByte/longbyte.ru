<?php

namespace Api\Sensors;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\SensorsSystemTable
 */
class SensorsSystemTable extends Main\Entity\DataManager {

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
            'UF_NAME' => new Main\Entity\StringField('UF_NAME', array(
                'required' => true,
                'title' => 'Название',
                )),
            'UF_TOKEN' => new Main\Entity\StringField('UF_TOKEN', array(
                'required' => true,
                'title' => 'Токен',
                )),
        );
    }

}
