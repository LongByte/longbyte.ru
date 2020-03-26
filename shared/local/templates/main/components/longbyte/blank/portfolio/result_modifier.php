<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/** @var \Api\Portfolio\Element\Collection $obElementsCollection */
/** @var \Api\Portfolio\Element\Entity $obElement */
$arCacahe = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Portfolio\Element\Model::getIblockId())
    ->setId('PortfolioList')
    ->get(function() {
    $obElementsCollection = \Api\Portfolio\Element\Model::getAll(array(
            'ACTIVE' => 'Y'
    ));

    if ($obElementsCollection->count() <= 0) {
        \Api\Core\Main\Cache::getInstance()->abortCache();
        \Api\Core\Main\NotFound::setStatus404();
        return;
    }

    foreach ($obElementsCollection as $obElement) {
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

    return $obElementsCollection->toArray();
});

$arResult['VUE'] = array(
    'items' => $arCacahe
);

