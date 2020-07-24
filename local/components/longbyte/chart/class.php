<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\PropertyTable;
use AB\Iblock\Element;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteChartComponent extends CBitrixComponent {

    /**
     *
     * @var \Api\Chart\Tests\Section\Collection
     */
    private $obTestTypes = null;

    /**
     *
     * @var \Api\Chart\Tests\Element\Collection
     */
    private $obTests = null;

    /**
     *
     * @var \Api\Chart\Systems\Element\Collection
     */
    private $obSystems = null;
    private $arResults = array();

    /**
     * Check Required Modules
     * @throws Exception
     */
    protected function checkModules() {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль инфоблоков не установлен');
        }
        if (!Loader::includeModule('ab.iblock')) {
            throw new SystemException('Модуль инфоблоков не установлен');
        }
    }

    /**
     * Start Component
     */
    public function executeComponent() {

        $this->checkModules();

        if ($this->startResultCache(60 * 60)) {
            $this->arResult = array(
                'TEST_TYPES' => array(),
                'JS_DATA' => array(),
            );

            $this->_getData();
            $this->_processTotalTest();
            $this->_prepareJsData();

            $this->IncludeComponentTemplate();
        }
    }

    /**
     * 
     * @global \CCacheManager $CACHE_MANAGER
     */
    private function _getData() {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->RegisterTag('iblock_id_' . IBLOCK_CHART_TESTS);
        $CACHE_MANAGER->RegisterTag('iblock_id_' . IBLOCK_CHART_SYSTEMS);
        $CACHE_MANAGER->RegisterTag('iblock_id_' . IBLOCK_CHART_RESULT);
        $this->getTestTypes();
        $this->getTests();
        $this->getSystems();
        $this->_getResults();
    }

    /**
     * 
     * @return \Api\Chart\Tests\Section\Collection
     */
    private function getTestTypes() {

        if (is_null($this->obTestTypes)) {

            /** @var \Api\Chart\Tests\Section\Collection $obTestTypes */
            $this->obTestTypes = \Api\Chart\Tests\Section\Model::getAll(array('=ACTIVE' => 'Y'), array(
                    'order' => array('SORT' => 'ASC'),
                    'select' => array('ID', 'NAME', 'TYPE' => 'CODE', 'DESCRIPTION')
            ));
        }
        return $this->obTestTypes;
    }

    /**
     * 
     * @return \Api\Chart\Tests\Element\Collection
     */
    private function getTests() {

        if (is_null($this->obTests)) {

            /** @var \Api\Chart\Tests\Element\Collection $obTests */
            $this->obTests = \Api\Chart\Tests\Element\Model::getAll(array('=ACTIVE' => 'Y'));

            /** @var \Api\Chart\Tests\Element\Entity $obTest */
            foreach ($this->obTests as $obTest) {
                $this
                    ->getTestTypes()
                    ->getByKey($obTest->getTestTypeId())
                    ->getTests()
                    ->addItem($obTest)
                ;
            }
        }
        return $this->obTests;
    }

    /**
     * 
     * @return \Api\Chart\Systems\Element\Collection
     */
    private function getSystems() {

        if (is_null($this->obSystems)) {
            /** @var \Api\Chart\Systems\Element\Collection $obSystems */
            $this->obSystems = \Api\Chart\Systems\Element\Model::getAll(array('=ACTIVE' => 'Y'));
        }
        return $this->obSystems;
    }

    /**
     * 
     */
    private function _getResults() {

        $obResults = \Api\Chart\Result\Element\Model::getAll(array('=ACTIVE' => 'Y'));

        /** @var \Api\Chart\Result\Element\Entity $obResult */
        foreach ($obResults as $obResult) {
            $this
                ->getTests()
                ->getByKey($obResult->getTestId())
                ->getResults()
                ->addItem($obResult)
            ;
        }
    }

    /**
     * 
     */
    private function _prepareJsData() {

        /** @var \Api\Chart\Tests\Section\Entity $obTestType */
        foreach ($this->getTestTypes() as $obTestType) {

            /** @var \Api\Chart\Tests\Element\Entity $obTest */
            foreach ($obTestType->getTests() as $obTest) {
                $arDataTests = array();
                /** @var  \Api\Chart\Result\Element\Entity $obResult */
                foreach ($obTest->getResults() as $obResult) {

                    if (floatval($obResult->getResult()) == 0.0) {
                        continue;
                    }

                    $obResult->prepareData();

                    $arRes = array($arResult['RESULT']);
                    if (!empty($arResult['RESULT2']))
                        $arRes[] = $arResult['RESULT2'];
                    if (!empty($arResult['RESULT3']))
                        $arRes[] = $arResult['RESULT3'];

                    $arDataItem = array(
                        $arResult['NAME']
                    );
                    foreach (explode(',', $arResult['COLOR']) as $colorPart) {
                        $arDataItem[] = (int) trim($colorPart);
                    }
                    foreach ($arRes as $oneRes) {
                        $arDataItem[] = ((int) $oneRes == $oneRes) ? (int) $oneRes : (float) $oneRes;
                    }
                    $arDataTests[] = $arDataItem;
                }
                unset($arResult);
                $this->arResult['JS_DATA'][] = $arDataTests;
            }
            unset($arTest);
        }
        unset($arTestType);
    }

    /**
     * 
     * @param int|float $va1ue_1
     * @param int|float $va1ue_2
     * @param int $presicion
     * @return int
     */
    private function _percent($va1ue_1, $va1ue_2, $presicion = 0) {
        return round(($va1ue_1 / $va1ue_2 - 1) * 100, $presicion);
    }

}
