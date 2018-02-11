/**
 * 
 * @param {type} form - html selector
 * @param {type} validate - json list obj: pattern
 * @param {type} error_calback - function
 * @param {type} success_calback - function
 * @returns {undefined}
 */
function LongByteValidate(form, validate, error_calback, success_calback)
{
    form = $(form).first();
    var error = false;
    var errorTarget;
    for (selector in validate) {
        errorTarget = $(selector, form).data('error-target');
        if (errorTarget == undefined) {
            if ($(selector, form).attr("type") == "checkbox" || $(selector, form).attr("type") == "radio") {
                errorTarget = $("label[for=" + $(selector, form).attr("id") + "]", form);
            } else if ($(selector, form).length > 0 && $(selector, form)[0].tagName == 'SELECT' && $(selector, form).next().hasClass('select2')) {
                errorTarget = $(selector, form).next();
            } else {
                errorTarget = $(selector, form);
            }
        } else {
            errorTarget = $(errorTarget);
        }
        errorTarget.siblings('.error-text').hide();
        errorTarget.removeClass("error");
        if ($(selector, form).attr("type") == "checkbox") {
            if ($(selector, form).is(":checked") != validate[selector]) {
                error = true;
                errorTarget.addClass("error");
                errorTarget.siblings('.error-text').show();
            }
        } else if ($(selector, form).attr("type") == "radio") {
            if ($(selector, form).filter(":checked").length != validate[selector]) {
                error = true;
                $(selector, form).addClass("error");
                errorTarget.addClass("error");
                errorTarget.siblings('.error-text').show();
            }
        } else if ($(selector, form).length > 0 && $(selector, form)[0].tagName == 'SELECT' && $(selector, form).next().hasClass('select2')) {
            if (!validate[selector].test($(selector, form).val())) {
                error = true;
                errorTarget.addClass('error');
            }
        } else if (!validate[selector].test($(selector, form).val())) {
            error = true;
            errorTarget.addClass("error");
            errorTarget.siblings('.error-text').show();
        }
    }
    if (error) {
        if (typeof (error_calback) == "function")
            error_calback();
        return false;
    } else {
        if (typeof (success_calback) == "function")
            success_calback();
        return true;
    }
}