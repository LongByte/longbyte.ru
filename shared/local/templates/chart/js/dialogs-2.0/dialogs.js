/**
 * ILexDialogs by LongByte
 * ilex.chesnokov@gmail.com
 * version 2.1.3
 */
$(document).ready(function () {
    ILexDialogs.initDialogs();
});

var ILexDialogs = {
    DialogStack: [],
    oldXT: 0, //Old X Touch
    oldYT: 0, //Old Y Touch
    oldYD: 0, //Old Y Desktop (scroll)
    enable_scroll: function () {
        $(document).off('mousewheel', ILexDialogs.disable_scroll);
    },
    disable_scroll: function (e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    },
    bindTouchEvents: function (target, dialog) {
        $(target)
            .on('touchstart', ILexDialogs.onTouchStart)
            .on('touchmove', ILexDialogs.onTouchMove)
            .on('mousewheel', function (event, delta) {
                ILexDialogs.scrollDialog(dialog, {x: 0, y: delta});
                return false;
            });
    },
    initGlobalScrollHandler: function () {
        if (ILexDialogs.DialogStack.length > 0) {
            var delta = ILexDialogs.oldYD - $(window).scrollTop();
            if (delta != 0) {
                ILexDialogs.scrollDialog(ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1], {x: 0, y: delta}, 1);
            }
        }
        ILexDialogs.oldYD = $(window).scrollTop();
    },
    replaceDialogs: function () {
        $('.ilex-dialog').each(function () {
            $(this).appendTo('body');
        });
    },
    initDialogs: function () {
        ILexDialogs.replaceDialogs();
        //закрытие диалога по ESC
        $(document).keyup(function (event) {
            if (event.keyCode == 27) {
                ILex_CloseDialog();
            }
        });
        //событие прокрутки окна скроллбаром
        ILexDialogs.oldYD = $(window).scrollTop();
        $(window)
            .off('scroll', ILexDialogs.initGlobalScrollHandler)
            .on('scroll', ILexDialogs.initGlobalScrollHandler);
        $('[data-ilex-dialog]').each(function () {
            $(this)
                .unbind('click')
                .click(function () {
                    return ILex_OpenDialog($(this).data('ilex-dialog'))
                });
        });

        $('body').on('click', function (e) {
            ILexDialogs.checkOuterClick(e.target);
        });
    },
    recalcZIndex: function () {
        $('.ilex-dialog:visible').each(function () {
            var dialog_index = $.inArray('#' + $(this).attr('id'), ILexDialogs.DialogStack);
            $(this).css('z-index', 1100 + 100 * dialog_index);
        });
        $('#ilex-dialog-overlay').css('z-index', 1050 + 100 * (ILexDialogs.DialogStack.length - 1));
    },
    onTouchStart: function (event) {
        ILexDialogs.oldXT = event.originalEvent.touches[0].pageX;
        ILexDialogs.oldYT = event.originalEvent.touches[0].pageY;
    },
    onTouchMove: function (event) {
        if (event.originalEvent.touches.length > 1)
            return true;
        event.preventDefault();
        var dialog = ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1];
        var left = event.originalEvent.touches[0].pageX;
        var top = event.originalEvent.touches[0].pageY;
        ILexDialogs.scrollDialog(dialog, {
            x: left - ILexDialogs.oldXT,
            y: top - ILexDialogs.oldYT
        }, 1);
        ILexDialogs.oldXT = left;
        ILexDialogs.oldYT = top;
    },
    scrollDialog: function (dialog, delta, multi) {
        var options = $(dialog).data('options');
        if (options.disableScroll == false)
            return true;
        multi = multi || 10; //Сила скролла
        var width = $(dialog).outerWidth();
        var wndwidth = window.innerWidth ? window.innerWidth : $(window).width(); //оставил по подобию высоты
        var height = $(dialog).outerHeight();
        var wndheight = window.innerHeight ? window.innerHeight : $(window).height(); //Фикс высоты экрана мобильных устройств
        var left = parseInt($(dialog).css('left'), 10);
        var top = parseInt($(dialog).css('top'), 10);
        delta.x *= multi;
        delta.y *= multi;

        if (width > wndwidth) {
            if (width + left >= wndwidth && delta.x < 0) {
                $(dialog).css('left', (left + delta.x) + 'px');
                if (width + left + delta.x < wndwidth) {
                    $(dialog).css('left', (wndwidth - width) + 'px');
                }
            }
            if (left < 0 && delta.x > 0) {
                $(dialog).css('left', (left + delta.x) + 'px');
                if (left + delta.x > 0) {
                    $(dialog).css('left', '0px');
                }
            }
        }

        if (height > wndheight) {
            if (height + top >= wndheight && delta.y < 0) {
                $(dialog).css('top', (top + delta.y) + 'px');
                if (height + top + delta.y < wndheight) {
                    $(dialog).css('top', (wndheight - height) + 'px');
                }
            }
            if (top < 0 && delta.y > 0) {
                $(dialog).css('top', (top + delta.y) + 'px');
                if (top + delta.y > 0) {
                    $(dialog).css('top', '0px');
                }
            }
        }
    },
    positionDialog: function (dialog) {
        dialog = $(dialog);
        var options = dialog.data('options');
        var height = dialog.outerHeight();
        var width = dialog.outerWidth();
        var wndwidth = window.innerWidth ? window.innerWidth : $(window).width(); //оставил по подобию высоты
        var wndheight = window.innerHeight ? window.innerHeight : $(window).height(); //Фикс высота экрана мобильных устройств

        if (options.position == 'fixed') {
            dialog.css({
                'margin-top': (-height / 2) + 'px',
                'margin-left': (-width / 2) + 'px'
            });
            if (wndheight < height || wndwidth < width) {
                if (wndwidth < width)
                    dialog.css('left', 0).css('margin-left', '0px');
                if (wndheight < height)
                    dialog.css('top', 0).css('margin-top', '0px');
                ILexDialogs.bindTouchEvents($(dialog).add('#ilex-dialog-overlay'), dialog)
            } else {
                dialog
                    .css({
                        left: '',
                        top: ''
                    })
                    .add('#ilex-dialog-overlay')
                    .off('touchstart touchmove mousewheel');
            }
        } else if (options.position == 'absolute') {
            var target = $(options.pos.target);
            if (!(target.length > 0))   //при некоторых селекторах нету атрибута length
                target = $('body');
            var coords = {
                x: 0,
                y: 0
            };
            switch (options.pos.alignX) {
                case 'left':
                    coords.x = target.offset().left;
                    break;
                case 'outerLeft':
                    coords.x = target.offset().left - dialog.outerWidth();
                    break;
                case 'right':
                    coords.x = target.offset().left + target.outerWidth() - dialog.outerWidth();
                    break;
                case 'outerRight':
                    coords.x = target.offset().left + target.outerWidth();
                    break;
                case 'center':
                default :
                    coords.x = target.offset().left + (target.outerWidth() - dialog.outerWidth()) / 2;
                    break;
            }

            switch (options.pos.alignY) {
                case 'top':
                    coords.y = target.offset().top;
                    break;
                case 'outerTop':
                    coords.y = target.offset().top - dialog.outerHeight();
                    break;
                case 'bottom':
                    coords.y = target.offset().top + target.outerHeight() - dialog.outerHeight();
                    break;
                case 'outerBottom':
                    coords.y = target.offset().top + target.outerHeight();
                    break;
                case 'center':
                default :
                    coords.y = target.offset().top + (target.outerHeight() - dialog.outerHeight()) / 2;
                    break;
            }

            coords.x += options.pos.offsetX;
            coords.y += options.pos.offsetY;
            dialog.css({
                position: 'absolute',
                left: coords.x + 'px',
                top: coords.y + 'px',
                'margin-top': '0px',
                'margin-left': '0px'
            });
        }
    },
    checkOuterClick: function (sender) {
        if (ILexDialogs.DialogStack.length > 0) {
            for (var i = 0; i < ILexDialogs.DialogStack.length; i++) {
                var dialog = $(ILexDialogs.DialogStack[i]);
                var options = dialog.data('options');
                if (options.closeOnOuterClick && $(sender).closest(dialog).length == 0) {
                    ILex_CloseDialog(dialog);
                }
            }
        }
    }
};
//
// Открытие диалога
//
function ILex_OpenDialog(dialog, options) {

    if ($(dialog).length == 0)
        return false;
    dialog = $(dialog).first();
    if (options === undefined) {
        options = dialog.data('dialog-options');
    }
    options = options || {};

    if (options.onBeforeShow !== undefined) {
        var newOptions = options.onBeforeShow(dialog);
        if (newOptions != undefined)
            options = newOptions;
    }

    options.disableScroll = !(options.disableScroll === false);
    options.showClose = !(options.showClose === false);
    options.showOverlay = !(options.showOverlay === false);
    if (!options.showOverlay) {
        options.closeOnOuterClick = options.closeOnOuterClick === true;
        options.toggle = options.toggle === true;
    } else {
        options.closeOnOuterClick = false;
        options.toggle = false;
    }
    options.width = options.width || 0;
    options.position = options.position || 'fixed';
    if (options.position == 'absolute') {
        options.disableScroll = false;
        options.pos = options.pos || {};
        options.pos.offsetX = options.pos.offsetX || 0;
        options.pos.offsetY = options.pos.offsetY || 0;
        options.pos.alignX = options.pos.alignX || 'center';
        options.pos.alignY = options.pos.alignY || 'top';
        options.pos.target = options.pos.target || 'body';
    }
    dialog.data('options', options);

    if ($.inArray('#' + dialog.attr('id'), ILexDialogs.DialogStack) !== -1) {
        if (!options.toggle) {
            return false;
        } else {
            ILex_CloseDialog(dialog);
            return false;
        }
    }

    ILexDialogs.enable_scroll();
    if (options.disableScroll != false) {
        $(document).on('mousewheel', ILexDialogs.disable_scroll);
    }

    if (options.width == 0)
        options.width = $(dialog).outerWidth();
    //создание заголовка
    if (options.title !== false) {
        if ($('.dialog-title', dialog).length == 0) {
            dialog.prepend('<div class="dialog-title"></div>');
        }
        if (dialog.data('dialog-title') !== undefined)
            $(".dialog-title", dialog).text(dialog.data('dialog-title'));
        else if (options.title != '')
            $('.dialog-title', dialog).text(options.title);
    } else {
        $('.dialog-title', dialog).remove();
    }

    //создание оверлея
    var overlay = $('#ilex-dialog-overlay');
    if ($(overlay).length == 0) {
        overlay = $('<div id="ilex-dialog-overlay"></div>');
        overlay.appendTo('body');
        overlay = $('#ilex-dialog-overlay');
    }

    var countVisibleDialog = ILexDialogs.DialogStack.length;
    overlay.css('z-index', 1050 + 100 * countVisibleDialog);
    dialog.css('z-index', 1100 + 100 * countVisibleDialog);
    ILexDialogs.DialogStack.push('#' + $(dialog).attr('id'));
    //создание кнопки close
    if (options.showClose) {
        if ($('.dialog-close', dialog).length == 0) {
            $(dialog).prepend('<div class="dialog-close" title="Закрыть"></div>');
        }
        $('.dialog-close', dialog).unbind('click').click(function () {
            ILex_CloseDialog(dialog, options);
        });
    } else {
        $('.dialog-close', dialog).remove();
    }

    //применение параметров
    dialog.width(options.width);
    //позиционирование диалога
    ILexDialogs.positionDialog(dialog);
    if (options.showOverlay) {
        //клик на оверлее - закрытие диалога
        overlay
            .off('click')
            .on('click', function () {
                ILex_CloseDialog(dialog);
            });
        overlay.show();
    }
    dialog.show();
    if (options.onAfterShow !== undefined) {
        options.onAfterShow(dialog);
    }

    return false;
}

