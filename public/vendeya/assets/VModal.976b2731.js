import { b as defineComponent, f as openBlock, g as createElementBlock, y as renderSlot, Y as normalizeClass, Z as unref, X as createBaseVNode, z as toDisplayString, C as createCommentVNode, aj as useI18n, r as ref, a as computed, a1 as watchEffect, P as onUnmounted, v as createBlock, aa as normalizeStyle, aq as Teleport } from "./vendor.80d481a8.js";
function useViaPlaceholderError(event, size) {
  const target = event.target;
  target.src = `https://placehold.co/${size}`;
}
const _hoisted_1$1 = ["src"];
const _hoisted_2$1 = ["src"];
const _hoisted_3$1 = ["src"];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    picture: { type: String, required: false, default: void 0 },
    pictureDark: { type: String, required: false, default: void 0 },
    placeholder: { type: String, required: false, default: "https://via.placeholder.com/50x50" },
    badge: { type: String, required: false, default: void 0 },
    initials: { type: String, required: false, default: "?" },
    size: { type: String, required: false, default: void 0 },
    color: { type: String, required: false, default: void 0 },
    dotColor: { type: String, required: false, default: void 0 },
    squared: { type: Boolean, required: false },
    dot: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["v-avatar", [
          __props.size && `is-${props.size}`,
          __props.dot && "has-dot",
          __props.dotColor && `dot-${props.dotColor}`,
          __props.squared && __props.dot && "has-dot-squared"
        ]])
      }, [
        renderSlot(_ctx.$slots, "avatar", {}, () => [
          props.picture ? (openBlock(), createElementBlock("img", {
            key: 0,
            class: normalizeClass(["avatar", [
              props.squared && "is-squared",
              props.pictureDark && "light-image"
            ]]),
            src: props.picture,
            alt: "",
            onErrorOnce: _cache[0] || (_cache[0] = (event) => unref(useViaPlaceholderError)(event, "150x150"))
          }, null, 42, _hoisted_1$1)) : (openBlock(), createElementBlock("span", {
            key: 1,
            class: normalizeClass(["avatar is-fake", [
              props.squared && "is-squared",
              props.color && `is-${props.color}`
            ]])
          }, [
            createBaseVNode("span", null, toDisplayString(props.initials), 1)
          ], 2)),
          props.picture && props.pictureDark ? (openBlock(), createElementBlock("img", {
            key: 2,
            class: normalizeClass(["avatar dark-image", [props.squared && "is-squared"]]),
            src: props.pictureDark,
            alt: "",
            onErrorOnce: _cache[1] || (_cache[1] = (event) => unref(useViaPlaceholderError)(event, "150x150"))
          }, null, 42, _hoisted_2$1)) : createCommentVNode("", true)
        ]),
        renderSlot(_ctx.$slots, "badge", {}, () => [
          props.badge ? (openBlock(), createElementBlock("img", {
            key: 0,
            class: "badge",
            src: props.badge,
            alt: "",
            onErrorOnce: _cache[2] || (_cache[2] = (event) => unref(useViaPlaceholderError)(event, "150x150"))
          }, null, 40, _hoisted_3$1)) : createCommentVNode("", true)
        ])
      ], 2);
    };
  }
});
var VModal_vue_vue_type_style_index_0_lang = "";
function block0(Component) {
  Component.__i18n = Component.__i18n || [];
  Component.__i18n.push({
    "locale": "",
    "resource": {
      "de": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Abbrechen"]);
        }
      },
      "en": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cancel"]);
        }
      },
      "es-MX": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cancelar"]);
        }
      },
      "es": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cancelar"]);
        }
      },
      "fr": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Annuler"]);
        }
      },
      "zh-CN": {
        "cancel-label": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u53D6\u6D88"]);
        }
      }
    }
  });
}
const _hoisted_1 = { class: "modal-content" };
const _hoisted_2 = { class: "modal-card" };
const _hoisted_3 = {
  key: 0,
  class: "modal-card-head"
};
const _hoisted_4 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:x"
}, null, -1);
const _hoisted_5 = [
  _hoisted_4
];
const _hoisted_6 = { class: "inner-content" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    title: { type: String, required: true },
    size: { type: String, required: false, default: void 0 },
    actions: { type: String, required: false, default: void 0 },
    open: { type: Boolean, required: false },
    rounded: { type: Boolean, required: false },
    noscroll: { type: Boolean, required: false },
    noclose: { type: Boolean, required: false },
    tabs: { type: Boolean, required: false },
    cancelLabel: { type: String, required: false, default: void 0 },
    hideheader: { type: Boolean, required: false },
    hidefooter: { type: Boolean, required: false },
    bglight: { type: Boolean, required: false },
    withmenu: { type: Boolean, required: false },
    cascade: { type: Boolean, required: false, default: void 0 }
  },
  emits: ["close"],
  setup(__props, { emit }) {
    const props = __props;
    const { t } = useI18n();
    const wasOpen = ref(false);
    const cancelLabel = computed(() => props.cancelLabel || t("cancel-label"));
    const checkScroll = () => {
      if (props.noscroll && props.open) {
        document.documentElement.classList.add("no-scroll");
        wasOpen.value = true;
      } else if (wasOpen.value && props.noscroll && !props.open) {
        document.documentElement.classList.remove("no-scroll");
        wasOpen.value = false;
      }
    };
    watchEffect(checkScroll);
    onUnmounted(() => {
      document.documentElement.classList.remove("no-scroll");
    });
    const styleCascade = {
      zIndex: "300 !important"
    };
    return (_ctx, _cache) => {
      return openBlock(), createBlock(Teleport, { to: "body" }, [
        createBaseVNode("div", {
          class: normalizeClass([[__props.open && "is-active", __props.size && `is-${__props.size}`, __props.withmenu && "with-top-menu"], "modal v-modal"]),
          style: normalizeStyle(props.cascade ? styleCascade : null)
        }, [
          createBaseVNode("div", {
            class: normalizeClass(["modal-background v-modal-close", [__props.bglight && "bg-light"]]),
            onClick: _cache[0] || (_cache[0] = () => __props.noclose === false && emit("close"))
          }, null, 2),
          createBaseVNode("div", _hoisted_1, [
            createBaseVNode("div", _hoisted_2, [
              !__props.hideheader ? (openBlock(), createElementBlock("header", _hoisted_3, [
                createBaseVNode("h3", null, toDisplayString(__props.title), 1),
                createBaseVNode("button", {
                  class: "v-modal-close ml-auto",
                  "aria-label": "close",
                  onClick: _cache[1] || (_cache[1] = ($event) => emit("close"))
                }, _hoisted_5)
              ])) : createCommentVNode("", true),
              createBaseVNode("div", {
                class: normalizeClass(["modal-card-body", [props.tabs && "has-tabs", __props.hideheader == true && "radius"]])
              }, [
                createBaseVNode("div", _hoisted_6, [
                  renderSlot(_ctx.$slots, "content")
                ])
              ], 2),
              !__props.hidefooter ? (openBlock(), createElementBlock("div", {
                key: 1,
                class: normalizeClass(["modal-card-foot", [
                  __props.actions === "center" && "is-centered",
                  __props.actions === "right" && "is-end"
                ]])
              }, [
                renderSlot(_ctx.$slots, "cancel", {}, () => [
                  createBaseVNode("a", {
                    class: normalizeClass(["button v-button v-modal-close", [__props.rounded && "is-rounded"]]),
                    onClick: _cache[2] || (_cache[2] = ($event) => emit("close"))
                  }, toDisplayString(unref(cancelLabel)), 3)
                ]),
                renderSlot(_ctx.$slots, "action")
              ], 2)) : createCommentVNode("", true)
            ])
          ])
        ], 6)
      ]);
    };
  }
});
if (typeof block0 === "function")
  block0(_sfc_main);
export { _sfc_main as _, _sfc_main$1 as a, useViaPlaceholderError as u };
