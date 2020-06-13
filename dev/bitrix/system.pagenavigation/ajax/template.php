<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$ClientID = 'navigation_' . $arResult['NavNum'];

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}
?>
<?
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
$NavRecordGroupPrint = $arResult["NavPageNomer"] + 1;
if ($arResult["NavLastRecordShow"] != $arResult["NavRecordCount"]) {
    ?>
    <div class="navigation ajax">
        <a class="show-more" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $NavRecordGroupPrint ?>"></a><br>
        <script type="text/javascript">
            $(function () {
                navAjaxSender = false;
                $("div.navigation.ajax a").click(function () {
                    if ($(this).hasClass("loading"))
                        return false;
                    $(this).addClass("loading");
                    navAjaxSender = this;
                    blockAjaxSender = $(this).closest(".ajax-nav-container");
                    $.ajax({
                        url: $(this).attr("href"),
                        type: "get",
                        dataType: "html",
                        success: function (data) {
                            var content = $(data);
                            if (!content.hasClass("ajax-nav-container")) {
                                content = content.find(".ajax-nav-container");
                            }
                            $(navAjaxSender).closest(".navigation").remove();
                            $(blockAjaxSender).find("> :last").after(content.html());
                            navAjaxSender = false;
                        }
                    });

                    return false;
                });
            });
        </script>
    </div>
<? } ?>