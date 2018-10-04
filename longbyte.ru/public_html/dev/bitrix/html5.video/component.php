<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);

if (file_exists($_SERVER['DOCUMENT_ROOT']) . $arParams['FILE_PATH']) {

    if (empty($arParams['WIDTH']))
        $arParams['WIDTH'] = 447;
    if (is_numeric($arParams['WIDTH']))
        $arParams['WIDTH'] .= 'px';
    
    if (empty($arParams['HEIGHT']))
        $arParams['HEIGHT'] = 447;
    if (is_numeric($arParams['HEIGHT']))
        $arParams['HEIGHT'] .= 'px';
    
    $this->IncludeComponentTemplate();
}
?>