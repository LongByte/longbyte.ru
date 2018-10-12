<?

IncludeModuleLangFile(__FILE__);
if (class_exists("longbyte_sitemap"))
    return;

class longbyte_sitemap extends CModule {

    var $MODULE_ID = "longbyte.sitemap";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = "N";
    var $errors = false;

    function __construct() {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");
        $this->PARTNER_URI = "https://longbyte.ru/";
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = "Расширенная карта сайта";
        $this->MODULE_DESCRIPTION = "Позволяет указывать в карте сайте поля priority и changefreq";
    }

    function InstallDB($arParams = array()) {
        RegisterModule($this->MODULE_ID);

        if (Bitrix\Main\Loader::includeModule('iblock')) {

            $arExistSitemapIblock = \Bitrix\Iblock\IblockTable::getList(array(
                    'filter' => array('=CODE' => 'longbyte_sitemap'),
                    'limit' => 1
                ))->fetch();

            if (!$arExistSitemapIblock) {
                $obIBlock = new CIBlock();
                $iIBlockID = $obIBlock->Add(array(
                    "ACTIVE" => 'Y',
                    "NAME" => 'Правила для карты сайта',
                    "CODE" => 'longbyte_sitemap',
                    "LIST_PAGE_URL" => '',
                    "DETAIL_PAGE_URL" => '',
                    "IBLOCK_TYPE_ID" => 'service',
                    "SITE_ID" => Array("s1"),
                    "SORT" => 1000,
                    "GROUP_ID" => Array("2" => "R")
                ));

                if ($iIBlockID) {
                    $obProperty = new CIBlockProperty();
                    $obProperty->Add(array(
                        "NAME" => "Приоритет",
                        "ACTIVE" => "Y",
                        "SORT" => 100,
                        "CODE" => "PRIORITY",
                        "PROPERTY_TYPE" => "N",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );

                    $obProperty->Add(array(
                        "NAME" => "Частота изменения",
                        "ACTIVE" => "Y",
                        "SORT" => 200,
                        "CODE" => "CHANGEFREQ",
                        "PROPERTY_TYPE" => "L",
                        "IBLOCK_ID" => $iIBlockID,
                        "VALUES" => array(
                            array(
                                'XML_ID' => 'always',
                                "VALUE" => "always",
                                "DEF" => "N",
                                "SORT" => 100
                            ),
                            array(
                                'XML_ID' => 'hourly',
                                "VALUE" => "hourly",
                                "DEF" => "N",
                                "SORT" => 200
                            ),
                            array(
                                'XML_ID' => 'daily',
                                "VALUE" => "daily",
                                "DEF" => "N",
                                "SORT" => 300
                            ),
                            array(
                                'XML_ID' => 'weekly',
                                "VALUE" => "weekly",
                                "DEF" => "N",
                                "SORT" => 400
                            ),
                            array(
                                'XML_ID' => 'monthly',
                                "VALUE" => "monthly",
                                "DEF" => "N",
                                "SORT" => 500
                            ),
                            array(
                                'XML_ID' => 'yearly',
                                "VALUE" => "yearly",
                                "DEF" => "N",
                                "SORT" => 600
                            ),
                            array(
                                'XML_ID' => 'never',
                                "VALUE" => "never",
                                "DEF" => "N",
                                "SORT" => 700
                            ),
                        )
                    ));
                    
                    $obProperty->Add(array(
                        "NAME" => "Запретить вывод правила",
                        "ACTIVE" => "Y",
                        "SORT" => 300,
                        "CODE" => "BANRULE",
                        "PROPERTY_TYPE" => "L",
                        'USER_TYPE_SETTINGS' => array(
                            'LIST_TYPE' => 'C'
                        ),
                        "IBLOCK_ID" => $iIBlockID,
                        "VALUES" => array(
                            array(
                                'XML_ID' => 'Y',
                                "VALUE" => "Да",
                                "DEF" => "N",
                                "SORT" => 100
                            ),
                        )
                    ));
                }
            }
        }

        return true;
    }

    function UnInstallDB($arParams = array()) {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallEvents() {
        return true;
    }

    function UnInstallEvents() {
        return true;
    }

    function InstallFiles($arParams = array()) {

        $strMainDir = 'bitrix';
        if (dir($_SERVER["DOCUMENT_ROOT"] . '/local/modules/' . $this->MODULE_ID . '/')) {
            $strMainDir = 'local';
        }

        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . "/" . $strMainDir . "/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin");
        return true;
    }

    function UnInstallFiles() {

        $strMainDir = 'bitrix';
        if (dir($_SERVER["DOCUMENT_ROOT"] . '/local/modules/' . $this->MODULE_ID . '/')) {
            $strMainDir = 'local';
        }

        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . "/" . $strMainDir . "/modules/" . $this->MODULE_ID . "/install/admin", $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin");
        return true;
    }

    function DoInstall() {
        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();
    }

    function DoUninstall() {
        $this->UnInstallFiles();
        $this->UnInstallEvents();
        $this->UnInstallDB();
    }

}

?>
