import { b as defineComponent, ac as useI18n, r as ref, a as computed, a1 as watchEffect, P as onUnmounted, f as openBlock, v as createBlock, X as createBaseVNode, Y as normalizeClass, g as createElementBlock, z as toDisplayString, y as renderSlot, C as createCommentVNode, Z as unref, aa as normalizeStyle, ad as Teleport } from "./vendor.05b8b5b5.js";
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
          class: normalizeClass([[
            __props.open && "is-active",
            __props.size && `is-${__props.size}`,
            __props.withmenu && "with-top-menu"
          ], "modal v-modal"]),
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
                renderSlot(_ctx.$slots, "header"),
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
export { _sfc_main as _ };
