<?php

namespace Api\Files\Element;

/**
 * Class \Api\Files\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_FILES_FILES;

    /**
     * 
     * @return string
     */
    public static function getEntity() {
        return Entity::class;
    }

}
