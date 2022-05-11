<?php

namespace Api\WikiApi\Section;

/**
 * Class \Api\WikiApi\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model
{

    protected static int $_iblockId = IBLOCK_MAIN_WIKI_API;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
