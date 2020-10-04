<?php

namespace Api\Chart\Firm;

/**
 * Class \Api\Chart\Firm\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method mixed getXmlId()
 * @method string getPreviewText()
 * @method string getDetailText()
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public function getTextColor() {
        return $this->getXmlId();
    }

    /**
     * 
     * @return string
     */
    public function getPassiveColor() {
        return $this->getPreviewText();
    }

    /**
     * 
     * @return string
     */
    public function getActiveColor() {
        return $this->getDetailText();
    }

}
