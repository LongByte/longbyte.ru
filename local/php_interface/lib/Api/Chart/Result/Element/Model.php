<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Model
 */
class Model extends \Api\Core\Iblock\Element\Model {

    /**
     * @var int
     */
    protected static $_iblockId = IBLOCK_CHART_RESULT;

    public static function getEntity() {
        return Entity::class;
    }

}
