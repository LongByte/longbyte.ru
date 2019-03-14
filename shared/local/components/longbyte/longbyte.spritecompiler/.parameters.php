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
            'NAME' => Loc::getMessage('OP_SP_PATH'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
            'REFRESH' => 'Y',
        ),
        'FILES' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_FILES'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => '',
        ),
        'FILES_MASK' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_FILES'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'Y',
            'DEFAULT' => '',
        ),
        'ID_PREFIX' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_ID_PREFIX'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => '',
        ),
        'REMOVE_OLD_SPRITE_FILES' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_REMOVE_OLD_SPRITE_FILES'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'TARGET_FILE_MASK' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_TARGET_FILE_MASK'),
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'DEFAULT' => 'styles_%s.css',
        ),
        'SHOW_ERRORS_IN_DISPLAY' => array(
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('OP_SP_SHOW_ERRORS_IN_DISPLAY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
    ),
);
?>