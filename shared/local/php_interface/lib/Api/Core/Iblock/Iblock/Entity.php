<?php

namespace Api\Core\Iblock\Iblock;

/**
 * Class \Api\Core\Iblock\Iblock\Entity
 *
 */
class Entity extends \Api\Core\Base\Entity {

    protected static $arFields = array(
        'ID',
        'XML_ID',
        'IBLOCK_TYPE_ID',
        'CODE',
        'NAME',
        'SORT',
        'ACTIVE',
        'DESCRIPTION',
        'LIST_PAGE_URL',
        'DETAIL_PAGE_URL',
        'SECTION_PAGE_URL'
    );

    /**
     * 
     * @return string
     */
    public static function getModel() {
        return Model::class;
    }

}
