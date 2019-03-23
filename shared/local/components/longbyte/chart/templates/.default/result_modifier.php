<?

$arResult['JS_DATA'] = array();

foreach ($arResult['TEST_TYPES'] as &$arTestType) {

    foreach ($arTestType['TESTS'] as $i => &$arTest) {
        $arDataTests = array();
        foreach ($arTest['ITEMS'] as $j => &$arItem) {

            if (floatval($arItem['RESULT']) == 0.0)
                continue;

            $arSystem = $arItem['SYSTEM'];
            $color = '127, 127, 127';

            $arItem['NAME'] = '<span';
            if (!empty($arItem['INFO']))
                $arItem['NAME'] .= ' title="' . nl2br($arItem['INFO']) . '"';
            $arItem['NAME'] .= '>';

            switch ($arTestType['TYPE']) {
                case 'GPU':
                    $arItem['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_GPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_GPU'];
                    $ocCore = 0;
                    $ocRam = 0;
                    if (!empty($arSystem['PROP_GPU_CORE_FREQ'])) {
                        $arItem['NAME'] .= '@' . $arSystem['PROP_GPU_CORE_FREQ'];
                        if (!empty($arSystem['PROP_GPU_VRAM_FREQ'])) {
                            $arItem['NAME'] .= '/' . $arSystem['PROP_GPU_VRAM_FREQ'];
                        }
                    }
                    if (!empty($arSystem['PROP_GPU_CORE_BFREQ']) && $arSystem['PROP_GPU_CORE_BFREQ'] != $arSystem['PROP_GPU_CORE_FREQ']) {
                        $ocCore = round(($arSystem['PROP_GPU_CORE_FREQ'] / $arSystem['PROP_GPU_CORE_BFREQ'] - 1) * 100);
                    }
                    if (!empty($arSystem['PROP_GPU_VRAM_BFREQ']) && $arSystem['PROP_GPU_VRAM_BFREQ'] != $arSystem['PROP_GPU_VRAM_FREQ']) {
                        $ocRam = round(($arSystem['PROP_GPU_VRAM_FREQ'] / $arSystem['PROP_GPU_VRAM_BFREQ'] - 1) * 100);
                    }
                    if ($ocCore || $ocRam) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem['NAME'] .= '<span class="oc"> ' . $ocCore . '%';
                        if ($ocRam) {
                            if ($ocRam > 0)
                                $ocRam = '+' . $ocRam;
                            $arItem['NAME'] .= '/' . $ocRam . '%';
                        }
                        $arItem['NAME'] .= '</span>';
                    }
                    if (!empty($arSystem['PROP_GPU_VCORE'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_GPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_GPU_PCIE'])) {
                        $arItem['NAME'] .= '<span class="comment">PCI-E ' . $arSystem['PROP_GPU_PCIE'] . '</span>';
                    }
                    $arItem['NAME'] .= '</span>';

                    if ($arSystem['PROP_GPU_CF'])
                        $arItem['NAME'] .= '<span class="oc"> CF</span>';
                    if ($arSystem['PROP_GPU_SLI'])
                        $arItem['NAME'] .= '<span class="oc"> SLI</span>';

                    $arItem['NAME'] .= ', ';

                    $arItem['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
                    if (!empty($arSystem['PROP_CPU_FREQ'])) {
                        $arItem['NAME'] .= '@' . $arSystem['PROP_CPU_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
                        $ocCore = round(($arSystem['PROP_CPU_FREQ'] / $arSystem['PROP_CPU_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_VCORE'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_CONFIG'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
                    }
                    $arItem['NAME'] .= '</span>, ';

                    $arItem['NAME'] .= $arSystem['PROP_RAM'];
                    if (!empty($arSystem['PROP_RAM_FREQ'])) {
                        $arItem['NAME'] .= '@' . $arSystem['PROP_RAM_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
                        $ocCore = round(($arSystem['PROP_RAM_FREQ'] / $arSystem['PROP_RAM_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
                    }
                    $arItem['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_GPU_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_GPU_FIRM_PASSIVE_COLOR'];
                    }

                    break;
                case 'CPU':
                case 'RAM':

                    $arItem['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
                    if (!empty($arSystem['PROP_CPU_FREQ'])) {
                        $arItem['NAME'] .= '@' . $arSystem['PROP_CPU_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
                        $ocCore = round(($arSystem['PROP_CPU_FREQ'] / $arSystem['PROP_CPU_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_VCORE'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_CONFIG'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
                    }
                    $arItem['NAME'] .= '</span>, ';

                    $arItem['NAME'] .= $arSystem['PROP_RAM'];
                    if (!empty($arSystem['PROP_RAM_FREQ'])) {
                        $arItem['NAME'] .= '@' . $arSystem['PROP_RAM_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
                        $ocCore = round(($arSystem['PROP_RAM_FREQ'] / $arSystem['PROP_RAM_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem['NAME'] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
                        $arItem['NAME'] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
                    }
                    $arItem['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_CPU_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_CPU_FIRM_PASSIVE_COLOR'];
                    }

                    break;
                case 'DRIVE':
                    $arItem['NAME'] .= '<span style="color: rgb(' . $arSystem['PROP_HD_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_HD'] . '</span> ';

                    $arItem['NAME'] .= $arSystem['PROP_HD_CAPACITY'];
                    $arItem['NAME'] .= ' <span class="comment">' . $arSystem['PROP_HD_INTERFACE'] . ', ' . $arSystem['PROP_HD_CHIPSET'] . '</span>';

                    $arItem['NAME'] .= '<a name="' . $arTestType['TYPE'] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_HD_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_HD_FIRM_PASSIVE_COLOR'];
                    }
                    break;
            }
            $arItem['NAME'] .= '</span>';

            $arRes = array($arItem['RESULT']);
            if (!empty($arItem['RESULT2']))
                $arRes[] = $arItem['RESULT2'];
            if (!empty($arItem['RESULT3']))
                $arRes[] = $arItem['RESULT3'];

            $arDataItem = array(
                $arItem['NAME']
            );
            foreach (explode(',', $color) as $colorPart) {
                $arDataItem[] = (int) trim($colorPart);
            }
            foreach ($arRes as $oneRes) {
                $arDataItem[] = (int) $oneRes;
            }
            $arDataTests[] = $arDataItem;
        }
        unset($arItem);
        $arResult['JS_DATA'][] = $arDataTests;
    }
    unset($arTest);
}
unset($arTestType);
?>