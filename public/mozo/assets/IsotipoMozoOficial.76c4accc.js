import { a8 as usePreferredDark, u as useStorage, a as computed, a1 as watchEffect, b as defineComponent, a4 as useRouter, r as ref, Z as unref, f as openBlock, g as createElementBlock } from "./vendor.05b8b5b5.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
var _imports_0 = "/mozo/images/logos/logo-iso-light.svg";
var _imports_1 = "/mozo/images/logos/logo-iso.svg";
const DARK_MODE_BODY_CLASS = "is-dark";
const preferredDark = usePreferredDark();
const colorSchema = useStorage("color-schema", "light");
const isDark = computed({
  get() {
    return colorSchema.value === "auto" ? preferredDark.value : colorSchema.value === "dark";
  },
  set(v) {
    if (v === preferredDark.value)
      colorSchema.value = "auto";
    else
      colorSchema.value = v ? "dark" : "light";
  }
});
const toggleDarkModeHandler = (event) => {
  const target = event.target;
  isDark.value = !target.checked;
};
watchEffect(() => {
  const body = document.documentElement;
  if (isDark.value) {
    body.classList.add(DARK_MODE_BODY_CLASS);
  } else {
    body.classList.remove(DARK_MODE_BODY_CLASS);
  }
});
var IsotipoMozoOficial_vue_vue_type_style_index_0_scoped_true_lang = "";
const _hoisted_1 = {
  key: 0,
  src: _imports_0,
  alt: "logo",
  style: { "width": "60px", "height": "60px" }
};
const _hoisted_2 = {
  key: 1,
  src: _imports_1,
  alt: "logo",
  style: { "width": "60px", "height": "60px" }
};
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    light: { type: Boolean, required: false }
  },
  setup(__props) {
    const router = useRouter();
    const isLoading = ref(false);
    router.beforeEach(() => {
      isLoading.value = true;
    });
    router.afterEach(() => {
      setTimeout(() => {
        isLoading.value = false;
      }, 200);
    });
    return (_ctx, _cache) => {
      return unref(isDark) ? (openBlock(), createElementBlock("img", _hoisted_1)) : (openBlock(), createElementBlock("img", _hoisted_2));
    };
  }
});
var __unplugin_components_1 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-b37e2f68"]]);
export { __unplugin_components_1 as _, isDark as i, toggleDarkModeHandler as t };
