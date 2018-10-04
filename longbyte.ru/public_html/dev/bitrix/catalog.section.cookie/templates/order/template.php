<?
$this->createFrame()->begin();
$sum = 0;
$CAPTCHA_CODE = htmlspecialchars($GLOBALS["APPLICATION"]->CaptchaGetCode());
?>
<div class="catalog-section-cookie order">
    <div class="top-wrapper">
        <div class="fleft">
            <div class="title">Ваш заказ</div>
            <?
            foreach ($arResult["ITEMS"] as $i => $arItem):
                $sum += $arItem["PRICE"] * $arItem["QUANTITY"];
                ?>
                <div class="item clearfix">
                    <div class="name">
                        <?= ($i + 1) ?>) <?= $arItem["NAME"] ?>. <?= $arItem["FASOVKA"] ?>
                    </div>
                    <div class="price"><?= $arItem["QUANTITY"] ?> X <?= number_format($arItem["PRICE"], 0, ".", " ") ?> P</div>
                </div>
            <? endforeach; ?>
        </div>
        <div class="fright">
            На общую сумму<br>
            <span><?= number_format($sum, 0, ".", " ") ?> P</span>
        </div>
    </div>
    <div class="edit">
        <a href="/cart/" class="sprite-after">Редактировать заказ</a>
    </div>
    <form>
        <div class="description">
            <div class="title">Условия ОПЛАТЫ</div>
            <p>Вы можете оплатить заказ курьеру DPD непосредственно при получении или в пункте выдачи. Возможны варианты
                оплаты пластиковой картой или наличным расчетом. Для юридических лиц предусмотрена оплата по безналичному
                расчету.</p>
            <div class="title">Условия ДОСТАВКИ</div>
            <p>
                SIBARISTICA предоставляет возможность доставки заказов курьером по Санкт-Петербургу (от 99р) и Москве (250р) 
                компанией DPD - ведущей международной службой экспресс-доставки и признанным лидером российского рынка. Ваш
                заказ будет надежно упакован и транспортирован. Вы будете информированы обо всех перемещениях вашего заказа по 
                средствам текстовых сообщений и, также, звонком для уточнения времени прибытия курьера. Доставка 
                свежеобжаренного кофе осуществляется в течении 2-3 дней с момента поступления заказа в курьерскую службу. 
                Самовывоз заказа возможен по адресу Адрес для самовывоза 1. Ежедневно с 12 до 20 часов.</p>
            <p>
                Для жителей других регионов России мы также готовы предложить курьерскую доставку. Просто напишите нам письмо с 
                описанием заказа и адресом получателя, и Вы получите итоговую калькуляцию в кратчайшие сроки. Рады сообщить, что 
                заказы свыше 3000 рублей доставляются бесплатно по Санкт-Петербургу и Москве.
            </p>
            <div class="agree">
                <input type="checkbox" name="agree" id="agree">
                <label for="agree">С условиями ОПЛАТЫ и ДОСТАВКИ ознакомлен</label>
            </div>
        </div>
        <div class="order-form">
            <div class="title">Пожалуйста, укажите ваши контактные данные</div>
            <div class="form-order-form">
                <div class="inner clearfix">
                    <div class="fleft">
                        <div class="input-outer num1">
                            <input type="text" name="name" placeholder="Ваше Имя">
                        </div>
                        <div class="input-outer num2">
                            <input type="email" name="email" placeholder="E-mail">
                        </div>
                        <div class="input-outer num3">
                            <input type="text" name="phone" placeholder="Номер телефона">
                        </div>
                        <div class="input-outer num4">
                            Пожалуйста, укажите адрес доставки<br>
                            <input type="text" name="address" placeholder="Улица, номер дома">
                            <? if (count($arResult["ADDRESS"]) > 0): ?>
                                ... или заберите самостоятельно
                                <select name="pickup">
                                    <? foreach ($arResult["ADDRESS"] as $address): ?>
                                        <option><?= $address ?></option>
                                    <? endforeach; ?>
                                </select>
                            <? endif; ?>
                        </div>
                    </div>
                    <div class="fright">
                        <div class="input-outer num5">
                            <textarea name="comment" placeholder="Дополнительные пожелания и коментарии"></textarea>
                        </div>
                        <div class="input-outer">
                            <img src="/bitrix/tools/captcha.php?captcha_code=<?= $CAPTCHA_CODE ?>" width="180" height="40" alt="CAPTCHA" /> 
                            <input type="hidden" name="captcha_sid" value="<?= $CAPTCHA_CODE ?>" /> 
                            <div class="captcha-input">
                                Введите символы с картинки<br>
                                <input name="captcha_word" type="text">
                            </div>
                        </div>
                        <div class="do-order">
                            <button>Заказать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="ilex-dialog clearfix" id="complite-order">
    <div class="fleft">
        Статус заказа
    </div>
    <div class="fright">
        <div class="text1">
            Ваш заказ <span class="order-num"></span><br>
            принят в работу!
        </div>
        <div class="text2">
            В самое ближайшее время, вам будет отправлен SMS, содержащий
            краткую информацию о заказе и сроках выполнения. 
            <br><br>
            В момент завершения комплектации вашего заказа, на указанный 
            вами почтовый адрес будет отправлено письмо с инструкциями.
        </div>
        <div class="text3">Спасибо!</div>
    </div>
</div>