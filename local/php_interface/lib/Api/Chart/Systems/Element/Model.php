<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model
{

    protected static int $_iblockId = IBLOCK_CHART_SYSTEMS;

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
