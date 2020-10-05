<?php

namespace Api\Chart\Systems\Section;

/**
 * Class \Api\Chart\Systems\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_SYSTEMS;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Model::class;
    }

}
