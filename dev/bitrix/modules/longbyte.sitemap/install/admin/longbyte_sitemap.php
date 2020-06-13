<?

$strMainDir = 'bitrix';
$strModuleName = 'longbyte.sitemap';

if (dir($_SERVER["DOCUMENT_ROOT"] . '/local/modules/' . $strModuleName . '/')) {
    $strMainDir = 'local';
}

require($_SERVER["DOCUMENT_ROOT"] . '/' . $strMainDir . '/modules/' . $strModuleName . '/admin/longbyte_sitemap.php');
?>