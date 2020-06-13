added2basketTimer = -1;
function Add2Cart(id, q, f) {
    f = f || "";
    $.ajax({
        url: "/ajax/getproduct.php",
        type: "post",
        dataType: "html",
        data: {
            id: id,
            ajax: 1,
            q: q,
            f: f
        },
        success: function (data, textStatus, jqXHR) {
            if (data !== "") {
                try {
                    var arCart = $.parseJSON(getCookie("cur_cart"));
                } catch (e) {
                    var arCart = {};
                }

                if (arCart[id] == undefined) {
                    arCart[id] = {QUANTITY: Number(q), FASOVKA: f};
                } else {
                    arCart[id].QUANTITY += Number(q);
                }
                $(".header .cart").replaceWith($(data).filter(".cart"));
                document.cookie = "cur_cart=" + JSON.stringify(arCart) + "; path=/";

                added2basket = $("#added2basket");
                if (added2basket.length < 1) {
                    added2basket = $("<div id='added2basket'>РўРѕРІР°СЂ РґРѕР±Р°РІР»РµРЅ РІ РєРѕСЂР·РёРЅСѓ</div>").appendTo(".header a.cart");
                }
                clearTimeout(added2basketTimer);
                added2basketTimer = setTimeout(function () {
                    added2basket.fadeOut(1000, function () {
                        added2basket.remove();
                    });
                }, 5000);

            }
        }
    });

    return false;
}

function DelFromCart(id, sender) {
    try {
        var arCart = $.parseJSON(getCookie("cur_cart"))
    } catch (e) {
        var arCart = {};
    }

    if (arCart[id] != undefined) {
        delete arCart[id];
    }

    document.cookie = "cur_cart=" + JSON.stringify(arCart) + "; path=/";

    $(sender).closest(".item").remove();
    return false;
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}