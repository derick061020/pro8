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
import { _ as _sfc_main$5 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$6 } from "./VModal.9533b2d3.js";
import { b as defineComponent, N as Notyf, r as ref, t as reactive, L as watch, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, a6 as withDirectives, a7 as vModelText, Z as unref, D as createTextVNode, g as createElementBlock, y as renderSlot, z as toDisplayString, Y as normalizeClass, C as createCommentVNode, ac as useI18n, a5 as useRoute, a as computed, x as resolveComponent, w as createVNode, F as Fragment, A as renderList, a4 as useRouter, o as onMounted, at as TransitionGroup, e as useHead } from "./vendor.05b8b5b5.js";
import { a as useCompanySession, p as provideApi, t as themeColors, u as useUserSession } from "./index.2bb13d6f.js";
import { M as MasterService, g as useProductSession } from "./masterService.8a758d8d.js";
import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$8 } from "./VField.152a42ff.js";
import { _ as _sfc_main$9 } from "./VAvatar.0f0bd550.js";
import { _ as _sfc_main$7 } from "./CategoriesNavBar.5044304a.js";
import { p as pageTitle } from "./sidebarLayoutState.6ddf5d4e.js";
import "./plugin-vue_export-helper.5a098b48.js";
useCompanySession();
const SavePrice = async (payload) => {
  try {
    const { data } = await provideApi().post("/restaurant/items/price", payload);
    return data;
  } catch (err) {
    return { message: "No se pudo guardar los datos", success: false };
  }
};
const _hoisted_1$4 = { class: "modal-form" };
const _hoisted_2$3 = { class: "field" };
const _hoisted_3$3 = /* @__PURE__ */ createBaseVNode("label", null, "Precio *", -1);
const _hoisted_4$2 = { class: "control" };
const _hoisted_5$2 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_6$2 = /* @__PURE__ */ createTextVNode(" Button ");
const _hoisted_7$2 = /* @__PURE__ */ createTextVNode("Guardar");
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true },
    model: { type: Object, required: true }
  },
  emits: ["close", "save"],
  setup(__props, { emit }) {
    const props = __props;
    const notyf = new Notyf({
      duration: 4e3,
      position: {
        x: "right",
        y: "bottom"
      },
      types: [
        {
          type: "warning",
          background: themeColors.warning,
          icon: {
            className: "fas fa-check",
            tagName: "i",
            text: ""
          }
        }
      ]
    });
    let loading = ref(false);
    const form = reactive({
      id: null,
      sale_unit_price: 0
    });
    const save = async () => {
      if (form.sale_unit_price < 1) {
        return notyf.open({
          type: "warning",
          message: "El precio no puede ser menor o igual a 0."
        });
      }
      loading.value = true;
      const response = await SavePrice(form);
      if (response.success) {
        notyf.success(response.message);
        await MasterService.saveDataProducts();
      } else {
        notyf.error(response.message);
      }
      loading.value = false;
      emit("save");
    };
    watch(() => props.open, (newValue, oldValue) => {
      if (newValue) {
        const { id, price } = props.model;
        form.id = id;
        form.sale_unit_price = price;
      }
    });
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$5;
      const _component_VModal = _sfc_main$6;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Editar Precio",
        size: "small",
        actions: "right",
        onClose: _cache[2] || (_cache[2] = ($event) => emit("close"))
      }, {
        content: withCtx(() => [
          createBaseVNode("form", _hoisted_1$4, [
            createBaseVNode("div", _hoisted_2$3, [
              _hoisted_3$3,
              createBaseVNode("div", _hoisted_4$2, [
                withDirectives(createBaseVNode("input", {
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => unref(form).sale_unit_price = $event),
                  type: "text",
                  class: "input",
                  placeholder: "Precio"
                }, null, 512), [
                  [vModelText, unref(form).sale_unit_price]
                ])
              ])
            ]),
            _hoisted_5$2
          ])
        ]),
        action: withCtx(() => [
          unref(loading) ? (openBlock(), createBlock(_component_VButton, {
            key: 0,
            placeload: "40px",
            color: "primary"
          }, {
            default: withCtx(() => [
              _hoisted_6$2
            ]),
            _: 1
          })) : (openBlock(), createBlock(_component_VButton, {
            key: 1,
            color: "primary",
            raised: "",
            onClick: _cache[1] || (_cache[1] = ($event) => save())
          }, {
            default: withCtx(() => [
              _hoisted_7$2
            ]),
            _: 1
          }))
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
const _hoisted_1$3 = { class: "page-placeholder" };
const _hoisted_2$2 = { class: "placeholder-content" };
const _hoisted_3$2 = { class: "dark-inverted" };
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: void 0 },
    larger: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$3, [
        createBaseVNode("div", _hoisted_2$2, [
          renderSlot(_ctx.$slots, "image"),
          createBaseVNode("h3", _hoisted_3$2, toDisplayString(props.title), 1),
          props.subtitle ? (openBlock(), createElementBlock("p", {
            key: 0,
            class: normalizeClass([props.larger && "is-larger"])
          }, toDisplayString(props.subtitle), 3)) : createCommentVNode("", true),
          renderSlot(_ctx.$slots, "action")
        ])
      ]);
    };
  }
});
function block0(Component) {
  Component.__i18n = Component.__i18n || [];
  Component.__i18n.push({
    "locale": "",
    "resource": {
      "de": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Gehe zu Seite ", _interpolate(_named("page"))]);
        }
      },
      "en": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Goto page ", _interpolate(_named("page"))]);
        }
      },
      "es-MX": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Ir a la p\xE1gina ", _interpolate(_named("page"))]);
        }
      },
      "es": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Ir a la p\xE1gina ", _interpolate(_named("page"))]);
        }
      },
      "fr": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["Aller \xE0 la page ", _interpolate(_named("page"))]);
        }
      },
      "zh-CN": {
        "goto-page-title": (ctx) => {
          const { normalize: _normalize, interpolate: _interpolate, named: _named } = ctx;
          return _normalize(["\u8F6C\u5230\u7B2C", _interpolate(_named("page")), "\u9875"]);
        }
      }
    }
  });
}
const _hoisted_1$2 = {
  role: "navigation",
  class: "flex-pagination pagination is-rounded",
  "aria-label": "pagination",
  "data-filter-hide": ""
};
const _hoisted_2$1 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:chevron-left"
}, null, -1);
const _hoisted_3$1 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:chevron-right"
}, null, -1);
const _hoisted_4$1 = { class: "pagination-list" };
const _hoisted_5$1 = /* @__PURE__ */ createTextVNode(" 1 ");
const _hoisted_6$1 = { key: 0 };
const _hoisted_7$1 = /* @__PURE__ */ createBaseVNode("span", { class: "pagination-ellipsis" }, "\u2026", -1);
const _hoisted_8$1 = [
  _hoisted_7$1
];
const _hoisted_9$1 = { key: 1 };
const _hoisted_10$1 = /* @__PURE__ */ createBaseVNode("span", { class: "pagination-ellipsis" }, "\u2026", -1);
const _hoisted_11$1 = [
  _hoisted_10$1
];
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    itemPerPage: { type: Number, required: true },
    totalItems: { type: Number, required: true },
    currentPage: { type: Number, required: false, default: 1 },
    maxLinksDisplayed: { type: Number, required: false, default: 4 }
  },
  setup(__props) {
    const props = __props;
    const { t } = useI18n();
    const route = useRoute();
    const lastPage = computed(() => Math.ceil(props.totalItems / props.itemPerPage) || 1);
    const totalPageDisplayed = computed(() => lastPage.value > props.maxLinksDisplayed - 2 ? props.maxLinksDisplayed - 2 : lastPage.value);
    const pages = computed(() => {
      const _pages = [];
      let firstButton = props.currentPage - Math.floor(totalPageDisplayed.value / 2);
      let lastButton = firstButton + (totalPageDisplayed.value - Math.ceil(totalPageDisplayed.value % 2));
      if (firstButton < 1) {
        firstButton = 1;
        lastButton = firstButton + (totalPageDisplayed.value - 1);
      }
      if (lastButton > lastPage.value) {
        lastButton = lastPage.value;
        firstButton = lastButton - (totalPageDisplayed.value - 1);
      }
      for (let page = firstButton; page <= lastButton; page += 1) {
        if (page === firstButton || page === lastButton) {
          continue;
        }
        _pages.push(page);
      }
      return _pages;
    });
    computed(() => pages.value[0] > 1);
    computed(() => pages.value[pages.value.length - 1] < lastPage.value);
    const paginatedLink = (page = 1) => {
      const _page = Math.min(page, lastPage.value);
      const query = __spreadProps(__spreadValues({}, route.query), {
        page: _page <= 1 ? void 0 : _page
      });
      return {
        name: route.name,
        params: route.params,
        query
      };
    };
    return (_ctx, _cache) => {
      const _component_RouterLink = resolveComponent("RouterLink");
      return openBlock(), createElementBlock("nav", _hoisted_1$2, [
        unref(lastPage) > 1 ? (openBlock(), createBlock(_component_RouterLink, {
          key: 0,
          to: paginatedLink(__props.currentPage - 1),
          class: "pagination-previous has-chevron"
        }, {
          default: withCtx(() => [
            _hoisted_2$1
          ]),
          _: 1
        }, 8, ["to"])) : createCommentVNode("", true),
        unref(lastPage) > 1 ? (openBlock(), createBlock(_component_RouterLink, {
          key: 1,
          to: paginatedLink(__props.currentPage + 1),
          class: "pagination-next has-chevron"
        }, {
          default: withCtx(() => [
            _hoisted_3$1
          ]),
          _: 1
        }, 8, ["to"])) : createCommentVNode("", true),
        createBaseVNode("ul", _hoisted_4$1, [
          createBaseVNode("li", null, [
            createVNode(_component_RouterLink, {
              to: paginatedLink(1),
              class: normalizeClass(["pagination-link", [__props.currentPage === 1 && "is-current"]]),
              "aria-label": unref(t)("goto-page-title", { page: 1 })
            }, {
              default: withCtx(() => [
                _hoisted_5$1
              ]),
              _: 1
            }, 8, ["to", "aria-label", "class"])
          ]),
          unref(pages).length === 0 || unref(pages)[0] > 2 ? (openBlock(), createElementBlock("li", _hoisted_6$1, _hoisted_8$1)) : createCommentVNode("", true),
          (openBlock(true), createElementBlock(Fragment, null, renderList(unref(pages), (page) => {
            return openBlock(), createElementBlock("li", { key: page }, [
              createVNode(_component_RouterLink, {
                to: paginatedLink(page),
                class: normalizeClass(["pagination-link", [__props.currentPage === page && "is-current"]]),
                "aria-label": unref(t)("goto-page-title", { page }),
                "aria-current": __props.currentPage === page ? "page" : void 0
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(page), 1)
                ]),
                _: 2
              }, 1032, ["to", "aria-label", "aria-current", "class"])
            ]);
          }), 128)),
          unref(pages)[unref(pages).length - 1] < unref(lastPage) - 1 ? (openBlock(), createElementBlock("li", _hoisted_9$1, _hoisted_11$1)) : createCommentVNode("", true),
          createBaseVNode("li", null, [
            createVNode(_component_RouterLink, {
              to: paginatedLink(unref(lastPage)),
              class: normalizeClass(["pagination-link", [__props.currentPage === unref(lastPage) && "is-current"]]),
              "aria-label": unref(t)("goto-page-title", { page: unref(lastPage) })
            }, {
              default: withCtx(() => [
                createTextVNode(toDisplayString(unref(lastPage)), 1)
              ]),
              _: 1
            }, 8, ["to", "aria-label", "class"])
          ])
        ])
      ]);
    };
  }
});
if (typeof block0 === "function")
  block0(_sfc_main$2);
