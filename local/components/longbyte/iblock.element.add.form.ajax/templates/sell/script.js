//requare simple-validate
$(function () {
//    $("form.iblock-element-add-form.sell select").select2({
//        width: "100%",
//        minimumResultsForSearch: -1
//    });

    $("form.iblock-element-add-form.sell").submit(function () {

        var good = LongByteValidate(this, {
            "input[name=PROPERTY\\[NAME\\]\\[0\\]]": /.+/,
            "input[name=PROPERTY\\[129\\]\\[0\\]]": /^[+0-9\s-()]{7,}$/,
            "input[name=PROPERTY\\[130\\]\\[0\\]]": /^.{2,}$/i
        });

        if (good) {

            var data = new FormData();
            $("input, textarea, select, button", this).each(function () {
                if ($(this).attr("type") == "file") {
                    data.append($(this).attr("name"), $(this).prop('files')[0]);
                } else {
                    data.append($(this).attr("name"), $(this).val());
                }
            });

            data.append('ajax', 1);
            data.append('component', "iblock.element.add.form.ajax");
            data.append('template', "opinions_form");
            data.append('need_json', 1);

            $.ajax({
                url: $(this).attr('action'),
                method: "post",
                dataType: "json",
                data: data,
                processData: false, // Не обрабатываем файлы (Don't process the files)
                contentType: false, // Так jQuery скажет серверу что это строковой запрос
                success: function (arResult, textStatus, jqXHR) {
                    if (arResult.ERRORS.length > 0) {
                        var errors = arResult.ERRORS.join("<br>");
                        errors = errors.replace("'", "");
                        ILex_OpenErrorDialog(errors);
                    } else if (arResult.MESSAGE.length > 0) {
                        ILex_OpenMessageDialog(arResult.MESSAGE);
                    }
                }
            });

        }

        return false;


    });
});