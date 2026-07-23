import _sfc_main$1 from "./AppLayout.c1d2308e.js";
import { b as defineComponent, a5 as useRoute, x as resolveComponent, f as openBlock, v as createBlock, B as withCtx, w as createVNode, T as Transition, s as resolveDynamicComponent, Z as unref } from "./vendor.80d481a8.js";
import "./VButton.ec3d6c22.js";
import "./plugin-vue_export-helper.5a098b48.js";
import "./VIconButton.f9e7ed06.js";
import "./index.ea91e044.js";
import "./IsotipoMozoOficial.415c8e31.js";
import "./VModal.976b2731.js";
import "./VControl.55044e40.js";
import "./VField.dd3df504.js";
import "./VDropdown.5d6ea697.js";
import "./masterService.8bbc48d4.js";
var block0 = {};
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const route = useRoute();
    return (_ctx, _cache) => {
      const _component_RouterView = resolveComponent("RouterView");
      const _component_AppLayout = _sfc_main$1;
      return openBlock(), createBlock(_component_AppLayout, null, {
        default: withCtx(() => [
          createVNode(_component_RouterView, null, {
            default: withCtx(({ Component }) => [
              createVNode(Transition, {
                name: "fade-fast",
                mode: "out-in"
              }, {
                default: withCtx(() => [
                  (openBlock(), createBlock(resolveDynamicComponent(Component), {
                    key: unref(route).fullPath
                  }))
                ]),
                _: 2
              }, 1024)
            ]),
            _: 1
          })
        ]),
        _: 1
      });
    };
  }
});
if (typeof block0 === "function")
  block0(_sfc_main);
export { _sfc_main as default };
