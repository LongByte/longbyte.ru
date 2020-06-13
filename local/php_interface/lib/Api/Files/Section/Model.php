<?php

namespace Api\Files\Section;

/**
 * Class \Api\Files\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model {

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
