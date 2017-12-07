var curBlock;

$(function () {
    $('[data-block]').click(function () {
        $(this).closest('.container-fluid').fadeOut();
        curBlock = $(this).data('block');
        setTimeout(function () {
            $('body').removeClass().addClass(curBlock);
            $('#' + curBlock).fadeIn();
            $('.form').fadeIn();
        }, 400);
    });

    $('a.back').click(function () {
        $('.service.container-fluid, .form').fadeOut();
        setTimeout(function () {
            $('body').removeClass();
            $('.main.container-fluid').fadeIn();
        }, 400);
        return false;
    });
});