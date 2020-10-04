<?php

namespace Api\Chart\Tests\Element;

/**
 * Class \Api\Chart\Tests\Element\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method mixed getUnits()
 * @method mixed getLessBetter()
 * @method mixed getUse4sum()
 * @method mixed getUse4sum2()
 * @method mixed getUse4sum3()
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    public $max = null;
    public $max2 = null;
    public $max3 = null;
    public $min = null;
    public $min2 = null;
    public $min3 = null;
    public $iTestsCount = null;

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
        'PREVIEW_TEXT'
    );

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Chart\Tests\Section\Entity 
     */
    public function getTestType() {
        return $this->obTestType;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Section\Entity $obTestType
     * @return $this
     */
    public function setTestType(\Api\Chart\Tests\Section\Entity $obTestType) {
        $this->obTestType = $obTestType;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Result\Element\Collection
     */
    public function getResults() {
        if (is_null($this->obResults)) {
            $this->obResults = new \Api\Chart\Result\Element\Collection();
        }
        return $this->obResults;
    }

    /**
     * 
     * @return float|null
     */
    public function getMax() {

        if (!is_null($this->max)) {
            return $this->max;
        }

        if ($this->getUse4sum()) {
            return $this->getResults()->getMax();
        }
        return null;
    }

    /**
     * 
     * @return float|null
     */
    public function getMax2() {

        if (!is_null($this->max2)) {
            return $this->max2;
        }

        if ($this->getUse4sum2()) {
            return $this->getResults()->getMax2();
        }
        return null;
    }

    /**
     * 
     * @return float|null
     */
    public function getMax3() {

        if (!is_null($this->max3)) {
            return $this->max3;
        }

        if ($this->getUse4sum3()) {
            return $this->getResults()->getMax3();
        }
        return null;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin() {

        if (!is_null($this->min)) {
            return $this->min;
        }

        if ($this->getUse4sum()) {
            return $this->getResults()->getMin();
        }
        return null;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin2() {

        if (!is_null($this->min2)) {
            return $this->min2;
        }

        if ($this->getUse4sum2()) {
            return $this->getResults()->getMin2();
        }
        return null;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin3() {

        if (!is_null($this->min3)) {
            return $this->min3;
        }

        if ($this->getUse4sum3()) {
            return $this->getResults()->getMin3();
        }
        return null;
    }

    /**
     * 
     * @return int
     */
    public function getTestTypeId() {
        return (int) $this->getIblockSectionId();
    }

    /**
     * 
     * @param int $iTestTypeId
     * @return $this
     */
    public function setTestTypeId(int $iTestTypeId) {
        return $this->setIblockSectionId($iTestTypeId);
    }

    /**
     * 
     * @return string
     */
    public function getDescription() {
        return $this->getPreviewText();
    }

    /**
     * 
     * @return string
     */
    public function getTitle() {
        return $this->getName() . ($this->getUnits() ? ', ' . $this->getUnits() : '') . ($this->getLessBetter() ? ' (меньше - лучше)' : '');
    }

}
