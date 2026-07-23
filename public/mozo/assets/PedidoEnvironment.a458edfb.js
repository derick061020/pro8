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
import { a as useCompanySession, u as useUserSession } from "./index.2bb13d6f.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { b as defineComponent, a as computed, f as openBlock, g as createElementBlock, X as createBaseVNode, z as toDisplayString, C as createCommentVNode, F as Fragment, A as renderList, Z as unref, D as createTextVNode, G as pushScopeId, H as popScopeId, _ as createStaticVNode, r as ref, Y as normalizeClass, I as withModifiers, v as createBlock, B as withCtx, w as createVNode, aa as normalizeStyle, ad as Teleport } from "./vendor.05b8b5b5.js";
import { _ as _sfc_main$2 } from "./VButton.b4d67cab.js";
import { a as useMesaSession, d as MESA_NO_DISPONIBLE, e as MESA_DISPONIBLE, i as MESA_SHAPES_CIRCLE } from "./masterService.8a758d8d.js";
var BagDetails_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId$1 = (n) => (pushScopeId("data-v-ab0fa360"), n = n(), popScopeId(), n);
const _hoisted_1$1 = { class: "bag-details-panel" };
const _hoisted_2$1 = { class: "order-header" };
const _hoisted_3$1 = { class: "order-label" };
const _hoisted_4$1 = /* @__PURE__ */ createTextVNode(" PEDIDO ");
const _hoisted_5$1 = { class: "order-id" };
const _hoisted_6$1 = { class: "ticket-section cliente-info" };
const _hoisted_7$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("h4", { class: "section-title" }, "INFORMACI\xD3N", -1));
const _hoisted_8$1 = { class: "cliente-datos" };
const _hoisted_9$1 = {
  key: 0,
  class: "info-row"
};
const _hoisted_10$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-user"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" })
], -1));
const _hoisted_11$1 = { class: "info-text" };
const _hoisted_12$1 = {
  key: 1,
  class: "info-row"
};
const _hoisted_13$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-phone"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" })
], -1));
const _hoisted_14$1 = { class: "info-text" };
const _hoisted_15$1 = {
  key: 2,
  class: "info-row"
};
const _hoisted_16$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-map-pin"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0" })
], -1));
const _hoisted_17$1 = { class: "info-text" };
const _hoisted_18$1 = {
  key: 3,
  class: "info-row info-row--muted reference-container"
};
const _hoisted_19$1 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-info-circle" style="margin-top:-2px;" data-v-ab0fa360><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-ab0fa360></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" data-v-ab0fa360></path><path d="M12 9h.01" data-v-ab0fa360></path><path d="M11 12h1v4h1" data-v-ab0fa360></path></svg>', 1);
const _hoisted_20$1 = { class: "info-text" };
const _hoisted_21$1 = { class: "ticket-section productos-bolsa" };
const _hoisted_22$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("h4", { class: "section-title" }, "PRODUCTOS", -1));
const _hoisted_23$1 = { class: "product-list" };
const _hoisted_24$1 = { class: "product-top" };
const _hoisted_25$1 = { class: "product-name" };
const _hoisted_26$1 = { class: "product-total" };
const _hoisted_27$1 = {
  key: 0,
  class: "product-discount"
};
const _hoisted_28$1 = { class: "discount-label" };
const _hoisted_29$1 = {
  class: "discount-amount",
  style: { "color": "#e05a5a" }
};
const _hoisted_30$1 = {
  key: 1,
  class: "product-sets"
};
const _hoisted_31$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", { class: "product-sets-title" }, "Detalles", -1));
const _hoisted_32$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "product-set-bullet" }, "-", -1));
const _hoisted_33$1 = { class: "product-set-qty" };
const _hoisted_34$1 = { class: "product-set-text" };
const _hoisted_35$1 = { class: "product-meta" };
const _hoisted_36$1 = { class: "qty-badge" };
const _hoisted_37$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "qty-label" }, "Cantidad", -1));
const _hoisted_38$1 = { class: "qty-value" };
const _hoisted_39$1 = { class: "unit-price" };
const _hoisted_40$1 = {
  key: 0,
  class: "sin-productos"
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    mesaSelectDetails: { type: null, required: true }
  },
  setup(__props) {
    const props = __props;
    const companySession = useCompanySession();
    useUserSession();
    const configuration = companySession.configuration;
    const productsInBag = computed(() => {
      return props.mesaSelectDetails.products || [];
    });
    function formatStock(value) {
      const sign = value < 0 ? "-" : "";
      const num = Math.abs(value).toString().padStart(1, "0");
      return sign + num;
    }
    return (_ctx, _cache) => {
      var _a, _b, _c, _d, _e, _f;
      return openBlock(), createElementBlock("div", _hoisted_1$1, [
        createBaseVNode("div", _hoisted_2$1, [
          createBaseVNode("span", _hoisted_3$1, [
            _hoisted_4$1,
            createBaseVNode("strong", _hoisted_5$1, toDisplayString(__props.mesaSelectDetails.label), 1)
          ])
        ]),
        createBaseVNode("section", _hoisted_6$1, [
          _hoisted_7$1,
          createBaseVNode("div", _hoisted_8$1, [
            __props.mesaSelectDetails.cliente ? (openBlock(), createElementBlock("div", _hoisted_9$1, [
              _hoisted_10$1,
              createBaseVNode("span", _hoisted_11$1, toDisplayString(__props.mesaSelectDetails.cliente), 1)
            ])) : createCommentVNode("", true),
            ((_a = __props.mesaSelectDetails.delivery) == null ? void 0 : _a.phone) ? (openBlock(), createElementBlock("div", _hoisted_12$1, [
              _hoisted_13$1,
              createBaseVNode("span", _hoisted_14$1, toDisplayString((_b = __props.mesaSelectDetails.delivery) == null ? void 0 : _b.phone), 1)
            ])) : createCommentVNode("", true),
            ((_c = __props.mesaSelectDetails.delivery) == null ? void 0 : _c.address) ? (openBlock(), createElementBlock("div", _hoisted_15$1, [
              _hoisted_16$1,
              createBaseVNode("span", _hoisted_17$1, toDisplayString((_d = __props.mesaSelectDetails.delivery) == null ? void 0 : _d.address), 1)
            ])) : createCommentVNode("", true),
            ((_e = __props.mesaSelectDetails.delivery) == null ? void 0 : _e.reference) ? (openBlock(), createElementBlock("div", _hoisted_18$1, [
              _hoisted_19$1,
              createBaseVNode("span", _hoisted_20$1, toDisplayString((_f = __props.mesaSelectDetails.delivery) == null ? void 0 : _f.reference), 1)
            ])) : createCommentVNode("", true)
          ])
        ]),
        createBaseVNode("section", _hoisted_21$1, [
          _hoisted_22$1,
          createBaseVNode("ul", _hoisted_23$1, [
            (openBlock(true), createElementBlock(Fragment, null, renderList(unref(productsInBag), (product, index) => {
              var _a2;
              return openBlock(), createElementBlock("li", {
                key: index,
                class: "product-item"
              }, [
                createBaseVNode("div", _hoisted_24$1, [
                  createBaseVNode("span", _hoisted_25$1, toDisplayString(product.name), 1),
                  createBaseVNode("span", _hoisted_26$1, "S/. " + toDisplayString((product.price * product.quantity).toFixed(2)), 1)
                ]),
                ((_a2 = product.discount) != null ? _a2 : 0) > 0 ? (openBlock(), createElementBlock("div", _hoisted_27$1, [
                  createBaseVNode("span", _hoisted_28$1, " Descuento " + toDisplayString(product.discountType === "amount" ? "S/." : "%") + " " + toDisplayString(product.discount), 1),
                  createBaseVNode("span", _hoisted_29$1, " -S/. " + toDisplayString(product.discountType === "amount" ? Math.min(product.discount, product.price * product.quantity).toFixed(2) : (product.price * product.quantity * (product.discount / 100)).toFixed(2)), 1)
                ])) : createCommentVNode("", true),
                product.has_sets && unref(configuration).show_item_description_pack ? (openBlock(), createElementBlock("div", _hoisted_30$1, [
                  _hoisted_31$1,
                  (openBlock(true), createElementBlock(Fragment, null, renderList(product.items_sets, (set, setIndex) => {
                    return openBlock(), createElementBlock("div", {
                      key: setIndex,
                      class: "product-set-row"
                    }, [
                      _hoisted_32$1,
                      createBaseVNode("span", _hoisted_33$1, toDisplayString(formatStock(set.pivot.quantity)), 1),
                      createBaseVNode("span", _hoisted_34$1, toDisplayString(set.description), 1)
                    ]);
                  }), 128))
                ])) : createCommentVNode("", true),
                createBaseVNode("div", _hoisted_35$1, [
                  createBaseVNode("span", _hoisted_36$1, [
                    _hoisted_37$1,
                    createBaseVNode("span", _hoisted_38$1, toDisplayString(product.quantity), 1)
                  ]),
                  createBaseVNode("span", _hoisted_39$1, "S/. " + toDisplayString(product.price.toFixed(2)), 1)
                ])
              ]);
            }), 128)),
            unref(productsInBag).length === 0 ? (openBlock(), createElementBlock("li", _hoisted_40$1, " Sin productos ")) : createCommentVNode("", true)
          ])
        ])
      ]);
    };
  }
});
var BagDetails = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-ab0fa360"]]);
var PedidoEnvironment_vue_vue_type_style_index_0_scoped_true_lang = "";
var PedidoEnvironment_vue_vue_type_style_index_1_lang = "";
var PedidoEnvironment_vue_vue_type_style_index_2_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-a61f06b2"), n = n(), popScopeId(), n);
const _hoisted_1 = { class: "container" };
const _hoisted_2 = ["onClick"];
const _hoisted_3 = { class: "mesa-label" };
const _hoisted_4 = ["title", "disabled", "onClick"];
const _hoisted_5 = {
  key: 0,
  class: "fa-solid fa-check"
};
const _hoisted_6 = {
  key: 1,
  class: "fa-solid fa-ban"
};
const _hoisted_7 = ["onClick"];
const _hoisted_8 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-arrow-right-arrow-left" }, null, -1));
const _hoisted_9 = [
  _hoisted_8
];
const _hoisted_10 = ["onClick"];
const _hoisted_11 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-rotate-left" }, null, -1));
const _hoisted_12 = [
  _hoisted_11
];
const _hoisted_13 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "mesa-closing-spinner",
  "aria-hidden": "true"
}, null, -1));
const _hoisted_14 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "mesa-closing-text" }, "Cerrando mesa...", -1));
const _hoisted_15 = [
  _hoisted_13,
  _hoisted_14
];
const _hoisted_16 = { class: "item-left" };
const _hoisted_17 = { class: "item-details" };
const _hoisted_18 = { class: "item-customer" };
const _hoisted_19 = { class: "item-meta" };
const _hoisted_20 = {
  key: 0,
  class: "meta-info meta-phone"
};
const _hoisted_21 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-phone",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" })
], -1));
const _hoisted_22 = {
  key: 1,
  class: "meta-info meta-address"
};
const _hoisted_23 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-map-pin",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0" })
], -1));
const _hoisted_24 = {
  key: 2,
  class: "meta-info meta-time"
};
const _hoisted_25 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4" style="margin-top:-2px;" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" data-v-a61f06b2></path><path d="M12 12l3 2" data-v-a61f06b2></path><path d="M12 7v5" data-v-a61f06b2></path></svg>', 1);
const _hoisted_26 = {
  key: 3,
  class: "meta-info meta-orders"
};
const _hoisted_27 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12m0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" })
], -1));
const _hoisted_28 = {
  key: 4,
  class: "meta-info reference-info meta-reference"
};
const _hoisted_29 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-info-circle" style="margin-top:-2px;" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" data-v-a61f06b2></path><path d="M12 9h.01" data-v-a61f06b2></path><path d="M11 12h1v4h1" data-v-a61f06b2></path></svg>', 1);
const _hoisted_30 = { class: "item-right" };
const _hoisted_31 = {
  key: 0,
  class: "item-amount"
};
const _hoisted_32 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-truck-delivery",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M15 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 9l4 0" })
], -1));
const _hoisted_33 = /* @__PURE__ */ createTextVNode(" En camino ");
const _hoisted_34 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-checklist",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14 19l2 2l4 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 8h4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12h2" })
], -1));
const _hoisted_35 = /* @__PURE__ */ createTextVNode(" Despachado ");
const _hoisted_36 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-list-details",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 5h8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 9h5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 15h8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 19h5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" })
], -1));
const _hoisted_37 = /* @__PURE__ */ createTextVNode(" Detalles ");
const _hoisted_38 = { key: 0 };
const _hoisted_39 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-wallet",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 12v4h-4a2 2 0 0 1 0 -4h4" })
], -1));
const _hoisted_40 = /* @__PURE__ */ createTextVNode(" Pagar ");
const _hoisted_41 = [
  _hoisted_39,
  _hoisted_40
];
const _hoisted_42 = { key: 1 };
const _hoisted_43 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-circle-check",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12l2 2l4 -4" })
], -1));
const _hoisted_44 = /* @__PURE__ */ createTextVNode(" Pagado ");
const _hoisted_45 = [
  _hoisted_43,
  _hoisted_44
];
const _hoisted_46 = /* @__PURE__ */ createTextVNode(" Cerrar ");
const _hoisted_47 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "mesa-closing-spinner",
  "aria-hidden": "true"
}, null, -1));
const _hoisted_48 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "mesa-closing-text" }, "Cerrando mesa...", -1));
const _hoisted_49 = [
  _hoisted_47,
  _hoisted_48
];
const _hoisted_50 = { class: "item-left item-top" };
const _hoisted_51 = { class: "item-customer has-text-weight-bold" };
const _hoisted_52 = {
  key: 0,
  class: "item-amount ml-auto has-text-weight-bold"
};
const _hoisted_53 = { class: "item-left mt-2" };
const _hoisted_54 = { class: "item-details" };
const _hoisted_55 = { class: "item-meta" };
const _hoisted_56 = {
  key: 0,
  class: "meta-info meta-phone"
};
const _hoisted_57 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-phone",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" })
], -1));
const _hoisted_58 = { class: "is-flex" };
const _hoisted_59 = {
  key: 0,
  class: "meta-info meta-address mr-3"
};
const _hoisted_60 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-map-pin",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0" })
], -1));
const _hoisted_61 = {
  key: 1,
  class: "meta-info meta-orders"
};
const _hoisted_62 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12m0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" })
], -1));
const _hoisted_63 = {
  key: 1,
  class: "meta-info meta-time"
};
const _hoisted_64 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4" style="margin-top:-2px;" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" data-v-a61f06b2></path><path d="M12 12l3 2" data-v-a61f06b2></path><path d="M12 7v5" data-v-a61f06b2></path></svg>', 1);
const _hoisted_65 = {
  key: 2,
  class: "meta-info reference-info meta-reference"
};
const _hoisted_66 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-info-circle" style="margin-top:-2px;" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" data-v-a61f06b2></path><path d="M12 9h.01" data-v-a61f06b2></path><path d="M11 12h1v4h1" data-v-a61f06b2></path></svg>', 1);
const _hoisted_67 = { class: "item-right mt-1 is-flex is-justify-content-center" };
const _hoisted_68 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-truck-delivery",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M15 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 9l4 0" })
], -1));
const _hoisted_69 = /* @__PURE__ */ createTextVNode(" En camino ");
const _hoisted_70 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-checklist",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14 19l2 2l4 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 8h4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12h2" })
], -1));
const _hoisted_71 = /* @__PURE__ */ createTextVNode(" Despachado ");
const _hoisted_72 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-list-details",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 5h8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 9h5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 15h8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 19h5" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 15a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -4" })
], -1));
const _hoisted_73 = /* @__PURE__ */ createTextVNode(" Detalles ");
const _hoisted_74 = { key: 0 };
const _hoisted_75 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-wallet",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 12v4h-4a2 2 0 0 1 0 -4h4" })
], -1));
const _hoisted_76 = /* @__PURE__ */ createTextVNode(" Pagar ");
const _hoisted_77 = [
  _hoisted_75,
  _hoisted_76
];
const _hoisted_78 = { key: 1 };
const _hoisted_79 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-circle-check",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12l2 2l4 -4" })
], -1));
const _hoisted_80 = /* @__PURE__ */ createTextVNode(" Pagado ");
const _hoisted_81 = [
  _hoisted_79,
  _hoisted_80
];
const _hoisted_82 = /* @__PURE__ */ createTextVNode(" Cerrar ");
const _hoisted_83 = ["onClick"];
const _hoisted_84 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "mesa-closing-spinner",
  "aria-hidden": "true"
}, null, -1));
const _hoisted_85 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "mesa-closing-text" }, "Cerrando mesa...", -1));
const _hoisted_86 = [
  _hoisted_84,
  _hoisted_85
];
const _hoisted_87 = {
  key: 1,
  class: "mesa-info-item mesa-time-opening"
};
const _hoisted_88 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" data-v-a61f06b2></path><path d="M12 12l3 2" data-v-a61f06b2></path><path d="M12 7v5" data-v-a61f06b2></path></svg>', 1);
const _hoisted_89 = {
  key: 2,
  class: "check-icon"
};
const _hoisted_90 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-check" }, null, -1));
const _hoisted_91 = [
  _hoisted_90
];
const _hoisted_92 = {
  key: 3,
  class: "star-icon"
};
const _hoisted_93 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-star" }, null, -1));
const _hoisted_94 = [
  _hoisted_93
];
const _hoisted_95 = ["onClick"];
const _hoisted_96 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-ellipsis-vertical" }, null, -1));
const _hoisted_97 = [
  _hoisted_96
];
const _hoisted_98 = ["title"];
const _hoisted_99 = {
  key: 0,
  class: "mesa-fuera-servicio"
};
const _hoisted_100 = /* @__PURE__ */ createTextVNode(" Fuera de");
const _hoisted_101 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("br", null, null, -1));
const _hoisted_102 = /* @__PURE__ */ createTextVNode("servicio ");
const _hoisted_103 = [
  _hoisted_100,
  _hoisted_101,
  _hoisted_102
];
const _hoisted_104 = {
  key: 1,
  class: "mesas-unidas-burbujas"
};
const _hoisted_105 = {
  key: 2,
  class: "mesa-info-item is-justify-content-center",
  style: { "margin-top": "-8px" }
};
const _hoisted_106 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "15",
  height: "15",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-user",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" })
], -1));
const _hoisted_107 = {
  key: 3,
  class: "is-flex is-flex-direction-row is-align-items-center is-justify-content-space-between mt-4",
  style: { "width": "100%" }
};
const _hoisted_108 = {
  key: 0,
  class: "mesa-info-item"
};
const _hoisted_109 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "15",
  height: "15",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12zm0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" })
], -1));
const _hoisted_110 = {
  key: 1,
  class: "mesa-info-item"
};
const _hoisted_111 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-users" style="margin-top:-2px;" data-v-a61f06b2><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-a61f06b2></path><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" data-v-a61f06b2></path><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" data-v-a61f06b2></path><path d="M16 3.13a4 4 0 0 1 0 7.75" data-v-a61f06b2></path><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" data-v-a61f06b2></path></svg>', 1);
const _hoisted_112 = {
  key: 0,
  class: "mesa-info-item",
  style: { "width": "50%", "justify-content": "flex-end" }
};
const _hoisted_113 = { class: "dropdown-content" };
const _hoisted_114 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-link" }, null, -1));
const _hoisted_115 = /* @__PURE__ */ createTextVNode(" Unir mesas ");
const _hoisted_116 = [
  _hoisted_114,
  _hoisted_115
];
const _hoisted_117 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-plus" }, null, -1));
const _hoisted_118 = /* @__PURE__ */ createTextVNode(" Agregar m\xE1s mesas ");
const _hoisted_119 = [
  _hoisted_117,
  _hoisted_118
];
const _hoisted_120 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-scissors" }, null, -1));
const _hoisted_121 = /* @__PURE__ */ createTextVNode(" Separar todas las mesas ");
const _hoisted_122 = [
  _hoisted_120,
  _hoisted_121
];
const _hoisted_123 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-arrow-right" }, null, -1));
const _hoisted_124 = /* @__PURE__ */ createTextVNode(" Mover pedido ");
const _hoisted_125 = [
  _hoisted_123,
  _hoisted_124
];
const _hoisted_126 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-pen-to-square" }, null, -1));
const _hoisted_127 = /* @__PURE__ */ createTextVNode(" Editar mesa ");
const _hoisted_128 = [
  _hoisted_126,
  _hoisted_127
];
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    mesas: { type: Array, required: true, default: () => [] },
    todasLasMesas: { type: Array, required: true, default: () => [] },
    selected: { type: null, required: true },
    modeEdit: { type: Boolean, required: true, default: false },
    modoUnion: { type: Boolean, required: false, default: false },
    mesaPrincipalUnion: { type: null, required: false, default: () => null },
    mesasSeleccionadas: { type: Set, required: false, default: () => new Set() },
    mostrarConfig: { type: Object, required: false, default: () => ({
      mozo: true,
      tiempo: true,
      pedidos: true,
      monto: false,
      personas: false,
      cliente: false
    }) },
    viewList: { type: Boolean, required: false },
    isDelivery: { type: Boolean, required: false },
    isTakeaway: { type: Boolean, required: false }
  },
  emits: ["select", "edit", "separar", "separar-especifica", "iniciar-union", "agregar-mesa", "toggle-active", "mover-pedido", "shipped", "delivered", "payment", "close-table", "mover-ambiente", "restaurar-ambiente", "details"],
  setup(__props, { emit }) {
    const props = __props;
    const mesasSession = useMesaSession();
    const mesaSelected = computed(() => mesasSession.mesaSelected);
    const isClosingMesa = (mesaId) => mesasSession.isMesaClosing(mesaId);
    const requestCloseMesa = (mesa) => {
      mesasSession.startClosingMesa(mesa.id);
      emit("close-table", mesa);
    };
    console.log("MESAS EN ESTE AMBIENTE:", props.mesas.map((m) => ({
      id: m.id,
      label: m.label,
      original_environment: m.original_environment
    })));
    const menuAbierto = ref(null);
    const menuPosition = ref({ top: 0, left: 0 });
    const mesaDelMenu = ref(null);
    const toggleMenu = (mesa, event) => {
      event.stopPropagation();
      if (props.modoUnion)
        return;
      if (menuAbierto.value === mesa.id) {
        menuAbierto.value = null;
        mesaDelMenu.value = null;
        return;
      }
      const target = event.currentTarget;
      const rect = target.getBoundingClientRect();
      menuPosition.value = {
        top: rect.bottom + 4,
        left: rect.right - 200
      };
      menuAbierto.value = mesa.id;
      mesaDelMenu.value = mesa;
    };
    const cerrarMenu = () => {
      menuAbierto.value = null;
      mesaDelMenu.value = null;
    };
    const select = (item) => {
      if (menuAbierto.value !== null) {
        cerrarMenu();
        return;
      }
      if (item.is_active === false) {
        return;
      }
      emit("select", item);
    };
    const ordersServed = (products) => {
      return (products == null ? void 0 : products.some((prod) => prod.statusBar === 4 || prod.statusKitchen === 4)) || false;
    };
    const getMesasDelGrupo = (mesa) => {
      if (!mesa.group_id)
        return [];
      return props.todasLasMesas.filter((m) => m.group_id === mesa.group_id && m.id !== mesa.id);
    };
    const toggleActiveIcon = (mesa, event) => {
      event.stopPropagation();
      if (mesa.status === MESA_NO_DISPONIBLE) {
        return;
      }
      emit("toggle-active", mesa.id);
    };
    const edit = (item) => {
      cerrarMenu();
      emit("edit", item);
    };
    const moverPedido = (mesa) => {
      cerrarMenu();
      emit("mover-pedido", mesa);
    };
    const ordersPending = (products) => {
      let pending = false;
      for (let i = 0; i < products.length; i++) {
        if (products[i].quantity_pending > 0) {
          pending = false;
          break;
        } else {
          pending = true;
        }
      }
      return pending;
    };
    const getMesaLabel = (mesa) => {
      const mesasGrupo = getMesasDelGrupo(mesa);
      if (mesasGrupo.length === 0)
        return mesa.label;
      const labelsSecundarias = mesasGrupo.map((m) => m.label);
      return `${mesa.label} + ${labelsSecundarias.join(" + ")}`;
    };
    const tieneMesasUnidas = (mesaId) => {
      const mesa = props.mesas.find((m) => m.id === mesaId);
      return (mesa == null ? void 0 : mesa.group_id) && (mesa == null ? void 0 : mesa.is_main_table) && getMesasDelGrupo(mesa).length > 0;
    };
    const getMesaClasses = (item) => {
      return {
        notavailableandprecuenta: item.status == MESA_NO_DISPONIBLE && ordersPending(item.products) && item.order_status == "precuenta",
        notavailableandorderserved: item.status === MESA_NO_DISPONIBLE && ordersServed(item.products),
        notavailableandorderpending: item.status == MESA_NO_DISPONIBLE && (!ordersPending(item.products) || item.order_status == "pending"),
        notavailable: item.status == MESA_NO_DISPONIBLE,
        available: item.status == MESA_DISPONIBLE,
        inactive: item.is_active === false,
        selected: mesaSelected.value && mesaSelected.value.id == item.id
      };
    };
    const getMesaListClasses = (item) => {
      const isShipped = item.order_status === "shipped";
      const isDelivered = item.order_status === "delivered";
      const isServed = item.order_status === "served";
      const allProductsReady = ordersPending(item.products);
      const isActuallyServed = isServed && allProductsReady;
      return {
        "is-shipped": isShipped,
        "is-delivered": isDelivered,
        notavailableandorderserved: isActuallyServed,
        notavailableandorderpending: !isShipped && !isDelivered && !isActuallyServed
      };
    };
    const handleMoveAmbiente = (mesa, event) => {
      event.stopPropagation();
      if (mesa.status === MESA_NO_DISPONIBLE) {
        return;
      }
      emit("mover-ambiente", mesa);
    };
    const esMesaMovida = (mesa) => {
      return mesa.original_environment !== null;
    };
    const hayMasDeUnAmbiente = computed(() => {
      const ambientes = new Set(props.todasLasMesas.filter((mesa) => {
        var _a;
        const env = ((_a = mesa.environment) == null ? void 0 : _a.toLowerCase()) || "";
        return !env.includes("delivery") && !env.includes("para llevar") && !env.includes("parallevar");
      }).map((mesa) => mesa.environment).filter(Boolean));
      return ambientes.size > 1;
    });
    const formatTimeOpening = (value) => {
      if (!value)
        return "";
      const normalized = value.toLowerCase().replace(/\s+/g, " ").trim();
      const hoursMatch = normalized.match(/(\d+)\s*h(?:s)?/);
      const minutesMatch = normalized.match(/(\d+)\s*m(?:in)?/);
      const hours = hoursMatch == null ? void 0 : hoursMatch[1];
      const minutes = minutesMatch == null ? void 0 : minutesMatch[1];
      if (hours && minutes)
        return `${hours}h ${minutes}m`;
      if (hours)
        return `${hours}h`;
      if (minutes)
        return `${minutes}m`;
      return value;
    };
    const isMesaAbierta = (item) => {
      return item.status === MESA_NO_DISPONIBLE;
    };
    const shouldShowPedidos = (item) => {
      var _a;
      return !!((_a = props.mostrarConfig) == null ? void 0 : _a.pedidos) && isMesaAbierta(item) && item.quantityOrders > 0 && item.is_active !== false;
    };
    const shouldShowPersonas = (item) => {
      var _a;
      return !!((_a = props.mostrarConfig) == null ? void 0 : _a.personas) && isMesaAbierta(item) && !!item.personas && item.is_active !== false;
    };
    const shouldShowMonto = (item) => {
      var _a;
      return !!((_a = props.mostrarConfig) == null ? void 0 : _a.monto) && isMesaAbierta(item) && !!item.total && item.is_active !== false;
    };
    const shouldShowMetaRow = (item) => {
      return shouldShowPedidos(item) || shouldShowPersonas(item) || shouldShowMonto(item);
    };
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$2;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        __props.modeEdit ? (openBlock(true), createElementBlock(Fragment, { key: 0 }, renderList(props.mesas, (item) => {
          return openBlock(), createElementBlock("div", {
            key: item.id + "M",
            class: "mesa mesa-parent-edit"
          }, [
            createBaseVNode("div", {
              class: normalizeClass(["mesa-child edit", {
                circle: item.shape == unref(MESA_SHAPES_CIRCLE),
                "mesa-movida": esMesaMovida(item)
              }]),
              onClick: ($event) => edit(item)
            }, [
              createBaseVNode("div", _hoisted_3, toDisplayString(item.label), 1),
              createBaseVNode("button", {
                class: normalizeClass(["button-toggle-active", {
                  "is-inactive": item.is_active === false,
                  "is-disabled": item.status === unref(MESA_NO_DISPONIBLE)
                }]),
                title: item.status === unref(MESA_NO_DISPONIBLE) ? "No se puede desactivar una mesa ocupada" : item.is_active ? "Poner fuera de servicio" : "Activar mesa",
                disabled: item.status === unref(MESA_NO_DISPONIBLE),
                onClick: withModifiers(($event) => toggleActiveIcon(item, $event), ["stop"])
              }, [
                item.is_active ? (openBlock(), createElementBlock("i", _hoisted_5)) : (openBlock(), createElementBlock("i", _hoisted_6))
              ], 10, _hoisted_4),
              item.status === unref(MESA_DISPONIBLE) && !item.group_id && unref(hayMasDeUnAmbiente) ? (openBlock(), createElementBlock("button", {
                key: 0,
                class: "button-move-ambiente",
                title: "Mover a otro ambiente",
                onClick: withModifiers(($event) => handleMoveAmbiente(item, $event), ["stop"])
              }, _hoisted_9, 8, _hoisted_7)) : createCommentVNode("", true),
              item.status === unref(MESA_DISPONIBLE) && item.original_environment && !item.group_id ? (openBlock(), createElementBlock("button", {
                key: 1,
                class: "button-restore-ambiente",
                title: "Restaurar a ambiente original",
                onClick: withModifiers(($event) => _ctx.$emit("restaurar-ambiente", item.id), ["stop"])
              }, _hoisted_12, 8, _hoisted_10)) : createCommentVNode("", true)
            ], 10, _hoisted_2)
          ]);
        }), 128)) : (openBlock(true), createElementBlock(Fragment, { key: 1 }, renderList(props.mesas, (item) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n;
          return openBlock(), createElementBlock("div", {
            key: item.id,
            class: normalizeClass(["mesa-pedido", { "view-list p-1": __props.viewList }])
          }, [
            __props.viewList ? (openBlock(), createElementBlock("div", {
              key: 0,
              class: normalizeClass(["mesa-list-item p-1 is-desktop-only", [
                getMesaListClasses(item),
                { "is-closing": isClosingMesa(item.id) }
              ]])
            }, [
              isClosingMesa(item.id) ? (openBlock(), createElementBlock("div", {
                key: 0,
                class: "mesa-closing-overlay mesa-closing-overlay--list",
                onClick: _cache[0] || (_cache[0] = withModifiers(() => {
                }, ["stop"]))
              }, _hoisted_15)) : createCommentVNode("", true),
              createBaseVNode("div", _hoisted_16, [
                createBaseVNode("div", {
                  class: normalizeClass(["item-label-badge", getMesaListClasses(item)])
                }, toDisplayString(item.label), 3),
                createBaseVNode("div", _hoisted_17, [
                  createBaseVNode("div", _hoisted_18, toDisplayString(item.cliente ? item.cliente : "Sin cliente"), 1),
                  createBaseVNode("div", _hoisted_19, [
                    ((_a = item.delivery) == null ? void 0 : _a.phone) ? (openBlock(), createElementBlock("span", _hoisted_20, [
                      _hoisted_21,
                      createTextVNode(" " + toDisplayString(item.delivery.phone), 1)
                    ])) : createCommentVNode("", true),
                    ((_b = item.delivery) == null ? void 0 : _b.address) ? (openBlock(), createElementBlock("span", _hoisted_22, [
                      _hoisted_23,
                      createTextVNode(" " + toDisplayString(item.delivery.address), 1)
                    ])) : createCommentVNode("", true),
                    item.timeOpening ? (openBlock(), createElementBlock("span", _hoisted_24, [
                      _hoisted_25,
                      createTextVNode(" " + toDisplayString(formatTimeOpening(item.timeOpening)), 1)
                    ])) : createCommentVNode("", true),
                    item.quantityOrders ? (openBlock(), createElementBlock("span", _hoisted_26, [
                      _hoisted_27,
                      createTextVNode(" " + toDisplayString(item.quantityOrders), 1)
                    ])) : createCommentVNode("", true),
                    ((_c = item.delivery) == null ? void 0 : _c.reference) ? (openBlock(), createElementBlock("span", _hoisted_28, [
                      _hoisted_29,
                      createBaseVNode("span", null, toDisplayString(item.delivery.reference), 1)
                    ])) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createBaseVNode("div", _hoisted_30, [
                item.total ? (openBlock(), createElementBlock("div", _hoisted_31, " S/ " + toDisplayString(item.total.toFixed(2)), 1)) : createCommentVNode("", true),
                createBaseVNode("div", {
                  class: "item-actions",
                  onClick: _cache[1] || (_cache[1] = withModifiers(() => {
                  }, ["stop"]))
                }, [
                  props.isDelivery && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                    key: 0,
                    class: "is-link",
                    onClick: ($event) => emit("shipped", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_32,
                      _hoisted_33
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true),
                  props.isDelivery && item.order_status === "shipped" || props.isTakeaway && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                    key: 1,
                    color: "success",
                    onClick: ($event) => emit("delivered", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_34,
                      _hoisted_35
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true),
                  createVNode(_component_VButton, {
                    class: "is-info",
                    onClick: ($event) => emit("details", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_36,
                      _hoisted_37
                    ]),
                    _: 2
                  }, 1032, ["onClick"]),
                  createVNode(_component_VButton, {
                    color: item.is_paid ? "white" : "danger",
                    class: normalizeClass(item.is_paid ? "is-paid" : ""),
                    onClick: ($event) => emit("payment", item)
                  }, {
                    default: withCtx(() => [
                      !item.is_paid ? (openBlock(), createElementBlock("span", _hoisted_38, _hoisted_41)) : (openBlock(), createElementBlock("span", _hoisted_42, _hoisted_45))
                    ]),
                    _: 2
                  }, 1032, ["color", "class", "onClick"]),
                  item.is_paid && item.order_status === "delivered" ? (openBlock(), createBlock(_component_VButton, {
                    key: 2,
                    color: "success",
                    onClick: ($event) => requestCloseMesa(item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_46
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true)
                ])
              ])
            ], 2)) : createCommentVNode("", true),
            __props.viewList ? (openBlock(), createElementBlock("div", {
              key: 1,
              class: normalizeClass(["mesa-list-item p-2 is-mobile-only", [
                getMesaListClasses(item),
                { "is-closing": isClosingMesa(item.id) }
              ]])
            }, [
              isClosingMesa(item.id) ? (openBlock(), createElementBlock("div", {
                key: 0,
                class: "mesa-closing-overlay mesa-closing-overlay--list",
                onClick: _cache[2] || (_cache[2] = withModifiers(() => {
                }, ["stop"]))
              }, _hoisted_49)) : createCommentVNode("", true),
              createBaseVNode("div", _hoisted_50, [
                createBaseVNode("div", {
                  class: normalizeClass(["item-label-badge", getMesaListClasses(item)])
                }, toDisplayString(item.label), 3),
                createBaseVNode("div", _hoisted_51, toDisplayString(item.cliente ? item.cliente : "Sin cliente"), 1),
                item.total ? (openBlock(), createElementBlock("div", _hoisted_52, " S/ " + toDisplayString(item.total.toFixed(2)), 1)) : createCommentVNode("", true)
              ]),
              createBaseVNode("div", _hoisted_53, [
                createBaseVNode("div", _hoisted_54, [
                  createBaseVNode("div", _hoisted_55, [
                    ((_d = item.delivery) == null ? void 0 : _d.phone) ? (openBlock(), createElementBlock("span", _hoisted_56, [
                      _hoisted_57,
                      createTextVNode(" " + toDisplayString(item.delivery.phone), 1)
                    ])) : createCommentVNode("", true),
                    createBaseVNode("span", _hoisted_58, [
                      ((_e = item.delivery) == null ? void 0 : _e.address) ? (openBlock(), createElementBlock("span", _hoisted_59, [
                        _hoisted_60,
                        createTextVNode(" " + toDisplayString(item.delivery.address), 1)
                      ])) : createCommentVNode("", true),
                      item.quantityOrders ? (openBlock(), createElementBlock("span", _hoisted_61, [
                        _hoisted_62,
                        createTextVNode(" " + toDisplayString(item.quantityOrders), 1)
                      ])) : createCommentVNode("", true)
                    ]),
                    item.timeOpening ? (openBlock(), createElementBlock("span", _hoisted_63, [
                      _hoisted_64,
                      createTextVNode(" " + toDisplayString(formatTimeOpening(item.timeOpening)), 1)
                    ])) : createCommentVNode("", true),
                    ((_f = item.delivery) == null ? void 0 : _f.reference) ? (openBlock(), createElementBlock("span", _hoisted_65, [
                      _hoisted_66,
                      createBaseVNode("span", null, toDisplayString(item.delivery.reference), 1)
                    ])) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createBaseVNode("div", _hoisted_67, [
                createBaseVNode("div", {
                  class: "item-actions",
                  onClick: _cache[3] || (_cache[3] = withModifiers(() => {
                  }, ["stop"]))
                }, [
                  createBaseVNode("div", null, [
                    props.isDelivery && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                      key: 0,
                      class: "is-link",
                      onClick: ($event) => emit("shipped", item)
                    }, {
                      default: withCtx(() => [
                        _hoisted_68,
                        _hoisted_69
                      ]),
                      _: 2
                    }, 1032, ["onClick"])) : createCommentVNode("", true),
                    props.isDelivery && item.order_status === "shipped" || props.isTakeaway && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                      key: 1,
                      color: "success",
                      onClick: ($event) => emit("delivered", item)
                    }, {
                      default: withCtx(() => [
                        _hoisted_70,
                        _hoisted_71
                      ]),
                      _: 2
                    }, 1032, ["onClick"])) : createCommentVNode("", true)
                  ]),
                  createBaseVNode("div", null, [
                    createVNode(_component_VButton, {
                      class: "is-info",
                      onClick: ($event) => emit("details", item)
                    }, {
                      default: withCtx(() => [
                        _hoisted_72,
                        _hoisted_73
                      ]),
                      _: 2
                    }, 1032, ["onClick"]),
                    createVNode(_component_VButton, {
                      color: item.is_paid ? "white" : "danger",
                      class: normalizeClass(item.is_paid ? "is-paid" : ""),
                      onClick: ($event) => emit("payment", item)
                    }, {
                      default: withCtx(() => [
                        !item.is_paid ? (openBlock(), createElementBlock("span", _hoisted_74, _hoisted_77)) : (openBlock(), createElementBlock("span", _hoisted_78, _hoisted_81))
                      ]),
                      _: 2
                    }, 1032, ["color", "class", "onClick"])
                  ]),
                  item.is_paid && item.order_status === "delivered" ? (openBlock(), createBlock(_component_VButton, {
                    key: 0,
                    color: "danger",
                    onClick: ($event) => requestCloseMesa(item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_82
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true)
                ])
              ])
            ], 2)) : (openBlock(), createElementBlock("div", {
              key: 2,
              class: normalizeClass(["mesa-child", __spreadProps(__spreadValues({}, getMesaClasses(item)), {
                "has-union": tieneMesasUnidas(item.id),
                "mesa-movida": esMesaMovida(item),
                seleccionada: (_g = __props.mesasSeleccionadas) == null ? void 0 : _g.has(item.id),
                "principal-union": ((_h = __props.mesaPrincipalUnion) == null ? void 0 : _h.id) === item.id,
                "is-closing": isClosingMesa(item.id),
                "modo-seleccion": __props.modoUnion && !item.group_id && ((_i = __props.mesaPrincipalUnion) == null ? void 0 : _i.id) !== item.id && item.is_active !== false
              })]),
              onClick: ($event) => select(item)
            }, [
              isClosingMesa(item.id) ? (openBlock(), createElementBlock("div", {
                key: 0,
                class: "mesa-closing-overlay",
                onClick: _cache[4] || (_cache[4] = withModifiers(() => {
                }, ["stop"]))
              }, _hoisted_86)) : createCommentVNode("", true),
              ((_j = __props.mostrarConfig) == null ? void 0 : _j.tiempo) && isMesaAbierta(item) && item.timeOpening && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_87, [
                _hoisted_88,
                createTextVNode(" " + toDisplayString(formatTimeOpening(item.timeOpening)), 1)
              ])) : createCommentVNode("", true),
              __props.modoUnion && ((_k = __props.mesasSeleccionadas) == null ? void 0 : _k.has(item.id)) ? (openBlock(), createElementBlock("div", _hoisted_89, _hoisted_91)) : createCommentVNode("", true),
              __props.modoUnion && ((_l = __props.mesaPrincipalUnion) == null ? void 0 : _l.id) === item.id ? (openBlock(), createElementBlock("div", _hoisted_92, _hoisted_94)) : createCommentVNode("", true),
              !__props.modeEdit && !__props.modoUnion && item.is_active !== false ? (openBlock(), createElementBlock("button", {
                key: 4,
                class: "button-menu",
                onClick: withModifiers(($event) => toggleMenu(item, $event), ["stop"])
              }, _hoisted_97, 8, _hoisted_95)) : createCommentVNode("", true),
              createBaseVNode("div", {
                class: normalizeClass(["mesa-content", shouldShowMetaRow(item) ? "mt-auto" : ""])
              }, [
                createBaseVNode("div", {
                  class: "mesa-label",
                  title: getMesaLabel(item)
                }, toDisplayString(getMesaLabel(item)), 9, _hoisted_98),
                item.is_active === false ? (openBlock(), createElementBlock("div", _hoisted_99, _hoisted_103)) : createCommentVNode("", true),
                tieneMesasUnidas(item.id) && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_104, [
                  (openBlock(true), createElementBlock(Fragment, null, renderList(getMesasDelGrupo(item), (mesaUnida) => {
                    return openBlock(), createElementBlock("span", {
                      key: mesaUnida.id,
                      class: "burbuja-mesa"
                    }, toDisplayString(mesaUnida.label), 1);
                  }), 128))
                ])) : createCommentVNode("", true),
                ((_m = __props.mostrarConfig) == null ? void 0 : _m.mozo) && isMesaAbierta(item) && item.waiter && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_105, [
                  _hoisted_106,
                  createTextVNode(" " + toDisplayString(item.waiter), 1)
                ])) : createCommentVNode("", true),
                shouldShowMetaRow(item) ? (openBlock(), createElementBlock("div", _hoisted_107, [
                  createBaseVNode("div", {
                    class: "is-flex is-justify-content-space-between",
                    style: normalizeStyle({
                      width: shouldShowMonto(item) ? "50%" : "100%"
                    })
                  }, [
                    shouldShowPedidos(item) ? (openBlock(), createElementBlock("div", _hoisted_108, [
                      _hoisted_109,
                      createTextVNode(" " + toDisplayString(item.quantityOrders), 1)
                    ])) : createCommentVNode("", true),
                    shouldShowPersonas(item) ? (openBlock(), createElementBlock("div", _hoisted_110, [
                      _hoisted_111,
                      createTextVNode(" " + toDisplayString(item.personas), 1)
                    ])) : createCommentVNode("", true)
                  ], 4),
                  shouldShowMonto(item) ? (openBlock(), createElementBlock("div", _hoisted_112, " S/ " + toDisplayString((_n = item.total) == null ? void 0 : _n.toFixed(2)), 1)) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ], 2)
            ], 10, _hoisted_83))
          ], 2);
        }), 128)),
        (openBlock(), createBlock(Teleport, { to: "body" }, [
          menuAbierto.value !== null ? (openBlock(), createElementBlock("div", {
            key: 0,
            class: "menu-overlay",
            onClick: cerrarMenu
          })) : createCommentVNode("", true)
        ])),
        (openBlock(), createBlock(Teleport, { to: "body" }, [
          menuAbierto.value !== null && mesaDelMenu.value ? (openBlock(), createElementBlock("div", {
            key: 0,
            class: "dropdown-menu-fixed",
            style: normalizeStyle({
              top: menuPosition.value.top + "px",
              left: menuPosition.value.left + "px"
            }),
            onClick: _cache[10] || (_cache[10] = withModifiers(() => {
            }, ["stop"]))
          }, [
            createBaseVNode("div", _hoisted_113, [
              !mesaDelMenu.value.group_id ? (openBlock(), createElementBlock("a", {
                key: 0,
                class: "dropdown-item",
                onClick: _cache[5] || (_cache[5] = ($event) => (emit("iniciar-union", mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_116)) : createCommentVNode("", true),
              tieneMesasUnidas(mesaDelMenu.value.id) ? (openBlock(), createElementBlock("a", {
                key: 1,
                class: "dropdown-item",
                onClick: _cache[6] || (_cache[6] = ($event) => (emit("agregar-mesa", mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_119)) : createCommentVNode("", true),
              tieneMesasUnidas(mesaDelMenu.value.id) ? (openBlock(), createElementBlock("a", {
                key: 2,
                class: "dropdown-item",
                onClick: _cache[7] || (_cache[7] = ($event) => (emit("separar", mesaDelMenu.value.id), cerrarMenu()))
              }, _hoisted_122)) : createCommentVNode("", true),
              mesaDelMenu.value.status === unref(MESA_NO_DISPONIBLE) ? (openBlock(), createElementBlock("a", {
                key: 3,
                class: "dropdown-item",
                onClick: _cache[8] || (_cache[8] = ($event) => (moverPedido(mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_125)) : createCommentVNode("", true),
              createBaseVNode("a", {
                class: "dropdown-item",
                onClick: _cache[9] || (_cache[9] = ($event) => (edit(mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_128)
            ])
          ], 4)) : createCommentVNode("", true)
        ]))
      ]);
    };
  }
});
var PedidoEnvironment = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-a61f06b2"]]);
export { BagDetails as B, PedidoEnvironment as P };
