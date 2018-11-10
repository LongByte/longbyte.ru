<?

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

$localPath = getLocalPath("");

Class longbyte_metatags extends CModule {

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
    var $MODULE_ID = 'longbyte.metatags';

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function longbyte_metatags() {
        $this->PARTNER_NAME = 'Longbyte';
        $this->PARTNER_URI = 'http://longbyte.ru';
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
        $this->MODULE_NAME = 'Мета-теги для каталога';
    }

    function DoInstall() {
        $this->InstallFiles();
        $this->InstallDB();
        $included = false;
        if (!CModule::IncludeModule($this->MODULE_ID)) {
            RegisterModule($this->MODULE_ID);
            $included = CModule::IncludeModule($this->MODULE_ID);
        }
    }

    function InstallFiles() {
//        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . $this->MODULE_ID . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/local/admin/", true, true);

        return true;
    }

    function InstallDB($arParams = array()) {

        if (Loader::includeModule('iblock')) {

            $arExistIblock = IblockTable::getList(array(
                    'filter' => array('=CODE' => 'longbyte_metatags'),
                    'limit' => 1
                ))->fetch();

            if (!$arExistIblock) {
                $obIBlock = new CIBlock();
                $iIBlockID = $obIBlock->Add(array(
                    "ACTIVE" => 'Y',
                    "NAME" => 'Мета-теги',
                    "CODE" => 'longbyte_metatags',
                    "LIST_PAGE_URL" => '',
                    "DETAIL_PAGE_URL" => '',
                    "IBLOCK_TYPE_ID" => 'seo_texts',    //2do in future
                    "SITE_ID" => Array('s1'),
                    "SORT" => 1000,
                    "GROUP_ID" => Array("2" => "R")
                ));

                if ($iIBlockID) {
                    $obProperty = new CIBlockProperty();
                    $obProperty->Add(array(
                        "NAME" => "Название в хлебных крошках",
                        "ACTIVE" => "Y",
                        "SORT" => 100,
                        "CODE" => "BREAD_NAME",
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );

                    $obProperty->Add(array(
                        "NAME" => "H1",
                        "ACTIVE" => "Y",
                        "SORT" => 200,
                        "CODE" => "H1",
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );

                    $obProperty->Add(array(
                        "NAME" => "Title",
                        "ACTIVE" => "Y",
                        "SORT" => 300,
                        "CODE" => "TITLE",
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );

                    $obProperty->Add(array(
                        "NAME" => "Keywords",
                        "ACTIVE" => "Y",
                        "SORT" => 400,
                        "CODE" => "KEYWORDS",
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );

                    $obProperty->Add(array(
                        "NAME" => "Description",
                        "ACTIVE" => "Y",
                        "SORT" => 500,
                        "CODE" => "DESCRIPTION",
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => $iIBlockID
                        )
                    );
                }
            }
        }

        return true;
    }

    function DoUninstall() {
        global $DB, $APPLICATION;

        if (CModule::IncludeModule($this->MODULE_ID)) {
            UnRegisterModule($this->MODULE_ID);
        }
        $this->UnInstallFiles();
    }

    function UnInstallFiles() {
//        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . "/local/modules/" . $this->MODULE_ID . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/local/admin/");
        return true;
    }

}

?>
