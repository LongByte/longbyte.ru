<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \Api\Chart\Systems\Element\Entity
 * 
 * @method mixed getId()
 * @method mixed getIblockId()
 * @method mixed getCpu()
 * @method mixed getCpuFirmId()
 * @method mixed getCpuFreq()
 * @method mixed getCpuBfreq()
 * @method mixed getCpuConfig()
 * @method mixed getCpuVcore()
 * @method mixed getRam()
 * @method mixed getRamFreq()
 * @method mixed getRamBfreq()
 * @method mixed getRamTimings()
 * @method mixed getGpu()
 * @method mixed getGpuFirmId()
 * @method mixed getGpuCoreFreq()
 * @method mixed getGpuCoreBfreq()
 * @method mixed getGpuVramFreq()
 * @method mixed getGpuVramBfreq()
 * @method mixed getGpuVcore()
 * @method mixed getGpuPcie()
 * @method mixed getGpuCf()
 * @method mixed getHd()
 * @method mixed getHdCapacity()
 * @method mixed getHdInterface()
 * @method mixed getHdChipset()
 * @method mixed getActual()
 * @method mixed getGpuSli()
 * @method mixed getHdFirmId()
 * @method mixed getActualFor()
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
     * @var \Api\Chart\Tests\Section\Entity 
     */
    private $_obTestType = null;

    /**
     *
     * @var \Api\Chart\Firm\Entity 
     */
    protected $obHdFirm = null;

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
     * @return \Api\Chart\Firm\Entity 
     */
    public function getCpuFirm(): ?\Api\Chart\Firm\Entity {
        return $this->obCpuFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return \self
     */
    public function setCpuFirm(\Api\Chart\Firm\Entity $obFirm): self {
        $this->obCpuFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Firm\Entity 
     */
    public function getGpuFirm(): ?\Api\Chart\Firm\Entity {
        return $this->obGpuFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return \self
     */
    public function setGpuFirm(\Api\Chart\Firm\Entity $obFirm): self {
        $this->obGpuFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Firm\Entity 
     */
    public function getHdFirm(): ?\Api\Chart\Firm\Entity {
        return $this->obHdFirm;
    }

    /**
     * 
     * @param \Api\Chart\Firm\Entity $obFirm
     * @return \self
     */
    public function setHdFirm(\Api\Chart\Firm\Entity $obFirm): self {
        $this->obHdFirm = $obFirm;
        return $this;
    }

    /**
     * 
     * @return \Api\Chart\Tests\Section\Entity 
     */
    private function _getTestType(): ?\Api\Chart\Tests\Section\Entity {
        return $this->_obTestType;
    }

    /**
     * 
     * @param \Api\Chart\Tests\Section\Entity $obTestType
     * @return string
     */
    public function getFullName(\Api\Chart\Tests\Section\Entity $obTestType): string {

        $strName = $this->getName();
        $this->_obTestType = $obTestType;

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

    /**
     * 
     * @param \Api\Chart\Tests\Section\Entity $obTestType
     * @return string
     */
    public function getClearFullName(\Api\Chart\Tests\Section\Entity $obTestType): string {
        return \strip_tags($this->getFullName($obTestType));
    }

    /**
     * 
     * @return string
     */
    private function _prepareGPUs(): string {
        $strName = '';
        $strName .= '<span style="color: rgb(' . $this->getGpuFirm()->getTextColor() . ')">' . $this->getGpu();
        $strName .= $this->_appendGpuFreq();
        $strName .= $this->_appendGpuOc();
        $strName .= $this->_appendGpuVcore();
        $strName .= $this->_appendPcie();
        $strName .= $this->_appendMGpu();
        $strName .= '</span>';
        $strName .= ', ';

        $strName .= '<span style="color: rgb(' . $this->getCpuFirm()->getTextColor() . ')">' . $this->getCpu();
        $strName .= $this->_appendCpuFreq();
        $strName .= $this->_appendCpuOc();
        $strName .= $this->_appendCpuVcore();
        $strName .= $this->_appendCpuConfig();
        $strName .= '</span>, ';

        $strName .= $this->getRam();
        $strName .= $this->_appendRamFreq();
        $strName .= $this->_appendRamOc();
        $strName .= $this->_appendRamTimings();

        $strName .= '<a name="' . $this->_getTestType()->getCode() . '_' . $this->getId() . '"></a>';

        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _prepareCPU_RAMs(): string {
        $strName = '';
        $strName .= '<span style="color: rgb(' . $this->getCpuFirm()->getTextColor() . ')">' . $this->getCpu();
        $strName .= $this->_appendCpuFreq();
        $strName .= $this->_appendCpuOc();
        $strName .= $this->_appendCpuVcore();
        $strName .= $this->_appendCpuConfig();
        $strName .= '</span>, ';

        $strName .= $this->getRam();
        $strName .= $this->_appendRamFreq();
        $strName .= $this->_appendRamOc();
        $strName .= $this->_appendRamTimings();

        $strName .= '<a name="' . $this->_getTestType()->getCode() . '_' . $this->getId() . '"></a>';

        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _prepareHDDs(): string {
        $strName = '';
        $strName .= '<span style="color: rgb(' . $this->getHdFirm()->getTextColor() . ')">' . $this->getHd() . '</span> ';

        $strName .= $this->getHdCapacity();
        $strName .= ' <span class="comment">' . $this->getHdInterface() . ', ' . $this->getHdChipset() . '</span>';

        $strName .= '<a name="' . $this->_getTestType()->getCode() . '_' . $this->getId() . '"></a>';

        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendGpuFreq(): string {
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
    private function _getGpuOcCoreFreq(): string {
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
    private function _getGpuOcVRamFreq(): string {
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
    private function _appendGpuOc(): string {
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
    private function _appendGpuVcore(): string {
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
    private function _appendPcie(): string {
        $strName = '';
        if (!empty($this->getGpuPcie())) {
            $strName .= '<span class="comment">PCI-E ' . $this->getGpuPcie() . '</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendMGpu(): string {
        $strName = '';
        if ($this->getGpuCf())
            $strName .= '<span class="oc"> CF</span>';
        if ($this->getGpuSli())
            $strName .= '<span class="oc"> SLI</span>';
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendCpuFreq(): string {
        $strName = '';
        if (!empty($this->getCpuFreq())) {
            $strName .= '@' . $this->getCpuFreq();
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendCpuOc(): string {
        $strName = '';
        $ocCore = 0;
        if (!empty($this->getCpuBfreq()) && $this->getCpuBfreq() != $this->getCpuFreq()) {
            $ocCore = $this->_percent($this->getCpuFreq(), $this->getCpuBfreq());
        }
        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $strName .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendCpuConfig(): string {
        $strName = '';
        if (!empty($this->getCpuConfig())) {
            $strName .= '<span class="comment">' . $this->getCpuConfig() . '</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendCpuVcore(): string {
        $strName = '';
        if (!empty($this->getCpuVcore())) {
            $strName .= '<span class="comment">' . $this->getCpuVcore() . 'V</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendRamFreq(): string {
        $strName = '';
        if (!empty($this->getRamFreq())) {
            $strName .= '@' . $this->getRamFreq();
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendRamOc(): string {
        $strName = '';
        $ocCore = 0;
        if (!empty($this->getRamBfreq()) && $this->getRamBfreq() != $this->getRamFreq()) {
            $ocCore = $this->_percent($this->getRamFreq(), $this->getRamBfreq());
        }
        if ($ocCore > 0) {
            $ocCore = '+' . $ocCore;
            $strName .= '<span class="oc"> ' . $ocCore . '%</span>';
        }
        return $strName;
    }

    /**
     * 
     * @return string
     */
    private function _appendRamTimings(): string {
        $strName = '';
        if (!empty($this->getRamTimings())) {
            $strName .= '<span class="comment">' . $this->getRamTimings() . '</span>';
        }
        return $strName;
    }

    /**
     * 
     * @param int|float $va1ue_1
     * @param int|float $va1ue_2
     * @param int $presicion
     * @return int
     */
    private function _percent($va1ue_1, $va1ue_2, int $presicion = 0): int {
        return round(($va1ue_1 / $va1ue_2 - 1) * 100, $presicion);
    }

}
