if (!window.vueData) {
    window.vueData = {};
}

$(function () {
    $("a[rel^=prettyPhoto]").not(".skip").prettyPhoto({
        showTitle: true,
        autoplay_slideshow: false,
        social_tools: '',
    });
});
