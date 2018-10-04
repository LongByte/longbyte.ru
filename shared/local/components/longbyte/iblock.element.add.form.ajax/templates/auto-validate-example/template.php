<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

//$arResult["PROPERTY_REQUIRED"];
//$arResult["PROPERTY_LIST"];

$arResult['CODES'] = array();
foreach ($arResult["PROPERTY_LIST_FULL"] as &$arProp) {
    $arResult['CODES'][$arProp['CODE']] = $arProp['ID'];
}
unset($arProp);

if (!empty($arResult["ERRORS"])):
    ?>
    <? ShowError(implode("<br />", $arResult["ERRORS"])) ?>
    <?
endif;
if (strlen($arResult["MESSAGE"]) > 0):
    ?>
    <? ShowNote($arResult["MESSAGE"]) ?>
<? endif ?>


<form method="post" action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data" class="iblock-element-add-form-ajax <?= $this->__name ?>">
    <?= bitrix_sessid_post() ?>
    <? if ($arParams["MAX_FILE_SIZE"] > 0): ?><input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>" /><? endif ?>
    <!--fields-->
    <label>ФИО<i>*</i>:</label><input name="PROPERTY[NAME][0]" type="text">
    <!--prop-->
    <label>E-mail<i>*</i>:</label><input name="PROPERTY[<?= $arResult['CODES']['EMAIL'] ?>][0]" type="text">
    <!--enum-->
    <div data-code="INSTALL">
        <?
        $propID = $arResult['CODES']['INSTALL'];
        foreach ($arResult["PROPERTY_LIST_FULL"][$propID]['ENUM'] as &$arEnum):
            ?>
            <input name="PROPERTY[<?= $propID ?>][<?= $arEnum['ID'] ?>]" data-sort="<?= $arEnum['SORT'] ?>" value="<?= $arEnum['ID'] ?>" id="property_<?= $arEnum['ID'] ?>" type="checkbox">
            <label for="property_<?= $arEnum['ID'] ?>"><?= $arEnum['VALUE'] ?></label>
            <?
        endforeach;
        unset($arEnum);
        ?>
    </div>                                                    

    <!--captcha-->
    <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
    <label>Защитный код<i>*</i>:</label>
    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" style="float:left;" align="absmiddle" />
    <input name="captcha_word" style="width:48px;" type="text">
    <!--submit-->
    <input value="Отправить" class="bbtn btn-enter" style="float:left; margin-left:-41px !important;" type="submit" name="iblock_submit">
</form>

<?
ob_start();
foreach ($arResult["PROPERTY_REQUIRED"] as &$field):
    ?>
    "[name=PROPERTY\\[<?= $field ?>\\]\\[0\\]]": /.+/,
    <?
endforeach;
unset($field);
$fields = ob_get_contents();
ob_end_clean();
?>

<script>
    $(function () {
        $("form.iblock-element-add-form-ajax.<?= $this->__name ?>").submit(function () {

            var good = LongByteValidate(this, {
<?= $fields ?>
            });
            if (good) {

                var data = new FormData();
                $("input, textarea, select, button", this).each(function () {
                    if ($(this).attr("name") == 'undefined' || $(this).attr("name") == '' || $(this).attr("name") == null)
                        return;
                    if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
                        if ($(this).is(':checked')) {
                            data.append($(this).attr("name"), $(this).val());
                        }
                    } else if ($(this).attr('type') == 'file') {
                        var files = $(this).prop("files");
                        for (var i = 0; i < files.length; i++) {
                            data.append('PROPERTY[' + $(this).attr("name") + '][' + i + ']', '');
                            data.append('PROPERTY_FILE_' + $(this).attr("name") + '_' + i, files[i]);
                        }
                    } else {
                        data.append($(this).attr("name"), $(this).val());
                    }
                });
                data.append('ajax', 1);
                data.append('component', "iblock.element.add.form.ajax");
                data.append('template', "<?= $this->__name ?>");
                data.append('need_json', 1);
                $.ajax({
                    url: $(this).attr('action'),
                    type: "post",
                    dataType: "json",
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (arResult, textStatus, jqXHR) {
                        if (arResult.ERRORS.length > 0) {
                            var errors = arResult.ERRORS.join("<br>");
                            errors = errors.replace("'", "");
                            ILex_OpenErrorDialog(errors);
                        } else {
                            ILex_OpenMessageDialog(arResult.MESSAGE);
                        }
                    }
                });
            }

            return false;
        });
    });
</script>