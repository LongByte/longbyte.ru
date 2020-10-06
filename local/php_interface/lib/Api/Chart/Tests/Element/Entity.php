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
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var \Api\Chart\Tests\Section\Entity 
     */
    protected $obTestType = null;

    /**
     *
     * @var \Api\Chart\Result\Element\Collection
     */
    protected $obResults = null;

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'SORT',
        'IBLOCK_SECTION_ID',
        'PREVIEW_TEXT',
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
     * @return \Api\Chart\Tests\Section\Entity 
     */
    public function getTestType(): ?\Api\Chart\Tests\Section\Entity {
        return $this->obTestType;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Section\Entity $obTestType
     * @return \self
     */
    public function setTestType(\Api\Chart\Tests\Section\Entity $obTestType): self {
        $this->obTestType = $obTestType;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Result\Element\Collection
     */
    public function getResults(): \Api\Chart\Result\Element\Collection {
        if (is_null($this->obResults)) {
            $this->obResults = new \Api\Chart\Result\Element\Collection();
        }
        return $this->obResults;
    }

    /**
     * 
     * @return int
     */
    public function getTestTypeId(): int {
        return (int) $this->getIblockSectionId();
    }

    /**
     * 
     * @return string
     */
    public function getTitle(): string {
        return $this->getName() . ($this->getUnits() ? ', ' . $this->getUnits() : '') . ($this->getLessBetter() ? ' (меньше - лучше)' : '');
    }

}
