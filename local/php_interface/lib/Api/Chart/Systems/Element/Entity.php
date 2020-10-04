<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Entity
 * 
 * @method mixed getId()
 * @method mixed getIblockId()
 * @method mixed getCpu()
 * @method $this setCpu(mixed $mixedCpu)
 * @method mixed getCpuFirmId()
 * @method $this setCpuFirmId(mixed $mixedCpuFirmId)
 * @method mixed getCpuFreq()
 * @method $this setCpuFreq(mixed $mixedCpuFreq)
 * @method mixed getCpuBfreq()
 * @method $this setCpuBfreq(mixed $mixedCpuBfreq)
 * @method mixed getCpuConfig()
 * @method $this setCpuConfig(mixed $mixedCpuConfig)
 * @method mixed getCpuVcore()
 * @method $this setCpuVcore(mixed $mixedCpuVcore)
 * @method mixed getRam()
 * @method $this setRam(mixed $mixedRam)
 * @method mixed getRamFreq()
 * @method $this setRamFreq(mixed $mixedRamFreq)
 * @method mixed getRamBfreq()
 * @method $this setRamBfreq(mixed $mixedRamBfreq)
 * @method mixed getRamTimings()
 * @method $this setRamTimings(mixed $mixedRamTimings)
 * @method mixed getGpu()
 * @method $this setGpu(mixed $mixedGpu)
 * @method mixed getGpuFirmId()
 * @method $this setGpuFirmId(mixed $mixedGpuFirmId)
 * @method mixed getGpuCoreFreq()
 * @method $this setGpuCoreFreq(mixed $mixedGpuCoreFreq)
 * @method mixed getGpuCoreBfreq()
 * @method $this setGpuCoreBfreq(mixed $mixedGpuCoreBfreq)
 * @method mixed getGpuVramFreq()
 * @method $this setGpuVramFreq(mixed $mixedGpuVramFreq)
 * @method mixed getGpuVramBfreq()
 * @method $this setGpuVramBfreq(mixed $mixedGpuVramBfreq)
 * @method mixed getGpuVcore()
 * @method $this setGpuVcore(mixed $mixedGpuVcore)
 * @method mixed getGpuPcie()
 * @method $this setGpuPcie(mixed $mixedGpuPcie)
 * @method mixed getGpuCf()
 * @method $this setGpuCf(mixed $mixedGpuCf)
 * @method mixed getHd()
 * @method $this setHd(mixed $mixedHd)
 * @method mixed getHdCapacity()
 * @method $this setHdCapacity(mixed $mixedHdCapacity)
 * @method mixed getHdInterface()
 * @method $this setHdInterface(mixed $mixedHdInterface)
 * @method mixed getHdChipset()
 * @method $this setHdChipset(mixed $mixedHdChipset)
 * @method mixed getActual()
 * @method $this setActual(mixed $mixedActual)
 * @method mixed getGpuSli()
 * @method $this setGpuSli(mixed $mixedGpuSli)
 * @method mixed getHdFirmId()
 * @method $this setHdFirmId(mixed $mixedHdFirmId)
 * @method mixed getActualFor()
 * @method $this setActualFor(mixed $mixedActualFor)
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var \Api\Chart\Firm\Entity 
     */
    protected $obCpuFirm = null;

    /**
     *
     * @var \Api\Chart\Firm\Entity 
     */
    protected $obGpuFirm = null;

    /**
     *
     * @var \Api\Chart\Firm\Entity 
     */
    protected $obHdFirm = null;

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Chart\Firm\Entity 
     */
    public function getCpuFirm() {
        return $this->obCpuFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return $this
     */
    public function setCpuFirm(\Api\Chart\Firm\Entity $obFirm) {
        $this->obCpuFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Firm\Entity 
     */
    public function getGpuFirm() {
        return $this->obGpuFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return $this
     */
    public function setGpuFirm(\Api\Chart\Firm\Entity $obFirm) {
        $this->obCpuFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Firm\Entity 
     */
    public function getHdFirm() {
        return $this->obHdFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return $this
     */
    public function setHdFirm(\Api\Chart\Firm\Entity $obFirm) {
        $this->obCpuFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Section\Entity $obTestType
     * @return string
     */
    public function getFullName(\Api\Chart\Tests\Section\Entity $obTestType) {

        $strName = $this->getName();

        switch ($obTestType->getCode()) {
            case 'GPU':
                $strName = $this->_prepareGPUs();
                break;
            case 'CPU':
            case 'RAM':
                $strName = $this->_prepareCPU_RAMs();
                break;
            case 'DRIVE':
                $strName = $this->_prepareHDDs();
                break;
        }

        return $strName;
    }

    public function prepareData() {


        $arResult['COLOR'] = '127, 127, 127';

        $strName = '<span';
        if (!empty($arResult['INFO']))
            $strName .= ' title="' . nl2br($arResult['INFO']) . '"';
        $strName .= '>';

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
        $strName .= '</span>';
    }

    /**
     * 
     * @return string
     */
    private function _appendGpuFreq() {
        $strName = '';
        if (!empty($this->getGpuCoreFreq())) {
            $strName .= '@' . $this->getGpuCoreFreq();
            if (!empty($this->getGpuVramFreq())) {
                $strName .= '/' . $this->getGpuVramFreq();
            }
        }
        return $strName;
    }

    /**
     * 
     * @return float
     */
    private function _getGpuOcCoreFreq() {
        $ocCore = 0.0;
        if (!empty($this->getGpuCoreBfreq()) && $this->getGpuCoreBfreq() != $this->getGpuCoreFreq()) {
            $ocCore = $this->_percent($this->getGpuCoreFreq(), $this->getGpuCoreBfreq());
        }
        return $ocCore;
    }

    /**
     * 
     * @return float
     */
    private function _getGpuOcVRamFreq() {
        $ocRam = 0.0;
        if (!empty($this->getGpuVramBfreq()) && $this->getGpuVramBfreq() != $this->getGpuVramFreq()) {
            $ocRam = $this->_percent($this->getGpuVramFreq(), $this->getGpuVramBfreq());
        }
        return $ocRam;
    }

    /**
     * 
     * @return string
     */
    private function _appendGpuOc() {
        $strName = '';
        $ocCore = $this->_getGpuOcCoreFreq();
        $ocRam = $this->_getGpuOcVRamFreq();
        if ($ocCore > 0 || $ocRam > 0) {
            if ($ocCore > 0)
                $ocCore = '+' . $ocCore;
            $strName .= '<span class="oc"> ' . $ocCore . '%';
            if ($ocRam) {
                if ($ocRam > 0)
                    $ocRam = '+' . $ocRam;
                $strName .= '/' . $ocRam . '%';
            }
            $strName .= '</span>';
        }


        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendGpuVcore() {
        $strName = '';
        if (!empty($this->getGpuVcore())) {
            $strName .= '<span class="comment">' . $this->getGpuVcore() . 'V</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendPcie() {
        $strName = '';
        if (!empty($this['PROP_GPU_PCIE'])) {
            $strName .= '<span class="comment">PCI-E ' . $this->getGpuPcie() . '</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendMGpu() {
        $strName = '';
        if ($this->getGpuCf())
            $strName .= '<span class="oc"> CF</span>';
        if ($this->getGpuSli())
            $strName .= '<span class="oc"> SLI</span>';
        return $strName;
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareGPUs() {
//        $arTestType = &$arResult['TEST_TYPE'];
        $strName = '';
        $strName .= '<span style="color: rgb(' . $this->getGpuFirm()->getTextColor() . ')">' . $this->getGpu();
        $strName .= $this->_appendGpuFreq();
        $strName .= $this->_appendGpuOc();
        $strName .= $this->_appendGpuVcore();
        $strName .= $this->_appendPcie();
        $strName .= '</span>';
        $strName .= ', ';

        $strName .= '<span style="color: rgb(' . $this['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $this['PROP_CPU'];
        if (!empty($this['PROP_CPU_FREQ'])) {
            $strName .= '@' . $this['PROP_CPU_FREQ'];
        }
        $ocCore = 0;
        if (!empty($this['PROP_CPU_BFREQ']) && $this['PROP_CPU_BFREQ'] != $this['PROP_CPU_FREQ']) {
            $ocCore = $this->_percent($this['PROP_CPU_FREQ'], $this['PROP_CPU_BFREQ']);
        }

        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $strName .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        if (!empty($this['PROP_CPU_VCORE'])) {
            $strName .= '<span class="comment">' . $this['PROP_CPU_VCORE'] . 'V</span>';
        }
        if (!empty($this['PROP_CPU_CONFIG'])) {
            $strName .= '<span class="comment">' . $this['PROP_CPU_CONFIG'] . '</span>';
        }
        $strName .= '</span>, ';

        $strName .= $this['PROP_RAM'];
        if (!empty($this['PROP_RAM_FREQ'])) {
            $strName .= '@' . $this['PROP_RAM_FREQ'];
        }
        $ocRam = 0;
        if (!empty($this['PROP_RAM_BFREQ']) && $this['PROP_RAM_BFREQ'] != $this['PROP_RAM_FREQ']) {
            $ocRam = $this->_percent($this['PROP_RAM_FREQ'], $this['PROP_RAM_BFREQ']);
        }

        if ($ocRam > 0) {
            $ocRam = '+' . $ocRam;
            $strName .= '<span class="oc"> ' . $ocRam . '%</span>';
        }
        if (!empty($this['PROP_RAM_TIMINGS'])) {
            $strName .= '<span class="comment">' . $this['PROP_RAM_TIMINGS'] . '</span>';
        }
        $strName .= '<a name="' . $arTestType['TYPE'] . '_' . $this['ID'] . '"></a>';

        if (empty($this['PROP_ACTUAL_FOR']) && $this['PROP_ACTUAL'] || !empty($this['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $this['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $this['PROP_GPU_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $this['PROP_GPU_FIRM_PASSIVE_COLOR'];
        }
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareCPU_RAMs(&$arResult) {
        $this = &$arResult['SYSTEM'];
        $arTestType = &$arResult['TEST_TYPE'];
        $strName .= '<span style="color: rgb(' . $this['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $this['PROP_CPU'];
        if (!empty($this['PROP_CPU_FREQ'])) {
            $strName .= '@' . $this['PROP_CPU_FREQ'];
        }
        $ocCore = 0;
        if (!empty($this['PROP_CPU_BFREQ']) && $this['PROP_CPU_BFREQ'] != $this['PROP_CPU_FREQ']) {
            $ocCore = $this->_percent($this['PROP_CPU_FREQ'], $this['PROP_CPU_BFREQ']);
        }

        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $strName .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        if (!empty($this['PROP_CPU_VCORE'])) {
            $strName .= '<span class="comment">' . $this['PROP_CPU_VCORE'] . 'V</span>';
        }
        if (!empty($this['PROP_CPU_CONFIG'])) {
            $strName .= '<span class="comment">' . $this['PROP_CPU_CONFIG'] . '</span>';
        }
        $strName .= '</span>, ';

        $strName .= $this['PROP_RAM'];
        if (!empty($this['PROP_RAM_FREQ'])) {
            $strName .= '@' . $this['PROP_RAM_FREQ'];
        }
        $ocRam = 0;
        if (!empty($this['PROP_RAM_BFREQ']) && $this['PROP_RAM_BFREQ'] != $this['PROP_RAM_FREQ']) {
            $ocRam = $this->_percent($this['PROP_RAM_FREQ'], $this['PROP_RAM_BFREQ']);
        }

        if ($ocRam > 0) {
            $ocRam = '+' . $ocRam;
            $strName .= '<span class="oc"> ' . $ocRam . '%</span>';
        }
        if (!empty($this['PROP_RAM_TIMINGS'])) {
            $strName .= '<span class="comment">' . $this['PROP_RAM_TIMINGS'] . '</span>';
        }
        $strName .= '<a name="' . $arTestType['TYPE'] . '_' . $this['ID'] . '"></a>';

        if (empty($this['PROP_ACTUAL_FOR']) && $this['PROP_ACTUAL'] || !empty($this['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $this['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $this['PROP_CPU_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $this['PROP_CPU_FIRM_PASSIVE_COLOR'];
        }
    }

    /**
     * 
     * @param array $arResult
     */
    private function _prepareHDDs(&$arResult) {
        $this = &$arResult['SYSTEM'];
        $arTestType = &$arResult['TEST_TYPE'];
        $strName .= '<span style="color: rgb(' . $this['PROP_HD_FIRM_TEXT_COLOR'] . ')">' . $this['PROP_HD'] . '</span> ';

        $strName .= $this['PROP_HD_CAPACITY'];
        $strName .= ' <span class="comment">' . $this['PROP_HD_INTERFACE'] . ', ' . $this['PROP_HD_CHIPSET'] . '</span>';

        $strName .= '<a name="' . $arTestType['TYPE'] . '_' . $this['ID'] . '"></a>';

        if (empty($this['PROP_ACTUAL_FOR']) && $this['PROP_ACTUAL'] || !empty($this['PROP_ACTUAL_FOR']) && in_array($arTestType['ID'], $this['PROP_ACTUAL_FOR'])) {
            $arResult['COLOR'] = $this['PROP_HD_FIRM_ACTIVE_COLOR'];
        } else {
            $arResult['COLOR'] = $this['PROP_HD_FIRM_PASSIVE_COLOR'];
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
