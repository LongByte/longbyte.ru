/*! jQuery UI - v1.12.1 - 2017-03-19
 * http://jqueryui.com
 * Includes: widget.js, position.js, keycode.js, unique-id.js, widgets/tooltip.js
 * Copyright jQuery Foundation and other contributors; Licensed MIT */

(function (factory) {
    if (typeof define === "function" && define.amd) {

        // AMD. Register as an anonymous module.
        define(["jquery"], factory);
    } else {

        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    $.ui = $.ui || {};

    var version = $.ui.version = "1.12.1";


    /*!
     * jQuery UI Widget 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Widget
//>>group: Core
//>>description: Provides a factory for creating stateful widgets with a common API.
//>>docs: http://api.jqueryui.com/jQuery.widget/
//>>demos: http://jqueryui.com/widget/


    var widgetUuid = 0;
    var widgetSlice = Array.prototype.slice;

    $.cleanData = (function (orig) {
        return function (elems) {
            var events,
                elem,
                i;
            for (i = 0; (elem = elems[i]) != null; i++) {
                try {

                    // Only trigger remove when necessary to save time
                    events = $._data(elem, "events");
                    if (events && events.remove) {
                        $(elem).triggerHandler("remove");
                    }

                    // Http://bugs.jquery.com/ticket/8235
                } catch (e) {
                }
            }
            orig(elems);
        };
    })($.cleanData);

    $.widget = function (name, base, prototype) {
        var existingConstructor,
            constructor,
            basePrototype;

        // ProxiedPrototype allows the provided prototype to remain unmodified
        // so that it can be used as a mixin for multiple widgets (#8876)
        var proxiedPrototype = {};

        var namespace = name.split(".")[0];
        name = name.split(".")[1];
        var fullName = namespace + "-" + name;

        if (!prototype) {
            prototype = base;
            base = $.Widget;
        }

        if ($.isArray(prototype)) {
            prototype = $.extend.apply(null, [{}].concat(prototype));
        }

        // Create selector for plugin
        $.expr[":"][fullName.toLowerCase()] = function (elem) {
            return !!$.data(elem, fullName);
        };

        $[namespace] = $[namespace] || {};
        existingConstructor = $[namespace][name];
        constructor = $[namespace][name] = function (options, element) {

            // Allow instantiation without "new" keyword
            if (!this._createWidget) {
                return new constructor(options, element);
            }

            // Allow instantiation without initializing for simple inheritance
            // must use "new" keyword (the code above always passes args)
            if (arguments.length) {
                this._createWidget(options, element);
            }
        };

        // Extend with the existing constructor to carry over any static properties
        $.extend(constructor, existingConstructor, {
            version: prototype.version,
            // Copy the object used to create the prototype in case we need to
            // redefine the widget later
            _proto: $.extend({}, prototype),
            // Track widgets that inherit from this widget in case this widget is
            // redefined after a widget inherits from it
            _childConstructors: []
        });

        basePrototype = new base();

        // We need to make the options hash a property directly on the new instance
        // otherwise we'll modify the options hash on the prototype that we're
        // inheriting from
        basePrototype.options = $.widget.extend({}, basePrototype.options);
        $.each(prototype, function (prop, value) {
            if (!$.isFunction(value)) {
                proxiedPrototype[prop] = value;
                return;
            }
            proxiedPrototype[prop] = (function () {
                function _super() {
                    return base.prototype[prop].apply(this, arguments);
                }

                function _superApply(args) {
                    return base.prototype[prop].apply(this, args);
                }

                return function () {
                    var __super = this._super;
                    var __superApply = this._superApply;
                    var returnValue;

                    this._super = _super;
                    this._superApply = _superApply;

                    returnValue = value.apply(this, arguments);

                    this._super = __super;
                    this._superApply = __superApply;

                    return returnValue;
                };
            })();
        });
        constructor.prototype = $.widget.extend(basePrototype, {
            // TODO: remove support for widgetEventPrefix
            // always use the name + a colon as the prefix, e.g., draggable:start
            // don't prefix for widgets that aren't DOM-based
            widgetEventPrefix: existingConstructor ? (basePrototype.widgetEventPrefix || name) : name
        }, proxiedPrototype, {
            constructor: constructor,
            namespace: namespace,
            widgetName: name,
            widgetFullName: fullName
        });

        // If this widget is being redefined then we need to find all widgets that
        // are inheriting from it and redefine all of them so that they inherit from
        // the new version of this widget. We're essentially trying to replace one
        // level in the prototype chain.
        if (existingConstructor) {
            $.each(existingConstructor._childConstructors, function (i, child) {
                var childPrototype = child.prototype;

                // Redefine the child widget using the same prototype that was
                // originally used, but inherit from the new version of the base
                $.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor,
                    child._proto);
            });

            // Remove the list of existing child constructors from the old constructor
            // so the old child constructors can be garbage collected
            delete existingConstructor._childConstructors;
        } else {
            base._childConstructors.push(constructor);
        }

        $.widget.bridge(name, constructor);

        return constructor;
    };

    $.widget.extend = function (target) {
        var input = widgetSlice.call(arguments, 1);
        var inputIndex = 0;
        var inputLength = input.length;
        var key;
        var value;

        for (; inputIndex < inputLength; inputIndex++) {
            for (key in input[inputIndex]) {
                value = input[inputIndex][key];
                if (input[inputIndex].hasOwnProperty(key) && value !== undefined) {

                    // Clone objects
                    if ($.isPlainObject(value)) {
                        target[key] = $.isPlainObject(target[key]) ?
                            $.widget.extend({}, target[key], value) :
                            // Don't extend strings, arrays, etc. with objects
                            $.widget.extend({}, value);

                        // Copy everything else by reference
                    } else {
                        target[key] = value;
                    }
                }
            }
        }
        return target;
    };

    $.widget.bridge = function (name, object) {
        var fullName = object.prototype.widgetFullName || name;
        $.fn[name] = function (options) {
            var isMethodCall = typeof options === "string";
            var args = widgetSlice.call(arguments, 1);
            var returnValue = this;

            if (isMethodCall) {

                // If this is an empty collection, we need to have the instance method
                // return undefined instead of the jQuery instance
                if (!this.length && options === "instance") {
                    returnValue = undefined;
                } else {
                    this.each(function () {
                        var methodValue;
                        var instance = $.data(this, fullName);

                        if (options === "instance") {
                            returnValue = instance;
                            return false;
                        }

                        if (!instance) {
                            return $.error("cannot call methods on " + name +
                                " prior to initialization; " +
                                "attempted to call method '" + options + "'");
                        }

                        if (!$.isFunction(instance[options]) || options.charAt(0) === "_") {
                            return $.error("no such method '" + options + "' for " + name +
                                " widget instance");
                        }

                        methodValue = instance[options].apply(instance, args);

                        if (methodValue !== instance && methodValue !== undefined) {
                            returnValue = methodValue && methodValue.jquery ?
                                returnValue.pushStack(methodValue.get()) :
                                methodValue;
                            return false;
                        }
                    });
                }
            } else {

                // Allow multiple hashes to be passed on init
                if (args.length) {
                    options = $.widget.extend.apply(null, [options].concat(args));
                }

                this.each(function () {
                    var instance = $.data(this, fullName);
                    if (instance) {
                        instance.option(options || {});
                        if (instance._init) {
                            instance._init();
                        }
                    } else {
                        $.data(this, fullName, new object(options, this));
                    }
                });
            }

            return returnValue;
        };
    };

    $.Widget = function ( /* options, element */) {
    };
    $.Widget._childConstructors = [];

    $.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        defaultElement: "<div>",
        options: {
            classes: {},
            disabled: false,
            // Callbacks
            create: null
        },
        _createWidget: function (options, element) {
            element = $(element || this.defaultElement || this)[0];
            this.element = $(element);
            this.uuid = widgetUuid++;
            this.eventNamespace = "." + this.widgetName + this.uuid;

            this.bindings = $();
            this.hoverable = $();
            this.focusable = $();
            this.classesElementLookup = {};

            if (element !== this) {
                $.data(element, this.widgetFullName, this);
                this._on(true, this.element, {
                    remove: function (event) {
                        if (event.target === element) {
                            this.destroy();
                        }
                    }
                });
                this.document = $(element.style ?
                    // Element within the document
                    element.ownerDocument :
                    // Element is window or document
                    element.document || element);
                this.window = $(this.document[0].defaultView || this.document[0].parentWindow);
            }

            this.options = $.widget.extend({},
                this.options,
                this._getCreateOptions(),
                options);

            this._create();

            if (this.options.disabled) {
                this._setOptionDisabled(this.options.disabled);
            }

            this._trigger("create", null, this._getCreateEventData());
            this._init();
        },
        _getCreateOptions: function () {
            return {};
        },
        _getCreateEventData: $.noop,
        _create: $.noop,
        _init: $.noop,
        destroy: function () {
            var that = this;

            this._destroy();
            $.each(this.classesElementLookup, function (key, value) {
                that._removeClass(value, key);
            });

            // We can probably remove the unbind calls in 2.0
            // all event bindings should go through this._on()
            this.element
                .off(this.eventNamespace)
                .removeData(this.widgetFullName);
            this.widget()
                .off(this.eventNamespace)
                .removeAttr("aria-disabled");

            // Clean up events and states
            this.bindings.off(this.eventNamespace);
        },
        _destroy: $.noop,
        widget: function () {
            return this.element;
        },
        option: function (key, value) {
            var options = key;
            var parts;
            var curOption;
            var i;

            if (arguments.length === 0) {

                // Don't return a reference to the internal hash
                return $.widget.extend({}, this.options);
            }

            if (typeof key === "string") {

                // Handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
                options = {};
                parts = key.split(".");
                key = parts.shift();
                if (parts.length) {
                    curOption = options[key] = $.widget.extend({}, this.options[key]);
                    for (i = 0; i < parts.length - 1; i++) {
                        curOption[parts[i]] = curOption[parts[i]] || {};
                        curOption = curOption[parts[i]];
                    }
                    key = parts.pop();
                    if (arguments.length === 1) {
                        return curOption[key] === undefined ? null : curOption[key];
                    }
                    curOption[key] = value;
                } else {
                    if (arguments.length === 1) {
                        return this.options[key] === undefined ? null : this.options[key];
                    }
                    options[key] = value;
                }
            }

            this._setOptions(options);

            return this;
        },
        _setOptions: function (options) {
            var key;

            for (key in options) {
                this._setOption(key, options[key]);
            }

            return this;
        },
        _setOption: function (key, value) {
            if (key === "classes") {
                this._setOptionClasses(value);
            }

            this.options[key] = value;

            if (key === "disabled") {
                this._setOptionDisabled(value);
            }

            return this;
        },
        _setOptionClasses: function (value) {
            var classKey,
                elements,
                currentElements;

            for (classKey in value) {
                currentElements = this.classesElementLookup[classKey];
                if (value[classKey] === this.options.classes[classKey] ||
                    !currentElements ||
                    !currentElements.length) {
                    continue;
                }

                // We are doing this to create a new jQuery object because the _removeClass() call
                // on the next line is going to destroy the reference to the current elements being
                // tracked. We need to save a copy of this collection so that we can add the new classes
                // below.
                elements = $(currentElements.get());
                this._removeClass(currentElements, classKey);

                // We don't use _addClass() here, because that uses this.options.classes
                // for generating the string of classes. We want to use the value passed in from
                // _setOption(), this is the new value of the classes option which was passed to
                // _setOption(). We pass this value directly to _classes().
                elements.addClass(this._classes({
                    element: elements,
                    keys: classKey,
                    classes: value,
                    add: true
                }));
            }
        },
        _setOptionDisabled: function (value) {
            this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value);

            // If the widget is becoming disabled, then nothing is interactive
            if (value) {
                this._removeClass(this.hoverable, null, "ui-state-hover");
                this._removeClass(this.focusable, null, "ui-state-focus");
            }
        },
        enable: function () {
            return this._setOptions({disabled: false});
        },
        disable: function () {
            return this._setOptions({disabled: true});
        },
        _classes: function (options) {
            var full = [];
            var that = this;

            options = $.extend({
                element: this.element,
                classes: this.options.classes || {}
            }, options);

            function processClassString(classes, checkOption) {
                var current,
                    i;
                for (i = 0; i < classes.length; i++) {
                    current = that.classesElementLookup[classes[i]] || $();
                    if (options.add) {
                        current = $($.unique(current.get().concat(options.element.get())));
                    } else {
                        current = $(current.not(options.element).get());
                    }
                    that.classesElementLookup[classes[i]] = current;
                    full.push(classes[i]);
                    if (checkOption && options.classes[classes[i]]) {
                        full.push(options.classes[classes[i]]);
                    }
                }
            }

            this._on(options.element, {
                "remove": "_untrackClassesElement"
            });

            if (options.keys) {
                processClassString(options.keys.match(/\S+/g) || [], true);
            }
            if (options.extra) {
                processClassString(options.extra.match(/\S+/g) || []);
            }

            return full.join(" ");
        },
        _untrackClassesElement: function (event) {
            var that = this;
            $.each(that.classesElementLookup, function (key, value) {
                if ($.inArray(event.target, value) !== -1) {
                    that.classesElementLookup[key] = $(value.not(event.target).get());
                }
            });
        },
        _removeClass: function (element, keys, extra) {
            return this._toggleClass(element, keys, extra, false);
        },
        _addClass: function (element, keys, extra) {
            return this._toggleClass(element, keys, extra, true);
        },
        _toggleClass: function (element, keys, extra, add) {
            add = (typeof add === "boolean") ? add : extra;
            var shift = (typeof element === "string" || element === null),
                options = {
                    extra: shift ? keys : extra,
                    keys: shift ? element : keys,
                    element: shift ? this.element : element,
                    add: add
                };
            options.element.toggleClass(this._classes(options), add);
            return this;
        },
        _on: function (suppressDisabledCheck, element, handlers) {
            var delegateElement;
            var instance = this;

            // No suppressDisabledCheck flag, shuffle arguments
            if (typeof suppressDisabledCheck !== "boolean") {
                handlers = element;
                element = suppressDisabledCheck;
                suppressDisabledCheck = false;
            }

            // No element argument, shuffle and use this.element
            if (!handlers) {
                handlers = element;
                element = this.element;
                delegateElement = this.widget();
            } else {
                element = delegateElement = $(element);
                this.bindings = this.bindings.add(element);
            }

            $.each(handlers, function (event, handler) {
                function handlerProxy() {

                    // Allow widgets to customize the disabled handling
                    // - disabled as an array instead of boolean
                    // - disabled class as method for disabling individual parts
                    if (!suppressDisabledCheck &&
                        (instance.options.disabled === true ||
                            $(this).hasClass("ui-state-disabled"))) {
                        return;
                    }
                    return (typeof handler === "string" ? instance[handler] : handler)
                        .apply(instance, arguments);
                }

                // Copy the guid so direct unbinding works
                if (typeof handler !== "string") {
                    handlerProxy.guid = handler.guid =
                        handler.guid || handlerProxy.guid || $.guid++;
                }

                var match = event.match(/^([\w:-]*)\s*(.*)$/);
                var eventName = match[1] + instance.eventNamespace;
                var selector = match[2];

                if (selector) {
                    delegateElement.on(eventName, selector, handlerProxy);
                } else {
                    element.on(eventName, handlerProxy);
                }
            });
        },
        _off: function (element, eventName) {
            eventName = (eventName || "").split(" ").join(this.eventNamespace + " ") +
                this.eventNamespace;
            element.off(eventName).off(eventName);

            // Clear the stack to avoid memory leaks (#10056)
            this.bindings = $(this.bindings.not(element).get());
            this.focusable = $(this.focusable.not(element).get());
            this.hoverable = $(this.hoverable.not(element).get());
        },
        _delay: function (handler, delay) {
            function handlerProxy() {
                return (typeof handler === "string" ? instance[handler] : handler)
                    .apply(instance, arguments);
            }

            var instance = this;
            return setTimeout(handlerProxy, delay || 0);
        },
        _hoverable: function (element) {
            this.hoverable = this.hoverable.add(element);
            this._on(element, {
                mouseenter: function (event) {
                    this._addClass($(event.currentTarget), null, "ui-state-hover");
                },
                mouseleave: function (event) {
                    this._removeClass($(event.currentTarget), null, "ui-state-hover");
                }
            });
        },
        _focusable: function (element) {
            this.focusable = this.focusable.add(element);
            this._on(element, {
                focusin: function (event) {
                    this._addClass($(event.currentTarget), null, "ui-state-focus");
                },
                focusout: function (event) {
                    this._removeClass($(event.currentTarget), null, "ui-state-focus");
                }
            });
        },
        _trigger: function (type, event, data) {
            var prop,
                orig;
            var callback = this.options[type];

            data = data || {};
            event = $.Event(event);
            event.type = (type === this.widgetEventPrefix ?
                type :
                this.widgetEventPrefix + type).toLowerCase();

            // The original event may come from any element
            // so we need to reset the target on the new event
            event.target = this.element[0];

            // Copy original event properties over to the new event
            orig = event.originalEvent;
            if (orig) {
                for (prop in orig) {
                    if (!(prop in event)) {
                        event[prop] = orig[prop];
                    }
                }
            }

            this.element.trigger(event, data);
            return !($.isFunction(callback) &&
                callback.apply(this.element[0], [event].concat(data)) === false ||
                event.isDefaultPrevented());
        }
    };

    $.each({
        show: "fadeIn",
        hide: "fadeOut"
    }, function (method, defaultEffect) {
        $.Widget.prototype["_" + method] = function (element, options, callback) {
            if (typeof options === "string") {
                options = {effect: options};
            }

            var hasOptions;
            var effectName = !options ?
                method :
                options === true || typeof options === "number" ?
                    defaultEffect :
                    options.effect || defaultEffect;

            options = options || {};
            if (typeof options === "number") {
                options = {duration: options};
            }

            hasOptions = !$.isEmptyObject(options);
//            options.complete = callback;

            if (options.delay) {
                element.delay(options.delay);
            }

            if (hasOptions && $.effects && $.effects.effect[effectName]) {
                element[method](options);
            } else if (effectName !== method && element[effectName]) {
                element[effectName](options.duration, options.easing, callback);
            } else {
                element.queue(function (next) {
                    $(this)[method]();
                    if (callback) {
                        callback.call(element[0]);
                    }
                    next();
                });
            }
        };
    });

    var widget = $.widget;


    /*!
     * jQuery UI Position 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     *
     * http://api.jqueryui.com/position/
     */

