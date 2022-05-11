<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteChartComponent extends CBitrixComponent
{

    private ?\Api\Chart\Tests\Section\Collection $obTestTypes = null;
    private ?\Api\Chart\Tests\Element\Collection $obTests = null;
    private ?\Api\Chart\Systems\Element\Collection $obSystems = null;
    private ?\Api\Chart\Firm\Collection $obFirms = null;

    protected function checkModules(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new \Bitrix\Main\SystemException('Модуль инфоблоков не установлен');
        }
    }

    public function executeComponent()
    {

        $this->checkModules();

        $this->obTestTypes = \Api\Core\Main\Cache::getInstance()
            ->setIblockTag(IBLOCK_CHART_TESTS)
            ->setIblockTag(IBLOCK_CHART_SYSTEMS)
            ->setIblockTag(IBLOCK_CHART_RESULT)
            ->setIblockTag(IBLOCK_CHART_FIRM)
            ->setTime(24 * 60 * 60)
            ->setId('chart')
            ->get(function () {

                $this->_getData();

                return $this->getTestTypes();
            });

        $this->_prepareData();
        $this->_prepareJsData();

        $this->IncludeComponentTemplate();
    }

    private function _getData(): void
    {
        $this->getTestTypes();
        $this->getTests();
        $this->getFirms();
        $this->getSystems();
        $this->getResults();
    }

    private function getTestTypes(): \Api\Chart\Tests\Section\Collection
    {

        if (is_null($this->obTestTypes)) {

            /** @var \Api\Chart\Tests\Section\Collection $obTestTypes */
            $obTestTypes = \Api\Chart\Tests\Section\Model::getAll(array('=ACTIVE' => 'Y'), array(
                'order' => array('SORT' => 'ASC'),
            ));
            $this->obTestTypes = $obTestTypes;
        }
        return $this->obTestTypes;
    }

    private function getTests(): \Api\Chart\Tests\Element\Collection
    {
        if (is_null($this->obTests)) {

            /** @var \Api\Chart\Tests\Element\Collection $obTests */
            $obTests = \Api\Chart\Tests\Element\Model::getAll(array('=ACTIVE' => 'Y'));
            $this->obTests = $obTests;

            /** @var \Api\Chart\Tests\Element\Entity $obTest */
            foreach ($this->obTests as $obTest) {
                $obTestType = $this->getTestTypes()->getByKey($obTest->getTestTypeId());
                if (!is_null($obTestType)) {
                    $obTestType->getTests()->addItem($obTest);
                    $obTest->setTestType($obTestType);
                }
            }
        }
        return $this->obTests;
    }

    private function getFirms(): \Api\Chart\Firm\Collection
    {
        if (is_null($this->obFirms)) {
            /** @var \Api\Chart\Firm\Collection $obFirms */
            $obFirms = \Api\Chart\Firm\Model::getAll(array('=ACTIVE' => 'Y'));
            $this->obFirms = $obFirms;
        }
        return $this->obFirms;
    }

    private function getSystems(): \Api\Chart\Systems\Element\Collection
    {

        if (is_null($this->obSystems)) {
            /** @var \Api\Chart\Systems\Element\Collection $obSystems */
            $obSystems = \Api\Chart\Systems\Element\Model::getAll(array('=ACTIVE' => 'Y'));
            $this->obSystems = $obSystems;

            /** @var \Api\Chart\Systems\Element\Entity $obSystem */
            foreach ($this->getSystems() as $obSystem) {
                if ($obSystem->getCpuFirmId() > 0) {
                    if ($obFirm = $this->getFirms()->getByKey($obSystem->getCpuFirmId())) {
                        $obSystem->setCpuFirm($obFirm);
                    }
                }
                if ($obSystem->getGpuFirmId() > 0) {
                    if ($obFirm = $this->getFirms()->getByKey($obSystem->getGpuFirmId())) {
                        $obSystem->setGpuFirm($obFirm);
                    }
                }
                if ($obSystem->getHdFirmId() > 0) {
                    if ($obFirm = $this->getFirms()->getByKey($obSystem->getHdFirmId())) {
                        $obSystem->setHdFirm($obFirm);
                    }
                }
            }
        }
        return $this->obSystems;
    }

    private function getResults(): void
    {

        $obResults = \Api\Chart\Result\Element\Model::getAll(array('=ACTIVE' => 'Y'));

        /** @var \Api\Chart\Result\Element\Entity $obResult */
        foreach ($obResults as $obResult) {
            $obTest = $this->getTests()->getByKey($obResult->getTestId());
            $obSystem = $this->getSystems()->getByKey($obResult->getSystemId());

            if (!is_null($obTest) && !is_null($obSystem)) {
                $obTest->getResults()->addItem($obResult);
                $obResult->setTest($obTest);

                $obResult->setSystem($obSystem);
            }
        }
    }

    private function _prepareJsData(): void
    {

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

                    $arRes = array($obResult->getResult());
                    if (!empty($obResult->getResult2()))
                        $arRes[] = $obResult->getResult2();
                    if (!empty($obResult->getResult3()))
                        $arRes[] = $obResult->getResult3();

                    $arDataItem = array(
                        $obResult->getFullName()
                    );
                    foreach (explode(',', $obResult->getColor()) as $colorPart) {
                        $arDataItem[] = (int) trim($colorPart);
                    }
                    foreach ($arRes as $oneRes) {
                        $arDataItem[] = ((int) $oneRes == $oneRes) ? (int) $oneRes : (float) $oneRes;
                    }
                    $arDataTests[] = $arDataItem;
                }
                $this->arResult['JS_DATA'][] = $arDataTests;
            }
        }
    }

    private function _prepareData(): void
    {
        $this->arResult['obData'] = $this->getTestTypes();
    }

}
