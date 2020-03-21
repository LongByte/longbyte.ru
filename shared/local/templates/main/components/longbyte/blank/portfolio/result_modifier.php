<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/** @var \Api\Portfolio\Element\Collection $obElementsCollection */
/** @var \Api\Portfolio\Element\Entity $obElement */
$obElementsCollection = \Api\Portfolio\Element\Model::getAll(array(
        'ACTIVE' => 'Y'
    ));

foreach ($obElementsCollection as $obElement) {
    $strPreviewPicture = $obElement->getPreviewPictureFile()->setResize(100, 10000)->getSrc();


    $startYear = $obElement->getYearStart();
    $endYear = $obElement->getYearFinish();
    $strPrintYear = $startYear . ' г.';
    if (empty($endYear) || $endYear != $startYear) {
        $strPrintYear .= ' — ';
        if (empty($endYear)) {
            $strPrintYear .= 'н. в.';
        } elseif ($endYear != $startYear) {
            $strPrintYear .= $endYear . ' г.';
        }
    }
    $obElement->setPrintYear($strPrintYear);

    $url = $obElement->getUrl();
    if (!empty($url)) {
        if (strpos($url, 'http') !== 0) {
            $obElement->setUrl('http://' . $url);
        }
    }
}
unset($arItem);


$arResult['VUE'] = array(
    'items' => $obElementsCollection->toArray()
);

