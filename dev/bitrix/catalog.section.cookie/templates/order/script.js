$(function () {
    $("div.catalog-section-cookie.order form").submit(function () {

        if (LongByteValidate(this, {
            "input[name=name]": /^.+$/,
            "input[name=phone]": /^[0-9-+()\s]{7,}$/,
            "input[name=email]": /@/,
            "#agree": true
        })) {

            try {
                var arCart = $.parseJSON(getCookie("cur_cart"));
            } catch (e) {
                var arCart = {};
            }

            $("input[name=captcha_word]").removeClass("error");
            $.ajax({
                url: "/ajax/addorder.php",
                type: "post",
                dataType: "json",
                data: {
                    name: $("input[name=name]").val(),
                    address: $("input[name=address]").val(),
                    phone: $("input[name=phone]").val(),
                    email: $("input[name=email]").val(),
                    comment: $("input[name=commane]").val(),
                    captcha_word: $("input[name=captcha_word]").val(),
                    captcha_sid: $("input[name=captcha_sid]").val(),
                    pickup: $("select[name=pickup] option:selected").text(),
                    cart: arCart,
                    submit: 1
                },
                success: function (data, textStatus, jqXHR) {
                    if (data.result == "ok") {
                        $("#complite-order .order-num").text("№" + data.ORDER_ID);
                        document.cookie = "cur_cart=" + JSON.stringify({}) + "; path=/";
                        ILex_OpenDialog("#complite-order", {
                            onClose: function(){
                                window.location = "/";
                            }
                        });
                    } else if (data.error == "captcha") {
                        $("input[name=captcha_word]").addClass("error");
                    }
                }
            });

        }

        return false;
    });
});