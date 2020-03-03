<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

require_once(Application::getDocumentRoot() . $bxRoot . '/modules/realweb.redirects/prolog.php'); // пролог модуля

Class realweb_redirects extends CModule {
    // Обязательные свойства.

    /**
     * Имя партнера - автора модуля.
     * @var string
     */
    var $PARTNER_NAME;

    /**
     * URL партнера - автора модуля.
     * @var string
     */
    var $PARTNER_URI;

    /**
     * Версия модуля.
     * @var string
     */
    var $MODULE_VERSION;

    /**
     * Дата и время создания модуля.
     * @var string
     */
    var $MODULE_VERSION_DATE;

    /**
     * Имя модуля.
     * @var string
     */
    var $MODULE_NAME;

    /**
     * Описание модуля.
     * @var string
     */
    var $MODULE_DESCRIPTION;

    /**
     * ID модуля.
     * @var string
     */
    var $MODULE_ID = 'realweb.redirects';
    private $bxRoot = BX_ROOT;

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function __construct() {
        $this->PARTNER_NAME = 'Realweb';
        $this->PARTNER_URI = 'http://www.realweb.ru';
        $this->errors = array();
        $this->result = array();
        $arModuleVersion = array();

        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strlen($path) - strlen('/index.php'));
        include($path . '/version.php');

        $localPath = getLocalPath("");
        $this->bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('REALWEB.REDIRECTS.NAME');
        if (Loader::includeModule($this->MODULE_ID)) {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.REDIRECTS.INSTALL_DESCRIPTION');
        } else {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.REDIRECTS.PREINSTALL_DESCRIPTION');
        }
    }

    function DoInstall() {
        $this->InstallFiles();
        $this->InstallDB();
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);

            return true;
        }
    }

    function DoUninstall() {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }

        $this->UnInstallDB();
        $this->UnInstallFiles();
        return true;
    }

    function InstallFiles() {
        return true;
    }

    function UnInstallFiles() {
        return true;
    }

    function InstallDB() {
        self::createHLBlock();
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onPageStart');
        EventManager::getInstance()->registerEventHandler('', 'RedirectsOnBeforeAdd', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onBeforeSave');
        EventManager::getInstance()->registerEventHandler('', 'RedirectsOnBeforeUpdate', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onBeforeSave');
    }

    function UnInstallDB() {
        EventManager::getInstance()->unregisterEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onPageStart');
        EventManager::getInstance()->unregisterEventHandler('', 'RedirectsOnBeforeAdd', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onBeforeSave');
        EventManager::getInstance()->unregisterEventHandler('', 'RedirectsOnBeforeUpdate', $this->MODULE_ID, '\Realweb\Redirects\Redirects', 'onBeforeSave');
    }

    function createHLBlock() {
        if (Loader::includeModule('iblock') && Loader::includeModule('highloadblock')) {

            $arHighBlockTypeProduct = array(
                'realweb_redirects',
                'Redirects',
                array(
                    'FIELDS' => array(
                        'UF_ACTIVE' => array('N', 'boolean', array(
                                'SETTINGS' => array(
                                    'DEFAULT_VALUE' => true,
                                ),
                                'EDIT_FORM_LABEL' => array(
                                    'ru' => 'Активность',
                                ),
                                'LIST_COLUMN_LABEL' => array(
                                    'ru' => 'Активность',
                                ),
                            )),
                        'UF_FROM' => array('Y', 'string', array(
                                'EDIT_FORM_LABEL' => array(
                                    'ru' => 'Исходный адрес',
                                ),
                                'LIST_COLUMN_LABEL' => array(
                                    'ru' => 'Исходный адрес',
                                ),
                                'SHOW_FILTER' => 'S',
                            )),
                        'UF_TO' => array('Y', 'string', array(
                                'EDIT_FORM_LABEL' => array(
                                    'ru' => 'Целевой адрес',
                                ),
                                'LIST_COLUMN_LABEL' => array(
                                    'ru' => 'Целевой адрес',
                                ),
                                'SHOW_FILTER' => 'S',
                            )),
                        'UF_SORT' => array('Y', 'integer', array(
                                'SETTINGS' => array(
                                    'DEFAULT_VALUE' => 500,
                                ),
                                'EDIT_FORM_LABEL' => array(
                                    'ru' => 'Сортировка',
                                ),
                                'LIST_COLUMN_LABEL' => array(
                                    'ru' => 'Сортировка',
                                ),
                            )),
                        'UF_REGEXP' => array('N', 'boolean', array(
                                'SETTINGS' => array(
                                    'DEFAULT_VALUE' => false,
                                ),
                                'EDIT_FORM_LABEL' => array(
                                    'ru' => 'Регулярное выражение',
                                ),
                                'LIST_COLUMN_LABEL' => array(
                                    'ru' => 'Регулярное выражение',
                                ),
                            )),
                    ),
                )
            );

            self::createHighLoadBlock($arHighBlockTypeProduct[0], $arHighBlockTypeProduct[1], $arHighBlockTypeProduct[2]);
        }
    }

    function createHighLoadBlock($tableName, $highBlockName, array $hlData) {
        global $APPLICATION;

        $info = array();

        foreach (array('highloadblock') as $moduleId) {
            if (!\Bitrix\Main\Loader::includeModule($moduleId)) {
                throw new \Bitrix\Main\SystemException(Loc::getMessage('ERROR_INCLUDE_HIGHLOADBLOCK_MODULE', array(
                        '#MODULE#' => $moduleId
                )));
            }
        }

        $connection = \Bitrix\Main\Application::getConnection();

        $sqlHelper = $connection->getSqlHelper();

        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
                'filter' => array(
                    'TABLE_NAME' => $tableName,
                ))
            )->fetch();

        if (!$hlblock) {

            $highBlockName = preg_replace('/([^A-Za-z0-9]+)/', '', trim($highBlockName));

            if ($highBlockName == '') {
                throw new \Bitrix\Main\SystemException(Loc::getMessage('HIGHLOADBLOCK_NAME_IS_INVALID'));
            }

            $highBlockName = strtoupper(substr($highBlockName, 0, 1)) . substr($highBlockName, 1);

            $data = array(
                'NAME' => $highBlockName,
                'TABLE_NAME' => $tableName,
            );

            $result = Bitrix\Highloadblock\HighloadBlockTable::add($data);

            if ($result->isSuccess()) {
                $highBlockID = $result->getId();

                $info[] = Loc::getMessage('HIGHLOADBLOCK_ADDED_INFO', array(
                        '#NAME#' => $highBlockName,
                        '#ID#' => $highBlockID,
                ));
            } else {
                throw new \Bitrix\Main\SystemException(Loc::getMessage('HIGHLOADBLOCK_ADDED_INFO_ERROR', array(
                        '#NAME#' => $highBlockName,
                        '#ERROR#' => $result->getErrorMessages(),
                )));
            }
        } else {
            $highBlockID = $hlblock['ID'];
        }

        $oUserTypeEntity = new CUserTypeEntity();

        $sort = 500;

        foreach ($hlData['FIELDS'] as $fieldName => $fieldValue) {
            $aUserField = array(
                'ENTITY_ID' => 'HLBLOCK_' . $highBlockID,
                'FIELD_NAME' => $fieldName,
                'USER_TYPE_ID' => $fieldValue[1],
                'SORT' => $sort,
                'MULTIPLE' => 'N',
                'MANDATORY' => $fieldValue[0],
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
                'SETTINGS' => array(),
            );

            if (isset($fieldValue[2]) && is_array($fieldValue[2])) {
                $aUserField = array_merge($aUserField, $fieldValue[2]);
            }

            $resProperty = CUserTypeEntity::GetList(
                    array(), array('ENTITY_ID' => $aUserField['ENTITY_ID'], 'FIELD_NAME' => $aUserField['FIELD_NAME'])
            );

            if ($aUserHasField = $resProperty->Fetch()) {
                $idUserTypeProp = $aUserHasField['ID'];
                if ($oUserTypeEntity->Update($idUserTypeProp, $aUserField)) {
                    $info[] = Loc::getMessage('USER_TYPE_UPDATE', array(
                            '#FIELD_NAME#' => $aUserHasField['FIELD_NAME'],
                            '#ENTITY_ID#' => $aUserHasField['ENTITY_ID'],
                    ));
                } else {
                    if (($ex = $APPLICATION->GetException())) {
                        throw new \Bitrix\Main\SystemException(Loc::getMessage('USER_TYPE_UPDATE_ERROR', array(
                                '#FIELD_NAME#' => $aUserHasField['FIELD_NAME'],
                                '#ENTITY_ID#' => $aUserHasField['ENTITY_ID'],
                                '#ERROR#' => $ex->GetString(),
                        )));
                    }
                }
            } else {
                if ($idUserTypeProp = $oUserTypeEntity->Add($aUserField)) {
                    $info[] = Loc::getMessage('USER_TYPE_ADDED', array(
                            '#FIELD_NAME#' => $aUserField['FIELD_NAME'],
                            '#ENTITY_ID#' => $aUserField['ENTITY_ID'],
                    ));
                } else {
                    if (($ex = $APPLICATION->GetException())) {
                        throw new \Bitrix\Main\SystemException(Loc::getMessage('USER_TYPE_ADDED_ERROR', array(
                                '#FIELD_NAME#' => $aUserField['FIELD_NAME'],
                                '#ENTITY_ID#' => $aUserField['ENTITY_ID'],
                                '#ERROR#' => $ex->GetString(),
                        )));
                    }
                }
            }

            $sort += 100;
        }

        $hlEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(
                \Bitrix\Highloadblock\HighloadBlockTable::getRowById($highBlockID)
        );

        if (isset($hlData['ALTER']) && is_array($hlData['ALTER'])) {

            foreach ($hlData['ALTER'] as $alterData) {

                if ($connection->query(
                        str_replace(
                            '#TABLE_NAME#', $sqlHelper->quote($hlEntity->getDBTableName()), $alterData
                        )
                    )
                ) {
                    $info[] = Loc::getMessage('HIGHLOADBLOCK_ALTER_SUCCESS_INFO', array(
                            '#ROW#' => str_replace(
                                '#TABLE_NAME#', $sqlHelper->quote($hlEntity->getDBTableName()), $alterData
                            )
                    ));
                }
            }
        }

        if (isset($hlData['INDEXES']) && is_array($hlData['INDEXES'])) {

            foreach ($hlData['INDEXES'] as $indexData) {

                $iResult = $connection->createIndex(
                    str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[0]), str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]), $indexData[2]
                );

                if ($iResult) {
                    $info[] = Loc::getMessage('HIGHLOADBLOCK_ADDED_INDEX_INFO', array(
                            '#INDEX_NAME#' => str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]),
                            '#TABLE_NAME#' => $hlEntity->getDBTableName(),
                    ));
                } else {
                    throw new \Bitrix\Main\SystemException(Loc::getMessage('HIGHLOADBLOCK_ADDED_INDEX_ERROR', array(
                            '#INDEX_NAME#' => str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]),
                            '#TABLE_NAME#' => $hlEntity->getDBTableName(),
                            '#ERROR#' => '',
                    )));
                }
            }
        }

        return $highBlockID;
    }

}

?>
