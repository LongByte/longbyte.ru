<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'INCLUDE_COMPONENTS' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Так же подключить компоненты',
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
        ),
        'STYLE_TO_COMPILER' => array(
            'PARENT' => 'BASE',
            'NAME' => 'Передать Less/Sass-файл компилятору',
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        ),
    ),
);
?>