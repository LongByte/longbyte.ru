<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$fs = array(" б", " кб", " мб", " гб");

global $isMobile;

foreach ($arResult['ITEMS'] as $key => &$arItem) {

    if ($arItem['IBLOCK_SECTION_ID'] != $arResult['ID']) {
        unset($arResult['ITEMS'][$key]);
        continue;
    }

    $file = $arItem['PROPERTIES']['FILE']['VALUE'];

    if (empty($file)) {
        $arFile = $arItem['PREVIEW_PICTURE'];
    } else {
        $arFile = CFile::GetFileArray($file);
    }

    $fs_type = 0;
    while ($arFile['FILE_SIZE'] > 1024) {
        $arFile['FILE_SIZE'] /= 1024;
        $fs_type++;
    }

    $arItem['IS_IMG'] = strpos($arFile['CONTENT_TYPE'], 'image/') !== false;

    if (Site::isMobile() && $arItem['IS_IMG'] && $arFile['CONTENT_TYPE'] != 'image/gif') {
        if ($img = CFile::ResizeImageGet($arFile['ID'], array('width' => 700, 'height' => 700), BX_RESIZE_IMAGE_PROPORTIONAL)) {
            $arFile['SRC'] = $img['src'];
            $arFile['WIDTH'] = $img['width'];
            $arFile['HEIGHT'] = $img['height'];
        }
    }

    $arFile['FILE_SIZE'] = round($arFile['FILE_SIZE']) . $fs[$fs_type];
    $arItem['FILE'] = $arFile;

    if ($img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 228, 'height' => 190), BX_RESIZE_IMAGE_PROPORTIONAL)) {
        $arItem['PREVIEW_PICTURE']['SRC'] = $img['src'];
        $arItem['PREVIEW_PICTURE']['WIDTH'] = $img['width'];
        $arItem['PREVIEW_PICTURE']['HEIGHT'] = $img['height'];
    }
}
unset($arItem);
