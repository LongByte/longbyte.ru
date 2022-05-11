<?php

namespace Api\WikiApi\Element;

/**
 * Class \Api\WikiApi\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model
{

    protected static int $_iblockId =  IBLOCK_MAIN_WIKI_API;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
