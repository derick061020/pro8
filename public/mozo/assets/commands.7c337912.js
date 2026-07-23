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
import { _ as __unplugin_components_0 } from "./VDropdown.53f4f138.js";
import { _ as _sfc_main$4 } from "./KanbanDropdown.7d294c92.js";
import { _ as _sfc_main$3 } from "./VLoader.a784d703.js";
import { d as defineStore, b as defineComponent, r as ref, a as computed, o as onMounted, P as onUnmounted, f as openBlock, g as createElementBlock, X as createBaseVNode, Y as normalizeClass, D as createTextVNode, z as toDisplayString, Z as unref, C as createCommentVNode, w as createVNode, B as withCtx, F as Fragment, A as renderList, _ as createStaticVNode, L as watch, a6 as withDirectives, aq as vModelCheckbox, e as useHead } from "./vendor.05b8b5b5.js";
import { a as useMesaSession, C as COMMAND_BAR, b as COMMAND_KITCHEN, c as MesaService, u as use, M as MasterService } from "./masterService.8a758d8d.js";
import { C as CommandStatusService } from "./restaurantService.f8e320da.js";
import { p as provideApi, u as useUserSession, R as ROLES, a as useCompanySession } from "./index.2bb13d6f.js";
import { p as pageTitle } from "./sidebarLayoutState.6ddf5d4e.js";
import "./VIcon.f7f680f8.js";
import "./plugin-vue_export-helper.5a098b48.js";
const { getMesas2 } = useMesaSession();
const useCommandSession = defineStore("commandSession", () => {
  function productStatusFilter(status, command_type) {
    const products = [];
    if (command_type == "kitchen") {
      getMesas2().forEach((mesa) => {
        mesa.products.forEach((item) => {
          const newItem = item;
          newItem.mesa = mesa.id;
          newItem.statusKitchen === status ? products.push(newItem) : "";
        });
      });
    } else {
      getMesas2().forEach((mesa) => {
        mesa.products.forEach((item) => {
          const newItem = item;
          newItem.mesa = mesa.id;
          newItem.statusBar === status ? products.push(newItem) : "";
        });
      });
    }
    return products;
  }
  async function changeStatus(product, type) {
    const mesa = getMesas2().find((mesa2) => mesa2.id == product.mesa);
    mesa && mesa.products.forEach(async (item) => {
      if (type.code === COMMAND_BAR.code) {
        product.id == item.id ? item.statusBar = item.statusBar + 1 : "";
      }
      if (type.code === COMMAND_KITCHEN.code) {
        product.id == item.id ? item.statusKitchen = item.statusKitchen + 1 : "";
      }
    });
    if (mesa) {
      await MesaService.saveMesa(mesa);
      use.updateTable(mesa);
    }
  }
  return {
    productStatusFilter,
    changeStatus
  };
});
var CommandProduct_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$2 = { class: "command-card-grid" };
const _hoisted_2$1 = { class: "mesa-header" };
const _hoisted_3$1 = { class: "table-number" };
const _hoisted_4$1 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_5$1 = { class: "tab-environment" };
const _hoisted_6$1 = { class: "is-cliente-name ml-2 is-size-6" };
const _hoisted_7$1 = { class: "action-body" };
const _hoisted_8$1 = { class: "action-meta is-flex" };
const _hoisted_9$1 = {
  key: 0,
  class: "custom-tag is-info"
};
const _hoisted_10$1 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-tools-kitchen"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 3h8l-1 9h-6l-1 -9"></path><path d="M7 18h2v3h-2l0 -3"></path><path d="M20 3v12h-5c-.023 -3.681 .184 -7.406 5 -12"></path><path d="M20 15v6h-1v-3"></path><path d="M8 12l0 6"></path></svg>', 1);
const _hoisted_11$1 = {
  key: 1,
  class: "custom-tag is-warning"
};
const _hoisted_12$1 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-clock-hour-4"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 12l3 2"></path><path d="M12 7v5"></path></svg>', 1);
const _hoisted_13$1 = {
  key: 2,
  class: "custom-tag is-primary"
};
const _hoisted_14$1 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
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
], -1);
const _hoisted_15$1 = { class: "product-body" };
const _hoisted_16$1 = { class: "card-body p-0" };
const _hoisted_17$1 = { class: "px-2" };
const _hoisted_18$1 = { class: "ml-2" };
const _hoisted_19$1 = {
  key: 0,
  class: "product-note-container mt-2"
};
const _hoisted_20$1 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "17",
  height: "17",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-note",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 20l7 -7" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 20v-6a1 1 0 0 1 1 -1h6v-7a2 2 0 0 0 -2 -2h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7" })
], -1);
const _hoisted_21$1 = {
  key: 1,
  class: "modifiers-container mt-2"
};
const _hoisted_22$1 = {
  class: "is-narrow toc-ignore is-thin",
  style: { "font-size": "0.7rem" }
};
const _hoisted_23$1 = { class: "item-modifier-container" };
const _hoisted_24$1 = { class: "action-header mt-2" };
const _hoisted_25$1 = {
  key: 0,
  class: "btn-next"
};
const _hoisted_26$1 = ["disabled", "aria-busy"];
const _hoisted_27 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-arrow-right ml-2",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    product: { type: null, required: true, default: Object },
    view: { type: null, required: true, default: Object },
    mostrarConfig: { type: Object, required: false, default: () => ({
      tiempo: true,
      ambiente: true,
      area: true,
      mozo: true
    }) }
  },
  emits: ["setProducts"],
  setup(__props, { emit }) {
    const props = __props;
    const loading = ref(false);
    const isLoaderActive = ref(false);
    useCommandSession();
    const mesaSession = useMesaSession();
    const mesa = computed(() => mesaSession.getMesas2().find((m) => m.id === props.product.mesa_id));
    const mesaLabel = computed(() => {
      const mesaValue = props.product.mesa;
      if (!mesaValue)
        return "";
      const currentEnv = mesaSession.environments.find((env) => env.name === props.product.environment);
      if ((currentEnv == null ? void 0 : currentEnv.is_delivery) || (currentEnv == null ? void 0 : currentEnv.is_takeaway)) {
        return mesaValue;
      }
      const mesaNum = Number(mesaValue);
      if (!Number.isNaN(mesaNum) && String(mesaNum) === String(mesaValue).trim()) {
        return `M${mesaValue}`;
      }
      return mesaValue;
    });
    const changeStatus = async (id, product) => {
      if (loading.value)
        return;
      loading.value = true;
      const mesaid = product.mesa_id || 0;
      try {
        const response = await provideApi().get(`restaurant/command-status/set/${id}`);
        const data = response.data;
        if (data.success) {
          const served = await CommandStatusService.getProductsByCommandStatus(mesaid);
          useMesaSession().setServerMesa(mesaid, served);
          const service = await MasterService.saveDataProducts();
          emit("setProducts");
        }
      } catch (error) {
        console.error("Error data:", error);
      } finally {
        loading.value = false;
      }
    };
    const statusMessageMapping = {
      1: "Preparar",
      2: "Listo",
      3: "Entregar"
    };
    const changeStatusMessageName = (status) => {
      if (!status) {
        return "Preparar";
      }
      return statusMessageMapping[status] || "Preparar";
    };
    const statusClass = (status) => {
      switch (status) {
        case 1:
          return "is-secondary";
        case 2:
          return "is-primary";
        case 3:
          return "is-info";
        default:
          return "is-light";
      }
    };
    const now = ref(Date.now());
    onMounted(() => {
      const interval = setInterval(() => {
        now.value = Date.now();
      }, 1e3);
      onUnmounted(() => clearInterval(interval));
    });
    const elapsedTime = computed(() => {
      var _a;
      if (!((_a = props.mostrarConfig) == null ? void 0 : _a.tiempo))
        return "";
      if (!props.product.created_at)
        return "";
      const start = new Date(props.product.created_at).getTime();
      if (Number.isNaN(start))
        return "";
      const end = props.product.status === 4 && props.product.updated_at ? new Date(props.product.updated_at).getTime() : now.value;
      const diffMs = end - start;
      if (diffMs < 0)
        return "";
      const totalSeconds = Math.floor(diffMs / 1e3);
      const minutes = Math.floor(totalSeconds / 60);
      const seconds = totalSeconds % 60;
      if (minutes >= 60) {
        const hours = Math.floor(minutes / 60);
        return `${hours}h ${minutes % 60}m`;
      }
      return `${minutes}m ${seconds}s`;
    });
    return (_ctx, _cache) => {
      var _a, _b;
      const _component_VLoader = _sfc_main$3;
      return openBlock(), createElementBlock("div", _hoisted_1$2, [
        createBaseVNode("div", _hoisted_2$1, [
          createBaseVNode("p", {
            class: normalizeClass([{
              "is-mesa": !Number.isNaN(Number(props.product.mesa))
            }, "is-flex card-header-title m-0 p-2 has-text-primary title is-5"])
          }, [
            createBaseVNode("span", _hoisted_3$1, [
              createTextVNode(toDisplayString(unref(mesaLabel)) + " ", 1),
              _hoisted_4$1,
              createBaseVNode("span", _hoisted_5$1, toDisplayString(props.product.environment), 1)
            ]),
            createBaseVNode("label", _hoisted_6$1, toDisplayString((_a = unref(mesa)) == null ? void 0 : _a.cliente), 1)
          ], 2)
        ]),
        createBaseVNode("div", _hoisted_7$1, [
          createBaseVNode("div", _hoisted_8$1, [
            props.product.preparation_area_name && props.mostrarConfig.area ? (openBlock(), createElementBlock("span", _hoisted_9$1, [
              _hoisted_10$1,
              createBaseVNode("span", null, toDisplayString(props.product.preparation_area_name), 1)
            ])) : createCommentVNode("", true),
            unref(elapsedTime) && props.mostrarConfig.tiempo ? (openBlock(), createElementBlock("span", _hoisted_11$1, [
              _hoisted_12$1,
              createBaseVNode("span", null, toDisplayString(unref(elapsedTime)), 1)
            ])) : createCommentVNode("", true),
            ((_b = unref(mesa)) == null ? void 0 : _b.waiter) && props.mostrarConfig.mozo ? (openBlock(), createElementBlock("span", _hoisted_13$1, [
              _hoisted_14$1,
              createBaseVNode("span", null, toDisplayString(unref(mesa).waiter), 1)
            ])) : createCommentVNode("", true)
          ])
        ]),
        createBaseVNode("div", _hoisted_15$1, [
          createVNode(_component_VLoader, {
            size: "small",
            active: isLoaderActive.value,
            translucent: ""
          }, {
            default: withCtx(() => [
              createBaseVNode("div", _hoisted_16$1, [
                createBaseVNode("div", _hoisted_17$1, [
                  createBaseVNode("div", null, [
                    createBaseVNode("span", null, toDisplayString(props.product.quantity), 1),
                    createBaseVNode("span", _hoisted_18$1, toDisplayString(props.product.name), 1)
                  ]),
                  props.product.note ? (openBlock(), createElementBlock("div", _hoisted_19$1, [
                    createBaseVNode("div", null, [
                      _hoisted_20$1,
                      createTextVNode(" " + toDisplayString(props.product.note), 1)
                    ])
                  ])) : createCommentVNode("", true),
                  props.product.modifiers_applied ? (openBlock(), createElementBlock("div", _hoisted_21$1, [
                    (openBlock(true), createElementBlock(Fragment, null, renderList(props.product.modifiers_applied, (modify, index) => {
                      return openBlock(), createElementBlock("div", {
                        class: "is-flex is-flex-direction-column",
                        key: index
                      }, [
                        createBaseVNode("span", _hoisted_22$1, toDisplayString(modify.groupName), 1),
                        (openBlock(true), createElementBlock(Fragment, null, renderList(modify.items, (item, idx) => {
                          return openBlock(), createElementBlock("div", { key: idx }, [
                            createBaseVNode("span", _hoisted_23$1, toDisplayString(item.name), 1)
                          ]);
                        }), 128))
                      ]);
                    }), 128))
                  ])) : createCommentVNode("", true)
                ])
              ])
            ]),
            _: 1
          }, 8, ["active"])
        ]),
        createBaseVNode("div", _hoisted_24$1, [
          props.product.status < 4 ? (openBlock(), createElementBlock("div", _hoisted_25$1, [
            createBaseVNode("button", {
              type: "button",
              class: normalizeClass(["v-button", [
                "card-footer-item",
                "button",
                "is-light",
                "status-button",
                "m-1",
                statusClass(props.product.status),
                loading.value && "is-loading"
              ]]),
              disabled: loading.value,
              "aria-busy": loading.value,
              onClick: _cache[0] || (_cache[0] = ($event) => changeStatus(props.product.id, props.product))
            }, [
              createTextVNode(toDisplayString(changeStatusMessageName(props.product.status)) + " ", 1),
              _hoisted_27
            ], 10, _hoisted_26$1)
          ])) : createCommentVNode("", true)
        ])
      ]);
    };
  }
});
var CommandRestaurant_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$1 = { class: "page-content kanban-content is-relative" };
const _hoisted_2 = { class: "columns is-multiline is-mobile header-command" };
const _hoisted_3 = { class: "column command-tabs-container" };
const _hoisted_4 = { class: "tabs-wrapper" };
const _hoisted_5 = { class: "tabs-inner" };
const _hoisted_6 = { class: "tabs is-left mb-1" };
const _hoisted_7 = { class: "tabs-list-command tabs-listing-table mb-0" };
const _hoisted_8 = /* @__PURE__ */ createBaseVNode("span", {
  class: "text-center",
  style: { "line-height": "1.2" }
}, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:grid"
  }),
  /* @__PURE__ */ createBaseVNode("br"),
  /* @__PURE__ */ createBaseVNode("span", { style: { "font-size": "0.7rem" } }, "Todo")
], -1);
const _hoisted_9 = [
  _hoisted_8
];
const _hoisted_10 = ["onClick"];
const _hoisted_11 = { class: "label-container" };
const _hoisted_12 = {
  key: 0,
  class: "m-prefix"
};
const _hoisted_13 = /* @__PURE__ */ createBaseVNode("strong", null, "M", -1);
const _hoisted_14 = [
  _hoisted_13
];
const _hoisted_15 = { class: "label-value m-0" };
const _hoisted_16 = { class: "tab-environment" };
const _hoisted_17 = /* @__PURE__ */ createBaseVNode("li", { class: "tab-naver" }, null, -1);
const _hoisted_18 = { class: "is-flex command-area-container" };
const _hoisted_19 = { class: "column is-narrow is-flex is-align-items-center" };
const _hoisted_20 = { class: "filter-resume" };
const _hoisted_21 = /* @__PURE__ */ createBaseVNode("span", { class: "filter-resume-label" }, "\xC1rea", -1);
const _hoisted_22 = {
  key: 0,
  class: "filter-resume-value"
};
const _hoisted_23 = {
  key: 1,
  class: "filter-resume-value"
};
const _hoisted_24 = { class: "column is-narrow is-flex is-align-items-center is-justify-content-end" };
const _hoisted_25 = {
  key: 0,
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.8",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon icon-tabler icons-tabler-outline icon-tabler-adjustments"
};
const _hoisted_26 = /* @__PURE__ */ createStaticVNode('<path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M6 4v4"></path><path d="M6 12v8"></path><path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M12 4v10"></path><path d="M12 18v2"></path><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M18 4v1"></path><path d="M18 9v11"></path>', 10);
const _hoisted_36 = [
  _hoisted_26
];
const _hoisted_37 = {
  key: 1,
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.8",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon icon-tabler icons-tabler-outline icon-tabler-adjustments-off"
};
const _hoisted_38 = /* @__PURE__ */ createStaticVNode('<path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M6 6v2"></path><path d="M6 12v8"></path><path d="M10 16a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M12 4v4m0 4v2"></path><path d="M12 18v2"></path><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M18 4v1"></path><path d="M18 9v5m0 4v2"></path><path d="M3 3l18 18"></path>', 11);
const _hoisted_49 = [
  _hoisted_38
];
const _hoisted_50 = {
  key: 0,
  class: "command-filters-panel pt-0 pl-0"
};
const _hoisted_51 = { class: "columns is-mobile is-vcentered filter-columns" };
const _hoisted_52 = { class: "column is-narrow" };
const _hoisted_53 = ["onClick"];
const _hoisted_54 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-stack-2"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M12 4l-8 4l8 4l8 -4l-8 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 12l8 4l8 -4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 16l8 4l8 -4" })
], -1);
const _hoisted_55 = { class: "filter-text" };
const _hoisted_56 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-down mt-1"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 9l6 6l6 -6" })
], -1);
const _hoisted_57 = ["onClick"];
const _hoisted_58 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-world"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3.6 9h16.8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3.6 15h16.8" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M11.5 3a17 17 0 0 0 0 18" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M12.5 3a17 17 0 0 1 0 18" })
], -1);
const _hoisted_59 = /* @__PURE__ */ createTextVNode(" Todas las \xE1reas ");
const _hoisted_60 = [
  _hoisted_58,
  _hoisted_59
];
const _hoisted_61 = {
  key: 0,
  class: "dropdown-divider"
};
const _hoisted_62 = ["onClick"];
const _hoisted_63 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-tools-kitchen"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 3h8l-1 9h-6l-1 -9" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M7 18h2v3h-2l0 -3" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 3v12h-5c-.023 -3.681 .184 -7.406 5 -12" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M20 15v6h-1v-3" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8 12l0 6" })
], -1);
const _hoisted_64 = { class: "column is-narrow" };
const _hoisted_65 = ["onClick"];
const _hoisted_66 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-eye"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" })
], -1);
const _hoisted_67 = /* @__PURE__ */ createBaseVNode("span", { class: "filter-text" }, "Mostrar", -1);
const _hoisted_68 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-down mt-1"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 9l6 6l6 -6" })
], -1);
const _hoisted_69 = [
  _hoisted_66,
  _hoisted_67,
  _hoisted_68
];
const _hoisted_70 = ["onUpdate:modelValue"];
const _hoisted_71 = { class: "column is-narrow" };
const _hoisted_72 = ["onClick"];
const _hoisted_73 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
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
], -1);
const _hoisted_74 = { class: "filter-text" };
const _hoisted_75 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-down mt-1"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 9l6 6l6 -6" })
], -1);
const _hoisted_76 = ["onClick"];
const _hoisted_77 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-users"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 7a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M16 3.13a4 4 0 0 1 0 7.75" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M21 21v-2a4 4 0 0 0 -3 -3.85" })
], -1);
const _hoisted_78 = /* @__PURE__ */ createTextVNode(" Todos los mozos ");
const _hoisted_79 = [
  _hoisted_77,
  _hoisted_78
];
const _hoisted_80 = {
  key: 0,
  class: "dropdown-divider"
};
const _hoisted_81 = ["onClick"];
const _hoisted_82 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "19",
  height: "19",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.5",
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
], -1);
const _hoisted_83 = { class: "column is-narrow" };
const _hoisted_84 = ["title"];
const _hoisted_85 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-text-decrease"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 19v-10.5a3.5 3.5 0 1 1 7 0v10.5"></path><path d="M4 13h7"></path><path d="M21 12h-6"></path></svg><span class="filter-text">Tama\xF1o de texto</span>', 2);
const _hoisted_87 = /* @__PURE__ */ createStaticVNode('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-tabler icons-tabler-outline icon-tabler-text-increase"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 19v-10.5a3.5 3.5 0 1 1 7 0v10.5"></path><path d="M4 13h7"></path><path d="M18 9v6"></path><path d="M21 12h-6"></path></svg><span class="filter-text">Tama\xF1o de texto</span>', 2);
const _hoisted_89 = { class: "columns is-kanban-wrapper" };
const _hoisted_90 = { class: "collapsed-content" };
const _hoisted_91 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_92 = [
  _hoisted_91
];
const _hoisted_93 = { class: "task-count" };
const _hoisted_94 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Recibidos", -1);
const _hoisted_95 = { class: "expanded-content" };
const _hoisted_96 = { class: "column-title" };
const _hoisted_97 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_98 = { class: "task-count tag is-danger px-2 py-1 is-inline" };
const _hoisted_99 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, " Recibidos", -1);
const _hoisted_100 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_101 = { class: "empty-text" };
const _hoisted_102 = ["data-id"];
const _hoisted_103 = { class: "collapsed-content" };
const _hoisted_104 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_105 = [
  _hoisted_104
];
const _hoisted_106 = { class: "task-count" };
const _hoisted_107 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Preparando", -1);
const _hoisted_108 = { class: "expanded-content" };
const _hoisted_109 = { class: "column-title" };
const _hoisted_110 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_111 = { class: "task-count tag is-warning px-2 py-1 is-inline" };
const _hoisted_112 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, " Preparando ", -1);
const _hoisted_113 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_114 = { class: "empty-text" };
const _hoisted_115 = ["data-id"];
const _hoisted_116 = { class: "collapsed-content" };
const _hoisted_117 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_118 = [
  _hoisted_117
];
const _hoisted_119 = { class: "task-count" };
const _hoisted_120 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Por entregar", -1);
const _hoisted_121 = { class: "expanded-content" };
const _hoisted_122 = { class: "column-title" };
const _hoisted_123 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_124 = { class: "task-count tag is-primary px-2 py-1 is-inline" };
const _hoisted_125 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, " Por entregar ", -1);
const _hoisted_126 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_127 = { class: "empty-text" };
const _hoisted_128 = ["data-id"];
const _hoisted_129 = { class: "collapsed-content" };
const _hoisted_130 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_131 = [
  _hoisted_130
];
const _hoisted_132 = { class: "task-count" };
const _hoisted_133 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Entregados", -1);
const _hoisted_134 = { class: "expanded-content" };
const _hoisted_135 = { class: "column-title" };
const _hoisted_136 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_137 = { class: "task-count tag is-secondary px-2 py-1 is-inline" };
const _hoisted_138 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, " Entregados ", -1);
const _hoisted_139 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_140 = { class: "empty-text" };
const _hoisted_141 = ["data-id"];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const userSession = useUserSession();
    const roleCurrent = Number(userSession.userRole);
    const role = ROLES.find((role2) => role2.id === roleCurrent);
    const disableBar = ref(true);
    const disableKitchen = ref(true);
    useMesaSession().mesas;
    let intervalId = void 0;
    const selectArea = (area) => {
      selectedPreparationArea.value = area;
    };
    const defaultView = () => {
      let response = COMMAND_KITCHEN;
      if (role && role.id != 3) {
        if (role.code == "BAR") {
          response = COMMAND_BAR;
          disableBar.value = false;
        }
        if (role.code == "KIT") {
          response = COMMAND_KITCHEN;
          disableKitchen.value = false;
        }
      }
      if ((role == null ? void 0 : role.id) == 3) {
        disableBar.value = false;
        disableKitchen.value = false;
      }
      return response;
    };
    let currentView = ref(defaultView());
    const isColumnPreparationCollapsed = ref(false);
    const isColumnDispatchedCollapsed = ref(false);
    const isColumnToDeliverCollapsed = ref(false);
    const isColumnDeliveredCollapsed = ref(false);
    const isLoaderActive = ref(false);
    const loaderCounter = ref(0);
    const emptyText = ref("No existe registro actualmente");
    useCommandSession();
    ref([]);
    let productsStatusReceived = ref([]);
    let productsStatusProcessing = ref([]);
    let productsStatusToDeliver = ref([]);
    let productsStatusDelivered = ref([]);
    const companySession = useCompanySession();
    companySession.configuration;
    const selectedTableId = ref(0);
    const mesasSession = useMesaSession();
    const mesasDisabled = mesasSession.getMesasNotAvailable();
    const isCheckLetterZise = ref(false);
    const getProductsApi = async (showLoader = false) => {
      var _a;
      if (showLoader) {
        loaderCounter.value += 1;
        isLoaderActive.value = true;
      }
      try {
        let id = (_a = selectedTableId.value) != null ? _a : 0;
        const response = await provideApi().get(`restaurant/command-status/items/${id}`);
        const data = response.data;
        if (data.success) {
          const addWaiterToProducts = (products) => {
            return products.map((p) => {
              const mesa = mesasSession.getMesas2().find((m) => m.id === p.mesa_id);
              return __spreadProps(__spreadValues({}, p), {
                waiter: (mesa == null ? void 0 : mesa.waiter) || null
              });
            });
          };
          productsStatusReceived.value = addWaiterToProducts(data.data.productsStatusReceived);
          productsStatusProcessing.value = addWaiterToProducts(data.data.productsStatusProcessing);
          productsStatusToDeliver.value = addWaiterToProducts(data.data.productsStatusToDeliver);
          productsStatusDelivered.value = addWaiterToProducts(data.data.productsStatusDelivered);
        }
      } catch (error) {
        console.error("Error data:", error);
      } finally {
        if (showLoader) {
          loaderCounter.value = Math.max(0, loaderCounter.value - 1);
          isLoaderActive.value = loaderCounter.value > 0;
        }
      }
    };
    const selectTable = (id) => {
      selectedTableId.value = id;
      getProductsApi(true);
    };
    const isNumeric = (value) => {
      return !isNaN(parseFloat(value)) && isFinite(value);
    };
    const changeView = (view) => {
      currentView.value = view;
      getProductsApi(true);
    };
    const checkedLetterSize = () => {
      isCheckLetterZise.value = !isCheckLetterZise.value;
      let isChecked = isCheckLetterZise.value ? 1 : 0;
      userSession.setIsCheckLetterZise(isChecked);
    };
    const STORAGE_KEY_PREPARATION_AREA = "command_preparation_area";
    const selectedPreparationArea = ref(localStorage.getItem(STORAGE_KEY_PREPARATION_AREA) || "all");
    watch(selectedPreparationArea, (value) => {
      localStorage.setItem(STORAGE_KEY_PREPARATION_AREA, value);
    });
    const STORAGE_KEY_WAITER = "command_waiter_filter";
    const selectedWaiter = ref(localStorage.getItem(STORAGE_KEY_WAITER) || "all");
    watch(selectedWaiter, (value) => {
      localStorage.setItem(STORAGE_KEY_WAITER, value);
    });
    const selectWaiter = (waiter) => {
      selectedWaiter.value = waiter;
    };
    const allProducts = computed(() => [
      ...productsStatusReceived.value,
      ...productsStatusProcessing.value,
      ...productsStatusToDeliver.value,
      ...productsStatusDelivered.value
    ]);
    const preparationAreas = computed(() => {
      const set = new Set();
      allProducts.value.forEach((p) => {
        if (p.preparation_area_name && p.preparation_area_name.trim() !== "") {
          set.add(p.preparation_area_name.trim());
        }
      });
      return Array.from(set);
    });
    const waiters = computed(() => {
      const set = new Set();
      allProducts.value.forEach((p) => {
        if (p.waiter && p.waiter.trim() !== "") {
          set.add(p.waiter.trim());
        }
      });
      return Array.from(set);
    });
    const filterProducts = (items) => {
      let filtered = items;
      if (selectedPreparationArea.value !== "all") {
        filtered = filtered.filter((p) => p.preparation_area_name === selectedPreparationArea.value);
      }
      if (selectedWaiter.value !== "all") {
        filtered = filtered.filter((p) => p.waiter === selectedWaiter.value);
      }
      return filtered;
    };
    const productsReceivedFiltered = computed(() => filterProducts(productsStatusReceived.value));
    const productsProcessingFiltered = computed(() => filterProducts(productsStatusProcessing.value));
    const productsToDeliverFiltered = computed(() => filterProducts(productsStatusToDeliver.value));
    const productsDeliveredFiltered = computed(() => filterProducts(productsStatusDelivered.value));
    const STORAGE_KEY_VISUALIZACION_PEDIDOS = "restaurant_visualizacion_pedidos";
    const visualizacionPedidos = ref({
      tiempo: {
        label: "Tiempo",
        activo: true
      },
      area: {
        label: "\xC1rea",
        activo: true
      },
      ambiente: {
        label: "Ambiente",
        activo: false
      },
      mozo: {
        label: "Mozo",
        activo: true
      }
    });
    const mostrarPedidosConfig = computed(() => {
      return {
        tiempo: visualizacionPedidos.value.tiempo.activo,
        area: visualizacionPedidos.value.area.activo,
        ambiente: visualizacionPedidos.value.ambiente.activo,
        mozo: visualizacionPedidos.value.mozo.activo
      };
    });
    const showAdvancedFilters = ref(false);
    const syncMesasEnviroments = async () => {
      await MesaService.syncMesasAndEnvironments();
    };
    onMounted(() => {
      changeView(currentView.value);
      getProductsApi(true);
      intervalId = window.setInterval(() => getProductsApi(false), 5e3);
      intervalId = window.setInterval(syncMesasEnviroments, 5e3);
      isCheckLetterZise.value = userSession.isCheckLetterZise ? true : false;
      const raw = localStorage.getItem(STORAGE_KEY_VISUALIZACION_PEDIDOS);
      if (raw) {
        const saved = JSON.parse(raw);
        visualizacionPedidos.value = {
          tiempo: saved.tiempo || { label: "Tiempo", activo: true },
          area: saved.area || { label: "\xC1rea", activo: true },
          ambiente: saved.ambiente || { label: "Ambiente", activo: false },
          mozo: saved.mozo || { label: "Mozo", activo: true }
        };
      }
    });
    watch(visualizacionPedidos, (val) => {
      localStorage.setItem(STORAGE_KEY_VISUALIZACION_PEDIDOS, JSON.stringify(val));
    }, { deep: true });
    onUnmounted(() => {
      if (intervalId !== void 0) {
        clearInterval(intervalId);
      }
    });
    return (_ctx, _cache) => {
      const _component_VDropdown = __unplugin_components_0;
      const _component_KanbanDropdown = _sfc_main$4;
      const _component_CommandProduct = _sfc_main$2;
      const _component_VLoader = _sfc_main$3;
      return openBlock(), createElementBlock("div", _hoisted_1$1, [
        createBaseVNode("div", _hoisted_2, [
          createBaseVNode("div", _hoisted_3, [
            createBaseVNode("div", _hoisted_4, [
              createBaseVNode("div", _hoisted_5, [
                createBaseVNode("div", _hoisted_6, [
                  createBaseVNode("ul", _hoisted_7, [
                    createBaseVNode("li", {
                      class: normalizeClass({ "is-active": selectedTableId.value === 0 })
                    }, [
                      createBaseVNode("a", {
                        onClick: _cache[0] || (_cache[0] = ($event) => selectTable(0))
                      }, _hoisted_9)
                    ], 2),
                    (openBlock(true), createElementBlock(Fragment, null, renderList(unref(mesasDisabled), (tab, key) => {
                      return openBlock(), createElementBlock("li", {
                        key,
                        class: normalizeClass([selectedTableId.value == tab.id && "is-active"])
                      }, [
                        createBaseVNode("a", {
                          class: "list-table",
                          onClick: ($event) => selectTable(tab.id)
                        }, [
                          createBaseVNode("span", _hoisted_11, [
                            createBaseVNode("span", null, [
                              isNumeric(tab.label) ? (openBlock(), createElementBlock("span", _hoisted_12, _hoisted_14)) : createCommentVNode("", true),
                              createBaseVNode("small", _hoisted_15, [
                                createBaseVNode("strong", null, toDisplayString(tab.label), 1)
                              ])
                            ]),
                            createBaseVNode("span", _hoisted_16, toDisplayString(tab.environment), 1)
                          ])
                        ], 8, _hoisted_10)
                      ], 2);
                    }), 128)),
                    _hoisted_17
                  ])
                ])
              ])
            ])
          ]),
          createBaseVNode("div", _hoisted_18, [
            createBaseVNode("div", _hoisted_19, [
              createBaseVNode("div", _hoisted_20, [
                _hoisted_21,
                selectedPreparationArea.value !== "all" ? (openBlock(), createElementBlock("span", _hoisted_22, toDisplayString(selectedPreparationArea.value), 1)) : (openBlock(), createElementBlock("span", _hoisted_23, " Todas las \xE1reas "))
              ])
            ]),
            createBaseVNode("div", _hoisted_24, [
              createBaseVNode("div", {
                class: "filter-general",
                onClick: _cache[1] || (_cache[1] = ($event) => showAdvancedFilters.value = !showAdvancedFilters.value)
              }, [
                showAdvancedFilters.value ? (openBlock(), createElementBlock("svg", _hoisted_25, _hoisted_36)) : (openBlock(), createElementBlock("svg", _hoisted_37, _hoisted_49))
              ])
            ])
          ])
        ]),
        showAdvancedFilters.value ? (openBlock(), createElementBlock("div", _hoisted_50, [
          createBaseVNode("div", _hoisted_51, [
            createBaseVNode("div", _hoisted_52, [
              createVNode(_component_VDropdown, {
                color: "primary",
                spaced: "",
                modern: ""
              }, {
                button: withCtx(({ toggle }) => [
                  createBaseVNode("div", {
                    class: "filter-container",
                    onClick: toggle
                  }, [
                    _hoisted_54,
                    createBaseVNode("span", _hoisted_55, toDisplayString(selectedPreparationArea.value === "all" ? "Todas las \xE1reas" : selectedPreparationArea.value), 1),
                    _hoisted_56
                  ], 8, _hoisted_53)
                ]),
                content: withCtx(({ close }) => [
                  createBaseVNode("a", {
                    class: normalizeClass(["dropdown-item", { "is-active": selectedPreparationArea.value === "all" }]),
                    onClick: ($event) => (selectArea("all"), close())
                  }, _hoisted_60, 10, _hoisted_57),
                  unref(preparationAreas).length ? (openBlock(), createElementBlock("hr", _hoisted_61)) : createCommentVNode("", true),
                  (openBlock(true), createElementBlock(Fragment, null, renderList(unref(preparationAreas), (area) => {
                    return openBlock(), createElementBlock("a", {
                      key: area,
                      class: normalizeClass(["dropdown-item", { "is-active": selectedPreparationArea.value === area }]),
                      onClick: ($event) => (selectArea(area), close())
                    }, [
                      _hoisted_63,
                      createTextVNode(" " + toDisplayString(area), 1)
                    ], 10, _hoisted_62);
                  }), 128))
                ]),
                _: 1
              })
            ]),
            createBaseVNode("div", _hoisted_64, [
              createVNode(_component_VDropdown, {
                color: "primary",
                spaced: "",
                modern: ""
              }, {
                button: withCtx(({ toggle }) => [
                  createBaseVNode("div", {
                    class: "filter-container",
                    onClick: toggle
                  }, _hoisted_69, 8, _hoisted_65)
                ]),
                content: withCtx(() => [
                  createBaseVNode("div", null, [
                    (openBlock(true), createElementBlock(Fragment, null, renderList(visualizacionPedidos.value, (opcion, key) => {
                      return openBlock(), createElementBlock("label", {
                        key,
                        class: "dropdown-item is-flex is-align-items-center"
                      }, [
                        withDirectives(createBaseVNode("input", {
                          "onUpdate:modelValue": ($event) => opcion.activo = $event,
                          type: "checkbox",
                          class: "mr-2"
                        }, null, 8, _hoisted_70), [
                          [vModelCheckbox, opcion.activo]
                        ]),
                        createBaseVNode("span", null, toDisplayString(opcion.label), 1)
                      ]);
                    }), 128))
                  ])
                ]),
                _: 1
              })
            ]),
            createBaseVNode("div", _hoisted_71, [
              createVNode(_component_VDropdown, {
                color: "primary",
                spaced: "",
                modern: ""
              }, {
                button: withCtx(({ toggle }) => [
                  createBaseVNode("div", {
                    class: "filter-container",
                    onClick: toggle
                  }, [
                    _hoisted_73,
                    createBaseVNode("span", _hoisted_74, toDisplayString(selectedWaiter.value === "all" ? "Todos los mozos" : selectedWaiter.value), 1),
                    _hoisted_75
                  ], 8, _hoisted_72)
                ]),
                content: withCtx(({ close }) => [
                  createBaseVNode("a", {
                    class: normalizeClass(["dropdown-item", { "is-active": selectedWaiter.value === "all" }]),
                    onClick: ($event) => (selectWaiter("all"), close())
                  }, _hoisted_79, 10, _hoisted_76),
                  unref(waiters).length > 0 ? (openBlock(), createElementBlock("hr", _hoisted_80)) : createCommentVNode("", true),
                  (openBlock(true), createElementBlock(Fragment, null, renderList(unref(waiters), (waiter) => {
                    return openBlock(), createElementBlock("a", {
                      key: waiter,
                      class: normalizeClass(["dropdown-item", { "is-active": selectedWaiter.value === waiter }]),
                      onClick: ($event) => (selectWaiter(waiter), close())
                    }, [
                      _hoisted_82,
                      createTextVNode(" " + toDisplayString(waiter), 1)
                    ], 10, _hoisted_81);
                  }), 128))
                ]),
                _: 1
              })
            ]),
            createBaseVNode("div", _hoisted_83, [
              createBaseVNode("div", {
                class: "filter-container",
                title: isCheckLetterZise.value ? "Disminuir tama\xF1o del texto" : "Aumentar tama\xF1o del texto",
                onClick: _cache[2] || (_cache[2] = ($event) => checkedLetterSize())
              }, [
                isCheckLetterZise.value ? (openBlock(), createElementBlock(Fragment, { key: 0 }, [
                  _hoisted_85
                ], 64)) : (openBlock(), createElementBlock(Fragment, { key: 1 }, [
                  _hoisted_87
                ], 64))
              ], 8, _hoisted_84)
            ])
          ])
        ])) : createCommentVNode("", true),
        createBaseVNode("div", _hoisted_89, [
          createBaseVNode("div", {
            class: normalizeClass(["column", [
              isColumnPreparationCollapsed.value && "is-1 is-mini",
              !isColumnPreparationCollapsed.value && "is-one-quarter"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column state-1", [isColumnPreparationCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_90, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[3] || (_cache[3] = ($event) => isColumnPreparationCollapsed.value = false)
                    }, _hoisted_92),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_93, toDisplayString(unref(productsStatusReceived).length), 1)
                    ]),
                    _hoisted_94
                  ]),
                  createBaseVNode("div", _hoisted_95, [
                    createBaseVNode("div", _hoisted_96, [
                      _hoisted_97,
                      createBaseVNode("h3", null, [
                        createBaseVNode("span", _hoisted_98, toDisplayString(unref(productsStatusReceived).length), 1),
                        _hoisted_99
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[4] || (_cache[4] = ($event) => isColumnPreparationCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "preparation"
                    }, [
                      unref(productsReceivedFiltered).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_100, [
                        createBaseVNode("p", _hoisted_101, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(productsReceivedFiltered), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          "data-id": item.id,
                          class: normalizeClass(["kanban-card is-new", [
                            isCheckLetterZise.value && "is-size-3 has-text-weight-bold"
                          ]])
                        }, [
                          createVNode(_component_CommandProduct, {
                            product: item,
                            view: unref(currentView),
                            "mostrar-config": unref(mostrarPedidosConfig),
                            onSetProducts: getProductsApi
                          }, null, 8, ["product", "view", "mostrar-config"])
                        ], 10, _hoisted_102);
                      }), 128))
                    ], 512)
                  ])
                ], 2)
              ]),
              _: 1
            }, 8, ["active"])
          ], 2),
          createBaseVNode("div", {
            class: normalizeClass(["column", [
              isColumnDispatchedCollapsed.value && "is-1 is-mini",
              !isColumnDispatchedCollapsed.value && "is-one-quarter"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column state-2", [isColumnDispatchedCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_103, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[5] || (_cache[5] = ($event) => isColumnDispatchedCollapsed.value = false)
                    }, _hoisted_105),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_106, toDisplayString(unref(productsStatusProcessing).length), 1)
                    ]),
                    _hoisted_107
                  ]),
                  createBaseVNode("div", _hoisted_108, [
                    createBaseVNode("div", _hoisted_109, [
                      _hoisted_110,
                      createBaseVNode("h3", null, [
                        createBaseVNode("span", _hoisted_111, toDisplayString(unref(productsStatusProcessing).length), 1),
                        _hoisted_112
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[6] || (_cache[6] = ($event) => isColumnDispatchedCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "preparation"
                    }, [
                      unref(productsProcessingFiltered).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_113, [
                        createBaseVNode("p", _hoisted_114, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(productsProcessingFiltered), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          "data-id": item.id,
                          class: normalizeClass(["kanban-card is-new", [
                            isCheckLetterZise.value && "is-size-3 has-text-weight-bold"
                          ]])
                        }, [
                          createVNode(_component_CommandProduct, {
                            product: item,
                            view: unref(currentView),
                            "mostrar-config": unref(mostrarPedidosConfig),
                            onSetProducts: getProductsApi
                          }, null, 8, ["product", "view", "mostrar-config"])
                        ], 10, _hoisted_115);
                      }), 128))
                    ], 512)
                  ])
                ], 2)
              ]),
              _: 1
            }, 8, ["active"])
          ], 2),
          createBaseVNode("div", {
            class: normalizeClass(["column", [
              isColumnToDeliverCollapsed.value && "is-1 is-mini",
              !isColumnToDeliverCollapsed.value && "is-one-quarter"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column state-3", [isColumnToDeliverCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_116, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[7] || (_cache[7] = ($event) => isColumnToDeliverCollapsed.value = false)
                    }, _hoisted_118),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_119, toDisplayString(unref(productsStatusToDeliver).length), 1)
                    ]),
                    _hoisted_120
                  ]),
                  createBaseVNode("div", _hoisted_121, [
                    createBaseVNode("div", _hoisted_122, [
                      _hoisted_123,
                      createBaseVNode("h3", null, [
                        createBaseVNode("span", _hoisted_124, toDisplayString(unref(productsStatusToDeliver).length), 1),
                        _hoisted_125
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[8] || (_cache[8] = ($event) => isColumnToDeliverCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "preparation"
                    }, [
                      unref(productsToDeliverFiltered).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_126, [
                        createBaseVNode("p", _hoisted_127, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(productsToDeliverFiltered), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          "data-id": item.id,
                          class: normalizeClass(["kanban-card is-new", [
                            isCheckLetterZise.value && "is-size-3 has-text-weight-bold"
                          ]])
                        }, [
                          createVNode(_component_CommandProduct, {
                            product: item,
                            view: unref(currentView),
                            "mostrar-config": unref(mostrarPedidosConfig),
                            onSetProducts: getProductsApi
                          }, null, 8, ["product", "view", "mostrar-config"])
                        ], 10, _hoisted_128);
                      }), 128))
                    ], 512)
                  ])
                ], 2)
              ]),
              _: 1
            }, 8, ["active"])
          ], 2),
          createBaseVNode("div", {
            class: normalizeClass(["column", [
              isColumnDeliveredCollapsed.value && "is-1 is-mini",
              !isColumnDeliveredCollapsed.value && "is-one-quarter"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column state-4", [isColumnDeliveredCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_129, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[9] || (_cache[9] = ($event) => isColumnDeliveredCollapsed.value = false)
                    }, _hoisted_131),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_132, toDisplayString(unref(productsStatusDelivered).length), 1)
                    ]),
                    _hoisted_133
                  ]),
                  createBaseVNode("div", _hoisted_134, [
                    createBaseVNode("div", _hoisted_135, [
                      _hoisted_136,
                      createBaseVNode("h3", null, [
                        createBaseVNode("span", _hoisted_137, toDisplayString(unref(productsStatusDelivered).length), 1),
                        _hoisted_138
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[10] || (_cache[10] = ($event) => isColumnDeliveredCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "preparation"
                    }, [
                      unref(productsDeliveredFiltered).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_139, [
                        createBaseVNode("p", _hoisted_140, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(productsDeliveredFiltered), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          "data-id": item.id,
                          class: normalizeClass(["kanban-card is-new", [
                            isCheckLetterZise.value && "is-size-3 has-text-weight-bold"
                          ]])
                        }, [
                          createVNode(_component_CommandProduct, {
                            product: item,
                            view: unref(currentView),
                            "mostrar-config": unref(mostrarPedidosConfig),
                            onSetProducts: getProductsApi
                          }, null, 8, ["product", "view", "mostrar-config"])
                        ], 10, _hoisted_141);
                      }), 128))
                    ], 512)
                  ])
                ], 2)
              ]),
              _: 1
            }, 8, ["active"])
          ], 2)
        ])
      ]);
    };
  }
});
const _hoisted_1 = { class: "page-content-inner" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    pageTitle.value = "Pedidos";
    useHead({
      title: "Pedidos"
    });
    return (_ctx, _cache) => {
      const _component_CommandRestaurant = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_CommandRestaurant)
      ]);
    };
  }
});
export { _sfc_main as default };
