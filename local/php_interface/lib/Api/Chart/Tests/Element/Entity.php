<?php

namespace Api\Chart\Tests\Element;

/**
 * Class \Api\Chart\Tests\Element\Entity
 *
 * @method int getId()
 * @method string getName()
 * @method string getPreviewText()
 * @method mixed getUnits()
 * @method mixed getLessBetter()
 * @method mixed getUse4sum()
 * @method mixed getUse4sum2()
 * @method mixed getUse4sum3()
 */
class Entity extends \Api\Core\Iblock\Element\Entity
{

    protected ?\Api\Chart\Tests\Section\Entity $obTestType = null;
    protected ?\Api\Chart\Result\Element\Collection $obResults = null;
    protected static array $arFields = array(
        'ID',
        'NAME',
        'SORT',
        'IBLOCK_SECTION_ID',
        'PREVIEW_TEXT',
    );

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function getTestType(): ?\Api\Chart\Tests\Section\Entity
    {
        return $this->obTestType;
    }

    public function setTestType(\Api\Chart\Tests\Section\Entity $obTestType): self
    {
        $this->obTestType = $obTestType;
        return $this;
    }

    public function getResults(): \Api\Chart\Result\Element\Collection
    {
        if (is_null($this->obResults)) {
            $this->obResults = new \Api\Chart\Result\Element\Collection();
        }
        return $this->obResults;
    }

    public function getTestTypeId(): int
    {
        return (int) $this->getIblockSectionId();
    }

    public function getTitle(): string
    {
        return $this->getName() . ($this->getUnits() ? ', ' . $this->getUnits() : '') . ($this->getLessBetter() ? ' (меньше - лучше)' : '');
    }

}
