<?php

namespace Api\WikiApi\Element;

/**
 * Class \Api\WikiApi\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_MAIN_WIKI_API;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
