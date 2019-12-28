'use strict';

if (!window.vueData)
    window.vueData = {};

$(function () {
    $("[data-filter]").click(() => {
        UrlGenerate();
        return false;
    });

    $('[id^=filter] input[name=hide]').change(function () {
        var isChecked = $(this).is(':checked');
        var val = $(this).val();
        if (isChecked) {
            $('body').addClass($(this).val());
            $('[id^=filter]').filter('[value=' + val + ']').not(this).not(':checked').prop('checked', true);
        } else {
            $('body').removeClass($(this).val());
            $('[id^=filter]').filter('[value=' + val + ']').not(this).filter(':checked').prop('checked', false);
        }
        UrlGenerate();
    });

    $('[id^=filter] input[name=line]').change(function () {
        var target = $('a[name=' + $(this).val() + ']').closest('tr')
        if ($(this).is(':checked')) {
            target.show();
        } else {
            target.hide();
            let all = $(this).siblings('[name=line-all]:checked');
            if (all.length)
                all.prop('checked', false);
        }
        UrlGenerate();
    });

    $('[id^=filter] input[name=line-all]').change(function () {
        $(this).siblings('input').not('[value=hideOc], [value=hideComment]').prop('checked', $(this).is(':checked')).change();
        UrlGenerate();
    });

    var params = window.location.hash.substr(1);
    if (!!params) {
        var arParams = params.split(';');
        if (!!arParams[0]) {
            arParams[0] = arParams[0].split(',');
            for (let i in arParams[0]) {
                $('.lb-spoiler.spoiler-type > .spoiler-title[data-filter=' + arParams[0][i] + ']').click();
            }
        }
        if (!!arParams[1]) {
            arParams[1] = arParams[1].split(',');
            for (let i in arParams[1]) {
                $('[id^=filter] [name=hide]').filter('[value=hideOc], [value=hideComment]').filter('[value=' + arParams[1][i] + ']').first().click();
            }
        }
        if (!!arParams[2]) {
            arParams[2] = arParams[2].split(',');
            $('[id^=filter] input').not('[value=hideOc], [value=hideComment]').prop('checked', false).change();
            for (let i in arParams[2]) {
                $('[id^=filter] input').not('[value=hideOc], [value=hideComment]').filter('[value=' + arParams[2][i] + ']').prop('checked', true).change();
            }
        }
    }

    $(document).tooltip();
});

var OpenFilter = (type) => {
    ILex_CloseDialog();
    ILex_OpenDialog('#filter-' + type, {
        width: window.innerWidth > 990 ? 990 : window.innerWidth
    });
    return false;
}

var UrlGenerate = () => {
    let GetUnique = (inputArray) => {
        var outputArray = [];
        for (let i = 0; i < inputArray.length; i++)
        {
            if (($.inArray(inputArray[i], outputArray)) == -1)
            {
                outputArray.push(inputArray[i]);
            }
        }
        return outputArray;
    }

    var url = window.location.origin + '/#';
    var mode = $('.lb-spoiler.spoiler-type.open > .spoiler-title');

    if (mode.length > 0) {
        for (let i = 0; i < mode.length; i++) {
            if (i > 0)
                url += ',';
            url += mode.eq(i).data('filter');
        }
        url += ';';
    }

    var hide = $('[id^=filter] [name=hide]:checked').filter('[value=hideOc], [value=hideComment]');
    var arHide = [];

    for (let i = 0; i < hide.length; i++) {
        arHide.push(hide.eq(i).val());
    }

    arHide = GetUnique(arHide);

    url += arHide.join(',') + ';';

    var spoilers = $('[id^=filter]');
    var arFilter = [];

    for (let i = 0; i < spoilers.length; i++) {
        var spoiler = spoilers.eq(i);
        var filter = $('input:checked', spoiler).not('[value=hideOc], [value=hideComment]');
        for (let j = 0; j < filter.length; j++) {
            if (filter.eq(j).attr('name') == 'line-all')
                break;
            arFilter.push(filter.eq(j).val());
        }
    }

    url += arFilter.join(',');

    window.history.pushState(false, false, url);
}