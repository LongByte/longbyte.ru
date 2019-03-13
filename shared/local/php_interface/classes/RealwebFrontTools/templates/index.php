<!DOCTYPE html>
<html>

<head>
    <title>Макеты верстки RunWay by Realweb</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="shortcut icon" href="_index/favicon.ico" type="image/x-icon">
    <link rel="icon" href="_index/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="_index/style.css">
</head>

<body>
<header>
    <a class="left" href="https://www.realweb.ru" target="_blank">
        <img src="_index/realweb.png" alt="realweb" width="250">
    </a>
    <div class="right">Профессиональная разработка сайтов
        <a href="https://sites.realweb.ru/contacts" target="_blank">Контактная информация</a>
    </div>
    <div class="clear"></div>
</header>
<section>
    <div class="block-text">
        <table class="table-pages">
            <tr class="__filter-ignore">
                <th width="30%">Ссылка</th>
                <th>Название страницы</th>
            </tr>
            <tr class="__filter-ignore">
                <td colspan="2" id="filter">
                    <input type="text" name="" placeholder="Фильтрация (активируется при нажатии клавиши буквы)"
                           id="filter-input"/>
                </td>
            </tr>
            #PAGE_LIST#
        </table>
    </div>
</section>
<script src="_index/jquery-1.12.3.min.js"></script>
<script>
    $('#filter-input').on('input', function () {
        var $this = $(this);
        $('.table-pages .hidden').removeClass('hidden');

        if ($this.val().length < 1) return;

        $('.table-pages tr:not(.__filter-ignore)').each(function () {
            if ($(this).text().toLowerCase().indexOf($.trim($this.val().toLowerCase())) == -1) {
                $(this).addClass('hidden');
            }
        });
    })

</script>
</body>

</html>