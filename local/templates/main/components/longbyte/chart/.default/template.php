<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/** @var \Api\Chart\Tests\Section\Collection $obData */
/** @var \Api\Chart\Tests\Section\Entity $obTestType */
/** @var \Api\Chart\Tests\Element\Entity $obTest */
/** @var \Api\Chart\Result\Element\Entity $obResult */
/** @var \Api\Chart\Systems\Element\Entity $obSystem */
$obData = $arResult['obData'];
?>
<h2 style="text-align: center;">Добро пожаловать на сборник результатов бенчмарков</h2>
<h3 style="text-align: center;">Выберите интересующий вид тестов</h3>
<br>
<script>

    var groups = [
        [0]
    ];

    var serviceCount = <?= $obData->count() ?>;

    var lineCol = 'A0A0A0';
    var axisCol = '808080';

    var data = <?= CUtil::PhpToJSObject($arResult['JS_DATA'], false, false, true) ?>
</script>

<? foreach ($obData->getCollection() as $obTestType): ?>
    <div class="ilex-dialog" id="filter-<?= $obTestType->getCode() ?>">
        <div class="dialog-content">
            <input type="checkbox" name="hide" value="hideOc" id="filterHideOc" autocomplete="off">
            <label for="filterHideOc">Скрыть % разгона</label>
            <br>
            <input type="checkbox" name="hide" value="hideComment" id="filterHideComment" autocomplete="off">
            <label for="filterHideComment">Скрыть тайминги, напряжения и прочую фигню</label>
            <br>
            <input type="checkbox" autocomplete="off" checked name="line-all" value="<?= $obTestType->getCode() . '_all' ?>" id="filter<?= $obTestType->getCode() . '_all' ?>">
            <label for="filter<?= $obTestType->getCode() . '_all' ?>">Все</label>
            <br>
            <? foreach ($obTestType->getTests()->getFilterCollection() as $obSystem): ?>
                <input type="checkbox" autocomplete="off" checked name="line" value="<?= $obTestType->getCode() . '_' . $obSystem->getId() ?>" id="filter<?= $obTestType->getCode() . '_' . $obSystem->getId() ?>">
                <label for="filter<?= $obTestType->getCode() . '_' . $obSystem->getId() ?>"><?= $obSystem->getFullName($obTestType) ?></label>
                <br>
            <? endforeach; ?>
        </div>
    </div>
<? endforeach; ?>
<? $i = 0; ?>
<? foreach ($obData->getCollection() as $obTestType): ?>
    <div class="lb-spoiler spoiler-type">
        <div class="spoiler-title" data-filter="<?= $obTestType->getCode() ?>"><?= $obTestType->getName() ?></div>
        <div class="spoiler-text">
            <div class="btn-wrapper">
                <a class="filter-call" href="#" onclick="return OpenFilter('<?= $obTestType->getCode() ?>')">Фильтр</a>
            </div>
            <?
            foreach ($obTestType->getTests() as $obTest):
                ?>
                <div class="graphic <?= $obTestType->getCode() ?>">
                    <center>
                        <h3><?= $obTest->getTitle() ?></h3>
                        <? if (!empty($obTest->getPreviewText())): ?>
                            <div class="lb-spoiler spoiler-desc">
                                <div class="spoiler-title">Описание теста</div>
                                <div class="spoiler-text">
                                    <?= $obTest->getPreviewText() ?>
                                </div>
                            </div>
                        <? endif; ?>
                        <font color="#800000" id="c<?= $i ?>">
                            <br><br>
                            <b>---</b>
                            <br>
                        </font>
                        <script language="javascript">chartdraw(<?= $i ?>, window.innerWidth > 420 ? 400 : window.innerWidth - 20, {
                                axisMin: 0,
                                type: 3,
                                padding1: 1,
                                padding2: 0,
                                dataBorder: true,
                                fontSize: 14,
                                srt: true,
                                <? if ($obTest->getLessBetter()): ?>
                                srtAsc: true,<? endif; ?>
                            });</script>
                    </center>
                </div>
                <?
                $i++;
            endforeach;
            ?>
        </div>
    </div>
