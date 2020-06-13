<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

foreach ($arResult['ITEMS'] as &$arItem) {
    if ($arItem['PREVIEW_PICTURE']) {
        if ($img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], ['width' => 100, 'height' => 10000], BX_RESIZE_IMAGE_PROPORTIONAL)) {
            $arItem['PREVIEW_PICTURE']['SRC'] = $img['src'];
        }
    }
    $startYear = $arItem['PROPERTIES']['YEAR_START']['VALUE'];
    $endYear = $arItem['PROPERTIES']['YEAR_FINISH']['VALUE'];
    $arItem['PRINT_YEAR'] = $startYear . ' г.';
    if (empty($endYear) || $endYear != $startYear) {
        $arItem['PRINT_YEAR'] .= ' — ';
        if (empty($endYear)) {
            $arItem['PRINT_YEAR'] .= 'н. в.';
        } elseif ($endYear != $startYear) {
            $arItem['PRINT_YEAR'] .= $endYear . ' г.';
        }
    }

    if (!empty($arItem['PROPERTIES']['URL']['VALUE'])) {
        if (strpos($arItem['PROPERTIES']['URL']['VALUE'], 'http') !== 0)
            $arItem['PROPERTIES']['URL']['VALUE'] = 'http://' . $arItem['PROPERTIES']['URL']['VALUE'];
    }
}
unset($arItem);


$arResult['VUE'] = array(
    'items' => array()
);
foreach ($arResult['ITEMS'] as $arItem) {

    $arTags = array();
    foreach (explode(',', $arItem['TAGS']) as &$tag) {
        $arTags[] = array(
            'TAG' => $tag,
            'ID' => $arItem['ID'] . '_' . md5($tag)
        );
    }
    unset($tag);

    $arVueItem = array(
        'ID' => $arItem['ID'],
        'PREVIEW_SRC' => $arItem['PREVIEW_PICTURE']['SRC'],
        'DETAIL_SRC' => $arItem['DETAIL_PICTURE']['SRC'],
        'NAME' => $arItem['NAME'],
        'DETAIL_TEXT' => $arItem['~DETAIL_TEXT'],
        'PREVIEW_TEXT' => $arItem['~PREVIEW_TEXT'],
        'PRINT_YEAR' => $arItem['PRINT_YEAR'],
        'URL' => $arItem['PROPERTIES']['URL']['VALUE'],
        'TAGS' => $arTags,
    );

    $arResult['VUE']['items'][] = $arVueItem;
}