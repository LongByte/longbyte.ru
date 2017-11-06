var scrolling = false;
var page = 0;
var delta = 0;

$(function () {
    addHandler(window, 'DOMMouseScroll', wheel);
    addHandler(window, 'mousewheel', wheel);
    addHandler(document, 'mousewheel', wheel);
    $(window).scroll(function (event) {
        if (!scrolling) {
            AutoScroll();
        }
    });
});

function addHandler(object, event, handler) {
    if (object.addEventListener) {
        object.addEventListener(event, handler, false);
    }
    else if (object.attachEvent) {
        object.attachEvent('on' + event, handler);
    }
}


function wheel(event) {
    event = event || window.event;
    // Opera и IE работают со свойством wheelDelta
    if (event.wheelDelta) { // В Opera и IE
        delta = event.wheelDelta / 120;
        // В Опере значение wheelDelta такое же, но с противоположным знаком
        if (window.opera)
            delta = -delta; // Дополнительно для Opera
    }
    else if (event.detail) { // Для Gecko
        delta = -event.detail / 3;
    }

    AutoScroll();
}

function AutoScroll()
{
    var selector = "section";
    var count = $(selector).length;
    var scroll_top = $(window).scrollTop();
    var wnd_height = window.innerHeight;

    if (
        delta == -1 //крутим вниз
        &&
        !scrolling  //не анимируем в данный моммент
        &&
        scroll_top + wnd_height > $(selector).eq(page).offset().top + $(selector).eq(page).height()    //проверяем нижнюю границу
        &&
        page + 1 <= count - 1   //проверяем не последний ли экран
        &&
        scroll_top < $(selector).eq(page + 1).offset().top //проверяем, не залезли ли на следующий экран
        ) {
        scrolling = true;
        $("html, body").animate({scrollTop: $(selector).eq(page + 1).offset().top + "px"}, function () {
            if (scrolling) {
                scrolling = false;
                page++;
            }
        });
        return;
    }

    if (
        delta == 1
        &&
        !scrolling
        &&
        page > 0
        &&
        scroll_top < $(selector).eq(page).offset().top     //проверяем верхнюю границу
        &&
        scroll_top + wnd_height > $(selector).eq(page).offset().top
        ) {
        scrolling = true;
        $("html, body").animate({scrollTop: ($(selector).eq(page).offset().top - wnd_height) + "px"}, function () {
            if (scrolling) {
                scrolling = false;
                page--;
            }
        });
        return;
    }

//    }
}