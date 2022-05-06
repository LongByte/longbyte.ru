<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_CHART_RESULT;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
