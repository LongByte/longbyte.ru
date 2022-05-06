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
class Entity extends \Api\Core\Iblock\Element\Entity
{

    protected ?\Api\Chart\Firm\Entity $obCpuFirm = null;
    protected ?\Api\Chart\Firm\Entity $obGpuFirm = null;
    private ?\Api\Chart\Tests\Section\Entity $_obTestType = null;
    protected ?\Api\Chart\Firm\Entity $obHdFirm = null;

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function getCpuFirm(): ?\Api\Chart\Firm\Entity
    {
        return $this->obCpuFirm;
    }

    public function setCpuFirm(\Api\Chart\Firm\Entity $obFirm): self
    {
        $this->obCpuFirm = $obFirm;
        return $this;
    }

    public function getGpuFirm(): ?\Api\Chart\Firm\Entity
    {
        return $this->obGpuFirm;
    }

    public function setGpuFirm(\Api\Chart\Firm\Entity $obFirm): self
    {
        $this->obGpuFirm = $obFirm;
        return $this;
    }

    public function getHdFirm(): ?\Api\Chart\Firm\Entity
    {
        return $this->obHdFirm;
    }

    public function setHdFirm(\Api\Chart\Firm\Entity $obFirm): self
    {
        $this->obHdFirm = $obFirm;
        return $this;
    }

    private function _getTestType(): ?\Api\Chart\Tests\Section\Entity
    {
        return $this->_obTestType;
    }

    public function getFullName(\Api\Chart\Tests\Section\Entity $obTestType): string
    {

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

    public function getClearFullName(\Api\Chart\Tests\Section\Entity $obTestType): string
    {
        return \strip_tags($this->getFullName($obTestType));
    }

    private function _prepareGPUs(): string
    {
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

    private function _prepareCPU_RAMs(): string
    {
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

    private function _prepareHDDs(): string
    {
        $strName = '';
        $strName .= '<span style="color: rgb(' . $this->getHdFirm()->getTextColor() . ')">' . $this->getHd() . '</span> ';

        $strName .= $this->getHdCapacity();
        $strName .= ' <span class="comment">' . $this->getHdInterface() . ', ' . $this->getHdChipset() . '</span>';

        $strName .= '<a name="' . $this->_getTestType()->getCode() . '_' . $this->getId() . '"></a>';

        return $strName;
    }

    private function _appendGpuFreq(): string
    {
        $strName = '';
        if (!empty($this->getGpuCoreFreq())) {
            $strName .= '@' . $this->getGpuCoreFreq();
            if (!empty($this->getGpuVramFreq())) {
                $strName .= '/' . $this->getGpuVramFreq();
            }
        }
        return $strName;
    }

    private function _getGpuOcCoreFreq(): string
    {
        $ocCore = 0.0;
        if (!empty($this->getGpuCoreBfreq()) && $this->getGpuCoreBfreq() != $this->getGpuCoreFreq()) {
            $ocCore = $this->_percent($this->getGpuCoreFreq(), $this->getGpuCoreBfreq());
        }
        return $ocCore;
    }

    private function _getGpuOcVRamFreq(): string
    {
        $ocRam = 0.0;
        if (!empty($this->getGpuVramBfreq()) && $this->getGpuVramBfreq() != $this->getGpuVramFreq()) {
            $ocRam = $this->_percent($this->getGpuVramFreq(), $this->getGpuVramBfreq());
        }
        return $ocRam;
    }

    private function _appendGpuOc(): string
    {
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

    private function _appendGpuVcore(): string
    {
        $strName = '';
        if (!empty($this->getGpuVcore())) {
            $strName .= '<span class="comment">' . $this->getGpuVcore() . 'V</span>';
        }
        return $strName;
    }

    private function _appendPcie(): string
    {
        $strName = '';
        if (!empty($this->getGpuPcie())) {
            $strName .= '<span class="comment">PCI-E ' . $this->getGpuPcie() . '</span>';
        }
        return $strName;
    }

    private function _appendMGpu(): string
    {
        $strName = '';
        if ($this->getGpuCf())
            $strName .= '<span class="oc"> CF</span>';
        if ($this->getGpuSli())
            $strName .= '<span class="oc"> SLI</span>';
        return $strName;
    }

    private function _appendCpuFreq(): string
    {
        $strName = '';
        if (!empty($this->getCpuFreq())) {
            $strName .= '@' . $this->getCpuFreq();
        }
        return $strName;
    }

    private function _appendCpuOc(): string
    {
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

    private function _appendCpuConfig(): string
    {
        $strName = '';
        if (!empty($this->getCpuConfig())) {
            $strName .= '<span class="comment">' . $this->getCpuConfig() . '</span>';
        }
        return $strName;
    }

    private function _appendCpuVcore(): string
    {
        $strName = '';
        if (!empty($this->getCpuVcore())) {
            $strName .= '<span class="comment">' . $this->getCpuVcore() . 'V</span>';
        }
        return $strName;
    }

    private function _appendRamFreq(): string
    {
        $strName = '';
        if (!empty($this->getRamFreq())) {
            $strName .= '@' . $this->getRamFreq();
        }
        return $strName;
    }

    private function _appendRamOc(): string
    {
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

    private function _appendRamTimings(): string
    {
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
    private function _percent($va1ue_1, $va1ue_2, int $presicion = 0): int
    {
        return round(($va1ue_1 / $va1ue_2 - 1) * 100, $presicion);
    }

}
