<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("генератор паролей");
?>

    <script>
        function generatePassword() {
            var length = $('#length').val(),
                charset = $('#charlist').val(),
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            $('#result').val(retVal);
        }

        $(function () {
            $('#gogen').click(function () {
                generatePassword();
            });
        });
    </script>

    Список символов:
    <input type="text" id="charlist" value="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+=-">
    <br>
    Длина:
    <input type="number" id="length" value="10" min="1">
    <br>
    <button id="gogen">Сгенерировать</button>
    <br>
    Пароль:
    <input type="text" id="result" readonly value="">
    <br>


<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>