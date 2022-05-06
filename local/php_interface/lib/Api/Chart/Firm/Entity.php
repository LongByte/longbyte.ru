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
class Entity extends \Api\Core\Iblock\Element\Entity
{

    protected static array $arFields = array(
        'ID',
        'NAME',
        'XML_ID',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
    );

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function getTextColor(): string
    {
        return $this->getXmlId();
    }

    public function getPassiveColor(): string
    {
        return $this->getPreviewText();
    }

    public function getActiveColor(): string
    {
        return $this->getDetailText();
    }

}
