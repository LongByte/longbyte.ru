<?

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\SiteTable;
use Bitrix\Main\IO;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\PropertyEnumerationTable;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

require_once(Application::getDocumentRoot() . $bxRoot . '/modules/realweb.pagetype/prolog.php'); // пролог модуля

class realweb_pagetype extends CModule
{
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
    var $MODULE_ID = 'realweb.pagetype';
    private $bxRoot = BX_ROOT;

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function __construct()
    {
        $this->PARTNER_NAME = 'Realweb';
        $this->PARTNER_URI = 'http://www.realweb.ru';
        $this->errors = array();
        $this->result = array();
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        $localPath = getLocalPath("");
        $this->bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = GetMessage('REALWEB.PAGETYPE.NAME');
        if (CModule::IncludeModule($this->MODULE_ID)) {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.PAGETYPE.INSTALL_DESCRIPTION');
        } else {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.PAGETYPE.PREINSTALL_DESCRIPTION');
        }
    }

    function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);

            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallIblock();
            $this->AppendUrlrewrite();
        }

        return true;
    }

    function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallDB();
            $this->UnInstallFiles();
            ModuleManager::unRegisterModule($this->MODULE_ID);
        }
        return true;
    }

    function InstallFiles()
    {
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/install/admin/', Application::getDocumentRoot() . '/bitrix/admin/', true, true);
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/install/components/', Application::getDocumentRoot() . $this->bxRoot . '/components/', true, true);
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/install/templates/', Application::getDocumentRoot() . $this->bxRoot . '/templates/', false, true);
        CopyDirFiles(Application::getDocumentRoot() . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/install/public/', Application::getDocumentRoot() . '/', false, true);

        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(Application::getDocumentRoot() . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/install/admin/', Application::getDocumentRoot() . '/bitrix/admin/');
        return true;
    }

    function InstallDB()
    {
        EventManager::getInstance()->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Realweb\PageType\PageType', 'GetUserTypeDescription');
        EventManager::getInstance()->registerEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\PageType\Handlers', 'OnPageStart');
    }

    function UnInstallDB()
    {
        EventManager::getInstance()->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', $this->MODULE_ID, '\Realweb\PageType\PageType', 'GetUserTypeDescription');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnPageStart', $this->MODULE_ID, '\Realweb\PageType\Handlers', 'OnPageStart');
    }

    function InstallIblock()
    {

        if (!Loader::includeModule('iblock'))
            return false;

        $iblockType = null;
        $iblockId = null;
        $obIblockType = new \CIBlockType();
        $obIblock = new \CIBlock();
        $obProperty = new \CIBlockProperty();
        $obUserField = new \CUserTypeEntity();
        $obUserFieldEnum = new \CUserFieldEnum();
        $iblockTypeTarget = 'content';
        $iblockCodeTarget = 'realweb_content';

        $arMenus = array();
        $obRootDir = new IO\Directory(Application::getDocumentRoot());
        $sort = 10;
        foreach ($obRootDir->getChildren() as $obChild) {
            if ($obChild->isDirectory())
                continue;
            if (preg_match('/^\.(.*)\.menu\.php$/i', $obChild->getName(), $matches)) {
                $arMenus[] = array(
                    'NAME' => $matches[1],
                    'SORT' => $sort,
                );
                $sort += 10;
            }
        }

        $arIblockType = TypeTable::getRow(array(
            'filter' => array('ID' => $iblockTypeTarget),
        ));
        if ($arIblockType) {
            $iblockType = $arIblockType['ID'];
        } else {
            if ($obIblockType->Add(array(
                'ID' => $iblockTypeTarget,
                'SECTIONS' => 'Y',
                'SORT' => 100,
                'LANG' => array(
                    'ru' => array(
                        'NAME' => 'Контент',
                        'SECTION_NAME' => 'Разделы',
                        'ELEMENT_NAME' => 'Страницы',
                    ),
                ),
            ))) {
                $iblockType = $iblockTypeTarget;
                TypeTable::getEntity()->cleanCache();
            }
        }


        if (!$iblockType)
            return false;

        $arIblock = IblockTable::getRow(array(
            'filter' => array('IBLOCK_TYPE_ID' => $iblockType, '=CODE' => $iblockCodeTarget),
        ));

        if ($arIblock) {
            $iblockId = $arIblock['ID'];
        } else {
            $arSites = array();
            $rsSites = SiteTable::getList(array(
                'filter' => array('ACTIVE' => 'Y'),
            ));

            while ($arSite = $rsSites->fetch()) {
                $arSites[] = $arSite['LID'];
            }

            $iblockId = $obIblock->Add(array(
                'ACTIVE' => 'Y',
                'NAME' => 'Контентные страницы',
                'CODE' => $iblockCodeTarget,
                'LIST_PAGE_URL' => '/',
                'SECTION_PAGE_URL' => '/#SECTION_CODE_PATH#/',
                'DETAIL_PAGE_URL' => '/#SECTION_CODE_PATH#/#ELEMENT_CODE#/',
                'IBLOCK_TYPE_ID' => $iblockType,
                'SITE_ID' => $arSites,
                'SORT' => 1000,
                'VERSION' => 2,
                'GROUP_ID' => array('1' => 'X', '2' => 'D'),
            ));
        }

        if (!$iblockId)
            return false;

        $arProperties = array();
        $rsProperties = PropertyTable::getList(array(
            'filter' => array('IBLOCK_ID' => $iblockId),
        ));

        while ($arProperty = $rsProperties->fetch()) {
            $arProperties[$arProperty['CODE']] = $arProperty;
        }

        if (!$arProperties['PAGE_TYPE']) {
            $arFields = array(
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                'NAME' => 'Тип страницы',
                'CODE' => 'PAGE_TYPE',
                'SORT' => 10,
                'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
                'USER_TYPE' => 'PageType',
                'MULTIPLE' => 'Y',
                'SECTION_PROPERTY' => 'Y',
            );

            $propertyId = $obProperty->Add($arFields);

            if ($propertyId) {
                $arFields['ID'] = $propertyId;
                $arProperties[$arFields['CODE']] = $arFields;
            }
        }

        if (!$arProperties['MENU']) {
            $arFields = array(
                'IBLOCK_ID' => $iblockId,
                'ACTIVE' => 'Y',
                'NAME' => 'Меню',
                'CODE' => 'MENU',
                'SORT' => 20,
                'PROPERTY_TYPE' => PropertyTable::TYPE_LIST,
                'MULTIPLE' => 'Y',
                'SECTION_PROPERTY' => 'Y',
            );

            $propertyId = $obProperty->Add($arFields);

            if ($propertyId) {
                $arFields['ID'] = $propertyId;
                $arProperties[$arFields['CODE']] = $arFields;

                foreach ($arMenus as $arMenu) {
                    PropertyEnumerationTable::add(array(
                        'PROPERTY_ID' => $propertyId,
                        'VALUE' => $arMenu['NAME'],
                        'SORT' => $arMenu['SORT'],
                        'XML_ID' => $arMenu['NAME'],
                    ));
                }
            }
        }

        $arUserField = $obUserField->GetList(array(), array(
            'ENTITY_ID' => 'IBLOCK_' . $iblockId . '_SECTION',
            'FIELD_NAME' => 'UF_MENU',
        ))->fetch();

        if (!$arUserField) {
            $arFields = array(
                'ENTITY_ID' => 'IBLOCK_' . $iblockId . '_SECTION',
                'USER_TYPE_ID' => 'enumeration',
                'FIELD_NAME' => 'UF_MENU',
                'XML_ID' => 'UF_MENU',
                'SORT' => 10,
                'MULTIPLE' => 'Y',
                'SETTINGS' => array(
                    'DISPLAY' => 'LIST',
                    'LIST_HEIGHT' => '5',
                ),
                'EDIT_FORM_LABEL' => array(
                    'ru' => 'Меню',
                    'en' => 'Menu',
                ),
                'LIST_COLUMN_LABEL' => array(
                    'ru' => 'Меню',
                    'en' => 'Menu',
                ),
                'LIST_FILTER_LABEL' => array(
                    'ru' => 'Меню',
                    'en' => 'Menu',
                ),
            );

            $userFieldId = $obUserField->Add($arFields);

            $arNewEnumValues = array();
            foreach ($arMenus as $arMenu) {

                $arNewEnumValues['n' . count($arNewEnumValues)] = array(
                    'XML_ID' => $arMenu['NAME'],
                    'VALUE' => $arMenu['NAME'],
                    'DEF' => 'N',
                    'SORT' => $arMenu['SORT'],
                );
            }

            $obUserFieldEnum->SetEnumValues($userFieldId, $arNewEnumValues);
        }

        return true;
    }

    function AppendUrlrewrite()
    {

        $strUrlRewritePath = Application::getDocumentRoot() . '/urlrewrite.php';
        include($strUrlRewritePath);

        foreach ($arUrlRewrite as $position => $arRule) {
            if ($arRule['PATH'] == '/pages/index.php')
                return true;
        }

        $arUrlRewrite[] = array(
            'CONDITION' => '#^/#',
            'RULE' => '',
            'ID' => 'bitrix:catalog',
            'PATH' => '/pages/index.php',
            'SORT' => 1000,
        );

        $strUrlrewritePhp = '<?' . PHP_EOL
            . '$arUrlRewrite = array(' . PHP_EOL;

        foreach ($arUrlRewrite as $position => $arRule) {
            $strUrlrewritePhp .= $position . ' => array(
            \'CONDITION\' => \'' . $arRule['CONDITION'] . '\',
            \'RULE\' => \'' . $arRule['RULE'] . '\',
            \'ID\' => \'' . $arRule['ID'] . '\',
            \'PATH\' => \'' . $arRule['PATH'] . '\',
            \'SORT\' => ' . ($arRule['SORT'] ?: 100) . ',
        ),' . PHP_EOL;
        }

        $strUrlrewritePhp .= ');' . PHP_EOL
            . '?>';

        $obFile = new IO\File($strUrlRewritePath);
        $obFile->putContents($strUrlrewritePhp);

        return true;
    }

}
