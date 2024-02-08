/*!
 * jQuery UI 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI
 */
/*!
 * jQuery UI 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI
 */
(function(c, j) {
    function k(a) {
        return !c(a).parents().addBack().filter(function() {
            return c.curCSS(this, "visibility") === "hidden" || c.expr.filters.hidden(this)
        }).length
    }
    c.ui = c.ui || {};
    if (!c.ui.version) {
        c.extend(c.ui, {
            version: "1.8.11",
            keyCode: {
                ALT: 18,
                BACKSPACE: 8,
                CAPS_LOCK: 20,
                COMMA: 188,
                COMMAND: 91,
                COMMAND_LEFT: 91,
                COMMAND_RIGHT: 93,
                CONTROL: 17,
                DELETE: 46,
                DOWN: 40,
                END: 35,
                ENTER: 13,
                ESCAPE: 27,
                HOME: 36,
                INSERT: 45,
                LEFT: 37,
                MENU: 93,
                NUMPAD_ADD: 107,
                NUMPAD_DECIMAL: 110,
                NUMPAD_DIVIDE: 111,
                NUMPAD_ENTER: 108,
                NUMPAD_MULTIPLY: 106,
                NUMPAD_SUBTRACT: 109,
                PAGE_DOWN: 34,
                PAGE_UP: 33,
                PERIOD: 190,
                RIGHT: 39,
                SHIFT: 16,
                SPACE: 32,
                TAB: 9,
                UP: 38,
                WINDOWS: 91
            }
        });
        c.fn.extend({
            _focus: c.fn.focus,
            focus: function(a, b) {
                return typeof a === "number" ? this.each(function() {
                    var d = this;
                    setTimeout(function() {
                        c(d).focus();
                        b && b.call(d)
                    }, a)
                }) : this._focus.apply(this, arguments)
            },
            scrollParent: function() {
                var a;
                a = c.browser.msie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? this.parents().filter(function() {
                    return /(relative|absolute|fixed)/.test(c.curCSS(this, "position", 1)) && /(auto|scroll)/.test(c.curCSS(this, "overflow", 1) + c.curCSS(this, "overflow-y", 1) + c.curCSS(this, "overflow-x", 1))
                }).eq(0) : this.parents().filter(function() {
                    return /(auto|scroll)/.test(c.curCSS(this, "overflow", 1) + c.curCSS(this, "overflow-y", 1) + c.curCSS(this, "overflow-x", 1))
                }).eq(0);
                return /fixed/.test(this.css("position")) || !a.length ? c(document) : a
            },
            zIndex: function(a) {
                if (a !== j)
                    return this.css("zIndex", a);
                if (this.length) {
                    a = c(this[0]);
                    for (var b; a.length && a[0] !== document; ) {
                        b = a.css("position");
                        if (b === "absolute" || b === "relative" || b === "fixed") {
                            b = parseInt(a.css("zIndex"), 10);
                            if (!isNaN(b) && b !== 0)
                                return b
                        }
                        a = a.parent()
                    }
                }
                return 0
            },
            disableSelection: function() {
                return this.bind((c.support.selectstart ? "selectstart" : "mousedown") + ".ui-disableSelection", function(a) {
                    a.preventDefault()
                })
            },
            enableSelection: function() {
                return this.unbind(".ui-disableSelection")
            }
        });
        c.each(["Width", "Height"], function(a, b) {
            function d(f, g, l, m) {
                c.each(e, function() {
                    g -= parseFloat(c.curCSS(f, "padding" + this, true)) || 0;
                    if (l)
                        g -= parseFloat(c.curCSS(f, "border" + this + "Width", true)) || 0;
                    if (m)
                        g -= parseFloat(c.curCSS(f, "margin" + this, true)) || 0
                });
                return g
            }
            var e = b === "Width" ? ["Left", "Right"] : ["Top", "Bottom"]
              , h = b.toLowerCase()
              , i = {
                innerWidth: c.fn.innerWidth,
                innerHeight: c.fn.innerHeight,
                outerWidth: c.fn.outerWidth,
                outerHeight: c.fn.outerHeight
            };
            c.fn["inner" + b] = function(f) {
                if (f === j)
                    return i["inner" + b].call(this);
                return this.each(function() {
                    c(this).css(h, d(this, f) + "px")
                })
            }
            ;
            c.fn["outer" + b] = function(f, g) {
                if (typeof f !== "number")
                    return i["outer" + b].call(this, f);
                return this.each(function() {
                    c(this).css(h, d(this, f, true, g) + "px")
                })
            }
        });
        c.extend(c.expr[":"], {
            data: function(a, b, d) {
                return !!c.data(a, d[3])
            },
            focusable: function(a) {
                var b = a.nodeName.toLowerCase()
                  , d = c.attr(a, "tabindex");
                if ("area" === b) {
                    b = a.parentNode;
                    d = b.name;
                    if (!a.href || !d || b.nodeName.toLowerCase() !== "map")
                        return false;
                    a = c("img[usemap=#" + d + "]")[0];
                    return !!a && k(a)
                }
                return (/input|select|textarea|button|object/.test(b) ? !a.disabled : "a" == b ? a.href || !isNaN(d) : !isNaN(d)) && k(a)
            },
            tabbable: function(a) {
                var b = c.attr(a, "tabindex");
                return (isNaN(b) || b >= 0) && c(a).is(":focusable")
            }
        });
        c(function() {
            var a = document.body
              , b = a.appendChild(b = document.createElement("div"));
            c.extend(b.style, {
                minHeight: "100px",
                height: "auto",
                padding: 0,
                borderWidth: 0
            });
            c.support.minHeight = b.offsetHeight === 100;
            c.support.selectstart = "onselectstart"in b;
            a.removeChild(b).style.display = "none"
        });
        c.extend(c.ui, {
            plugin: {
                add: function(a, b, d) {
                    a = c.ui[a].prototype;
                    for (var e in d) {
                        a.plugins[e] = a.plugins[e] || [];
                        a.plugins[e].push([b, d[e]])
                    }
                },
                call: function(a, b, d) {
                    if ((b = a.plugins[b]) && a.element[0].parentNode)
                        for (var e = 0; e < b.length; e++)
                            a.options[b[e][0]] && b[e][1].apply(a.element, d)
                }
            },
            contains: function(a, b) {
                return document.compareDocumentPosition ? a.compareDocumentPosition(b) & 16 : a !== b && a.contains(b)
            },
            hasScroll: function(a, b) {
                if (c(a).css("overflow") === "hidden")
                    return false;
                b = b && b === "left" ? "scrollLeft" : "scrollTop";
                var d = false;
                if (a[b] > 0)
                    return true;
                a[b] = 1;
                d = a[b] > 0;
                a[b] = 0;
                return d
            },
            isOverAxis: function(a, b, d) {
                return a > b && a < b + d
            },
            isOver: function(a, b, d, e, h, i) {
                return c.ui.isOverAxis(a, d, h) && c.ui.isOverAxis(b, e, i)
            }
        })
    }
}
)(jQuery);
;/*!
 * jQuery UI Widget 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Widget
 */
(function(b, j) {
    if (b.cleanData) {
        var k = b.cleanData;
        b.cleanData = function(a) {
            for (var c = 0, d; (d = a[c]) != null; c++)
                b(d).triggerHandler("remove");
            k(a)
        }
    } else {
        var l = b.fn.remove;
        b.fn.remove = function(a, c) {
            return this.each(function() {
                if (!c)
                    if (!a || b.filter(a, [this]).length)
                        b("*", this).add([this]).each(function() {
                            b(this).triggerHandler("remove")
                        });
                return l.call(b(this), a, c)
            })
        }
    }
    b.widget = function(a, c, d) {
        var e = a.split(".")[0], f;
        a = a.split(".")[1];
        f = e + "-" + a;
        if (!d) {
            d = c;
            c = b.Widget
        }
        b.expr[":"][f] = function(h) {
            return !!b.data(h, a)
        }
        ;
        b[e] = b[e] || {};
        b[e][a] = function(h, g) {
            arguments.length && this._createWidget(h, g)
        }
        ;
        c = new c;
        c.options = b.extend(true, {}, c.options);
        b[e][a].prototype = b.extend(true, c, {
            namespace: e,
            widgetName: a,
            widgetEventPrefix: b[e][a].prototype.widgetEventPrefix || a,
            widgetBaseClass: f
        }, d);
        b.widget.bridge(a, b[e][a])
    }
    ;
    b.widget.bridge = function(a, c) {
        b.fn[a] = function(d) {
            var e = typeof d === "string"
              , f = Array.prototype.slice.call(arguments, 1)
              , h = this;
            d = !e && f.length ? b.extend.apply(null, [true, d].concat(f)) : d;
            if (e && d.charAt(0) === "_")
                return h;
            e ? this.each(function() {
                var g = b.data(this, a)
                  , i = g && b.isFunction(g[d]) ? g[d].apply(g, f) : g;
                if (i !== g && i !== j) {
                    h = i;
                    return false
                }
            }) : this.each(function() {
                var g = b.data(this, a);
                g ? g.option(d || {})._init() : b.data(this, a, new c(d,this))
            });
            return h
        }
    }
    ;
    b.Widget = function(a, c) {
        arguments.length && this._createWidget(a, c)
    }
    ;
    b.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        options: {
            disabled: false
        },
        _createWidget: function(a, c) {
            b.data(c, this.widgetName, this);
            this.element = b(c);
            this.options = b.extend(true, {}, this.options, this._getCreateOptions(), a);
            var d = this;
            this.element.bind("remove." + this.widgetName, function() {
                d.destroy()
            });
            this._create();
            this._trigger("create");
            this._init()
        },
        _getCreateOptions: function() {
            return b.metadata && b.metadata.get(this.element[0])[this.widgetName]
        },
        _create: function() {},
        _init: function() {},
        destroy: function() {
            this.element.unbind("." + this.widgetName).removeData(this.widgetName);
            this.widget().unbind("." + this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass + "-disabled ui-state-disabled")
        },
        widget: function() {
            return this.element
        },
        option: function(a, c) {
            var d = a;
            if (arguments.length === 0)
                return b.extend({}, this.options);
            if (typeof a === "string") {
                if (c === j)
                    return this.options[a];
                d = {};
                d[a] = c
            }
            this._setOptions(d);
            return this
        },
        _setOptions: function(a) {
            var c = this;
            b.each(a, function(d, e) {
                c._setOption(d, e)
            });
            return this
        },
        _setOption: function(a, c) {
            this.options[a] = c;
            if (a === "disabled")
                this.widget()[c ? "addClass" : "removeClass"](this.widgetBaseClass + "-disabled ui-state-disabled").attr("aria-disabled", c);
            return this
        },
        enable: function() {
            return this._setOption("disabled", false)
        },
        disable: function() {
            return this._setOption("disabled", true)
        },
        _trigger: function(a, c, d) {
            var e = this.options[a];
            c = b.Event(c);
            c.type = (a === this.widgetEventPrefix ? a : this.widgetEventPrefix + a).toLowerCase();
            d = d || {};
            if (c.originalEvent) {
                a = b.event.props.length;
                for (var f; a; ) {
                    f = b.event.props[--a];
                    c[f] = c.originalEvent[f]
                }
            }
            this.element.trigger(c, d);
            return !(b.isFunction(e) && e.call(this.element[0], c, d) === false || c.isDefaultPrevented())
        }
    }
}
)(jQuery);
;/*!
 * jQuery UI Mouse 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Mouse
 *
 * Depends:
 *	jquery.ui.widget.js
 */
(function(b) {
    b.widget("ui.mouse", {
        options: {
            cancel: ":input,option",
            distance: 1,
            delay: 0
        },
        _mouseInit: function() {
            var a = this;
            this.element.bind("mousedown." + this.widgetName, function(c) {
                return a._mouseDown(c)
            }).bind("click." + this.widgetName, function(c) {
                if (true === b.data(c.target, a.widgetName + ".preventClickEvent")) {
                    b.removeData(c.target, a.widgetName + ".preventClickEvent");
                    c.stopImmediatePropagation();
                    return false
                }
            });
            this.started = false
        },
        _mouseDestroy: function() {
            this.element.unbind("." + this.widgetName)
        },
        _mouseDown: function(a) {
            a.originalEvent = a.originalEvent || {};
            if (!a.originalEvent.mouseHandled) {
                this._mouseStarted && this._mouseUp(a);
                this._mouseDownEvent = a;
                var c = this
                  , e = a.which == 1
                  , f = typeof this.options.cancel == "string" ? b(a.target).parents().add(a.target).filter(this.options.cancel).length : false;
                if (!e || f || !this._mouseCapture(a))
                    return true;
                this.mouseDelayMet = !this.options.delay;
                if (!this.mouseDelayMet)
                    this._mouseDelayTimer = setTimeout(function() {
                        c.mouseDelayMet = true
                    }, this.options.delay);
                if (this._mouseDistanceMet(a) && this._mouseDelayMet(a)) {
                    this._mouseStarted = this._mouseStart(a) !== false;
                    if (!this._mouseStarted) {
                        a.preventDefault();
                        return true
                    }
                }
                true === b.data(a.target, this.widgetName + ".preventClickEvent") && b.removeData(a.target, this.widgetName + ".preventClickEvent");
                this._mouseMoveDelegate = function(d) {
                    return c._mouseMove(d)
                }
                ;
                this._mouseUpDelegate = function(d) {
                    return c._mouseUp(d)
                }
                ;
                b(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate);
                a.preventDefault();
                return a.originalEvent.mouseHandled = true
            }
        },
        _mouseMove: function(a) {
            if (b.browser.msie && !(document.documentMode >= 9) && !a.button)
                return this._mouseUp(a);
            if (this._mouseStarted) {
                this._mouseDrag(a);
                return a.preventDefault()
            }
            if (this._mouseDistanceMet(a) && this._mouseDelayMet(a))
                (this._mouseStarted = this._mouseStart(this._mouseDownEvent, a) !== false) ? this._mouseDrag(a) : this._mouseUp(a);
            return !this._mouseStarted
        },
        _mouseUp: function(a) {
            b(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate);
            if (this._mouseStarted) {
                this._mouseStarted = false;
                a.target == this._mouseDownEvent.target && b.data(a.target, this.widgetName + ".preventClickEvent", true);
                this._mouseStop(a)
            }
            return false
        },
        _mouseDistanceMet: function(a) {
            return Math.max(Math.abs(this._mouseDownEvent.pageX - a.pageX), Math.abs(this._mouseDownEvent.pageY - a.pageY)) >= this.options.distance
        },
        _mouseDelayMet: function() {
            return this.mouseDelayMet
        },
        _mouseStart: function() {},
        _mouseDrag: function() {},
        _mouseStop: function() {},
        _mouseCapture: function() {
            return true
        }
    })
}
)(jQuery);
;/*
 * jQuery UI Position 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Position
 */
(function(c) {
    c.ui = c.ui || {};
    var n = /left|center|right/
      , o = /top|center|bottom/
      , t = c.fn.position
      , u = c.fn.offset;
    c.fn.position = function(b) {
        if (!b || !b.of)
            return t.apply(this, arguments);
        b = c.extend({}, b);
        var a = c(b.of), d = a[0], g = (b.collision || "flip").split(" "), e = b.offset ? b.offset.split(" ") : [0, 0], h, k, j;
        if (d.nodeType === 9) {
            h = a.width();
            k = a.height();
            j = {
                top: 0,
                left: 0
            }
        } else if (d.setTimeout) {
            h = a.width();
            k = a.height();
            j = {
                top: a.scrollTop(),
                left: a.scrollLeft()
            }
        } else if (d.preventDefault) {
            b.at = "left top";
            h = k = 0;
            j = {
                top: b.of.pageY,
                left: b.of.pageX
            }
        } else {
            h = a.outerWidth();
            k = a.outerHeight();
            j = a.offset()
        }
        c.each(["my", "at"], function() {
            var f = (b[this] || "").split(" ");
            if (f.length === 1)
                f = n.test(f[0]) ? f.concat(["center"]) : o.test(f[0]) ? ["center"].concat(f) : ["center", "center"];
            f[0] = n.test(f[0]) ? f[0] : "center";
            f[1] = o.test(f[1]) ? f[1] : "center";
            b[this] = f
        });
        if (g.length === 1)
            g[1] = g[0];
        e[0] = parseInt(e[0], 10) || 0;
        if (e.length === 1)
            e[1] = e[0];
        e[1] = parseInt(e[1], 10) || 0;
        if (b.at[0] === "right")
            j.left += h;
        else if (b.at[0] === "center")
            j.left += h / 2;
        if (b.at[1] === "bottom")
            j.top += k;
        else if (b.at[1] === "center")
            j.top += k / 2;
        j.left += e[0];
        j.top += e[1];
        return this.each(function() {
            var f = c(this), l = f.outerWidth(), m = f.outerHeight(), p = parseInt(c.curCSS(this, "marginLeft", true)) || 0, q = parseInt(c.curCSS(this, "marginTop", true)) || 0, v = l + p + (parseInt(c.curCSS(this, "marginRight", true)) || 0), w = m + q + (parseInt(c.curCSS(this, "marginBottom", true)) || 0), i = c.extend({}, j), r;
            if (b.my[0] === "right")
                i.left -= l;
            else if (b.my[0] === "center")
                i.left -= l / 2;
            if (b.my[1] === "bottom")
                i.top -= m;
            else if (b.my[1] === "center")
                i.top -= m / 2;
            i.left = Math.round(i.left);
            i.top = Math.round(i.top);
            r = {
                left: i.left - p,
                top: i.top - q
            };
            c.each(["left", "top"], function(s, x) {
                c.ui.position[g[s]] && c.ui.position[g[s]][x](i, {
                    targetWidth: h,
                    targetHeight: k,
                    elemWidth: l,
                    elemHeight: m,
                    collisionPosition: r,
                    collisionWidth: v,
                    collisionHeight: w,
                    offset: e,
                    my: b.my,
                    at: b.at
                })
            });
            c.fn.bgiframe && f.bgiframe();
            f.offset(c.extend(i, {
                using: b.using
            }))
        })
    }
    ;
    c.ui.position = {
        fit: {
            left: function(b, a) {
                var d = c(window);
                d = a.collisionPosition.left + a.collisionWidth - d.width() - d.scrollLeft();
                b.left = d > 0 ? b.left - d : Math.max(b.left - a.collisionPosition.left, b.left)
            },
            top: function(b, a) {
                var d = c(window);
                d = a.collisionPosition.top + a.collisionHeight - d.height() - d.scrollTop();
                b.top = d > 0 ? b.top - d : Math.max(b.top - a.collisionPosition.top, b.top)
            }
        },
        flip: {
            left: function(b, a) {
                if (a.at[0] !== "center") {
                    var d = c(window);
                    d = a.collisionPosition.left + a.collisionWidth - d.width() - d.scrollLeft();
                    var g = a.my[0] === "left" ? -a.elemWidth : a.my[0] === "right" ? a.elemWidth : 0
                      , e = a.at[0] === "left" ? a.targetWidth : -a.targetWidth
                      , h = -2 * a.offset[0];
                    b.left += a.collisionPosition.left < 0 ? g + e + h : d > 0 ? g + e + h : 0
                }
            },
            top: function(b, a) {
                if (a.at[1] !== "center") {
                    var d = c(window);
                    d = a.collisionPosition.top + a.collisionHeight - d.height() - d.scrollTop();
                    var g = a.my[1] === "top" ? -a.elemHeight : a.my[1] === "bottom" ? a.elemHeight : 0
                      , e = a.at[1] === "top" ? a.targetHeight : -a.targetHeight
                      , h = -2 * a.offset[1];
                    b.top += a.collisionPosition.top < 0 ? g + e + h : d > 0 ? g + e + h : 0
                }
            }
        }
    };
    if (!c.offset.setOffset) {
        c.offset.setOffset = function(b, a) {
            if (/static/.test(c.curCSS(b, "position")))
                b.style.position = "relative";
            var d = c(b)
              , g = d.offset()
              , e = parseInt(c.curCSS(b, "top", true), 10) || 0
              , h = parseInt(c.curCSS(b, "left", true), 10) || 0;
            g = {
                top: a.top - g.top + e,
                left: a.left - g.left + h
            };
            "using"in a ? a.using.call(b, g) : d.css(g)
        }
        ;
        c.fn.offset = function(b) {
            var a = this[0];
            if (!a || !a.ownerDocument)
                return null;
            if (b)
                return this.each(function() {
                    c.offset.setOffset(this, b)
                });
            return u.call(this)
        }
    }
}
)(jQuery);
;/*
 * jQuery UI Draggable 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Draggables
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.mouse.js
 *	jquery.ui.widget.js
 */
