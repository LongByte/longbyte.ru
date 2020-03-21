<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Entity
 * 
 */
class Entity extends \Api\Core\Base\Entity {

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public static function getCollection() {
        return Collection::class;
    }

    public function getFields() {
        $arFields = array_keys(static::getModel()::getTable()::getScalarFields());
        $arFields[] = 'VALUE';
        $arFields[] = 'VALUE_XML_ID';
        $arFields[] = 'VALUE_ID';
        $arFields[] = 'DESCRIPTION';
        return $arFields;
    }

    /**
     * 
     * @return null|array
     */
    public function getData() {
        return null;
    }

    public function save() {
        return false;
    }

    public function delete() {
        return false;
    }

}
