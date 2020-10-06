<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Model
 */
class Model extends \Api\Chart\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_SYSTEMS;

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
