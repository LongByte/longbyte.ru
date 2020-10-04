<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Systems\Element\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     * 
     * @param type $strKey
     * @return Entity
     */
    public function getByKey($strKey) {
        return parent::getByKey($strKey);
    }

    /**
     * 
     * @return float|null
     */
    public function getMax() {
        $fMax = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMax) || $obItem->getResult() > $fMax) {
                $fMax = $obItem->getResult();
            }
        }
        return $fMax;
    }

    /**
     * 
     * @return float|null
     */
    public function getMax2() {
        $fMax = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMax) || $obItem->getResult2() > $fMax) {
                $fMax = $obItem->getResult2();
            }
        }
        return $fMax;
    }

    /**
     * 
     * @return float|null
     */
    public function getMax3() {
        $fMax = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMax) || $obItem->getResult3() > $fMax) {
                $fMax = $obItem->getResult3();
            }
        }
        return $fMax;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin() {
        $fMin = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMin) || $obItem->getResult() < $fMin) {
                $fMin = $obItem->getResult();
            }
        }
        return $fMin;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin2() {
        $fMin = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMin) || $obItem->getResult2() < $fMin) {
                $fMin = $obItem->getResult2();
            }
        }
        return $fMin;
    }

    /**
     * 
     * @return float|null
     */
    public function getMin3() {
        $fMin = null;
        /** @var Entity $obItem */
        foreach ($this->getCollection() as $obItem) {
            if (is_null($fMin) || $obItem->getResult3() < $fMin) {
                $fMin = $obItem->getResult3();
            }
        }
        return $fMin;
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Collection
     */
    public function getHasResultSystems() {

        $obSystemsCollection = new \Api\Chart\Systems\Element\Collection();

        /** @var Entity $obResult */
        foreach ($this->getCollection() as $obResult) {
            if ($obResult->getResult() > 0.0) {
                $obSystemsCollection->addItem($obResult->getSystem());
            }
        }
        return $obSystemsCollection;
    }

}
