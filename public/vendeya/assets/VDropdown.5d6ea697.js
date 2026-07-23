var __defProp = Object.defineProperty;
var __getOwnPropSymbols = Object.getOwnPropertySymbols;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __propIsEnum = Object.prototype.propertyIsEnumerable;
var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
var __spreadValues = (a, b) => {
  for (var prop in b || (b = {}))
    if (__hasOwnProp.call(b, prop))
      __defNormalProp(a, prop, b[prop]);
  if (__getOwnPropSymbols)
    for (var prop of __getOwnPropSymbols(b)) {
      if (__propIsEnum.call(b, prop))
        __defNormalProp(a, prop, b[prop]);
    }
  return a;
};
import { b as defineComponent, a as computed, f as openBlock, g as createElementBlock, Z as unref, Y as normalizeClass, r as ref, a0 as onClickOutside, a1 as watchEffect, t as reactive, y as renderSlot, w as createVNode, z as toDisplayString, C as createCommentVNode, X as createBaseVNode, v as createBlock, a2 as normalizeProps, a3 as guardReactiveProps } from "./vendor.80d481a8.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
const _hoisted_1$1 = ["data-icon"];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    icon: { type: String, required: true }
  },
  setup(__props) {
    const props = __props;
    const isIconify = computed(() => {
      return props.icon && props.icon.indexOf(":") !== -1;
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("span", {
        key: props.icon
      }, [
        unref(isIconify) ? (openBlock(), createElementBlock("i", {
          key: 0,
          "aria-hidden": "true",
          class: "iconify",
          "data-icon": props.icon
        }, null, 8, _hoisted_1$1)) : (openBlock(), createElementBlock("i", {
          key: 1,
          "aria-hidden": "true",
          class: normalizeClass(props.icon)
        }, null, 2))
      ]);
    };
  }
});
function useDropdown(container) {
  const isOpen = ref(false);
  onClickOutside(container, () => {
    isOpen.value = false;
  });
  const open = () => {
    isOpen.value = true;
  };
  const close = () => {
    isOpen.value = false;
  };
  const toggle = () => {
    isOpen.value = !isOpen.value;
  };
  watchEffect(() => {
    if (!container.value) {
      return;
    }
    if (isOpen.value) {
      container.value.classList.add("is-active");
    } else {
      container.value.classList.remove("is-active");
    }
  });
  return reactive({
    isOpen,
    open,
    close,
    toggle
  });
}
var VDropdown_vue_vue_type_style_index_0_scoped_true_lang = "";
const _hoisted_1 = { key: 0 };
const _hoisted_2 = {
  class: "dropdown-menu",
  role: "menu"
};
const _hoisted_3 = { class: "dropdown-content" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    title: { type: String, required: false, default: void 0 },
    color: { type: String, required: false, default: void 0 },
    icon: { type: String, required: false, default: void 0 },
    up: { type: Boolean, required: false },
    right: { type: Boolean, required: false },
    modern: { type: Boolean, required: false },
    spaced: { type: Boolean, required: false }
  },
  setup(__props, { expose }) {
    const props = __props;
    const dropdownElement = ref();
    const dropdown = useDropdown(dropdownElement);
    expose(__spreadValues({}, dropdown));
    return (_ctx, _cache) => {
      const _component_VIcon = _sfc_main$1;
      return openBlock(), createElementBlock("div", {
        ref: (_value, _refs) => {
          _refs["dropdownElement"] = _value;
          dropdownElement.value = _value;
        },
        class: normalizeClass([[
          props.right && "is-right",
          props.up && "is-up",
          props.icon && "is-dots",
          props.modern && "is-modern",
          props.spaced && "is-spaced"
        ], "dropdown"])
      }, [
        renderSlot(_ctx.$slots, "button", normalizeProps(guardReactiveProps(unref(dropdown))), () => [
          props.icon ? (openBlock(), createElementBlock("a", {
            key: 0,
            class: "is-trigger dropdown-trigger",
            "aria-label": "View more actions",
            onClick: _cache[0] || (_cache[0] = (...args) => unref(dropdown).toggle && unref(dropdown).toggle(...args))
          }, [
            createVNode(_component_VIcon, {
              icon: props.icon
            }, null, 8, ["icon"])
          ])) : (openBlock(), createElementBlock("a", {
            key: 1,
            class: normalizeClass(["is-trigger button dropdown-trigger", [props.color && `is-${props.color}`]]),
            onClick: _cache[1] || (_cache[1] = (...args) => unref(dropdown).toggle && unref(dropdown).toggle(...args))
          }, [
            props.title ? (openBlock(), createElementBlock("span", _hoisted_1, toDisplayString(props.title), 1)) : createCommentVNode("", true),
            createBaseVNode("span", {
              class: normalizeClass([!props.modern && "base-caret", props.modern && "base-caret"])
            }, [
              !unref(dropdown).isOpen ? (openBlock(), createBlock(_component_VIcon, {
                key: 0,
                icon: "fa:angle-down"
              })) : (openBlock(), createBlock(_component_VIcon, {
                key: 1,
                icon: "fa:angle-up"
              }))
            ], 2)
          ], 2))
        ], true),
        createBaseVNode("div", _hoisted_2, [
          createBaseVNode("div", _hoisted_3, [
            renderSlot(_ctx.$slots, "content", normalizeProps(guardReactiveProps(unref(dropdown))), void 0, true)
          ])
        ])
      ], 2);
    };
  }
});
var __unplugin_components_4 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-28edd19c"]]);
export { __unplugin_components_4 as _, useDropdown as u };
