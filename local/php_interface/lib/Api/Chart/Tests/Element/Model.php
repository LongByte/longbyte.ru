<?php

namespace Api\Chart\Tests\Element;

/**
 * Class \Api\Chart\Tests\Element\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_CHART_TESTS;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
