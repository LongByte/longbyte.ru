<?

use Bitrix\Main\Application;
use Realweb\RealwebMainIncludeTable;
use Realweb\RealwebMainIncludeCategoryTable;

$localPath = getLocalPath("");
$bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

require_once($_SERVER['DOCUMENT_ROOT'] . $bxRoot . '/modules/realweb.main.include/prolog.php'); // пролог модуля

Class realweb_main_include extends CModule {

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
    var $MODULE_ID = 'realweb.main.include';
    private $bxRoot = BX_ROOT;

    /**
     * Конструктор класса. Задаёт начальные значения свойствам.
     */
    function realweb_main_include() {
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
        $this->MODULE_NAME = GetMessage('REALWEB.MAIN.INCLUDE.NAME');
        if (CModule::IncludeModule($this->MODULE_ID)) {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.MAIN.INCLUDE.INSTALL_DESCRIPTION');
        } else {
            $this->MODULE_DESCRIPTION = GetMessage('REALWEB.MAIN.INCLUDE.PREINSTALL_DESCRIPTION');
        }
    }

    function DoInstall() {
        $connection = Application::getInstance()->getConnection();


        $this->InstallFiles();
        $included = false;
        if (!CModule::IncludeModule($this->MODULE_ID)) {
            RegisterModule($this->MODULE_ID);
            $included = CModule::IncludeModule($this->MODULE_ID);
        }
        if ($included) {
            
            $categoryTableName = RealwebMainIncludeCategoryTable::getTableName();
            if (!$connection->isTableExists($categoryTableName)) {
                $connection->createTable($categoryTableName, RealwebMainIncludeCategoryTable::getMap(), array("ID"), array("ID"));
            }

            $tableName = RealwebMainIncludeTable::getTableName();
            if (!$connection->isTableExists($tableName)) {
                $connection->createTable($tableName, RealwebMainIncludeTable::getScalarFields(), array("ID"), array("ID"));
            }
        }
    }

    function InstallFiles() {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.' || $item == 'menu.php')
                        continue;

                    file_put_contents($file = $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/admin/' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . $this->bxRoot . "/components/", true, true);

        return true;
    }

    function DoUninstall() {
        global $DB, $APPLICATION;

        if (CModule::IncludeModule($this->MODULE_ID)) {

            $connection = Application::getInstance()->getConnection();
            
            $categoryTableName = RealwebMainIncludeCategoryTable::getTableName();
            if ($connection->isTableExists($categoryTableName)) {
                $connection->dropTable($categoryTableName);
            }
            
            
            $tableName = RealwebMainIncludeTable::getTableName();
            if ($connection->isTableExists($tableName)) {
                $connection->dropTable($tableName);
            }


            UnRegisterModule($this->MODULE_ID);
        }
        $this->UnInstallFiles();
    }

    function UnInstallFiles() {
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'] . $this->bxRoot . '/modules/' . $this->MODULE_ID . '/admin')) {
            if ($dir = opendir($p)) {
                while (false !== $item = readdir($dir)) {
                    if ($item == '..' || $item == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/admin/' . $item);
                }
                closedir($dir);
            }
        }

        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->bxRoot . "/modules/" . $this->MODULE_ID . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . $this->bxRoot . "/components/");
        return true;
    }

}

?>
