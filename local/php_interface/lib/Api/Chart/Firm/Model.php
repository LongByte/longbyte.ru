<?php

namespace Api\Chart\Firm;

/**
 * Class \Api\Chart\Firm\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_CHART_FIRM;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
