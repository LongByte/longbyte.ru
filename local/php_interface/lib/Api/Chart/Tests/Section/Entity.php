<?php

namespace Api\Chart\Tests\Section;

/**
 * Class \Api\Chart\Tests\Section\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method mixed getType()
 * @method $this setType(mixed $mixedType)
 * @method mixed getDescription()
 * @method $this setDescription(mixed $mixedDescription)
 */
class Entity extends \Api\Core\Iblock\Section\Entity {

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
