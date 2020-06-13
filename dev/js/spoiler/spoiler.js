/**
 * Spoiler by LongByte
 * ilex.chesnokov@gmail.com
 * version 1.1
 */

$(function () {
    LongByte_InitSpoilers();
});

function LongByte_InitSpoilers()
{
    var container = $(".lb-spoiler");
    $(".spoiler-title", container).click(function () {
        var thisSpoiler = $(this).closest(".lb-spoiler");
        thisSpoiler.toggleClass("open");
        var group = thisSpoiler.data('spoiler-group');
        if (group != undefined && group != null && group != '' && thisSpoiler.hasClass('open')) {
            $('.lb-spoiler[data-spoiler-group=' + group + ']').not(thisSpoiler).removeClass('open');
        }
        return false;
    });
}