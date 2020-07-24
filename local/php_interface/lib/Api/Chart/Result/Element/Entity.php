<?php

namespace Api\Chart\Result\Element;

/**
 * Class \Api\Chart\Result\Element\Entity
 * 
 * @method int getId()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method mixed getInfo()
 * @method $this setInfo(mixed $mixedInfo)
 * @method mixed getTestTypeId()
 * @method mixed getTestId()
 * @method $this setTestId(mixed $mixedTestId)
 * @method mixed getSystemId()
 * @method $this setSystemId(mixed $mixedSystemId)
 * @method mixed getResult()
 * @method $this setResult(mixed $mixedResult)
 * @method mixed getResult2()
 * @method $this setResult2(mixed $mixedResult2)
 * @method mixed getResult3()
 * @method $this setResult3(mixed $mixedResult3)
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    public static function getModel() {
        return Model::class;
    }

    public function prepareData() {


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
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareGPUs() {
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

}
