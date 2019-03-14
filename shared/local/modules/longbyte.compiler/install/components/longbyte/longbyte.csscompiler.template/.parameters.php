<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'TEMPLATE_PATH' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_PATH'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
        ),
    ),
);
?>