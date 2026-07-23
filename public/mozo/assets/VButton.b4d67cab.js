var __defProp = Object.defineProperty;
var __defProps = Object.defineProperties;
var __getOwnPropDescs = Object.getOwnPropertyDescriptors;
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
var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));
import { b as defineComponent, a9 as useCssVars, f as openBlock, g as createElementBlock, aa as normalizeStyle, Y as normalizeClass, a as computed, q as h, ab as RouterLink } from "./vendor.05b8b5b5.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
const CssUnitRe = /(\d*\.?\d+)\s?(cm|mm|in|px|pt|pc|em|ex|ch|rem|vw|vh|vmin|vmax|%)/;
var VPlaceload_vue_vue_type_style_index_0_scoped_true_lang = "";
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    width: { type: String, required: false, default: "100%" },
    height: { type: String, required: false, default: "10px" },
    disabled: { type: Boolean, required: false },
    centered: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    useCssVars((_ctx) => ({
      "3ed46b6a": props.width
    }));
    if (props.width.match(CssUnitRe) === null) {
      console.warn(`VPlaceload: invalid "${props.width}" width. Should be a valid css unit value.`);
    }
    if (props.height.match(CssUnitRe) === null) {
      console.warn(`VPlaceload: invalid "${props.height}" height. Should be a valid css unit value.`);
    }
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        style: normalizeStyle(`height:${props.height}`),
        class: normalizeClass(["content-shape", [props.centered && "is-centered", !props.disabled && "loads"]])
      }, null, 6);
    };
  }
});
var __unplugin_components_4 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-ee360ce6"]]);
const _sfc_main = defineComponent({
  props: {
    to: {
      type: [Object, String],
      default: void 0
    },
    href: {
      type: String,
      default: void 0
    },
    icon: {
      type: String,
      default: void 0
    },
    iconCaret: {
      type: String,
      default: void 0
    },
    placeload: {
      type: String,
      default: void 0,
      validator: (value) => {
        if (value.match(CssUnitRe) === null) {
          console.warn(`VButton: invalid "${value}" placeload. Should be a valid css unit value.`);
        }
        return true;
      }
    },
    color: {
      type: String,
      default: void 0,
      validator: (value) => {
        if ([
          void 0,
          "primary",
          "info",
          "success",
          "warning",
          "danger",
          "white",
          "dark",
          "light"
        ].indexOf(value) === -1) {
          console.warn(`VButton: invalid "${value}" color. Should be primary, info, success, warning, danger, dark, light, white or undefined`);
          return false;
        }
        return true;
      }
    },
    size: {
      type: String,
      default: void 0,
      validator: (value) => {
        if ([void 0, "big", "huge"].indexOf(value) === -1) {
          console.warn(`VButton: invalid "${value}" size. Should be big, huge or undefined`);
          return false;
        }
        return true;
      }
    },
    dark: {
      type: String,
      default: void 0,
      validator: (value) => {
        if ([void 0, "1", "2", "3", "4", "5", "6"].indexOf(value) === -1) {
          console.warn(`VButton: invalid "${value}" dark. Should be 1, 2, 3, 4, 5, 6 or undefined`);
          return false;
        }
        return true;
      }
    },
    rounded: {
      type: Boolean,
      default: false
    },
    bold: {
      type: Boolean,
      default: false
    },
    fullwidth: {
      type: Boolean,
      default: false
    },
    light: {
      type: Boolean,
      default: false
    },
    raised: {
      type: Boolean,
      default: false
    },
    elevated: {
      type: Boolean,
      default: false
    },
    outlined: {
      type: Boolean,
      default: false
    },
    darkOutlined: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    lower: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  setup(props, { slots, attrs }) {
    const classes = computed(() => {
      var _a;
      const defaultClasses = (_a = attrs == null ? void 0 : attrs.class) != null ? _a : [];
      return [
        defaultClasses,
        "button",
        "v-button",
        props.disabled && "is-disabled",
        props.rounded && "is-rounded",
        props.bold && "is-bold",
        props.size && `is-${props.size}`,
        props.lower && "is-lower",
        props.fullwidth && "is-fullwidth",
        props.outlined && "is-outlined",
        props.dark && `is-dark-bg-${props.dark}`,
        props.darkOutlined && "is-dark-outlined",
        props.raised && "is-raised",
        props.elevated && "is-elevated",
        props.loading && !props.placeload && "is-loading",
        props.color && `is-${props.color}`,
        props.light && "is-light"
      ];
    });
    const isIconify = computed(() => props.icon && props.icon.indexOf(":") !== -1);
    const isCaretIconify = computed(() => props.iconCaret && props.iconCaret.indexOf(":") !== -1);
    const getChildrens = () => {
      var _a;
      const childrens = [];
      let iconWrapper;
      if (isIconify.value) {
        const icon = h("i", {
          "aria-hidden": true,
          class: "iconify",
          "data-icon": props.icon
        });
        iconWrapper = h("span", { class: "icon" }, icon);
      } else if (props.icon) {
        const icon = h("i", { "aria-hidden": true, class: props.icon });
        iconWrapper = h("span", { class: "icon" }, icon);
      }
      let caretWrapper;
      if (isCaretIconify.value) {
        const caret = h("i", {
          "aria-hidden": true,
          class: "iconify",
          "data-icon": props.iconCaret
        });
        caretWrapper = h("span", { class: "caret" }, caret);
      } else if (props.iconCaret) {
        const caret = h("i", { "aria-hidden": true, class: props.iconCaret });
        caretWrapper = h("span", { class: "caret" }, caret);
      }
      if (iconWrapper) {
        childrens.push(iconWrapper);
      }
      if (props.placeload) {
        childrens.push(h(__unplugin_components_4, {
          width: props.placeload
        }));
      } else {
        childrens.push(h("span", (_a = slots.default) == null ? void 0 : _a.call(slots)));
      }
      if (caretWrapper) {
        childrens.push(caretWrapper);
      }
      return childrens;
    };
    return () => {
      if (props.to) {
        return h(RouterLink, __spreadProps(__spreadValues({}, attrs), {
          "aria-hidden": !!props.placeload && true,
          to: props.to,
          class: ["button", ...classes.value]
        }), {
          default: getChildrens
        });
      } else if (props.href) {
        return h("a", __spreadProps(__spreadValues({}, attrs), {
          "aria-hidden": !!props.placeload && true,
          href: props.href,
          class: classes.value
        }), {
          default: getChildrens
        });
      }
      return h("button", __spreadProps(__spreadValues({
        type: "button"
      }, attrs), {
        "aria-hidden": !!props.placeload && true,
        disabled: props.disabled,
        class: ["button", ...classes.value]
      }), {
        default: getChildrens
      });
    };
  }
});
export { CssUnitRe as C, _sfc_main as _, __unplugin_components_4 as a };
