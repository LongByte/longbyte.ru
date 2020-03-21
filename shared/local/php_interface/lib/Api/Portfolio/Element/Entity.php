<?php

namespace Api\Portfolio\Element;

/**
 * Class \Api\Portfolio\Element\Entity
 * 
 */
class Entity extends \Api\Core\Entity\Element {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'DETAIL_TEXT',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'TAGS',
    );

    /**
     * @var array
     */
    protected static $arProps = array(
        'URL'
    );

    /**
     * 
     * @return \Api\Sensors\Data\Model
     */
    public static function getModel() {
        return Model::class;
    }

}
