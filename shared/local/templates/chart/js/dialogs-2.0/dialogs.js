/**
 * ILexDialogs by LongByte
 * ilex.chesnokov@gmail.com
 * version 2.2.0b
 */

'use strict';

$(function () {
    ILexDialogs.initDialogs();
});

var ILexDialogs = {
    DialogStack: [],
    oldXT: 0, //Old X Touch
    oldYT: 0, //Old Y Touch
    oldYD: 0, //Old Y Desktop (scroll)
    enable_scroll() {
        $(document).off('mousewheel', ILexDialogs.disable_scroll);
    },
    disable_scroll(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    },
    bindTouchEvents(target, dialog) {
        $(target)
            .on('touchstart', ILexDialogs.onTouchStart)
            .on('touchmove', ILexDialogs.onTouchMove)
            .on('mousewheel', function (event, delta) {
                ILexDialogs.scrollDialog(dialog, {x: 0, y: delta});
                return false;
            });
    },
    initGlobalScrollHandler() {
        if (ILexDialogs.DialogStack.length > 0) {
            let delta = ILexDialogs.oldYD - $(window).scrollTop();
            if (delta != 0) {
                ILexDialogs.scrollDialog(ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1], {x: 0, y: delta}, 1);
            }
        }
        ILexDialogs.oldYD = $(window).scrollTop();
    },
    replaceDialogs() {
        $('.ilex-dialog').each(function () {
            $(this).appendTo('body');
        });
    },
    initDialogs() {
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
    recalcZIndex() {
        $('.ilex-dialog:visible').each(function () {
            let dialog_index = $.inArray('#' + $(this).attr('id'), ILexDialogs.DialogStack);
            $(this).css('z-index', 1100 + 100 * dialog_index);
        });
        $('#ilex-dialog-overlay').css('z-index', 1050 + 100 * (ILexDialogs.DialogStack.length - 1));
    },
    onTouchStart(event) {
        ILexDialogs.oldXT = event.originalEvent.touches[0].pageX;
        ILexDialogs.oldYT = event.originalEvent.touches[0].pageY;
    },
    onTouchMove(event) {
        if (event.originalEvent.touches.length > 1)
            return true;
        event.preventDefault();
        let dialog = ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1];
        let left = event.originalEvent.touches[0].pageX;
        let top = event.originalEvent.touches[0].pageY;
        ILexDialogs.scrollDialog(dialog, {
            x: left - ILexDialogs.oldXT,
            y: top - ILexDialogs.oldYT
        }, 1);
        ILexDialogs.oldXT = left;
        ILexDialogs.oldYT = top;
    },
    scrollDialog(dialog, delta, multi) {
        let options = $(dialog).data('options');
        if (options.disableScroll == false)
            return true;
        multi = multi || 10; //Сила скролла
        let width = $(dialog).outerWidth();
        let wndwidth = window.innerWidth ? window.innerWidth : $(window).width(); //оставил по подобию высоты
        let height = $(dialog).outerHeight();
        let wndheight = window.innerHeight ? window.innerHeight : $(window).height(); //Фикс высоты экрана мобильных устройств
        let left = parseInt($(dialog).css('left'), 10);
        let top = parseInt($(dialog).css('top'), 10);
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
    positionDialog(dialog) {
        dialog = $(dialog);
        let options = dialog.data('options');
        let height = dialog.outerHeight();
        let width = dialog.outerWidth();
        let wndwidth = window.innerWidth ? window.innerWidth : $(window).width(); //оставил по подобию высоты
        let wndheight = window.innerHeight ? window.innerHeight : $(window).height(); //Фикс высота экрана мобильных устройств

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
            let target, targetX, targetY;

            target = $(options.pos.target);
            if (!(target.length > 0))   //при некоторых селекторах нету атрибута length
                target = $('body');

            if (options.pos.targetX != undefined && options.pos.targetX != null) {
                targetX = $(options.pos.targetX);
            }
            if (targetX == undefined || !(targetX.length > 0))
                targetX = target;

            if (options.pos.targetY != undefined && options.pos.targetY != null) {
                targetY = $(options.pos.targetY);
            }
            if (targetY == undefined || !(targetY.length > 0))
                targetY = target;

            let coords = {
                x: 0,
                y: 0
            };
            switch (options.pos.alignX) {
                case 'left':
                    coords.x = targetX.offset().left;
                    break;
                case 'outerLeft':
                    coords.x = targetX.offset().left - dialog.outerWidth();
                    break;
                case 'right':
                    coords.x = targetX.offset().left + targetX.outerWidth() - dialog.outerWidth();
                    break;
                case 'outerRight':
                    coords.x = targetX.offset().left + targetX.outerWidth();
                    break;
                case 'center':
                default :
                    coords.x = targetX.offset().left + (targetX.outerWidth() - dialog.outerWidth()) / 2;
                    break;
            }

            switch (options.pos.alignY) {
                case 'top':
                    coords.y = targetY.offset().top;
                    break;
                case 'outerTop':
                    coords.y = targetY.offset().top - dialog.outerHeight();
                    break;
                case 'bottom':
                    coords.y = targetY.offset().top + targetY.outerHeight() - dialog.outerHeight();
                    break;
                case 'outerBottom':
                    coords.y = targetY.offset().top + targetY.outerHeight();
                    break;
                case 'center':
                default :
                    coords.y = targetY.offset().top + (targetY.outerHeight() - dialog.outerHeight()) / 2;
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
    checkOuterClick(sender) {
        if (ILexDialogs.DialogStack.length > 0) {
            for (let i = 0; i < ILexDialogs.DialogStack.length; i++) {
                let dialog = $(ILexDialogs.DialogStack[i]);
                let options = dialog.data('options');
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
var ILex_OpenDialog = (dialog, options) => {

    if ($(dialog).length == 0)
        return false;
    dialog = $(dialog).first();
    if (options === undefined) {
        options = dialog.data('dialog-options');
    }
    options = options || {};

    if (typeof (options.onBeforeShow) == 'function') {
        let newOptions = options.onBeforeShow(dialog);
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
    if (options.disableScroll) {
        $(document).on('mousewheel', ILexDialogs.disable_scroll);
    }

    dialog.prop('style', '');
    if (options.width == 0)
        options.width = $(dialog).outerWidth();
    //создание заголовка
    if (!options.title) {
        $('.dialog-title', dialog).remove();
    } else {
        if ($('.dialog-title', dialog).length == 0) {
            dialog.prepend('<div class="dialog-title"></div>');
        }
        if (dialog.data('dialog-title') !== undefined) {
            $(".dialog-title", dialog).text(dialog.data('dialog-title'));
        } else if (options.title) {
            $('.dialog-title', dialog).text(options.title);
        }
    }

    //создание оверлея
    var overlay = $('#ilex-dialog-overlay');
    if ($(overlay).length == 0) {
        overlay = $('<div id="ilex-dialog-overlay"></div>');
        overlay.appendTo('body');
        overlay = $('#ilex-dialog-overlay');
    }

    let countVisibleDialog = ILexDialogs.DialogStack.length;
    overlay.css('z-index', 1050 + 100 * countVisibleDialog);
    dialog.css('z-index', 1100 + 100 * countVisibleDialog);
    ILexDialogs.DialogStack.push('#' + $(dialog).attr('id'));
    //создание кнопки close
    if (options.showClose) {
        if ($('.dialog-close', dialog).length == 0) {
            $(dialog).prepend('<div class="dialog-close" title="Закрыть"></div>');
        }
        $('.dialog-close', dialog).unbind('click').click(() => {
            ILex_CloseDialog(dialog, options);
        });
    } else {
        $('.dialog-close', dialog).remove();
    }

    //применение параметров
    if (dialog.css('box-sizing') == 'content-box') {
        dialog.outerWidth(options.width
            - parseInt(dialog.css('padding-left')) - parseInt(dialog.css('padding-right'))
            - parseInt(dialog.css('border-left-width')) - parseInt(dialog.css('border-right-width')));
    } else {
        dialog.outerWidth(options.width);
    }
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
    if (typeof (options.onAfterShow) == 'function') {
        options.onAfterShow(dialog);
    }

    return false;
}

//
// Закрытие диалога
//
var ILex_CloseDialog = (dialog) => {
    let overlay = $('#ilex-dialog-overlay');
    if (dialog === undefined) {
        dialog = $(ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1]);
    }
    let closeDialogoOptions = $(dialog).data('options');
    if (typeof (closeDialogoOptions) == 'undefined') {
        closeDialogoOptions = {};
    }
    if (closeDialogoOptions.showOverlay) {
        $(overlay).off('click touchstart touchmove mousewheel');
    }
    $(dialog).hide();
    //получаем позицию закрываемого диалога в стеке
    let dialog_index = $.inArray('#' + $(dialog).attr('id'), ILexDialogs.DialogStack);
    //удаляем именно ее
    ILexDialogs.DialogStack.splice(dialog_index, 1);
    //утрамбомываем z-indexы диалогов, что бы небыло пробелов     
    ILexDialogs.recalcZIndex();
    //ну это не то что бы каунт, это скорее id последнего
    let countVisibleDialog = ILexDialogs.DialogStack.length - 1;
    if (closeDialogoOptions.showOverlay) {
        $(overlay).on('click', function () {
            ILex_CloseDialog(ILexDialogs.DialogStack[countVisibleDialog]);
        });
    }

    let options = $(dialog).data('options');
    if (typeof (options) == 'undefined') {
        options = {};
    }
    if (options.onClose !== undefined) {
        options.onClose(dialog);
    }

    let newLastDialog = ILexDialogs.DialogStack[ILexDialogs.DialogStack.length - 1];
    if (newLastDialog) {
        ILexDialogs.positionDialog(newLastDialog);

        options = $(newLastDialog).data('options');
        if (options.disableScroll) {
            ILexDialogs.enable_scroll();
        } else {
            $(document).on('mousewheel', ILexDialogs.disable_scroll);
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
var ILex_OpenErrorDialog = (content, options) => {
    let dialog = $('#error-dialog.ilex-dialog');
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
var ILex_OpenMessageDialog = (content, options) => {
    let dialog = $('#message-dialog.ilex-dialog');
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