var PricesMaintenance_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$1 = { class: "mb-4" };
const _hoisted_2 = { class: "list-flex-toolbar flex-list-v1" };
const _hoisted_3 = { class: "tabs-wrapper" };
const _hoisted_4 = { class: "tabs-inner" };
const _hoisted_5 = { class: "tabs tabs-list tabs-list-pos is-centered mb-1" };
const _hoisted_6 = {
  style: { "border": "none" },
  class: "m-0"
};
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:grid"
  })
], -1);
const _hoisted_8 = [
  _hoisted_7
];
const _hoisted_9 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:list"
  })
], -1);
const _hoisted_10 = [
  _hoisted_9
];
const _hoisted_11 = /* @__PURE__ */ createBaseVNode("li", { class: "tab-naver" }, null, -1);
const _hoisted_12 = { class: "page-content-inner" };
const _hoisted_13 = { class: "flex-list-wrapper flex-list-v1" };
const _hoisted_14 = {
  key: 0,
  class: "columns is-multiline"
};
const _hoisted_15 = { class: "price-card" };
const _hoisted_16 = { class: "price-card-header" };
const _hoisted_17 = { class: "price-card-body" };
const _hoisted_18 = { class: "price-card-title" };
const _hoisted_19 = { class: "price-card-code" };
const _hoisted_20 = { class: "price-card-price" };
const _hoisted_21 = { class: "price-card-footer" };
const _hoisted_22 = /* @__PURE__ */ createTextVNode("Editar Precio");
const _hoisted_23 = {
  key: 1,
  class: "flex-table"
};
const _hoisted_24 = /* @__PURE__ */ createBaseVNode("span", { class: "is-grow" }, "Producto", -1);
const _hoisted_25 = /* @__PURE__ */ createBaseVNode("span", null, "Codigo", -1);
const _hoisted_26 = /* @__PURE__ */ createBaseVNode("span", null, "Precio", -1);
const _hoisted_27 = /* @__PURE__ */ createBaseVNode("span", { class: "cell-end" }, "Acciones", -1);
const _hoisted_28 = [
  _hoisted_24,
  _hoisted_25,
  _hoisted_26,
  _hoisted_27
];
const _hoisted_29 = { class: "flex-list-inner" };
const _hoisted_30 = { class: "flex-table-cell is-media is-grow" };
const _hoisted_31 = { class: "item-name dark-inverted" };
const _hoisted_32 = /* @__PURE__ */ createBaseVNode("span", { class: "item-meta" }, null, -1);
const _hoisted_33 = {
  class: "flex-table-cell",
  "data-th": "C\xF3digo"
};
const _hoisted_34 = { class: "light-text" };
const _hoisted_35 = {
  class: "flex-table-cell",
  "data-th": "Precio"
};
const _hoisted_36 = { class: "light-text" };
const _hoisted_37 = {
  class: "flex-table-cell cell-end",
  "data-th": "Acciones"
};
const _hoisted_38 = /* @__PURE__ */ createTextVNode("Editar Precio");
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const { getProducts } = useProductSession();
    const route = useRoute();
    const router = useRouter();
    const userSession = useUserSession();
    const companySession = useCompanySession();
    let openModalEdit = ref(false);
    const filters = ref(route.query.search || "");
    const selectedCategoryId = ref(Number(route.query.category) || 0);
    const isListItems = ref(false);
    const itemsPerPage = 12;
    const formEdit = reactive({
      id: 0,
      price: 0
    });
    const currentPage = computed(() => {
      const page = Number(route.query.page);
      return page > 0 ? page : 1;
    });
    const filteredData = computed(() => {
      let products = getProducts();
      if (selectedCategoryId.value !== 0) {
        products = products.filter((item) => item.categoryId === selectedCategoryId.value);
      }
      if (!filters.value) {
        return products;
      } else {
        return products.filter((item) => {
          const searchTerm = new RegExp(filters.value, "i");
          return item.name && item.name.match(searchTerm) || item.price && item.price.toString().match(searchTerm) || item.internalId && item.internalId.match(searchTerm);
        });
      }
    });
    const paginatedData = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      return filteredData.value.slice(start, end);
    });
    watch(selectedCategoryId, (newValue) => {
      var _a;
      const query = __spreadValues({}, route.query);
      if (newValue !== 0) {
        query.category = newValue.toString();
      } else {
        delete query.category;
      }
      delete query.page;
      router.push({
        name: (_a = route.name) != null ? _a : void 0,
        params: route.params,
        query
      });
    });
    const clickEdit = (id, price) => {
      formEdit.id = id;
      formEdit.price = price;
      openModalEdit.value = true;
    };
    const closeForm = () => {
      openModalEdit.value = false;
    };
    const saveForm = () => {
      openModalEdit.value = false;
    };
    const selectCategory = (id) => {
      selectedCategoryId.value = id;
    };
    watch(isListItems, () => {
      localStorage.setItem("isListItemsPrices", isListItems.value ? "true" : "false");
    });
    onMounted(() => {
      isListItems.value = localStorage.getItem("isListItemsPrices") === "true" ? true : false;
    });
    return (_ctx, _cache) => {
      const _component_PricesForm = _sfc_main$4;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$8;
      const _component_VPlaceholderPage = _sfc_main$3;
      const _component_VAvatar = _sfc_main$9;
      const _component_VButton = _sfc_main$5;
      const _component_VFlexPagination = _sfc_main$2;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_PricesForm, {
          open: unref(openModalEdit),
          model: unref(formEdit),
          onClose: closeForm,
          onSave: saveForm
        }, null, 8, ["open", "model"]),
        createBaseVNode("div", null, [
          createBaseVNode("div", _hoisted_1$1, [
            createVNode(_sfc_main$7, { onSelectcategory: selectCategory })
          ]),
          createBaseVNode("div", _hoisted_2, [
            createVNode(_component_VField, null, {
              default: withCtx(() => [
                createVNode(_component_VControl, { icon: "feather:search" }, {
                  default: withCtx(() => [
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => filters.value = $event),
                      class: "input custom-text-filter",
                      placeholder: "Buscar..."
                    }, null, 512), [
                      [vModelText, filters.value]
                    ])
                  ]),
                  _: 1
                })
              ]),
              _: 1
            }),
            createBaseVNode("div", _hoisted_3, [
              createBaseVNode("div", _hoisted_4, [
                createBaseVNode("div", _hoisted_5, [
                  createBaseVNode("ul", _hoisted_6, [
                    createBaseVNode("li", {
                      class: normalizeClass([isListItems.value == false && "is-active"])
                    }, [
                      createBaseVNode("a", {
                        onClick: _cache[1] || (_cache[1] = ($event) => isListItems.value = false)
                      }, _hoisted_8)
                    ], 2),
                    createBaseVNode("li", {
                      class: normalizeClass([isListItems.value == true && "is-active"])
                    }, [
                      createBaseVNode("a", {
                        onClick: _cache[2] || (_cache[2] = ($event) => isListItems.value = true)
                      }, _hoisted_10)
                    ], 2),
                    _hoisted_11
                  ])
                ])
              ])
            ])
          ]),
          createBaseVNode("div", _hoisted_12, [
            createBaseVNode("div", _hoisted_13, [
              createVNode(_component_VPlaceholderPage, {
                class: normalizeClass([unref(filteredData).length !== 0 && "is-hidden"]),
                title: "No se encontraron resultados.",
                larger: ""
              }, null, 8, ["class"]),
              !isListItems.value ? (openBlock(), createElementBlock("div", _hoisted_14, [
                (openBlock(true), createElementBlock(Fragment, null, renderList(unref(paginatedData), (item) => {
                  return openBlock(), createElementBlock("div", {
                    key: item.id,
                    class: "column is-3"
                  }, [
                    createBaseVNode("div", _hoisted_15, [
                      createBaseVNode("div", _hoisted_16, [
                        createVNode(_component_VAvatar, {
                          picture: item.imageUrl,
                          size: "large"
                        }, null, 8, ["picture"])
                      ]),
                      createBaseVNode("div", _hoisted_17, [
                        createBaseVNode("h3", _hoisted_18, toDisplayString(item.name), 1),
                        createBaseVNode("p", _hoisted_19, "C\xF3digo: " + toDisplayString(item.internalId), 1),
                        createBaseVNode("p", _hoisted_20, toDisplayString(item.currencyTypeSymbol) + " " + toDisplayString(item.price), 1)
                      ]),
                      createBaseVNode("div", _hoisted_21, [
                        unref(userSession).getPermissionEditItemPrices() || unref(companySession).configuration.allow_edit_unit_price_to_seller ? (openBlock(), createBlock(_component_VButton, {
                          key: 0,
                          color: "primary",
                          icon: "feather:edit-2",
                          fullwidth: "",
                          onClick: ($event) => clickEdit(item.id, item.price)
                        }, {
                          default: withCtx(() => [
                            _hoisted_22
                          ]),
                          _: 2
                        }, 1032, ["onClick"])) : createCommentVNode("", true)
                      ])
                    ])
                  ]);
                }), 128))
              ])) : (openBlock(), createElementBlock("div", _hoisted_23, [
                createBaseVNode("div", {
                  class: normalizeClass(["flex-table-header", [unref(filteredData).length === 0 && "is-hidden"]])
                }, _hoisted_28, 2),
                createBaseVNode("div", _hoisted_29, [
                  createVNode(TransitionGroup, {
                    name: "list",
                    tag: "div"
                  }, {
                    default: withCtx(() => [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(paginatedData), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          class: "flex-table-item"
                        }, [
                          createBaseVNode("div", _hoisted_30, [
                            createVNode(_component_VAvatar, {
                              picture: item.imageUrl,
                              size: "medium"
                            }, null, 8, ["picture"]),
                            createBaseVNode("div", null, [
                              createBaseVNode("span", _hoisted_31, toDisplayString(item.name), 1),
                              _hoisted_32
                            ])
                          ]),
                          createBaseVNode("div", _hoisted_33, [
                            createBaseVNode("span", _hoisted_34, toDisplayString(item.internalId), 1)
                          ]),
                          createBaseVNode("div", _hoisted_35, [
                            createBaseVNode("span", _hoisted_36, toDisplayString(item.currencyTypeSymbol) + " " + toDisplayString(item.price), 1)
                          ]),
                          createBaseVNode("div", _hoisted_37, [
                            unref(userSession).getPermissionEditItemPrices() || unref(companySession).configuration.allow_edit_unit_price_to_seller ? (openBlock(), createBlock(_component_VButton, {
                              key: 0,
                              icon: "feather:edit-2",
                              onClick: ($event) => clickEdit(item.id, item.price)
                            }, {
                              default: withCtx(() => [
                                _hoisted_38
                              ]),
                              _: 2
                            }, 1032, ["onClick"])) : createCommentVNode("", true)
                          ])
                        ]);
                      }), 128))
                    ]),
                    _: 1
                  })
                ])
              ])),
              unref(filteredData).length > itemsPerPage ? (openBlock(), createBlock(_component_VFlexPagination, {
                key: 2,
                "item-per-page": itemsPerPage,
                "total-items": unref(filteredData).length,
                "current-page": unref(currentPage),
                "max-links-displayed": 7
              }, null, 8, ["total-items", "current-page"])) : createCommentVNode("", true)
            ])
          ])
        ])
      ], 64);
    };
  }
});
const _hoisted_1 = { class: "page-content-inner" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    pageTitle.value = "Editar Precios";
    useHead({
      title: "Editar Precios"
    });
    return (_ctx, _cache) => {
      const _component_PricesMaintenance = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_PricesMaintenance)
      ]);
    };
  }
});
export { _sfc_main as default };