//>>label: Position
//>>group: Core
//>>description: Positions elements relative to other elements.
//>>docs: http://api.jqueryui.com/position/
//>>demos: http://jqueryui.com/position/


    (function () {
        var cachedScrollbarWidth,
            max = Math.max,
            abs = Math.abs,
            rhorizontal = /left|center|right/,
            rvertical = /top|center|bottom/,
            roffset = /[\+\-]\d+(\.[\d]+)?%?/,
            rposition = /^\w+/,
            rpercent = /%$/,
            _position = $.fn.position;

        function getOffsets(offsets, width, height) {
            return [
                parseFloat(offsets[0]) * (rpercent.test(offsets[0]) ? width / 100 : 1),
                parseFloat(offsets[1]) * (rpercent.test(offsets[1]) ? height / 100 : 1)
            ];
        }

        function parseCss(element, property) {
            return parseInt($.css(element, property), 10) || 0;
        }

        function getDimensions(elem) {
            var raw = elem[0];
            if (raw.nodeType === 9) {
                return {
                    width: elem.width(),
                    height: elem.height(),
                    offset: {
                        top: 0,
                        left: 0
                    }
                };
            }
            if ($.isWindow(raw)) {
                return {
                    width: elem.width(),
                    height: elem.height(),
                    offset: {
                        top: elem.scrollTop(),
                        left: elem.scrollLeft()
                    }
                };
            }
            if (raw.preventDefault) {
                return {
                    width: 0,
                    height: 0,
                    offset: {
                        top: raw.pageY,
                        left: raw.pageX
                    }
                };
            }
            return {
                width: elem.outerWidth(),
                height: elem.outerHeight(),
                offset: elem.offset()
            };
        }

        $.position = {
            scrollbarWidth: function () {
                if (cachedScrollbarWidth !== undefined) {
                    return cachedScrollbarWidth;
                }
                var w1,
                    w2,
                    div = $("<div " +
                        "style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" +
                        "<div style='height:100px;width:auto;'></div></div>"),
                    innerDiv = div.children()[0];

                $("body").append(div);
                w1 = innerDiv.offsetWidth;
                div.css("overflow", "scroll");

                w2 = innerDiv.offsetWidth;

                if (w1 === w2) {
                    w2 = div[0].clientWidth;
                }

                div.remove();

                return (cachedScrollbarWidth = w1 - w2);
            },
            getScrollInfo: function (within) {
                var overflowX = within.isWindow || within.isDocument ? "" :
                        within.element.css("overflow-x"),
                    overflowY = within.isWindow || within.isDocument ? "" :
                        within.element.css("overflow-y"),
                    hasOverflowX = overflowX === "scroll" ||
                        (overflowX === "auto" && within.width < within.element[0].scrollWidth),
                    hasOverflowY = overflowY === "scroll" ||
                        (overflowY === "auto" && within.height < within.element[0].scrollHeight);
                return {
                    width: hasOverflowY ? $.position.scrollbarWidth() : 0,
                    height: hasOverflowX ? $.position.scrollbarWidth() : 0
                };
            },
            getWithinInfo: function (element) {
                var withinElement = $(element || window),
                    isWindow = $.isWindow(withinElement[0]),
                    isDocument = !!withinElement[0] && withinElement[0].nodeType === 9,
                    hasOffset = !isWindow && !isDocument;
                return {
                    element: withinElement,
                    isWindow: isWindow,
                    isDocument: isDocument,
                    offset: hasOffset ? $(element).offset() : {
                        left: 0,
                        top: 0
                    },
                    scrollLeft: withinElement.scrollLeft(),
                    scrollTop: withinElement.scrollTop(),
                    width: withinElement.outerWidth(),
                    height: withinElement.outerHeight()
                };
            }
        };

        $.fn.position = function (options) {
            if (!options || !options.of) {
                return _position.apply(this, arguments);
            }

            // Make a copy, we don't want to modify arguments
            options = $.extend({}, options);

            var atOffset,
                targetWidth,
                targetHeight,
                targetOffset,
                basePosition,
                dimensions,
                target = $(options.of),
                within = $.position.getWithinInfo(options.within),
                scrollInfo = $.position.getScrollInfo(within),
                collision = (options.collision || "flip").split(" "),
                offsets = {};

            dimensions = getDimensions(target);
            if (target[0].preventDefault) {

                // Force left top to allow flipping
                options.at = "left top";
            }
            targetWidth = dimensions.width;
            targetHeight = dimensions.height;
            targetOffset = dimensions.offset;

            // Clone to reuse original targetOffset later
            basePosition = $.extend({}, targetOffset);

            // Force my and at to have valid horizontal and vertical positions
            // if a value is missing or invalid, it will be converted to center
            $.each(["my", "at"], function () {
                var pos = (options[this] || "").split(" "),
                    horizontalOffset,
                    verticalOffset;

                if (pos.length === 1) {
                    pos = rhorizontal.test(pos[0]) ?
                        pos.concat(["center"]) :
                        rvertical.test(pos[0]) ?
                            ["center"].concat(pos) :
                            ["center", "center"];
                }
                pos[0] = rhorizontal.test(pos[0]) ? pos[0] : "center";
                pos[1] = rvertical.test(pos[1]) ? pos[1] : "center";

                // Calculate offsets
                horizontalOffset = roffset.exec(pos[0]);
                verticalOffset = roffset.exec(pos[1]);
                offsets[this] = [
                    horizontalOffset ? horizontalOffset[0] : 0,
                    verticalOffset ? verticalOffset[0] : 0
                ];

                // Reduce to just the positions without the offsets
                options[this] = [
                    rposition.exec(pos[0])[0],
                    rposition.exec(pos[1])[0]
                ];
            });

            // Normalize collision option
            if (collision.length === 1) {
                collision[1] = collision[0];
            }

            if (options.at[0] === "right") {
                basePosition.left += targetWidth;
            } else if (options.at[0] === "center") {
                basePosition.left += targetWidth / 2;
            }

            if (options.at[1] === "bottom") {
                basePosition.top += targetHeight;
            } else if (options.at[1] === "center") {
                basePosition.top += targetHeight / 2;
            }

            atOffset = getOffsets(offsets.at, targetWidth, targetHeight);
            basePosition.left += atOffset[0];
            basePosition.top += atOffset[1];

            return this.each(function () {
                var collisionPosition,
                    using,
                    elem = $(this),
                    elemWidth = elem.outerWidth(),
                    elemHeight = elem.outerHeight(),
                    marginLeft = parseCss(this, "marginLeft"),
                    marginTop = parseCss(this, "marginTop"),
                    collisionWidth = elemWidth + marginLeft + parseCss(this, "marginRight") +
                        scrollInfo.width,
                    collisionHeight = elemHeight + marginTop + parseCss(this, "marginBottom") +
                        scrollInfo.height,
                    position = $.extend({}, basePosition),
                    myOffset = getOffsets(offsets.my, elem.outerWidth(), elem.outerHeight());

                if (options.my[0] === "right") {
                    position.left -= elemWidth;
                } else if (options.my[0] === "center") {
                    position.left -= elemWidth / 2;
                }

                if (options.my[1] === "bottom") {
                    position.top -= elemHeight;
                } else if (options.my[1] === "center") {
                    position.top -= elemHeight / 2;
                }

                position.left += myOffset[0];
                position.top += myOffset[1];

                collisionPosition = {
                    marginLeft: marginLeft,
                    marginTop: marginTop
                };

                $.each(["left", "top"], function (i, dir) {
                    if ($.ui.position[collision[i]]) {
                        $.ui.position[collision[i]][dir](position, {
                            targetWidth: targetWidth,
                            targetHeight: targetHeight,
                            elemWidth: elemWidth,
                            elemHeight: elemHeight,
                            collisionPosition: collisionPosition,
                            collisionWidth: collisionWidth,
                            collisionHeight: collisionHeight,
                            offset: [atOffset[0] + myOffset[0], atOffset [1] + myOffset[1]],
                            my: options.my,
                            at: options.at,
                            within: within,
                            elem: elem
                        });
                    }
                });

                if (options.using) {

                    // Adds feedback as second argument to using callback, if present
                    using = function (props) {
                        var left = targetOffset.left - position.left,
                            right = left + targetWidth - elemWidth,
                            top = targetOffset.top - position.top,
                            bottom = top + targetHeight - elemHeight,
                            feedback = {
                                target: {
                                    element: target,
                                    left: targetOffset.left,
                                    top: targetOffset.top,
                                    width: targetWidth,
                                    height: targetHeight
                                },
                                element: {
                                    element: elem,
                                    left: position.left,
                                    top: position.top,
                                    width: elemWidth,
                                    height: elemHeight
                                },
                                horizontal: right < 0 ? "left" : left > 0 ? "right" : "center",
                                vertical: bottom < 0 ? "top" : top > 0 ? "bottom" : "middle"
                            };
                        if (targetWidth < elemWidth && abs(left + right) < targetWidth) {
                            feedback.horizontal = "center";
                        }
                        if (targetHeight < elemHeight && abs(top + bottom) < targetHeight) {
                            feedback.vertical = "middle";
                        }
                        if (max(abs(left), abs(right)) > max(abs(top), abs(bottom))) {
                            feedback.important = "horizontal";
                        } else {
                            feedback.important = "vertical";
                        }
                        options.using.call(this, props, feedback);
                    };
                }

                elem.offset($.extend(position, {using: using}));
            });
        };

        $.ui.position = {
            fit: {
                left: function (position, data) {
                    var within = data.within,
                        withinOffset = within.isWindow ? within.scrollLeft : within.offset.left,
                        outerWidth = within.width,
                        collisionPosLeft = position.left - data.collisionPosition.marginLeft,
                        overLeft = withinOffset - collisionPosLeft,
                        overRight = collisionPosLeft + data.collisionWidth - outerWidth - withinOffset,
                        newOverRight;

                    // Element is wider than within
                    if (data.collisionWidth > outerWidth) {

                        // Element is initially over the left side of within
                        if (overLeft > 0 && overRight <= 0) {
                            newOverRight = position.left + overLeft + data.collisionWidth - outerWidth -
                                withinOffset;
                            position.left += overLeft - newOverRight;

                            // Element is initially over right side of within
                        } else if (overRight > 0 && overLeft <= 0) {
                            position.left = withinOffset;

                            // Element is initially over both left and right sides of within
                        } else {
                            if (overLeft > overRight) {
                                position.left = withinOffset + outerWidth - data.collisionWidth;
                            } else {
                                position.left = withinOffset;
                            }
                        }

                        // Too far left -> align with left edge
                    } else if (overLeft > 0) {
                        position.left += overLeft;

                        // Too far right -> align with right edge
                    } else if (overRight > 0) {
                        position.left -= overRight;

                        // Adjust based on position and margin
                    } else {
                        position.left = max(position.left - collisionPosLeft, position.left);
                    }
                },
                top: function (position, data) {
                    var within = data.within,
                        withinOffset = within.isWindow ? within.scrollTop : within.offset.top,
                        outerHeight = data.within.height,
                        collisionPosTop = position.top - data.collisionPosition.marginTop,
                        overTop = withinOffset - collisionPosTop,
                        overBottom = collisionPosTop + data.collisionHeight - outerHeight - withinOffset,
                        newOverBottom;

                    // Element is taller than within
                    if (data.collisionHeight > outerHeight) {

                        // Element is initially over the top of within
                        if (overTop > 0 && overBottom <= 0) {
                            newOverBottom = position.top + overTop + data.collisionHeight - outerHeight -
                                withinOffset;
                            position.top += overTop - newOverBottom;

                            // Element is initially over bottom of within
                        } else if (overBottom > 0 && overTop <= 0) {
                            position.top = withinOffset;

                            // Element is initially over both top and bottom of within
                        } else {
                            if (overTop > overBottom) {
                                position.top = withinOffset + outerHeight - data.collisionHeight;
                            } else {
                                position.top = withinOffset;
                            }
                        }

                        // Too far up -> align with top
                    } else if (overTop > 0) {
                        position.top += overTop;

                        // Too far down -> align with bottom edge
                    } else if (overBottom > 0) {
                        position.top -= overBottom;

                        // Adjust based on position and margin
                    } else {
                        position.top = max(position.top - collisionPosTop, position.top);
                    }
                }
            },
            flip: {
                left: function (position, data) {
                    var within = data.within,
                        withinOffset = within.offset.left + within.scrollLeft,
                        outerWidth = within.width,
                        offsetLeft = within.isWindow ? within.scrollLeft : within.offset.left,
                        collisionPosLeft = position.left - data.collisionPosition.marginLeft,
                        overLeft = collisionPosLeft - offsetLeft,
                        overRight = collisionPosLeft + data.collisionWidth - outerWidth - offsetLeft,
                        myOffset = data.my[0] === "left" ?
                            -data.elemWidth :
                            data.my[0] === "right" ?
                                data.elemWidth :
                                0,
                        atOffset = data.at[0] === "left" ?
                            data.targetWidth :
                            data.at[0] === "right" ?
                                -data.targetWidth :
                                0,
                        offset = -2 * data.offset[0],
                        newOverRight,
                        newOverLeft;

                    if (overLeft < 0) {
                        newOverRight = position.left + myOffset + atOffset + offset + data.collisionWidth -
                            outerWidth - withinOffset;
                        if (newOverRight < 0 || newOverRight < abs(overLeft)) {
                            position.left += myOffset + atOffset + offset;
                        }
                    } else if (overRight > 0) {
                        newOverLeft = position.left - data.collisionPosition.marginLeft + myOffset +
                            atOffset + offset - offsetLeft;
                        if (newOverLeft > 0 || abs(newOverLeft) < overRight) {
                            position.left += myOffset + atOffset + offset;
                        }
                    }
                },
                top: function (position, data) {
                    var within = data.within,
                        withinOffset = within.offset.top + within.scrollTop,
                        outerHeight = within.height,
                        offsetTop = within.isWindow ? within.scrollTop : within.offset.top,
                        collisionPosTop = position.top - data.collisionPosition.marginTop,
                        overTop = collisionPosTop - offsetTop,
                        overBottom = collisionPosTop + data.collisionHeight - outerHeight - offsetTop,
                        top = data.my[1] === "top",
                        myOffset = top ?
                            -data.elemHeight :
                            data.my[1] === "bottom" ?
                                data.elemHeight :
                                0,
                        atOffset = data.at[1] === "top" ?
                            data.targetHeight :
                            data.at[1] === "bottom" ?
                                -data.targetHeight :
                                0,
                        offset = -2 * data.offset[1],
                        newOverTop,
                        newOverBottom;
                    if (overTop < 0) {
                        newOverBottom = position.top + myOffset + atOffset + offset + data.collisionHeight -
                            outerHeight - withinOffset;
                        if (newOverBottom < 0 || newOverBottom < abs(overTop)) {
                            position.top += myOffset + atOffset + offset;
                        }
                    } else if (overBottom > 0) {
                        newOverTop = position.top - data.collisionPosition.marginTop + myOffset + atOffset +
                            offset - offsetTop;
                        if (newOverTop > 0 || abs(newOverTop) < overBottom) {
                            position.top += myOffset + atOffset + offset;
                        }
                    }
                }
            },
            flipfit: {
                left: function () {
                    $.ui.position.flip.left.apply(this, arguments);
                    $.ui.position.fit.left.apply(this, arguments);
                },
                top: function () {
                    $.ui.position.flip.top.apply(this, arguments);
                    $.ui.position.fit.top.apply(this, arguments);
                }
            }
        };

    })();

    var position = $.ui.position;


    /*!
     * jQuery UI Keycode 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Keycode
//>>group: Core
//>>description: Provide keycodes as keynames
//>>docs: http://api.jqueryui.com/jQuery.ui.keyCode/


    var keycode = $.ui.keyCode = {
        BACKSPACE: 8,
        COMMA: 188,
        DELETE: 46,
        DOWN: 40,
        END: 35,
        ENTER: 13,
        ESCAPE: 27,
        HOME: 36,
        LEFT: 37,
        PAGE_DOWN: 34,
        PAGE_UP: 33,
        PERIOD: 190,
        RIGHT: 39,
        SPACE: 32,
        TAB: 9,
        UP: 38
    };


    /*!
     * jQuery UI Unique ID 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: uniqueId
//>>group: Core
//>>description: Functions to generate and remove uniqueId's
//>>docs: http://api.jqueryui.com/uniqueId/


    var uniqueId = $.fn.extend({
        uniqueId: (function () {
            var uuid = 0;

            return function () {
                return this.each(function () {
                    if (!this.id) {
                        this.id = "ui-id-" + (++uuid);
                    }
                });
            };
        })(),
        removeUniqueId: function () {
            return this.each(function () {
                if (/^ui-id-\d+$/.test(this.id)) {
                    $(this).removeAttr("id");
                }
            });
        }
    });


    /*!
     * jQuery UI Tooltip 1.12.1
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     */

