function formatPrice(data) {
    data = (data + "").replace(/(\D)/g, ".");

    var price = Number.prototype.toFixed.call(parseFloat(data) || 0, 2),
        price_sep = ($.isNumeric(+price)) ? parseInt(price, 10) + "" : price;

    price_sep = price_sep.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ");

    return price_sep;
}