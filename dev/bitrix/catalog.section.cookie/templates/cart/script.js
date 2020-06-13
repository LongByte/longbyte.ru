$(function () {
    $("input[name^=quantity]", "div.catalog-section-cookie.cart").on('input', function () {
        var __this = $(this);
        var q = __this.val();
        var id = __this.data("id");

        if (q == "" || isNaN(q) || q <= 0) {
            q = 1;
            __this.val(q);
        }

        try {
            var arCart = $.parseJSON(getCookie("cur_cart"));
        } catch (e) {
            var arCart = {};
        }

        if (arCart[id] != undefined) {
            arCart[id] = {QUANTITY: Number(q)};
        }

        document.cookie = "cur_cart=" + JSON.stringify(arCart) + "; path=/";

        var sum = 0;
        $("input[name^=quantity]", "div.catalog-section-cookie.cart").each(function () {
            sum += $(this).data("price") * $(this).val();
        });

        $('.PriceSum').html('<span>Итого</span> ' + sum + ' P');
    });

});

