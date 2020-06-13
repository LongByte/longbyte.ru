<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

global $MESS;
$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/prolog.php'));
include(GetLangFileName($strPath2Lang . '/lang/', '/all.php'));
?>