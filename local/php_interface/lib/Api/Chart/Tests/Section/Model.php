<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model
{

    protected static int $_iblockId = IBLOCK_CHART_TESTS;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
