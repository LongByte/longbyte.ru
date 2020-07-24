<?php

namespace Api\Chart\Systems\Element;

/**
 * Class \ Api\Chart\Systems\Element\Entity
 * 
 * @method mixed getId()
 * @method mixed getIblockId()
 * @method mixed getCpu()
 * @method $this setCpu(mixed $mixedCpu)
 * @method mixed getCpuFirm()
 * @method $this setCpuFirm(mixed $mixedCpuFirm)
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
 * @method mixed getGpuFirm()
 * @method $this setGpuFirm(mixed $mixedGpuFirm)
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
 * @method mixed getHdFirm()
 * @method $this setHdFirm(mixed $mixedHdFirm)
 * @method mixed getActualFor()
 * @method $this setActualFor(mixed $mixedActualFor)
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    public static function getModel() {
        return Model::class;
    }

}
