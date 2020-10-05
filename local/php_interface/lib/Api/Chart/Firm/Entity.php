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

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'XML_ID',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
    );

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public static function getCollection(): string {
        return Collection::class;
    }

    /**
     * 
     * @return string
     */
    public function getTextColor(): string {
        return $this->getXmlId();
    }

    /**
     * 
     * @return string
     */
    public function getPassiveColor(): string {
        return $this->getPreviewText();
    }

    /**
     * 
     * @return string
     */
    public function getActiveColor(): string {
        return $this->getDetailText();
    }

}
