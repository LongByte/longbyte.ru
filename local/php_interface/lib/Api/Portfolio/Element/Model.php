<?php

namespace Api\Portfolio\Element;

/**
 * Class \Api\Portfolio\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_MAIN_PORTFOLIO;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
