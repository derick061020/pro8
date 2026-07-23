import { _ as _sfc_main$c, C as CssUnitRe, a as __unplugin_components_4 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$d, a as _sfc_main$h } from "./VIconButton.dcb74fc8.js";
import { b as defineComponent, a as computed, f as openBlock, g as createElementBlock, y as renderSlot, Y as normalizeClass, Z as unref, r as ref, ac as useI18n, v as createBlock, B as withCtx, X as createBaseVNode, z as toDisplayString, w as createVNode, D as createTextVNode, C as createCommentVNode, T as Transition, N as Notyf, a6 as withDirectives, al as vModelSelect, F as Fragment, A as renderList, _ as createStaticVNode, t as reactive, o as onMounted, a7 as vModelText, G as pushScopeId, H as popScopeId, I as withModifiers, s as resolveDynamicComponent, ad as Teleport, am as resolveDirective, an as useWindowScroll, x as resolveComponent, u as useStorage, a5 as useRoute, ao as watchPostEffect, L as watch } from "./vendor.05b8b5b5.js";
import { i as isDark, t as toggleDarkModeHandler, _ as __unplugin_components_1$1 } from "./IsotipoMozoOficial.76c4accc.js";
import { _ as _sfc_main$g } from "./VAvatar.0f0bd550.js";
import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$e } from "./VField.152a42ff.js";
import { _ as _sfc_main$f } from "./VModal.9533b2d3.js";
import { u as useUserSession, p as provideApi, s as setThemeConfig, a as useCompanySession, N as NAME_ROUTE_POS, d as NAME_ROUTE_MESAS, e as NAME_ROUTE_ORDERS, f as NAME_ROUTE_DELIVERY, g as NAME_ROUTE_TAKEAWAY, h as NAME_ROUTE_COMMANDS, R as ROLES } from "./index.2bb13d6f.js";
import { _ as __unplugin_components_0, u as useDropdown } from "./VDropdown.53f4f138.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { M as MasterService, a as useMesaSession, u as use } from "./masterService.8a758d8d.js";
import { p as pageTitle } from "./navbarLayoutState.43b7723b.js";
import "./VIcon.f7f680f8.js";
const _sfc_main$b = /* @__PURE__ */ defineComponent({
  props: {
    radius: { type: String, required: false, default: void 0 },
    color: { type: String, required: false, default: void 0 },
    elevated: { type: Boolean, required: false, default: false }
  },
  setup(__props) {
    const props = __props;
    const cardRadius = computed(() => {
      if (props.radius === "smooth") {
        return "s-card";
      } else if (props.radius === "rounded") {
        return "l-card";
      }
      return "r-card";
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass([
          unref(cardRadius),
          __props.elevated && "is-raised",
          props.color && `is-${props.color}`
        ])
      }, [
        renderSlot(_ctx.$slots, "default")
      ], 2);
    };
  }
});
try {
  self["workbox:window:6.4.1"] && _();
} catch (n2) {
}
function n(n2, t2) {
  return new Promise(function(r2) {
    var e2 = new MessageChannel();
    e2.port1.onmessage = function(n3) {
      r2(n3.data);
    }, n2.postMessage(t2, [e2.port2]);
  });
}
function t(n2, t2) {
  for (var r2 = 0; r2 < t2.length; r2++) {
    var e2 = t2[r2];
    e2.enumerable = e2.enumerable || false, e2.configurable = true, "value" in e2 && (e2.writable = true), Object.defineProperty(n2, e2.key, e2);
  }
}
function r(n2, t2) {
  (t2 == null || t2 > n2.length) && (t2 = n2.length);
  for (var r2 = 0, e2 = new Array(t2); r2 < t2; r2++)
    e2[r2] = n2[r2];
  return e2;
}
function e(n2, t2) {
  var e2;
  if (typeof Symbol == "undefined" || n2[Symbol.iterator] == null) {
    if (Array.isArray(n2) || (e2 = function(n3, t3) {
      if (n3) {
        if (typeof n3 == "string")
          return r(n3, t3);
        var e3 = Object.prototype.toString.call(n3).slice(8, -1);
        return e3 === "Object" && n3.constructor && (e3 = n3.constructor.name), e3 === "Map" || e3 === "Set" ? Array.from(n3) : e3 === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(e3) ? r(n3, t3) : void 0;
      }
    }(n2)) || t2 && n2 && typeof n2.length == "number") {
      e2 && (n2 = e2);
      var i2 = 0;
      return function() {
        return i2 >= n2.length ? { done: true } : { done: false, value: n2[i2++] };
      };
    }
    throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }
  return (e2 = n2[Symbol.iterator]()).next.bind(e2);
}
try {
  self["workbox:core:6.4.1"] && _();
} catch (n2) {
}
var i = function() {
  var n2 = this;
  this.promise = new Promise(function(t2, r2) {
    n2.resolve = t2, n2.reject = r2;
  });
};
function o(n2, t2) {
  var r2 = location.href;
  return new URL(n2, r2).href === new URL(t2, r2).href;
}
var u = function(n2, t2) {
  this.type = n2, Object.assign(this, t2);
};
function a(n2, t2, r2) {
  return r2 ? t2 ? t2(n2) : n2 : (n2 && n2.then || (n2 = Promise.resolve(n2)), t2 ? n2.then(t2) : n2);
}
function c() {
}
var f = { type: "SKIP_WAITING" };
function s(n2, t2) {
  if (!t2)
    return n2 && n2.then ? n2.then(c) : Promise.resolve();
}
var v = function(r2) {
  var e2, c2;
  function v2(n2, t2) {
    var e3, c3;
    return t2 === void 0 && (t2 = {}), (e3 = r2.call(this) || this).nn = {}, e3.tn = 0, e3.rn = new i(), e3.en = new i(), e3.on = new i(), e3.un = 0, e3.an = new Set(), e3.cn = function() {
      var n3 = e3.fn, t3 = n3.installing;
      e3.tn > 0 || !o(t3.scriptURL, e3.sn.toString()) || performance.now() > e3.un + 6e4 ? (e3.vn = t3, n3.removeEventListener("updatefound", e3.cn)) : (e3.hn = t3, e3.an.add(t3), e3.rn.resolve(t3)), ++e3.tn, t3.addEventListener("statechange", e3.ln);
    }, e3.ln = function(n3) {
      var t3 = e3.fn, r3 = n3.target, i2 = r3.state, o2 = r3 === e3.vn, a2 = { sw: r3, isExternal: o2, originalEvent: n3 };
      !o2 && e3.mn && (a2.isUpdate = true), e3.dispatchEvent(new u(i2, a2)), i2 === "installed" ? e3.wn = self.setTimeout(function() {
        i2 === "installed" && t3.waiting === r3 && e3.dispatchEvent(new u("waiting", a2));
      }, 200) : i2 === "activating" && (clearTimeout(e3.wn), o2 || e3.en.resolve(r3));
    }, e3.dn = function(n3) {
      var t3 = e3.hn, r3 = t3 !== navigator.serviceWorker.controller;
      e3.dispatchEvent(new u("controlling", { isExternal: r3, originalEvent: n3, sw: t3, isUpdate: e3.mn })), r3 || e3.on.resolve(t3);
    }, e3.gn = (c3 = function(n3) {
      var t3 = n3.data, r3 = n3.ports, i2 = n3.source;
      return a(e3.getSW(), function() {
        e3.an.has(i2) && e3.dispatchEvent(new u("message", { data: t3, originalEvent: n3, ports: r3, sw: i2 }));
      });
    }, function() {
      for (var n3 = [], t3 = 0; t3 < arguments.length; t3++)
        n3[t3] = arguments[t3];
      try {
        return Promise.resolve(c3.apply(this, n3));
      } catch (n4) {
        return Promise.reject(n4);
      }
    }), e3.sn = n2, e3.nn = t2, navigator.serviceWorker.addEventListener("message", e3.gn), e3;
  }
  c2 = r2, (e2 = v2).prototype = Object.create(c2.prototype), e2.prototype.constructor = e2, e2.__proto__ = c2;
  var h, l, w = v2.prototype;
  return w.register = function(n2) {
    var t2 = (n2 === void 0 ? {} : n2).immediate, r3 = t2 !== void 0 && t2;
    try {
      var e3 = this;
      return function(n3, t3) {
        var r4 = n3();
        if (r4 && r4.then)
          return r4.then(t3);
        return t3(r4);
      }(function() {
        if (!r3 && document.readyState !== "complete")
          return s(new Promise(function(n3) {
            return window.addEventListener("load", n3);
          }));
      }, function() {
        return e3.mn = Boolean(navigator.serviceWorker.controller), e3.yn = e3.pn(), a(e3.bn(), function(n3) {
          e3.fn = n3, e3.yn && (e3.hn = e3.yn, e3.en.resolve(e3.yn), e3.on.resolve(e3.yn), e3.yn.addEventListener("statechange", e3.ln, { once: true }));
          var t3 = e3.fn.waiting;
          return t3 && o(t3.scriptURL, e3.sn.toString()) && (e3.hn = t3, Promise.resolve().then(function() {
            e3.dispatchEvent(new u("waiting", { sw: t3, wasWaitingBeforeRegister: true }));
          }).then(function() {
          })), e3.hn && (e3.rn.resolve(e3.hn), e3.an.add(e3.hn)), e3.fn.addEventListener("updatefound", e3.cn), navigator.serviceWorker.addEventListener("controllerchange", e3.dn), e3.fn;
        });
      });
    } catch (n3) {
      return Promise.reject(n3);
    }
  }, w.update = function() {
    try {
      return this.fn ? s(this.fn.update()) : void 0;
    } catch (n2) {
      return Promise.reject(n2);
    }
  }, w.getSW = function() {
    return this.hn !== void 0 ? Promise.resolve(this.hn) : this.rn.promise;
  }, w.messageSW = function(t2) {
    try {
      return a(this.getSW(), function(r3) {
        return n(r3, t2);
      });
    } catch (n2) {
      return Promise.reject(n2);
    }
  }, w.messageSkipWaiting = function() {
    this.fn && this.fn.waiting && n(this.fn.waiting, f);
  }, w.pn = function() {
    var n2 = navigator.serviceWorker.controller;
    return n2 && o(n2.scriptURL, this.sn.toString()) ? n2 : void 0;
  }, w.bn = function() {
    try {
      var n2 = this;
      return function(n3, t2) {
        try {
          var r3 = n3();
        } catch (n4) {
          return t2(n4);
        }
        if (r3 && r3.then)
          return r3.then(void 0, t2);
        return r3;
      }(function() {
        return a(navigator.serviceWorker.register(n2.sn, n2.nn), function(t2) {
          return n2.un = performance.now(), t2;
        });
      }, function(n3) {
        throw n3;
      });
    } catch (n3) {
      return Promise.reject(n3);
    }
  }, h = v2, (l = [{ key: "active", get: function() {
    return this.en.promise;
  } }, { key: "controlling", get: function() {
    return this.on.promise;
  } }]) && t(h.prototype, l), v2;
}(function() {
  function n2() {
    this.Pn = new Map();
  }
  var t2 = n2.prototype;
  return t2.addEventListener = function(n3, t3) {
    this.Sn(n3).add(t3);
  }, t2.removeEventListener = function(n3, t3) {
    this.Sn(n3).delete(t3);
  }, t2.dispatchEvent = function(n3) {
    n3.target = this;
    for (var t3, r2 = e(this.Sn(n3.type)); !(t3 = r2()).done; ) {
      (0, t3.value)(n3);
    }
  }, t2.Sn = function(n3) {
    return this.Pn.has(n3) || this.Pn.set(n3, new Set()), this.Pn.get(n3);
  }, n2;
}());
function registerSW(options = {}) {
  const {
    immediate = false,
    onNeedRefresh,
    onOfflineReady,
    onRegistered,
    onRegisterError
  } = options;
  let wb;
  const updateServiceWorker = async (reloadPage = true) => {
  };
  if ("serviceWorker" in navigator) {
    wb = new v("/mozo/sw.js", { scope: "/mozo/" });
    wb.addEventListener("activated", (event) => {
      if (event.isUpdate)
        window.location.reload();
      else
        onOfflineReady == null ? void 0 : onOfflineReady();
    });
    wb.register({ immediate }).then((r2) => {
      onRegistered == null ? void 0 : onRegistered(r2);
    }).catch((e2) => {
      onRegisterError == null ? void 0 : onRegisterError(e2);
    });
  }
  return updateServiceWorker;
}
function useRegisterSW(options = {}) {
  const {
    immediate = true,
    onNeedRefresh,
    onOfflineReady,
    onRegistered,
    onRegisterError
  } = options;
  const needRefresh = ref(false);
  const offlineReady = ref(false);
  const updateServiceWorker = registerSW({
    immediate,
    onNeedRefresh() {
      needRefresh.value = true;
      onNeedRefresh == null ? void 0 : onNeedRefresh();
    },
    onOfflineReady() {
      offlineReady.value = true;
      onOfflineReady == null ? void 0 : onOfflineReady();
    },
    onRegistered,
    onRegisterError
  });
  return {
    updateServiceWorker,
    offlineReady,
    needRefresh
  };
}
var VReloadPrompt_vue_vue_type_style_index_0_lang = "";
function block0(Component) {
  Component.__i18n = Component.__i18n || [];
  Component.__i18n.push({
    "locale": "",
    "resource": {
      "de": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), " ist bereit, offline zu arbeiten"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Eine neue Version von ", _interpolate(_named("appName")), " ist verf\xFCgbar, klicken Sie auf die Schaltfl\xE4che Neu laden, um sie zu aktualisieren."]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Neu laden"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Schlie\xDFen"]);
        }
      },
      "en": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), " is ready to work offline"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["A new version of ", _interpolate(_named("appName")), " is available, click on reload button to update."]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Reload"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Close"]);
        }
      },
      "es-MX": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), " est\xE1 listo para trabajar sin conexi\xF3n"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Una nueva versi\xF3n de ", _interpolate(_named("appName")), " est\xE1 disponible, haga clic en el bot\xF3n Recarga para actualizar."]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recarga"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cerrar"]);
        }
      },
      "es": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), " est\xE1 listo para trabajar sin conexi\xF3n"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Una nueva versi\xF3n de ", _interpolate(_named("appName")), " est\xE1 disponible, haga clic en el bot\xF3n Recarga para actualizar."]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recarga"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cerrar"]);
        }
      },
      "fr": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), " est pr\xEAt \xE0 \xEAtre utilis\xE9 hors ligne"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Une nouvelle version de ", _interpolate(_named("appName")), " est disponible, cliquez sur le bouton Recharger pour la mettre \xE0 jour."]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recharger"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Fermer"]);
        }
      },
      "zh-CN": {
        "offline-ready": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize([_interpolate(_named("appName")), "\u5DF2\u51C6\u5907\u597D\u8131\u673A\u5DE5\u4F5C"]);
        },
        "need-refresh": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["\u65B0\u7248\u672C\u7684", _interpolate(_named("appName")), "\u5DF2\u7ECF\u53EF\u7528\uFF0C\u70B9\u51FB\u91CD\u65B0\u52A0\u8F7D\u6309\u94AE\u6765\u66F4\u65B0\u3002"]);
        },
        "reload-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u91CD\u65B0\u52A0\u8F7D"]);
        },
        "close-button": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u5173\u95ED"]);
        }
      }
    }
  });
}
const _hoisted_1$a = { class: "pwa-message" };
const _hoisted_2$8 = { key: 0 };
const _hoisted_3$9 = { key: 1 };
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  props: {
    appName: { type: String, required: true }
  },
  setup(__props) {
    const props = __props;
    const loading = ref(false);
    const { t: t2 } = useI18n();
    const { offlineReady, needRefresh, updateServiceWorker } = useRegisterSW();
    const close = async () => {
      loading.value = false;
      offlineReady.value = false;
      needRefresh.value = false;
    };
    const update = async () => {
      loading.value = true;
      await updateServiceWorker();
      loading.value = false;
    };
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$c;
      const _component_VButtons = _sfc_main$d;
      const _component_VCard = _sfc_main$b;
      return openBlock(), createBlock(Transition, { name: "from-bottom" }, {
        default: withCtx(() => [
          unref(offlineReady) || unref(needRefresh) ? (openBlock(), createBlock(_component_VCard, {
            key: 0,
            class: "pwa-toast",
            role: "alert",
            radius: "smooth"
          }, {
            default: withCtx(() => [
              createBaseVNode("div", _hoisted_1$a, [
                unref(offlineReady) ? (openBlock(), createElementBlock("span", _hoisted_2$8, toDisplayString(unref(t2)("offline-ready", { appName: props.appName })), 1)) : (openBlock(), createElementBlock("span", _hoisted_3$9, toDisplayString(unref(t2)("need-refresh", { appName: props.appName })), 1))
              ]),
              createVNode(_component_VButtons, { align: "right" }, {
                default: withCtx(() => [
                  unref(needRefresh) ? (openBlock(), createBlock(_component_VButton, {
                    key: 0,
                    color: "primary",
                    icon: "ion:reload-outline",
                    loading: loading.value,
                    onClick: _cache[0] || (_cache[0] = () => update())
                  }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t2)("reload-button")), 1)
                    ]),
                    _: 1
                  }, 8, ["loading"])) : createCommentVNode("", true),
                  createVNode(_component_VButton, {
                    icon: "feather:x",
                    onClick: close
                  }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t2)("close-button")), 1)
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              })
            ]),
            _: 1
          })) : createCommentVNode("", true)
        ]),
        _: 1
      });
    };
  }
});
if (typeof block0 === "function")
  block0(_sfc_main$a);
