<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_SYSTEMS;

    public static function getEntity() {
        return Entity::class;
    }

}
