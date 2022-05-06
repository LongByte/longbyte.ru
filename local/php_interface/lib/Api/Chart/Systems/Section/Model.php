<?php

namespace Api\Chart\Systems\Section;

/**
 * Class \Api\Chart\Systems\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model
{

    protected static int $_iblockId = IBLOCK_CHART_SYSTEMS;

    public static function getEntity(): string
    {
        return Model::class;
    }

}
