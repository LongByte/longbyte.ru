$(function () {
    $('input.style[type=file]').each(function () {
        $(this).hide().after('<div class="form__file"><a href="#">Прикрепить файл</a></div>');
    });

    $(document).on('click', '.form__file a', function () {
        $(this).closest('.form__file').siblings('input.style[type=file]').click();
        return false;
    });

    $(document).on('change', 'input.style[type=file]', function () {
        $(this).siblings('.form__file').find('a').text($(this)[0].files[0].name);
    });
});