//
// Закрытие диалога
//
function ILex_CloseDialog(dialog) {
    var overlay = $('#ilex-dialog-overlay');
    if (dialog === undefined) {
        dialog = $(ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1]);
    }
    var closeDialogoOptions = $(dialog).data('options');
    if (typeof (closeDialogoOptions) == 'undefined') {
        closeDialogoOptions = {};
    }
    if (closeDialogoOptions.showOverlay) {
        $(overlay).off('click touchstart touchmove mousewheel');
    }
    $(dialog).hide();
    //получаем позицию закрываемого диалога в стеке
    var dialog_index = $.inArray('#' + $(dialog).attr('id'), ILexDialogs.DialogStack);
    //удаляем именно ее
    ILexDialogs.DialogStack.splice(dialog_index, 1);
    //утрамбомываем z-indexы диалогов, что бы небыло пробелов     
    ILexDialogs.recalcZIndex();
    //ну это не то что бы каунт, это скорее id последнего
    var countVisibleDialog = ILexDialogs.DialogStack.length - 1;
    if (closeDialogoOptions.showOverlay) {
        $(overlay).on('click', function () {
            ILex_CloseDialog(ILexDialogs.DialogStack[countVisibleDialog]);
        });
    }

    var options = $(dialog).data('options');
    if (typeof (options) == 'undefined') {
        options = {};
    }
    if (options.onClose !== undefined) {
        options.onClose(dialog);
    }

    var newLastDialog = ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1];
    if (newLastDialog) {
        ILexDialogs.positionDialog(newLastDialog);

        var options = $(newLastDialog).data('options');
        if (options.disableScroll != false) {
            $(document).on('mousewheel', ILexDialogs.disable_scroll);
        } else {
            ILexDialogs.enable_scroll();
        }
    }

    //если открытых диалогов больше нет
    if ($('.ilex-dialog:visible').length == 0) {
        ILexDialogs.enable_scroll();
        $(overlay).hide();
    }
}

//
// Открывает диалог ошибки
//
function ILex_OpenErrorDialog(content, options) {
    var dialog = $('#error-dialog.ilex-dialog');
    if (dialog.length <= 0) {
        $('body').append('<div class="ilex-dialog" id="error-dialog"></div>');
        dialog = $('#error-dialog.ilex-dialog');
    }

    if ($('.dialog-content', dialog).length == 0) {
        $(dialog).append("<div class='dialog-content'></div>");
    }

    $('.dialog-content', dialog).html(content);
    ILex_OpenDialog(dialog, options);
}

//
// Открывает диалог сообщения
//
function ILex_OpenMessageDialog(content, options) {
    var dialog = $('#message-dialog.ilex-dialog');
    if (dialog.length == 0) {
        $('body').append('<div class="ilex-dialog" id="message-dialog"></div>');
        dialog = $('#message-dialog.ilex-dialog');
    }

    if ($('.dialog-content', dialog).length == 0) {
        $(dialog).append("<div class='dialog-content'></div>");
    }

    $('.dialog-content', dialog).html(content);
    ILex_OpenDialog(dialog, options);
}