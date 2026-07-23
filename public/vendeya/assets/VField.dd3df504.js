import { b as defineComponent, f as openBlock, g as createElementBlock, F as Fragment, X as createBaseVNode, z as toDisplayString, y as renderSlot, Y as normalizeClass } from "./vendor.80d481a8.js";
const _hoisted_1 = { class: "field-label is-normal" };
const _hoisted_2 = { class: "label" };
const _hoisted_3 = { class: "field-body" };
const _hoisted_4 = { class: "label" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    label: { type: String, required: false, default: void 0 },
    addons: { type: Boolean, required: false },
    textaddon: { type: Boolean, required: false },
    grouped: { type: Boolean, required: false },
    multiline: { type: Boolean, required: false },
    horizontal: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["field", [
          props.addons && "has-addons",
          props.textaddon && "has-textarea-addon",
          props.grouped && "is-grouped",
          props.grouped && props.multiline && "is-grouped-multiline",
          props.horizontal && "is-horizontal"
        ]])
      }, [
        typeof props.label === "string" && props.horizontal ? (openBlock(), createElementBlock(Fragment, { key: 0 }, [
          createBaseVNode("div", _hoisted_1, [
            createBaseVNode("label", _hoisted_2, toDisplayString(props.label), 1)
          ]),
          createBaseVNode("div", _hoisted_3, [
            renderSlot(_ctx.$slots, "default")
          ])
        ], 64)) : typeof props.label === "string" ? (openBlock(), createElementBlock(Fragment, { key: 1 }, [
          createBaseVNode("label", _hoisted_4, toDisplayString(props.label), 1),
          renderSlot(_ctx.$slots, "default")
        ], 64)) : renderSlot(_ctx.$slots, "default", { key: 2 })
      ], 2);
    };
  }
});
export { _sfc_main as _ };