(function(d) {
    d.widget("ui.draggable", d.ui.mouse, {
        widgetEventPrefix: "drag",
        options: {
            addClasses: true,
            appendTo: "parent",
            axis: false,
            connectToSortable: false,
            containment: false,
            cursor: "auto",
            cursorAt: false,
            grid: false,
            handle: false,
            helper: "original",
            iframeFix: false,
            opacity: false,
            refreshPositions: false,
            revert: false,
            revertDuration: 500,
            scope: "default",
            scroll: true,
            scrollSensitivity: 20,
            scrollSpeed: 20,
            snap: false,
            snapMode: "both",
            snapTolerance: 20,
            stack: false,
            zIndex: false
        },
        _create: function() {
            if (this.options.helper == "original" && !/^(?:r|a|f)/.test(this.element.css("position")))
                this.element[0].style.position = "relative";
            this.options.addClasses && this.element.addClass("ui-draggable");
            this.options.disabled && this.element.addClass("ui-draggable-disabled");
            this._mouseInit()
        },
        destroy: function() {
            if (this.element.data("draggable")) {
                this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled");
                this._mouseDestroy();
                return this
            }
        },
        _mouseCapture: function(a) {
            var b = this.options;
            if (this.helper || b.disabled || d(a.target).is(".ui-resizable-handle"))
                return false;
            this.handle = this._getHandle(a);
            if (!this.handle)
                return false;
            return true
        },
        _mouseStart: function(a) {
            var b = this.options;
            this.helper = this._createHelper(a);
            this._cacheHelperProportions();
            if (d.ui.ddmanager)
                d.ui.ddmanager.current = this;
            this._cacheMargins();
            this.cssPosition = this.helper.css("position");
            this.scrollParent = this.helper.scrollParent();
            this.offset = this.positionAbs = this.element.offset();
            this.offset = {
                top: this.offset.top - this.margins.top,
                left: this.offset.left - this.margins.left
            };
            d.extend(this.offset, {
                click: {
                    left: a.pageX - this.offset.left,
                    top: a.pageY - this.offset.top
                },
                parent: this._getParentOffset(),
                relative: this._getRelativeOffset()
            });
            this.originalPosition = this.position = this._generatePosition(a);
            this.originalPageX = a.pageX;
            this.originalPageY = a.pageY;
            b.cursorAt && this._adjustOffsetFromHelper(b.cursorAt);
            b.containment && this._setContainment();
            if (this._trigger("start", a) === false) {
                this._clear();
                return false
            }
            this._cacheHelperProportions();
            d.ui.ddmanager && !b.dropBehaviour && d.ui.ddmanager.prepareOffsets(this, a);
            this.helper.addClass("ui-draggable-dragging");
            this._mouseDrag(a, true);
            return true
        },
        _mouseDrag: function(a, b) {
            this.position = this._generatePosition(a);
            this.positionAbs = this._convertPositionTo("absolute");
            if (!b) {
                b = this._uiHash();
                if (this._trigger("drag", a, b) === false) {
                    this._mouseUp({});
                    return false
                }
                this.position = b.position
            }
            if (!this.options.axis || this.options.axis != "y")
                this.helper[0].style.left = this.position.left + "px";
            if (!this.options.axis || this.options.axis != "x")
                this.helper[0].style.top = this.position.top + "px";
            d.ui.ddmanager && d.ui.ddmanager.drag(this, a);
            return false
        },
        _mouseStop: function(a) {
            var b = false;
            if (d.ui.ddmanager && !this.options.dropBehaviour)
                b = d.ui.ddmanager.drop(this, a);
            if (this.dropped) {
                b = this.dropped;
                this.dropped = false
            }
            if ((!this.element[0] || !this.element[0].parentNode) && this.options.helper == "original")
                return false;
            if (this.options.revert == "invalid" && !b || this.options.revert == "valid" && b || this.options.revert === true || d.isFunction(this.options.revert) && this.options.revert.call(this.element, b)) {
                var c = this;
                d(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function() {
                    c._trigger("stop", a) !== false && c._clear()
                })
            } else
                this._trigger("stop", a) !== false && this._clear();
            return false
        },
        cancel: function() {
            this.helper.is(".ui-draggable-dragging") ? this._mouseUp({}) : this._clear();
            return this
        },
        _getHandle: function(a) {
            var b = !this.options.handle || !d(this.options.handle, this.element).length ? true : false;
            d(this.options.handle, this.element).find("*").addBack().each(function() {
                if (this == a.target)
                    b = true
            });
            return b
        },
        _createHelper: function(a) {
            var b = this.options;
            a = d.isFunction(b.helper) ? d(b.helper.apply(this.element[0], [a])) : b.helper == "clone" ? this.element.clone() : this.element;
            a.parents("body").length || a.appendTo(b.appendTo == "parent" ? this.element[0].parentNode : b.appendTo);
            a[0] != this.element[0] && !/(fixed|absolute)/.test(a.css("position")) && a.css("position", "absolute");
            return a
        },
        _adjustOffsetFromHelper: function(a) {
            if (typeof a == "string")
                a = a.split(" ");
            if (d.isArray(a))
                a = {
                    left: +a[0],
                    top: +a[1] || 0
                };
            if ("left"in a)
                this.offset.click.left = a.left + this.margins.left;
            if ("right"in a)
                this.offset.click.left = this.helperProportions.width - a.right + this.margins.left;
            if ("top"in a)
                this.offset.click.top = a.top + this.margins.top;
            if ("bottom"in a)
                this.offset.click.top = this.helperProportions.height - a.bottom + this.margins.top
        },
        _getParentOffset: function() {
            this.offsetParent = this.helper.offsetParent();
            var a = this.offsetParent.offset();
            if (this.cssPosition == "absolute" && this.scrollParent[0] != document && d.ui.contains(this.scrollParent[0], this.offsetParent[0])) {
                a.left += this.scrollParent.scrollLeft();
                a.top += this.scrollParent.scrollTop()
            }
            if (this.offsetParent[0] == document.body || this.offsetParent[0].tagName && this.offsetParent[0].tagName.toLowerCase() == "html" && d.browser.msie)
                a = {
                    top: 0,
                    left: 0
                };
            return {
                top: a.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                left: a.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
            }
        },
        _getRelativeOffset: function() {
            if (this.cssPosition == "relative") {
                var a = this.element.position();
                return {
                    top: a.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                    left: a.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                }
            } else
                return {
                    top: 0,
                    left: 0
                }
        },
        _cacheMargins: function() {
            this.margins = {
                left: parseInt(this.element.css("marginLeft"), 10) || 0,
                top: parseInt(this.element.css("marginTop"), 10) || 0,
                right: parseInt(this.element.css("marginRight"), 10) || 0,
                bottom: parseInt(this.element.css("marginBottom"), 10) || 0
            }
        },
        _cacheHelperProportions: function() {
            this.helperProportions = {
                width: this.helper.outerWidth(),
                height: this.helper.outerHeight()
            }
        },
        _setContainment: function() {
            var a = this.options;
            if (a.containment == "parent")
                a.containment = this.helper[0].parentNode;
            if (a.containment == "document" || a.containment == "window")
                this.containment = [(a.containment == "document" ? 0 : d(window).scrollLeft()) - this.offset.relative.left - this.offset.parent.left, (a.containment == "document" ? 0 : d(window).scrollTop()) - this.offset.relative.top - this.offset.parent.top, (a.containment == "document" ? 0 : d(window).scrollLeft()) + d(a.containment == "document" ? document : window).width() - this.helperProportions.width - this.margins.left, (a.containment == "document" ? 0 : d(window).scrollTop()) + (d(a.containment == "document" ? document : window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
            if (!/^(document|window|parent)$/.test(a.containment) && a.containment.constructor != Array) {
                var b = d(a.containment)[0];
                if (b) {
                    a = d(a.containment).offset();
                    var c = d(b).css("overflow") != "hidden";
                    this.containment = [a.left + (parseInt(d(b).css("borderLeftWidth"), 10) || 0) + (parseInt(d(b).css("paddingLeft"), 10) || 0), a.top + (parseInt(d(b).css("borderTopWidth"), 10) || 0) + (parseInt(d(b).css("paddingTop"), 10) || 0), a.left + (c ? Math.max(b.scrollWidth, b.offsetWidth) : b.offsetWidth) - (parseInt(d(b).css("borderLeftWidth"), 10) || 0) - (parseInt(d(b).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right, a.top + (c ? Math.max(b.scrollHeight, b.offsetHeight) : b.offsetHeight) - (parseInt(d(b).css("borderTopWidth"), 10) || 0) - (parseInt(d(b).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom]
                }
            } else if (a.containment.constructor == Array)
                this.containment = a.containment
        },
        _convertPositionTo: function(a, b) {
            if (!b)
                b = this.position;
            a = a == "absolute" ? 1 : -1;
            var c = this.cssPosition == "absolute" && !(this.scrollParent[0] != document && d.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent
              , f = /(html|body)/i.test(c[0].tagName);
            return {
                top: b.top + this.offset.relative.top * a + this.offset.parent.top * a - (d.browser.safari && d.browser.version < 526 && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : f ? 0 : c.scrollTop()) * a),
                left: b.left + this.offset.relative.left * a + this.offset.parent.left * a - (d.browser.safari && d.browser.version < 526 && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : f ? 0 : c.scrollLeft()) * a)
            }
        },
        _generatePosition: function(a) {
            var b = this.options
              , c = this.cssPosition == "absolute" && !(this.scrollParent[0] != document && d.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent
              , f = /(html|body)/i.test(c[0].tagName)
              , e = a.pageX
              , g = a.pageY;
            if (this.originalPosition) {
                if (this.containment) {
                    if (a.pageX - this.offset.click.left < this.containment[0])
                        e = this.containment[0] + this.offset.click.left;
                    if (a.pageY - this.offset.click.top < this.containment[1])
                        g = this.containment[1] + this.offset.click.top;
                    if (a.pageX - this.offset.click.left > this.containment[2])
                        e = this.containment[2] + this.offset.click.left;
                    if (a.pageY - this.offset.click.top > this.containment[3])
                        g = this.containment[3] + this.offset.click.top
                }
                if (b.grid) {
                    g = this.originalPageY + Math.round((g - this.originalPageY) / b.grid[1]) * b.grid[1];
                    g = this.containment ? !(g - this.offset.click.top < this.containment[1] || g - this.offset.click.top > this.containment[3]) ? g : !(g - this.offset.click.top < this.containment[1]) ? g - b.grid[1] : g + b.grid[1] : g;
                    e = this.originalPageX + Math.round((e - this.originalPageX) / b.grid[0]) * b.grid[0];
                    e = this.containment ? !(e - this.offset.click.left < this.containment[0] || e - this.offset.click.left > this.containment[2]) ? e : !(e - this.offset.click.left < this.containment[0]) ? e - b.grid[0] : e + b.grid[0] : e
                }
            }
            return {
                top: g - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + (d.browser.safari && d.browser.version < 526 && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : f ? 0 : c.scrollTop()),
                left: e - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + (d.browser.safari && d.browser.version < 526 && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : f ? 0 : c.scrollLeft())
            }
        },
        _clear: function() {
            this.helper.removeClass("ui-draggable-dragging");
            this.helper[0] != this.element[0] && !this.cancelHelperRemoval && this.helper.remove();
            this.helper = null;
            this.cancelHelperRemoval = false
        },
        _trigger: function(a, b, c) {
            c = c || this._uiHash();
            d.ui.plugin.call(this, a, [b, c]);
            if (a == "drag")
                this.positionAbs = this._convertPositionTo("absolute");
            return d.Widget.prototype._trigger.call(this, a, b, c)
        },
        plugins: {},
        _uiHash: function() {
            return {
                helper: this.helper,
                position: this.position,
                originalPosition: this.originalPosition,
                offset: this.positionAbs
            }
        }
    });
    d.extend(d.ui.draggable, {
        version: "1.8.11"
    });
    d.ui.plugin.add("draggable", "connectToSortable", {
        start: function(a, b) {
            var c = d(this).data("draggable")
              , f = c.options
              , e = d.extend({}, b, {
                item: c.element
            });
            c.sortables = [];
            d(f.connectToSortable).each(function() {
                var g = d.data(this, "sortable");
                if (g && !g.options.disabled) {
                    c.sortables.push({
                        instance: g,
                        shouldRevert: g.options.revert
                    });
                    g.refreshPositions();
                    g._trigger("activate", a, e)
                }
            })
        },
        stop: function(a, b) {
            var c = d(this).data("draggable")
              , f = d.extend({}, b, {
                item: c.element
            });
            d.each(c.sortables, function() {
                if (this.instance.isOver) {
                    this.instance.isOver = 0;
                    c.cancelHelperRemoval = true;
                    this.instance.cancelHelperRemoval = false;
                    if (this.shouldRevert)
                        this.instance.options.revert = true;
                    this.instance._mouseStop(a);
                    this.instance.options.helper = this.instance.options._helper;
                    c.options.helper == "original" && this.instance.currentItem.css({
                        top: "auto",
                        left: "auto"
                    })
                } else {
                    this.instance.cancelHelperRemoval = false;
                    this.instance._trigger("deactivate", a, f)
                }
            })
        },
        drag: function(a, b) {
            var c = d(this).data("draggable")
              , f = this;
            d.each(c.sortables, function() {
                this.instance.positionAbs = c.positionAbs;
                this.instance.helperProportions = c.helperProportions;
                this.instance.offset.click = c.offset.click;
                if (this.instance._intersectsWith(this.instance.containerCache)) {
                    if (!this.instance.isOver) {
                        this.instance.isOver = 1;
                        this.instance.currentItem = d(f).clone().appendTo(this.instance.element).data("sortable-item", true);
                        this.instance.options._helper = this.instance.options.helper;
                        this.instance.options.helper = function() {
                            return b.helper[0]
                        }
                        ;
                        a.target = this.instance.currentItem[0];
                        this.instance._mouseCapture(a, true);
                        this.instance._mouseStart(a, true, true);
                        this.instance.offset.click.top = c.offset.click.top;
                        this.instance.offset.click.left = c.offset.click.left;
                        this.instance.offset.parent.left -= c.offset.parent.left - this.instance.offset.parent.left;
                        this.instance.offset.parent.top -= c.offset.parent.top - this.instance.offset.parent.top;
                        c._trigger("toSortable", a);
                        c.dropped = this.instance.element;
                        c.currentItem = c.element;
                        this.instance.fromOutside = c
                    }
                    this.instance.currentItem && this.instance._mouseDrag(a)
                } else if (this.instance.isOver) {
                    this.instance.isOver = 0;
                    this.instance.cancelHelperRemoval = true;
                    this.instance.options.revert = false;
                    this.instance._trigger("out", a, this.instance._uiHash(this.instance));
                    this.instance._mouseStop(a, true);
                    this.instance.options.helper = this.instance.options._helper;
                    this.instance.currentItem.remove();
                    this.instance.placeholder && this.instance.placeholder.remove();
                    c._trigger("fromSortable", a);
                    c.dropped = false
                }
            })
        }
    });
    d.ui.plugin.add("draggable", "cursor", {
        start: function() {
            var a = d("body")
              , b = d(this).data("draggable").options;
            if (a.css("cursor"))
                b._cursor = a.css("cursor");
            a.css("cursor", b.cursor)
        },
        stop: function() {
            var a = d(this).data("draggable").options;
            a._cursor && d("body").css("cursor", a._cursor)
        }
    });
    d.ui.plugin.add("draggable", "iframeFix", {
        start: function() {
            var a = d(this).data("draggable").options;
            d(a.iframeFix === true ? "iframe" : a.iframeFix).each(function() {
                d('<div class="ui-draggable-iframeFix" style="background: #fff;"></div>').css({
                    width: this.offsetWidth + "px",
                    height: this.offsetHeight + "px",
                    position: "absolute",
                    opacity: "0.001",
                    zIndex: 1E3
                }).css(d(this).offset()).appendTo("body")
            })
        },
        stop: function() {
            d("div.ui-draggable-iframeFix").each(function() {
                this.parentNode.removeChild(this)
            })
        }
    });
    d.ui.plugin.add("draggable", "opacity", {
        start: function(a, b) {
            a = d(b.helper);
            b = d(this).data("draggable").options;
            if (a.css("opacity"))
                b._opacity = a.css("opacity");
            a.css("opacity", b.opacity)
        },
        stop: function(a, b) {
            a = d(this).data("draggable").options;
            a._opacity && d(b.helper).css("opacity", a._opacity)
        }
    });
    d.ui.plugin.add("draggable", "scroll", {
        start: function() {
            var a = d(this).data("draggable");
            if (a.scrollParent[0] != document && a.scrollParent[0].tagName != "HTML")
                a.overflowOffset = a.scrollParent.offset()
        },
        drag: function(a) {
            var b = d(this).data("draggable")
              , c = b.options
              , f = false;
            if (b.scrollParent[0] != document && b.scrollParent[0].tagName != "HTML") {
                if (!c.axis || c.axis != "x")
                    if (b.overflowOffset.top + b.scrollParent[0].offsetHeight - a.pageY < c.scrollSensitivity)
                        b.scrollParent[0].scrollTop = f = b.scrollParent[0].scrollTop + c.scrollSpeed;
                    else if (a.pageY - b.overflowOffset.top < c.scrollSensitivity)
                        b.scrollParent[0].scrollTop = f = b.scrollParent[0].scrollTop - c.scrollSpeed;
                if (!c.axis || c.axis != "y")
                    if (b.overflowOffset.left + b.scrollParent[0].offsetWidth - a.pageX < c.scrollSensitivity)
                        b.scrollParent[0].scrollLeft = f = b.scrollParent[0].scrollLeft + c.scrollSpeed;
                    else if (a.pageX - b.overflowOffset.left < c.scrollSensitivity)
                        b.scrollParent[0].scrollLeft = f = b.scrollParent[0].scrollLeft - c.scrollSpeed
            } else {
                if (!c.axis || c.axis != "x")
                    if (a.pageY - d(document).scrollTop() < c.scrollSensitivity)
                        f = d(document).scrollTop(d(document).scrollTop() - c.scrollSpeed);
                    else if (d(window).height() - (a.pageY - d(document).scrollTop()) < c.scrollSensitivity)
                        f = d(document).scrollTop(d(document).scrollTop() + c.scrollSpeed);
                if (!c.axis || c.axis != "y")
                    if (a.pageX - d(document).scrollLeft() < c.scrollSensitivity)
                        f = d(document).scrollLeft(d(document).scrollLeft() - c.scrollSpeed);
                    else if (d(window).width() - (a.pageX - d(document).scrollLeft()) < c.scrollSensitivity)
                        f = d(document).scrollLeft(d(document).scrollLeft() + c.scrollSpeed)
            }
            f !== false && d.ui.ddmanager && !c.dropBehaviour && d.ui.ddmanager.prepareOffsets(b, a)
        }
    });
    d.ui.plugin.add("draggable", "snap", {
        start: function() {
            var a = d(this).data("draggable")
              , b = a.options;
            a.snapElements = [];
            d(b.snap.constructor != String ? b.snap.items || ":data(draggable)" : b.snap).each(function() {
                var c = d(this)
                  , f = c.offset();
                this != a.element[0] && a.snapElements.push({
                    item: this,
                    width: c.outerWidth(),
                    height: c.outerHeight(),
                    top: f.top,
                    left: f.left
                })
            })
        },
        drag: function(a, b) {
            for (var c = d(this).data("draggable"), f = c.options, e = f.snapTolerance, g = b.offset.left, n = g + c.helperProportions.width, m = b.offset.top, o = m + c.helperProportions.height, h = c.snapElements.length - 1; h >= 0; h--) {
                var i = c.snapElements[h].left
                  , k = i + c.snapElements[h].width
                  , j = c.snapElements[h].top
                  , l = j + c.snapElements[h].height;
                if (i - e < g && g < k + e && j - e < m && m < l + e || i - e < g && g < k + e && j - e < o && o < l + e || i - e < n && n < k + e && j - e < m && m < l + e || i - e < n && n < k + e && j - e < o && o < l + e) {
                    if (f.snapMode != "inner") {
                        var p = Math.abs(j - o) <= e
                          , q = Math.abs(l - m) <= e
                          , r = Math.abs(i - n) <= e
                          , s = Math.abs(k - g) <= e;
                        if (p)
                            b.position.top = c._convertPositionTo("relative", {
                                top: j - c.helperProportions.height,
                                left: 0
                            }).top - c.margins.top;
                        if (q)
                            b.position.top = c._convertPositionTo("relative", {
                                top: l,
                                left: 0
                            }).top - c.margins.top;
                        if (r)
                            b.position.left = c._convertPositionTo("relative", {
                                top: 0,
                                left: i - c.helperProportions.width
                            }).left - c.margins.left;
                        if (s)
                            b.position.left = c._convertPositionTo("relative", {
                                top: 0,
                                left: k
                            }).left - c.margins.left
                    }
                    var t = p || q || r || s;
                    if (f.snapMode != "outer") {
                        p = Math.abs(j - m) <= e;
                        q = Math.abs(l - o) <= e;
                        r = Math.abs(i - g) <= e;
                        s = Math.abs(k - n) <= e;
                        if (p)
                            b.position.top = c._convertPositionTo("relative", {
                                top: j,
                                left: 0
                            }).top - c.margins.top;
                        if (q)
                            b.position.top = c._convertPositionTo("relative", {
                                top: l - c.helperProportions.height,
                                left: 0
                            }).top - c.margins.top;
                        if (r)
                            b.position.left = c._convertPositionTo("relative", {
                                top: 0,
                                left: i
                            }).left - c.margins.left;
                        if (s)
                            b.position.left = c._convertPositionTo("relative", {
                                top: 0,
                                left: k - c.helperProportions.width
                            }).left - c.margins.left
                    }
                    if (!c.snapElements[h].snapping && (p || q || r || s || t))
                        c.options.snap.snap && c.options.snap.snap.call(c.element, a, d.extend(c._uiHash(), {
                            snapItem: c.snapElements[h].item
                        }));
                    c.snapElements[h].snapping = p || q || r || s || t
                } else {
                    c.snapElements[h].snapping && c.options.snap.release && c.options.snap.release.call(c.element, a, d.extend(c._uiHash(), {
                        snapItem: c.snapElements[h].item
                    }));
                    c.snapElements[h].snapping = false
                }
            }
        }
    });
    d.ui.plugin.add("draggable", "stack", {
        start: function() {
            var a = d(this).data("draggable").options;
            a = d.makeArray(d(a.stack)).sort(function(c, f) {
                return (parseInt(d(c).css("zIndex"), 10) || 0) - (parseInt(d(f).css("zIndex"), 10) || 0)
            });
            if (a.length) {
                var b = parseInt(a[0].style.zIndex) || 0;
                d(a).each(function(c) {
                    this.style.zIndex = b + c
                });
                this[0].style.zIndex = b + a.length
            }
        }
    });
    d.ui.plugin.add("draggable", "zIndex", {
        start: function(a, b) {
            a = d(b.helper);
            b = d(this).data("draggable").options;
            if (a.css("zIndex"))
                b._zIndex = a.css("zIndex");
            a.css("zIndex", b.zIndex)
        },
        stop: function(a, b) {
            a = d(this).data("draggable").options;
            a._zIndex && d(b.helper).css("zIndex", a._zIndex)
        }
    })
}
)(jQuery);
;/*
 * jQuery UI Resizable 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Resizables
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.mouse.js
 *	jquery.ui.widget.js
 */
(function(e) {
    e.widget("ui.resizable", e.ui.mouse, {
        widgetEventPrefix: "resize",
        options: {
            alsoResize: false,
            animate: false,
            animateDuration: "slow",
            animateEasing: "swing",
            aspectRatio: false,
            autoHide: false,
            containment: false,
            ghost: false,
            grid: false,
            handles: "e,s,se",
            helper: false,
            maxHeight: null,
            maxWidth: null,
            minHeight: 10,
            minWidth: 10,
            zIndex: 1E3
        },
        _create: function() {
            var b = this
              , a = this.options;
            this.element.addClass("ui-resizable");
            e.extend(this, {
                _aspectRatio: !!a.aspectRatio,
                aspectRatio: a.aspectRatio,
                originalElement: this.element,
                _proportionallyResizeElements: [],
                _helper: a.helper || a.ghost || a.animate ? a.helper || "ui-resizable-helper" : null
            });
            if (this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i)) {
                /relative/.test(this.element.css("position")) && e.browser.opera && this.element.css({
                    position: "relative",
                    top: "auto",
                    left: "auto"
                });
                this.element.wrap(e('<div class="ui-wrapper" style="overflow: hidden;"></div>').css({
                    position: this.element.css("position"),
                    width: this.element.outerWidth(),
                    height: this.element.outerHeight(),
                    top: this.element.css("top"),
                    left: this.element.css("left")
                }));
                this.element = this.element.parent().data("resizable", this.element.data("resizable"));
                this.elementIsWrapper = true;
                this.element.css({
                    marginLeft: this.originalElement.css("marginLeft"),
                    marginTop: this.originalElement.css("marginTop"),
                    marginRight: this.originalElement.css("marginRight"),
                    marginBottom: this.originalElement.css("marginBottom")
                });
                this.originalElement.css({
                    marginLeft: 0,
                    marginTop: 0,
                    marginRight: 0,
                    marginBottom: 0
                });
                this.originalResizeStyle = this.originalElement.css("resize");
                this.originalElement.css("resize", "none");
                this._proportionallyResizeElements.push(this.originalElement.css({
                    position: "static",
                    zoom: 1,
                    display: "block"
                }));
                this.originalElement.css({
                    margin: this.originalElement.css("margin")
                });
                this._proportionallyResize()
            }
            this.handles = a.handles || (!e(".ui-resizable-handle", this.element).length ? "e,s,se" : {
                n: ".ui-resizable-n",
                e: ".ui-resizable-e",
                s: ".ui-resizable-s",
                w: ".ui-resizable-w",
                se: ".ui-resizable-se",
                sw: ".ui-resizable-sw",
                ne: ".ui-resizable-ne",
                nw: ".ui-resizable-nw"
            });
            if (this.handles.constructor == String) {
                if (this.handles == "all")
                    this.handles = "n,e,s,w,se,sw,ne,nw";
                var c = this.handles.split(",");
                this.handles = {};
                for (var d = 0; d < c.length; d++) {
                    var f = e.trim(c[d])
                      , g = e('<div class="ui-resizable-handle ' + ("ui-resizable-" + f) + '"></div>');
                    /sw|se|ne|nw/.test(f) && g.css({
                        zIndex: ++a.zIndex
                    });
                    "se" == f && g.addClass("ui-icon ui-icon-gripsmall-diagonal-se");
                    this.handles[f] = ".ui-resizable-" + f;
                    this.element.append(g)
                }
            }
            this._renderAxis = function(h) {
                h = h || this.element;
                for (var i in this.handles) {
                    if (this.handles[i].constructor == String)
                        this.handles[i] = e(this.handles[i], this.element).show();
                    if (this.elementIsWrapper && this.originalElement[0].nodeName.match(/textarea|input|select|button/i)) {
                        var j = e(this.handles[i], this.element)
                          , k = 0;
                        k = /sw|ne|nw|se|n|s/.test(i) ? j.outerHeight() : j.outerWidth();
                        j = ["padding", /ne|nw|n/.test(i) ? "Top" : /se|sw|s/.test(i) ? "Bottom" : /^e$/.test(i) ? "Right" : "Left"].join("");
                        h.css(j, k);
                        this._proportionallyResize()
                    }
                    e(this.handles[i])
                }
            }
            ;
            this._renderAxis(this.element);
            this._handles = e(".ui-resizable-handle", this.element).disableSelection();
            this._handles.mouseover(function() {
                if (!b.resizing) {
                    if (this.className)
                        var h = this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
                    b.axis = h && h[1] ? h[1] : "se"
                }
            });
            if (a.autoHide) {
                this._handles.hide();
                e(this.element).addClass("ui-resizable-autohide").hover(function() {
                    e(this).removeClass("ui-resizable-autohide");
                    b._handles.show()
                }, function() {
                    if (!b.resizing) {
                        e(this).addClass("ui-resizable-autohide");
                        b._handles.hide()
                    }
                })
            }
            this._mouseInit()
        },
        destroy: function() {
            this._mouseDestroy();
            var b = function(c) {
                e(c).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").unbind(".resizable").find(".ui-resizable-handle").remove()
            };
            if (this.elementIsWrapper) {
                b(this.element);
                var a = this.element;
                a.after(this.originalElement.css({
                    position: a.css("position"),
                    width: a.outerWidth(),
                    height: a.outerHeight(),
                    top: a.css("top"),
                    left: a.css("left")
                })).remove()
            }
            this.originalElement.css("resize", this.originalResizeStyle);
            b(this.originalElement);
            return this
        },
        _mouseCapture: function(b) {
            var a = false;
            for (var c in this.handles)
                if (e(this.handles[c])[0] == b.target)
                    a = true;
            return !this.options.disabled && a
        },
        _mouseStart: function(b) {
            var a = this.options
              , c = this.element.position()
              , d = this.element;
            this.resizing = true;
            this.documentScroll = {
                top: e(document).scrollTop(),
                left: e(document).scrollLeft()
            };
            if (d.is(".ui-draggable") || /absolute/.test(d.css("position")))
                d.css({
                    position: "absolute",
                    top: c.top,
                    left: c.left
                });
            e.browser.opera && /relative/.test(d.css("position")) && d.css({
                position: "relative",
                top: "auto",
                left: "auto"
            });
            this._renderProxy();
            c = m(this.helper.css("left"));
            var f = m(this.helper.css("top"));
            if (a.containment) {
                c += e(a.containment).scrollLeft() || 0;
                f += e(a.containment).scrollTop() || 0
            }
            this.offset = this.helper.offset();
            this.position = {
                left: c,
                top: f
            };
            this.size = this._helper ? {
                width: d.outerWidth(),
                height: d.outerHeight()
            } : {
                width: d.width(),
                height: d.height()
            };
            this.originalSize = this._helper ? {
                width: d.outerWidth(),
                height: d.outerHeight()
            } : {
                width: d.width(),
                height: d.height()
            };
            this.originalPosition = {
                left: c,
                top: f
            };
            this.sizeDiff = {
                width: d.outerWidth() - d.width(),
                height: d.outerHeight() - d.height()
            };
            this.originalMousePosition = {
                left: b.pageX,
                top: b.pageY
            };
            this.aspectRatio = typeof a.aspectRatio == "number" ? a.aspectRatio : this.originalSize.width / this.originalSize.height || 1;
            a = e(".ui-resizable-" + this.axis).css("cursor");
            e("body").css("cursor", a == "auto" ? this.axis + "-resize" : a);
            d.addClass("ui-resizable-resizing");
            this._propagate("start", b);
            return true
        },
        _mouseDrag: function(b) {
            var a = this.helper
              , c = this.originalMousePosition
              , d = this._change[this.axis];
            if (!d)
                return false;
            c = d.apply(this, [b, b.pageX - c.left || 0, b.pageY - c.top || 0]);
            if (this._aspectRatio || b.shiftKey)
                c = this._updateRatio(c, b);
            c = this._respectSize(c, b);
            this._propagate("resize", b);
            a.css({
                top: this.position.top + "px",
                left: this.position.left + "px",
                width: this.size.width + "px",
                height: this.size.height + "px"
            });
            !this._helper && this._proportionallyResizeElements.length && this._proportionallyResize();
            this._updateCache(c);
            this._trigger("resize", b, this.ui());
            return false
        },
        _mouseStop: function(b) {
            this.resizing = false;
            var a = this.options
              , c = this;
            if (this._helper) {
                var d = this._proportionallyResizeElements
                  , f = d.length && /textarea/i.test(d[0].nodeName);
                d = f && e.ui.hasScroll(d[0], "left") ? 0 : c.sizeDiff.height;
                f = f ? 0 : c.sizeDiff.width;
                f = {
                    width: c.helper.width() - f,
                    height: c.helper.height() - d
                };
                d = parseInt(c.element.css("left"), 10) + (c.position.left - c.originalPosition.left) || null;
                var g = parseInt(c.element.css("top"), 10) + (c.position.top - c.originalPosition.top) || null;
                a.animate || this.element.css(e.extend(f, {
                    top: g,
                    left: d
                }));
                c.helper.height(c.size.height);
                c.helper.width(c.size.width);
                this._helper && !a.animate && this._proportionallyResize()
            }
            e("body").css("cursor", "auto");
            this.element.removeClass("ui-resizable-resizing");
            this._propagate("stop", b);
            this._helper && this.helper.remove();
            return false
        },
        _updateCache: function(b) {
            this.offset = this.helper.offset();
            if (l(b.left))
                this.position.left = b.left;
            if (l(b.top))
                this.position.top = b.top;
            if (l(b.height))
                this.size.height = b.height;
            if (l(b.width))
                this.size.width = b.width
        },
        _updateRatio: function(b) {
            var a = this.position
              , c = this.size
              , d = this.axis;
            if (b.height)
                b.width = c.height * this.aspectRatio;
            else if (b.width)
                b.height = c.width / this.aspectRatio;
            if (d == "sw") {
                b.left = a.left + (c.width - b.width);
                b.top = null
            }
            if (d == "nw") {
                b.top = a.top + (c.height - b.height);
                b.left = a.left + (c.width - b.width)
            }
            return b
        },
        _respectSize: function(b) {
            var a = this.options
              , c = this.axis
              , d = l(b.width) && a.maxWidth && a.maxWidth < b.width
              , f = l(b.height) && a.maxHeight && a.maxHeight < b.height
              , g = l(b.width) && a.minWidth && a.minWidth > b.width
              , h = l(b.height) && a.minHeight && a.minHeight > b.height;
            if (g)
                b.width = a.minWidth;
            if (h)
                b.height = a.minHeight;
            if (d)
                b.width = a.maxWidth;
            if (f)
                b.height = a.maxHeight;
            var i = this.originalPosition.left + this.originalSize.width
              , j = this.position.top + this.size.height
              , k = /sw|nw|w/.test(c);
            c = /nw|ne|n/.test(c);
            if (g && k)
                b.left = i - a.minWidth;
            if (d && k)
                b.left = i - a.maxWidth;
            if (h && c)
                b.top = j - a.minHeight;
            if (f && c)
                b.top = j - a.maxHeight;
            if ((a = !b.width && !b.height) && !b.left && b.top)
                b.top = null;
            else if (a && !b.top && b.left)
                b.left = null;
            return b
        },
        _proportionallyResize: function() {
            if (this._proportionallyResizeElements.length)
                for (var b = this.helper || this.element, a = 0; a < this._proportionallyResizeElements.length; a++) {
                    var c = this._proportionallyResizeElements[a];
                    if (!this.borderDif) {
                        var d = [c.css("borderTopWidth"), c.css("borderRightWidth"), c.css("borderBottomWidth"), c.css("borderLeftWidth")]
                          , f = [c.css("paddingTop"), c.css("paddingRight"), c.css("paddingBottom"), c.css("paddingLeft")];
                        this.borderDif = e.map(d, function(g, h) {
                            g = parseInt(g, 10) || 0;
                            h = parseInt(f[h], 10) || 0;
                            return g + h
                        })
                    }
                    e.browser.msie && (e(b).is(":hidden") || e(b).parents(":hidden").length) || c.css({
                        height: b.height() - this.borderDif[0] - this.borderDif[2] || 0,
                        width: b.width() - this.borderDif[1] - this.borderDif[3] || 0
                    })
                }
        },
        _renderProxy: function() {
            var b = this.options;
            this.elementOffset = this.element.offset();
            if (this._helper) {
                this.helper = this.helper || e('<div style="overflow:hidden;"></div>');
                var a = e.browser.msie && e.browser.version < 7
                  , c = a ? 1 : 0;
                a = a ? 2 : -1;
                this.helper.addClass(this._helper).css({
                    width: this.element.outerWidth() + a,
                    height: this.element.outerHeight() + a,
                    position: "absolute",
                    left: this.elementOffset.left - c + "px",
                    top: this.elementOffset.top - c + "px",
                    zIndex: ++b.zIndex
                });
                this.helper.appendTo("body").disableSelection()
            } else
                this.helper = this.element
        },
        _change: {
            e: function(b, a) {
                return {
                    width: this.originalSize.width + a
                }
            },
            w: function(b, a) {
                return {
                    left: this.originalPosition.left + a,
                    width: this.originalSize.width - a
                }
            },
            n: function(b, a, c) {
                return {
                    top: this.originalPosition.top + c,
                    height: this.originalSize.height - c
                }
            },
            s: function(b, a, c) {
                return {
                    height: this.originalSize.height + c
                }
            },
            se: function(b, a, c) {
                return e.extend(this._change.s.apply(this, arguments), this._change.e.apply(this, [b, a, c]))
            },
            sw: function(b, a, c) {
                return e.extend(this._change.s.apply(this, arguments), this._change.w.apply(this, [b, a, c]))
            },
            ne: function(b, a, c) {
                return e.extend(this._change.n.apply(this, arguments), this._change.e.apply(this, [b, a, c]))
            },
            nw: function(b, a, c) {
                return e.extend(this._change.n.apply(this, arguments), this._change.w.apply(this, [b, a, c]))
            }
        },
        _propagate: function(b, a) {
            e.ui.plugin.call(this, b, [a, this.ui()]);
            b != "resize" && this._trigger(b, a, this.ui())
        },
        plugins: {},
        ui: function() {
            return {
                originalElement: this.originalElement,
                element: this.element,
                helper: this.helper,
                position: this.position,
                size: this.size,
                originalSize: this.originalSize,
                originalPosition: this.originalPosition
            }
        }
    });
    e.extend(e.ui.resizable, {
        version: "1.8.11"
    });
    e.ui.plugin.add("resizable", "alsoResize", {
        start: function() {
            var b = e(this).data("resizable").options
              , a = function(c) {
                e(c).each(function() {
                    var d = e(this);
                    d.data("resizable-alsoresize", {
                        width: parseInt(d.width(), 10),
                        height: parseInt(d.height(), 10),
                        left: parseInt(d.css("left"), 10),
                        top: parseInt(d.css("top"), 10),
                        position: d.css("position")
                    })
                })
            };
            if (typeof b.alsoResize == "object" && !b.alsoResize.parentNode)
                if (b.alsoResize.length) {
                    b.alsoResize = b.alsoResize[0];
                    a(b.alsoResize)
                } else
                    e.each(b.alsoResize, function(c) {
                        a(c)
                    });
            else
                a(b.alsoResize)
        },
        resize: function(b, a) {
            var c = e(this).data("resizable");
            b = c.options;
            var d = c.originalSize
              , f = c.originalPosition
              , g = {
                height: c.size.height - d.height || 0,
                width: c.size.width - d.width || 0,
                top: c.position.top - f.top || 0,
                left: c.position.left - f.left || 0
            }
              , h = function(i, j) {
                e(i).each(function() {
                    var k = e(this)
                      , q = e(this).data("resizable-alsoresize")
                      , p = {}
                      , r = j && j.length ? j : k.parents(a.originalElement[0]).length ? ["width", "height"] : ["width", "height", "top", "left"];
                    e.each(r, function(n, o) {
                        if ((n = (q[o] || 0) + (g[o] || 0)) && n >= 0)
                            p[o] = n || null
                    });
                    if (e.browser.opera && /relative/.test(k.css("position"))) {
                        c._revertToRelativePosition = true;
                        k.css({
                            position: "absolute",
                            top: "auto",
                            left: "auto"
                        })
                    }
                    k.css(p)
                })
            };
            typeof b.alsoResize == "object" && !b.alsoResize.nodeType ? e.each(b.alsoResize, function(i, j) {
                h(i, j)
            }) : h(b.alsoResize)
        },
        stop: function() {
            var b = e(this).data("resizable")
              , a = b.options
              , c = function(d) {
                e(d).each(function() {
                    var f = e(this);
                    f.css({
                        position: f.data("resizable-alsoresize").position
                    })
                })
            };
            if (b._revertToRelativePosition) {
                b._revertToRelativePosition = false;
                typeof a.alsoResize == "object" && !a.alsoResize.nodeType ? e.each(a.alsoResize, function(d) {
                    c(d)
                }) : c(a.alsoResize)
            }
            e(this).removeData("resizable-alsoresize")
        }
    });
    e.ui.plugin.add("resizable", "animate", {
        stop: function(b) {
            var a = e(this).data("resizable")
              , c = a.options
              , d = a._proportionallyResizeElements
              , f = d.length && /textarea/i.test(d[0].nodeName)
              , g = f && e.ui.hasScroll(d[0], "left") ? 0 : a.sizeDiff.height;
            f = {
                width: a.size.width - (f ? 0 : a.sizeDiff.width),
                height: a.size.height - g
            };
            g = parseInt(a.element.css("left"), 10) + (a.position.left - a.originalPosition.left) || null;
            var h = parseInt(a.element.css("top"), 10) + (a.position.top - a.originalPosition.top) || null;
            a.element.animate(e.extend(f, h && g ? {
                top: h,
                left: g
            } : {}), {
                duration: c.animateDuration,
                easing: c.animateEasing,
                step: function() {
                    var i = {
                        width: parseInt(a.element.css("width"), 10),
                        height: parseInt(a.element.css("height"), 10),
                        top: parseInt(a.element.css("top"), 10),
                        left: parseInt(a.element.css("left"), 10)
                    };
                    d && d.length && e(d[0]).css({
                        width: i.width,
                        height: i.height
                    });
                    a._updateCache(i);
                    a._propagate("resize", b)
                }
            })
        }
    });
    e.ui.plugin.add("resizable", "containment", {
        start: function() {
            var b = e(this).data("resizable")
              , a = b.element
              , c = b.options.containment;
            if (a = c instanceof e ? c.get(0) : /parent/.test(c) ? a.parent().get(0) : c) {
                b.containerElement = e(a);
                if (/document/.test(c) || c == document) {
                    b.containerOffset = {
                        left: 0,
                        top: 0
                    };
                    b.containerPosition = {
                        left: 0,
                        top: 0
                    };
                    b.parentData = {
                        element: e(document),
                        left: 0,
                        top: 0,
                        width: e(document).width(),
                        height: e(document).height() || document.body.parentNode.scrollHeight
                    }
                } else {
                    var d = e(a)
                      , f = [];
                    e(["Top", "Right", "Left", "Bottom"]).each(function(i, j) {
                        f[i] = m(d.css("padding" + j))
                    });
                    b.containerOffset = d.offset();
                    b.containerPosition = d.position();
                    b.containerSize = {
                        height: d.innerHeight() - f[3],
                        width: d.innerWidth() - f[1]
                    };
                    c = b.containerOffset;
                    var g = b.containerSize.height
                      , h = b.containerSize.width;
                    h = e.ui.hasScroll(a, "left") ? a.scrollWidth : h;
                    g = e.ui.hasScroll(a) ? a.scrollHeight : g;
                    b.parentData = {
                        element: a,
                        left: c.left,
                        top: c.top,
                        width: h,
                        height: g
                    }
                }
            }
        },
        resize: function(b) {
            var a = e(this).data("resizable")
              , c = a.options
              , d = a.containerOffset
              , f = a.position;
            b = a._aspectRatio || b.shiftKey;
            var g = {
                top: 0,
                left: 0
            }
              , h = a.containerElement;
            if (h[0] != document && /static/.test(h.css("position")))
                g = d;
            if (f.left < (a._helper ? d.left : 0)) {
                a.size.width += a._helper ? a.position.left - d.left : a.position.left - g.left;
                if (b)
                    a.size.height = a.size.width / c.aspectRatio;
                a.position.left = c.helper ? d.left : 0
            }
            if (f.top < (a._helper ? d.top : 0)) {
                a.size.height += a._helper ? a.position.top - d.top : a.position.top;
                if (b)
                    a.size.width = a.size.height * c.aspectRatio;
                a.position.top = a._helper ? d.top : 0
            }
            a.offset.left = a.parentData.left + a.position.left;
            a.offset.top = a.parentData.top + a.position.top;
            c = Math.abs((a._helper ? a.offset.left - g.left : a.offset.left - g.left) + a.sizeDiff.width);
            d = Math.abs((a._helper ? a.offset.top - g.top : a.offset.top - d.top) + a.sizeDiff.height);
            f = a.containerElement.get(0) == a.element.parent().get(0);
            g = /relative|absolute/.test(a.containerElement.css("position"));
            if (f && g)
                c -= a.parentData.left;
            if (c + a.size.width >= a.parentData.width) {
                a.size.width = a.parentData.width - c;
                if (b)
                    a.size.height = a.size.width / a.aspectRatio
            }
            if (d + a.size.height >= a.parentData.height) {
                a.size.height = a.parentData.height - d;
                if (b)
                    a.size.width = a.size.height * a.aspectRatio
            }
        },
        stop: function() {
            var b = e(this).data("resizable")
              , a = b.options
              , c = b.containerOffset
              , d = b.containerPosition
              , f = b.containerElement
              , g = e(b.helper)
              , h = g.offset()
              , i = g.outerWidth() - b.sizeDiff.width;
            g = g.outerHeight() - b.sizeDiff.height;
            b._helper && !a.animate && /relative/.test(f.css("position")) && e(this).css({
                left: h.left - d.left - c.left,
                width: i,
                height: g
            });
            b._helper && !a.animate && /static/.test(f.css("position")) && e(this).css({
                left: h.left - d.left - c.left,
                width: i,
                height: g
            })
        }
    });
    e.ui.plugin.add("resizable", "ghost", {
        start: function() {
            var b = e(this).data("resizable")
              , a = b.options
              , c = b.size;
            b.ghost = b.originalElement.clone();
            b.ghost.css({
                opacity: 0.25,
                display: "block",
                position: "relative",
                height: c.height,
                width: c.width,
                margin: 0,
                left: 0,
                top: 0
            }).addClass("ui-resizable-ghost").addClass(typeof a.ghost == "string" ? a.ghost : "");
            b.ghost.appendTo(b.helper)
        },
        resize: function() {
            var b = e(this).data("resizable");
            b.ghost && b.ghost.css({
                position: "relative",
                height: b.size.height,
                width: b.size.width
            })
        },
        stop: function() {
            var b = e(this).data("resizable");
            b.ghost && b.helper && b.helper.get(0).removeChild(b.ghost.get(0))
        }
    });
    e.ui.plugin.add("resizable", "grid", {
        resize: function() {
            var b = e(this).data("resizable")
              , a = b.options
              , c = b.size
              , d = b.originalSize
              , f = b.originalPosition
              , g = b.axis;
            a.grid = typeof a.grid == "number" ? [a.grid, a.grid] : a.grid;
            var h = Math.round((c.width - d.width) / (a.grid[0] || 1)) * (a.grid[0] || 1);
            a = Math.round((c.height - d.height) / (a.grid[1] || 1)) * (a.grid[1] || 1);
            if (/^(se|s|e)$/.test(g)) {
                b.size.width = d.width + h;
                b.size.height = d.height + a
            } else if (/^(ne)$/.test(g)) {
                b.size.width = d.width + h;
                b.size.height = d.height + a;
                b.position.top = f.top - a
            } else {
                if (/^(sw)$/.test(g)) {
                    b.size.width = d.width + h;
                    b.size.height = d.height + a
                } else {
                    b.size.width = d.width + h;
                    b.size.height = d.height + a;
                    b.position.top = f.top - a
                }
                b.position.left = f.left - h
            }
        }
    });
    var m = function(b) {
        return parseInt(b, 10) || 0
    }
      , l = function(b) {
        return !isNaN(parseInt(b, 10))
    }
}
)(jQuery);
;/*
 * jQuery UI Button 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Button
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 */
(function(a) {
    var g, i = function(b) {
        a(":ui-button", b.target.form).each(function() {
            var c = a(this).data("button");
            setTimeout(function() {
                c.refresh()
            }, 1)
        })
    }, h = function(b) {
        var c = b.name
          , d = b.form
          , f = a([]);
        if (c)
            f = d ? a(d).find("[name='" + c + "']") : a("[name='" + c + "']", b.ownerDocument).filter(function() {
                return !this.form
            });
        return f
    };
    a.widget("ui.button", {
        options: {
            disabled: null,
            text: true,
            label: null,
            icons: {
                primary: null,
                secondary: null
            }
        },
        _create: function() {
            this.element.closest("form").unbind("reset.button").bind("reset.button", i);
            if (typeof this.options.disabled !== "boolean")
                this.options.disabled = this.element.attr("disabled");
            this._determineButtonType();
            this.hasTitle = !!this.buttonElement.attr("title");
            var b = this
              , c = this.options
              , d = this.type === "checkbox" || this.type === "radio"
              , f = "ui-state-hover" + (!d ? " ui-state-active" : "");
            if (c.label === null)
                c.label = this.buttonElement.html();
            if (this.element.is(":disabled"))
                c.disabled = true;
            this.buttonElement.addClass("ui-button ui-widget ui-state-default ui-corner-all").attr("role", "button").bind("mouseenter.button", function() {
                if (!c.disabled) {
                    a(this).addClass("ui-state-hover");
                    this === g && a(this).addClass("ui-state-active")
                }
            }).bind("mouseleave.button", function() {
                c.disabled || a(this).removeClass(f)
            }).bind("focus.button", function() {
                a(this).addClass("ui-state-focus")
            }).bind("blur.button", function() {
                a(this).removeClass("ui-state-focus")
            });
            d && this.element.bind("change.button", function() {
                b.refresh()
            });
            if (this.type === "checkbox")
                this.buttonElement.bind("click.button", function() {
                    if (c.disabled)
                        return false;
                    a(this).toggleClass("ui-state-active");
                    b.buttonElement.attr("aria-pressed", b.element[0].checked)
                });
            else if (this.type === "radio")
                this.buttonElement.bind("click.button", function() {
                    if (c.disabled)
                        return false;
                    a(this).addClass("ui-state-active");
                    b.buttonElement.attr("aria-pressed", true);
                    var e = b.element[0];
                    h(e).not(e).map(function() {
                        return a(this).button("widget")[0]
                    }).removeClass("ui-state-active").attr("aria-pressed", false)
                });
            else {
                this.buttonElement.bind("mousedown.button", function() {
                    if (c.disabled)
                        return false;
                    a(this).addClass("ui-state-active");
                    g = this;
                    a(document).one("mouseup", function() {
                        g = null
                    })
                }).bind("mouseup.button", function() {
                    if (c.disabled)
                        return false;
                    a(this).removeClass("ui-state-active")
                }).bind("keydown.button", function(e) {
                    if (c.disabled)
                        return false;
                    if (e.keyCode == a.ui.keyCode.SPACE || e.keyCode == a.ui.keyCode.ENTER)
                        a(this).addClass("ui-state-active")
                }).bind("keyup.button", function() {
                    a(this).removeClass("ui-state-active")
                });
                this.buttonElement.is("a") && this.buttonElement.keyup(function(e) {
                    e.keyCode === a.ui.keyCode.SPACE && a(this).click()
                })
            }
            this._setOption("disabled", c.disabled)
        },
        _determineButtonType: function() {
            this.type = this.element.is(":checkbox") ? "checkbox" : this.element.is(":radio") ? "radio" : this.element.is("input") ? "input" : "button";
            if (this.type === "checkbox" || this.type === "radio") {
                var b = this.element.parents().filter(":last")
                  , c = "label[for=" + this.element.attr("id") + "]";
                this.buttonElement = b.find(c);
                if (!this.buttonElement.length) {
                    b = b.length ? b.siblings() : this.element.siblings();
                    this.buttonElement = b.filter(c);
                    if (!this.buttonElement.length)
                        this.buttonElement = b.find(c)
                }
                this.element.addClass("ui-helper-hidden-accessible");
                (b = this.element.is(":checked")) && this.buttonElement.addClass("ui-state-active");
                this.buttonElement.attr("aria-pressed", b)
            } else
                this.buttonElement = this.element
        },
        widget: function() {
            return this.buttonElement
        },
        destroy: function() {
            this.element.removeClass("ui-helper-hidden-accessible");
            this.buttonElement.removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-state-hover ui-state-active  ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only").removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html());
            this.hasTitle || this.buttonElement.removeAttr("title");
            a.Widget.prototype.destroy.call(this)
        },
        _setOption: function(b, c) {
            a.Widget.prototype._setOption.apply(this, arguments);
            if (b === "disabled")
                c ? this.element.attr("disabled", true) : this.element.removeAttr("disabled");
            this._resetButton()
        },
        refresh: function() {
            var b = this.element.is(":disabled");
            b !== this.options.disabled && this._setOption("disabled", b);
            if (this.type === "radio")
                h(this.element[0]).each(function() {
                    a(this).is(":checked") ? a(this).button("widget").addClass("ui-state-active").attr("aria-pressed", true) : a(this).button("widget").removeClass("ui-state-active").attr("aria-pressed", false)
                });
            else if (this.type === "checkbox")
                this.element.is(":checked") ? this.buttonElement.addClass("ui-state-active").attr("aria-pressed", true) : this.buttonElement.removeClass("ui-state-active").attr("aria-pressed", false)
        },
        _resetButton: function() {
            if (this.type === "input")
                this.options.label && this.element.val(this.options.label);
            else {
                var b = this.buttonElement.removeClass("ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only")
                  , c = a("<span></span>").addClass("ui-button-text").html(this.options.label).appendTo(b.empty()).text()
                  , d = this.options.icons
                  , f = d.primary && d.secondary
                  , e = [];
                if (d.primary || d.secondary) {
                    if (this.options.text)
                        e.push("ui-button-text-icon" + (f ? "s" : d.primary ? "-primary" : "-secondary"));
                    d.primary && b.prepend("<span class='ui-button-icon-primary ui-icon " + d.primary + "'></span>");
                    d.secondary && b.append("<span class='ui-button-icon-secondary ui-icon " + d.secondary + "'></span>");
                    if (!this.options.text) {
                        e.push(f ? "ui-button-icons-only" : "ui-button-icon-only");
                        this.hasTitle || b.attr("title", c)
                    }
                } else
                    e.push("ui-button-text-only");
                b.addClass(e.join(" "))
            }
        }
    });
    a.widget("ui.buttonset", {
        options: {
            items: ":button, :submit, :reset, :checkbox, :radio, a, :data(button)"
        },
        _create: function() {
            this.element.addClass("ui-buttonset")
        },
        _init: function() {
            this.refresh()
        },
        _setOption: function(b, c) {
            b === "disabled" && this.buttons.button("option", b, c);
            a.Widget.prototype._setOption.apply(this, arguments)
        },
        refresh: function() {
            this.buttons = this.element.find(this.options.items).filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function() {
                return a(this).button("widget")[0]
            }).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass("ui-corner-left").end().filter(":last").addClass("ui-corner-right").end().end()
        },
        destroy: function() {
            this.element.removeClass("ui-buttonset");
            this.buttons.map(function() {
                return a(this).button("widget")[0]
            }).removeClass("ui-corner-left ui-corner-right").end().button("destroy");
            a.Widget.prototype.destroy.call(this)
        }
    })
}
)(jQuery);
;/*
 * jQuery UI Dialog 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Dialog
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.widget.js
 *  jquery.ui.button.js
 *	jquery.ui.draggable.js
 *	jquery.ui.mouse.js
 *	jquery.ui.position.js
 *	jquery.ui.resizable.js
 */
(function(c, j) {
    var k = {
        buttons: true,
        height: true,
        maxHeight: true,
        maxWidth: true,
        minHeight: true,
        minWidth: true,
        width: true
    }
      , l = {
        maxHeight: true,
        maxWidth: true,
        minHeight: true,
        minWidth: true
    };
    c.widget("ui.dialog", {
        options: {
            autoOpen: true,
            buttons: {},
            closeOnEscape: true,
            closeText: "close",
            dialogClass: "",
            draggable: true,
            hide: null,
            height: "auto",
            maxHeight: false,
            maxWidth: false,
            minHeight: 150,
            minWidth: 150,
            modal: false,
            position: {
                my: "center",
                at: "center",
                collision: "fit",
                using: function(a) {
                    var b = c(this).css(a).offset().top;
                    b < 0 && c(this).css("top", a.top - b)
                }
            },
            resizable: true,
            show: null,
            stack: true,
            title: "",
            width: 300,
            zIndex: 1E3
        },
        _create: function() {
            this.originalTitle = this.element.attr("title");
            if (typeof this.originalTitle !== "string")
                this.originalTitle = "";
            this.options.title = this.options.title || this.originalTitle;
            var a = this
              , b = a.options
              , d = b.title || "&#160;"
              , e = c.ui.dialog.getTitleId(a.element)
              , g = (a.uiDialog = c("<div></div>")).appendTo(document.body).hide().addClass("ui-dialog ui-widget ui-widget-content ui-corner-all " + b.dialogClass).css({
                zIndex: b.zIndex
            }).attr("tabIndex", -1).css("outline", 0).keydown(function(i) {
                if (b.closeOnEscape && i.keyCode && i.keyCode === c.ui.keyCode.ESCAPE) {
                    a.close(i);
                    i.preventDefault()
                }
            }).attr({
                role: "dialog",
                "aria-labelledby": e
            }).mousedown(function(i) {
                a.moveToTop(false, i)
            });
            a.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(g);
            var f = (a.uiDialogTitlebar = c("<div></div>")).addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(g)
              , h = c('<a href="#"></a>').addClass("ui-dialog-titlebar-close ui-corner-all").attr("role", "button").hover(function() {
                h.addClass("ui-state-hover")
            }, function() {
                h.removeClass("ui-state-hover")
            }).focus(function() {
                h.addClass("ui-state-focus")
            }).blur(function() {
                h.removeClass("ui-state-focus")
            }).click(function(i) {
                a.close(i);
                return false
            }).appendTo(f);
            (a.uiDialogTitlebarCloseText = c("<span></span>")).addClass("ui-icon ui-icon-closethick").text(b.closeText).appendTo(h);
            c("<span></span>").addClass("ui-dialog-title").attr("id", e).html(d).prependTo(f);
            if (c.isFunction(b.beforeclose) && !c.isFunction(b.beforeClose))
                b.beforeClose = b.beforeclose;
            f.find("*").add(f).disableSelection();
            b.draggable && c.fn.draggable && a._makeDraggable();
            b.resizable && c.fn.resizable && a._makeResizable();
            a._createButtons(b.buttons);
            a._isOpen = false;
            c.fn.bgiframe && g.bgiframe()
        },
        _init: function() {
            this.options.autoOpen && this.open()
        },
        destroy: function() {
            var a = this;
            a.overlay && a.overlay.destroy();
            a.uiDialog.hide();
            a.element.unbind(".dialog").removeData("dialog").removeClass("ui-dialog-content ui-widget-content").hide().appendTo("body");
            a.uiDialog.remove();
            a.originalTitle && a.element.attr("title", a.originalTitle);
            return a
        },
        widget: function() {
            return this.uiDialog
        },
        close: function(a) {
            var b = this, d, e;
            if (false !== b._trigger("beforeClose", a)) {
                b.overlay && b.overlay.destroy();
                b.uiDialog.unbind("keypress.ui-dialog");
                b._isOpen = false;
                if (b.options.hide)
                    b.uiDialog.hide(b.options.hide, function() {
                        b._trigger("close", a)
                    });
                else {
                    b.uiDialog.hide();
                    b._trigger("close", a)
                }
                c.ui.dialog.overlay.resize();
                if (b.options.modal) {
                    d = 0;
                    c(".ui-dialog").each(function() {
                        if (this !== b.uiDialog[0]) {
                            e = c(this).css("z-index");
                            isNaN(e) || (d = Math.max(d, e))
                        }
                    });
                    c.ui.dialog.maxZ = d
                }
                return b
            }
        },
        isOpen: function() {
            return this._isOpen
        },
        moveToTop: function(a, b) {
            var d = this
              , e = d.options;
            if (e.modal && !a || !e.stack && !e.modal)
                return d._trigger("focus", b);
            if (e.zIndex > c.ui.dialog.maxZ)
                c.ui.dialog.maxZ = e.zIndex;
            if (d.overlay) {
                c.ui.dialog.maxZ += 1;
                d.overlay.$el.css("z-index", c.ui.dialog.overlay.maxZ = c.ui.dialog.maxZ)
            }
            a = {
                scrollTop: d.element.attr("scrollTop"),
                scrollLeft: d.element.attr("scrollLeft")
            };
            c.ui.dialog.maxZ += 1;
            d.uiDialog.css("z-index", c.ui.dialog.maxZ);
            d.element.attr(a);
            d._trigger("focus", b);
            return d
        },
        open: function() {
            if (!this._isOpen) {
                var a = this
                  , b = a.options
                  , d = a.uiDialog;
                a.overlay = b.modal ? new c.ui.dialog.overlay(a) : null;
                a._size();
                a._position(b.position);
                d.show(b.show);
                a.moveToTop(true);
                b.modal && d.bind("keypress.ui-dialog", function(e) {
                    if (e.keyCode === c.ui.keyCode.TAB) {
                        var g = c(":tabbable", this)
                          , f = g.filter(":first");
                        g = g.filter(":last");
                        if (e.target === g[0] && !e.shiftKey) {
                            f.focus(1);
                            return false
                        } else if (e.target === f[0] && e.shiftKey) {
                            g.focus(1);
                            return false
                        }
                    }
                });
                c(a.element.find(":tabbable").get().concat(d.find(".ui-dialog-buttonpane :tabbable").get().concat(d.get()))).eq(0).focus();
                a._isOpen = true;
                a._trigger("open");
                return a
            }
        },
        _createButtons: function(a) {
            var b = this
              , d = false
              , e = c("<div></div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix")
              , g = c("<div></div>").addClass("ui-dialog-buttonset").appendTo(e);
            b.uiDialog.find(".ui-dialog-buttonpane").remove();
            typeof a === "object" && a !== null && c.each(a, function() {
                return !(d = true)
            });
            if (d) {
                c.each(a, function(f, h) {
                    h = c.isFunction(h) ? {
                        click: h,
                        text: f
                    } : h;
                    f = c('<button type="button"></button>').attr(h, true).unbind("click").click(function() {
                        h.click.apply(b.element[0], arguments)
                    }).appendTo(g);
                    c.fn.button && f.button()
                });
                e.appendTo(b.uiDialog)
            }
        },
        _makeDraggable: function() {
            function a(f) {
                return {
                    position: f.position,
                    offset: f.offset
                }
            }
            var b = this, d = b.options, e = c(document), g;
            b.uiDialog.draggable({
                cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
                handle: ".ui-dialog-titlebar",
                containment: "document",
                start: function(f, h) {
                    g = d.height === "auto" ? "auto" : c(this).height();
                    c(this).height(c(this).height()).addClass("ui-dialog-dragging");
                    b._trigger("dragStart", f, a(h))
                },
                drag: function(f, h) {
                    b._trigger("drag", f, a(h))
                },
                stop: function(f, h) {
                    d.position = [h.position.left - e.scrollLeft(), h.position.top - e.scrollTop()];
                    c(this).removeClass("ui-dialog-dragging").height(g);
                    b._trigger("dragStop", f, a(h));
                    c.ui.dialog.overlay.resize()
                }
            })
        },
        _makeResizable: function(a) {
            function b(f) {
                return {
                    originalPosition: f.originalPosition,
                    originalSize: f.originalSize,
                    position: f.position,
                    size: f.size
                }
            }
            a = a === j ? this.options.resizable : a;
            var d = this
              , e = d.options
              , g = d.uiDialog.css("position");
            a = typeof a === "string" ? a : "n,e,s,w,se,sw,ne,nw";
            d.uiDialog.resizable({
                cancel: ".ui-dialog-content",
                containment: "document",
                alsoResize: d.element,
                maxWidth: e.maxWidth,
                maxHeight: e.maxHeight,
                minWidth: e.minWidth,
                minHeight: d._minHeight(),
                handles: a,
                start: function(f, h) {
                    c(this).addClass("ui-dialog-resizing");
                    d._trigger("resizeStart", f, b(h))
                },
                resize: function(f, h) {
                    d._trigger("resize", f, b(h))
                },
                stop: function(f, h) {
                    c(this).removeClass("ui-dialog-resizing");
                    e.height = c(this).height();
                    e.width = c(this).width();
                    d._trigger("resizeStop", f, b(h));
                    c.ui.dialog.overlay.resize()
                }
            }).css("position", g).find(".ui-resizable-se").addClass("ui-icon ui-icon-grip-diagonal-se")
        },
        _minHeight: function() {
            var a = this.options;
            return a.height === "auto" ? a.minHeight : Math.min(a.minHeight, a.height)
        },
        _position: function(a) {
            var b = [], d = [0, 0], e;
            if (a) {
                if (typeof a === "string" || typeof a === "object" && "0"in a) {
                    b = a.split ? a.split(" ") : [a[0], a[1]];
                    if (b.length === 1)
                        b[1] = b[0];
                    c.each(["left", "top"], function(g, f) {
                        if (+b[g] === b[g]) {
                            d[g] = b[g];
                            b[g] = f
                        }
                    });
                    a = {
                        my: b.join(" "),
                        at: b.join(" "),
                        offset: d.join(" ")
                    }
                }
                a = c.extend({}, c.ui.dialog.prototype.options.position, a)
            } else
                a = c.ui.dialog.prototype.options.position;
            (e = this.uiDialog.is(":visible")) || this.uiDialog.show();
            this.uiDialog.css({
                top: 0,
                left: 0
            }).position(c.extend({
                of: window
            }, a));
            e || this.uiDialog.hide()
        },
        _setOptions: function(a) {
            var b = this
              , d = {}
              , e = false;
            c.each(a, function(g, f) {
                b._setOption(g, f);
                if (g in k)
                    e = true;
                if (g in l)
                    d[g] = f
            });
            e && this._size();
            this.uiDialog.is(":data(resizable)") && this.uiDialog.resizable("option", d)
        },
        _setOption: function(a, b) {
            var d = this
              , e = d.uiDialog;
            switch (a) {
            case "beforeclose":
                a = "beforeClose";
                break;
            case "buttons":
                d._createButtons(b);
                break;
            case "closeText":
                d.uiDialogTitlebarCloseText.text("" + b);
                break;
            case "dialogClass":
                e.removeClass(d.options.dialogClass).addClass("ui-dialog ui-widget ui-widget-content ui-corner-all " + b);
                break;
            case "disabled":
                b ? e.addClass("ui-dialog-disabled") : e.removeClass("ui-dialog-disabled");
                break;
            case "draggable":
                var g = e.is(":data(draggable)");
                g && !b && e.draggable("destroy");
                !g && b && d._makeDraggable();
                break;
            case "position":
                d._position(b);
                break;
            case "resizable":
                (g = e.is(":data(resizable)")) && !b && e.resizable("destroy");
                g && typeof b === "string" && e.resizable("option", "handles", b);
                !g && b !== false && d._makeResizable(b);
                break;
            case "title":
                c(".ui-dialog-title", d.uiDialogTitlebar).html("" + (b || "&#160;"));
                break
            }
            c.Widget.prototype._setOption.apply(d, arguments)
        },
        _size: function() {
            var a = this.options, b, d, e = this.uiDialog.is(":visible");
            this.element.show().css({
                width: "auto",
                minHeight: 0,
                height: 0
            });
            if (a.minWidth > a.width)
                a.width = a.minWidth;
            b = this.uiDialog.css({
                height: "auto",
                width: a.width
            }).height();
            d = Math.max(0, a.minHeight - b);
            if (a.height === "auto")
                if (c.support.minHeight)
                    this.element.css({
                        minHeight: d,
                        height: "auto"
                    });
                else {
                    this.uiDialog.show();
                    a = this.element.css("height", "auto").height();
                    e || this.uiDialog.hide();
                    this.element.height(Math.max(a, d))
                }
            else
                this.element.height(Math.max(a.height - b, 0));
            this.uiDialog.is(":data(resizable)") && this.uiDialog.resizable("option", "minHeight", this._minHeight())
        }
    });
    c.extend(c.ui.dialog, {
        version: "1.8.11",
        uuid: 0,
        maxZ: 0,
        getTitleId: function(a) {
            a = a.attr("id");
            if (!a) {
                this.uuid += 1;
                a = this.uuid
            }
            return "ui-dialog-title-" + a
        },
        overlay: function(a) {
            this.$el = c.ui.dialog.overlay.create(a)
        }
    });
    c.extend(c.ui.dialog.overlay, {
        instances: [],
        oldInstances: [],
        maxZ: 0,
        events: c.map("focus,mousedown,mouseup,keydown,keypress,click".split(","), function(a) {
            return a + ".dialog-overlay"
        }).join(" "),
        create: function(a) {
            if (this.instances.length === 0) {
                setTimeout(function() {
                    c.ui.dialog.overlay.instances.length && c(document).bind(c.ui.dialog.overlay.events, function(d) {
                        if (c(d.target).zIndex() < c.ui.dialog.overlay.maxZ)
                            return false
                    })
                }, 1);
                c(document).bind("keydown.dialog-overlay", function(d) {
                    if (a.options.closeOnEscape && d.keyCode && d.keyCode === c.ui.keyCode.ESCAPE) {
                        a.close(d);
                        d.preventDefault()
                    }
                });
                c(window).bind("resize.dialog-overlay", c.ui.dialog.overlay.resize)
            }
            var b = (this.oldInstances.pop() || c("<div></div>").addClass("ui-widget-overlay")).appendTo(document.body).css({
                width: this.width(),
                height: this.height()
            });
            c.fn.bgiframe && b.bgiframe();
            this.instances.push(b);
            return b
        },
        destroy: function(a) {
            var b = c.inArray(a, this.instances);
            b != -1 && this.oldInstances.push(this.instances.splice(b, 1)[0]);
            this.instances.length === 0 && c([document, window]).unbind(".dialog-overlay");
            a.remove();
            var d = 0;
            c.each(this.instances, function() {
                d = Math.max(d, this.css("z-index"))
            });
            this.maxZ = d
        },
        height: function() {
            var a, b;
            if (c.browser.msie && c.browser.version < 7) {
                a = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
                b = Math.max(document.documentElement.offsetHeight, document.body.offsetHeight);
                return a < b ? c(window).height() + "px" : a + "px"
            } else
                return c(document).height() + "px"
        },
        width: function() {
            var a, b;
            if (c.browser.msie && c.browser.version < 7) {
                a = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth);
                b = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);
                return a < b ? c(window).width() + "px" : a + "px"
            } else
                return c(document).width() + "px"
        },
        resize: function() {
            var a = c([]);
            c.each(c.ui.dialog.overlay.instances, function() {
                a = a.add(this)
            });
            a.css({
                width: 0,
                height: 0
            }).css({
                width: c.ui.dialog.overlay.width(),
                height: c.ui.dialog.overlay.height()
            })
        }
    });
    c.extend(c.ui.dialog.overlay.prototype, {
        destroy: function() {
            c.ui.dialog.overlay.destroy(this.$el)
        }
    })
}
)(jQuery);
;/*
 * jQuery UI Slider 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Slider
 *
 * Depends:
 *	jquery.ui.core.js
 *	jquery.ui.mouse.js
 *	jquery.ui.widget.js
 */
(function(d) {
    d.widget("ui.slider", d.ui.mouse, {
        widgetEventPrefix: "slide",
        options: {
            animate: false,
            distance: 0,
            max: 100,
            min: 0,
            orientation: "horizontal",
            range: false,
            step: 1,
            value: 0,
            values: null
        },
        _create: function() {
            var b = this
              , a = this.options;
            this._mouseSliding = this._keySliding = false;
            this._animateOff = true;
            this._handleIndex = null;
            this._detectOrientation();
            this._mouseInit();
            this.element.addClass("ui-slider ui-slider-" + this.orientation + " ui-widget ui-widget-content ui-corner-all");
            a.disabled && this.element.addClass("ui-slider-disabled ui-disabled");
            this.range = d([]);
            if (a.range) {
                if (a.range === true) {
                    this.range = d("<div></div>");
                    if (!a.values)
                        a.values = [this._valueMin(), this._valueMin()];
                    if (a.values.length && a.values.length !== 2)
                        a.values = [a.values[0], a.values[0]]
                } else
                    this.range = d("<div></div>");
                this.range.appendTo(this.element).addClass("ui-slider-range");
                if (a.range === "min" || a.range === "max")
                    this.range.addClass("ui-slider-range-" + a.range);
                this.range.addClass("ui-widget-header")
            }
            d(".ui-slider-handle", this.element).length === 0 && d("<a href='#'></a>").appendTo(this.element).addClass("ui-slider-handle");
            if (a.values && a.values.length)
                for (; d(".ui-slider-handle", this.element).length < a.values.length; )
                    d("<a href='#'></a>").appendTo(this.element).addClass("ui-slider-handle");
            this.handles = d(".ui-slider-handle", this.element).addClass("ui-state-default ui-corner-all");
            this.handle = this.handles.eq(0);
            this.handles.add(this.range).filter("a").click(function(c) {
                c.preventDefault()
            }).hover(function() {
                a.disabled || d(this).addClass("ui-state-hover")
            }, function() {
                d(this).removeClass("ui-state-hover")
            }).focus(function() {
                if (a.disabled)
                    d(this).blur();
                else {
                    d(".ui-slider .ui-state-focus").removeClass("ui-state-focus");
                    d(this).addClass("ui-state-focus")
                }
            }).blur(function() {
                d(this).removeClass("ui-state-focus")
            });
            this.handles.each(function(c) {
                d(this).data("index.ui-slider-handle", c)
            });
            this.handles.keydown(function(c) {
                var e = true, f = d(this).data("index.ui-slider-handle"), h, g, i;
                if (!b.options.disabled) {
                    switch (c.keyCode) {
                    case d.ui.keyCode.HOME:
                    case d.ui.keyCode.END:
                    case d.ui.keyCode.PAGE_UP:
                    case d.ui.keyCode.PAGE_DOWN:
                    case d.ui.keyCode.UP:
                    case d.ui.keyCode.RIGHT:
                    case d.ui.keyCode.DOWN:
                    case d.ui.keyCode.LEFT:
                        e = false;
                        if (!b._keySliding) {
                            b._keySliding = true;
                            d(this).addClass("ui-state-active");
                            h = b._start(c, f);
                            if (h === false)
                                return
                        }
                        break
                    }
                    i = b.options.step;
                    h = b.options.values && b.options.values.length ? (g = b.values(f)) : (g = b.value());
                    switch (c.keyCode) {
                    case d.ui.keyCode.HOME:
                        g = b._valueMin();
                        break;
                    case d.ui.keyCode.END:
                        g = b._valueMax();
                        break;
                    case d.ui.keyCode.PAGE_UP:
                        g = b._trimAlignValue(h + (b._valueMax() - b._valueMin()) / 5);
                        break;
                    case d.ui.keyCode.PAGE_DOWN:
                        g = b._trimAlignValue(h - (b._valueMax() - b._valueMin()) / 5);
                        break;
                    case d.ui.keyCode.UP:
                    case d.ui.keyCode.RIGHT:
                        if (h === b._valueMax())
                            return;
                        g = b._trimAlignValue(h + i);
                        break;
                    case d.ui.keyCode.DOWN:
                    case d.ui.keyCode.LEFT:
                        if (h === b._valueMin())
                            return;
                        g = b._trimAlignValue(h - i);
                        break
                    }
                    b._slide(c, f, g);
                    return e
                }
            }).keyup(function(c) {
                var e = d(this).data("index.ui-slider-handle");
                if (b._keySliding) {
                    b._keySliding = false;
                    b._stop(c, e);
                    b._change(c, e);
                    d(this).removeClass("ui-state-active")
                }
            });
            this._refreshValue();
            this._animateOff = false
        },
        destroy: function() {
            this.handles.remove();
            this.range.remove();
            this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-slider-disabled ui-widget ui-widget-content ui-corner-all").removeData("slider").unbind(".slider");
            this._mouseDestroy();
            return this
        },
        _mouseCapture: function(b) {
            var a = this.options, c, e, f, h, g;
            if (a.disabled)
                return false;
            this.elementSize = {
                width: this.element.outerWidth(),
                height: this.element.outerHeight()
            };
            this.elementOffset = this.element.offset();
            c = this._normValueFromMouse({
                x: b.pageX,
                y: b.pageY
            });
            e = this._valueMax() - this._valueMin() + 1;
            h = this;
            this.handles.each(function(i) {
                var j = Math.abs(c - h.values(i));
                if (e > j) {
                    e = j;
                    f = d(this);
                    g = i
                }
            });
            if (a.range === true && this.values(1) === a.min) {
                g += 1;
                f = d(this.handles[g])
            }
            if (this._start(b, g) === false)
                return false;
            this._mouseSliding = true;
            h._handleIndex = g;
            f.addClass("ui-state-active").focus();
            a = f.offset();
            this._clickOffset = !d(b.target).parents().addBack().is(".ui-slider-handle") ? {
                left: 0,
                top: 0
            } : {
                left: b.pageX - a.left - f.width() / 2,
                top: b.pageY - a.top - f.height() / 2 - (parseInt(f.css("borderTopWidth"), 10) || 0) - (parseInt(f.css("borderBottomWidth"), 10) || 0) + (parseInt(f.css("marginTop"), 10) || 0)
            };
            this.handles.hasClass("ui-state-hover") || this._slide(b, g, c);
            return this._animateOff = true
        },
        _mouseStart: function() {
            return true
        },
        _mouseDrag: function(b) {
            var a = this._normValueFromMouse({
                x: b.pageX,
                y: b.pageY
            });
            this._slide(b, this._handleIndex, a);
            return false
        },
        _mouseStop: function(b) {
            this.handles.removeClass("ui-state-active");
            this._mouseSliding = false;
            this._stop(b, this._handleIndex);
            this._change(b, this._handleIndex);
            this._clickOffset = this._handleIndex = null;
            return this._animateOff = false
        },
        _detectOrientation: function() {
            this.orientation = this.options.orientation === "vertical" ? "vertical" : "horizontal"
        },
        _normValueFromMouse: function(b) {
            var a;
            if (this.orientation === "horizontal") {
                a = this.elementSize.width;
                b = b.x - this.elementOffset.left - (this._clickOffset ? this._clickOffset.left : 0)
            } else {
                a = this.elementSize.height;
                b = b.y - this.elementOffset.top - (this._clickOffset ? this._clickOffset.top : 0)
            }
            a = b / a;
            if (a > 1)
                a = 1;
            if (a < 0)
                a = 0;
            if (this.orientation === "vertical")
                a = 1 - a;
            b = this._valueMax() - this._valueMin();
            return this._trimAlignValue(this._valueMin() + a * b)
        },
        _start: function(b, a) {
            var c = {
                handle: this.handles[a],
                value: this.value()
            };
            if (this.options.values && this.options.values.length) {
                c.value = this.values(a);
                c.values = this.values()
            }
            return this._trigger("start", b, c)
        },
        _slide: function(b, a, c) {
            var e;
            if (this.options.values && this.options.values.length) {
                e = this.values(a ? 0 : 1);
                if (this.options.values.length === 2 && this.options.range === true && (a === 0 && c > e || a === 1 && c < e))
                    c = e;
                if (c !== this.values(a)) {
                    e = this.values();
                    e[a] = c;
                    b = this._trigger("slide", b, {
                        handle: this.handles[a],
                        value: c,
                        values: e
                    });
                    this.values(a ? 0 : 1);
                    b !== false && this.values(a, c, true)
                }
            } else if (c !== this.value()) {
                b = this._trigger("slide", b, {
                    handle: this.handles[a],
                    value: c
                });
                b !== false && this.value(c)
            }
        },
        _stop: function(b, a) {
            var c = {
                handle: this.handles[a],
                value: this.value()
            };
            if (this.options.values && this.options.values.length) {
                c.value = this.values(a);
                c.values = this.values()
            }
            this._trigger("stop", b, c)
        },
        _change: function(b, a) {
            if (!this._keySliding && !this._mouseSliding) {
                var c = {
                    handle: this.handles[a],
                    value: this.value()
                };
                if (this.options.values && this.options.values.length) {
                    c.value = this.values(a);
                    c.values = this.values()
                }
                this._trigger("change", b, c)
            }
        },
        value: function(b) {
            if (arguments.length) {
                this.options.value = this._trimAlignValue(b);
                this._refreshValue();
                this._change(null, 0)
            }
            return this._value()
        },
        values: function(b, a) {
            var c, e, f;
            if (arguments.length > 1) {
                this.options.values[b] = this._trimAlignValue(a);
                this._refreshValue();
                this._change(null, b)
            }
            if (arguments.length)
                if (d.isArray(arguments[0])) {
                    c = this.options.values;
                    e = arguments[0];
                    for (f = 0; f < c.length; f += 1) {
                        c[f] = this._trimAlignValue(e[f]);
                        this._change(null, f)
                    }
                    this._refreshValue()
                } else
                    return this.options.values && this.options.values.length ? this._values(b) : this.value();
            else
                return this._values()
        },
        _setOption: function(b, a) {
            var c, e = 0;
            if (d.isArray(this.options.values))
                e = this.options.values.length;
            d.Widget.prototype._setOption.apply(this, arguments);
            switch (b) {
            case "disabled":
                if (a) {
                    this.handles.filter(".ui-state-focus").blur();
                    this.handles.removeClass("ui-state-hover");
                    this.handles.attr("disabled", "disabled");
                    this.element.addClass("ui-disabled")
                } else {
                    this.handles.removeAttr("disabled");
                    this.element.removeClass("ui-disabled")
                }
                break;
            case "orientation":
                this._detectOrientation();
                this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-" + this.orientation);
                this._refreshValue();
                break;
            case "value":
                this._animateOff = true;
                this._refreshValue();
                this._change(null, 0);
                this._animateOff = false;
                break;
            case "values":
                this._animateOff = true;
                this._refreshValue();
                for (c = 0; c < e; c += 1)
                    this._change(null, c);
                this._animateOff = false;
                break
            }
        },
        _value: function() {
            var b = this.options.value;
            return b = this._trimAlignValue(b)
        },
        _values: function(b) {
            var a, c;
            if (arguments.length) {
                a = this.options.values[b];
                return a = this._trimAlignValue(a)
            } else {
                a = this.options.values.slice();
                for (c = 0; c < a.length; c += 1)
                    a[c] = this._trimAlignValue(a[c]);
                return a
            }
        },
        _trimAlignValue: function(b) {
            if (b <= this._valueMin())
                return this._valueMin();
            if (b >= this._valueMax())
                return this._valueMax();
            var a = this.options.step > 0 ? this.options.step : 1
              , c = (b - this._valueMin()) % a;
            alignValue = b - c;
            if (Math.abs(c) * 2 >= a)
                alignValue += c > 0 ? a : -a;
            return parseFloat(alignValue.toFixed(5))
        },
        _valueMin: function() {
            return this.options.min
        },
        _valueMax: function() {
            return this.options.max
        },
        _refreshValue: function() {
            var b = this.options.range, a = this.options, c = this, e = !this._animateOff ? a.animate : false, f, h = {}, g, i, j, l;
            if (this.options.values && this.options.values.length)
                this.handles.each(function(k) {
                    f = (c.values(k) - c._valueMin()) / (c._valueMax() - c._valueMin()) * 100;
                    h[c.orientation === "horizontal" ? "left" : "bottom"] = f + "%";
                    d(this).stop(1, 1)[e ? "animate" : "css"](h, a.animate);
                    if (c.options.range === true)
                        if (c.orientation === "horizontal") {
                            if (k === 0)
                                c.range.stop(1, 1)[e ? "animate" : "css"]({
                                    left: f + "%"
                                }, a.animate);
                            if (k === 1)
                                c.range[e ? "animate" : "css"]({
                                    width: f - g + "%"
                                }, {
                                    queue: false,
                                    duration: a.animate
                                })
                        } else {
                            if (k === 0)
                                c.range.stop(1, 1)[e ? "animate" : "css"]({
                                    bottom: f + "%"
                                }, a.animate);
                            if (k === 1)
                                c.range[e ? "animate" : "css"]({
                                    height: f - g + "%"
                                }, {
                                    queue: false,
                                    duration: a.animate
                                })
                        }
                    g = f
                });
            else {
                i = this.value();
                j = this._valueMin();
                l = this._valueMax();
                f = l !== j ? (i - j) / (l - j) * 100 : 0;
                h[c.orientation === "horizontal" ? "left" : "bottom"] = f + "%";
                this.handle.stop(1, 1)[e ? "animate" : "css"](h, a.animate);
                if (b === "min" && this.orientation === "horizontal")
                    this.range.stop(1, 1)[e ? "animate" : "css"]({
                        width: f + "%"
                    }, a.animate);
                if (b === "max" && this.orientation === "horizontal")
                    this.range[e ? "animate" : "css"]({
                        width: 100 - f + "%"
                    }, {
                        queue: false,
                        duration: a.animate
                    });
                if (b === "min" && this.orientation === "vertical")
                    this.range.stop(1, 1)[e ? "animate" : "css"]({
                        height: f + "%"
                    }, a.animate);
                if (b === "max" && this.orientation === "vertical")
                    this.range[e ? "animate" : "css"]({
                        height: 100 - f + "%"
                    }, {
                        queue: false,
                        duration: a.animate
                    })
            }
        }
    });
    d.extend(d.ui.slider, {
        version: "1.8.11"
    })
}
)(jQuery);
;/*
 * jQuery UI Effects 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Effects/
 */
jQuery.effects || function(f, j) {
    function n(c) {
        var a;
        if (c && c.constructor == Array && c.length == 3)
            return c;
        if (a = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(c))
            return [parseInt(a[1], 10), parseInt(a[2], 10), parseInt(a[3], 10)];
        if (a = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(c))
            return [parseFloat(a[1]) * 2.55, parseFloat(a[2]) * 2.55, parseFloat(a[3]) * 2.55];
        if (a = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(c))
            return [parseInt(a[1], 16), parseInt(a[2], 16), parseInt(a[3], 16)];
        if (a = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(c))
            return [parseInt(a[1] + a[1], 16), parseInt(a[2] + a[2], 16), parseInt(a[3] + a[3], 16)];
        if (/rgba\(0, 0, 0, 0\)/.exec(c))
            return o.transparent;
        return o[f.trim(c).toLowerCase()]
    }
    function s(c, a) {
        var b;
        do {
            b = f.curCSS(c, a);
            if (b != "" && b != "transparent" || f.nodeName(c, "body"))
                break;
            a = "backgroundColor"
        } while (c = c.parentNode);
        return n(b)
    }
    function p() {
        var c = document.defaultView ? document.defaultView.getComputedStyle(this, null) : this.currentStyle, a = {}, b, d;
        if (c && c.length && c[0] && c[c[0]])
            for (var e = c.length; e--; ) {
                b = c[e];
                if (typeof c[b] == "string") {
                    d = b.replace(/\-(\w)/g, function(g, h) {
                        return h.toUpperCase()
                    });
                    a[d] = c[b]
                }
            }
        else
            for (b in c)
                if (typeof c[b] === "string")
                    a[b] = c[b];
        return a
    }
    function q(c) {
        var a, b;
        for (a in c) {
            b = c[a];
            if (b == null || f.isFunction(b) || a in t || /scrollbar/.test(a) || !/color/i.test(a) && isNaN(parseFloat(b)))
                delete c[a]
        }
        return c
    }
    function u(c, a) {
        var b = {
            _: 0
        }, d;
        for (d in a)
            if (c[d] != a[d])
                b[d] = a[d];
        return b
    }
    function k(c, a, b, d) {
        if (typeof c == "object") {
            d = a;
            b = null;
            a = c;
            c = a.effect
        }
        if (f.isFunction(a)) {
            d = a;
            b = null;
            a = {}
        }
        if (typeof a == "number" || f.fx.speeds[a]) {
            d = b;
            b = a;
            a = {}
        }
        if (f.isFunction(b)) {
            d = b;
            b = null
        }
        a = a || {};
        b = b || a.duration;
        b = f.fx.off ? 0 : typeof b == "number" ? b : b in f.fx.speeds ? f.fx.speeds[b] : f.fx.speeds._default;
        d = d || a.complete;
        return [c, a, b, d]
    }
    function m(c) {
        if (!c || typeof c === "number" || f.fx.speeds[c])
            return true;
        if (typeof c === "string" && !f.effects[c])
            return true;
        return false
    }
    f.effects = {};
    f.each(["backgroundColor", "borderBottomColor", "borderLeftColor", "borderRightColor", "borderTopColor", "borderColor", "color", "outlineColor"], function(c, a) {
        f.fx.step[a] = function(b) {
            if (!b.colorInit) {
                b.start = s(b.elem, a);
                b.end = n(b.end);
                b.colorInit = true
            }
            b.elem.style[a] = "rgb(" + Math.max(Math.min(parseInt(b.pos * (b.end[0] - b.start[0]) + b.start[0], 10), 255), 0) + "," + Math.max(Math.min(parseInt(b.pos * (b.end[1] - b.start[1]) + b.start[1], 10), 255), 0) + "," + Math.max(Math.min(parseInt(b.pos * (b.end[2] - b.start[2]) + b.start[2], 10), 255), 0) + ")"
        }
    });
    var o = {
        aqua: [0, 255, 255],
        azure: [240, 255, 255],
        beige: [245, 245, 220],
        black: [0, 0, 0],
        blue: [0, 0, 255],
        brown: [165, 42, 42],
        cyan: [0, 255, 255],
        darkblue: [0, 0, 139],
        darkcyan: [0, 139, 139],
        darkgrey: [169, 169, 169],
        darkgreen: [0, 100, 0],
        darkkhaki: [189, 183, 107],
        darkmagenta: [139, 0, 139],
        darkolivegreen: [85, 107, 47],
        darkorange: [255, 140, 0],
        darkorchid: [153, 50, 204],
        darkred: [139, 0, 0],
        darksalmon: [233, 150, 122],
        darkviolet: [148, 0, 211],
        fuchsia: [255, 0, 255],
        gold: [255, 215, 0],
        green: [0, 128, 0],
        indigo: [75, 0, 130],
        khaki: [240, 230, 140],
        lightblue: [173, 216, 230],
        lightcyan: [224, 255, 255],
        lightgreen: [144, 238, 144],
        lightgrey: [211, 211, 211],
        lightpink: [255, 182, 193],
        lightyellow: [255, 255, 224],
        lime: [0, 255, 0],
        magenta: [255, 0, 255],
        maroon: [128, 0, 0],
        navy: [0, 0, 128],
        olive: [128, 128, 0],
        orange: [255, 165, 0],
        pink: [255, 192, 203],
        purple: [128, 0, 128],
        violet: [128, 0, 128],
        red: [255, 0, 0],
        silver: [192, 192, 192],
        white: [255, 255, 255],
        yellow: [255, 255, 0],
        transparent: [255, 255, 255]
    }
      , r = ["add", "remove", "toggle"]
      , t = {
        border: 1,
        borderBottom: 1,
        borderColor: 1,
        borderLeft: 1,
        borderRight: 1,
        borderTop: 1,
        borderWidth: 1,
        margin: 1,
        padding: 1
    };
    f.effects.animateClass = function(c, a, b, d) {
        if (f.isFunction(b)) {
            d = b;
            b = null
        }
        return this.queue("fx", function() {
            var e = f(this), g = e.attr("style") || " ", h = q(p.call(this)), l, v = e.attr("className");
            f.each(r, function(w, i) {
                c[i] && e[i + "Class"](c[i])
            });
            l = q(p.call(this));
            e.attr("className", v);
            e.animate(u(h, l), a, b, function() {
                f.each(r, function(w, i) {
                    c[i] && e[i + "Class"](c[i])
                });
                if (typeof e.attr("style") == "object") {
                    e.attr("style").cssText = "";
                    e.attr("style").cssText = g
                } else
                    e.attr("style", g);
                d && d.apply(this, arguments)
            });
            h = f.queue(this);
            l = h.splice(h.length - 1, 1)[0];
            h.splice(1, 0, l);
            f.dequeue(this)
        })
    }
    ;
    f.fn.extend({
        _addClass: f.fn.addClass,
        addClass: function(c, a, b, d) {
            return a ? f.effects.animateClass.apply(this, [{
                add: c
            }, a, b, d]) : this._addClass(c)
        },
        _removeClass: f.fn.removeClass,
        removeClass: function(c, a, b, d) {
            return a ? f.effects.animateClass.apply(this, [{
                remove: c
            }, a, b, d]) : this._removeClass(c)
        },
        _toggleClass: f.fn.toggleClass,
        toggleClass: function(c, a, b, d, e) {
            return typeof a == "boolean" || a === j ? b ? f.effects.animateClass.apply(this, [a ? {
                add: c
            } : {
                remove: c
            }, b, d, e]) : this._toggleClass(c, a) : f.effects.animateClass.apply(this, [{
                toggle: c
            }, a, b, d])
        },
        switchClass: function(c, a, b, d, e) {
            return f.effects.animateClass.apply(this, [{
                add: a,
                remove: c
            }, b, d, e])
        }
    });
    f.extend(f.effects, {
        version: "1.8.11",
        save: function(c, a) {
            for (var b = 0; b < a.length; b++)
                a[b] !== null && c.data("ec.storage." + a[b], c[0].style[a[b]])
        },
        restore: function(c, a) {
            for (var b = 0; b < a.length; b++)
                a[b] !== null && c.css(a[b], c.data("ec.storage." + a[b]))
        },
        setMode: function(c, a) {
            if (a == "toggle")
                a = c.is(":hidden") ? "show" : "hide";
            return a
        },
        getBaseline: function(c, a) {
            var b;
            switch (c[0]) {
            case "top":
                b = 0;
                break;
            case "middle":
                b = 0.5;
                break;
            case "bottom":
                b = 1;
                break;
            default:
                b = c[0] / a.height
            }
            switch (c[1]) {
            case "left":
                c = 0;
                break;
            case "center":
                c = 0.5;
                break;
            case "right":
                c = 1;
                break;
            default:
                c = c[1] / a.width
            }
            return {
                x: c,
                y: b
            }
        },
        createWrapper: function(c) {
            if (c.parent().is(".ui-effects-wrapper"))
                return c.parent();
            var a = {
                width: c.outerWidth(true),
                height: c.outerHeight(true),
                "float": c.css("float")
            }
              , b = f("<div></div>").addClass("ui-effects-wrapper").css({
                fontSize: "100%",
                background: "transparent",
                border: "none",
                margin: 0,
                padding: 0
            });
            c.wrap(b);
            b = c.parent();
            if (c.css("position") == "static") {
                b.css({
                    position: "relative"
                });
                c.css({
                    position: "relative"
                })
            } else {
                f.extend(a, {
                    position: c.css("position"),
                    zIndex: c.css("z-index")
                });
                f.each(["top", "left", "bottom", "right"], function(d, e) {
                    a[e] = c.css(e);
                    if (isNaN(parseInt(a[e], 10)))
                        a[e] = "auto"
                });
                c.css({
                    position: "relative",
                    top: 0,
                    left: 0,
                    right: "auto",
                    bottom: "auto"
                })
            }
            return b.css(a).show()
        },
        removeWrapper: function(c) {
            if (c.parent().is(".ui-effects-wrapper"))
                return c.parent().replaceWith(c);
            return c
        },
        setTransition: function(c, a, b, d) {
            d = d || {};
            f.each(a, function(e, g) {
                unit = c.cssUnit(g);
                if (unit[0] > 0)
                    d[g] = unit[0] * b + unit[1]
            });
            return d
        }
    });
    f.fn.extend({
        effect: function(c) {
            var a = k.apply(this, arguments)
              , b = {
                options: a[1],
                duration: a[2],
                callback: a[3]
            };
            a = b.options.mode;
            var d = f.effects[c];
            if (f.fx.off || !d)
                return a ? this[a](b.duration, b.callback) : this.each(function() {
                    b.callback && b.callback.call(this)
                });
            return d.call(this, b)
        },
        _show: f.fn.show,
        show: function(c) {
            if (m(c))
                return this._show.apply(this, arguments);
            else {
                var a = k.apply(this, arguments);
                a[1].mode = "show";
                return this.effect.apply(this, a)
            }
        },
        _hide: f.fn.hide,
        hide: function(c) {
            if (m(c))
                return this._hide.apply(this, arguments);
            else {
                var a = k.apply(this, arguments);
                a[1].mode = "hide";
                return this.effect.apply(this, a)
            }
        },
        __toggle: f.fn.toggle,
        toggle: function(c) {
            if (m(c) || typeof c === "boolean" || f.isFunction(c))
                return this.__toggle.apply(this, arguments);
            else {
                var a = k.apply(this, arguments);
                a[1].mode = "toggle";
                return this.effect.apply(this, a)
            }
        },
        cssUnit: function(c) {
            var a = this.css(c)
              , b = [];
            f.each(["em", "px", "%", "pt"], function(d, e) {
                if (a.indexOf(e) > 0)
                    b = [parseFloat(a), e]
            });
            return b
        }
    });
    f.easing.jswing = f.easing.swing;
    f.extend(f.easing, {
        def: "easeOutQuad",
        swing: function(c, a, b, d, e) {
            return f.easing[f.easing.def](c, a, b, d, e)
        },
        easeInQuad: function(c, a, b, d, e) {
            return d * (a /= e) * a + b
        },
        easeOutQuad: function(c, a, b, d, e) {
            return -d * (a /= e) * (a - 2) + b
        },
        easeInOutQuad: function(c, a, b, d, e) {
            if ((a /= e / 2) < 1)
                return d / 2 * a * a + b;
            return -d / 2 * (--a * (a - 2) - 1) + b
        },
        easeInCubic: function(c, a, b, d, e) {
            return d * (a /= e) * a * a + b
        },
        easeOutCubic: function(c, a, b, d, e) {
            return d * ((a = a / e - 1) * a * a + 1) + b
        },
        easeInOutCubic: function(c, a, b, d, e) {
            if ((a /= e / 2) < 1)
                return d / 2 * a * a * a + b;
            return d / 2 * ((a -= 2) * a * a + 2) + b
        },
        easeInQuart: function(c, a, b, d, e) {
            return d * (a /= e) * a * a * a + b
        },
        easeOutQuart: function(c, a, b, d, e) {
            return -d * ((a = a / e - 1) * a * a * a - 1) + b
        },
        easeInOutQuart: function(c, a, b, d, e) {
            if ((a /= e / 2) < 1)
                return d / 2 * a * a * a * a + b;
            return -d / 2 * ((a -= 2) * a * a * a - 2) + b
        },
        easeInQuint: function(c, a, b, d, e) {
            return d * (a /= e) * a * a * a * a + b
        },
        easeOutQuint: function(c, a, b, d, e) {
            return d * ((a = a / e - 1) * a * a * a * a + 1) + b
        },
        easeInOutQuint: function(c, a, b, d, e) {
            if ((a /= e / 2) < 1)
                return d / 2 * a * a * a * a * a + b;
            return d / 2 * ((a -= 2) * a * a * a * a + 2) + b
        },
        easeInSine: function(c, a, b, d, e) {
            return -d * Math.cos(a / e * (Math.PI / 2)) + d + b
        },
        easeOutSine: function(c, a, b, d, e) {
            return d * Math.sin(a / e * (Math.PI / 2)) + b
        },
        easeInOutSine: function(c, a, b, d, e) {
            return -d / 2 * (Math.cos(Math.PI * a / e) - 1) + b
        },
        easeInExpo: function(c, a, b, d, e) {
            return a == 0 ? b : d * Math.pow(2, 10 * (a / e - 1)) + b
        },
        easeOutExpo: function(c, a, b, d, e) {
            return a == e ? b + d : d * (-Math.pow(2, -10 * a / e) + 1) + b
        },
        easeInOutExpo: function(c, a, b, d, e) {
            if (a == 0)
                return b;
            if (a == e)
                return b + d;
            if ((a /= e / 2) < 1)
                return d / 2 * Math.pow(2, 10 * (a - 1)) + b;
            return d / 2 * (-Math.pow(2, -10 * --a) + 2) + b
        },
        easeInCirc: function(c, a, b, d, e) {
            return -d * (Math.sqrt(1 - (a /= e) * a) - 1) + b
        },
        easeOutCirc: function(c, a, b, d, e) {
            return d * Math.sqrt(1 - (a = a / e - 1) * a) + b
        },
        easeInOutCirc: function(c, a, b, d, e) {
            if ((a /= e / 2) < 1)
                return -d / 2 * (Math.sqrt(1 - a * a) - 1) + b;
            return d / 2 * (Math.sqrt(1 - (a -= 2) * a) + 1) + b
        },
        easeInElastic: function(c, a, b, d, e) {
            c = 1.70158;
            var g = 0
              , h = d;
            if (a == 0)
                return b;
            if ((a /= e) == 1)
                return b + d;
            g || (g = e * 0.3);
            if (h < Math.abs(d)) {
                h = d;
                c = g / 4
            } else
                c = g / (2 * Math.PI) * Math.asin(d / h);
            return -(h * Math.pow(2, 10 * (a -= 1)) * Math.sin((a * e - c) * 2 * Math.PI / g)) + b
        },
        easeOutElastic: function(c, a, b, d, e) {
            c = 1.70158;
            var g = 0
              , h = d;
            if (a == 0)
                return b;
            if ((a /= e) == 1)
                return b + d;
            g || (g = e * 0.3);
            if (h < Math.abs(d)) {
                h = d;
                c = g / 4
            } else
                c = g / (2 * Math.PI) * Math.asin(d / h);
            return h * Math.pow(2, -10 * a) * Math.sin((a * e - c) * 2 * Math.PI / g) + d + b
        },
        easeInOutElastic: function(c, a, b, d, e) {
            c = 1.70158;
            var g = 0
              , h = d;
            if (a == 0)
                return b;
            if ((a /= e / 2) == 2)
                return b + d;
            g || (g = e * 0.3 * 1.5);
            if (h < Math.abs(d)) {
                h = d;
                c = g / 4
            } else
                c = g / (2 * Math.PI) * Math.asin(d / h);
            if (a < 1)
                return -0.5 * h * Math.pow(2, 10 * (a -= 1)) * Math.sin((a * e - c) * 2 * Math.PI / g) + b;
            return h * Math.pow(2, -10 * (a -= 1)) * Math.sin((a * e - c) * 2 * Math.PI / g) * 0.5 + d + b
        },
        easeInBack: function(c, a, b, d, e, g) {
            if (g == j)
                g = 1.70158;
            return d * (a /= e) * a * ((g + 1) * a - g) + b
        },
        easeOutBack: function(c, a, b, d, e, g) {
            if (g == j)
                g = 1.70158;
            return d * ((a = a / e - 1) * a * ((g + 1) * a + g) + 1) + b
        },
        easeInOutBack: function(c, a, b, d, e, g) {
            if (g == j)
                g = 1.70158;
            if ((a /= e / 2) < 1)
                return d / 2 * a * a * (((g *= 1.525) + 1) * a - g) + b;
            return d / 2 * ((a -= 2) * a * (((g *= 1.525) + 1) * a + g) + 2) + b
        },
        easeInBounce: function(c, a, b, d, e) {
            return d - f.easing.easeOutBounce(c, e - a, 0, d, e) + b
        },
        easeOutBounce: function(c, a, b, d, e) {
            return (a /= e) < 1 / 2.75 ? d * 7.5625 * a * a + b : a < 2 / 2.75 ? d * (7.5625 * (a -= 1.5 / 2.75) * a + 0.75) + b : a < 2.5 / 2.75 ? d * (7.5625 * (a -= 2.25 / 2.75) * a + 0.9375) + b : d * (7.5625 * (a -= 2.625 / 2.75) * a + 0.984375) + b
        },
        easeInOutBounce: function(c, a, b, d, e) {
            if (a < e / 2)
                return f.easing.easeInBounce(c, a * 2, 0, d, e) * 0.5 + b;
            return f.easing.easeOutBounce(c, a * 2 - e, 0, d, e) * 0.5 + d * 0.5 + b
        }
    })
}(jQuery);
;/*
 * jQuery UI Effects Slide 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Effects/Slide
 *
 * Depends:
 *	jquery.effects.core.js
 */
(function(c) {
    c.effects.slide = function(d) {
        return this.queue(function() {
            var a = c(this)
              , h = ["position", "top", "bottom", "left", "right"]
              , f = c.effects.setMode(a, d.options.mode || "show")
              , b = d.options.direction || "left";
            c.effects.save(a, h);
            a.show();
            c.effects.createWrapper(a).css({
                overflow: "hidden"
            });
            var g = b == "up" || b == "down" ? "top" : "left";
            b = b == "up" || b == "left" ? "pos" : "neg";
            var e = d.options.distance || (g == "top" ? a.outerHeight({
                margin: true
            }) : a.outerWidth({
                margin: true
            }));
            if (f == "show")
                a.css(g, b == "pos" ? isNaN(e) ? "-" + e : -e : e);
            var i = {};
            i[g] = (f == "show" ? b == "pos" ? "+=" : "-=" : b == "pos" ? "-=" : "+=") + e;
            a.animate(i, {
                queue: false,
                duration: d.duration,
                easing: d.options.easing,
                complete: function() {
                    f == "hide" && a.hide();
                    c.effects.restore(a, h);
                    c.effects.removeWrapper(a);
                    d.callback && d.callback.apply(this, arguments);
                    a.dequeue()
                }
            })
        })
    }
}
)(jQuery);
;/*
 * jQuery UI Effects Transfer 1.8.11
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Effects/Transfer
 *
 * Depends:
 *	jquery.effects.core.js
 */
(function(e) {
    e.effects.transfer = function(a) {
        return this.queue(function() {
            var b = e(this)
              , c = e(a.options.to)
              , d = c.offset();
            c = {
                top: d.top,
                left: d.left,
                height: c.innerHeight(),
                width: c.innerWidth()
            };
            d = b.offset();
            var f = e('<div class="ui-effects-transfer"></div>').appendTo(document.body).addClass(a.options.className).css({
                top: d.top,
                left: d.left,
                height: b.innerHeight(),
                width: b.innerWidth(),
                position: "absolute"
            }).animate(c, a.duration, a.options.easing, function() {
                f.remove();
                a.callback && a.callback.apply(b[0], arguments);
                b.dequeue()
            })
        })
    }
}
)(jQuery);
;;/*!
 * jQuery dragscrollable Plugin
 * version: 1.0 (25-Jun-2009)
 * Copyright (c) 2009 Miquel Herrera
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Modified by David Richard (2011)
 *   - added delegateMode option
 */
!function(o) {
    o.fn.dragscrollable = function(e) {
        var t = o.extend({
            dragSelector: ">:first",
            acceptPropagatedEvent: !0,
            preventDefault: !0,
            delegateMode: !1
        }, e || {})
          , a = {
            mouseDownHandler: function(e) {
                if (1 != e.which)
                    return !1;
                if (!e.data.acceptPropagatedEvent) {
                    if ("input" == e.target.localName || "textarea" == e.target.localName)
                        return !0;
                    if (e.target != this)
                        return !1
                }
                return e.data.lastCoord = {
                    left: e.clientX,
                    top: e.clientY
                },
                o.event.add(document, "mouseup", a.mouseUpHandler, e.data),
                o.event.add(document, "mousemove", a.mouseMoveHandler, e.data),
                e.data.preventDefault ? (e.preventDefault(),
                !1) : void 0
            },
            mouseMoveHandler: function(e) {
                var t = e.clientX - e.data.lastCoord.left
                  , a = e.clientY - e.data.lastCoord.top;
                if (e.data.scrollable.scrollLeft(e.data.scrollable.scrollLeft() - t),
                e.data.scrollable.scrollTop(e.data.scrollable.scrollTop() - a),
                e.data.lastCoord = {
                    left: e.clientX,
                    top: e.clientY
                },
                e.data.preventDefault)
                    return e.preventDefault(),
                    !1
            },
            mouseUpHandler: function(e) {
                if (o.event.remove(document, "mousemove", a.mouseMoveHandler),
                o.event.remove(document, "mouseup", a.mouseUpHandler),
                e.data.preventDefault)
                    return e.preventDefault(),
                    !1
            }
        };
        this.each(function() {
            var e = {
                scrollable: o(this),
                acceptPropagatedEvent: t.acceptPropagatedEvent,
                preventDefault: t.preventDefault
            };
            t.delegateMode ? o(this).delegate(t.dragSelector, "mousedown", e, a.mouseDownHandler) : o(this).find(t.dragSelector).bind("mousedown", e, a.mouseDownHandler)
        })
    }
}(jQuery);
;/*!
 * jQuery Hotkeys Plugin
 * Copyright 2010, John Resig
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Based upon the plugin by Tzury Bar Yochay:
 * http://github.com/tzuryby/hotkeys
 *
 * Original idea by:
 * Binny V A, http://www.openjs.com/scripts/events/keyboard_shortcuts/
*/
!function(c) {
    function e(e) {
        if ("string" == typeof e.data) {
            var h = e.handler
              , o = e.data.toLowerCase().split(" ");
            e.handler = function(e) {
                if (this === e.target || !/textarea|select/i.test(e.target.nodeName) && "text" !== e.target.type) {
                    var t = "keypress" !== e.type && c.hotkeys.specialKeys[e.which]
                      , s = String.fromCharCode(e.which).toLowerCase()
                      , a = ""
                      , r = {};
                    e.altKey && "alt" !== t && (a += "alt+"),
                    e.ctrlKey && "ctrl" !== t && (a += "ctrl+"),
                    e.metaKey && !e.ctrlKey && "meta" !== t && (a += "meta+"),
                    e.shiftKey && "shift" !== t && (a += "shift+"),
                    t ? r[a + t] = !0 : (r[a + s] = !0,
                    r[a + c.hotkeys.shiftNums[s]] = !0,
                    "shift+" === a && (r[c.hotkeys.shiftNums[s]] = !0));
                    for (var f = 0, i = o.length; f < i; f++)
                        if (r[o[f]])
                            return h.apply(this, arguments)
                }
            }
        }
    }
    c.hotkeys = {
        version: "0.8",
        specialKeys: {
            8: "backspace",
            9: "tab",
            13: "return",
            16: "shift",
            17: "ctrl",
            18: "alt",
            19: "pause",
            20: "capslock",
            27: "esc",
            32: "space",
            33: "pageup",
            34: "pagedown",
            35: "end",
            36: "home",
            37: "left",
            38: "up",
            39: "right",
            40: "down",
            45: "insert",
            46: "del",
            96: "0",
            97: "1",
            98: "2",
            99: "3",
            100: "4",
            101: "5",
            102: "6",
            103: "7",
            104: "8",
            105: "9",
            106: "*",
            107: "+",
            109: "-",
            110: ".",
            111: "/",
            112: "f1",
            113: "f2",
            114: "f3",
            115: "f4",
            116: "f5",
            117: "f6",
            118: "f7",
            119: "f8",
            120: "f9",
            121: "f10",
            122: "f11",
            123: "f12",
            144: "numlock",
            145: "scroll",
            191: "/",
            224: "meta"
        },
        shiftNums: {
            "`": "~",
            1: "!",
            2: "@",
            3: "#",
            4: "$",
            5: "%",
            6: "^",
            7: "&",
            8: "*",
            9: "(",
            0: ")",
            "-": "_",
            "=": "+",
            ";": ": ",
            "'": '"',
            ",": "<",
            ".": ">",
            "/": "?",
            "\\": "|"
        }
    },
    c.each(["keydown", "keyup", "keypress"], function() {
        c.event.special[this] = {
            add: e
        }
    })
}(jQuery);
;/*! Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.4
 * 
 * Requires: 1.2.2+
 */
!function(h) {
    var t = ["DOMMouseScroll", "mousewheel"];
    function n(e) {
        var t = e || window.event
          , n = [].slice.call(arguments, 1)
          , l = 0
          , i = 0
          , s = 0;
        return (e = h.event.fix(t)).type = "mousewheel",
        e.wheelDelta && (l = e.wheelDelta / 120),
        e.detail && (l = -e.detail / 3),
        s = l,
        void 0 !== t.axis && t.axis === t.HORIZONTAL_AXIS && (s = 0,
        i = -1 * l),
        void 0 !== t.wheelDeltaY && (s = t.wheelDeltaY / 120),
        void 0 !== t.wheelDeltaX && (i = -1 * t.wheelDeltaX / 120),
        n.unshift(e, l, i, s),
        h.event.dispatch(this, n)
    }
    h.event.special.mousewheel = {
        setup: function() {
            if (this.addEventListener)
                for (var e = t.length; e; )
                    this.addEventListener(t[--e], n, !1);
            else
                this.onmousewheel = n
        },
        teardown: function() {
            if (this.removeEventListener)
                for (var e = t.length; e; )
                    this.removeEventListener(t[--e], n, !1);
            else
                this.onmousewheel = null
        }
    },
    h.fn.extend({
        mousewheel: function(e) {
            return e ? this.bind("mousewheel", e) : this.trigger("mousewheel")
        },
        unmousewheel: function(e) {
            return this.unbind("mousewheel", e)
        }
    })
}(jQuery);
;/*!
 * jQuery miniColors: A small color selector
 *
 * Copyright 2011 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 *
 * Dual licensed under the MIT or GPL Version 2 licenses
 *
 */
jQuery && function(C) {
    C.extend(C.fn, {
        miniColors: function(o, t) {
            var n = function(o) {
                o.attr("disabled", !0),
                o.data("trigger").css("opacity", .5),
                c(o)
            }
              , s = function(t) {
                if (t.attr("disabled"))
                    return !1;
                c();
                var o = C('<div class="miniColors-selector"></div>');
                o.append('<div class="miniColors-colors" style="background-color: #FFF;"><div class="miniColors-colorPicker"></div></div>'),
                o.append('<div class="miniColors-hues"><div class="miniColors-huePicker"></div></div>'),
                o.css({
                    top: t.is(":visible") ? t.offset().top + t.outerHeight() : t.data("trigger").offset().top + t.data("trigger").outerHeight(),
                    left: t.is(":visible") ? t.offset().left : t.data("trigger").offset().left - 87,
                    display: "none"
                }).addClass(t.attr("class"));
                var a = t.data("hsb");
                o.find(".miniColors-colors").css("backgroundColor", "#" + p({
                    h: a.h,
                    s: 100,
                    b: 100
                }));
                var r = t.data("colorPosition");
                r || (r = f(a)),
                o.find(".miniColors-colorPicker").css("top", r.y + "px").css("left", r.x + "px");
                var i = t.data("huePosition");
                i || (i = m(a)),
                o.find(".miniColors-huePicker").css("top", i.y + "px"),
                t.data("selector", o),
                t.data("huePicker", o.find(".miniColors-huePicker")),
                t.data("colorPicker", o.find(".miniColors-colorPicker")),
                t.data("mousebutton", 0);
                var e = C("<div class='miniColors-overlay no-select'/>").css({
                    position: "absolute",
                    left: 0,
                    top: 0,
                    width: "100%",
                    height: "100%",
                    "z-index": 1e4
                }).appendTo("BODY").mousedown(function() {
                    c(t),
                    e.remove()
                });
                C("BODY").append(o),
                o.fadeIn(100),
                o.bind("selectstart", function() {
                    return !1
                }),
                o.bind("mousedown.miniColors", function(o) {
                    t.data("mousebutton", 1),
                    C(o.target).hasClass("miniColors-colors") && (o.preventDefault(),
                    t.data("moving", "colors"),
                    l(t, o)),
                    C(o.target).hasClass("miniColors-hues") && (o.preventDefault(),
                    t.data("moving", "hues"),
                    d(t, o)),
                    t.data("move") && t.data("move").call(t, t.val())
                }),
                o.bind("mouseup.miniColors", function(o) {
                    t.data("mousebutton", 0),
                    t.removeData("moving")
                }),
                o.bind("mousemove.miniColors", function(o) {
                    1 === t.data("mousebutton") && ("colors" === t.data("moving") && l(t, o),
                    "hues" === t.data("moving") && d(t, o),
                    t.data("move") && t.data("move").call(t, t.val()))
                })
            }
              , c = function(o) {
                o && o.data("hide") && o.data("hide").call(o, o.val()),
                o || (o = ".miniColors"),
                C(o).each(function() {
                    var o = C(this).data("selector");
                    C(this).removeData("selector"),
                    C(o).fadeOut(100, function() {
                        C(this).remove()
                    })
                })
            }
              , l = function(o, t) {
                var a = o.data("colorPicker");
                a.hide();
                var r = {
                    x: t.clientX - o.data("selector").find(".miniColors-colors").offset().left + C(document).scrollLeft() - 5,
                    y: t.clientY - o.data("selector").find(".miniColors-colors").offset().top + C(document).scrollTop() - 5
                };
                r.x <= -5 && (r.x = -5),
                144 <= r.x && (r.x = 144),
                r.y <= -5 && (r.y = -5),
                144 <= r.y && (r.y = 144),
                o.data("colorPosition", r),
                a.css("left", r.x).css("top", r.y).show();
                var i = Math.round(.67 * (r.x + 5));
                i < 0 && (i = 0),
                100 < i && (i = 100);
                var e = 100 - Math.round(.67 * (r.y + 5));
                e < 0 && (e = 0),
                100 < e && (e = 100);
                var n = o.data("hsb");
                n.s = i,
                n.b = e,
                u(o, n, !0)
            }
              , d = function(o, t) {
                var a = o.data("huePicker");
                a.hide();
                var r = {
                    y: t.clientY - o.data("selector").find(".miniColors-colors").offset().top + C(document).scrollTop() - 1
                };
                r.y <= -1 && (r.y = -1),
                149 <= r.y && (r.y = 149),
                o.data("huePosition", r),
                a.css("top", r.y).show();
                var i = Math.round(2.4 * (150 - r.y - 1));
                i < 0 && (i = 0),
                360 < i && (i = 360);
                var e = o.data("hsb");
                e.h = i,
                u(o, e, !0)
            }
              , u = function(o, t, a) {
                o.data("hsb", t);
                var r = p(t);
                a && o.val("#" + r),
                o.data("trigger").css("backgroundColor", "#" + r),
                o.data("selector") && o.data("selector").find(".miniColors-colors").css("backgroundColor", "#" + p({
                    h: t.h,
                    s: 100,
                    b: 100
                })),
                o.data("change") && o.data("change").call(o, "#" + r, i(t))
            }
              , v = function(o) {
                var t = g(o.val());
                if (!t)
                    return !1;
                var a = b(t)
                  , r = o.data("hsb");
                if (a.h === r.h && a.s === r.s && a.b === r.b)
                    return !0;
                var i = f(a);
                C(o.data("colorPicker")).css("top", i.y + "px").css("left", i.x + "px");
                var e = m(a);
                return C(o.data("huePicker")).css("top", e.y + "px"),
                u(o, a, !1),
                !0
            }
              , f = function(o) {
                var t = Math.ceil(o.s / .67);
                t < 0 && (t = 0),
                150 < t && (t = 150);
                var a = 150 - Math.ceil(o.b / .67);
                return a < 0 && (a = 0),
                150 < a && (a = 150),
                {
                    x: t - 5,
                    y: a - 5
                }
            }
              , m = function(o) {
                var t = 150 - o.h / 2.4;
                return t < 0 && (h = 0),
                150 < t && (h = 150),
                {
                    y: t - 1
                }
            }
              , g = function(o) {
                return 3 == (o = o.replace(/[^A-Fa-f0-9]/, "")).length && (o = o[0] + o[0] + o[1] + o[1] + o[2] + o[2]),
                6 === o.length ? o : null
            }
              , i = function(o) {
                var t = {}
                  , a = Math.round(o.h)
                  , r = Math.round(255 * o.s / 100)
                  , i = Math.round(255 * o.b / 100);
                if (0 == r)
                    t.r = t.g = t.b = i;
                else {
                    var e = i
                      , n = (255 - r) * i / 255
                      , s = a % 60 * (e - n) / 60;
                    360 == a && (a = 0),
                    a < 60 ? (t.r = e,
                    t.b = n,
                    t.g = n + s) : a < 120 ? (t.g = e,
                    t.b = n,
                    t.r = e - s) : a < 180 ? (t.g = e,
                    t.r = n,
                    t.b = n + s) : a < 240 ? (t.b = e,
                    t.r = n,
                    t.g = e - s) : a < 300 ? (t.b = e,
                    t.g = n,
                    t.r = n + s) : a < 360 ? (t.r = e,
                    t.g = n,
                    t.b = e - s) : (t.r = 0,
                    t.g = 0,
                    t.b = 0)
                }
                return {
                    r: Math.round(t.r),
                    g: Math.round(t.g),
                    b: Math.round(t.b)
                }
            }
              , b = function(o) {
                var t, a, r, i, e, n, s = (n = o,
                t = {
                    r: (n = parseInt(-1 < n.indexOf("#") ? n.substring(1) : n, 16)) >> 16,
                    g: (65280 & n) >> 8,
                    b: 255 & n
                },
                a = {
                    h: 0,
                    s: 0,
                    b: 0
                },
                r = Math.min(t.r, t.g, t.b),
                i = Math.max(t.r, t.g, t.b),
                e = i - r,
                a.b = i,
                a.s = 0 != i ? 255 * e / i : 0,
                0 != a.s ? t.r == i ? a.h = (t.g - t.b) / e : t.g == i ? a.h = 2 + (t.b - t.r) / e : a.h = 4 + (t.r - t.g) / e : a.h = -1,
                a.h *= 60,
                a.h < 0 && (a.h += 360),
                a.s *= 100 / 255,
                a.b *= 100 / 255,
                a);
                return 0 === s.s && (s.h = 360),
                s
            }
              , p = function(o) {
                return t = i(o),
                a = [t.r.toString(16), t.g.toString(16), t.b.toString(16)],
                C.each(a, function(o, t) {
                    1 == t.length && (a[o] = "0" + t)
                }),
                a.join("");
                var t, a
            };
            switch (o) {
            case "readonly":
                return C(this).each(function() {
                    C(this).attr("readonly", t)
                }),
                C(this);
            case "disabled":
                return C(this).each(function() {
                    var o;
                    t ? n(C(this)) : ((o = C(this)).attr("disabled", !1),
                    o.data("trigger").css("opacity", 1))
                }),
                C(this);
            case "value":
                return C(this).each(function() {
                    C(this).val(t).trigger("keyup")
                }),
                C(this);
            case "destroy":
                return C(this).each(function() {
                    var o;
                    o = C(this),
                    c(),
                    (o = C(o)).data("trigger").remove(),
                    o.removeAttr("autocomplete"),
                    o.removeData("trigger"),
                    o.removeData("selector"),
                    o.removeData("hsb"),
                    o.removeData("huePicker"),
                    o.removeData("colorPicker"),
                    o.removeData("mousebutton"),
                    o.removeData("moving"),
                    o.unbind("click.miniColors"),
                    o.unbind("focus.miniColors"),
                    o.unbind("blur.miniColors"),
                    o.unbind("keyup.miniColors"),
                    o.unbind("keydown.miniColors"),
                    o.unbind("paste.miniColors")
                }),
                C(this);
            default:
                return o || (o = {}),
                C(this).each(function() {
                    "input" === C(this)[0].tagName.toLowerCase() && (C(this).data("trigger") || function(a, o, t) {
                        var r = g(a.val());
                        r || (r = "FFFFFF");
                        var i = b(r)
                          , e = C('<a class="miniColors-trigger" style="background-color: #' + r + '" href="#"></a>');
                        e.insertAfter(a),
                        a.addClass("miniColors").attr("maxlength", 7).attr("autocomplete", "off"),
                        a.data("trigger", e),
                        a.data("hsb", i),
                        o.change && a.data("change", o.change),
                        o.hide && a.data("hide", o.hide),
                        o.move && a.data("move", o.move),
                        o.readonly && a.attr("readonly", !0),
                        o.disabled && n(a),
                        e.bind("click.miniColors", function(o) {
                            o.preventDefault(),
                            a.trigger("focus")
                        }),
                        a.bind("focus.miniColors", function(o) {
                            s(a)
                        }),
                        a.bind("blur.miniColors", function(o) {
                            var t = g(a.val());
                            a.val(t ? "#" + t : "")
                        }),
                        a.bind("keydown.miniColors", function(o) {
                            9 === o.keyCode && c(a)
                        }),
                        a.bind("keyup.miniColors", function(o) {
                            var t = a.val().replace(/[^A-F0-9#]/gi, "");
                            a.val(t),
                            v(a) || a.data("trigger").css("backgroundColor", "#FFF")
                        }),
                        a.bind("paste.miniColors", function(o) {
                            setTimeout(function() {
                                a.trigger("keyup")
                            }, 5)
                        })
                    }(C(this), o))
                }),
                C(this)
            }
        }
    })
}(jQuery);
;/*!
 * jQuery Templates Plugin 1.0.0pre
 * http://github.com/jquery/jquery-tmpl
 * Requires jQuery 1.4.2
 *
 * Copyright Software Freedom Conservancy, Inc.
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 */
!function(f, t) {
    var c, u = f.fn.domManip, m = "_tmplitem", n = /^[^<]*(<[\w\W]+>)[^>]*$|\{\{\! /, s = {}, d = {}, p = {
        key: 0,
        data: {}
    }, $ = 0, _ = 0, r = [];
    function h(t, e, n, l) {
        var a = {
            data: l || 0 === l || !1 === l ? l : e ? e.data : {},
            _wrap: e ? e._wrap : null,
            tmpl: null,
            parent: e || null,
            nodes: [],
            calls: v,
            nest: k,
            wrap: T,
            html: j,
            update: A
        };
        return t && f.extend(a, t, {
            nodes: [],
            parent: e
        }),
        n && (a.tmpl = n,
        a._ctnt = a._ctnt || a.tmpl(f, a),
        a.key = ++$,
        (r.length ? d : s)[$] = a),
        a
    }
    function i(e, t, n) {
        var a, l = n ? f.map(n, function(t) {
            return "string" == typeof t ? e.key ? t.replace(/(<\w+)(?=[\s>])(?![^>]*_tmplitem)([^>]*)/g, "$1 " + m + '="' + e.key + '" $2') : t : i(t, e, t._ctnt)
        }) : e;
        return t ? l : ((l = l.join("")).replace(/^\s*([^<\s][^<]*)?(<[\w\W]+>)([^>]*[^>\s])?\s*$/, function(t, e, n, l) {
            w(a = f(n).get()),
            e && (a = o(e).concat(a)),
            l && (a = a.concat(o(l)))
        }),
        a || o(l))
    }
    function o(t) {
        var e = document.createElement("div");
        return e.innerHTML = t,
        f.makeArray(e.childNodes)
    }
    function l(t) {
        return new Function("jQuery","$item","var $=jQuery,call,__=[],$data=$item.data;with($data){__.push('" + f.trim(t).replace(/([\\'])/g, "\\$1").replace(/[\r\t\n]/g, " ").replace(/\$\{([^\}]*)\}/g, "{{= $1}}").replace(/\{\{(\/?)(\w+|.)(?:\(((?:[^\}]|\}(?!\}))*?)?\))?(?:\s+(.*?)?)?(\(((?:[^\}]|\}(?!\}))*?)\))?\s*\}\}/g, function(t, e, n, l, a, r, p) {
            var i, o, u, c = f.tmpl.tag[n];
            if (!c)
                throw "Unknown template tag: " + n;
            return i = c._default || [],
            r && !/\w$/.test(a) && (a += r,
            r = ""),
            a ? (a = g(a),
            p = p ? "," + g(p) + ")" : r ? ")" : "",
            o = r ? -1 < a.indexOf(".") ? a + g(r) : "(" + a + ").call($item" + p : a,
            u = r ? o : "(typeof(" + a + ")==='function'?(" + a + ").call($item):(" + a + "))") : u = o = i.$1 || "null",
            l = g(l),
            "');" + c[e ? "close" : "open"].split("$notnull_1").join(a ? "typeof(" + a + ")!=='undefined' && (" + a + ")!=null" : "true").split("$1a").join(u).split("$1").join(o).split("$2").join(l || i.$2 || "") + "__.push('"
        }) + "');}return __;")
    }
    function y(t, e) {
        t._wrap = i(t, !0, f.isArray(e) ? e : [n.test(e) ? e : f(e).html()]).join("")
    }
    function g(t) {
        return t ? t.replace(/\\'/g, "'").replace(/\\\\/g, "\\") : null
    }
    function w(t) {
        var e, n, l, a, r, i = "_" + _, o = {};
        for (l = 0,
        a = t.length; l < a; l++)
            if (1 === (e = t[l]).nodeType) {
                for (r = (n = e.getElementsByTagName("*")).length - 1; 0 <= r; r--)
                    p(n[r]);
                p(e)
            }
        function p(t) {
            var e, n, l, a, r = t;
            if (a = t.getAttribute(m)) {
                for (; r.parentNode && 1 === (r = r.parentNode).nodeType && !(e = r.getAttribute(m)); )
                    ;
                e !== a && (r = r.parentNode ? 11 === r.nodeType ? 0 : r.getAttribute(m) || 0 : 0,
                (l = s[a]) || ((l = h(l = d[a], s[r] || d[r])).key = ++$,
                s[$] = l),
                _ && p(a)),
                t.removeAttribute(m)
            } else
                _ && (l = f.data(t, "tmplItem")) && (p(l.key),
                s[l.key] = l,
                r = (r = f.data(t.parentNode, "tmplItem")) ? r.key : 0);
            if (l) {
                for (n = l; n && n.key != r; )
                    n.nodes.push(t),
                    n = n.parent;
                delete l._ctnt,
                delete l._wrap,
                f.data(t, "tmplItem", l)
            }
            function p(t) {
                l = o[t += i] = o[t] || h(l, s[l.parent.key + i] || l.parent)
            }
        }
    }
    function v(t, e, n, l) {
        if (!t)
            return r.pop();
        r.push({
            _: t,
            tmpl: e,
            item: this,
            data: n,
            options: l
        })
    }
    function k(t, e, n) {
        return f.tmpl(f.template(t), e, n, this)
    }
    function T(t, e) {
        var n = t.options || {};
        return n.wrapped = e,
        f.tmpl(f.template(t.tmpl), t.data, n, t.item)
    }
    function j(t, l) {
        var e = this._wrap;
        return f.map(f(f.isArray(e) ? e.join("") : e).filter(t || "*"), function(t) {
            return l ? t.innerText || t.textContent : t.outerHTML || (e = t,
            (n = document.createElement("div")).appendChild(e.cloneNode(!0)),
            n.innerHTML);
            var e, n
        })
    }
    function A() {
        var t = this.nodes;
        f.tmpl(null, null, null, this).insertBefore(t[0]),
        f(t).remove()
    }
    f.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    }, function(o, u) {
        f.fn[o] = function(t) {
            var e, n, l, a, r = [], p = f(t), i = 1 === this.length && this[0].parentNode;
            if (c = s || {},
            i && 11 === i.nodeType && 1 === i.childNodes.length && 1 === p.length)
                p[u](this[0]),
                r = this;
            else {
                for (n = 0,
                l = p.length; n < l; n++)
                    e = (0 < (_ = n) ? this.clone(!0) : this).get(),
                    f(p[n])[u](e),
                    r = r.concat(e);
                _ = 0,
                r = this.pushStack(r, o, p.selector)
            }
            return a = c,
            c = null,
            f.tmpl.complete(a),
            r
        }
    }),
    f.fn.extend({
        tmpl: function(t, e, n) {
            return f.tmpl(this[0], t, e, n)
        },
        tmplItem: function() {
            return f.tmplItem(this[0])
        },
        template: function(t) {
            return f.template(t, this[0])
        },
        domManip: function(t, e, n, l) {
            if (t[0] && f.isArray(t[0])) {
                for (var a, r = f.makeArray(arguments), p = t[0], i = p.length, o = 0; o < i && !(a = f.data(p[o++], "tmplItem")); )
                    ;
                a && _ && (r[2] = function(t) {
                    f.tmpl.afterManip(this, t, n)
                }
                ),
                u.apply(this, r)
            } else
                u.apply(this, arguments);
            return _ = 0,
            c || f.tmpl.complete(s),
            this
        }
    }),
    f.extend({
        tmpl: function(e, t, n, l) {
            var a, r = !l;
            if (r)
                l = p,
                e = f.template[e] || f.template(null, e),
                d = {};
            else if (!e)
                return e = l.tmpl,
                (s[l.key] = l).nodes = [],
                l.wrapped && y(l, l.wrapped),
                f(i(l, null, l.tmpl(f, l)));
            return e ? ("function" == typeof t && (t = t.call(l || {})),
            n && n.wrapped && y(n, n.wrapped),
            a = f.isArray(t) ? f.map(t, function(t) {
                return t ? h(n, l, e, t) : null
            }) : [h(n, l, e, t)],
            r ? f(i(l, null, a)) : a) : []
        },
        tmplItem: function(t) {
            var e;
            for (t instanceof f && (t = t[0]); t && 1 === t.nodeType && !(e = f.data(t, "tmplItem")) && (t = t.parentNode); )
                ;
            return e || p
        },
        template: function(t, e) {
            return e ? ("string" == typeof e ? e = l(e) : e instanceof f && (e = e[0] || {}),
            e.nodeType && (e = f.data(e, "tmpl") || f.data(e, "tmpl", l(e.innerHTML))),
            "string" == typeof t ? f.template[t] = e : e) : t ? "string" != typeof t ? f.template(null, t) : f.template[t] || f.template(null, n.test(t) ? t : f(t)) : null
        },
        encode: function(t) {
            return ("" + t).split("<").join("&lt;").split(">").join("&gt;").split('"').join("&#34;").split("'").join("&#39;")
        }
    }),
    f.extend(f.tmpl, {
        tag: {
            tmpl: {
                _default: {
                    $2: "null"
                },
                open: "if($notnull_1){__=__.concat($item.nest($1,$2));}"
            },
            wrap: {
                _default: {
                    $2: "null"
                },
                open: "$item.calls(__,$1,$2);__=[];",
                close: "call=$item.calls();__=call._.concat($item.wrap(call,__));"
            },
            each: {
                _default: {
                    $2: "$index, $value"
                },
                open: "if($notnull_1){$.each($1a,function($2){with(this){",
                close: "}});}"
            },
            if: {
                open: "if(($notnull_1) && $1a){",
                close: "}"
            },
            else: {
                _default: {
                    $1: "true"
                },
                open: "}else if(($notnull_1) && $1a){"
            },
            html: {
                open: "if($notnull_1){__.push($1a);}"
            },
            "=": {
                _default: {
                    $1: "$data"
                },
                open: "if($notnull_1){__.push($.encode($1a));}"
            },
            "!": {
                open: ""
            }
        },
        complete: function(t) {
            s = {}
        },
        afterManip: function(t, e, n) {
            var l = 11 === e.nodeType ? f.makeArray(e.childNodes) : 1 === e.nodeType ? [e] : [];
            n.call(t, e),
            w(l),
            _++
        }
    })
}(jQuery);
;/*!
// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
var EventEmitter = function() {};
EventEmitter.prototype.setMaxListeners = function(e) {
    this._events || (this._events = {}),
    this._events.maxListeners = e
}
,
EventEmitter.prototype.emit = function(e) {
    if ("error" === e && (!this._events || !this._events.error || Array.isArray(this._events.error) && !this._events.error.length))
        throw arguments[1]instanceof Error ? arguments[1] : new Error("Uncaught, unspecified 'error' event.");
    if (!this._events)
        return !1;
    var t = this._events[e];
    if (!t)
        return !1;
    if ("function" == typeof t) {
        switch (arguments.length) {
        case 1:
            t.call(this);
            break;
        case 2:
            t.call(this, arguments[1]);
            break;
        case 3:
            t.call(this, arguments[1], arguments[2]);
            break;
        default:
            var r = Array.prototype.slice.call(arguments, 1);
            t.apply(this, r)
        }
        return !0
    }
    if (Array.isArray(t)) {
        r = Array.prototype.slice.call(arguments, 1);
        for (var n = t.slice(), s = 0, i = n.length; s < i; s++)
            n[s].apply(this, r);
        return !0
    }
    return !1
}
,
EventEmitter.prototype.publish = EventEmitter.prototype.emit,
EventEmitter.prototype.addListener = function(e, t) {
    if ("function" != typeof t)
        throw new Error("addListener only takes instances of Function");
    if (this._events || (this._events = {}),
    this.emit("newListener", e, t),
    this._events[e])
        if (Array.isArray(this._events[e])) {
            var r;
            if (this._events[e].push(t),
            !this._events[e].warned)
                (r = void 0 !== this._events.maxListeners ? this._events.maxListeners : 10) && 0 < r && this._events[e].length > r && (this._events[e].warned = !0,
                console.error("(node) warning: possible EventEmitter memory leak detected. %d listeners added. Use emitter.setMaxListeners() to increase limit.", this._events[e].length),
                console.trace())
        } else
            this._events[e] = [this._events[e], t];
    else
        this._events[e] = t;
    return this
}
,
EventEmitter.prototype.on = EventEmitter.prototype.subscribe = EventEmitter.prototype.addListener,
EventEmitter.prototype.once = function(e, t) {
    if ("function" != typeof t)
        throw new Error(".once only takes instances of Function");
    var r = this;
    function n() {
        r.removeListener(e, n),
        t.apply(this, arguments)
    }
    return n.listener = t,
    r.on(e, n),
    this
}
,
EventEmitter.prototype.removeListener = function(e, t) {
    if ("function" != typeof t)
        throw new Error("removeListener only takes instances of Function");
    if (!this._events || !this._events[e])
        return this;
    var r = this._events[e];
    if (Array.isArray(r)) {
        for (var n = -1, s = 0, i = r.length; s < i; s++)
            if (r[s] === t || r[s].listener && r[s].listener === t) {
                n = s;
                break
            }
        if (n < 0)
            return this;
        r.splice(n, 1),
        0 == r.length && delete this._events[e]
    } else
        (r === t || r.listener && r.listener === t) && delete this._events[e];
    return this
}
,
EventEmitter.prototype.unsubscribe = EventEmitter.prototype.removeListener,
EventEmitter.prototype.removeAllListeners = function(e) {
    return 0 === arguments.length ? this._events = {} : e && this._events && this._events[e] && (this._events[e] = null),
    this
}
,
EventEmitter.prototype.listeners = function(e) {
    return this._events || (this._events = {}),
    this._events[e] || (this._events[e] = []),
    Array.isArray(this._events[e]) || (this._events[e] = [this._events[e]]),
    this._events[e]
}
,
EventEmitter.mixin = function(e) {
    for (var t in EventEmitter.prototype)
        e.prototype[t] || (e.prototype[t] = EventEmitter.prototype[t])
}
;
;/*!
 *  mindmaps - a HTML5 powered mind mapping application
 *  Copyright (C) 2011  David Richard
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
"use strict";
var mindmaps = mindmaps || {};
function addUnloadHook() {
    window.onbeforeunload = function(r) {
        var t = "Are you sure? Any unsaved progress will be lost.";
        return (r = r || window.event) && (r.returnValue = t),
        t
    }
}
function trackErrors() {
    window.onerror = function(r, t, n) {
        if (window.gtag)
            return gtag("event", r, {
                event_category: "Error Log",
                event_label: r,
                value: t + "_" + n
            }),
            !1
    }
}
function setupConsole() {
    var t = function() {}
      , n = window.console || {};
    ["log", "info", "debug", "warn", "error"].forEach(function(r) {
        n[r] = n[r] || t
    }),
    mindmaps.DEBUG || (n.debug = t,
    n.info = t,
    n.log = t,
    n.warn = t,
    n.error = function(r) {
        window.alert("Error: " + r)
    }
    ),
    window.console = n
}
function createECMA5Shims() {
    if (!Function.prototype.bind) {
        var e = Array.prototype.slice;
        Function.prototype.bind = function(r) {
            var t = this;
            if ("function" != typeof t.apply || "function" != typeof t.call)
                return new TypeError;
            var n = e.call(arguments);
            function o() {
                if (this instanceof o) {
                    var r = Object.create(t.prototype);
                    return t.apply(r, n.concat(e.call(arguments))),
                    r
                }
                return t.call.apply(t, n.concat(e.call(arguments)))
            }
            return o.length = "function" == typeof t ? Math.max(t.length - n.length, 0) : 0,
            o
        }
    }
    Array.isArray || (Array.isArray = function(r) {
        return "[object Array]" === Object.prototype.toString.call(r)
    }
    ),
    Array.prototype.forEach || (Array.prototype.forEach = function(r, t) {
        for (var n = +this.length, o = 0; o < n; o++)
            o in this && r.call(t, this[o], o, this)
    }
    ),
    Array.prototype.map || (Array.prototype.map = function(r) {
        var t = +this.length;
        if ("function" != typeof r)
            throw new TypeError;
        for (var n = new Array(t), o = arguments[1], e = 0; e < t; e++)
            e in this && (n[e] = r.call(o, this[e], e, this));
        return n
    }
    ),
    Array.prototype.filter || (Array.prototype.filter = function(r) {
        for (var t = [], n = arguments[1], o = 0; o < this.length; o++)
            r.call(n, this[o]) && t.push(this[o]);
        return t
    }
    ),
    Array.prototype.every || (Array.prototype.every = function(r) {
        for (var t = arguments[1], n = 0; n < this.length; n++)
            if (!r.call(t, this[n]))
                return !1;
        return !0
    }
    ),
    Array.prototype.some || (Array.prototype.some = function(r) {
        for (var t = arguments[1], n = 0; n < this.length; n++)
            if (r.call(t, this[n]))
                return !0;
        return !1
    }
    ),
    Array.prototype.reduce || (Array.prototype.reduce = function(r) {
        var t = +this.length;
        if ("function" != typeof r)
            throw new TypeError;
        if (0 === t && 1 === arguments.length)
            throw new TypeError;
        var n = 0;
        if (2 <= arguments.length)
            var o = arguments[1];
        else
            for (; ; ) {
                if (n in this) {
                    o = this[n++];
                    break
                }
                if (++n >= t)
                    throw new TypeError
            }
        for (; n < t; n++)
            n in this && (o = r.call(null, o, this[n], n, this));
        return o
    }
    ),
    Array.prototype.reduceRight || (Array.prototype.reduceRight = function(r) {
        var t = +this.length;
        if ("function" != typeof r)
            throw new TypeError;
        if (0 === t && 1 === arguments.length)
            throw new TypeError;
        var n, o = t - 1;
        if (2 <= arguments.length)
            n = arguments[1];
        else
            for (; ; ) {
                if (o in this) {
                    n = this[o--];
                    break
                }
                if (--o < 0)
                    throw new TypeError
            }
        for (; 0 <= o; o--)
            o in this && (n = r.call(null, n, this[o], o, this));
        return n
    }
    ),
    Array.prototype.indexOf || (Array.prototype.indexOf = function(r) {
        var t = this.length;
        if (!t)
            return -1;
        var n = arguments[1] || 0;
        if (t <= n)
            return -1;
        for (n < 0 && (n += t); n < t; n++)
            if (n in this && r === this[n])
                return n;
        return -1
    }
    ),
    Array.prototype.lastIndexOf || (Array.prototype.lastIndexOf = function(r) {
        var t = this.length;
        if (!t)
            return -1;
        var n = arguments[1] || t;
        for (n < 0 && (n += t),
        n = Math.min(n, t - 1); 0 <= n; n--)
            if (n in this && r === this[n])
                return n;
        return -1
    }
    ),
    Date.now || (Date.now = function() {
        return (new Date).getTime()
    }
    )
}
function createHTML5Shims() {
    void 0 === window.localStorage && (window.localStorage = {
        getItem: function() {
            return null
        },
        setItem: function() {},
        clear: function() {},
        removeItem: function() {},
        length: 0,
        key: function() {
            return null
        }
    })
}
mindmaps.VERSION = "0.7.2",
"drichard.org" === window.location.hostname && (window.onbeforeunload = null,
window.location.assign("https://www.mindmaps.app")),
$(function() {
    createECMA5Shims(),
    createHTML5Shims(),
    setupConsole(),
    trackErrors(),
    mindmaps.DEBUG || addUnloadHook(),
    (new mindmaps.ApplicationController).go()
}),
$(function() {
    $("#bottombar table").remove(),
    $("input[name='hosted_button_id']").val("123")
});
;mindmaps.Command = function() {
    this.id = "BASE_COMMAND",
    this.shortcut = null,
    this.handler = null,
    this.label = null,
    this.description = null,
    this.enabled = !1
}
,
mindmaps.Command.Event = {
    HANDLER_REGISTERED: "HandlerRegisteredCommandEvent",
    HANDLER_REMOVED: "HandlerRemovedCommandEvent",
    ENABLED_CHANGED: "EnabledChangedCommandEvent"
},
mindmaps.Command.prototype = {
    execute: function() {
        this.handler ? (this.handler(),
        mindmaps.DEBUG && console.log("handler called for", this.id)) : mindmaps.DEBUG && console.log("no handler found for", this.id)
    },
    setHandler: function(i) {
        this.removeHandler(),
        this.handler = i,
        this.publish(mindmaps.Command.Event.HANDLER_REGISTERED)
    },
    removeHandler: function() {
        this.handler = null,
        this.publish(mindmaps.Command.Event.HANDLER_REMOVED)
    },
    setEnabled: function(i) {
        this.enabled = i,
        this.publish(mindmaps.Command.Event.ENABLED_CHANGED, i)
    }
},
EventEmitter.mixin(mindmaps.Command),
mindmaps.CreateNodeCommand = function() {
    this.id = "CREATE_NODE_COMMAND",
    this.shortcut = "tab",
    this.label = "Add",
    this.icon = "ui-icon-plusthick",
    this.description = "Creates a new node"
}
,
mindmaps.CreateNodeCommand.prototype = new mindmaps.Command,
mindmaps.CreateSiblingNodeCommand = function() {
    this.id = "CREATE_SIBLING_NODE_COMMAND",
    this.shortcut = "shift+tab",
    this.label = "Add",
    this.icon = "ui-icon-plusthick",
    this.description = "Creates a new sibling node"
}
,
mindmaps.CreateSiblingNodeCommand.prototype = new mindmaps.Command,
mindmaps.DeleteNodeCommand = function() {
    this.id = "DELETE_NODE_COMMAND",
    this.shortcut = ["del", "backspace"],
    this.label = "Delete",
    this.icon = "ui-icon-minusthick",
    this.description = "Deletes a new node"
}
,
mindmaps.DeleteNodeCommand.prototype = new mindmaps.Command,
mindmaps.EditNodeCaptionCommand = function() {
    this.id = "EDIT_NODE_CAPTION_COMMAND",
    this.shortcut = ["F2", "return"],
    this.label = "Edit node caption",
    this.description = "Edits the node text"
}
,
mindmaps.EditNodeCaptionCommand.prototype = new mindmaps.Command,
mindmaps.ToggleNodeFoldedCommand = function() {
    this.id = "TOGGLE_NODE_FOLDED_COMMAND",
    this.shortcut = "space",
    this.description = "Show or hide the node's children"
}
,
mindmaps.ToggleNodeFoldedCommand.prototype = new mindmaps.Command,
mindmaps.UndoCommand = function() {
    this.id = "UNDO_COMMAND",
    this.shortcut = ["ctrl+z", "meta+z"],
    this.label = "Undo",
    this.icon = "ui-icon-arrowreturnthick-1-w",
    this.description = "Undo"
}
,
mindmaps.UndoCommand.prototype = new mindmaps.Command,
mindmaps.RedoCommand = function() {
    this.id = "REDO_COMMAND",
    this.shortcut = ["ctrl+y", "meta+shift+z"],
    this.label = "Redo",
    this.icon = "ui-icon-arrowreturnthick-1-e",
    this.description = "Redo"
}
,
mindmaps.RedoCommand.prototype = new mindmaps.Command,
mindmaps.CopyNodeCommand = function() {
    this.id = "COPY_COMMAND",
    this.shortcut = ["ctrl+c", "meta+c"],
    this.label = "Copy",
    this.icon = "ui-icon-copy",
    this.description = "Copy a branch"
}
,
mindmaps.CopyNodeCommand.prototype = new mindmaps.Command,
mindmaps.CutNodeCommand = function() {
    this.id = "CUT_COMMAND",
    this.shortcut = ["ctrl+x", "meta+x"],
    this.label = "Cut",
    this.icon = "ui-icon-scissors",
    this.description = "Cut a branch"
}
,
mindmaps.CutNodeCommand.prototype = new mindmaps.Command,
mindmaps.PasteNodeCommand = function() {
    this.id = "PASTE_COMMAND",
    this.shortcut = ["ctrl+v", "meta+v"],
    this.label = "Paste",
    this.icon = "ui-icon-clipboard",
    this.description = "Paste a branch"
}
,
mindmaps.PasteNodeCommand.prototype = new mindmaps.Command,
mindmaps.NewDocumentCommand = function() {
    this.id = "NEW_DOCUMENT_COMMAND",
    this.label = "New",
    this.icon = "ui-icon-document-b",
    this.description = "Start working on a new mind map"
}
,
mindmaps.NewDocumentCommand.prototype = new mindmaps.Command,
mindmaps.OpenDocumentCommand = function() {
    this.id = "OPEN_DOCUMENT_COMMAND",
    this.label = "Open...",
    this.shortcut = ["ctrl+o", "meta+o"],
    this.icon = "ui-icon-folder-open",
    this.description = "Open an existing mind map"
}
,
mindmaps.OpenDocumentCommand.prototype = new mindmaps.Command,
mindmaps.SaveDocumentCommand = function() {
    this.id = "SAVE_DOCUMENT_COMMAND",
    this.label = "Save...",
    this.shortcut = ["ctrl+s", "meta+s"],
    this.icon = "ui-icon-disk",
    this.description = "Save the mind map"
}
,
mindmaps.SaveDocumentCommand.prototype = new mindmaps.Command,
mindmaps.CloseDocumentCommand = function() {
    this.id = "CLOSE_DOCUMENT_COMMAND",
    this.label = "Close",
    this.icon = "ui-icon-close",
    this.description = "Close the mind map"
}
,
mindmaps.CloseDocumentCommand.prototype = new mindmaps.Command,
mindmaps.HelpCommand = function() {
    this.id = "HELP_COMMAND",
    this.enabled = !0,
    this.icon = "ui-icon-help",
    this.label = "Help",
    this.shortcut = "F1",
    this.description = "Get help!"
}
,
mindmaps.HelpCommand.prototype = new mindmaps.Command,
mindmaps.PrintCommand = function() {
    this.id = "PRINT_COMMAND",
    this.icon = "ui-icon-print",
    this.label = "Print",
    this.shortcut = ["ctrl+p", "meta+p"],
    this.description = "Print the mind map"
}
,
mindmaps.PrintCommand.prototype = new mindmaps.Command,
mindmaps.ExportCommand = function() {
    this.id = "EXPORT_COMMAND",
    this.icon = "ui-icon-image",
    this.label = "Export As Image...",
    this.description = "Export the mind map"
}
,
mindmaps.ExportCommand.prototype = new mindmaps.Command;
;mindmaps.CommandRegistry = function(n) {
    this.commands = {},
    this.get = function(t) {
        var s, e = this.commands[t];
        return e || (e = new t,
        this.commands[t] = e,
        n && (s = e).shortcut && s.execute && n.register(s.shortcut, s.execute.bind(s))),
        e
    }
    ,
    this.remove = function(t) {
        var s, e = this.commands[t];
        e && (delete this.commands[t],
        n && (s = e).shortcut && n.unregister(s.shortcut))
    }
}
;
;mindmaps.action = {},
mindmaps.action.Action = function() {}
,
mindmaps.action.Action.prototype = {
    noUndo: function() {
        return delete this.undo,
        delete this.redo,
        this
    },
    noEvent: function() {
        return delete this.event,
        this
    },
    execute: function() {},
    cancel: function() {
        this.cancelled = !0
    }
},
mindmaps.action.MoveNodeAction = function(n, t) {
    var o = n.offset;
    this.execute = function() {
        n.offset = t
    }
    ,
    this.event = [mindmaps.Event.NODE_MOVED, n],
    this.undo = function() {
        return new mindmaps.action.MoveNodeAction(n,o)
    }
}
,
mindmaps.action.MoveNodeAction.prototype = new mindmaps.action.Action,
mindmaps.action.DeleteNodeAction = function(n, t) {
    var o = n.getParent();
    this.execute = function() {
        if (n.isRoot())
            return !1;
        t.removeNode(n)
    }
    ,
    this.event = [mindmaps.Event.NODE_DELETED, n, o],
    this.undo = function() {
        return new mindmaps.action.CreateNodeAction(n,o,t)
    }
}
,
mindmaps.action.DeleteNodeAction.prototype = new mindmaps.action.Action,
mindmaps.action.CreateAutoPositionedNodeAction = function(n, t) {
    if (n.isRoot())
        var o = mindmaps.Util.randomColor()
          , i = .49 < Math.random() ? 1 : -1
          , e = .49 < Math.random() ? 1 : -1
          , a = i * (100 + 250 * Math.random())
          , c = e * (250 * Math.random());
    else {
        o = n.branchColor,
        a = (i = 0 < n.offset.x ? 1 : -1) * (150 + 10 * Math.random());
        if (n.isLeaf())
            var m = 5
              , d = -5;
        else
            m = 150,
            d = -150;
        c = Math.floor(Math.random() * (m - d + 1) + d)
    }
    var s = new mindmaps.Node;
    return s.branchColor = o,
    s.shouldEditCaption = !0,
    s.offset = new mindmaps.Point(a,c),
    new mindmaps.action.CreateNodeAction(s,n,t)
}
,
mindmaps.action.CreateNodeAction = function(n, t, o) {
    this.execute = function() {
        o.addNode(n),
        t.addChild(n)
    }
    ,
    this.event = [mindmaps.Event.NODE_CREATED, n],
    this.undo = function() {
        return new mindmaps.action.DeleteNodeAction(n,o)
    }
}
,
mindmaps.action.CreateNodeAction.prototype = new mindmaps.action.Action,
mindmaps.action.ToggleNodeFoldAction = function(n) {
    return n.foldChildren ? new mindmaps.action.OpenNodeAction(n) : new mindmaps.action.CloseNodeAction(n)
}
,
mindmaps.action.OpenNodeAction = function(n) {
    this.execute = function() {
        n.foldChildren = !1
    }
    ,
    this.event = [mindmaps.Event.NODE_OPENED, n]
}
,
mindmaps.action.OpenNodeAction.prototype = new mindmaps.action.Action,
mindmaps.action.CloseNodeAction = function(n) {
    this.execute = function() {
        n.foldChildren = !0
    }
    ,
    this.event = [mindmaps.Event.NODE_CLOSED, n]
}
,
mindmaps.action.CloseNodeAction.prototype = new mindmaps.action.Action,
mindmaps.action.ChangeNodeCaptionAction = function(n, t) {
    var o = n.getCaption();
    this.execute = function() {
        if (o === t)
            return !1;
        n.setCaption(t)
    }
    ,
    this.event = [mindmaps.Event.NODE_TEXT_CAPTION_CHANGED, n],
    this.undo = function() {
        return new mindmaps.action.ChangeNodeCaptionAction(n,o)
    }
}
,
mindmaps.action.ChangeNodeCaptionAction.prototype = new mindmaps.action.Action,
mindmaps.action.ChangeNodeFontSizeAction = function(n, t) {
    this.execute = function() {
        n.text.font.size += t
    }
    ,
    this.event = [mindmaps.Event.NODE_FONT_CHANGED, n],
    this.undo = function() {
        return new mindmaps.action.ChangeNodeFontSizeAction(n,-t)
    }
}
,
mindmaps.action.ChangeNodeFontSizeAction.prototype = new mindmaps.action.Action,
mindmaps.action.DecreaseNodeFontSizeAction = function(n) {
    return new mindmaps.action.ChangeNodeFontSizeAction(n,-4)
}
,
mindmaps.action.IncreaseNodeFontSizeAction = function(n) {
    return new mindmaps.action.ChangeNodeFontSizeAction(n,4)
}
,
mindmaps.action.SetFontWeightAction = function(t, o) {
    this.execute = function() {
        var n = o ? "bold" : "normal";
        t.text.font.weight = n
    }
    ,
    this.event = [mindmaps.Event.NODE_FONT_CHANGED, t],
    this.undo = function() {
        return new mindmaps.action.SetFontWeightAction(t,!o)
    }
}
,
mindmaps.action.SetFontWeightAction.prototype = new mindmaps.action.Action,
mindmaps.action.SetFontStyleAction = function(t, o) {
    this.execute = function() {
        var n = o ? "italic" : "normal";
        t.text.font.style = n
    }
    ,
    this.event = [mindmaps.Event.NODE_FONT_CHANGED, t],
    this.undo = function() {
        return new mindmaps.action.SetFontStyleAction(t,!o)
    }
}
,
mindmaps.action.SetFontStyleAction.prototype = new mindmaps.action.Action,
mindmaps.action.SetFontDecorationAction = function(n, t) {
    var o = n.text.font.decoration;
    this.execute = function() {
        n.text.font.decoration = t
    }
    ,
    this.event = [mindmaps.Event.NODE_FONT_CHANGED, n],
    this.undo = function() {
        return new mindmaps.action.SetFontDecorationAction(n,o)
    }
}
,
mindmaps.action.SetFontDecorationAction.prototype = new mindmaps.action.Action,
mindmaps.action.SetFontColorAction = function(n, t) {
    var o = n.text.font.color;
    this.execute = function() {
        n.text.font.color = t
    }
    ,
    this.event = [mindmaps.Event.NODE_FONT_CHANGED, n],
    this.undo = function() {
        return new mindmaps.action.SetFontColorAction(n,o)
    }
}
,
mindmaps.action.SetFontColorAction.prototype = new mindmaps.action.Action,
mindmaps.action.SetBranchColorAction = function(n, t) {
    var o = n.branchColor;
    this.execute = function() {
        if (t === n.branchColor)
            return !1;
        n.branchColor = t
    }
    ,
    this.event = [mindmaps.Event.NODE_BRANCH_COLOR_CHANGED, n],
    this.undo = function() {
        return new mindmaps.action.SetBranchColorAction(n,o)
    }
}
,
mindmaps.action.SetBranchColorAction.prototype = new mindmaps.action.Action,
mindmaps.action.CompositeAction = function() {
    this.actions = []
}
,
mindmaps.action.CompositeAction.prototype.addAction = function(n) {
    this.actions.push(n)
}
,
mindmaps.action.CompositeAction.prototype.forEachAction = function(n) {
    this.actions.forEach(n)
}
,
mindmaps.action.SetChildrenBranchColorAction = function(n) {
    mindmaps.action.CompositeAction.call(this);
    var t = n.branchColor
      , o = this;
    n.forEachDescendant(function(n) {
        o.addAction(new mindmaps.action.SetBranchColorAction(n,t))
    })
}
,
mindmaps.action.SetChildrenBranchColorAction.prototype = new mindmaps.action.CompositeAction;
;function timeit(e, t) {
    var a = (new Date).getTime();
    e();
    var n = (new Date).getTime() - a;
    console.log(t || "", n, "ms")
}
function getBinaryMapWithDepth(e) {
    var r = new mindmaps.MindMap
      , t = r.root;
    !function e(t, a) {
        if (0 !== a) {
            var n = r.createNode();
            n.text.caption = "Node " + n.id,
            t.addChild(n),
            e(n, a - 1);
            var d = r.createNode();
            d.text.caption = "Node " + d.id,
            t.addChild(d),
            e(d, a - 1)
        }
    }(t, e = e || 10),
    t.offset = new mindmaps.Point(400,400);
    var a = t.children.values();
    function d(e, t, a) {
        if (e.offset = new mindmaps.Point(50 * (t + 1),a),
        !e.isLeaf()) {
            var n = e.children.values();
            d(n[0], t + 1, a - a / 2),
            d(n[1], t + 1, a + a / 2)
        }
    }
    return d(a[0], 0, -80),
    d(a[1], 0, 80),
    a[0].branchColor = mindmaps.Util.randomColor(),
    a[0].forEachDescendant(function(e) {
        e.branchColor = mindmaps.Util.randomColor()
    }),
    a[1].branchColor = mindmaps.Util.randomColor(),
    a[1].forEachDescendant(function(e) {
        e.branchColor = mindmaps.Util.randomColor()
    }),
    r
}
function getDefaultTestMap() {
    var e = new mindmaps.MindMap
      , t = e.root
      , a = e.createNode()
      , n = e.createNode()
      , d = e.createNode();
    t.addChild(a),
    t.addChild(n),
    t.addChild(d);
    var r = e.createNode()
      , i = e.createNode();
    n.addChild(r),
    n.addChild(i);
    var o = e.createNode()
      , c = e.createNode()
      , l = e.createNode()
      , m = e.createNode();
    d.addChild(o),
    d.addChild(c),
    d.addChild(l),
    d.addChild(m);
    var s = e.createNode();
    r.addChild(s);
    var u = e.createNode();
    return s.addChild(u),
    e
}
function getDefaultTestDocument() {
    var e = new mindmaps.Document;
    return e.title = "test document",
    e.mindmap = getDefaultTestMap(),
    e
}
function getSimpleMap() {
    var e = new mindmaps.MindMap
      , t = e.root
      , a = e.createNode()
      , n = e.createNode();
    return t.addChild(a),
    t.addChild(n),
    e
}
mindmaps.Util = mindmaps.Util || {},
mindmaps.Util.trackEvent = function(e, t, a) {
    console.log("trackEvent:", e, t, a || ""),
    window.gtag && gtag("event", t, {
        event_category: e,
        event_label: a
    })
}
,
mindmaps.Util.createUUID = function() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(e) {
        var t = 16 * Math.random() | 0;
        return ("x" == e ? t : 3 & t | 8).toString(16)
    })
}
,
mindmaps.Util.getId = function() {
    return mindmaps.Util.createUUID()
}
,
mindmaps.Util.randomColor = function() {
    return e = (~~(Math.random() * (1 << 24))).toString(16),
    "#000000".substr(0, 7 - e.length) + e;
    var e
}
,
mindmaps.Util.getUrlParams = function() {
    for (var e, t = {}, a = /\+/g, n = /([^&=]+)=?([^&]*)/g, d = function(e) {
        return decodeURIComponent(e.replace(a, " "))
    }, r = window.location.search.substring(1); e = n.exec(r); )
        t[d(e[1])] = d(e[2]);
    return t
}
,
mindmaps.Util.distance = function(e, t) {
    return Math.sqrt(e * e + t * t)
}
;
;mindmaps.Point = function(t, n) {
    this.x = t || 0,
    this.y = n || 0
}
,
mindmaps.Point.fromObject = function(t) {
    return new mindmaps.Point(t.x,t.y)
}
,
mindmaps.Point.prototype.clone = function() {
    return new mindmaps.Point(this.x,this.y)
}
,
mindmaps.Point.prototype.add = function(t) {
    this.x += t.x,
    this.y += t.y
}
,
mindmaps.Point.prototype.toString = function() {
    return "{x: " + this.x + " y: " + this.y + "}"
}
;
;mindmaps.Document = function() {
    this.id = mindmaps.Util.createUUID(),
    this.title = "New Document",
    this.mindmap = new mindmaps.MindMap,
    this.dates = {
        created: new Date,
        modified: null
    },
    this.dimensions = new mindmaps.Point(4e3,2e3),
    this.autosave = !1
}
,
mindmaps.Document.fromJSON = function(t) {
    return mindmaps.Document.fromObject(JSON.parse(t))
}
,
mindmaps.Document.fromObject = function(t) {
    var e = new mindmaps.Document;
    return e.id = t.id,
    e.title = t.title,
    e.mindmap = mindmaps.MindMap.fromObject(t.mindmap),
    e.dates = {
        created: new Date(t.dates.created),
        modified: t.dates.modified ? new Date(t.dates.modified) : null
    },
    e.dimensions = mindmaps.Point.fromObject(t.dimensions),
    e.autosave = t.autosave,
    e
}
,
mindmaps.Document.prototype.toJSON = function() {
    var t = {
        created: this.dates.created.getTime()
    };
    return this.dates.modified && (t.modified = this.dates.modified.getTime()),
    {
        id: this.id,
        title: this.title,
        mindmap: this.mindmap,
        dates: t,
        dimensions: this.dimensions,
        autosave: this.autosave
    }
}
,
mindmaps.Document.prototype.serialize = function() {
    return JSON.stringify(this)
}
,
mindmaps.Document.prototype.prepareSave = function() {
    return this.dates.modified = new Date,
    this.title = this.mindmap.getRoot().getCaption(),
    this
}
,
mindmaps.Document.sortByModifiedDateDescending = function(t, e) {
    return t.dates.modified > e.dates.modified ? -1 : t.dates.modified < e.dates.modified ? 1 : 0
}
,
mindmaps.Document.prototype.isNew = function() {
    return null === this.dates.modified
}
,
mindmaps.Document.prototype.getCreatedDate = function() {
    return this.dates.created
}
,
mindmaps.Document.prototype.getWidth = function() {
    return this.dimensions.x
}
,
mindmaps.Document.prototype.getHeight = function() {
    return this.dimensions.y
}
,
mindmaps.Document.prototype.isAutoSave = function() {
    return this.autosave
}
,
mindmaps.Document.prototype.setAutoSave = function(t) {
    this.autosave = t
}
;
;mindmaps.MindMap = function(t) {
    this.nodes = new mindmaps.NodeMap,
    t ? this.root = t : (this.root = new mindmaps.Node,
    this.root.text.font.size = 20,
    this.root.text.font.weight = "bold",
    this.root.text.caption = "Central Idea"),
    this.addNode(this.root)
}
,
mindmaps.MindMap.fromJSON = function(t) {
    return mindmaps.MindMap.fromObject(JSON.parse(t))
}
,
mindmaps.MindMap.fromObject = function(t) {
    var n = mindmaps.Node.fromObject(t.root)
      , o = new mindmaps.MindMap(n);
    return n.forEachDescendant(function(t) {
        o.addNode(t)
    }),
    o
}
,
mindmaps.MindMap.prototype.toJSON = function() {
    return {
        root: this.root
    }
}
,
mindmaps.MindMap.prototype.serialize = function() {
    return JSON.stringify(this)
}
,
mindmaps.MindMap.prototype.createNode = function() {
    var t = new mindmaps.Node;
    return this.addNode(t),
    t
}
,
mindmaps.MindMap.prototype.addNode = function(t) {
    this.nodes.add(t);
    var n = this;
    t.forEachDescendant(function(t) {
        n.nodes.add(t)
    })
}
,
mindmaps.MindMap.prototype.removeNode = function(t) {
    t.parent.removeChild(t);
    var n = this;
    t.forEachDescendant(function(t) {
        n.nodes.remove(t)
    }),
    this.nodes.remove(t)
}
,
mindmaps.MindMap.prototype.getRoot = function() {
    return this.root
}
;
;mindmaps.Node = function() {
    this.id = mindmaps.Util.getId(),
    this.parent = null,
    this.children = new mindmaps.NodeMap,
    this.text = {
        caption: "New Idea",
        font: {
            style: "normal",
            weight: "normal",
            decoration: "none",
            size: 15,
            color: "#000000"
        }
    },
    this.offset = new mindmaps.Point,
    this.foldChildren = !1,
    this.branchColor = "#000000"
}
,
mindmaps.Node.prototype.clone = function() {
    var o = new mindmaps.Node
      , t = {
        caption: this.text.caption
    }
      , n = {
        weight: this.text.font.weight,
        style: this.text.font.style,
        decoration: this.text.font.decoration,
        size: this.text.font.size,
        color: this.text.font.color
    };
    return t.font = n,
    o.text = t,
    o.offset = this.offset.clone(),
    o.foldChildren = this.foldChildren,
    o.branchColor = this.branchColor,
    this.forEachChild(function(t) {
        var n = t.clone();
        o.addChild(n)
    }),
    o
}
,
mindmaps.Node.fromJSON = function(t) {
    return mindmaps.Node.fromObject(JSON.parse(t))
}
,
mindmaps.Node.fromObject = function(t) {
    var o = new mindmaps.Node;
    return o.id = t.id,
    o.text = t.text,
    o.offset = mindmaps.Point.fromObject(t.offset),
    o.foldChildren = t.foldChildren,
    o.branchColor = t.branchColor,
    t.children.forEach(function(t) {
        var n = mindmaps.Node.fromObject(t);
        o.addChild(n)
    }),
    o
}
,
mindmaps.Node.prototype.toJSON = function() {
    var n, t = (n = [],
    this.forEachChild(function(t) {
        n.push(t.toJSON())
    }),
    n);
    return {
        id: this.id,
        parentId: this.parent ? this.parent.id : null,
        text: this.text,
        offset: this.offset,
        foldChildren: this.foldChildren,
        branchColor: this.branchColor,
        children: t
    }
}
,
mindmaps.Node.prototype.serialize = function() {
    return JSON.stringify(this)
}
,
mindmaps.Node.prototype.addChild = function(t) {
    (t.parent = this).children.add(t)
}
,
mindmaps.Node.prototype.removeChild = function(t) {
    t.parent = null,
    this.children.remove(t)
}
,
mindmaps.Node.prototype.isRoot = function() {
    return null === this.parent
}
,
mindmaps.Node.prototype.isLeaf = function() {
    return 0 === this.children.size()
}
,
mindmaps.Node.prototype.getParent = function() {
    return this.parent
}
,
mindmaps.Node.prototype.getRoot = function() {
    for (var t = this; t.parent; )
        t = t.parent;
    return t
}
,
mindmaps.Node.prototype.getPosition = function() {
    for (var t = this.offset.clone(), n = this.parent; n; )
        t.add(n.offset),
        n = n.parent;
    return t
}
,
mindmaps.Node.prototype.getDepth = function() {
    for (var t = this.parent, n = 0; t; )
        n++,
        t = t.parent;
    return n
}
,
mindmaps.Node.prototype.getChildren = function(n) {
    var o = [];
    return this.children.each(function(t) {
        n && t.getChildren(!0).forEach(function(t) {
            o.push(t)
        });
        o.push(t)
    }),
    o
}
,
mindmaps.Node.prototype.forEachChild = function(t) {
    this.children.each(t)
}
,
mindmaps.Node.prototype.forEachDescendant = function(n) {
    this.children.each(function(t) {
        n(t),
        t.forEachDescendant(n)
    })
}
,
mindmaps.Node.prototype.setCaption = function(t) {
    this.text.caption = t
}
,
mindmaps.Node.prototype.getCaption = function() {
    return this.text.caption
}
,
mindmaps.Node.prototype.isDescendant = function(d) {
    return function t(n) {
        for (var o = n.children.values(), e = 0, i = o.length; e < i; e++) {
            var r = o[e];
            if (t(r))
                return !0;
            if (r === d)
                return !0
        }
        return !1
    }(this)
}
;
;mindmaps.NodeMap = function() {
    this.nodes = {},
    this.count = 0
}
,
mindmaps.NodeMap.prototype.get = function(t) {
    return this.nodes[t]
}
,
mindmaps.NodeMap.prototype.add = function(t) {
    return !this.nodes.hasOwnProperty(t.id) && (this.nodes[t.id] = t,
    this.count++,
    !0)
}
,
mindmaps.NodeMap.prototype.remove = function(t) {
    return !!this.nodes.hasOwnProperty(t.id) && (delete this.nodes[t.id],
    this.count--,
    !0)
}
,
mindmaps.NodeMap.prototype.size = function() {
    return this.count
}
,
mindmaps.NodeMap.prototype.values = function() {
    return Object.keys(this.nodes).map(function(t) {
        return this.nodes[t]
    }, this)
}
,
mindmaps.NodeMap.prototype.each = function(t) {
    for (var n in this.nodes)
        t(this.nodes[n])
}
;
;function UndoManager(t) {
    this.maxStackSize = t || 64;
    var i = "undo"
      , n = "redo"
      , e = this
      , r = new UndoManager.CircularStack(this.maxStackSize)
      , o = new UndoManager.CircularStack(this.maxStackSize)
      , a = !1
      , u = null
      , s = null
      , h = function() {
        e.stateChanged && e.stateChanged()
    }
      , c = function(t) {
        switch (u = t,
        a = !0,
        s) {
        case i:
            t.undo();
            break;
        case n:
            t.redo()
        }
        a = !1
    };
    this.addUndo = function(t, n) {
        if (a)
            null == u.redo && s == i && (u.redo = t);
        else {
            var e = {
                undo: t,
                redo: n
            };
            r.push(e),
            o.clear(),
            h()
        }
    }
    ,
    this.undo = function() {
        if (this.canUndo()) {
            s = i;
            var t = r.pop();
            c(t),
            t.redo && o.push(t),
            h()
        }
    }
    ,
    this.redo = function() {
        if (this.canRedo()) {
            s = n;
            var t = o.pop();
            c(t),
            t.undo && r.push(t),
            h()
        }
    }
    ,
    this.canUndo = function() {
        return !r.isEmpty()
    }
    ,
    this.canRedo = function() {
        return !o.isEmpty()
    }
    ,
    this.reset = function() {
        r.clear(),
        o.clear(),
        a = !1,
        s = u = null,
        h()
    }
    ,
    this.stateChanged = function() {}
}
UndoManager.CircularStack = function(t) {
    this.maxSize = t || 32,
    this.buffer = [],
    this.nextPointer = 0
}
,
UndoManager.CircularStack.prototype.push = function(t) {
    this.buffer[this.nextPointer] = t,
    this.nextPointer = (this.nextPointer + 1) % this.maxSize
}
,
UndoManager.CircularStack.prototype.isEmpty = function() {
    if (0 === this.buffer.length)
        return !0;
    var t = this.getPreviousPointer();
    return null === t || null === this.buffer[t]
}
,
UndoManager.CircularStack.prototype.getPreviousPointer = function() {
    return 0 < this.nextPointer ? this.nextPointer - 1 : this.buffer.length < this.maxSize ? null : this.maxSize - 1
}
,
UndoManager.CircularStack.prototype.clear = function() {
    this.buffer.length = 0,
    this.nextPointer = 0
}
,
UndoManager.CircularStack.prototype.pop = function() {
    if (this.isEmpty())
        return null;
    var t = this.getPreviousPointer()
      , n = this.buffer[t];
    return this.buffer[t] = null,
    this.nextPointer = t,
    n
}
,
UndoManager.CircularStack.prototype.peek = function() {
    return this.isEmpty() ? null : this.buffer[this.getPreviousPointer()]
}
;
;mindmaps.UndoController = function(n, d) {
    this.init = function() {
        this.undoManager = new UndoManager(128),
        this.undoManager.stateChanged = this.undoStateChanged.bind(this),
        this.undoCommand = d.get(mindmaps.UndoCommand),
        this.undoCommand.setHandler(this.doUndo.bind(this)),
        this.redoCommand = d.get(mindmaps.RedoCommand),
        this.redoCommand.setHandler(this.doRedo.bind(this)),
        n.subscribe(mindmaps.Event.DOCUMENT_OPENED, this.documentOpened.bind(this)),
        n.subscribe(mindmaps.Event.DOCUMENT_CLOSED, this.documentClosed.bind(this))
    }
    ,
    this.undoStateChanged = function() {
        this.undoCommand.setEnabled(this.undoManager.canUndo()),
        this.redoCommand.setEnabled(this.undoManager.canRedo())
    }
    ,
    this.addUndo = function(n, d) {
        this.undoManager.addUndo(n, d)
    }
    ,
    this.doUndo = function() {
        this.undoManager.undo()
    }
    ,
    this.doRedo = function() {
        this.undoManager.redo()
    }
    ,
    this.documentOpened = function() {
        this.undoManager.reset(),
        this.undoStateChanged()
    }
    ,
    this.documentClosed = function() {
        this.undoManager.reset(),
        this.undoStateChanged()
    }
    ,
    this.init()
}
;
;mindmaps.ClipboardController = function(e, n, d) {
    var t, s, a, o;
    function l() {
        t = d.selectedNode.clone(),
        o.setEnabled(!0)
    }
    function m() {
        l()
    }
    function i() {
        l(),
        d.deleteNode(d.selectedNode)
    }
    function c() {
        t && d.createNode(t.clone(), d.selectedNode)
    }
    (s = n.get(mindmaps.CopyNodeCommand)).setHandler(m),
    (a = n.get(mindmaps.CutNodeCommand)).setHandler(i),
    (o = n.get(mindmaps.PasteNodeCommand)).setHandler(c),
    o.setEnabled(!1),
    e.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
        s.setEnabled(!1),
        a.setEnabled(!1),
        o.setEnabled(!1)
    }),
    e.subscribe(mindmaps.Event.DOCUMENT_OPENED, function() {
        s.setEnabled(!0),
        a.setEnabled(!0),
        o.setEnabled(null != t)
    })
}
;
;mindmaps.ZoomController = function(t, o) {
    var i = this;
    this.ZOOM_STEP = .25,
    this.MAX_ZOOM = 3,
    this.MIN_ZOOM = .25,
    this.DEFAULT_ZOOM = 1,
    this.zoomFactor = this.DEFAULT_ZOOM,
    this.zoomTo = function(o) {
        o <= this.MAX_ZOOM && o >= this.MIN_ZOOM && (this.zoomFactor = o,
        t.publish(mindmaps.Event.ZOOM_CHANGED, o))
    }
    ,
    this.zoomIn = function() {
        return this.zoomFactor += this.ZOOM_STEP,
        this.zoomFactor > this.MAX_ZOOM ? this.zoomFactor -= this.ZOOM_STEP : t.publish(mindmaps.Event.ZOOM_CHANGED, this.zoomFactor),
        this.zoomFactor
    }
    ,
    this.zoomOut = function() {
        return this.zoomFactor -= this.ZOOM_STEP,
        this.zoomFactor < this.MIN_ZOOM ? this.zoomFactor += this.ZOOM_STEP : t.publish(mindmaps.Event.ZOOM_CHANGED, this.zoomFactor),
        this.zoomFactor
    }
    ,
    t.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function(o) {
        i.zoomTo(i.DEFAULT_ZOOM)
    })
}
;
;mindmaps.ShortcutController = function() {
    function o(t, n) {
        return (n = n || "keydown") + "." + t
    }
    this.shortcuts = {},
    this.register = function(t, n, r) {
        Array.isArray(t) || (t = [t]);
        t.forEach(function(t) {
            r = o(t, r),
            $(document).bind(r, t, function(t) {
                return t.stopImmediatePropagation(),
                t.stopPropagation(),
                t.preventDefault(),
                n(),
                !1
            })
        })
    }
    ,
    this.unregister = function(t, n) {
        n = o(t, n),
        $(document).unbind(n),
        delete this.shortcuts[n]
    }
    ,
    this.unregisterAll = function() {
        for (var t in shortcuts)
            $(document).unbind(t)
    }
}
;
;mindmaps.HelpController = function(l, t) {
    !function() {
        if (h())
            console.debug("skipping tutorial");
        else {
            var o, t, e = [];
            l.once(mindmaps.Event.DOCUMENT_OPENED, function() {
                setTimeout(i, 1e3)
            })
        }
        function i() {
            o = new mindmaps.Notification("#toolbar",{
                position: "bottomMiddle",
                maxWidth: 550,
                title: "Welcome to mindmaps",
                content: "Hello there, it seems like you are new here! These bubbles will guide you through the app. Or they won't if you want to skip this tutorial and <a class='skip-tutorial link'>click here<a/>."
            }),
            e.push(o),
            o.$().find("a.skip-tutorial").click(function() {
                e.forEach(function(t) {
                    t.close()
                }),
                d()
            }),
            setTimeout(n, 2e3)
        }
        function n() {
            h() || (t = new mindmaps.Notification(".node-caption.root",{
                position: "bottomMiddle",
                closeButton: !0,
                maxWidth: 350,
                title: "This is where you start - your main idea",
                content: "Double click the idea to change what it says. This will be the main topic of your mind map."
            }),
            e.push(t),
            l.once(mindmaps.Event.NODE_TEXT_CAPTION_CHANGED, function() {
                t.close(),
                setTimeout(a, 900)
            }))
        }
        function a() {
            if (!h()) {
                var t = new mindmaps.Notification(".node-caption.root",{
                    position: "bottomMiddle",
                    closeButton: !0,
                    maxWidth: 350,
                    padding: 20,
                    title: "Creating new ideas",
                    content: "Now it's time to build your mind map.<br/> Move your mouse over the idea, click and then drag the <span style='color:red'>red circle</span> away from the root. This is how you create a new branch."
                });
                e.push(t),
                l.once(mindmaps.Event.NODE_CREATED, function() {
                    o.close(),
                    t.close(),
                    setTimeout(s, 900)
                })
            }
        }
        function s() {
            if (!h()) {
                var t = new mindmaps.Notification(".node-container.root > .node-container:first",{
                    position: "bottomMiddle",
                    closeButton: !0,
                    maxWidth: 350,
                    title: "Your first branch",
                    content: "Great! This is easy, right? The red circle is your most important tool. Now, you can move your idea around by dragging it or double click to change the text again."
                });
                e.push(t),
                setTimeout(c, 2e3),
                l.once(mindmaps.Event.NODE_MOVED, function() {
                    t.close(),
                    setTimeout(r, 0),
                    setTimeout(u, 15e3),
                    setTimeout(m, 1e4),
                    setTimeout(d, 2e4)
                })
            }
        }
        function r() {
            if (!h()) {
                var t = new mindmaps.Notification(".float-panel:has(#navigator)",{
                    position: "bottomRight",
                    closeButton: !0,
                    maxWidth: 350,
                    expires: 1e4,
                    title: "Navigation",
                    content: "You can click and drag the background of the map to move around. Use your mousewheel or slider over there to zoom in and out."
                });
                e.push(t)
            }
        }
        function c() {
            if (!h()) {
                var t = new mindmaps.Notification("#inspector",{
                    position: "leftBottom",
                    closeButton: !0,
                    maxWidth: 350,
                    padding: 20,
                    title: "Don't like the colors?",
                    content: "Use these controls to change the appearance of your ideas. Try clicking the icon in the upper right corner to minimize this panel."
                });
                e.push(t)
            }
        }
        function u() {
            if (!h()) {
                var t = new mindmaps.Notification("#toolbar .buttons-left",{
                    position: "bottomLeft",
                    closeButton: !0,
                    maxWidth: 350,
                    padding: 20,
                    title: "The tool bar",
                    content: "Those buttons do what they say. You can use them or work with keyboard shortcuts. Hover over the buttons for the key combinations."
                });
                e.push(t)
            }
        }
        function m() {
            if (!h()) {
                var t = new mindmaps.Notification("#toolbar .buttons-right",{
                    position: "leftTop",
                    closeButton: !0,
                    maxWidth: 350,
                    title: "Save your work",
                    content: "The button to the right opens a menu where you can save your mind map or start working on another one if you like."
                });
                e.push(t)
            }
        }
        function h() {
            return 1 == mindmaps.LocalStorage.get("mindmaps.tutorial.done")
        }
        function d() {
            mindmaps.LocalStorage.put("mindmaps.tutorial.done", 1)
        }
    }(),
    function() {
        t.get(mindmaps.HelpCommand).setHandler(function() {
            if (n.some(function(t) {
                return t.isVisible()
            }))
                return n.forEach(function(t) {
                    t.close()
                }),
                void (n.length = 0);
            var t = new mindmaps.Notification(".node-caption.root",{
                position: "bottomLeft",
                closeButton: !0,
                maxWidth: 350,
                title: "This is your main idea",
                content: "Double click an idea to edit its text. Move the mouse over an idea and drag the red circle to create a new idea."
            })
              , o = new mindmaps.Notification("#navigator",{
                position: "leftTop",
                closeButton: !0,
                maxWidth: 350,
                padding: 20,
                title: "This is the navigator",
                content: "Use this panel to get an overview of your map. You can navigate around by dragging the red rectangle or change the zoom by clicking on the magnifier buttons."
            })
              , e = new mindmaps.Notification("#inspector",{
                position: "leftTop",
                closeButton: !0,
                maxWidth: 350,
                padding: 20,
                title: "This is the inspector",
                content: "Use these controls to change the appearance of your ideas. Try clicking the icon in the upper right corner to minimize this panel."
            })
              , i = new mindmaps.Notification("#toolbar .buttons-left",{
                position: "bottomLeft",
                closeButton: !0,
                maxWidth: 350,
                title: "This is your toolbar",
                content: "Those buttons do what they say. You can use them or work with keyboard shortcuts. Hover over the buttons for the key combinations."
            });
            n.push(t, o, e, i)
        });
        var n = []
    }()
}
;
;mindmaps.FloatPanelFactory = function(o) {
    var h = o.getContent()
      , a = []
      , r = 15
      , d = 5;
    this.create = function(t, i) {
        var e = new mindmaps.FloatPanel(t,h,i);
        return function(t) {
            o.subscribe(mindmaps.CanvasContainer.Event.RESIZED, function() {
                a.forEach(function(t) {
                    t.visible && t.ensurePosition()
                })
            });
            var i = h.outerWidth()
              , e = h.offset().top
              , n = t.width()
              , s = (t.height(),
            a.reduce(function(t, i) {
                return t + i.height() + d
            }, 0));
            t.setPosition(i - n - r, e + d + s)
        }(e),
        a.push(e),
        e
    }
}
,
mindmaps.FloatPanel = function(t, r, i) {
    var e, n = this, h = !1;
    this.caption = t,
    this.visible = !1,
    this.animationDuration = 400,
    this.setContent = function(t) {
        this.clearContent(),
        $("div.ui-dialog-content", this.$widget).append(t)
    }
    ,
    this.clearContent = function() {
        $("div.ui-dialog-content", this.$widget).children().detach()
    }
    ,
    this.$widget = ((e = $("#template-float-panel").tmpl({
        title: t
    })).find(".ui-dialog-titlebar-close").click(function() {
        n.hide()
    }),
    i && e.find(".ui-dialog-content").append(i),
    e.draggable({
        containment: "parent",
        handle: "div.ui-dialog-titlebar",
        opacity: .75
    }).hide().appendTo(r),
    e),
    this.hide = function() {
        !h && this.visible && (this.visible = !1,
        this.$widget.fadeOut(1.5 * this.animationDuration),
        this.$hideTarget && this.transfer(this.$widget, this.$hideTarget))
    }
    ,
    this.show = function() {
        h || this.visible || (this.visible = !0,
        this.$widget.fadeIn(1.5 * this.animationDuration),
        this.ensurePosition(),
        this.$hideTarget && this.transfer(this.$hideTarget, this.$widget))
    }
    ,
    this.toggle = function() {
        this.visible ? this.hide() : this.show()
    }
    ,
    this.transfer = function(t, i) {
        h = !0;
        var e = i.offset()
          , n = {
            top: e.top,
            left: e.left,
            height: i.innerHeight(),
            width: i.innerWidth()
        }
          , s = t.offset()
          , o = $('<div class="ui-effects-transfer"></div>').appendTo(document.body).css({
            top: s.top,
            left: s.left,
            height: t.innerHeight(),
            width: t.innerWidth(),
            position: "absolute"
        }).animate(n, this.animationDuration, "linear", function() {
            o.remove(),
            h = !1
        })
    }
    ,
    this.width = function() {
        return this.$widget.outerWidth()
    }
    ,
    this.height = function() {
        return this.$widget.outerHeight()
    }
    ,
    this.offset = function() {
        return this.$widget.offset()
    }
    ,
    this.setPosition = function(t, i) {
        this.$widget.offset({
            left: t,
            top: i
        })
    }
    ,
    this.ensurePosition = function() {
        var t = r.outerWidth()
          , i = r.outerHeight()
          , e = r.offset().left
          , n = r.offset().top
          , s = this.width()
          , o = this.height()
          , h = this.offset().left
          , a = this.offset().top;
        t + e < s + h && s <= t && this.setPosition(t + e - s, a),
        i + n < o + a && o <= i && this.setPosition(h, i + n - o)
    }
    ,
    this.setHideTarget = function(t) {
        this.$hideTarget = t
    }
}
;
;mindmaps.NavigatorView = function() {
    var e = this
      , t = $("#template-navigator").tmpl()
      , n = t.children(".active").hide()
      , i = t.children(".inactive").hide()
      , o = $("#navi-canvas-overlay", t)
      , c = $("#navi-canvas", t);
    this.getContent = function() {
        return t
    }
    ,
    this.showActiveContent = function() {
        i.hide(),
        n.show()
    }
    ,
    this.showInactiveContent = function() {
        n.hide(),
        i.show()
    }
    ,
    this.setDraggerSize = function(n, t) {
        o.css({
            width: n,
            height: t
        })
    }
    ,
    this.setDraggerPosition = function(n, t) {
        o.css({
            left: n,
            top: t
        })
    }
    ,
    this.setCanvasHeight = function(n) {
        $("#navi-canvas", t).css({
            height: n
        })
    }
    ,
    this.getCanvasWidth = function() {
        return $("#navi-canvas", t).width()
    }
    ,
    this.init = function(n) {
        $("#navi-slider", t).slider({
            min: 0,
            max: 11,
            step: 1,
            value: 3,
            slide: function(n, t) {
                e.sliderChanged && e.sliderChanged(t.value)
            }
        }),
        $("#button-navi-zoom-in", t).button({
            text: !1,
            icons: {
                primary: "ui-icon-zoomin"
            }
        }).click(function() {
            e.buttonZoomInClicked && e.buttonZoomInClicked()
        }),
        $("#button-navi-zoom-out", t).button({
            text: !1,
            icons: {
                primary: "ui-icon-zoomout"
            }
        }).click(function() {
            e.buttonZoomOutClicked && e.buttonZoomOutClicked()
        }),
        o.draggable({
            containment: "parent",
            start: function(n, t) {
                e.dragStart && e.dragStart()
            },
            drag: function(n, t) {
                if (e.dragging) {
                    var i = t.position.left
                      , o = t.position.top;
                    e.dragging(i, o)
                }
            },
            stop: function(n, t) {
                e.dragStop && e.dragStop()
            }
        })
    }
    ,
    this.draw = function(n, t) {
        var i = n.root
          , o = c[0]
          , e = o.width
          , a = o.height
          , s = o.getContext("2d");
        function r(n) {
            return n / t
        }
        s.clearRect(0, 0, e, a),
        s.lineWidth = 1.8,
        function a(n, t, i) {
            s.save();
            s.translate(t, i);
            n.collapseChildren || n.forEachChild(function(n) {
                s.beginPath(),
                s.strokeStyle = n.branchColor,
                s.moveTo(0, 0);
                var t = r(n.offset.x)
                  , i = r(n.offset.y);
                if (t < 0)
                    var o = t + 5
                      , e = t;
                else
                    var o = t
                      , e = t + 5;
                s.lineTo(o, i),
                s.lineTo(e, i),
                s.stroke(),
                a(n, e, i)
            });
            s.restore()
        }(i, e / 2, a / 2),
        s.fillRect(e / 2 - 4, a / 2 - 2, 8, 4)
    }
    ,
    this.showZoomLevel = function(n) {
        $("#navi-zoom-level").text(n)
    }
    ,
    this.setSliderValue = function(n) {
        $("#navi-slider").slider("value", n)
    }
}
,
mindmaps.NavigatorPresenter = function(n, e, t, i) {
    var a = t.getContent()
      , s = !1
      , r = i.DEFAULT_ZOOM
      , c = new mindmaps.Point
      , u = null
      , l = null;
    function d() {
        var n = a.width() / r
          , t = a.height() / r
          , i = n * c.x / u.x
          , o = t * c.y / u.y;
        i > c.x && (i = c.x),
        o > c.y && (o = c.y),
        e.setDraggerSize(i, o)
    }
    function v() {
        var n = a.scrollLeft() / r
          , t = a.scrollTop() / r
          , i = n * c.x / u.x
          , o = t * c.y / u.y;
        e.setDraggerPosition(i, o)
    }
    function h() {
        var n = 100 * r + " %";
        e.showZoomLevel(n)
    }
    function m() {
        var n = r / i.ZOOM_STEP - 1;
        e.setSliderValue(n)
    }
    function f() {
        var n = u.x / c.x;
        e.draw(l, n)
    }
    e.dragStart = function() {
        s = !0
    }
    ,
    e.dragging = function(n, t) {
        var i = r * u.x * n / c.x
          , o = r * u.y * t / c.y;
        a.scrollLeft(i).scrollTop(o)
    }
    ,
    e.dragStop = function() {
        s = !1
    }
    ,
    e.buttonZoomInClicked = function() {
        i.zoomIn()
    }
    ,
    e.buttonZoomOutClicked = function() {
        i.zoomOut()
    }
    ,
    e.sliderChanged = function(n) {
        i.zoomTo((n + 1) * i.ZOOM_STEP)
    }
    ,
    t.subscribe(mindmaps.CanvasContainer.Event.RESIZED, function() {
        u && d()
    }),
    n.subscribe(mindmaps.Event.DOCUMENT_OPENED, function(n) {
        var t, i, o;
        u = n.dimensions,
        l = n.mindmap,
        t = e.getCanvasWidth(),
        i = u.x / t,
        o = u.y / i,
        e.setCanvasHeight(o),
        c.x = t,
        c.y = o,
        v(),
        d(),
        h(),
        m(),
        f(),
        e.showActiveContent(),
        a.bind("scroll.navigator-view", function() {
            s || v()
        })
    }),
    n.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
        l = u = null,
        r = 1,
        a.unbind("scroll.navigator-view"),
        e.showInactiveContent()
    }),
    n.subscribe(mindmaps.Event.NODE_MOVED, f),
    n.subscribe(mindmaps.Event.NODE_BRANCH_COLOR_CHANGED, f),
    n.subscribe(mindmaps.Event.NODE_CREATED, f),
    n.subscribe(mindmaps.Event.NODE_DELETED, f),
    n.subscribe(mindmaps.Event.NODE_OPENED, f),
    n.subscribe(mindmaps.Event.NODE_CLOSED, f),
    n.subscribe(mindmaps.Event.ZOOM_CHANGED, function(n) {
        r = n,
        v(),
        d(),
        h(),
        m()
    }),
    this.go = function() {
        e.init(),
        e.showInactiveContent()
    }
}
;
;mindmaps.InspectorView = function() {
    var o = this
      , e = $("#template-inspector").tmpl()
      , n = $("#inspector-button-font-size-decrease", e)
      , t = $("#inspector-button-font-size-increase", e)
      , c = $("#inspector-checkbox-font-bold", e)
      , i = $("#inspector-checkbox-font-italic", e)
      , r = $("#inspector-checkbox-font-underline", e)
      , l = $("#inspector-checkbox-font-linethrough", e)
      , s = $("#inspector-button-branch-color-children", e)
      , d = $("#inspector-branch-color-picker", e)
      , a = $("#inspector-font-color-picker", e)
      , h = [n, t, c, i, r, l, s]
      , u = [d, a];
    this.getContent = function() {
        return e
    }
    ,
    this.setControlsEnabled = function(o) {
        var n = o ? "enable" : "disable";
        h.forEach(function(e) {
            e.button(n)
        }),
        u.forEach(function(e) {
            e.miniColors("disabled", !o)
        })
    }
    ,
    this.setBoldCheckboxState = function(e) {
        c.prop("checked", e).button("refresh")
    }
    ,
    this.setItalicCheckboxState = function(e) {
        i.prop("checked", e).button("refresh")
    }
    ,
    this.setUnderlineCheckboxState = function(e) {
        r.prop("checked", e).button("refresh")
    }
    ,
    this.setLinethroughCheckboxState = function(e) {
        l.prop("checked", e).button("refresh")
    }
    ,
    this.setBranchColorPickerColor = function(e) {
        d.miniColors("value", e)
    }
    ,
    this.setFontColorPickerColor = function(e) {
        a.miniColors("value", e)
    }
    ,
    this.init = function() {
        $(".buttonset", e).buttonset(),
        s.button(),
        n.click(function() {
            o.fontSizeDecreaseButtonClicked && o.fontSizeDecreaseButtonClicked()
        }),
        t.click(function() {
            o.fontSizeIncreaseButtonClicked && o.fontSizeIncreaseButtonClicked()
        }),
        c.click(function() {
            if (o.fontBoldCheckboxClicked) {
                var e = $(this).prop("checked");
                o.fontBoldCheckboxClicked(e)
            }
        }),
        i.click(function() {
            if (o.fontItalicCheckboxClicked) {
                var e = $(this).prop("checked");
                o.fontItalicCheckboxClicked(e)
            }
        }),
        r.click(function() {
            if (o.fontUnderlineCheckboxClicked) {
                var e = $(this).prop("checked");
                o.fontUnderlineCheckboxClicked(e)
            }
        }),
        l.click(function() {
            if (o.fontLinethroughCheckboxClicked) {
                var e = $(this).prop("checked");
                o.fontLinethroughCheckboxClicked(e)
            }
        }),
        d.miniColors({
            hide: function(e) {
                this.attr("disabled") || (console.log("hide branch", e),
                o.branchColorPicked && o.branchColorPicked(e))
            },
            move: function(e) {
                o.branchColorPreview && o.branchColorPreview(e)
            }
        }),
        a.miniColors({
            hide: function(e) {
                this.attr("disabled") || (console.log("font", e),
                o.fontColorPicked && o.fontColorPicked(e))
            },
            move: function(e) {
                o.fontColorPreview && o.fontColorPreview(e)
            }
        }),
        s.click(function() {
            o.branchColorChildrenButtonClicked && o.branchColorChildrenButtonClicked()
        })
    }
}
,
mindmaps.InspectorPresenter = function(o, n, t) {
    function c(e) {
        var o = e.text.font;
        t.setBoldCheckboxState("bold" === o.weight),
        t.setItalicCheckboxState("italic" === o.style),
        t.setUnderlineCheckboxState("underline" === o.decoration),
        t.setLinethroughCheckboxState("line-through" === o.decoration),
        t.setFontColorPickerColor(o.color),
        t.setBranchColorPickerColor(e.branchColor)
    }
    t.fontSizeDecreaseButtonClicked = function() {
        var e = new mindmaps.action.DecreaseNodeFontSizeAction(n.selectedNode);
        n.executeAction(e)
    }
    ,
    t.fontSizeIncreaseButtonClicked = function() {
        var e = new mindmaps.action.IncreaseNodeFontSizeAction(n.selectedNode);
        n.executeAction(e)
    }
    ,
    t.fontBoldCheckboxClicked = function(e) {
        var o = new mindmaps.action.SetFontWeightAction(n.selectedNode,e);
        n.executeAction(o)
    }
    ,
    t.fontItalicCheckboxClicked = function(e) {
        var o = new mindmaps.action.SetFontStyleAction(n.selectedNode,e);
        n.executeAction(o)
    }
    ,
    t.fontUnderlineCheckboxClicked = function(e) {
        var o = new mindmaps.action.SetFontDecorationAction(n.selectedNode,e ? "underline" : "none");
        n.executeAction(o)
    }
    ,
    t.fontLinethroughCheckboxClicked = function(e) {
        var o = new mindmaps.action.SetFontDecorationAction(n.selectedNode,e ? "line-through" : "none");
        n.executeAction(o)
    }
    ,
    t.branchColorPicked = function(e) {
        var o = new mindmaps.action.SetBranchColorAction(n.selectedNode,e);
        n.executeAction(o)
    }
    ,
    t.branchColorPreview = function(e) {
        o.publish(mindmaps.Event.NODE_BRANCH_COLOR_PREVIEW, n.selectedNode, e)
    }
    ,
    t.fontColorPicked = function(e) {
        var o = new mindmaps.action.SetFontColorAction(n.selectedNode,e);
        n.executeAction(o)
    }
    ,
    t.fontColorPreview = function(e) {
        o.publish(mindmaps.Event.NODE_FONT_COLOR_PREVIEW, n.selectedNode, e)
    }
    ,
    t.branchColorChildrenButtonClicked = function() {
        var e = new mindmaps.action.SetChildrenBranchColorAction(n.selectedNode);
        n.executeAction(e)
    }
    ,
    o.subscribe(mindmaps.Event.NODE_FONT_CHANGED, function(e) {
        n.selectedNode === e && c(e)
    }),
    o.subscribe(mindmaps.Event.NODE_BRANCH_COLOR_CHANGED, function(e) {
        n.selectedNode === e && c(e)
    }),
    o.subscribe(mindmaps.Event.NODE_SELECTED, function(e) {
        c(e)
    }),
    o.subscribe(mindmaps.Event.DOCUMENT_OPENED, function() {
        t.setControlsEnabled(!0)
    }),
    o.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
        t.setControlsEnabled(!1)
    }),
    this.go = function() {
        t.init()
    }
}
;
;mindmaps.ToolBarView = function() {
    this.init = function() {}
    ,
    this.addButton = function(n, t) {
        t(n.asJquery())
    }
    ,
    this.addButtonGroup = function(n, t) {
        var o = $("<span/>");
        n.forEach(function(n) {
            o.append(n.asJquery())
        }),
        o.buttonset(),
        t(o)
    }
    ,
    this.addMenu = function(n) {
        this.alignRight(n.getContent())
    }
    ,
    this.alignLeft = function(n) {
        n.appendTo("#toolbar .buttons-left")
    }
    ,
    this.alignRight = function(n) {
        n.appendTo("#toolbar .buttons-right")
    }
}
,
mindmaps.ToolBarButton = function(n) {
    this.command = n;
    var t = this;
    n.subscribe(mindmaps.Command.Event.ENABLED_CHANGED, function(n) {
        t.setEnabled && t.setEnabled(n)
    })
}
,
mindmaps.ToolBarButton.prototype.isEnabled = function() {
    return this.command.enabled
}
,
mindmaps.ToolBarButton.prototype.click = function() {
    this.command.execute()
}
,
mindmaps.ToolBarButton.prototype.getTitle = function() {
    return this.command.label
}
,
mindmaps.ToolBarButton.prototype.getToolTip = function() {
    var n = this.command.description
      , t = this.command.shortcut;
    return t && (Array.isArray(t) && (t = t.join(", ")),
    n += " [" + t.toUpperCase() + "]"),
    n
}
,
mindmaps.ToolBarButton.prototype.getId = function() {
    return "button-" + this.command.id
}
,
mindmaps.ToolBarButton.prototype.asJquery = function() {
    var n = this
      , t = $("<button/>", {
        id: this.getId(),
        title: this.getToolTip(),
        class : 'btn btn-default'
    }).click(function() {
        n.click()
    }).button({
        label: this.getTitle(),
        disabled: !this.isEnabled()
    })
      , o = this.command.icon;
    return o && t.button({
        icons: {
            primary: o
        }
        
    }),
    this.setEnabled = function(n) {
        t.button(n ? "enable" : "disable")
    }
    ,
    t
}
,
mindmaps.ToolBarMenu = function(n, t) {
    var o = this;
    this.$menuWrapper = $("<span/>", {
        class: "menu-wrapper"
    }).hover(function() {
        o.$menu.show()
    }, function() {
        o.$menu.hide()
    }),
    this.$menuButton = $("<button/>").button({
        label: n,
        icons: {
            primary: t,
            secondary: "ui-icon-triangle-1-s"
        }
    }).appendTo(this.$menuWrapper),
    this.$menu = $("<div/>", {
        class: "menu"
    }).click(function() {
        o.$menu.hide()
    }).appendTo(this.$menuWrapper),
    this.add = function(n) {
        Array.isArray(n) || (n = [n]),
        n.forEach(function(n) {
            var t = n.asJquery().removeClass("ui-corner-all").addClass("menu-item");
            this.$menu.append(t)
        }, this),
        this.$menu.children().last().addClass("ui-corner-bottom").prev().removeClass("ui-corner-bottom")
    }
    ,
    this.getContent = function() {
        return this.$menuWrapper
    }
}
,
mindmaps.ToolBarPresenter = function(n, o, t, i) {
    function a(n) {
        var t = o.get(n);
        return new mindmaps.ToolBarButton(t)
    }
    function e(n) {
        return n.map(a)
    }
    var m = e([mindmaps.CreateNodeCommand, mindmaps.DeleteNodeCommand]);
    t.addButtonGroup(m, t.alignLeft);
    var d = e([mindmaps.UndoCommand, mindmaps.RedoCommand]);
    t.addButtonGroup(d, t.alignLeft);
    var s = e([mindmaps.CopyNodeCommand, mindmaps.CutNodeCommand, mindmaps.PasteNodeCommand]);
    t.addButtonGroup(s, t.alignLeft);
    var r = new mindmaps.ToolBarMenu("Mind map","ui-icon-document")
      , u = e([mindmaps.NewDocumentCommand, mindmaps.OpenDocumentCommand, mindmaps.SaveDocumentCommand, mindmaps.ExportCommand, mindmaps.PrintCommand, mindmaps.CloseDocumentCommand]);
    r.add(u),
    t.addMenu(r),
    t.addButton(a(mindmaps.HelpCommand), t.alignRight),
    this.go = function() {
        t.init()
    }
}
;
;mindmaps.StatusBarView = function() {
    var i = this
      , o = $("#statusbar");
    this.init = function() {}
    ,
    this.createButton = function(t, n) {
        return $("<button/>", {
            id: "statusbar-button-" + t
        }).button({
            label: n
        }).click(function() {
            i.buttonClicked && i.buttonClicked(t)
        }).prependTo(o.find(".buttons"))
    }
    ,
    this.getContent = function() {
        return o
    }
}
,
mindmaps.StatusBarPresenter = function(t, o) {
    var e = 0
      , s = {};
    new mindmaps.StatusNotificationController(t,o.getContent());
    o.buttonClicked = function(t) {
        s[t].toggle()
    }
    ,
    this.go = function() {
        o.init()
    }
    ,
    this.addEntry = function(t) {
        var n = e++
          , i = o.createButton(n, t.caption);
        t.setHideTarget(i),
        s[n] = t
    }
}
,
mindmaps.StatusNotificationController = function(t, n) {
    var i = $("<div class='notification-anchor'/>").css({
        position: "absolute",
        right: 20
    }).appendTo(n);
    t.subscribe(mindmaps.Event.DOCUMENT_SAVED, function() {
        new mindmaps.Notification(i,{
            position: "topRight",
            expires: 3500,
            content: "Mind map saved"
        })
    }),
    t.subscribe(mindmaps.Event.NOTIFICATION_INFO, function(t) {
        new mindmaps.Notification(i,{
            position: "topRight",
            content: t,
            expires: 3500,
            type: "info"
        })
    }),
    t.subscribe(mindmaps.Event.NOTIFICATION_WARN, function(t) {
        new mindmaps.Notification(i,{
            position: "topRight",
            title: "Warning",
            content: t,
            expires: 4e3,
            type: "warn"
        })
    }),
    t.subscribe(mindmaps.Event.NOTIFICATION_ERROR, function(t) {
        new mindmaps.Notification(i,{
            position: "topRight",
            title: "Error",
            content: t,
            expires: 4500,
            type: "error"
        })
    })
}
;
;mindmaps.CanvasDrawingUtil = {
    getLineWidth: function(i, t) {
        var e = i * (12 - 2 * t);
        return e < 2 && (e = 2),
        e
    },
    roundedRect: function(i, t, e, n, a, r) {
        i.beginPath(),
        i.moveTo(t, e + r),
        i.lineTo(t, e + a - r),
        i.quadraticCurveTo(t, e + a, t + r, e + a),
        i.lineTo(t + n - r, e + a),
        i.quadraticCurveTo(t + n, e + a, t + n, e + a - r),
        i.lineTo(t + n, e + r),
        i.quadraticCurveTo(t + n, e, t + n - r, e),
        i.lineTo(t + r, e),
        i.quadraticCurveTo(t, e, t, e + r),
        i.stroke(),
        i.fill()
    }
},
mindmaps.CanvasBranchDrawer = function() {
    this.beforeDraw = function(i, t, e, n) {}
    ,
    this.render = function(i, t, e, n, a, r, o, h) {
        e *= h,
        n *= h;
        var s, d, T, u, v, g, l = r.width(), c = a.width(), _ = r.innerHeight(), f = a.innerHeight(), C = !1;
        if (e + c / 2 < l / 2) {
            var D = Math.abs(e);
            c < D ? (v = D - c + 1,
            T = c,
            s = !0) : (T = -e,
            v = c + e,
            C = !(s = !1))
        } else
            l < e ? (v = e - l + 1,
            T = l - e,
            s = !1) : (v = l - e,
            C = s = !(T = 0));
        var I, w, m, N, O = mindmaps.CanvasDrawingUtil.getLineWidth(h, t), b = O / 2;
        v < O && (v = O),
        n + f < _ ? (u = f,
        g = r.outerHeight() - n - u,
        d = !0) : (u = _ - n,
        g = a.outerHeight() - u,
        d = !1),
        this.beforeDraw(v, g, T, u),
        s ? (I = 0,
        m = v) : (I = v,
        m = 0);
        var M = (mindmaps.CanvasDrawingUtil.getLineWidth(h, t - 1) - O) / 2;
        if (d ? (w = 0 + b,
        N = g - b - M) : (w = g - b,
        N = 0 + b + M),
        C) {
            s ? (I += b,
            m -= b) : (I -= b,
            m += b);
            p = I,
            A = Math.abs(w - N) / 2,
            H = m,
            W = N < w ? w / 5 : N - N / 5
        } else
            var H = m < I ? I / 5 : m - m / 5
              , W = N
              , p = Math.abs(I - m) / 2
              , A = w;
        i.lineWidth = O,
        i.strokeStyle = o,
        i.fillStyle = o,
        i.beginPath(),
        i.moveTo(I, w),
        i.bezierCurveTo(p, A, H, W, m, N),
        i.stroke()
    }
}
,
mindmaps.TextMetrics = function() {
    var o = $("<div/>", {
        class: "node-text-behaviour"
    }).css({
        position: "absolute",
        visibility: "hidden",
        height: "auto",
        width: "auto"
    }).prependTo($("body"));
    return {
        ROOT_CAPTION_MIN_WIDTH: 100,
        NODE_CAPTION_MIN_WIDTH: 70,
        NODE_CAPTION_MAX_WIDTH: 150,
        getTextMetrics: function(i, t, e) {
            t = t || 1,
            e = e || i.getCaption();
            var n = i.text.font
              , a = i.isRoot() ? this.ROOT_CAPTION_MIN_WIDTH : this.NODE_CAPTION_MIN_WIDTH
              , r = this.NODE_CAPTION_MAX_WIDTH;
            return o.css({
                "font-size": t * n.size,
                "min-width": t * a,
                "max-width": t * r,
                "font-weight": n.weight
            }).text(e),
            {
                width: o.width() + 2,
                height: o.height() + 2
            }
        }
    }
}();
;mindmaps.CanvasView = function() {
    this.$getDrawingArea = function() {
        return $("#drawing-area")
    }
    ,
    this.$getContainer = function() {
        return $("#canvas-container")
    }
    ,
    this.center = function() {
        var t = this.$getContainer()
          , o = this.$getDrawingArea()
          , e = o.width() - t.width()
          , n = o.height() - t.height();
        this.scroll(e / 2, n / 2)
    }
    ,
    this.scroll = function(t, o) {
        this.$getContainer().scrollLeft(t).scrollTop(o)
    }
    ,
    this.applyViewZoom = function() {
        var t = this.zoomFactorDelta
          , o = this.$getContainer()
          , e = o.scrollLeft()
          , n = o.scrollTop()
          , i = o.width()
          , a = o.height()
          , s = i / 2 + e
          , r = a / 2 + n;
        e = (s *= this.zoomFactorDelta) - i / 2,
        n = (r *= this.zoomFactorDelta) - a / 2;
        var d = this.$getDrawingArea()
          , c = d.width()
          , h = d.height();
        d.width(c * t).height(h * t),
        this.scroll(e, n);
        var l = parseFloat(d.css("background-size"));
        isNaN(l) && console.warn("Could not set background-size!"),
        d.css("background-size", l * t)
    }
    ,
    this.setDimensions = function(t, o) {
        t *= this.zoomFactor,
        o *= this.zoomFactor,
        this.$getDrawingArea().width(t).height(o)
    }
    ,
    this.setZoomFactor = function(t) {
        this.zoomFactorDelta = t / (this.zoomFactor || 1),
        this.zoomFactor = t
    }
}
,
mindmaps.CanvasView.prototype.drawMap = function(t) {
    throw new Error("Not implemented")
}
,
mindmaps.DefaultCanvasView = function() {
    var g = this
      , m = !1
      , t = new function(d) {
        var c = this
          , h = !1;
        this.node = null,
        this.lineColor = null;
        var l = $("<div/>", {
            id: "creator-wrapper"
        }).on("remove", function(t) {
            return c.detach(),
            t.stopImmediatePropagation(),
            console.debug("creator detached."),
            !1
        })
          , t = $("<div/>", {
            id: "creator-nub"
        }).appendTo(l)
          , a = $("<div/>", {
            id: "creator-fakenode"
        }).appendTo(t)
          , f = $("<canvas/>", {
            id: "creator-canvas",
            class: "line-canvas"
        }).hide().appendTo(l);
        l.draggable({
            revert: !0,
            revertDuration: 0,
            start: function() {
                h = !0,
                f.show(),
                c.dragStarted && (c.lineColor = c.dragStarted(c.node))
            },
            drag: function(t, o) {
                var e = o.position.left / d.zoomFactor
                  , n = o.position.top / d.zoomFactor
                  , i = C(c.node);
                b(f, c.depth + 1, e, n, a, i, c.lineColor)
            },
            stop: function(t, o) {
                if (h = !1,
                f.hide(),
                c.dragStopped) {
                    var e = l.position()
                      , n = e.left / d.zoomFactor
                      , i = e.top / d.zoomFactor
                      , a = o.position.left / d.zoomFactor
                      , s = o.position.top / d.zoomFactor
                      , r = mindmaps.Util.distance(n - a, i - s);
                    c.dragStopped(c.node, a, s, r)
                }
                l.css({
                    left: "",
                    top: ""
                })
            }
        }),
        this.attachToNode = function(t) {
            if (this.node !== t) {
                this.node = t,
                l.removeClass("left right"),
                0 < t.offset.x ? l.addClass("right") : t.offset.x < 0 && l.addClass("left"),
                this.depth = t.getDepth();
                var o = d.getLineWidth(this.depth + 1);
                a.css("border-bottom-width", o);
                var e = C(t);
                l.appendTo(e)
            }
        }
        ,
        this.detach = function() {
            l.detach(),
            this.node = null
        }
        ,
        this.isDragging = function() {
            return h
        }
    }
    (this)
      , o = new function(n) {
        var i = this
          , a = !1
          , s = $("<textarea/>", {
            id: "caption-editor",
            class: "node-text-behaviour"
        }).bind("keydown", "esc", function() {
            i.stop()
        }).bind("keydown", "return", function() {
            r()
        }).mousedown(function(t) {
            t.stopPropagation()
        }).blur(function() {
            r()
        }).bind("input", function() {
            var t = v.getTextMetrics(i.node, n.zoomFactor, s.val());
            s.css(t),
            o()
        });
        function r() {
            a && i.commit && i.commit(i.node, s.val())
        }
        function o() {
            setTimeout(function() {
                n.redrawNodeConnectors(i.node)
            }, 1)
        }
        this.edit = function(t, o) {
            if (!a) {
                this.node = t,
                a = !0,
                this.$text = c(t),
                this.$cancelArea = o,
                this.text = this.$text.text(),
                this.$text.css({
                    width: "auto",
                    height: "auto"
                }).empty().addClass("edit"),
                o.bind("mousedown.editNodeCaption", function(t) {
                    r()
                });
                var e = v.getTextMetrics(i.node, n.zoomFactor, this.text);
                s.attr({
                    value: this.text
                }).css(e).appendTo(this.$text).select()
            }
        }
        ,
        this.stop = function() {
            a && (a = !1,
            this.$text.removeClass("edit"),
            s.detach(),
            this.$cancelArea.unbind("mousedown.editNodeCaption"),
            n.setNodeText(this.node, this.text),
            o())
        }
    }
    (this);
    o.commit = function(t, o) {
        g.nodeCaptionEditCommitted && g.nodeCaptionEditCommitted(t, o)
    }
    ;
    var v = mindmaps.TextMetrics
      , d = new mindmaps.CanvasBranchDrawer;
    function w(t) {
        return $("#node-canvas-" + t.id)
    }
    function C(t) {
        return $("#node-" + t.id)
    }
    function c(t) {
        return $("#node-caption-" + t.id)
    }
    function b(t, o, e, n, i, a, s) {
        var r = t[0].getContext("2d");
        d.$canvas = t,
        d.render(r, o, e, n, i, a, s, g.zoomFactor)
    }
    function h(t, o) {
        var e = t.getParent()
          , n = t.getDepth()
          , i = t.offset.x
          , a = t.offset.y;
        o = o || t.branchColor;
        var s = C(t)
          , r = C(e);
        b(w(t), n, i, a, s, r, o)
    }
    d.beforeDraw = function(t, o, e, n) {
        this.$canvas.attr({
            width: t,
            height: o
        }).css({
            left: e,
            top: n
        })
    }
    ,
    this.init = function() {
        g.$getContainer().dragscrollable({
            dragSelector: "#drawing-area, canvas.line-canvas",
            acceptPropagatedEvent: !1,
            delegateMode: !0,
            preventDefault: !0
        }),
        this.center();
        var t = this.$getDrawingArea();
        t.addClass("mindmap"),
        t.delegate("div.node-caption", "mousedown", function(t) {
            var o = $(this).parent().data("node");
            g.nodeMouseDown && g.nodeMouseDown(o)
        }),
        t.delegate("div.node-caption", "mouseup", function(t) {
            var o = $(this).parent().data("node");
            g.nodeMouseUp && g.nodeMouseUp(o)
        }),
        t.delegate("div.node-caption", "dblclick", function(t) {
            var o = $(this).parent().data("node");
            g.nodeDoubleClicked && g.nodeDoubleClicked(o)
        }),
        t.delegate("div.node-container", "mouseover", function(t) {
            if (t.target === this) {
                var o = $(this).data("node");
                g.nodeMouseOver && g.nodeMouseOver(o)
            }
            return !1
        }),
        t.delegate("div.node-caption", "mouseover", function(t) {
            if (t.target === this) {
                var o = $(this).parent().data("node");
                g.nodeCaptionMouseOver && g.nodeCaptionMouseOver(o)
            }
            return !1
        }),
        this.$getContainer().bind("mousewheel", function(t, o) {
            g.mouseWheeled && g.mouseWheeled(o)
        })
    }
    ,
    this.clear = function() {
        var t = this.$getDrawingArea();
        t.children().remove(),
        t.width(0).height(0)
    }
    ,
    this.getLineWidth = function(t) {
        return mindmaps.CanvasDrawingUtil.getLineWidth(this.zoomFactor, t)
    }
    ,
    this.drawMap = function(t) {
        var o = (new Date).getTime()
          , e = this.$getDrawingArea();
        e.children().remove();
        var n = t.root;
        g.createNode(n, e),
        console.debug("draw map ms: ", (new Date).getTime() - o)
    }
    ,
    this.createNode = function(a, s, r) {
        var t = a.getParent()
          , o = (s = s || C(t),
        r = r || a.getDepth(),
        a.offset.x)
          , e = a.offset.y
          , d = $("<div/>", {
            id: "node-" + a.id,
            class: "node-container"
        }).data({
            node: a
        }).css({
            "font-size": a.text.font.size
        });
        if (d.appendTo(s),
        a.isRoot()) {
            var n = this.getLineWidth(r);
            d.css("border-bottom-width", n)
        }
        if (!a.isRoot()) {
            var i = this.getLineWidth(r) + "px solid " + a.branchColor;
            d.css({
                left: this.zoomFactor * o,
                top: this.zoomFactor * e,
                "border-bottom": i
            }),
            d.one("mouseenter", function() {
                d.draggable({
                    handle: "div.node-caption:first",
                    start: function() {
                        m = !0
                    },
                    drag: function(t, o) {
                        var e = o.position.left / g.zoomFactor
                          , n = o.position.top / g.zoomFactor
                          , i = a.branchColor;
                        b(w(a), r, e, n, d, s, i),
                        g.nodeDragging && g.nodeDragging()
                    },
                    stop: function(t, o) {
                        m = !1;
                        var e = new mindmaps.Point(o.position.left / g.zoomFactor,o.position.top / g.zoomFactor);
                        g.nodeDragged && g.nodeDragged(a, e)
                    }
                })
            })
        }
        var c = a.text.font
          , h = $("<div/>", {
            id: "node-caption-" + a.id,
            class: "node-caption node-text-behaviour",
            text: a.text.caption
        }).css({
            color: c.color,
            "font-size": 100 * this.zoomFactor + "%",
            "font-weight": c.weight,
            "font-style": c.style,
            "text-decoration": c.decoration
        }).appendTo(d)
          , l = v.getTextMetrics(a, this.zoomFactor);
        h.css(l);
        var f = s.data("foldButton")
          , u = a.isRoot() || t.isRoot();
        if (f || u || this.createFoldButton(t),
        !a.isRoot()) {
            t.foldChildren ? d.hide() : d.show();
            var p = $("<canvas/>", {
                id: "node-canvas-" + a.id,
                class: "line-canvas"
            });
            b(p, r, o, e, d, s, a.branchColor),
            p.appendTo(d)
        }
        a.isRoot() && d.children().addBack().addClass("root"),
        a.forEachChild(function(t) {
            g.createNode(t, d, r + 1)
        })
    }
    ,
    this.deleteNode = function(t) {
        C(t).remove()
    }
    ,
    this.highlightNode = function(t) {
        c(t).addClass("selected")
    }
    ,
    this.unhighlightNode = function(t) {
        c(t).removeClass("selected")
    }
    ,
    this.closeNode = function(t) {
        var o = C(t);
        o.children(".node-container").hide(),
        o.children(".button-fold").first().removeClass("open").addClass("closed")
    }
    ,
    this.openNode = function(t) {
        var o = C(t);
        o.children(".node-container").show(),
        o.children(".button-fold").first().removeClass("closed").addClass("open")
    }
    ,
    this.createFoldButton = function(o) {
        var t = 0 < o.offset.x ? " right" : " left"
          , e = o.foldChildren ? " closed" : " open"
          , n = $("<div/>", {
            class: "button-fold no-select" + e + t
        }).click(function(t) {
            return g.foldButtonClicked && g.foldButtonClicked(o),
            t.preventDefault(),
            !1
        });
        C(o).data({
            foldButton: !0
        }).append(n)
    }
    ,
    this.removeFoldButton = function(t) {
        C(t).data({
            foldButton: !1
        }).children(".button-fold").remove()
    }
    ,
    this.editNodeCaption = function(t) {
        o.edit(t, this.$getDrawingArea())
    }
    ,
    this.stopEditNodeCaption = function() {
        o.stop()
    }
    ,
    this.setNodeText = function(t, o) {
        var e = c(t)
          , n = v.getTextMetrics(t, this.zoomFactor, o);
        e.css(n).text(o)
    }
    ,
    this.getCreator = function() {
        return t
    }
    ,
    this.isNodeDragging = function() {
        return m
    }
    ,
    this.redrawNodeConnectors = function(t) {
        t.isRoot() || h(t),
        t.isLeaf() || t.forEachChild(function(t) {
            h(t)
        })
    }
    ,
    this.updateBranchColor = function(t, o) {
        C(t).css("border-bottom-color", o),
        t.isRoot() || h(t, o)
    }
    ,
    this.updateFontColor = function(t, o) {
        c(t).css("color", o)
    }
    ,
    this.updateNode = function(t) {
        var o = C(t)
          , e = c(t)
          , n = t.text.font;
        o.css({
            "font-size": n.size,
            "border-bottom-color": t.branchColor
        });
        var i = v.getTextMetrics(t, this.zoomFactor);
        e.css({
            color: n.color,
            "font-weight": n.weight,
            "font-style": n.style,
            "text-decoration": n.decoration
        }).css(i),
        this.redrawNodeConnectors(t)
    }
    ,
    this.positionNode = function(t) {
        C(t).css({
            left: this.zoomFactor * t.offset.x,
            top: this.zoomFactor * t.offset.y
        }),
        h(t)
    }
    ,
    this.scaleMap = function() {
        var r = this.zoomFactor
          , t = this.$getDrawingArea().children().first()
          , o = t.data("node")
          , e = this.getLineWidth(0);
        t.css("border-bottom-width", e);
        var n = c(o)
          , i = v.getTextMetrics(o, this.zoomFactor);
        n.css({
            "font-size": 100 * r + "%",
            left: r * -mindmaps.TextMetrics.ROOT_CAPTION_MIN_WIDTH / 2
        }).css(i),
        o.forEachChild(function(t) {
            !function o(t, e) {
                var n = C(t);
                var i = g.getLineWidth(e);
                n.css({
                    left: r * t.offset.x,
                    top: r * t.offset.y,
                    "border-bottom-width": i
                });
                var a = c(t);
                a.css({
                    "font-size": 100 * r + "%"
                });
                var s = v.getTextMetrics(t, g.zoomFactor);
                a.css(s);
                h(t);
                t.isLeaf() || t.forEachChild(function(t) {
                    o(t, e + 1)
                })
            }(t, 1)
        })
    }
}
,
mindmaps.DefaultCanvasView.prototype = new mindmaps.CanvasView;
;mindmaps.CanvasPresenter = function(e, n, d, t, i) {
    var a = t.getCreator();
    this.init = function() {
        n.get(mindmaps.EditNodeCaptionCommand).setHandler(this.editNodeCaption.bind(this)),
        n.get(mindmaps.ToggleNodeFoldedCommand).setHandler(o)
    }
    ,
    this.editNodeCaption = function(e) {
        e || (e = d.selectedNode),
        t.editNodeCaption(e)
    }
    ;
    var o = function(e) {
        e || (e = d.selectedNode);
        var n = new mindmaps.action.ToggleNodeFoldAction(e);
        d.executeAction(n)
    }
      , s = function(e, n) {
        n && t.unhighlightNode(n),
        t.highlightNode(e)
    };
    t.mouseWheeled = function(e) {
        t.stopEditNodeCaption(),
        0 < e ? i.zoomIn() : i.zoomOut()
    }
    ,
    t.nodeMouseOver = function(e) {
        t.isNodeDragging() || a.isDragging() || a.attachToNode(e)
    }
    ,
    t.nodeCaptionMouseOver = function(e) {
        t.isNodeDragging() || a.isDragging() || a.attachToNode(e)
    }
    ,
    t.nodeMouseDown = function(e) {
        d.selectNode(e),
        a.attachToNode(e)
    }
    ,
    t.nodeDoubleClicked = function(e) {
        t.editNodeCaption(e)
    }
    ,
    t.nodeDragged = function(e, n) {
        var o = new mindmaps.action.MoveNodeAction(e,n);
        d.executeAction(o)
    }
    ,
    t.foldButtonClicked = function(e) {
        o(e)
    }
    ,
    a.dragStarted = function(e) {
        return e.isRoot() ? mindmaps.Util.randomColor() : e.branchColor
    }
    ,
    a.dragStopped = function(e, n, o, t) {
        if (!(t < 50)) {
            var i = new mindmaps.Node;
            i.branchColor = a.lineColor,
            i.offset = new mindmaps.Point(n,o),
            i.shouldEditCaption = !0,
            d.createNode(i, e)
        }
    }
    ,
    t.nodeCaptionEditCommitted = function(e, n) {
        (n = $.trim(n)) && (t.stopEditNodeCaption(),
        d.changeNodeCaption(e, n))
    }
    ,
    this.go = function() {
        t.init()
    }
    ,
    e.subscribe(mindmaps.Event.DOCUMENT_OPENED, function(e, n) {
        !function(e) {
            t.setZoomFactor(i.DEFAULT_ZOOM);
            var n = e.dimensions;
            console.log("DIM");
            console.log(n);
            t.setDimensions(n.x, n.y);
            var o = e.mindmap;
            t.drawMap(o),
            t.center(),
            d.selectNode(o.root)
        }(e)
    }),
    e.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function(e) {
        t.clear()
    }),
    e.subscribe(mindmaps.Event.NODE_MOVED, function(e) {
        t.positionNode(e)
    }),
    e.subscribe(mindmaps.Event.NODE_TEXT_CAPTION_CHANGED, function(e) {
        t.setNodeText(e, e.getCaption()),
        t.redrawNodeConnectors(e)
    }),
    e.subscribe(mindmaps.Event.NODE_CREATED, function(e) {
        if (t.createNode(e),
        e.shouldEditCaption) {
            delete e.shouldEditCaption;
            var n = e.getParent();
            if (n.foldChildren) {
                var o = new mindmaps.action.OpenNodeAction(n);
                d.executeAction(o)
            }
            d.selectNode(e),
            a.attachToNode(e),
            t.editNodeCaption(e)
        }
    }),
    e.subscribe(mindmaps.Event.NODE_DELETED, function(e, n) {
        var o = d.selectedNode;
        (e === o || e.isDescendant(o)) && d.selectNode(n),
        t.deleteNode(e),
        n.isLeaf() && t.removeFoldButton(n)
    }),
    e.subscribe(mindmaps.Event.NODE_SELECTED, s),
    e.subscribe(mindmaps.Event.NODE_OPENED, function(e) {
        t.openNode(e)
    }),
    e.subscribe(mindmaps.Event.NODE_CLOSED, function(e) {
        t.closeNode(e)
    }),
    e.subscribe(mindmaps.Event.NODE_FONT_CHANGED, function(e) {
        t.updateNode(e)
    }),
    e.subscribe(mindmaps.Event.NODE_FONT_COLOR_PREVIEW, function(e, n) {
        t.updateFontColor(e, n)
    }),
    e.subscribe(mindmaps.Event.NODE_BRANCH_COLOR_CHANGED, function(e) {
        t.updateNode(e)
    }),
    e.subscribe(mindmaps.Event.NODE_BRANCH_COLOR_PREVIEW, function(e, n) {
        t.updateBranchColor(e, n)
    }),
    e.subscribe(mindmaps.Event.ZOOM_CHANGED, function(e) {
        t.setZoomFactor(e),
        t.applyViewZoom(),
        t.scaleMap()
    }),
    this.init()
}
;
;mindmaps.ApplicationController = function() {
    var o = new mindmaps.EventBus
      , n = new mindmaps.ShortcutController
      , i = new mindmaps.CommandRegistry(n)
      , e = new mindmaps.UndoController(o,i)
      , m = new mindmaps.MindMapModel(o,i,e)
      , t = (new mindmaps.ClipboardController(o,i,m),
    new mindmaps.HelpController(o,i),
    new mindmaps.PrintController(o,i,m),
    new mindmaps.AutoSaveController(o,m))
      , a = new mindmaps.FilePicker(o,m);
    function s() {
        m.getDocument();
        r(),
        new mindmaps.NewDocumentPresenter(o,m,new mindmaps.NewDocumentView).go()
    }
    function d() {
        new mindmaps.SaveDocumentPresenter(o,m,new mindmaps.SaveDocumentView,t,a).go()
    }
    function r() {
        m.getDocument() && m.setDocument(null)
    }
    function p() {
        new mindmaps.OpenDocumentPresenter(o,m,new mindmaps.OpenDocumentView,a).go()
    }
    function l() {
        new mindmaps.ExportMapPresenter(o,m,new mindmaps.ExportMapView).go()
    }
    $("#mm_button-export-image").on('click',function(){
        l()
    })
    $("#mm_button-new").on('click',function(){
        s()
    })
    this.init = function() {
        var n = i.get(mindmaps.NewDocumentCommand);
        n.setHandler(s),
        n.setEnabled(!0);
        var e = i.get(mindmaps.OpenDocumentCommand);
        e.setHandler(p),
        e.setEnabled(!0);
        var m = i.get(mindmaps.SaveDocumentCommand);
        m.setHandler(d);
        var t = i.get(mindmaps.CloseDocumentCommand);
        t.setHandler(r);
        var a = i.get(mindmaps.ExportCommand);
        a.setHandler(l),
        o.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
            m.setEnabled(!1),
            t.setEnabled(!1),
            a.setEnabled(!1)
        }),
        o.subscribe(mindmaps.Event.DOCUMENT_OPENED, function() {
            m.setEnabled(!0),
            t.setEnabled(!0),
            a.setEnabled(!0)
        })
    }
    ,
    this.go = function() {
        new mindmaps.MainViewController(o,m,i).go(),
        s()
        d()
        p()
    }
    ,
    this.init()
}
;
;mindmaps.MindMapModel = function(o, i, s) {
    var d = this;
    this.document = null,
    this.selectedNode = null,
    this.getDocument = function() {
        return this.document
    }
    ,
    this.setDocument = function(e) {
        (this.document = e) ? o.publish(mindmaps.Event.DOCUMENT_OPENED, e) : o.publish(mindmaps.Event.DOCUMENT_CLOSED)
    }
    ,
    this.getMindMap = function() {
        return this.document ? this.document.mindmap : null
    }
    ,
    this.init = function() {
        var e = i.get(mindmaps.CreateNodeCommand);
        e.setHandler(this.createNode.bind(this));
        var t = i.get(mindmaps.CreateSiblingNodeCommand);
        t.setHandler(this.createSiblingNode.bind(this));
        var n = i.get(mindmaps.DeleteNodeCommand);
        n.setHandler(this.deleteNode.bind(this)),
        o.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
            e.setEnabled(!1),
            t.setEnabled(!1),
            n.setEnabled(!1)
        }),
        o.subscribe(mindmaps.Event.DOCUMENT_OPENED, function() {
            e.setEnabled(!0),
            t.setEnabled(!0),
            n.setEnabled(!0)
        })
    }
    ,
    this.deleteNode = function(e) {
        e || (e = this.selectedNode);
        var t = this.getMindMap()
          , n = new mindmaps.action.DeleteNodeAction(e,t);
        this.executeAction(n)
    }
    ,
    this.createNode = function(e, t) {
        var n = this.getMindMap();
        if (e && t)
            i = new mindmaps.action.CreateNodeAction(e,t,n);
        else {
            t = this.selectedNode;
            var i = new mindmaps.action.CreateAutoPositionedNodeAction(t,n)
        }
        this.executeAction(i)
    }
    ,
    this.createSiblingNode = function() {
        var e = this.getMindMap()
          , t = this.selectedNode.getParent();
        if (null !== t) {
            var n = new mindmaps.action.CreateAutoPositionedNodeAction(t,e);
            this.executeAction(n)
        }
    }
    ,
    this.selectNode = function(e) {
        if (e !== this.selectedNode) {
            var t = this.selectedNode;
            this.selectedNode = e,
            o.publish(mindmaps.Event.NODE_SELECTED, e, t)
        }
    }
    ,
    this.changeNodeCaption = function(e, t) {
        e || (e = this.selectedNode);
        var n = new mindmaps.action.ChangeNodeCaptionAction(e,t);
        this.executeAction(n)
    }
    ,
    this.executeAction = function(e) {
        if (e instanceof mindmaps.action.CompositeAction) {
            var t = this.executeAction.bind(this);
            e.forEachAction(t)
        } else {
            var n = e.execute();
            if (void 0 !== n && !n)
                return !1;
            if (e.event && (Array.isArray(e.event) || (e.event = [e.event]),
            o.publish.apply(o, e.event)),
            e.undo) {
                if (e.redo)
                    var i = function() {
                        d.executeAction(e.redo())
                    };
                s.addUndo(function() {
                    d.executeAction(e.undo())
                }, i)
            }
        }
    }
    ,
    this.saveToLocalStorage = function() {
        var e = this.document.prepareSave();
        console.log("data"),
        console.log(e);
        var t = mindmaps.LocalDocumentStorage.saveDocument(e);
        return t && o.publish(mindmaps.Event.DOCUMENT_SAVED, e),
        t
    }
    ,
    this.init()
}
;
;mindmaps.NewDocumentView = function() {}
,
mindmaps.NewDocumentPresenter = function(n, e, m) {
    this.go = function() {
        var n = new mindmaps.Document;
        e.setDocument(n)
    }
}
;
;mindmaps.OpenDocumentView = function() {
    var n = this
      , o = $("#template-open").tmpl().dialog({
        autoOpen: !1,
        modal: !0,
        zIndex: 5e3,
        width: 550,
        close: function() {
            $(this).dialog("destroy"),
            $(this).remove()
        }
    });
    $("#button-open-cloud").button().click(function() {
        n.openCloudButtonClicked && n.openCloudButtonClicked()
    });
    $("#button-open-system").on('click',function(){
        n.systemClicked && n.systemClicked()
    })
    $("#mm-local-file-selector").bind("change", function(e) {
        n.openExernalFileClicked && n.openExernalFileClicked(e)
    }),
    o.find(".localstorage-filelist").delegate("a.title", "click", function() {
        if (n.documentClicked) {
            var e = $(this).tmplItem();
            n.documentClicked(e.data)
        }
    }).delegate("a.delete", "click", function() {
        if (n.deleteDocumentClicked) {
            var e = $(this).tmplItem();
            n.deleteDocumentClicked(e.data)
        }
    }),
    this.render = function(e) {
        var n = $(".document-list", o).empty();
        $("#template-open-table-item").tmpl(e, {
            format: function(e) {
                return e ? e.getDate() + "/" + (e.getMonth() + 1) + "/" + e.getFullYear() : ""
            }
        }).appendTo(n)
    }
    ,
    this.showOpenDialog = function(e) {
        this.render(e),
        o.dialog("open")
    }
    ,
    this.hideOpenDialog = function() {
        o.dialog("close")
    }
    ,
    this.showCloudError = function(e) {
        o.find(".cloud-loading").removeClass("loading"),
        o.find(".cloud-error").text(e)
    }
    ,
    this.showCloudLoading = function() {
        o.find(".cloud-error").text(""),
        o.find(".cloud-loading").addClass("loading")
    }
    ,
    this.hideCloudLoading = function() {
        o.find(".cloud-loading").removeClass("loading")
    }
}
,
mindmaps.OpenDocumentPresenter = function(t, i, l, n) {
    l.openCloudButtonClicked = function(e) {
        mindmaps.Util.trackEvent("Clicks", "cloud-open"),
        mindmaps.Util.trackEvent("CloudOpen", "click"),
        n.open({
            load: function() {
                l.showCloudLoading()
            },
            cancel: function() {
                l.hideCloudLoading(),
                mindmaps.Util.trackEvent("CloudOpen", "cancel")
            },
            success: function() {
                l.hideOpenDialog(),
                mindmaps.Util.trackEvent("CloudOpen", "success")
            },
            error: function(e) {
                l.showCloudError(e),
                mindmaps.Util.trackEvent("CloudOpen", "error", e)
            }
        })
    }
    ,
    l.openExernalFileClicked = function(e) {
        mindmaps.Util.trackEvent("Clicks", "hdd-open");
        var n = e.target.files[0]
          , o = new FileReader;
        o.onload = function() {
            try {
                var e = mindmaps.Document.fromJSON(o.result)
            } catch (e) {
                throw t.publish(mindmaps.Event.NOTIFICATION_ERROR, "File is not a valid mind map!"),
                new Error("Error while opening map from hdd",e)
            }
            i.setDocument(e),
            $("#MindMapOpen .close-modal").trigger('click');
            l.hideOpenDialog()
        }
        ,
        o.readAsText(n)
    }
    ,
    l.systemClicked = function() {
        mindmaps.Util.trackEvent("Clicks", "system-open");
        if($("#mind-map-system-select").val()) {
            var selected_id = $("#mind-map-system-select").val();
            var get_id_url = $('#get_id_url').val();
            var ajax_url = get_id_url.replace(":id",selected_id);
            $.ajax({
                url: ajax_url,
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                success: function (response) {
                    var selected_opt = response.mindMap.data;
                    var e = mindmaps.Document.fromJSON(selected_opt)

                    i.setDocument(e),
                    $("#MindMapOpen .close-modal").trigger('click');
                    l.hideOpenDialog()

                    
                    $("#loading-image-preview").hide();
                    // $("#MindMapSave .close-modal").trigger('click');
                    // c.hideSaveDialog()
                },
                error: function () {
                    $("#loading-image-preview").hide();
                    alert("An error occured!");
                }
            });


            // var selected_opt = $("#mind-map-system-select option:selected").attr('data-val');
            
            // var e = selected_opt;
            

        } else {
            alert("Please select value from dropwdown")
        }
    }
    ,
    l.documentClicked = function(e) {
        mindmaps.Util.trackEvent("Clicks", "localstorage-open"),
        i.setDocument(e),
        l.hideOpenDialog()
    }
    ,
    l.deleteDocumentClicked = function(e) {
        mindmaps.LocalDocumentStorage.deleteDocument(e);
        var n = mindmaps.LocalDocumentStorage.getDocuments();
        l.render(n)
    }
    ,
    this.go = function() {
        var e = mindmaps.LocalDocumentStorage.getDocuments();
        e.sort(mindmaps.Document.sortByModifiedDateDescending)
        // l.showOpenDialog(e)
    }
}
;
;mindmaps.SaveDocumentView = function() {
    var o = this
      , t = $("#template-save").tmpl().dialog({
        autoOpen: !1,
        modal: !0,
        zIndex: 5e3,
        width: 550,
        close: function() {
            $(this).dialog("destroy"),
            $(this).remove()
        }
    })
      , e = ($("#button-save-cloudstorage").button().click(function() {
        o.cloudStorageButtonClicked && o.cloudStorageButtonClicked()
    }),
    $("#button-save-localstorage").button().click(function() {
        o.localStorageButtonClicked && o.localStorageButtonClicked()
    }),
    $("#checkbox-autosave-localstorage").click(function() {
        o.autoSaveCheckboxClicked && o.autoSaveCheckboxClicked($(this).prop("checked"))
    }));
    $("#button-save-hdd").on('click',function() {
        o.hddSaveButtonClicked && o.hddSaveButtonClicked()
    });
    $("#button-save-system").on('click',function() {
        o.systemSaveButtonClicked && o.systemSaveButtonClicked()
    });
    this.setAutoSaveCheckboxState = function(o) {
        e.prop("checked", o)
    }
    ,
    this.showSaveDialog = function() {
        t.dialog("open")
    }
    ,
    this.hideSaveDialog = function() {
        t.dialog("close")
    }
    ,
    this.showCloudError = function(o) {
        t.find(".cloud-loading").removeClass("loading"),
        t.find(".cloud-error").text(o)
    }
    ,
    this.showCloudLoading = function() {
        t.find(".cloud-error").text(""),
        t.find(".cloud-loading").addClass("loading")
    }
    ,
    this.hideCloudLoading = function() {
        t.find(".cloud-loading").removeClass("loading")
    }
}
,
mindmaps.SaveDocumentPresenter = function(i, n, c, t, o) {
    c.cloudStorageButtonClicked = function() {
        mindmaps.Util.trackEvent("Clicks", "cloud-save"),
        mindmaps.Util.trackEvent("CloudSave", "click"),
        o.save({
            load: function() {
                c.showCloudLoading()
            },
            cancel: function() {
                c.hideCloudLoading(),
                mindmaps.Util.trackEvent("CloudSave", "cancel")
            },
            success: function() {
                c.hideSaveDialog(),
                mindmaps.Util.trackEvent("CloudSave", "success")
            },
            error: function(o) {
                c.showCloudError(o),
                mindmaps.Util.trackEvent("CloudSave", "error", o)
            }
        })
    }
    ,
    c.localStorageButtonClicked = function() {
        mindmaps.Util.trackEvent("Clicks", "localstorage-save"),
        n.saveToLocalStorage() ? c.hideSaveDialog() : i.publish(mindmaps.Event.NOTIFICATION_ERROR, "Error while saving to local storage")
    }
    ,
    c.autoSaveCheckboxClicked = function(o) {
        o ? t.enable() : t.disable()
    }
    ,
    c.hddSaveButtonClicked = function() {
        mindmaps.Util.trackEvent("Clicks", "hdd-save");
        var o = n.getMindMap().getRoot().getCaption() + ".json"
          , t = n.getDocument().prepareSave().serialize()
          , e = new Blob([t],{
            type: "text/plain;charset=utf-8"
        });
        window.saveAs(e, o);
        var a = n.getDocument();
        i.publish(mindmaps.Event.DOCUMENT_SAVED, a),
        c.hideSaveDialog()
    }
    ,
    c.systemSaveButtonClicked = function() {

        mindmaps.Util.trackEvent("Clicks", "system-save");

        var t = n.getDocument().prepareSave().serialize();
        var title = $("#mind-map-title").val();
        var description = $("#mind-map-description").val();
        var form_process =true;
        if(title == ''){
            form_process = false;
            alert('Please enter Title');

        }
        if(form_process) {

            $.ajax({
                url: $('#save_url').val(),
                type: 'POST',
                data: {
                    title,
                    description,
                    data : t
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // dataType: 'json',
                beforeSend: function () {
                    $("#loading-image-preview").show();
                },
                success: function (response) {
                    $("#loading-image-preview").hide();
                    $("#MindMapSave .close-modal").trigger('click');
                    alert("Saved");
                    c.hideSaveDialog()
                },
                error: function () {
                    $("#loading-image-preview").hide();
                    alert("An error occured!");
                }
            });

        }


        // var o = n.getMindMap().getRoot().getCaption() + ".json"
        //   , t = n.getDocument().prepareSave().serialize()
        //   , e = new Blob([t],{
        //     type: "text/plain;charset=utf-8"
        // });
        // window.saveAs(e, o);
        // var a = n.getDocument();
        // i.publish(mindmaps.Event.DOCUMENT_SAVED, a),
        

    }
    this.go = function() {
        // c.setAutoSaveCheckboxState(t.isEnabled()),
        // c.showSaveDialog()
    }
}
;
;mindmaps.CanvasContainer = function() {
    var a = this
      , t = $("#canvas-container");
    this.getContent = function() {
        return t
    }
    ,
    this.setSize = function() {
        var n = $(window).height() - $("#topbar").outerHeight(!0) - $("#bottombar").outerHeight(!0);
        t.height(n);
        var e = new mindmaps.Point(t.width(),n);
        a.publish(mindmaps.CanvasContainer.Event.RESIZED, e)
    }
    ,
    this.acceptFileDrop = function() {
        function i(n) {
            n.originalEvent.stopPropagation(),
            n.originalEvent.preventDefault()
        }
        t.bind("dragover", function(n) {
            i(n)
        }),
        t.bind("drop", function(n) {
            i(n);
            var e = n.originalEvent.dataTransfer.files[0]
              , t = new FileReader;
            t.onload = function() {
                a.receivedFileDrop(t.result)
            }
            ,
            t.readAsText(e)
        })
    }
    ,
    this.init = function() {
        $(window).resize(function() {
            a.setSize()
        }),
        this.setSize(),
        this.acceptFileDrop()
    }
    ,
    this.receivedFileDrop = function(n) {}
}
,
EventEmitter.mixin(mindmaps.CanvasContainer),
mindmaps.CanvasContainer.Event = {
    RESIZED: "ResizedEvent"
},
mindmaps.MainViewController = function(d, p, v) {
    var c = new mindmaps.ZoomController(d,v)
      , w = new mindmaps.CanvasContainer;
    w.receivedFileDrop = function(n) {
        try {
            var e = mindmaps.Document.fromJSON(n);
            p.setDocument(e)
        } catch (n) {
            d.publish(mindmaps.Event.NOTIFICATION_ERROR, "Could not read the file."),
            console.warn("Could not open the mind map via drag and drop.")
        }
    }
    ,
    this.go = function() {
        w.init();
        var n = new mindmaps.ToolBarView;
        new mindmaps.ToolBarPresenter(d,v,n,p).go();
        var e = new mindmaps.DefaultCanvasView;
        new mindmaps.CanvasPresenter(d,v,p,e,c).go();
        var t = new mindmaps.StatusBarView
          , i = new mindmaps.StatusBarPresenter(d,t);
        i.go();
        var a = new mindmaps.FloatPanelFactory(w)
          , o = new mindmaps.InspectorView;
        new mindmaps.InspectorPresenter(d,p,o).go();
        var r = a.create("Inspector", o.getContent());
        r.show(),
        i.addEntry(r);
        var s = new mindmaps.NavigatorView;
        new mindmaps.NavigatorPresenter(d,s,w,c).go();
        var m = a.create("Navigator", s.getContent());
        m.show(),
        i.addEntry(m)
    }
}
;
;mindmaps.LocalStorage = {
    put: function(e, t) {
        localStorage.setItem(e, t)
    },
    get: function(e) {
        return localStorage.getItem(e)
    },
    clear: function() {
        localStorage.clear()
    }
},
mindmaps.SessionStorage = {
    put: function(e, t) {
        sessionStorage.setItem(e, t)
    },
    get: function(e) {
        return sessionStorage.getItem(e)
    },
    clear: function() {
        sessionStorage.clear()
    }
},
mindmaps.LocalDocumentStorage = function() {
    var a = "mindmaps.document."
      , c = function(e) {
        var t = localStorage.getItem(e);
        if (null === t)
            return null;
        try {
            return mindmaps.Document.fromJSON(t)
        } catch (e) {
            return console.error("Error while loading document from local storage", e),
            null
        }
    };
    return {
        saveDocument: function(e) {
            try {
                return localStorage.setItem(a + e.id, e.serialize()),
                !0
            } catch (e) {
                return console.error("Error while saving document to local storage", e),
                !1
            }
        },
        loadDocument: function(e) {
            return c(a + e)
        },
        getDocuments: function() {
            for (var e = [], t = 0, o = localStorage.length; t < o; t++) {
                var n = localStorage.key(t);
                if (0 == n.indexOf(a)) {
                    var r = c(n);
                    r && e.push(r)
                }
            }
            return e
        },
        getDocumentIds: function() {
            for (var e = [], t = 0, o = localStorage.length; t < o; t++) {
                var n = localStorage.key(t);
                0 == n.indexOf(a) && e.push(n.substring(a.length))
            }
            return e
        },
        deleteDocument: function(e) {
            localStorage.removeItem(a + e.id)
        },
        deleteAllDocuments: function() {
            this.getDocuments().forEach(this.deleteDocument)
        }
    }
}();
;if (mindmaps.Event = {
    DOCUMENT_OPENED: "DocumentOpenedEvent",
    DOCUMENT_SAVED: "DocumentSavedEvent",
    DOCUMENT_CLOSED: "DocumentClosedEvent",
    NODE_SELECTED: "NodeSelectedEvent",
    NODE_DESELECTED: "NodeDeselectedEvent",
    NODE_MOVED: "NodeMovedEvent",
    NODE_TEXT_CAPTION_CHANGED: "NodeTextCaptionChangedEvent",
    NODE_FONT_CHANGED: "NodeFontChangedEvent",
    NODE_FONT_COLOR_PREVIEW: "NodeFontColorPreviewEvent",
    NODE_BRANCH_COLOR_CHANGED: "NodeBranchColorChangedEvent",
    NODE_BRANCH_COLOR_PREVIEW: "NodeBranchColorPreviewEvent",
    NODE_CREATED: "NodeCreatedEvent",
    NODE_DELETED: "NodeDeletedEvent",
    NODE_OPENED: "NodeOpenedEvent",
    NODE_CLOSED: "NodeClosedEvent",
    ZOOM_CHANGED: "ZoomChangedEvent",
    NOTIFICATION_INFO: "NotificationInfoEvent",
    NOTIFICATION_WARN: "NotificationWarnEvent",
    NOTIFICATION_ERROR: "NotificationErrorEvent"
},
mindmaps.EventBus = EventEmitter,
mindmaps.DEBUG) {
    var old = mindmaps.EventBus.prototype.emit;
    mindmaps.EventBus.prototype.publish = function(e) {
        var E = this.listeners(e).length;
        console.log("EventBus > publish: " + e, "(Listeners: " + E + ")"),
        old.apply(this, arguments)
    }
}
;mindmaps.Notification = function(t, e) {
    var i = this;
    e = $.extend({}, mindmaps.Notification.Defaults, e);
    var o = this.$el = $("#template-notification").css({
        "max-width": e.maxWidth
    }).addClass(e.type)
      , a = $(t);
    if (0 === a.length)
        return this;
    var s = a.offset()
      , n = s.left
      , c = s.top
      , r = a.outerWidth()
      , f = a.outerHeight();
    o.appendTo($("body"));
    var d, l, p = o.outerWidth(), m = o.outerHeight(), u = e.padding;
    switch (e.position) {
    case "topLeft":
        l = c - u - m,
        d = n;
        break;
    case "topMiddle":
        l = c - u - m,
        d = p < r ? n + (r - p) / 2 : n - (p - r) / 2;
        break;
    case "topRight":
        l = c - u - m,
        d = n + r - p;
        break;
    case "rightTop":
        l = c;
        break;
    case "rightMiddle":
        l = m < f ? c + (f - m) / 2 : c - (m - f) / 2,
        d = n + u + r;
        break;
    case "rightBottom":
        l = c + f - m,
        d = n + u + r;
        break;
    case "bottomLeft":
        l = c + u + f,
        d = n;
        break;
    case "bottomMiddle":
        l = c + u + f,
        d = p < r ? n + (r - p) / 2 : n - (p - r) / 2;
        break;
    case "bottomRight":
        l = c + u + f,
        d = n + r - p;
        break;
    case "leftTop":
        l = c,
        d = n - u - p;
        break;
    case "leftMiddle":
        l = m < f ? c + (f - m) / 2 : c - (m - f) / 2,
        d = n - u - p;
        break;
    case "leftBottom":
        l = c + f - m,
        d = n - u - p
    }
    o.offset({
        left: d,
        top: l
    }),
    e.expires && setTimeout(function() {
        i.close()
    }, e.expires),
    e.closeButton && o.find(".close-button").click(function() {
        i.close()
    }),
    o.fadeIn(600)
}
,
mindmaps.Notification.prototype = {
    close: function() {
        var t = this.$el;
        t.fadeOut(800, function() {
            t.remove(),
            this.removed = !0
        })
    },
    isVisible: function() {
        return !this.removed
    },
    $: function() {
        return this.$el
    }
},
mindmaps.Notification.Defaults = {
    title: null,
    content: "New Notification",
    position: "topLeft",
    padding: 10,
    expires: 0,
    closeButton: !1,
    maxWidth: 500,
    type: "info"
};
;mindmaps.StaticCanvasRenderer = function() {
    var h = 8
      , f = 1
      , s = $("<canvas/>", {
        class: "map"
    })
      , w = s[0].getContext("2d")
      , l = new mindmaps.CanvasBranchDrawer;
    function e(t) {
        var e, i = t.mindmap, n = (function e(t) {
            var i = mindmaps.CanvasDrawingUtil.getLineWidth(f, t.getDepth())
              , n = mindmaps.TextMetrics.getTextMetrics(t, f)
              , a = {
                lineWidth: i,
                textMetrics: n,
                width: function() {
                    return t.isRoot() ? 0 : n.width
                },
                innerHeight: function() {
                    return n.height + h
                },
                outerHeight: function() {
                    return n.height + i + h
                }
            };
            $.extend(t, a),
            t.forEachChild(function(t) {
                e(t)
            })
        }(e = i.getRoot().clone()),
        e), a = function(t) {
            t.getPosition();
            var n = 0
              , a = 0
              , r = 0
              , o = 0;
            function e(t) {
                var e = t.getPosition()
                  , i = t.textMetrics;
                e.x < n && (n = e.x),
                e.x + i.width > r && (r = e.x + i.width),
                e.y < a && (a = e.y),
                e.y + t.outerHeight() > o && (o = e.y + t.outerHeight())
            }
            return e(t),
            t.forEachDescendant(function(t) {
                e(t)
            }),
            {
                width: 2 * Math.max(Math.abs(r), Math.abs(n)) + 50,
                height: 2 * Math.max(Math.abs(o), Math.abs(a)) + 50
            }
        }(n), r = a.width, o = a.height;
        s.attr({
            width: r,
            height: o
        }),
        w.textBaseline = "top",
        w.textAlign = "center",
        w.fillStyle = "white",
        w.fillRect(0, 0, r, o),
        w.translate(r / 2, o / 2),
        function e(i, t) {
            w.save();
            var n = i.offset.x;
            var a = i.offset.y;
            w.translate(n, a);
            t && (r = i,
            o = t,
            w.save(),
            l.render(w, r.getDepth(), r.offset.x, r.offset.y, r, o, r.branchColor, f),
            w.restore());
            var r, o;
            if (!i.isRoot()) {
                w.fillStyle = i.branchColor;
                var s = i.textMetrics;
                w.fillRect(0, s.height + h, s.width, i.lineWidth)
            }
            i.forEachChild(function(t) {
                e(t, i)
            });
            w.restore()
        }(n),
        function e(t) {
            w.save();
            var i = t.offset.x;
            var n = t.offset.y;
            w.translate(i, n);
            var a = t.textMetrics;
            var r = t.getCaption();
            var o = t.text.font;
            w.font = o.style + " " + o.weight + " " + o.size + "px sans-serif";
            var s = a.width / 2;
            var h = 0;
            t.isRoot() && (s = 0,
            h = 20,
            w.lineWidth = 5,
            w.strokeStyle = "orange",
            w.fillStyle = "white",
            mindmaps.CanvasDrawingUtil.roundedRect(w, 0 - a.width / 2 - 4, 16, a.width + 8, a.height + 8, 10));
            w.strokeStyle = o.color;
            w.fillStyle = o.color;
            function f(t) {
                var e = w.measureText(t);
                return e.width <= a.width
            }
            if (f(r))
                w.fillText(r, s, h);
            else {
                var l = r.match(/[^ ]+ */gi);
                console.log("words1", l);
                var c = [];
                l.forEach(function(t) {
                    if (f(t))
                        c.push(t);
                    else {
                        for (var e = "", i = 0; i < t.length; i++) {
                            var n = t.charAt(i);
                            f(e + n) ? e += n : (c.push(e),
                            e = n)
                        }
                        c.push(e)
                    }
                }),
                console.log("words2", c);
                var u = []
                  , d = ""
                  , g = a.width;
                c.forEach(function(t) {
                    var e;
                    "" === d ? d = t : (e = d + " " + t,
                    w.measureText(e).width > g ? (u.push(d),
                    d = t) : d += " " + t)
                }),
                u.push(d),
                console.log("lines", u);
                for (var v = 0; v < u.length; v++) {
                    var d = u[v];
                    w.fillText(d, s, h + v * o.size)
                }
            }
            t.forEachChild(function(t) {
                e(t)
            });
            w.restore()
        }(n)
    }
    l.beforeDraw = function(t, e, i, n) {
        w.translate(i, n)
    }
    ,
    this.getImageData = function(t) {
        return e(t),
        s[0].toDataURL("image/png")
    }
    ,
    this.renderAsPNG = function(t) {
        var e = this.getImageData(t);
        return $("<img/>", {
            src: e,
            class: "map"
        })
    }
    ,
    this.renderAsCanvas = function(t) {
        return e(t),
        s
    }
}
;
;mindmaps.PrintController = function(n, e, t) {
    var a = e.get(mindmaps.PrintCommand);
    a.setHandler(function() {
        var n = i.renderAsPNG(t.getDocument());
        $("#print-area").html(n),
        window.print()
    });
    var i = new mindmaps.StaticCanvasRenderer;
    n.subscribe(mindmaps.Event.DOCUMENT_CLOSED, function() {
        a.setEnabled(!1)
    }),
    n.subscribe(mindmaps.Event.DOCUMENT_OPENED, function() {
        a.setEnabled(!0)
    })
}
;
;mindmaps.ExportMapView = function() {
    var t = $("#template-export-map").tmpl().dialog({
        autoOpen: !1,
        modal: !0,
        zIndex: 5e3,
        width: "auto",
        height: "auto",
        close: function() {
            $(this).dialog("destroy"),
            $(this).remove()
        },
        open: function() {
            $(this).css({
                "max-width": .9 * $(window).width(),
                "max-height": .8 * $(window).height()
            }),
            t.dialog("option", "position", "center")
        },
        buttons: {
            Ok: function() {
                $(this).dialog("close")
            }
        }
    });
    this.showDialog = function() {
        t.dialog("open")
    }
    ,
    this.hideDialog = function() {
        t.dialog("close")
    }
    ,
    this.setImage = function(t) {
        $("#export-preview").html(t)
    }
}
,
mindmaps.ExportMapPresenter = function(t, i, o) {
    var e = new mindmaps.StaticCanvasRenderer;
    this.go = function() {
        var t = e.renderAsPNG(i.getDocument());
        o.setImage(t),
        setTimeout(function() {
            o.showDialog()
        }, 30)
    }
}
;
;mindmaps.AutoSaveController = function(n, t) {
    var e = null;
    function i() {
        console.debug("Autosaving..."),
        t.saveToLocalStorage()
    }
    function o() {
        e || (e = setInterval(i, 6e4))
    }
    function s() {
        e && (clearInterval(e),
        e = null)
    }
    this.enable = function() {
        o(),
        t.getDocument().setAutoSave(!0)
    }
    ,
    this.disable = function() {
        s(),
        t.getDocument().setAutoSave(!1)
    }
    ,
    this.isEnabled = function() {
        return t.getDocument().isAutoSave()
    }
    ,
    this.init = function() {
        n.subscribe(mindmaps.Event.DOCUMENT_OPENED, this.documentOpened.bind(this)),
        n.subscribe(mindmaps.Event.DOCUMENT_CLOSED, this.documentClosed.bind(this))
    }
    ,
    this.documentOpened = function(n) {
        this.isEnabled() && o()
    }
    ,
    this.documentClosed = function() {
        s()
    }
    ,
    this.init()
}
;
;mindmaps.FilePicker = function(a, t) {
    if (window.filepicker) {
        var s = window.filepicker;
        s.setKey("P9tQ4bicRwyIe8ZUsny5")
    }
    var l = "application/json";
    this.open = function(n) {
        n = n || {},
        s && navigator.onLine ? (n.load && n.load(),
        s.pick({
            mimetype: l,
            container: "modal",
            openTo: "DROPBOX",
            services: ["COMPUTER", "GOOGLE_DRIVE", "DROPBOX", "BOX", "SKYDRIVE"]
        }, function(e) {
            $.ajax({
                url: e.url,
                success: function(e) {
                    try {
                        "[object String]" == Object.prototype.toString.call(e) && (e = JSON.parse(e));
                        var r = mindmaps.Document.fromObject(e)
                    } catch (e) {
                        throw a.publish(mindmaps.Event.NOTIFICATION_ERROR, "File is not a valid mind map!"),
                        new Error("Error while parsing map from cloud",e)
                    }
                    t.setDocument(r),
                    n.success && n.success(r)
                },
                error: function(e, r, o) {
                    throw n.error && n.error("Error: Could not open mind map!"),
                    new Error("Error while loading map from filepicker. " + r + " " + o)
                }
            })
        }, function(e) {
            if (101 !== e.code)
                throw new Error(e);
            n.cancel && n.cancel()
        })) : n.error && n.error("Cannot access cloud, it appears you are offline.")
    }
    ,
    this.save = function(r) {
        if (r = r || {},
        s && navigator.onLine) {
            r.load && r.load();
            var o = t.getDocument().prepareSave()
              , e = o.serialize()
              , n = new Blob([e],{
                type: "application/json"
            });
            s.store(n, function(e) {
                s.exportFile(e.url, {
                    mimetype: l,
                    suggestedFilename: o.title,
                    container: "modal",
                    openTo: "DROPBOX",
                    services: ["DROPBOX", "BOX", "SKYDRIVE", "GOOGLE_DRIVE"]
                }, i, c)
            })
        } else
            r.error && r.error("Cannot access cloud, it appears you are offline.");
        function i(e) {
            a.publish(mindmaps.Event.DOCUMENT_SAVED, o),
            r.success && r.success()
        }
        function c(e) {
            if (131 !== e.code)
                throw new Error(e);
            r.cancel && r.cancel()
        }
    }
}
;
;