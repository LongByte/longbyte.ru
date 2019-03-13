<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_js.php");

global $APPLICATION;
$FN = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["FORM_NAME"]);
$FC = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["PROPERTY_INPUT"]);
$JSON_INPUT = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $_REQUEST["JSON_INPUT"]);

$APPLICATION->ShowAjaxHead();
CUtil::JSPostUnescape();
$obJSPopup = new CJSPopup('', array(
    'TITLE' => "Внешняя ссылка"
        )
);
$strWarning = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $EXTERNAL_LINK = $_REQUEST['EXTERNAL_LINK'];
    if (strlen($EXTERNAL_LINK) == 0) {
        $strWarning .= "Введите значение внешней ссылки<br>";
    } else {
        $DATA = array(
            'TYPE' => 'EXTERNAL_LINK',
            'LINK' => $EXTERNAL_LINK,
        );
        ?>
        <script>
            top.<? echo $FN; ?>["<? echo $FC; ?>"].value = '<?php echo serialize($DATA); ?>';
        </script>
        <?php
        $obJSPopup->Close(false);
    }
}


$obJSPopup->ShowTitlebar();
?>
<?php
if ($strWarning !== "") {
    $obJSPopup->ShowValidationError($strWarning);
}

$obJSPopup->StartContent();
?>
<form action="">
    <input type="text" size="90" name="EXTERNAL_LINK" id="EXTERNAL_LINK_VALUE" />
</form>
<script>
    BX.ready(function () {
        current_value = JSON.parse(top.<? echo $FN; ?>["<? echo $JSON_INPUT; ?>"].value);
        if (current_value.LINK) {
            BX('EXTERNAL_LINK_VALUE').value = current_value.LINK;
        }
    });
</script>
<? $obJSPopup->StartButtons(); ?>
<input type="button" value="Сохранить" onclick="<?= $obJSPopup->jsPopup ?>.PostParameters('<?= '?action=save&amp;FORM_NAME=' . $FN . '&amp;JSON_INPUT=' . $JSON_INPUT . '&amp;PROPERTY_INPUT=' . $FC ?>');" title="Сохранить" name="save" class="adm-btn-save" />
<input type="button" value="Отменить" onclick="<?= $obJSPopup->jsPopup ?>.CloseDialog()" title="Отменить" />
<? $obJSPopup->EndButtons(); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_js.php"); ?>

