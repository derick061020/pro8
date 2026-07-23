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
import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$6 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$7 } from "./VModal.9533b2d3.js";
import { b as defineComponent, t as reactive, L as watch, x as resolveComponent, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, a6 as withDirectives, a7 as vModelText, Z as unref, w as createVNode, D as createTextVNode, r as ref, a as computed, g as createElementBlock, Y as normalizeClass, F as Fragment, A as renderList, aa as normalizeStyle, C as createCommentVNode, z as toDisplayString, y as renderSlot, T as Transition, as as withKeys, I as withModifiers, G as pushScopeId, H as popScopeId, o as onMounted, ag as onBeforeUnmount, aq as vModelCheckbox, at as TransitionGroup, al as vModelSelect } from "./vendor.05b8b5b5.js";
import { a as useMesaSession, f as MESA_SHAPES, D as DEFAULT_ENVIRONMENT, c as MesaService, g as useProductSession } from "./masterService.8a758d8d.js";
import { p as provideApi, u as useUserSession, c as useNotyf } from "./index.2bb13d6f.js";
import { _ as _sfc_main$8 } from "./VIcon.f7f680f8.js";
import { b as _sfc_main$9, _ as _sfc_main$a, d as dayjs, c as _sfc_main$b, e as _sfc_main$c, s as sectionsData, P as ProductModifiersDialog, f as _imports_0, g as _sfc_main$g, h as __unplugin_components_7, i as __unplugin_components_8 } from "./ProductModifiersDialog.637fd331.js";
import { _ as _sfc_main$e } from "./CategoriesNavBar.5044304a.js";
import { _ as _sfc_main$f } from "./VField.152a42ff.js";
import { _ as _sfc_main$h } from "./VAvatar.0f0bd550.js";
import { R as RestaurantService } from "./restaurantService.f8e320da.js";
import { a as _sfc_main$d } from "./VIconButton.dcb74fc8.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
const _hoisted_1$5 = { class: "modal-form" };
const _hoisted_2$5 = { class: "field" };
const _hoisted_3$5 = /* @__PURE__ */ createBaseVNode("label", null, "Nombre *", -1);
const _hoisted_4$5 = { class: "control" };
const _hoisted_5$4 = { class: "field" };
const _hoisted_6$4 = /* @__PURE__ */ createBaseVNode("label", null, "Forma *", -1);
const _hoisted_7$4 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_8$4 = /* @__PURE__ */ createTextVNode("Guardar");
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true },
    model: { type: null, required: true }
  },
  emits: ["close", "save"],
  setup(__props, { emit }) {
    const props = __props;
    const mesasSession = reactive(useMesaSession());
    let stateFormMesa = reactive({
      id: props.model.id,
      label: props.model.label,
      shape: props.model.shape,
      environment: props.model.environment
    });
    const save = () => {
      const { id, label, shape, environment } = stateFormMesa;
      mesasSession.setFormLabelMesa(id, label, shape, environment ? environment : DEFAULT_ENVIRONMENT);
      saveLabelMesa();
      emit("close");
    };
    const saveLabelMesa = async () => {
      const payload = {
        id: stateFormMesa.id,
        label: stateFormMesa.label,
        shape: stateFormMesa.shape,
        enviroment: stateFormMesa
      };
      try {
        const response = await provideApi().post("restaurant/label-table/save", payload);
        const data = response.data;
        if (data.success) {
        }
      } catch (error) {
        console.error("Error data:", error);
      }
    };
    watch(() => props.open, (newValue, oldValue) => {
      if (newValue) {
        const { id, label, shape, environment } = props.model;
        stateFormMesa.id = id;
        stateFormMesa.label = label;
        stateFormMesa.shape = shape;
        stateFormMesa.environment = environment;
      }
    });
    return (_ctx, _cache) => {
      const _component_Multiselect = resolveComponent("Multiselect");
      const _component_VControl = __unplugin_components_1;
      const _component_VButton = _sfc_main$6;
      const _component_VModal = _sfc_main$7;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Editar Mesa",
        size: "small",
        actions: "right",
        onClose: _cache[3] || (_cache[3] = ($event) => emit("close"))
      }, {
        content: withCtx(() => [
          createBaseVNode("form", _hoisted_1$5, [
            createBaseVNode("div", _hoisted_2$5, [
              _hoisted_3$5,
              createBaseVNode("div", _hoisted_4$5, [
                withDirectives(createBaseVNode("input", {
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => unref(stateFormMesa).label = $event),
                  type: "text",
                  class: "input",
                  placeholder: "Nombre"
                }, null, 512), [
                  [vModelText, unref(stateFormMesa).label]
                ])
              ])
            ]),
            createBaseVNode("div", _hoisted_5$4, [
              _hoisted_6$4,
              createVNode(_component_VControl, null, {
                default: withCtx(() => [
                  createVNode(_component_Multiselect, {
                    modelValue: unref(stateFormMesa).shape,
                    "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => unref(stateFormMesa).shape = $event),
                    placeholder: "Forma",
                    options: unref(MESA_SHAPES)
                  }, null, 8, ["modelValue", "options"])
                ]),
                _: 1
              })
            ]),
            _hoisted_7$4
          ])
        ]),
        action: withCtx(() => [
          createVNode(_component_VButton, {
            color: "primary",
            raised: "",
            onClick: _cache[2] || (_cache[2] = ($event) => save())
          }, {
            default: withCtx(() => [
              _hoisted_8$4
            ]),
            _: 1
          })
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
const _hoisted_1$4 = { class: "tabs-inner" };
const _hoisted_2$4 = ["onClick"];
const _hoisted_3$4 = {
  key: 0,
  class: "tab-naver"
};
const _hoisted_4$4 = { class: "tab-content is-active" };
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  props: {
    tabs: { type: Array, required: true },
    selected: { type: String, required: false, default: void 0 },
    type: { type: String, required: false, default: void 0 },
    align: { type: String, required: false, default: void 0 },
    slider: { type: Boolean, required: false },
    slow: { type: Boolean, required: false }
  },
  emits: ["update:selected"],
  setup(__props, { emit }) {
    const props = __props;
    const activeValue = ref(props.selected);
    watch(() => props.selected, (newValue) => {
      activeValue.value = newValue;
    });
    const isFirstRight = (index) => {
      const firstRightIndex = props.tabs.findIndex((t) => t.alignRight);
      return index === firstRightIndex;
    };
    const sliderClass = computed(() => {
      if (!props.slider) {
        return "";
      }
      if (props.type === "rounded") {
        if (props.tabs.length === 3) {
          return "is-triple-slider";
        }
        if (props.tabs.length === 2) {
          return "is-slider";
        }
        return "";
      }
      if (!props.type) {
        if (props.tabs.length === 3) {
          return "is-squared is-triple-slider";
        }
        if (props.tabs.length === 2) {
          return "is-squared is-slider";
        }
      }
      return "";
    });
    return (_ctx, _cache) => {
      const _component_VIcon = _sfc_main$8;
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["tabs-wrapper", [unref(sliderClass)]])
      }, [
        createBaseVNode("div", _hoisted_1$4, [
          createBaseVNode("div", {
            class: normalizeClass(["tabs", [
              props.align === "centered" && "is-centered",
              props.align === "right" && "is-right",
              props.type === "rounded" && !props.slider && "is-toggle is-toggle-rounded",
              props.type === "toggle" && "is-toggle",
              props.type === "boxed" && "is-boxed"
            ]])
          }, [
            createBaseVNode("ul", null, [
              (openBlock(true), createElementBlock(Fragment, null, renderList(__props.tabs, (tab, key) => {
                return openBlock(), createElementBlock("li", {
                  key,
                  class: normalizeClass([activeValue.value === tab.value && "is-active"]),
                  style: normalizeStyle(tab.alignRight && isFirstRight(key) ? { marginLeft: "auto" } : {})
                }, [
                  createBaseVNode("a", {
                    onClick: () => {
                      activeValue.value = tab.value;
                      emit("update:selected", tab.value);
                    }
                  }, [
                    tab.icon ? (openBlock(), createBlock(_component_VIcon, {
                      key: 0,
                      icon: tab.icon
                    }, null, 8, ["icon"])) : createCommentVNode("", true),
                    createBaseVNode("span", null, toDisplayString(tab.label), 1)
                  ], 8, _hoisted_2$4)
                ], 6);
              }), 128)),
              unref(sliderClass) ? (openBlock(), createElementBlock("li", _hoisted_3$4)) : createCommentVNode("", true)
            ])
          ], 2)
        ]),
        createBaseVNode("div", _hoisted_4$4, [
          createVNode(Transition, {
            name: props.slow ? "fade-slow" : "fade-fast",
            mode: "out-in"
          }, {
            default: withCtx(() => [
              renderSlot(_ctx.$slots, "tab", { activeValue: activeValue.value })
            ]),
            _: 3
          }, 8, ["name"])
        ])
      ], 2);
    };
  }
});
var DivideSaleDialog_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId$1 = (n) => (pushScopeId("data-v-70b77720"), n = n(), popScopeId(), n);
const _hoisted_1$3 = { class: "divide-sale-container" };
const _hoisted_2$3 = { class: "column p-0" };
const _hoisted_3$3 = { class: "message status-container is-primary p-0" };
const _hoisted_4$3 = { class: "message-body p-2 is-flex is-align-items-center is-justify-content-space-between" };
const _hoisted_5$3 = { class: "is-flex is-flex-direction-column" };
const _hoisted_6$3 = { class: "total-value" };
const _hoisted_7$3 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "total-label" }, "Mesa", -1));
const _hoisted_8$3 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-layout-grid icon-transparent-stroke"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" })
  ])
], -1));
const _hoisted_9$3 = { class: "column p-0" };
const _hoisted_10$3 = { class: "message status-container is-total p-0" };
const _hoisted_11$3 = { class: "message-body p-2 is-flex is-align-items-center is-justify-content-space-between" };
const _hoisted_12$3 = { class: "is-flex is-flex-direction-column" };
const _hoisted_13$3 = { class: "total-value" };
const _hoisted_14$3 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "total-label" }, "Total", -1));
const _hoisted_15$3 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-calculator icon-transparent-stroke"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M4 3m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 7m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 14l0 .01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 14l0 .01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M16 14l0 .01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M8 17l0 .01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 17l0 .01" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M16 17l0 .01" })
  ])
], -1));
const _hoisted_16$3 = { class: "column p-0" };
const _hoisted_17$3 = { class: "message status-container is-warning p-0" };
const _hoisted_18$3 = { class: "message-body p-2 is-flex is-align-items-center is-justify-content-space-between" };
const _hoisted_19$3 = { class: "is-flex is-flex-direction-column" };
const _hoisted_20$2 = { class: "total-value" };
const _hoisted_21$2 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "total-label" }, "Pendiente", -1));
const _hoisted_22$2 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-clock-hour-4 icon-transparent-stroke"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 12l3 2" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 7v5" })
  ])
], -1));
const _hoisted_23$2 = { class: "column p-0" };
const _hoisted_24$1 = { class: "message status-container is-success p-0" };
const _hoisted_25$1 = { class: "message-body p-2 is-flex is-align-items-center is-justify-content-space-between" };
const _hoisted_26$1 = { class: "is-flex is-flex-direction-column" };
const _hoisted_27$1 = { class: "total-value" };
const _hoisted_28$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "total-label" }, "Pagado", -1));
const _hoisted_29$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "24",
    height: "24",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-cash icon-transparent-stroke"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" })
  ])
], -1));
const _hoisted_30$1 = { class: "columns is-multiline" };
const _hoisted_31$1 = { class: "column is-6 container-card" };
const _hoisted_32$1 = { class: "" };
const _hoisted_33$1 = { class: "card-header px-3" };
const _hoisted_34$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "title" }, "Paso 1 - Seleccionar Productos", -1));
const _hoisted_35$1 = { class: "subtitle has-text-grey mt-1" };
const _hoisted_36$1 = { class: "products-scrollable" };
const _hoisted_37$1 = { class: "flex-list-inner" };
const _hoisted_38$1 = ["onClick"];
const _hoisted_39$1 = { class: "flex-table-cell is-grow" };
const _hoisted_40$1 = { class: "item-name dark-inverted" };
const _hoisted_41$1 = { class: "item-meta" };
const _hoisted_42$1 = {
  key: 0,
  class: "has-text-grey ml-2"
};
const _hoisted_43$1 = {
  key: 0,
  class: "modifiers-compact"
};
const _hoisted_44$1 = { class: "flex-table-cell has-text-right" };
const _hoisted_45$1 = { class: "dark-inverted is-weight-700 product-price" };
const _hoisted_46$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "title" }, "Paso 2 - Asignar Persona", -1));
const _hoisted_47$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-circle-plus",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12h6" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M12 9v6" })
], -1));
const _hoisted_48$1 = /* @__PURE__ */ createTextVNode(" A\xF1adir ");
const _hoisted_49$1 = [
  _hoisted_47$1,
  _hoisted_48$1
];
const _hoisted_50$1 = { class: "card-footer" };
const _hoisted_51$1 = { class: "buttons" };
const _hoisted_52$1 = ["onClick", "onKeydown"];
const _hoisted_53$1 = {
  class: "person-pill-left",
  role: "button",
  tabindex: "0"
};
const _hoisted_54$1 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "18",
  height: "18",
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
const _hoisted_55$1 = ["onKeydown", "onBlur"];
const _hoisted_56$1 = {
  key: 1,
  class: "person-pill-name"
};
const _hoisted_57$1 = { class: "person-pill-actions" };
const _hoisted_58 = { class: "column is-6 container-card" };
const _hoisted_59 = { class: "" };
const _hoisted_60 = { class: "card-header px-3" };
const _hoisted_61 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("span", { class: "title" }, "Comensales", -1));
const _hoisted_62 = { class: "subtitle has-text-grey mt-1" };
const _hoisted_63 = { class: "persons-scrollable" };
const _hoisted_64 = { class: "person-block-header m-0" };
const _hoisted_65 = { class: "person-block-name" };
const _hoisted_66 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-user",
  style: { "margin": "-2px 0 0 -4px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" })
], -1));
const _hoisted_67 = { class: "person-block-items" };
const _hoisted_68 = { class: "person-block-total" };
const _hoisted_69 = ["title", "onClick"];
const _hoisted_70 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("path", { d: "M6 9l6 6l6 -6" }, null, -1));
const _hoisted_71 = [
  _hoisted_70
];
const _hoisted_72 = { class: "is-weight-700" };
const _hoisted_73 = {
  key: 0,
  class: "document-badge-compact"
};
const _hoisted_74 = { class: "doc-header" };
const _hoisted_75 = { class: "doc-info" };
const _hoisted_76 = {
  key: 1,
  class: "person-products-list"
};
const _hoisted_77 = { class: "product-mini-info" };
const _hoisted_78 = { class: "product-mini-name" };
const _hoisted_79 = {
  key: 0,
  class: "modifiers-mini"
};
const _hoisted_80 = { class: "product-mini-actions" };
const _hoisted_81 = { class: "is-weight-600 price-mini" };
const _hoisted_82 = {
  key: 0,
  class: "person-action"
};
const _hoisted_83 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-file-invoice mr-0",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14 3v4a1 1 0 0 0 1 1h4" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 7l1 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 13l6 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M13 17l2 0" })
], -1));
const _hoisted_84 = /* @__PURE__ */ createTextVNode(" Facturar ");
const _hoisted_85 = {
  key: 1,
  class: "person-action"
};
const _hoisted_86 = {
  key: 2,
  class: "person-action person-action--delete"
};
const _hoisted_87 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-trash",
  style: { "margin-top": "-2px" }
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M4 7l16 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M10 11l0 6" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M14 11l0 6" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" })
], -1));
const _hoisted_88 = /* @__PURE__ */ createTextVNode(" Cerrar y Continuar ");
const _hoisted_89 = /* @__PURE__ */ _withScopeId$1(() => /* @__PURE__ */ createBaseVNode("i", { class: "fas fa-check-circle mr-2" }, null, -1));
const _hoisted_90 = /* @__PURE__ */ createTextVNode(" Cerrar Mesa ");
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true, default: false }
  },
  emits: ["close", "finalizePerson", "closeTable"],
  setup(__props, { emit }) {
    const props = __props;
    const notif = useNotyf();
    const mesasSession = useMesaSession();
    const userSession = useUserSession();
    const collapsedPersons = ref(new Set());
    const isCollapsed = (person) => collapsedPersons.value.has(person.id);
    const toggleCollapse = (person) => {
      if (!person.products || person.products.length === 0)
        return;
      const s = new Set(collapsedPersons.value);
      if (s.has(person.id))
        s.delete(person.id);
      else
        s.add(person.id);
      collapsedPersons.value = s;
    };
    const openDocumentDialog = ref(false);
    const personToPay = ref(null);
    const posBagForPerson = reactive({
      products: [],
      total: 0,
      barman: userSession.name || "",
      waiter: "",
      enterAmount: 0
    });
    const mesa = computed(() => mesasSession.getMesaSelected());
    const persons = computed(() => {
      var _a;
      return ((_a = mesa.value) == null ? void 0 : _a.divisionAccounts) || [];
    });
    const personsSorted = computed(() => {
      return [...persons.value].sort((a, b) => {
        const aHasProductsNotPaid = a.products.length > 0 && !a.paid;
        const bHasProductsNotPaid = b.products.length > 0 && !b.paid;
        if (aHasProductsNotPaid && !bHasProductsNotPaid)
          return -1;
        if (!aHasProductsNotPaid && bHasProductsNotPaid)
          return 1;
        const aEmpty = a.products.length === 0 && !a.paid;
        const bEmpty = b.products.length === 0 && !b.paid;
        if (aEmpty && !bEmpty && !bHasProductsNotPaid)
          return -1;
        if (!aEmpty && bEmpty && !aHasProductsNotPaid)
          return 1;
        if (a.paid && !b.paid)
          return 1;
        if (!a.paid && b.paid)
          return -1;
        return a.id - b.id;
      });
    });
    const availableProducts = computed(() => {
      var _a;
      const currentProducts = ((_a = mesa.value) == null ? void 0 : _a.products) || [];
      const invoicedProducts = new Map();
      persons.value.filter((p) => p.paid).forEach((person) => {
        person.products.forEach((prod) => {
          if (!invoicedProducts.has(prod.id)) {
            invoicedProducts.set(prod.id, __spreadProps(__spreadValues({}, prod), { quantity: 0 }));
          }
        });
      });
      const allProducts = [...currentProducts];
      invoicedProducts.forEach((prod, id) => {
        if (!allProducts.find((p) => p.id === id)) {
          allProducts.push(prod);
        }
      });
      return allProducts;
    });
    const getProductUniqueKey = (product) => {
      return `${product.id}_${product.modifiersSignature || "no-mod"}`;
    };
    const selectedProducts = ref(new Map());
    const getAssignedQtyNotPaid = (product) => persons.value.filter((p) => !p.paid).reduce((sum, p) => {
      const found = p.products.find((pp) => pp.id === product.id && pp.modifiersSignature === product.modifiersSignature);
      return sum + ((found == null ? void 0 : found.quantity) || 0);
    }, 0);
    const getInvoicedQty = (product) => persons.value.filter((p) => p.paid).reduce((sum, p) => {
      const found = p.products.find((pp) => pp.id === product.id && pp.modifiersSignature === product.modifiersSignature);
      return sum + ((found == null ? void 0 : found.quantity) || 0);
    }, 0);
    const getPendingQty = (product) => {
      var _a, _b;
      const mesaProduct = (_b = (_a = mesa.value) == null ? void 0 : _a.products) == null ? void 0 : _b.find((p) => p.id === product.id && p.modifiersSignature === product.modifiersSignature);
      return ((mesaProduct == null ? void 0 : mesaProduct.quantity) || 0) - getAssignedQtyNotPaid(product);
    };
    const getOriginalQty = (product) => {
      var _a, _b;
      const mesaProduct = (_b = (_a = mesa.value) == null ? void 0 : _a.products) == null ? void 0 : _b.find((p) => p.id === product.id && p.modifiersSignature === product.modifiersSignature);
      return (mesaProduct == null ? void 0 : mesaProduct.quantity) || 0;
    };
    const totalGeneral = computed(() => {
      var _a;
      if (!((_a = mesa.value) == null ? void 0 : _a.products))
        return 0;
      const totalProductosMesa = mesa.value.products.reduce((sum, p) => sum + p.price * p.quantity, 0);
      const totalYaPagado = persons.value.filter((p) => p.paid).reduce((sum, p) => sum + p.total, 0);
      return totalProductosMesa + totalYaPagado;
    });
    const totalPaid = computed(() => persons.value.filter((p) => p.paid).reduce((sum, p) => sum + p.total, 0));
    const totalPending = computed(() => totalGeneral.value - totalPaid.value);
    const isFullyPaid = computed(() => totalPending.value <= 0.01 && persons.value.some((p) => p.paid));
    const syncToStore = () => {
      var _a;
      if (!((_a = mesa.value) == null ? void 0 : _a.id))
        return;
      const mesaClone = JSON.parse(JSON.stringify(mesa.value));
      mesasSession.updateMesa(mesaClone);
      mesasSession.mesas.find((m) => m.id === mesa.value.id);
      MesaService.saveMesa(mesa.value).catch((err) => console.error("Error guardando mesa:", err));
    };
    const initialize = () => {
      selectedProducts.value = new Map();
      if (!mesa.value)
        return;
      if (!mesa.value.divisionAccounts || mesa.value.divisionAccounts.length === 0) {
        const count = mesa.value.personas || 1;
        mesa.value.divisionAccounts = Array.from({ length: count }, (_, i) => ({
          id: i + 1,
          name: `Persona ${i + 1}`,
          products: [],
          total: 0,
          paid: false
        }));
        syncToStore();
      } else {
        console.log("divisionAccounts ya existe, manteniendo datos:", mesa.value.divisionAccounts.length, "personas");
      }
    };
    const addPerson = () => {
      if (!mesa.value.divisionAccounts)
        mesa.value.divisionAccounts = [];
      const newId = mesa.value.divisionAccounts.length + 1;
      mesa.value.divisionAccounts.push({
        id: newId,
        name: `Persona ${newId}`,
        products: [],
        total: 0,
        paid: false
      });
      syncToStore();
      notif.success(`Persona ${newId} agregada`);
    };
    const toggleProduct = (product) => {
      var _a;
      const available = getPendingQty(product);
      if (available <= 0) {
        notif.warning("No hay m\xE1s unidades disponibles");
        return;
      }
      const uniqueKey = getProductUniqueKey(product);
      const current = ((_a = selectedProducts.value.get(uniqueKey)) == null ? void 0 : _a.quantity) || 0;
      const newMap = new Map(selectedProducts.value);
      if (current < available) {
        newMap.set(uniqueKey, { product, quantity: current + 1 });
      } else {
        newMap.delete(uniqueKey);
      }
      selectedProducts.value = newMap;
    };
    const assignProductsToPerson = (person) => {
      var _a;
      if (person.paid || selectedProducts.value.size === 0 || !((_a = mesa.value) == null ? void 0 : _a.products))
        return;
      selectedProducts.value.forEach((selectedData) => {
        const { product, quantity } = selectedData;
        const existing = person.products.find((p) => p.id === product.id && p.modifiersSignature === product.modifiersSignature);
        if (existing) {
          existing.quantity += quantity;
        } else {
          person.products.push(__spreadProps(__spreadValues({}, product), { quantity }));
        }
      });
      person.total = person.products.reduce((sum, p) => sum + p.price * p.quantity, 0);
      selectedProducts.value = new Map();
      syncToStore();
      notif.success(`Productos asignados a ${person.name}`);
    };
    const handlePersonClick = (person) => {
      if (selectedProducts.value.size === 0) {
        notif.warning("Debe seleccionar al menos 1 producto");
        return;
      }
      assignProductsToPerson(person);
    };
    const editingPersonId = ref(null);
    const editedName = ref("");
    const startEdit = (person) => {
      if (person.paid)
        return;
      editingPersonId.value = person.id;
      editedName.value = person.name || "";
    };
    const cancelEdit = () => {
      editingPersonId.value = null;
      editedName.value = "";
    };
    const saveEdit = (person) => {
      const name = (editedName.value || "").trim();
      if (!name) {
        notif.warning("El nombre no puede estar vac\xEDo");
        return;
      }
      person.name = name;
      syncToStore();
      notif.success("Nombre actualizado");
      editingPersonId.value = null;
    };
    const removeProductFromPerson = (person, product) => {
      if (person.paid)
        return;
      const index = person.products.findIndex((p) => p.id === product.id && p.modifiersSignature === product.modifiersSignature);
      if (index > -1) {
        person.products.splice(index, 1);
        person.total = person.products.reduce((sum, p) => sum + p.price * p.quantity, 0);
        syncToStore();
      }
    };
    const removePerson = (person) => {
      var _a;
      if (person.paid) {
        notif.warning("No se puede eliminar una persona que ya ha pagado");
        return;
      }
      const index = (_a = mesa.value.divisionAccounts) == null ? void 0 : _a.findIndex((p) => p.id === person.id);
      if (index !== void 0 && index > -1 && mesa.value.divisionAccounts) {
        mesa.value.divisionAccounts.splice(index, 1);
        const s = new Set(collapsedPersons.value);
        if (s.has(person.id))
          s.delete(person.id);
        collapsedPersons.value = s;
        syncToStore();
        notif.success(`${person.name} eliminada`);
      }
    };
    const payPersonBill = (person) => {
      var _a;
      if (person.products.length === 0 || person.paid)
        return;
      personToPay.value = person;
      posBagForPerson.products = JSON.parse(JSON.stringify(person.products));
      posBagForPerson.total = person.total;
      posBagForPerson.waiter = ((_a = mesa.value) == null ? void 0 : _a.waiter) || "";
      syncToStore();
      openDocumentDialog.value = true;
    };
    const handlePaymentSuccess = (paymentData) => {
      if (!personToPay.value) {
        console.error("No hay persona seleccionada para pago");
        return;
      }
      personToPay.value.paid = true;
      personToPay.value.generatedDocument = {
        type: paymentData.documentType,
        number: paymentData.currentData.number,
        date: dayjs().format("DD/MM/YYYY"),
        total: personToPay.value.total,
        externalId: paymentData.currentData.external_id,
        filename: paymentData.currentData.filename,
        hash: paymentData.currentData.hash,
        qr: paymentData.currentData.qr
      };
      personToPay.value.products.forEach((personProduct) => {
        const mesaProduct = mesa.value.products.find((p) => p.id === personProduct.id && p.modifiersSignature === personProduct.modifiersSignature);
        if (mesaProduct) {
          mesaProduct.quantity -= personProduct.quantity;
        }
      });
      syncToStore();
      openDocumentDialog.value = false;
      personToPay.value = null;
      notif.success("Pago registrado correctamente");
    };
    const closeTable = () => {
      if (!isFullyPaid.value) {
        notif.warning("No se puede cerrar la mesa. A\xFAn hay saldo pendiente.");
        return;
      }
      emit("closeTable");
    };
    const closeDialog = () => {
      syncToStore();
      emit("close");
    };
    watch(() => props.open, (isOpen) => {
      if (isOpen)
        initialize();
    });
    return (_ctx, _cache) => {
      const _component_VFlex = _sfc_main$b;
      const _component_VTag = _sfc_main$c;
      const _component_VIconButton = _sfc_main$d;
      const _component_VButton = _sfc_main$6;
      const _component_VModal = _sfc_main$7;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_VModal, {
          open: __props.open,
          title: "Divisi\xF3n de Cuentas",
          size: "big",
          actions: "center",
          onClose: closeDialog
        }, {
          content: withCtx(() => [
            createBaseVNode("div", _hoisted_1$3, [
              createVNode(_component_VFlex, { class: "status-grid" }, {
                default: withCtx(() => [
                  createBaseVNode("div", _hoisted_2$3, [
                    createBaseVNode("div", _hoisted_3$3, [
                      createBaseVNode("div", _hoisted_4$3, [
                        createBaseVNode("div", _hoisted_5$3, [
                          createBaseVNode("span", _hoisted_6$3, toDisplayString(unref(mesa).label), 1),
                          _hoisted_7$3
                        ]),
                        _hoisted_8$3
                      ])
                    ])
                  ]),
                  createBaseVNode("div", _hoisted_9$3, [
                    createBaseVNode("div", _hoisted_10$3, [
                      createBaseVNode("div", _hoisted_11$3, [
                        createBaseVNode("div", _hoisted_12$3, [
                          createBaseVNode("span", _hoisted_13$3, "S/ " + toDisplayString(unref(totalGeneral).toFixed(2)), 1),
                          _hoisted_14$3
                        ]),
                        _hoisted_15$3
                      ])
                    ])
                  ]),
                  createBaseVNode("div", _hoisted_16$3, [
                    createBaseVNode("div", _hoisted_17$3, [
                      createBaseVNode("div", _hoisted_18$3, [
                        createBaseVNode("div", _hoisted_19$3, [
                          createBaseVNode("span", _hoisted_20$2, "S/ " + toDisplayString(unref(totalPending).toFixed(2)), 1),
                          _hoisted_21$2
                        ]),
                        _hoisted_22$2
                      ])
                    ])
                  ]),
                  createBaseVNode("div", _hoisted_23$2, [
                    createBaseVNode("div", _hoisted_24$1, [
                      createBaseVNode("div", _hoisted_25$1, [
                        createBaseVNode("div", _hoisted_26$1, [
                          createBaseVNode("span", _hoisted_27$1, "S/ " + toDisplayString(unref(totalPaid).toFixed(2)), 1),
                          _hoisted_28$1
                        ]),
                        _hoisted_29$1
                      ])
                    ])
                  ])
                ]),
                _: 1
              }),
              createBaseVNode("div", _hoisted_30$1, [
                createBaseVNode("div", _hoisted_31$1, [
                  createBaseVNode("div", _hoisted_32$1, [
                    createBaseVNode("div", _hoisted_33$1, [
                      _hoisted_34$1,
                      createBaseVNode("span", _hoisted_35$1, toDisplayString(unref(availableProducts).length) + " " + toDisplayString(unref(availableProducts).length === 1 ? "producto" : "productos"), 1)
                    ]),
                    createBaseVNode("div", _hoisted_36$1, [
                      createBaseVNode("div", _hoisted_37$1, [
                        (openBlock(true), createElementBlock(Fragment, null, renderList(unref(availableProducts), (product) => {
                          var _a;
                          return openBlock(), createElementBlock("div", {
                            key: getProductUniqueKey(product),
                            class: normalizeClass(["flex-table-item", {
                              "is-selected": selectedProducts.value.has(getProductUniqueKey(product)),
                              "is-blocked": getPendingQty(product) <= 0
                            }]),
                            onClick: ($event) => toggleProduct(product)
                          }, [
                            selectedProducts.value.has(getProductUniqueKey(product)) ? (openBlock(), createBlock(_component_VTag, {
                              key: 0,
                              color: "success",
                              rounded: "",
                              class: "product-badge",
                              label: `${(_a = selectedProducts.value.get(getProductUniqueKey(product))) == null ? void 0 : _a.quantity}/${getPendingQty(product)}`
                            }, null, 8, ["label"])) : createCommentVNode("", true),
                            createBaseVNode("div", _hoisted_39$1, [
                              createBaseVNode("span", _hoisted_40$1, toDisplayString(product.name), 1),
                              createBaseVNode("span", _hoisted_41$1, [
                                createTextVNode(" Disponible: " + toDisplayString(getPendingQty(product)) + " / " + toDisplayString(getOriginalQty(product)) + " ", 1),
                                getInvoicedQty(product) > 0 ? (openBlock(), createElementBlock("span", _hoisted_42$1, " (Facturado: " + toDisplayString(getInvoicedQty(product)) + ") ", 1)) : createCommentVNode("", true)
                              ]),
                              product.modifiersApplied && product.modifiersApplied.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_43$1, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(product.modifiersApplied, (group, idx) => {
                                  return openBlock(), createElementBlock("span", {
                                    key: idx,
                                    class: "modifier-group"
                                  }, [
                                    createBaseVNode("strong", null, toDisplayString(group.groupName) + ":", 1),
                                    createTextVNode(" " + toDisplayString(group.items.map((i) => i.name).join(", ")), 1)
                                  ]);
                                }), 128))
                              ])) : createCommentVNode("", true)
                            ]),
                            createBaseVNode("div", _hoisted_44$1, [
                              createBaseVNode("span", _hoisted_45$1, " S/ " + toDisplayString((product.price * getOriginalQty(product)).toFixed(2)), 1)
                            ])
                          ], 10, _hoisted_38$1);
                        }), 128))
                      ])
                    ]),
                    createBaseVNode("div", { class: "card-header py-1 px-3" }, [
                      _hoisted_46$1,
                      createBaseVNode("button", {
                        class: "tab-button tab-add",
                        onClick: addPerson
                      }, _hoisted_49$1)
                    ]),
                    createBaseVNode("div", _hoisted_50$1, [
                      createBaseVNode("div", _hoisted_51$1, [
                        (openBlock(true), createElementBlock(Fragment, null, renderList(unref(persons).filter((p) => !p.paid), (person) => {
                          return openBlock(), createElementBlock("div", {
                            key: person.id,
                            class: "person-pill button is-outlined is-primary m-0",
                            onClick: ($event) => editingPersonId.value === person.id ? void 0 : handlePersonClick(person),
                            onKeydown: [
                              withKeys(($event) => editingPersonId.value === person.id ? void 0 : ($event.preventDefault(), handlePersonClick(person)), ["enter"]),
                              withKeys(($event) => editingPersonId.value === person.id ? void 0 : ($event.preventDefault(), handlePersonClick(person)), ["space"])
                            ]
                          }, [
                            createBaseVNode("div", _hoisted_53$1, [
                              _hoisted_54$1,
                              editingPersonId.value === person.id ? withDirectives((openBlock(), createElementBlock("input", {
                                key: 0,
                                ref: (_value, _refs) => {
                                  _refs["el => onEditInputRef(el, person.id)"] = _value;
                                },
                                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => editedName.value = $event),
                                class: "person-edit-input",
                                style: normalizeStyle({
                                  width: `clamp(80px, ${Math.max(editedName.value.length, 1) * 6 + 20}px, 150px)`,
                                  transition: "width 0.2s ease"
                                }),
                                onClick: _cache[1] || (_cache[1] = withModifiers(() => {
                                }, ["stop"])),
                                onKeydown: [
                                  withKeys(withModifiers(($event) => saveEdit(person), ["prevent"]), ["enter"]),
                                  withKeys(withModifiers(cancelEdit, ["prevent"]), ["esc"])
                                ],
                                onBlur: ($event) => saveEdit(person)
                              }, null, 44, _hoisted_55$1)), [
                                [vModelText, editedName.value]
                              ]) : (openBlock(), createElementBlock("span", _hoisted_56$1, toDisplayString(person.name), 1))
                            ]),
                            createBaseVNode("div", _hoisted_57$1, [
                              createVNode(_component_VIconButton, {
                                size: "mini",
                                color: "info",
                                icon: "feather:edit",
                                light: "",
                                class: "btn-sm m-0",
                                title: "Editar",
                                onClick: withModifiers(($event) => startEdit(person), ["stop"])
                              }, null, 8, ["onClick"]),
                              createVNode(_component_VIconButton, {
                                size: "mini",
                                color: "danger",
                                icon: "feather:trash-2",
                                light: "",
                                class: "btn-sm m-0",
                                title: "Eliminar",
                                onClick: withModifiers(($event) => removePerson(person), ["stop"])
                              }, null, 8, ["onClick"])
                            ])
                          ], 40, _hoisted_52$1);
                        }), 128))
                      ])
                    ])
                  ])
                ]),
                createBaseVNode("div", _hoisted_58, [
                  createBaseVNode("div", _hoisted_59, [
                    createBaseVNode("div", _hoisted_60, [
                      _hoisted_61,
                      createBaseVNode("span", _hoisted_62, toDisplayString(unref(persons).length) + " " + toDisplayString(unref(persons).length === 1 ? "persona" : "personas"), 1)
                    ]),
                    createBaseVNode("div", _hoisted_63, [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(personsSorted), (person) => {
                        return openBlock(), createElementBlock("div", {
                          key: person.id,
                          class: normalizeClass(["person-block", { "is-paid": person.paid }])
                        }, [
                          createBaseVNode("div", _hoisted_64, [
                            createBaseVNode("div", null, [
                              createBaseVNode("span", _hoisted_65, [
                                _hoisted_66,
                                createTextVNode(" " + toDisplayString(person.name), 1)
                              ]),
                              createBaseVNode("span", _hoisted_67, toDisplayString(person.products.length) + " items ", 1)
                            ]),
                            createBaseVNode("div", _hoisted_68, [
                              person.products.length > 0 ? (openBlock(), createElementBlock("button", {
                                key: 0,
                                type: "button",
                                class: "collapse-toggle",
                                title: isCollapsed(person) ? "Expandir" : "Colapsar",
                                onClick: withModifiers(($event) => toggleCollapse(person), ["stop"])
                              }, [
                                (openBlock(), createElementBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  width: "18",
                                  height: "18",
                                  viewBox: "0 0 24 24",
                                  fill: "none",
                                  stroke: "currentColor",
                                  "stroke-width": "2",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  class: normalizeClass(["collapse-chevron", { "is-open": !isCollapsed(person) }])
                                }, _hoisted_71, 2))
                              ], 8, _hoisted_69)) : createCommentVNode("", true),
                              createBaseVNode("span", _hoisted_72, "S/ " + toDisplayString(person.total.toFixed(2)), 1)
                            ])
                          ]),
                          person.paid && person.generatedDocument ? (openBlock(), createElementBlock("div", _hoisted_73, [
                            createBaseVNode("div", _hoisted_74, [
                              person.paid ? (openBlock(), createBlock(_component_VTag, {
                                key: 0,
                                color: "success",
                                rounded: "",
                                label: "PAGADO"
                              })) : createCommentVNode("", true),
                              createBaseVNode("div", _hoisted_75, [
                                createBaseVNode("strong", null, toDisplayString(person.generatedDocument.type), 1),
                                createBaseVNode("span", null, toDisplayString(person.generatedDocument.number), 1)
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          person.products.length > 0 && !isCollapsed(person) ? (openBlock(), createElementBlock("div", _hoisted_76, [
                            (openBlock(true), createElementBlock(Fragment, null, renderList(person.products, (product) => {
                              return openBlock(), createElementBlock("div", {
                                key: getProductUniqueKey(product),
                                class: "product-mini-item"
                              }, [
                                createBaseVNode("div", _hoisted_77, [
                                  createBaseVNode("span", _hoisted_78, toDisplayString(product.quantity) + "x " + toDisplayString(product.name), 1),
                                  product.modifiersApplied && product.modifiersApplied.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_79, [
                                    (openBlock(true), createElementBlock(Fragment, null, renderList(product.modifiersApplied, (group, idx) => {
                                      return openBlock(), createElementBlock("span", {
                                        key: idx,
                                        class: "modifier-item"
                                      }, [
                                        createBaseVNode("strong", null, toDisplayString(group.groupName) + ":", 1),
                                        createTextVNode(" " + toDisplayString(group.items.map((i) => i.name).join(", ")), 1)
                                      ]);
                                    }), 128))
                                  ])) : createCommentVNode("", true)
                                ]),
                                createBaseVNode("div", _hoisted_80, [
                                  createBaseVNode("span", _hoisted_81, " S/ " + toDisplayString((product.price * product.quantity).toFixed(2)), 1),
                                  !person.paid ? (openBlock(), createBlock(_component_VIconButton, {
                                    key: 0,
                                    size: "mini",
                                    color: "danger",
                                    icon: "feather:x",
                                    light: "",
                                    class: "btn-sm btn-trash-product",
                                    onClick: ($event) => removeProductFromPerson(person, product)
                                  }, null, 8, ["onClick"])) : createCommentVNode("", true)
                                ])
                              ]);
                            }), 128))
                          ])) : createCommentVNode("", true),
                          !person.paid && !isCollapsed(person) ? (openBlock(), createBlock(_component_VFlex, {
                            key: 2,
                            class: "person-actions",
                            "justify-content": "space-around",
                            "column-gap": "0.5rem"
                          }, {
                            default: withCtx(() => [
                              person.products.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_82, [
                                createVNode(_component_VButton, {
                                  color: "primary",
                                  raised: "",
                                  class: "px-2",
                                  onClick: ($event) => payPersonBill(person)
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_83,
                                    _hoisted_84
                                  ]),
                                  _: 2
                                }, 1032, ["onClick"])
                              ])) : createCommentVNode("", true),
                              person.products.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_85, [
                                createVNode(_sfc_main$9, {
                                  products: person.products,
                                  "button-class": ""
                                }, null, 8, ["products"])
                              ])) : createCommentVNode("", true),
                              person.products.length > 0 ? (openBlock(), createElementBlock("div", _hoisted_86, [
                                createVNode(_component_VButton, {
                                  color: "danger",
                                  raised: "",
                                  class: "btn-delete-person px-2",
                                  onClick: ($event) => removePerson(person)
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_87
                                  ]),
                                  _: 2
                                }, 1032, ["onClick"])
                              ])) : createCommentVNode("", true)
                            ]),
                            _: 2
                          }, 1024)) : createCommentVNode("", true)
                        ], 2);
                      }), 128))
                    ])
                  ])
                ])
              ])
            ])
          ]),
          action: withCtx(() => [
            !unref(isFullyPaid) ? (openBlock(), createBlock(_component_VButton, {
              key: 0,
              class: "ml-auto",
              color: "primary",
              onClick: closeDialog
            }, {
              default: withCtx(() => [
                _hoisted_88
              ]),
              _: 1
            })) : (openBlock(), createBlock(_component_VButton, {
              key: 1,
              color: "success",
              raised: "",
              bold: "",
              onClick: closeTable
            }, {
              default: withCtx(() => [
                _hoisted_89,
                _hoisted_90
              ]),
              _: 1
            }))
          ]),
          _: 1
        }, 8, ["open"]),
        createVNode(_sfc_main$a, {
          open: openDocumentDialog.value,
          "pos-bag": unref(posBagForPerson),
          "products-for-view": unref(posBagForPerson).products,
          onClose: _cache[2] || (_cache[2] = ($event) => openDocumentDialog.value = false),
          onPaymentSuccess: handlePaymentSuccess
        }, null, 8, ["open", "pos-bag", "products-for-view"])
      ], 64);
    };
  }
});
var DivideSaleDialog = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-70b77720"]]);
var MesaRestaurantPos_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$2 = { class: "food-delivery-dashboard" };
const _hoisted_2$2 = { class: "left" };
const _hoisted_3$2 = { class: "left-body" };
const _hoisted_4$2 = { class: "restaurants" };
const _hoisted_5$2 = { class: "columns is-multiline is-mobile" };
const _hoisted_6$2 = {
  class: "column is-12 category-content",
  style: { "padding-bottom": "0px" }
};
const _hoisted_7$2 = { class: "pt-4 searchCategory-content columns mt-0" };
const _hoisted_8$2 = /* @__PURE__ */ createBaseVNode("img", {
  src: _imports_0,
  alt: "Buscar",
  class: "search-icon"
}, null, -1);
const _hoisted_9$2 = {
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 14 14"
};
const _hoisted_10$2 = ["stroke"];
const _hoisted_11$2 = { class: "tooltip-text" };
const _hoisted_12$2 = { class: "column is-2 px-0" };
const _hoisted_13$2 = { class: "tabs-wrapper" };
const _hoisted_14$2 = { class: "tabs-inner" };
const _hoisted_15$2 = { class: "tabs tabs-list is-centered mb-1" };
const _hoisted_16$2 = {
  style: { "border": "none" },
  class: "m-0"
};
const _hoisted_17$2 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:grid"
  })
], -1);
const _hoisted_18$2 = [
  _hoisted_17$2
];
const _hoisted_19$2 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:list"
  })
], -1);
const _hoisted_20$1 = [
  _hoisted_19$2
];
const _hoisted_21$1 = /* @__PURE__ */ createBaseVNode("li", { class: "tab-naver" }, null, -1);
const _hoisted_22$1 = {
  key: 0,
  class: "restaurants-list"
};
const _hoisted_23$1 = {
  key: 1,
  style: { "padding-top": "1%" },
  class: "flex-table"
};
const _hoisted_24 = /* @__PURE__ */ createBaseVNode("div", { class: "flex-table-header" }, [
  /* @__PURE__ */ createBaseVNode("span", { class: "is-grow" }, "Producto"),
  /* @__PURE__ */ createBaseVNode("span", null, "Codigo"),
  /* @__PURE__ */ createBaseVNode("span", { class: "cell-end" }, "Precio")
], -1);
const _hoisted_25 = { class: "flex-list-inner" };
const _hoisted_26 = ["onClick"];
const _hoisted_27 = { class: "flex-table-cell is-media is-grow" };
const _hoisted_28 = { class: "item-name dark-inverted" };
const _hoisted_29 = ["disabled", "title", "onClick"];
const _hoisted_30 = {
  key: 0,
  width: "16",
  height: "16",
  viewBox: "0 0 50 50",
  class: "spin"
};
const _hoisted_31 = /* @__PURE__ */ createBaseVNode("circle", {
  cx: "25",
  cy: "25",
  r: "20",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "4",
  "stroke-linecap": "round",
  "stroke-dasharray": "31.4 31.4"
}, null, -1);
const _hoisted_32 = [
  _hoisted_31
];
const _hoisted_33 = {
  key: 1,
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "currentColor",
  xmlns: "http://www.w3.org/2000/svg"
};
const _hoisted_34 = /* @__PURE__ */ createBaseVNode("path", { d: "M12 .587l3.668 7.431L23.5 9.75l-5.75 5.6L19.336 23 12 19.77 4.664 23l1.586-7.65L0.5 9.75l7.832-1.732L12 .587z" }, null, -1);
const _hoisted_35 = [
  _hoisted_34
];
const _hoisted_36 = {
  key: 2,
  width: "16",
  height: "16",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "1.6",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  xmlns: "http://www.w3.org/2000/svg"
};
const _hoisted_37 = /* @__PURE__ */ createBaseVNode("path", { d: "M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" }, null, -1);
const _hoisted_38 = [
  _hoisted_37
];
const _hoisted_39 = {
  class: "flex-table-cell",
  "data-th": "C\xF3digo"
};
const _hoisted_40 = { class: "light-text" };
const _hoisted_41 = {
  class: "flex-table-cell cell-end",
  "data-th": "Precio"
};
const _hoisted_42 = { class: "is-primary" };
const _hoisted_43 = /* @__PURE__ */ createBaseVNode("div", {
  class: "is-hidden-desktop",
  style: { "min-height": "150px" }
}, null, -1);
const _hoisted_44 = { class: "is-hidden-desktop cart-bottom-fixed h-hidden-tablet-l" };
const _hoisted_45 = { class: "columns is-mobile is-centered m-custom" };
const _hoisted_46 = {
  key: 0,
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-up"
};
const _hoisted_47 = /* @__PURE__ */ createBaseVNode("path", {
  stroke: "none",
  d: "M0 0h24v24H0z",
  fill: "none"
}, null, -1);
const _hoisted_48 = /* @__PURE__ */ createBaseVNode("path", { d: "M6 15l6 -6l6 6" }, null, -1);
const _hoisted_49 = [
  _hoisted_47,
  _hoisted_48
];
const _hoisted_50 = {
  key: 1,
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon-tabler icons-tabler-outline icon-tabler-chevron-down"
};
const _hoisted_51 = /* @__PURE__ */ createBaseVNode("path", {
  stroke: "none",
  d: "M0 0h24v24H0z",
  fill: "none"
}, null, -1);
const _hoisted_52 = /* @__PURE__ */ createBaseVNode("path", { d: "M6 9l6 6l6 -6" }, null, -1);
const _hoisted_53 = [
  _hoisted_51,
  _hoisted_52
];
const _hoisted_54 = {
  key: 0,
  class: "column is-11"
};
const _hoisted_55 = {
  key: 1,
  class: "column is-11 collapsed-view pb-1"
};
const _hoisted_56 = /* @__PURE__ */ createBaseVNode("div", null, " Mis pedidos ", -1);
const _hoisted_57 = [
  _hoisted_56
];
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    mesaid: { type: Number, required: false, default: void 0 }
  },
  emits: ["viewMesas"],
  setup(__props, { emit }) {
    const userSession = useUserSession();
    const isPorConsumo = computed(() => {
      const products = mesaSelected.value.products;
      return products.length === 1 && products[0].name === "Por consumo";
    });
    const bagRef = ref();
    const isCartCollapsed = ref(false);
    const toggleCartCollapse = () => {
      isCartCollapsed.value = !isCartCollapsed.value;
    };
    ref("cart");
    const textSearch = ref("");
    const searchByBarcode = ref(false);
    ref(0);
    const mesasSession = useMesaSession();
    const productSession = useProductSession();
    mesasSession.getMesasNotAvailable();
    ref(false);
    const isBarcodeActive = ref(false);
    const tooltipMessage = ref("Activar b\xFAsqueda por c\xF3digo de barras");
    const productsForView = computed(() => {
      return mesaSelected.value.products;
    });
    reactive({
      description: ""
    });
    const chunks = ref([]);
    const chunkSize = 500;
    let stockIntervalId = void 0;
    const mesaSelected = computed(() => mesasSession.getMesaSelected());
    const isListItems = ref(false);
    const openDialogDivide = ref(false);
    const personToPay = ref(null);
    const openModifiersDialog = ref(false);
    const productForModifiers = ref(null);
    const notif = useNotyf();
    const posBag = reactive({
      products: mesaSelected.value.products,
      total: mesaSelected.value.total,
      barman: userSession.name,
      waiter: mesaSelected.value.waiter,
      enterAmount: 0
    });
    const state = reactive({
      products: productSession.products,
      products_filter: productSession.products,
      openDialogDocument: false,
      loading: false,
      generatedDocument: false,
      sections: sectionsData,
      categories: productSession.categories
    });
    const productIndexByBarcode = new Map();
    const productIndexByInternalId = new Map();
    const filteredProducts = ref(state.products);
    const productGridClass = computed(() => {
      return filteredProducts.value.length <= 2 ? "product-container-limited" : "";
    });
    state.products.forEach((product) => {
      if (product.barcode) {
        productIndexByBarcode.set(product.barcode, product);
      }
      if (product.internalId) {
        productIndexByInternalId.set(product.internalId.toLowerCase(), product);
      }
    });
    const clickAddProduct = (item) => {
      if (isPorConsumo.value)
        return;
      if (item.modifiers && item.modifiers.length > 0) {
        const hasActiveModifiers = item.modifiers.some((g) => g.active);
        if (hasActiveModifiers) {
          productForModifiers.value = item;
          openModifiersDialog.value = true;
          return;
        }
      }
      mesasSession.addProduct(item);
    };
    const sortProductsArray = (arr) => {
      return arr.slice().sort((a, b) => {
        const af = a.restaurant_favorite ? 1 : 0;
        const bf = b.restaurant_favorite ? 1 : 0;
        if (af !== bf)
          return bf - af;
        return a.id - b.id;
      });
    };
    const pendingFavorites = ref(new Set());
    const toggleFavorite = async (itemId) => {
      const currentProducts = productSession.getProducts();
      const snapshot = JSON.parse(JSON.stringify(currentProducts));
      const prodIndex = currentProducts.findIndex((p) => p.id === itemId);
      if (prodIndex === -1)
        return;
      const newFav = !currentProducts[prodIndex].restaurant_favorite;
      try {
        pendingFavorites.value.add(itemId);
        const newProducts = currentProducts.map((p) => p.id === itemId ? __spreadProps(__spreadValues({}, p), { restaurant_favorite: newFav }) : p);
        const sorted = sortProductsArray(newProducts);
        productSession.setProducts(sorted);
        filteredProducts.value = sorted.slice(0, 500);
        await RestaurantService.setRestaurantFavorite(itemId, newFav);
        notif.success(newFav ? "Marcado como favorito" : "Quitado de favoritos");
      } catch (err) {
        productSession.setProducts(snapshot);
        filteredProducts.value = snapshot.length >= 500 ? snapshot.slice(0, 500) : snapshot;
        notif.error("No se pudo actualizar favorito");
      } finally {
        pendingFavorites.value.delete(itemId);
      }
    };
    const openDialogDocument = () => {
      var _a;
      const products = mesaSelected.value.products;
      if (mesaSelected.value.total > 0) {
        if (products.length === 1 && products[0].esFusionado && Array.isArray(products[0].productosFusionados)) {
          posBag.products = products[0].productosFusionados.map((p) => __spreadProps(__spreadValues({}, p), {
            esFusionado: true
          }));
        } else {
          posBag.products = JSON.parse(JSON.stringify(products));
        }
        posBag.total = mesaSelected.value.total;
        posBag.totalDiscount = (_a = mesaSelected.value.totalDiscount) != null ? _a : 0;
        state.openDialogDocument = true;
      }
    };
    const closeDialogDocument = async () => {
      var _a;
      const hasDivision = mesaSelected.value.divisionAccounts && mesaSelected.value.divisionAccounts.length > 0;
      const notAllPaid = (_a = mesaSelected.value.divisionAccounts) == null ? void 0 : _a.some((p) => !p.paid);
      if (hasDivision && notAllPaid) {
        state.openDialogDocument = false;
        openDialogDivide.value = true;
        return;
      }
      if (bagRef.value) {
        bagRef.value.restoreAllMesaProductsToOriginal();
        bagRef.value.clearMesaEditedFields();
      }
      const closingMesaId = mesaSelected.value.id;
      mesasSession.startClosingMesa(closingMesaId);
      state.openDialogDocument = false;
      emit("viewMesas");
      try {
        await mesasSession.setAvailableMesa();
        mesasSession.setMesaSelected(0);
      } catch (error) {
        console.error("Error al cerrar la mesa:", error);
        notif.error("Error al cerrar la mesa");
      } finally {
        mesasSession.finishClosingMesa(closingMesaId);
      }
    };
    const changeToMesas = () => {
      emit("viewMesas");
    };
    const back = () => {
      state.openDialogDocument = false;
    };
    const selectMesa = (id) => {
      mesasSession.setMesaSelected(id);
    };
    let debounceTimeout = null;
    const debouncedSearch = (callback, delay = 70) => {
      if (debounceTimeout)
        clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(callback, delay);
    };
    let debounceTimeoutBarcode = null;
    const debouncedSearchBarcode = (callback, delay = 300) => {
      if (debounceTimeoutBarcode)
        clearTimeout(debounceTimeoutBarcode);
      debounceTimeoutBarcode = setTimeout(callback, delay);
    };
    const loadProducts = () => {
      const allProducts = productSession.getProducts();
      const sorted = sortProductsArray(allProducts);
      productSession.setProducts(sorted);
      chunks.value = [];
      for (let i = 0; i < sorted.length; i += chunkSize) {
        chunks.value.push(sorted.slice(i, i + chunkSize));
      }
      filteredProducts.value = sorted.length >= 500 ? sorted.slice(0, 500) : sorted;
    };
    const removeAccents = (name) => {
      return name ? name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
    };
    const products_filter = computed(() => filteredProducts.value);
    watch(textSearch, () => {
      if (textSearch.value.length > 0) {
        if (searchByBarcode.value) {
          debouncedSearchBarcode(() => {
            const itemFound = productIndexByBarcode.get(textSearch.value);
            if (itemFound) {
              mesasSession.addProduct(itemFound);
              notif.success("Producto a\xF1adido!");
            } else {
              notif.error("Producto no encontrado");
            }
            textSearch.value = "";
            filteredProducts.value = itemFound ? [itemFound] : [];
          });
        } else {
          debouncedSearch(() => {
            const lowerSearchText = textSearch.value.toLowerCase();
            filteredProducts.value = chunks.value.flatMap((chunk) => chunk.filter((item) => {
              var _a;
              return removeAccents(item.name || "").toLowerCase().includes(removeAccents(lowerSearchText)) || productIndexByInternalId.has(lowerSearchText) && ((_a = productIndexByInternalId.get(lowerSearchText)) == null ? void 0 : _a.internalId) === item.internalId;
            }));
          });
        }
      } else {
        filteredProducts.value = state.products.length >= 500 ? state.products.slice(0, 500) : state.products;
      }
    });
    watch(isListItems, () => {
      localStorage.setItem("isListItems", isListItems.value ? "true" : "false");
    });
    const selectCategory = (id) => {
      textSearch.value = "";
      if (id === 0) {
        filteredProducts.value = state.products_filter;
      } else {
        filteredProducts.value = state.products_filter.filter((x) => x.categoryId === id);
      }
    };
    const cancelTable = () => {
      closeDialogDocument();
    };
    const changeSearchByBarcode = () => {
      isBarcodeActive.value = !isBarcodeActive.value;
      tooltipMessage.value = isBarcodeActive.value ? "Desactivar b\xFAsqueda por c\xF3digo de barras" : "Activar b\xFAsqueda por c\xF3digo de barras";
      let byBarcode = searchByBarcode.value ? 1 : 0;
      userSession.setSearchByBarcode(byBarcode);
    };
    onMounted(() => {
      searchByBarcode.value = userSession.searchByBarcode ? true : false;
      isBarcodeActive.value = userSession.searchByBarcode ? true : false;
      isListItems.value = localStorage.getItem("isListItems") === "true" ? true : false;
      const searchCategoryContent = document.querySelector(".searchCategory-content");
      const categoryContent = document.querySelector(".category-content");
      if (!searchCategoryContent || !categoryContent)
        return;
      const offsetTop = searchCategoryContent.offsetTop;
      const handleScroll = () => {
        const shouldStick = window.scrollY >= offsetTop;
        searchCategoryContent.classList.toggle("is-sticky", shouldStick);
        searchCategoryContent.classList.toggle("mt-3", shouldStick);
        if (shouldStick) {
          categoryContent.style.marginBottom = "90px";
        } else {
          categoryContent.style.marginBottom = "0";
        }
      };
      window.addEventListener("scroll", handleScroll);
      loadProducts();
      stockIntervalId = window.setInterval(syncProductsStock, 5e3);
    });
    const syncProductsStock = async () => {
      try {
        const response = await RestaurantService.getProductsStock();
        if (response && response.success && response.data) {
          productSession.updateProductsStock(response.data);
        }
      } catch (error) {
        console.error("Error al sincronizar stock:", error);
      }
    };
    onBeforeUnmount(() => {
      if (stockIntervalId !== void 0) {
        clearInterval(stockIntervalId);
      }
    });
    const openDialogDivideSale = () => {
      if (mesaSelected.value.total > 0) {
        openDialogDivide.value = true;
      } else {
        notif.warning("No hay productos en la mesa");
      }
    };
    const closeDialogDivide = () => {
      openDialogDivide.value = false;
      personToPay.value = null;
      if (mesaSelected.value.divisionAccounts && mesaSelected.value.divisionAccounts.length > 0) {
        const index = mesasSession.mesas.findIndex((m) => m.id === mesaSelected.value.id);
        if (index !== -1) {
          mesasSession.mesas[index] = JSON.parse(JSON.stringify(mesaSelected.value));
          MesaService.saveMesa(mesaSelected.value).then(() => {
            console.log("Divisi\xF3n de cuentas guardada en backend");
          }).catch((error) => {
            console.error("Error al guardar divisi\xF3n en backend:", error);
          });
        }
      }
    };
    const handleCloseTableFromDivide = () => {
      openDialogDivide.value = false;
      personToPay.value = null;
      closeDialogDocument();
    };
    const handleModifiersConfirm = (data) => {
      const { units } = data;
      units.forEach((unitData) => {
        const { product, selectedModifiers, stockItems, additionalPrice } = unitData;
        const modifiersSignature = selectedModifiers.map((group) => `${group.groupName}:${group.items.map((i) => i.name).sort().join(",")}`).sort().join("|");
        const existingProductIndex = mesaSelected.value.products.findIndex((p) => p.id === product.id && p.modifiersSignature === modifiersSignature);
        if (existingProductIndex >= 0) {
          const existingProduct = mesaSelected.value.products[existingProductIndex];
          existingProduct.quantity += 1;
          existingProduct.quantity_pending += 1;
        } else {
          const productToAdd = __spreadProps(__spreadValues({}, product), {
            quantity: 1,
            quantity_pending: 1,
            price: product.price + additionalPrice,
            basePrice: product.price,
            additionalPrice,
            modifiersApplied: selectedModifiers,
            modifiersSignature,
            esFusionado: false,
            productosFusionados: []
          });
          mesasSession.addProduct(productToAdd);
        }
      });
      mesasSession.calculaTotalBag();
      openModifiersDialog.value = false;
      productForModifiers.value = null;
      notif.success(`${units.length} producto${units.length > 1 ? "s agregados" : " agregado"} con modificadores`);
    };
    const handleModifiersClose = () => {
      openModifiersDialog.value = false;
      productForModifiers.value = null;
    };
    return (_ctx, _cache) => {
      const _component_DocumentDialog = _sfc_main$a;
      const _component_CategoriesNavBar = _sfc_main$e;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$f;
      const _component_ProductPos = _sfc_main$g;
      const _component_VAvatar = _sfc_main$h;
      const _component_CartMobile = __unplugin_components_7;
      const _component_Bag = __unplugin_components_8;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(ProductModifiersDialog, {
          open: openModifiersDialog.value,
          product: productForModifiers.value,
          onClose: handleModifiersClose,
          onConfirm: handleModifiersConfirm
        }, null, 8, ["open", "product"]),
        createVNode(DivideSaleDialog, {
          open: openDialogDivide.value,
          onClose: closeDialogDivide,
          onCloseTable: handleCloseTableFromDivide
        }, null, 8, ["open"]),
        createVNode(_component_DocumentDialog, {
          "pos-bag": unref(posBag),
          open: unref(state).openDialogDocument,
          "products-for-view": unref(productsForView),
          onClose: closeDialogDocument,
          onBack: back
        }, null, 8, ["pos-bag", "open", "products-for-view"]),
        createBaseVNode("div", _hoisted_1$2, [
          createBaseVNode("div", _hoisted_2$2, [
            createBaseVNode("div", _hoisted_3$2, [
              createBaseVNode("div", _hoisted_4$2, [
                createBaseVNode("div", _hoisted_5$2, [
                  createBaseVNode("div", _hoisted_6$2, [
                    createVNode(_component_CategoriesNavBar, { onSelectcategory: selectCategory })
                  ]),
                  createBaseVNode("div", _hoisted_7$2, [
                    createVNode(_component_VField, { class: "search-conteiner column is-10" }, {
                      default: withCtx(() => [
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            _hoisted_8$2,
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => textSearch.value = $event),
                              type: "text",
                              class: "input input-search-mesa is-rounded",
                              placeholder: "Busca un producto ..."
                            }, null, 512), [
                              [vModelText, textSearch.value]
                            ]),
                            createBaseVNode("label", null, [
                              withDirectives(createBaseVNode("input", {
                                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => searchByBarcode.value = $event),
                                class: "barcode-toggle",
                                type: "checkbox",
                                onChange: changeSearchByBarcode
                              }, null, 544), [
                                [vModelCheckbox, searchByBarcode.value]
                              ]),
                              createBaseVNode("span", {
                                class: normalizeClass(["barcode-icon-mesa", { active: isBarcodeActive.value }])
                              }, [
                                (openBlock(), createElementBlock("svg", _hoisted_9$2, [
                                  createBaseVNode("path", {
                                    fill: "none",
                                    stroke: isBarcodeActive.value ? "#4caf50" : "#cfcfcf",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M5.11.656H1.208a.49.49 0 0 0-.488.488v3.904m12.686 0V1.144a.49.49 0 0 0-.488-.488H9.014m0 12.688h3.904a.49.49 0 0 0 .488-.488V8.952m-12.687 0v3.904a.49.49 0 0 0 .488.488H5.11m5.696-9.552v6.416M3.194 3.792v6.416m5.709-6.416v4.666m0 1.75v-.291M7 3.792v4.666m0 1.75v-.291M5.097 3.792v4.666m0 1.75v-.291"
                                  }, null, 8, _hoisted_10$2)
                                ]))
                              ], 2),
                              createBaseVNode("span", _hoisted_11$2, toDisplayString(tooltipMessage.value), 1)
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    }),
                    createBaseVNode("div", _hoisted_12$2, [
                      createBaseVNode("div", _hoisted_13$2, [
                        createBaseVNode("div", _hoisted_14$2, [
                          createBaseVNode("div", _hoisted_15$2, [
                            createBaseVNode("ul", _hoisted_16$2, [
                              createBaseVNode("li", {
                                class: normalizeClass([isListItems.value == false && "is-active"])
                              }, [
                                createBaseVNode("a", {
                                  onClick: _cache[2] || (_cache[2] = ($event) => isListItems.value = false)
                                }, _hoisted_18$2)
                              ], 2),
                              createBaseVNode("li", {
                                class: normalizeClass([isListItems.value == true && "is-active"])
                              }, [
                                createBaseVNode("a", {
                                  onClick: _cache[3] || (_cache[3] = ($event) => isListItems.value = true)
                                }, _hoisted_20$1)
                              ], 2),
                              _hoisted_21$1
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                !isListItems.value ? (openBlock(), createElementBlock("div", _hoisted_22$1, [
                  createBaseVNode("div", {
                    class: normalizeClass(["product-conteiner", unref(productGridClass)])
                  }, [
                    (openBlock(true), createElementBlock(Fragment, null, renderList(unref(products_filter), (product) => {
                      return openBlock(), createElementBlock("div", {
                        key: product.id
                      }, [
                        createVNode(_component_ProductPos, {
                          product,
                          pending: pendingFavorites.value.has(product.id),
                          onClickAddProduct: clickAddProduct,
                          onToggleFavorite: toggleFavorite
                        }, null, 8, ["product", "pending"])
                      ]);
                    }), 128))
                  ], 2)
                ])) : (openBlock(), createElementBlock("div", _hoisted_23$1, [
                  _hoisted_24,
                  createBaseVNode("div", _hoisted_25, [
                    createVNode(TransitionGroup, {
                      name: "list",
                      tag: "div"
                    }, {
                      default: withCtx(() => [
                        (openBlock(true), createElementBlock(Fragment, null, renderList(unref(products_filter), (item) => {
                          return openBlock(), createElementBlock("div", {
                            key: item.id,
                            class: "flex-table-item restaurants-list-item",
                            style: { "cursor": "pointer" },
                            onClick: ($event) => clickAddProduct(item)
                          }, [
                            createBaseVNode("div", _hoisted_27, [
                              createVNode(_component_VAvatar, {
                                picture: item.imageUrl,
                                size: "medium"
                              }, null, 8, ["picture"]),
                              createBaseVNode("div", null, [
                                createBaseVNode("span", _hoisted_28, toDisplayString(item.name), 1),
                                createBaseVNode("button", {
                                  class: normalizeClass(["favorite-inline", { "is-fav": item.restaurant_favorite }]),
                                  disabled: pendingFavorites.value.has(item.id),
                                  title: item.restaurant_favorite ? "Quitar favorito" : "Marcar favorito",
                                  onClick: withModifiers(($event) => toggleFavorite(item.id), ["stop"])
                                }, [
                                  pendingFavorites.value.has(item.id) ? (openBlock(), createElementBlock("svg", _hoisted_30, _hoisted_32)) : item.restaurant_favorite ? (openBlock(), createElementBlock("svg", _hoisted_33, _hoisted_35)) : (openBlock(), createElementBlock("svg", _hoisted_36, _hoisted_38))
                                ], 10, _hoisted_29)
                              ])
                            ]),
                            createBaseVNode("div", _hoisted_39, [
                              createBaseVNode("span", _hoisted_40, toDisplayString(item.internalId), 1)
                            ]),
                            createBaseVNode("div", _hoisted_41, [
                              createBaseVNode("span", _hoisted_42, toDisplayString(item.currencyTypeSymbol) + " " + toDisplayString(item.price.toFixed(2)), 1)
                            ])
                          ], 8, _hoisted_26);
                        }), 128))
                      ]),
                      _: 1
                    })
                  ])
                ]))
              ])
            ]),
            _hoisted_43
          ]),
          createBaseVNode("div", _hoisted_44, [
            createBaseVNode("div", _hoisted_45, [
              createBaseVNode("button", {
                class: "btn-collapse px-2",
                onClick: toggleCartCollapse
              }, [
                isCartCollapsed.value ? (openBlock(), createElementBlock("svg", _hoisted_46, _hoisted_49)) : (openBlock(), createElementBlock("svg", _hoisted_50, _hoisted_53))
              ]),
              !isCartCollapsed.value ? (openBlock(), createElementBlock("div", _hoisted_54, [
                createVNode(_component_CartMobile, {
                  parent: "mesas",
                  onSelectMesa: selectMesa,
                  onFinalizeSale: openDialogDocument,
                  onCancelTable: cancelTable,
                  onChangeToMesas: changeToMesas,
                  onDivideSale: openDialogDivideSale
                })
              ])) : createCommentVNode("", true),
              isCartCollapsed.value ? (openBlock(), createElementBlock("div", _hoisted_55, _hoisted_57)) : createCommentVNode("", true)
            ])
          ]),
          createVNode(_component_Bag, {
            ref: (_value, _refs) => {
              _refs["bagRef"] = _value;
              bagRef.value = _value;
            },
            parent: "mesas",
            onSelectMesa: selectMesa,
            onFinalizeSale: openDialogDocument,
            onCancelTable: cancelTable,
            onChangeToMesas: changeToMesas,
            onDivideSale: openDialogDivideSale
          }, null, 512)
        ])
      ], 64);
    };
  }
});
const _hoisted_1$1 = { class: "modal-form p-4" };
const _hoisted_2$1 = /* @__PURE__ */ createBaseVNode("div", { style: { "width": "100%", "text-align": "center" } }, [
  /* @__PURE__ */ createBaseVNode("p", null, "Selecciona el ambiente y la mesa destino")
], -1);
const _hoisted_3$1 = {
  class: "columns is-variable is-6 mt-4",
  style: { "margin": "0px" }
};
const _hoisted_4$1 = {
  class: "column p-4 mt-4",
  style: { "justify-content": "center", "align-items": "center", "display": "flex", "flex-direction": "column" }
};
const _hoisted_5$1 = /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("label", {
    class: "label",
    style: { "font-weight": "normal", "font-size": "larger", "margin": "5px" }
  }, "DESDE")
], -1);
const _hoisted_6$1 = { style: { "background-color": "var(--primary)", "width": "200px", "height": "200px", "display": "flex", "align-items": "center", "justify-content": "center", "border-radius": "8px" } };
const _hoisted_7$1 = { style: { "display": "flex", "align-items": "center", "justify-content": "center", "flex-direction": "column", "width": "100%" } };
const _hoisted_8$1 = { style: { "color": "white", "font-weight": "bold", "font-size": "24px" } };
const _hoisted_9$1 = { style: { "color": "white", "font-weight": "bold", "font-size": "24px" } };
const _hoisted_10$1 = /* @__PURE__ */ createBaseVNode("div", {
  class: "column is-narrow",
  style: { "width": "64px" }
}, [
  /* @__PURE__ */ createBaseVNode("div", { style: { "height": "100%", "display": "flex", "align-items": "center", "justify-content": "center", "color": "var (--primary)" } }, [
    /* @__PURE__ */ createBaseVNode("i", {
      class: "fas fa-arrow-right",
      style: { "font-size": "32px", "color": "var(--text-secondary)" }
    })
  ])
], -1);
const _hoisted_11$1 = {
  class: "column p-4 mt-4",
  style: { "justify-content": "center", "align-items": "center", "display": "flex", "flex-direction": "column" }
};
const _hoisted_12$1 = /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("label", {
    class: "label",
    style: { "font-weight": "normal", "font-size": "larger", "margin": "5px" }
  }, "HACIA")
], -1);
const _hoisted_13$1 = { style: { "width": "200px", "height": "200px", "border": "2px solid var(--primary)", "border-radius": "8px", "padding": "16px" } };
const _hoisted_14$1 = /* @__PURE__ */ createBaseVNode("label", { class: "label" }, "Seleccionar Ambiente", -1);
const _hoisted_15$1 = { class: "select is-fullwidth" };
const _hoisted_16$1 = ["value"];
const _hoisted_17$1 = /* @__PURE__ */ createBaseVNode("label", { class: "label" }, "Seleccionar Mesa", -1);
const _hoisted_18$1 = { class: "select is-fullwidth" };
const _hoisted_19$1 = ["disabled"];
const _hoisted_20 = ["value"];
const _hoisted_21 = { class: "modal-actions" };
const _hoisted_22 = /* @__PURE__ */ createTextVNode(" Cancelar ");
const _hoisted_23 = /* @__PURE__ */ createTextVNode(" Confirmar ");
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true },
    mesaOrigen: { type: null, required: true },
    mesasDisponibles: { type: Array, required: true }
  },
  emits: ["close", "confirm-move"],
  setup(__props, { emit }) {
    const props = __props;
    const selectedEnv = ref(null);
    const selectedMesaId = ref(null);
    const isSubmitting = ref(false);
    watch(() => props.open, (isOpen) => {
      var _a;
      if (isOpen) {
        selectedEnv.value = ((_a = props.mesaOrigen) == null ? void 0 : _a.environment) || null;
        selectedMesaId.value = mesasFiltradas.value.length ? mesasFiltradas.value[0].id : null;
        isSubmitting.value = false;
      }
    });
    watch(selectedEnv, (newEnv) => {
      const mesasEnAmbiente = props.mesasDisponibles.filter((mesa) => mesa.environment === newEnv);
      selectedMesaId.value = mesasEnAmbiente.length ? mesasEnAmbiente[0].id : null;
    });
    const uniqueEnvironments = computed(() => {
      return [...new Set(props.mesasDisponibles.map((m) => m.environment))];
    });
    const mesasFiltradas = computed(() => {
      if (!selectedEnv.value)
        return [];
      return props.mesasDisponibles.filter((mesa) => mesa.environment === selectedEnv.value);
    });
    const confirm = () => {
      if (selectedMesaId.value !== null) {
        isSubmitting.value = true;
        emit("confirm-move", selectedMesaId.value);
      }
    };
    const closeModal = () => {
      if (!isSubmitting.value) {
        emit("close");
      }
    };
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$f;
      const _component_VButton = _sfc_main$6;
      const _component_VModal = _sfc_main$7;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Cambiar Mesa",
        actions: "center",
        size: "medium",
        onClose: closeModal
      }, {
        content: withCtx(() => {
          var _a, _b;
          return [
            createBaseVNode("div", _hoisted_1$1, [
              _hoisted_2$1,
              createBaseVNode("div", _hoisted_3$1, [
                createBaseVNode("div", _hoisted_4$1, [
                  _hoisted_5$1,
                  createBaseVNode("div", _hoisted_6$1, [
                    createBaseVNode("div", _hoisted_7$1, [
                      createBaseVNode("span", _hoisted_8$1, toDisplayString(((_a = props.mesaOrigen) == null ? void 0 : _a.environment) || "..."), 1),
                      createBaseVNode("span", _hoisted_9$1, toDisplayString(((_b = props.mesaOrigen) == null ? void 0 : _b.label) || "..."), 1)
                    ])
                  ])
                ]),
                _hoisted_10$1,
                createBaseVNode("div", _hoisted_11$1, [
                  _hoisted_12$1,
                  createBaseVNode("div", _hoisted_13$1, [
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_14$1,
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            createBaseVNode("div", _hoisted_15$1, [
                              withDirectives(createBaseVNode("select", {
                                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => selectedEnv.value = $event)
                              }, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(unref(uniqueEnvironments), (env) => {
                                  return openBlock(), createElementBlock("option", {
                                    key: env,
                                    value: env
                                  }, toDisplayString(env), 9, _hoisted_16$1);
                                }), 128))
                              ], 512), [
                                [vModelSelect, selectedEnv.value]
                              ])
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    }),
                    createVNode(_component_VField, null, {
                      default: withCtx(() => [
                        _hoisted_17$1,
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            createBaseVNode("div", _hoisted_18$1, [
                              withDirectives(createBaseVNode("select", {
                                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => selectedMesaId.value = $event),
                                disabled: !selectedEnv.value
                              }, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(unref(mesasFiltradas), (mesa) => {
                                  return openBlock(), createElementBlock("option", {
                                    key: mesa.id,
                                    value: mesa.id
                                  }, toDisplayString(mesa.label), 9, _hoisted_20);
                                }), 128))
                              ], 8, _hoisted_19$1), [
                                [vModelSelect, selectedMesaId.value]
                              ])
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ])
                ])
              ])
            ])
          ];
        }),
        action: withCtx(() => [
          createBaseVNode("div", _hoisted_21, [
            createVNode(_component_VButton, {
              disabled: isSubmitting.value,
              onClick: closeModal
            }, {
              default: withCtx(() => [
                _hoisted_22
              ]),
              _: 1
            }, 8, ["disabled"]),
            createVNode(_component_VButton, {
              color: "primary",
              loading: isSubmitting.value,
              disabled: selectedMesaId.value === null || isSubmitting.value,
              onClick: confirm
            }, {
              default: withCtx(() => [
                _hoisted_23
              ]),
              _: 1
            }, 8, ["loading", "disabled"])
          ])
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
var MoveAmbienteModal_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-a1934cc6"), n = n(), popScopeId(), n);
const _hoisted_1 = { class: "modal-form p-4" };
const _hoisted_2 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", { style: { "width": "100%", "text-align": "center" } }, [
  /* @__PURE__ */ createBaseVNode("p", null, "Selecciona el ambiente destino")
], -1));
const _hoisted_3 = {
  class: "columns is-variable is-6 mt-4",
  style: { "margin": "0px" }
};
const _hoisted_4 = {
  class: "column p-4 mt-4",
  style: { "justify-content": "center", "align-items": "center", "display": "flex", "flex-direction": "column" }
};
const _hoisted_5 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("label", {
    class: "label",
    style: { "font-weight": "normal", "font-size": "larger", "margin": "5px" }
  }, " DESDE ")
], -1));
const _hoisted_6 = { style: { "background-color": "var(--primary)", "width": "200px", "height": "200px", "display": "flex", "align-items": "center", "justify-content": "center", "border-radius": "8px" } };
const _hoisted_7 = { style: { "display": "flex", "align-items": "center", "justify-content": "center", "flex-direction": "column", "width": "100%" } };
const _hoisted_8 = { style: { "color": "white", "font-weight": "bold", "font-size": "24px" } };
const _hoisted_9 = { style: { "color": "white", "font-weight": "bold", "font-size": "32px" } };
const _hoisted_10 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", {
  class: "column is-narrow",
  style: { "width": "64px" }
}, [
  /* @__PURE__ */ createBaseVNode("div", { style: { "height": "100%", "display": "flex", "align-items": "center", "justify-content": "center" } }, [
    /* @__PURE__ */ createBaseVNode("i", {
      class: "fas fa-arrow-right",
      style: { "font-size": "32px", "color": "var(--text-secondary)" }
    })
  ])
], -1));
const _hoisted_11 = {
  class: "column p-4 mt-4",
  style: { "justify-content": "center", "align-items": "center", "display": "flex", "flex-direction": "column" }
};
const _hoisted_12 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("div", null, [
  /* @__PURE__ */ createBaseVNode("label", {
    class: "label",
    style: { "font-weight": "normal", "font-size": "larger", "margin": "5px" }
  }, " HACIA ")
], -1));
const _hoisted_13 = { style: { "width": "200px", "height": "200px", "border": "2px solid var(--primary)", "border-radius": "8px", "padding": "16px", "display": "flex", "align-items": "center", "justify-content": "center" } };
const _hoisted_14 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("label", { class: "label" }, "Seleccionar Ambiente", -1));
const _hoisted_15 = { class: "select is-fullwidth" };
const _hoisted_16 = ["value"];
const _hoisted_17 = { class: "modal-actions" };
const _hoisted_18 = /* @__PURE__ */ createTextVNode(" Cancelar ");
const _hoisted_19 = /* @__PURE__ */ createTextVNode(" Confirmar ");
const _sfc_main = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true },
    mesa: { type: null, required: true },
    ambientesDisponibles: { type: Array, required: true }
  },
  emits: ["close", "confirm"],
  setup(__props, { emit }) {
    const props = __props;
    const selectedAmbiente = ref(null);
    const isSubmitting = ref(false);
    const ambientesFisicos = computed(() => {
      return props.ambientesDisponibles.filter((ambiente) => {
        const ambienteLower = ambiente.toLowerCase();
        return !ambienteLower.includes("delivery") && !ambienteLower.includes("para llevar") && !ambienteLower.includes("parallevar");
      });
    });
    watch(() => props.open, (isOpen) => {
      if (isOpen) {
        const ambientesFiltrados = ambientesFiltradosComputed.value;
        selectedAmbiente.value = ambientesFiltrados.length ? ambientesFiltrados[0] : null;
        isSubmitting.value = false;
      }
    });
    const ambientesFiltradosComputed = computed(() => {
      if (!props.mesa)
        return ambientesFisicos.value;
      return ambientesFisicos.value.filter((amb) => {
        var _a;
        return amb !== ((_a = props.mesa) == null ? void 0 : _a.environment);
      });
    });
    const confirm = () => {
      if (selectedAmbiente.value !== null) {
        isSubmitting.value = true;
        emit("confirm", selectedAmbiente.value);
      }
    };
    const closeModal = () => {
      if (!isSubmitting.value) {
        emit("close");
      }
    };
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$f;
      const _component_VButton = _sfc_main$6;
      const _component_VModal = _sfc_main$7;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Mover Mesa a Otro Ambiente",
        actions: "center",
        size: "medium",
        onClose: closeModal
      }, {
        content: withCtx(() => {
          var _a, _b;
          return [
            createBaseVNode("div", _hoisted_1, [
              _hoisted_2,
              createBaseVNode("div", _hoisted_3, [
                createBaseVNode("div", _hoisted_4, [
                  _hoisted_5,
                  createBaseVNode("div", _hoisted_6, [
                    createBaseVNode("div", _hoisted_7, [
                      createBaseVNode("span", _hoisted_8, toDisplayString(((_a = props.mesa) == null ? void 0 : _a.environment) || "..."), 1),
                      createBaseVNode("span", _hoisted_9, " Mesa " + toDisplayString(((_b = props.mesa) == null ? void 0 : _b.label) || "..."), 1)
                    ])
                  ])
                ]),
                _hoisted_10,
                createBaseVNode("div", _hoisted_11, [
                  _hoisted_12,
                  createBaseVNode("div", _hoisted_13, [
                    createVNode(_component_VField, { style: { "width": "100%" } }, {
                      default: withCtx(() => [
                        _hoisted_14,
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            createBaseVNode("div", _hoisted_15, [
                              withDirectives(createBaseVNode("select", {
                                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => selectedAmbiente.value = $event)
                              }, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(unref(ambientesFiltradosComputed), (ambiente) => {
                                  return openBlock(), createElementBlock("option", {
                                    key: ambiente,
                                    value: ambiente
                                  }, toDisplayString(ambiente), 9, _hoisted_16);
                                }), 128))
                              ], 512), [
                                [vModelSelect, selectedAmbiente.value]
                              ])
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    })
                  ])
                ])
              ])
            ])
          ];
        }),
        action: withCtx(() => [
          createBaseVNode("div", _hoisted_17, [
            createVNode(_component_VButton, {
              disabled: isSubmitting.value,
              onClick: closeModal
            }, {
              default: withCtx(() => [
                _hoisted_18
              ]),
              _: 1
            }, 8, ["disabled"]),
            createVNode(_component_VButton, {
              color: "primary",
              loading: isSubmitting.value,
              disabled: selectedAmbiente.value === null || isSubmitting.value,
              onClick: confirm
            }, {
              default: withCtx(() => [
                _hoisted_19
              ]),
              _: 1
            }, 8, ["loading", "disabled"])
          ])
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
var MoveAmbienteModal = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-a1934cc6"]]);
export { MoveAmbienteModal as M, _sfc_main$1 as _, _sfc_main$5 as a, _sfc_main$4 as b, _sfc_main$2 as c };
