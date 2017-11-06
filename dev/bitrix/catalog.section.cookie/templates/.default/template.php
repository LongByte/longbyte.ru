<?
$this->createFrame()->begin();
?>
<a href="/cart/" class="cart <? if (count($arResult["ITEMS"]) > 0): ?>cart--active<? endif; ?>">
    <? if (count($arResult["ITEMS"]) <= 0): ?>
        корзина пуста
    <? else: ?>
        Товаров в корзине <?= count($arResult["ITEMS"]) ?>
    <? endif; ?>
</a>