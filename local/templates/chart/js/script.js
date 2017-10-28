$(function () {
    $(".mode a[data-filter]").click(function () {
        var mode = $(this).data("filter");
        if (mode == "all") {
            $("div.graphic").show();
        } else {
            $("div.graphic").show().not("." + mode).hide();
        }
        $('div.mode').data('active', mode);
        UrlGenerate();
        return false;
    });

    $('#filter .spoiler-title').click(function () {
        OpenFilter();
    });

    $('#filter input[name=hide]').change(function () {
        if ($(this).is(':checked')) {
            $('body').addClass($(this).val());
        } else {
            $('body').removeClass($(this).val());
        }
        UrlGenerate();
    });

    $('#filter input[name=line]').change(function () {
        var target = $('a[name=' + $(this).val() + ']').closest('tr')
        if ($(this).is(':checked')) {
            target.show();
        } else {
            target.hide();
        }
        UrlGenerate();
    });

    $('#filter input[name=line-all]').change(function () {
        $(this).siblings('input').prop('checked', $(this).is(':checked')).change();
        UrlGenerate();
    });

    var params = window.location.hash.substr(1);
    if (params != '') {
        var arParams = params.split(';');
        if (arParams[0] != '')
            $('div.mode [data-filter=' + arParams[0] + ']').click();
        if (arParams[1] != '') {
            arParams[1] = arParams[1].split(',');
            for (var i in arParams[1]) {
                $('#filter [name=hide]').filter('[value=' + arParams[1][i] + ']').click();
            }
        }
        if (arParams[2] != '') {
            arParams[2] = arParams[2].split(',');
            $('#filter .lb-spoiler input').prop('checked', false).change();
            for (var i in arParams[2]) {
                $('#filter .lb-spoiler input').filter('[value=' + arParams[2][i] + ']').prop('checked', true).change();
            }
        }

    }

    $(document).tooltip();
});

function OpenFilter() {
    ILex_CloseDialog('#filter');
    ILex_OpenDialog('#filter', {
        width: window.innerWidth > 990 ? 990 : window.innerWidth
    });
    return false;
}

function UrlGenerate()
{

    var url = window.location.origin + '/#';
    var mode = $('div.mode').data('active');
    if (mode == 'all')
        mode = '';

    url += mode + ';';

    var hide = $('#filter [name=hide]:checked');
    var arHide = [];

    for (var i = 0; i < hide.length; i++) {
        arHide.push(hide.eq(i).val());
    }

    url += arHide.join(',') + ';';

    var spoilers = $('#filter .lb-spoiler');
    var arFilter = [];

    for (var i = 0; i < spoilers.length; i++) {
        var spoiler = spoilers.eq(i);
        var filter = $('input:checked', spoiler);
        for (var j = 0; j < filter.length; j++) {
            arFilter.push(filter.eq(j).val());
            if (filter.eq(j).attr('name') == 'line-all')
                break;
        }
    }

    url += arFilter.join(',');

    window.history.pushState(false, false, url);
}