var AvailablePrinterDialog_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$9 = /* @__PURE__ */ createStaticVNode('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 15a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2l0 -4"></path></svg></div><div class="meta"><span>Seleccionar impresora</span></div>', 2);
const _hoisted_3$8 = [
  _hoisted_1$9
];
const _hoisted_4$8 = { class: "columns is-multiline is-mobile" };
const _hoisted_5$7 = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, " Seleccione las impresoras disponibles ")
], -1);
const _hoisted_6$7 = { class: "column is-6" };
const _hoisted_7$6 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "IMPRESORA COMANDA", -1);
const _hoisted_8$6 = { class: "select" };
const _hoisted_9$5 = ["value"];
const _hoisted_10$5 = { class: "column is-6" };
const _hoisted_11$5 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_12$4 = /* @__PURE__ */ createTextVNode(" Guardar impresora comanda ");
const _hoisted_13$4 = { class: "column is-6" };
const _hoisted_14$4 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "IMPRESORA PRECUENTA", -1);
const _hoisted_15$4 = { class: "select" };
const _hoisted_16$4 = ["value"];
const _hoisted_17$4 = { class: "column is-6" };
const _hoisted_18$4 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_19$4 = /* @__PURE__ */ createTextVNode(" Guardar impresora precuenta ");
const _hoisted_20$3 = { class: "column is-6" };
const _hoisted_21$3 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "IMPRESORA DOCUMENTOS", -1);
const _hoisted_22$3 = { class: "select" };
const _hoisted_23$3 = ["value"];
const _hoisted_24$3 = { class: "column is-6" };
const _hoisted_25$3 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_26$3 = /* @__PURE__ */ createTextVNode(" Guardar impresora documentos ");
const _sfc_main$9 = /* @__PURE__ */ defineComponent({
  props: {
    parent: { type: String, required: false, default: void 0 }
  },
  setup(__props) {
    var _a, _b, _c;
    const userSession = useUserSession();
    const notyf = new Notyf();
    const dialogPrintAvailable = ref(false);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const printers = ref([]);
    const selectedPrinterCommand = ref((_a = userSession.printerNameCommand) != null ? _a : "");
    const selectedPrinterPreOrder = ref((_b = userSession.printerNamePreOrder) != null ? _b : "");
    const selectedPrinterDocument = ref((_c = userSession.printerNameDocument) != null ? _c : "");
    const loadConfiguration = async () => {
      var _a2, _b2, _c2, _d, _e, _f, _g;
      isLoading.value = true;
      try {
        const { data } = await provideApi().get("/restaurant/configurations");
        const config = data.data;
        printers.value = (_a2 = config.printers) != null ? _a2 : [];
        selectedPrinterCommand.value = (_c2 = (_b2 = config.printer_name_comanda) != null ? _b2 : userSession.printerNameCommand) != null ? _c2 : "";
        selectedPrinterPreOrder.value = (_e = (_d = config.printer_name_precuenta) != null ? _d : userSession.printerNamePreOrder) != null ? _e : "";
        selectedPrinterDocument.value = (_g = (_f = config.printer_name_documents) != null ? _f : userSession.printerNameDocument) != null ? _g : "";
      } catch (err) {
        console.error("Error al obtener configuraci\xF3n de impresoras:", err);
      } finally {
        isLoading.value = false;
      }
    };
    const openDialogPrintAvailable = () => {
      dialogPrintAvailable.value = true;
      loadConfiguration();
    };
    const savePrinters = async (field) => {
      isSaving.value = true;
      try {
        await provideApi().post("/restaurant/printers/set", {
          printer_name_comanda: selectedPrinterCommand.value,
          printer_name_precuenta: selectedPrinterPreOrder.value,
          printer_name_documents: selectedPrinterDocument.value
        });
        userSession.setPrinterNameCommand(selectedPrinterCommand.value);
        userSession.setPrinterNamePreOrder(selectedPrinterPreOrder.value);
        userSession.setPrinterNameDocument(selectedPrinterDocument.value);
        const labels = {
          comanda: selectedPrinterCommand.value,
          precuenta: selectedPrinterPreOrder.value,
          documentos: selectedPrinterDocument.value
        };
        notyf.success("Impresora " + labels[field] + " guardada correctamente.");
      } catch (err) {
        console.error("Error al guardar impresora:", err);
        notyf.error("Error al guardar la configuraci\xF3n de impresora.");
      } finally {
        isSaving.value = false;
      }
    };
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$e;
      const _component_VButton = _sfc_main$c;
      const _component_VModal = _sfc_main$f;
      return openBlock(), createElementBlock(Fragment, null, [
        createBaseVNode("a", {
          href: "#",
          role: "menuitem",
          class: "dropdown-item is-media",
          onClick: openDialogPrintAvailable
        }, _hoisted_3$8),
        createVNode(_component_VModal, {
          open: dialogPrintAvailable.value,
          title: "Listado de impresoras",
          size: "large",
          actions: "right",
          "cancel-label": "Cerrar",
          onClose: _cache[6] || (_cache[6] = ($event) => dialogPrintAvailable.value = false)
        }, {
          content: withCtx(() => [
            createBaseVNode("div", _hoisted_4$8, [
              _hoisted_5$7,
              createBaseVNode("div", _hoisted_6$7, [
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VControl, null, {
                      default: withCtx(() => [
                        _hoisted_7$6,
                        createBaseVNode("div", _hoisted_8$6, [
                          withDirectives(createBaseVNode("select", {
                            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => selectedPrinterCommand.value = $event)
                          }, [
                            (openBlock(true), createElementBlock(Fragment, null, renderList(printers.value, (printer) => {
                              return openBlock(), createElementBlock("option", {
                                key: printer.id,
                                value: printer.name
                              }, toDisplayString(printer.name), 9, _hoisted_9$5);
                            }), 128))
                          ], 512), [
                            [vModelSelect, selectedPrinterCommand.value]
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", _hoisted_10$5, [
                _hoisted_11$5,
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VButton, {
                      color: "primary",
                      fullwidth: "",
                      class: "",
                      size: "big",
                      loading: isLoading.value || isSaving.value,
                      onClick: _cache[1] || (_cache[1] = ($event) => savePrinters("comanda"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_12$4
                      ]),
                      _: 1
                    }, 8, ["loading"])
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", _hoisted_13$4, [
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VControl, null, {
                      default: withCtx(() => [
                        _hoisted_14$4,
                        createBaseVNode("div", _hoisted_15$4, [
                          withDirectives(createBaseVNode("select", {
                            "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => selectedPrinterPreOrder.value = $event)
                          }, [
                            (openBlock(true), createElementBlock(Fragment, null, renderList(printers.value, (printer) => {
                              return openBlock(), createElementBlock("option", {
                                key: printer.id,
                                value: printer.name
                              }, toDisplayString(printer.name), 9, _hoisted_16$4);
                            }), 128))
                          ], 512), [
                            [vModelSelect, selectedPrinterPreOrder.value]
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", _hoisted_17$4, [
                _hoisted_18$4,
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VButton, {
                      color: "primary",
                      fullwidth: "",
                      class: "",
                      size: "big",
                      loading: isLoading.value || isSaving.value,
                      onClick: _cache[3] || (_cache[3] = ($event) => savePrinters("precuenta"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_19$4
                      ]),
                      _: 1
                    }, 8, ["loading"])
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", _hoisted_20$3, [
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VControl, null, {
                      default: withCtx(() => [
                        _hoisted_21$3,
                        createBaseVNode("div", _hoisted_22$3, [
                          withDirectives(createBaseVNode("select", {
                            "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => selectedPrinterDocument.value = $event)
                          }, [
                            (openBlock(true), createElementBlock(Fragment, null, renderList(printers.value, (printer) => {
                              return openBlock(), createElementBlock("option", {
                                key: printer.id,
                                value: printer.name
                              }, toDisplayString(printer.name), 9, _hoisted_23$3);
                            }), 128))
                          ], 512), [
                            [vModelSelect, selectedPrinterDocument.value]
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", _hoisted_24$3, [
                _hoisted_25$3,
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VButton, {
                      color: "primary",
                      fullwidth: "",
                      class: "",
                      size: "big",
                      loading: isLoading.value || isSaving.value,
                      onClick: _cache[5] || (_cache[5] = ($event) => savePrinters("documentos"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_26$3
                      ]),
                      _: 1
                    }, 8, ["loading"])
                  ]),
                  _: 1
                })
              ])
            ])
          ]),
          _: 1
        }, 8, ["open"])
      ], 64);
    };
  }
});
const _hoisted_1$8 = { class: "content-shape-group" };
const _sfc_main$8 = /* @__PURE__ */ defineComponent({
  props: {
    width: { type: String, required: false, default: "100%" },
    lastLineWidth: { type: String, required: false, default: "100%" },
    lines: { type: Number, required: false, default: 2 },
    disabled: { type: Boolean, required: false },
    centered: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    if (props.width.match(CssUnitRe) === null) {
      console.warn(`VPlaceloadText: invalid "${props.width}" width. Should be a valid css unit value.`);
    }
    if (props.lastLineWidth.match(CssUnitRe) === null) {
      console.warn(`VPlaceloadText: invalid "${props.lastLineWidth}" lastLineWidth. Should be a valid css unit value.`);
    }
    return (_ctx, _cache) => {
      const _component_VPlaceload = __unplugin_components_4;
      return openBlock(), createElementBlock("div", _hoisted_1$8, [
        (openBlock(true), createElementBlock(Fragment, null, renderList(props.lines - 1, (line) => {
          return openBlock(), createBlock(_component_VPlaceload, {
            key: line,
            width: props.width,
            centered: props.centered
          }, null, 8, ["width", "centered"]);
        }), 128)),
        createVNode(_component_VPlaceload, {
          width: props.lastLineWidth,
          centered: props.centered
        }, null, 8, ["width", "centered"])
      ]);
    };
  }
});
var paletteForm_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId$1 = (n2) => (pushScopeId("data-v-050f13d3"), n2 = n2(), popScopeId(), n2);
const _hoisted_1$7 = { class: "palette-form" };
const _hoisted_2$7 = { class: "palette-form__header" };
const _hoisted_3$7 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("h5", null, "Cambiar paleta", -1));
const _hoisted_4$7 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "22",
  height: "22",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-x"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M18 6l-12 12" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 6l12 12" })
], -1));
const _hoisted_5$6 = [
  _hoisted_4$7
];
const _hoisted_6$6 = { class: "palette-form__body" };
const _hoisted_7$5 = {
  key: 0,
  class: "palette-section"
};
const _hoisted_8$5 = { class: "is-flex is-align-items-center is-justify-content-space-between mb-3" };
const _hoisted_9$4 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("p", { class: "palette-section__title" }, "Modo claro", -1));
const _hoisted_10$4 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-refresh mr-1"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" })
], -1));
const _hoisted_11$4 = /* @__PURE__ */ createTextVNode(" Modo oscuro ");
const _hoisted_12$3 = [
  _hoisted_10$4,
  _hoisted_11$4
];
const _hoisted_13$3 = { class: "label" };
const _hoisted_14$3 = { class: "is-size-7" };
const _hoisted_15$3 = { class: "columns is-mobile is-gapless mt-2 prevent-select" };
const _hoisted_16$3 = { class: "column is-8 color-value-input" };
const _hoisted_17$3 = ["onUpdate:modelValue"];
const _hoisted_18$3 = { class: "column is-4 has-text-centered" };
const _hoisted_19$3 = ["onUpdate:modelValue"];
const _hoisted_20$2 = {
  key: 1,
  class: "palette-section"
};
const _hoisted_21$2 = { class: "is-flex is-align-items-center is-justify-content-space-between mb-3" };
const _hoisted_22$2 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("p", { class: "palette-section__title" }, "Modo oscuro", -1));
const _hoisted_23$2 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-refresh mr-1"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" })
], -1));
const _hoisted_24$2 = /* @__PURE__ */ createTextVNode(" Modo claro ");
const _hoisted_25$2 = [
  _hoisted_23$2,
  _hoisted_24$2
];
const _hoisted_26$2 = { class: "label" };
const _hoisted_27$2 = { class: "is-size-7" };
const _hoisted_28$2 = { class: "columns is-mobile is-gapless mt-2 prevent-select" };
const _hoisted_29$2 = { class: "column is-8 color-value-input" };
const _hoisted_30$1 = ["onUpdate:modelValue"];
const _hoisted_31$1 = { class: "column is-4 has-text-centered" };
const _hoisted_32$1 = ["onUpdate:modelValue"];
const _hoisted_33$1 = { class: "palette-form__footer" };
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  emits: ["close"],
  setup(__props, { emit }) {
    const notyf = new Notyf();
    const colors = reactive({
      Primary: {
        label: "Color Primario",
        hex: "var(--primary)"
      },
      Secondary: {
        label: "Color Secundario",
        hex: "var(--secondary)"
      },
      Background: {
        label: "Background",
        hex: "var(--background)"
      },
      Text: {
        label: "Texto",
        hex: "var(--text)"
      },
      lightText: {
        label: "Texto Claro",
        hex: "var(--light-text)"
      },
      darkPrimary: {
        label: "Dark Primary",
        hex: "var(--dark-primary)"
      },
      darkLightText: {
        label: "Dark Light Text",
        hex: "var(--dark-light-text)"
      }
    });
    const lightColorEntries = computed(() => Object.entries(colors).filter(([key]) => !key.toLowerCase().startsWith("dark")));
    const darkColorEntries = computed(() => Object.entries(colors).filter(([key]) => key.toLowerCase().startsWith("dark")));
    const applyCssVariables = () => {
      try {
        Object.keys(colors).forEach((key) => {
          const rawValue = colors[key].hex;
          const value = resolveCssValue(rawValue);
          const rawVar = `--${key}`;
          document.documentElement.style.setProperty(rawVar, value);
          const kebab = key.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
          const normalizedVar = `--${kebab}`;
          document.documentElement.style.setProperty(normalizedVar, value);
        });
        try {
          const themeCfg = {};
          Object.keys(colors).forEach((k) => themeCfg[k] = resolveCssValue(colors[k].hex));
          setThemeConfig(themeCfg);
        } catch (e2) {
          console.error("Error updating theme config", e2);
        }
      } catch (e2) {
        console.error("Error applying css variables", e2);
      }
    };
    const normalizeKey = (k) => k.replace(/[^a-z0-9]/gi, "").toLowerCase();
    const resolveCssValue = (val) => {
      try {
        if (typeof val !== "string")
          return String(val);
        const m = val.trim().match(/^var\((--[a-z0-9-]+)\)$/i);
        if (m) {
          const varName = m[1];
          const computed2 = getComputedStyle(document.documentElement).getPropertyValue(varName);
          if (computed2 && computed2.trim())
            return computed2.trim();
          return val;
        }
        return val;
      } catch (e2) {
        return String(val);
      }
    };
    const applyConfigToColors = (cfg) => {
      try {
        const normToOrig = {};
        Object.keys(colors).forEach((orig) => {
          normToOrig[normalizeKey(orig)] = orig;
        });
        Object.keys(cfg || {}).forEach((inKey) => {
          const nk = normalizeKey(inKey);
          const orig = normToOrig[nk];
          if (orig && colors[orig]) {
            ;
            colors[orig].hex = resolveCssValue(cfg[inKey]);
          }
        });
        applyCssVariables();
      } catch (e2) {
        console.error("Error applying config to colors", e2);
      }
    };
    const saveToServer = async () => {
      try {
        const plain = {};
        Object.keys(colors).forEach((k) => plain[k] = colors[k].hex);
        const tryUrls = ["/api/config", `${location.protocol}//${location.hostname}:3001/api/config`];
        let lastError = null;
        for (const url of tryUrls) {
          try {
            const resp = await fetch(url, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify(plain)
            });
            const data = await resp.json().catch(() => ({}));
            if (resp.ok && data.success) {
              notyf.success("config.json actualizado en servidor");
              try {
                const r2 = await fetch("/api/config", { cache: "no-store" });
                if (r2.ok) {
                  const updated = await r2.json().catch(() => ({}));
                  applyConfigToColors(updated);
                }
              } catch (e2) {
              }
              return;
            } else {
              lastError = data.message || `Status ${resp.status}`;
            }
          } catch (err) {
            lastError = err;
          }
        }
        console.error("Error saving to server", lastError);
        notyf.error("No se pudo actualizar config.json en el servidor");
      } catch (e2) {
        console.error("Error saving to server (unexpected)", e2);
        notyf.error("No se pudo conectar con el servidor");
      }
    };
    const initSse = () => {
      let es = null;
      try {
        es = new EventSource("/api/stream");
      } catch (e2) {
        es = null;
      }
      if (!es) {
        try {
          es = new EventSource(`${location.protocol}//${location.hostname}:3001/api/stream`);
        } catch (e2) {
          es = null;
        }
      }
      if (!es)
        return;
      es.onopen = () => {
        console.log("SSE connected");
      };
      es.onmessage = (ev) => {
        try {
          const data = JSON.parse(ev.data);
          applyConfigToColors(data);
          console.log("Received config via SSE");
        } catch (e2) {
        }
      };
      es.onerror = (err) => {
        console.error("SSE error", err);
      };
    };
    const loadFromConfig = async () => {
      try {
        const resp = await fetch("/api/config", { cache: "no-store" });
        if (!resp.ok)
          throw new Error("No se pudo cargar config.json");
        const cfg = await resp.json();
        applyConfigToColors(cfg);
        const stored = localStorage.getItem("app-palette-config");
        if (stored) {
          const parsed = JSON.parse(stored);
          applyConfigToColors(parsed);
        }
      } catch (e2) {
        console.error("Error loading config.json", e2);
      }
    };
    onMounted(async () => {
      await loadFromConfig();
      initSse();
      try {
        Object.keys(colors).forEach((k) => {
          const v2 = colors[k].hex;
          colors[k].hex = resolveCssValue(v2);
        });
        applyCssVariables();
      } catch (e2) {
      }
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$7, [
        createBaseVNode("header", _hoisted_2$7, [
          _hoisted_3$7,
          createBaseVNode("button", {
            class: "close-btn",
            onClick: _cache[0] || (_cache[0] = ($event) => emit("close"))
          }, _hoisted_5$6)
        ]),
        createBaseVNode("div", _hoisted_6$6, [
          !unref(isDark) ? (openBlock(), createElementBlock("section", _hoisted_7$5, [
            createBaseVNode("div", _hoisted_8$5, [
              _hoisted_9$4,
              createBaseVNode("button", {
                class: "button is-primary px-2 mini-btn",
                onClick: _cache[1] || (_cache[1] = ($event) => isDark.value = true)
              }, _hoisted_12$3)
            ]),
            (openBlock(true), createElementBlock(Fragment, null, renderList(unref(lightColorEntries), ([key, color]) => {
              return openBlock(), createElementBlock("div", {
                key,
                class: "field palette-item"
              }, [
                createBaseVNode("label", _hoisted_13$3, [
                  createTextVNode(toDisplayString(color.label) + " ", 1),
                  createBaseVNode("span", _hoisted_14$3, "(" + toDisplayString(key) + ")", 1)
                ]),
                createBaseVNode("div", _hoisted_15$3, [
                  createBaseVNode("div", _hoisted_16$3, [
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": ($event) => color.hex = $event,
                      class: "input",
                      type: "text",
                      onInput: applyCssVariables
                    }, null, 40, _hoisted_17$3), [
                      [vModelText, color.hex]
                    ])
                  ]),
                  createBaseVNode("div", _hoisted_18$3, [
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": ($event) => color.hex = $event,
                      type: "color",
                      class: "color-picker",
                      onInput: applyCssVariables
                    }, null, 40, _hoisted_19$3), [
                      [vModelText, color.hex]
                    ])
                  ])
                ])
              ]);
            }), 128))
          ])) : createCommentVNode("", true),
          unref(isDark) ? (openBlock(), createElementBlock("section", _hoisted_20$2, [
            createBaseVNode("div", _hoisted_21$2, [
              _hoisted_22$2,
              createBaseVNode("button", {
                class: "button is-primary px-2 mini-btn",
                onClick: _cache[2] || (_cache[2] = ($event) => isDark.value = false)
              }, _hoisted_25$2)
            ]),
            (openBlock(true), createElementBlock(Fragment, null, renderList(unref(darkColorEntries), ([key, color]) => {
              return openBlock(), createElementBlock("div", {
                key,
                class: "field palette-item"
              }, [
                createBaseVNode("label", _hoisted_26$2, [
                  createTextVNode(toDisplayString(color.label) + " ", 1),
                  createBaseVNode("span", _hoisted_27$2, "(" + toDisplayString(key) + ")", 1)
                ]),
                createBaseVNode("div", _hoisted_28$2, [
                  createBaseVNode("div", _hoisted_29$2, [
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": ($event) => color.hex = $event,
                      class: "input",
                      type: "text",
                      onInput: applyCssVariables
                    }, null, 40, _hoisted_30$1), [
                      [vModelText, color.hex]
                    ])
                  ]),
                  createBaseVNode("div", _hoisted_31$1, [
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": ($event) => color.hex = $event,
                      type: "color",
                      class: "color-picker",
                      onInput: applyCssVariables
                    }, null, 40, _hoisted_32$1), [
                      [vModelText, color.hex]
                    ])
                  ])
                ])
              ]);
            }), 128))
          ])) : createCommentVNode("", true)
        ]),
        createBaseVNode("footer", _hoisted_33$1, [
          createBaseVNode("button", {
            class: "button mr-2",
            onClick: _cache[3] || (_cache[3] = ($event) => emit("close"))
          }, "Cerrar"),
          createBaseVNode("button", {
            class: "button is-primary",
            onClick: saveToServer
          }, "Guardar")
        ])
      ]);
    };
  }
});
var PaletteForm = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["__scopeId", "data-v-050f13d3"]]);
var UserProfileDropdown_vue_vue_type_style_index_0_lang = "";
var UserProfileDropdown_vue_vue_type_style_index_1_scoped_true_lang = "";
const _withScopeId = (n2) => (pushScopeId("data-v-fdbb2b9e"), n2 = n2(), popScopeId(), n2);
const _hoisted_1$6 = ["onClick"];
const _hoisted_2$6 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-cog",
  style: {}
}, null, -1));
const _hoisted_3$6 = [
  _hoisted_2$6
];
const _hoisted_4$6 = { class: "dropdown-head" };
const _hoisted_5$5 = { class: "meta" };
const _hoisted_6$5 = { style: { "font-size": "12px" } };
const _hoisted_7$4 = {
  href: "#",
  role: "menuitem",
  class: "dropdown-item is-media"
};
const _hoisted_8$4 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-user"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" })
  ])
], -1));
const _hoisted_9$3 = { class: "meta" };
const _hoisted_10$3 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("hr", { class: "dropdown-divider" }, null, -1));
const _hoisted_11$3 = ["href"];
const _hoisted_12$2 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-world"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3.6 9h16.8" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3.6 15h16.8" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M11.5 3a17 17 0 0 0 0 18" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12.5 3a17 17 0 0 1 0 18" })
  ])
], -1));
const _hoisted_13$2 = { class: "meta" };
const _hoisted_14$2 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", null, "Dominio", -1));
const _hoisted_15$2 = ["href"];
const _hoisted_16$2 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-building-store"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 21l18 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M5 21l0 -10.15" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M19 21l0 -10.15" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" })
  ])
], -1));
const _hoisted_17$2 = { class: "meta" };
const _hoisted_18$2 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", null, "Men\xFA digital", -1));
const _hoisted_19$2 = ["onClick"];
const _hoisted_20$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-palette"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 21a9 9 0 0 1 0 -18c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 2.828c-.844 .75 -1.989 1.172 -3.182 1.172h-2.5a2 2 0 0 0 -1 3.75a1.3 1.3 0 0 1 -1 2.25" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M7.5 10.5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M11.5 7.5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M15.5 10.5a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" })
  ])
], -1));
const _hoisted_21$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
  /* @__PURE__ */ createBaseVNode("span", null, "Cambiar paleta")
], -1));
const _hoisted_22$1 = [
  _hoisted_20$1,
  _hoisted_21$1
];
const _hoisted_23$1 = {
  key: 0,
  href: "/app/prices",
  target: "BLANK",
  role: "menuitem",
  class: "dropdown-item is-media"
};
const _hoisted_24$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-pencil"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M13.5 6.5l4 4" })
  ])
], -1));
const _hoisted_25$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
  /* @__PURE__ */ createBaseVNode("span", null, "Configuraci\xF3n precios")
], -1));
const _hoisted_26$1 = [
  _hoisted_24$1,
  _hoisted_25$1
];
const _hoisted_27$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("hr", { class: "dropdown-divider" }, null, -1));
const _hoisted_28$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "20",
    height: "20",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-cloud-download"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M19 18a3.5 3.5 0 0 0 0 -7h-1a5 4.5 0 0 0 -11 -2a4.6 4.4 0 0 0 -2.1 8.4" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 13l0 9" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M9 19l3 3l3 -3" })
  ])
], -1));
const _hoisted_29$1 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
  /* @__PURE__ */ createBaseVNode("span"),
  /* @__PURE__ */ createBaseVNode("span", null, "Descargar datos de la nube")
], -1));
const _hoisted_30 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "icon close-cash" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "1.25",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-cash-register"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M19 21v1m0 -8v1" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 14v.01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 17v.01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 13.99v.01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 17v.01" })
  ])
], -1));
const _hoisted_31 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
  /* @__PURE__ */ createBaseVNode("span"),
  /* @__PURE__ */ createBaseVNode("span", { class: "close-cash" }, "Cerrar caja y enviar a la nube")
], -1));
const _hoisted_32 = [
  _hoisted_30,
  _hoisted_31
];
const _hoisted_33 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("hr", { class: "dropdown-divider" }, null, -1));
const _hoisted_34 = { class: "dropdown-item is-button" };
const _hoisted_35 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.25",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-logout",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12h12l-3 -3" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M18 15l3 -3" })
], -1));
const _hoisted_36 = /* @__PURE__ */ createTextVNode(" Salir ");
const _hoisted_37 = {
  key: 1,
  class: "palette-flyout",
  role: "dialog",
  "aria-label": "Cambiar paleta"
};
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const userSession = useUserSession();
    const companySession = useCompanySession();
    const company = companySession.company;
    const establishment = companySession.establishments;
    const notyf = new Notyf();
    const userRole = userSession.getRole();
    const loading = ref(false);
    const showPalette = ref(false);
    const openPalette = () => {
      showPalette.value = true;
    };
    const closePalette = () => {
      showPalette.value = false;
    };
    let url_logo = "/mozo/images/avatars/svg/vuero-1.svg";
    if (establishment[0].logo) {
      url_logo = userSession.ssl + userSession.url + "/" + establishment[0].logo;
    }
    const logout = () => {
      userSession.logoutUser();
      userSession.setCashId(0);
      location.href = userRole != "MOZO" ? "/mozo/auth/login" : "/mozo/auth/mozo";
    };
    const syncData = async () => {
      loading.value = true;
      await MasterService.syncData();
      notyf.success("Se actualizaron los datos correctamente.");
      loading.value = false;
    };
    const closeCash = async () => {
      const id = userSession.getCashId();
      if (id == 0) {
        notyf.error("No existe caja aperturada.");
        return false;
      }
      try {
        const response = await provideApi().get(`/cash/close/${id}`);
        const data = response.data;
        if (data.success) {
          userSession.setCashId(0);
          notyf.success(data.message);
        } else {
          notyf.error(data.message);
        }
      } catch (error) {
        console.error("Error data:", error);
      }
    };
    return (_ctx, _cache) => {
      const _component_VAvatar = _sfc_main$g;
      const _component_AvailablePrinterDialog = _sfc_main$9;
      const _component_VPlaceloadText = _sfc_main$8;
      const _component_VButton = _sfc_main$c;
      const _component_VDropdown = __unplugin_components_0;
      return openBlock(), createElementBlock("div", null, [
        createVNode(_component_VDropdown, {
          right: "",
          spaced: "",
          class: "user-dropdown profile-dropdown"
        }, {
          button: withCtx(({ toggle }) => [
            createBaseVNode("a", {
              class: "is-trigger dropdown-trigger",
              "aria-haspopup": "true",
              onClick: toggle
            }, _hoisted_3$6, 8, _hoisted_1$6)
          ]),
          content: withCtx(() => [
            createBaseVNode("div", _hoisted_4$6, [
              createVNode(_component_VAvatar, {
                squared: "",
                size: "medium",
                picture: unref(url_logo)
              }, null, 8, ["picture"]),
              createBaseVNode("div", _hoisted_5$5, [
                createBaseVNode("span", null, toDisplayString(unref(company).name), 1),
                createBaseVNode("span", null, toDisplayString(unref(establishment)[0].description), 1),
                createBaseVNode("span", _hoisted_6$5, toDisplayString(unref(userSession).cashDescription), 1)
              ])
            ]),
            createBaseVNode("a", _hoisted_7$4, [
              _hoisted_8$4,
              createBaseVNode("div", _hoisted_9$3, [
                createBaseVNode("span", null, toDisplayString(unref(userSession).name), 1),
                createBaseVNode("span", null, toDisplayString(unref(userSession).email), 1)
              ])
            ]),
            _hoisted_10$3,
            createBaseVNode("a", {
              href: unref(userSession).ssl + unref(userSession).url,
              target: "BLANK",
              role: "menuitem",
              class: "dropdown-item is-media"
            }, [
              _hoisted_12$2,
              createBaseVNode("div", _hoisted_13$2, [
                _hoisted_14$2,
                createBaseVNode("span", null, toDisplayString(unref(userSession).url), 1)
              ])
            ], 8, _hoisted_11$3),
            createBaseVNode("a", {
              href: unref(userSession).ssl + unref(userSession).url + "/pedidos",
              target: "BLANK",
              role: "menuitem",
              class: "dropdown-item is-media"
            }, [
              _hoisted_16$2,
              createBaseVNode("div", _hoisted_17$2, [
                _hoisted_18$2,
                createBaseVNode("span", null, toDisplayString(unref(userSession).url) + "/pedidos", 1)
              ])
            ], 8, _hoisted_15$2),
            createBaseVNode("a", {
              href: "#",
              role: "menuitem",
              class: "dropdown-item is-media",
              onClick: withModifiers(openPalette, ["prevent"])
            }, _hoisted_22$1, 8, _hoisted_19$2),
            unref(userSession).getPermissionEditItemPrices() || unref(companySession).configuration.allow_edit_unit_price_to_seller ? (openBlock(), createElementBlock("a", _hoisted_23$1, _hoisted_26$1)) : createCommentVNode("", true),
            unref(companySession).configuration.printer_enabled ? (openBlock(), createBlock(_component_AvailablePrinterDialog, { key: 1 })) : createCommentVNode("", true),
            _hoisted_27$1,
            createBaseVNode("a", {
              href: "#",
              role: "menuitem",
              class: "dropdown-item is-media",
              onClick: syncData
            }, [
              loading.value ? (openBlock(), createBlock(_component_VPlaceloadText, {
                key: 0,
                lines: 1,
                width: "75%",
                "last-line-width": "25%"
              })) : (openBlock(), createElementBlock(Fragment, { key: 1 }, [
                _hoisted_28$1,
                _hoisted_29$1
              ], 64))
            ]),
            unref(userRole) != "MOZO" ? (openBlock(), createElementBlock("a", {
              key: 2,
              href: "#",
              role: "menuitem",
              class: "dropdown-item is-media",
              onClick: _cache[0] || (_cache[0] = ($event) => closeCash())
            }, _hoisted_32)) : createCommentVNode("", true),
            _hoisted_33,
            createBaseVNode("div", _hoisted_34, [
              createVNode(_component_VButton, {
                class: "logout-button",
                color: "primary",
                role: "menuitem",
                raised: "",
                fullwidth: "",
                onClick: logout
              }, {
                default: withCtx(() => [
                  _hoisted_35,
                  _hoisted_36
                ]),
                _: 1
              })
            ])
          ]),
          _: 1
        }),
        (openBlock(), createBlock(Teleport, { to: "body" }, [
          showPalette.value ? (openBlock(), createElementBlock("div", {
            key: 0,
            class: "flyout-backdrop",
            onClick: closePalette
          })) : createCommentVNode("", true),
          showPalette.value ? (openBlock(), createElementBlock("aside", _hoisted_37, [
            (openBlock(), createBlock(resolveDynamicComponent(PaletteForm), { onClose: closePalette }))
          ])) : createCommentVNode("", true)
        ]))
      ]);
    };
  }
});
var __unplugin_components_2 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-fdbb2b9e"]]);
var MobileNavbar_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$5 = {
  class: "navbar mobile-navbar is-hidden-desktop is-hidden-tablet",
  "aria-label": "main navigation"
};
const _hoisted_2$5 = { class: "container" };
const _hoisted_3$5 = { class: "navbar-brand" };
const _hoisted_4$5 = { class: "brand-start" };
const _hoisted_5$4 = /* @__PURE__ */ createBaseVNode("span", null, null, -1);
const _hoisted_6$4 = /* @__PURE__ */ createBaseVNode("span", null, null, -1);
const _hoisted_7$3 = /* @__PURE__ */ createBaseVNode("span", null, null, -1);
const _hoisted_8$3 = [
  _hoisted_5$4,
  _hoisted_6$4,
  _hoisted_7$3
];
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  props: {
    isOpen: { type: Boolean, required: false }
  },
  emits: ["toggle"],
  setup(__props, { emit }) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("nav", _hoisted_1$5, [
        createBaseVNode("div", _hoisted_2$5, [
          createBaseVNode("div", _hoisted_3$5, [
            createBaseVNode("div", _hoisted_4$5, [
              createBaseVNode("div", {
                class: normalizeClass(["navbar-burger", [props.isOpen && "is-active"]]),
                onClick: _cache[0] || (_cache[0] = ($event) => emit("toggle"))
              }, _hoisted_8$3, 2)
            ]),
            renderSlot(_ctx.$slots, "brand")
          ])
        ])
      ]);
    };
  }
});
var MobileSidebar_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$4 = { class: "inner" };
const _hoisted_2$4 = { class: "icon-side-menu" };
const _hoisted_3$4 = /* @__PURE__ */ createBaseVNode("li", null, [
  /* @__PURE__ */ createBaseVNode("a", {
    "aria-label": "Back to homepage",
    href: "/"
  }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "iconify",
      "data-icon": "feather:activity"
    })
  ])
], -1);
const _hoisted_4$4 = { class: "bottom-icon-side-menu" };
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  props: {
    isOpen: { type: Boolean, required: false }
  },
  emits: ["toggle"],
  setup(__props, { emit }) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock(Fragment, null, [
        createBaseVNode("div", {
          class: normalizeClass([[props.isOpen && "is-active"], "mobile-main-sidebar"])
        }, [
          createBaseVNode("div", _hoisted_1$4, [
            createBaseVNode("ul", _hoisted_2$4, [
              renderSlot(_ctx.$slots, "links", {}, () => [
                _hoisted_3$4
              ])
            ]),
            createBaseVNode("ul", _hoisted_4$4, [
              renderSlot(_ctx.$slots, "bottom-links")
            ])
          ])
        ], 2),
        props.isOpen ? (openBlock(), createElementBlock("div", {
          key: 0,
          class: "mobile-overlay",
          onClick: _cache[0] || (_cache[0] = ($event) => emit("toggle"))
        })) : createCommentVNode("", true)
      ], 64);
    };
  }
});
const _hoisted_1$3 = { class: "toolbar ml-auto" };
const _hoisted_2$3 = {
  key: 0,
  class: "toolbar-link"
};
const _hoisted_3$3 = {
  key: 1,
  class: "toolbar-link right-panel-trigger wifi-on",
  color: "solid"
};
const _hoisted_4$3 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  "aria-hidden": "true",
  role: "img",
  width: "1em",
  height: "1em",
  preserveAspectRatio: "xMidYMid meet",
  viewBox: "0 0 24 24"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    d: "M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3l3-3a4.237 4.237 0 0 0-6 0zm-4-4l2 2a7.074 7.074 0 0 1 10 0l2-2C15.14 9.14 8.87 9.14 5 13z",
    fill: "currentColor"
  })
], -1);
const _hoisted_5$3 = [
  _hoisted_4$3
];
const _hoisted_6$3 = {
  key: 2,
  class: "toolbar-link right-panel-trigger wifi-off",
  color: "solid"
};
const _hoisted_7$2 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  "aria-hidden": "true",
  role: "img",
  width: "1em",
  height: "1em",
  preserveAspectRatio: "xMidYMid meet",
  viewBox: "0 0 24 24"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    d: "M22.99 9C19.15 5.16 13.8 3.76 8.84 4.78l2.52 2.52c3.47-.17 6.99 1.05 9.63 3.7l2-2zm-4 4a9.793 9.793 0 0 0-4.49-2.56l3.53 3.53l.96-.97zM2 3.05L5.07 6.1C3.6 6.82 2.22 7.78 1 9l1.99 2c1.24-1.24 2.67-2.16 4.2-2.77l2.24 2.24A9.684 9.684 0 0 0 5 13v.01L6.99 15a7.042 7.042 0 0 1 4.92-2.06L18.98 20l1.27-1.26L3.29 1.79L2 3.05zM9 17l3 3l3-3a4.237 4.237 0 0 0-6 0z",
    fill: "currentColor"
  })
], -1);
const _hoisted_8$2 = [
  _hoisted_7$2
];
const _hoisted_9$2 = {
  class: "toolbar-link",
  style: { "position": "relative", "cursor": "default" }
};
const _hoisted_10$2 = ["stroke"];
const _hoisted_11$2 = /* @__PURE__ */ createBaseVNode("path", { d: "M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" }, null, -1);
const _hoisted_12$1 = /* @__PURE__ */ createBaseVNode("path", { d: "M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" }, null, -1);
const _hoisted_13$1 = /* @__PURE__ */ createBaseVNode("path", { d: "M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" }, null, -1);
const _hoisted_14$1 = [
  _hoisted_11$2,
  _hoisted_12$1,
  _hoisted_13$1
];
const _hoisted_15$1 = {
  key: 0,
  style: { "position": "absolute", "top": "4px", "right": "4px", "width": "8px", "height": "8px", "border-radius": "50%", "background": "var(--danger)" }
};
const _hoisted_16$1 = { class: "toolbar-link" };
const _hoisted_17$1 = { class: "dark-mode ml-auto" };
const _hoisted_18$1 = ["checked"];
const _hoisted_19$1 = /* @__PURE__ */ createBaseVNode("span", null, null, -1);
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const companySession = useCompanySession();
    companySession.configuration;
    const { locale } = useI18n();
    const dropdownElement = ref();
    useDropdown(dropdownElement);
    let isOnLine = ref(false);
    let isOnLineDescription = ref("ONLINE");
    const userSession = useUserSession();
    const userRole = userSession.getRole();
    computed(() => {
      switch (locale.value) {
        case "fr":
          return "/images/icons/flags/france.svg";
        case "es":
          return "/images/icons/flags/spain.svg";
        case "es-MX":
          return "/images/icons/flags/mexico.svg";
        case "de":
          return "/images/icons/flags/germany.svg";
        case "zh-CN":
          return "/images/icons/flags/china.svg";
        case "en":
        default:
          return "/images/icons/flags/united-states-of-america.svg";
      }
    });
    function setOnlineStatus(e2) {
      let condition = navigator.onLine ? true : false;
      isOnLine.value = condition;
      let conditiondesc = navigator.onLine ? "ONLINE" : "OFFLINE";
      isOnLineDescription.value = conditiondesc;
    }
    const setPinLocked = () => {
      userSession.setIsBlockedPin(1);
    };
    onMounted(() => {
      isOnLine.value = navigator.onLine ? true : false;
      isOnLineDescription.value = navigator.onLine ? "ONLINE" : "OFFLINE";
      window.addEventListener("online", setOnlineStatus);
      window.addEventListener("offline", setOnlineStatus);
    });
    return (_ctx, _cache) => {
      const _component_VIconButton = _sfc_main$h;
      const _component_VButtons = _sfc_main$d;
      const _component_VField = _sfc_main$e;
      const _directive_tooltip = resolveDirective("tooltip");
      const _directive_tippy = resolveDirective("tippy");
      return openBlock(), createElementBlock("div", _hoisted_1$3, [
        unref(userRole) == "MOZO" ? (openBlock(), createElementBlock("div", _hoisted_2$3, [
          createVNode(_component_VField, null, {
            default: withCtx(() => [
              withDirectives(createVNode(_component_VButtons, null, {
                default: withCtx(() => [
                  createVNode(_component_VIconButton, {
                    color: "danger",
                    outlined: "",
                    circle: "",
                    icon: "lucide:lock",
                    onClick: setPinLocked
                  })
                ]),
                _: 1
              }, 512), [
                [
                  _directive_tooltip,
                  "Bloquear pantalla",
                  void 0,
                  {
                    bottom: true,
                    bubble: true
                  }
                ]
              ])
            ]),
            _: 1
          })
        ])) : createCommentVNode("", true),
        unref(isOnLine) ? withDirectives((openBlock(), createElementBlock("a", _hoisted_3$3, _hoisted_5$3, 512)), [
          [
            _directive_tooltip,
            unref(isOnLineDescription),
            void 0,
            {
              bottom: true,
              bubble: true
            }
          ]
        ]) : withDirectives((openBlock(), createElementBlock("a", _hoisted_6$3, _hoisted_8$2, 512)), [
          [
            _directive_tooltip,
            unref(isOnLineDescription),
            void 0,
            {
              bottom: true,
              bubble: true
            }
          ]
        ]),
        withDirectives(createBaseVNode("span", _hoisted_9$2, [
          (openBlock(), createElementBlock("svg", {
            xmlns: "http://www.w3.org/2000/svg",
            width: "32",
            height: "32",
            viewBox: "0 0 24 24",
            fill: "none",
            stroke: unref(companySession).configuration.printer_enabled ? "var(--primary)" : "var(--light-text)",
            "stroke-width": "2.25",
            "stroke-linecap": "round",
            "stroke-linejoin": "round"
          }, _hoisted_14$1, 8, _hoisted_10$2)),
          !unref(companySession).configuration.printer_enabled ? (openBlock(), createElementBlock("span", _hoisted_15$1)) : createCommentVNode("", true)
        ], 512), [
          [_directive_tippy, !unref(companySession).configuration.printer_enabled ? "Configurar impresi\xF3n directa en tu administrador" : ""]
        ]),
        createBaseVNode("div", _hoisted_16$1, [
          createBaseVNode("label", _hoisted_17$1, [
            createBaseVNode("input", {
              type: "checkbox",
              checked: !unref(isDark),
              onChange: _cache[0] || (_cache[0] = (...args) => unref(toggleDarkModeHandler) && unref(toggleDarkModeHandler)(...args))
            }, null, 40, _hoisted_18$1),
            _hoisted_19$1
          ])
        ])
      ]);
    };
  }
});
var Navbar_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$2 = { class: "navbar-navbar-inner" };
const _hoisted_2$2 = { class: "left" };
const _hoisted_3$2 = /* @__PURE__ */ createBaseVNode("h1", { class: "title is-5" }, "Page Title", -1);
const _hoisted_4$2 = { class: "center" };
const _hoisted_5$2 = /* @__PURE__ */ createBaseVNode("div", { class: "centered-links" }, [
  /* @__PURE__ */ createBaseVNode("a", {
    href: "/",
    class: "centered-link centered-link-toggle"
  }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "iconify",
      "data-icon": "feather:activity"
    }),
    /* @__PURE__ */ createBaseVNode("span", null, "Homepage")
  ])
], -1);
const _hoisted_6$2 = { class: "right" };
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    theme: { type: String, required: false, default: "default" }
  },
  setup(__props) {
    const props = __props;
    const { y } = useWindowScroll();
    const isScrolling = computed(() => {
      return y.value > 30;
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock(Fragment, null, [
        createBaseVNode("div", {
          class: normalizeClass(["navbar-navbar", [
            unref(isScrolling) && "is-scrolled",
            props.theme === "fade" && "is-transparent",
            props.theme === "colored" && "is-colored"
          ]])
        }, [
          createBaseVNode("div", _hoisted_1$2, [
            createBaseVNode("div", _hoisted_2$2, [
              renderSlot(_ctx.$slots, "title", {}, () => [
                _hoisted_3$2
              ])
            ]),
            createBaseVNode("div", _hoisted_4$2, [
              renderSlot(_ctx.$slots, "links", {}, () => [
                _hoisted_5$2
              ])
            ]),
            createBaseVNode("div", _hoisted_6$2, [
              renderSlot(_ctx.$slots, "toolbar")
            ])
          ])
        ], 2),
        renderSlot(_ctx.$slots, "subnav")
      ], 64);
    };
  }
});
const _hoisted_1$1 = { class: "toolbar-notifications is-hidden-mobile" };
const _hoisted_2$1 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:bell"
}, null, -1);
const _hoisted_3$1 = /* @__PURE__ */ createBaseVNode("span", { class: "new-indicator pulsate" }, null, -1);
const _hoisted_4$1 = [
  _hoisted_2$1,
  _hoisted_3$1
];
const _hoisted_5$1 = {
  class: "dropdown-menu",
  role: "menu"
};
const _hoisted_6$1 = { class: "dropdown-content" };
const _hoisted_7$1 = { class: "heading" };
const _hoisted_8$1 = /* @__PURE__ */ createBaseVNode("div", { class: "heading-left" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "heading-title" }, "Notificaciones")
], -1);
const _hoisted_9$1 = { class: "heading-right" };
const _hoisted_10$1 = /* @__PURE__ */ createTextVNode(" Ver todo ");
const _hoisted_11$1 = /* @__PURE__ */ createStaticVNode('<ul class="notification-list"><li><a class="notification-item"><div class="img-left"></div><div class="user-content"><p class="user-info"><span class="name">Alice C.</span> left a comment. </p><p class="time">1 hour ago</p></div></a></li></ul>', 1);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const dropdownElement = ref();
    const dropdown = useDropdown(dropdownElement);
    return (_ctx, _cache) => {
      const _component_RouterLink = resolveComponent("RouterLink");
      return openBlock(), createElementBlock("div", _hoisted_1$1, [
        createBaseVNode("div", {
          ref: (_value, _refs) => {
            _refs["dropdownElement"] = _value;
            dropdownElement.value = _value;
          },
          class: "dropdown is-spaced is-dots is-right dropdown-trigger"
        }, [
          createBaseVNode("div", {
            class: "is-trigger",
            "aria-haspopup": "true",
            onClick: _cache[0] || (_cache[0] = (...args) => unref(dropdown).toggle && unref(dropdown).toggle(...args))
          }, _hoisted_4$1),
          createBaseVNode("div", _hoisted_5$1, [
            createBaseVNode("div", _hoisted_6$1, [
              createBaseVNode("div", _hoisted_7$1, [
                _hoisted_8$1,
                createBaseVNode("div", _hoisted_9$1, [
                  createVNode(_component_RouterLink, {
                    class: "notification-link",
                    to: { name: "app" }
                  }, {
                    default: withCtx(() => [
                      _hoisted_10$1
                    ]),
                    _: 1
                  })
                ])
              ]),
              _hoisted_11$1
            ])
          ])
        ], 512)
      ]);
    };
  }
});
const activePanel = useStorage("active-panel", "none");
const _hoisted_1 = { class: "navbar-layout" };
const _hoisted_2 = /* @__PURE__ */ createBaseVNode("div", { class: "app-overlay" }, null, -1);
const _hoisted_3 = { class: "brand-end" };
const _hoisted_4 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:airplay"
}, null, -1);
const _hoisted_5 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:grid"
}, null, -1);
const _hoisted_6 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:book"
}, null, -1);
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:truck"
}, null, -1);
const _hoisted_8 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:shopping-bag"
}, null, -1);
const _hoisted_9 = { key: 0 };
const _hoisted_10 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:check-square"
}, null, -1);
const _hoisted_11 = /* @__PURE__ */ createBaseVNode("li", null, [
  /* @__PURE__ */ createBaseVNode("a", { href: "#" }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "iconify",
      "data-icon": "feather:settings"
    })
  ])
], -1);
const _hoisted_12 = /* @__PURE__ */ createBaseVNode("div", { class: "separator" }, null, -1);
const _hoisted_13 = { class: "title is-5" };
const _hoisted_14 = { class: "centered-links" };
const _hoisted_15 = /* @__PURE__ */ createBaseVNode("span", null, "POS", -1);
const _hoisted_16 = /* @__PURE__ */ createBaseVNode("span", null, "Mesas", -1);
const _hoisted_17 = /* @__PURE__ */ createBaseVNode("span", null, "Pedidos", -1);
const _hoisted_18 = /* @__PURE__ */ createBaseVNode("span", null, "Delivery", -1);
const _hoisted_19 = /* @__PURE__ */ createBaseVNode("span", { style: { "text-wrap": "nowrap" } }, "Para Llevar", -1);
const _hoisted_20 = /* @__PURE__ */ createBaseVNode("span", null, "Comanda", -1);
const _hoisted_21 = { class: "view-wrapper has-top-nav" };
const _hoisted_22 = { class: "page-content-wrapper" };
const _hoisted_23 = {
  key: 1,
  class: "page-content is-relative"
};
const _hoisted_24 = { class: "is-navbar-lg" };
const _hoisted_25 = { class: "page-title has-text-centered" };
const _hoisted_26 = { class: "title-wrap" };
const _hoisted_27 = { class: "title is-4" };
const _hoisted_28 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:grid"
}, null, -1);
const _hoisted_29 = [
  _hoisted_28
];
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    theme: { type: String, required: false, default: "colored" },
    nowrap: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    const route = useRoute();
    ref("");
    const isMobileSidebarOpen = ref(false);
    const isDesktopSidebarOpen = ref(props.openOnMounted);
    ref(props.defaultSidebar);
    const companySession = useCompanySession();
    const configuration = companySession.configuration;
    const userSession = reactive(useUserSession());
    const role = ref("NOTHING");
    const rolePermission = ref([]);
    const mesasSession = useMesaSession();
    const verifyPermissionRoute = (name) => {
      if (role.value && rolePermission.value.includes(name)) {
        return true;
      }
      return false;
    };
    const setRole = () => {
      role.value = userSession.userRole;
      const roleFound = ROLES.find((rol) => rol.code == role.value);
      if (roleFound) {
        rolePermission.value = roleFound.permissions;
        if (configuration.enabled_command_waiter) {
          rolePermission.value.push(NAME_ROUTE_COMMANDS);
        }
        if (configuration.enabled_pos_waiter) {
          rolePermission.value.push(NAME_ROUTE_POS);
        }
      }
    };
    watchPostEffect(() => {
      const isOpen = isDesktopSidebarOpen.value;
      const wrappers = document.querySelectorAll(".view-wrapper");
      wrappers.forEach((wrapper) => {
        if (isOpen === false) {
          wrapper.classList.remove("is-pushed-full");
        } else if (!wrapper.classList.contains("is-pushed-full")) {
          wrapper.classList.add("is-pushed-full");
        }
      });
    });
    watch(() => route.fullPath, () => {
      isMobileSidebarOpen.value = false;
      if (props.closeOnChange && isDesktopSidebarOpen.value) {
        isDesktopSidebarOpen.value = false;
      }
    });
    watch(() => [...userSession.userRole], () => {
      setRole();
    });
    onMounted(async () => {
      use.connect();
      use.sendCompany();
      setRole();
    });
    return (_ctx, _cache) => {
      const _component_VReloadPrompt = _sfc_main$a;
      const _component_IsotipoMozoOficial = __unplugin_components_1$1;
      const _component_RouterLink = resolveComponent("RouterLink");
      const _component_UserProfileDropdown = __unplugin_components_2;
      const _component_MobileNavbar = _sfc_main$5;
      const _component_MobileSidebar = _sfc_main$4;
      const _component_Toolbar = _sfc_main$3;
      const _component_Navbar = _sfc_main$2;
      const _component_ToolbarNotification = _sfc_main$1;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_VReloadPrompt, { "app-name": "Mozo.pe" }),
        createBaseVNode("div", _hoisted_1, [
          _hoisted_2,
          createVNode(_component_MobileNavbar, {
            "is-open": isMobileSidebarOpen.value,
            onToggle: _cache[0] || (_cache[0] = ($event) => isMobileSidebarOpen.value = !isMobileSidebarOpen.value)
          }, {
            brand: withCtx(() => [
              createVNode(_component_RouterLink, {
                to: { name: unref(companySession).firstMenu },
                class: "navbar-item is-brand"
              }, {
                default: withCtx(() => [
                  createVNode(_component_IsotipoMozoOficial, {
                    width: "38px",
                    height: "38px"
                  })
                ]),
                _: 1
              }, 8, ["to"]),
              createBaseVNode("div", _hoisted_3, [
                createVNode(_component_UserProfileDropdown)
              ])
            ]),
            _: 1
          }, 8, ["is-open"]),
          createVNode(_component_MobileSidebar, {
            "is-open": isMobileSidebarOpen.value,
            onToggle: _cache[1] || (_cache[1] = ($event) => isMobileSidebarOpen.value = !isMobileSidebarOpen.value)
          }, {
            links: withCtx(() => [
              createBaseVNode("li", null, [
                verifyPermissionRoute(unref(NAME_ROUTE_POS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  to: { name: "app" }
                }, {
                  default: withCtx(() => [
                    _hoisted_4
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ]),
              createBaseVNode("li", null, [
                verifyPermissionRoute(unref(NAME_ROUTE_MESAS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  to: { name: "app-mesas" }
                }, {
                  default: withCtx(() => [
                    _hoisted_5
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ]),
              createBaseVNode("li", null, [
                verifyPermissionRoute(unref(NAME_ROUTE_ORDERS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  to: { name: "app-orders" }
                }, {
                  default: withCtx(() => [
                    _hoisted_6
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ]),
              createBaseVNode("li", null, [
                verifyPermissionRoute(unref(NAME_ROUTE_DELIVERY)) && unref(mesasSession).hasDeliveryEnvironment ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  to: { name: "app-delivery" }
                }, {
                  default: withCtx(() => [
                    _hoisted_7
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ]),
              createBaseVNode("li", null, [
                verifyPermissionRoute(unref(NAME_ROUTE_TAKEAWAY)) && unref(mesasSession).hasTakeawayEnvironment ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  to: { name: "app-takeaway" }
                }, {
                  default: withCtx(() => [
                    _hoisted_8
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ]),
              verifyPermissionRoute(unref(NAME_ROUTE_COMMANDS)) ? (openBlock(), createElementBlock("li", _hoisted_9, [
                createVNode(_component_RouterLink, { to: { name: "app-commands" } }, {
                  default: withCtx(() => [
                    _hoisted_10
                  ]),
                  _: 1
                })
              ])) : createCommentVNode("", true)
            ]),
            "bottom-links": withCtx(() => [
              _hoisted_11
            ]),
            _: 1
          }, 8, ["is-open"]),
          createVNode(_component_Navbar, {
            theme: props.theme
          }, {
            title: withCtx(() => [
              createVNode(_component_RouterLink, {
                to: { name: unref(companySession).firstMenu },
                class: "brand"
              }, {
                default: withCtx(() => [
                  createVNode(_component_IsotipoMozoOficial, { style: { "width": "38px", "height": "38px" } })
                ]),
                _: 1
              }, 8, ["to"]),
              _hoisted_12,
              createBaseVNode("h1", _hoisted_13, toDisplayString(unref(pageTitle)), 1)
            ]),
            toolbar: withCtx(() => [
              createVNode(_component_Toolbar, { class: "desktop-toolbar" }),
              createVNode(_component_UserProfileDropdown, { right: "" })
            ]),
            links: withCtx(() => [
              createBaseVNode("div", _hoisted_14, [
                verifyPermissionRoute(unref(NAME_ROUTE_POS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 0,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app" }
                }, {
                  default: withCtx(() => [
                    _hoisted_15
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                verifyPermissionRoute(unref(NAME_ROUTE_MESAS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 1,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app-mesas" }
                }, {
                  default: withCtx(() => [
                    _hoisted_16
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                verifyPermissionRoute(unref(NAME_ROUTE_ORDERS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 2,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app-orders" }
                }, {
                  default: withCtx(() => [
                    _hoisted_17
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                verifyPermissionRoute(unref(NAME_ROUTE_DELIVERY)) && unref(mesasSession).hasDeliveryEnvironment ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 3,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app-delivery" }
                }, {
                  default: withCtx(() => [
                    _hoisted_18
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                verifyPermissionRoute(unref(NAME_ROUTE_TAKEAWAY)) && unref(mesasSession).hasTakeawayEnvironment ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 4,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app-takeaway" }
                }, {
                  default: withCtx(() => [
                    _hoisted_19
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                verifyPermissionRoute(unref(NAME_ROUTE_COMMANDS)) ? (openBlock(), createBlock(_component_RouterLink, {
                  key: 5,
                  class: "centered-link centered-link-toggle",
                  to: { name: "app-commands" }
                }, {
                  default: withCtx(() => [
                    _hoisted_20
                  ]),
                  _: 1
                })) : createCommentVNode("", true)
              ])
            ]),
            _: 1
          }, 8, ["theme"]),
          createBaseVNode("div", _hoisted_21, [
            createBaseVNode("div", _hoisted_22, [
              props.nowrap ? renderSlot(_ctx.$slots, "default", { key: 0 }) : (openBlock(), createElementBlock("div", _hoisted_23, [
                createBaseVNode("div", _hoisted_24, [
                  createBaseVNode("div", _hoisted_25, [
                    createBaseVNode("div", _hoisted_26, [
                      createBaseVNode("h1", _hoisted_27, toDisplayString(unref(pageTitle)), 1)
                    ]),
                    createVNode(_component_Toolbar, { class: "mobile-toolbar" }, {
                      default: withCtx(() => [
                        createVNode(_component_ToolbarNotification),
                        createBaseVNode("a", {
                          class: "toolbar-link right-panel-trigger",
                          onClick: _cache[2] || (_cache[2] = ($event) => activePanel.value = "activity")
                        }, _hoisted_29)
                      ]),
                      _: 1
                    })
                  ]),
                  renderSlot(_ctx.$slots, "default")
                ])
              ]))
            ])
          ])
        ])
      ], 64);
    };
  }
});
export { _sfc_main as default };
