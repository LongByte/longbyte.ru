<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model {

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
