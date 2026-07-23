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
import { _ as _sfc_main$b } from "./LockedScreen.14a12adf.js";
import { _ as _sfc_main$4, M as MoveAmbienteModal, a as _sfc_main$6, b as _sfc_main$8, c as _sfc_main$a } from "./MoveAmbienteModal.7351a753.js";
import { _ as _sfc_main$5, a as _sfc_main$7 } from "./ProductModifiersDialog.637fd331.js";
import { _ as _sfc_main$3, a as __unplugin_components_4 } from "./VButton.b4d67cab.js";
import { b as defineComponent, a as computed, r as ref, f as openBlock, g as createElementBlock, F as Fragment, A as renderList, X as createBaseVNode, z as toDisplayString, Y as normalizeClass, Z as unref, I as withModifiers, C as createCommentVNode, D as createTextVNode, v as createBlock, B as withCtx, w as createVNode, aa as normalizeStyle, ad as Teleport, G as pushScopeId, H as popScopeId, _ as createStaticVNode, N as Notyf, t as reactive, L as watch, o as onMounted, P as onUnmounted, a6 as withDirectives, ar as vShow, a7 as vModelText, al as vModelSelect, S as isRef, e as useHead } from "./vendor.05b8b5b5.js";
import { a as useMesaSession, d as MESA_NO_DISPONIBLE, e as MESA_DISPONIBLE, i as MESA_SHAPES_CIRCLE, D as DEFAULT_ENVIRONMENT, c as MesaService, u as use } from "./masterService.8a758d8d.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { _ as __unplugin_components_5 } from "./VPlaceloadWrap.d37210d3.js";
import { _ as __unplugin_components_1$1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$9 } from "./VField.152a42ff.js";
import { a as useCompanySession, u as useUserSession } from "./index.2bb13d6f.js";
import { p as pageTitle } from "./sidebarLayoutState.6ddf5d4e.js";
import "./VModal.9533b2d3.js";
import "./VIcon.f7f680f8.js";
import "./CategoriesNavBar.5044304a.js";
import "./VAvatar.0f0bd550.js";
import "./restaurantService.f8e320da.js";
import "./VIconButton.dcb74fc8.js";
import "./VLoader.a784d703.js";
var MesaEnvironment_vue_vue_type_style_index_0_scoped_true_lang = "";
var MesaEnvironment_vue_vue_type_style_index_1_lang = "";
var MesaEnvironment_vue_vue_type_style_index_2_scoped_true_lang = "";
const _withScopeId$1 = (n) => (pushScopeId("data-v-99325592"), n = n(), popScopeId(), n);
const _hoisted_1$2 = { class: "container" };
const _hoisted_2$1 = ["onClick"];
const _hoisted_3$1 = { class: "mesa-label" };
const _hoisted_4$1 = ["title", "disabled", "onClick"];
const _hoisted_5$1 = {
  key: 0,
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-check"
};
const _hoisted_6$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", {
  stroke: "none",
  d: "M0 0h24v24H0z",
  fill: "none"
}, null, -1));
const _hoisted_7$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", { d: "M5 12l5 5l10 -10" }, null, -1));
const _hoisted_8$1 = [
  _hoisted_6$1,
  _hoisted_7$1
];
const _hoisted_9$1 = {
  key: 1,
  xmlns: "http://www.w3.org/2000/svg",
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-ban"
};
const _hoisted_10$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", {
  stroke: "none",
  d: "M0 0h24v24H0z",
  fill: "none"
}, null, -1));
const _hoisted_11$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }, null, -1));
const _hoisted_12$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", { d: "M5.7 5.7l12.6 12.6" }, null, -1));
const _hoisted_13$1 = [
  _hoisted_10$1,
  _hoisted_11$1,
  _hoisted_12$1
];
const _hoisted_14$1 = ["onClick"];
const _hoisted_15$1 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-arrows-left-right" data-v-99325592><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-99325592></path><path d="M21 17l-18 0" data-v-99325592></path><path d="M6 10l-3 -3l3 -3" data-v-99325592></path><path d="M3 7l18 0" data-v-99325592></path><path d="M18 20l3 -3l-3 -3" data-v-99325592></path></svg>', 1);
const _hoisted_16$1 = [
  _hoisted_15$1
];
const _hoisted_17$1 = ["onClick"];
const _hoisted_18$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-rotate-left" }, null, -1));
const _hoisted_19$1 = [
  _hoisted_18$1
];
const _hoisted_20$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "mesa-closing-spinner",
  "aria-hidden": "true"
}, null, -1));
const _hoisted_21$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", { class: "mesa-closing-text" }, "Cerrando mesa...", -1));
const _hoisted_22$1 = [
  _hoisted_20$1,
  _hoisted_21$1
];
const _hoisted_23$1 = { class: "item-left" };
const _hoisted_24$1 = { class: "item-details" };
const _hoisted_25$1 = { class: "item-customer" };
const _hoisted_26$1 = { class: "item-meta" };
const _hoisted_27$1 = {
  key: 0,
  class: "meta-info"
};
const _hoisted_28$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-phone" }, null, -1));
const _hoisted_29$1 = {
  key: 1,
  class: "meta-info"
};
const _hoisted_30 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-location-dot" }, null, -1));
const _hoisted_31$1 = {
  key: 2,
  class: "meta-info"
};
const _hoisted_32$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-note-sticky" }, null, -1));
const _hoisted_33$1 = {
  key: 3,
  class: "meta-info"
};
const _hoisted_34$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-clock" }, null, -1));
const _hoisted_35$1 = {
  key: 4,
  class: "meta-info"
};
const _hoisted_36$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-receipt" }, null, -1));
const _hoisted_37$1 = { class: "item-right" };
const _hoisted_38$1 = {
  key: 0,
  class: "item-amount"
};
const _hoisted_39$1 = /* @__PURE__ */ createTextVNode(" En camino ");
const _hoisted_40$1 = /* @__PURE__ */ createTextVNode(" Despachado ");
const _hoisted_41$1 = /* @__PURE__ */ createTextVNode(" Ver Detalles ");
const _hoisted_42$1 = { key: 0 };
const _hoisted_43$1 = { key: 1 };
const _hoisted_44$1 = /* @__PURE__ */ createTextVNode(" Cerrar ");
const _hoisted_45$1 = ["onClick"];
const _hoisted_46$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "mesa-closing-spinner",
  "aria-hidden": "true"
}, null, -1));
const _hoisted_47$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", { class: "mesa-closing-text" }, "Cerrando mesa...", -1));
const _hoisted_48$1 = [
  _hoisted_46$1,
  _hoisted_47$1
];
const _hoisted_49 = {
  key: 1,
  class: "mesa-info-item mesa-time-opening"
};
const _hoisted_50 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4" data-v-99325592><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-99325592></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" data-v-99325592></path><path d="M12 12l3 2" data-v-99325592></path><path d="M12 7v5" data-v-99325592></path></svg>', 1);
const _hoisted_51 = {
  key: 2,
  class: "check-icon"
};
const _hoisted_52$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-check" }, null, -1));
const _hoisted_53$1 = [
  _hoisted_52$1
];
const _hoisted_54$1 = {
  key: 3,
  class: "star-icon"
};
const _hoisted_55$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-star" }, null, -1));
const _hoisted_56$1 = [
  _hoisted_55$1
];
const _hoisted_57$1 = ["onClick"];
const _hoisted_58$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-ellipsis-vertical" }, null, -1));
const _hoisted_59$1 = [
  _hoisted_58$1
];
const _hoisted_60$1 = ["title"];
const _hoisted_61$1 = {
  key: 0,
  class: "mesa-fuera-servicio"
};
const _hoisted_62$1 = /* @__PURE__ */ createTextVNode(" Fuera de");
const _hoisted_63$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("br", null, null, -1));
const _hoisted_64$1 = /* @__PURE__ */ createTextVNode("servicio ");
const _hoisted_65$1 = [
  _hoisted_62$1,
  _hoisted_63$1,
  _hoisted_64$1
];
const _hoisted_66$1 = {
  key: 1,
  class: "mesas-unidas-burbujas"
};
const _hoisted_67$1 = ["onClick"];
const _hoisted_68$1 = {
  key: 2,
  class: "mesa-info-item is-justify-content-center",
  style: { "margin-top": "-8px" }
};
const _hoisted_69$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
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
const _hoisted_70$1 = { class: "mesa-info-text" };
const _hoisted_71$1 = {
  key: 3,
  class: "is-flex is-flex-direction-row is-align-items-center is-justify-content-space-between bottom-meta-row"
};
const _hoisted_72$1 = {
  key: 0,
  class: "mesa-info-item"
};
const _hoisted_73$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
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
const _hoisted_74$1 = {
  key: 1,
  class: "mesa-info-item"
};
const _hoisted_75$1 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-users" style="margin-top:-2px;" data-v-99325592><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-99325592></path><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" data-v-99325592></path><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" data-v-99325592></path><path d="M16 3.13a4 4 0 0 1 0 7.75" data-v-99325592></path><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" data-v-99325592></path></svg>', 1);
const _hoisted_76$1 = {
  key: 0,
  class: "mesa-info-item",
  style: { "width": "50%", "justify-content": "flex-end" }
};
const _hoisted_77$1 = { class: "dropdown-content" };
const _hoisted_78$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-circles-relation"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9.183 6.117a6 6 0 1 0 4.511 3.986" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14.813 17.883a6 6 0 1 0 -4.496 -3.954" })
], -1));
const _hoisted_79$1 = /* @__PURE__ */ createTextVNode(" Unir mesas ");
const _hoisted_80$1 = [
  _hoisted_78$1,
  _hoisted_79$1
];
const _hoisted_81$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-plus"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M12 5l0 14" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 12l14 0" })
], -1));
const _hoisted_82$1 = /* @__PURE__ */ createTextVNode(" Agregar m\xE1s mesas ");
const _hoisted_83$1 = [
  _hoisted_81$1,
  _hoisted_82$1
];
const _hoisted_84$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-scissors"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 7a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 17a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8.6 8.6l10.4 10.4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8.6 15.4l10.4 -10.4" })
], -1));
const _hoisted_85$1 = /* @__PURE__ */ createTextVNode(" Separar todas las mesas ");
const _hoisted_86$1 = [
  _hoisted_84$1,
  _hoisted_85$1
];
const _hoisted_87$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 12l14 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M15 16l4 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M15 8l4 4" })
], -1));
const _hoisted_88$1 = /* @__PURE__ */ createTextVNode(" Mover pedido ");
const _hoisted_89$1 = [
  _hoisted_87$1,
  _hoisted_88$1
];
const _hoisted_90$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-edit"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M16 5l3 3" })
], -1));
const _hoisted_91$1 = /* @__PURE__ */ createTextVNode(" Editar mesa ");
const _hoisted_92$1 = [
  _hoisted_90$1,
  _hoisted_91$1
];
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
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
  emits: ["select", "select-details", "edit", "separar", "separar-especifica", "iniciar-union", "agregar-mesa", "toggle-active", "mover-pedido", "shipped", "delivered", "payment", "close-table", "mover-ambiente", "restaurar-ambiente", "details"],
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
    const selectDetails = (item, event) => {
      event.stopPropagation();
      if (item.is_active === false) {
        return;
      }
      emit("select-details", item);
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
      const _component_VButton = _sfc_main$3;
      return openBlock(), createElementBlock("div", _hoisted_1$2, [
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
              createBaseVNode("div", _hoisted_3$1, toDisplayString(item.label), 1),
              createBaseVNode("button", {
                class: normalizeClass(["button-toggle-active p-0", {
                  "is-inactive": item.is_active === false,
                  "is-disabled": item.status === unref(MESA_NO_DISPONIBLE)
                }]),
                title: item.status === unref(MESA_NO_DISPONIBLE) ? "No se puede desactivar una mesa ocupada" : item.is_active ? "Poner fuera de servicio" : "Activar mesa",
                disabled: item.status === unref(MESA_NO_DISPONIBLE),
                onClick: withModifiers(($event) => toggleActiveIcon(item, $event), ["stop"])
              }, [
                item.is_active ? (openBlock(), createElementBlock("svg", _hoisted_5$1, _hoisted_8$1)) : (openBlock(), createElementBlock("svg", _hoisted_9$1, _hoisted_13$1))
              ], 10, _hoisted_4$1),
              item.status === unref(MESA_DISPONIBLE) && !item.group_id && unref(hayMasDeUnAmbiente) ? (openBlock(), createElementBlock("button", {
                key: 0,
                class: "button-move-ambiente",
                title: "Mover a otro ambiente",
                onClick: withModifiers(($event) => handleMoveAmbiente(item, $event), ["stop"])
              }, _hoisted_16$1, 8, _hoisted_14$1)) : createCommentVNode("", true),
              item.status === unref(MESA_DISPONIBLE) && item.original_environment && !item.group_id ? (openBlock(), createElementBlock("button", {
                key: 1,
                class: "button-restore-ambiente",
                title: "Restaurar a ambiente original",
                onClick: withModifiers(($event) => _ctx.$emit("restaurar-ambiente", item.id), ["stop"])
              }, _hoisted_19$1, 8, _hoisted_17$1)) : createCommentVNode("", true)
            ], 10, _hoisted_2$1)
          ]);
        }), 128)) : (openBlock(true), createElementBlock(Fragment, { key: 1 }, renderList(props.mesas, (item) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k;
          return openBlock(), createElementBlock("div", {
            key: item.id,
            class: normalizeClass(["mesa", { "view-list p-1": __props.viewList }])
          }, [
            __props.viewList ? (openBlock(), createElementBlock("div", {
              key: 0,
              class: normalizeClass(["mesa-list-item p-1", [getMesaListClasses(item), { "is-closing": isClosingMesa(item.id) }]])
            }, [
              isClosingMesa(item.id) ? (openBlock(), createElementBlock("div", {
                key: 0,
                class: "mesa-closing-overlay mesa-closing-overlay--list",
                onClick: _cache[0] || (_cache[0] = withModifiers(() => {
                }, ["stop"]))
              }, _hoisted_22$1)) : createCommentVNode("", true),
              createBaseVNode("div", _hoisted_23$1, [
                createBaseVNode("div", {
                  class: normalizeClass(["item-label-badge", getMesaListClasses(item)])
                }, toDisplayString(item.label), 3),
                createBaseVNode("div", _hoisted_24$1, [
                  createBaseVNode("div", _hoisted_25$1, toDisplayString(item.cliente ? item.cliente : "Sin cliente"), 1),
                  createBaseVNode("div", _hoisted_26$1, [
                    ((_a = item.delivery) == null ? void 0 : _a.phone) ? (openBlock(), createElementBlock("span", _hoisted_27$1, [
                      _hoisted_28$1,
                      createTextVNode(" " + toDisplayString(item.delivery.phone), 1)
                    ])) : createCommentVNode("", true),
                    ((_b = item.delivery) == null ? void 0 : _b.address) ? (openBlock(), createElementBlock("span", _hoisted_29$1, [
                      _hoisted_30,
                      createTextVNode(" " + toDisplayString(item.delivery.address), 1)
                    ])) : createCommentVNode("", true),
                    ((_c = item.delivery) == null ? void 0 : _c.reference) ? (openBlock(), createElementBlock("span", _hoisted_31$1, [
                      _hoisted_32$1,
                      createTextVNode(" " + toDisplayString(item.delivery.reference), 1)
                    ])) : createCommentVNode("", true),
                    item.timeOpening ? (openBlock(), createElementBlock("span", _hoisted_33$1, [
                      _hoisted_34$1,
                      createTextVNode(" " + toDisplayString(formatTimeOpening(item.timeOpening)), 1)
                    ])) : createCommentVNode("", true),
                    item.quantityOrders ? (openBlock(), createElementBlock("span", _hoisted_35$1, [
                      _hoisted_36$1,
                      createTextVNode(" " + toDisplayString(item.quantityOrders), 1)
                    ])) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createBaseVNode("div", _hoisted_37$1, [
                item.total ? (openBlock(), createElementBlock("div", _hoisted_38$1, " S/ " + toDisplayString(item.total.toFixed(2)), 1)) : createCommentVNode("", true),
                createBaseVNode("div", {
                  class: "item-actions ml-3",
                  onClick: _cache[1] || (_cache[1] = withModifiers(() => {
                  }, ["stop"]))
                }, [
                  props.isDelivery && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                    key: 0,
                    class: "is-link",
                    onClick: ($event) => emit("shipped", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_39$1
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true),
                  props.isDelivery && item.order_status === "shipped" || props.isTakeaway && item.order_status === "served" ? (openBlock(), createBlock(_component_VButton, {
                    key: 1,
                    color: "success",
                    onClick: ($event) => emit("delivered", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_40$1
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true),
                  createVNode(_component_VButton, {
                    class: "is-info",
                    onClick: ($event) => emit("details", item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_41$1
                    ]),
                    _: 2
                  }, 1032, ["onClick"]),
                  createVNode(_component_VButton, {
                    color: item.is_paid ? "white" : "danger",
                    disabled: item.is_paid,
                    onClick: ($event) => emit("payment", item)
                  }, {
                    default: withCtx(() => [
                      !item.is_paid ? (openBlock(), createElementBlock("span", _hoisted_42$1, "Pagar")) : (openBlock(), createElementBlock("span", _hoisted_43$1, "Pagado"))
                    ]),
                    _: 2
                  }, 1032, ["color", "disabled", "onClick"]),
                  item.is_paid && item.order_status === "delivered" ? (openBlock(), createBlock(_component_VButton, {
                    key: 2,
                    color: "success",
                    onClick: ($event) => requestCloseMesa(item)
                  }, {
                    default: withCtx(() => [
                      _hoisted_44$1
                    ]),
                    _: 2
                  }, 1032, ["onClick"])) : createCommentVNode("", true)
                ])
              ])
            ], 2)) : (openBlock(), createElementBlock("div", {
              key: 1,
              class: normalizeClass(["mesa-child", __spreadProps(__spreadValues({}, getMesaClasses(item)), {
                "has-union": tieneMesasUnidas(item.id),
                "mesa-movida": esMesaMovida(item),
                seleccionada: (_d = __props.mesasSeleccionadas) == null ? void 0 : _d.has(item.id),
                "principal-union": ((_e = __props.mesaPrincipalUnion) == null ? void 0 : _e.id) === item.id,
                "is-closing": isClosingMesa(item.id),
                "modo-seleccion": __props.modoUnion && !item.group_id && ((_f = __props.mesaPrincipalUnion) == null ? void 0 : _f.id) !== item.id && item.is_active !== false,
                circle: item.shape == unref(MESA_SHAPES_CIRCLE)
              })]),
              onClick: ($event) => select(item)
            }, [
              isClosingMesa(item.id) ? (openBlock(), createElementBlock("div", {
                key: 0,
                class: "mesa-closing-overlay",
                onClick: _cache[2] || (_cache[2] = withModifiers(() => {
                }, ["stop"]))
              }, _hoisted_48$1)) : createCommentVNode("", true),
              ((_g = __props.mostrarConfig) == null ? void 0 : _g.tiempo) && isMesaAbierta(item) && item.timeOpening && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_49, [
                _hoisted_50,
                createTextVNode(" " + toDisplayString(formatTimeOpening(item.timeOpening)), 1)
              ])) : createCommentVNode("", true),
              __props.modoUnion && ((_h = __props.mesasSeleccionadas) == null ? void 0 : _h.has(item.id)) ? (openBlock(), createElementBlock("div", _hoisted_51, _hoisted_53$1)) : createCommentVNode("", true),
              __props.modoUnion && ((_i = __props.mesaPrincipalUnion) == null ? void 0 : _i.id) === item.id ? (openBlock(), createElementBlock("div", _hoisted_54$1, _hoisted_56$1)) : createCommentVNode("", true),
              !__props.modeEdit && !__props.modoUnion && item.is_active !== false ? (openBlock(), createElementBlock("button", {
                key: 4,
                class: "button-menu",
                onClick: withModifiers(($event) => toggleMenu(item, $event), ["stop"])
              }, _hoisted_59$1, 8, _hoisted_57$1)) : createCommentVNode("", true),
              createBaseVNode("div", {
                class: normalizeClass(["mesa-content", shouldShowMetaRow(item) ? "mt-auto" : ""])
              }, [
                createBaseVNode("div", {
                  class: "mesa-label",
                  title: getMesaLabel(item)
                }, toDisplayString(getMesaLabel(item)), 9, _hoisted_60$1),
                item.is_active === false ? (openBlock(), createElementBlock("div", _hoisted_61$1, _hoisted_65$1)) : createCommentVNode("", true),
                tieneMesasUnidas(item.id) && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_66$1, [
                  (openBlock(true), createElementBlock(Fragment, null, renderList(getMesasDelGrupo(item), (mesaUnida) => {
                    return openBlock(), createElementBlock("span", {
                      key: mesaUnida.id,
                      class: "burbuja-mesa",
                      onClick: ($event) => selectDetails(mesaUnida, $event)
                    }, toDisplayString(mesaUnida.label), 9, _hoisted_67$1);
                  }), 128))
                ])) : createCommentVNode("", true),
                ((_j = __props.mostrarConfig) == null ? void 0 : _j.mozo) && isMesaAbierta(item) && item.waiter && item.is_active !== false ? (openBlock(), createElementBlock("div", _hoisted_68$1, [
                  _hoisted_69$1,
                  createBaseVNode("span", _hoisted_70$1, toDisplayString(item.waiter), 1)
                ])) : createCommentVNode("", true),
                shouldShowMetaRow(item) ? (openBlock(), createElementBlock("div", _hoisted_71$1, [
                  createBaseVNode("div", {
                    class: "is-flex is-justify-content-space-between",
                    style: normalizeStyle({
                      width: shouldShowMonto(item) ? "50%" : "100%"
                    })
                  }, [
                    shouldShowPedidos(item) ? (openBlock(), createElementBlock("div", _hoisted_72$1, [
                      _hoisted_73$1,
                      createTextVNode(" " + toDisplayString(item.quantityOrders), 1)
                    ])) : createCommentVNode("", true),
                    shouldShowPersonas(item) ? (openBlock(), createElementBlock("div", _hoisted_74$1, [
                      _hoisted_75$1,
                      createTextVNode(" " + toDisplayString(item.personas), 1)
                    ])) : createCommentVNode("", true)
                  ], 4),
                  shouldShowMonto(item) ? (openBlock(), createElementBlock("div", _hoisted_76$1, " S/ " + toDisplayString((_k = item.total) == null ? void 0 : _k.toFixed(2)), 1)) : createCommentVNode("", true)
                ])) : createCommentVNode("", true)
              ], 2)
            ], 10, _hoisted_45$1))
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
            onClick: _cache[8] || (_cache[8] = withModifiers(() => {
            }, ["stop"]))
          }, [
            createBaseVNode("div", _hoisted_77$1, [
              !mesaDelMenu.value.group_id ? (openBlock(), createElementBlock("a", {
                key: 0,
                class: "dropdown-item",
                onClick: _cache[3] || (_cache[3] = ($event) => (emit("iniciar-union", mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_80$1)) : createCommentVNode("", true),
              tieneMesasUnidas(mesaDelMenu.value.id) ? (openBlock(), createElementBlock("a", {
                key: 1,
                class: "dropdown-item",
                onClick: _cache[4] || (_cache[4] = ($event) => (emit("agregar-mesa", mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_83$1)) : createCommentVNode("", true),
              tieneMesasUnidas(mesaDelMenu.value.id) ? (openBlock(), createElementBlock("a", {
                key: 2,
                class: "dropdown-item",
                onClick: _cache[5] || (_cache[5] = ($event) => (emit("separar", mesaDelMenu.value.id), cerrarMenu()))
              }, _hoisted_86$1)) : createCommentVNode("", true),
              mesaDelMenu.value.status === unref(MESA_NO_DISPONIBLE) ? (openBlock(), createElementBlock("a", {
                key: 3,
                class: "dropdown-item",
                onClick: _cache[6] || (_cache[6] = ($event) => (moverPedido(mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_89$1)) : createCommentVNode("", true),
              createBaseVNode("a", {
                class: "dropdown-item",
                onClick: _cache[7] || (_cache[7] = ($event) => (edit(mesaDelMenu.value), cerrarMenu()))
              }, _hoisted_92$1)
            ])
          ], 4)) : createCommentVNode("", true)
        ]))
      ]);
    };
  }
});
var __unplugin_components_3 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-99325592"]]);
var MesaRestaurant_vue_vue_type_style_index_0_lang = "";
var MesaRestaurant_vue_vue_type_style_index_1_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-bea9c8d4"), n = n(), popScopeId(), n);
const _hoisted_1$1 = {
  key: 0,
  class: "ecommerce-dashboard ecommerce-dashboard-v1"
};
const _hoisted_2 = { class: "columns is-multiline" };
const _hoisted_3 = { class: "column is-9" };
const _hoisted_4 = { class: "stat-widget line-stats-widget is-straight" };
const _hoisted_5 = { class: "mesa-top-bar mb-3" };
const _hoisted_6 = {
  key: 0,
  class: "filtros-mesas"
};
const _hoisted_7 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "filtros-label" }, "Mostrar Mesas:", -1));
const _hoisted_8 = {
  key: 0,
  class: "fa-solid fa-check"
};
const _hoisted_9 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "checkbox-text" }, "Disponibles", -1));
const _hoisted_10 = {
  key: 0,
  class: "fa-solid fa-check"
};
const _hoisted_11 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "checkbox-text" }, "Ocupadas", -1));
const _hoisted_12 = {
  key: 0,
  class: "fa-solid fa-check"
};
const _hoisted_13 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "checkbox-text" }, "Fuera de servicio", -1));
const _hoisted_14 = {
  key: 1,
  class: "visualizacion-mesas"
};
const _hoisted_15 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "filtros-label" }, "Mostrar en mesas:", -1));
const _hoisted_16 = ["onClick"];
const _hoisted_17 = {
  key: 0,
  class: "fa-solid fa-check"
};
const _hoisted_18 = { class: "checkbox-text" };
const _hoisted_19 = ["innerHTML"];
const _hoisted_20 = { class: "mesa-buttons" };
const _hoisted_21 = /* @__PURE__ */ createTextVNode(" Editar mesas ");
const _hoisted_22 = /* @__PURE__ */ createTextVNode(" Finalizar ");
const _hoisted_23 = {
  key: 1,
  class: "mesa-bottom-bar mt-3"
};
const _hoisted_24 = { class: "mesa-legend" };
const _hoisted_25 = { class: "legend-item" };
const _hoisted_26 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator available" }, null, -1));
const _hoisted_27 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Mesa disponible", -1));
const _hoisted_28 = [
  _hoisted_26,
  _hoisted_27
];
const _hoisted_29 = /* @__PURE__ */ createStaticVNode('<div class="legend-item" data-v-bea9c8d4><span class="legend-indicator notavailableandorderpending" data-v-bea9c8d4></span><span class="legend-text" data-v-bea9c8d4>Ordenes pendientes</span></div><div class="legend-item" data-v-bea9c8d4><span class="legend-indicator notavailableandorderserved" data-v-bea9c8d4></span><span class="legend-text" data-v-bea9c8d4>Ordenes servidas</span></div>', 2);
const _hoisted_31 = { class: "legend-item" };
const _hoisted_32 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator notavailableandprecuenta" }, null, -1));
const _hoisted_33 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Precuenta generada", -1));
const _hoisted_34 = [
  _hoisted_32,
  _hoisted_33
];
const _hoisted_35 = { class: "legend-item" };
const _hoisted_36 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator moved" }, null, -1));
const _hoisted_37 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Mesa reubicada", -1));
const _hoisted_38 = [
  _hoisted_36,
  _hoisted_37
];
const _hoisted_39 = { class: "legend-item" };
const _hoisted_40 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator is-shipped" }, null, -1));
const _hoisted_41 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "En camino", -1));
const _hoisted_42 = [
  _hoisted_40,
  _hoisted_41
];
const _hoisted_43 = { class: "legend-item" };
const _hoisted_44 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator is-delivered" }, null, -1));
const _hoisted_45 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Despachado", -1));
const _hoisted_46 = [
  _hoisted_44,
  _hoisted_45
];
const _hoisted_47 = {
  key: 2,
  class: "mesa-legend-edit"
};
const _hoisted_48 = /* @__PURE__ */ createStaticVNode('<div class="legend-item" data-v-bea9c8d4><span class="legend-icon-edit disponible" data-v-bea9c8d4><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-check" data-v-bea9c8d4><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-bea9c8d4></path><path d="M5 12l5 5l10 -10" data-v-bea9c8d4></path></svg></span><span class="legend-text" data-v-bea9c8d4>Disponible</span></div><div class="legend-item" data-v-bea9c8d4><span class="legend-icon-edit ocupada" data-v-bea9c8d4><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-check" data-v-bea9c8d4><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-bea9c8d4></path><path d="M5 12l5 5l10 -10" data-v-bea9c8d4></path></svg></span><span class="legend-text" data-v-bea9c8d4>Ocupada (no se puede desactivar)</span></div><div class="legend-item" data-v-bea9c8d4><span class="legend-icon-edit fuera-servicio" data-v-bea9c8d4><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-ban" data-v-bea9c8d4><path stroke="none" d="M0 0h24v24H0z" fill="none" data-v-bea9c8d4></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" data-v-bea9c8d4></path><path d="M5.7 5.7l12.6 12.6" data-v-bea9c8d4></path></svg></span><span class="legend-text" data-v-bea9c8d4>Fuera de servicio</span></div><div class="legend-item" data-v-bea9c8d4><span class="legend-indicator moved" data-v-bea9c8d4></span><span class="legend-text" data-v-bea9c8d4>Mesa reubicada</span></div>', 4);
const _hoisted_52 = [
  _hoisted_48
];
const _hoisted_53 = { class: "column is-3" };
const _hoisted_54 = {
  key: 0,
  class: "stat-widget area-stats-widget is-straight mesa-details-box"
};
const _hoisted_55 = { class: "fieldset-heading pb-4" };
const _hoisted_56 = { style: { "font-size": "13px" } };
const _hoisted_57 = {
  key: 0,
  class: "notification is-secondary mb-2 p-3"
};
const _hoisted_58 = { class: "container-text" };
const _hoisted_59 = { class: "" };
const _hoisted_60 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("br", null, null, -1));
const _hoisted_61 = { style: { "font-size": "13px" } };
const _hoisted_62 = {
  class: "is-flex is-justify-content-space-between mt-2",
  style: { "gap": "10px" }
};
const _hoisted_63 = /* @__PURE__ */ createTextVNode(" Cancelar ");
const _hoisted_64 = {
  key: 1,
  class: "column is-12 mb-3 px-0"
};
const _hoisted_65 = { class: "box p-3 joined-tables-container" };
const _hoisted_66 = { style: { "display": "flex", "align-items": "center", "justify-content": "space-between", "margin-bottom": "8px" } };
const _hoisted_67 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", { style: { "font-weight": "600", "font-size": "13px", "color": "#48c774" } }, [
  /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-link" }),
  /* @__PURE__ */ createTextVNode(" Mesas unidas ")
], -1));
const _hoisted_68 = /* @__PURE__ */ createTextVNode(" Separar todas ");
const _hoisted_69 = { style: { "display": "flex", "flex-direction": "column", "gap": "6px" } };
const _hoisted_70 = { style: { "font-size": "13px", "font-weight": "500" } };
const _hoisted_71 = ["onClick"];
const _hoisted_72 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fa-solid fa-xmark" }, null, -1));
const _hoisted_73 = [
  _hoisted_72
];
const _hoisted_74 = { class: "columns is-multiline" };
const _hoisted_75 = { class: "column is-12" };
const _hoisted_76 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", null, "Personas en la mesa", -1));
const _hoisted_77 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "form-icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-hash"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M5 9l14 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M5 15l14 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M11 4l-4 16" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M17 4l-4 16" })
  ])
], -1));
const _hoisted_78 = { class: "column is-12" };
const _hoisted_79 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", null, "Cliente", -1));
const _hoisted_80 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "form-icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-user"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" })
  ])
], -1));
const _hoisted_81 = { class: "column is-12" };
const _hoisted_82 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", null, "Mozo", -1));
const _hoisted_83 = {
  class: "select",
  placeholder: "Seleccione mozo"
};
const _hoisted_84 = ["value"];
const _hoisted_85 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { class: "form-icon" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon icon-tabler icons-tabler-outline icon-tabler-user-check"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M15 19l2 2l4 -4" })
  ])
], -1));
const _hoisted_86 = { class: "column is-12" };
const _hoisted_87 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", null, "Comentarios", -1));
const _hoisted_88 = { class: "column is-12" };
const _hoisted_89 = /* @__PURE__ */ createTextVNode(" Abrir mesa ");
const _hoisted_90 = {
  key: 1,
  class: "stat-widget area-stats-widget is-straight"
};
const _hoisted_91 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("h3", null, "SELECCIONE UNA MESA", -1));
const _hoisted_92 = [
  _hoisted_91
];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    var _a;
    const STORAGE_KEY_VISUALIZACION = "restaurant_visualizacion_mesas";
    const notyf = new Notyf();
    const mesasSession = reactive(useMesaSession());
    const companySession = reactive(useCompanySession());
    const mesaSelected = computed(() => mesasSession.mesaSelected);
    let mesas = ref([]);
    const userSession = useUserSession();
    const userRole = userSession.getRole();
    let viewPos = ref(false);
    let canUpdate = ref(false);
    let openModalEditMesa = ref(false);
    let loadingRefresh = ref(false);
    let intervalId = void 0;
    const environments = mesasSession.environments;
    const environmentsWithoutDelivery = computed(() => {
      return environments.filter((env) => !env.is_delivery && !env.is_takeaway);
    });
    const sortedEnvironments = computed(() => {
      return [...environments].sort((a, b) => {
        const aRight = a.is_delivery || a.is_takeaway;
        const bRight = b.is_delivery || b.is_takeaway;
        if (aRight === bRight)
          return 0;
        return aRight ? 1 : -1;
      });
    });
    const activeTab = ref(((_a = sortedEnvironments.value[0]) == null ? void 0 : _a.name) || "");
    const isDeliveryOrTakeaway = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_delivery || env.is_takeaway : false;
    });
    const isDelivery = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_delivery : false;
    });
    const isTakeaway = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_takeaway : false;
    });
    let stateFormMesa = reactive({
      id: 0,
      label: "",
      shape: "",
      environment: DEFAULT_ENVIRONMENT
    });
    let modeEditMesas = ref(false);
    const opcionesVisualizacion = ref({
      mozo: {
        activo: true,
        label: "Mozo",
        icono: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-user" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>'
      },
      tiempo: {
        activo: true,
        label: "Tiempo",
        icono: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 12l3 2" /><path d="M12 7v5" /></svg>'
      },
      pedidos: {
        activo: true,
        label: "N\xB0 Pedidos",
        icono: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-tools-kitchen-2" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 3v12h-5c-.023 -3.681 .184 -7.406 5 -12zm0 12v6h-1v-3m-10 -14v17m-3 -17v3a3 3 0 1 0 6 0v-3" /></svg>'
      },
      monto: {
        activo: false,
        label: "Monto",
        icono: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-coin" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12.5 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path><path d="M17 7.5l-2 9"></path></svg>'
      },
      personas: {
        activo: false,
        label: "Personas",
        icono: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-users" style="margin-top: -2px"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>'
      },
      cliente: { activo: false, label: "Cliente", icono: "fa-user" }
    });
    const cargarConfiguracionVisualizacion = () => {
      const saved = localStorage.getItem(STORAGE_KEY_VISUALIZACION);
      if (saved) {
        try {
          const parsed = JSON.parse(saved);
          Object.keys(parsed).forEach((key) => {
            if (opcionesVisualizacion.value[key]) {
              opcionesVisualizacion.value[key].activo = parsed[key];
            }
          });
        } catch (error) {
          console.error("Error al cargar configuraci\xF3n de visualizaci\xF3n:", error);
        }
      }
    };
    watch(opcionesVisualizacion, (newVal) => {
      const toSave = {};
      Object.keys(newVal).forEach((key) => {
        toSave[key] = newVal[key].activo;
      });
      localStorage.setItem(STORAGE_KEY_VISUALIZACION, JSON.stringify(toSave));
    }, { deep: true });
    const mostrarConfig = computed(() => {
      return {
        mozo: opcionesVisualizacion.value.mozo.activo,
        tiempo: opcionesVisualizacion.value.tiempo.activo,
        pedidos: opcionesVisualizacion.value.pedidos.activo,
        monto: opcionesVisualizacion.value.monto.activo,
        personas: opcionesVisualizacion.value.personas.activo,
        cliente: opcionesVisualizacion.value.cliente.activo
      };
    });
    const isSvgIcon = (icono) => icono.trim().startsWith("<svg");
    const toggleVisualizacion = (key) => {
      const opcion = opcionesVisualizacion.value[key];
      opcion.activo = !opcion.activo;
    };
    const filtros = ref({
      disponibles: true,
      ocupadas: true,
      fueraServicio: true
    });
    const toggleFiltro = (tipo) => {
      filtros.value[tipo] = !filtros.value[tipo];
    };
    const todasLasMesas = computed(() => mesas.value);
    const mesasVisibles = computed(() => {
      return mesas.value.filter((mesa) => {
        if (mesa.group_id && !mesa.is_main_table) {
          return false;
        }
        if (mesa.is_active === false) {
          return filtros.value.fueraServicio;
        }
        if (mesa.status === MESA_NO_DISPONIBLE) {
          return filtros.value.ocupadas;
        }
        if (mesa.status === MESA_DISPONIBLE) {
          return filtros.value.disponibles;
        }
        return true;
      });
    });
    const modoSeleccionUnion = ref(false);
    const mesaPrincipalParaUnion = ref(null);
    const mesasSecundariasSeleccionadas = ref(new Set());
    const grupoExistenteParaAgregar = ref(null);
    const iniciarModoUnion = (mesa) => {
      if (mesa.is_active === false) {
        notyf.error("No puedes unir una mesa fuera de servicio");
        return;
      }
      if (mesa.group_id) {
        if (mesa.status !== MESA_NO_DISPONIBLE) {
          notyf.error("Solo puedes agregar mesas a un grupo que tenga pedidos");
          return;
        }
        grupoExistenteParaAgregar.value = mesa.group_id;
        mesaPrincipalParaUnion.value = mesa;
        modoSeleccionUnion.value = true;
        mesasSecundariasSeleccionadas.value = new Set();
        notyf.success(`Agrega mesas disponibles al grupo de Mesa ${mesa.label}`);
        return;
      }
      mesaPrincipalParaUnion.value = mesa;
      modoSeleccionUnion.value = true;
      mesasSecundariasSeleccionadas.value = new Set();
      grupoExistenteParaAgregar.value = null;
      const estadoMesa = mesa.status === MESA_NO_DISPONIBLE ? "ocupada" : "disponible";
      notyf.success(`Mesa ${mesa.label} (${estadoMesa}) seleccionada. Selecciona las mesas secundarias.`);
    };
    const toggleMesaSecundaria = (mesa) => {
      var _a2;
      if (!modoSeleccionUnion.value) {
        selectMesa(mesa);
        return;
      }
      if (mesa.id === ((_a2 = mesaPrincipalParaUnion.value) == null ? void 0 : _a2.id)) {
        notyf.error("Esta es la mesa principal");
        return;
      }
      if (mesa.is_active === false) {
        notyf.error("No puedes unir una mesa fuera de servicio");
        return;
      }
      if (mesa.group_id && mesa.group_id !== grupoExistenteParaAgregar.value) {
        notyf.error("Esta mesa ya pertenece a otro grupo");
        return;
      }
      if (mesa.status !== MESA_DISPONIBLE) {
        notyf.error("Solo puedes agregar mesas disponibles (vac\xEDas)");
        return;
      }
      const seleccionadas = new Set(mesasSecundariasSeleccionadas.value);
      if (seleccionadas.has(mesa.id)) {
        seleccionadas.delete(mesa.id);
        notyf.success(`Mesa ${mesa.label} deseleccionada`);
      } else {
        seleccionadas.add(mesa.id);
        notyf.success(`Mesa ${mesa.label} seleccionada`);
      }
      mesasSecundariasSeleccionadas.value = seleccionadas;
    };
    const confirmarUnion = async () => {
      if (!mesaPrincipalParaUnion.value) {
        notyf.error("No hay mesa principal seleccionada");
        return;
      }
      if (mesasSecundariasSeleccionadas.value.size === 0) {
        notyf.error("Debes seleccionar al menos una mesa secundaria");
        return;
      }
      try {
        notyf.success("Procesando uni\xF3n de mesas...");
        let grupoId;
        if (grupoExistenteParaAgregar.value) {
          grupoId = grupoExistenteParaAgregar.value;
          console.log(`Agregando mesas al grupo existente ${grupoId}`);
        } else {
          console.log("Creando nuevo grupo con mesa principal:", mesaPrincipalParaUnion.value.id);
          try {
            const grupo = await MesaService.crearGrupoMesas(mesaPrincipalParaUnion.value.id);
            grupoId = grupo.id;
            console.log("Grupo creado con ID:", grupoId);
          } catch (error) {
            notyf.error(error.message || "Error al crear el grupo");
            return;
          }
        }
        const mesasArray = Array.from(mesasSecundariasSeleccionadas.value);
        console.log(`Agregando ${mesasArray.length} mesa(s) al grupo ${grupoId}`);
        let mesasAgregadas = 0;
        let mesasFallidas = 0;
        for (const mesaId of mesasArray) {
          try {
            console.log(`Agregando mesa ${mesaId}...`);
            await MesaService.agregarMesaAGrupo(grupoId, mesaId);
            console.log(`Mesa ${mesaId} agregada correctamente`);
            mesasAgregadas++;
          } catch (error) {
            console.error(`Error al agregar mesa ${mesaId}:`, error);
            notyf.error(error.message || `Error al agregar mesa ${mesaId}`);
            mesasFallidas++;
          }
        }
        console.log(`Resumen: ${mesasAgregadas} agregadas, ${mesasFallidas} fallidas`);
        await MesaService.syncMesasAndEnvironments();
        modoSeleccionUnion.value = false;
        mesaPrincipalParaUnion.value = null;
        mesasSecundariasSeleccionadas.value = new Set();
        grupoExistenteParaAgregar.value = null;
        if (mesasAgregadas > 0) {
          notyf.success(` ${mesasAgregadas} mesa(s) unida(s) correctamente`);
        }
      } catch (error) {
        console.error("Error general al unir mesas:", error);
        notyf.error("Error al unir mesas. Intenta nuevamente.");
      }
    };
    const cancelarUnion = () => {
      modoSeleccionUnion.value = false;
      mesaPrincipalParaUnion.value = null;
      mesasSecundariasSeleccionadas.value = new Set();
      grupoExistenteParaAgregar.value = null;
      notyf.success("Uni\xF3n cancelada");
    };
    const handleSepararMesa = async (mesaId) => {
      const mesa = mesas.value.find((m) => m.id === mesaId);
      if (!(mesa == null ? void 0 : mesa.group_id)) {
        notyf.error("Esta mesa no tiene grupo");
        return;
      }
      try {
        notyf.success("Separando mesas...");
        await MesaService.disolverGrupo(mesa.group_id);
        await MesaService.syncMesasAndEnvironments();
        notyf.success(`Grupo disuelto correctamente`);
      } catch (error) {
        console.error("Error al separar mesas:", error);
        notyf.error("Error al separar mesas");
      }
    };
    const separarMesaEspecifica = async (mesaId) => {
      const mesa = mesas.value.find((m) => m.id === mesaId);
      if (!(mesa == null ? void 0 : mesa.group_id)) {
        notyf.error("Esta mesa no tiene grupo");
        return;
      }
      try {
        notyf.success("Separando mesa...");
        await MesaService.separarMesaDeGrupo(mesaId);
        await MesaService.syncMesasAndEnvironments();
        notyf.success(`Mesa ${mesa.label} separada`);
      } catch (error) {
        console.error("Error al separar mesa:", error);
        notyf.error("Error al separar mesa");
      }
    };
    const getMesasUnidasCompletas = (mesaId) => {
      const mesa = mesas.value.find((m) => m.id === mesaId);
      if (!(mesa == null ? void 0 : mesa.group_id))
        return [];
      return mesas.value.filter((m) => m.group_id === mesa.group_id && m.id !== mesaId);
    };
    const getMesaLabelConUniones = (mesa) => {
      const mesasGrupo = getMesasUnidasCompletas(mesa.id);
      if (mesasGrupo.length === 0)
        return mesa.label;
      const labels = mesasGrupo.map((m) => m.label).join(" + ");
      return `${mesa.label} + ${labels}`;
    };
    const handleToggleActive = async (mesaId) => {
      try {
        const result = await MesaService.toggleMesaActive(mesaId);
        notyf.success(result.message);
        await syncMesasEnviroments();
      } catch (error) {
        console.error("Error al cambiar estado:", error);
        notyf.error(error.message || "Error al cambiar estado de la mesa");
      }
    };
    let openMoveAmbienteModal = ref(false);
    let mesaToMoveAmbiente = ref(null);
    const handleMoveAmbiente = (mesa) => {
      if (mesa.status === MESA_NO_DISPONIBLE) {
        return;
      }
      mesaToMoveAmbiente.value = mesa;
      openMoveAmbienteModal.value = true;
    };
    const confirmMoveAmbiente = async (nuevoAmbiente) => {
      if (!mesaToMoveAmbiente.value)
        return;
      try {
        await MesaService.cambiarAmbienteMesa(mesaToMoveAmbiente.value.id, nuevoAmbiente);
        notyf.success(`Mesa ${mesaToMoveAmbiente.value.label} movida a ${nuevoAmbiente}`);
        await syncMesasEnviroments();
        openMoveAmbienteModal.value = false;
        mesaToMoveAmbiente.value = null;
      } catch (error) {
        notyf.error(error.message || "Error al mover la mesa");
        console.error(error);
      }
    };
    const handleRestaurarAmbiente = async (mesaId) => {
      try {
        const mesa = todasLasMesas.value.find((m) => m.id === mesaId);
        if (!mesa)
          return;
        await MesaService.restaurarAmbienteMesa(mesaId);
        notyf.success(`Mesa ${mesa.label} restaurada a ${mesa.original_environment}`);
        await syncMesasEnviroments();
      } catch (error) {
        notyf.error(error.message || "Error al restaurar la mesa");
        console.error(error);
      }
    };
    const loggedInWaiterName = userSession.sellerName;
    companySession.getCustomerDefault();
    let openMoveOrderModal = ref(false);
    let mesaToMoveOrder = ref(null);
    const openDocumentDialog = ref(false);
    const mesaForPayment = ref(null);
    const posBagForPayment = reactive({
      products: [],
      total: 0,
      barman: userSession.name,
      waiter: "",
      enterAmount: 0
    });
    const handleMoveOrder = (mesa) => {
      mesaToMoveOrder.value = mesa;
      openMoveOrderModal.value = true;
    };
    const confirmMoveOrder = async (mesaDestinoId) => {
      var _a2;
      if (mesaToMoveOrder.value) {
        try {
          await MesaService.changeTablePedido(mesaToMoveOrder.value.id, mesaDestinoId);
          const mesaDestino = ((_a2 = mesas.value.find((m) => m.id === mesaDestinoId)) == null ? void 0 : _a2.label) || mesaDestinoId;
          notyf.success(`Pedido movido de ${mesaToMoveOrder.value.label} a la mesa ${mesaDestino}.`);
          mesaToMoveOrder.value = null;
          openMoveOrderModal.value = false;
          await syncMesasEnviroments();
        } catch (error) {
          notyf.error("Error al mover el pedido. Intente nuevamente.");
          console.error(error);
          openMoveOrderModal.value = false;
        }
      }
    };
    const resolveMesaPrincipal = (mesa) => {
      if (mesa.group_id && !mesa.is_main_table) {
        const mesaPrincipal = mesas.value.find((m) => m.group_id === mesa.group_id && m.is_main_table);
        if (mesaPrincipal) {
          console.log(`Mesa ${mesa.label} est\xE1 agrupada. Redirigiendo a mesa principal ${mesaPrincipal.label}`);
          notyf.success(`Mesa ${mesa.label} est\xE1 agrupada con ${mesaPrincipal.label}`);
          return mesaPrincipal;
        }
        console.error(`No se encontr\xF3 la mesa principal del grupo ${mesa.group_id}`);
        notyf.error("Error: No se encontr\xF3 la mesa principal del grupo");
        return null;
      }
      return mesa;
    };
    const selectMesa = (mesa) => {
      if (modeEditMesas.value)
        return;
      if (modoSeleccionUnion.value) {
        toggleMesaSecundaria(mesa);
        return;
      }
      const mesaResolved = resolveMesaPrincipal(mesa);
      if (!mesaResolved)
        return;
      mesa = mesaResolved;
      if (mesa.status == MESA_DISPONIBLE) {
        mesasSession.setMesaSelected(mesa.id);
        if (mesaSelected.value) {
          mesaSelected.value.waiter = loggedInWaiterName;
        }
        canUpdate.value = true;
      } else {
        mesasSession.setMesaSelected(mesa.id);
        viewPos.value = true;
      }
    };
    const selectMesaDetails = (mesa) => {
      if (modeEditMesas.value)
        return;
      if (modoSeleccionUnion.value) {
        toggleMesaSecundaria(mesa);
        return;
      }
      const mesaResolved = resolveMesaPrincipal(mesa);
      if (!mesaResolved)
        return;
      mesa = mesaResolved;
      mesasSession.setMesaSelected(mesa.id);
      if (mesaSelected.value) {
        mesaSelected.value.waiter = loggedInWaiterName;
      }
      canUpdate.value = true;
      viewPos.value = false;
    };
    const openMesa = () => {
      if (mesaSelected.value) {
        console.log("Abriendo mesa:", mesaSelected);
        mesasSession.openMesa(mesaSelected.value);
        viewPos.value = true;
      }
    };
    const handleShipped = async (mesa) => {
      try {
        await mesasSession.setShippedMesa(mesa.id);
        notyf.success(`Pedido de ${mesa.label} en camino`);
      } catch (error) {
        notyf.error("Error al actualizar estado");
      }
    };
    const handleDelivered = async (mesa) => {
      try {
        await mesasSession.setDeliveredMesa(mesa.id);
        notyf.success(`Pedido de ${mesa.label} despachado`);
      } catch (error) {
        notyf.error("Error al actualizar estado");
      }
    };
    const handlePayment = async (mesa) => {
      mesaForPayment.value = mesa;
      await mesasSession.setMesaSelected(mesa.id);
      await mesasSession.calculaTotalBag();
      const products = mesa.products || [];
      Object.assign(posBagForPayment, {
        products: JSON.parse(JSON.stringify(products)),
        total: mesa.total || 0,
        waiter: mesa.waiter || "",
        barman: userSession.name,
        enterAmount: 0
      });
      openDocumentDialog.value = true;
    };
    const viewMesas = () => {
      viewPos.value = false;
    };
    const closeDocumentDialog = () => {
      openDocumentDialog.value = false;
      mesaForPayment.value = null;
    };
    const backFromPayment = () => {
      openDocumentDialog.value = false;
    };
    const handlePaymentSuccess = async (data) => {
      if (mesaForPayment.value) {
        const mesaIndex = mesas.value.findIndex((m) => {
          var _a2;
          return m.id === ((_a2 = mesaForPayment.value) == null ? void 0 : _a2.id);
        });
        if (mesaIndex !== -1) {
          mesas.value[mesaIndex].is_paid = true;
          await MesaService.saveMesa(mesas.value[mesaIndex]);
          notyf.success(`Pago registrado para ${mesaForPayment.value.label}`);
        }
      }
      mesasSession.setMesaSelected(0);
      openDocumentDialog.value = false;
      mesaForPayment.value = null;
    };
    const handleCloseMesa = async (mesa) => {
      mesasSession.startClosingMesa(mesa.id);
      try {
        const index = mesas.value.findIndex((m) => m.id === mesa.id);
        if (index !== -1) {
          const updatedMesa = __spreadProps(__spreadValues({}, mesas.value[index]), {
            status: MESA_DISPONIBLE,
            products: [],
            personas: 0,
            cliente: "",
            comentarios: "",
            waiter: "",
            open: false,
            close: true,
            order_status: "pending",
            is_paid: false,
            total: 0
          });
          mesas.value[index] = updatedMesa;
          mesasSession.updateMesa(updatedMesa);
          await MesaService.saveMesa(updatedMesa);
          use.resetTable(updatedMesa.id);
          mesasSession.setMesaSelected(0);
          notyf.success(`Mesa ${mesa.label} cerrada correctamente`);
        }
      } catch (error) {
        notyf.error("Error al cerrar la mesa");
      } finally {
        mesasSession.finishClosingMesa(mesa.id);
      }
    };
    const editMesaForm = (item) => {
      const { id, label, shape, environment } = item;
      stateFormMesa.id = id;
      stateFormMesa.label = label;
      stateFormMesa.shape = shape;
      stateFormMesa.environment = environment;
      openModalEditMesa.value = true;
    };
    const editMesas = () => {
      modeEditMesas.value = true;
      if (intervalId !== void 0) {
        clearInterval(intervalId);
      }
    };
    const finishEditMesas = () => {
      modeEditMesas.value = false;
      intervalId = window.setInterval(syncMesasEnviroments, 5e3);
    };
    const setMesas = () => {
      mesas.value = mesasSession.mesas;
    };
    const closeFormMesa = () => {
      openModalEditMesa.value = false;
    };
    const hayMasDeUnAmbiente = computed(() => {
      const ambientes = new Set(todasLasMesas.value.filter((mesa) => {
        var _a2;
        const env = ((_a2 = mesa.environment) == null ? void 0 : _a2.toLowerCase()) || "";
        return !env.includes("delivery") && !env.includes("para llevar") && !env.includes("parallevar");
      }).map((mesa) => mesa.environment).filter(Boolean));
      return ambientes.size > 1;
    });
    const syncMesasEnviroments = async () => {
      await MesaService.syncMesasAndEnvironments();
      setMesas();
    };
    watch(() => [...mesasSession.mesas], () => {
      setMesas();
    });
    onMounted(() => {
      cargarConfiguracionVisualizacion();
      setMesas();
      syncMesasEnviroments();
      intervalId = window.setInterval(syncMesasEnviroments, 5e3);
    });
    onUnmounted(() => {
      if (intervalId !== void 0) {
        clearInterval(intervalId);
      }
    });
    return (_ctx, _cache) => {
      const _component_MesaForm = _sfc_main$6;
      const _component_CashDialog = _sfc_main$7;
      const _component_VButton = _sfc_main$3;
      const _component_MesaEnvironment = __unplugin_components_3;
      const _component_VTabs = _sfc_main$8;
      const _component_VPlaceload = __unplugin_components_4;
      const _component_VPlaceloadWrap = __unplugin_components_5;
      const _component_VControl = __unplugin_components_1$1;
      const _component_VField = _sfc_main$9;
      const _component_MesaRestaurantPos = _sfc_main$a;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_MesaForm, {
          open: unref(openModalEditMesa),
          model: unref(stateFormMesa),
          onClose: closeFormMesa
        }, null, 8, ["open", "model"]),
        createVNode(_component_CashDialog),
        !unref(viewPos) ? (openBlock(), createElementBlock("div", _hoisted_1$1, [
          createBaseVNode("div", _hoisted_2, [
            createBaseVNode("div", _hoisted_3, [
              createBaseVNode("div", _hoisted_4, [
                createBaseVNode("div", _hoisted_5, [
                  !unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_6, [
                    _hoisted_7,
                    !unref(isDeliveryOrTakeaway) ? (openBlock(), createElementBlock("label", {
                      key: 0,
                      class: "checkbox-inline",
                      onClick: _cache[0] || (_cache[0] = ($event) => toggleFiltro("disponibles"))
                    }, [
                      createBaseVNode("span", {
                        class: normalizeClass(["checkbox-box", { "is-checked": filtros.value.disponibles }])
                      }, [
                        filtros.value.disponibles ? (openBlock(), createElementBlock("i", _hoisted_8)) : createCommentVNode("", true)
                      ], 2),
                      _hoisted_9
                    ])) : createCommentVNode("", true),
                    !unref(isDeliveryOrTakeaway) ? (openBlock(), createElementBlock("label", {
                      key: 1,
                      class: "checkbox-inline",
                      onClick: _cache[1] || (_cache[1] = ($event) => toggleFiltro("ocupadas"))
                    }, [
                      createBaseVNode("span", {
                        class: normalizeClass(["checkbox-box", { "is-checked": filtros.value.ocupadas }])
                      }, [
                        filtros.value.ocupadas ? (openBlock(), createElementBlock("i", _hoisted_10)) : createCommentVNode("", true)
                      ], 2),
                      _hoisted_11
                    ])) : createCommentVNode("", true),
                    !unref(isDeliveryOrTakeaway) ? (openBlock(), createElementBlock("label", {
                      key: 2,
                      class: "checkbox-inline",
                      onClick: _cache[2] || (_cache[2] = ($event) => toggleFiltro("fueraServicio"))
                    }, [
                      createBaseVNode("span", {
                        class: normalizeClass(["checkbox-box", { "is-checked": filtros.value.fueraServicio }])
                      }, [
                        filtros.value.fueraServicio ? (openBlock(), createElementBlock("i", _hoisted_12)) : createCommentVNode("", true)
                      ], 2),
                      _hoisted_13
                    ])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true),
                  unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_14, [
                    _hoisted_15,
                    (openBlock(true), createElementBlock(Fragment, null, renderList(opcionesVisualizacion.value, (opcion, key) => {
                      return withDirectives((openBlock(), createElementBlock("label", {
                        key,
                        class: "checkbox-inline",
                        onClick: ($event) => toggleVisualizacion(key)
                      }, [
                        createBaseVNode("span", {
                          class: normalizeClass(["checkbox-box", { "is-checked": opcion.activo }])
                        }, [
                          opcion.activo ? (openBlock(), createElementBlock("i", _hoisted_17)) : createCommentVNode("", true)
                        ], 2),
                        createBaseVNode("span", _hoisted_18, [
                          isSvgIcon(opcion.icono) ? (openBlock(), createElementBlock("span", {
                            key: 0,
                            class: "checkbox-icon",
                            innerHTML: opcion.icono
                          }, null, 8, _hoisted_19)) : (openBlock(), createElementBlock("i", {
                            key: 1,
                            class: normalizeClass(["fa-solid", opcion.icono]),
                            "aria-hidden": "true"
                          }, null, 2)),
                          createTextVNode(" " + toDisplayString(opcion.label), 1)
                        ])
                      ], 8, _hoisted_16)), [
                        [vShow, key !== "cliente"]
                      ]);
                    }), 128))
                  ])) : createCommentVNode("", true),
                  createBaseVNode("div", _hoisted_20, [
                    !unref(modeEditMesas) && unref(userRole) != "MOZO" ? (openBlock(), createBlock(_component_VButton, {
                      key: 0,
                      color: "primary",
                      raised: "",
                      disabled: unref(isDeliveryOrTakeaway),
                      onClick: editMesas
                    }, {
                      default: withCtx(() => [
                        _hoisted_21
                      ]),
                      _: 1
                    }, 8, ["disabled"])) : createCommentVNode("", true),
                    unref(modeEditMesas) && unref(userRole) != "MOZO" ? (openBlock(), createBlock(_component_VButton, {
                      key: 1,
                      class: "btn-secondary",
                      raised: "",
                      onClick: finishEditMesas
                    }, {
                      default: withCtx(() => [
                        _hoisted_22
                      ]),
                      _: 1
                    })) : createCommentVNode("", true)
                  ])
                ]),
                createVNode(_component_VTabs, {
                  selected: activeTab.value,
                  "onUpdate:selected": _cache[4] || (_cache[4] = ($event) => activeTab.value = $event),
                  tabs: unref(environmentsWithoutDelivery).map((item) => ({
                    label: item.name,
                    value: item.name,
                    alignRight: item.is_delivery || item.is_takeaway
                  }))
                }, {
                  tab: withCtx(({ activeValue }) => [
                    createVNode(_component_MesaEnvironment, {
                      mesas: unref(mesasVisibles).filter((x) => x.environment == activeValue),
                      "todas-las-mesas": unref(todasLasMesas),
                      selected: unref(mesaSelected),
                      "mode-edit": unref(modeEditMesas),
                      "modo-union": modoSeleccionUnion.value,
                      "mesa-principal-union": mesaPrincipalParaUnion.value,
                      "mesas-seleccionadas": mesasSecundariasSeleccionadas.value,
                      "mostrar-config": unref(mostrarConfig),
                      "view-list": unref(isDeliveryOrTakeaway),
                      "is-delivery": unref(isDelivery),
                      "is-takeaway": unref(isTakeaway),
                      onSelect: _cache[3] || (_cache[3] = ($event) => modoSeleccionUnion.value ? toggleMesaSecundaria($event) : selectMesa($event)),
                      onSelectDetails: selectMesaDetails,
                      onEdit: editMesaForm,
                      onSeparar: handleSepararMesa,
                      onSepararEspecifica: separarMesaEspecifica,
                      onIniciarUnion: iniciarModoUnion,
                      onAgregarMesa: iniciarModoUnion,
                      onToggleActive: handleToggleActive,
                      onMoverPedido: handleMoveOrder,
                      onShipped: handleShipped,
                      onDelivered: handleDelivered,
                      onPayment: handlePayment,
                      onCloseTable: handleCloseMesa,
                      onMoverAmbiente: handleMoveAmbiente,
                      onRestaurarAmbiente: handleRestaurarAmbiente
                    }, null, 8, ["mesas", "todas-las-mesas", "selected", "mode-edit", "modo-union", "mesa-principal-union", "mesas-seleccionadas", "mostrar-config", "view-list", "is-delivery", "is-takeaway"])
                  ]),
                  _: 1
                }, 8, ["selected", "tabs"]),
                unref(loadingRefresh) ? (openBlock(), createBlock(_component_VPlaceloadWrap, { key: 0 }, {
                  default: withCtx(() => [
                    createVNode(_component_VPlaceload, {
                      height: "25px",
                      width: "20%",
                      class: "mx-2"
                    })
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                !unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_23, [
                  createBaseVNode("div", _hoisted_24, [
                    withDirectives(createBaseVNode("div", _hoisted_25, _hoisted_28, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway)]
                    ]),
                    _hoisted_29,
                    withDirectives(createBaseVNode("div", _hoisted_31, _hoisted_34, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway)]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_35, _hoisted_38, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway) && unref(hayMasDeUnAmbiente)]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_39, _hoisted_42, 512), [
                      [vShow, unref(isDeliveryOrTakeaway)]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_43, _hoisted_46, 512), [
                      [vShow, unref(isDeliveryOrTakeaway)]
                    ])
                  ])
                ])) : createCommentVNode("", true),
                unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_47, _hoisted_52)) : createCommentVNode("", true)
              ])
            ]),
            createBaseVNode("div", _hoisted_53, [
              unref(mesaSelected) && unref(mesaSelected).id !== 0 && unref(mesaSelected).environment != "Para Llevar" && unref(mesaSelected).environment != "Delivery" ? (openBlock(), createElementBlock("div", _hoisted_54, [
                createBaseVNode("div", _hoisted_55, [
                  createBaseVNode("h4", null, [
                    createTextVNode(" MESA " + toDisplayString(getMesaLabelConUniones(unref(mesaSelected))) + " ", 1),
                    createBaseVNode("span", _hoisted_56, toDisplayString(unref(mesaSelected).environment), 1)
                  ])
                ]),
                modoSeleccionUnion.value && mesaPrincipalParaUnion.value ? (openBlock(), createElementBlock("div", _hoisted_57, [
                  createBaseVNode("div", null, [
                    createBaseVNode("div", _hoisted_58, [
                      createBaseVNode("strong", _hoisted_59, [
                        createBaseVNode("i", {
                          class: normalizeClass(grupoExistenteParaAgregar.value ? "fa-solid fa-plus" : "fa-solid fa-link")
                        }, null, 2),
                        createTextVNode(" " + toDisplayString(grupoExistenteParaAgregar.value ? " Agregando mesas" : " Uniendo Mesa") + " " + toDisplayString(mesaPrincipalParaUnion.value.label), 1)
                      ]),
                      _hoisted_60,
                      createBaseVNode("span", _hoisted_61, " Selecciona las mesas " + toDisplayString(grupoExistenteParaAgregar.value ? "adicionales" : "secundarias") + " (" + toDisplayString(mesasSecundariasSeleccionadas.value.size) + " seleccionadas) ", 1)
                    ]),
                    createBaseVNode("div", _hoisted_62, [
                      createVNode(_component_VButton, {
                        class: "is-fullwidth",
                        color: "primary",
                        disabled: mesasSecundariasSeleccionadas.value.size === 0,
                        onClick: confirmarUnion
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(grupoExistenteParaAgregar.value ? "Agregar mesas" : "Confirmar"), 1)
                        ]),
                        _: 1
                      }, 8, ["disabled"]),
                      createVNode(_component_VButton, {
                        class: "is-fullwidth",
                        color: "danger",
                        onClick: cancelarUnion
                      }, {
                        default: withCtx(() => [
                          _hoisted_63
                        ]),
                        _: 1
                      })
                    ])
                  ])
                ])) : createCommentVNode("", true),
                getMesasUnidasCompletas(unref(mesaSelected).id).length > 0 ? (openBlock(), createElementBlock("div", _hoisted_64, [
                  createBaseVNode("div", _hoisted_65, [
                    createBaseVNode("div", _hoisted_66, [
                      _hoisted_67,
                      createVNode(_component_VButton, {
                        color: "warning",
                        style: { "padding": "4px 8px", "font-size": "11px" },
                        onClick: _cache[5] || (_cache[5] = ($event) => handleSepararMesa(unref(mesaSelected).id))
                      }, {
                        default: withCtx(() => [
                          _hoisted_68
                        ]),
                        _: 1
                      })
                    ]),
                    createBaseVNode("div", _hoisted_69, [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(getMesasUnidasCompletas(unref(mesaSelected).id), (mesaUnida) => {
                        return openBlock(), createElementBlock("div", {
                          key: mesaUnida.id,
                          style: { "display": "flex", "align-items": "center", "justify-content": "space-between", "padding": "6px 8px", "background": "white", "border-radius": "4px", "border": "1px solid #dee2e6" }
                        }, [
                          createBaseVNode("span", _hoisted_70, " Mesa " + toDisplayString(mesaUnida.label), 1),
                          createBaseVNode("button", {
                            style: { "background": "none", "border": "none", "color": "#f14668", "cursor": "pointer", "padding": "2px 6px", "font-size": "18px", "line-height": "1" },
                            title: "Separar esta mesa",
                            onClick: ($event) => separarMesaEspecifica(mesaUnida.id)
                          }, _hoisted_73, 8, _hoisted_71)
                        ]);
                      }), 128))
                    ])
                  ])
                ])) : createCommentVNode("", true),
                createBaseVNode("div", _hoisted_74, [
                  createBaseVNode("div", _hoisted_75, [
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_76,
                        createVNode(_component_VControl, { class: "has-icon" }, {
                          default: withCtx(() => [
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => unref(mesaSelected).personas = $event),
                              type: "number",
                              class: "input",
                              min: "0",
                              step: "1",
                              placeholder: "",
                              autocomplete: "given-name",
                              onKeydown: _cache[7] || (_cache[7] = withModifiers(($event) => {
                                ["-", "e", "E"].includes($event.key);
                              }, ["prevent"]))
                            }, null, 544), [
                              [
                                vModelText,
                                unref(mesaSelected).personas,
                                void 0,
                                { number: true }
                              ]
                            ]),
                            _hoisted_77
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_78, [
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_79,
                        createVNode(_component_VControl, { class: "has-icon" }, {
                          default: withCtx(() => [
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": _cache[8] || (_cache[8] = ($event) => unref(mesaSelected).cliente = $event),
                              type: "text",
                              class: "input",
                              placeholder: "Ingrese nombre del cliente",
                              autocomplete: "off"
                            }, null, 512), [
                              [vModelText, unref(mesaSelected).cliente]
                            ]),
                            _hoisted_80
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_81, [
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_82,
                        createVNode(_component_VControl, { class: "has-icon" }, {
                          default: withCtx(() => [
                            createBaseVNode("div", _hoisted_83, [
                              withDirectives(createBaseVNode("select", {
                                "onUpdate:modelValue": _cache[9] || (_cache[9] = ($event) => unref(mesaSelected).waiter = $event)
                              }, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(unref(companySession).waiters, (item, index) => {
                                  return openBlock(), createElementBlock("option", {
                                    key: index + "Wa",
                                    value: `${item.name}`
                                  }, toDisplayString(`${item.name}`), 9, _hoisted_84);
                                }), 128))
                              ], 512), [
                                [vModelSelect, unref(mesaSelected).waiter]
                              ])
                            ]),
                            _hoisted_85
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_86, [
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_87,
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            withDirectives(createBaseVNode("textarea", {
                              "onUpdate:modelValue": _cache[10] || (_cache[10] = ($event) => unref(mesaSelected).comentarios = $event),
                              class: "textarea",
                              rows: "4",
                              placeholder: "",
                              autocomplete: "off",
                              autocapitalize: "off",
                              spellcheck: "true"
                            }, null, 512), [
                              [vModelText, unref(mesaSelected).comentarios]
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_88, [
                    createVNode(_component_VButton, {
                      color: "primary",
                      raised: "",
                      class: "is-fullwidth",
                      onClick: openMesa
                    }, {
                      default: withCtx(() => [
                        _hoisted_89
                      ]),
                      _: 1
                    })
                  ])
                ])
              ])) : createCommentVNode("", true),
              unref(mesaSelected).id == 0 || unref(mesaSelected).environment == "Para Llevar" || unref(mesaSelected).environment == "Delivery" ? (openBlock(), createElementBlock("div", _hoisted_90, _hoisted_92)) : createCommentVNode("", true)
            ])
          ])
        ])) : createCommentVNode("", true),
        createVNode(_sfc_main$4, {
          open: unref(openMoveOrderModal),
          "mesa-origen": unref(mesaToMoveOrder),
          "mesas-disponibles": unref(mesas).filter((x) => {
            var _a2;
            return x.status === unref(MESA_DISPONIBLE) && x.id !== ((_a2 = unref(mesaToMoveOrder)) == null ? void 0 : _a2.id);
          }),
          onClose: _cache[11] || (_cache[11] = ($event) => isRef(openMoveOrderModal) ? openMoveOrderModal.value = false : openMoveOrderModal = false),
          onConfirmMove: confirmMoveOrder
        }, null, 8, ["open", "mesa-origen", "mesas-disponibles"]),
        mesaForPayment.value ? (openBlock(), createBlock(_sfc_main$5, {
          key: 1,
          "pos-bag": unref(posBagForPayment),
          open: openDocumentDialog.value,
          "products-for-view": mesaForPayment.value.products || [],
          onClose: closeDocumentDialog,
          onBack: backFromPayment,
          onPaymentSuccess: handlePaymentSuccess
        }, null, 8, ["pos-bag", "open", "products-for-view"])) : createCommentVNode("", true),
        createVNode(MoveAmbienteModal, {
          open: unref(openMoveAmbienteModal),
          mesa: unref(mesaToMoveAmbiente),
          "ambientes-disponibles": unref(environments).map((e) => e.name),
          onClose: _cache[12] || (_cache[12] = ($event) => isRef(openMoveAmbienteModal) ? openMoveAmbienteModal.value = false : openMoveAmbienteModal = false),
          onConfirm: confirmMoveAmbiente
        }, null, 8, ["open", "mesa", "ambientes-disponibles"]),
        unref(viewPos) ? (openBlock(), createBlock(_component_MesaRestaurantPos, {
          key: 2,
          onViewMesas: viewMesas
        })) : createCommentVNode("", true)
      ], 64);
    };
  }
});
var __unplugin_components_1 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-bea9c8d4"]]);
const _hoisted_1 = { class: "page-content-inner" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    pageTitle.value = "Mesas";
    useHead({
      title: "Main Mesas"
    });
    return (_ctx, _cache) => {
      const _component_LockedScreen = _sfc_main$b;
      const _component_MesaRestaurant = __unplugin_components_1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_LockedScreen),
        createVNode(_component_MesaRestaurant)
      ]);
    };
  }
});
export { _sfc_main as default };
