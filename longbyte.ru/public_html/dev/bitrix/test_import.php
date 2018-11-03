<!DOCTYPE>
<html>
    <head>
        <title>Имитация работы 1С</title>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script>
            $(function () {
                $('form').submit(function () {
                    var arParams = {};
                    var valParams = $('[name=params]').val();
                    if (valParams.length > 0) {
                        valParams = valParams.split('&');
                        if (valParams.length > 0) {
                            for (var i in valParams) {
                                var arDual = valParams[i].split('=');
                                arParams[arDual[0]] = arDual[1];
                            }
                        }
                    }
                    $.ajax({
                        url: $('[name=url]').val(),
                        type: 'get',
                        data: arParams,
                        dataType: 'html',
                        success: function (data) {
                            $('.result').prepend('<br>' + data);
                            if (data.indexOf('progress') != -1) {
                                setTimeout(function(){
                                    $('form').submit();
                                }, 200);
                            }
                        }
                    });
                    return false;
                });
            });
        </script>
    </head>
    <body>
        <form>
            Путь: <input name="url" size="100" value="/bitrix/admin/1c_import.php"><br>
            Параметры: <input name="params" size="100"><br>
            <button type="submit">Отправить</button>
        </form>
        <div class="result"></div>
    </body>
</html>