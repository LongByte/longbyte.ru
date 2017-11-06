(function (a) {
    a.fn.prettyCheckbox = function (b) {
        var c = a.extend({"class": "prettyCheckbox"}, b);
        return this.each(function () {
            var b = a(this), d;
            if (b.next().hasClass(c.class))
                return;
            b.hide().change(function () {
                d = a(this).next();
                d.toggleClass('checked');
            }).after('<div class="' + c.class + (b.is(':checked') ? ' checked' : '') + '"></div>').next().on("click", function () {
                d = a(this);
                a('label[for=' + b.attr('id') + ']').click();
            });
        })
    }

    a.fn.prettyRadiobox = function (b) {
        var c = a.extend({"class": "prettyRadiobox"}, b);
        return this.each(function () {
            var b = a(this), d;
            if (b.next().hasClass(c.class))
                return;
            b.hide().change(function () {
                if (b.is(':checked')) {
                    d = a(this).next();
                    $('input[name=' + b.attr('name') + ']').next().removeClass('checked');
                    d.addClass('checked');
                }
            }).after('<div class="' + c.class + '"></div>').next().on("click", function () {
                b.click()
            });
            b.change();
        })
    }
})(jQuery)