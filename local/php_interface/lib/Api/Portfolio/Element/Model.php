<?php

namespace Api\Portfolio\Element;

/**
 * Class \Api\Portfolio\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_MAIN_PORTFOLIO;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
