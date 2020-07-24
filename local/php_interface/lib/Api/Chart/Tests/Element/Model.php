<?php

namespace Api\Chart\Tests\Element;

/**
 * Class \Api\Chart\Tests\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_TESTS;

    public static function getEntity() {
        return Entity::class;
    }

}
