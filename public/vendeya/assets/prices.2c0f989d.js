import { _ as _sfc_main$4 } from "./VButton.ec3d6c22.js";
import { _ as _sfc_main$5, a as _sfc_main$7 } from "./VModal.976b2731.js";
import { b as defineComponent, N as Notyf, r as ref, t as reactive, L as watch, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, a6 as withDirectives, a7 as vModelText, Z as unref, D as createTextVNode, g as createElementBlock, y as renderSlot, z as toDisplayString, Y as normalizeClass, C as createCommentVNode, a as computed, o as onMounted, P as onUnmounted, w as createVNode, F as Fragment, A as renderList, au as TransitionGroup, e as useHead } from "./vendor.80d481a8.js";
import { p as provideApi, t as themeColors } from "./index.ea91e044.js";
import { u as useCompanySession, M as MasterService, a as useProductSession } from "./masterService.8bbc48d4.js";
import { _ as __unplugin_components_1 } from "./VControl.55044e40.js";
import { _ as _sfc_main$6 } from "./VField.dd3df504.js";
import { p as pageTitle } from "./sidebarLayoutState.1370c93c.js";
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
const _hoisted_1$3 = { class: "modal-form" };
const _hoisted_2$2 = { class: "field" };
const _hoisted_3$2 = /* @__PURE__ */ createBaseVNode("label", null, "Precio *", -1);
const _hoisted_4$1 = { class: "control" };
const _hoisted_5$1 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_6$1 = /* @__PURE__ */ createTextVNode(" Button ");
const _hoisted_7$1 = /* @__PURE__ */ createTextVNode("Guardar");
const _sfc_main$3 = /* @__PURE__ */ defineComponent({
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
      if (form.sale_unit_price <= 0) {
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
        window.location.reload();
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
      const _component_VButton = _sfc_main$4;
      const _component_VModal = _sfc_main$5;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Editar Precio",
        size: "small",
        actions: "right",
        onClose: _cache[2] || (_cache[2] = ($event) => emit("close"))
      }, {
        content: withCtx(() => [
          createBaseVNode("form", _hoisted_1$3, [
            createBaseVNode("div", _hoisted_2$2, [
              _hoisted_3$2,
              createBaseVNode("div", _hoisted_4$1, [
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
            _hoisted_5$1
          ])
        ]),
        action: withCtx(() => [
          unref(loading) ? (openBlock(), createBlock(_component_VButton, {
            key: 0,
            placeload: "40px",
            color: "primary"
          }, {
            default: withCtx(() => [
              _hoisted_6$1
            ]),
            _: 1
          })) : (openBlock(), createBlock(_component_VButton, {
            key: 1,
            color: "primary",
            raised: "",
            onClick: _cache[1] || (_cache[1] = ($event) => save())
          }, {
            default: withCtx(() => [
              _hoisted_7$1
            ]),
            _: 1
          }))
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
const _hoisted_1$2 = { class: "page-placeholder" };
const _hoisted_2$1 = { class: "placeholder-content" };
const _hoisted_3$1 = { class: "dark-inverted" };
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: void 0 },
    larger: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$2, [
        createBaseVNode("div", _hoisted_2$1, [
          renderSlot(_ctx.$slots, "image"),
          createBaseVNode("h3", _hoisted_3$1, toDisplayString(props.title), 1),
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
var PricesMaintenance_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$1 = { class: "list-flex-toolbar flex-list-v1" };
const _hoisted_2 = { class: "page-content-inner" };
const _hoisted_3 = { class: "flex-list-wrapper flex-list-v1" };
const _hoisted_4 = { class: "flex-table" };
const _hoisted_5 = /* @__PURE__ */ createBaseVNode("span", { class: "is-grow" }, "Producto", -1);
const _hoisted_6 = /* @__PURE__ */ createBaseVNode("span", null, "Codigo", -1);
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("span", null, "Precio", -1);
const _hoisted_8 = /* @__PURE__ */ createBaseVNode("span", { class: "cell-end" }, "Acciones", -1);
const _hoisted_9 = [
  _hoisted_5,
  _hoisted_6,
  _hoisted_7,
  _hoisted_8
];
const _hoisted_10 = { class: "flex-list-inner" };
const _hoisted_11 = { class: "flex-table-cell is-media is-grow" };
const _hoisted_12 = { class: "item-name dark-inverted" };
const _hoisted_13 = /* @__PURE__ */ createBaseVNode("span", { class: "item-meta" }, null, -1);
const _hoisted_14 = {
  class: "flex-table-cell",
  "data-th": "C\xF3digo"
};
const _hoisted_15 = { class: "light-text" };
const _hoisted_16 = {
  class: "flex-table-cell",
  "data-th": "Precio"
};
const _hoisted_17 = { class: "light-text" };
const _hoisted_18 = {
  class: "flex-table-cell cell-end",
  "data-th": "Acciones"
};
const _hoisted_19 = /* @__PURE__ */ createTextVNode("Editar Precio");
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const productSession = useProductSession();
    const productIndexByBarcode = new Map();
    const productIndexByInternalId = new Map();
    const filteredProducts = ref(productSession.products);
    const chunks = ref([]);
    const chunkSize = 50;
    const visibleProductCount = ref(chunkSize);
    productSession.products.forEach((product) => {
      if (product.barcode) {
        productIndexByBarcode.set(product.barcode, product);
      }
      if (product.internalId) {
        productIndexByInternalId.set(product.internalId.toLowerCase(), product);
      }
    });
    let openModalEdit = ref(false);
    const filters = ref("");
    const formEdit = reactive({
      id: 0,
      price: 0
    });
    let debounceTimeout = null;
    const debouncedSearch = (callback, delay = 100) => {
      if (debounceTimeout)
        clearTimeout(debounceTimeout);
      debounceTimeout = setTimeout(callback, delay);
    };
    const loadProducts = () => {
      const allProducts = productSession.products;
      chunks.value = [];
      for (let i = 0; i < allProducts.length; i += chunkSize) {
        chunks.value.push(allProducts.slice(i, i + chunkSize));
      }
    };
    const removeAccents = (name) => {
      return name ? name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
    };
    const products_filter = computed(() => filteredProducts.value);
    watch(filters, () => {
      if (filters.value.length > 0) {
        debouncedSearch(() => {
          const lowerSearchText = filters.value.toLowerCase();
          filteredProducts.value = chunks.value.flatMap((chunk) => chunk.filter((item) => {
            var _a;
            return removeAccents(item.name || "").toLowerCase().includes(removeAccents(lowerSearchText)) || productIndexByInternalId.has(lowerSearchText) && ((_a = productIndexByInternalId.get(lowerSearchText)) == null ? void 0 : _a.internalId) === item.internalId;
          }));
        });
      } else {
        debounceTimeout && clearTimeout(debounceTimeout);
        filteredProductsDefault();
      }
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
    const loadMoreProducts = () => {
      if (visibleProductCount.value < productSession.products.length) {
        visibleProductCount.value += chunkSize;
        filteredProducts.value = productSession.products.slice(0, visibleProductCount.value);
      }
    };
    const filteredProductsDefault = () => {
      filteredProducts.value = productSession.products.slice(0, visibleProductCount.value);
    };
    const onScroll = () => {
      const scrollTop = window.scrollY;
      const windowHeight = window.innerHeight;
      const docHeight = document.documentElement.scrollHeight;
      if (scrollTop + windowHeight >= docHeight - 100 && filters.value.length <= 0) {
        loadMoreProducts();
      }
    };
    onMounted(() => {
      filteredProductsDefault();
      window.addEventListener("scroll", onScroll);
      loadProducts();
    });
    onUnmounted(() => {
      window.removeEventListener("scroll", onScroll);
    });
    return (_ctx, _cache) => {
      const _component_PricesForm = _sfc_main$3;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$6;
      const _component_VPlaceholderPage = _sfc_main$2;
      const _component_VAvatar = _sfc_main$7;
      const _component_VButton = _sfc_main$4;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_PricesForm, {
          open: unref(openModalEdit),
          model: unref(formEdit),
          onClose: closeForm,
          onSave: saveForm
        }, null, 8, ["open", "model"]),
        createBaseVNode("div", null, [
          createBaseVNode("div", _hoisted_1$1, [
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
            })
          ]),
          createBaseVNode("div", _hoisted_2, [
            createBaseVNode("div", _hoisted_3, [
              createVNode(_component_VPlaceholderPage, {
                class: normalizeClass([unref(products_filter).length !== 0 && "is-hidden"]),
                title: "No se encontraron resultados.",
                larger: ""
              }, null, 8, ["class"]),
              createBaseVNode("div", _hoisted_4, [
                createBaseVNode("div", {
                  class: normalizeClass(["flex-table-header", [unref(products_filter).length === 0 && "is-hidden"]])
                }, _hoisted_9, 2),
                createBaseVNode("div", _hoisted_10, [
                  createVNode(TransitionGroup, {
                    name: "list",
                    tag: "div"
                  }, {
                    default: withCtx(() => [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(products_filter), (item) => {
                        return openBlock(), createElementBlock("div", {
                          key: item.id,
                          class: "flex-table-item"
                        }, [
                          createBaseVNode("div", _hoisted_11, [
                            createVNode(_component_VAvatar, {
                              picture: item.imageUrl,
                              size: "medium"
                            }, null, 8, ["picture"]),
                            createBaseVNode("div", null, [
                              createBaseVNode("span", _hoisted_12, toDisplayString(item.name), 1),
                              _hoisted_13
                            ])
                          ]),
                          createBaseVNode("div", _hoisted_14, [
                            createBaseVNode("span", _hoisted_15, toDisplayString(item.internalId), 1)
                          ]),
                          createBaseVNode("div", _hoisted_16, [
                            createBaseVNode("span", _hoisted_17, toDisplayString(item.currencyTypeSymbol) + " " + toDisplayString(item.price), 1)
                          ]),
                          createBaseVNode("div", _hoisted_18, [
                            createVNode(_component_VButton, {
                              icon: "feather:edit-2",
                              onClick: ($event) => clickEdit(item.id, item.price)
                            }, {
                              default: withCtx(() => [
                                _hoisted_19
                              ]),
                              _: 2
                            }, 1032, ["onClick"])
                          ])
                        ]);
                      }), 128))
                    ]),
                    _: 1
                  })
                ])
              ])
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
