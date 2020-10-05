<?php

namespace Api\Chart\Firm;

/**
 * Class \Api\Chart\Firm\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_FIRM;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
