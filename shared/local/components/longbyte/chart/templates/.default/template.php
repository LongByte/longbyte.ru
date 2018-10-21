
<script>


    var groups = [
        [0]
    ];

    var serviceCount = <?= count($arResult['TEST_TYPES']) ?>;
//            var defaultCol = 96;
    var lineCol = 'A0A0A0';
    var axisCol = '808080';

    var data = [
<? foreach ($arResult['TEST_TYPES'] as &$arTestType): ?>
    <? foreach ($arTestType['TESTS'] as $i => &$arTest): ?>
            [
        <? foreach ($arTest["ITEMS"] as $j => &$arItem): ?>
            <?
            if (floatval($arItem['RESULT']) == 0.0)
                continue;

            $arSystem = $arItem['SYSTEM'];
            $color = '127, 127, 127';

            $arItem["NAME"] = '<span';
            if (!empty($arItem['INFO']))
                $arItem["NAME"] .= ' title="' . str_replace("\r\n", "<br>", $arItem['INFO']) . '"';
            $arItem["NAME"] .= '>';

            switch ($arTestType["TYPE"]) {
                case "GPU":
                    $arItem["NAME"] .= '<span style="color: rgb(' . $arSystem['PROP_GPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_GPU'];
                    $ocCore = 0;
                    $ocRam = 0;
                    if (!empty($arSystem['PROP_GPU_CORE_FREQ'])) {
                        $arItem["NAME"] .= '@' . $arSystem['PROP_GPU_CORE_FREQ'];
                        if (!empty($arSystem['PROP_GPU_VRAM_FREQ'])) {
                            $arItem["NAME"] .= '/' . $arSystem['PROP_GPU_VRAM_FREQ'];
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
                        $arItem["NAME"] .= '<span class="oc"> ' . $ocCore . '%';
                        if ($ocRam) {
                            if ($ocRam > 0)
                                $ocRam = '+' . $ocRam;
                            $arItem["NAME"] .= '/' . $ocRam . '%';
                        }
                        $arItem["NAME"] .= '</span>';
                    }
                    if (!empty($arSystem['PROP_GPU_VCORE'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_GPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_GPU_PCIE'])) {
                        $arItem["NAME"] .= '<span class="comment">PCI-E ' . $arSystem['PROP_GPU_PCIE'] . '</span>';
                    }
                    $arItem["NAME"] .= '</span>';

                    if ($arSystem['PROP_GPU_CF'])
                        $arItem["NAME"] .= '<span class="oc"> CF</span>';
                    if ($arSystem['PROP_GPU_SLI'])
                        $arItem["NAME"] .= '<span class="oc"> SLI</span>';

                    $arItem["NAME"] .= ', ';

                    $arItem["NAME"] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
                    if (!empty($arSystem['PROP_CPU_FREQ'])) {
                        $arItem["NAME"] .= '@' . $arSystem['PROP_CPU_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
                        $ocCore = round(($arSystem['PROP_CPU_FREQ'] / $arSystem['PROP_CPU_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem["NAME"] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_VCORE'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_CONFIG'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
                    }
                    $arItem["NAME"] .= '</span>, ';

                    $arItem["NAME"] .= $arSystem['PROP_RAM'];
                    if (!empty($arSystem['PROP_RAM_FREQ'])) {
                        $arItem["NAME"] .= '@' . $arSystem['PROP_RAM_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
                        $ocCore = round(($arSystem['PROP_RAM_FREQ'] / $arSystem['PROP_RAM_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem["NAME"] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
                    }
                    $arItem["NAME"] .= '<a name="' . $arTestType["TYPE"] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_GPU_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_GPU_FIRM_PASSIVE_COLOR'];
                    }

                    break;
                case "CPU":
                case "RAM":

                    $arItem["NAME"] .= '<span style="color: rgb(' . $arSystem['PROP_CPU_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_CPU'];
                    if (!empty($arSystem['PROP_CPU_FREQ'])) {
                        $arItem["NAME"] .= '@' . $arSystem['PROP_CPU_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_CPU_BFREQ']) && $arSystem['PROP_CPU_BFREQ'] != $arSystem['PROP_CPU_FREQ']) {
                        $ocCore = round(($arSystem['PROP_CPU_FREQ'] / $arSystem['PROP_CPU_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem["NAME"] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_VCORE'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_CPU_VCORE'] . 'V</span>';
                    }
                    if (!empty($arSystem['PROP_CPU_CONFIG'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_CPU_CONFIG'] . '</span>';
                    }
                    $arItem["NAME"] .= '</span>, ';

                    $arItem["NAME"] .= $arSystem['PROP_RAM'];
                    if (!empty($arSystem['PROP_RAM_FREQ'])) {
                        $arItem["NAME"] .= '@' . $arSystem['PROP_RAM_FREQ'];
                    }
                    $ocCore = 0;
                    if (!empty($arSystem['PROP_RAM_BFREQ']) && $arSystem['PROP_RAM_BFREQ'] != $arSystem['PROP_RAM_FREQ']) {
                        $ocCore = round(($arSystem['PROP_RAM_FREQ'] / $arSystem['PROP_RAM_BFREQ'] - 1) * 100);
                    }

                    if ($ocCore) {
                        if ($ocCore > 0)
                            $ocCore = '+' . $ocCore;
                        $arItem["NAME"] .= '<span class="oc"> ' . $ocCore . '%</span>';
                    }
                    if (!empty($arSystem['PROP_RAM_TIMINGS'])) {
                        $arItem["NAME"] .= '<span class="comment">' . $arSystem['PROP_RAM_TIMINGS'] . '</span>';
                    }
                    $arItem["NAME"] .= '<a name="' . $arTestType["TYPE"] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_CPU_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_CPU_FIRM_PASSIVE_COLOR'];
                    }

                    break;
                case "DRIVE":
                    $arItem["NAME"] .= '<span style="color: rgb(' . $arSystem['PROP_HD_FIRM_TEXT_COLOR'] . ')">' . $arSystem['PROP_HD'] . '</span> ';

                    $arItem["NAME"] .= $arSystem['PROP_HD_CAPACITY'];
                    $arItem["NAME"] .= ' <span class="comment">' . $arSystem['PROP_HD_INTERFACE'] . ', ' . $arSystem['PROP_HD_CHIPSET'] . '</span>';

                    $arItem["NAME"] .= '<a name="' . $arTestType["TYPE"] . '_' . $arSystem['ID'] . '"></a>';

                    if ($arSystem['PROP_ACTIAL']) {
                        $color = $arSystem['PROP_HD_FIRM_ACTIVE_COLOR'];
                    } else {
                        $color = $arSystem['PROP_HD_FIRM_PASSIVE_COLOR'];
                    }
                    break;
            }
            $arItem["NAME"] .= '</span>';

            $arRes = array($arItem["RESULT"]);
            if (!empty($arItem["RESULT2"]))
                $arRes[] = $arItem["RESULT2"];
            if (!empty($arItem["RESULT3"]))
                $arRes[] = $arItem["RESULT3"];
//            rsort($arRes);
            ?>
                ['<?= $arItem["NAME"] ?>', <?= $color ?>, <?= implode(', ', $arRes) ?>],
            <?
        endforeach;
        unset($arItem);
        ?>
            ],
        <?
    endforeach;
    unset($arTest);
endforeach;
unset($arTestType);
?>
    ];</script>

<? foreach ($arResult['TEST_TYPES'] as &$arTestType) { ?>
    <div class="ilex-dialog" id="filter-<?= $arTestType['TYPE'] ?>">
        <div class="dialog-content">
            <input type="checkbox" name="hide" value="hideOc" id="filterHideOc" autocomplete="off">
            <label for="filterHideOc">Скрыть % разгона</label><br>
            <input type="checkbox" name="hide" value="hideComment" id="filterHideComment" autocomplete="off">
            <label for="filterHideComment">Скрыть тайминги, напряжения и прочую фигню</label><br>
            <?
            $arFilter = array();
            foreach ($arTestType['TESTS'] as $i => &$arTest) {
                foreach ($arTest["ITEMS"] as $j => &$arItem) {
                    if (floatval($arItem['RESULT']) == 0.0)
                        continue;
                    $arFilter[preg_replace('/ title="[^"]+"/', '', $arItem["NAME"])] = $arItem['SYSTEM']['ID'];
                }
                unset($arItem);
            }
            unset($arTest);
            ?>
            <input type="checkbox" autocomplete="off" checked name="line-all" value="<?= $arTestType['TYPE'] . '_all' ?>" id="filter<?= $arTestType['TYPE'] . '_all' ?>">
            <label for="filter<?= $arTestType['TYPE'] . '_all' ?>">Все</label><br>
            <?
            ksort($arFilter);
            foreach ($arFilter as $name => $number) {
                ?>
                <input type="checkbox" autocomplete="off" checked name="line" value="<?= $arTestType['TYPE'] . '_' . $number ?>" id="filter<?= $arTestType['TYPE'] . '_' . $number ?>">
                <label for="filter<?= $arTestType['TYPE'] . '_' . $number ?>"><?= $name ?></label><br>
                <?
            }
            ?>
        </div>
    </div>
    <?
}
unset($arTestType);
$i = 0;
foreach ($arResult['TEST_TYPES'] as &$arTestType):
    ?>
    <div class="lb-spoiler spoiler-type">
        <div class="spoiler-title" data-filter="<?= $arTestType['TYPE'] ?>"><?= $arTestType['NAME'] ?></div>
        <div class="spoiler-text">
            <div class="btn-wrapper">
                <a class="filter-call" href="#" onclick="return OpenFilter('<?= $arTestType['TYPE'] ?>')">Фильтр</a>
            </div>
            <?
            foreach ($arTestType['TESTS'] as &$arTest):
                ?>
                <div class="graphic <?= $arTestType["TYPE"] ?> <?= strpos($arTest["NAME"], "Итог") !== false ? "SUMMARY" : "" ?>">
                    <center>
                        <h3><?= $arTest["NAME"] . ($arTest['UNITS'] ? ', ' . $arTest['UNITS'] : '') . ($arTest['LESS_BETTER'] ? ' (меньше - лучше)' : '') ?></h3>
                        <? if (!empty($arTest['DESCRIPTION'])): ?>
                            <div class="lb-spoiler spoiler-desc">
                                <div class="spoiler-title">Описание теста</div>
                                <div class="spoiler-text">
                                    <?= $arTest['DESCRIPTION'] ?>
                                </div>
                            </div>
                        <? endif; ?>
                        <font color="#800000" id="c<?= $i ?>"><br><br><b>---</b><br></font>
                        <script language="javascript">chartdraw(<?= $i ?>, window.innerWidth > 420 ? 400 : window.innerWidth - 20, {
                                axisMin: 0,
                                type: 3,
                                padding1: 1,
                                padding2: 0,
                                dataBorder: true,
                                fontSize: 14,
                                srt: true,
        <? if ($arTest['LESS_BETTER']): ?> srtAsc: true,<? endif; ?>
                            });</script>
                    </center>
                </div>
                <?
                $i++;
            endforeach;
            unset($arTest);
            ?>
        </div>
    </div>
    <?
endforeach;
unset($arTestType);
?>
<div class="help">
    <h3>Расшифровка</h3>
    <table align="center">
        <tr>
            <td colspan="2">
                <span class="intel">Intel i5-4570@3600 <span class="oc">+13%</span> <span class="comment">1.040V</span></span>, 
                16GB-DDR3@2200 <span class="oc">+3%</span> <span class="comment">11-12-11-30/1T</span>, 
                <span class="amd">AMD HD7850@1050/5600 <span class="oc">+14%/+12%</span> <span class="comment">1.138V</span> +50%PL <span class="comment">PCI-E 3.0 16x</span></span>
            </td>
        </tr>
        <tr><td><span class="intel">Intel</span> / <span class="amd">AMD</span></td><td>производитель процессора</td></tr>
        <tr><td><span class="intel">i5-4570</span></td><td>модель процессора</td></tr>
        <tr><td><span class="intel">3600</span></td><td>частота процессора в МГц</td></tr>
        <!--<tr><td><span class="intel">+TB</span> / <span class="amd">+TC</span></td><td>работает технология автоматического повышения частоты процессора <span class="comment">TurboBoost / TurboCore</span></td></tr>-->
        <tr><td><span class="oc">+13%</span> (если указано)</td><td>разгон от штатной частоты</td></tr>
        <tr><td><span class="comment">4C/4T</span></td><td>количество процссорных ядер (C), модулей (M) (amd), вычислительных потоков (T)</td></tr>
        <tr><td><span class="comment">1.040V</span></td><td>напряжение на ядре процессора под полной нагрузкой</td></tr>
        <tr><td>16GB</td><td>объем оперативной памяти</td></tr>
        <tr><td>DDR3</td><td>тип оперативной памяти <span class="comment">DDR / DDR2 / DDR3 / DDR4</span></td></tr>
        <tr><td>2200</td><td>частота оперативной памяти <span class="comment">стандартный диапазоны частот (оверклокерские модули): DDR 266-400 / DDR2 533-800 (OC 1200) / DDR3 1066-1600 (OC 3200) / DDR4 2133-4266</span></td></tr>
        <tr><td><span class="oc">+3%</span> (если указано)</td><td>разгон от штатной частоты</td></tr>
        <tr><td><span class="comment">11-12-11-30/1T</span></td><td>тайминги памяти</td></tr>
        <tr><td><span class="comment">SingleChannel</span></td><td>режим работы нескольких модулей оперативной памяти. Если неуказано, подразумивается двухканальный <span class="comment">Single / Dual / Triple / Quadro</span></td></tr>
        <!--<tr><td>92.6% (если указано)</td><td>отношение скорости работы памяти к ее теоретической пропускной способности</td></tr>-->
        <tr><td><span class="amd">AMD</span> / <span class="nvidia">nVidia</span> / <span class="intel">Intel</span></td><td>производитель видеокарты</td></tr>
        <tr><td><span class="amd">HD7850</span></td><td>модель видеокарты</td></tr>
        <tr><td><span class="amd">1050/5600</span></td><td>частота видеоядра / видеопамяти. Если указан диапазон - значит работает технология динамического управления частотой в зависимости от нагрузки или температуры</td></tr>
        <tr><td><span class="oc">+14%/+12%</span> (если указано)</td><td>разгон относительно штатной частоты. Если только одно значение, значит разгонялось только видеоядро</td></tr>
        <tr><td><span class="comment">1.138V</span></td><td>напряжение на видеоядре под полной нагрузкой</td></tr>
        <!--<tr><td>+50%PL (если указано)</td><td>увеличение максимального энергопотребления видеокарты (PowerLimit)</td></tr>-->
        <tr><td><span class="comment">PCI-E 3.0 16x</span></td><td>режим работы шины <span class="comment">при работе нескольких видеокарт указывается как распределение линий, например 8x+8x, 16x+16x, 16x+4x, 8x+8x+4x, 4x+4x+4x+4x, и т.д.</span></td></tr>
        <tr><td><span class="oc">CF/SLI</span> (если указано)</td><td>режим работы нескольких видеокарт одновременно <span class="comment">CrossFireX / SLI</span></td></tr>
    </table>
</div>
