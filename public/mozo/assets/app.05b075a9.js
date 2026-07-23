import _sfc_main$1 from "./AppLayout.c5f8dd87.js";
import { b as defineComponent, a5 as useRoute, x as resolveComponent, f as openBlock, v as createBlock, B as withCtx, w as createVNode, T as Transition, s as resolveDynamicComponent, Z as unref } from "./vendor.05b8b5b5.js";
import "./VButton.b4d67cab.js";
import "./plugin-vue_export-helper.5a098b48.js";
import "./VIconButton.dcb74fc8.js";
import "./IsotipoMozoOficial.76c4accc.js";
import "./VAvatar.0f0bd550.js";
import "./VControl.9555ec96.js";
import "./VField.152a42ff.js";
import "./VModal.9533b2d3.js";
import "./index.2bb13d6f.js";
import "./VDropdown.53f4f138.js";
import "./VIcon.f7f680f8.js";
import "./masterService.8a758d8d.js";
import "./navbarLayoutState.43b7723b.js";
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