//>>label: Tooltip
//>>group: Widgets
//>>description: Shows additional information for any element on hover or focus.
//>>docs: http://api.jqueryui.com/tooltip/
//>>demos: http://jqueryui.com/tooltip/
//>>css.structure: ../../themes/base/core.css
//>>css.structure: ../../themes/base/tooltip.css
//>>css.theme: ../../themes/base/theme.css


    $.widget("ui.tooltip", {
        version: "1.12.1",
        options: {
            classes: {
                "ui-tooltip": "ui-corner-all ui-widget-shadow"
            },
            content: function () {

                // support: IE<9, Opera in jQuery <1.7
                // .text() can't accept undefined, so coerce to a string
                var title = $(this).attr("title") || "";

                // Escape title, since we're going from an attribute to raw HTML
                return $("<a>").html(title);
            },
            hide: true,
            // Disabled elements have inconsistent behavior across browsers (#8661)
            items: "[title]:not([disabled])",
            position: {
                my: "left top+15",
                at: "left bottom",
                collision: "flipfit flip"
            },
            show: true,
            track: false,
            // Callbacks
            close: null,
            open: null
        },
        _addDescribedBy: function (elem, id) {
            var describedby = (elem.attr("aria-describedby") || "").split(/\s+/);
            describedby.push(id);
            elem
                .data("ui-tooltip-id", id)
                .attr("aria-describedby", $.trim(describedby.join(" ")));
        },
        _removeDescribedBy: function (elem) {
            var id = elem.data("ui-tooltip-id"),
                describedby = (elem.attr("aria-describedby") || "").split(/\s+/),
                index = $.inArray(id, describedby);

            if (index !== -1) {
                describedby.splice(index, 1);
            }

            elem.removeData("ui-tooltip-id");
            describedby = $.trim(describedby.join(" "));
            if (describedby) {
                elem.attr("aria-describedby", describedby);
            } else {
                elem.removeAttr("aria-describedby");
            }
        },
        _create: function () {
            this._on({
                mouseover: "open",
                focusin: "open"
            });

            // IDs of generated tooltips, needed for destroy
            this.tooltips = {};

            // IDs of parent tooltips where we removed the title attribute
            this.parents = {};

            // Append the aria-live region so tooltips announce correctly
            this.liveRegion = $("<div>")
                .attr({
                    role: "log",
                    "aria-live": "assertive",
                    "aria-relevant": "additions"
                })
                .appendTo(this.document[0].body);
            this._addClass(this.liveRegion, null, "ui-helper-hidden-accessible");

            this.disabledTitles = $([]);
        },
        _setOption: function (key, value) {
            var that = this;

            this._super(key, value);

            if (key === "content") {
                $.each(this.tooltips, function (id, tooltipData) {
                    that._updateContent(tooltipData.element);
                });
            }
        },
        _setOptionDisabled: function (value) {
            this[value ? "_disable" : "_enable"]();
        },
        _disable: function () {
            var that = this;

            // Close open tooltips
            $.each(this.tooltips, function (id, tooltipData) {
                var event = $.Event("blur");
                event.target = event.currentTarget = tooltipData.element[0];
                that.close(event, true);
            });

            // Remove title attributes to prevent native tooltips
            this.disabledTitles = this.disabledTitles.add(
                this.element.find(this.options.items).addBack()
                    .filter(function () {
                        var element = $(this);
                        if (element.is("[title]")) {
                            return element
                                .data("ui-tooltip-title", element.attr("title"))
                                .removeAttr("title");
                        }
                    })
            );
        },
        _enable: function () {

            // restore title attributes
            this.disabledTitles.each(function () {
                var element = $(this);
                if (element.data("ui-tooltip-title")) {
                    element.attr("title", element.data("ui-tooltip-title"));
                }
            });
            this.disabledTitles = $([]);
        },
        open: function (event) {
            var that = this,
                target = $(event ? event.target : this.element)

                    // we need closest here due to mouseover bubbling,
                    // but always pointing at the same event target
                    .closest(this.options.items);

            // No element to show a tooltip for or the tooltip is already open
            if (!target.length || target.data("ui-tooltip-id")) {
                return;
            }

            if (target.attr("title")) {
                target.data("ui-tooltip-title", target.attr("title"));
            }

            target.data("ui-tooltip-open", true);

            // Kill parent tooltips, custom or native, for hover
            if (event && event.type === "mouseover") {
                target.parents().each(function () {
                    var parent = $(this),
                        blurEvent;
                    if (parent.data("ui-tooltip-open")) {
                        blurEvent = $.Event("blur");
                        blurEvent.target = blurEvent.currentTarget = this;
                        that.close(blurEvent, true);
                    }
                    if (parent.attr("title")) {
                        parent.uniqueId();
                        that.parents[this.id] = {
                            element: this,
                            title: parent.attr("title")
                        };
                        parent.attr("title", "");
                    }
                });
            }

            this._registerCloseHandlers(event, target);
            this._updateContent(target, event);
        },
        _updateContent: function (target, event) {
            var content,
                contentOption = this.options.content,
                that = this,
                eventType = event ? event.type : null;

            if (typeof contentOption === "string" || contentOption.nodeType ||
                contentOption.jquery) {
                return this._open(event, target, contentOption);
            }

            content = contentOption.call(target[0], function (response) {

                // IE may instantly serve a cached response for ajax requests
                // delay this call to _open so the other call to _open runs first
                that._delay(function () {

                    // Ignore async response if tooltip was closed already
                    if (!target.data("ui-tooltip-open")) {
                        return;
                    }

                    // JQuery creates a special event for focusin when it doesn't
                    // exist natively. To improve performance, the native event
                    // object is reused and the type is changed. Therefore, we can't
                    // rely on the type being correct after the event finished
                    // bubbling, so we set it back to the previous value. (#8740)
                    if (event) {
                        event.type = eventType;
                    }
                    this._open(event, target, response);
                });
            });
            if (content) {
                this._open(event, target, content);
            }
        },
        _open: function (event, target, content) {
            var tooltipData,
                tooltip,
                delayedShow,
                a11yContent,
                positionOption = $.extend({}, this.options.position);

            if (!content) {
                return;
            }

            // Content can be updated multiple times. If the tooltip already
            // exists, then just update the content and bail.
            tooltipData = this._find(target);
            if (tooltipData) {
                tooltipData.tooltip.find(".ui-tooltip-content").html(content);
                return;
            }

            // If we have a title, clear it to prevent the native tooltip
            // we have to check first to avoid defining a title if none exists
            // (we don't want to cause an element to start matching [title])
            //
            // We use removeAttr only for key events, to allow IE to export the correct
            // accessible attributes. For mouse events, set to empty string to avoid
            // native tooltip showing up (happens only when removing inside mouseover).
            if (target.is("[title]")) {
                if (event && event.type === "mouseover") {
                    target.attr("title", "");
                } else {
                    target.removeAttr("title");
                }
            }

            tooltipData = this._tooltip(target);
            tooltip = tooltipData.tooltip;
            this._addDescribedBy(target, tooltip.attr("id"));
            tooltip.find(".ui-tooltip-content").html(content);

            // Support: Voiceover on OS X, JAWS on IE <= 9
            // JAWS announces deletions even when aria-relevant="additions"
            // Voiceover will sometimes re-read the entire log region's contents from the beginning
            this.liveRegion.children().hide();
            a11yContent = $("<div>").html(tooltip.find(".ui-tooltip-content").html());
            a11yContent.removeAttr("name").find("[name]").removeAttr("name");
            a11yContent.removeAttr("id").find("[id]").removeAttr("id");
            a11yContent.appendTo(this.liveRegion);

            function position(event) {
                positionOption.of = event;
                if (tooltip.is(":hidden")) {
                    return;
                }
                tooltip.position(positionOption);
            }

            if (this.options.track && event && /^mouse/.test(event.type)) {
                this._on(this.document, {
                    mousemove: position
                });

                // trigger once to override element-relative positioning
                position(event);
            } else {
                tooltip.position($.extend({
                    of: target
                }, this.options.position));
            }

            tooltip.hide();

            this._show(tooltip, this.options.show);

            // Handle tracking tooltips that are shown with a delay (#8644). As soon
            // as the tooltip is visible, position the tooltip using the most recent
            // event.
            // Adds the check to add the timers only when both delay and track options are set (#14682)
            if (this.options.track && this.options.show && this.options.show.delay) {
                delayedShow = this.delayedShow = setInterval(function () {
                    if (tooltip.is(":visible")) {
                        position(positionOption.of);
                        clearInterval(delayedShow);
                    }
                }, $.fx.interval);
            }

            this._trigger("open", event, {tooltip: tooltip});
        },
        _registerCloseHandlers: function (event, target) {
            var events = {
                keyup: function (event) {
                    if (event.keyCode === $.ui.keyCode.ESCAPE) {
                        var fakeEvent = $.Event(event);
                        fakeEvent.currentTarget = target[0];
                        this.close(fakeEvent, true);
                    }
                }
            };

            // Only bind remove handler for delegated targets. Non-delegated
            // tooltips will handle this in destroy.
            if (target[0] !== this.element[0]) {
                events.remove = function () {
                    this._removeTooltip(this._find(target).tooltip);
                };
            }

            if (!event || event.type === "mouseover") {
                events.mouseleave = "close";
            }
            if (!event || event.type === "focusin") {
                events.focusout = "close";
            }
            this._on(true, target, events);
        },
        close: function (event) {
            var tooltip,
                that = this,
                target = $(event ? event.currentTarget : this.element),
                tooltipData = this._find(target);

            // The tooltip may already be closed
            if (!tooltipData) {

                // We set ui-tooltip-open immediately upon open (in open()), but only set the
                // additional data once there's actually content to show (in _open()). So even if the
                // tooltip doesn't have full data, we always remove ui-tooltip-open in case we're in
                // the period between open() and _open().
                target.removeData("ui-tooltip-open");
                return;
            }

            tooltip = tooltipData.tooltip;

            // Disabling closes the tooltip, so we need to track when we're closing
            // to avoid an infinite loop in case the tooltip becomes disabled on close
            if (tooltipData.closing) {
                return;
            }

            // Clear the interval for delayed tracking tooltips
            clearInterval(this.delayedShow);

            // Only set title if we had one before (see comment in _open())
            // If the title attribute has changed since open(), don't restore
            if (target.data("ui-tooltip-title") && !target.attr("title")) {
                target.attr("title", target.data("ui-tooltip-title"));
            }

            this._removeDescribedBy(target);

            tooltipData.hiding = true;
            tooltip.stop(true);
            this._hide(tooltip, this.options.hide, function () {
                that._removeTooltip($(this));
            });

            target.removeData("ui-tooltip-open");
            this._off(target, "mouseleave focusout keyup");

            // Remove 'remove' binding only on delegated targets
            if (target[0] !== this.element[0]) {
                this._off(target, "remove");
            }
            this._off(this.document, "mousemove");

            if (event && event.type === "mouseleave") {
                $.each(this.parents, function (id, parent) {
                    $(parent.element).attr("title", parent.title);
                    delete that.parents[id];
                });
            }

            tooltipData.closing = true;
            this._trigger("close", event, {tooltip: tooltip});
            if (!tooltipData.hiding) {
                tooltipData.closing = false;
            }
        },
        _tooltip: function (element) {
            var tooltip = $("<div>").attr("role", "tooltip"),
                content = $("<div>").appendTo(tooltip),
                id = tooltip.uniqueId().attr("id");

            this._addClass(content, "ui-tooltip-content");
            this._addClass(tooltip, "ui-tooltip", "ui-widget ui-widget-content");

            tooltip.appendTo(this._appendTo(element));

            return this.tooltips[id] = {
                element: element,
                tooltip: tooltip
            };
        },
        _find: function (target) {
            var id = target.data("ui-tooltip-id");
            return id ? this.tooltips[id] : null;
        },
        _removeTooltip: function (tooltip) {
            tooltip.remove();
            delete this.tooltips[tooltip.attr("id")];
        },
        _appendTo: function (target) {
            var element = target.closest(".ui-front, dialog");

            if (!element.length) {
                element = this.document[0].body;
            }

            return element;
        },
        _destroy: function () {
            var that = this;

            // Close open tooltips
            $.each(this.tooltips, function (id, tooltipData) {

                // Delegate to close method to handle common cleanup
                var event = $.Event("blur"),
                    element = tooltipData.element;
                event.target = event.currentTarget = element[0];
                that.close(event, true);

                // Remove immediately; destroying an open tooltip doesn't use the
                // hide animation
                $("#" + id).remove();

                // Restore the title
                if (element.data("ui-tooltip-title")) {

                    // If the title attribute has changed since open(), don't restore
                    if (!element.attr("title")) {
                        element.attr("title", element.data("ui-tooltip-title"));
                    }
                    element.removeData("ui-tooltip-title");
                }
            });
            this.liveRegion.remove();
        }
    });

// DEPRECATED
// TODO: Switch return back to widget declaration at top of file when this is removed
    if ($.uiBackCompat !== false) {

        // Backcompat for tooltipClass option
        $.widget("ui.tooltip", $.ui.tooltip, {
            options: {
                tooltipClass: null
            },
            _tooltip: function () {
                var tooltipData = this._superApply(arguments);
                if (this.options.tooltipClass) {
                    tooltipData.tooltip.addClass(this.options.tooltipClass);
                }
                return tooltipData;
            }
        });
    }

    var widgetsTooltip = $.ui.tooltip;


}));