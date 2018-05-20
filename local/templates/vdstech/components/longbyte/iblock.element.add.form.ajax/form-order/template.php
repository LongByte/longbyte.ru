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


<div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <form method="post" action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data" class="iblock-element-add-form-ajax <?= $this->__name ?>">
            <p>
                Если у Вас есть вопросы или Вы уже готовы заказать данную услугу, пожалуйста, заполните поля ниже.
            </p>

            <?= bitrix_sessid_post() ?>
            <? if ($arParams["MAX_FILE_SIZE"] > 0): ?><input type="hidden" name="MAX_FILE_SIZE" value="<?= $arParams["MAX_FILE_SIZE"] ?>" /><? endif ?>
            <p>
                <label>Ваше имя <i>*</i></label>
                <input name="PROPERTY[NAME][0]" type="text">
            </p>
            <p>
                <label>Название компании</label>
                <input name="PROPERTY[<?= $arResult['CODES']['COMPANY_NAME'] ?>][0]" type="text">
            </p>
            <p>
                <label>Телефон <i>*</i></label>
                <input name="PROPERTY[<?= $arResult['CODES']['PHONE'] ?>][0]" type="tel">
            </p>
            <p>
                <label>E-mail</label>
                <input name="PROPERTY[<?= $arResult['CODES']['EMAIL'] ?>][0]" type="email">
            </p>
            <p>
                <label>Комментарий</label>
                <textarea name="PROPERTY[PREVIEW_TEXT][0]"></textarea>
            </p>

            <p>
                <label>Услуга</label>
                <?
                $propID = $arResult['CODES']['PRODUCT'];
                ?>
                <select name="PROPERTY[<?= $propID ?>][0]" class="js-select2">
                    <?
                    foreach ($arResult["PROPERTY_LIST_FULL"][$propID]['ENUM'] as &$arEnum):
                        ?>
                        <option <?= strpos($APPLICATION->GetCurPage(), $arEnum['XML_ID']) !== false ? 'selected' : '' ?> value="<?= $arEnum['ID'] ?>"><?= $arEnum['VALUE'] ?></option>
                        <?
                    endforeach;
                    unset($arEnum);
                    ?>
                </select>
            </p>        
            <?
            if (in_array('DETAIL_TEXT', $arParams["PROPERTY_CODES"])) {
                ?>
                <label>Желаемая конфигурация сервера</label>
                <div class="row">
                    <div class="col-xs-12 col-sm-9">
                        <textarea disabled name="PROPERTY[DETAIL_TEXT][0]" class="config"></textarea>
                    </div>
                    <div class="col-xs-12 col-sm-3">
                        <a href="#" class="button" data-ilex-dialog="#dialog-calc" style="margin: 0">Выбрать</a>
                    </div>
                    <div class="ilex-dialog" id="dialog-calc">
                        <?
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.include", "", Array(
                            "AREA_FILE_RECURSIVE" => "Y",
                            "AREA_FILE_SHOW" => "file",
                            "EDIT_TEMPLATE" => "",
                            "PATH" => "/include/calculate.php"
                            )
                        );
                        ?>
                        <div class="text-center">
                            <a href="#" class="button save-config inline-block">Выбрать</a>
                        </div>
                    </div>
                </div>
                <?
            }
            ?>

            <p>
                <input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" />
                <span class="captcha-line">
                    <span>
                        <label>Защитный код <i>*</i></label>
                    </span>
                    <span>
                        <img class="captcha-img" src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" align="absmiddle" />
                    </span>
                    <span>
                        <input name="captcha_word" type="text" style="width: auto">
                    </span>
                </span>
            </p>
            <p class="popd">
                Нажимая кнопку «Отправить», я даю свое согласие на обработку моих персональных данных, в соответствии с Федеральным законом от 27.07.2006 года №152-ФЗ 
                «О персональных данных», на условиях и для целей, определенных в <a href="/popd/" target="_blank" rel="nofollow">Согласии на обработку персональных данных</a>.
            </p>
            <p>
                <button value="Отправить" type="submit" name="iblock_submit">Отправить</button>
            </p>
        </form>
    </div>
</div>

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
if ($arParams['USE_CAPTCHA'] == 'Y') {
    $fields .= '"[name=captcha_word]": /.+/,';
}
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
                            if (arResult.CAPTCHA_CODE != undefined) {
                                $("form.iblock-element-add-form-ajax.<?= $this->__name ?> input[name=captcha_sid]").val(arResult.CAPTCHA_CODE);
                                $("form.iblock-element-add-form-ajax.<?= $this->__name ?> img.captcha-img").attr('src', '/bitrix/tools/captcha.php?captcha_sid=' + arResult.CAPTCHA_CODE);
                            }
                        } else {
                            ILex_OpenMessageDialog('Спасибо за заявку.<br>Мы свяжемся с Вами в ближайшее время.');
                        }
                    }
                });
            }

            return false;
        });
    });
</script>