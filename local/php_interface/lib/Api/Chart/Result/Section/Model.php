<?php

namespace Api\Chart\Result\Section;

/**
 * Class \Api\Chart\Result\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model
{

    protected static int $_iblockId = IBLOCK_CHART_RESULT;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
