<?php

namespace Api\Chart\Result\Section;

/**
 * Class \Api\Chart\Result\Section\Model
 */
class Model extends \Api\Core\Iblock\Section\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_RESULT;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
