$(function () {
    $('[data-block]').click(function () {
        $(this).closest('.container-fluid').fadeOut();
        $('#' + $(this).data('block')).fadeIn();
    });
});