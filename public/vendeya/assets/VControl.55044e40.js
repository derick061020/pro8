import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { b as defineComponent, a as computed, f as openBlock, g as createElementBlock, y as renderSlot, Z as unref, Y as normalizeClass, C as createCommentVNode, G as pushScopeId, H as popScopeId, X as createBaseVNode } from "./vendor.80d481a8.js";
var VControl_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-fc9d2584"), n = n(), popScopeId(), n);
const _hoisted_1 = {
  key: 0,
  class: "form-icon"
};
const _hoisted_2 = ["data-icon"];
const _hoisted_3 = {
  key: 1,
  class: "validation-icon is-success"
};
const _hoisted_4 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:check"
}, null, -1));
const _hoisted_5 = [
  _hoisted_4
];
const _hoisted_6 = {
  key: 2,
  class: "validation-icon is-error"
};
const _hoisted_7 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:x"
}, null, -1));
const _hoisted_8 = [
  _hoisted_7
];
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    icon: { type: String, required: false, default: void 0 },
    isValid: { type: Boolean, required: false },
    hasError: { type: Boolean, required: false },
    loading: { type: Boolean, required: false },
    expanded: { type: Boolean, required: false },
    textaddon: { type: Boolean, required: false },
    nogrow: { type: Boolean, required: false },
    subcontrol: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    const isIconify = computed(() => {
      return props.icon && props.icon.indexOf(":") !== -1;
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["control", [
          __props.icon && "has-icon",
          __props.loading && "is-loading",
          __props.expanded && "is-expanded",
          __props.nogrow && "is-nogrow",
          __props.textaddon && "is-textarea-addon",
          __props.isValid && "has-validation has-success",
          __props.hasError && "has-validation has-error",
          __props.subcontrol && "subcontrol"
        ]])
      }, [
        renderSlot(_ctx.$slots, "default", {}, void 0, true),
        __props.icon ? (openBlock(), createElementBlock("div", _hoisted_1, [
          unref(isIconify) ? (openBlock(), createElementBlock("i", {
            key: 0,
            "aria-hidden": "true",
            class: "iconify",
            "data-icon": __props.icon
          }, null, 8, _hoisted_2)) : (openBlock(), createElementBlock("i", {
            key: 1,
            "aria-hidden": "true",
            class: normalizeClass(__props.icon)
          }, null, 2))
        ])) : createCommentVNode("", true),
        __props.isValid ? (openBlock(), createElementBlock("div", _hoisted_3, _hoisted_5)) : __props.hasError ? (openBlock(), createElementBlock("div", _hoisted_6, _hoisted_8)) : createCommentVNode("", true),
        renderSlot(_ctx.$slots, "extra", {}, void 0, true)
      ], 2);
    };
  }
});
var __unplugin_components_1 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-fc9d2584"]]);
export { __unplugin_components_1 as _ };
