var curBlock;

$(function () {
    $('[data-block]').click(function () {
        $(this).closest('.container-fluid').fadeOut();
        curBlock = $(this).data('block');
        setTimeout(function () {
            $('body').removeClass().addClass(curBlock);
            $('#' + curBlock).fadeIn();
        }, 400);
    });

    $('a.back').click(function () {
        $('.service.container-fluid, .form').fadeOut();
        setTimeout(function () {
            $('body').removeClass();
            $('.main.container-fluid').fadeIn();
        }, 400);
    });

    var hash = window.location.hash;
    var target = $('[data-block]').filter('[href="' + hash + '"]');
    if (target.length == 1) {
        $(target).closest('.container-fluid').hide();
        curBlock = $(target).data('block');
        $('body').removeClass().addClass(curBlock);
        $('#' + curBlock).show();
    }

    $('select.js-select2').select2({
        minimumResultsForSearch: Infinity,
        width: 'element'
    });
});