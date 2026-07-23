import { b as defineComponent, a4 as useRouter, r as ref, Z as unref, f as openBlock, g as createElementBlock } from "./vendor.80d481a8.js";
import { i as isDark } from "./IsotipoMozoOficial.415c8e31.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
var _imports_0 = "/vendeya/images/logos/logo-horizontal-light.svg";
var _imports_1 = "/vendeya/images/logos/logo-oficial-horizontal.svg";
var LogoMozoOficial_vue_vue_type_style_index_0_scoped_true_lang = "";
const _hoisted_1 = {
  key: 0,
  class: "mt-1",
  src: _imports_0,
  alt: "",
  style: { "width": "150px" }
};
const _hoisted_2 = {
  key: 1,
  class: "mt-1",
  src: _imports_1,
  alt: "",
  style: { "width": "150px" }
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
var __unplugin_components_0 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-8e50d7e2"]]);
export { __unplugin_components_0 as _ };
