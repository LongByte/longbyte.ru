<?
$this->createFrame()->begin();
$sum = 0;
?>
<div class="catalog-section-cookie cart">
    <div class="item head">
        <div class="number"></div>
        <div class="picture"></div>
        <div class="name">Наименование</div>
        <div class="quantity">Количество</div>
        <div class="price">Цена</div>
        <div class="del"></div>
    </div>
    <?
    foreach ($arResult["ITEMS"] as $i => $arItem):
        $sum += $arItem["PRICE"] * $arItem["QUANTITY"];
        ?>
        <div class="item clearfix">
            <div class="number"><?= ($i + 1) ?></div>
            <div class="picture" style="background-image: url(<?= $arItem["PREVIEW_PICTURE"]["src"] ?>)"></div>
            <div class="name">
                <span><?= $arItem["NAME"] ?></span><br>
                <?= $arItem["PREVIEW_TEXT"] ?><br>
                <span><?= $arItem["FASOVKA"] ?></span>
            </div>
            <div class="quantity">
                <input class="quantity_<?= $arItem["ID"] ?>" name="quantity_<?= $arItem["ID"] ?>" value="<?= $arItem["QUANTITY"] ?>" data-id="<?= $arItem["ID"] ?>" data-price="<?= $arItem["PRICE"] ?>">
            </div>
            <div class="price"><?= number_format($arItem["PRICE"], 0, ".", " ") ?> P</div>
            <div class="del">
                <a href="#" class="sprite" onclick="return DelFromCart('<?= $arItem["ID"] ?>', this)"></a>
                <input name="item" type="hidden" value="<?= $arItem["ID"] ?>">
            </div>
        </div>
    <? endforeach; ?>
    <div class="summary">
        <div class="price PriceSum">
            <span>Итого</span> <?= $sum ?> P
        </div>
        <button class="do-order" onclick="window.location = '/order/';">Оформить заказ</button>
    </div>
</div>