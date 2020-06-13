$(function () {
    /*$("section.clients .slider").carouFredSel({
     responsive: false,
     items: 4,
     auto: false,
     scroll: {
     fx: "scroll",
     duration: 1000,
     pauseOnHover: true
     },
     prev: "section.clients .arrow.left",
     next: "section.clients .arrow.right"
     });*/
});

function ScrollTo(target)
{
    if ($(target).length > 0)
        $("html,body").animate({scrollTop: $(target).first().offset().top + "px"});
    return false;
}