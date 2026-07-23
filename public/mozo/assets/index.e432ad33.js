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
import { _ as _sfc_main$8 } from "./LockedScreen.14a12adf.js";
import { u as useBagStore, s as sectionsData, P as ProductModifiersDialog, f as _imports_0, a as _sfc_main$2, _ as _sfc_main$3, g as _sfc_main$6, h as __unplugin_components_7, i as __unplugin_components_8 } from "./ProductModifiersDialog.637fd331.js";
import { _ as _sfc_main$4 } from "./CategoriesNavBar.5044304a.js";
import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$5 } from "./VField.152a42ff.js";
import { _ as _sfc_main$7 } from "./VAvatar.0f0bd550.js";
import { b as defineComponent, r as ref, a as computed, t as reactive, L as watch, O as nextTick, o as onMounted, ag as onBeforeUnmount, f as openBlock, g as createElementBlock, w as createVNode, Z as unref, X as createBaseVNode, B as withCtx, a6 as withDirectives, a7 as vModelText, aq as vModelCheckbox, Y as normalizeClass, z as toDisplayString, F as Fragment, A as renderList, I as withModifiers, at as TransitionGroup, C as createCommentVNode, e as useHead } from "./vendor.05b8b5b5.js";
import { R as RestaurantService } from "./restaurantService.f8e320da.js";
import { u as useUserSession, c as useNotyf } from "./index.2bb13d6f.js";
import { g as useProductSession } from "./masterService.8a758d8d.js";
import { p as pageTitle } from "./navbarLayoutState.43b7723b.js";
import "./VButton.b4d67cab.js";
import "./plugin-vue_export-helper.5a098b48.js";
import "./VModal.9533b2d3.js";
import "./VLoader.a784d703.js";
import "./VIconButton.dcb74fc8.js";
var PosRestaurant_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$1 = { class: "food-delivery-dashboard" };
const _hoisted_2 = { class: "left" };
const _hoisted_3 = { class: "left-body" };
const _hoisted_4 = { class: "restaurants" };
const _hoisted_5 = { class: "columns is-multiline is-mobile" };
const _hoisted_6 = {
  class: "column is-12 category-content",
  style: { "padding-bottom": "0px" }
};
const _hoisted_7 = { class: "pt-5 searchCategory-content columns mt-0" };
const _hoisted_8 = /* @__PURE__ */ createBaseVNode("img", {
  src: _imports_0,
  alt: "Buscar",
  class: "search-icon"
}, null, -1);
const _hoisted_9 = {
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 14 14"
};
const _hoisted_10 = ["stroke"];
const _hoisted_11 = { class: "tooltip-text" };
const _hoisted_12 = { class: "column is-2 px-0" };
const _hoisted_13 = { class: "tabs-wrapper" };
const _hoisted_14 = { class: "tabs-inner" };
const _hoisted_15 = { class: "tabs tabs-list tabs-list-pos is-centered mb-1" };
const _hoisted_16 = {
  style: { "border": "none" },
  class: "m-0"
};
const _hoisted_17 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:grid"
  })
], -1);
const _hoisted_18 = [
  _hoisted_17
];
const _hoisted_19 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:list"
  })
], -1);
const _hoisted_20 = [
  _hoisted_19
];
const _hoisted_21 = /* @__PURE__ */ createBaseVNode("li", { class: "tab-naver" }, null, -1);
const _hoisted_22 = {
  key: 0,
  class: "restaurants-list pt-2"
};
const _hoisted_23 = {
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
const _hoisted_44 = {
  key: 0,
  class: "is-hidden-desktop cart-bottom-fixed h-hidden-tablet-l"
};
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
const _hoisted_58 = { class: "right fixed-parent is-hidden-mobile h-hidden-tablet-p only-pos" };
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const userSession = useUserSession();
    const productSession = useProductSession();
    const notif = useNotyf();
    const bagRef = ref();
    const isCartCollapsed = ref(false);
    const toggleCartCollapse = () => {
      isCartCollapsed.value = !isCartCollapsed.value;
    };
    const openModifiersDialog = ref(false);
    const productForModifiers = ref(null);
    const isPorConsumo = computed(() => {
      const products = bagStore.getProducts();
      return products.length === 1 && products[0].name === "Por consumo";
    });
    const productsForView = computed(() => bagStore.getProducts());
    const textSearch = ref("");
    const searchByBarcode = ref(false);
    const bagStore = useBagStore();
    const isListItems = ref(false);
    const isBarcodeActive = ref(false);
    const tooltipMessage = ref("Activar b\xFAsqueda por c\xF3digo de barras");
    const chunks = ref([]);
    const chunkSize = 500;
    let stockIntervalId = void 0;
    const posBag = reactive({
      products: [],
      total: 0,
      barman: userSession.name
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
    const filteredProducts = ref([]);
    const productGridClass = computed(() => {
      return filteredProducts.value.length <= 2 ? "product-container-limited" : "";
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
      bagStore.addProduct(item);
    };
    const handleModifiersConfirm = (data) => {
      const { units } = data;
      units.forEach((unitData) => {
        const { product, selectedModifiers, stockItems, additionalPrice } = unitData;
        const modifiersSignature = selectedModifiers.map((group) => `${group.groupName}:${group.items.map((i) => i.name).sort().join(",")}`).sort().join("|");
        const existingIndex = bagStore.getProducts().findIndex((p) => p.id === product.id && p.modifiersSignature === modifiersSignature);
        if (existingIndex >= 0) {
          const existing = bagStore.getProducts()[existingIndex];
          existing.quantity += 1;
          existing.quantity_pending += 1;
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
          bagStore.getProducts().push(productToAdd);
        }
      });
      bagStore.calculaTotalBag && bagStore.calculaTotalBag();
      openModifiersDialog.value = false;
      productForModifiers.value = null;
      notif.success(`${units.length} producto${units.length > 1 ? "s agregados" : " agregado"} con modificadores`);
    };
    const handleModifiersClose = () => {
      openModifiersDialog.value = false;
      productForModifiers.value = null;
    };
    let isUpdating = false;
    watch(() => productSession.getProducts(), () => {
      if (!isUpdating) {
        isUpdating = true;
        loadProducts();
        nextTick(() => {
          isUpdating = false;
        });
      }
    });
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
      const products = bagStore.getProducts();
      if (products.length > 0) {
        if (products.length === 1 && products[0].esFusionado && Array.isArray(products[0].productosFusionados)) {
          posBag.products = products[0].productosFusionados.map((p) => __spreadProps(__spreadValues({}, p), {
            esFusionado: true
          }));
        } else {
          posBag.products = JSON.parse(JSON.stringify(products));
        }
        posBag.total = bagStore.getTotal();
        posBag.totalDiscount = bagStore.totalDiscount;
        state.openDialogDocument = true;
      }
    };
    const closeDialogDocument = () => {
      if (bagRef.value) {
        bagRef.value.restoreAllProductsToOriginal();
        bagRef.value.clearEditedFields();
      }
      bagStore.reset();
      posBag.products = [];
      posBag.total = 0;
      posBag.enterAmount = 0;
      state.openDialogDocument = false;
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
      state.products = sorted;
      state.products_filter = sorted;
      chunks.value = [];
      for (let i = 0; i < sorted.length; i += chunkSize) {
        chunks.value.push(sorted.slice(i, i + chunkSize));
      }
      filteredProducts.value = sorted.length >= 500 ? sorted.slice(0, 500) : sorted;
      productIndexByBarcode.clear();
      productIndexByInternalId.clear();
      sorted.forEach((product) => {
        if (product.barcode) {
          productIndexByBarcode.set(product.barcode, product);
        }
        if (product.internalId) {
          productIndexByInternalId.set(product.internalId.toLowerCase(), product);
        }
      });
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
              bagStore.addProduct(itemFound);
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
    const back = () => {
      console.log("back");
      state.openDialogDocument = false;
    };
    const selectCategory = (id) => {
      textSearch.value = "";
      if (id === 0) {
        filteredProducts.value = state.products_filter;
      } else {
        filteredProducts.value = state.products_filter.filter((x) => x.categoryId === id);
      }
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
    return (_ctx, _cache) => {
      const _component_CashDialog = _sfc_main$2;
      const _component_DocumentDialog = _sfc_main$3;
      const _component_CategoriesNavBar = _sfc_main$4;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$5;
      const _component_ProductPos = _sfc_main$6;
      const _component_VAvatar = _sfc_main$7;
      const _component_CartMobile = __unplugin_components_7;
      const _component_Bag = __unplugin_components_8;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_CashDialog),
        createVNode(_component_DocumentDialog, {
          "pos-bag": unref(posBag),
          open: unref(state).openDialogDocument,
          "products-for-view": unref(productsForView),
          onClose: closeDialogDocument,
          onBack: back
        }, null, 8, ["pos-bag", "open", "products-for-view"]),
        createVNode(ProductModifiersDialog, {
          open: openModifiersDialog.value,
          product: productForModifiers.value,
          onClose: handleModifiersClose,
          onConfirm: handleModifiersConfirm
        }, null, 8, ["open", "product"]),
        createBaseVNode("div", _hoisted_1$1, [
          createBaseVNode("div", _hoisted_2, [
            createBaseVNode("div", _hoisted_3, [
              createBaseVNode("div", _hoisted_4, [
                createBaseVNode("div", _hoisted_5, [
                  createBaseVNode("div", _hoisted_6, [
                    createVNode(_component_CategoriesNavBar, { onSelectcategory: selectCategory })
                  ]),
                  createBaseVNode("div", _hoisted_7, [
                    createVNode(_component_VField, { class: "search-conteiner column is-10" }, {
                      default: withCtx(() => [
                        createVNode(_component_VControl, { class: "input-container" }, {
                          default: withCtx(() => [
                            _hoisted_8,
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => textSearch.value = $event),
                              type: "text",
                              class: "input input-search-pos is-rounded",
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
                                class: normalizeClass(["barcode-icon", { active: isBarcodeActive.value }])
                              }, [
                                (openBlock(), createElementBlock("svg", _hoisted_9, [
                                  createBaseVNode("path", {
                                    fill: "none",
                                    stroke: isBarcodeActive.value ? "#4caf50" : "#cfcfcf",
                                    "stroke-linecap": "round",
                                    "stroke-linejoin": "round",
                                    d: "M5.11.656H1.208a.49.49 0 0 0-.488.488v3.904m12.686 0V1.144a.49.49 0 0 0-.488-.488H9.014m0 12.688h3.904a.49.49 0 0 0 .488-.488V8.952m-12.687 0v3.904a.49.49 0 0 0 .488.488H5.11m5.696-9.552v6.416M3.194 3.792v6.416m5.709-6.416v4.666m0 1.75v-.291M7 3.792v4.666m0 1.75v-.291M5.097 3.792v4.666m0 1.75v-.291"
                                  }, null, 8, _hoisted_10)
                                ]))
                              ], 2),
                              createBaseVNode("span", _hoisted_11, toDisplayString(tooltipMessage.value), 1)
                            ])
                          ]),
                          _: 1
                        })
                      ]),
                      _: 1
                    }),
                    createBaseVNode("div", _hoisted_12, [
                      createBaseVNode("div", _hoisted_13, [
                        createBaseVNode("div", _hoisted_14, [
                          createBaseVNode("div", _hoisted_15, [
                            createBaseVNode("ul", _hoisted_16, [
                              createBaseVNode("li", {
                                class: normalizeClass([isListItems.value == false && "is-active"])
                              }, [
                                createBaseVNode("a", {
                                  onClick: _cache[2] || (_cache[2] = ($event) => isListItems.value = false)
                                }, _hoisted_18)
                              ], 2),
                              createBaseVNode("li", {
                                class: normalizeClass([isListItems.value == true && "is-active"])
                              }, [
                                createBaseVNode("a", {
                                  onClick: _cache[3] || (_cache[3] = ($event) => isListItems.value = true)
                                }, _hoisted_20)
                              ], 2),
                              _hoisted_21
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                !isListItems.value ? (openBlock(), createElementBlock("div", _hoisted_22, [
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
                ])) : (openBlock(), createElementBlock("div", _hoisted_23, [
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
          unref(bagStore).getProducts().length >= 0 ? (openBlock(), createElementBlock("div", _hoisted_44, [
            createBaseVNode("div", _hoisted_45, [
              createBaseVNode("button", {
                class: "btn-collapse px-2",
                onClick: toggleCartCollapse
              }, [
                isCartCollapsed.value ? (openBlock(), createElementBlock("svg", _hoisted_46, _hoisted_49)) : (openBlock(), createElementBlock("svg", _hoisted_50, _hoisted_53))
              ]),
              !isCartCollapsed.value ? (openBlock(), createElementBlock("div", _hoisted_54, [
                createVNode(_component_CartMobile, { onFinalizeSale: openDialogDocument })
              ])) : createCommentVNode("", true),
              isCartCollapsed.value ? (openBlock(), createElementBlock("div", _hoisted_55, _hoisted_57)) : createCommentVNode("", true)
            ])
          ])) : createCommentVNode("", true),
          createBaseVNode("div", _hoisted_58, [
            createVNode(_component_Bag, {
              ref: (_value, _refs) => {
                _refs["bagRef"] = _value;
                bagRef.value = _value;
              },
              onFinalizeSale: openDialogDocument
            }, null, 512)
          ])
        ])
      ], 64);
    };
  }
});
const _hoisted_1 = { class: "page-content-inner" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    useHead({
      title: pageTitle
    });
    return (_ctx, _cache) => {
      const _component_LockedScreen = _sfc_main$8;
      const _component_PosRestaurant = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_LockedScreen),
        createVNode(_component_PosRestaurant)
      ]);
    };
  }
});
export { _sfc_main as default };
