$(function () {
    $("a[href^=#]").click(function () {
        var href = $(this).attr("href").substring(1);
        if (href.length > 0) {
            ScrollTo($("a[name=" + href + "]"));
            return false;
        }
    });
});

function ScrollTo(target, offset) {
    offset = offset || 0;
    if ($(target).length > 0) {
        var top = $(target).first().offset().top + offset;
        $("html,body").animate({scrollTop: top + "px"});
    }
    return false;
}