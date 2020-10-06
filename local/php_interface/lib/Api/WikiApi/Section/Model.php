<?php

namespace Api\WikiApi\Section;

/**
 * Class \Api\WikiApi\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model {

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
