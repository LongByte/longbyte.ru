$(function () {
    $(".swither-control > *").click(function () {
        var index = $(this).index(".swither-control > *");
        $(".swither-items > *").hide().eq(index).show();
        $(this).addClass("active").siblings().removeClass("active");
        return false;
    });
});