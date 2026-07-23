import { b as defineComponent, a as computed, f as openBlock, g as createElementBlock, Z as unref, Y as normalizeClass } from "./vendor.05b8b5b5.js";
const _hoisted_1 = ["data-icon"];
const _sfc_main = /* @__PURE__ */ defineComponent({
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
        }, null, 8, _hoisted_1)) : (openBlock(), createElementBlock("i", {
          key: 1,
          "aria-hidden": "true",
          class: normalizeClass(props.icon)
        }, null, 2))
      ]);
    };
  }
});
export { _sfc_main as _ };
