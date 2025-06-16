(() => {
    var t = {
            221: function (t) {
                var e;
                (e = function () {
                    'use strict';
                    var t = '(prefers-reduced-motion: reduce)';
                    function e(t) {
                        t.length = 0;
                    }
                    function n(t, e, n) {
                        return Array.prototype.slice.call(t, e, n);
                    }
                    function i(t) {
                        return t.bind.apply(t, [null].concat(n(arguments, 1)));
                    }
                    function r() {}
                    var o = setTimeout;
                    function s(t) {
                        return requestAnimationFrame(t);
                    }
                    function u(t, e) {
                        return typeof e === t;
                    }
                    function a(t) {
                        return null !== t && u('object', t);
                    }
                    var c = Array.isArray,
                        l = i(u, 'function'),
                        f = i(u, 'string'),
                        d = i(u, 'undefined');
                    function p(t) {
                        try {
                            return (
                                t instanceof
                                (t.ownerDocument.defaultView || window)
                                    .HTMLElement
                            );
                        } catch (t) {
                            return !1;
                        }
                    }
                    function h(t) {
                        return c(t) ? t : [t];
                    }
                    function g(t, e) {
                        h(t).forEach(e);
                    }
                    function v(t, e) {
                        return -1 < t.indexOf(e);
                    }
                    function m(t, e) {
                        return t.push.apply(t, h(e)), t;
                    }
                    function y(t, e, n) {
                        t &&
                            g(e, function (e) {
                                e && t.classList[n ? 'add' : 'remove'](e);
                            });
                    }
                    function b(t, e) {
                        y(t, f(e) ? e.split(' ') : e, !0);
                    }
                    function w(t, e) {
                        g(e, t.appendChild.bind(t));
                    }
                    function E(t, e) {
                        g(t, function (t) {
                            var n = (e || t).parentNode;
                            n && n.insertBefore(t, e);
                        });
                    }
                    function S(t, e) {
                        return (
                            p(t) &&
                            (t.msMatchesSelector || t.matches).call(t, e)
                        );
                    }
                    function x(t, e) {
                        return (
                            (t = t ? n(t.children) : []),
                            e
                                ? t.filter(function (t) {
                                      return S(t, e);
                                  })
                                : t
                        );
                    }
                    function P(t, e) {
                        return e ? x(t, e)[0] : t.firstElementChild;
                    }
                    var C = Object.keys;
                    function _(t, e, n) {
                        t &&
                            (n ? C(t).reverse() : C(t)).forEach(function (n) {
                                '__proto__' !== n && e(t[n], n);
                            });
                    }
                    function A(t) {
                        return (
                            n(arguments, 1).forEach(function (e) {
                                _(e, function (n, i) {
                                    t[i] = e[i];
                                });
                            }),
                            t
                        );
                    }
                    function O(t) {
                        return (
                            n(arguments, 1).forEach(function (e) {
                                _(e, function (e, n) {
                                    c(e)
                                        ? (t[n] = e.slice())
                                        : a(e)
                                          ? (t[n] = O(
                                                {},
                                                a(t[n]) ? t[n] : {},
                                                e
                                            ))
                                          : (t[n] = e);
                                });
                            }),
                            t
                        );
                    }
                    function L(t, e) {
                        g(e || C(t), function (e) {
                            delete t[e];
                        });
                    }
                    function k(t, e) {
                        g(t, function (t) {
                            g(e, function (e) {
                                t && t.removeAttribute(e);
                            });
                        });
                    }
                    function M(t, e, n) {
                        a(e)
                            ? _(e, function (e, n) {
                                  M(t, n, e);
                              })
                            : g(t, function (t) {
                                  null === n || '' === n
                                      ? k(t, e)
                                      : t.setAttribute(e, String(n));
                              });
                    }
                    function N(t, e, n) {
                        return (
                            (t = document.createElement(t)),
                            e && (f(e) ? b : M)(t, e),
                            n && w(n, t),
                            t
                        );
                    }
                    function D(t, e, n) {
                        if (d(n)) return getComputedStyle(t)[e];
                        null === n || (t.style[e] = '' + n);
                    }
                    function j(t, e) {
                        D(t, 'display', e);
                    }
                    function z(t) {
                        (t.setActive && t.setActive()) ||
                            t.focus({ preventScroll: !0 });
                    }
                    function F(t, e) {
                        return t.getAttribute(e);
                    }
                    function I(t, e) {
                        return t && t.classList.contains(e);
                    }
                    function T(t) {
                        return t.getBoundingClientRect();
                    }
                    function R(t) {
                        g(t, function (t) {
                            t && t.parentNode && t.parentNode.removeChild(t);
                        });
                    }
                    function $(t) {
                        return P(
                            new DOMParser().parseFromString(t, 'text/html').body
                        );
                    }
                    function W(t, e) {
                        t.preventDefault(),
                            e &&
                                (t.stopPropagation(),
                                t.stopImmediatePropagation());
                    }
                    function X(t, e) {
                        return t && t.querySelector(e);
                    }
                    function G(t, e) {
                        return e ? n(t.querySelectorAll(e)) : [];
                    }
                    function B(t, e) {
                        y(t, e, !1);
                    }
                    function U(t) {
                        return t.timeStamp;
                    }
                    function H(t) {
                        return f(t) ? t : t ? t + 'px' : '';
                    }
                    var q = 'splide',
                        J = 'data-' + q;
                    function Y(t, e) {
                        if (!t) throw Error('[' + q + '] ' + (e || ''));
                    }
                    var K = Math.min,
                        V = Math.max,
                        Q = Math.floor,
                        Z = Math.ceil,
                        tt = Math.abs;
                    function te(t, e, n, i) {
                        var r = K(e, n),
                            e = V(e, n);
                        return i ? r < t && t < e : r <= t && t <= e;
                    }
                    function tn(t, e, n) {
                        var i = K(e, n),
                            e = V(e, n);
                        return K(V(i, t), e);
                    }
                    function ti(t, e) {
                        return (
                            g(e, function (e) {
                                t = t.replace('%s', '' + e);
                            }),
                            t
                        );
                    }
                    function tr(t) {
                        return t < 10 ? '0' + t : '' + t;
                    }
                    var to = {};
                    function ts() {
                        var t = [];
                        function n(t, e, n) {
                            g(t, function (t) {
                                t &&
                                    g(e, function (e) {
                                        e.split(' ').forEach(function (e) {
                                            n(t, (e = e.split('.'))[0], e[1]);
                                        });
                                    });
                            });
                        }
                        return {
                            bind: function (e, i, r, o) {
                                n(e, i, function (e, n, i) {
                                    var s = 'addEventListener' in e,
                                        u = s
                                            ? e.removeEventListener.bind(
                                                  e,
                                                  n,
                                                  r,
                                                  o
                                              )
                                            : e.removeListener.bind(e, r);
                                    s
                                        ? e.addEventListener(n, r, o)
                                        : e.addListener(r),
                                        t.push([e, n, i, r, u]);
                                });
                            },
                            unbind: function (e, i, r) {
                                n(e, i, function (e, n, i) {
                                    t = t.filter(function (t) {
                                        return (
                                            !!(
                                                t[0] !== e ||
                                                t[1] !== n ||
                                                t[2] !== i ||
                                                (r && t[3] !== r)
                                            ) || (t[4](), !1)
                                        );
                                    });
                                });
                            },
                            dispatch: function (t, e, n) {
                                var i;
                                return (
                                    'function' == typeof CustomEvent
                                        ? (i = new CustomEvent(e, {
                                              bubbles: !0,
                                              detail: n,
                                          }))
                                        : (i =
                                              document.createEvent(
                                                  'CustomEvent'
                                              )).initCustomEvent(e, !0, !1, n),
                                    t.dispatchEvent(i),
                                    i
                                );
                            },
                            destroy: function () {
                                t.forEach(function (t) {
                                    t[4]();
                                }),
                                    e(t);
                            },
                        };
                    }
                    var tu = 'mounted',
                        ta = 'move',
                        tc = 'moved',
                        tl = 'click',
                        tf = 'refresh',
                        td = 'updated',
                        tp = 'resize',
                        th = 'resized',
                        tg = 'scroll',
                        tv = 'scrolled',
                        tm = 'destroy',
                        ty = 'navigation:mounted',
                        tb = 'autoplay:play',
                        tw = 'autoplay:pause',
                        tE = 'lazyload:loaded';
                    function tS(t) {
                        var e = t
                                ? t.event.bus
                                : document.createDocumentFragment(),
                            r = ts();
                        return (
                            t && t.event.on(tm, r.destroy),
                            A(r, {
                                bus: e,
                                on: function (t, n) {
                                    r.bind(e, h(t).join(' '), function (t) {
                                        n.apply(n, c(t.detail) ? t.detail : []);
                                    });
                                },
                                off: i(r.unbind, e),
                                emit: function (t) {
                                    r.dispatch(e, t, n(arguments, 1));
                                },
                            })
                        );
                    }
                    function tx(t, e, n, i) {
                        var r,
                            o,
                            u = Date.now,
                            a = 0,
                            c = !0,
                            l = 0;
                        function f() {
                            if (!c) {
                                if (
                                    ((a = t ? K((u() - r) / t, 1) : 1),
                                    n && n(a),
                                    1 <= a && (e(), (r = u()), i && ++l >= i))
                                )
                                    return d();
                                o = s(f);
                            }
                        }
                        function d() {
                            c = !0;
                        }
                        function p() {
                            o && cancelAnimationFrame(o), (o = a = 0), (c = !0);
                        }
                        return {
                            start: function (e) {
                                e || p(),
                                    (r = u() - (e ? a * t : 0)),
                                    (c = !1),
                                    (o = s(f));
                            },
                            rewind: function () {
                                (r = u()), (a = 0), n && n(a);
                            },
                            pause: d,
                            cancel: p,
                            set: function (e) {
                                t = e;
                            },
                            isPaused: function () {
                                return c;
                            },
                        };
                    }
                    var tP = 'Arrow',
                        tC = tP + 'Left',
                        t_ = tP + 'Right',
                        tA = tP + 'Up',
                        tP = tP + 'Down',
                        tO = {
                            width: ['height'],
                            left: ['top', 'right'],
                            right: ['bottom', 'left'],
                            x: ['y'],
                            X: ['Y'],
                            Y: ['X'],
                            ArrowLeft: [tA, t_],
                            ArrowRight: [tP, tC],
                        },
                        tL = 'role',
                        tk = 'tabindex',
                        tM = 'aria-',
                        tN = tM + 'controls',
                        tD = tM + 'current',
                        tj = tM + 'selected',
                        tz = tM + 'label',
                        tF = tM + 'labelledby',
                        tI = tM + 'hidden',
                        tT = tM + 'orientation',
                        tR = tM + 'roledescription',
                        t$ = tM + 'live',
                        tW = tM + 'busy',
                        tX = tM + 'atomic',
                        tG = [tL, tk, 'disabled', tN, tD, tz, tF, tI, tT, tR],
                        tM = q + '__',
                        tB = tM + 'track',
                        tU = tM + 'list',
                        tH = tM + 'slide',
                        tq = tH + '--clone',
                        tJ = tH + '__container',
                        tY = tM + 'arrows',
                        tK = tM + 'arrow',
                        tV = tK + '--prev',
                        tQ = tK + '--next',
                        tZ = tM + 'pagination',
                        t0 = tZ + '__page',
                        t1 = tM + 'progress__bar',
                        t4 = tM + 'toggle',
                        t2 = tM + 'sr',
                        t5 = 'is-active',
                        t3 = 'is-prev',
                        t6 = 'is-next',
                        t8 = 'is-visible',
                        t7 = 'is-loading',
                        t9 = 'is-focus-in',
                        et = 'is-overflow',
                        ee = [t5, t8, t3, t6, t7, t9, et],
                        en = 'touchstart mousedown',
                        ei = 'touchmove mousemove',
                        er = 'touchend touchcancel mouseup click',
                        eo = 'slide',
                        es = 'loop',
                        eu = 'fade',
                        ea = J + '-interval',
                        ec = { passive: !1, capture: !0 },
                        el = {
                            Spacebar: ' ',
                            Right: t_,
                            Left: tC,
                            Up: tA,
                            Down: tP,
                        };
                    function ef(t) {
                        return el[(t = f(t) ? t : t.key)] || t;
                    }
                    var ed = 'keydown',
                        ep = J + '-lazy',
                        eh = ep + '-srcset',
                        eg = '[' + ep + '], [' + eh + ']',
                        ev = [' ', 'Enter'],
                        em = Object.freeze({
                            __proto__: null,
                            Media: function (e, n, i) {
                                var r = e.state,
                                    o = i.breakpoints || {},
                                    s = i.reducedMotion || {},
                                    u = ts(),
                                    a = [];
                                function c(t) {
                                    t && u.destroy();
                                }
                                function l(t, e) {
                                    (e = matchMedia(e)),
                                        u.bind(e, 'change', f),
                                        a.push([t, e]);
                                }
                                function f() {
                                    var t = r.is(7),
                                        n = i.direction,
                                        o = a.reduce(function (t, e) {
                                            return O(
                                                t,
                                                e[1].matches ? e[0] : {}
                                            );
                                        }, {});
                                    L(i),
                                        d(o),
                                        i.destroy
                                            ? e.destroy(
                                                  'completely' === i.destroy
                                              )
                                            : t
                                              ? (c(!0), e.mount())
                                              : n !== i.direction &&
                                                e.refresh();
                                }
                                function d(t, n, o) {
                                    O(i, t),
                                        n && O(Object.getPrototypeOf(i), t),
                                        (!o && r.is(1)) || e.emit(td, i);
                                }
                                return {
                                    setup: function () {
                                        var e = 'min' === i.mediaQuery;
                                        C(o)
                                            .sort(function (t, n) {
                                                return e ? +t - +n : +n - +t;
                                            })
                                            .forEach(function (t) {
                                                l(
                                                    o[t],
                                                    '(' +
                                                        (e ? 'min' : 'max') +
                                                        '-width:' +
                                                        t +
                                                        'px)'
                                                );
                                            }),
                                            l(s, t),
                                            f();
                                    },
                                    destroy: c,
                                    reduce: function (e) {
                                        matchMedia(t).matches &&
                                            (e ? O(i, s) : L(i, C(s)));
                                    },
                                    set: d,
                                };
                            },
                            Direction: function (t, e, n) {
                                return {
                                    resolve: function (t, e, i) {
                                        var r =
                                            'rtl' !== (i = i || n.direction) ||
                                            e
                                                ? 'ttb' === i
                                                    ? 0
                                                    : -1
                                                : 1;
                                        return (
                                            (tO[t] && tO[t][r]) ||
                                            t.replace(
                                                /width|left|right/i,
                                                function (t, e) {
                                                    return (
                                                        (t =
                                                            tO[t.toLowerCase()][
                                                                r
                                                            ] || t),
                                                        0 < e
                                                            ? t
                                                                  .charAt(0)
                                                                  .toUpperCase() +
                                                              t.slice(1)
                                                            : t
                                                    );
                                                }
                                            )
                                        );
                                    },
                                    orient: function (t) {
                                        return (
                                            t * ('rtl' === n.direction ? 1 : -1)
                                        );
                                    },
                                };
                            },
                            Elements: function (t, n, i) {
                                var r,
                                    o,
                                    s,
                                    u = tS(t),
                                    a = u.on,
                                    c = u.bind,
                                    f = t.root,
                                    d = i.i18n,
                                    p = {},
                                    h = [],
                                    g = [],
                                    v = [];
                                function w() {
                                    (o = P((r = O('.' + tB)), '.' + tU)),
                                        Y(
                                            r && o,
                                            'A track/list element is missing.'
                                        ),
                                        m(
                                            h,
                                            x(o, '.' + tH + ':not(.' + tq + ')')
                                        ),
                                        _(
                                            {
                                                arrows: tY,
                                                pagination: tZ,
                                                prev: tV,
                                                next: tQ,
                                                bar: t1,
                                                toggle: t4,
                                            },
                                            function (t, e) {
                                                p[e] = O('.' + t);
                                            }
                                        ),
                                        A(p, {
                                            root: f,
                                            track: r,
                                            list: o,
                                            slides: h,
                                        });
                                    var t =
                                            f.id ||
                                            '' +
                                                q +
                                                tr((to[q] = (to[q] || 0) + 1)),
                                        e = i.role;
                                    (f.id = t),
                                        (r.id = r.id || t + '-track'),
                                        (o.id = o.id || t + '-list'),
                                        !F(f, tL) &&
                                            'SECTION' !== f.tagName &&
                                            e &&
                                            M(f, tL, e),
                                        M(f, tR, d.carousel),
                                        M(o, tL, 'presentation'),
                                        C();
                                }
                                function E(t) {
                                    var n = tG.concat('style');
                                    e(h),
                                        B(f, g),
                                        B(r, v),
                                        k([r, o], n),
                                        k(f, t ? n : ['style', tR]);
                                }
                                function C() {
                                    B(f, g),
                                        B(r, v),
                                        (g = L(q)),
                                        (v = L(tB)),
                                        b(f, g),
                                        b(r, v),
                                        M(f, tz, i.label),
                                        M(f, tF, i.labelledby);
                                }
                                function O(t) {
                                    return (t = X(f, t)) &&
                                        (function (t, e) {
                                            if (l(t.closest))
                                                return t.closest(e);
                                            for (
                                                var n = t;
                                                n &&
                                                1 === n.nodeType &&
                                                !S(n, e);

                                            )
                                                n = n.parentElement;
                                            return n;
                                        })(t, '.' + q) === f
                                        ? t
                                        : void 0;
                                }
                                function L(t) {
                                    return [
                                        t + '--' + i.type,
                                        t + '--' + i.direction,
                                        i.drag && t + '--draggable',
                                        i.isNavigation && t + '--nav',
                                        t === q && t5,
                                    ];
                                }
                                return A(p, {
                                    setup: w,
                                    mount: function () {
                                        a(tf, E),
                                            a(tf, w),
                                            a(td, C),
                                            c(
                                                document,
                                                en + ' keydown',
                                                function (t) {
                                                    s = 'keydown' === t.type;
                                                },
                                                { capture: !0 }
                                            ),
                                            c(f, 'focusin', function () {
                                                y(f, t9, !!s);
                                            });
                                    },
                                    destroy: E,
                                });
                            },
                            Slides: function (t, n, r) {
                                var o = tS(t),
                                    s = o.on,
                                    u = o.emit,
                                    a = o.bind,
                                    c = (o = n.Elements).slides,
                                    d = o.list,
                                    m = [];
                                function x() {
                                    c.forEach(function (t, e) {
                                        _(t, e, -1);
                                    });
                                }
                                function C() {
                                    O(function (t) {
                                        t.destroy();
                                    }),
                                        e(m);
                                }
                                function _(e, n, r) {
                                    (n = (function (t, e, n, r) {
                                        var o,
                                            s = tS(t),
                                            u = s.on,
                                            a = s.emit,
                                            c = s.bind,
                                            l = t.Components,
                                            f = t.root,
                                            d = t.options,
                                            p = d.isNavigation,
                                            h = d.updateOnMove,
                                            g = d.i18n,
                                            v = d.pagination,
                                            m = d.slideFocus,
                                            b = l.Direction.resolve,
                                            w = F(r, 'style'),
                                            E = F(r, tz),
                                            S = -1 < n,
                                            x = P(r, '.' + tJ);
                                        function C() {
                                            var i = t.splides
                                                .map(function (t) {
                                                    return (t =
                                                        t.splide.Components.Slides.getAt(
                                                            e
                                                        ))
                                                        ? t.slide.id
                                                        : '';
                                                })
                                                .join(' ');
                                            M(
                                                r,
                                                tz,
                                                ti(g.slideX, (S ? n : e) + 1)
                                            ),
                                                M(r, tN, i),
                                                M(r, tL, m ? 'button' : ''),
                                                m && k(r, tR);
                                        }
                                        function _() {
                                            o || A();
                                        }
                                        function A() {
                                            var n, i, s;
                                            o ||
                                                ((n = t.index),
                                                (s = O()) !== I(r, t5) &&
                                                    (y(r, t5, s),
                                                    M(r, tD, (p && s) || ''),
                                                    a(
                                                        s
                                                            ? 'active'
                                                            : 'inactive',
                                                        L
                                                    )),
                                                (i =
                                                    !(s = (function () {
                                                        if (t.is(eu))
                                                            return O();
                                                        var e = T(
                                                                l.Elements.track
                                                            ),
                                                            n = T(r),
                                                            i = b('left', !0),
                                                            o = b('right', !0);
                                                        return (
                                                            Q(e[i]) <=
                                                                Z(n[i]) &&
                                                            Q(n[o]) <= Z(e[o])
                                                        );
                                                    })()) &&
                                                    (!O() || S)),
                                                t.state.is([4, 5]) ||
                                                    M(r, tI, i || ''),
                                                M(
                                                    G(
                                                        r,
                                                        d.focusableNodes || ''
                                                    ),
                                                    tk,
                                                    i ? -1 : ''
                                                ),
                                                m && M(r, tk, i ? -1 : 0),
                                                s !== I(r, t8) &&
                                                    (y(r, t8, s),
                                                    a(
                                                        s
                                                            ? 'visible'
                                                            : 'hidden',
                                                        L
                                                    )),
                                                s ||
                                                    document.activeElement !==
                                                        r ||
                                                    ((i = l.Slides.getAt(
                                                        t.index
                                                    )) &&
                                                        z(i.slide)),
                                                y(r, t3, e === n - 1),
                                                y(r, t6, e === n + 1));
                                        }
                                        function O() {
                                            var i = t.index;
                                            return (
                                                i === e ||
                                                (d.cloneStatus && i === n)
                                            );
                                        }
                                        var L = {
                                            index: e,
                                            slideIndex: n,
                                            slide: r,
                                            container: x,
                                            isClone: S,
                                            mount: function () {
                                                S ||
                                                    ((r.id =
                                                        f.id +
                                                        '-slide' +
                                                        tr(e + 1)),
                                                    M(
                                                        r,
                                                        tL,
                                                        v ? 'tabpanel' : 'group'
                                                    ),
                                                    M(r, tR, g.slide),
                                                    M(
                                                        r,
                                                        tz,
                                                        E ||
                                                            ti(g.slideLabel, [
                                                                e + 1,
                                                                t.length,
                                                            ])
                                                    )),
                                                    c(r, 'click', i(a, tl, L)),
                                                    c(
                                                        r,
                                                        'keydown',
                                                        i(a, 'sk', L)
                                                    ),
                                                    u([tc, 'sh', tv], A),
                                                    u(ty, C),
                                                    h && u(ta, _);
                                            },
                                            destroy: function () {
                                                (o = !0),
                                                    s.destroy(),
                                                    B(r, ee),
                                                    k(r, tG),
                                                    M(r, 'style', w),
                                                    M(r, tz, E || '');
                                            },
                                            update: A,
                                            style: function (t, e, n) {
                                                D((n && x) || r, t, e);
                                            },
                                            isWithin: function (n, i) {
                                                return (
                                                    (n = tt(n - e)),
                                                    (n =
                                                        !S &&
                                                        (d.rewind || t.is(es))
                                                            ? K(n, t.length - n)
                                                            : n) <= i
                                                );
                                            },
                                        };
                                        return L;
                                    })(t, n, r, e)).mount(),
                                        m.push(n),
                                        m.sort(function (t, e) {
                                            return t.index - e.index;
                                        });
                                }
                                function A(t) {
                                    return t
                                        ? L(function (t) {
                                              return !t.isClone;
                                          })
                                        : m;
                                }
                                function O(t, e) {
                                    A(e).forEach(t);
                                }
                                function L(t) {
                                    return m.filter(
                                        l(t)
                                            ? t
                                            : function (e) {
                                                  return f(t)
                                                      ? S(e.slide, t)
                                                      : v(h(t), e.index);
                                              }
                                    );
                                }
                                return {
                                    mount: function () {
                                        x(), s(tf, C), s(tf, x);
                                    },
                                    destroy: C,
                                    update: function () {
                                        O(function (t) {
                                            t.update();
                                        });
                                    },
                                    register: _,
                                    get: A,
                                    getIn: function (t) {
                                        var e = n.Controller,
                                            i = e.toIndex(t),
                                            o = e.hasFocus() ? 1 : r.perPage;
                                        return L(function (t) {
                                            return te(t.index, i, i + o - 1);
                                        });
                                    },
                                    getAt: function (t) {
                                        return L(t)[0];
                                    },
                                    add: function (t, e) {
                                        g(t, function (t) {
                                            var n, o, s;
                                            p((t = f(t) ? $(t) : t)) &&
                                                ((n = c[e]) ? E(t, n) : w(d, t),
                                                b(t, r.classes.slide),
                                                (n = t),
                                                (o = i(u, tp)),
                                                (s = (n = G(n, 'img')).length)
                                                    ? n.forEach(function (t) {
                                                          a(
                                                              t,
                                                              'load error',
                                                              function () {
                                                                  --s || o();
                                                              }
                                                          );
                                                      })
                                                    : o());
                                        }),
                                            u(tf);
                                    },
                                    remove: function (t) {
                                        R(
                                            L(t).map(function (t) {
                                                return t.slide;
                                            })
                                        ),
                                            u(tf);
                                    },
                                    forEach: O,
                                    filter: L,
                                    style: function (t, e, n) {
                                        O(function (i) {
                                            i.style(t, e, n);
                                        });
                                    },
                                    getLength: function (t) {
                                        return (t ? c : m).length;
                                    },
                                    isEnough: function () {
                                        return m.length > r.perPage;
                                    },
                                };
                            },
                            Layout: function (t, e, n) {
                                var r,
                                    o,
                                    s,
                                    u = (f = tS(t)).on,
                                    c = f.bind,
                                    l = f.emit,
                                    f = e.Slides,
                                    d = e.Direction.resolve,
                                    p = (e = e.Elements).root,
                                    h = e.track,
                                    g = e.list,
                                    v = f.getAt,
                                    m = f.style;
                                function b() {
                                    (r = 'ttb' === n.direction),
                                        D(p, 'maxWidth', H(n.width)),
                                        D(h, d('paddingLeft'), E(!1)),
                                        D(h, d('paddingRight'), E(!0)),
                                        w(!0);
                                }
                                function w(t) {
                                    var e,
                                        i = T(p);
                                    (t ||
                                        o.width !== i.width ||
                                        o.height !== i.height) &&
                                        (D(
                                            h,
                                            'height',
                                            ((e = ''),
                                            r &&
                                                (Y(
                                                    (e = S()),
                                                    'height or heightRatio is missing.'
                                                ),
                                                (e =
                                                    'calc(' +
                                                    e +
                                                    ' - ' +
                                                    E(!1) +
                                                    ' - ' +
                                                    E(!0) +
                                                    ')')),
                                            e)
                                        ),
                                        m(d('marginRight'), H(n.gap)),
                                        m(
                                            'width',
                                            n.autoWidth
                                                ? null
                                                : H(n.fixedWidth) ||
                                                      (r ? '' : x())
                                        ),
                                        m(
                                            'height',
                                            H(n.fixedHeight) ||
                                                (r
                                                    ? n.autoHeight
                                                        ? null
                                                        : x()
                                                    : S()),
                                            !0
                                        ),
                                        (o = i),
                                        l(th),
                                        s !== (s = L()) &&
                                            (y(p, et, s), l('overflow', s)));
                                }
                                function E(t) {
                                    var e = n.padding,
                                        t = d(t ? 'right' : 'left');
                                    return (
                                        (e && H(e[t] || (a(e) ? 0 : e))) ||
                                        '0px'
                                    );
                                }
                                function S() {
                                    return H(
                                        n.height || T(g).width * n.heightRatio
                                    );
                                }
                                function x() {
                                    var t = H(n.gap);
                                    return (
                                        'calc((100%' +
                                        (t && ' + ' + t) +
                                        ')/' +
                                        (n.perPage || 1) +
                                        (t && ' - ' + t) +
                                        ')'
                                    );
                                }
                                function P() {
                                    return T(g)[d('width')];
                                }
                                function C(t, e) {
                                    return (t = v(t || 0))
                                        ? T(t.slide)[d('width')] + (e ? 0 : O())
                                        : 0;
                                }
                                function _(t, e) {
                                    var t = v(t);
                                    return t
                                        ? tt(
                                              (t = T(t.slide)[d('right')]) -
                                                  T(g)[d('left')]
                                          ) + (e ? 0 : O())
                                        : 0;
                                }
                                function A(e) {
                                    return _(t.length - 1) - _(0) + C(0, e);
                                }
                                function O() {
                                    var t = v(0);
                                    return (
                                        (t &&
                                            parseFloat(
                                                D(t.slide, d('marginRight'))
                                            )) ||
                                        0
                                    );
                                }
                                function L() {
                                    return t.is(eu) || A(!0) > P();
                                }
                                return {
                                    mount: function () {
                                        var t, e;
                                        b(),
                                            c(
                                                window,
                                                'resize load',
                                                ((e = tx(
                                                    t || 0,
                                                    i(l, tp),
                                                    null,
                                                    1
                                                )),
                                                function () {
                                                    e.isPaused() && e.start();
                                                })
                                            ),
                                            u([td, tf], b),
                                            u(tp, w);
                                    },
                                    resize: w,
                                    listSize: P,
                                    slideSize: C,
                                    sliderSize: A,
                                    totalSize: _,
                                    getPadding: function (t) {
                                        return (
                                            parseFloat(
                                                D(
                                                    h,
                                                    d(
                                                        'padding' +
                                                            (t
                                                                ? 'Right'
                                                                : 'Left')
                                                    )
                                                )
                                            ) || 0
                                        );
                                    },
                                    isOverflow: L,
                                };
                            },
                            Clones: function (t, n, i) {
                                var r,
                                    o = tS(t),
                                    s = o.on,
                                    u = n.Elements,
                                    a = n.Slides,
                                    c = n.Direction.resolve,
                                    l = [];
                                function f() {
                                    if ((s(tf, p), s([td, tp], g), (r = v()))) {
                                        var e = r,
                                            o = a.get().slice(),
                                            c = o.length;
                                        if (c) {
                                            for (; o.length < e; ) m(o, o);
                                            m(
                                                o.slice(-e),
                                                o.slice(0, e)
                                            ).forEach(function (n, r) {
                                                var s,
                                                    f = r < e,
                                                    d =
                                                        (b(
                                                            (s = (s =
                                                                n.slide).cloneNode(
                                                                !0
                                                            )),
                                                            i.classes.clone
                                                        ),
                                                        (s.id =
                                                            t.root.id +
                                                            '-clone' +
                                                            tr(r + 1)),
                                                        s);
                                                f
                                                    ? E(d, o[0].slide)
                                                    : w(u.list, d),
                                                    m(l, d),
                                                    a.register(
                                                        d,
                                                        r - e + (f ? 0 : c),
                                                        n.index
                                                    );
                                            });
                                        }
                                        n.Layout.resize(!0);
                                    }
                                }
                                function p() {
                                    h(), f();
                                }
                                function h() {
                                    R(l), e(l), o.destroy();
                                }
                                function g() {
                                    var t = v();
                                    r !== t && (r < t || !t) && o.emit(tf);
                                }
                                function v() {
                                    var e,
                                        r = i.clones;
                                    return (
                                        t.is(es)
                                            ? d(r) &&
                                              (r =
                                                  ((e =
                                                      i[c('fixedWidth')] &&
                                                      n.Layout.slideSize(0)) &&
                                                      Z(
                                                          T(u.track)[
                                                              c('width')
                                                          ] / e
                                                      )) ||
                                                  (i[c('autoWidth')] &&
                                                      t.length) ||
                                                  2 * i.perPage)
                                            : (r = 0),
                                        r
                                    );
                                }
                                return { mount: f, destroy: h };
                            },
                            Move: function (t, e, n) {
                                var i,
                                    r = tS(t),
                                    o = r.on,
                                    s = r.emit,
                                    u = t.state.set,
                                    a = (r = e.Layout).slideSize,
                                    c = r.getPadding,
                                    l = r.totalSize,
                                    f = r.listSize,
                                    p = r.sliderSize,
                                    h = (r = e.Direction).resolve,
                                    g = r.orient,
                                    v = (r = e.Elements).list,
                                    m = r.track;
                                function y() {
                                    e.Controller.isBusy() ||
                                        (e.Scroll.cancel(),
                                        b(t.index),
                                        e.Slides.update());
                                }
                                function b(t) {
                                    w(P(t, !0));
                                }
                                function w(n, i) {
                                    var r, o, u;
                                    t.is(eu) ||
                                        ((i = i
                                            ? n
                                            : ((r = n),
                                              t.is(es) &&
                                                  ((u =
                                                      (o = x(r)) >
                                                      e.Controller.getEnd()),
                                                  (o < 0 || u) &&
                                                      (r = E(r, u))),
                                              r)),
                                        D(
                                            v,
                                            'transform',
                                            'translate' +
                                                h('X') +
                                                '(' +
                                                i +
                                                'px)'
                                        ),
                                        n !== i && s('sh'));
                                }
                                function E(t, e) {
                                    var n = t - _(e),
                                        i = p();
                                    return (
                                        t -
                                        g(i * (Z(tt(n) / i) || 1)) *
                                            (e ? 1 : -1)
                                    );
                                }
                                function S() {
                                    w(C(), !0), i.cancel();
                                }
                                function x(t) {
                                    for (
                                        var n = e.Slides.get(),
                                            i = 0,
                                            r = 1 / 0,
                                            o = 0;
                                        o < n.length;
                                        o++
                                    ) {
                                        var s = n[o].index,
                                            u = tt(P(s, !0) - t);
                                        if (!(u <= r)) break;
                                        (r = u), (i = s);
                                    }
                                    return i;
                                }
                                function P(e, i) {
                                    var r = g(
                                        l(e - 1) -
                                            ('center' === (r = n.focus)
                                                ? (f() - a(e, !0)) / 2
                                                : +r * a(e) || 0)
                                    );
                                    return i
                                        ? ((e = r),
                                          (e =
                                              n.trimSpace && t.is(eo)
                                                  ? tn(e, 0, g(p(!0) - f()))
                                                  : e))
                                        : r;
                                }
                                function C() {
                                    var t = h('left');
                                    return T(v)[t] - T(m)[t] + g(c(!1));
                                }
                                function _(t) {
                                    return P(
                                        t ? e.Controller.getEnd() : 0,
                                        !!n.trimSpace
                                    );
                                }
                                return {
                                    mount: function () {
                                        (i = e.Transition),
                                            o([tu, th, td, tf], y);
                                    },
                                    move: function (t, e, n, r) {
                                        var o, a;
                                        t !== e &&
                                            ((o = n < t),
                                            (a = g(E(C(), o))),
                                            o
                                                ? 0 <= a
                                                : a <=
                                                  v[h('scrollWidth')] -
                                                      T(m)[h('width')]) &&
                                            (S(), w(E(C(), n < t), !0)),
                                            u(4),
                                            s(ta, e, n, t),
                                            i.start(e, function () {
                                                u(3), s(tc, e, n, t), r && r();
                                            });
                                    },
                                    jump: b,
                                    translate: w,
                                    shift: E,
                                    cancel: S,
                                    toIndex: x,
                                    toPosition: P,
                                    getPosition: C,
                                    getLimit: _,
                                    exceededLimit: function (t, e) {
                                        e = d(e) ? C() : e;
                                        var n = !0 !== t && g(e) < g(_(!1)),
                                            t = !1 !== t && g(e) > g(_(!0));
                                        return n || t;
                                    },
                                    reposition: y,
                                };
                            },
                            Controller: function (t, e, n) {
                                var r,
                                    o,
                                    s,
                                    u,
                                    a = tS(t),
                                    c = a.on,
                                    l = a.emit,
                                    p = e.Move,
                                    h = p.getPosition,
                                    g = p.getLimit,
                                    v = p.toPosition,
                                    m = (a = e.Slides).isEnough,
                                    y = a.getLength,
                                    b = n.omitEnd,
                                    w = t.is(es),
                                    E = t.is(eo),
                                    S = i(O, !1),
                                    x = i(O, !0),
                                    P = n.start || 0,
                                    C = P;
                                function _() {
                                    (o = y(!0)),
                                        (s = n.perMove),
                                        (u = n.perPage),
                                        (r = M());
                                    var t = tn(P, 0, b ? r : o - 1);
                                    t !== P && ((P = t), p.reposition());
                                }
                                function A() {
                                    r !== M() && l('ei');
                                }
                                function O(t, e) {
                                    var n = s || (z() ? 1 : u),
                                        n = L(
                                            P + n * (t ? -1 : 1),
                                            P,
                                            !(s || z())
                                        );
                                    return -1 === n &&
                                        E &&
                                        !(1 > tt(h() - g(!t)))
                                        ? t
                                            ? 0
                                            : r
                                        : e
                                          ? n
                                          : k(n);
                                }
                                function L(e, i, a) {
                                    var c;
                                    return (
                                        m() || z()
                                            ? ((c = (function (e) {
                                                  if (
                                                      E &&
                                                      'move' === n.trimSpace &&
                                                      e !== P
                                                  )
                                                      for (
                                                          var i = h();
                                                          i === v(e, !0) &&
                                                          te(
                                                              e,
                                                              0,
                                                              t.length - 1,
                                                              !n.rewind
                                                          );

                                                      )
                                                          e < P ? --e : ++e;
                                                  return e;
                                              })(e)) !== e &&
                                                  ((i = e), (e = c), (a = !1)),
                                              e < 0 || r < e
                                                  ? (e =
                                                        !s &&
                                                        (te(0, e, i, !0) ||
                                                            te(r, i, e, !0))
                                                            ? N(D(e))
                                                            : w
                                                              ? a
                                                                  ? e < 0
                                                                      ? -(
                                                                            o %
                                                                                u ||
                                                                            u
                                                                        )
                                                                      : o
                                                                  : e
                                                              : n.rewind
                                                                ? e < 0
                                                                    ? r
                                                                    : 0
                                                                : -1)
                                                  : a &&
                                                    e !== i &&
                                                    (e = N(
                                                        D(i) + (e < i ? -1 : 1)
                                                    )))
                                            : (e = -1),
                                        e
                                    );
                                }
                                function k(t) {
                                    return w ? (t + o) % o || 0 : t;
                                }
                                function M() {
                                    for (
                                        var t = o - (z() || (w && s) ? 1 : u);
                                        b && 0 < t--;

                                    )
                                        if (v(o - 1, !0) !== v(t, !0)) {
                                            t++;
                                            break;
                                        }
                                    return tn(t, 0, o - 1);
                                }
                                function N(t) {
                                    return tn(z() ? t : u * t, 0, r);
                                }
                                function D(t) {
                                    return z()
                                        ? K(t, r)
                                        : Q((r <= t ? o - 1 : t) / u);
                                }
                                function j(t) {
                                    t !== P && ((C = P), (P = t));
                                }
                                function z() {
                                    return !d(n.focus) || n.isNavigation;
                                }
                                function F() {
                                    return (
                                        t.state.is([4, 5]) &&
                                        !!n.waitForTransition
                                    );
                                }
                                return {
                                    mount: function () {
                                        _(), c([td, tf, 'ei'], _), c(th, A);
                                    },
                                    go: function (t, e, n) {
                                        var i, o, s, u, a;
                                        F() ||
                                            (-1 <
                                                (i = k(
                                                    ((o = t),
                                                    (a = P),
                                                    f(o)
                                                        ? ((s = (u =
                                                              o.match(
                                                                  /([+\-<>])(\d+)?/
                                                              ) || [])[1]),
                                                          (u = u[2]),
                                                          '+' === s || '-' === s
                                                              ? (a = L(
                                                                    P +
                                                                        +(
                                                                            '' +
                                                                            s +
                                                                            (+u ||
                                                                                1)
                                                                        ),
                                                                    P
                                                                ))
                                                              : '>' === s
                                                                ? (a = u
                                                                      ? N(+u)
                                                                      : S(!0))
                                                                : '<' === s &&
                                                                  (a = x(!0)))
                                                        : (a = w
                                                              ? o
                                                              : tn(o, 0, r)),
                                                    (t = a))
                                                )) &&
                                                (e || i !== P) &&
                                                (j(i), p.move(t, i, C, n)));
                                    },
                                    scroll: function (t, n, i, o) {
                                        e.Scroll.scroll(t, n, i, function () {
                                            var t = k(p.toIndex(h()));
                                            j(b ? K(t, r) : t), o && o();
                                        });
                                    },
                                    getNext: S,
                                    getPrev: x,
                                    getAdjacent: O,
                                    getEnd: M,
                                    setIndex: j,
                                    getIndex: function (t) {
                                        return t ? C : P;
                                    },
                                    toIndex: N,
                                    toPage: D,
                                    toDest: function (t) {
                                        return (
                                            (t = p.toIndex(t)),
                                            E ? tn(t, 0, r) : t
                                        );
                                    },
                                    hasFocus: z,
                                    isBusy: F,
                                };
                            },
                            Arrows: function (t, e, n) {
                                var r,
                                    o,
                                    s = tS(t),
                                    u = s.on,
                                    a = s.bind,
                                    c = s.emit,
                                    l = n.classes,
                                    f = n.i18n,
                                    d = e.Elements,
                                    p = e.Controller,
                                    h = d.arrows,
                                    g = d.track,
                                    v = h,
                                    m = d.prev,
                                    y = d.next,
                                    S = {};
                                function x() {
                                    var t = n.arrows;
                                    !t ||
                                        (m && y) ||
                                        ((v = h || N('div', l.arrows)),
                                        (m = O(!0)),
                                        (y = O(!1)),
                                        (r = !0),
                                        w(v, [m, y]),
                                        h || E(v, g)),
                                        m &&
                                            y &&
                                            (A(S, { prev: m, next: y }),
                                            j(v, t ? '' : 'none'),
                                            b(v, (o = tY + '--' + n.direction)),
                                            t &&
                                                (u([tu, tc, tf, tv, 'ei'], L),
                                                a(y, 'click', i(_, '>')),
                                                a(m, 'click', i(_, '<')),
                                                L(),
                                                M([m, y], tN, g.id),
                                                c('arrows:mounted', m, y))),
                                        u(td, P);
                                }
                                function P() {
                                    C(), x();
                                }
                                function C() {
                                    s.destroy(),
                                        B(v, o),
                                        r
                                            ? (R(h ? [m, y] : v),
                                              (m = y = null))
                                            : k([m, y], tG);
                                }
                                function _(t) {
                                    p.go(t, !0);
                                }
                                function O(t) {
                                    return $(
                                        '<button class="' +
                                            l.arrow +
                                            ' ' +
                                            (t ? l.prev : l.next) +
                                            '" type="button"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40" focusable="false"><path d="' +
                                            (n.arrowPath ||
                                                'm15.5 0.932-4.3 4.38 14.5 14.6-14.5 14.5 4.3 4.4 14.6-14.6 4.4-4.3-4.4-4.4-14.6-14.6z') +
                                            '" />'
                                    );
                                }
                                function L() {
                                    var e, n, i, r;
                                    m &&
                                        y &&
                                        ((r = t.index),
                                        (e = p.getPrev()),
                                        (n = p.getNext()),
                                        (i = -1 < e && r < e ? f.last : f.prev),
                                        (r =
                                            -1 < n && n < r ? f.first : f.next),
                                        (m.disabled = e < 0),
                                        (y.disabled = n < 0),
                                        M(m, tz, i),
                                        M(y, tz, r),
                                        c('arrows:updated', m, y, e, n));
                                }
                                return {
                                    arrows: S,
                                    mount: x,
                                    destroy: C,
                                    update: L,
                                };
                            },
                            Autoplay: function (t, e, n) {
                                var i,
                                    r,
                                    o = tS(t),
                                    s = o.on,
                                    u = o.bind,
                                    a = o.emit,
                                    c = tx(
                                        n.interval,
                                        t.go.bind(t, '>'),
                                        function (t) {
                                            var e = f.bar;
                                            e && D(e, 'width', 100 * t + '%'),
                                                a('autoplay:playing', t);
                                        }
                                    ),
                                    l = c.isPaused,
                                    f = e.Elements,
                                    d = (o = e.Elements).root,
                                    p = o.toggle,
                                    h = n.autoplay,
                                    g = 'pause' === h;
                                function v() {
                                    l() &&
                                        e.Slides.isEnough() &&
                                        (c.start(!n.resetProgress),
                                        (r = i = g = !1),
                                        w(),
                                        a(tb));
                                }
                                function m(t) {
                                    (g = !!(t = void 0 === t || t)),
                                        w(),
                                        l() || (c.pause(), a(tw));
                                }
                                function b() {
                                    g || (i || r ? m(!1) : v());
                                }
                                function w() {
                                    p &&
                                        (y(p, t5, !g),
                                        M(p, tz, n.i18n[g ? 'play' : 'pause']));
                                }
                                function E(t) {
                                    (t = e.Slides.getAt(t)),
                                        c.set(
                                            (t && +F(t.slide, ea)) || n.interval
                                        );
                                }
                                return {
                                    mount: function () {
                                        h &&
                                            (n.pauseOnHover &&
                                                u(
                                                    d,
                                                    'mouseenter mouseleave',
                                                    function (t) {
                                                        (i =
                                                            'mouseenter' ===
                                                            t.type),
                                                            b();
                                                    }
                                                ),
                                            n.pauseOnFocus &&
                                                u(
                                                    d,
                                                    'focusin focusout',
                                                    function (t) {
                                                        (r =
                                                            'focusin' ===
                                                            t.type),
                                                            b();
                                                    }
                                                ),
                                            p &&
                                                u(p, 'click', function () {
                                                    g ? v() : m(!0);
                                                }),
                                            s([ta, tg, tf], c.rewind),
                                            s(ta, E),
                                            p && M(p, tN, f.track.id),
                                            g || v(),
                                            w());
                                    },
                                    destroy: c.cancel,
                                    play: v,
                                    pause: m,
                                    isPaused: l,
                                };
                            },
                            Cover: function (t, e, n) {
                                var r = tS(t).on;
                                function o(t) {
                                    e.Slides.forEach(function (e) {
                                        var n = P(
                                            e.container || e.slide,
                                            'img'
                                        );
                                        n && n.src && s(t, n, e);
                                    });
                                }
                                function s(t, e, n) {
                                    n.style(
                                        'background',
                                        t
                                            ? 'center/cover no-repeat url("' +
                                                  e.src +
                                                  '")'
                                            : '',
                                        !0
                                    ),
                                        j(e, t ? 'none' : '');
                                }
                                return {
                                    mount: function () {
                                        n.cover &&
                                            (r(tE, i(s, !0)),
                                            r([tu, td, tf], i(o, !0)));
                                    },
                                    destroy: i(o, !1),
                                };
                            },
                            Scroll: function (t, e, n) {
                                var r,
                                    o,
                                    s = tS(t),
                                    u = s.on,
                                    a = s.emit,
                                    c = t.state.set,
                                    l = e.Move,
                                    f = l.getPosition,
                                    d = l.getLimit,
                                    p = l.exceededLimit,
                                    h = l.translate,
                                    g = t.is(eo),
                                    v = 1;
                                function m(t, n, s, u, d) {
                                    var h,
                                        m,
                                        E = f(),
                                        s =
                                            (w(),
                                            !s ||
                                                (g && p()) ||
                                                ((s = e.Layout.sliderSize()),
                                                (m =
                                                    ((0 < (h = t)) - (h < 0)) *
                                                        s *
                                                        Q(tt(t) / s) || 0),
                                                (t =
                                                    l.toPosition(
                                                        e.Controller.toDest(
                                                            t % s
                                                        )
                                                    ) + m)),
                                            1 > tt(E - t));
                                    (v = 1),
                                        (n = s
                                            ? 0
                                            : n || V(tt(t - E) / 1.5, 800)),
                                        (o = u),
                                        (r = tx(n, y, i(b, E, t, d), 1)),
                                        c(5),
                                        a(tg),
                                        r.start();
                                }
                                function y() {
                                    c(3), o && o(), a(tv);
                                }
                                function b(t, e, i, r) {
                                    var s = f(),
                                        r =
                                            (t +
                                                (e - t) *
                                                    ((e = r),
                                                    (t = n.easingFunc)
                                                        ? t(e)
                                                        : 1 -
                                                          Math.pow(1 - e, 4)) -
                                                s) *
                                            v;
                                    h(s + r),
                                        g &&
                                            !i &&
                                            p() &&
                                            ((v *= 0.6),
                                            10 > tt(r) &&
                                                m(d(p(!0)), 600, !1, o, !0));
                                }
                                function w() {
                                    r && r.cancel();
                                }
                                function E() {
                                    r && !r.isPaused() && (w(), y());
                                }
                                return {
                                    mount: function () {
                                        u(ta, w), u([td, tf], E);
                                    },
                                    destroy: w,
                                    scroll: m,
                                    cancel: E,
                                };
                            },
                            Drag: function (t, e, n) {
                                var i,
                                    o,
                                    s,
                                    u,
                                    c,
                                    l,
                                    f,
                                    d,
                                    p = tS(t),
                                    h = p.on,
                                    g = p.emit,
                                    v = p.bind,
                                    m = p.unbind,
                                    y = t.state,
                                    b = e.Move,
                                    w = e.Scroll,
                                    E = e.Controller,
                                    x = e.Elements.track,
                                    P = e.Media.reduce,
                                    C = (p = e.Direction).resolve,
                                    _ = p.orient,
                                    A = b.getPosition,
                                    O = b.exceededLimit,
                                    L = !1;
                                function k() {
                                    var t = n.drag;
                                    (f = !t), (u = 'free' === t);
                                }
                                function M(t) {
                                    var e, i, r;
                                    (l = !1),
                                        f ||
                                            ((e = R(t)),
                                            (i = t.target),
                                            (r = n.noDrag),
                                            S(i, '.' + t0 + ', .' + tK) ||
                                                (r && S(i, r)) ||
                                                (!e && t.button) ||
                                                (E.isBusy()
                                                    ? W(t, !0)
                                                    : ((d = e ? x : window),
                                                      (c = y.is([4, 5])),
                                                      (s = null),
                                                      v(d, ei, N, ec),
                                                      v(d, er, D, ec),
                                                      b.cancel(),
                                                      w.cancel(),
                                                      z(t))));
                                }
                                function N(e) {
                                    var r, o, s, u, f;
                                    y.is(6) || (y.set(6), g('drag')),
                                        e.cancelable &&
                                            (c
                                                ? (b.translate(
                                                      i +
                                                          F(e) /
                                                              (L && t.is(eo)
                                                                  ? 5
                                                                  : 1)
                                                  ),
                                                  (f = 200 < I(e)),
                                                  (r = L !== (L = O())),
                                                  (f || r) && z(e),
                                                  (l = !0),
                                                  g('dragging'),
                                                  W(e))
                                                : tt(F((f = e))) >
                                                      tt(F(f, !0)) &&
                                                  ((r = e),
                                                  (u =
                                                      ((s = a(
                                                          (o =
                                                              n.dragMinThreshold)
                                                      )) &&
                                                          o.mouse) ||
                                                      0),
                                                  (s =
                                                      (s ? o.touch : +o) || 10),
                                                  (c =
                                                      tt(F(r)) >
                                                      (R(r) ? s : u)),
                                                  W(e)));
                                }
                                function D(i) {
                                    var r, o, s, a, l;
                                    y.is(6) && (y.set(3), g('dragged')),
                                        c &&
                                            ((a = r =
                                                (function (e) {
                                                    if (t.is(es) || !L) {
                                                        var n = I(e);
                                                        if (n && n < 200)
                                                            return F(e) / n;
                                                    }
                                                    return 0;
                                                })((r = i))),
                                            (o =
                                                A() +
                                                ((0 < a) - (a < 0)) *
                                                    K(
                                                        tt(a) *
                                                            (n.flickPower ||
                                                                600),
                                                        u
                                                            ? 1 / 0
                                                            : e.Layout.listSize() *
                                                                  (n.flickMaxPages ||
                                                                      1)
                                                    )),
                                            (s = n.rewind && n.rewindByDrag),
                                            P(!1),
                                            u
                                                ? E.scroll(o, 0, n.snap)
                                                : t.is(eu)
                                                  ? E.go(
                                                        0 >
                                                            _(
                                                                (0 < (l = r)) -
                                                                    (l < 0)
                                                            )
                                                            ? s
                                                                ? '<'
                                                                : '-'
                                                            : s
                                                              ? '>'
                                                              : '+'
                                                    )
                                                  : t.is(eo) && L && s
                                                    ? E.go(O(!0) ? '>' : '<')
                                                    : E.go(E.toDest(o), !0),
                                            P(!0),
                                            W(i)),
                                        m(d, ei, N),
                                        m(d, er, D),
                                        (c = !1);
                                }
                                function j(t) {
                                    !f && l && W(t, !0);
                                }
                                function z(t) {
                                    (s = o), (o = t), (i = A());
                                }
                                function F(t, e) {
                                    return T(t, e) - T((o === t && s) || o, e);
                                }
                                function I(t) {
                                    return U(t) - U((o === t && s) || o);
                                }
                                function T(t, e) {
                                    return (R(t) ? t.changedTouches[0] : t)[
                                        'page' + C(e ? 'Y' : 'X')
                                    ];
                                }
                                function R(t) {
                                    return (
                                        'undefined' != typeof TouchEvent &&
                                        t instanceof TouchEvent
                                    );
                                }
                                return {
                                    mount: function () {
                                        v(x, ei, r, ec),
                                            v(x, er, r, ec),
                                            v(x, en, M, ec),
                                            v(x, 'click', j, { capture: !0 }),
                                            v(x, 'dragstart', W),
                                            h([tu, td], k);
                                    },
                                    disable: function (t) {
                                        f = t;
                                    },
                                    isDragging: function () {
                                        return c;
                                    },
                                };
                            },
                            Keyboard: function (t, e, n) {
                                var i,
                                    r,
                                    s = tS(t),
                                    u = s.on,
                                    a = s.bind,
                                    c = s.unbind,
                                    l = t.root,
                                    f = e.Direction.resolve;
                                function d() {
                                    var t = n.keyboard;
                                    t &&
                                        a(
                                            (i = 'global' === t ? window : l),
                                            ed,
                                            g
                                        );
                                }
                                function p() {
                                    c(i, ed);
                                }
                                function h() {
                                    var t = r;
                                    (r = !0),
                                        o(function () {
                                            r = t;
                                        });
                                }
                                function g(e) {
                                    r ||
                                        ((e = ef(e)) === f(tC)
                                            ? t.go('<')
                                            : e === f(t_) && t.go('>'));
                                }
                                return {
                                    mount: function () {
                                        d(), u(td, p), u(td, d), u(ta, h);
                                    },
                                    destroy: p,
                                    disable: function (t) {
                                        r = t;
                                    },
                                };
                            },
                            LazyLoad: function (t, n, r) {
                                var o = tS(t),
                                    s = o.on,
                                    u = o.off,
                                    a = o.bind,
                                    c = o.emit,
                                    l = 'sequential' === r.lazyLoad,
                                    f = [tc, tv],
                                    d = [];
                                function p() {
                                    e(d),
                                        n.Slides.forEach(function (t) {
                                            G(t.slide, eg).forEach(
                                                function (e) {
                                                    var n = F(e, ep),
                                                        i = F(e, eh);
                                                    (n === e.src &&
                                                        i === e.srcset) ||
                                                        ((n =
                                                            r.classes.spinner),
                                                        (n =
                                                            P(
                                                                (i =
                                                                    e.parentElement),
                                                                '.' + n
                                                            ) ||
                                                            N('span', n, i)),
                                                        d.push([e, t, n]),
                                                        e.src || j(e, 'none'));
                                                }
                                            );
                                        }),
                                        (l ? m : (u(f), s(f, h), h))();
                                }
                                function h() {
                                    (d = d.filter(function (e) {
                                        var n =
                                            r.perPage *
                                                ((r.preloadPages || 1) + 1) -
                                            1;
                                        return (
                                            !e[1].isWithin(t.index, n) || g(e)
                                        );
                                    })).length || u(f);
                                }
                                function g(t) {
                                    var e = t[0];
                                    b(t[1].slide, t7),
                                        a(e, 'load error', i(v, t)),
                                        M(e, 'src', F(e, ep)),
                                        M(e, 'srcset', F(e, eh)),
                                        k(e, ep),
                                        k(e, eh);
                                }
                                function v(t, e) {
                                    var n = t[0],
                                        i = t[1];
                                    B(i.slide, t7),
                                        'error' !== e.type &&
                                            (R(t[2]),
                                            j(n, ''),
                                            c(tE, n, i),
                                            c(tp)),
                                        l && m();
                                }
                                function m() {
                                    d.length && g(d.shift());
                                }
                                return {
                                    mount: function () {
                                        r.lazyLoad && (p(), s(tf, p));
                                    },
                                    destroy: i(e, d),
                                    check: h,
                                };
                            },
                            Pagination: function (t, r, o) {
                                var s,
                                    u,
                                    a = tS(t),
                                    c = a.on,
                                    l = a.emit,
                                    f = a.bind,
                                    d = r.Slides,
                                    p = r.Elements,
                                    h = r.Controller,
                                    g = h.hasFocus,
                                    v = h.getIndex,
                                    m = h.go,
                                    y = r.Direction.resolve,
                                    w = p.pagination,
                                    E = [];
                                function S() {
                                    s &&
                                        (R(w ? n(s.children) : s),
                                        B(s, u),
                                        e(E),
                                        (s = null)),
                                        a.destroy();
                                }
                                function x(t) {
                                    m('>' + t, !0);
                                }
                                function P(t, e) {
                                    var n = E.length,
                                        i = ef(e),
                                        r = C(),
                                        o = -1,
                                        r =
                                            (i === y(t_, !1, r)
                                                ? (o = ++t % n)
                                                : i === y(tC, !1, r)
                                                  ? (o = (--t + n) % n)
                                                  : 'Home' === i
                                                    ? (o = 0)
                                                    : 'End' === i &&
                                                      (o = n - 1),
                                            E[o]);
                                    r && (z(r.button), m('>' + o), W(e, !0));
                                }
                                function C() {
                                    return o.paginationDirection || o.direction;
                                }
                                function _(t) {
                                    return E[h.toPage(t)];
                                }
                                function A() {
                                    var t,
                                        e = _(v(!0)),
                                        n = _(v());
                                    e &&
                                        (B((t = e.button), t5),
                                        k(t, tj),
                                        M(t, tk, -1)),
                                        n &&
                                            (b((t = n.button), t5),
                                            M(t, tj, !0),
                                            M(t, tk, '')),
                                        l(
                                            'pagination:updated',
                                            { list: s, items: E },
                                            e,
                                            n
                                        );
                                }
                                return {
                                    items: E,
                                    mount: function e() {
                                        S(), c([td, tf, 'ei'], e);
                                        var n = o.pagination;
                                        if ((w && j(w, n ? '' : 'none'), n)) {
                                            c([ta, tg, tv], A);
                                            var n = t.length,
                                                r = o.classes,
                                                a = o.i18n,
                                                v = o.perPage,
                                                m = g()
                                                    ? h.getEnd() + 1
                                                    : Z(n / v);
                                            b(
                                                (s =
                                                    w ||
                                                    N(
                                                        'ul',
                                                        r.pagination,
                                                        p.track.parentElement
                                                    )),
                                                (u = tZ + '--' + C())
                                            ),
                                                M(s, tL, 'tablist'),
                                                M(s, tz, a.select),
                                                M(
                                                    s,
                                                    tT,
                                                    'ttb' === C()
                                                        ? 'vertical'
                                                        : ''
                                                );
                                            for (var y = 0; y < m; y++) {
                                                var O = N('li', null, s),
                                                    L = N(
                                                        'button',
                                                        {
                                                            class: r.page,
                                                            type: 'button',
                                                        },
                                                        O
                                                    ),
                                                    k = d
                                                        .getIn(y)
                                                        .map(function (t) {
                                                            return t.slide.id;
                                                        }),
                                                    D =
                                                        !g() && 1 < v
                                                            ? a.pageX
                                                            : a.slideX;
                                                f(L, 'click', i(x, y)),
                                                    o.paginationKeyboard &&
                                                        f(
                                                            L,
                                                            'keydown',
                                                            i(P, y)
                                                        ),
                                                    M(O, tL, 'presentation'),
                                                    M(L, tL, 'tab'),
                                                    M(L, tN, k.join(' ')),
                                                    M(L, tz, ti(D, y + 1)),
                                                    M(L, tk, -1),
                                                    E.push({
                                                        li: O,
                                                        button: L,
                                                        page: y,
                                                    });
                                            }
                                            A(),
                                                l(
                                                    'pagination:mounted',
                                                    { list: s, items: E },
                                                    _(t.index)
                                                );
                                        }
                                    },
                                    destroy: S,
                                    getAt: _,
                                    update: A,
                                };
                            },
                            Sync: function (t, n, r) {
                                var o = r.isNavigation,
                                    s = r.slideFocus,
                                    u = [];
                                function a() {
                                    var e, n;
                                    t.splides.forEach(function (e) {
                                        e.isParent ||
                                            (l(t, e.splide), l(e.splide, t));
                                    }),
                                        o &&
                                            ((n = (e = tS(t)).on)(tl, p),
                                            n('sk', h),
                                            n([tu, td], f),
                                            u.push(e),
                                            e.emit(ty, t.splides));
                                }
                                function c() {
                                    u.forEach(function (t) {
                                        t.destroy();
                                    }),
                                        e(u);
                                }
                                function l(t, e) {
                                    (t = tS(t)).on(ta, function (t, n, i) {
                                        e.go(e.is(es) ? i : t);
                                    }),
                                        u.push(t);
                                }
                                function f() {
                                    M(
                                        n.Elements.list,
                                        tT,
                                        'ttb' === r.direction ? 'vertical' : ''
                                    );
                                }
                                function p(e) {
                                    t.go(e.index);
                                }
                                function h(t, e) {
                                    v(ev, ef(e)) && (p(t), W(e));
                                }
                                return {
                                    setup: i(
                                        n.Media.set,
                                        { slideFocus: d(s) ? o : s },
                                        !0
                                    ),
                                    mount: a,
                                    destroy: c,
                                    remount: function () {
                                        c(), a();
                                    },
                                };
                            },
                            Wheel: function (t, e, n) {
                                var i = tS(t).bind,
                                    r = 0;
                                function o(i) {
                                    var o, s, u, a, c;
                                    i.cancelable &&
                                        ((o = (c = i.deltaY) < 0),
                                        (s = U(i)),
                                        (u = n.wheelMinThreshold || 0),
                                        (a = n.wheelSleep || 0),
                                        tt(c) > u &&
                                            a < s - r &&
                                            (t.go(o ? '<' : '>'), (r = s)),
                                        (c = o),
                                        (n.releaseWheel &&
                                            !t.state.is(4) &&
                                            -1 ===
                                                e.Controller.getAdjacent(c)) ||
                                            W(i));
                                }
                                return {
                                    mount: function () {
                                        n.wheel &&
                                            i(e.Elements.track, 'wheel', o, ec);
                                    },
                                };
                            },
                            Live: function (t, e, n) {
                                var r = tS(t).on,
                                    o = e.Elements.track,
                                    s = n.live && !n.isNavigation,
                                    u = N('span', t2),
                                    a = tx(90, i(c, !1));
                                function c(t) {
                                    M(o, tW, t),
                                        t
                                            ? (w(o, u), a.start())
                                            : (R(u), a.cancel());
                                }
                                function l(t) {
                                    s && M(o, t$, t ? 'off' : 'polite');
                                }
                                return {
                                    mount: function () {
                                        s &&
                                            (l(!e.Autoplay.isPaused()),
                                            M(o, tX, !0),
                                            (u.textContent = '…'),
                                            r(tb, i(l, !0)),
                                            r(tw, i(l, !1)),
                                            r([tc, tv], i(c, !0)));
                                    },
                                    disable: l,
                                    destroy: function () {
                                        k(o, [t$, tX, tW]), R(u);
                                    },
                                };
                            },
                        }),
                        ey = {
                            type: 'slide',
                            role: 'region',
                            speed: 400,
                            perPage: 1,
                            cloneStatus: !0,
                            arrows: !0,
                            pagination: !0,
                            paginationKeyboard: !0,
                            interval: 5e3,
                            pauseOnHover: !0,
                            pauseOnFocus: !0,
                            resetProgress: !0,
                            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                            drag: !0,
                            direction: 'ltr',
                            trimSpace: !0,
                            focusableNodes:
                                'a, button, textarea, input, select, iframe',
                            live: !0,
                            classes: {
                                slide: tH,
                                clone: tq,
                                arrows: tY,
                                arrow: tK,
                                prev: tV,
                                next: tQ,
                                pagination: tZ,
                                page: t0,
                                spinner: tM + 'spinner',
                            },
                            i18n: {
                                prev: 'Previous slide',
                                next: 'Next slide',
                                first: 'Go to first slide',
                                last: 'Go to last slide',
                                slideX: 'Go to slide %s',
                                pageX: 'Go to page %s',
                                play: 'Start autoplay',
                                pause: 'Pause autoplay',
                                carousel: 'carousel',
                                slide: 'slide',
                                select: 'Select a slide to show',
                                slideLabel: '%s of %s',
                            },
                            reducedMotion: {
                                speed: 0,
                                rewindSpeed: 0,
                                autoplay: 'pause',
                            },
                        };
                    function eb(t, e, n) {
                        var i = e.Slides;
                        function s() {
                            i.forEach(function (t) {
                                t.style(
                                    'transform',
                                    'translateX(-' + 100 * t.index + '%)'
                                );
                            });
                        }
                        return {
                            mount: function () {
                                tS(t).on([tu, tf], s);
                            },
                            start: function (t, e) {
                                i.style(
                                    'transition',
                                    'opacity ' + n.speed + 'ms ' + n.easing
                                ),
                                    o(e);
                            },
                            cancel: r,
                        };
                    }
                    function ew(t, e, n) {
                        var r,
                            o = e.Move,
                            s = e.Controller,
                            u = e.Scroll,
                            a = e.Elements.list,
                            c = i(D, a, 'transition');
                        function l() {
                            c(''), u.cancel();
                        }
                        return {
                            mount: function () {
                                tS(t).bind(a, 'transitionend', function (t) {
                                    t.target === a && r && (l(), r());
                                });
                            },
                            start: function (e, i) {
                                var a = o.toPosition(e, !0),
                                    l = o.getPosition(),
                                    f = (function (e) {
                                        var i = n.rewindSpeed;
                                        if (t.is(eo) && i) {
                                            var r = s.getIndex(!0),
                                                o = s.getEnd();
                                            if (
                                                (0 === r && o <= e) ||
                                                (o <= r && 0 === e)
                                            )
                                                return i;
                                        }
                                        return n.speed;
                                    })(e);
                                1 <= tt(a - l) && 1 <= f
                                    ? n.useScroll
                                        ? u.scroll(a, f, !1, i)
                                        : (c(
                                              'transform ' +
                                                  f +
                                                  'ms ' +
                                                  n.easing
                                          ),
                                          o.translate(a, !0),
                                          (r = i))
                                    : (o.jump(e), i());
                            },
                            cancel: l,
                        };
                    }
                    return (
                        ((tA = (function () {
                            function t(e, n) {
                                var i;
                                (this.event = tS()),
                                    (this.Components = {}),
                                    (this.state =
                                        ((i = 1),
                                        {
                                            set: function (t) {
                                                i = t;
                                            },
                                            is: function (t) {
                                                return v(h(t), i);
                                            },
                                        })),
                                    (this.splides = []),
                                    (this.n = {}),
                                    (this.t = {}),
                                    Y(
                                        (e = f(e) ? X(document, e) : e),
                                        e + ' is invalid.'
                                    ),
                                    (n = O(
                                        {
                                            label: F((this.root = e), tz) || '',
                                            labelledby: F(e, tF) || '',
                                        },
                                        ey,
                                        t.defaults,
                                        n || {}
                                    ));
                                try {
                                    O(n, JSON.parse(F(e, J)));
                                } catch (t) {
                                    Y(!1, 'Invalid JSON');
                                }
                                this.n = Object.create(O({}, n));
                            }
                            var i,
                                r = t.prototype;
                            return (
                                (r.mount = function (t, e) {
                                    var n = this,
                                        i = this.state,
                                        r = this.Components;
                                    return (
                                        Y(i.is([1, 7]), 'Already mounted!'),
                                        i.set(1),
                                        (this.i = r),
                                        (this.r =
                                            e ||
                                            this.r ||
                                            (this.is(eu) ? eb : ew)),
                                        (this.t = t || this.t),
                                        _(
                                            A({}, em, this.t, {
                                                Transition: this.r,
                                            }),
                                            function (t, e) {
                                                (t = t(n, r, n.n)),
                                                    (r[e] = t).setup &&
                                                        t.setup();
                                            }
                                        ),
                                        _(r, function (t) {
                                            t.mount && t.mount();
                                        }),
                                        this.emit(tu),
                                        b(this.root, 'is-initialized'),
                                        i.set(3),
                                        this.emit('ready'),
                                        this
                                    );
                                }),
                                (r.sync = function (t) {
                                    return (
                                        this.splides.push({ splide: t }),
                                        t.splides.push({
                                            splide: this,
                                            isParent: !0,
                                        }),
                                        this.state.is(3) &&
                                            (this.i.Sync.remount(),
                                            t.Components.Sync.remount()),
                                        this
                                    );
                                }),
                                (r.go = function (t) {
                                    return this.i.Controller.go(t), this;
                                }),
                                (r.on = function (t, e) {
                                    return this.event.on(t, e), this;
                                }),
                                (r.off = function (t) {
                                    return this.event.off(t), this;
                                }),
                                (r.emit = function (t) {
                                    var e;
                                    return (
                                        (e = this.event).emit.apply(
                                            e,
                                            [t].concat(n(arguments, 1))
                                        ),
                                        this
                                    );
                                }),
                                (r.add = function (t, e) {
                                    return this.i.Slides.add(t, e), this;
                                }),
                                (r.remove = function (t) {
                                    return this.i.Slides.remove(t), this;
                                }),
                                (r.is = function (t) {
                                    return this.n.type === t;
                                }),
                                (r.refresh = function () {
                                    return this.emit(tf), this;
                                }),
                                (r.destroy = function (t) {
                                    void 0 === t && (t = !0);
                                    var n = this.event,
                                        i = this.state;
                                    return (
                                        i.is(1)
                                            ? tS(this).on(
                                                  'ready',
                                                  this.destroy.bind(this, t)
                                              )
                                            : (_(
                                                  this.i,
                                                  function (e) {
                                                      e.destroy && e.destroy(t);
                                                  },
                                                  !0
                                              ),
                                              n.emit(tm),
                                              n.destroy(),
                                              t && e(this.splides),
                                              i.set(7)),
                                        this
                                    );
                                }),
                                (i = [
                                    {
                                        key: 'options',
                                        get: function () {
                                            return this.n;
                                        },
                                        set: function (t) {
                                            this.i.Media.set(t, !0, !0);
                                        },
                                    },
                                    {
                                        key: 'length',
                                        get: function () {
                                            return this.i.Slides.getLength(!0);
                                        },
                                    },
                                    {
                                        key: 'index',
                                        get: function () {
                                            return this.i.Controller.getIndex();
                                        },
                                    },
                                ]),
                                (function (t, e) {
                                    for (var n = 0; n < e.length; n++) {
                                        var i = e[n];
                                        (i.enumerable = i.enumerable || !1),
                                            (i.configurable = !0),
                                            'value' in i && (i.writable = !0),
                                            Object.defineProperty(t, i.key, i);
                                    }
                                })(t.prototype, i),
                                Object.defineProperty(t, 'prototype', {
                                    writable: !1,
                                }),
                                t
                            );
                        })()).defaults = {}),
                        (tA.STATES = {
                            CREATED: 1,
                            MOUNTED: 2,
                            IDLE: 3,
                            MOVING: 4,
                            SCROLLING: 5,
                            DRAGGING: 6,
                            DESTROYED: 7,
                        }),
                        tA
                    );
                }),
                    (t.exports = e());
            },
            156: (t) => {
                'use strict';
                var e = function (t) {
                        var e;
                        return (
                            !!t &&
                            'object' == typeof t &&
                            '[object RegExp]' !==
                                (e = Object.prototype.toString.call(t)) &&
                            '[object Date]' !== e &&
                            t.$$typeof !== n
                        );
                    },
                    n =
                        'function' == typeof Symbol && Symbol.for
                            ? Symbol.for('react.element')
                            : 60103;
                function i(t, e) {
                    return !1 !== e.clone && e.isMergeableObject(t)
                        ? u(Array.isArray(t) ? [] : {}, t, e)
                        : t;
                }
                function r(t, e, n) {
                    return t.concat(e).map(function (t) {
                        return i(t, n);
                    });
                }
                function o(t) {
                    return Object.keys(t).concat(
                        Object.getOwnPropertySymbols
                            ? Object.getOwnPropertySymbols(t).filter(
                                  function (e) {
                                      return Object.propertyIsEnumerable.call(
                                          t,
                                          e
                                      );
                                  }
                              )
                            : []
                    );
                }
                function s(t, e) {
                    try {
                        return e in t;
                    } catch (t) {
                        return !1;
                    }
                }
                function u(t, n, a) {
                    ((a = a || {}).arrayMerge = a.arrayMerge || r),
                        (a.isMergeableObject = a.isMergeableObject || e),
                        (a.cloneUnlessOtherwiseSpecified = i);
                    var c,
                        l,
                        f = Array.isArray(n);
                    return f !== Array.isArray(t)
                        ? i(n, a)
                        : f
                          ? a.arrayMerge(t, n, a)
                          : ((l = {}),
                            (c = a).isMergeableObject(t) &&
                                o(t).forEach(function (e) {
                                    l[e] = i(t[e], c);
                                }),
                            o(n).forEach(function (e) {
                                (!s(t, e) ||
                                    (Object.hasOwnProperty.call(t, e) &&
                                        Object.propertyIsEnumerable.call(
                                            t,
                                            e
                                        ))) &&
                                    (s(t, e) && c.isMergeableObject(n[e])
                                        ? (l[e] = (function (t, e) {
                                              if (!e.customMerge) return u;
                                              var n = e.customMerge(t);
                                              return 'function' == typeof n
                                                  ? n
                                                  : u;
                                          })(e, c)(t[e], n[e], c))
                                        : (l[e] = i(n[e], c)));
                            }),
                            l);
                }
                (u.all = function (t, e) {
                    if (!Array.isArray(t))
                        throw Error('first argument should be an array');
                    return t.reduce(function (t, n) {
                        return u(t, n, e);
                    }, {});
                }),
                    (t.exports = u);
            },
        },
        e = {};
    function n(i) {
        var r = e[i];
        if (void 0 !== r) return r.exports;
        var o = (e[i] = { exports: {} });
        return t[i].call(o.exports, o, o.exports, n), o.exports;
    }
    (n.n = (t) => {
        var e = t && t.__esModule ? () => t.default : () => t;
        return n.d(e, { a: e }), e;
    }),
        (n.d = (t, e) => {
            for (var i in e)
                n.o(e, i) &&
                    !n.o(t, i) &&
                    Object.defineProperty(t, i, { enumerable: !0, get: e[i] });
        }),
        (n.o = (t, e) => Object.prototype.hasOwnProperty.call(t, e)),
        (() => {
            'use strict';
            var t = n(156),
                e = n.n(t);
            class i {
                static ucFirst(t) {
                    return t.charAt(0).toUpperCase() + t.slice(1);
                }
                static lcFirst(t) {
                    return t.charAt(0).toLowerCase() + t.slice(1);
                }
                static toDashCase(t) {
                    return t
                        .replace(/([A-Z])/g, '-$1')
                        .replace(/^-/, '')
                        .toLowerCase();
                }
                static toLowerCamelCase(t, e) {
                    let n = i.toUpperCamelCase(t, e);
                    return i.lcFirst(n);
                }
                static toUpperCamelCase(t, e) {
                    return e
                        ? t
                              .split(e)
                              .map((t) => i.ucFirst(t.toLowerCase()))
                              .join('')
                        : i.ucFirst(t.toLowerCase());
                }
                static parsePrimitive(t) {
                    try {
                        return (
                            /^\d+(.|,)\d+$/.test(t) &&
                                (t = t.replace(',', '.')),
                            JSON.parse(t)
                        );
                    } catch (e) {
                        return t.toString();
                    }
                }
            }
            class r {
                constructor(t = document) {
                    (this._el = t), (t.$emitter = this), (this._listeners = []);
                }
                publish(t) {
                    let e =
                            arguments.length > 1 && void 0 !== arguments[1]
                                ? arguments[1]
                                : {},
                        n =
                            arguments.length > 2 &&
                            void 0 !== arguments[2] &&
                            arguments[2],
                        i = new CustomEvent(t, { detail: e, cancelable: n });
                    return this.el.dispatchEvent(i), i;
                }
                subscribe(t, e) {
                    let n =
                            arguments.length > 2 && void 0 !== arguments[2]
                                ? arguments[2]
                                : {},
                        i = this,
                        r = t.split('.'),
                        o = n.scope ? e.bind(n.scope) : e;
                    if (n.once && !0 === n.once) {
                        let e = o;
                        o = function (n) {
                            i.unsubscribe(t), e(n);
                        };
                    }
                    return (
                        this.el.addEventListener(r[0], o),
                        this.listeners.push({
                            splitEventName: r,
                            opts: n,
                            cb: o,
                        }),
                        !0
                    );
                }
                unsubscribe(t) {
                    let e = t.split('.');
                    return (
                        (this.listeners = this.listeners.reduce(
                            (t, n) => (
                                [...n.splitEventName].sort().toString() ===
                                e.sort().toString()
                                    ? this.el.removeEventListener(
                                          n.splitEventName[0],
                                          n.cb
                                      )
                                    : t.push(n),
                                t
                            ),
                            []
                        )),
                        !0
                    );
                }
                reset() {
                    return (
                        this.listeners.forEach((t) => {
                            this.el.removeEventListener(
                                t.splitEventName[0],
                                t.cb
                            );
                        }),
                        (this.listeners = []),
                        !0
                    );
                }
                get el() {
                    return this._el;
                }
                set el(t) {
                    this._el = t;
                }
                get listeners() {
                    return this._listeners;
                }
                set listeners(t) {
                    this._listeners = t;
                }
            }
            class o {
                constructor(t, e = {}, n = !1) {
                    if (!(t instanceof Node))
                        throw Error('There is no valid element given.');
                    (this.el = t),
                        (this.$emitter = new r(this.el)),
                        (this._pluginName = this._getPluginName(n)),
                        (this.options = this._mergeOptions(e)),
                        (this._initialized = !1),
                        this._registerInstance(),
                        this._init();
                }
                init() {
                    throw Error(
                        `The "init" method for the plugin "${this._pluginName}" is not defined.`
                    );
                }
                update() {}
                _init() {
                    this._initialized ||
                        (this.init(), (this._initialized = !0));
                }
                _update() {
                    this._initialized && this.update();
                }
                _mergeOptions(t) {
                    let n = [this.constructor.options, this.options, t];
                    return (
                        n.push(this._getConfigFromDataAttribute()),
                        n.push(this._getOptionsFromDataAttribute()),
                        e().all(
                            n
                                .filter(
                                    (t) =>
                                        t instanceof Object &&
                                        !(t instanceof Array)
                                )
                                .map((t) => t || {})
                        )
                    );
                }
                _getConfigFromDataAttribute() {
                    let t = {};
                    if ('function' != typeof this.el.getAttribute) return t;
                    let e = i.toDashCase(this._pluginName),
                        n = this.el.getAttribute(`data-${e}-config`);
                    return n
                        ? window.PluginConfigManager.get(this._pluginName, n)
                        : t;
                }
                _getOptionsFromDataAttribute() {
                    let t = {};
                    if ('function' != typeof this.el.getAttribute) return t;
                    let e = i.toDashCase(this._pluginName),
                        n = this.el.getAttribute(`data-${e}-options`);
                    if (n)
                        try {
                            return JSON.parse(n);
                        } catch (t) {
                            console.error(
                                `The data attribute "data-${e}-options" could not be parsed to json: ${t.message}`
                            );
                        }
                    return t;
                }
                _registerInstance() {
                    window.PluginManager.getPluginInstancesFromElement(
                        this.el
                    ).set(this._pluginName, this),
                        window.PluginManager.getPlugin(this._pluginName, !1)
                            .get('instances')
                            .push(this);
                }
                _getPluginName(t) {
                    return t || (t = this.constructor.name), t;
                }
            }
            var s = n(221),
                u = n.n(s);
            class a extends o {
                static #t = (this.options = {
                    splideSelector: null,
                    splideOptions: {
                        classes: {
                            page: 'splide__pagination__page blur-esldr__nav-bullet',
                        },
                        pagination: !0,
                        omitEnd: !0,
                    },
                });
                init() {
                    let t = this.el,
                        n = null;
                    null !== this.options.splideSelector &&
                        (t = this.el.querySelector(
                            this.options.splideSelector
                        )),
                        'string' == typeof this.el.dataset.blurElysiumSlider &&
                            ((n = JSON.parse(
                                this.el.dataset.blurElysiumSlider
                            )),
                            (this.options = e()(this.options, n))),
                        t.classList.remove('is-loading'),
                        (this.slider = new (u())(
                            t,
                            this.options.splideOptions
                        )),
                        this.slider.mount();
                }
            }
            window.PluginManager.register(
                'BlurElysiumSliderPlugin',
                a,
                '[data-blur-elysium-slider]',
                { splideSelector: '[data-blur-elysium-slider-container]' }
            );
        })();
})();
