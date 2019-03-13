<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
    'GROUPS' => array(),
    'PARAMETERS' => array(
        'PATH_TO_FILES' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_PATH'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
        ),
        'FILES' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_FILES'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => '',
        ),
        'FILES_MASK' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_FILES'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => '',
        ),
        'PATH_CSS' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_PATH_CSS'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
        ),
        'COMPILER' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_COMPILER'),
            'TYPE' => 'STRING',
            'VALUE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'SASS',
            'REFRESH' => 'N',
        ),
        'USE_SETADDITIONALCSS' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_USE_SETADDITIONALCSS'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'ADD_CSS_TO_THE_END' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_ADD_CSS_TO_THE_END'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'REMOVE_OLD_CSS_FILES' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_REMOVE_OLD_CSS_FILES'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'TMP_FILE_MASK' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_TMP_FILE_MASK'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'tmp_%s.less',
        ),
        'TARGET_FILE_MASK' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_TARGET_FILE_MASK'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'styles_%s.css',
        ),
        'SHOW_ERRORS_IN_DISPLAY' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_CS_SHOW_ERRORS_IN_DISPLAY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
    ),
);
?>