<?
endforeach;
?>
<div class="help">
    <h3>Расшифровка</h3>
    <table align="center">
        <tr>
            <td colspan="2">
                <span class="intel">Intel i5-4570@3600
                    <span class="oc">+13%</span>
                    <span class="comment">1.040V</span></span>,
                16GB-DDR3@2200
                <span class="oc">+3%</span>
                <span class="comment">11-12-11-30/1T</span>,
                <span class="amd">AMD HD7850@1050/5600
                    <span class="oc">+14%/+12%</span>
                    <span class="comment">1.138V</span> +50%PL
                    <span class="comment">PCI-E 3.0 16x</span></span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="intel">Intel</span> /
                <span class="amd">AMD</span>
            </td>
            <td>производитель процессора</td>
        </tr>
        <tr>
            <td>
                <span class="intel">i5-4570</span>
            </td>
            <td>модель процессора</td>
        </tr>
        <tr>
            <td>
                <span class="intel">3600</span>
            </td>
            <td>частота процессора в МГц</td>
        </tr>
        <!--<tr><td><span class="intel">+TB</span> / <span class="amd">+TC</span></td><td>работает технология автоматического повышения частоты процессора <span class="comment">TurboBoost / TurboCore</span></td></tr>-->
        <tr>
            <td>
                <span class="oc">+13%</span> (если указано)
            </td>
            <td>разгон от штатной частоты</td>
        </tr>
        <tr>
            <td>
                <span class="comment">4C/4T</span>
            </td>
            <td>количество процссорных ядер (C), модулей (M) (amd), вычислительных потоков (T)</td>
        </tr>
        <tr>
            <td>
                <span class="comment">1.040V</span>
            </td>
            <td>напряжение на ядре процессора под полной нагрузкой</td>
        </tr>
        <tr>
            <td>16GB</td>
            <td>объем оперативной памяти</td>
        </tr>
        <tr>
            <td>DDR3</td>
            <td>тип оперативной памяти
                <span class="comment">DDR / DDR2 / DDR3 / DDR4</span>
            </td>
        </tr>
        <tr>
            <td>2200</td>
            <td>частота оперативной памяти
                <span class="comment">стандартный диапазоны частот (оверклокерские модули): DDR 266-400 / DDR2 533-800 (OC 1200) / DDR3 1066-1600 (OC 3200) / DDR4 2133-4266</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="oc">+3%</span> (если указано)
            </td>
            <td>разгон от штатной частоты</td>
        </tr>
        <tr>
            <td>
                <span class="comment">11-12-11-30/1T</span>
            </td>
            <td>тайминги памяти</td>
        </tr>
        <tr>
            <td>
                <span class="comment">SingleChannel</span>
            </td>
            <td>режим работы нескольких модулей оперативной памяти. Если неуказано, подразумивается двухканальный
                <span class="comment">Single / Dual / Triple / Quadro</span>
            </td>
        </tr>
        <!--<tr><td>92.6% (если указано)</td><td>отношение скорости работы памяти к ее теоретической пропускной способности</td></tr>-->
        <tr>
            <td>
                <span class="amd">AMD</span> /
                <span class="nvidia">nVidia</span> /
                <span class="intel">Intel</span>
            </td>
            <td>производитель видеокарты</td>
        </tr>
        <tr>
            <td>
                <span class="amd">HD7850</span>
            </td>
            <td>модель видеокарты</td>
        </tr>
        <tr>
            <td>
                <span class="amd">1050/5600</span>
            </td>
            <td>частота видеоядра / видеопамяти. Если указан диапазон - значит работает технология динамического управления частотой в зависимости от нагрузки или температуры</td>
        </tr>
        <tr>
            <td>
                <span class="oc">+14%/+12%</span> (если указано)
            </td>
            <td>разгон относительно штатной частоты. Если только одно значение, значит разгонялось только видеоядро</td>
        </tr>
        <tr>
            <td>
                <span class="comment">1.138V</span>
            </td>
            <td>напряжение на видеоядре под полной нагрузкой</td>
        </tr>
        <!--<tr><td>+50%PL (если указано)</td><td>увеличение максимального энергопотребления видеокарты (PowerLimit)</td></tr>-->
        <tr>
            <td>
                <span class="comment">PCI-E 3.0 16x</span>
            </td>
            <td>режим работы шины
                <span class="comment">при работе нескольких видеокарт указывается как распределение линий, например 8x+8x, 16x+16x, 16x+4x, 8x+8x+4x, 4x+4x+4x+4x, и т.д.</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="oc">CF/SLI</span> (если указано)
            </td>
            <td>режим работы нескольких видеокарт одновременно
                <span class="comment">CrossFireX / SLI</span>
            </td>
        </tr>
    </table>
</div>
