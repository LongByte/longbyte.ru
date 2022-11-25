<?php

use Longbyte\Builder\IblockBuilder;
use WS\ReduceMigrations\Builder\Entity\Iblock;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1577923115_opisanie_rezultatov_testov extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Описание результатов тестов";
    }

    /**
     * Priority of scenario
     * */
    static public function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    static public function hash()
    {
        return "bc38ea245b4e9c21b24d2a0b94d5493bc35e6473";
    }

    /**
     * @return int approximately time in seconds
     */
    static public function approximatelyTime()
    {
        return 0;
    }

    /**
     * Write action by apply scenario. Use method `setData` for save need rollback data
     * */
    public function commit()
    {
        \Bitrix\Main\Loader::includeModule('iblock');
        $obBuilder = new IblockBuilder();

        $obElement = new \CIBlockElement();

        if (IntVal(IBLOCK_CHART_TESTS) > 0) {

            $rsIblockCatalog = $obBuilder->updateIblock(IBLOCK_CHART_TESTS, function (Iblock $rsIblock) {

                $builder = new IblockBuilder();
                $arProps = $builder->GetPropertiesByIblockId(IBLOCK_CHART_TESTS);

                if (!isset($arProps['PLACEHOLDER_RESULT'])) {
                    $rsIblock
                        ->addProperty('Описание результата 1')
                        ->code('PLACEHOLDER_RESULT')
                        ->sort(600)
                    ;
                }
                if (!isset($arProps['PLACEHOLDER_RESULT2'])) {
                    $rsIblock
                        ->addProperty('Описание результата 2')
                        ->code('PLACEHOLDER_RESULT2')
                        ->sort(700)
                    ;
                }
                if (!isset($arProps['PLACEHOLDER_RESULT3'])) {
                    $rsIblock
                        ->addProperty('Описание результата 3')
                        ->code('PLACEHOLDER_RESULT3')
                        ->sort(800)
                    ;
                }
            });

            $rsTests = \Bitrix\Iblock\ElementTable::getList(array(
                'select' => array('ID', 'CODE'),
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS),
            ));

            while ($arTest = $rsTests->fetch()) {

                switch ($arTest['CODE']) {
                    case '3dmark-time-spy-1440p-dx12':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => "G: Баллов за графику
P: Баллов за физику
Ссылка на результат
Max CPU Temp: макс. температура процессора
Max GPU Temp: макс. теспература видеокарты",
                        );
                        break;
                    case 'superposition':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => '1080p Extrime',
                            'PLACEHOLDER_RESULT2' => '1080p Medium',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => "Extrime: fps мин/сред/макс, GPU Temp макс. температура видеокарты
Medium: fps мин/сред/макс, GPU Temp макс. температура видеокарты",
                        );
                        break;
                    case '3dmark-fire-strike':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => 'Баллов за графику',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => 'G: Баллов за графику
P: Баллов за физику
C: Баллов за комбо текст
Ссылка на результат
Max CPU Temp: макс. температура процессора
Max GPU Temp: макс. теспература видеокарты',
                        );
                        break;
                    case '3dmark-sky-diver':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => 'G: Баллов за графику
P: Баллов за физику
C: Баллов за комбо текст
Ссылка на результат
Max CPU Temp: макс. температура процессора
Max GPU Temp: макс. теспература видеокарты',
                        );
                        break;
                    case 'catzilla-720p':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'heaven':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => 'Max CPU Temp: макс. температура процессора
Max GPU Temp: макс. теспература видеокарты
FPS: среднее кол-вл кадров
Min FPS: минимальное кол-вл кадров
Max FPS: максимальное кол-вл кадров',
                        );
                        break;
                    case 'valley':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => 'Max CPU Temp: макс. температура процессора
Max GPU Temp: макс. теспература видеокарты
FPS: среднее кол-вл кадров
Min FPS: минимальное кол-вл кадров
Max FPS: максимальное кол-вл кадров',
                        );


                        break;
                    case 'cpu-z':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Однопоточный',
                            'PLACEHOLDER_RESULT2' => 'Многопоточный',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'aida64-cpu-queen':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'cpu-photoworxx-mpiks-sek':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Мпикс/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'cpu-zlib-mb-s':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'cpu-aes-mb-s':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/с',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'cpu-hash-mb-s':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/с',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fpu-vp8':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fpu-julia':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fpu-mandel':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fpu-sinjulia':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fp32-ray-trace-kray-s':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'KRay/с',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'fp64-ray-trace-kray-s':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'KRay/с',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case '':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Всего баллов',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'superpi-1m-sekund-menshe-luchshe':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => '1М',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'superpi-32m-minut-menshe-luchshe':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => '32М',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'linx-0-6-5-24k-gflops':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Gflops',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );

                        break;
                    case 'aida64-read':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Теоретически МБ/сек',
                            'PLACEHOLDER_RESULT2' => 'МБ/сек',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'write':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Теоретически МБ/сек',
                            'PLACEHOLDER_RESULT2' => 'МБ/сек',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'copy':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'Теоретически МБ/сек',
                            'PLACEHOLDER_RESULT2' => 'МБ/сек',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'latency':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'ns',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );

                        break;
                    case 'read-seq-q32t1':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'read-4k-q32t1':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'read-seq':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'read-4k':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'write-seq-q32t1':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'write-4k-q32t1':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'write-seq':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                    case 'write-4k':
                        $arFields = array(
                            'PLACEHOLDER_RESULT' => 'МБ/сек',
                            'PLACEHOLDER_RESULT2' => '',
                            'PLACEHOLDER_RESULT3' => '',
                            'INFO' => '',
                        );
                        break;
                }


                $obElement->Update($arTest['ID'], array('DETAIL_TEXT' => $arFields['INFO'], 'DETAIL_TEXT_TYPE' => 'text'));
                \CIBlockElement::SetPropertyValuesEx($arTest['ID'], IBLOCK_CHART_TESTS, $arFields);
            }
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback()
    {
        // my code
    }

}
