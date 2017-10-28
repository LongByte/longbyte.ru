$(function(){
    LongByte_InitSpoilers();
});

function LongByte_InitSpoilers()
{
    var container = $(".lb-spoiler");
    $(".spoiler-title", container).click(function(){
        $(this).closest(".lb-spoiler").toggleClass("open");
        return false;
    });
}