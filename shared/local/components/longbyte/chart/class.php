<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\PropertyTable;
use AB\Iblock\Element;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

class LongbyteChartComponent extends CBitrixComponent {

    private $arTestTypes = array();
    private $arTests = array();
    private $arSystems = array();
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
        $this->_getTestTypes();
        $this->_getTests();
        $this->_getSystems();
        $this->_getResults();
    }

    /**
     * 
     */
    private function _getTestTypes() {
        $this->arResult = array('TEST_TYPES' => array());
        $rsTestTypes = SectionTable::getList(array(
                'order' => array('SORT' => 'ASC'),
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS, '=ACTIVE' => 'Y'),
                'select' => array('ID', 'NAME', 'TYPE' => 'CODE', 'DESCRIPTION')
        ));

        while ($arTestType = $rsTestTypes->fetch()) {
            $this->arTestTypes[$arTestType['ID']] = $arTestType;
            $this->arResult['TEST_TYPES'][(string) $arTestType['ID']] = &$this->arTestTypes[$arTestType['ID']];
        }
    }

    /**
     * 
     */
    private function _getTests() {
        $rsTest = Element::getList(array(
                'order' => array('SORT' => 'ASC'),
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS, '=ACTIVE' => 'Y'),
                'select' => array(
                    'ID',
                    'NAME',
                    'DESCRIPTION' => 'PREVIEW_TEXT',
                    'TEST_TYPE_ID' => 'IBLOCK_SECTION_ID',
                    'UNITS' =>
                    'PROPERTY.UNITS',
                    'LESS_BETTER' => 'PROPERTY.LESS_BETTER',
                    'USE4SUM' => 'PROPERTY.USE4SUM',
                    'USE4SUM2' => 'PROPERTY.USE4SUM2',
                    'USE4SUM3' => 'PROPERTY.USE4SUM3'
                ),
        ));

        while ($arTest = $rsTest->fetch()) {
            $arTest['MAX'] = 0;
            $arTest['MIN'] = PHP_INT_MAX;
            $arTest['MAX2'] = 0;
            $arTest['MIN2'] = PHP_INT_MAX;
            $arTest['MAX3'] = 0;
            $arTest['MIN3'] = PHP_INT_MAX;
            $arTest['TEST_TYPE'] = &$this->arTestTypes[$arTest['TEST_TYPE_ID']];
            $this->arTests[$arTest['ID']] = $arTest;
            $this->arResult['TEST_TYPES'][$arTest['TEST_TYPE_ID']]['TESTS'][(string) $arTest['ID']] = &$this->arTests[$arTest['ID']];
        }
    }

    /**
     * 
     */
    private function _getSystems() {

        $arSelect = array(
            'ID',
            'NAME',
        );

        $arSystemProps = array();
        $rsProps = PropertyTable::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, '=ACTIVE' => 'Y'),
                'select' => array('ID', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE')
        ));

        while ($arProp = $rsProps->fetch()) {
            $arSystemProps['PROP_' . $arProp['CODE']] = $arProp;
            if ($arProp['PROPERTY_TYPE'] == PropertyTable::TYPE_ELEMENT) {
                $arSelect['PROP_' . $arProp['CODE'] . '_VALUE'] = 'PROPERTY.' . $arProp['CODE'] . '.NAME';
                $arSelect['PROP_' . $arProp['CODE'] . '_TEXT_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.XML_ID';
                $arSelect['PROP_' . $arProp['CODE'] . '_PASSIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.PREVIEW_TEXT';
                $arSelect['PROP_' . $arProp['CODE'] . '_ACTIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.DETAIL_TEXT';
            } else {
                $arSelect['PROP_' . $arProp['CODE']] = 'PROPERTY.' . $arProp['CODE'];
            }
        }

        $rsSytems = Element::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, '=ACTIVE' => 'Y'),
                'select' => $arSelect,
        ));

        while ($arSystem = $rsSytems->fetch()) {
            if (!isset($this->arSystems[$arSystem['ID']])) {
                foreach ($arSystem as $field => &$value) {
                    if ($arSystemProps[$field]['MULTIPLE'] == 'Y') {
                        $value = is_null($value) ? array() : array($value);
                    }
                }
                unset($value);
                $this->arSystems[$arSystem['ID']] = $arSystem;
            } else {
                foreach ($arSystem as $field => &$value) {
                    if ($arSystemProps[$field]['MULTIPLE'] == 'Y' && !is_null($value) && !in_array($value, $this->arSystems[$arSystem['ID']][$field])) {
                        $this->arSystems[$arSystem['ID']][$field][] = $value;
                    }
                }
                unset($value);
            }
        }
    }

    /**
     * 
     */
    private function _getResults() {
        $rsResults = Element::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_RESULT, '=ACTIVE' => 'Y'),
                'select' => array(
                    'ID',
                    'NAME',
                    'INFO' => 'PREVIEW_TEXT',
                    'TEST_TYPE_ID' => 'PROPERTY.TEST.IBLOCK_SECTION_ID',
                    'TEST_ID' => 'PROPERTY.TEST.ID',
                    'SYSTEM_ID' => 'PROPERTY.SYSTEM.ID',
                    'RESULT' => 'PROPERTY.RESULT',
                    'RESULT2' => 'PROPERTY.RESULT2',
                    'RESULT3' => 'PROPERTY.RESULT3',
                ),
        ));

        while ($arResult = $rsResults->fetch()) {
            if (isset($this->arTestTypes[$arResult['TEST_TYPE_ID']]) && isset($this->arTests[$arResult['TEST_ID']]) && isset($this->arSystems[$arResult['SYSTEM_ID']])) {

                $arResult['TEST_TYPE'] = &$this->arTestTypes[$arResult['TEST_TYPE_ID']];
                $arResult['TEST'] = &$this->arTests[$arResult['TEST_ID']];
                $arResult['SYSTEM'] = &$this->arSystems[$arResult['SYSTEM_ID']];

                if ($arResult['TEST']['USE4SUM']) {
                    $arResult['TEST']['MAX'] = max($arResult['TEST']['MAX'], $arResult['RESULT']);
                    $arResult['TEST']['MIN'] = min($arResult['TEST']['MIN'], $arResult['RESULT']);
                }
                if ($arResult['TEST']['USE4SUM2']) {
                    $arResult['TEST']['MAX2'] = max($arResult['TEST']['MAX2'], $arResult['RESULT2']);
                    $arResult['TEST']['MIN2'] = min($arResult['TEST']['MIN2'], $arResult['RESULT2']);
                }
                if ($arResult['TEST']['USE4SUM3']) {
                    $arResult['TEST']['MAX3'] = max($arResult['TEST']['MAX3'], $arResult['RESULT3']);
                    $arResult['TEST']['MIN3'] = min($arResult['TEST']['MIN3'], $arResult['RESULT3']);
                }

                $this->arResults[$arResult['ID']] = $arResult;
                $arResult['TEST']['RESULTS'][$arResult['ID']] = &$this->arResults[$arResult['ID']];
            }
        }
    }

    /**
     * 
     */
    private function _processTotalTest() {
        foreach ($this->arTestTypes as &$arTestType) {

            $testId = 'total' . $arTestType['TYPE'];

            $this->arTests[$testId] = array(
                'ID' => $testId,
                'NAME' => 'Итог',
                'DESCRIPTION' => $arTestType['DESCRIPTION'],
                'TEST_TYPE' => &$this->arTestTypes[$arTestType['ID']],
                'TEST_TYPE_ID' => $arTestType['ID'],
                'UNITS' => '',
                'LESS_BETTER' => 0,
                'USE4SUM' => 0,
                'MAX' => 0,
                'TESTS_COUNT' => 0,
                'RESULTS' => array()
            );

            $arTestType['TESTS'][$testId] = &$this->arTests[$testId];
            $arTotalTest = &$this->arTests[$testId];

            foreach ($arTestType['TESTS'] as &$arTest) {

                if (count($arTest['RESULTS']) > 0) {
                    if ($arTest['USE4SUM']) {
                        $arTotalTest['MAX'] += 100;
                        $arTotalTest['TESTS_COUNT'] ++;
                    }
                    if ($arTest['USE4SUM2']) {
                        $arTotalTest['MAX2'] += 100;
                        $arTotalTest['TESTS_COUNT'] ++;
                    }
                    if ($arTest['USE4SUM3']) {
                        $arTotalTest['MAX3'] += 100;
                        $arTotalTest['TESTS_COUNT'] ++;
                    }
                }
                foreach ($arTest['RESULTS'] as &$arResult) {
                    if (!isset($arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']])) {
                        $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']] = array(
                            'ID' => $arResult['SYSTEM']['ID'],
                            'NAME' => $arResult['SYSTEM']['NAME'],
                            'TEST_TYPE' => &$this->arTestTypes[$arTestType['ID']],
                            'TEST_TYPE_ID' => $arTestType['ID'],
                            'TEST' => &$this->arTests[$testId],
                            'TEST_ID' => $arTotalTest['ID'],
                            'SYSTEM_ID' => $arResult['SYSTEM']['ID'],
                            'SYSTEM' => &$this->arSystems[$arResult['SYSTEM']['ID']],
                            'RESULT' => 0,
                            'INFO' => '',
                            'TESTS_COUNT' => 0
                        );
                    }

                    $this->_calculateSum($arResult, 1);
                    $this->_calculateSum($arResult, 2);
                    $this->_calculateSum($arResult, 3);
                }
                unset($arResult);
            }
            unset($arTest);

            foreach ($arTotalTest['RESULTS'] as $i => $arSystem) {
                if ($arSystem['TESTS_COUNT'] < $arTotalTest['TESTS_COUNT']) {
                    unset($arTotalTest['RESULTS'][$i]);
                }
            }
            unset($arTotalTest);
        }
        unset($arTestType);
    }

    /**
     * 
     * @param array $arResult
     * @param string $numResult
     */
    private function _calculateSum($arResult, $numResult) {
        if ($numResult == 1)
            $numResult = '';

        $arTestType = &$arResult['TEST_TYPE'];
        $arTest = &$arResult['TEST'];
        $arTotalTest = &$this->arTests['total' . $arTestType['TYPE']];

        if ($arTest['USE4SUM' . $numResult]) {
            if (!empty($arResult['RESULT' . $numResult])) {
                $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']]['TESTS_COUNT'] ++;

                if ($arTestType['TYPE'] == 'RAM') {
                    if ($arTest['LESS_BETTER']) {
                        $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']]['RESULT'] += round((100 - $arResult['RESULT']) * 2 / 4, 2);
                    } else {
                        $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']]['RESULT'] += round($arResult['RESULT'] / $arResult['RESULT' . $numResult] * 100 / 4, 2);
                    }
                } elseif ($arTest['LESS_BETTER']) {
                    $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']]['RESULT'] += floor(($arTest['MAX' . $numResult] - $arResult['RESULT' . $numResult]) / ($arTest['MAX' . $numResult] - $arTest['MIN' . $numResult]) * 100);
                } else {
                    $arTotalTest['RESULTS'][$arResult['SYSTEM']['ID']]['RESULT'] += floor($arResult['RESULT' . $numResult] / $arTest['MAX' . $numResult] * 100);
                }
            }
        }
    }

    /**
     * 
     */
    private function _prepareJsData() {

        foreach ($this->arTestTypes as &$arTestType) {

            foreach ($arTestType['TESTS'] as $i => &$arTest) {
                $arDataTests = array();
                foreach ($arTest['RESULTS'] as $j => &$arResult) {

                    if (floatval($arResult['RESULT']) == 0.0)
                        continue;

                    $arResult['COLOR'] = '127, 127, 127';

                    $arResult['NAME'] = '<span';
                    if (!empty($arResult['INFO']))
                        $arResult['NAME'] .= ' title="' . nl2br($arResult['INFO']) . '"';
                    $arResult['NAME'] .= '>';

                    switch ($arResult['TEST_TYPE']['TYPE']) {
                        case 'GPU':
                            $this->_prepareGPUs($arResult);
                            break;
                        case 'CPU':
                        case 'RAM':
                            $this->_prepareCPU_RAMs($arResult);
                            break;
                        case 'DRIVE':
                            $this->_prepareHDDs($arResult);
                            break;
                    }
                    $arResult['NAME'] .= '</span>';

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
     * @param array $arResult
     */
    private function _prepareGPUs(&$arResult) {
        $arSystem = &$arResult['SYSTEM'];
        $arTestType = &$arResult['TEST_TYPE'];
        $arResult['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_GPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_GPU'];
        $ocCore = 0;
        $ocRam = 0;
        if (!empty($arSystem['PROP_GPU_CORE_FREQ'])) {
            $arResult['NAME'] .= '@' . $arSystem['PROP_GPU_CORE_FREQ'];
            if (!empty($arSystem['PROP_GPU_VRAM_FREQ'])) {
                $arResult['NAME'] .= '/' . $arSystem['PROP_GPU_VRAM_FREQ'];
            }
        }
        if (!empty($arSystem['PROP_GPU_CORE_BFREQ']) && $arSystem['PROP_GPU_CORE_BFREQ'] != $arSystem['PROP_GPU_CORE_FREQ']) {
            $ocCore = $this->_percent($arSystem['PROP_GPU_CORE_FREQ'], $arSystem['PROP_GPU_CORE_BFREQ']);
        }
        if (!empty($arSystem['PROP_GPU_VRAM_BFREQ']) && $arSystem['PROP_GPU_VRAM_BFREQ'] != $arSystem['PROP_GPU_VRAM_FREQ']) {
            $ocRam = $this->_percent($arSystem['PROP_GPU_VRAM_FREQ'], $arSystem['PROP_GPU_VRAM_BFREQ']);
        }
        if ($ocCore > 0 || $ocRam > 0) {
            if ($ocCore > 0)
                $ocCore = '+' . $ocCore;
            $arResult['NAME'] .= '<span class="oc"> ' . $ocCore . '%';
            if ($ocRam) {
                if ($ocRam > 0)
                    $ocRam = '+' . $ocRam;
                $arResult['NAME'] .= '/' . $ocRam . '%';
            }
            $arResult['NAME'] .= '</span>';
        }
        if (!empty($arSystem['PROP_GPU_VCORE'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_GPU_VCORE'] . 'V</span>';
        }
        if (!empty($arSystem['PROP_GPU_PCIE'])) {
            $arResult['NAME'] .= '<span class="comment">PCI-E ' . $arSystem['PROP_GPU_PCIE'] . '</span>';
        }
        $arResult['NAME'] .= '</span>';

        if ($arSystem['PROP_GPU_CF'])
            $arResult['NAME'] .= '<span class="oc"> CF</span>';
        if ($arSystem['PROP_GPU_SLI'])
            $arResult['NAME'] .= '<span class="oc"> SLI</span>';

        $arResult['NAME'] .= ', ';

        $arResult['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
        if (!empty($arSystem['PROP_CPU_FREQ'])) {
            $arResult['NAME'] .= '@' . $arSystem['PROP_CPU_FREQ'];
        }
        $ocCore = 0;
        if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
            $ocCore = $this->_percent($arSystem['PROP_CPU_FREQ'], $arSystem['PROP_CPU_BFREQ']);
        }

        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $arResult['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        if (!empty($arSystem['PROP_CPU_VCORE'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
        }
        if (!empty($arSystem['PROP_CPU_CONFIG'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
        }
        $arResult['NAME'] .= '</span>, ';

        $arResult['NAME'] .= $arSystem['PROP_RAM'];
        if (!empty($arSystem['PROP_RAM_FREQ'])) {
            $arResult['NAME'] .= '@' . $arSystem['PROP_RAM_FREQ'];
        }
        $ocRam = 0;
        if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
            $ocRam = $this->_percent($arSystem['PROP_RAM_FREQ'], $arSystem['PROP_RAM_BFREQ']);
        }

        if ($ocRam > 0) {
            $ocRam = '+' . $ocRam;
            $arResult['NAME'] .= '<span class="oc"> ' . $ocRam . '%</span>';
        }
        if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
        }
        $arResult['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

        if (empty($arSystem['PROP_ACTUAL_FOR']) && $arSystem['PROP_ACTUAL'] || !empty($arSystem['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $arSystem['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $arSystem['PROP_GPU_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $arSystem['PROP_GPU_FIRM_PASSIVE_COLOR'];
        }
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareCPU_RAMs(&$arResult) {
        $arSystem = &$arResult['SYSTEM'];
        $arTestType = &$arResult['TEST_TYPE'];
        $arResult['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
        if (!empty($arSystem['PROP_CPU_FREQ'])) {
            $arResult['NAME'] .= '@' . $arSystem['PROP_CPU_FREQ'];
        }
        $ocCore = 0;
        if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
            $ocCore = $this->_percent($arSystem['PROP_CPU_FREQ'], $arSystem['PROP_CPU_BFREQ']);
        }

        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $arResult['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        if (!empty($arSystem['PROP_CPU_VCORE'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
        }
        if (!empty($arSystem['PROP_CPU_CONFIG'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
        }
        $arResult['NAME'] .= '</span>, ';

        $arResult['NAME'] .= $arSystem['PROP_RAM'];
        if (!empty($arSystem['PROP_RAM_FREQ'])) {
            $arResult['NAME'] .= '@' . $arSystem['PROP_RAM_FREQ'];
        }
        $ocRam = 0;
        if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
            $ocRam = $this->_percent($arSystem['PROP_RAM_FREQ'], $arSystem['PROP_RAM_BFREQ']);
        }

        if ($ocRam > 0) {
            $ocRam = '+' . $ocRam;
            $arResult['NAME'] .= '<span class="oc"> ' . $ocRam . '%</span>';
        }
        if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
            $arResult['NAME'] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
        }
        $arResult['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

        if (empty($arSystem['PROP_ACTUAL_FOR']) && $arSystem['PROP_ACTUAL'] || !empty($arSystem['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $arSystem['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $arSystem['PROP_CPU_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $arSystem['PROP_CPU_FIRM_PASSIVE_COLOR'];
        }
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareHDDs(&$arResult) {
        $arSystem = &$arResult['SYSTEM'];
        $arTestType = &$arResult['TEST_TYPE'];
        $arResult['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_HD_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_HD'] . '</span> ';

        $arResult['NAME'] .= $arSystem['PROP_HD_CAPACITY'];
        $arResult['NAME'] .= ' <span class="comment">' . $arSystem['PROP_HD_INTERFACE'] . ', ' . $arSystem['PROP_HD_CHIPSET'] . '</span>';

        $arResult['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

        if (empty($arSystem['PROP_ACTUAL_FOR']) && $arSystem['PROP_ACTUAL'] || !empty($arSystem['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $arSystem['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $arSystem['PROP_HD_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $arSystem['PROP_HD_FIRM_PASSIVE_COLOR'];
        }
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
