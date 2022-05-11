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
class Entity extends \Api\Core\Iblock\Section\Entity
{

    protected static array $arFields = array(
        'ID',
        'NAME',
        'CODE',
        'DESCRIPTION',
    );

    protected ?\Api\Chart\Tests\Element\Collection $obTests = null;

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function getTests(): \Api\Chart\Tests\Element\Collection
    {
        if (is_null($this->obTests)) {
            $this->obTests = new \Api\Chart\Tests\Element\Collection();
        }
        return $this->obTests;
    }

}
