$(function () {
    $("a[href^=#]").click(function () {
        ScrollTo($("a[name=" + $(this).attr("href").substring(1) + "]"));
        return false;
    });
});

function ScrollTo(target) {
    if ($(target).length > 0) {
        $("html, body").animate({scrollTop: $(target).first().offset().top});
    }
}