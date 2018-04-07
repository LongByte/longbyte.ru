$(function () {
    LongByte_ParalaxResize();
    LongByte_ParalaxScroll();
    $(window).scroll(function () {
        LongByte_ParalaxScroll();
    });
    $(window).resize(function () {
        LongByte_ParalaxResize();
    });
});

function LongByte_ParalaxScroll()
{
    $(".paralax-bg").each(function () {
        var wnd_height = $(window).height();
        var scroll = $(window).scrollTop();
        var cur_top = $(this).offset().top;
        var speed = $(this).data("speed");
        if (speed === undefined)
            speed = 2;
        var y = -(scroll - cur_top + wnd_height) / speed;
        $(this).css("background-position", "center " + y + "px");
    });
}

function LongByte_ParalaxResize()
{
    $(".paralax-bg").each(function () {
        var wnd_height = $(window).height();
        var cur_height = $(this).height();
        var speed = $(this).data("speed");
        if (speed === undefined)
            speed = 2;
        var y = wnd_height / speed * 2 + cur_height;
        $(this).css("background-size", "auto " + y + "px");
    });
}