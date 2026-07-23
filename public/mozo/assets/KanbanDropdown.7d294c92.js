import { _ as __unplugin_components_0 } from "./VDropdown.53f4f138.js";
import { b as defineComponent, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, D as createTextVNode } from "./vendor.05b8b5b5.js";
const _hoisted_1 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-compact-left",
  style: { "flex-shrink": "0" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 20l-3 -8l3 -8" })
], -1);
const _hoisted_2 = /* @__PURE__ */ createTextVNode(" Vista compacta ");
const _hoisted_3 = [
  _hoisted_1,
  _hoisted_2
];
const _sfc_main = /* @__PURE__ */ defineComponent({
  emits: ["rename", "collapse"],
  setup(__props, { emit }) {
    return (_ctx, _cache) => {
      const _component_VDropdown = __unplugin_components_0;
      return openBlock(), createBlock(_component_VDropdown, {
        icon: "feather:more-vertical",
        right: "",
        class: "is-hidden-mobile"
      }, {
        content: withCtx(() => [
          createBaseVNode("a", {
            class: "dropdown-item kanban-collapse kill-drop",
            onClick: _cache[0] || (_cache[0] = ($event) => emit("collapse"))
          }, _hoisted_3)
        ]),
        _: 1
      });
    };
  }
});
export { _sfc_main as _ };
