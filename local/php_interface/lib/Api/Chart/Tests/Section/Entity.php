<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method string getCode()
 * @method mixed getDescription()
 */
class Entity extends \Api\Core\Iblock\Section\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'CODE',
        'DESCRIPTION',
    );

    /**
     *
     * @var \Api\Chart\Tests\Element\Collection
     */
    protected $obTests = null;

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Chart\Tests\Element\Collection
     */
    public function getTests() {
        if (is_null($this->obTests)) {
            $this->obTests = new \Api\Chart\Tests\Element\Collection();
        }
        return $this->obTests;
    }

}
