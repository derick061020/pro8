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
import { _ as __unplugin_components_1 } from "./VControl.55044e40.js";
import { _ as _sfc_main$h } from "./VField.dd3df504.js";
import { _ as _sfc_main$i } from "./VButton.ec3d6c22.js";
import { _ as _sfc_main$j, u as useViaPlaceholderError, a as _sfc_main$m } from "./VModal.976b2731.js";
import { d as defineStore, u as useStorage, a as computed, b as defineComponent, N as Notyf, a5 as useRoute, t as reactive, r as ref, o as onMounted, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, w as createVNode, a6 as withDirectives, ak as vModelSelect, g as createElementBlock, A as renderList, z as toDisplayString, F as Fragment, Z as unref, D as createTextVNode, ar as createSlots, I as withModifiers, C as createCommentVNode, y as renderSlot, Y as normalizeClass, a7 as vModelText, a9 as useCssVars, ai as commonjsGlobal, L as watch, ae as onBeforeUnmount, as as vShow, j as axios, al as resolveDirective, G as pushScopeId, H as popScopeId, P as onUnmounted, at as vModelCheckbox, au as TransitionGroup, e as useHead } from "./vendor.80d481a8.js";
import { N as NAME_ROUTE_POS, d as NAME_ROUTE_MESAS, a as useNotyf, u as useUserSession, p as provideApi } from "./index.ea91e044.js";
import { u as useCompanySession, a as useProductSession, M as MasterService } from "./masterService.8bbc48d4.js";
import { a as _sfc_main$k, s as startConnection, q as qz, _ as _sfc_main$l, g as getUpdatedConfig, d as displayError, p as pageTitle } from "./VIconButton.f9e7ed06.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
const useCashSession = defineStore("cashSession", () => {
  const notif = useNotyf();
  const defaultData = {
    beginningBalance: 0,
    dateOpening: "0",
    timeOpening: "0",
    documents: [],
    referenceNumber: "0",
    documentsSynchronized: [],
    notes: [],
    allowClose: false
  };
  const cash = useStorage("cash", defaultData);
  const cashPos = useStorage("cashPos", defaultData);
  const getCash = computed(() => cash.value.dateOpening != "0" && cash.value.timeOpening != "0" ? cash.value : null);
  const getCashPos = computed(() => cashPos.value.dateOpening != "0" && cashPos.value.timeOpening != "0" ? cashPos.value : null);
  function setCash(data, nameRoute) {
    if (nameRoute == NAME_ROUTE_POS) {
      cashPos.value = data;
    }
    if (nameRoute == NAME_ROUTE_MESAS) {
      cash.value = data;
    }
  }
  function setCloseCash() {
    cashPos.value.allowClose = true;
  }
  function setOpenCash() {
    cashPos.value.allowClose = false;
  }
  function addDocumentToCash(document2, nameRoute) {
    if (nameRoute == NAME_ROUTE_POS) {
      if (getCashPos.value != null) {
        cashPos.value.documents.push(document2);
      }
    }
    if (nameRoute == NAME_ROUTE_MESAS) {
      cash.value.documents.push(document2);
    }
  }
  function cleanCash(nameRoute) {
    if (nameRoute == NAME_ROUTE_POS) {
      cashPos.value = defaultData;
    }
    if (nameRoute == NAME_ROUTE_MESAS) {
      cash.value = defaultData;
    }
    notif.success("Caja cerrada exitosamente");
  }
  function addDocumentSyncronized(document2, nameRoute) {
    if (nameRoute == NAME_ROUTE_POS) {
      if (getCashPos.value != null) {
        cashPos.value.documentsSynchronized.push(document2);
      }
    }
    if (nameRoute == NAME_ROUTE_MESAS) {
      cash.value.documentsSynchronized.push(document2);
    }
  }
  function addSaleNoteToCash(saleNote, nameRoute) {
    if (nameRoute == NAME_ROUTE_POS) {
      if (getCashPos.value != null) {
        console.log("hay caja abierta");
        cashPos.value.notes.push(saleNote);
      }
    }
    if (nameRoute == NAME_ROUTE_MESAS) {
      cash.value.notes.push(saleNote);
    }
  }
  function getCashMovements(nameRoute) {
    const rows = [];
    let listSync = cash.value.documentsSynchronized;
    let listDocuments = cash.value.documents;
    let listSaleNotes = cash.value.notes;
    if (nameRoute == NAME_ROUTE_POS) {
      listSync = cashPos.value.documentsSynchronized;
      listDocuments = cashPos.value.documents;
      listSaleNotes = cashPos.value.notes;
    }
    listSync.forEach((row) => {
      const cpe = {
        type: row.type,
        customer: row.customer,
        date: row.date,
        total: row.total,
        igv: row.igv,
        number: row.number
      };
      rows.push(cpe);
    });
    listDocuments.forEach((row) => {
      const document2 = {
        type: row.codigo_tipo_documento == "1" ? "FACTURA" : "BOLETA",
        customer: row.datos_del_cliente_o_receptor,
        date: row.fecha_de_emision,
        total: row.totales.total_venta,
        igv: row.totales.total_igv
      };
      rows.push(document2);
    });
    listSaleNotes.forEach((row) => {
      const saleNote = {
        type: "NOTA",
        customer: row.datos_del_cliente_o_receptor,
        date: row.date_of_issue,
        total: row.total,
        igv: row.total_taxes
      };
      rows.push(saleNote);
    });
    return rows;
  }
  return {
    setCash,
    getCash,
    getCashPos,
    cashPos,
    addDocumentToCash,
    cleanCash,
    addDocumentSyncronized,
    getCashMovements,
    addSaleNoteToCash,
    setCloseCash,
    setOpenCash
  };
});
var LockedScreen_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$e = { class: "columns is-multiline is-mobile" };
const _hoisted_2$b = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, "Seleccione un vendedor")
], -1);
const _hoisted_3$a = { class: "column is-12" };
const _hoisted_4$a = { class: "select" };
const _hoisted_5$9 = ["value"];
const _hoisted_6$9 = { class: "columns is-multiline is-mobile" };
const _hoisted_7$9 = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, "Ingresar PIN")
], -1);
const _hoisted_8$8 = { class: "column is-12 has-text-centered is-flex is-flex-direction-column is-align-self-flex-end" };
const _hoisted_9$8 = {
  key: 0,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_10$8 = {
  key: 1,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_11$8 = { class: "column is-12" };
const _hoisted_12$7 = { class: "columns is-multiline is-mobile has-text-centered" };
const _hoisted_13$7 = { class: "column is-4" };
const _hoisted_14$7 = /* @__PURE__ */ createTextVNode("1");
const _hoisted_15$7 = { class: "column is-4" };
const _hoisted_16$7 = /* @__PURE__ */ createTextVNode("2");
const _hoisted_17$6 = { class: "column is-4" };
const _hoisted_18$6 = /* @__PURE__ */ createTextVNode("3");
const _hoisted_19$5 = { class: "column is-4 pt-0" };
const _hoisted_20$5 = /* @__PURE__ */ createTextVNode("4");
const _hoisted_21$4 = { class: "column is-4 pt-0" };
const _hoisted_22$4 = /* @__PURE__ */ createTextVNode("5");
const _hoisted_23$4 = { class: "column is-4 pt-0" };
const _hoisted_24$4 = /* @__PURE__ */ createTextVNode("6");
const _hoisted_25$4 = { class: "column is-4 pt-0" };
const _hoisted_26$4 = /* @__PURE__ */ createTextVNode("7");
const _hoisted_27$4 = { class: "column is-4 pt-0" };
const _hoisted_28$4 = /* @__PURE__ */ createTextVNode("8");
const _hoisted_29$4 = { class: "column is-4 pt-0" };
const _hoisted_30$4 = /* @__PURE__ */ createTextVNode("9");
const _hoisted_31$3 = { class: "column is-4 pt-0" };
const _hoisted_32$3 = /* @__PURE__ */ createTextVNode("Limpiar");
const _hoisted_33$2 = { class: "column is-4 pt-0" };
const _hoisted_34$2 = /* @__PURE__ */ createTextVNode("0");
const _hoisted_35$2 = { class: "column is-4 pt-0" };
const _hoisted_36$2 = /* @__PURE__ */ createTextVNode("Borrar");
const _sfc_main$g = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const notyf = new Notyf();
    useCashSession();
    useRoute();
    const userSession2 = useUserSession();
    const sellerId = userSession2.sellerId;
    const formLocked = reactive({
      pinText: "",
      pinSecret: "",
      userId: ""
    });
    const users = ref([]);
    ref("");
    const user_id = ref(1);
    const isDialogOpen = ref(false);
    const openDialogForm = computed(() => {
      const isBlocked = userSession2.isBlockedPin;
      isDialogOpen.value = isBlocked == 1 ? true : false;
      return isDialogOpen.value;
    });
    const addPinText = (value) => {
      if (formLocked.pinText.length < 4) {
        formLocked.pinText += value;
        formLocked.pinSecret += "*";
        if (formLocked.pinText.length == 4) {
          correctPinCheck();
        }
      }
    };
    const clearPinText = () => {
      formLocked.pinText = "";
      formLocked.pinSecret = "";
    };
    const deletePinText = () => {
      if (formLocked.pinText.length > 0) {
        formLocked.pinText = formLocked.pinText.slice(0, -1);
      }
      if (formLocked.pinSecret.length > 0) {
        formLocked.pinSecret = formLocked.pinSecret.slice(0, -1);
      }
    };
    const availableSellers = async () => {
      try {
        const response = await provideApi().get("/restaurant/available-sellers");
        const data = response.data;
        users.value = data.data;
      } catch (error) {
        console.error("Error data:", error);
      }
    };
    const correctPinCheck = async () => {
      const id = user_id.value;
      const pin2 = formLocked.pinText;
      try {
        const response = await provideApi().get(`/restaurant/correct_pin_check/${id}/${pin2}`);
        const data = response.data;
        if (!data.success) {
          userSession2.setIsBlockedPin(1);
          notyf.error("Pin incorrecto");
        } else {
          userSession2.setIsBlockedPin(0);
          userSession2.setSellerId(id);
          userSession2.setSellerName(data.data.name);
          isDialogOpen.value = false;
          notyf.success("Pin correcto");
          formLocked.pinSecret = "";
          formLocked.pinText = "";
        }
      } catch (error) {
        console.error("Error data:", error);
        userSession2.setIsBlockedPin(1);
      }
    };
    const close = () => {
      userSession2.setIsBlockedPin(0);
      isDialogOpen.value = false;
    };
    onMounted(() => {
      user_id.value = sellerId;
      availableSellers();
    });
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$h;
      const _component_VButton = _sfc_main$i;
      const _component_VModal = _sfc_main$j;
      return openBlock(), createBlock(_component_VModal, {
        open: unref(openDialogForm),
        title: "",
        size: "small",
        actions: "right",
        hidefooter: "",
        bglight: "",
        withmenu: "",
        onClose: _cache[13] || (_cache[13] = ($event) => close())
      }, {
        content: withCtx(() => [
          createBaseVNode("div", _hoisted_1$e, [
            _hoisted_2$b,
            createBaseVNode("div", _hoisted_3$a, [
              createVNode(_component_VField, null, {
                default: withCtx(() => [
                  createVNode(_component_VControl, null, {
                    default: withCtx(() => [
                      createBaseVNode("div", _hoisted_4$a, [
                        withDirectives(createBaseVNode("select", {
                          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => user_id.value = $event)
                        }, [
                          (openBlock(true), createElementBlock(Fragment, null, renderList(users.value, (user) => {
                            return openBlock(), createElementBlock("option", {
                              key: user.id,
                              value: user.id
                            }, toDisplayString(user.name), 9, _hoisted_5$9);
                          }), 128))
                        ], 512), [
                          [vModelSelect, user_id.value]
                        ])
                      ])
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              })
            ])
          ]),
          createBaseVNode("div", _hoisted_6$9, [
            _hoisted_7$9,
            createBaseVNode("div", _hoisted_8$8, [
              unref(formLocked).pinSecret != "" ? (openBlock(), createElementBlock("h4", _hoisted_9$8, toDisplayString(unref(formLocked).pinSecret), 1)) : (openBlock(), createElementBlock("h4", _hoisted_10$8, "----"))
            ]),
            createBaseVNode("div", _hoisted_11$8, [
              createBaseVNode("div", _hoisted_12$7, [
                createBaseVNode("div", _hoisted_13$7, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[1] || (_cache[1] = ($event) => addPinText("1"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_14$7
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_15$7, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[2] || (_cache[2] = ($event) => addPinText("2"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_16$7
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_17$6, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[3] || (_cache[3] = ($event) => addPinText("3"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_18$6
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_19$5, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[4] || (_cache[4] = ($event) => addPinText("4"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_20$5
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_21$4, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[5] || (_cache[5] = ($event) => addPinText("5"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_22$4
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_23$4, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[6] || (_cache[6] = ($event) => addPinText("6"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_24$4
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_25$4, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[7] || (_cache[7] = ($event) => addPinText("7"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_26$4
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_27$4, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[8] || (_cache[8] = ($event) => addPinText("8"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_28$4
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_29$4, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[9] || (_cache[9] = ($event) => addPinText("9"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_30$4
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_31$3, [
                  createVNode(_component_VButton, {
                    color: "danger",
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[10] || (_cache[10] = ($event) => clearPinText())
                  }, {
                    default: withCtx(() => [
                      _hoisted_32$3
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_33$2, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[11] || (_cache[11] = ($event) => addPinText("0"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_34$2
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_35$2, [
                  createVNode(_component_VButton, {
                    color: "danger",
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[12] || (_cache[12] = ($event) => deletePinText())
                  }, {
                    default: withCtx(() => [
                      _hoisted_36$2
                    ]),
                    _: 1
                  })
                ])
              ])
            ])
          ])
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
var CashDialog_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$d = { class: "columns is-multiline is-mobile" };
const _hoisted_2$a = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, " Aperturar caja con un monto en soles ")
], -1);
const _hoisted_3$9 = { class: "column is-6" };
const _hoisted_4$9 = { class: "columns is-multiline is-mobile has-text-centered" };
const _hoisted_5$8 = { class: "column is-4" };
const _hoisted_6$8 = /* @__PURE__ */ createTextVNode("1");
const _hoisted_7$8 = { class: "column is-4" };
const _hoisted_8$7 = /* @__PURE__ */ createTextVNode("2");
const _hoisted_9$7 = { class: "column is-4" };
const _hoisted_10$7 = /* @__PURE__ */ createTextVNode("3");
const _hoisted_11$7 = { class: "column is-4 pt-0" };
const _hoisted_12$6 = /* @__PURE__ */ createTextVNode("4");
const _hoisted_13$6 = { class: "column is-4 pt-0" };
const _hoisted_14$6 = /* @__PURE__ */ createTextVNode("5");
const _hoisted_15$6 = { class: "column is-4 pt-0" };
const _hoisted_16$6 = /* @__PURE__ */ createTextVNode("6");
const _hoisted_17$5 = { class: "column is-4 pt-0" };
const _hoisted_18$5 = /* @__PURE__ */ createTextVNode("7");
const _hoisted_19$4 = { class: "column is-4 pt-0" };
const _hoisted_20$4 = /* @__PURE__ */ createTextVNode("8");
const _hoisted_21$3 = { class: "column is-4 pt-0" };
const _hoisted_22$3 = /* @__PURE__ */ createTextVNode("9");
const _hoisted_23$3 = { class: "column is-4 pt-0" };
const _hoisted_24$3 = /* @__PURE__ */ createTextVNode("0");
const _hoisted_25$3 = { class: "column is-4 pt-0" };
const _hoisted_26$3 = /* @__PURE__ */ createTextVNode("00");
const _hoisted_27$3 = { class: "column is-4 pt-0" };
const _hoisted_28$3 = /* @__PURE__ */ createTextVNode("C");
const _hoisted_29$3 = { class: "column is-6 has-text-centered is-flex is-flex-direction-column is-align-self-flex-end" };
const _hoisted_30$3 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "Monto", -1);
const _hoisted_31$2 = /* @__PURE__ */ createBaseVNode("span", null, "S/", -1);
const _hoisted_32$2 = /* @__PURE__ */ createTextVNode(" Aceptar ");
const _sfc_main$f = /* @__PURE__ */ defineComponent({
  setup(__props) {
    useCashSession();
    const route = useRoute();
    const nameRoute = computed(() => route.name);
    const userSession2 = useUserSession();
    const userRole = userSession2.getRole();
    const cashId = userSession2.getCashId();
    const isShowClose = () => {
      return nameRoute.value == NAME_ROUTE_POS;
    };
    isShowClose();
    const formCash = reactive({
      currency: "PEN",
      amount: 0,
      exchangeRate: 1,
      amountText: "0.00",
      referenceNumber: ""
    });
    ref(false);
    const cashs = ref([]);
    const cash_id = ref(0);
    const isDialogOpen = ref(false);
    const cashOpening = async () => {
      try {
        const response = await provideApi().get("/cash/opening_cash");
        const data = response.data;
        isDialogOpen.value = !data.success;
        if (!data.success) {
          userSession2.setCashId(0);
        } else {
          userSession2.setCashId(data.data.cash_id);
        }
      } catch (error) {
        console.error("Error data:", error);
        isDialogOpen.value = true;
        userSession2.setCashId(0);
      }
    };
    const openDialogForm = computed(() => {
      return cashId != 0 ? false : isDialogOpen.value;
    });
    const addMountText = (value) => {
      if (formCash.amountText == "0.00") {
        formCash.amountText = value;
      } else {
        formCash.amountText += value;
      }
      formCash.amount = Number(formCash.amountText);
    };
    const clearMountText = () => {
      formCash.amountText = "0.00";
      formCash.amount = 0;
    };
    const openCash = async () => {
      await cashOpening();
      if (!isDialogOpen.value) {
        return false;
      }
      const payload = {
        id: null,
        beginning_balance: formCash.amount,
        date_closed: null,
        date_opening: null,
        final_balance: 0,
        income: 0,
        reference_number: "restaurant",
        state: true,
        time_closed: null,
        time_opening: null,
        user_id: 0
      };
      try {
        const response = await provideApi().post("/cash/open", payload);
        const data = response.data;
        isDialogOpen.value = !data.success;
        if (data.success) {
          userSession2.setCashId(data.data.cash_id);
        }
      } catch (error) {
        console.error("Error data:", error);
        isDialogOpen.value = true;
      }
    };
    const cashOpeningCheck = async () => {
      const id = userSession2.getCashId();
      try {
        const response = await provideApi().get(`/cash/opening_cash_check/${id}`);
        const data = response.data;
        if (!data.success) {
          userSession2.setCashId(0);
        }
      } catch (error) {
        console.error("Error data:", error);
        userSession2.setCashId(0);
      }
    };
    const cashAvailable = async () => {
      try {
        const response = await provideApi().get("/cash/available-restaurant");
        const data = response.data;
        cashs.value = data.data;
        if (cashs.value.length === 0) {
          cash_id.value = 0;
          isDialogOpen.value = true;
        }
        if (cashs.value.length === 1) {
          cash_id.value = cashs.value[0].id;
          selectCashAvailable();
        }
        if (cashs.value.length > 1) {
          cash_id.value = 0;
          isDialogOpen.value = true;
        }
      } catch (error) {
        console.error("Error data:", error);
      }
    };
    const selectCashAvailable = async () => {
      const id = cash_id ? cash_id.value : 0;
      try {
        const response = await provideApi().get(`/cash/opening_cash_check/${id}`);
        const data = response.data;
        isDialogOpen.value = !data.success;
        if (data.success) {
          userSession2.setCashId(data.data.cash_id);
          userSession2.setCashDescription(data.data.description);
        }
      } catch (error) {
        console.error("Error data:", error);
        isDialogOpen.value = true;
      }
    };
    onMounted(() => {
      if (userRole == "MOZO") {
        cashOpeningCheck();
        cashAvailable();
      } else {
        cashOpening();
      }
    });
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$i;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$h;
      const _component_VModal = _sfc_main$j;
      return openBlock(), createBlock(_component_VModal, {
        open: unref(openDialogForm),
        title: "Apertura de caja chica",
        size: "small",
        actions: "right",
        noclose: "",
        hideheader: "",
        hidefooter: "",
        bglight: "",
        withmenu: ""
      }, createSlots({ _: 2 }, [
        unref(userRole) != "MOZO" ? {
          name: "content",
          fn: withCtx(() => [
            createBaseVNode("div", _hoisted_1$d, [
              _hoisted_2$a,
              createBaseVNode("div", _hoisted_3$9, [
                createBaseVNode("div", _hoisted_4$9, [
                  createBaseVNode("div", _hoisted_5$8, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[0] || (_cache[0] = ($event) => addMountText("1"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_6$8
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_7$8, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[1] || (_cache[1] = ($event) => addMountText("2"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_8$7
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_9$7, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[2] || (_cache[2] = ($event) => addMountText("3"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_10$7
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_11$7, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[3] || (_cache[3] = ($event) => addMountText("4"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_12$6
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_13$6, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[4] || (_cache[4] = ($event) => addMountText("5"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_14$6
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_15$6, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[5] || (_cache[5] = ($event) => addMountText("6"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_16$6
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_17$5, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[6] || (_cache[6] = ($event) => addMountText("7"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_18$5
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_19$4, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[7] || (_cache[7] = ($event) => addMountText("8"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_20$4
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_21$3, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[8] || (_cache[8] = ($event) => addMountText("9"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_22$3
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_23$3, [
                    createVNode(_component_VButton, {
                      size: "big",
                      onClick: _cache[9] || (_cache[9] = ($event) => addMountText("0"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_24$3
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_25$3, [
                    createVNode(_component_VButton, {
                      size: "big",
                      class: "px-4",
                      onClick: _cache[10] || (_cache[10] = ($event) => addMountText("00"))
                    }, {
                      default: withCtx(() => [
                        _hoisted_26$3
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", _hoisted_27$3, [
                    createVNode(_component_VButton, {
                      color: "danger",
                      size: "big",
                      onClick: _cache[11] || (_cache[11] = ($event) => clearMountText())
                    }, {
                      default: withCtx(() => [
                        _hoisted_28$3
                      ]),
                      _: 1
                    })
                  ])
                ])
              ]),
              createBaseVNode("div", _hoisted_29$3, [
                _hoisted_30$3,
                createBaseVNode("h4", null, [
                  _hoisted_31$2,
                  createTextVNode(" " + toDisplayString(unref(formCash).amountText), 1)
                ]),
                createVNode(_component_VButton, {
                  color: "primary",
                  fullwidth: "",
                  class: "mt-6",
                  size: "big",
                  onClick: openCash
                }, {
                  default: withCtx(() => [
                    _hoisted_32$2
                  ]),
                  _: 1
                })
              ])
            ])
          ])
        } : {
          name: "content",
          fn: withCtx(() => [
            createBaseVNode("div", { class: "columns is-multiline is-mobile" }, [
              createBaseVNode("div", { class: "column is-12" }, [
                createBaseVNode("h6", { class: "has-text-centered" }, " Seleccione una caja disponible ")
              ]),
              createBaseVNode("div", { class: "column is-12" }, [
                createVNode(_component_VField, null, {
                  default: withCtx(() => [
                    createVNode(_component_VControl, null, {
                      default: withCtx(() => [
                        createBaseVNode("div", { class: "select" }, [
                          withDirectives(createBaseVNode("select", {
                            "onUpdate:modelValue": _cache[12] || (_cache[12] = ($event) => cash_id.value = $event)
                          }, [
                            (openBlock(true), createElementBlock(Fragment, null, renderList(cashs.value, (cash) => {
                              return openBlock(), createElementBlock("option", {
                                key: cash.id,
                                value: cash.id
                              }, toDisplayString(cash.description), 9, ["value"]);
                            }), 128))
                          ], 512), [
                            [vModelSelect, cash_id.value]
                          ])
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  _: 1
                })
              ]),
              createBaseVNode("div", { class: "column is-12" }, [
                createVNode(_component_VButton, {
                  color: "primary",
                  fullwidth: "",
                  class: "mt-2",
                  size: "big",
                  onClick: selectCashAvailable
                }, {
                  default: withCtx(() => [
                    createTextVNode(" Aceptar ")
                  ]),
                  _: 1
                })
              ])
            ])
          ])
        }
      ]), 1032, ["open"]);
    };
  }
});
const _hoisted_1$c = { class: "message-body" };
const _sfc_main$e = /* @__PURE__ */ defineComponent({
  props: {
    color: { type: String, required: false, default: void 0 },
    closable: { type: Boolean, required: false }
  },
  emits: ["close"],
  setup(__props, { emit }) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["message", [props.color && `is-${props.color}`]])
      }, [
        props.closable ? (openBlock(), createElementBlock("a", {
          key: 0,
          "aria-label": "Dismiss",
          class: "delete",
          onClick: _cache[0] || (_cache[0] = withModifiers(($event) => emit("close"), ["prevent"]))
        })) : createCommentVNode("", true),
        createBaseVNode("div", _hoisted_1$c, [
          renderSlot(_ctx.$slots, "default")
        ])
      ], 2);
    };
  }
});
const _sfc_main$d = /* @__PURE__ */ defineComponent({
  props: {
    size: { type: String, required: false, default: void 0 },
    card: { type: String, required: false, default: void 0 },
    active: { type: Boolean, required: false },
    grey: { type: Boolean, required: false },
    translucent: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["has-loader", [props.active && "has-loader-active"]])
      }, [
        props.active ? (openBlock(), createElementBlock("div", {
          key: 0,
          class: normalizeClass(["v-loader-wrapper is-active", [
            __props.grey && "is-grey",
            __props.translucent && "is-translucent",
            __props.card === "regular" && "s-card",
            __props.card === "smooth" && "r-card",
            __props.card === "rounded" && "l-card"
          ]])
        }, [
          createBaseVNode("div", {
            class: normalizeClass(["loader is-loading", [props.size && `is-${props.size}`]])
          }, null, 2)
        ], 2)) : createCommentVNode("", true),
        renderSlot(_ctx.$slots, "default")
      ], 2);
    };
  }
});
const companySession$1 = useCompanySession();
const SaveCustomer = async (payload) => {
  try {
    const { data } = await provideApi().post("/person", payload);
    const response = data.data;
    const customers = companySession$1.customers;
    customers.push({
      id: response.id,
      codigo_tipo_documento_identidad: response.identity_document_type_id == 6 ? "6" : "1",
      numero_documento: response.number,
      apellidos_y_nombres_o_razon_social: response.name,
      codigo_pais: response.country_id,
      direccion: response.address,
      correo_electronico: response.email,
      telefono: response.telephone
    });
    companySession$1.setCustomers(customers);
    return response;
  } catch (err) {
    return null;
  }
};
const userSession$2 = useUserSession();
const SearchDocumentNumber = async (type, number) => {
  const { data } = await provideApi().get(`${userSession$2.ssl + userSession$2.url}/api/service/${type}/${number}`);
  return data.data;
};
const _hoisted_1$b = { class: "columns" };
const _hoisted_2$9 = { class: "column is-6" };
const _hoisted_3$8 = { class: "select" };
const _hoisted_4$8 = /* @__PURE__ */ createBaseVNode("option", { value: "1" }, "DNI", -1);
const _hoisted_5$7 = /* @__PURE__ */ createBaseVNode("option", { value: "6" }, "RUC", -1);
const _hoisted_6$7 = [
  _hoisted_4$8,
  _hoisted_5$7
];
const _hoisted_7$7 = {
  class: "column is-6",
  style: { "padding-top": "6%" }
};
const _hoisted_8$6 = ["maxlength"];
const _hoisted_9$6 = { class: "columns" };
const _hoisted_10$6 = { class: "column is-6" };
const _hoisted_11$6 = { class: "column is-6" };
const _hoisted_12$5 = { class: "columns" };
const _hoisted_13$5 = { class: "column is-6" };
const _hoisted_14$5 = { class: "column is-6" };
const _hoisted_15$5 = { class: "columns" };
const _hoisted_16$5 = { class: "column is-6" };
const _hoisted_17$4 = {
  key: 0,
  class: "column is-6"
};
const _hoisted_18$4 = /* @__PURE__ */ createTextVNode(" Guardar ");
const _sfc_main$c = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true, default: false }
  },
  emits: ["save", "cancel"],
  setup(__props, { emit }) {
    const props = __props;
    const form = {
      id: null,
      type: "customers",
      identity_document_type_id: 6,
      number: "",
      name: null,
      trade_name: null,
      country_id: "PE",
      department_id: null,
      province_id: null,
      district_id: null,
      address: null,
      telephone: null,
      condition: null,
      state: null,
      email: null,
      perception_agent: false,
      percentage_perception: 0,
      more_address: []
    };
    let state = reactive(__spreadValues({
      loading: false,
      keyInput: "",
      keyError: ""
    }, form));
    const save = async () => {
      state.keyInput = "";
      state.keyError = "";
      if (!state.number) {
        state.keyInput = "N\xFAmero";
        state.keyError = "Debe ingresar un n\xFAmero de documento";
        return;
      }
      if (state.identity_document_type_id == 6) {
        if (state.number.length != 11) {
          state.keyInput = "N\xFAmero";
          state.keyError = "Debe ingresar un n\xFAmero de ruc v\xE1lido";
          return;
        }
      }
      if (state.identity_document_type_id == 1) {
        if (state.number.length != 8) {
          state.keyInput = "N\xFAmero";
          state.keyError = "Debe ingresar un n\xFAmero de dni v\xE1lido";
          return;
        }
      }
      if (!state.number) {
        state.keyInput = "N\xFAmero";
        state.keyError = "Debe ingresar un nombre";
        return;
      }
      let payload = state;
      delete payload["loading"];
      delete payload["key"];
      delete payload["error"];
      const customer = await SaveCustomer(state);
      if (!customer) {
        state.keyInput = "Registro";
        state.keyError = "No se pudo crear el cliente.";
        return;
      }
      emit("save", customer.id);
      state.number = "";
      state.name = null;
      state.identity_document_type_id = 6;
      state.email = null;
      state.trade_name = null;
      state.address = null;
    };
    const cancel = () => {
      emit("cancel");
    };
    const searchDocumentNumber = async () => {
      state.loading = true;
      const datos = await SearchDocumentNumber(state.identity_document_type_id == 1 ? "dni" : "ruc", state.number);
      if (datos) {
        state.name = datos.name;
        state.trade_name = datos.trade_name;
        state.address = datos.address;
      }
      state.loading = false;
    };
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$h;
      const _component_VButton = _sfc_main$i;
      const _component_VMessage = _sfc_main$e;
      const _component_VLoader = _sfc_main$d;
      const _component_VModal = _sfc_main$j;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Nuevo Cliente",
        size: "large",
        actions: "right",
        "cancel-label": "Cancelar",
        noclose: "",
        cascade: true,
        onClose: cancel
      }, {
        content: withCtx(() => [
          createVNode(_component_VLoader, {
            size: "large",
            active: unref(state).loading,
            translucent: ""
          }, {
            default: withCtx(() => [
              createBaseVNode("div", _hoisted_1$b, [
                createBaseVNode("div", _hoisted_2$9, [
                  createVNode(_component_VField, { label: "Tipo Doc. Identidad" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          createBaseVNode("div", _hoisted_3$8, [
                            withDirectives(createBaseVNode("select", {
                              "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => unref(state).identity_document_type_id = $event),
                              placeholder: "Seleccione",
                              class: "select"
                            }, _hoisted_6$7, 512), [
                              [vModelSelect, unref(state).identity_document_type_id]
                            ])
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_7$7, [
                  createVNode(_component_VField, { addons: "" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, { expanded: "" }, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => unref(state).number = $event),
                            placeholder: "Ingrese N\xFAmero",
                            type: "text",
                            class: "input",
                            maxlength: unref(state).identity_document_type_id == 1 ? 8 : 11
                          }, null, 8, _hoisted_8$6), [
                            [vModelText, unref(state).number]
                          ])
                        ]),
                        _: 1
                      }),
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          createVNode(_component_VButton, {
                            color: "primary",
                            onClick: searchDocumentNumber
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(state).identity_document_type_id == 1 ? "RENIEC" : "SUNAT"), 1)
                            ]),
                            _: 1
                          })
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ])
              ]),
              createBaseVNode("div", _hoisted_9$6, [
                createBaseVNode("div", _hoisted_10$6, [
                  createVNode(_component_VField, { label: "Nombre *" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => unref(state).name = $event),
                            type: "text",
                            class: "input"
                          }, null, 512), [
                            [vModelText, unref(state).name]
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_11$6, [
                  createVNode(_component_VField, { label: "Nombre comercial" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => unref(state).trade_name = $event),
                            type: "text",
                            class: "input"
                          }, null, 512), [
                            [vModelText, unref(state).trade_name]
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ])
              ]),
              createBaseVNode("div", _hoisted_12$5, [
                createBaseVNode("div", _hoisted_13$5, [
                  createVNode(_component_VField, { label: "Direcci\xF3n" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => unref(state).address = $event),
                            type: "text",
                            class: "input"
                          }, null, 512), [
                            [vModelText, unref(state).address]
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_14$5, [
                  createVNode(_component_VField, { label: "Tel\xE9fono" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[5] || (_cache[5] = ($event) => unref(state).telephone = $event),
                            type: "text",
                            class: "input",
                            maxlength: 9
                          }, null, 512), [
                            [vModelText, unref(state).telephone]
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ])
              ]),
              createBaseVNode("div", _hoisted_15$5, [
                createBaseVNode("div", _hoisted_16$5, [
                  createVNode(_component_VField, { label: "Correo electr\xF3nico" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, null, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => unref(state).email = $event),
                            type: "text",
                            class: "input"
                          }, null, 512), [
                            [vModelText, unref(state).email]
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  })
                ]),
                unref(state).keyInput && unref(state).keyError ? (openBlock(), createElementBlock("div", _hoisted_17$4, [
                  createVNode(_component_VMessage, { color: "warning" }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(state).keyInput) + ": " + toDisplayString(unref(state).keyError), 1)
                    ]),
                    _: 1
                  })
                ])) : createCommentVNode("", true)
              ])
            ]),
            _: 1
          }, 8, ["active"])
        ]),
        action: withCtx(() => [
          createVNode(_component_VField, null, {
            default: withCtx(() => [
              createVNode(_component_VControl, null, {
                default: withCtx(() => [
                  createVNode(_component_VButton, {
                    color: "primary",
                    onClick: _cache[7] || (_cache[7] = ($event) => save())
                  }, {
                    default: withCtx(() => [
                      _hoisted_18$4
                    ]),
                    _: 1
                  })
                ]),
                _: 1
              })
            ]),
            _: 1
          })
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
const _sfc_main$b = /* @__PURE__ */ defineComponent({
  props: {
    size: { type: String, required: false, default: void 0 },
    color: { type: String, required: false, default: void 0 },
    rounded: { type: Boolean, required: false },
    bordered: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["v-icon", [
          props.size && "is-" + props.size,
          props.color && "is-" + props.color,
          props.rounded && "is-rounded",
          props.bordered && "is-bordered"
        ]])
      }, [
        renderSlot(_ctx.$slots, "default")
      ], 2);
    };
  }
});
var VFlex_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$a = { class: "v-flex" };
const _sfc_main$a = /* @__PURE__ */ defineComponent({
  props: {
    inline: { type: Boolean, required: false },
    flexDirection: { type: String, required: false, default: "row" },
    flexWrap: { type: String, required: false, default: "nowrap" },
    justifyContent: { type: String, required: false, default: "normal" },
    alignItems: { type: String, required: false, default: "normal" },
    alignContent: { type: String, required: false, default: "normal" },
    rowGap: { type: String, required: false, default: "normal" },
    columnGap: { type: String, required: false, default: "normal" }
  },
  setup(__props) {
    const props = __props;
    useCssVars((_ctx) => ({
      "2c5d33dd": unref(display),
      "547e1a23": props.flexDirection,
      "7779a726": props.flexWrap,
      "97eaae68": props.justifyContent,
      "09038384": props.alignItems,
      "61083912": props.alignContent,
      "d6245ac2": props.rowGap,
      "60f25cbd": props.columnGap
    }));
    const display = computed(() => props.inline ? "inline-flex" : "flex");
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$a, [
        renderSlot(_ctx.$slots, "default")
      ]);
    };
  }
});
var dayjs_min = { exports: {} };
(function(module, exports) {
  !function(t, e) {
    module.exports = e();
  }(commonjsGlobal, function() {
    var t = 1e3, e = 6e4, n = 36e5, r = "millisecond", i = "second", s = "minute", u = "hour", a = "day", o = "week", f = "month", h = "quarter", c = "year", d = "date", $ = "Invalid Date", l = /^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/, y = /\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g, M = { name: "en", weekdays: "Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"), months: "January_February_March_April_May_June_July_August_September_October_November_December".split("_") }, m = function(t2, e2, n2) {
      var r2 = String(t2);
      return !r2 || r2.length >= e2 ? t2 : "" + Array(e2 + 1 - r2.length).join(n2) + t2;
    }, g = { s: m, z: function(t2) {
      var e2 = -t2.utcOffset(), n2 = Math.abs(e2), r2 = Math.floor(n2 / 60), i2 = n2 % 60;
      return (e2 <= 0 ? "+" : "-") + m(r2, 2, "0") + ":" + m(i2, 2, "0");
    }, m: function t2(e2, n2) {
      if (e2.date() < n2.date())
        return -t2(n2, e2);
      var r2 = 12 * (n2.year() - e2.year()) + (n2.month() - e2.month()), i2 = e2.clone().add(r2, f), s2 = n2 - i2 < 0, u2 = e2.clone().add(r2 + (s2 ? -1 : 1), f);
      return +(-(r2 + (n2 - i2) / (s2 ? i2 - u2 : u2 - i2)) || 0);
    }, a: function(t2) {
      return t2 < 0 ? Math.ceil(t2) || 0 : Math.floor(t2);
    }, p: function(t2) {
      return { M: f, y: c, w: o, d: a, D: d, h: u, m: s, s: i, ms: r, Q: h }[t2] || String(t2 || "").toLowerCase().replace(/s$/, "");
    }, u: function(t2) {
      return t2 === void 0;
    } }, D = "en", v = {};
    v[D] = M;
    var p = function(t2) {
      return t2 instanceof _2;
    }, S = function(t2, e2, n2) {
      var r2;
      if (!t2)
        return D;
      if (typeof t2 == "string")
        v[t2] && (r2 = t2), e2 && (v[t2] = e2, r2 = t2);
      else {
        var i2 = t2.name;
        v[i2] = t2, r2 = i2;
      }
      return !n2 && r2 && (D = r2), r2 || !n2 && D;
    }, w = function(t2, e2) {
      if (p(t2))
        return t2.clone();
      var n2 = typeof e2 == "object" ? e2 : {};
      return n2.date = t2, n2.args = arguments, new _2(n2);
    }, O = g;
    O.l = S, O.i = p, O.w = function(t2, e2) {
      return w(t2, { locale: e2.$L, utc: e2.$u, x: e2.$x, $offset: e2.$offset });
    };
    var _2 = function() {
      function M2(t2) {
        this.$L = S(t2.locale, null, true), this.parse(t2);
      }
      var m2 = M2.prototype;
      return m2.parse = function(t2) {
        this.$d = function(t3) {
          var e2 = t3.date, n2 = t3.utc;
          if (e2 === null)
            return new Date(NaN);
          if (O.u(e2))
            return new Date();
          if (e2 instanceof Date)
            return new Date(e2);
          if (typeof e2 == "string" && !/Z$/i.test(e2)) {
            var r2 = e2.match(l);
            if (r2) {
              var i2 = r2[2] - 1 || 0, s2 = (r2[7] || "0").substring(0, 3);
              return n2 ? new Date(Date.UTC(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2)) : new Date(r2[1], i2, r2[3] || 1, r2[4] || 0, r2[5] || 0, r2[6] || 0, s2);
            }
          }
          return new Date(e2);
        }(t2), this.$x = t2.x || {}, this.init();
      }, m2.init = function() {
        var t2 = this.$d;
        this.$y = t2.getFullYear(), this.$M = t2.getMonth(), this.$D = t2.getDate(), this.$W = t2.getDay(), this.$H = t2.getHours(), this.$m = t2.getMinutes(), this.$s = t2.getSeconds(), this.$ms = t2.getMilliseconds();
      }, m2.$utils = function() {
        return O;
      }, m2.isValid = function() {
        return !(this.$d.toString() === $);
      }, m2.isSame = function(t2, e2) {
        var n2 = w(t2);
        return this.startOf(e2) <= n2 && n2 <= this.endOf(e2);
      }, m2.isAfter = function(t2, e2) {
        return w(t2) < this.startOf(e2);
      }, m2.isBefore = function(t2, e2) {
        return this.endOf(e2) < w(t2);
      }, m2.$g = function(t2, e2, n2) {
        return O.u(t2) ? this[e2] : this.set(n2, t2);
      }, m2.unix = function() {
        return Math.floor(this.valueOf() / 1e3);
      }, m2.valueOf = function() {
        return this.$d.getTime();
      }, m2.startOf = function(t2, e2) {
        var n2 = this, r2 = !!O.u(e2) || e2, h2 = O.p(t2), $2 = function(t3, e3) {
          var i2 = O.w(n2.$u ? Date.UTC(n2.$y, e3, t3) : new Date(n2.$y, e3, t3), n2);
          return r2 ? i2 : i2.endOf(a);
        }, l2 = function(t3, e3) {
          return O.w(n2.toDate()[t3].apply(n2.toDate("s"), (r2 ? [0, 0, 0, 0] : [23, 59, 59, 999]).slice(e3)), n2);
        }, y2 = this.$W, M3 = this.$M, m3 = this.$D, g2 = "set" + (this.$u ? "UTC" : "");
        switch (h2) {
          case c:
            return r2 ? $2(1, 0) : $2(31, 11);
          case f:
            return r2 ? $2(1, M3) : $2(0, M3 + 1);
          case o:
            var D2 = this.$locale().weekStart || 0, v2 = (y2 < D2 ? y2 + 7 : y2) - D2;
            return $2(r2 ? m3 - v2 : m3 + (6 - v2), M3);
          case a:
          case d:
            return l2(g2 + "Hours", 0);
          case u:
            return l2(g2 + "Minutes", 1);
          case s:
            return l2(g2 + "Seconds", 2);
          case i:
            return l2(g2 + "Milliseconds", 3);
          default:
            return this.clone();
        }
      }, m2.endOf = function(t2) {
        return this.startOf(t2, false);
      }, m2.$set = function(t2, e2) {
        var n2, o2 = O.p(t2), h2 = "set" + (this.$u ? "UTC" : ""), $2 = (n2 = {}, n2[a] = h2 + "Date", n2[d] = h2 + "Date", n2[f] = h2 + "Month", n2[c] = h2 + "FullYear", n2[u] = h2 + "Hours", n2[s] = h2 + "Minutes", n2[i] = h2 + "Seconds", n2[r] = h2 + "Milliseconds", n2)[o2], l2 = o2 === a ? this.$D + (e2 - this.$W) : e2;
        if (o2 === f || o2 === c) {
          var y2 = this.clone().set(d, 1);
          y2.$d[$2](l2), y2.init(), this.$d = y2.set(d, Math.min(this.$D, y2.daysInMonth())).$d;
        } else
          $2 && this.$d[$2](l2);
        return this.init(), this;
      }, m2.set = function(t2, e2) {
        return this.clone().$set(t2, e2);
      }, m2.get = function(t2) {
        return this[O.p(t2)]();
      }, m2.add = function(r2, h2) {
        var d2, $2 = this;
        r2 = Number(r2);
        var l2 = O.p(h2), y2 = function(t2) {
          var e2 = w($2);
          return O.w(e2.date(e2.date() + Math.round(t2 * r2)), $2);
        };
        if (l2 === f)
          return this.set(f, this.$M + r2);
        if (l2 === c)
          return this.set(c, this.$y + r2);
        if (l2 === a)
          return y2(1);
        if (l2 === o)
          return y2(7);
        var M3 = (d2 = {}, d2[s] = e, d2[u] = n, d2[i] = t, d2)[l2] || 1, m3 = this.$d.getTime() + r2 * M3;
        return O.w(m3, this);
      }, m2.subtract = function(t2, e2) {
        return this.add(-1 * t2, e2);
      }, m2.format = function(t2) {
        var e2 = this, n2 = this.$locale();
        if (!this.isValid())
          return n2.invalidDate || $;
        var r2 = t2 || "YYYY-MM-DDTHH:mm:ssZ", i2 = O.z(this), s2 = this.$H, u2 = this.$m, a2 = this.$M, o2 = n2.weekdays, f2 = n2.months, h2 = function(t3, n3, i3, s3) {
          return t3 && (t3[n3] || t3(e2, r2)) || i3[n3].substr(0, s3);
        }, c2 = function(t3) {
          return O.s(s2 % 12 || 12, t3, "0");
        }, d2 = n2.meridiem || function(t3, e3, n3) {
          var r3 = t3 < 12 ? "AM" : "PM";
          return n3 ? r3.toLowerCase() : r3;
        }, l2 = { YY: String(this.$y).slice(-2), YYYY: this.$y, M: a2 + 1, MM: O.s(a2 + 1, 2, "0"), MMM: h2(n2.monthsShort, a2, f2, 3), MMMM: h2(f2, a2), D: this.$D, DD: O.s(this.$D, 2, "0"), d: String(this.$W), dd: h2(n2.weekdaysMin, this.$W, o2, 2), ddd: h2(n2.weekdaysShort, this.$W, o2, 3), dddd: o2[this.$W], H: String(s2), HH: O.s(s2, 2, "0"), h: c2(1), hh: c2(2), a: d2(s2, u2, true), A: d2(s2, u2, false), m: String(u2), mm: O.s(u2, 2, "0"), s: String(this.$s), ss: O.s(this.$s, 2, "0"), SSS: O.s(this.$ms, 3, "0"), Z: i2 };
        return r2.replace(y, function(t3, e3) {
          return e3 || l2[t3] || i2.replace(":", "");
        });
      }, m2.utcOffset = function() {
        return 15 * -Math.round(this.$d.getTimezoneOffset() / 15);
      }, m2.diff = function(r2, d2, $2) {
        var l2, y2 = O.p(d2), M3 = w(r2), m3 = (M3.utcOffset() - this.utcOffset()) * e, g2 = this - M3, D2 = O.m(this, M3);
        return D2 = (l2 = {}, l2[c] = D2 / 12, l2[f] = D2, l2[h] = D2 / 3, l2[o] = (g2 - m3) / 6048e5, l2[a] = (g2 - m3) / 864e5, l2[u] = g2 / n, l2[s] = g2 / e, l2[i] = g2 / t, l2)[y2] || g2, $2 ? D2 : O.a(D2);
      }, m2.daysInMonth = function() {
        return this.endOf(f).$D;
      }, m2.$locale = function() {
        return v[this.$L];
      }, m2.locale = function(t2, e2) {
        if (!t2)
          return this.$L;
        var n2 = this.clone(), r2 = S(t2, e2, true);
        return r2 && (n2.$L = r2), n2;
      }, m2.clone = function() {
        return O.w(this.$d, this);
      }, m2.toDate = function() {
        return new Date(this.valueOf());
      }, m2.toJSON = function() {
        return this.isValid() ? this.toISOString() : null;
      }, m2.toISOString = function() {
        return this.$d.toISOString();
      }, m2.toString = function() {
        return this.$d.toUTCString();
      }, M2;
    }(), b = _2.prototype;
    return w.prototype = b, [["$ms", r], ["$s", i], ["$m", s], ["$H", u], ["$W", a], ["$M", f], ["$y", c], ["$D", d]].forEach(function(t2) {
      b[t2[1]] = function(e2) {
        return this.$g(e2, t2[0], t2[1]);
      };
    }), w.extend = function(t2, e2) {
      return t2.$i || (t2(e2, _2, w), t2.$i = true), w;
    }, w.locale = S, w.isDayjs = p, w.unix = function(t2) {
      return w(1e3 * t2);
    }, w.en = v[D], w.Ls = v, w.p = {}, w;
  });
})(dayjs_min);
var dayjs = dayjs_min.exports;
const time = dayjs().format("HH:mm:ss");
const userSession$1 = useUserSession();
const companySession = useCompanySession();
const establishments = companySession.establishments;
const getDocumentPayload = (items, customer, cpe, payments = []) => {
  const payload = {
    serie_documento: cpe.serie,
    numero_documento: "#",
    fecha_de_emision: cpe.date,
    hora_de_emision: time,
    codigo_tipo_operacion: "0101",
    codigo_tipo_documento: cpe.documentSelected == "FACTURA" ? "01" : "03",
    codigo_tipo_moneda: "PEN",
    fecha_de_vencimiento: cpe.dateOfDue,
    numero_orden_de_compra: "",
    numero_de_placa: cpe.plate_number,
    pagos: payments.map((pay) => ({
      fecha_de_emision: pay.date_of_payment,
      codigo_metodo_pago: pay.payment_method_type_id,
      codigo_destino_pago: pay.payment_destination_id,
      referencia: pay.reference,
      monto: pay.payment
    })),
    codigo_vendedor: userSession$1.sellerId
  };
  payload["datos_del_cliente_o_receptor"] = {
    codigo_tipo_documento_identidad: customer.codigo_tipo_documento_identidad,
    numero_documento: customer.numero_documento,
    apellidos_y_nombres_o_razon_social: customer.apellidos_y_nombres_o_razon_social,
    codigo_pais: customer.codigo_pais,
    ubigeo: "",
    direccion: customer.direccion,
    correo_electronico: customer.correo_electronico,
    telefono: customer.telefono
  };
  let total = 0;
  let total_gravadas = 0;
  let total_impuestos = 0;
  let total_exoneradas = 0;
  let total_igv = 0;
  payload["items"] = items.map((item) => {
    const establishment = establishments.find((item2) => item2.id = userSession$1.establishmentId);
    const has_igv_31556 = establishment ? establishment.has_igv_31556 : false;
    const line_quantity = item.quantity;
    let line_percentage_igv = has_igv_31556 ? 10 : 18;
    const line_percentage_igv_number = line_percentage_igv / 100;
    const line_unit_price = item.price;
    const line_total = line_unit_price * line_quantity;
    let line_total_base_igv = line_total / (1 + line_percentage_igv_number);
    let line_total_igv = line_total - line_total_base_igv;
    let line_unit_value = line_total_base_igv / line_quantity;
    if (item.sale_affectation_igv_type_id == "20" || item.sale_affectation_igv_type_id == "30") {
      line_total_igv = 0;
      line_total_base_igv = line_total;
      line_percentage_igv = 0;
      line_unit_value = line_unit_price;
      total_exoneradas += Number(line_total);
    }
    const row = {
      codigo_interno: item.internalId,
      descripcion: item.name,
      codigo_producto_sunat: item.itemCode,
      unidad_de_medida: item.unitTypeId,
      cantidad: line_quantity,
      valor_unitario: line_unit_value,
      precio_unitario: item.price,
      codigo_tipo_precio: "01",
      codigo_tipo_afectacion_igv: item.sale_affectation_igv_type_id,
      total_base_igv: line_total_base_igv,
      porcentaje_igv: line_percentage_igv,
      total_igv: line_total_igv,
      total_impuestos: line_total_igv,
      total_valor_item: line_total_base_igv,
      total_item: line_total
    };
    total += Number(row.total_item);
    total_impuestos += row.total_impuestos;
    total_igv += row.total_igv;
    if (item.sale_affectation_igv_type_id == "10") {
      total_gravadas += Number(row.total_valor_item);
    }
    return row;
  });
  payload["totales"] = {
    total_exportacion: 0,
    total_operaciones_gravadas: total_gravadas,
    total_operaciones_inafectas: 0,
    total_operaciones_exoneradas: total_exoneradas,
    total_operaciones_gratuitas: 0,
    total_igv,
    total_impuestos,
    total_valor: total_gravadas + total_exoneradas,
    total_venta: total
  };
  return payload;
};
const getSaleNotePayload = (items, customer, serie, date, payments = [], plate_number) => {
  const payload = {
    document_type_id: "80",
    prefix: "NV",
    series_id: serie.id,
    establishment_id: null,
    date_of_issue: date,
    time_of_issue: time,
    customer_id: customer.id,
    currency_type_id: "PEN",
    purchase_order: null,
    plate_number,
    exchange_rate_sale: 0,
    operation_type_id: null,
    charges: [],
    discounts: [],
    attributes: [],
    guides: [],
    payments,
    additional_information: null,
    seller_id: userSession$1.sellerId,
    actions: { format_pdf: "a4" },
    apply_concurrency: false,
    type_period: null,
    quantity_period: 0,
    automatic_date_of_issue: null,
    enabled_concurrency: false,
    total_prepayment: 0,
    total_charge: 0,
    total_discount: 0,
    total_exportation: 0,
    total_free: 0,
    total_unaffected: 0,
    total_exonerated: 0,
    total_base_isc: 0,
    total_isc: 0,
    total_base_other_taxes: 0,
    total_other_taxes: 0
  };
  payload["datos_del_cliente_o_receptor"] = {
    codigo_tipo_documento_identidad: customer.codigo_tipo_documento_identidad,
    numero_documento: customer.numero_documento,
    apellidos_y_nombres_o_razon_social: customer.apellidos_y_nombres_o_razon_social,
    codigo_pais: customer.codigo_pais,
    ubigeo: "",
    direccion: customer.direccion,
    correo_electronico: customer.correo_electronico,
    telefono: customer.telefono
  };
  let total = 0;
  const establishment = establishments.find((item) => item.id = userSession$1.establishmentId);
  const has_igv_31556 = establishment ? establishment.has_igv_31556 : false;
  const line_percentage_igv = has_igv_31556 ? 10 : 18;
  payload["items"] = items.map((item) => {
    const item_base_igv = item.price / (1 + line_percentage_igv / 100);
    const totalItem = item.price * item.quantity;
    const row = {
      item_id: item.id,
      item: {
        id: item.id,
        item_id: item.id,
        name: item.name,
        full_description: item.name,
        description: item.name,
        currency_type_id: "PEN",
        internal_id: item.internalId,
        item_code: item.itemCode,
        currency_type_symbol: item.currencyTypeSymbol,
        purchase_unit_price: item.purchase_unit_price,
        unit_type_id: item.unitTypeId,
        sale_affectation_igv_type_id: "10",
        purchase_affectation_igv_type_id: "10",
        calculate_quantity: false,
        has_igv: true,
        is_set: false,
        aux_quantity: 1,
        brand: "CIELO",
        category: "CIELO",
        stock: 0,
        image: "https://demo.facturalo.pro/logo/imagen-no-disponible.jpg",
        presentation: null,
        warehouses: [],
        unit_price: item.price,
        sale_unit_price: item.price
      },
      currency_type_id: "PEN",
      affectation_igv_type_id: "10",
      system_isc_type_id: null,
      total_base_isc: 0,
      percentage_isc: 0,
      total_isc: 0,
      total_base_other_taxes: 0,
      percentage_other_taxes: 0,
      total_other_taxes: 0,
      total_plastic_bag_taxes: 0,
      price_type_id: "01",
      total_discount: 0,
      total_charge: 0,
      attributes: [],
      charges: [],
      discounts: [],
      affectation_igv_type: {
        id: "10",
        active: 1,
        exportation: 0,
        free: 0,
        description: "Gravado - Operaci\xF3n Onerosa"
      },
      quantity: item.quantity,
      unit_price: item.price,
      unit_value: item_base_igv,
      total_value: item_base_igv * item.quantity,
      percentage_igv: line_percentage_igv,
      total_base_igv: item_base_igv * item.quantity,
      total_igv: totalItem - item_base_igv * item.quantity,
      total: totalItem,
      total_taxes: totalItem - item_base_igv * item.quantity
    };
    total += Number(totalItem);
    return row;
  });
  const base_igv = total / (1 + line_percentage_igv / 100);
  payload["total_taxed"] = base_igv;
  payload["total_taxes"] = total - base_igv;
  payload["total_igv"] = total - base_igv;
  payload["total_value"] = base_igv;
  payload["subtotal"] = total;
  payload["total"] = total;
  return payload;
};
const IdentityDocumentTypes = {
  ["0"]: "Doc.trib.no.dom.sin.ruc",
  ["1"]: "DNI",
  ["4"]: "CE",
  ["6"]: "RUC",
  ["7"]: "Pasaporte"
};
var lodash = { exports: {} };
/**
 * @license
 * Lodash <https://lodash.com/>
 * Copyright OpenJS Foundation and other contributors <https://openjsf.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */
(function(module, exports) {
  (function() {
    var undefined$1;
    var VERSION = "4.17.21";
    var LARGE_ARRAY_SIZE = 200;
    var CORE_ERROR_TEXT = "Unsupported core-js use. Try https://npms.io/search?q=ponyfill.", FUNC_ERROR_TEXT = "Expected a function", INVALID_TEMPL_VAR_ERROR_TEXT = "Invalid `variable` option passed into `_.template`";
    var HASH_UNDEFINED = "__lodash_hash_undefined__";
    var MAX_MEMOIZE_SIZE = 500;
    var PLACEHOLDER = "__lodash_placeholder__";
    var CLONE_DEEP_FLAG = 1, CLONE_FLAT_FLAG = 2, CLONE_SYMBOLS_FLAG = 4;
    var COMPARE_PARTIAL_FLAG = 1, COMPARE_UNORDERED_FLAG = 2;
    var WRAP_BIND_FLAG = 1, WRAP_BIND_KEY_FLAG = 2, WRAP_CURRY_BOUND_FLAG = 4, WRAP_CURRY_FLAG = 8, WRAP_CURRY_RIGHT_FLAG = 16, WRAP_PARTIAL_FLAG = 32, WRAP_PARTIAL_RIGHT_FLAG = 64, WRAP_ARY_FLAG = 128, WRAP_REARG_FLAG = 256, WRAP_FLIP_FLAG = 512;
    var DEFAULT_TRUNC_LENGTH = 30, DEFAULT_TRUNC_OMISSION = "...";
    var HOT_COUNT = 800, HOT_SPAN = 16;
    var LAZY_FILTER_FLAG = 1, LAZY_MAP_FLAG = 2, LAZY_WHILE_FLAG = 3;
    var INFINITY = 1 / 0, MAX_SAFE_INTEGER = 9007199254740991, MAX_INTEGER = 17976931348623157e292, NAN = 0 / 0;
    var MAX_ARRAY_LENGTH = 4294967295, MAX_ARRAY_INDEX = MAX_ARRAY_LENGTH - 1, HALF_MAX_ARRAY_LENGTH = MAX_ARRAY_LENGTH >>> 1;
    var wrapFlags = [
      ["ary", WRAP_ARY_FLAG],
      ["bind", WRAP_BIND_FLAG],
      ["bindKey", WRAP_BIND_KEY_FLAG],
      ["curry", WRAP_CURRY_FLAG],
      ["curryRight", WRAP_CURRY_RIGHT_FLAG],
      ["flip", WRAP_FLIP_FLAG],
      ["partial", WRAP_PARTIAL_FLAG],
      ["partialRight", WRAP_PARTIAL_RIGHT_FLAG],
      ["rearg", WRAP_REARG_FLAG]
    ];
    var argsTag = "[object Arguments]", arrayTag = "[object Array]", asyncTag = "[object AsyncFunction]", boolTag = "[object Boolean]", dateTag = "[object Date]", domExcTag = "[object DOMException]", errorTag = "[object Error]", funcTag = "[object Function]", genTag = "[object GeneratorFunction]", mapTag = "[object Map]", numberTag = "[object Number]", nullTag = "[object Null]", objectTag = "[object Object]", promiseTag = "[object Promise]", proxyTag = "[object Proxy]", regexpTag = "[object RegExp]", setTag = "[object Set]", stringTag = "[object String]", symbolTag = "[object Symbol]", undefinedTag = "[object Undefined]", weakMapTag = "[object WeakMap]", weakSetTag = "[object WeakSet]";
    var arrayBufferTag = "[object ArrayBuffer]", dataViewTag = "[object DataView]", float32Tag = "[object Float32Array]", float64Tag = "[object Float64Array]", int8Tag = "[object Int8Array]", int16Tag = "[object Int16Array]", int32Tag = "[object Int32Array]", uint8Tag = "[object Uint8Array]", uint8ClampedTag = "[object Uint8ClampedArray]", uint16Tag = "[object Uint16Array]", uint32Tag = "[object Uint32Array]";
    var reEmptyStringLeading = /\b__p \+= '';/g, reEmptyStringMiddle = /\b(__p \+=) '' \+/g, reEmptyStringTrailing = /(__e\(.*?\)|\b__t\)) \+\n'';/g;
    var reEscapedHtml = /&(?:amp|lt|gt|quot|#39);/g, reUnescapedHtml = /[&<>"']/g, reHasEscapedHtml = RegExp(reEscapedHtml.source), reHasUnescapedHtml = RegExp(reUnescapedHtml.source);
    var reEscape = /<%-([\s\S]+?)%>/g, reEvaluate = /<%([\s\S]+?)%>/g, reInterpolate = /<%=([\s\S]+?)%>/g;
    var reIsDeepProp = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/, reIsPlainProp = /^\w*$/, rePropName = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g;
    var reRegExpChar = /[\\^$.*+?()[\]{}|]/g, reHasRegExpChar = RegExp(reRegExpChar.source);
    var reTrimStart = /^\s+/;
    var reWhitespace = /\s/;
    var reWrapComment = /\{(?:\n\/\* \[wrapped with .+\] \*\/)?\n?/, reWrapDetails = /\{\n\/\* \[wrapped with (.+)\] \*/, reSplitDetails = /,? & /;
    var reAsciiWord = /[^\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]+/g;
    var reForbiddenIdentifierChars = /[()=,{}\[\]\/\s]/;
    var reEscapeChar = /\\(\\)?/g;
    var reEsTemplate = /\$\{([^\\}]*(?:\\.[^\\}]*)*)\}/g;
    var reFlags = /\w*$/;
    var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;
    var reIsBinary = /^0b[01]+$/i;
    var reIsHostCtor = /^\[object .+?Constructor\]$/;
    var reIsOctal = /^0o[0-7]+$/i;
    var reIsUint = /^(?:0|[1-9]\d*)$/;
    var reLatin = /[\xc0-\xd6\xd8-\xf6\xf8-\xff\u0100-\u017f]/g;
    var reNoMatch = /($^)/;
    var reUnescapedString = /['\n\r\u2028\u2029\\]/g;
    var rsAstralRange = "\\ud800-\\udfff", rsComboMarksRange = "\\u0300-\\u036f", reComboHalfMarksRange = "\\ufe20-\\ufe2f", rsComboSymbolsRange = "\\u20d0-\\u20ff", rsComboRange = rsComboMarksRange + reComboHalfMarksRange + rsComboSymbolsRange, rsDingbatRange = "\\u2700-\\u27bf", rsLowerRange = "a-z\\xdf-\\xf6\\xf8-\\xff", rsMathOpRange = "\\xac\\xb1\\xd7\\xf7", rsNonCharRange = "\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf", rsPunctuationRange = "\\u2000-\\u206f", rsSpaceRange = " \\t\\x0b\\f\\xa0\\ufeff\\n\\r\\u2028\\u2029\\u1680\\u180e\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200a\\u202f\\u205f\\u3000", rsUpperRange = "A-Z\\xc0-\\xd6\\xd8-\\xde", rsVarRange = "\\ufe0e\\ufe0f", rsBreakRange = rsMathOpRange + rsNonCharRange + rsPunctuationRange + rsSpaceRange;
    var rsApos = "['\u2019]", rsAstral = "[" + rsAstralRange + "]", rsBreak = "[" + rsBreakRange + "]", rsCombo = "[" + rsComboRange + "]", rsDigits = "\\d+", rsDingbat = "[" + rsDingbatRange + "]", rsLower = "[" + rsLowerRange + "]", rsMisc = "[^" + rsAstralRange + rsBreakRange + rsDigits + rsDingbatRange + rsLowerRange + rsUpperRange + "]", rsFitz = "\\ud83c[\\udffb-\\udfff]", rsModifier = "(?:" + rsCombo + "|" + rsFitz + ")", rsNonAstral = "[^" + rsAstralRange + "]", rsRegional = "(?:\\ud83c[\\udde6-\\uddff]){2}", rsSurrPair = "[\\ud800-\\udbff][\\udc00-\\udfff]", rsUpper = "[" + rsUpperRange + "]", rsZWJ = "\\u200d";
    var rsMiscLower = "(?:" + rsLower + "|" + rsMisc + ")", rsMiscUpper = "(?:" + rsUpper + "|" + rsMisc + ")", rsOptContrLower = "(?:" + rsApos + "(?:d|ll|m|re|s|t|ve))?", rsOptContrUpper = "(?:" + rsApos + "(?:D|LL|M|RE|S|T|VE))?", reOptMod = rsModifier + "?", rsOptVar = "[" + rsVarRange + "]?", rsOptJoin = "(?:" + rsZWJ + "(?:" + [rsNonAstral, rsRegional, rsSurrPair].join("|") + ")" + rsOptVar + reOptMod + ")*", rsOrdLower = "\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])", rsOrdUpper = "\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])", rsSeq = rsOptVar + reOptMod + rsOptJoin, rsEmoji = "(?:" + [rsDingbat, rsRegional, rsSurrPair].join("|") + ")" + rsSeq, rsSymbol = "(?:" + [rsNonAstral + rsCombo + "?", rsCombo, rsRegional, rsSurrPair, rsAstral].join("|") + ")";
    var reApos = RegExp(rsApos, "g");
    var reComboMark = RegExp(rsCombo, "g");
    var reUnicode = RegExp(rsFitz + "(?=" + rsFitz + ")|" + rsSymbol + rsSeq, "g");
    var reUnicodeWord = RegExp([
      rsUpper + "?" + rsLower + "+" + rsOptContrLower + "(?=" + [rsBreak, rsUpper, "$"].join("|") + ")",
      rsMiscUpper + "+" + rsOptContrUpper + "(?=" + [rsBreak, rsUpper + rsMiscLower, "$"].join("|") + ")",
      rsUpper + "?" + rsMiscLower + "+" + rsOptContrLower,
      rsUpper + "+" + rsOptContrUpper,
      rsOrdUpper,
      rsOrdLower,
      rsDigits,
      rsEmoji
    ].join("|"), "g");
    var reHasUnicode = RegExp("[" + rsZWJ + rsAstralRange + rsComboRange + rsVarRange + "]");
    var reHasUnicodeWord = /[a-z][A-Z]|[A-Z]{2}[a-z]|[0-9][a-zA-Z]|[a-zA-Z][0-9]|[^a-zA-Z0-9 ]/;
    var contextProps = [
      "Array",
      "Buffer",
      "DataView",
      "Date",
      "Error",
      "Float32Array",
      "Float64Array",
      "Function",
      "Int8Array",
      "Int16Array",
      "Int32Array",
      "Map",
      "Math",
      "Object",
      "Promise",
      "RegExp",
      "Set",
      "String",
      "Symbol",
      "TypeError",
      "Uint8Array",
      "Uint8ClampedArray",
      "Uint16Array",
      "Uint32Array",
      "WeakMap",
      "_",
      "clearTimeout",
      "isFinite",
      "parseInt",
      "setTimeout"
    ];
    var templateCounter = -1;
    var typedArrayTags = {};
    typedArrayTags[float32Tag] = typedArrayTags[float64Tag] = typedArrayTags[int8Tag] = typedArrayTags[int16Tag] = typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] = typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] = typedArrayTags[uint32Tag] = true;
    typedArrayTags[argsTag] = typedArrayTags[arrayTag] = typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] = typedArrayTags[dataViewTag] = typedArrayTags[dateTag] = typedArrayTags[errorTag] = typedArrayTags[funcTag] = typedArrayTags[mapTag] = typedArrayTags[numberTag] = typedArrayTags[objectTag] = typedArrayTags[regexpTag] = typedArrayTags[setTag] = typedArrayTags[stringTag] = typedArrayTags[weakMapTag] = false;
    var cloneableTags = {};
    cloneableTags[argsTag] = cloneableTags[arrayTag] = cloneableTags[arrayBufferTag] = cloneableTags[dataViewTag] = cloneableTags[boolTag] = cloneableTags[dateTag] = cloneableTags[float32Tag] = cloneableTags[float64Tag] = cloneableTags[int8Tag] = cloneableTags[int16Tag] = cloneableTags[int32Tag] = cloneableTags[mapTag] = cloneableTags[numberTag] = cloneableTags[objectTag] = cloneableTags[regexpTag] = cloneableTags[setTag] = cloneableTags[stringTag] = cloneableTags[symbolTag] = cloneableTags[uint8Tag] = cloneableTags[uint8ClampedTag] = cloneableTags[uint16Tag] = cloneableTags[uint32Tag] = true;
    cloneableTags[errorTag] = cloneableTags[funcTag] = cloneableTags[weakMapTag] = false;
    var deburredLetters = {
      "\xC0": "A",
      "\xC1": "A",
      "\xC2": "A",
      "\xC3": "A",
      "\xC4": "A",
      "\xC5": "A",
      "\xE0": "a",
      "\xE1": "a",
      "\xE2": "a",
      "\xE3": "a",
      "\xE4": "a",
      "\xE5": "a",
      "\xC7": "C",
      "\xE7": "c",
      "\xD0": "D",
      "\xF0": "d",
      "\xC8": "E",
      "\xC9": "E",
      "\xCA": "E",
      "\xCB": "E",
      "\xE8": "e",
      "\xE9": "e",
      "\xEA": "e",
      "\xEB": "e",
      "\xCC": "I",
      "\xCD": "I",
      "\xCE": "I",
      "\xCF": "I",
      "\xEC": "i",
      "\xED": "i",
      "\xEE": "i",
      "\xEF": "i",
      "\xD1": "N",
      "\xF1": "n",
      "\xD2": "O",
      "\xD3": "O",
      "\xD4": "O",
      "\xD5": "O",
      "\xD6": "O",
      "\xD8": "O",
      "\xF2": "o",
      "\xF3": "o",
      "\xF4": "o",
      "\xF5": "o",
      "\xF6": "o",
      "\xF8": "o",
      "\xD9": "U",
      "\xDA": "U",
      "\xDB": "U",
      "\xDC": "U",
      "\xF9": "u",
      "\xFA": "u",
      "\xFB": "u",
      "\xFC": "u",
      "\xDD": "Y",
      "\xFD": "y",
      "\xFF": "y",
      "\xC6": "Ae",
      "\xE6": "ae",
      "\xDE": "Th",
      "\xFE": "th",
      "\xDF": "ss",
      "\u0100": "A",
      "\u0102": "A",
      "\u0104": "A",
      "\u0101": "a",
      "\u0103": "a",
      "\u0105": "a",
      "\u0106": "C",
      "\u0108": "C",
      "\u010A": "C",
      "\u010C": "C",
      "\u0107": "c",
      "\u0109": "c",
      "\u010B": "c",
      "\u010D": "c",
      "\u010E": "D",
      "\u0110": "D",
      "\u010F": "d",
      "\u0111": "d",
      "\u0112": "E",
      "\u0114": "E",
      "\u0116": "E",
      "\u0118": "E",
      "\u011A": "E",
      "\u0113": "e",
      "\u0115": "e",
      "\u0117": "e",
      "\u0119": "e",
      "\u011B": "e",
      "\u011C": "G",
      "\u011E": "G",
      "\u0120": "G",
      "\u0122": "G",
      "\u011D": "g",
      "\u011F": "g",
      "\u0121": "g",
      "\u0123": "g",
      "\u0124": "H",
      "\u0126": "H",
      "\u0125": "h",
      "\u0127": "h",
      "\u0128": "I",
      "\u012A": "I",
      "\u012C": "I",
      "\u012E": "I",
      "\u0130": "I",
      "\u0129": "i",
      "\u012B": "i",
      "\u012D": "i",
      "\u012F": "i",
      "\u0131": "i",
      "\u0134": "J",
      "\u0135": "j",
      "\u0136": "K",
      "\u0137": "k",
      "\u0138": "k",
      "\u0139": "L",
      "\u013B": "L",
      "\u013D": "L",
      "\u013F": "L",
      "\u0141": "L",
      "\u013A": "l",
      "\u013C": "l",
      "\u013E": "l",
      "\u0140": "l",
      "\u0142": "l",
      "\u0143": "N",
      "\u0145": "N",
      "\u0147": "N",
      "\u014A": "N",
      "\u0144": "n",
      "\u0146": "n",
      "\u0148": "n",
      "\u014B": "n",
      "\u014C": "O",
      "\u014E": "O",
      "\u0150": "O",
      "\u014D": "o",
      "\u014F": "o",
      "\u0151": "o",
      "\u0154": "R",
      "\u0156": "R",
      "\u0158": "R",
      "\u0155": "r",
      "\u0157": "r",
      "\u0159": "r",
      "\u015A": "S",
      "\u015C": "S",
      "\u015E": "S",
      "\u0160": "S",
      "\u015B": "s",
      "\u015D": "s",
      "\u015F": "s",
      "\u0161": "s",
      "\u0162": "T",
      "\u0164": "T",
      "\u0166": "T",
      "\u0163": "t",
      "\u0165": "t",
      "\u0167": "t",
      "\u0168": "U",
      "\u016A": "U",
      "\u016C": "U",
      "\u016E": "U",
      "\u0170": "U",
      "\u0172": "U",
      "\u0169": "u",
      "\u016B": "u",
      "\u016D": "u",
      "\u016F": "u",
      "\u0171": "u",
      "\u0173": "u",
      "\u0174": "W",
      "\u0175": "w",
      "\u0176": "Y",
      "\u0177": "y",
      "\u0178": "Y",
      "\u0179": "Z",
      "\u017B": "Z",
      "\u017D": "Z",
      "\u017A": "z",
      "\u017C": "z",
      "\u017E": "z",
      "\u0132": "IJ",
      "\u0133": "ij",
      "\u0152": "Oe",
      "\u0153": "oe",
      "\u0149": "'n",
      "\u017F": "s"
    };
    var htmlEscapes = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;"
    };
    var htmlUnescapes = {
      "&amp;": "&",
      "&lt;": "<",
      "&gt;": ">",
      "&quot;": '"',
      "&#39;": "'"
    };
    var stringEscapes = {
      "\\": "\\",
      "'": "'",
      "\n": "n",
      "\r": "r",
      "\u2028": "u2028",
      "\u2029": "u2029"
    };
    var freeParseFloat = parseFloat, freeParseInt = parseInt;
    var freeGlobal = typeof commonjsGlobal == "object" && commonjsGlobal && commonjsGlobal.Object === Object && commonjsGlobal;
    var freeSelf = typeof self == "object" && self && self.Object === Object && self;
    var root = freeGlobal || freeSelf || Function("return this")();
    var freeExports = exports && !exports.nodeType && exports;
    var freeModule = freeExports && true && module && !module.nodeType && module;
    var moduleExports = freeModule && freeModule.exports === freeExports;
    var freeProcess = moduleExports && freeGlobal.process;
    var nodeUtil = function() {
      try {
        var types = freeModule && freeModule.require && freeModule.require("util").types;
        if (types) {
          return types;
        }
        return freeProcess && freeProcess.binding && freeProcess.binding("util");
      } catch (e) {
      }
    }();
    var nodeIsArrayBuffer = nodeUtil && nodeUtil.isArrayBuffer, nodeIsDate = nodeUtil && nodeUtil.isDate, nodeIsMap = nodeUtil && nodeUtil.isMap, nodeIsRegExp = nodeUtil && nodeUtil.isRegExp, nodeIsSet = nodeUtil && nodeUtil.isSet, nodeIsTypedArray = nodeUtil && nodeUtil.isTypedArray;
    function apply(func, thisArg, args) {
      switch (args.length) {
        case 0:
          return func.call(thisArg);
        case 1:
          return func.call(thisArg, args[0]);
        case 2:
          return func.call(thisArg, args[0], args[1]);
        case 3:
          return func.call(thisArg, args[0], args[1], args[2]);
      }
      return func.apply(thisArg, args);
    }
    function arrayAggregator(array, setter, iteratee, accumulator) {
      var index = -1, length = array == null ? 0 : array.length;
      while (++index < length) {
        var value = array[index];
        setter(accumulator, value, iteratee(value), array);
      }
      return accumulator;
    }
    function arrayEach(array, iteratee) {
      var index = -1, length = array == null ? 0 : array.length;
      while (++index < length) {
        if (iteratee(array[index], index, array) === false) {
          break;
        }
      }
      return array;
    }
    function arrayEachRight(array, iteratee) {
      var length = array == null ? 0 : array.length;
      while (length--) {
        if (iteratee(array[length], length, array) === false) {
          break;
        }
      }
      return array;
    }
    function arrayEvery(array, predicate) {
      var index = -1, length = array == null ? 0 : array.length;
      while (++index < length) {
        if (!predicate(array[index], index, array)) {
          return false;
        }
      }
      return true;
    }
    function arrayFilter(array, predicate) {
      var index = -1, length = array == null ? 0 : array.length, resIndex = 0, result = [];
      while (++index < length) {
        var value = array[index];
        if (predicate(value, index, array)) {
          result[resIndex++] = value;
        }
      }
      return result;
    }
    function arrayIncludes(array, value) {
      var length = array == null ? 0 : array.length;
      return !!length && baseIndexOf(array, value, 0) > -1;
    }
    function arrayIncludesWith(array, value, comparator) {
      var index = -1, length = array == null ? 0 : array.length;
      while (++index < length) {
        if (comparator(value, array[index])) {
          return true;
        }
      }
      return false;
    }
    function arrayMap(array, iteratee) {
      var index = -1, length = array == null ? 0 : array.length, result = Array(length);
      while (++index < length) {
        result[index] = iteratee(array[index], index, array);
      }
      return result;
    }
    function arrayPush(array, values) {
      var index = -1, length = values.length, offset = array.length;
      while (++index < length) {
        array[offset + index] = values[index];
      }
      return array;
    }
    function arrayReduce(array, iteratee, accumulator, initAccum) {
      var index = -1, length = array == null ? 0 : array.length;
      if (initAccum && length) {
        accumulator = array[++index];
      }
      while (++index < length) {
        accumulator = iteratee(accumulator, array[index], index, array);
      }
      return accumulator;
    }
    function arrayReduceRight(array, iteratee, accumulator, initAccum) {
      var length = array == null ? 0 : array.length;
      if (initAccum && length) {
        accumulator = array[--length];
      }
      while (length--) {
        accumulator = iteratee(accumulator, array[length], length, array);
      }
      return accumulator;
    }
    function arraySome(array, predicate) {
      var index = -1, length = array == null ? 0 : array.length;
      while (++index < length) {
        if (predicate(array[index], index, array)) {
          return true;
        }
      }
      return false;
    }
    var asciiSize = baseProperty("length");
    function asciiToArray(string) {
      return string.split("");
    }
    function asciiWords(string) {
      return string.match(reAsciiWord) || [];
    }
    function baseFindKey(collection, predicate, eachFunc) {
      var result;
      eachFunc(collection, function(value, key, collection2) {
        if (predicate(value, key, collection2)) {
          result = key;
          return false;
        }
      });
      return result;
    }
    function baseFindIndex(array, predicate, fromIndex, fromRight) {
      var length = array.length, index = fromIndex + (fromRight ? 1 : -1);
      while (fromRight ? index-- : ++index < length) {
        if (predicate(array[index], index, array)) {
          return index;
        }
      }
      return -1;
    }
    function baseIndexOf(array, value, fromIndex) {
      return value === value ? strictIndexOf(array, value, fromIndex) : baseFindIndex(array, baseIsNaN, fromIndex);
    }
    function baseIndexOfWith(array, value, fromIndex, comparator) {
      var index = fromIndex - 1, length = array.length;
      while (++index < length) {
        if (comparator(array[index], value)) {
          return index;
        }
      }
      return -1;
    }
    function baseIsNaN(value) {
      return value !== value;
    }
    function baseMean(array, iteratee) {
      var length = array == null ? 0 : array.length;
      return length ? baseSum(array, iteratee) / length : NAN;
    }
    function baseProperty(key) {
      return function(object) {
        return object == null ? undefined$1 : object[key];
      };
    }
    function basePropertyOf(object) {
      return function(key) {
        return object == null ? undefined$1 : object[key];
      };
    }
    function baseReduce(collection, iteratee, accumulator, initAccum, eachFunc) {
      eachFunc(collection, function(value, index, collection2) {
        accumulator = initAccum ? (initAccum = false, value) : iteratee(accumulator, value, index, collection2);
      });
      return accumulator;
    }
    function baseSortBy(array, comparer) {
      var length = array.length;
      array.sort(comparer);
      while (length--) {
        array[length] = array[length].value;
      }
      return array;
    }
    function baseSum(array, iteratee) {
      var result, index = -1, length = array.length;
      while (++index < length) {
        var current = iteratee(array[index]);
        if (current !== undefined$1) {
          result = result === undefined$1 ? current : result + current;
        }
      }
      return result;
    }
    function baseTimes(n, iteratee) {
      var index = -1, result = Array(n);
      while (++index < n) {
        result[index] = iteratee(index);
      }
      return result;
    }
    function baseToPairs(object, props) {
      return arrayMap(props, function(key) {
        return [key, object[key]];
      });
    }
    function baseTrim(string) {
      return string ? string.slice(0, trimmedEndIndex(string) + 1).replace(reTrimStart, "") : string;
    }
    function baseUnary(func) {
      return function(value) {
        return func(value);
      };
    }
    function baseValues(object, props) {
      return arrayMap(props, function(key) {
        return object[key];
      });
    }
    function cacheHas(cache, key) {
      return cache.has(key);
    }
    function charsStartIndex(strSymbols, chrSymbols) {
      var index = -1, length = strSymbols.length;
      while (++index < length && baseIndexOf(chrSymbols, strSymbols[index], 0) > -1) {
      }
      return index;
    }
    function charsEndIndex(strSymbols, chrSymbols) {
      var index = strSymbols.length;
      while (index-- && baseIndexOf(chrSymbols, strSymbols[index], 0) > -1) {
      }
      return index;
    }
    function countHolders(array, placeholder) {
      var length = array.length, result = 0;
      while (length--) {
        if (array[length] === placeholder) {
          ++result;
        }
      }
      return result;
    }
    var deburrLetter = basePropertyOf(deburredLetters);
    var escapeHtmlChar = basePropertyOf(htmlEscapes);
    function escapeStringChar(chr) {
      return "\\" + stringEscapes[chr];
    }
    function getValue(object, key) {
      return object == null ? undefined$1 : object[key];
    }
    function hasUnicode(string) {
      return reHasUnicode.test(string);
    }
    function hasUnicodeWord(string) {
      return reHasUnicodeWord.test(string);
    }
    function iteratorToArray(iterator) {
      var data, result = [];
      while (!(data = iterator.next()).done) {
        result.push(data.value);
      }
      return result;
    }
    function mapToArray(map) {
      var index = -1, result = Array(map.size);
      map.forEach(function(value, key) {
        result[++index] = [key, value];
      });
      return result;
    }
    function overArg(func, transform) {
      return function(arg) {
        return func(transform(arg));
      };
    }
    function replaceHolders(array, placeholder) {
      var index = -1, length = array.length, resIndex = 0, result = [];
      while (++index < length) {
        var value = array[index];
        if (value === placeholder || value === PLACEHOLDER) {
          array[index] = PLACEHOLDER;
          result[resIndex++] = index;
        }
      }
      return result;
    }
    function setToArray(set) {
      var index = -1, result = Array(set.size);
      set.forEach(function(value) {
        result[++index] = value;
      });
      return result;
    }
    function setToPairs(set) {
      var index = -1, result = Array(set.size);
      set.forEach(function(value) {
        result[++index] = [value, value];
      });
      return result;
    }
    function strictIndexOf(array, value, fromIndex) {
      var index = fromIndex - 1, length = array.length;
      while (++index < length) {
        if (array[index] === value) {
          return index;
        }
      }
      return -1;
    }
    function strictLastIndexOf(array, value, fromIndex) {
      var index = fromIndex + 1;
      while (index--) {
        if (array[index] === value) {
          return index;
        }
      }
      return index;
    }
    function stringSize(string) {
      return hasUnicode(string) ? unicodeSize(string) : asciiSize(string);
    }
    function stringToArray(string) {
      return hasUnicode(string) ? unicodeToArray(string) : asciiToArray(string);
    }
    function trimmedEndIndex(string) {
      var index = string.length;
      while (index-- && reWhitespace.test(string.charAt(index))) {
      }
      return index;
    }
    var unescapeHtmlChar = basePropertyOf(htmlUnescapes);
    function unicodeSize(string) {
      var result = reUnicode.lastIndex = 0;
      while (reUnicode.test(string)) {
        ++result;
      }
      return result;
    }
    function unicodeToArray(string) {
      return string.match(reUnicode) || [];
    }
    function unicodeWords(string) {
      return string.match(reUnicodeWord) || [];
    }
    var runInContext = function runInContext2(context) {
      context = context == null ? root : _2.defaults(root.Object(), context, _2.pick(root, contextProps));
      var Array2 = context.Array, Date2 = context.Date, Error2 = context.Error, Function2 = context.Function, Math2 = context.Math, Object2 = context.Object, RegExp2 = context.RegExp, String2 = context.String, TypeError2 = context.TypeError;
      var arrayProto = Array2.prototype, funcProto = Function2.prototype, objectProto = Object2.prototype;
      var coreJsData = context["__core-js_shared__"];
      var funcToString = funcProto.toString;
      var hasOwnProperty = objectProto.hasOwnProperty;
      var idCounter = 0;
      var maskSrcKey = function() {
        var uid = /[^.]+$/.exec(coreJsData && coreJsData.keys && coreJsData.keys.IE_PROTO || "");
        return uid ? "Symbol(src)_1." + uid : "";
      }();
      var nativeObjectToString = objectProto.toString;
      var objectCtorString = funcToString.call(Object2);
      var oldDash = root._;
      var reIsNative = RegExp2("^" + funcToString.call(hasOwnProperty).replace(reRegExpChar, "\\$&").replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, "$1.*?") + "$");
      var Buffer2 = moduleExports ? context.Buffer : undefined$1, Symbol = context.Symbol, Uint8Array2 = context.Uint8Array, allocUnsafe = Buffer2 ? Buffer2.allocUnsafe : undefined$1, getPrototype = overArg(Object2.getPrototypeOf, Object2), objectCreate = Object2.create, propertyIsEnumerable = objectProto.propertyIsEnumerable, splice = arrayProto.splice, spreadableSymbol = Symbol ? Symbol.isConcatSpreadable : undefined$1, symIterator = Symbol ? Symbol.iterator : undefined$1, symToStringTag = Symbol ? Symbol.toStringTag : undefined$1;
      var defineProperty = function() {
        try {
          var func = getNative(Object2, "defineProperty");
          func({}, "", {});
          return func;
        } catch (e) {
        }
      }();
      var ctxClearTimeout = context.clearTimeout !== root.clearTimeout && context.clearTimeout, ctxNow = Date2 && Date2.now !== root.Date.now && Date2.now, ctxSetTimeout = context.setTimeout !== root.setTimeout && context.setTimeout;
      var nativeCeil = Math2.ceil, nativeFloor = Math2.floor, nativeGetSymbols = Object2.getOwnPropertySymbols, nativeIsBuffer = Buffer2 ? Buffer2.isBuffer : undefined$1, nativeIsFinite = context.isFinite, nativeJoin = arrayProto.join, nativeKeys = overArg(Object2.keys, Object2), nativeMax = Math2.max, nativeMin = Math2.min, nativeNow = Date2.now, nativeParseInt = context.parseInt, nativeRandom = Math2.random, nativeReverse = arrayProto.reverse;
      var DataView = getNative(context, "DataView"), Map2 = getNative(context, "Map"), Promise2 = getNative(context, "Promise"), Set2 = getNative(context, "Set"), WeakMap = getNative(context, "WeakMap"), nativeCreate = getNative(Object2, "create");
      var metaMap = WeakMap && new WeakMap();
      var realNames = {};
      var dataViewCtorString = toSource(DataView), mapCtorString = toSource(Map2), promiseCtorString = toSource(Promise2), setCtorString = toSource(Set2), weakMapCtorString = toSource(WeakMap);
      var symbolProto = Symbol ? Symbol.prototype : undefined$1, symbolValueOf = symbolProto ? symbolProto.valueOf : undefined$1, symbolToString = symbolProto ? symbolProto.toString : undefined$1;
      function lodash2(value) {
        if (isObjectLike(value) && !isArray(value) && !(value instanceof LazyWrapper)) {
          if (value instanceof LodashWrapper) {
            return value;
          }
          if (hasOwnProperty.call(value, "__wrapped__")) {
            return wrapperClone(value);
          }
        }
        return new LodashWrapper(value);
      }
      var baseCreate = function() {
        function object() {
        }
        return function(proto) {
          if (!isObject(proto)) {
            return {};
          }
          if (objectCreate) {
            return objectCreate(proto);
          }
          object.prototype = proto;
          var result2 = new object();
          object.prototype = undefined$1;
          return result2;
        };
      }();
      function baseLodash() {
      }
      function LodashWrapper(value, chainAll) {
        this.__wrapped__ = value;
        this.__actions__ = [];
        this.__chain__ = !!chainAll;
        this.__index__ = 0;
        this.__values__ = undefined$1;
      }
      lodash2.templateSettings = {
        "escape": reEscape,
        "evaluate": reEvaluate,
        "interpolate": reInterpolate,
        "variable": "",
        "imports": {
          "_": lodash2
        }
      };
      lodash2.prototype = baseLodash.prototype;
      lodash2.prototype.constructor = lodash2;
      LodashWrapper.prototype = baseCreate(baseLodash.prototype);
      LodashWrapper.prototype.constructor = LodashWrapper;
      function LazyWrapper(value) {
        this.__wrapped__ = value;
        this.__actions__ = [];
        this.__dir__ = 1;
        this.__filtered__ = false;
        this.__iteratees__ = [];
        this.__takeCount__ = MAX_ARRAY_LENGTH;
        this.__views__ = [];
      }
      function lazyClone() {
        var result2 = new LazyWrapper(this.__wrapped__);
        result2.__actions__ = copyArray(this.__actions__);
        result2.__dir__ = this.__dir__;
        result2.__filtered__ = this.__filtered__;
        result2.__iteratees__ = copyArray(this.__iteratees__);
        result2.__takeCount__ = this.__takeCount__;
        result2.__views__ = copyArray(this.__views__);
        return result2;
      }
      function lazyReverse() {
        if (this.__filtered__) {
          var result2 = new LazyWrapper(this);
          result2.__dir__ = -1;
          result2.__filtered__ = true;
        } else {
          result2 = this.clone();
          result2.__dir__ *= -1;
        }
        return result2;
      }
      function lazyValue() {
        var array = this.__wrapped__.value(), dir = this.__dir__, isArr = isArray(array), isRight = dir < 0, arrLength = isArr ? array.length : 0, view = getView(0, arrLength, this.__views__), start = view.start, end = view.end, length = end - start, index = isRight ? end : start - 1, iteratees = this.__iteratees__, iterLength = iteratees.length, resIndex = 0, takeCount = nativeMin(length, this.__takeCount__);
        if (!isArr || !isRight && arrLength == length && takeCount == length) {
          return baseWrapperValue(array, this.__actions__);
        }
        var result2 = [];
        outer:
          while (length-- && resIndex < takeCount) {
            index += dir;
            var iterIndex = -1, value = array[index];
            while (++iterIndex < iterLength) {
              var data = iteratees[iterIndex], iteratee2 = data.iteratee, type = data.type, computed2 = iteratee2(value);
              if (type == LAZY_MAP_FLAG) {
                value = computed2;
              } else if (!computed2) {
                if (type == LAZY_FILTER_FLAG) {
                  continue outer;
                } else {
                  break outer;
                }
              }
            }
            result2[resIndex++] = value;
          }
        return result2;
      }
      LazyWrapper.prototype = baseCreate(baseLodash.prototype);
      LazyWrapper.prototype.constructor = LazyWrapper;
      function Hash(entries) {
        var index = -1, length = entries == null ? 0 : entries.length;
        this.clear();
        while (++index < length) {
          var entry = entries[index];
          this.set(entry[0], entry[1]);
        }
      }
      function hashClear() {
        this.__data__ = nativeCreate ? nativeCreate(null) : {};
        this.size = 0;
      }
      function hashDelete(key) {
        var result2 = this.has(key) && delete this.__data__[key];
        this.size -= result2 ? 1 : 0;
        return result2;
      }
      function hashGet(key) {
        var data = this.__data__;
        if (nativeCreate) {
          var result2 = data[key];
          return result2 === HASH_UNDEFINED ? undefined$1 : result2;
        }
        return hasOwnProperty.call(data, key) ? data[key] : undefined$1;
      }
      function hashHas(key) {
        var data = this.__data__;
        return nativeCreate ? data[key] !== undefined$1 : hasOwnProperty.call(data, key);
      }
      function hashSet(key, value) {
        var data = this.__data__;
        this.size += this.has(key) ? 0 : 1;
        data[key] = nativeCreate && value === undefined$1 ? HASH_UNDEFINED : value;
        return this;
      }
      Hash.prototype.clear = hashClear;
      Hash.prototype["delete"] = hashDelete;
      Hash.prototype.get = hashGet;
      Hash.prototype.has = hashHas;
      Hash.prototype.set = hashSet;
      function ListCache(entries) {
        var index = -1, length = entries == null ? 0 : entries.length;
        this.clear();
        while (++index < length) {
          var entry = entries[index];
          this.set(entry[0], entry[1]);
        }
      }
      function listCacheClear() {
        this.__data__ = [];
        this.size = 0;
      }
      function listCacheDelete(key) {
        var data = this.__data__, index = assocIndexOf(data, key);
        if (index < 0) {
          return false;
        }
        var lastIndex = data.length - 1;
        if (index == lastIndex) {
          data.pop();
        } else {
          splice.call(data, index, 1);
        }
        --this.size;
        return true;
      }
      function listCacheGet(key) {
        var data = this.__data__, index = assocIndexOf(data, key);
        return index < 0 ? undefined$1 : data[index][1];
      }
      function listCacheHas(key) {
        return assocIndexOf(this.__data__, key) > -1;
      }
      function listCacheSet(key, value) {
        var data = this.__data__, index = assocIndexOf(data, key);
        if (index < 0) {
          ++this.size;
          data.push([key, value]);
        } else {
          data[index][1] = value;
        }
        return this;
      }
      ListCache.prototype.clear = listCacheClear;
      ListCache.prototype["delete"] = listCacheDelete;
      ListCache.prototype.get = listCacheGet;
      ListCache.prototype.has = listCacheHas;
      ListCache.prototype.set = listCacheSet;
      function MapCache(entries) {
        var index = -1, length = entries == null ? 0 : entries.length;
        this.clear();
        while (++index < length) {
          var entry = entries[index];
          this.set(entry[0], entry[1]);
        }
      }
      function mapCacheClear() {
        this.size = 0;
        this.__data__ = {
          "hash": new Hash(),
          "map": new (Map2 || ListCache)(),
          "string": new Hash()
        };
      }
      function mapCacheDelete(key) {
        var result2 = getMapData(this, key)["delete"](key);
        this.size -= result2 ? 1 : 0;
        return result2;
      }
      function mapCacheGet(key) {
        return getMapData(this, key).get(key);
      }
      function mapCacheHas(key) {
        return getMapData(this, key).has(key);
      }
      function mapCacheSet(key, value) {
        var data = getMapData(this, key), size2 = data.size;
        data.set(key, value);
        this.size += data.size == size2 ? 0 : 1;
        return this;
      }
      MapCache.prototype.clear = mapCacheClear;
      MapCache.prototype["delete"] = mapCacheDelete;
      MapCache.prototype.get = mapCacheGet;
      MapCache.prototype.has = mapCacheHas;
      MapCache.prototype.set = mapCacheSet;
      function SetCache(values2) {
        var index = -1, length = values2 == null ? 0 : values2.length;
        this.__data__ = new MapCache();
        while (++index < length) {
          this.add(values2[index]);
        }
      }
      function setCacheAdd(value) {
        this.__data__.set(value, HASH_UNDEFINED);
        return this;
      }
      function setCacheHas(value) {
        return this.__data__.has(value);
      }
      SetCache.prototype.add = SetCache.prototype.push = setCacheAdd;
      SetCache.prototype.has = setCacheHas;
      function Stack(entries) {
        var data = this.__data__ = new ListCache(entries);
        this.size = data.size;
      }
      function stackClear() {
        this.__data__ = new ListCache();
        this.size = 0;
      }
      function stackDelete(key) {
        var data = this.__data__, result2 = data["delete"](key);
        this.size = data.size;
        return result2;
      }
      function stackGet(key) {
        return this.__data__.get(key);
      }
      function stackHas(key) {
        return this.__data__.has(key);
      }
      function stackSet(key, value) {
        var data = this.__data__;
        if (data instanceof ListCache) {
          var pairs = data.__data__;
          if (!Map2 || pairs.length < LARGE_ARRAY_SIZE - 1) {
            pairs.push([key, value]);
            this.size = ++data.size;
            return this;
          }
          data = this.__data__ = new MapCache(pairs);
        }
        data.set(key, value);
        this.size = data.size;
        return this;
      }
      Stack.prototype.clear = stackClear;
      Stack.prototype["delete"] = stackDelete;
      Stack.prototype.get = stackGet;
      Stack.prototype.has = stackHas;
      Stack.prototype.set = stackSet;
      function arrayLikeKeys(value, inherited) {
        var isArr = isArray(value), isArg = !isArr && isArguments(value), isBuff = !isArr && !isArg && isBuffer(value), isType = !isArr && !isArg && !isBuff && isTypedArray(value), skipIndexes = isArr || isArg || isBuff || isType, result2 = skipIndexes ? baseTimes(value.length, String2) : [], length = result2.length;
        for (var key in value) {
          if ((inherited || hasOwnProperty.call(value, key)) && !(skipIndexes && (key == "length" || isBuff && (key == "offset" || key == "parent") || isType && (key == "buffer" || key == "byteLength" || key == "byteOffset") || isIndex(key, length)))) {
            result2.push(key);
          }
        }
        return result2;
      }
      function arraySample(array) {
        var length = array.length;
        return length ? array[baseRandom(0, length - 1)] : undefined$1;
      }
      function arraySampleSize(array, n) {
        return shuffleSelf(copyArray(array), baseClamp(n, 0, array.length));
      }
      function arrayShuffle(array) {
        return shuffleSelf(copyArray(array));
      }
      function assignMergeValue(object, key, value) {
        if (value !== undefined$1 && !eq(object[key], value) || value === undefined$1 && !(key in object)) {
          baseAssignValue(object, key, value);
        }
      }
      function assignValue(object, key, value) {
        var objValue = object[key];
        if (!(hasOwnProperty.call(object, key) && eq(objValue, value)) || value === undefined$1 && !(key in object)) {
          baseAssignValue(object, key, value);
        }
      }
      function assocIndexOf(array, key) {
        var length = array.length;
        while (length--) {
          if (eq(array[length][0], key)) {
            return length;
          }
        }
        return -1;
      }
      function baseAggregator(collection, setter, iteratee2, accumulator) {
        baseEach(collection, function(value, key, collection2) {
          setter(accumulator, value, iteratee2(value), collection2);
        });
        return accumulator;
      }
      function baseAssign(object, source) {
        return object && copyObject(source, keys(source), object);
      }
      function baseAssignIn(object, source) {
        return object && copyObject(source, keysIn(source), object);
      }
      function baseAssignValue(object, key, value) {
        if (key == "__proto__" && defineProperty) {
          defineProperty(object, key, {
            "configurable": true,
            "enumerable": true,
            "value": value,
            "writable": true
          });
        } else {
          object[key] = value;
        }
      }
      function baseAt(object, paths) {
        var index = -1, length = paths.length, result2 = Array2(length), skip = object == null;
        while (++index < length) {
          result2[index] = skip ? undefined$1 : get(object, paths[index]);
        }
        return result2;
      }
      function baseClamp(number, lower, upper) {
        if (number === number) {
          if (upper !== undefined$1) {
            number = number <= upper ? number : upper;
          }
          if (lower !== undefined$1) {
            number = number >= lower ? number : lower;
          }
        }
        return number;
      }
      function baseClone(value, bitmask, customizer, key, object, stack) {
        var result2, isDeep = bitmask & CLONE_DEEP_FLAG, isFlat = bitmask & CLONE_FLAT_FLAG, isFull = bitmask & CLONE_SYMBOLS_FLAG;
        if (customizer) {
          result2 = object ? customizer(value, key, object, stack) : customizer(value);
        }
        if (result2 !== undefined$1) {
          return result2;
        }
        if (!isObject(value)) {
          return value;
        }
        var isArr = isArray(value);
        if (isArr) {
          result2 = initCloneArray(value);
          if (!isDeep) {
            return copyArray(value, result2);
          }
        } else {
          var tag = getTag(value), isFunc = tag == funcTag || tag == genTag;
          if (isBuffer(value)) {
            return cloneBuffer(value, isDeep);
          }
          if (tag == objectTag || tag == argsTag || isFunc && !object) {
            result2 = isFlat || isFunc ? {} : initCloneObject(value);
            if (!isDeep) {
              return isFlat ? copySymbolsIn(value, baseAssignIn(result2, value)) : copySymbols(value, baseAssign(result2, value));
            }
          } else {
            if (!cloneableTags[tag]) {
              return object ? value : {};
            }
            result2 = initCloneByTag(value, tag, isDeep);
          }
        }
        stack || (stack = new Stack());
        var stacked = stack.get(value);
        if (stacked) {
          return stacked;
        }
        stack.set(value, result2);
        if (isSet(value)) {
          value.forEach(function(subValue) {
            result2.add(baseClone(subValue, bitmask, customizer, subValue, value, stack));
          });
        } else if (isMap(value)) {
          value.forEach(function(subValue, key2) {
            result2.set(key2, baseClone(subValue, bitmask, customizer, key2, value, stack));
          });
        }
        var keysFunc = isFull ? isFlat ? getAllKeysIn : getAllKeys : isFlat ? keysIn : keys;
        var props = isArr ? undefined$1 : keysFunc(value);
        arrayEach(props || value, function(subValue, key2) {
          if (props) {
            key2 = subValue;
            subValue = value[key2];
          }
          assignValue(result2, key2, baseClone(subValue, bitmask, customizer, key2, value, stack));
        });
        return result2;
      }
      function baseConforms(source) {
        var props = keys(source);
        return function(object) {
          return baseConformsTo(object, source, props);
        };
      }
      function baseConformsTo(object, source, props) {
        var length = props.length;
        if (object == null) {
          return !length;
        }
        object = Object2(object);
        while (length--) {
          var key = props[length], predicate = source[key], value = object[key];
          if (value === undefined$1 && !(key in object) || !predicate(value)) {
            return false;
          }
        }
        return true;
      }
      function baseDelay(func, wait, args) {
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        return setTimeout2(function() {
          func.apply(undefined$1, args);
        }, wait);
      }
      function baseDifference(array, values2, iteratee2, comparator) {
        var index = -1, includes2 = arrayIncludes, isCommon = true, length = array.length, result2 = [], valuesLength = values2.length;
        if (!length) {
          return result2;
        }
        if (iteratee2) {
          values2 = arrayMap(values2, baseUnary(iteratee2));
        }
        if (comparator) {
          includes2 = arrayIncludesWith;
          isCommon = false;
        } else if (values2.length >= LARGE_ARRAY_SIZE) {
          includes2 = cacheHas;
          isCommon = false;
          values2 = new SetCache(values2);
        }
        outer:
          while (++index < length) {
            var value = array[index], computed2 = iteratee2 == null ? value : iteratee2(value);
            value = comparator || value !== 0 ? value : 0;
            if (isCommon && computed2 === computed2) {
              var valuesIndex = valuesLength;
              while (valuesIndex--) {
                if (values2[valuesIndex] === computed2) {
                  continue outer;
                }
              }
              result2.push(value);
            } else if (!includes2(values2, computed2, comparator)) {
              result2.push(value);
            }
          }
        return result2;
      }
      var baseEach = createBaseEach(baseForOwn);
      var baseEachRight = createBaseEach(baseForOwnRight, true);
      function baseEvery(collection, predicate) {
        var result2 = true;
        baseEach(collection, function(value, index, collection2) {
          result2 = !!predicate(value, index, collection2);
          return result2;
        });
        return result2;
      }
      function baseExtremum(array, iteratee2, comparator) {
        var index = -1, length = array.length;
        while (++index < length) {
          var value = array[index], current = iteratee2(value);
          if (current != null && (computed2 === undefined$1 ? current === current && !isSymbol(current) : comparator(current, computed2))) {
            var computed2 = current, result2 = value;
          }
        }
        return result2;
      }
      function baseFill(array, value, start, end) {
        var length = array.length;
        start = toInteger(start);
        if (start < 0) {
          start = -start > length ? 0 : length + start;
        }
        end = end === undefined$1 || end > length ? length : toInteger(end);
        if (end < 0) {
          end += length;
        }
        end = start > end ? 0 : toLength(end);
        while (start < end) {
          array[start++] = value;
        }
        return array;
      }
      function baseFilter(collection, predicate) {
        var result2 = [];
        baseEach(collection, function(value, index, collection2) {
          if (predicate(value, index, collection2)) {
            result2.push(value);
          }
        });
        return result2;
      }
      function baseFlatten(array, depth, predicate, isStrict, result2) {
        var index = -1, length = array.length;
        predicate || (predicate = isFlattenable);
        result2 || (result2 = []);
        while (++index < length) {
          var value = array[index];
          if (depth > 0 && predicate(value)) {
            if (depth > 1) {
              baseFlatten(value, depth - 1, predicate, isStrict, result2);
            } else {
              arrayPush(result2, value);
            }
          } else if (!isStrict) {
            result2[result2.length] = value;
          }
        }
        return result2;
      }
      var baseFor = createBaseFor();
      var baseForRight = createBaseFor(true);
      function baseForOwn(object, iteratee2) {
        return object && baseFor(object, iteratee2, keys);
      }
      function baseForOwnRight(object, iteratee2) {
        return object && baseForRight(object, iteratee2, keys);
      }
      function baseFunctions(object, props) {
        return arrayFilter(props, function(key) {
          return isFunction(object[key]);
        });
      }
      function baseGet(object, path) {
        path = castPath(path, object);
        var index = 0, length = path.length;
        while (object != null && index < length) {
          object = object[toKey(path[index++])];
        }
        return index && index == length ? object : undefined$1;
      }
      function baseGetAllKeys(object, keysFunc, symbolsFunc) {
        var result2 = keysFunc(object);
        return isArray(object) ? result2 : arrayPush(result2, symbolsFunc(object));
      }
      function baseGetTag(value) {
        if (value == null) {
          return value === undefined$1 ? undefinedTag : nullTag;
        }
        return symToStringTag && symToStringTag in Object2(value) ? getRawTag(value) : objectToString(value);
      }
      function baseGt(value, other) {
        return value > other;
      }
      function baseHas(object, key) {
        return object != null && hasOwnProperty.call(object, key);
      }
      function baseHasIn(object, key) {
        return object != null && key in Object2(object);
      }
      function baseInRange(number, start, end) {
        return number >= nativeMin(start, end) && number < nativeMax(start, end);
      }
      function baseIntersection(arrays, iteratee2, comparator) {
        var includes2 = comparator ? arrayIncludesWith : arrayIncludes, length = arrays[0].length, othLength = arrays.length, othIndex = othLength, caches = Array2(othLength), maxLength = Infinity, result2 = [];
        while (othIndex--) {
          var array = arrays[othIndex];
          if (othIndex && iteratee2) {
            array = arrayMap(array, baseUnary(iteratee2));
          }
          maxLength = nativeMin(array.length, maxLength);
          caches[othIndex] = !comparator && (iteratee2 || length >= 120 && array.length >= 120) ? new SetCache(othIndex && array) : undefined$1;
        }
        array = arrays[0];
        var index = -1, seen = caches[0];
        outer:
          while (++index < length && result2.length < maxLength) {
            var value = array[index], computed2 = iteratee2 ? iteratee2(value) : value;
            value = comparator || value !== 0 ? value : 0;
            if (!(seen ? cacheHas(seen, computed2) : includes2(result2, computed2, comparator))) {
              othIndex = othLength;
              while (--othIndex) {
                var cache = caches[othIndex];
                if (!(cache ? cacheHas(cache, computed2) : includes2(arrays[othIndex], computed2, comparator))) {
                  continue outer;
                }
              }
              if (seen) {
                seen.push(computed2);
              }
              result2.push(value);
            }
          }
        return result2;
      }
      function baseInverter(object, setter, iteratee2, accumulator) {
        baseForOwn(object, function(value, key, object2) {
          setter(accumulator, iteratee2(value), key, object2);
        });
        return accumulator;
      }
      function baseInvoke(object, path, args) {
        path = castPath(path, object);
        object = parent(object, path);
        var func = object == null ? object : object[toKey(last(path))];
        return func == null ? undefined$1 : apply(func, object, args);
      }
      function baseIsArguments(value) {
        return isObjectLike(value) && baseGetTag(value) == argsTag;
      }
      function baseIsArrayBuffer(value) {
        return isObjectLike(value) && baseGetTag(value) == arrayBufferTag;
      }
      function baseIsDate(value) {
        return isObjectLike(value) && baseGetTag(value) == dateTag;
      }
      function baseIsEqual(value, other, bitmask, customizer, stack) {
        if (value === other) {
          return true;
        }
        if (value == null || other == null || !isObjectLike(value) && !isObjectLike(other)) {
          return value !== value && other !== other;
        }
        return baseIsEqualDeep(value, other, bitmask, customizer, baseIsEqual, stack);
      }
      function baseIsEqualDeep(object, other, bitmask, customizer, equalFunc, stack) {
        var objIsArr = isArray(object), othIsArr = isArray(other), objTag = objIsArr ? arrayTag : getTag(object), othTag = othIsArr ? arrayTag : getTag(other);
        objTag = objTag == argsTag ? objectTag : objTag;
        othTag = othTag == argsTag ? objectTag : othTag;
        var objIsObj = objTag == objectTag, othIsObj = othTag == objectTag, isSameTag = objTag == othTag;
        if (isSameTag && isBuffer(object)) {
          if (!isBuffer(other)) {
            return false;
          }
          objIsArr = true;
          objIsObj = false;
        }
        if (isSameTag && !objIsObj) {
          stack || (stack = new Stack());
          return objIsArr || isTypedArray(object) ? equalArrays(object, other, bitmask, customizer, equalFunc, stack) : equalByTag(object, other, objTag, bitmask, customizer, equalFunc, stack);
        }
        if (!(bitmask & COMPARE_PARTIAL_FLAG)) {
          var objIsWrapped = objIsObj && hasOwnProperty.call(object, "__wrapped__"), othIsWrapped = othIsObj && hasOwnProperty.call(other, "__wrapped__");
          if (objIsWrapped || othIsWrapped) {
            var objUnwrapped = objIsWrapped ? object.value() : object, othUnwrapped = othIsWrapped ? other.value() : other;
            stack || (stack = new Stack());
            return equalFunc(objUnwrapped, othUnwrapped, bitmask, customizer, stack);
          }
        }
        if (!isSameTag) {
          return false;
        }
        stack || (stack = new Stack());
        return equalObjects(object, other, bitmask, customizer, equalFunc, stack);
      }
      function baseIsMap(value) {
        return isObjectLike(value) && getTag(value) == mapTag;
      }
      function baseIsMatch(object, source, matchData, customizer) {
        var index = matchData.length, length = index, noCustomizer = !customizer;
        if (object == null) {
          return !length;
        }
        object = Object2(object);
        while (index--) {
          var data = matchData[index];
          if (noCustomizer && data[2] ? data[1] !== object[data[0]] : !(data[0] in object)) {
            return false;
          }
        }
        while (++index < length) {
          data = matchData[index];
          var key = data[0], objValue = object[key], srcValue = data[1];
          if (noCustomizer && data[2]) {
            if (objValue === undefined$1 && !(key in object)) {
              return false;
            }
          } else {
            var stack = new Stack();
            if (customizer) {
              var result2 = customizer(objValue, srcValue, key, object, source, stack);
            }
            if (!(result2 === undefined$1 ? baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG, customizer, stack) : result2)) {
              return false;
            }
          }
        }
        return true;
      }
      function baseIsNative(value) {
        if (!isObject(value) || isMasked(value)) {
          return false;
        }
        var pattern = isFunction(value) ? reIsNative : reIsHostCtor;
        return pattern.test(toSource(value));
      }
      function baseIsRegExp(value) {
        return isObjectLike(value) && baseGetTag(value) == regexpTag;
      }
      function baseIsSet(value) {
        return isObjectLike(value) && getTag(value) == setTag;
      }
      function baseIsTypedArray(value) {
        return isObjectLike(value) && isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
      }
      function baseIteratee(value) {
        if (typeof value == "function") {
          return value;
        }
        if (value == null) {
          return identity;
        }
        if (typeof value == "object") {
          return isArray(value) ? baseMatchesProperty(value[0], value[1]) : baseMatches(value);
        }
        return property(value);
      }
      function baseKeys(object) {
        if (!isPrototype(object)) {
          return nativeKeys(object);
        }
        var result2 = [];
        for (var key in Object2(object)) {
          if (hasOwnProperty.call(object, key) && key != "constructor") {
            result2.push(key);
          }
        }
        return result2;
      }
      function baseKeysIn(object) {
        if (!isObject(object)) {
          return nativeKeysIn(object);
        }
        var isProto = isPrototype(object), result2 = [];
        for (var key in object) {
          if (!(key == "constructor" && (isProto || !hasOwnProperty.call(object, key)))) {
            result2.push(key);
          }
        }
        return result2;
      }
      function baseLt(value, other) {
        return value < other;
      }
      function baseMap(collection, iteratee2) {
        var index = -1, result2 = isArrayLike(collection) ? Array2(collection.length) : [];
        baseEach(collection, function(value, key, collection2) {
          result2[++index] = iteratee2(value, key, collection2);
        });
        return result2;
      }
      function baseMatches(source) {
        var matchData = getMatchData(source);
        if (matchData.length == 1 && matchData[0][2]) {
          return matchesStrictComparable(matchData[0][0], matchData[0][1]);
        }
        return function(object) {
          return object === source || baseIsMatch(object, source, matchData);
        };
      }
      function baseMatchesProperty(path, srcValue) {
        if (isKey(path) && isStrictComparable(srcValue)) {
          return matchesStrictComparable(toKey(path), srcValue);
        }
        return function(object) {
          var objValue = get(object, path);
          return objValue === undefined$1 && objValue === srcValue ? hasIn(object, path) : baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG);
        };
      }
      function baseMerge(object, source, srcIndex, customizer, stack) {
        if (object === source) {
          return;
        }
        baseFor(source, function(srcValue, key) {
          stack || (stack = new Stack());
          if (isObject(srcValue)) {
            baseMergeDeep(object, source, key, srcIndex, baseMerge, customizer, stack);
          } else {
            var newValue = customizer ? customizer(safeGet(object, key), srcValue, key + "", object, source, stack) : undefined$1;
            if (newValue === undefined$1) {
              newValue = srcValue;
            }
            assignMergeValue(object, key, newValue);
          }
        }, keysIn);
      }
      function baseMergeDeep(object, source, key, srcIndex, mergeFunc, customizer, stack) {
        var objValue = safeGet(object, key), srcValue = safeGet(source, key), stacked = stack.get(srcValue);
        if (stacked) {
          assignMergeValue(object, key, stacked);
          return;
        }
        var newValue = customizer ? customizer(objValue, srcValue, key + "", object, source, stack) : undefined$1;
        var isCommon = newValue === undefined$1;
        if (isCommon) {
          var isArr = isArray(srcValue), isBuff = !isArr && isBuffer(srcValue), isTyped = !isArr && !isBuff && isTypedArray(srcValue);
          newValue = srcValue;
          if (isArr || isBuff || isTyped) {
            if (isArray(objValue)) {
              newValue = objValue;
            } else if (isArrayLikeObject(objValue)) {
              newValue = copyArray(objValue);
            } else if (isBuff) {
              isCommon = false;
              newValue = cloneBuffer(srcValue, true);
            } else if (isTyped) {
              isCommon = false;
              newValue = cloneTypedArray(srcValue, true);
            } else {
              newValue = [];
            }
          } else if (isPlainObject(srcValue) || isArguments(srcValue)) {
            newValue = objValue;
            if (isArguments(objValue)) {
              newValue = toPlainObject(objValue);
            } else if (!isObject(objValue) || isFunction(objValue)) {
              newValue = initCloneObject(srcValue);
            }
          } else {
            isCommon = false;
          }
        }
        if (isCommon) {
          stack.set(srcValue, newValue);
          mergeFunc(newValue, srcValue, srcIndex, customizer, stack);
          stack["delete"](srcValue);
        }
        assignMergeValue(object, key, newValue);
      }
      function baseNth(array, n) {
        var length = array.length;
        if (!length) {
          return;
        }
        n += n < 0 ? length : 0;
        return isIndex(n, length) ? array[n] : undefined$1;
      }
      function baseOrderBy(collection, iteratees, orders) {
        if (iteratees.length) {
          iteratees = arrayMap(iteratees, function(iteratee2) {
            if (isArray(iteratee2)) {
              return function(value) {
                return baseGet(value, iteratee2.length === 1 ? iteratee2[0] : iteratee2);
              };
            }
            return iteratee2;
          });
        } else {
          iteratees = [identity];
        }
        var index = -1;
        iteratees = arrayMap(iteratees, baseUnary(getIteratee()));
        var result2 = baseMap(collection, function(value, key, collection2) {
          var criteria = arrayMap(iteratees, function(iteratee2) {
            return iteratee2(value);
          });
          return { "criteria": criteria, "index": ++index, "value": value };
        });
        return baseSortBy(result2, function(object, other) {
          return compareMultiple(object, other, orders);
        });
      }
      function basePick(object, paths) {
        return basePickBy(object, paths, function(value, path) {
          return hasIn(object, path);
        });
      }
      function basePickBy(object, paths, predicate) {
        var index = -1, length = paths.length, result2 = {};
        while (++index < length) {
          var path = paths[index], value = baseGet(object, path);
          if (predicate(value, path)) {
            baseSet(result2, castPath(path, object), value);
          }
        }
        return result2;
      }
      function basePropertyDeep(path) {
        return function(object) {
          return baseGet(object, path);
        };
      }
      function basePullAll(array, values2, iteratee2, comparator) {
        var indexOf2 = comparator ? baseIndexOfWith : baseIndexOf, index = -1, length = values2.length, seen = array;
        if (array === values2) {
          values2 = copyArray(values2);
        }
        if (iteratee2) {
          seen = arrayMap(array, baseUnary(iteratee2));
        }
        while (++index < length) {
          var fromIndex = 0, value = values2[index], computed2 = iteratee2 ? iteratee2(value) : value;
          while ((fromIndex = indexOf2(seen, computed2, fromIndex, comparator)) > -1) {
            if (seen !== array) {
              splice.call(seen, fromIndex, 1);
            }
            splice.call(array, fromIndex, 1);
          }
        }
        return array;
      }
      function basePullAt(array, indexes) {
        var length = array ? indexes.length : 0, lastIndex = length - 1;
        while (length--) {
          var index = indexes[length];
          if (length == lastIndex || index !== previous) {
            var previous = index;
            if (isIndex(index)) {
              splice.call(array, index, 1);
            } else {
              baseUnset(array, index);
            }
          }
        }
        return array;
      }
      function baseRandom(lower, upper) {
        return lower + nativeFloor(nativeRandom() * (upper - lower + 1));
      }
      function baseRange(start, end, step, fromRight) {
        var index = -1, length = nativeMax(nativeCeil((end - start) / (step || 1)), 0), result2 = Array2(length);
        while (length--) {
          result2[fromRight ? length : ++index] = start;
          start += step;
        }
        return result2;
      }
      function baseRepeat(string, n) {
        var result2 = "";
        if (!string || n < 1 || n > MAX_SAFE_INTEGER) {
          return result2;
        }
        do {
          if (n % 2) {
            result2 += string;
          }
          n = nativeFloor(n / 2);
          if (n) {
            string += string;
          }
        } while (n);
        return result2;
      }
      function baseRest(func, start) {
        return setToString(overRest(func, start, identity), func + "");
      }
      function baseSample(collection) {
        return arraySample(values(collection));
      }
      function baseSampleSize(collection, n) {
        var array = values(collection);
        return shuffleSelf(array, baseClamp(n, 0, array.length));
      }
      function baseSet(object, path, value, customizer) {
        if (!isObject(object)) {
          return object;
        }
        path = castPath(path, object);
        var index = -1, length = path.length, lastIndex = length - 1, nested = object;
        while (nested != null && ++index < length) {
          var key = toKey(path[index]), newValue = value;
          if (key === "__proto__" || key === "constructor" || key === "prototype") {
            return object;
          }
          if (index != lastIndex) {
            var objValue = nested[key];
            newValue = customizer ? customizer(objValue, key, nested) : undefined$1;
            if (newValue === undefined$1) {
              newValue = isObject(objValue) ? objValue : isIndex(path[index + 1]) ? [] : {};
            }
          }
          assignValue(nested, key, newValue);
          nested = nested[key];
        }
        return object;
      }
      var baseSetData = !metaMap ? identity : function(func, data) {
        metaMap.set(func, data);
        return func;
      };
      var baseSetToString = !defineProperty ? identity : function(func, string) {
        return defineProperty(func, "toString", {
          "configurable": true,
          "enumerable": false,
          "value": constant(string),
          "writable": true
        });
      };
      function baseShuffle(collection) {
        return shuffleSelf(values(collection));
      }
      function baseSlice(array, start, end) {
        var index = -1, length = array.length;
        if (start < 0) {
          start = -start > length ? 0 : length + start;
        }
        end = end > length ? length : end;
        if (end < 0) {
          end += length;
        }
        length = start > end ? 0 : end - start >>> 0;
        start >>>= 0;
        var result2 = Array2(length);
        while (++index < length) {
          result2[index] = array[index + start];
        }
        return result2;
      }
      function baseSome(collection, predicate) {
        var result2;
        baseEach(collection, function(value, index, collection2) {
          result2 = predicate(value, index, collection2);
          return !result2;
        });
        return !!result2;
      }
      function baseSortedIndex(array, value, retHighest) {
        var low = 0, high = array == null ? low : array.length;
        if (typeof value == "number" && value === value && high <= HALF_MAX_ARRAY_LENGTH) {
          while (low < high) {
            var mid = low + high >>> 1, computed2 = array[mid];
            if (computed2 !== null && !isSymbol(computed2) && (retHighest ? computed2 <= value : computed2 < value)) {
              low = mid + 1;
            } else {
              high = mid;
            }
          }
          return high;
        }
        return baseSortedIndexBy(array, value, identity, retHighest);
      }
      function baseSortedIndexBy(array, value, iteratee2, retHighest) {
        var low = 0, high = array == null ? 0 : array.length;
        if (high === 0) {
          return 0;
        }
        value = iteratee2(value);
        var valIsNaN = value !== value, valIsNull = value === null, valIsSymbol = isSymbol(value), valIsUndefined = value === undefined$1;
        while (low < high) {
          var mid = nativeFloor((low + high) / 2), computed2 = iteratee2(array[mid]), othIsDefined = computed2 !== undefined$1, othIsNull = computed2 === null, othIsReflexive = computed2 === computed2, othIsSymbol = isSymbol(computed2);
          if (valIsNaN) {
            var setLow = retHighest || othIsReflexive;
          } else if (valIsUndefined) {
            setLow = othIsReflexive && (retHighest || othIsDefined);
          } else if (valIsNull) {
            setLow = othIsReflexive && othIsDefined && (retHighest || !othIsNull);
          } else if (valIsSymbol) {
            setLow = othIsReflexive && othIsDefined && !othIsNull && (retHighest || !othIsSymbol);
          } else if (othIsNull || othIsSymbol) {
            setLow = false;
          } else {
            setLow = retHighest ? computed2 <= value : computed2 < value;
          }
          if (setLow) {
            low = mid + 1;
          } else {
            high = mid;
          }
        }
        return nativeMin(high, MAX_ARRAY_INDEX);
      }
      function baseSortedUniq(array, iteratee2) {
        var index = -1, length = array.length, resIndex = 0, result2 = [];
        while (++index < length) {
          var value = array[index], computed2 = iteratee2 ? iteratee2(value) : value;
          if (!index || !eq(computed2, seen)) {
            var seen = computed2;
            result2[resIndex++] = value === 0 ? 0 : value;
          }
        }
        return result2;
      }
      function baseToNumber(value) {
        if (typeof value == "number") {
          return value;
        }
        if (isSymbol(value)) {
          return NAN;
        }
        return +value;
      }
      function baseToString(value) {
        if (typeof value == "string") {
          return value;
        }
        if (isArray(value)) {
          return arrayMap(value, baseToString) + "";
        }
        if (isSymbol(value)) {
          return symbolToString ? symbolToString.call(value) : "";
        }
        var result2 = value + "";
        return result2 == "0" && 1 / value == -INFINITY ? "-0" : result2;
      }
      function baseUniq(array, iteratee2, comparator) {
        var index = -1, includes2 = arrayIncludes, length = array.length, isCommon = true, result2 = [], seen = result2;
        if (comparator) {
          isCommon = false;
          includes2 = arrayIncludesWith;
        } else if (length >= LARGE_ARRAY_SIZE) {
          var set2 = iteratee2 ? null : createSet(array);
          if (set2) {
            return setToArray(set2);
          }
          isCommon = false;
          includes2 = cacheHas;
          seen = new SetCache();
        } else {
          seen = iteratee2 ? [] : result2;
        }
        outer:
          while (++index < length) {
            var value = array[index], computed2 = iteratee2 ? iteratee2(value) : value;
            value = comparator || value !== 0 ? value : 0;
            if (isCommon && computed2 === computed2) {
              var seenIndex = seen.length;
              while (seenIndex--) {
                if (seen[seenIndex] === computed2) {
                  continue outer;
                }
              }
              if (iteratee2) {
                seen.push(computed2);
              }
              result2.push(value);
            } else if (!includes2(seen, computed2, comparator)) {
              if (seen !== result2) {
                seen.push(computed2);
              }
              result2.push(value);
            }
          }
        return result2;
      }
      function baseUnset(object, path) {
        path = castPath(path, object);
        object = parent(object, path);
        return object == null || delete object[toKey(last(path))];
      }
      function baseUpdate(object, path, updater, customizer) {
        return baseSet(object, path, updater(baseGet(object, path)), customizer);
      }
      function baseWhile(array, predicate, isDrop, fromRight) {
        var length = array.length, index = fromRight ? length : -1;
        while ((fromRight ? index-- : ++index < length) && predicate(array[index], index, array)) {
        }
        return isDrop ? baseSlice(array, fromRight ? 0 : index, fromRight ? index + 1 : length) : baseSlice(array, fromRight ? index + 1 : 0, fromRight ? length : index);
      }
      function baseWrapperValue(value, actions) {
        var result2 = value;
        if (result2 instanceof LazyWrapper) {
          result2 = result2.value();
        }
        return arrayReduce(actions, function(result3, action) {
          return action.func.apply(action.thisArg, arrayPush([result3], action.args));
        }, result2);
      }
      function baseXor(arrays, iteratee2, comparator) {
        var length = arrays.length;
        if (length < 2) {
          return length ? baseUniq(arrays[0]) : [];
        }
        var index = -1, result2 = Array2(length);
        while (++index < length) {
          var array = arrays[index], othIndex = -1;
          while (++othIndex < length) {
            if (othIndex != index) {
              result2[index] = baseDifference(result2[index] || array, arrays[othIndex], iteratee2, comparator);
            }
          }
        }
        return baseUniq(baseFlatten(result2, 1), iteratee2, comparator);
      }
      function baseZipObject(props, values2, assignFunc) {
        var index = -1, length = props.length, valsLength = values2.length, result2 = {};
        while (++index < length) {
          var value = index < valsLength ? values2[index] : undefined$1;
          assignFunc(result2, props[index], value);
        }
        return result2;
      }
      function castArrayLikeObject(value) {
        return isArrayLikeObject(value) ? value : [];
      }
      function castFunction(value) {
        return typeof value == "function" ? value : identity;
      }
      function castPath(value, object) {
        if (isArray(value)) {
          return value;
        }
        return isKey(value, object) ? [value] : stringToPath(toString(value));
      }
      var castRest = baseRest;
      function castSlice(array, start, end) {
        var length = array.length;
        end = end === undefined$1 ? length : end;
        return !start && end >= length ? array : baseSlice(array, start, end);
      }
      var clearTimeout2 = ctxClearTimeout || function(id) {
        return root.clearTimeout(id);
      };
      function cloneBuffer(buffer, isDeep) {
        if (isDeep) {
          return buffer.slice();
        }
        var length = buffer.length, result2 = allocUnsafe ? allocUnsafe(length) : new buffer.constructor(length);
        buffer.copy(result2);
        return result2;
      }
      function cloneArrayBuffer(arrayBuffer) {
        var result2 = new arrayBuffer.constructor(arrayBuffer.byteLength);
        new Uint8Array2(result2).set(new Uint8Array2(arrayBuffer));
        return result2;
      }
      function cloneDataView(dataView, isDeep) {
        var buffer = isDeep ? cloneArrayBuffer(dataView.buffer) : dataView.buffer;
        return new dataView.constructor(buffer, dataView.byteOffset, dataView.byteLength);
      }
      function cloneRegExp(regexp) {
        var result2 = new regexp.constructor(regexp.source, reFlags.exec(regexp));
        result2.lastIndex = regexp.lastIndex;
        return result2;
      }
      function cloneSymbol(symbol) {
        return symbolValueOf ? Object2(symbolValueOf.call(symbol)) : {};
      }
      function cloneTypedArray(typedArray, isDeep) {
        var buffer = isDeep ? cloneArrayBuffer(typedArray.buffer) : typedArray.buffer;
        return new typedArray.constructor(buffer, typedArray.byteOffset, typedArray.length);
      }
      function compareAscending(value, other) {
        if (value !== other) {
          var valIsDefined = value !== undefined$1, valIsNull = value === null, valIsReflexive = value === value, valIsSymbol = isSymbol(value);
          var othIsDefined = other !== undefined$1, othIsNull = other === null, othIsReflexive = other === other, othIsSymbol = isSymbol(other);
          if (!othIsNull && !othIsSymbol && !valIsSymbol && value > other || valIsSymbol && othIsDefined && othIsReflexive && !othIsNull && !othIsSymbol || valIsNull && othIsDefined && othIsReflexive || !valIsDefined && othIsReflexive || !valIsReflexive) {
            return 1;
          }
          if (!valIsNull && !valIsSymbol && !othIsSymbol && value < other || othIsSymbol && valIsDefined && valIsReflexive && !valIsNull && !valIsSymbol || othIsNull && valIsDefined && valIsReflexive || !othIsDefined && valIsReflexive || !othIsReflexive) {
            return -1;
          }
        }
        return 0;
      }
      function compareMultiple(object, other, orders) {
        var index = -1, objCriteria = object.criteria, othCriteria = other.criteria, length = objCriteria.length, ordersLength = orders.length;
        while (++index < length) {
          var result2 = compareAscending(objCriteria[index], othCriteria[index]);
          if (result2) {
            if (index >= ordersLength) {
              return result2;
            }
            var order = orders[index];
            return result2 * (order == "desc" ? -1 : 1);
          }
        }
        return object.index - other.index;
      }
      function composeArgs(args, partials, holders, isCurried) {
        var argsIndex = -1, argsLength = args.length, holdersLength = holders.length, leftIndex = -1, leftLength = partials.length, rangeLength = nativeMax(argsLength - holdersLength, 0), result2 = Array2(leftLength + rangeLength), isUncurried = !isCurried;
        while (++leftIndex < leftLength) {
          result2[leftIndex] = partials[leftIndex];
        }
        while (++argsIndex < holdersLength) {
          if (isUncurried || argsIndex < argsLength) {
            result2[holders[argsIndex]] = args[argsIndex];
          }
        }
        while (rangeLength--) {
          result2[leftIndex++] = args[argsIndex++];
        }
        return result2;
      }
      function composeArgsRight(args, partials, holders, isCurried) {
        var argsIndex = -1, argsLength = args.length, holdersIndex = -1, holdersLength = holders.length, rightIndex = -1, rightLength = partials.length, rangeLength = nativeMax(argsLength - holdersLength, 0), result2 = Array2(rangeLength + rightLength), isUncurried = !isCurried;
        while (++argsIndex < rangeLength) {
          result2[argsIndex] = args[argsIndex];
        }
        var offset = argsIndex;
        while (++rightIndex < rightLength) {
          result2[offset + rightIndex] = partials[rightIndex];
        }
        while (++holdersIndex < holdersLength) {
          if (isUncurried || argsIndex < argsLength) {
            result2[offset + holders[holdersIndex]] = args[argsIndex++];
          }
        }
        return result2;
      }
      function copyArray(source, array) {
        var index = -1, length = source.length;
        array || (array = Array2(length));
        while (++index < length) {
          array[index] = source[index];
        }
        return array;
      }
      function copyObject(source, props, object, customizer) {
        var isNew = !object;
        object || (object = {});
        var index = -1, length = props.length;
        while (++index < length) {
          var key = props[index];
          var newValue = customizer ? customizer(object[key], source[key], key, object, source) : undefined$1;
          if (newValue === undefined$1) {
            newValue = source[key];
          }
          if (isNew) {
            baseAssignValue(object, key, newValue);
          } else {
            assignValue(object, key, newValue);
          }
        }
        return object;
      }
      function copySymbols(source, object) {
        return copyObject(source, getSymbols(source), object);
      }
      function copySymbolsIn(source, object) {
        return copyObject(source, getSymbolsIn(source), object);
      }
      function createAggregator(setter, initializer) {
        return function(collection, iteratee2) {
          var func = isArray(collection) ? arrayAggregator : baseAggregator, accumulator = initializer ? initializer() : {};
          return func(collection, setter, getIteratee(iteratee2, 2), accumulator);
        };
      }
      function createAssigner(assigner) {
        return baseRest(function(object, sources) {
          var index = -1, length = sources.length, customizer = length > 1 ? sources[length - 1] : undefined$1, guard = length > 2 ? sources[2] : undefined$1;
          customizer = assigner.length > 3 && typeof customizer == "function" ? (length--, customizer) : undefined$1;
          if (guard && isIterateeCall(sources[0], sources[1], guard)) {
            customizer = length < 3 ? undefined$1 : customizer;
            length = 1;
          }
          object = Object2(object);
          while (++index < length) {
            var source = sources[index];
            if (source) {
              assigner(object, source, index, customizer);
            }
          }
          return object;
        });
      }
      function createBaseEach(eachFunc, fromRight) {
        return function(collection, iteratee2) {
          if (collection == null) {
            return collection;
          }
          if (!isArrayLike(collection)) {
            return eachFunc(collection, iteratee2);
          }
          var length = collection.length, index = fromRight ? length : -1, iterable = Object2(collection);
          while (fromRight ? index-- : ++index < length) {
            if (iteratee2(iterable[index], index, iterable) === false) {
              break;
            }
          }
          return collection;
        };
      }
      function createBaseFor(fromRight) {
        return function(object, iteratee2, keysFunc) {
          var index = -1, iterable = Object2(object), props = keysFunc(object), length = props.length;
          while (length--) {
            var key = props[fromRight ? length : ++index];
            if (iteratee2(iterable[key], key, iterable) === false) {
              break;
            }
          }
          return object;
        };
      }
      function createBind(func, bitmask, thisArg) {
        var isBind = bitmask & WRAP_BIND_FLAG, Ctor = createCtor(func);
        function wrapper() {
          var fn = this && this !== root && this instanceof wrapper ? Ctor : func;
          return fn.apply(isBind ? thisArg : this, arguments);
        }
        return wrapper;
      }
      function createCaseFirst(methodName) {
        return function(string) {
          string = toString(string);
          var strSymbols = hasUnicode(string) ? stringToArray(string) : undefined$1;
          var chr = strSymbols ? strSymbols[0] : string.charAt(0);
          var trailing = strSymbols ? castSlice(strSymbols, 1).join("") : string.slice(1);
          return chr[methodName]() + trailing;
        };
      }
      function createCompounder(callback) {
        return function(string) {
          return arrayReduce(words(deburr(string).replace(reApos, "")), callback, "");
        };
      }
      function createCtor(Ctor) {
        return function() {
          var args = arguments;
          switch (args.length) {
            case 0:
              return new Ctor();
            case 1:
              return new Ctor(args[0]);
            case 2:
              return new Ctor(args[0], args[1]);
            case 3:
              return new Ctor(args[0], args[1], args[2]);
            case 4:
              return new Ctor(args[0], args[1], args[2], args[3]);
            case 5:
              return new Ctor(args[0], args[1], args[2], args[3], args[4]);
            case 6:
              return new Ctor(args[0], args[1], args[2], args[3], args[4], args[5]);
            case 7:
              return new Ctor(args[0], args[1], args[2], args[3], args[4], args[5], args[6]);
          }
          var thisBinding = baseCreate(Ctor.prototype), result2 = Ctor.apply(thisBinding, args);
          return isObject(result2) ? result2 : thisBinding;
        };
      }
      function createCurry(func, bitmask, arity) {
        var Ctor = createCtor(func);
        function wrapper() {
          var length = arguments.length, args = Array2(length), index = length, placeholder = getHolder(wrapper);
          while (index--) {
            args[index] = arguments[index];
          }
          var holders = length < 3 && args[0] !== placeholder && args[length - 1] !== placeholder ? [] : replaceHolders(args, placeholder);
          length -= holders.length;
          if (length < arity) {
            return createRecurry(func, bitmask, createHybrid, wrapper.placeholder, undefined$1, args, holders, undefined$1, undefined$1, arity - length);
          }
          var fn = this && this !== root && this instanceof wrapper ? Ctor : func;
          return apply(fn, this, args);
        }
        return wrapper;
      }
      function createFind(findIndexFunc) {
        return function(collection, predicate, fromIndex) {
          var iterable = Object2(collection);
          if (!isArrayLike(collection)) {
            var iteratee2 = getIteratee(predicate, 3);
            collection = keys(collection);
            predicate = function(key) {
              return iteratee2(iterable[key], key, iterable);
            };
          }
          var index = findIndexFunc(collection, predicate, fromIndex);
          return index > -1 ? iterable[iteratee2 ? collection[index] : index] : undefined$1;
        };
      }
      function createFlow(fromRight) {
        return flatRest(function(funcs) {
          var length = funcs.length, index = length, prereq = LodashWrapper.prototype.thru;
          if (fromRight) {
            funcs.reverse();
          }
          while (index--) {
            var func = funcs[index];
            if (typeof func != "function") {
              throw new TypeError2(FUNC_ERROR_TEXT);
            }
            if (prereq && !wrapper && getFuncName(func) == "wrapper") {
              var wrapper = new LodashWrapper([], true);
            }
          }
          index = wrapper ? index : length;
          while (++index < length) {
            func = funcs[index];
            var funcName = getFuncName(func), data = funcName == "wrapper" ? getData(func) : undefined$1;
            if (data && isLaziable(data[0]) && data[1] == (WRAP_ARY_FLAG | WRAP_CURRY_FLAG | WRAP_PARTIAL_FLAG | WRAP_REARG_FLAG) && !data[4].length && data[9] == 1) {
              wrapper = wrapper[getFuncName(data[0])].apply(wrapper, data[3]);
            } else {
              wrapper = func.length == 1 && isLaziable(func) ? wrapper[funcName]() : wrapper.thru(func);
            }
          }
          return function() {
            var args = arguments, value = args[0];
            if (wrapper && args.length == 1 && isArray(value)) {
              return wrapper.plant(value).value();
            }
            var index2 = 0, result2 = length ? funcs[index2].apply(this, args) : value;
            while (++index2 < length) {
              result2 = funcs[index2].call(this, result2);
            }
            return result2;
          };
        });
      }
      function createHybrid(func, bitmask, thisArg, partials, holders, partialsRight, holdersRight, argPos, ary2, arity) {
        var isAry = bitmask & WRAP_ARY_FLAG, isBind = bitmask & WRAP_BIND_FLAG, isBindKey = bitmask & WRAP_BIND_KEY_FLAG, isCurried = bitmask & (WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG), isFlip = bitmask & WRAP_FLIP_FLAG, Ctor = isBindKey ? undefined$1 : createCtor(func);
        function wrapper() {
          var length = arguments.length, args = Array2(length), index = length;
          while (index--) {
            args[index] = arguments[index];
          }
          if (isCurried) {
            var placeholder = getHolder(wrapper), holdersCount = countHolders(args, placeholder);
          }
          if (partials) {
            args = composeArgs(args, partials, holders, isCurried);
          }
          if (partialsRight) {
            args = composeArgsRight(args, partialsRight, holdersRight, isCurried);
          }
          length -= holdersCount;
          if (isCurried && length < arity) {
            var newHolders = replaceHolders(args, placeholder);
            return createRecurry(func, bitmask, createHybrid, wrapper.placeholder, thisArg, args, newHolders, argPos, ary2, arity - length);
          }
          var thisBinding = isBind ? thisArg : this, fn = isBindKey ? thisBinding[func] : func;
          length = args.length;
          if (argPos) {
            args = reorder(args, argPos);
          } else if (isFlip && length > 1) {
            args.reverse();
          }
          if (isAry && ary2 < length) {
            args.length = ary2;
          }
          if (this && this !== root && this instanceof wrapper) {
            fn = Ctor || createCtor(fn);
          }
          return fn.apply(thisBinding, args);
        }
        return wrapper;
      }
      function createInverter(setter, toIteratee) {
        return function(object, iteratee2) {
          return baseInverter(object, setter, toIteratee(iteratee2), {});
        };
      }
      function createMathOperation(operator, defaultValue) {
        return function(value, other) {
          var result2;
          if (value === undefined$1 && other === undefined$1) {
            return defaultValue;
          }
          if (value !== undefined$1) {
            result2 = value;
          }
          if (other !== undefined$1) {
            if (result2 === undefined$1) {
              return other;
            }
            if (typeof value == "string" || typeof other == "string") {
              value = baseToString(value);
              other = baseToString(other);
            } else {
              value = baseToNumber(value);
              other = baseToNumber(other);
            }
            result2 = operator(value, other);
          }
          return result2;
        };
      }
      function createOver(arrayFunc) {
        return flatRest(function(iteratees) {
          iteratees = arrayMap(iteratees, baseUnary(getIteratee()));
          return baseRest(function(args) {
            var thisArg = this;
            return arrayFunc(iteratees, function(iteratee2) {
              return apply(iteratee2, thisArg, args);
            });
          });
        });
      }
      function createPadding(length, chars) {
        chars = chars === undefined$1 ? " " : baseToString(chars);
        var charsLength = chars.length;
        if (charsLength < 2) {
          return charsLength ? baseRepeat(chars, length) : chars;
        }
        var result2 = baseRepeat(chars, nativeCeil(length / stringSize(chars)));
        return hasUnicode(chars) ? castSlice(stringToArray(result2), 0, length).join("") : result2.slice(0, length);
      }
      function createPartial(func, bitmask, thisArg, partials) {
        var isBind = bitmask & WRAP_BIND_FLAG, Ctor = createCtor(func);
        function wrapper() {
          var argsIndex = -1, argsLength = arguments.length, leftIndex = -1, leftLength = partials.length, args = Array2(leftLength + argsLength), fn = this && this !== root && this instanceof wrapper ? Ctor : func;
          while (++leftIndex < leftLength) {
            args[leftIndex] = partials[leftIndex];
          }
          while (argsLength--) {
            args[leftIndex++] = arguments[++argsIndex];
          }
          return apply(fn, isBind ? thisArg : this, args);
        }
        return wrapper;
      }
      function createRange(fromRight) {
        return function(start, end, step) {
          if (step && typeof step != "number" && isIterateeCall(start, end, step)) {
            end = step = undefined$1;
          }
          start = toFinite(start);
          if (end === undefined$1) {
            end = start;
            start = 0;
          } else {
            end = toFinite(end);
          }
          step = step === undefined$1 ? start < end ? 1 : -1 : toFinite(step);
          return baseRange(start, end, step, fromRight);
        };
      }
      function createRelationalOperation(operator) {
        return function(value, other) {
          if (!(typeof value == "string" && typeof other == "string")) {
            value = toNumber(value);
            other = toNumber(other);
          }
          return operator(value, other);
        };
      }
      function createRecurry(func, bitmask, wrapFunc, placeholder, thisArg, partials, holders, argPos, ary2, arity) {
        var isCurry = bitmask & WRAP_CURRY_FLAG, newHolders = isCurry ? holders : undefined$1, newHoldersRight = isCurry ? undefined$1 : holders, newPartials = isCurry ? partials : undefined$1, newPartialsRight = isCurry ? undefined$1 : partials;
        bitmask |= isCurry ? WRAP_PARTIAL_FLAG : WRAP_PARTIAL_RIGHT_FLAG;
        bitmask &= ~(isCurry ? WRAP_PARTIAL_RIGHT_FLAG : WRAP_PARTIAL_FLAG);
        if (!(bitmask & WRAP_CURRY_BOUND_FLAG)) {
          bitmask &= ~(WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG);
        }
        var newData = [
          func,
          bitmask,
          thisArg,
          newPartials,
          newHolders,
          newPartialsRight,
          newHoldersRight,
          argPos,
          ary2,
          arity
        ];
        var result2 = wrapFunc.apply(undefined$1, newData);
        if (isLaziable(func)) {
          setData(result2, newData);
        }
        result2.placeholder = placeholder;
        return setWrapToString(result2, func, bitmask);
      }
      function createRound(methodName) {
        var func = Math2[methodName];
        return function(number, precision) {
          number = toNumber(number);
          precision = precision == null ? 0 : nativeMin(toInteger(precision), 292);
          if (precision && nativeIsFinite(number)) {
            var pair = (toString(number) + "e").split("e"), value = func(pair[0] + "e" + (+pair[1] + precision));
            pair = (toString(value) + "e").split("e");
            return +(pair[0] + "e" + (+pair[1] - precision));
          }
          return func(number);
        };
      }
      var createSet = !(Set2 && 1 / setToArray(new Set2([, -0]))[1] == INFINITY) ? noop : function(values2) {
        return new Set2(values2);
      };
      function createToPairs(keysFunc) {
        return function(object) {
          var tag = getTag(object);
          if (tag == mapTag) {
            return mapToArray(object);
          }
          if (tag == setTag) {
            return setToPairs(object);
          }
          return baseToPairs(object, keysFunc(object));
        };
      }
      function createWrap(func, bitmask, thisArg, partials, holders, argPos, ary2, arity) {
        var isBindKey = bitmask & WRAP_BIND_KEY_FLAG;
        if (!isBindKey && typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        var length = partials ? partials.length : 0;
        if (!length) {
          bitmask &= ~(WRAP_PARTIAL_FLAG | WRAP_PARTIAL_RIGHT_FLAG);
          partials = holders = undefined$1;
        }
        ary2 = ary2 === undefined$1 ? ary2 : nativeMax(toInteger(ary2), 0);
        arity = arity === undefined$1 ? arity : toInteger(arity);
        length -= holders ? holders.length : 0;
        if (bitmask & WRAP_PARTIAL_RIGHT_FLAG) {
          var partialsRight = partials, holdersRight = holders;
          partials = holders = undefined$1;
        }
        var data = isBindKey ? undefined$1 : getData(func);
        var newData = [
          func,
          bitmask,
          thisArg,
          partials,
          holders,
          partialsRight,
          holdersRight,
          argPos,
          ary2,
          arity
        ];
        if (data) {
          mergeData(newData, data);
        }
        func = newData[0];
        bitmask = newData[1];
        thisArg = newData[2];
        partials = newData[3];
        holders = newData[4];
        arity = newData[9] = newData[9] === undefined$1 ? isBindKey ? 0 : func.length : nativeMax(newData[9] - length, 0);
        if (!arity && bitmask & (WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG)) {
          bitmask &= ~(WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG);
        }
        if (!bitmask || bitmask == WRAP_BIND_FLAG) {
          var result2 = createBind(func, bitmask, thisArg);
        } else if (bitmask == WRAP_CURRY_FLAG || bitmask == WRAP_CURRY_RIGHT_FLAG) {
          result2 = createCurry(func, bitmask, arity);
        } else if ((bitmask == WRAP_PARTIAL_FLAG || bitmask == (WRAP_BIND_FLAG | WRAP_PARTIAL_FLAG)) && !holders.length) {
          result2 = createPartial(func, bitmask, thisArg, partials);
        } else {
          result2 = createHybrid.apply(undefined$1, newData);
        }
        var setter = data ? baseSetData : setData;
        return setWrapToString(setter(result2, newData), func, bitmask);
      }
      function customDefaultsAssignIn(objValue, srcValue, key, object) {
        if (objValue === undefined$1 || eq(objValue, objectProto[key]) && !hasOwnProperty.call(object, key)) {
          return srcValue;
        }
        return objValue;
      }
      function customDefaultsMerge(objValue, srcValue, key, object, source, stack) {
        if (isObject(objValue) && isObject(srcValue)) {
          stack.set(srcValue, objValue);
          baseMerge(objValue, srcValue, undefined$1, customDefaultsMerge, stack);
          stack["delete"](srcValue);
        }
        return objValue;
      }
      function customOmitClone(value) {
        return isPlainObject(value) ? undefined$1 : value;
      }
      function equalArrays(array, other, bitmask, customizer, equalFunc, stack) {
        var isPartial = bitmask & COMPARE_PARTIAL_FLAG, arrLength = array.length, othLength = other.length;
        if (arrLength != othLength && !(isPartial && othLength > arrLength)) {
          return false;
        }
        var arrStacked = stack.get(array);
        var othStacked = stack.get(other);
        if (arrStacked && othStacked) {
          return arrStacked == other && othStacked == array;
        }
        var index = -1, result2 = true, seen = bitmask & COMPARE_UNORDERED_FLAG ? new SetCache() : undefined$1;
        stack.set(array, other);
        stack.set(other, array);
        while (++index < arrLength) {
          var arrValue = array[index], othValue = other[index];
          if (customizer) {
            var compared = isPartial ? customizer(othValue, arrValue, index, other, array, stack) : customizer(arrValue, othValue, index, array, other, stack);
          }
          if (compared !== undefined$1) {
            if (compared) {
              continue;
            }
            result2 = false;
            break;
          }
          if (seen) {
            if (!arraySome(other, function(othValue2, othIndex) {
              if (!cacheHas(seen, othIndex) && (arrValue === othValue2 || equalFunc(arrValue, othValue2, bitmask, customizer, stack))) {
                return seen.push(othIndex);
              }
            })) {
              result2 = false;
              break;
            }
          } else if (!(arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) {
            result2 = false;
            break;
          }
        }
        stack["delete"](array);
        stack["delete"](other);
        return result2;
      }
      function equalByTag(object, other, tag, bitmask, customizer, equalFunc, stack) {
        switch (tag) {
          case dataViewTag:
            if (object.byteLength != other.byteLength || object.byteOffset != other.byteOffset) {
              return false;
            }
            object = object.buffer;
            other = other.buffer;
          case arrayBufferTag:
            if (object.byteLength != other.byteLength || !equalFunc(new Uint8Array2(object), new Uint8Array2(other))) {
              return false;
            }
            return true;
          case boolTag:
          case dateTag:
          case numberTag:
            return eq(+object, +other);
          case errorTag:
            return object.name == other.name && object.message == other.message;
          case regexpTag:
          case stringTag:
            return object == other + "";
          case mapTag:
            var convert = mapToArray;
          case setTag:
            var isPartial = bitmask & COMPARE_PARTIAL_FLAG;
            convert || (convert = setToArray);
            if (object.size != other.size && !isPartial) {
              return false;
            }
            var stacked = stack.get(object);
            if (stacked) {
              return stacked == other;
            }
            bitmask |= COMPARE_UNORDERED_FLAG;
            stack.set(object, other);
            var result2 = equalArrays(convert(object), convert(other), bitmask, customizer, equalFunc, stack);
            stack["delete"](object);
            return result2;
          case symbolTag:
            if (symbolValueOf) {
              return symbolValueOf.call(object) == symbolValueOf.call(other);
            }
        }
        return false;
      }
      function equalObjects(object, other, bitmask, customizer, equalFunc, stack) {
        var isPartial = bitmask & COMPARE_PARTIAL_FLAG, objProps = getAllKeys(object), objLength = objProps.length, othProps = getAllKeys(other), othLength = othProps.length;
        if (objLength != othLength && !isPartial) {
          return false;
        }
        var index = objLength;
        while (index--) {
          var key = objProps[index];
          if (!(isPartial ? key in other : hasOwnProperty.call(other, key))) {
            return false;
          }
        }
        var objStacked = stack.get(object);
        var othStacked = stack.get(other);
        if (objStacked && othStacked) {
          return objStacked == other && othStacked == object;
        }
        var result2 = true;
        stack.set(object, other);
        stack.set(other, object);
        var skipCtor = isPartial;
        while (++index < objLength) {
          key = objProps[index];
          var objValue = object[key], othValue = other[key];
          if (customizer) {
            var compared = isPartial ? customizer(othValue, objValue, key, other, object, stack) : customizer(objValue, othValue, key, object, other, stack);
          }
          if (!(compared === undefined$1 ? objValue === othValue || equalFunc(objValue, othValue, bitmask, customizer, stack) : compared)) {
            result2 = false;
            break;
          }
          skipCtor || (skipCtor = key == "constructor");
        }
        if (result2 && !skipCtor) {
          var objCtor = object.constructor, othCtor = other.constructor;
          if (objCtor != othCtor && ("constructor" in object && "constructor" in other) && !(typeof objCtor == "function" && objCtor instanceof objCtor && typeof othCtor == "function" && othCtor instanceof othCtor)) {
            result2 = false;
          }
        }
        stack["delete"](object);
        stack["delete"](other);
        return result2;
      }
      function flatRest(func) {
        return setToString(overRest(func, undefined$1, flatten), func + "");
      }
      function getAllKeys(object) {
        return baseGetAllKeys(object, keys, getSymbols);
      }
      function getAllKeysIn(object) {
        return baseGetAllKeys(object, keysIn, getSymbolsIn);
      }
      var getData = !metaMap ? noop : function(func) {
        return metaMap.get(func);
      };
      function getFuncName(func) {
        var result2 = func.name + "", array = realNames[result2], length = hasOwnProperty.call(realNames, result2) ? array.length : 0;
        while (length--) {
          var data = array[length], otherFunc = data.func;
          if (otherFunc == null || otherFunc == func) {
            return data.name;
          }
        }
        return result2;
      }
      function getHolder(func) {
        var object = hasOwnProperty.call(lodash2, "placeholder") ? lodash2 : func;
        return object.placeholder;
      }
      function getIteratee() {
        var result2 = lodash2.iteratee || iteratee;
        result2 = result2 === iteratee ? baseIteratee : result2;
        return arguments.length ? result2(arguments[0], arguments[1]) : result2;
      }
      function getMapData(map2, key) {
        var data = map2.__data__;
        return isKeyable(key) ? data[typeof key == "string" ? "string" : "hash"] : data.map;
      }
      function getMatchData(object) {
        var result2 = keys(object), length = result2.length;
        while (length--) {
          var key = result2[length], value = object[key];
          result2[length] = [key, value, isStrictComparable(value)];
        }
        return result2;
      }
      function getNative(object, key) {
        var value = getValue(object, key);
        return baseIsNative(value) ? value : undefined$1;
      }
      function getRawTag(value) {
        var isOwn = hasOwnProperty.call(value, symToStringTag), tag = value[symToStringTag];
        try {
          value[symToStringTag] = undefined$1;
          var unmasked = true;
        } catch (e) {
        }
        var result2 = nativeObjectToString.call(value);
        if (unmasked) {
          if (isOwn) {
            value[symToStringTag] = tag;
          } else {
            delete value[symToStringTag];
          }
        }
        return result2;
      }
      var getSymbols = !nativeGetSymbols ? stubArray : function(object) {
        if (object == null) {
          return [];
        }
        object = Object2(object);
        return arrayFilter(nativeGetSymbols(object), function(symbol) {
          return propertyIsEnumerable.call(object, symbol);
        });
      };
      var getSymbolsIn = !nativeGetSymbols ? stubArray : function(object) {
        var result2 = [];
        while (object) {
          arrayPush(result2, getSymbols(object));
          object = getPrototype(object);
        }
        return result2;
      };
      var getTag = baseGetTag;
      if (DataView && getTag(new DataView(new ArrayBuffer(1))) != dataViewTag || Map2 && getTag(new Map2()) != mapTag || Promise2 && getTag(Promise2.resolve()) != promiseTag || Set2 && getTag(new Set2()) != setTag || WeakMap && getTag(new WeakMap()) != weakMapTag) {
        getTag = function(value) {
          var result2 = baseGetTag(value), Ctor = result2 == objectTag ? value.constructor : undefined$1, ctorString = Ctor ? toSource(Ctor) : "";
          if (ctorString) {
            switch (ctorString) {
              case dataViewCtorString:
                return dataViewTag;
              case mapCtorString:
                return mapTag;
              case promiseCtorString:
                return promiseTag;
              case setCtorString:
                return setTag;
              case weakMapCtorString:
                return weakMapTag;
            }
          }
          return result2;
        };
      }
      function getView(start, end, transforms) {
        var index = -1, length = transforms.length;
        while (++index < length) {
          var data = transforms[index], size2 = data.size;
          switch (data.type) {
            case "drop":
              start += size2;
              break;
            case "dropRight":
              end -= size2;
              break;
            case "take":
              end = nativeMin(end, start + size2);
              break;
            case "takeRight":
              start = nativeMax(start, end - size2);
              break;
          }
        }
        return { "start": start, "end": end };
      }
      function getWrapDetails(source) {
        var match = source.match(reWrapDetails);
        return match ? match[1].split(reSplitDetails) : [];
      }
      function hasPath(object, path, hasFunc) {
        path = castPath(path, object);
        var index = -1, length = path.length, result2 = false;
        while (++index < length) {
          var key = toKey(path[index]);
          if (!(result2 = object != null && hasFunc(object, key))) {
            break;
          }
          object = object[key];
        }
        if (result2 || ++index != length) {
          return result2;
        }
        length = object == null ? 0 : object.length;
        return !!length && isLength(length) && isIndex(key, length) && (isArray(object) || isArguments(object));
      }
      function initCloneArray(array) {
        var length = array.length, result2 = new array.constructor(length);
        if (length && typeof array[0] == "string" && hasOwnProperty.call(array, "index")) {
          result2.index = array.index;
          result2.input = array.input;
        }
        return result2;
      }
      function initCloneObject(object) {
        return typeof object.constructor == "function" && !isPrototype(object) ? baseCreate(getPrototype(object)) : {};
      }
      function initCloneByTag(object, tag, isDeep) {
        var Ctor = object.constructor;
        switch (tag) {
          case arrayBufferTag:
            return cloneArrayBuffer(object);
          case boolTag:
          case dateTag:
            return new Ctor(+object);
          case dataViewTag:
            return cloneDataView(object, isDeep);
          case float32Tag:
          case float64Tag:
          case int8Tag:
          case int16Tag:
          case int32Tag:
          case uint8Tag:
          case uint8ClampedTag:
          case uint16Tag:
          case uint32Tag:
            return cloneTypedArray(object, isDeep);
          case mapTag:
            return new Ctor();
          case numberTag:
          case stringTag:
            return new Ctor(object);
          case regexpTag:
            return cloneRegExp(object);
          case setTag:
            return new Ctor();
          case symbolTag:
            return cloneSymbol(object);
        }
      }
      function insertWrapDetails(source, details) {
        var length = details.length;
        if (!length) {
          return source;
        }
        var lastIndex = length - 1;
        details[lastIndex] = (length > 1 ? "& " : "") + details[lastIndex];
        details = details.join(length > 2 ? ", " : " ");
        return source.replace(reWrapComment, "{\n/* [wrapped with " + details + "] */\n");
      }
      function isFlattenable(value) {
        return isArray(value) || isArguments(value) || !!(spreadableSymbol && value && value[spreadableSymbol]);
      }
      function isIndex(value, length) {
        var type = typeof value;
        length = length == null ? MAX_SAFE_INTEGER : length;
        return !!length && (type == "number" || type != "symbol" && reIsUint.test(value)) && (value > -1 && value % 1 == 0 && value < length);
      }
      function isIterateeCall(value, index, object) {
        if (!isObject(object)) {
          return false;
        }
        var type = typeof index;
        if (type == "number" ? isArrayLike(object) && isIndex(index, object.length) : type == "string" && index in object) {
          return eq(object[index], value);
        }
        return false;
      }
      function isKey(value, object) {
        if (isArray(value)) {
          return false;
        }
        var type = typeof value;
        if (type == "number" || type == "symbol" || type == "boolean" || value == null || isSymbol(value)) {
          return true;
        }
        return reIsPlainProp.test(value) || !reIsDeepProp.test(value) || object != null && value in Object2(object);
      }
      function isKeyable(value) {
        var type = typeof value;
        return type == "string" || type == "number" || type == "symbol" || type == "boolean" ? value !== "__proto__" : value === null;
      }
      function isLaziable(func) {
        var funcName = getFuncName(func), other = lodash2[funcName];
        if (typeof other != "function" || !(funcName in LazyWrapper.prototype)) {
          return false;
        }
        if (func === other) {
          return true;
        }
        var data = getData(other);
        return !!data && func === data[0];
      }
      function isMasked(func) {
        return !!maskSrcKey && maskSrcKey in func;
      }
      var isMaskable = coreJsData ? isFunction : stubFalse;
      function isPrototype(value) {
        var Ctor = value && value.constructor, proto = typeof Ctor == "function" && Ctor.prototype || objectProto;
        return value === proto;
      }
      function isStrictComparable(value) {
        return value === value && !isObject(value);
      }
      function matchesStrictComparable(key, srcValue) {
        return function(object) {
          if (object == null) {
            return false;
          }
          return object[key] === srcValue && (srcValue !== undefined$1 || key in Object2(object));
        };
      }
      function memoizeCapped(func) {
        var result2 = memoize(func, function(key) {
          if (cache.size === MAX_MEMOIZE_SIZE) {
            cache.clear();
          }
          return key;
        });
        var cache = result2.cache;
        return result2;
      }
      function mergeData(data, source) {
        var bitmask = data[1], srcBitmask = source[1], newBitmask = bitmask | srcBitmask, isCommon = newBitmask < (WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG | WRAP_ARY_FLAG);
        var isCombo = srcBitmask == WRAP_ARY_FLAG && bitmask == WRAP_CURRY_FLAG || srcBitmask == WRAP_ARY_FLAG && bitmask == WRAP_REARG_FLAG && data[7].length <= source[8] || srcBitmask == (WRAP_ARY_FLAG | WRAP_REARG_FLAG) && source[7].length <= source[8] && bitmask == WRAP_CURRY_FLAG;
        if (!(isCommon || isCombo)) {
          return data;
        }
        if (srcBitmask & WRAP_BIND_FLAG) {
          data[2] = source[2];
          newBitmask |= bitmask & WRAP_BIND_FLAG ? 0 : WRAP_CURRY_BOUND_FLAG;
        }
        var value = source[3];
        if (value) {
          var partials = data[3];
          data[3] = partials ? composeArgs(partials, value, source[4]) : value;
          data[4] = partials ? replaceHolders(data[3], PLACEHOLDER) : source[4];
        }
        value = source[5];
        if (value) {
          partials = data[5];
          data[5] = partials ? composeArgsRight(partials, value, source[6]) : value;
          data[6] = partials ? replaceHolders(data[5], PLACEHOLDER) : source[6];
        }
        value = source[7];
        if (value) {
          data[7] = value;
        }
        if (srcBitmask & WRAP_ARY_FLAG) {
          data[8] = data[8] == null ? source[8] : nativeMin(data[8], source[8]);
        }
        if (data[9] == null) {
          data[9] = source[9];
        }
        data[0] = source[0];
        data[1] = newBitmask;
        return data;
      }
      function nativeKeysIn(object) {
        var result2 = [];
        if (object != null) {
          for (var key in Object2(object)) {
            result2.push(key);
          }
        }
        return result2;
      }
      function objectToString(value) {
        return nativeObjectToString.call(value);
      }
      function overRest(func, start, transform2) {
        start = nativeMax(start === undefined$1 ? func.length - 1 : start, 0);
        return function() {
          var args = arguments, index = -1, length = nativeMax(args.length - start, 0), array = Array2(length);
          while (++index < length) {
            array[index] = args[start + index];
          }
          index = -1;
          var otherArgs = Array2(start + 1);
          while (++index < start) {
            otherArgs[index] = args[index];
          }
          otherArgs[start] = transform2(array);
          return apply(func, this, otherArgs);
        };
      }
      function parent(object, path) {
        return path.length < 2 ? object : baseGet(object, baseSlice(path, 0, -1));
      }
      function reorder(array, indexes) {
        var arrLength = array.length, length = nativeMin(indexes.length, arrLength), oldArray = copyArray(array);
        while (length--) {
          var index = indexes[length];
          array[length] = isIndex(index, arrLength) ? oldArray[index] : undefined$1;
        }
        return array;
      }
      function safeGet(object, key) {
        if (key === "constructor" && typeof object[key] === "function") {
          return;
        }
        if (key == "__proto__") {
          return;
        }
        return object[key];
      }
      var setData = shortOut(baseSetData);
      var setTimeout2 = ctxSetTimeout || function(func, wait) {
        return root.setTimeout(func, wait);
      };
      var setToString = shortOut(baseSetToString);
      function setWrapToString(wrapper, reference, bitmask) {
        var source = reference + "";
        return setToString(wrapper, insertWrapDetails(source, updateWrapDetails(getWrapDetails(source), bitmask)));
      }
      function shortOut(func) {
        var count = 0, lastCalled = 0;
        return function() {
          var stamp = nativeNow(), remaining = HOT_SPAN - (stamp - lastCalled);
          lastCalled = stamp;
          if (remaining > 0) {
            if (++count >= HOT_COUNT) {
              return arguments[0];
            }
          } else {
            count = 0;
          }
          return func.apply(undefined$1, arguments);
        };
      }
      function shuffleSelf(array, size2) {
        var index = -1, length = array.length, lastIndex = length - 1;
        size2 = size2 === undefined$1 ? length : size2;
        while (++index < size2) {
          var rand = baseRandom(index, lastIndex), value = array[rand];
          array[rand] = array[index];
          array[index] = value;
        }
        array.length = size2;
        return array;
      }
      var stringToPath = memoizeCapped(function(string) {
        var result2 = [];
        if (string.charCodeAt(0) === 46) {
          result2.push("");
        }
        string.replace(rePropName, function(match, number, quote, subString) {
          result2.push(quote ? subString.replace(reEscapeChar, "$1") : number || match);
        });
        return result2;
      });
      function toKey(value) {
        if (typeof value == "string" || isSymbol(value)) {
          return value;
        }
        var result2 = value + "";
        return result2 == "0" && 1 / value == -INFINITY ? "-0" : result2;
      }
      function toSource(func) {
        if (func != null) {
          try {
            return funcToString.call(func);
          } catch (e) {
          }
          try {
            return func + "";
          } catch (e) {
          }
        }
        return "";
      }
      function updateWrapDetails(details, bitmask) {
        arrayEach(wrapFlags, function(pair) {
          var value = "_." + pair[0];
          if (bitmask & pair[1] && !arrayIncludes(details, value)) {
            details.push(value);
          }
        });
        return details.sort();
      }
      function wrapperClone(wrapper) {
        if (wrapper instanceof LazyWrapper) {
          return wrapper.clone();
        }
        var result2 = new LodashWrapper(wrapper.__wrapped__, wrapper.__chain__);
        result2.__actions__ = copyArray(wrapper.__actions__);
        result2.__index__ = wrapper.__index__;
        result2.__values__ = wrapper.__values__;
        return result2;
      }
      function chunk(array, size2, guard) {
        if (guard ? isIterateeCall(array, size2, guard) : size2 === undefined$1) {
          size2 = 1;
        } else {
          size2 = nativeMax(toInteger(size2), 0);
        }
        var length = array == null ? 0 : array.length;
        if (!length || size2 < 1) {
          return [];
        }
        var index = 0, resIndex = 0, result2 = Array2(nativeCeil(length / size2));
        while (index < length) {
          result2[resIndex++] = baseSlice(array, index, index += size2);
        }
        return result2;
      }
      function compact(array) {
        var index = -1, length = array == null ? 0 : array.length, resIndex = 0, result2 = [];
        while (++index < length) {
          var value = array[index];
          if (value) {
            result2[resIndex++] = value;
          }
        }
        return result2;
      }
      function concat() {
        var length = arguments.length;
        if (!length) {
          return [];
        }
        var args = Array2(length - 1), array = arguments[0], index = length;
        while (index--) {
          args[index - 1] = arguments[index];
        }
        return arrayPush(isArray(array) ? copyArray(array) : [array], baseFlatten(args, 1));
      }
      var difference = baseRest(function(array, values2) {
        return isArrayLikeObject(array) ? baseDifference(array, baseFlatten(values2, 1, isArrayLikeObject, true)) : [];
      });
      var differenceBy = baseRest(function(array, values2) {
        var iteratee2 = last(values2);
        if (isArrayLikeObject(iteratee2)) {
          iteratee2 = undefined$1;
        }
        return isArrayLikeObject(array) ? baseDifference(array, baseFlatten(values2, 1, isArrayLikeObject, true), getIteratee(iteratee2, 2)) : [];
      });
      var differenceWith = baseRest(function(array, values2) {
        var comparator = last(values2);
        if (isArrayLikeObject(comparator)) {
          comparator = undefined$1;
        }
        return isArrayLikeObject(array) ? baseDifference(array, baseFlatten(values2, 1, isArrayLikeObject, true), undefined$1, comparator) : [];
      });
      function drop(array, n, guard) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        n = guard || n === undefined$1 ? 1 : toInteger(n);
        return baseSlice(array, n < 0 ? 0 : n, length);
      }
      function dropRight(array, n, guard) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        n = guard || n === undefined$1 ? 1 : toInteger(n);
        n = length - n;
        return baseSlice(array, 0, n < 0 ? 0 : n);
      }
      function dropRightWhile(array, predicate) {
        return array && array.length ? baseWhile(array, getIteratee(predicate, 3), true, true) : [];
      }
      function dropWhile(array, predicate) {
        return array && array.length ? baseWhile(array, getIteratee(predicate, 3), true) : [];
      }
      function fill(array, value, start, end) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        if (start && typeof start != "number" && isIterateeCall(array, value, start)) {
          start = 0;
          end = length;
        }
        return baseFill(array, value, start, end);
      }
      function findIndex(array, predicate, fromIndex) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return -1;
        }
        var index = fromIndex == null ? 0 : toInteger(fromIndex);
        if (index < 0) {
          index = nativeMax(length + index, 0);
        }
        return baseFindIndex(array, getIteratee(predicate, 3), index);
      }
      function findLastIndex(array, predicate, fromIndex) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return -1;
        }
        var index = length - 1;
        if (fromIndex !== undefined$1) {
          index = toInteger(fromIndex);
          index = fromIndex < 0 ? nativeMax(length + index, 0) : nativeMin(index, length - 1);
        }
        return baseFindIndex(array, getIteratee(predicate, 3), index, true);
      }
      function flatten(array) {
        var length = array == null ? 0 : array.length;
        return length ? baseFlatten(array, 1) : [];
      }
      function flattenDeep(array) {
        var length = array == null ? 0 : array.length;
        return length ? baseFlatten(array, INFINITY) : [];
      }
      function flattenDepth(array, depth) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        depth = depth === undefined$1 ? 1 : toInteger(depth);
        return baseFlatten(array, depth);
      }
      function fromPairs(pairs) {
        var index = -1, length = pairs == null ? 0 : pairs.length, result2 = {};
        while (++index < length) {
          var pair = pairs[index];
          result2[pair[0]] = pair[1];
        }
        return result2;
      }
      function head(array) {
        return array && array.length ? array[0] : undefined$1;
      }
      function indexOf(array, value, fromIndex) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return -1;
        }
        var index = fromIndex == null ? 0 : toInteger(fromIndex);
        if (index < 0) {
          index = nativeMax(length + index, 0);
        }
        return baseIndexOf(array, value, index);
      }
      function initial(array) {
        var length = array == null ? 0 : array.length;
        return length ? baseSlice(array, 0, -1) : [];
      }
      var intersection = baseRest(function(arrays) {
        var mapped = arrayMap(arrays, castArrayLikeObject);
        return mapped.length && mapped[0] === arrays[0] ? baseIntersection(mapped) : [];
      });
      var intersectionBy = baseRest(function(arrays) {
        var iteratee2 = last(arrays), mapped = arrayMap(arrays, castArrayLikeObject);
        if (iteratee2 === last(mapped)) {
          iteratee2 = undefined$1;
        } else {
          mapped.pop();
        }
        return mapped.length && mapped[0] === arrays[0] ? baseIntersection(mapped, getIteratee(iteratee2, 2)) : [];
      });
      var intersectionWith = baseRest(function(arrays) {
        var comparator = last(arrays), mapped = arrayMap(arrays, castArrayLikeObject);
        comparator = typeof comparator == "function" ? comparator : undefined$1;
        if (comparator) {
          mapped.pop();
        }
        return mapped.length && mapped[0] === arrays[0] ? baseIntersection(mapped, undefined$1, comparator) : [];
      });
      function join(array, separator) {
        return array == null ? "" : nativeJoin.call(array, separator);
      }
      function last(array) {
        var length = array == null ? 0 : array.length;
        return length ? array[length - 1] : undefined$1;
      }
      function lastIndexOf(array, value, fromIndex) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return -1;
        }
        var index = length;
        if (fromIndex !== undefined$1) {
          index = toInteger(fromIndex);
          index = index < 0 ? nativeMax(length + index, 0) : nativeMin(index, length - 1);
        }
        return value === value ? strictLastIndexOf(array, value, index) : baseFindIndex(array, baseIsNaN, index, true);
      }
      function nth(array, n) {
        return array && array.length ? baseNth(array, toInteger(n)) : undefined$1;
      }
      var pull = baseRest(pullAll);
      function pullAll(array, values2) {
        return array && array.length && values2 && values2.length ? basePullAll(array, values2) : array;
      }
      function pullAllBy(array, values2, iteratee2) {
        return array && array.length && values2 && values2.length ? basePullAll(array, values2, getIteratee(iteratee2, 2)) : array;
      }
      function pullAllWith(array, values2, comparator) {
        return array && array.length && values2 && values2.length ? basePullAll(array, values2, undefined$1, comparator) : array;
      }
      var pullAt = flatRest(function(array, indexes) {
        var length = array == null ? 0 : array.length, result2 = baseAt(array, indexes);
        basePullAt(array, arrayMap(indexes, function(index) {
          return isIndex(index, length) ? +index : index;
        }).sort(compareAscending));
        return result2;
      });
      function remove(array, predicate) {
        var result2 = [];
        if (!(array && array.length)) {
          return result2;
        }
        var index = -1, indexes = [], length = array.length;
        predicate = getIteratee(predicate, 3);
        while (++index < length) {
          var value = array[index];
          if (predicate(value, index, array)) {
            result2.push(value);
            indexes.push(index);
          }
        }
        basePullAt(array, indexes);
        return result2;
      }
      function reverse(array) {
        return array == null ? array : nativeReverse.call(array);
      }
      function slice(array, start, end) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        if (end && typeof end != "number" && isIterateeCall(array, start, end)) {
          start = 0;
          end = length;
        } else {
          start = start == null ? 0 : toInteger(start);
          end = end === undefined$1 ? length : toInteger(end);
        }
        return baseSlice(array, start, end);
      }
      function sortedIndex(array, value) {
        return baseSortedIndex(array, value);
      }
      function sortedIndexBy(array, value, iteratee2) {
        return baseSortedIndexBy(array, value, getIteratee(iteratee2, 2));
      }
      function sortedIndexOf(array, value) {
        var length = array == null ? 0 : array.length;
        if (length) {
          var index = baseSortedIndex(array, value);
          if (index < length && eq(array[index], value)) {
            return index;
          }
        }
        return -1;
      }
      function sortedLastIndex(array, value) {
        return baseSortedIndex(array, value, true);
      }
      function sortedLastIndexBy(array, value, iteratee2) {
        return baseSortedIndexBy(array, value, getIteratee(iteratee2, 2), true);
      }
      function sortedLastIndexOf(array, value) {
        var length = array == null ? 0 : array.length;
        if (length) {
          var index = baseSortedIndex(array, value, true) - 1;
          if (eq(array[index], value)) {
            return index;
          }
        }
        return -1;
      }
      function sortedUniq(array) {
        return array && array.length ? baseSortedUniq(array) : [];
      }
      function sortedUniqBy(array, iteratee2) {
        return array && array.length ? baseSortedUniq(array, getIteratee(iteratee2, 2)) : [];
      }
      function tail(array) {
        var length = array == null ? 0 : array.length;
        return length ? baseSlice(array, 1, length) : [];
      }
      function take(array, n, guard) {
        if (!(array && array.length)) {
          return [];
        }
        n = guard || n === undefined$1 ? 1 : toInteger(n);
        return baseSlice(array, 0, n < 0 ? 0 : n);
      }
      function takeRight(array, n, guard) {
        var length = array == null ? 0 : array.length;
        if (!length) {
          return [];
        }
        n = guard || n === undefined$1 ? 1 : toInteger(n);
        n = length - n;
        return baseSlice(array, n < 0 ? 0 : n, length);
      }
      function takeRightWhile(array, predicate) {
        return array && array.length ? baseWhile(array, getIteratee(predicate, 3), false, true) : [];
      }
      function takeWhile(array, predicate) {
        return array && array.length ? baseWhile(array, getIteratee(predicate, 3)) : [];
      }
      var union = baseRest(function(arrays) {
        return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true));
      });
      var unionBy = baseRest(function(arrays) {
        var iteratee2 = last(arrays);
        if (isArrayLikeObject(iteratee2)) {
          iteratee2 = undefined$1;
        }
        return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true), getIteratee(iteratee2, 2));
      });
      var unionWith = baseRest(function(arrays) {
        var comparator = last(arrays);
        comparator = typeof comparator == "function" ? comparator : undefined$1;
        return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true), undefined$1, comparator);
      });
      function uniq(array) {
        return array && array.length ? baseUniq(array) : [];
      }
      function uniqBy(array, iteratee2) {
        return array && array.length ? baseUniq(array, getIteratee(iteratee2, 2)) : [];
      }
      function uniqWith(array, comparator) {
        comparator = typeof comparator == "function" ? comparator : undefined$1;
        return array && array.length ? baseUniq(array, undefined$1, comparator) : [];
      }
      function unzip(array) {
        if (!(array && array.length)) {
          return [];
        }
        var length = 0;
        array = arrayFilter(array, function(group) {
          if (isArrayLikeObject(group)) {
            length = nativeMax(group.length, length);
            return true;
          }
        });
        return baseTimes(length, function(index) {
          return arrayMap(array, baseProperty(index));
        });
      }
      function unzipWith(array, iteratee2) {
        if (!(array && array.length)) {
          return [];
        }
        var result2 = unzip(array);
        if (iteratee2 == null) {
          return result2;
        }
        return arrayMap(result2, function(group) {
          return apply(iteratee2, undefined$1, group);
        });
      }
      var without = baseRest(function(array, values2) {
        return isArrayLikeObject(array) ? baseDifference(array, values2) : [];
      });
      var xor = baseRest(function(arrays) {
        return baseXor(arrayFilter(arrays, isArrayLikeObject));
      });
      var xorBy = baseRest(function(arrays) {
        var iteratee2 = last(arrays);
        if (isArrayLikeObject(iteratee2)) {
          iteratee2 = undefined$1;
        }
        return baseXor(arrayFilter(arrays, isArrayLikeObject), getIteratee(iteratee2, 2));
      });
      var xorWith = baseRest(function(arrays) {
        var comparator = last(arrays);
        comparator = typeof comparator == "function" ? comparator : undefined$1;
        return baseXor(arrayFilter(arrays, isArrayLikeObject), undefined$1, comparator);
      });
      var zip = baseRest(unzip);
      function zipObject(props, values2) {
        return baseZipObject(props || [], values2 || [], assignValue);
      }
      function zipObjectDeep(props, values2) {
        return baseZipObject(props || [], values2 || [], baseSet);
      }
      var zipWith = baseRest(function(arrays) {
        var length = arrays.length, iteratee2 = length > 1 ? arrays[length - 1] : undefined$1;
        iteratee2 = typeof iteratee2 == "function" ? (arrays.pop(), iteratee2) : undefined$1;
        return unzipWith(arrays, iteratee2);
      });
      function chain(value) {
        var result2 = lodash2(value);
        result2.__chain__ = true;
        return result2;
      }
      function tap(value, interceptor) {
        interceptor(value);
        return value;
      }
      function thru(value, interceptor) {
        return interceptor(value);
      }
      var wrapperAt = flatRest(function(paths) {
        var length = paths.length, start = length ? paths[0] : 0, value = this.__wrapped__, interceptor = function(object) {
          return baseAt(object, paths);
        };
        if (length > 1 || this.__actions__.length || !(value instanceof LazyWrapper) || !isIndex(start)) {
          return this.thru(interceptor);
        }
        value = value.slice(start, +start + (length ? 1 : 0));
        value.__actions__.push({
          "func": thru,
          "args": [interceptor],
          "thisArg": undefined$1
        });
        return new LodashWrapper(value, this.__chain__).thru(function(array) {
          if (length && !array.length) {
            array.push(undefined$1);
          }
          return array;
        });
      });
      function wrapperChain() {
        return chain(this);
      }
      function wrapperCommit() {
        return new LodashWrapper(this.value(), this.__chain__);
      }
      function wrapperNext() {
        if (this.__values__ === undefined$1) {
          this.__values__ = toArray(this.value());
        }
        var done = this.__index__ >= this.__values__.length, value = done ? undefined$1 : this.__values__[this.__index__++];
        return { "done": done, "value": value };
      }
      function wrapperToIterator() {
        return this;
      }
      function wrapperPlant(value) {
        var result2, parent2 = this;
        while (parent2 instanceof baseLodash) {
          var clone2 = wrapperClone(parent2);
          clone2.__index__ = 0;
          clone2.__values__ = undefined$1;
          if (result2) {
            previous.__wrapped__ = clone2;
          } else {
            result2 = clone2;
          }
          var previous = clone2;
          parent2 = parent2.__wrapped__;
        }
        previous.__wrapped__ = value;
        return result2;
      }
      function wrapperReverse() {
        var value = this.__wrapped__;
        if (value instanceof LazyWrapper) {
          var wrapped = value;
          if (this.__actions__.length) {
            wrapped = new LazyWrapper(this);
          }
          wrapped = wrapped.reverse();
          wrapped.__actions__.push({
            "func": thru,
            "args": [reverse],
            "thisArg": undefined$1
          });
          return new LodashWrapper(wrapped, this.__chain__);
        }
        return this.thru(reverse);
      }
      function wrapperValue() {
        return baseWrapperValue(this.__wrapped__, this.__actions__);
      }
      var countBy = createAggregator(function(result2, value, key) {
        if (hasOwnProperty.call(result2, key)) {
          ++result2[key];
        } else {
          baseAssignValue(result2, key, 1);
        }
      });
      function every(collection, predicate, guard) {
        var func = isArray(collection) ? arrayEvery : baseEvery;
        if (guard && isIterateeCall(collection, predicate, guard)) {
          predicate = undefined$1;
        }
        return func(collection, getIteratee(predicate, 3));
      }
      function filter(collection, predicate) {
        var func = isArray(collection) ? arrayFilter : baseFilter;
        return func(collection, getIteratee(predicate, 3));
      }
      var find = createFind(findIndex);
      var findLast = createFind(findLastIndex);
      function flatMap(collection, iteratee2) {
        return baseFlatten(map(collection, iteratee2), 1);
      }
      function flatMapDeep(collection, iteratee2) {
        return baseFlatten(map(collection, iteratee2), INFINITY);
      }
      function flatMapDepth(collection, iteratee2, depth) {
        depth = depth === undefined$1 ? 1 : toInteger(depth);
        return baseFlatten(map(collection, iteratee2), depth);
      }
      function forEach(collection, iteratee2) {
        var func = isArray(collection) ? arrayEach : baseEach;
        return func(collection, getIteratee(iteratee2, 3));
      }
      function forEachRight(collection, iteratee2) {
        var func = isArray(collection) ? arrayEachRight : baseEachRight;
        return func(collection, getIteratee(iteratee2, 3));
      }
      var groupBy = createAggregator(function(result2, value, key) {
        if (hasOwnProperty.call(result2, key)) {
          result2[key].push(value);
        } else {
          baseAssignValue(result2, key, [value]);
        }
      });
      function includes(collection, value, fromIndex, guard) {
        collection = isArrayLike(collection) ? collection : values(collection);
        fromIndex = fromIndex && !guard ? toInteger(fromIndex) : 0;
        var length = collection.length;
        if (fromIndex < 0) {
          fromIndex = nativeMax(length + fromIndex, 0);
        }
        return isString(collection) ? fromIndex <= length && collection.indexOf(value, fromIndex) > -1 : !!length && baseIndexOf(collection, value, fromIndex) > -1;
      }
      var invokeMap = baseRest(function(collection, path, args) {
        var index = -1, isFunc = typeof path == "function", result2 = isArrayLike(collection) ? Array2(collection.length) : [];
        baseEach(collection, function(value) {
          result2[++index] = isFunc ? apply(path, value, args) : baseInvoke(value, path, args);
        });
        return result2;
      });
      var keyBy = createAggregator(function(result2, value, key) {
        baseAssignValue(result2, key, value);
      });
      function map(collection, iteratee2) {
        var func = isArray(collection) ? arrayMap : baseMap;
        return func(collection, getIteratee(iteratee2, 3));
      }
      function orderBy(collection, iteratees, orders, guard) {
        if (collection == null) {
          return [];
        }
        if (!isArray(iteratees)) {
          iteratees = iteratees == null ? [] : [iteratees];
        }
        orders = guard ? undefined$1 : orders;
        if (!isArray(orders)) {
          orders = orders == null ? [] : [orders];
        }
        return baseOrderBy(collection, iteratees, orders);
      }
      var partition = createAggregator(function(result2, value, key) {
        result2[key ? 0 : 1].push(value);
      }, function() {
        return [[], []];
      });
      function reduce(collection, iteratee2, accumulator) {
        var func = isArray(collection) ? arrayReduce : baseReduce, initAccum = arguments.length < 3;
        return func(collection, getIteratee(iteratee2, 4), accumulator, initAccum, baseEach);
      }
      function reduceRight(collection, iteratee2, accumulator) {
        var func = isArray(collection) ? arrayReduceRight : baseReduce, initAccum = arguments.length < 3;
        return func(collection, getIteratee(iteratee2, 4), accumulator, initAccum, baseEachRight);
      }
      function reject(collection, predicate) {
        var func = isArray(collection) ? arrayFilter : baseFilter;
        return func(collection, negate(getIteratee(predicate, 3)));
      }
      function sample(collection) {
        var func = isArray(collection) ? arraySample : baseSample;
        return func(collection);
      }
      function sampleSize(collection, n, guard) {
        if (guard ? isIterateeCall(collection, n, guard) : n === undefined$1) {
          n = 1;
        } else {
          n = toInteger(n);
        }
        var func = isArray(collection) ? arraySampleSize : baseSampleSize;
        return func(collection, n);
      }
      function shuffle(collection) {
        var func = isArray(collection) ? arrayShuffle : baseShuffle;
        return func(collection);
      }
      function size(collection) {
        if (collection == null) {
          return 0;
        }
        if (isArrayLike(collection)) {
          return isString(collection) ? stringSize(collection) : collection.length;
        }
        var tag = getTag(collection);
        if (tag == mapTag || tag == setTag) {
          return collection.size;
        }
        return baseKeys(collection).length;
      }
      function some(collection, predicate, guard) {
        var func = isArray(collection) ? arraySome : baseSome;
        if (guard && isIterateeCall(collection, predicate, guard)) {
          predicate = undefined$1;
        }
        return func(collection, getIteratee(predicate, 3));
      }
      var sortBy = baseRest(function(collection, iteratees) {
        if (collection == null) {
          return [];
        }
        var length = iteratees.length;
        if (length > 1 && isIterateeCall(collection, iteratees[0], iteratees[1])) {
          iteratees = [];
        } else if (length > 2 && isIterateeCall(iteratees[0], iteratees[1], iteratees[2])) {
          iteratees = [iteratees[0]];
        }
        return baseOrderBy(collection, baseFlatten(iteratees, 1), []);
      });
      var now = ctxNow || function() {
        return root.Date.now();
      };
      function after(n, func) {
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        n = toInteger(n);
        return function() {
          if (--n < 1) {
            return func.apply(this, arguments);
          }
        };
      }
      function ary(func, n, guard) {
        n = guard ? undefined$1 : n;
        n = func && n == null ? func.length : n;
        return createWrap(func, WRAP_ARY_FLAG, undefined$1, undefined$1, undefined$1, undefined$1, n);
      }
      function before(n, func) {
        var result2;
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        n = toInteger(n);
        return function() {
          if (--n > 0) {
            result2 = func.apply(this, arguments);
          }
          if (n <= 1) {
            func = undefined$1;
          }
          return result2;
        };
      }
      var bind = baseRest(function(func, thisArg, partials) {
        var bitmask = WRAP_BIND_FLAG;
        if (partials.length) {
          var holders = replaceHolders(partials, getHolder(bind));
          bitmask |= WRAP_PARTIAL_FLAG;
        }
        return createWrap(func, bitmask, thisArg, partials, holders);
      });
      var bindKey = baseRest(function(object, key, partials) {
        var bitmask = WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG;
        if (partials.length) {
          var holders = replaceHolders(partials, getHolder(bindKey));
          bitmask |= WRAP_PARTIAL_FLAG;
        }
        return createWrap(key, bitmask, object, partials, holders);
      });
      function curry(func, arity, guard) {
        arity = guard ? undefined$1 : arity;
        var result2 = createWrap(func, WRAP_CURRY_FLAG, undefined$1, undefined$1, undefined$1, undefined$1, undefined$1, arity);
        result2.placeholder = curry.placeholder;
        return result2;
      }
      function curryRight(func, arity, guard) {
        arity = guard ? undefined$1 : arity;
        var result2 = createWrap(func, WRAP_CURRY_RIGHT_FLAG, undefined$1, undefined$1, undefined$1, undefined$1, undefined$1, arity);
        result2.placeholder = curryRight.placeholder;
        return result2;
      }
      function debounce(func, wait, options) {
        var lastArgs, lastThis, maxWait, result2, timerId, lastCallTime, lastInvokeTime = 0, leading = false, maxing = false, trailing = true;
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        wait = toNumber(wait) || 0;
        if (isObject(options)) {
          leading = !!options.leading;
          maxing = "maxWait" in options;
          maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
          trailing = "trailing" in options ? !!options.trailing : trailing;
        }
        function invokeFunc(time2) {
          var args = lastArgs, thisArg = lastThis;
          lastArgs = lastThis = undefined$1;
          lastInvokeTime = time2;
          result2 = func.apply(thisArg, args);
          return result2;
        }
        function leadingEdge(time2) {
          lastInvokeTime = time2;
          timerId = setTimeout2(timerExpired, wait);
          return leading ? invokeFunc(time2) : result2;
        }
        function remainingWait(time2) {
          var timeSinceLastCall = time2 - lastCallTime, timeSinceLastInvoke = time2 - lastInvokeTime, timeWaiting = wait - timeSinceLastCall;
          return maxing ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
        }
        function shouldInvoke(time2) {
          var timeSinceLastCall = time2 - lastCallTime, timeSinceLastInvoke = time2 - lastInvokeTime;
          return lastCallTime === undefined$1 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
        }
        function timerExpired() {
          var time2 = now();
          if (shouldInvoke(time2)) {
            return trailingEdge(time2);
          }
          timerId = setTimeout2(timerExpired, remainingWait(time2));
        }
        function trailingEdge(time2) {
          timerId = undefined$1;
          if (trailing && lastArgs) {
            return invokeFunc(time2);
          }
          lastArgs = lastThis = undefined$1;
          return result2;
        }
        function cancel() {
          if (timerId !== undefined$1) {
            clearTimeout2(timerId);
          }
          lastInvokeTime = 0;
          lastArgs = lastCallTime = lastThis = timerId = undefined$1;
        }
        function flush() {
          return timerId === undefined$1 ? result2 : trailingEdge(now());
        }
        function debounced() {
          var time2 = now(), isInvoking = shouldInvoke(time2);
          lastArgs = arguments;
          lastThis = this;
          lastCallTime = time2;
          if (isInvoking) {
            if (timerId === undefined$1) {
              return leadingEdge(lastCallTime);
            }
            if (maxing) {
              clearTimeout2(timerId);
              timerId = setTimeout2(timerExpired, wait);
              return invokeFunc(lastCallTime);
            }
          }
          if (timerId === undefined$1) {
            timerId = setTimeout2(timerExpired, wait);
          }
          return result2;
        }
        debounced.cancel = cancel;
        debounced.flush = flush;
        return debounced;
      }
      var defer = baseRest(function(func, args) {
        return baseDelay(func, 1, args);
      });
      var delay = baseRest(function(func, wait, args) {
        return baseDelay(func, toNumber(wait) || 0, args);
      });
      function flip(func) {
        return createWrap(func, WRAP_FLIP_FLAG);
      }
      function memoize(func, resolver) {
        if (typeof func != "function" || resolver != null && typeof resolver != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        var memoized = function() {
          var args = arguments, key = resolver ? resolver.apply(this, args) : args[0], cache = memoized.cache;
          if (cache.has(key)) {
            return cache.get(key);
          }
          var result2 = func.apply(this, args);
          memoized.cache = cache.set(key, result2) || cache;
          return result2;
        };
        memoized.cache = new (memoize.Cache || MapCache)();
        return memoized;
      }
      memoize.Cache = MapCache;
      function negate(predicate) {
        if (typeof predicate != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        return function() {
          var args = arguments;
          switch (args.length) {
            case 0:
              return !predicate.call(this);
            case 1:
              return !predicate.call(this, args[0]);
            case 2:
              return !predicate.call(this, args[0], args[1]);
            case 3:
              return !predicate.call(this, args[0], args[1], args[2]);
          }
          return !predicate.apply(this, args);
        };
      }
      function once(func) {
        return before(2, func);
      }
      var overArgs = castRest(function(func, transforms) {
        transforms = transforms.length == 1 && isArray(transforms[0]) ? arrayMap(transforms[0], baseUnary(getIteratee())) : arrayMap(baseFlatten(transforms, 1), baseUnary(getIteratee()));
        var funcsLength = transforms.length;
        return baseRest(function(args) {
          var index = -1, length = nativeMin(args.length, funcsLength);
          while (++index < length) {
            args[index] = transforms[index].call(this, args[index]);
          }
          return apply(func, this, args);
        });
      });
      var partial = baseRest(function(func, partials) {
        var holders = replaceHolders(partials, getHolder(partial));
        return createWrap(func, WRAP_PARTIAL_FLAG, undefined$1, partials, holders);
      });
      var partialRight = baseRest(function(func, partials) {
        var holders = replaceHolders(partials, getHolder(partialRight));
        return createWrap(func, WRAP_PARTIAL_RIGHT_FLAG, undefined$1, partials, holders);
      });
      var rearg = flatRest(function(func, indexes) {
        return createWrap(func, WRAP_REARG_FLAG, undefined$1, undefined$1, undefined$1, indexes);
      });
      function rest(func, start) {
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        start = start === undefined$1 ? start : toInteger(start);
        return baseRest(func, start);
      }
      function spread(func, start) {
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        start = start == null ? 0 : nativeMax(toInteger(start), 0);
        return baseRest(function(args) {
          var array = args[start], otherArgs = castSlice(args, 0, start);
          if (array) {
            arrayPush(otherArgs, array);
          }
          return apply(func, this, otherArgs);
        });
      }
      function throttle(func, wait, options) {
        var leading = true, trailing = true;
        if (typeof func != "function") {
          throw new TypeError2(FUNC_ERROR_TEXT);
        }
        if (isObject(options)) {
          leading = "leading" in options ? !!options.leading : leading;
          trailing = "trailing" in options ? !!options.trailing : trailing;
        }
        return debounce(func, wait, {
          "leading": leading,
          "maxWait": wait,
          "trailing": trailing
        });
      }
      function unary(func) {
        return ary(func, 1);
      }
      function wrap(value, wrapper) {
        return partial(castFunction(wrapper), value);
      }
      function castArray() {
        if (!arguments.length) {
          return [];
        }
        var value = arguments[0];
        return isArray(value) ? value : [value];
      }
      function clone(value) {
        return baseClone(value, CLONE_SYMBOLS_FLAG);
      }
      function cloneWith(value, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        return baseClone(value, CLONE_SYMBOLS_FLAG, customizer);
      }
      function cloneDeep(value) {
        return baseClone(value, CLONE_DEEP_FLAG | CLONE_SYMBOLS_FLAG);
      }
      function cloneDeepWith(value, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        return baseClone(value, CLONE_DEEP_FLAG | CLONE_SYMBOLS_FLAG, customizer);
      }
      function conformsTo(object, source) {
        return source == null || baseConformsTo(object, source, keys(source));
      }
      function eq(value, other) {
        return value === other || value !== value && other !== other;
      }
      var gt = createRelationalOperation(baseGt);
      var gte = createRelationalOperation(function(value, other) {
        return value >= other;
      });
      var isArguments = baseIsArguments(function() {
        return arguments;
      }()) ? baseIsArguments : function(value) {
        return isObjectLike(value) && hasOwnProperty.call(value, "callee") && !propertyIsEnumerable.call(value, "callee");
      };
      var isArray = Array2.isArray;
      var isArrayBuffer = nodeIsArrayBuffer ? baseUnary(nodeIsArrayBuffer) : baseIsArrayBuffer;
      function isArrayLike(value) {
        return value != null && isLength(value.length) && !isFunction(value);
      }
      function isArrayLikeObject(value) {
        return isObjectLike(value) && isArrayLike(value);
      }
      function isBoolean(value) {
        return value === true || value === false || isObjectLike(value) && baseGetTag(value) == boolTag;
      }
      var isBuffer = nativeIsBuffer || stubFalse;
      var isDate = nodeIsDate ? baseUnary(nodeIsDate) : baseIsDate;
      function isElement(value) {
        return isObjectLike(value) && value.nodeType === 1 && !isPlainObject(value);
      }
      function isEmpty(value) {
        if (value == null) {
          return true;
        }
        if (isArrayLike(value) && (isArray(value) || typeof value == "string" || typeof value.splice == "function" || isBuffer(value) || isTypedArray(value) || isArguments(value))) {
          return !value.length;
        }
        var tag = getTag(value);
        if (tag == mapTag || tag == setTag) {
          return !value.size;
        }
        if (isPrototype(value)) {
          return !baseKeys(value).length;
        }
        for (var key in value) {
          if (hasOwnProperty.call(value, key)) {
            return false;
          }
        }
        return true;
      }
      function isEqual(value, other) {
        return baseIsEqual(value, other);
      }
      function isEqualWith(value, other, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        var result2 = customizer ? customizer(value, other) : undefined$1;
        return result2 === undefined$1 ? baseIsEqual(value, other, undefined$1, customizer) : !!result2;
      }
      function isError(value) {
        if (!isObjectLike(value)) {
          return false;
        }
        var tag = baseGetTag(value);
        return tag == errorTag || tag == domExcTag || typeof value.message == "string" && typeof value.name == "string" && !isPlainObject(value);
      }
      function isFinite(value) {
        return typeof value == "number" && nativeIsFinite(value);
      }
      function isFunction(value) {
        if (!isObject(value)) {
          return false;
        }
        var tag = baseGetTag(value);
        return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
      }
      function isInteger(value) {
        return typeof value == "number" && value == toInteger(value);
      }
      function isLength(value) {
        return typeof value == "number" && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
      }
      function isObject(value) {
        var type = typeof value;
        return value != null && (type == "object" || type == "function");
      }
      function isObjectLike(value) {
        return value != null && typeof value == "object";
      }
      var isMap = nodeIsMap ? baseUnary(nodeIsMap) : baseIsMap;
      function isMatch(object, source) {
        return object === source || baseIsMatch(object, source, getMatchData(source));
      }
      function isMatchWith(object, source, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        return baseIsMatch(object, source, getMatchData(source), customizer);
      }
      function isNaN2(value) {
        return isNumber(value) && value != +value;
      }
      function isNative(value) {
        if (isMaskable(value)) {
          throw new Error2(CORE_ERROR_TEXT);
        }
        return baseIsNative(value);
      }
      function isNull(value) {
        return value === null;
      }
      function isNil(value) {
        return value == null;
      }
      function isNumber(value) {
        return typeof value == "number" || isObjectLike(value) && baseGetTag(value) == numberTag;
      }
      function isPlainObject(value) {
        if (!isObjectLike(value) || baseGetTag(value) != objectTag) {
          return false;
        }
        var proto = getPrototype(value);
        if (proto === null) {
          return true;
        }
        var Ctor = hasOwnProperty.call(proto, "constructor") && proto.constructor;
        return typeof Ctor == "function" && Ctor instanceof Ctor && funcToString.call(Ctor) == objectCtorString;
      }
      var isRegExp = nodeIsRegExp ? baseUnary(nodeIsRegExp) : baseIsRegExp;
      function isSafeInteger(value) {
        return isInteger(value) && value >= -MAX_SAFE_INTEGER && value <= MAX_SAFE_INTEGER;
      }
      var isSet = nodeIsSet ? baseUnary(nodeIsSet) : baseIsSet;
      function isString(value) {
        return typeof value == "string" || !isArray(value) && isObjectLike(value) && baseGetTag(value) == stringTag;
      }
      function isSymbol(value) {
        return typeof value == "symbol" || isObjectLike(value) && baseGetTag(value) == symbolTag;
      }
      var isTypedArray = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;
      function isUndefined(value) {
        return value === undefined$1;
      }
      function isWeakMap(value) {
        return isObjectLike(value) && getTag(value) == weakMapTag;
      }
      function isWeakSet(value) {
        return isObjectLike(value) && baseGetTag(value) == weakSetTag;
      }
      var lt = createRelationalOperation(baseLt);
      var lte = createRelationalOperation(function(value, other) {
        return value <= other;
      });
      function toArray(value) {
        if (!value) {
          return [];
        }
        if (isArrayLike(value)) {
          return isString(value) ? stringToArray(value) : copyArray(value);
        }
        if (symIterator && value[symIterator]) {
          return iteratorToArray(value[symIterator]());
        }
        var tag = getTag(value), func = tag == mapTag ? mapToArray : tag == setTag ? setToArray : values;
        return func(value);
      }
      function toFinite(value) {
        if (!value) {
          return value === 0 ? value : 0;
        }
        value = toNumber(value);
        if (value === INFINITY || value === -INFINITY) {
          var sign = value < 0 ? -1 : 1;
          return sign * MAX_INTEGER;
        }
        return value === value ? value : 0;
      }
      function toInteger(value) {
        var result2 = toFinite(value), remainder = result2 % 1;
        return result2 === result2 ? remainder ? result2 - remainder : result2 : 0;
      }
      function toLength(value) {
        return value ? baseClamp(toInteger(value), 0, MAX_ARRAY_LENGTH) : 0;
      }
      function toNumber(value) {
        if (typeof value == "number") {
          return value;
        }
        if (isSymbol(value)) {
          return NAN;
        }
        if (isObject(value)) {
          var other = typeof value.valueOf == "function" ? value.valueOf() : value;
          value = isObject(other) ? other + "" : other;
        }
        if (typeof value != "string") {
          return value === 0 ? value : +value;
        }
        value = baseTrim(value);
        var isBinary = reIsBinary.test(value);
        return isBinary || reIsOctal.test(value) ? freeParseInt(value.slice(2), isBinary ? 2 : 8) : reIsBadHex.test(value) ? NAN : +value;
      }
      function toPlainObject(value) {
        return copyObject(value, keysIn(value));
      }
      function toSafeInteger(value) {
        return value ? baseClamp(toInteger(value), -MAX_SAFE_INTEGER, MAX_SAFE_INTEGER) : value === 0 ? value : 0;
      }
      function toString(value) {
        return value == null ? "" : baseToString(value);
      }
      var assign = createAssigner(function(object, source) {
        if (isPrototype(source) || isArrayLike(source)) {
          copyObject(source, keys(source), object);
          return;
        }
        for (var key in source) {
          if (hasOwnProperty.call(source, key)) {
            assignValue(object, key, source[key]);
          }
        }
      });
      var assignIn = createAssigner(function(object, source) {
        copyObject(source, keysIn(source), object);
      });
      var assignInWith = createAssigner(function(object, source, srcIndex, customizer) {
        copyObject(source, keysIn(source), object, customizer);
      });
      var assignWith = createAssigner(function(object, source, srcIndex, customizer) {
        copyObject(source, keys(source), object, customizer);
      });
      var at = flatRest(baseAt);
      function create(prototype, properties) {
        var result2 = baseCreate(prototype);
        return properties == null ? result2 : baseAssign(result2, properties);
      }
      var defaults = baseRest(function(object, sources) {
        object = Object2(object);
        var index = -1;
        var length = sources.length;
        var guard = length > 2 ? sources[2] : undefined$1;
        if (guard && isIterateeCall(sources[0], sources[1], guard)) {
          length = 1;
        }
        while (++index < length) {
          var source = sources[index];
          var props = keysIn(source);
          var propsIndex = -1;
          var propsLength = props.length;
          while (++propsIndex < propsLength) {
            var key = props[propsIndex];
            var value = object[key];
            if (value === undefined$1 || eq(value, objectProto[key]) && !hasOwnProperty.call(object, key)) {
              object[key] = source[key];
            }
          }
        }
        return object;
      });
      var defaultsDeep = baseRest(function(args) {
        args.push(undefined$1, customDefaultsMerge);
        return apply(mergeWith, undefined$1, args);
      });
      function findKey(object, predicate) {
        return baseFindKey(object, getIteratee(predicate, 3), baseForOwn);
      }
      function findLastKey(object, predicate) {
        return baseFindKey(object, getIteratee(predicate, 3), baseForOwnRight);
      }
      function forIn(object, iteratee2) {
        return object == null ? object : baseFor(object, getIteratee(iteratee2, 3), keysIn);
      }
      function forInRight(object, iteratee2) {
        return object == null ? object : baseForRight(object, getIteratee(iteratee2, 3), keysIn);
      }
      function forOwn(object, iteratee2) {
        return object && baseForOwn(object, getIteratee(iteratee2, 3));
      }
      function forOwnRight(object, iteratee2) {
        return object && baseForOwnRight(object, getIteratee(iteratee2, 3));
      }
      function functions(object) {
        return object == null ? [] : baseFunctions(object, keys(object));
      }
      function functionsIn(object) {
        return object == null ? [] : baseFunctions(object, keysIn(object));
      }
      function get(object, path, defaultValue) {
        var result2 = object == null ? undefined$1 : baseGet(object, path);
        return result2 === undefined$1 ? defaultValue : result2;
      }
      function has(object, path) {
        return object != null && hasPath(object, path, baseHas);
      }
      function hasIn(object, path) {
        return object != null && hasPath(object, path, baseHasIn);
      }
      var invert = createInverter(function(result2, value, key) {
        if (value != null && typeof value.toString != "function") {
          value = nativeObjectToString.call(value);
        }
        result2[value] = key;
      }, constant(identity));
      var invertBy = createInverter(function(result2, value, key) {
        if (value != null && typeof value.toString != "function") {
          value = nativeObjectToString.call(value);
        }
        if (hasOwnProperty.call(result2, value)) {
          result2[value].push(key);
        } else {
          result2[value] = [key];
        }
      }, getIteratee);
      var invoke = baseRest(baseInvoke);
      function keys(object) {
        return isArrayLike(object) ? arrayLikeKeys(object) : baseKeys(object);
      }
      function keysIn(object) {
        return isArrayLike(object) ? arrayLikeKeys(object, true) : baseKeysIn(object);
      }
      function mapKeys(object, iteratee2) {
        var result2 = {};
        iteratee2 = getIteratee(iteratee2, 3);
        baseForOwn(object, function(value, key, object2) {
          baseAssignValue(result2, iteratee2(value, key, object2), value);
        });
        return result2;
      }
      function mapValues(object, iteratee2) {
        var result2 = {};
        iteratee2 = getIteratee(iteratee2, 3);
        baseForOwn(object, function(value, key, object2) {
          baseAssignValue(result2, key, iteratee2(value, key, object2));
        });
        return result2;
      }
      var merge = createAssigner(function(object, source, srcIndex) {
        baseMerge(object, source, srcIndex);
      });
      var mergeWith = createAssigner(function(object, source, srcIndex, customizer) {
        baseMerge(object, source, srcIndex, customizer);
      });
      var omit = flatRest(function(object, paths) {
        var result2 = {};
        if (object == null) {
          return result2;
        }
        var isDeep = false;
        paths = arrayMap(paths, function(path) {
          path = castPath(path, object);
          isDeep || (isDeep = path.length > 1);
          return path;
        });
        copyObject(object, getAllKeysIn(object), result2);
        if (isDeep) {
          result2 = baseClone(result2, CLONE_DEEP_FLAG | CLONE_FLAT_FLAG | CLONE_SYMBOLS_FLAG, customOmitClone);
        }
        var length = paths.length;
        while (length--) {
          baseUnset(result2, paths[length]);
        }
        return result2;
      });
      function omitBy(object, predicate) {
        return pickBy(object, negate(getIteratee(predicate)));
      }
      var pick = flatRest(function(object, paths) {
        return object == null ? {} : basePick(object, paths);
      });
      function pickBy(object, predicate) {
        if (object == null) {
          return {};
        }
        var props = arrayMap(getAllKeysIn(object), function(prop) {
          return [prop];
        });
        predicate = getIteratee(predicate);
        return basePickBy(object, props, function(value, path) {
          return predicate(value, path[0]);
        });
      }
      function result(object, path, defaultValue) {
        path = castPath(path, object);
        var index = -1, length = path.length;
        if (!length) {
          length = 1;
          object = undefined$1;
        }
        while (++index < length) {
          var value = object == null ? undefined$1 : object[toKey(path[index])];
          if (value === undefined$1) {
            index = length;
            value = defaultValue;
          }
          object = isFunction(value) ? value.call(object) : value;
        }
        return object;
      }
      function set(object, path, value) {
        return object == null ? object : baseSet(object, path, value);
      }
      function setWith(object, path, value, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        return object == null ? object : baseSet(object, path, value, customizer);
      }
      var toPairs = createToPairs(keys);
      var toPairsIn = createToPairs(keysIn);
      function transform(object, iteratee2, accumulator) {
        var isArr = isArray(object), isArrLike = isArr || isBuffer(object) || isTypedArray(object);
        iteratee2 = getIteratee(iteratee2, 4);
        if (accumulator == null) {
          var Ctor = object && object.constructor;
          if (isArrLike) {
            accumulator = isArr ? new Ctor() : [];
          } else if (isObject(object)) {
            accumulator = isFunction(Ctor) ? baseCreate(getPrototype(object)) : {};
          } else {
            accumulator = {};
          }
        }
        (isArrLike ? arrayEach : baseForOwn)(object, function(value, index, object2) {
          return iteratee2(accumulator, value, index, object2);
        });
        return accumulator;
      }
      function unset(object, path) {
        return object == null ? true : baseUnset(object, path);
      }
      function update(object, path, updater) {
        return object == null ? object : baseUpdate(object, path, castFunction(updater));
      }
      function updateWith(object, path, updater, customizer) {
        customizer = typeof customizer == "function" ? customizer : undefined$1;
        return object == null ? object : baseUpdate(object, path, castFunction(updater), customizer);
      }
      function values(object) {
        return object == null ? [] : baseValues(object, keys(object));
      }
      function valuesIn(object) {
        return object == null ? [] : baseValues(object, keysIn(object));
      }
      function clamp(number, lower, upper) {
        if (upper === undefined$1) {
          upper = lower;
          lower = undefined$1;
        }
        if (upper !== undefined$1) {
          upper = toNumber(upper);
          upper = upper === upper ? upper : 0;
        }
        if (lower !== undefined$1) {
          lower = toNumber(lower);
          lower = lower === lower ? lower : 0;
        }
        return baseClamp(toNumber(number), lower, upper);
      }
      function inRange(number, start, end) {
        start = toFinite(start);
        if (end === undefined$1) {
          end = start;
          start = 0;
        } else {
          end = toFinite(end);
        }
        number = toNumber(number);
        return baseInRange(number, start, end);
      }
      function random(lower, upper, floating) {
        if (floating && typeof floating != "boolean" && isIterateeCall(lower, upper, floating)) {
          upper = floating = undefined$1;
        }
        if (floating === undefined$1) {
          if (typeof upper == "boolean") {
            floating = upper;
            upper = undefined$1;
          } else if (typeof lower == "boolean") {
            floating = lower;
            lower = undefined$1;
          }
        }
        if (lower === undefined$1 && upper === undefined$1) {
          lower = 0;
          upper = 1;
        } else {
          lower = toFinite(lower);
          if (upper === undefined$1) {
            upper = lower;
            lower = 0;
          } else {
            upper = toFinite(upper);
          }
        }
        if (lower > upper) {
          var temp = lower;
          lower = upper;
          upper = temp;
        }
        if (floating || lower % 1 || upper % 1) {
          var rand = nativeRandom();
          return nativeMin(lower + rand * (upper - lower + freeParseFloat("1e-" + ((rand + "").length - 1))), upper);
        }
        return baseRandom(lower, upper);
      }
      var camelCase = createCompounder(function(result2, word, index) {
        word = word.toLowerCase();
        return result2 + (index ? capitalize(word) : word);
      });
      function capitalize(string) {
        return upperFirst(toString(string).toLowerCase());
      }
      function deburr(string) {
        string = toString(string);
        return string && string.replace(reLatin, deburrLetter).replace(reComboMark, "");
      }
      function endsWith(string, target, position) {
        string = toString(string);
        target = baseToString(target);
        var length = string.length;
        position = position === undefined$1 ? length : baseClamp(toInteger(position), 0, length);
        var end = position;
        position -= target.length;
        return position >= 0 && string.slice(position, end) == target;
      }
      function escape(string) {
        string = toString(string);
        return string && reHasUnescapedHtml.test(string) ? string.replace(reUnescapedHtml, escapeHtmlChar) : string;
      }
      function escapeRegExp(string) {
        string = toString(string);
        return string && reHasRegExpChar.test(string) ? string.replace(reRegExpChar, "\\$&") : string;
      }
      var kebabCase = createCompounder(function(result2, word, index) {
        return result2 + (index ? "-" : "") + word.toLowerCase();
      });
      var lowerCase = createCompounder(function(result2, word, index) {
        return result2 + (index ? " " : "") + word.toLowerCase();
      });
      var lowerFirst = createCaseFirst("toLowerCase");
      function pad(string, length, chars) {
        string = toString(string);
        length = toInteger(length);
        var strLength = length ? stringSize(string) : 0;
        if (!length || strLength >= length) {
          return string;
        }
        var mid = (length - strLength) / 2;
        return createPadding(nativeFloor(mid), chars) + string + createPadding(nativeCeil(mid), chars);
      }
      function padEnd(string, length, chars) {
        string = toString(string);
        length = toInteger(length);
        var strLength = length ? stringSize(string) : 0;
        return length && strLength < length ? string + createPadding(length - strLength, chars) : string;
      }
      function padStart(string, length, chars) {
        string = toString(string);
        length = toInteger(length);
        var strLength = length ? stringSize(string) : 0;
        return length && strLength < length ? createPadding(length - strLength, chars) + string : string;
      }
      function parseInt2(string, radix, guard) {
        if (guard || radix == null) {
          radix = 0;
        } else if (radix) {
          radix = +radix;
        }
        return nativeParseInt(toString(string).replace(reTrimStart, ""), radix || 0);
      }
      function repeat(string, n, guard) {
        if (guard ? isIterateeCall(string, n, guard) : n === undefined$1) {
          n = 1;
        } else {
          n = toInteger(n);
        }
        return baseRepeat(toString(string), n);
      }
      function replace() {
        var args = arguments, string = toString(args[0]);
        return args.length < 3 ? string : string.replace(args[1], args[2]);
      }
      var snakeCase = createCompounder(function(result2, word, index) {
        return result2 + (index ? "_" : "") + word.toLowerCase();
      });
      function split(string, separator, limit) {
        if (limit && typeof limit != "number" && isIterateeCall(string, separator, limit)) {
          separator = limit = undefined$1;
        }
        limit = limit === undefined$1 ? MAX_ARRAY_LENGTH : limit >>> 0;
        if (!limit) {
          return [];
        }
        string = toString(string);
        if (string && (typeof separator == "string" || separator != null && !isRegExp(separator))) {
          separator = baseToString(separator);
          if (!separator && hasUnicode(string)) {
            return castSlice(stringToArray(string), 0, limit);
          }
        }
        return string.split(separator, limit);
      }
      var startCase = createCompounder(function(result2, word, index) {
        return result2 + (index ? " " : "") + upperFirst(word);
      });
      function startsWith(string, target, position) {
        string = toString(string);
        position = position == null ? 0 : baseClamp(toInteger(position), 0, string.length);
        target = baseToString(target);
        return string.slice(position, position + target.length) == target;
      }
      function template(string, options, guard) {
        var settings = lodash2.templateSettings;
        if (guard && isIterateeCall(string, options, guard)) {
          options = undefined$1;
        }
        string = toString(string);
        options = assignInWith({}, options, settings, customDefaultsAssignIn);
        var imports = assignInWith({}, options.imports, settings.imports, customDefaultsAssignIn), importsKeys = keys(imports), importsValues = baseValues(imports, importsKeys);
        var isEscaping, isEvaluating, index = 0, interpolate = options.interpolate || reNoMatch, source = "__p += '";
        var reDelimiters = RegExp2((options.escape || reNoMatch).source + "|" + interpolate.source + "|" + (interpolate === reInterpolate ? reEsTemplate : reNoMatch).source + "|" + (options.evaluate || reNoMatch).source + "|$", "g");
        var sourceURL = "//# sourceURL=" + (hasOwnProperty.call(options, "sourceURL") ? (options.sourceURL + "").replace(/\s/g, " ") : "lodash.templateSources[" + ++templateCounter + "]") + "\n";
        string.replace(reDelimiters, function(match, escapeValue, interpolateValue, esTemplateValue, evaluateValue, offset) {
          interpolateValue || (interpolateValue = esTemplateValue);
          source += string.slice(index, offset).replace(reUnescapedString, escapeStringChar);
          if (escapeValue) {
            isEscaping = true;
            source += "' +\n__e(" + escapeValue + ") +\n'";
          }
          if (evaluateValue) {
            isEvaluating = true;
            source += "';\n" + evaluateValue + ";\n__p += '";
          }
          if (interpolateValue) {
            source += "' +\n((__t = (" + interpolateValue + ")) == null ? '' : __t) +\n'";
          }
          index = offset + match.length;
          return match;
        });
        source += "';\n";
        var variable = hasOwnProperty.call(options, "variable") && options.variable;
        if (!variable) {
          source = "with (obj) {\n" + source + "\n}\n";
        } else if (reForbiddenIdentifierChars.test(variable)) {
          throw new Error2(INVALID_TEMPL_VAR_ERROR_TEXT);
        }
        source = (isEvaluating ? source.replace(reEmptyStringLeading, "") : source).replace(reEmptyStringMiddle, "$1").replace(reEmptyStringTrailing, "$1;");
        source = "function(" + (variable || "obj") + ") {\n" + (variable ? "" : "obj || (obj = {});\n") + "var __t, __p = ''" + (isEscaping ? ", __e = _.escape" : "") + (isEvaluating ? ", __j = Array.prototype.join;\nfunction print() { __p += __j.call(arguments, '') }\n" : ";\n") + source + "return __p\n}";
        var result2 = attempt(function() {
          return Function2(importsKeys, sourceURL + "return " + source).apply(undefined$1, importsValues);
        });
        result2.source = source;
        if (isError(result2)) {
          throw result2;
        }
        return result2;
      }
      function toLower(value) {
        return toString(value).toLowerCase();
      }
      function toUpper(value) {
        return toString(value).toUpperCase();
      }
      function trim(string, chars, guard) {
        string = toString(string);
        if (string && (guard || chars === undefined$1)) {
          return baseTrim(string);
        }
        if (!string || !(chars = baseToString(chars))) {
          return string;
        }
        var strSymbols = stringToArray(string), chrSymbols = stringToArray(chars), start = charsStartIndex(strSymbols, chrSymbols), end = charsEndIndex(strSymbols, chrSymbols) + 1;
        return castSlice(strSymbols, start, end).join("");
      }
      function trimEnd(string, chars, guard) {
        string = toString(string);
        if (string && (guard || chars === undefined$1)) {
          return string.slice(0, trimmedEndIndex(string) + 1);
        }
        if (!string || !(chars = baseToString(chars))) {
          return string;
        }
        var strSymbols = stringToArray(string), end = charsEndIndex(strSymbols, stringToArray(chars)) + 1;
        return castSlice(strSymbols, 0, end).join("");
      }
      function trimStart(string, chars, guard) {
        string = toString(string);
        if (string && (guard || chars === undefined$1)) {
          return string.replace(reTrimStart, "");
        }
        if (!string || !(chars = baseToString(chars))) {
          return string;
        }
        var strSymbols = stringToArray(string), start = charsStartIndex(strSymbols, stringToArray(chars));
        return castSlice(strSymbols, start).join("");
      }
      function truncate(string, options) {
        var length = DEFAULT_TRUNC_LENGTH, omission = DEFAULT_TRUNC_OMISSION;
        if (isObject(options)) {
          var separator = "separator" in options ? options.separator : separator;
          length = "length" in options ? toInteger(options.length) : length;
          omission = "omission" in options ? baseToString(options.omission) : omission;
        }
        string = toString(string);
        var strLength = string.length;
        if (hasUnicode(string)) {
          var strSymbols = stringToArray(string);
          strLength = strSymbols.length;
        }
        if (length >= strLength) {
          return string;
        }
        var end = length - stringSize(omission);
        if (end < 1) {
          return omission;
        }
        var result2 = strSymbols ? castSlice(strSymbols, 0, end).join("") : string.slice(0, end);
        if (separator === undefined$1) {
          return result2 + omission;
        }
        if (strSymbols) {
          end += result2.length - end;
        }
        if (isRegExp(separator)) {
          if (string.slice(end).search(separator)) {
            var match, substring = result2;
            if (!separator.global) {
              separator = RegExp2(separator.source, toString(reFlags.exec(separator)) + "g");
            }
            separator.lastIndex = 0;
            while (match = separator.exec(substring)) {
              var newEnd = match.index;
            }
            result2 = result2.slice(0, newEnd === undefined$1 ? end : newEnd);
          }
        } else if (string.indexOf(baseToString(separator), end) != end) {
          var index = result2.lastIndexOf(separator);
          if (index > -1) {
            result2 = result2.slice(0, index);
          }
        }
        return result2 + omission;
      }
      function unescape(string) {
        string = toString(string);
        return string && reHasEscapedHtml.test(string) ? string.replace(reEscapedHtml, unescapeHtmlChar) : string;
      }
      var upperCase = createCompounder(function(result2, word, index) {
        return result2 + (index ? " " : "") + word.toUpperCase();
      });
      var upperFirst = createCaseFirst("toUpperCase");
      function words(string, pattern, guard) {
        string = toString(string);
        pattern = guard ? undefined$1 : pattern;
        if (pattern === undefined$1) {
          return hasUnicodeWord(string) ? unicodeWords(string) : asciiWords(string);
        }
        return string.match(pattern) || [];
      }
      var attempt = baseRest(function(func, args) {
        try {
          return apply(func, undefined$1, args);
        } catch (e) {
          return isError(e) ? e : new Error2(e);
        }
      });
      var bindAll = flatRest(function(object, methodNames) {
        arrayEach(methodNames, function(key) {
          key = toKey(key);
          baseAssignValue(object, key, bind(object[key], object));
        });
        return object;
      });
      function cond(pairs) {
        var length = pairs == null ? 0 : pairs.length, toIteratee = getIteratee();
        pairs = !length ? [] : arrayMap(pairs, function(pair) {
          if (typeof pair[1] != "function") {
            throw new TypeError2(FUNC_ERROR_TEXT);
          }
          return [toIteratee(pair[0]), pair[1]];
        });
        return baseRest(function(args) {
          var index = -1;
          while (++index < length) {
            var pair = pairs[index];
            if (apply(pair[0], this, args)) {
              return apply(pair[1], this, args);
            }
          }
        });
      }
      function conforms(source) {
        return baseConforms(baseClone(source, CLONE_DEEP_FLAG));
      }
      function constant(value) {
        return function() {
          return value;
        };
      }
      function defaultTo(value, defaultValue) {
        return value == null || value !== value ? defaultValue : value;
      }
      var flow = createFlow();
      var flowRight = createFlow(true);
      function identity(value) {
        return value;
      }
      function iteratee(func) {
        return baseIteratee(typeof func == "function" ? func : baseClone(func, CLONE_DEEP_FLAG));
      }
      function matches(source) {
        return baseMatches(baseClone(source, CLONE_DEEP_FLAG));
      }
      function matchesProperty(path, srcValue) {
        return baseMatchesProperty(path, baseClone(srcValue, CLONE_DEEP_FLAG));
      }
      var method = baseRest(function(path, args) {
        return function(object) {
          return baseInvoke(object, path, args);
        };
      });
      var methodOf = baseRest(function(object, args) {
        return function(path) {
          return baseInvoke(object, path, args);
        };
      });
      function mixin(object, source, options) {
        var props = keys(source), methodNames = baseFunctions(source, props);
        if (options == null && !(isObject(source) && (methodNames.length || !props.length))) {
          options = source;
          source = object;
          object = this;
          methodNames = baseFunctions(source, keys(source));
        }
        var chain2 = !(isObject(options) && "chain" in options) || !!options.chain, isFunc = isFunction(object);
        arrayEach(methodNames, function(methodName) {
          var func = source[methodName];
          object[methodName] = func;
          if (isFunc) {
            object.prototype[methodName] = function() {
              var chainAll = this.__chain__;
              if (chain2 || chainAll) {
                var result2 = object(this.__wrapped__), actions = result2.__actions__ = copyArray(this.__actions__);
                actions.push({ "func": func, "args": arguments, "thisArg": object });
                result2.__chain__ = chainAll;
                return result2;
              }
              return func.apply(object, arrayPush([this.value()], arguments));
            };
          }
        });
        return object;
      }
      function noConflict() {
        if (root._ === this) {
          root._ = oldDash;
        }
        return this;
      }
      function noop() {
      }
      function nthArg(n) {
        n = toInteger(n);
        return baseRest(function(args) {
          return baseNth(args, n);
        });
      }
      var over = createOver(arrayMap);
      var overEvery = createOver(arrayEvery);
      var overSome = createOver(arraySome);
      function property(path) {
        return isKey(path) ? baseProperty(toKey(path)) : basePropertyDeep(path);
      }
      function propertyOf(object) {
        return function(path) {
          return object == null ? undefined$1 : baseGet(object, path);
        };
      }
      var range = createRange();
      var rangeRight = createRange(true);
      function stubArray() {
        return [];
      }
      function stubFalse() {
        return false;
      }
      function stubObject() {
        return {};
      }
      function stubString() {
        return "";
      }
      function stubTrue() {
        return true;
      }
      function times(n, iteratee2) {
        n = toInteger(n);
        if (n < 1 || n > MAX_SAFE_INTEGER) {
          return [];
        }
        var index = MAX_ARRAY_LENGTH, length = nativeMin(n, MAX_ARRAY_LENGTH);
        iteratee2 = getIteratee(iteratee2);
        n -= MAX_ARRAY_LENGTH;
        var result2 = baseTimes(length, iteratee2);
        while (++index < n) {
          iteratee2(index);
        }
        return result2;
      }
      function toPath(value) {
        if (isArray(value)) {
          return arrayMap(value, toKey);
        }
        return isSymbol(value) ? [value] : copyArray(stringToPath(toString(value)));
      }
      function uniqueId(prefix) {
        var id = ++idCounter;
        return toString(prefix) + id;
      }
      var add = createMathOperation(function(augend, addend) {
        return augend + addend;
      }, 0);
      var ceil = createRound("ceil");
      var divide = createMathOperation(function(dividend, divisor) {
        return dividend / divisor;
      }, 1);
      var floor = createRound("floor");
      function max(array) {
        return array && array.length ? baseExtremum(array, identity, baseGt) : undefined$1;
      }
      function maxBy(array, iteratee2) {
        return array && array.length ? baseExtremum(array, getIteratee(iteratee2, 2), baseGt) : undefined$1;
      }
      function mean(array) {
        return baseMean(array, identity);
      }
      function meanBy(array, iteratee2) {
        return baseMean(array, getIteratee(iteratee2, 2));
      }
      function min(array) {
        return array && array.length ? baseExtremum(array, identity, baseLt) : undefined$1;
      }
      function minBy(array, iteratee2) {
        return array && array.length ? baseExtremum(array, getIteratee(iteratee2, 2), baseLt) : undefined$1;
      }
      var multiply = createMathOperation(function(multiplier, multiplicand) {
        return multiplier * multiplicand;
      }, 1);
      var round = createRound("round");
      var subtract = createMathOperation(function(minuend, subtrahend) {
        return minuend - subtrahend;
      }, 0);
      function sum(array) {
        return array && array.length ? baseSum(array, identity) : 0;
      }
      function sumBy(array, iteratee2) {
        return array && array.length ? baseSum(array, getIteratee(iteratee2, 2)) : 0;
      }
      lodash2.after = after;
      lodash2.ary = ary;
      lodash2.assign = assign;
      lodash2.assignIn = assignIn;
      lodash2.assignInWith = assignInWith;
      lodash2.assignWith = assignWith;
      lodash2.at = at;
      lodash2.before = before;
      lodash2.bind = bind;
      lodash2.bindAll = bindAll;
      lodash2.bindKey = bindKey;
      lodash2.castArray = castArray;
      lodash2.chain = chain;
      lodash2.chunk = chunk;
      lodash2.compact = compact;
      lodash2.concat = concat;
      lodash2.cond = cond;
      lodash2.conforms = conforms;
      lodash2.constant = constant;
      lodash2.countBy = countBy;
      lodash2.create = create;
      lodash2.curry = curry;
      lodash2.curryRight = curryRight;
      lodash2.debounce = debounce;
      lodash2.defaults = defaults;
      lodash2.defaultsDeep = defaultsDeep;
      lodash2.defer = defer;
      lodash2.delay = delay;
      lodash2.difference = difference;
      lodash2.differenceBy = differenceBy;
      lodash2.differenceWith = differenceWith;
      lodash2.drop = drop;
      lodash2.dropRight = dropRight;
      lodash2.dropRightWhile = dropRightWhile;
      lodash2.dropWhile = dropWhile;
      lodash2.fill = fill;
      lodash2.filter = filter;
      lodash2.flatMap = flatMap;
      lodash2.flatMapDeep = flatMapDeep;
      lodash2.flatMapDepth = flatMapDepth;
      lodash2.flatten = flatten;
      lodash2.flattenDeep = flattenDeep;
      lodash2.flattenDepth = flattenDepth;
      lodash2.flip = flip;
      lodash2.flow = flow;
      lodash2.flowRight = flowRight;
      lodash2.fromPairs = fromPairs;
      lodash2.functions = functions;
      lodash2.functionsIn = functionsIn;
      lodash2.groupBy = groupBy;
      lodash2.initial = initial;
      lodash2.intersection = intersection;
      lodash2.intersectionBy = intersectionBy;
      lodash2.intersectionWith = intersectionWith;
      lodash2.invert = invert;
      lodash2.invertBy = invertBy;
      lodash2.invokeMap = invokeMap;
      lodash2.iteratee = iteratee;
      lodash2.keyBy = keyBy;
      lodash2.keys = keys;
      lodash2.keysIn = keysIn;
      lodash2.map = map;
      lodash2.mapKeys = mapKeys;
      lodash2.mapValues = mapValues;
      lodash2.matches = matches;
      lodash2.matchesProperty = matchesProperty;
      lodash2.memoize = memoize;
      lodash2.merge = merge;
      lodash2.mergeWith = mergeWith;
      lodash2.method = method;
      lodash2.methodOf = methodOf;
      lodash2.mixin = mixin;
      lodash2.negate = negate;
      lodash2.nthArg = nthArg;
      lodash2.omit = omit;
      lodash2.omitBy = omitBy;
      lodash2.once = once;
      lodash2.orderBy = orderBy;
      lodash2.over = over;
      lodash2.overArgs = overArgs;
      lodash2.overEvery = overEvery;
      lodash2.overSome = overSome;
      lodash2.partial = partial;
      lodash2.partialRight = partialRight;
      lodash2.partition = partition;
      lodash2.pick = pick;
      lodash2.pickBy = pickBy;
      lodash2.property = property;
      lodash2.propertyOf = propertyOf;
      lodash2.pull = pull;
      lodash2.pullAll = pullAll;
      lodash2.pullAllBy = pullAllBy;
      lodash2.pullAllWith = pullAllWith;
      lodash2.pullAt = pullAt;
      lodash2.range = range;
      lodash2.rangeRight = rangeRight;
      lodash2.rearg = rearg;
      lodash2.reject = reject;
      lodash2.remove = remove;
      lodash2.rest = rest;
      lodash2.reverse = reverse;
      lodash2.sampleSize = sampleSize;
      lodash2.set = set;
      lodash2.setWith = setWith;
      lodash2.shuffle = shuffle;
      lodash2.slice = slice;
      lodash2.sortBy = sortBy;
      lodash2.sortedUniq = sortedUniq;
      lodash2.sortedUniqBy = sortedUniqBy;
      lodash2.split = split;
      lodash2.spread = spread;
      lodash2.tail = tail;
      lodash2.take = take;
      lodash2.takeRight = takeRight;
      lodash2.takeRightWhile = takeRightWhile;
      lodash2.takeWhile = takeWhile;
      lodash2.tap = tap;
      lodash2.throttle = throttle;
      lodash2.thru = thru;
      lodash2.toArray = toArray;
      lodash2.toPairs = toPairs;
      lodash2.toPairsIn = toPairsIn;
      lodash2.toPath = toPath;
      lodash2.toPlainObject = toPlainObject;
      lodash2.transform = transform;
      lodash2.unary = unary;
      lodash2.union = union;
      lodash2.unionBy = unionBy;
      lodash2.unionWith = unionWith;
      lodash2.uniq = uniq;
      lodash2.uniqBy = uniqBy;
      lodash2.uniqWith = uniqWith;
      lodash2.unset = unset;
      lodash2.unzip = unzip;
      lodash2.unzipWith = unzipWith;
      lodash2.update = update;
      lodash2.updateWith = updateWith;
      lodash2.values = values;
      lodash2.valuesIn = valuesIn;
      lodash2.without = without;
      lodash2.words = words;
      lodash2.wrap = wrap;
      lodash2.xor = xor;
      lodash2.xorBy = xorBy;
      lodash2.xorWith = xorWith;
      lodash2.zip = zip;
      lodash2.zipObject = zipObject;
      lodash2.zipObjectDeep = zipObjectDeep;
      lodash2.zipWith = zipWith;
      lodash2.entries = toPairs;
      lodash2.entriesIn = toPairsIn;
      lodash2.extend = assignIn;
      lodash2.extendWith = assignInWith;
      mixin(lodash2, lodash2);
      lodash2.add = add;
      lodash2.attempt = attempt;
      lodash2.camelCase = camelCase;
      lodash2.capitalize = capitalize;
      lodash2.ceil = ceil;
      lodash2.clamp = clamp;
      lodash2.clone = clone;
      lodash2.cloneDeep = cloneDeep;
      lodash2.cloneDeepWith = cloneDeepWith;
      lodash2.cloneWith = cloneWith;
      lodash2.conformsTo = conformsTo;
      lodash2.deburr = deburr;
      lodash2.defaultTo = defaultTo;
      lodash2.divide = divide;
      lodash2.endsWith = endsWith;
      lodash2.eq = eq;
      lodash2.escape = escape;
      lodash2.escapeRegExp = escapeRegExp;
      lodash2.every = every;
      lodash2.find = find;
      lodash2.findIndex = findIndex;
      lodash2.findKey = findKey;
      lodash2.findLast = findLast;
      lodash2.findLastIndex = findLastIndex;
      lodash2.findLastKey = findLastKey;
      lodash2.floor = floor;
      lodash2.forEach = forEach;
      lodash2.forEachRight = forEachRight;
      lodash2.forIn = forIn;
      lodash2.forInRight = forInRight;
      lodash2.forOwn = forOwn;
      lodash2.forOwnRight = forOwnRight;
      lodash2.get = get;
      lodash2.gt = gt;
      lodash2.gte = gte;
      lodash2.has = has;
      lodash2.hasIn = hasIn;
      lodash2.head = head;
      lodash2.identity = identity;
      lodash2.includes = includes;
      lodash2.indexOf = indexOf;
      lodash2.inRange = inRange;
      lodash2.invoke = invoke;
      lodash2.isArguments = isArguments;
      lodash2.isArray = isArray;
      lodash2.isArrayBuffer = isArrayBuffer;
      lodash2.isArrayLike = isArrayLike;
      lodash2.isArrayLikeObject = isArrayLikeObject;
      lodash2.isBoolean = isBoolean;
      lodash2.isBuffer = isBuffer;
      lodash2.isDate = isDate;
      lodash2.isElement = isElement;
      lodash2.isEmpty = isEmpty;
      lodash2.isEqual = isEqual;
      lodash2.isEqualWith = isEqualWith;
      lodash2.isError = isError;
      lodash2.isFinite = isFinite;
      lodash2.isFunction = isFunction;
      lodash2.isInteger = isInteger;
      lodash2.isLength = isLength;
      lodash2.isMap = isMap;
      lodash2.isMatch = isMatch;
      lodash2.isMatchWith = isMatchWith;
      lodash2.isNaN = isNaN2;
      lodash2.isNative = isNative;
      lodash2.isNil = isNil;
      lodash2.isNull = isNull;
      lodash2.isNumber = isNumber;
      lodash2.isObject = isObject;
      lodash2.isObjectLike = isObjectLike;
      lodash2.isPlainObject = isPlainObject;
      lodash2.isRegExp = isRegExp;
      lodash2.isSafeInteger = isSafeInteger;
      lodash2.isSet = isSet;
      lodash2.isString = isString;
      lodash2.isSymbol = isSymbol;
      lodash2.isTypedArray = isTypedArray;
      lodash2.isUndefined = isUndefined;
      lodash2.isWeakMap = isWeakMap;
      lodash2.isWeakSet = isWeakSet;
      lodash2.join = join;
      lodash2.kebabCase = kebabCase;
      lodash2.last = last;
      lodash2.lastIndexOf = lastIndexOf;
      lodash2.lowerCase = lowerCase;
      lodash2.lowerFirst = lowerFirst;
      lodash2.lt = lt;
      lodash2.lte = lte;
      lodash2.max = max;
      lodash2.maxBy = maxBy;
      lodash2.mean = mean;
      lodash2.meanBy = meanBy;
      lodash2.min = min;
      lodash2.minBy = minBy;
      lodash2.stubArray = stubArray;
      lodash2.stubFalse = stubFalse;
      lodash2.stubObject = stubObject;
      lodash2.stubString = stubString;
      lodash2.stubTrue = stubTrue;
      lodash2.multiply = multiply;
      lodash2.nth = nth;
      lodash2.noConflict = noConflict;
      lodash2.noop = noop;
      lodash2.now = now;
      lodash2.pad = pad;
      lodash2.padEnd = padEnd;
      lodash2.padStart = padStart;
      lodash2.parseInt = parseInt2;
      lodash2.random = random;
      lodash2.reduce = reduce;
      lodash2.reduceRight = reduceRight;
      lodash2.repeat = repeat;
      lodash2.replace = replace;
      lodash2.result = result;
      lodash2.round = round;
      lodash2.runInContext = runInContext2;
      lodash2.sample = sample;
      lodash2.size = size;
      lodash2.snakeCase = snakeCase;
      lodash2.some = some;
      lodash2.sortedIndex = sortedIndex;
      lodash2.sortedIndexBy = sortedIndexBy;
      lodash2.sortedIndexOf = sortedIndexOf;
      lodash2.sortedLastIndex = sortedLastIndex;
      lodash2.sortedLastIndexBy = sortedLastIndexBy;
      lodash2.sortedLastIndexOf = sortedLastIndexOf;
      lodash2.startCase = startCase;
      lodash2.startsWith = startsWith;
      lodash2.subtract = subtract;
      lodash2.sum = sum;
      lodash2.sumBy = sumBy;
      lodash2.template = template;
      lodash2.times = times;
      lodash2.toFinite = toFinite;
      lodash2.toInteger = toInteger;
      lodash2.toLength = toLength;
      lodash2.toLower = toLower;
      lodash2.toNumber = toNumber;
      lodash2.toSafeInteger = toSafeInteger;
      lodash2.toString = toString;
      lodash2.toUpper = toUpper;
      lodash2.trim = trim;
      lodash2.trimEnd = trimEnd;
      lodash2.trimStart = trimStart;
      lodash2.truncate = truncate;
      lodash2.unescape = unescape;
      lodash2.uniqueId = uniqueId;
      lodash2.upperCase = upperCase;
      lodash2.upperFirst = upperFirst;
      lodash2.each = forEach;
      lodash2.eachRight = forEachRight;
      lodash2.first = head;
      mixin(lodash2, function() {
        var source = {};
        baseForOwn(lodash2, function(func, methodName) {
          if (!hasOwnProperty.call(lodash2.prototype, methodName)) {
            source[methodName] = func;
          }
        });
        return source;
      }(), { "chain": false });
      lodash2.VERSION = VERSION;
      arrayEach(["bind", "bindKey", "curry", "curryRight", "partial", "partialRight"], function(methodName) {
        lodash2[methodName].placeholder = lodash2;
      });
      arrayEach(["drop", "take"], function(methodName, index) {
        LazyWrapper.prototype[methodName] = function(n) {
          n = n === undefined$1 ? 1 : nativeMax(toInteger(n), 0);
          var result2 = this.__filtered__ && !index ? new LazyWrapper(this) : this.clone();
          if (result2.__filtered__) {
            result2.__takeCount__ = nativeMin(n, result2.__takeCount__);
          } else {
            result2.__views__.push({
              "size": nativeMin(n, MAX_ARRAY_LENGTH),
              "type": methodName + (result2.__dir__ < 0 ? "Right" : "")
            });
          }
          return result2;
        };
        LazyWrapper.prototype[methodName + "Right"] = function(n) {
          return this.reverse()[methodName](n).reverse();
        };
      });
      arrayEach(["filter", "map", "takeWhile"], function(methodName, index) {
        var type = index + 1, isFilter = type == LAZY_FILTER_FLAG || type == LAZY_WHILE_FLAG;
        LazyWrapper.prototype[methodName] = function(iteratee2) {
          var result2 = this.clone();
          result2.__iteratees__.push({
            "iteratee": getIteratee(iteratee2, 3),
            "type": type
          });
          result2.__filtered__ = result2.__filtered__ || isFilter;
          return result2;
        };
      });
      arrayEach(["head", "last"], function(methodName, index) {
        var takeName = "take" + (index ? "Right" : "");
        LazyWrapper.prototype[methodName] = function() {
          return this[takeName](1).value()[0];
        };
      });
      arrayEach(["initial", "tail"], function(methodName, index) {
        var dropName = "drop" + (index ? "" : "Right");
        LazyWrapper.prototype[methodName] = function() {
          return this.__filtered__ ? new LazyWrapper(this) : this[dropName](1);
        };
      });
      LazyWrapper.prototype.compact = function() {
        return this.filter(identity);
      };
      LazyWrapper.prototype.find = function(predicate) {
        return this.filter(predicate).head();
      };
      LazyWrapper.prototype.findLast = function(predicate) {
        return this.reverse().find(predicate);
      };
      LazyWrapper.prototype.invokeMap = baseRest(function(path, args) {
        if (typeof path == "function") {
          return new LazyWrapper(this);
        }
        return this.map(function(value) {
          return baseInvoke(value, path, args);
        });
      });
      LazyWrapper.prototype.reject = function(predicate) {
        return this.filter(negate(getIteratee(predicate)));
      };
      LazyWrapper.prototype.slice = function(start, end) {
        start = toInteger(start);
        var result2 = this;
        if (result2.__filtered__ && (start > 0 || end < 0)) {
          return new LazyWrapper(result2);
        }
        if (start < 0) {
          result2 = result2.takeRight(-start);
        } else if (start) {
          result2 = result2.drop(start);
        }
        if (end !== undefined$1) {
          end = toInteger(end);
          result2 = end < 0 ? result2.dropRight(-end) : result2.take(end - start);
        }
        return result2;
      };
      LazyWrapper.prototype.takeRightWhile = function(predicate) {
        return this.reverse().takeWhile(predicate).reverse();
      };
      LazyWrapper.prototype.toArray = function() {
        return this.take(MAX_ARRAY_LENGTH);
      };
      baseForOwn(LazyWrapper.prototype, function(func, methodName) {
        var checkIteratee = /^(?:filter|find|map|reject)|While$/.test(methodName), isTaker = /^(?:head|last)$/.test(methodName), lodashFunc = lodash2[isTaker ? "take" + (methodName == "last" ? "Right" : "") : methodName], retUnwrapped = isTaker || /^find/.test(methodName);
        if (!lodashFunc) {
          return;
        }
        lodash2.prototype[methodName] = function() {
          var value = this.__wrapped__, args = isTaker ? [1] : arguments, isLazy = value instanceof LazyWrapper, iteratee2 = args[0], useLazy = isLazy || isArray(value);
          var interceptor = function(value2) {
            var result3 = lodashFunc.apply(lodash2, arrayPush([value2], args));
            return isTaker && chainAll ? result3[0] : result3;
          };
          if (useLazy && checkIteratee && typeof iteratee2 == "function" && iteratee2.length != 1) {
            isLazy = useLazy = false;
          }
          var chainAll = this.__chain__, isHybrid = !!this.__actions__.length, isUnwrapped = retUnwrapped && !chainAll, onlyLazy = isLazy && !isHybrid;
          if (!retUnwrapped && useLazy) {
            value = onlyLazy ? value : new LazyWrapper(this);
            var result2 = func.apply(value, args);
            result2.__actions__.push({ "func": thru, "args": [interceptor], "thisArg": undefined$1 });
            return new LodashWrapper(result2, chainAll);
          }
          if (isUnwrapped && onlyLazy) {
            return func.apply(this, args);
          }
          result2 = this.thru(interceptor);
          return isUnwrapped ? isTaker ? result2.value()[0] : result2.value() : result2;
        };
      });
      arrayEach(["pop", "push", "shift", "sort", "splice", "unshift"], function(methodName) {
        var func = arrayProto[methodName], chainName = /^(?:push|sort|unshift)$/.test(methodName) ? "tap" : "thru", retUnwrapped = /^(?:pop|shift)$/.test(methodName);
        lodash2.prototype[methodName] = function() {
          var args = arguments;
          if (retUnwrapped && !this.__chain__) {
            var value = this.value();
            return func.apply(isArray(value) ? value : [], args);
          }
          return this[chainName](function(value2) {
            return func.apply(isArray(value2) ? value2 : [], args);
          });
        };
      });
      baseForOwn(LazyWrapper.prototype, function(func, methodName) {
        var lodashFunc = lodash2[methodName];
        if (lodashFunc) {
          var key = lodashFunc.name + "";
          if (!hasOwnProperty.call(realNames, key)) {
            realNames[key] = [];
          }
          realNames[key].push({ "name": methodName, "func": lodashFunc });
        }
      });
      realNames[createHybrid(undefined$1, WRAP_BIND_KEY_FLAG).name] = [{
        "name": "wrapper",
        "func": undefined$1
      }];
      LazyWrapper.prototype.clone = lazyClone;
      LazyWrapper.prototype.reverse = lazyReverse;
      LazyWrapper.prototype.value = lazyValue;
      lodash2.prototype.at = wrapperAt;
      lodash2.prototype.chain = wrapperChain;
      lodash2.prototype.commit = wrapperCommit;
      lodash2.prototype.next = wrapperNext;
      lodash2.prototype.plant = wrapperPlant;
      lodash2.prototype.reverse = wrapperReverse;
      lodash2.prototype.toJSON = lodash2.prototype.valueOf = lodash2.prototype.value = wrapperValue;
      lodash2.prototype.first = lodash2.prototype.head;
      if (symIterator) {
        lodash2.prototype[symIterator] = wrapperToIterator;
      }
      return lodash2;
    };
    var _2 = runInContext();
    if (freeModule) {
      (freeModule.exports = _2)._ = _2;
      freeExports._ = _2;
    } else {
      root._ = _2;
    }
  }).call(commonjsGlobal);
})(lodash, lodash.exports);
var _ = lodash.exports;
var Payments_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$9 = { class: "columns" };
const _hoisted_2$8 = { class: "column is-12" };
const _hoisted_3$7 = { class: "table is-hoverable is-fullwidth" };
const _hoisted_4$7 = {
  width: "100%",
  class: "table-payements"
};
const _hoisted_5$6 = {
  key: 0,
  style: { "font-size": "14px" },
  scope: "col"
};
const _hoisted_6$6 = {
  key: 1,
  style: { "font-size": "14px" },
  scope: "col"
};
const _hoisted_7$6 = {
  key: 2,
  style: { "font-size": "14px" },
  scope: "col"
};
const _hoisted_8$5 = { scope: "col" };
const _hoisted_9$5 = {
  class: "select",
  style: { "font-size": "14px" }
};
const _hoisted_10$5 = ["onUpdate:modelValue"];
const _hoisted_11$5 = ["value"];
const _hoisted_12$4 = ["onUpdate:modelValue"];
const _hoisted_13$4 = ["onUpdate:modelValue"];
const _hoisted_14$4 = { class: "series-table-actions text-center" };
const _hoisted_15$4 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_16$4 = /* @__PURE__ */ createTextVNode("Aceptar");
const _sfc_main$9 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true, default: false },
    total: { type: Number, required: true, default: 0 },
    paymentsMethods: { type: Array, required: true, default: () => [] },
    paymentsDestinations: { type: Array, required: true, default: () => [] }
  },
  emits: ["close", "save"],
  setup(__props, { emit }) {
    const props = __props;
    const state = reactive({
      payments: []
    });
    const cash_payment_metod = computed(() => props.paymentsMethods.filter((x) => x.is_credit == 0));
    const getPaymentDestinationId = () => {
      if (props.paymentsDestinations.length > 0) {
        let cash = props.paymentsDestinations.find((x) => x.id == "cash");
        return cash ? cash.id : props.paymentsDestinations[0].id;
      }
      return void 0;
    };
    const clickAddPayment = () => {
      let id = "01";
      if (cash_payment_metod.value !== void 0 && cash_payment_metod.value[0] !== void 0) {
        id = cash_payment_metod.value[0].id;
      }
      let total = 0;
      if (props.total !== void 0) {
        total = props.total;
      }
      state.payments.push({
        date_of_payment: dayjs().format("YYYY-MM-DD"),
        payment_method_type_id: id,
        reference: "",
        payment_destination_id: getPaymentDestinationId(),
        payment: total
      });
      calculatePayments(true);
    };
    const calculatePayments = (calculate = false) => {
      let payment_count = state.payments.length;
      let total = props.total;
      let payment = 0;
      let amount = _.round(total / payment_count, 2);
      _.forEach(state.payments, (row) => {
        var _a;
        if (row.payment == amount || calculate) {
          payment += amount;
          if (total - payment < 0) {
            amount = _.round(total - payment + amount, 2);
          }
          row.payment = amount;
        }
        row.payment_method_type_name = (_a = props.paymentsMethods.find((x) => x.id == row.payment_method_type_id)) == null ? void 0 : _a.description;
      });
    };
    const clickCancel = (index) => {
      state.payments.splice(index, 1);
      calculatePayments(true);
    };
    const save = () => {
      calculatePayments();
      emit("save", state.payments);
    };
    const cancel = () => {
      emit("save", state.payments);
    };
    onMounted(() => {
    });
    watch(() => props.open, (isOpen) => {
      if (isOpen && state.payments.length < 1) {
        clickAddPayment();
      }
    });
    return (_ctx, _cache) => {
      const _component_VIconButton = _sfc_main$k;
      const _component_VLoader = _sfc_main$d;
      const _component_VButton = _sfc_main$i;
      const _component_VModal = _sfc_main$j;
      return openBlock(), createBlock(_component_VModal, {
        open: props.open,
        title: "Pagos",
        size: "big",
        actions: "right",
        "cancel-label": "Cancelar",
        noclose: "",
        cascade: true,
        onClose: cancel
      }, {
        content: withCtx(() => [
          createVNode(_component_VLoader, {
            size: "large",
            active: unref(state).loading,
            translucent: ""
          }, {
            default: withCtx(() => [
              createBaseVNode("div", _hoisted_1$9, [
                createBaseVNode("div", _hoisted_2$8, [
                  createBaseVNode("table", _hoisted_3$7, [
                    createBaseVNode("thead", null, [
                      createBaseVNode("tr", _hoisted_4$7, [
                        unref(state).payments.length > 0 ? (openBlock(), createElementBlock("th", _hoisted_5$6, " M\xE9todo de pago ")) : createCommentVNode("", true),
                        unref(state).payments.length > 0 ? (openBlock(), createElementBlock("th", _hoisted_6$6, " Referencia ")) : createCommentVNode("", true),
                        unref(state).payments.length > 0 ? (openBlock(), createElementBlock("th", _hoisted_7$6, " Monto ")) : createCommentVNode("", true),
                        createBaseVNode("th", _hoisted_8$5, [
                          createVNode(_component_VIconButton, {
                            color: "success",
                            outlined: "",
                            circle: "",
                            icon: "feather:plus",
                            onClick: _cache[0] || (_cache[0] = withModifiers(($event) => clickAddPayment(), ["prevent"]))
                          })
                        ])
                      ])
                    ]),
                    createBaseVNode("tbody", null, [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(state).payments, (row, index) => {
                        return openBlock(), createElementBlock("tr", {
                          key: index,
                          class: "payments-metod-tr column-table-payment"
                        }, [
                          createBaseVNode("td", null, [
                            createBaseVNode("div", _hoisted_9$5, [
                              withDirectives(createBaseVNode("select", {
                                "onUpdate:modelValue": ($event) => row.payment_method_type_id = $event
                              }, [
                                (openBlock(true), createElementBlock(Fragment, null, renderList(props.paymentsMethods, (item) => {
                                  return openBlock(), createElementBlock("option", {
                                    key: item.id + "MT",
                                    value: item.id
                                  }, toDisplayString(item.description), 9, _hoisted_11$5);
                                }), 128))
                              ], 8, _hoisted_10$5), [
                                [vModelSelect, row.payment_method_type_id]
                              ])
                            ])
                          ]),
                          createBaseVNode("td", null, [
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": ($event) => row.reference = $event,
                              style: { "font-size": "14px" },
                              type: "text",
                              class: "input"
                            }, null, 8, _hoisted_12$4), [
                              [vModelText, row.reference]
                            ])
                          ]),
                          createBaseVNode("td", null, [
                            withDirectives(createBaseVNode("input", {
                              "onUpdate:modelValue": ($event) => row.payment = $event,
                              type: "number",
                              class: "input",
                              style: { "font-size": "14px" }
                            }, null, 8, _hoisted_13$4), [
                              [vModelText, row.payment]
                            ])
                          ]),
                          createBaseVNode("td", _hoisted_14$4, [
                            createVNode(_component_VIconButton, {
                              color: "danger",
                              light: "",
                              raised: "",
                              circle: "",
                              icon: "feather:x",
                              onClick: withModifiers(($event) => clickCancel(index), ["prevent"])
                            }, null, 8, ["onClick"])
                          ]),
                          _hoisted_15$4
                        ]);
                      }), 128))
                    ])
                  ])
                ])
              ])
            ]),
            _: 1
          }, 8, ["active"])
        ]),
        action: withCtx(() => [
          createVNode(_component_VButton, {
            color: "primary",
            onClick: save
          }, {
            default: withCtx(() => [
              _hoisted_16$4
            ]),
            _: 1
          })
        ]),
        _: 1
      }, 8, ["open"]);
    };
  }
});
var DocumentDialog_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$8 = { class: "columns" };
const _hoisted_2$7 = { class: "column container-payment-all is-6" };
const _hoisted_3$6 = { class: "is-flex is-justify-content-space-between is-align-items-center p-0" };
const _hoisted_4$6 = { class: "is-flex is-justify-content-center p-0 content-buttons-payment-all" };
const _hoisted_5$5 = /* @__PURE__ */ createTextVNode(" N. Venta ");
const _hoisted_6$5 = /* @__PURE__ */ createTextVNode(" Boleta ");
const _hoisted_7$5 = /* @__PURE__ */ createTextVNode(" Factura ");
const _hoisted_8$4 = { class: "column pl-5 pr-0 type-document" };
const _hoisted_9$4 = {
  class: "select",
  placeholder: "Seleccione serie"
};
const _hoisted_10$4 = ["disabled"];
const _hoisted_11$4 = ["value"];
const _hoisted_12$3 = { class: "p-0 clients-content" };
const _hoisted_13$3 = { class: "column p-0 client-select" };
const _hoisted_14$3 = { style: { "width": "100%" } };
const _hoisted_15$3 = ["disabled"];
const _hoisted_16$3 = {
  class: "dropdown-menu",
  role: "menu"
};
const _hoisted_17$3 = { class: "dropdown-content" };
const _hoisted_18$3 = ["onClick"];
const _hoisted_19$3 = {
  key: 0,
  class: "dropdown-item"
};
const _hoisted_20$3 = { class: "add-client" };
const _hoisted_21$2 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-plus",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_22$2 = { class: "has-text-centered" };
const _hoisted_23$2 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-1" }, "Monto a cobrar", -1);
const _hoisted_24$2 = { class: "is-size-3 mt-1 mb-0" };
const _hoisted_25$2 = { class: "mt-1" };
const _hoisted_26$2 = /* @__PURE__ */ createTextVNode(" Vuelto: ");
const _hoisted_27$2 = { class: "field" };
const _hoisted_28$2 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "Ingrese monto", -1);
const _hoisted_29$2 = { class: "field has-addons" };
const _hoisted_30$2 = { class: "control" };
const _hoisted_31$1 = { class: "select currency-select" };
const _hoisted_32$1 = ["disabled"];
const _hoisted_33$1 = /* @__PURE__ */ createBaseVNode("option", null, "S/", -1);
const _hoisted_34$1 = [
  _hoisted_33$1
];
const _hoisted_35$1 = { class: "control is-expanded" };
const _hoisted_36$1 = ["readonly", "disabled"];
const _hoisted_37 = { class: "is-flex is-justify-content-center" };
const _hoisted_38 = /* @__PURE__ */ createTextVNode(" S/10 ");
const _hoisted_39 = /* @__PURE__ */ createTextVNode(" S/20 ");
const _hoisted_40 = /* @__PURE__ */ createTextVNode(" S/50 ");
const _hoisted_41 = /* @__PURE__ */ createTextVNode(" S/100 ");
const _hoisted_42 = /* @__PURE__ */ createTextVNode(" Exacto ");
const _hoisted_43 = { class: "mb-3" };
const _hoisted_44 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-check-circle",
  "aria-hidden": "true"
}, null, -1);
const _hoisted_45 = /* @__PURE__ */ createBaseVNode("strong", { class: "p-2 has-text-primary" }, "Venta realizada con \xE9xito", -1);
const _hoisted_46 = /* @__PURE__ */ createTextVNode(" Nueva Venta ");
const _hoisted_47 = { class: "mb-3 card card-content p-2" };
const _hoisted_48 = { class: "table is-hoverable is-fullwidth" };
const _hoisted_49 = { class: "thead-border-bottom" };
const _hoisted_50 = /* @__PURE__ */ createBaseVNode("th", { class: "is-vcentered" }, "Pagos agregados:", -1);
const _hoisted_51 = { class: "has-text-right" };
const _hoisted_52 = /* @__PURE__ */ createTextVNode(" + Pagos ");
const _hoisted_53 = { class: "has-text-right" };
const _hoisted_54 = { class: "field is-justify-content-center" };
const _hoisted_55 = /* @__PURE__ */ createBaseVNode("p", { class: "mb-0" }, "N\xB0 Placa:", -1);
const _hoisted_56 = { class: "field has-addons p-2" };
const _hoisted_57 = { class: "control is-expanded" };
const _hoisted_58 = ["disabled"];
const _hoisted_59 = /* @__PURE__ */ createTextVNode(" Finalizar Venta ");
const _hoisted_60 = /* @__PURE__ */ createTextVNode(" Volver a imprimir ");
const _hoisted_61 = /* @__PURE__ */ createTextVNode(" Ver formato PDF ");
const _hoisted_62 = { key: 0 };
const _hoisted_63 = ["src"];
const _hoisted_64 = { key: 1 };
const _hoisted_65 = {
  key: 0,
  class: "has-text-centered"
};
const _hoisted_66 = ["src"];
const _hoisted_67 = { class: "has-text-centered is-uppercase" };
const _hoisted_68 = { class: "has-text-centered" };
const _hoisted_69 = {
  key: 1,
  class: "has-text-centered"
};
const _hoisted_70 = {
  key: 2,
  class: "has-text-centered"
};
const _hoisted_71 = {
  key: 3,
  class: "has-text-centered"
};
const _hoisted_72 = /* @__PURE__ */ createBaseVNode("hr", null, null, -1);
const _hoisted_73 = { class: "has-text-centered" };
const _hoisted_74 = {
  key: 4,
  class: "has-text-centered"
};
const _hoisted_75 = /* @__PURE__ */ createBaseVNode("hr", null, null, -1);
const _hoisted_76 = {
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_77 = {
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_78 = {
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_79 = {
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_80 = {
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_81 = {
  key: 5,
  style: { "font-size": "0.65rem" },
  class: "m-0"
};
const _hoisted_82 = { class: "table-container" };
const _hoisted_83 = {
  class: "table has-background-white",
  style: { "font-size": "0.65rem" }
};
const _hoisted_84 = /* @__PURE__ */ createBaseVNode("thead", { style: { "font-size": "0.5rem" } }, [
  /* @__PURE__ */ createBaseVNode("tr", null, [
    /* @__PURE__ */ createBaseVNode("th", null, "COD."),
    /* @__PURE__ */ createBaseVNode("th", null, "CANT."),
    /* @__PURE__ */ createBaseVNode("th", null, "UNIDAD"),
    /* @__PURE__ */ createBaseVNode("th", null, "DESCRIPCI\xD3N"),
    /* @__PURE__ */ createBaseVNode("th", null, "P.UNIT"),
    /* @__PURE__ */ createBaseVNode("th", null, "TOTAL")
  ])
], -1);
const _hoisted_85 = /* @__PURE__ */ createBaseVNode("td", null, "NIU", -1);
const _hoisted_86 = { class: "has-text-right" };
const _hoisted_87 = { class: "has-text-right" };
const _hoisted_88 = /* @__PURE__ */ createBaseVNode("th", {
  style: { "text-align": "right" },
  colspan: "4"
}, " TOTAL A PAGAR: ", -1);
const _hoisted_89 = { class: "has-text-right" };
const _hoisted_90 = {
  class: "table has-background-white",
  style: { "font-size": "0.6rem" }
};
const _hoisted_91 = /* @__PURE__ */ createBaseVNode("tr", null, [
  /* @__PURE__ */ createBaseVNode("th", {
    style: { "text-align": "left" },
    width: "45%"
  }, " Condici\xF3n de pago: "),
  /* @__PURE__ */ createBaseVNode("th", null, "Contado")
], -1);
const _hoisted_92 = /* @__PURE__ */ createBaseVNode("th", { style: { "text-align": "left" } }, "Pago:", -1);
const _hoisted_93 = /* @__PURE__ */ createBaseVNode("th", { style: { "text-align": "left" } }, "Vuelto:", -1);
const _hoisted_94 = /* @__PURE__ */ createBaseVNode("th", { style: { "text-align": "left" } }, "Vendedor:", -1);
const _hoisted_95 = { key: 0 };
const _hoisted_96 = /* @__PURE__ */ createBaseVNode("th", null, "Son:", -1);
const _hoisted_97 = { key: 1 };
const _hoisted_98 = {
  colspan: "2",
  style: { "text-align": "center" }
};
const _hoisted_99 = ["src"];
const _hoisted_100 = { key: 2 };
const _hoisted_101 = {
  style: { "text-align": "center" },
  colspan: "2"
};
const _sfc_main$8 = /* @__PURE__ */ defineComponent({
  props: {
    open: { type: Boolean, required: true, default: false },
    posBag: { type: null, required: true, default: Object }
  },
  emits: ["close", "open", "back"],
  setup(__props, { emit }) {
    const props = __props;
    const cashSession = useCashSession();
    const isQzWebsocketActive = ref(false);
    const previewDocument = ref(null);
    const userSession2 = useUserSession();
    const companySession2 = reactive(useCompanySession());
    userSession2.printerNameDocument;
    const isBusinessTurnTap = ref(userSession2.isBusinessTurnTap ? true : false);
    const visibleButton = ref(true);
    const responseApiData = {
      external_id: "",
      filename: "",
      hash: "",
      number: "",
      number_to_letter: "",
      qr: "",
      state_type_description: "",
      state_type_id: ""
    };
    const currentData = reactive(__spreadValues({}, responseApiData));
    const date = dayjs().format("YYYY-MM-DD");
    const dateOfDue = dayjs().add(1, "month").format("YYYY-MM-DD");
    const time2 = dayjs().format("HH:mm:ss");
    const ticketPdf80 = ref("");
    const ticketPdf80base64 = ref();
    const companyLogo = userSession2.urlLogo;
    userSession2.logoBase64;
    const searchQuery = ref("");
    const dropdownOpen = ref(false);
    ({
      full_url: userSession2.ssl + userSession2.url
    });
    const route = useRoute();
    const nameRoute = computed(() => route.name);
    const isOnLine = ref(navigator.onLine);
    const isOnLineDescription = ref(navigator.onLine ? "ONLINE" : "OFFLINE");
    const notif = useNotyf();
    const customerDefault = companySession2.getCustomerDefault();
    const paymentsMethods = companySession2.paymentsMethods;
    const paymentsDestinations = companySession2.paymentsDestinations;
    const state = reactive({
      documentSelected: "NOTA",
      posBag: props.posBag,
      loading: false,
      urlDocument: "",
      isPreviewDocument: false,
      titleClose: "Volver",
      customers: companySession2.customers,
      customer: customerDefault,
      series: companySession2.series,
      serie: {},
      openDialogCustomer: false,
      payments: [],
      openDialogPayments: false,
      cashAvailable: true,
      plate_number: ""
    });
    const isFirstAmount = ref(true);
    const addAnterAmount = (amount) => {
      var _a;
      if (state.payments.length > 1) {
        return;
      }
      if (isFirstAmount.value) {
        const enterAmount = Number(amount);
        state.posBag.enterAmount = enterAmount;
        isFirstAmount.value = false;
      } else {
        const enterAmount = Number((_a = state.posBag.enterAmount) != null ? _a : 0) + Number(amount);
        state.posBag.enterAmount = enterAmount;
      }
    };
    const addExactAmount = (amount) => {
      if (state.payments.length > 1) {
        return;
      }
      const exactAmount = Number(state.posBag.total);
      state.posBag.enterAmount = exactAmount;
    };
    const isCpe = computed(() => {
      return state.documentSelected == "BOLETA" || state.documentSelected == "FACTURA";
    });
    const payment = async () => {
      if (!state.serie.number) {
        return notif.error("Debe seleccionar una serie.");
      }
      if (isCpe.value) {
        if (state.documentSelected == "BOLETA") {
          const codigo_tipo_documento_identidad_permitidos_boletas = ["6", "1"];
          if (state.posBag.total > 700 && !codigo_tipo_documento_identidad_permitidos_boletas.includes(state.customer.codigo_tipo_documento_identidad)) {
            return notif.error("No puede superar el monto de 700.0");
          }
        }
        if (state.documentSelected == "FACTURA") {
          const codigo_tipo_documento_identidad_permitidos_facturas = ["6"];
          if (!codigo_tipo_documento_identidad_permitidos_facturas.includes(state.customer.codigo_tipo_documento_identidad)) {
            return notif.error("El tipo doc. identidad del cliente no es v\xE1lido");
          }
        }
      }
      state.cashAvailable = await cashOpeningCheck();
      if (!state.cashAvailable) {
        return notif.error("Debe aperturar una caja");
      }
      state.loading = true;
      state.titleClose = "Cerrar";
      visibleButton.value = false;
      state.loading = false;
      const type_documents_cpe = ["BOLETA", "FACTURA"];
      const network = navigator.onLine ? true : false;
      if (state.documentSelected == "NOTA") {
        if (!network) {
          const payload = getSaleNotePayload(state.posBag.products, state.customer, state.serie, date, state.payments, state.plate_number);
          cashSession.addSaleNoteToCash(payload, nameRoute.value);
          notif.success("Se guard\xF3 el comprobante.");
          state.loading = false;
        } else {
          await generatePdfSaleNote();
        }
      } else if (type_documents_cpe.includes(state.documentSelected)) {
        if (!network) {
          const payload = getDocumentPayload(state.posBag.products, state.customer, {
            serie: state.serie.number,
            date,
            documentSelected: state.documentSelected,
            dateOfDue,
            plate_number: state.plate_number
          }, state.payments);
          cashSession.addDocumentToCash(payload, nameRoute.value);
          notif.success("Se guard\xF3 el comprobante.");
          state.loading = false;
        } else {
          await generatePdfDocument();
        }
      }
      saveSalesStorage(state.posBag);
    };
    const cashOpeningCheck = async () => {
      const id = userSession2.getCashId();
      try {
        const response = await provideApi().get(`/cash/opening_cash_check/${id}`);
        const data = response.data;
        if (!data.success) {
          userSession2.setCashId(0);
          return false;
        }
        return true;
      } catch (error) {
        userSession2.setCashId(0);
        return false;
      }
    };
    const autoPrintPdf = () => {
      userSession2.setPrinterName(userSession2.printerNameDocument);
      let config = getUpdatedConfig();
      let printData = [
        {
          type: "pixel",
          format: "pdf",
          flavor: "base64",
          data: ticketPdf80base64.value,
          options: config
        }
      ];
      qz.print(config, printData).catch(displayError);
    };
    const blobToBase64 = (blob) => {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result.split(",")[1]);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
      });
    };
    const generatePdfDocument = async () => {
      const payload = getDocumentPayload(state.posBag.products, state.customer, {
        serie: state.serie.number,
        date,
        documentSelected: state.documentSelected,
        dateOfDue,
        plate_number: state.plate_number
      }, state.payments);
      const { data } = await provideApi().post("/documents", payload);
      setCurrentData(data.data);
      if (data.success) {
        axios.get(data.data.print_ticket, {
          responseType: "blob"
        }).then((response) => {
          const pdfBlob = new Blob([response.data], { type: "application/pdf" });
          ticketPdf80.value = URL.createObjectURL(pdfBlob);
          return blobToBase64(pdfBlob);
        }).then((base64Data) => {
          ticketPdf80base64.value = base64Data;
          autoPrintPdf();
        }).catch((error) => {
          console.error("Error al descargar el PDF:", error);
        });
        const cash_payload = {
          cash_id: userSession2.getCashId(),
          document_id: data.data.id,
          sale_note_id: null
        };
        provideApi().post("/cash/cash_document", cash_payload);
      }
    };
    const setCurrentData = (data) => {
      currentData.external_id = data.external_id;
      currentData.filename = data.filename ? data.filename : "";
      currentData.hash = data.hash ? data.hash : "";
      currentData.number = data.number;
      currentData.number_to_letter = data.number_to_letter ? data.number_to_letter : "";
      currentData.qr = data.qr ? data.qr : "";
      currentData.state_type_description = data.state_type_description ? data.state_type_description : "";
      currentData.state_type_id = data.state_type_id ? data.state_type_id : "";
    };
    const generatePdfSaleNote = async () => {
      state.serie.id;
      const payload = getSaleNotePayload(state.posBag.products, state.customer, state.serie, date, state.payments, state.plate_number);
      const { data } = await provideApi().post("/sale-note", payload);
      setCurrentData(data.data);
      if (data.success) {
        axios.get(data.data.print_ticket, {
          responseType: "blob"
        }).then((response) => {
          const pdfBlob = new Blob([response.data], { type: "application/pdf" });
          ticketPdf80.value = URL.createObjectURL(pdfBlob);
          return blobToBase64(pdfBlob);
        }).then((base64Data) => {
          ticketPdf80base64.value = base64Data;
          autoPrintPdf();
        }).catch((error) => {
          console.error("Error al descargar el PDF:", error);
        });
        const cash_payload = {
          cash_id: userSession2.getCashId(),
          document_id: null,
          sale_note_id: data.data.id
        };
        provideApi().post("/cash/cash_document", cash_payload);
      }
    };
    const saveSalesStorage = (item) => {
      var _a;
      const ventas = JSON.parse((_a = localStorage.getItem("ventas")) != null ? _a : "[]");
      ventas.push(item);
      localStorage.setItem("ventas", JSON.stringify(ventas));
    };
    const resetData = () => {
      Object.assign(currentData, responseApiData);
    };
    const close = () => {
      URL.revokeObjectURL(ticketPdf80.value);
      ticketPdf80.value = "";
      ticketPdf80base64.value = null;
      if (visibleButton.value == false) {
        confirmClose();
        selectDefaultCustomer();
        return;
      }
      back();
    };
    const confirmClose = () => {
      state.isPreviewDocument = false;
      visibleButton.value = true;
      state.titleClose = "Volver";
      isFirstAmount.value = true;
      resetData();
      emit("close");
    };
    const back = () => {
      state.titleClose = "Volver";
      emit("back");
    };
    const rePrint = () => {
      if (state.documentSelected == "NOTA") {
        autoPrintPdf();
      } else if (state.documentSelected == "BOLETA" || state.documentSelected == "FACTURA") {
        autoPrintPdf();
      }
    };
    const showPdf = () => {
      state.isPreviewDocument = true;
    };
    const series = computed(() => {
      const document2 = state.documentSelected;
      let values = [];
      switch (document2) {
        case "BOLETA":
          values = state.series.filter((x) => x.document_type_id == "03");
          break;
        case "FACTURA":
          values = state.series.filter((x) => x.document_type_id == "01");
          break;
        case "NOTA":
          values = state.series.filter((x) => x.document_type_id == "80");
          break;
      }
      state.serie = values[0];
      return values;
    });
    computed(() => {
      const document2 = state.documentSelected;
      let values = [];
      switch (document2) {
        case "BOLETA":
          values = state.customers.filter((x) => x.codigo_tipo_documento_identidad == "1" || x.codigo_tipo_documento_identidad == "6" || x.codigo_tipo_documento_identidad == "0");
          state.customer = customerDefault;
          break;
        case "FACTURA":
          values = state.customers.filter((x) => x.codigo_tipo_documento_identidad == "6");
          if (values.length) {
            state.customer = values[0];
          } else {
            return notif.error("No existe clientes disponibles para Factura.");
          }
          break;
        case "NOTA":
          values = state.customers;
          state.customer = customerDefault;
          break;
      }
      return values;
    });
    const openModalFormCustomer = () => {
      state.openDialogCustomer = true;
    };
    const successCustomer = (id) => {
      state.openDialogCustomer = false;
      let findCustomer = state.customers.find((x) => x.id == id);
      if (findCustomer) {
        state.customer = findCustomer;
        searchQuery.value = findCustomer.apellidos_y_nombres_o_razon_social;
        dropdownOpen.value = false;
        if (findCustomer.codigo_tipo_documento_identidad == "1") {
          state.documentSelected = "BOLETA";
        } else if (findCustomer.codigo_tipo_documento_identidad == "6") {
          state.documentSelected = "FACTURA";
        }
      }
    };
    const cancelCustomer = () => {
      state.openDialogCustomer = false;
    };
    const showPaymentMethods = () => {
      state.openDialogPayments = true;
    };
    const closeDialogPayments = () => {
      state.openDialogPayments = false;
    };
    const savePayments = (data) => {
      state.payments = data;
      closeDialogPayments();
      if (state.payments.length > 0) {
        state.posBag.enterAmount = _.round(state.payments.reduce((total, payment2) => {
          return total + payment2.payment;
        }, 0), 2);
      }
    };
    const initPaymentDefault = () => {
      const defaultPayment = {
        date_of_payment: dayjs().format("YYYY-MM-DD"),
        payment_method_type_id: "01",
        payment_method_type_name: "Efectivo",
        reference: "",
        payment_destination_id: "cash",
        payment: state.posBag.total || 0
      };
      state.payments.push(defaultPayment);
    };
    const handleEnterAmountChange = () => {
      if (state.payments.length == 1) {
        const amount = parseFloat(state.posBag.enterAmount);
        if (!isNaN(amount)) {
          state.payments[0].payment = amount;
        }
      }
    };
    const filteredCustomers = computed(() => {
      if (!searchQuery.value)
        return state.customers;
      return state.customers.filter((customer) => customer.apellidos_y_nombres_o_razon_social.toLowerCase().includes(searchQuery.value.toLowerCase()) || customer.numero_documento.includes(searchQuery.value));
    });
    const selectCustomer = (customer) => {
      state.customer = customer;
      searchQuery.value = customer.apellidos_y_nombres_o_razon_social;
      dropdownOpen.value = false;
    };
    const focusSelect = () => {
      searchQuery.value = "";
      dropdownOpen.value = true;
    };
    const selectDefaultCustomer = () => {
      state.customer = customerDefault;
      searchQuery.value = customerDefault.apellidos_y_nombres_o_razon_social;
      dropdownOpen.value = false;
    };
    const handleBlur = () => {
      setTimeout(() => {
        let customer = state.customer;
        state.customer = customer;
        searchQuery.value = customer.apellidos_y_nombres_o_razon_social;
        dropdownOpen.value = false;
      }, 200);
    };
    watch(() => props.open, (isOpen) => {
      if (isOpen) {
        state.payments = [];
        initPaymentDefault();
        state.posBag.enterAmount = 0;
        addAnterAmount(state.posBag.total);
        isFirstAmount.value = true;
      }
    });
    function setOnlineStatus() {
      isOnLine.value = navigator.onLine;
      isOnLineDescription.value = navigator.onLine ? "ONLINE" : "OFFLINE";
    }
    onMounted(() => {
      startConnection();
      isQzWebsocketActive.value = qz.websocket.isActive();
      selectDefaultCustomer();
      window.addEventListener("online", setOnlineStatus);
      window.addEventListener("offline", setOnlineStatus);
    });
    onBeforeUnmount(() => {
      window.removeEventListener("online", setOnlineStatus);
      window.removeEventListener("offline", setOnlineStatus);
    });
    return (_ctx, _cache) => {
      const _component_CustomerFormDialog = _sfc_main$c;
      const _component_VButton = _sfc_main$i;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$h;
      const _component_VButtons = _sfc_main$l;
      const _component_VIconBox = _sfc_main$b;
      const _component_VFlex = _sfc_main$a;
      const _component_VLoader = _sfc_main$d;
      const _component_VModal = _sfc_main$j;
      return openBlock(), createElementBlock("div", null, [
        createVNode(_sfc_main$9, {
          total: unref(state).posBag.enterAmount,
          open: unref(state).openDialogPayments,
          "payments-methods": unref(paymentsMethods),
          "payments-destinations": unref(paymentsDestinations),
          onSave: savePayments
        }, null, 8, ["total", "open", "payments-methods", "payments-destinations"]),
        createVNode(_component_CustomerFormDialog, {
          open: unref(state).openDialogCustomer,
          onSave: successCustomer,
          onCancel: cancelCustomer
        }, null, 8, ["open"]),
        createVNode(_component_VModal, {
          open: props.open,
          title: "Comprobante de pago",
          size: "big",
          actions: "right",
          "cancel-label": unref(state).titleClose,
          noclose: "",
          onClose: close
        }, {
          content: withCtx(() => [
            createVNode(_component_VLoader, {
              size: "large",
              active: unref(state).loading,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", _hoisted_1$8, [
                  createBaseVNode("div", _hoisted_2$7, [
                    createBaseVNode("div", _hoisted_3$6, [
                      createBaseVNode("div", _hoisted_4$6, [
                        createVNode(_component_VField, {
                          class: "content-buttons-payment",
                          addons: ""
                        }, {
                          default: withCtx(() => [
                            createVNode(_component_VControl, { class: "btn-container" }, {
                              default: withCtx(() => [
                                createVNode(_component_VButton, {
                                  class: "btn-state",
                                  disabled: !visibleButton.value,
                                  color: unref(state).documentSelected == "NOTA" ? "primary" : "light",
                                  onClick: _cache[0] || (_cache[0] = ($event) => unref(state).documentSelected = "NOTA")
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_5$5
                                  ]),
                                  _: 1
                                }, 8, ["disabled", "color"])
                              ]),
                              _: 1
                            }),
                            createVNode(_component_VControl, { class: "btn-container" }, {
                              default: withCtx(() => [
                                createVNode(_component_VButton, {
                                  class: "btn-state",
                                  disabled: !visibleButton.value,
                                  color: unref(state).documentSelected == "BOLETA" ? "primary" : "light",
                                  onClick: _cache[1] || (_cache[1] = ($event) => unref(state).documentSelected = "BOLETA")
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_6$5
                                  ]),
                                  _: 1
                                }, 8, ["disabled", "color"])
                              ]),
                              _: 1
                            }),
                            createVNode(_component_VControl, { class: "btn-container" }, {
                              default: withCtx(() => [
                                createVNode(_component_VButton, {
                                  class: "btn-state",
                                  disabled: !visibleButton.value,
                                  color: unref(state).documentSelected == "FACTURA" ? "primary" : "light",
                                  onClick: _cache[2] || (_cache[2] = ($event) => unref(state).documentSelected = "FACTURA")
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_7$5
                                  ]),
                                  _: 1
                                }, 8, ["disabled", "color"])
                              ]),
                              _: 1
                            })
                          ]),
                          _: 1
                        })
                      ]),
                      createBaseVNode("div", _hoisted_8$4, [
                        createVNode(_component_VField, { class: "select-container" }, {
                          default: withCtx(() => [
                            createVNode(_component_VControl, null, {
                              default: withCtx(() => [
                                createBaseVNode("div", _hoisted_9$4, [
                                  withDirectives(createBaseVNode("select", {
                                    "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => unref(state).serie = $event),
                                    disabled: !visibleButton.value
                                  }, [
                                    (openBlock(true), createElementBlock(Fragment, null, renderList(unref(series), (item, index) => {
                                      return openBlock(), createElementBlock("option", {
                                        key: index + "Serie",
                                        value: item
                                      }, toDisplayString(item.number), 9, _hoisted_11$4);
                                    }), 128))
                                  ], 8, _hoisted_10$4), [
                                    [vModelSelect, unref(state).serie]
                                  ])
                                ])
                              ]),
                              _: 1
                            })
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createBaseVNode("div", _hoisted_12$3, [
                      createBaseVNode("div", _hoisted_13$3, [
                        createVNode(_component_VField, null, {
                          default: withCtx(() => [
                            createVNode(_component_VControl, null, {
                              default: withCtx(() => [
                                createBaseVNode("div", {
                                  class: normalizeClass(["dropdown", { "is-active": dropdownOpen.value }]),
                                  style: { "width": "100%" }
                                }, [
                                  createBaseVNode("div", _hoisted_14$3, [
                                    withDirectives(createBaseVNode("input", {
                                      "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => searchQuery.value = $event),
                                      class: "input",
                                      style: { "margin-left": "0px !important", "padding-left": "10px !important" },
                                      placeholder: "Buscar cliente",
                                      disabled: !visibleButton.value,
                                      onFocus: _cache[5] || (_cache[5] = ($event) => focusSelect()),
                                      onInput: _cache[6] || (_cache[6] = ($event) => dropdownOpen.value = true),
                                      onBlur: _cache[7] || (_cache[7] = ($event) => handleBlur())
                                    }, null, 40, _hoisted_15$3), [
                                      [vModelText, searchQuery.value]
                                    ])
                                  ]),
                                  createBaseVNode("div", _hoisted_16$3, [
                                    createBaseVNode("div", _hoisted_17$3, [
                                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(filteredCustomers), (customer) => {
                                        return openBlock(), createElementBlock("a", {
                                          key: customer.numero_documento,
                                          class: "dropdown-item",
                                          onClick: ($event) => selectCustomer(customer)
                                        }, toDisplayString(customer.numero_documento + " - " + customer.apellidos_y_nombres_o_razon_social), 9, _hoisted_18$3);
                                      }), 128)),
                                      unref(filteredCustomers).length === 0 ? (openBlock(), createElementBlock("p", _hoisted_19$3, " No se encontraron clientes ")) : createCommentVNode("", true)
                                    ])
                                  ])
                                ], 2)
                              ]),
                              _: 1
                            })
                          ]),
                          _: 1
                        })
                      ]),
                      createBaseVNode("div", _hoisted_20$3, [
                        createVNode(_component_VControl, null, {
                          default: withCtx(() => [
                            createVNode(_component_VButton, {
                              disabled: !visibleButton.value,
                              color: "primary",
                              style: { "margin-left": "auto !important" },
                              onClick: _cache[8] || (_cache[8] = ($event) => openModalFormCustomer())
                            }, {
                              default: withCtx(() => [
                                _hoisted_21$2
                              ]),
                              _: 1
                            }, 8, ["disabled"])
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createBaseVNode("div", _hoisted_22$2, [
                      _hoisted_23$2,
                      createBaseVNode("p", _hoisted_24$2, " S/ " + toDisplayString(unref(state).posBag.total.toFixed(2)), 1),
                      createBaseVNode("p", _hoisted_25$2, [
                        _hoisted_26$2,
                        createBaseVNode("span", null, "S/ " + toDisplayString((unref(state).posBag.enterAmount - unref(state).posBag.total).toFixed(2) > 0 ? (unref(state).posBag.enterAmount - unref(state).posBag.total).toFixed(2) : "0.00"), 1)
                      ])
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_27$2, [
                      _hoisted_28$2,
                      createBaseVNode("div", _hoisted_29$2, [
                        createBaseVNode("div", _hoisted_30$2, [
                          createBaseVNode("span", _hoisted_31$1, [
                            createBaseVNode("select", {
                              disabled: !visibleButton.value
                            }, _hoisted_34$1, 8, _hoisted_32$1)
                          ])
                        ]),
                        createBaseVNode("div", _hoisted_35$1, [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[9] || (_cache[9] = ($event) => unref(state).posBag.enterAmount = $event),
                            onInput: handleEnterAmountChange,
                            readonly: unref(state).payments.length > 1,
                            class: "input",
                            type: "text",
                            placeholder: "0.00",
                            disabled: !visibleButton.value
                          }, null, 40, _hoisted_36$1), [
                            [vModelText, unref(state).posBag.enterAmount]
                          ])
                        ])
                      ])
                    ], 512), [
                      [vShow, visibleButton.value]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_37, [
                      createVNode(_component_VButtons, { class: "mb-1" }, {
                        default: withCtx(() => [
                          createVNode(_component_VButton, {
                            light: "",
                            color: "primary",
                            class: "px-2",
                            disabled: !visibleButton.value,
                            onClick: _cache[10] || (_cache[10] = ($event) => addAnterAmount(10))
                          }, {
                            default: withCtx(() => [
                              _hoisted_38
                            ]),
                            _: 1
                          }, 8, ["disabled"]),
                          createVNode(_component_VButton, {
                            light: "",
                            color: "primary",
                            class: "px-2",
                            disabled: !visibleButton.value,
                            onClick: _cache[11] || (_cache[11] = ($event) => addAnterAmount(20))
                          }, {
                            default: withCtx(() => [
                              _hoisted_39
                            ]),
                            _: 1
                          }, 8, ["disabled"]),
                          createVNode(_component_VButton, {
                            light: "",
                            color: "primary",
                            class: "px-2",
                            disabled: !visibleButton.value,
                            onClick: _cache[12] || (_cache[12] = ($event) => addAnterAmount(50))
                          }, {
                            default: withCtx(() => [
                              _hoisted_40
                            ]),
                            _: 1
                          }, 8, ["disabled"]),
                          createVNode(_component_VButton, {
                            light: "",
                            color: "primary",
                            class: "px-2",
                            disabled: !visibleButton.value,
                            onClick: _cache[13] || (_cache[13] = ($event) => addAnterAmount(100))
                          }, {
                            default: withCtx(() => [
                              _hoisted_41
                            ]),
                            _: 1
                          }, 8, ["disabled"]),
                          createVNode(_component_VButton, {
                            light: "",
                            color: "primary",
                            class: "px-2",
                            disabled: !visibleButton.value,
                            onClick: _cache[14] || (_cache[14] = ($event) => addExactAmount(unref(state).posBag.total))
                          }, {
                            default: withCtx(() => [
                              _hoisted_42
                            ]),
                            _: 1
                          }, 8, ["disabled"])
                        ]),
                        _: 1
                      })
                    ], 512), [
                      [vShow, visibleButton.value]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_43, [
                      createVNode(_component_VFlex, { "justify-content": "center" }, {
                        default: withCtx(() => [
                          createVNode(_component_VIconBox, {
                            size: "medium",
                            color: "primary",
                            rounded: ""
                          }, {
                            default: withCtx(() => [
                              _hoisted_44
                            ]),
                            _: 1
                          })
                        ]),
                        _: 1
                      }),
                      createVNode(_component_VFlex, { "justify-content": "center" }, {
                        default: withCtx(() => [
                          _hoisted_45
                        ]),
                        _: 1
                      }),
                      createVNode(_component_VFlex, { "justify-content": "center" }, {
                        default: withCtx(() => [
                          createVNode(_component_VButton, {
                            color: "primary",
                            class: "is-fullwidth",
                            onClick: close
                          }, {
                            default: withCtx(() => [
                              _hoisted_46
                            ]),
                            _: 1
                          })
                        ]),
                        _: 1
                      })
                    ], 512), [
                      [vShow, !visibleButton.value]
                    ]),
                    createBaseVNode("div", _hoisted_47, [
                      createBaseVNode("table", _hoisted_48, [
                        createBaseVNode("thead", _hoisted_49, [
                          createBaseVNode("tr", null, [
                            _hoisted_50,
                            createBaseVNode("th", _hoisted_51, [
                              withDirectives(createVNode(_component_VButton, {
                                class: "btn-add-payment",
                                light: "",
                                disabled: unref(state).posBag.total < 1,
                                onClick: _cache[15] || (_cache[15] = ($event) => showPaymentMethods())
                              }, {
                                default: withCtx(() => [
                                  _hoisted_52
                                ]),
                                _: 1
                              }, 8, ["disabled"]), [
                                [vShow, visibleButton.value]
                              ])
                            ])
                          ])
                        ]),
                        createBaseVNode("tbody", null, [
                          (openBlock(true), createElementBlock(Fragment, null, renderList(unref(state).payments, (row, index) => {
                            return openBlock(), createElementBlock("tr", {
                              key: index + "PY",
                              class: "column-table-payment"
                            }, [
                              createBaseVNode("td", null, toDisplayString(index + 1) + " - " + toDisplayString(row.payment_method_type_name), 1),
                              createBaseVNode("td", _hoisted_53, " S/ " + toDisplayString(row.payment.toFixed(2)), 1)
                            ]);
                          }), 128))
                        ])
                      ])
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_54, [
                      _hoisted_55,
                      createBaseVNode("div", _hoisted_56, [
                        createBaseVNode("div", _hoisted_57, [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[16] || (_cache[16] = ($event) => unref(state).plate_number = $event),
                            class: "input",
                            type: "text",
                            placeholder: "Ingresar placa",
                            disabled: !visibleButton.value
                          }, null, 8, _hoisted_58), [
                            [vModelText, unref(state).plate_number]
                          ])
                        ])
                      ])
                    ], 512), [
                      [vShow, visibleButton.value && isBusinessTurnTap.value]
                    ]),
                    createVNode(_component_VFlex, { "justify-content": "center" }, {
                      default: withCtx(() => [
                        withDirectives(createVNode(_component_VButton, {
                          color: "primary",
                          style: { "padding": "24px", "width": "100%", "font-size": "1.25rem" },
                          disabled: unref(state).posBag.total <= 0 || !isOnLine.value,
                          onClick: payment
                        }, {
                          default: withCtx(() => [
                            _hoisted_59
                          ]),
                          _: 1
                        }, 8, ["disabled"]), [
                          [vShow, visibleButton.value]
                        ]),
                        withDirectives(createVNode(_component_VButton, {
                          color: "primary",
                          class: "mr-2 column is-6",
                          onClick: rePrint
                        }, {
                          default: withCtx(() => [
                            _hoisted_60
                          ]),
                          _: 1
                        }, 512), [
                          [vShow, !visibleButton.value]
                        ]),
                        withDirectives(createVNode(_component_VButton, {
                          class: "column is-6",
                          onClick: showPdf
                        }, {
                          default: withCtx(() => [
                            _hoisted_61
                          ]),
                          _: 1
                        }, 512), [
                          [vShow, !visibleButton.value]
                        ])
                      ]),
                      _: 1
                    })
                  ]),
                  createBaseVNode("div", {
                    class: normalizeClass(["column is-6", unref(state).isPreviewDocument == true ? "p-0" : ""])
                  }, [
                    unref(state).isPreviewDocument == true ? (openBlock(), createElementBlock("div", _hoisted_62, [
                      unref(state).isPreviewDocument ? (openBlock(), createElementBlock("iframe", {
                        key: 0,
                        src: ticketPdf80.value,
                        width: "100%",
                        height: "600px",
                        class: "border-0",
                        type: "application/pdf"
                      }, null, 8, _hoisted_63)) : createCommentVNode("", true)
                    ])) : (openBlock(), createElementBlock("div", _hoisted_64, [
                      createBaseVNode("div", {
                        ref: (_value, _refs) => {
                          _refs["previewDocument"] = _value;
                          previewDocument.value = _value;
                        },
                        class: "box is-hidden-mobile",
                        background: "light"
                      }, [
                        unref(companyLogo) ? (openBlock(), createElementBlock("div", _hoisted_65, [
                          createBaseVNode("img", {
                            src: unref(companyLogo),
                            alt: "Company Logo",
                            style: { "max-width": "170px", "margin-bottom": "5px" }
                          }, null, 8, _hoisted_66)
                        ])) : createCommentVNode("", true),
                        createBaseVNode("h6", _hoisted_67, toDisplayString(unref(companySession2).company.name), 1),
                        createBaseVNode("h6", _hoisted_68, " RUC " + toDisplayString(unref(companySession2).company.number), 1),
                        unref(companySession2).establishments[0].address != "-" ? (openBlock(), createElementBlock("h6", _hoisted_69, toDisplayString(unref(companySession2).establishments[0].address), 1)) : createCommentVNode("", true),
                        unref(companySession2).establishments[0].trade_address != "-" ? (openBlock(), createElementBlock("h6", _hoisted_70, " D. Comercial: " + toDisplayString(unref(companySession2).establishments[0].trade_address), 1)) : createCommentVNode("", true),
                        unref(companySession2).establishments[0].email ? (openBlock(), createElementBlock("h6", _hoisted_71, " Email: " + toDisplayString(unref(companySession2).establishments[0].email), 1)) : createCommentVNode("", true),
                        _hoisted_72,
                        createBaseVNode("h5", _hoisted_73, toDisplayString(unref(state).documentSelected) + " " + toDisplayString(unref(state).documentSelected == "BOLETA" || unref(state).documentSelected == "FACTURA" ? "ELECTR\xD3NICA" : "DE VENTA"), 1),
                        unref(currentData).number ? (openBlock(), createElementBlock("h5", _hoisted_74, toDisplayString(unref(currentData).number), 1)) : createCommentVNode("", true),
                        _hoisted_75,
                        createBaseVNode("p", _hoisted_76, " F. Emisi\xF3n: " + toDisplayString(unref(date)), 1),
                        createBaseVNode("p", _hoisted_77, " H. Emisi\xF3n: " + toDisplayString(unref(time2)), 1),
                        createBaseVNode("p", _hoisted_78, " F. Vencimiento: " + toDisplayString(unref(dateOfDue)), 1),
                        createBaseVNode("p", _hoisted_79, " Cliente: " + toDisplayString(unref(state).customer.apellidos_y_nombres_o_razon_social), 1),
                        createBaseVNode("p", _hoisted_80, toDisplayString(unref(IdentityDocumentTypes)[unref(state).customer.codigo_tipo_documento_identidad]) + ": " + toDisplayString(unref(state).customer.numero_documento), 1),
                        unref(state).posBag.waiter ? (openBlock(), createElementBlock("p", _hoisted_81, " Mozo: " + toDisplayString(unref(state).posBag.waiter), 1)) : createCommentVNode("", true),
                        createBaseVNode("div", _hoisted_82, [
                          createBaseVNode("table", _hoisted_83, [
                            _hoisted_84,
                            createBaseVNode("tbody", null, [
                              (openBlock(true), createElementBlock(Fragment, null, renderList(unref(state).posBag.products, (item) => {
                                return openBlock(), createElementBlock("tr", {
                                  key: item.id + "M"
                                }, [
                                  createBaseVNode("td", null, toDisplayString(item.internalId), 1),
                                  createBaseVNode("td", null, toDisplayString(item.quantity), 1),
                                  _hoisted_85,
                                  createBaseVNode("td", null, toDisplayString(item.name), 1),
                                  createBaseVNode("td", _hoisted_86, toDisplayString(item.price), 1),
                                  createBaseVNode("td", _hoisted_87, toDisplayString(item.price * item.quantity), 1)
                                ]);
                              }), 128))
                            ]),
                            createBaseVNode("tfoot", null, [
                              createBaseVNode("tr", null, [
                                _hoisted_88,
                                createBaseVNode("th", _hoisted_89, toDisplayString(unref(state).posBag.total), 1)
                              ])
                            ])
                          ]),
                          createBaseVNode("table", _hoisted_90, [
                            _hoisted_91,
                            createBaseVNode("tr", null, [
                              _hoisted_92,
                              createBaseVNode("th", null, " Efectivo - S/" + toDisplayString(Number(unref(state).posBag.enterAmount).toFixed(2)), 1)
                            ]),
                            createBaseVNode("tr", null, [
                              _hoisted_93,
                              createBaseVNode("th", null, toDisplayString((unref(state).posBag.enterAmount - unref(state).posBag.total).toFixed(2) > 0 ? (unref(state).posBag.enterAmount - unref(state).posBag.total).toFixed(2) : "0.00"), 1)
                            ]),
                            createBaseVNode("tr", null, [
                              _hoisted_94,
                              createBaseVNode("th", null, toDisplayString(unref(userSession2).sellerName), 1)
                            ]),
                            unref(currentData).number_to_letter ? (openBlock(), createElementBlock("tr", _hoisted_95, [
                              _hoisted_96,
                              createBaseVNode("th", null, toDisplayString(unref(currentData).number_to_letter), 1)
                            ])) : createCommentVNode("", true),
                            unref(currentData).qr ? (openBlock(), createElementBlock("tr", _hoisted_97, [
                              createBaseVNode("td", _hoisted_98, [
                                createBaseVNode("img", {
                                  style: { "width": "60%" },
                                  src: "data:image/png;base64," + unref(currentData).qr,
                                  alt: "QR"
                                }, null, 8, _hoisted_99)
                              ])
                            ])) : createCommentVNode("", true),
                            unref(currentData).hash ? (openBlock(), createElementBlock("tr", _hoisted_100, [
                              createBaseVNode("td", _hoisted_101, " HASH: " + toDisplayString(unref(currentData).hash), 1)
                            ])) : createCommentVNode("", true)
                          ])
                        ])
                      ], 512)
                    ]))
                  ], 2)
                ])
              ]),
              _: 1
            }, 8, ["active"])
          ]),
          _: 1
        }, 8, ["open", "cancel-label"])
      ]);
    };
  }
});
var uikit = { exports: {} };
/*! UIkit 3.10.1 | https://www.getuikit.com | (c) 2014 - 2022 YOOtheme | MIT License */
(function(module, exports) {
  (function(global, factory) {
    module.exports = factory();
  })(commonjsGlobal, function() {
    var objPrototype = Object.prototype;
    var hasOwnProperty = objPrototype.hasOwnProperty;
    function hasOwn(obj2, key2) {
      return hasOwnProperty.call(obj2, key2);
    }
    var hyphenateRe = /\B([A-Z])/g;
    var hyphenate = memoize(function(str) {
      return str.replace(hyphenateRe, "-$1").toLowerCase();
    });
    var camelizeRe = /-(\w)/g;
    var camelize = memoize(function(str) {
      return str.replace(camelizeRe, toUpper);
    });
    var ucfirst = memoize(function(str) {
      return str.length ? toUpper(null, str.charAt(0)) + str.slice(1) : "";
    });
    function toUpper(_2, c) {
      return c ? c.toUpperCase() : "";
    }
    var strPrototype = String.prototype;
    var startsWithFn = strPrototype.startsWith || function(search) {
      return this.lastIndexOf(search, 0) === 0;
    };
    function startsWith(str, search) {
      return startsWithFn.call(str, search);
    }
    var endsWithFn = strPrototype.endsWith || function(search) {
      return this.substr(-search.length) === search;
    };
    function endsWith(str, search) {
      return endsWithFn.call(str, search);
    }
    var arrPrototype = Array.prototype;
    var includesFn = function(search, i) {
      return !!~this.indexOf(search, i);
    };
    var includesStr = strPrototype.includes || includesFn;
    var includesArray = arrPrototype.includes || includesFn;
    function includes(obj2, search) {
      return obj2 && (isString(obj2) ? includesStr : includesArray).call(obj2, search);
    }
    var findIndexFn = arrPrototype.findIndex || function(predicate) {
      var arguments$1 = arguments;
      for (var i = 0; i < this.length; i++) {
        if (predicate.call(arguments$1[1], this[i], i, this)) {
          return i;
        }
      }
      return -1;
    };
    function findIndex(array, predicate) {
      return findIndexFn.call(array, predicate);
    }
    var isArray = Array.isArray;
    function isFunction(obj2) {
      return typeof obj2 === "function";
    }
    function isObject(obj2) {
      return obj2 !== null && typeof obj2 === "object";
    }
    var toString = objPrototype.toString;
    function isPlainObject(obj2) {
      return toString.call(obj2) === "[object Object]";
    }
    function isWindow(obj2) {
      return isObject(obj2) && obj2 === obj2.window;
    }
    function isDocument(obj2) {
      return nodeType(obj2) === 9;
    }
    function isNode(obj2) {
      return nodeType(obj2) >= 1;
    }
    function isElement(obj2) {
      return nodeType(obj2) === 1;
    }
    function nodeType(obj2) {
      return !isWindow(obj2) && isObject(obj2) && obj2.nodeType;
    }
    function isBoolean(value) {
      return typeof value === "boolean";
    }
    function isString(value) {
      return typeof value === "string";
    }
    function isNumber(value) {
      return typeof value === "number";
    }
    function isNumeric(value) {
      return isNumber(value) || isString(value) && !isNaN(value - parseFloat(value));
    }
    function isEmpty(obj2) {
      return !(isArray(obj2) ? obj2.length : isObject(obj2) ? Object.keys(obj2).length : false);
    }
    function isUndefined(value) {
      return value === void 0;
    }
    function toBoolean(value) {
      return isBoolean(value) ? value : value === "true" || value === "1" || value === "" ? true : value === "false" || value === "0" ? false : value;
    }
    function toNumber(value) {
      var number = Number(value);
      return isNaN(number) ? false : number;
    }
    function toFloat(value) {
      return parseFloat(value) || 0;
    }
    var toArray = Array.from || function(value) {
      return arrPrototype.slice.call(value);
    };
    function toNode(element) {
      return toNodes(element)[0];
    }
    function toNodes(element) {
      return element && (isNode(element) ? [element] : toArray(element).filter(isNode)) || [];
    }
    function toWindow(element) {
      if (isWindow(element)) {
        return element;
      }
      element = toNode(element);
      return element ? (isDocument(element) ? element : element.ownerDocument).defaultView : window;
    }
    function toMs(time2) {
      return !time2 ? 0 : endsWith(time2, "ms") ? toFloat(time2) : toFloat(time2) * 1e3;
    }
    function isEqual(value, other) {
      return value === other || isObject(value) && isObject(other) && Object.keys(value).length === Object.keys(other).length && each(value, function(val, key2) {
        return val === other[key2];
      });
    }
    function swap(value, a, b) {
      return value.replace(new RegExp(a + "|" + b, "g"), function(match2) {
        return match2 === a ? b : a;
      });
    }
    var assign = Object.assign || function(target) {
      var args = [], len = arguments.length - 1;
      while (len-- > 0)
        args[len] = arguments[len + 1];
      target = Object(target);
      for (var i = 0; i < args.length; i++) {
        var source = args[i];
        if (source !== null) {
          for (var key2 in source) {
            if (hasOwn(source, key2)) {
              target[key2] = source[key2];
            }
          }
        }
      }
      return target;
    };
    function last(array) {
      return array[array.length - 1];
    }
    function each(obj2, cb) {
      for (var key2 in obj2) {
        if (cb(obj2[key2], key2) === false) {
          return false;
        }
      }
      return true;
    }
    function sortBy$1(array, prop) {
      return array.slice().sort(function(ref2, ref$1) {
        var propA = ref2[prop];
        if (propA === void 0)
          propA = 0;
        var propB = ref$1[prop];
        if (propB === void 0)
          propB = 0;
        return propA > propB ? 1 : propB > propA ? -1 : 0;
      });
    }
    function uniqueBy(array, prop) {
      var seen = new Set();
      return array.filter(function(ref2) {
        var check = ref2[prop];
        return seen.has(check) ? false : seen.add(check) || true;
      });
    }
    function clamp(number, min, max) {
      if (min === void 0)
        min = 0;
      if (max === void 0)
        max = 1;
      return Math.min(Math.max(toNumber(number) || 0, min), max);
    }
    function noop() {
    }
    function intersectRect() {
      var rects = [], len = arguments.length;
      while (len--)
        rects[len] = arguments[len];
      return [["bottom", "top"], ["right", "left"]].every(function(ref2) {
        var minProp = ref2[0];
        var maxProp = ref2[1];
        return Math.min.apply(Math, rects.map(function(ref3) {
          var min = ref3[minProp];
          return min;
        })) - Math.max.apply(Math, rects.map(function(ref3) {
          var max = ref3[maxProp];
          return max;
        })) > 0;
      });
    }
    function pointInRect(point, rect) {
      return point.x <= rect.right && point.x >= rect.left && point.y <= rect.bottom && point.y >= rect.top;
    }
    var Dimensions = {
      ratio: function(dimensions2, prop, value) {
        var obj2;
        var aProp = prop === "width" ? "height" : "width";
        return obj2 = {}, obj2[aProp] = dimensions2[prop] ? Math.round(value * dimensions2[aProp] / dimensions2[prop]) : dimensions2[aProp], obj2[prop] = value, obj2;
      },
      contain: function(dimensions2, maxDimensions) {
        var this$1$1 = this;
        dimensions2 = assign({}, dimensions2);
        each(dimensions2, function(_2, prop) {
          return dimensions2 = dimensions2[prop] > maxDimensions[prop] ? this$1$1.ratio(dimensions2, prop, maxDimensions[prop]) : dimensions2;
        });
        return dimensions2;
      },
      cover: function(dimensions2, maxDimensions) {
        var this$1$1 = this;
        dimensions2 = this.contain(dimensions2, maxDimensions);
        each(dimensions2, function(_2, prop) {
          return dimensions2 = dimensions2[prop] < maxDimensions[prop] ? this$1$1.ratio(dimensions2, prop, maxDimensions[prop]) : dimensions2;
        });
        return dimensions2;
      }
    };
    function getIndex(i, elements, current, finite) {
      if (current === void 0)
        current = 0;
      if (finite === void 0)
        finite = false;
      elements = toNodes(elements);
      var length = elements.length;
      i = isNumeric(i) ? toNumber(i) : i === "next" ? current + 1 : i === "previous" ? current - 1 : elements.indexOf(toNode(i));
      if (finite) {
        return clamp(i, 0, length - 1);
      }
      i %= length;
      return i < 0 ? i + length : i;
    }
    function memoize(fn) {
      var cache = Object.create(null);
      return function(key2) {
        return cache[key2] || (cache[key2] = fn(key2));
      };
    }
    function attr(element, name, value) {
      if (isObject(name)) {
        for (var key2 in name) {
          attr(element, key2, name[key2]);
        }
        return;
      }
      if (isUndefined(value)) {
        element = toNode(element);
        return element && element.getAttribute(name);
      } else {
        toNodes(element).forEach(function(element2) {
          if (isFunction(value)) {
            value = value.call(element2, attr(element2, name));
          }
          if (value === null) {
            removeAttr(element2, name);
          } else {
            element2.setAttribute(name, value);
          }
        });
      }
    }
    function hasAttr(element, name) {
      return toNodes(element).some(function(element2) {
        return element2.hasAttribute(name);
      });
    }
    function removeAttr(element, name) {
      element = toNodes(element);
      name.split(" ").forEach(function(name2) {
        return element.forEach(function(element2) {
          return element2.hasAttribute(name2) && element2.removeAttribute(name2);
        });
      });
    }
    function data(element, attribute) {
      for (var i = 0, attrs = [attribute, "data-" + attribute]; i < attrs.length; i++) {
        if (hasAttr(element, attrs[i])) {
          return attr(element, attrs[i]);
        }
      }
    }
    var inBrowser = typeof window !== "undefined";
    var isIE = inBrowser && /msie|trident/i.test(window.navigator.userAgent);
    var isRtl = inBrowser && attr(document.documentElement, "dir") === "rtl";
    var hasTouchEvents = inBrowser && "ontouchstart" in window;
    var hasPointerEvents = inBrowser && window.PointerEvent;
    var hasTouch = inBrowser && (hasTouchEvents || window.DocumentTouch && document instanceof DocumentTouch || navigator.maxTouchPoints);
    var pointerDown = hasPointerEvents ? "pointerdown" : hasTouchEvents ? "touchstart" : "mousedown";
    var pointerMove = hasPointerEvents ? "pointermove" : hasTouchEvents ? "touchmove" : "mousemove";
    var pointerUp = hasPointerEvents ? "pointerup" : hasTouchEvents ? "touchend" : "mouseup";
    var pointerEnter = hasPointerEvents ? "pointerenter" : hasTouchEvents ? "" : "mouseenter";
    var pointerLeave = hasPointerEvents ? "pointerleave" : hasTouchEvents ? "" : "mouseleave";
    var pointerCancel = hasPointerEvents ? "pointercancel" : "touchcancel";
    var voidElements = {
      area: true,
      base: true,
      br: true,
      col: true,
      embed: true,
      hr: true,
      img: true,
      input: true,
      keygen: true,
      link: true,
      menuitem: true,
      meta: true,
      param: true,
      source: true,
      track: true,
      wbr: true
    };
    function isVoidElement(element) {
      return toNodes(element).some(function(element2) {
        return voidElements[element2.tagName.toLowerCase()];
      });
    }
    function isVisible(element) {
      return toNodes(element).some(function(element2) {
        return element2.offsetWidth || element2.offsetHeight || element2.getClientRects().length;
      });
    }
    var selInput = "input,select,textarea,button";
    function isInput(element) {
      return toNodes(element).some(function(element2) {
        return matches(element2, selInput);
      });
    }
    var selFocusable = selInput + ",a[href],[tabindex]";
    function isFocusable(element) {
      return matches(element, selFocusable);
    }
    function parent(element) {
      element = toNode(element);
      return element && isElement(element.parentNode) && element.parentNode;
    }
    function filter$1(element, selector) {
      return toNodes(element).filter(function(element2) {
        return matches(element2, selector);
      });
    }
    var elProto = inBrowser ? Element.prototype : {};
    var matchesFn = elProto.matches || elProto.webkitMatchesSelector || elProto.msMatchesSelector || noop;
    function matches(element, selector) {
      return toNodes(element).some(function(element2) {
        return matchesFn.call(element2, selector);
      });
    }
    var closestFn = elProto.closest || function(selector) {
      var ancestor = this;
      do {
        if (matches(ancestor, selector)) {
          return ancestor;
        }
      } while (ancestor = parent(ancestor));
    };
    function closest(element, selector) {
      if (startsWith(selector, ">")) {
        selector = selector.slice(1);
      }
      return isElement(element) ? closestFn.call(element, selector) : toNodes(element).map(function(element2) {
        return closest(element2, selector);
      }).filter(Boolean);
    }
    function within(element, selector) {
      return !isString(selector) ? element === selector || (isDocument(selector) ? selector.documentElement : toNode(selector)).contains(toNode(element)) : matches(element, selector) || !!closest(element, selector);
    }
    function parents(element, selector) {
      var elements = [];
      while (element = parent(element)) {
        if (!selector || matches(element, selector)) {
          elements.push(element);
        }
      }
      return elements;
    }
    function children(element, selector) {
      element = toNode(element);
      var children2 = element ? toNodes(element.children) : [];
      return selector ? filter$1(children2, selector) : children2;
    }
    function index(element, ref2) {
      return ref2 ? toNodes(element).indexOf(toNode(ref2)) : children(parent(element)).indexOf(element);
    }
    function query(selector, context) {
      return find(selector, getContext(selector, context));
    }
    function queryAll(selector, context) {
      return findAll(selector, getContext(selector, context));
    }
    function getContext(selector, context) {
      if (context === void 0)
        context = document;
      return isString(selector) && isContextSelector(selector) || isDocument(context) ? context : context.ownerDocument;
    }
    function find(selector, context) {
      return toNode(_query(selector, context, "querySelector"));
    }
    function findAll(selector, context) {
      return toNodes(_query(selector, context, "querySelectorAll"));
    }
    function _query(selector, context, queryFn) {
      if (context === void 0)
        context = document;
      if (!selector || !isString(selector)) {
        return selector;
      }
      selector = selector.replace(contextSanitizeRe, "$1 *");
      if (isContextSelector(selector)) {
        selector = splitSelector(selector).map(function(selector2) {
          var ctx = context;
          if (selector2[0] === "!") {
            var selectors = selector2.substr(1).trim().split(" ");
            ctx = closest(parent(context), selectors[0]);
            selector2 = selectors.slice(1).join(" ").trim();
          }
          if (selector2[0] === "-") {
            var selectors$1 = selector2.substr(1).trim().split(" ");
            var prev = (ctx || context).previousElementSibling;
            ctx = matches(prev, selector2.substr(1)) ? prev : null;
            selector2 = selectors$1.slice(1).join(" ");
          }
          if (!ctx) {
            return null;
          }
          return domPath(ctx) + " " + selector2;
        }).filter(Boolean).join(",");
        context = document;
      }
      try {
        return context[queryFn](selector);
      } catch (e) {
        return null;
      }
    }
    var contextSelectorRe = /(^|[^\\],)\s*[!>+~-]/;
    var contextSanitizeRe = /([!>+~-])(?=\s+[!>+~-]|\s*$)/g;
    var isContextSelector = memoize(function(selector) {
      return selector.match(contextSelectorRe);
    });
    var selectorRe = /.*?[^\\](?:,|$)/g;
    var splitSelector = memoize(function(selector) {
      return selector.match(selectorRe).map(function(selector2) {
        return selector2.replace(/,$/, "").trim();
      });
    });
    function domPath(element) {
      var names = [];
      while (element.parentNode) {
        var id = attr(element, "id");
        if (id) {
          names.unshift("#" + escape(id));
          break;
        } else {
          var tagName = element.tagName;
          if (tagName !== "HTML") {
            tagName += ":nth-child(" + (index(element) + 1) + ")";
          }
          names.unshift(tagName);
          element = element.parentNode;
        }
      }
      return names.join(" > ");
    }
    var escapeFn = inBrowser && window.CSS && CSS.escape || function(css2) {
      return css2.replace(/([^\x7f-\uFFFF\w-])/g, function(match2) {
        return "\\" + match2;
      });
    };
    function escape(css2) {
      return isString(css2) ? escapeFn.call(null, css2) : "";
    }
    function on() {
      var args = [], len = arguments.length;
      while (len--)
        args[len] = arguments[len];
      var ref2 = getArgs(args);
      var targets = ref2[0];
      var type = ref2[1];
      var selector = ref2[2];
      var listener = ref2[3];
      var useCapture = ref2[4];
      targets = toEventTargets(targets);
      if (listener.length > 1) {
        listener = detail(listener);
      }
      if (useCapture && useCapture.self) {
        listener = selfFilter(listener);
      }
      if (selector) {
        listener = delegate(selector, listener);
      }
      useCapture = useCaptureFilter(useCapture);
      type.split(" ").forEach(function(type2) {
        return targets.forEach(function(target) {
          return target.addEventListener(type2, listener, useCapture);
        });
      });
      return function() {
        return off(targets, type, listener, useCapture);
      };
    }
    function off(targets, type, listener, useCapture) {
      if (useCapture === void 0)
        useCapture = false;
      useCapture = useCaptureFilter(useCapture);
      targets = toEventTargets(targets);
      type.split(" ").forEach(function(type2) {
        return targets.forEach(function(target) {
          return target.removeEventListener(type2, listener, useCapture);
        });
      });
    }
    function once() {
      var args = [], len = arguments.length;
      while (len--)
        args[len] = arguments[len];
      var ref2 = getArgs(args);
      var element = ref2[0];
      var type = ref2[1];
      var selector = ref2[2];
      var listener = ref2[3];
      var useCapture = ref2[4];
      var condition = ref2[5];
      var off2 = on(element, type, selector, function(e) {
        var result = !condition || condition(e);
        if (result) {
          off2();
          listener(e, result);
        }
      }, useCapture);
      return off2;
    }
    function trigger(targets, event, detail2) {
      return toEventTargets(targets).reduce(function(notCanceled, target) {
        return notCanceled && target.dispatchEvent(createEvent(event, true, true, detail2));
      }, true);
    }
    function createEvent(e, bubbles, cancelable, detail2) {
      if (bubbles === void 0)
        bubbles = true;
      if (cancelable === void 0)
        cancelable = false;
      if (isString(e)) {
        var event = document.createEvent("CustomEvent");
        event.initCustomEvent(e, bubbles, cancelable, detail2);
        e = event;
      }
      return e;
    }
    function getArgs(args) {
      if (isFunction(args[2])) {
        args.splice(2, 0, false);
      }
      return args;
    }
    function delegate(selector, listener) {
      var this$1$1 = this;
      return function(e) {
        var current = selector[0] === ">" ? findAll(selector, e.currentTarget).reverse().filter(function(element) {
          return within(e.target, element);
        })[0] : closest(e.target, selector);
        if (current) {
          e.current = current;
          listener.call(this$1$1, e);
        }
      };
    }
    function detail(listener) {
      return function(e) {
        return isArray(e.detail) ? listener.apply(void 0, [e].concat(e.detail)) : listener(e);
      };
    }
    function selfFilter(listener) {
      return function(e) {
        if (e.target === e.currentTarget || e.target === e.current) {
          return listener.call(null, e);
        }
      };
    }
    function useCaptureFilter(options) {
      return options && isIE && !isBoolean(options) ? !!options.capture : options;
    }
    function isEventTarget(target) {
      return target && "addEventListener" in target;
    }
    function toEventTarget(target) {
      return isEventTarget(target) ? target : toNode(target);
    }
    function toEventTargets(target) {
      return isArray(target) ? target.map(toEventTarget).filter(Boolean) : isString(target) ? findAll(target) : isEventTarget(target) ? [target] : toNodes(target);
    }
    function isTouch(e) {
      return e.pointerType === "touch" || !!e.touches;
    }
    function getEventPos(e) {
      var touches = e.touches;
      var changedTouches = e.changedTouches;
      var ref2 = touches && touches[0] || changedTouches && changedTouches[0] || e;
      var x = ref2.clientX;
      var y = ref2.clientY;
      return { x, y };
    }
    var Promise$1 = inBrowser && window.Promise || PromiseFn;
    var Deferred = function() {
      var this$1$1 = this;
      this.promise = new Promise$1(function(resolve, reject) {
        this$1$1.reject = reject;
        this$1$1.resolve = resolve;
      });
    };
    var RESOLVED = 0;
    var REJECTED = 1;
    var PENDING = 2;
    var async = inBrowser && window.setImmediate || setTimeout;
    function PromiseFn(executor) {
      this.state = PENDING;
      this.value = void 0;
      this.deferred = [];
      var promise = this;
      try {
        executor(function(x) {
          promise.resolve(x);
        }, function(r) {
          promise.reject(r);
        });
      } catch (e) {
        promise.reject(e);
      }
    }
    PromiseFn.reject = function(r) {
      return new PromiseFn(function(resolve, reject) {
        reject(r);
      });
    };
    PromiseFn.resolve = function(x) {
      return new PromiseFn(function(resolve, reject) {
        resolve(x);
      });
    };
    PromiseFn.all = function all(iterable) {
      return new PromiseFn(function(resolve, reject) {
        var result = [];
        var count = 0;
        if (iterable.length === 0) {
          resolve(result);
        }
        function resolver(i2) {
          return function(x) {
            result[i2] = x;
            count += 1;
            if (count === iterable.length) {
              resolve(result);
            }
          };
        }
        for (var i = 0; i < iterable.length; i += 1) {
          PromiseFn.resolve(iterable[i]).then(resolver(i), reject);
        }
      });
    };
    PromiseFn.race = function race(iterable) {
      return new PromiseFn(function(resolve, reject) {
        for (var i = 0; i < iterable.length; i += 1) {
          PromiseFn.resolve(iterable[i]).then(resolve, reject);
        }
      });
    };
    var p = PromiseFn.prototype;
    p.resolve = function resolve(x) {
      var promise = this;
      if (promise.state === PENDING) {
        if (x === promise) {
          throw new TypeError("Promise settled with itself.");
        }
        var called = false;
        try {
          var then = x && x.then;
          if (x !== null && isObject(x) && isFunction(then)) {
            then.call(x, function(x2) {
              if (!called) {
                promise.resolve(x2);
              }
              called = true;
            }, function(r) {
              if (!called) {
                promise.reject(r);
              }
              called = true;
            });
            return;
          }
        } catch (e) {
          if (!called) {
            promise.reject(e);
          }
          return;
        }
        promise.state = RESOLVED;
        promise.value = x;
        promise.notify();
      }
    };
    p.reject = function reject(reason) {
      var promise = this;
      if (promise.state === PENDING) {
        if (reason === promise) {
          throw new TypeError("Promise settled with itself.");
        }
        promise.state = REJECTED;
        promise.value = reason;
        promise.notify();
      }
    };
    p.notify = function notify() {
      var this$1$1 = this;
      async(function() {
        if (this$1$1.state !== PENDING) {
          while (this$1$1.deferred.length) {
            var ref2 = this$1$1.deferred.shift();
            var onResolved = ref2[0];
            var onRejected = ref2[1];
            var resolve = ref2[2];
            var reject = ref2[3];
            try {
              if (this$1$1.state === RESOLVED) {
                if (isFunction(onResolved)) {
                  resolve(onResolved.call(void 0, this$1$1.value));
                } else {
                  resolve(this$1$1.value);
                }
              } else if (this$1$1.state === REJECTED) {
                if (isFunction(onRejected)) {
                  resolve(onRejected.call(void 0, this$1$1.value));
                } else {
                  reject(this$1$1.value);
                }
              }
            } catch (e) {
              reject(e);
            }
          }
        }
      });
    };
    p.then = function then(onResolved, onRejected) {
      var this$1$1 = this;
      return new PromiseFn(function(resolve, reject) {
        this$1$1.deferred.push([onResolved, onRejected, resolve, reject]);
        this$1$1.notify();
      });
    };
    p.catch = function(onRejected) {
      return this.then(void 0, onRejected);
    };
    function ajax(url, options) {
      var env = assign({
        data: null,
        method: "GET",
        headers: {},
        xhr: new XMLHttpRequest(),
        beforeSend: noop,
        responseType: ""
      }, options);
      return Promise$1.resolve().then(function() {
        return env.beforeSend(env);
      }).then(function() {
        return send(url, env);
      });
    }
    function send(url, env) {
      return new Promise$1(function(resolve, reject) {
        var xhr = env.xhr;
        for (var prop in env) {
          if (prop in xhr) {
            try {
              xhr[prop] = env[prop];
            } catch (e) {
            }
          }
        }
        xhr.open(env.method.toUpperCase(), url);
        for (var header in env.headers) {
          xhr.setRequestHeader(header, env.headers[header]);
        }
        on(xhr, "load", function() {
          if (xhr.status === 0 || xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) {
            if (env.responseType === "json" && isString(xhr.response)) {
              xhr = assign(copyXhr(xhr), { response: JSON.parse(xhr.response) });
            }
            resolve(xhr);
          } else {
            reject(assign(Error(xhr.statusText), {
              xhr,
              status: xhr.status
            }));
          }
        });
        on(xhr, "error", function() {
          return reject(assign(Error("Network Error"), { xhr }));
        });
        on(xhr, "timeout", function() {
          return reject(assign(Error("Network Timeout"), { xhr }));
        });
        xhr.send(env.data);
      });
    }
    function getImage(src, srcset, sizes) {
      return new Promise$1(function(resolve, reject) {
        var img2 = new Image();
        img2.onerror = function(e) {
          return reject(e);
        };
        img2.onload = function() {
          return resolve(img2);
        };
        sizes && (img2.sizes = sizes);
        srcset && (img2.srcset = srcset);
        img2.src = src;
      });
    }
    function copyXhr(source) {
      var target = {};
      for (var key2 in source) {
        target[key2] = source[key2];
      }
      return target;
    }
    function ready(fn) {
      if (document.readyState !== "loading") {
        fn();
        return;
      }
      once(document, "DOMContentLoaded", fn);
    }
    function empty(element) {
      element = $(element);
      element.innerHTML = "";
      return element;
    }
    function html(parent2, html2) {
      parent2 = $(parent2);
      return isUndefined(html2) ? parent2.innerHTML : append(parent2.hasChildNodes() ? empty(parent2) : parent2, html2);
    }
    function prepend(parent2, element) {
      parent2 = $(parent2);
      if (parent2.hasChildNodes()) {
        return insertNodes(element, function(element2) {
          return parent2.insertBefore(element2, parent2.firstChild);
        });
      } else {
        return append(parent2, element);
      }
    }
    function append(parent2, element) {
      parent2 = $(parent2);
      return insertNodes(element, function(element2) {
        return parent2.appendChild(element2);
      });
    }
    function before(ref2, element) {
      ref2 = $(ref2);
      return insertNodes(element, function(element2) {
        return ref2.parentNode.insertBefore(element2, ref2);
      });
    }
    function after(ref2, element) {
      ref2 = $(ref2);
      return insertNodes(element, function(element2) {
        return ref2.nextSibling ? before(ref2.nextSibling, element2) : append(ref2.parentNode, element2);
      });
    }
    function insertNodes(element, fn) {
      element = isString(element) ? fragment(element) : element;
      return element ? "length" in element ? toNodes(element).map(fn) : fn(element) : null;
    }
    function remove$1(element) {
      toNodes(element).forEach(function(element2) {
        return element2.parentNode && element2.parentNode.removeChild(element2);
      });
    }
    function wrapAll(element, structure) {
      structure = toNode(before(element, structure));
      while (structure.firstChild) {
        structure = structure.firstChild;
      }
      append(structure, element);
      return structure;
    }
    function wrapInner(element, structure) {
      return toNodes(toNodes(element).map(function(element2) {
        return element2.hasChildNodes ? wrapAll(toNodes(element2.childNodes), structure) : append(element2, structure);
      }));
    }
    function unwrap(element) {
      toNodes(element).map(parent).filter(function(value, index2, self2) {
        return self2.indexOf(value) === index2;
      }).forEach(function(parent2) {
        before(parent2, parent2.childNodes);
        remove$1(parent2);
      });
    }
    var fragmentRe = /^\s*<(\w+|!)[^>]*>/;
    var singleTagRe = /^<(\w+)\s*\/?>(?:<\/\1>)?$/;
    function fragment(html2) {
      var matches2 = singleTagRe.exec(html2);
      if (matches2) {
        return document.createElement(matches2[1]);
      }
      var container = document.createElement("div");
      if (fragmentRe.test(html2)) {
        container.insertAdjacentHTML("beforeend", html2.trim());
      } else {
        container.textContent = html2;
      }
      return container.childNodes.length > 1 ? toNodes(container.childNodes) : container.firstChild;
    }
    function apply$1(node, fn) {
      if (!isElement(node)) {
        return;
      }
      fn(node);
      node = node.firstElementChild;
      while (node) {
        var next = node.nextElementSibling;
        apply$1(node, fn);
        node = next;
      }
    }
    function $(selector, context) {
      return isHtml(selector) ? toNode(fragment(selector)) : find(selector, context);
    }
    function $$(selector, context) {
      return isHtml(selector) ? toNodes(fragment(selector)) : findAll(selector, context);
    }
    function isHtml(str) {
      return isString(str) && (str[0] === "<" || str.match(/^\s*</));
    }
    function addClass(element) {
      var args = [], len = arguments.length - 1;
      while (len-- > 0)
        args[len] = arguments[len + 1];
      apply(element, args, "add");
    }
    function removeClass(element) {
      var args = [], len = arguments.length - 1;
      while (len-- > 0)
        args[len] = arguments[len + 1];
      apply(element, args, "remove");
    }
    function removeClasses(element, cls) {
      attr(element, "class", function(value) {
        return (value || "").replace(new RegExp("\\b" + cls + "\\b", "g"), "");
      });
    }
    function replaceClass(element) {
      var args = [], len = arguments.length - 1;
      while (len-- > 0)
        args[len] = arguments[len + 1];
      args[0] && removeClass(element, args[0]);
      args[1] && addClass(element, args[1]);
    }
    function hasClass(element, cls) {
      var assign2;
      assign2 = getClasses(cls), cls = assign2[0];
      var nodes = toNodes(element);
      for (var n = 0; n < nodes.length; n++) {
        if (cls && nodes[n].classList.contains(cls)) {
          return true;
        }
      }
      return false;
    }
    function toggleClass(element, cls, force) {
      cls = getClasses(cls);
      var nodes = toNodes(element);
      for (var n = 0; n < nodes.length; n++) {
        var list = nodes[n].classList;
        for (var i = 0; i < cls.length; i++) {
          if (isUndefined(force)) {
            list.toggle(cls[i]);
          } else if (supports.Force) {
            list.toggle(cls[i], !!force);
          } else {
            list[force ? "add" : "remove"](cls[i]);
          }
        }
      }
    }
    function apply(element, args, fn) {
      var ref2;
      args = args.reduce(function(args2, arg) {
        return args2.concat(getClasses(arg));
      }, []);
      var nodes = toNodes(element);
      var loop = function(n2) {
        if (supports.Multiple) {
          (ref2 = nodes[n2].classList)[fn].apply(ref2, args);
        } else {
          args.forEach(function(cls) {
            return nodes[n2].classList[fn](cls);
          });
        }
      };
      for (var n = 0; n < nodes.length; n++)
        loop(n);
    }
    function getClasses(str) {
      return String(str).split(/\s|,/).filter(Boolean);
    }
    var supports = {
      get Multiple() {
        return this.get("Multiple");
      },
      get Force() {
        return this.get("Force");
      },
      get: function(key2) {
        var ref2 = document.createElement("_");
        var classList = ref2.classList;
        classList.add("a", "b");
        classList.toggle("c", false);
        supports = {
          Multiple: classList.contains("b"),
          Force: !classList.contains("c")
        };
        return supports[key2];
      }
    };
    var cssNumber = {
      "animation-iteration-count": true,
      "column-count": true,
      "fill-opacity": true,
      "flex-grow": true,
      "flex-shrink": true,
      "font-weight": true,
      "line-height": true,
      "opacity": true,
      "order": true,
      "orphans": true,
      "stroke-dasharray": true,
      "stroke-dashoffset": true,
      "widows": true,
      "z-index": true,
      "zoom": true
    };
    function css(element, property, value, priority) {
      if (priority === void 0)
        priority = "";
      return toNodes(element).map(function(element2) {
        if (isString(property)) {
          property = propName(property);
          if (isUndefined(value)) {
            return getStyle(element2, property);
          } else if (!value && !isNumber(value)) {
            element2.style.removeProperty(property);
          } else {
            element2.style.setProperty(property, isNumeric(value) && !cssNumber[property] ? value + "px" : value, priority);
          }
        } else if (isArray(property)) {
          var styles = getStyles(element2);
          return property.reduce(function(props2, property2) {
            props2[property2] = styles[propName(property2)];
            return props2;
          }, {});
        } else if (isObject(property)) {
          priority = value;
          each(property, function(value2, property2) {
            return css(element2, property2, value2, priority);
          });
        }
        return element2;
      })[0];
    }
    function getStyles(element, pseudoElt) {
      return toWindow(element).getComputedStyle(element, pseudoElt);
    }
    function getStyle(element, property, pseudoElt) {
      return getStyles(element, pseudoElt)[property];
    }
    var parseCssVar = memoize(function(name) {
      var element = append(document.documentElement, fragment("<div>"));
      addClass(element, "uk-" + name);
      var value = getStyle(element, "content", ":before");
      remove$1(element);
      return value;
    });
    var propertyRe = /^\s*(["'])?(.*?)\1\s*$/;
    function getCssVar(name) {
      return (isIE ? parseCssVar(name) : getStyles(document.documentElement).getPropertyValue("--uk-" + name)).replace(propertyRe, "$2");
    }
    var propName = memoize(function(name) {
      return vendorPropName(name);
    });
    var cssPrefixes = ["webkit", "moz", "ms"];
    function vendorPropName(name) {
      name = hyphenate(name);
      var ref2 = document.documentElement;
      var style = ref2.style;
      if (name in style) {
        return name;
      }
      var i = cssPrefixes.length, prefixedName;
      while (i--) {
        prefixedName = "-" + cssPrefixes[i] + "-" + name;
        if (prefixedName in style) {
          return prefixedName;
        }
      }
    }
    function transition(element, props2, duration, timing) {
      if (duration === void 0)
        duration = 400;
      if (timing === void 0)
        timing = "linear";
      return Promise$1.all(toNodes(element).map(function(element2) {
        return new Promise$1(function(resolve, reject) {
          for (var name in props2) {
            var value = css(element2, name);
            if (value === "") {
              css(element2, name, value);
            }
          }
          var timer = setTimeout(function() {
            return trigger(element2, "transitionend");
          }, duration);
          once(element2, "transitionend transitioncanceled", function(ref2) {
            var type = ref2.type;
            clearTimeout(timer);
            removeClass(element2, "uk-transition");
            css(element2, {
              transitionProperty: "",
              transitionDuration: "",
              transitionTimingFunction: ""
            });
            type === "transitioncanceled" ? reject() : resolve(element2);
          }, { self: true });
          addClass(element2, "uk-transition");
          css(element2, assign({
            transitionProperty: Object.keys(props2).map(propName).join(","),
            transitionDuration: duration + "ms",
            transitionTimingFunction: timing
          }, props2));
        });
      }));
    }
    var Transition = {
      start: transition,
      stop: function(element) {
        trigger(element, "transitionend");
        return Promise$1.resolve();
      },
      cancel: function(element) {
        trigger(element, "transitioncanceled");
      },
      inProgress: function(element) {
        return hasClass(element, "uk-transition");
      }
    };
    var animationPrefix = "uk-animation-";
    function animate$1(element, animation, duration, origin, out) {
      if (duration === void 0)
        duration = 200;
      return Promise$1.all(toNodes(element).map(function(element2) {
        return new Promise$1(function(resolve, reject) {
          trigger(element2, "animationcanceled");
          var timer = setTimeout(function() {
            return trigger(element2, "animationend");
          }, duration);
          once(element2, "animationend animationcanceled", function(ref2) {
            var type = ref2.type;
            clearTimeout(timer);
            type === "animationcanceled" ? reject() : resolve(element2);
            css(element2, "animationDuration", "");
            removeClasses(element2, animationPrefix + "\\S*");
          }, { self: true });
          css(element2, "animationDuration", duration + "ms");
          addClass(element2, animation, animationPrefix + (out ? "leave" : "enter"));
          if (startsWith(animation, animationPrefix)) {
            origin && addClass(element2, "uk-transform-origin-" + origin);
            out && addClass(element2, animationPrefix + "reverse");
          }
        });
      }));
    }
    var inProgress = new RegExp(animationPrefix + "(enter|leave)");
    var Animation = {
      in: animate$1,
      out: function(element, animation, duration, origin) {
        return animate$1(element, animation, duration, origin, true);
      },
      inProgress: function(element) {
        return inProgress.test(attr(element, "class"));
      },
      cancel: function(element) {
        trigger(element, "animationcanceled");
      }
    };
    var dirs$1 = {
      width: ["left", "right"],
      height: ["top", "bottom"]
    };
    function dimensions(element) {
      var rect = isElement(element) ? toNode(element).getBoundingClientRect() : { height: height(element), width: width(element), top: 0, left: 0 };
      return {
        height: rect.height,
        width: rect.width,
        top: rect.top,
        left: rect.left,
        bottom: rect.top + rect.height,
        right: rect.left + rect.width
      };
    }
    function offset(element, coordinates) {
      var currentOffset = dimensions(element);
      if (element) {
        var ref2 = toWindow(element);
        var pageYOffset = ref2.pageYOffset;
        var pageXOffset = ref2.pageXOffset;
        var offsetBy = { height: pageYOffset, width: pageXOffset };
        for (var dir in dirs$1) {
          for (var i in dirs$1[dir]) {
            currentOffset[dirs$1[dir][i]] += offsetBy[dir];
          }
        }
      }
      if (!coordinates) {
        return currentOffset;
      }
      var pos = css(element, "position");
      each(css(element, ["left", "top"]), function(value, prop) {
        return css(element, prop, coordinates[prop] - currentOffset[prop] + toFloat(pos === "absolute" && value === "auto" ? position(element)[prop] : value));
      });
    }
    function position(element) {
      var ref2 = offset(element);
      var top = ref2.top;
      var left = ref2.left;
      var ref$1 = toNode(element);
      var ref$1_ownerDocument = ref$1.ownerDocument;
      var body = ref$1_ownerDocument.body;
      var documentElement = ref$1_ownerDocument.documentElement;
      var offsetParent = ref$1.offsetParent;
      var parent2 = offsetParent || documentElement;
      while (parent2 && (parent2 === body || parent2 === documentElement) && css(parent2, "position") === "static") {
        parent2 = parent2.parentNode;
      }
      if (isElement(parent2)) {
        var parentOffset = offset(parent2);
        top -= parentOffset.top + toFloat(css(parent2, "borderTopWidth"));
        left -= parentOffset.left + toFloat(css(parent2, "borderLeftWidth"));
      }
      return {
        top: top - toFloat(css(element, "marginTop")),
        left: left - toFloat(css(element, "marginLeft"))
      };
    }
    function offsetPosition(element) {
      var offset2 = [0, 0];
      element = toNode(element);
      do {
        offset2[0] += element.offsetTop;
        offset2[1] += element.offsetLeft;
        if (css(element, "position") === "fixed") {
          var win = toWindow(element);
          offset2[0] += win.pageYOffset;
          offset2[1] += win.pageXOffset;
          return offset2;
        }
      } while (element = element.offsetParent);
      return offset2;
    }
    var height = dimension("height");
    var width = dimension("width");
    function dimension(prop) {
      var propName2 = ucfirst(prop);
      return function(element, value) {
        if (isUndefined(value)) {
          if (isWindow(element)) {
            return element["inner" + propName2];
          }
          if (isDocument(element)) {
            var doc = element.documentElement;
            return Math.max(doc["offset" + propName2], doc["scroll" + propName2]);
          }
          element = toNode(element);
          value = css(element, prop);
          value = value === "auto" ? element["offset" + propName2] : toFloat(value) || 0;
          return value - boxModelAdjust(element, prop);
        } else {
          return css(element, prop, !value && value !== 0 ? "" : +value + boxModelAdjust(element, prop) + "px");
        }
      };
    }
    function boxModelAdjust(element, prop, sizing) {
      if (sizing === void 0)
        sizing = "border-box";
      return css(element, "boxSizing") === sizing ? dirs$1[prop].map(ucfirst).reduce(function(value, prop2) {
        return value + toFloat(css(element, "padding" + prop2)) + toFloat(css(element, "border" + prop2 + "Width"));
      }, 0) : 0;
    }
    function flipPosition(pos) {
      for (var dir in dirs$1) {
        for (var i in dirs$1[dir]) {
          if (dirs$1[dir][i] === pos) {
            return dirs$1[dir][1 - i];
          }
        }
      }
      return pos;
    }
    function toPx(value, property, element) {
      if (property === void 0)
        property = "width";
      if (element === void 0)
        element = window;
      return isNumeric(value) ? +value : endsWith(value, "vh") ? percent(height(toWindow(element)), value) : endsWith(value, "vw") ? percent(width(toWindow(element)), value) : endsWith(value, "%") ? percent(dimensions(element)[property], value) : toFloat(value);
    }
    function percent(base, value) {
      return base * toFloat(value) / 100;
    }
    var fastdom = {
      reads: [],
      writes: [],
      read: function(task) {
        this.reads.push(task);
        scheduleFlush();
        return task;
      },
      write: function(task) {
        this.writes.push(task);
        scheduleFlush();
        return task;
      },
      clear: function(task) {
        remove(this.reads, task);
        remove(this.writes, task);
      },
      flush
    };
    function flush(recursion) {
      if (recursion === void 0)
        recursion = 1;
      runTasks(fastdom.reads);
      runTasks(fastdom.writes.splice(0));
      fastdom.scheduled = false;
      if (fastdom.reads.length || fastdom.writes.length) {
        scheduleFlush(recursion + 1);
      }
    }
    var RECURSION_LIMIT = 4;
    function scheduleFlush(recursion) {
      if (fastdom.scheduled) {
        return;
      }
      fastdom.scheduled = true;
      if (recursion && recursion < RECURSION_LIMIT) {
        Promise$1.resolve().then(function() {
          return flush(recursion);
        });
      } else {
        requestAnimationFrame(function() {
          return flush();
        });
      }
    }
    function runTasks(tasks) {
      var task;
      while (task = tasks.shift()) {
        try {
          task();
        } catch (e) {
          console.error(e);
        }
      }
    }
    function remove(array, item) {
      var index2 = array.indexOf(item);
      return ~index2 && array.splice(index2, 1);
    }
    function MouseTracker() {
    }
    MouseTracker.prototype = {
      positions: [],
      init: function() {
        var this$1$1 = this;
        this.positions = [];
        var position2;
        this.unbind = on(document, "mousemove", function(e) {
          return position2 = getEventPos(e);
        });
        this.interval = setInterval(function() {
          if (!position2) {
            return;
          }
          this$1$1.positions.push(position2);
          if (this$1$1.positions.length > 5) {
            this$1$1.positions.shift();
          }
        }, 50);
      },
      cancel: function() {
        this.unbind && this.unbind();
        this.interval && clearInterval(this.interval);
      },
      movesTo: function(target) {
        if (this.positions.length < 2) {
          return false;
        }
        var p2 = target.getBoundingClientRect();
        var left = p2.left;
        var right = p2.right;
        var top = p2.top;
        var bottom = p2.bottom;
        var ref2 = this.positions;
        var prevPosition = ref2[0];
        var position2 = last(this.positions);
        var path = [prevPosition, position2];
        if (pointInRect(position2, p2)) {
          return false;
        }
        var diagonals = [[{ x: left, y: top }, { x: right, y: bottom }], [{ x: left, y: bottom }, { x: right, y: top }]];
        return diagonals.some(function(diagonal) {
          var intersection = intersect(path, diagonal);
          return intersection && pointInRect(intersection, p2);
        });
      }
    };
    function intersect(ref2, ref$1) {
      var ref_0 = ref2[0];
      var x1 = ref_0.x;
      var y1 = ref_0.y;
      var ref_1 = ref2[1];
      var x2 = ref_1.x;
      var y2 = ref_1.y;
      var ref$1_0 = ref$1[0];
      var x3 = ref$1_0.x;
      var y3 = ref$1_0.y;
      var ref$1_1 = ref$1[1];
      var x4 = ref$1_1.x;
      var y4 = ref$1_1.y;
      var denominator = (y4 - y3) * (x2 - x1) - (x4 - x3) * (y2 - y1);
      if (denominator === 0) {
        return false;
      }
      var ua = ((x4 - x3) * (y1 - y3) - (y4 - y3) * (x1 - x3)) / denominator;
      if (ua < 0) {
        return false;
      }
      return { x: x1 + ua * (x2 - x1), y: y1 + ua * (y2 - y1) };
    }
    var strats = {};
    strats.events = strats.created = strats.beforeConnect = strats.connected = strats.beforeDisconnect = strats.disconnected = strats.destroy = concatStrat;
    strats.args = function(parentVal, childVal) {
      return childVal !== false && concatStrat(childVal || parentVal);
    };
    strats.update = function(parentVal, childVal) {
      return sortBy$1(concatStrat(parentVal, isFunction(childVal) ? { read: childVal } : childVal), "order");
    };
    strats.props = function(parentVal, childVal) {
      if (isArray(childVal)) {
        childVal = childVal.reduce(function(value, key2) {
          value[key2] = String;
          return value;
        }, {});
      }
      return strats.methods(parentVal, childVal);
    };
    strats.computed = strats.methods = function(parentVal, childVal) {
      return childVal ? parentVal ? assign({}, parentVal, childVal) : childVal : parentVal;
    };
    strats.data = function(parentVal, childVal, vm) {
      if (!vm) {
        if (!childVal) {
          return parentVal;
        }
        if (!parentVal) {
          return childVal;
        }
        return function(vm2) {
          return mergeFnData(parentVal, childVal, vm2);
        };
      }
      return mergeFnData(parentVal, childVal, vm);
    };
    function mergeFnData(parentVal, childVal, vm) {
      return strats.computed(isFunction(parentVal) ? parentVal.call(vm, vm) : parentVal, isFunction(childVal) ? childVal.call(vm, vm) : childVal);
    }
    function concatStrat(parentVal, childVal) {
      parentVal = parentVal && !isArray(parentVal) ? [parentVal] : parentVal;
      return childVal ? parentVal ? parentVal.concat(childVal) : isArray(childVal) ? childVal : [childVal] : parentVal;
    }
    function defaultStrat(parentVal, childVal) {
      return isUndefined(childVal) ? parentVal : childVal;
    }
    function mergeOptions(parent2, child, vm) {
      var options = {};
      if (isFunction(child)) {
        child = child.options;
      }
      if (child.extends) {
        parent2 = mergeOptions(parent2, child.extends, vm);
      }
      if (child.mixins) {
        for (var i = 0, l = child.mixins.length; i < l; i++) {
          parent2 = mergeOptions(parent2, child.mixins[i], vm);
        }
      }
      for (var key2 in parent2) {
        mergeKey(key2);
      }
      for (var key$1 in child) {
        if (!hasOwn(parent2, key$1)) {
          mergeKey(key$1);
        }
      }
      function mergeKey(key3) {
        options[key3] = (strats[key3] || defaultStrat)(parent2[key3], child[key3], vm);
      }
      return options;
    }
    function parseOptions(options, args) {
      var obj2;
      if (args === void 0)
        args = [];
      try {
        return !options ? {} : startsWith(options, "{") ? JSON.parse(options) : args.length && !includes(options, ":") ? (obj2 = {}, obj2[args[0]] = options, obj2) : options.split(";").reduce(function(options2, option) {
          var ref2 = option.split(/:(.*)/);
          var key2 = ref2[0];
          var value = ref2[1];
          if (key2 && !isUndefined(value)) {
            options2[key2.trim()] = value.trim();
          }
          return options2;
        }, {});
      } catch (e) {
        return {};
      }
    }
    function play(el) {
      if (isIFrame(el)) {
        call(el, { func: "playVideo", method: "play" });
      }
      if (isHTML5(el)) {
        try {
          el.play().catch(noop);
        } catch (e) {
        }
      }
    }
    function pause(el) {
      if (isIFrame(el)) {
        call(el, { func: "pauseVideo", method: "pause" });
      }
      if (isHTML5(el)) {
        el.pause();
      }
    }
    function mute(el) {
      if (isIFrame(el)) {
        call(el, { func: "mute", method: "setVolume", value: 0 });
      }
      if (isHTML5(el)) {
        el.muted = true;
      }
    }
    function isVideo(el) {
      return isHTML5(el) || isIFrame(el);
    }
    function isHTML5(el) {
      return el && el.tagName === "VIDEO";
    }
    function isIFrame(el) {
      return el && el.tagName === "IFRAME" && (isYoutube(el) || isVimeo(el));
    }
    function isYoutube(el) {
      return !!el.src.match(/\/\/.*?youtube(-nocookie)?\.[a-z]+\/(watch\?v=[^&\s]+|embed)|youtu\.be\/.*/);
    }
    function isVimeo(el) {
      return !!el.src.match(/vimeo\.com\/video\/.*/);
    }
    function call(el, cmd) {
      enableApi(el).then(function() {
        return post(el, cmd);
      });
    }
    function post(el, cmd) {
      try {
        el.contentWindow.postMessage(JSON.stringify(assign({ event: "command" }, cmd)), "*");
      } catch (e) {
      }
    }
    var stateKey$1 = "_ukPlayer";
    var counter = 0;
    function enableApi(el) {
      if (el[stateKey$1]) {
        return el[stateKey$1];
      }
      var youtube = isYoutube(el);
      var vimeo = isVimeo(el);
      var id = ++counter;
      var poller;
      return el[stateKey$1] = new Promise$1(function(resolve) {
        youtube && once(el, "load", function() {
          var listener = function() {
            return post(el, { event: "listening", id });
          };
          poller = setInterval(listener, 100);
          listener();
        });
        once(window, "message", resolve, false, function(ref2) {
          var data2 = ref2.data;
          try {
            data2 = JSON.parse(data2);
            return data2 && (youtube && data2.id === id && data2.event === "onReady" || vimeo && Number(data2.player_id) === id);
          } catch (e) {
          }
        });
        el.src = "" + el.src + (includes(el.src, "?") ? "&" : "?") + (youtube ? "enablejsapi=1" : "api=1&player_id=" + id);
      }).then(function() {
        return clearInterval(poller);
      });
    }
    function isInView(element, offsetTop, offsetLeft) {
      if (offsetTop === void 0)
        offsetTop = 0;
      if (offsetLeft === void 0)
        offsetLeft = 0;
      if (!isVisible(element)) {
        return false;
      }
      return intersectRect.apply(void 0, scrollParents(element).map(function(parent2) {
        var ref2 = offset(getViewport$1(parent2));
        var top = ref2.top;
        var left = ref2.left;
        var bottom = ref2.bottom;
        var right = ref2.right;
        return {
          top: top - offsetTop,
          left: left - offsetLeft,
          bottom: bottom + offsetTop,
          right: right + offsetLeft
        };
      }).concat(offset(element)));
    }
    function scrollTop(element, top) {
      if (isWindow(element) || isDocument(element)) {
        element = getScrollingElement(element);
      } else {
        element = toNode(element);
      }
      element.scrollTop = top;
    }
    function scrollIntoView(element, ref2) {
      if (ref2 === void 0)
        ref2 = {};
      var offsetBy = ref2.offset;
      if (offsetBy === void 0)
        offsetBy = 0;
      var parents2 = isVisible(element) ? scrollParents(element) : [];
      return parents2.reduce(function(fn, scrollElement, i) {
        var scrollTop2 = scrollElement.scrollTop;
        var scrollHeight = scrollElement.scrollHeight;
        var offsetHeight = scrollElement.offsetHeight;
        var maxScroll = scrollHeight - getViewportClientHeight(scrollElement);
        var ref3 = offset(parents2[i - 1] || element);
        var elHeight = ref3.height;
        var elTop = ref3.top;
        var top = Math.ceil(elTop - offset(getViewport$1(scrollElement)).top - offsetBy + scrollTop2);
        if (offsetBy > 0 && offsetHeight < elHeight + offsetBy) {
          top += offsetBy;
        } else {
          offsetBy = 0;
        }
        if (top > maxScroll) {
          offsetBy -= top - maxScroll;
          top = maxScroll;
        } else if (top < 0) {
          offsetBy -= top;
          top = 0;
        }
        return function() {
          return scrollTo(scrollElement, top - scrollTop2).then(fn);
        };
      }, function() {
        return Promise$1.resolve();
      })();
      function scrollTo(element2, top) {
        return new Promise$1(function(resolve) {
          var scroll2 = element2.scrollTop;
          var duration = getDuration(Math.abs(top));
          var start = Date.now();
          (function step() {
            var percent2 = ease2(clamp((Date.now() - start) / duration));
            scrollTop(element2, scroll2 + top * percent2);
            if (percent2 === 1) {
              resolve();
            } else {
              requestAnimationFrame(step);
            }
          })();
        });
      }
      function getDuration(dist) {
        return 40 * Math.pow(dist, 0.375);
      }
      function ease2(k) {
        return 0.5 * (1 - Math.cos(Math.PI * k));
      }
    }
    function scrolledOver(element, heightOffset) {
      if (heightOffset === void 0)
        heightOffset = 0;
      if (!isVisible(element)) {
        return 0;
      }
      var ref2 = scrollParents(element, /auto|scroll/, true);
      var scrollElement = ref2[0];
      var scrollHeight = scrollElement.scrollHeight;
      var scrollTop2 = scrollElement.scrollTop;
      var clientHeight = getViewportClientHeight(scrollElement);
      var viewportTop = offsetPosition(element)[0] - scrollTop2 - offsetPosition(scrollElement)[0];
      var viewportDist = Math.min(clientHeight, viewportTop + scrollTop2);
      var top = viewportTop - viewportDist;
      var dist = Math.min(element.offsetHeight + heightOffset + viewportDist, scrollHeight - (viewportTop + scrollTop2), scrollHeight - clientHeight);
      return clamp(-1 * top / dist);
    }
    function scrollParents(element, overflowRe, scrollable) {
      if (overflowRe === void 0)
        overflowRe = /auto|scroll|hidden/;
      if (scrollable === void 0)
        scrollable = false;
      var scrollEl = getScrollingElement(element);
      var ancestors = parents(element).reverse();
      ancestors = ancestors.slice(ancestors.indexOf(scrollEl) + 1);
      var fixedIndex = findIndex(ancestors, function(el) {
        return css(el, "position") === "fixed";
      });
      if (~fixedIndex) {
        ancestors = ancestors.slice(fixedIndex);
      }
      return [scrollEl].concat(ancestors.filter(function(parent2) {
        return overflowRe.test(css(parent2, "overflow")) && (!scrollable || parent2.scrollHeight > getViewportClientHeight(parent2));
      })).reverse();
    }
    function getViewport$1(scrollElement) {
      return scrollElement === getScrollingElement(scrollElement) ? window : scrollElement;
    }
    function getViewportClientHeight(scrollElement) {
      return (scrollElement === getScrollingElement(scrollElement) ? document.documentElement : scrollElement).clientHeight;
    }
    function getScrollingElement(element) {
      var ref2 = toWindow(element);
      var document2 = ref2.document;
      return document2.scrollingElement || document2.documentElement;
    }
    var dirs = {
      width: ["x", "left", "right"],
      height: ["y", "top", "bottom"]
    };
    function positionAt(element, target, elAttach, targetAttach, elOffset, targetOffset, flip, boundary) {
      elAttach = getPos(elAttach);
      targetAttach = getPos(targetAttach);
      var flipped = { element: elAttach, target: targetAttach };
      if (!element || !target) {
        return flipped;
      }
      var dim = offset(element);
      var targetDim = offset(target);
      var position2 = targetDim;
      moveTo(position2, elAttach, dim, -1);
      moveTo(position2, targetAttach, targetDim, 1);
      elOffset = getOffsets(elOffset, dim.width, dim.height);
      targetOffset = getOffsets(targetOffset, targetDim.width, targetDim.height);
      elOffset["x"] += targetOffset["x"];
      elOffset["y"] += targetOffset["y"];
      position2.left += elOffset["x"];
      position2.top += elOffset["y"];
      if (flip) {
        var boundaries = scrollParents(element).map(getViewport$1);
        if (boundary && !includes(boundaries, boundary)) {
          boundaries.unshift(boundary);
        }
        boundaries = boundaries.map(function(el) {
          return offset(el);
        });
        each(dirs, function(ref2, prop) {
          var dir = ref2[0];
          var align = ref2[1];
          var alignFlip = ref2[2];
          if (!(flip === true || includes(flip, dir))) {
            return;
          }
          boundaries.some(function(boundary2) {
            var elemOffset = elAttach[dir] === align ? -dim[prop] : elAttach[dir] === alignFlip ? dim[prop] : 0;
            var targetOffset2 = targetAttach[dir] === align ? targetDim[prop] : targetAttach[dir] === alignFlip ? -targetDim[prop] : 0;
            if (position2[align] < boundary2[align] || position2[align] + dim[prop] > boundary2[alignFlip]) {
              var centerOffset = dim[prop] / 2;
              var centerTargetOffset = targetAttach[dir] === "center" ? -targetDim[prop] / 2 : 0;
              return elAttach[dir] === "center" && (apply2(centerOffset, centerTargetOffset) || apply2(-centerOffset, -centerTargetOffset)) || apply2(elemOffset, targetOffset2);
            }
            function apply2(elemOffset2, targetOffset3) {
              var newVal = toFloat((position2[align] + elemOffset2 + targetOffset3 - elOffset[dir] * 2).toFixed(4));
              if (newVal >= boundary2[align] && newVal + dim[prop] <= boundary2[alignFlip]) {
                position2[align] = newVal;
                ["element", "target"].forEach(function(el) {
                  flipped[el][dir] = !elemOffset2 ? flipped[el][dir] : flipped[el][dir] === dirs[prop][1] ? dirs[prop][2] : dirs[prop][1];
                });
                return true;
              }
            }
          });
        });
      }
      offset(element, position2);
      return flipped;
    }
    function moveTo(position2, attach, dim, factor) {
      each(dirs, function(ref2, prop) {
        var dir = ref2[0];
        var align = ref2[1];
        var alignFlip = ref2[2];
        if (attach[dir] === alignFlip) {
          position2[align] += dim[prop] * factor;
        } else if (attach[dir] === "center") {
          position2[align] += dim[prop] * factor / 2;
        }
      });
    }
    function getPos(pos) {
      var x = /left|center|right/;
      var y = /top|center|bottom/;
      pos = (pos || "").split(" ");
      if (pos.length === 1) {
        pos = x.test(pos[0]) ? pos.concat("center") : y.test(pos[0]) ? ["center"].concat(pos) : ["center", "center"];
      }
      return {
        x: x.test(pos[0]) ? pos[0] : "center",
        y: y.test(pos[1]) ? pos[1] : "center"
      };
    }
    function getOffsets(offsets, width2, height2) {
      var ref2 = (offsets || "").split(" ");
      var x = ref2[0];
      var y = ref2[1];
      return {
        x: x ? toFloat(x) * (endsWith(x, "%") ? width2 / 100 : 1) : 0,
        y: y ? toFloat(y) * (endsWith(y, "%") ? height2 / 100 : 1) : 0
      };
    }
    var util = /* @__PURE__ */ Object.freeze({
      __proto__: null,
      ajax,
      getImage,
      transition,
      Transition,
      animate: animate$1,
      Animation,
      attr,
      hasAttr,
      removeAttr,
      data,
      addClass,
      removeClass,
      removeClasses,
      replaceClass,
      hasClass,
      toggleClass,
      dimensions,
      offset,
      position,
      offsetPosition,
      height,
      width,
      boxModelAdjust,
      flipPosition,
      toPx,
      ready,
      empty,
      html,
      prepend,
      append,
      before,
      after,
      remove: remove$1,
      wrapAll,
      wrapInner,
      unwrap,
      fragment,
      apply: apply$1,
      $,
      $$,
      inBrowser,
      isIE,
      isRtl,
      hasTouch,
      pointerDown,
      pointerMove,
      pointerUp,
      pointerEnter,
      pointerLeave,
      pointerCancel,
      on,
      off,
      once,
      trigger,
      createEvent,
      toEventTargets,
      isTouch,
      getEventPos,
      fastdom,
      isVoidElement,
      isVisible,
      selInput,
      isInput,
      selFocusable,
      isFocusable,
      parent,
      filter: filter$1,
      matches,
      closest,
      within,
      parents,
      children,
      index,
      hasOwn,
      hyphenate,
      camelize,
      ucfirst,
      startsWith,
      endsWith,
      includes,
      findIndex,
      isArray,
      isFunction,
      isObject,
      isPlainObject,
      isWindow,
      isDocument,
      isNode,
      isElement,
      isBoolean,
      isString,
      isNumber,
      isNumeric,
      isEmpty,
      isUndefined,
      toBoolean,
      toNumber,
      toFloat,
      toArray,
      toNode,
      toNodes,
      toWindow,
      toMs,
      isEqual,
      swap,
      assign,
      last,
      each,
      sortBy: sortBy$1,
      uniqueBy,
      clamp,
      noop,
      intersectRect,
      pointInRect,
      Dimensions,
      getIndex,
      memoize,
      MouseTracker,
      mergeOptions,
      parseOptions,
      play,
      pause,
      mute,
      isVideo,
      positionAt,
      Promise: Promise$1,
      Deferred,
      query,
      queryAll,
      find,
      findAll,
      escape,
      css,
      getCssVar,
      propName,
      isInView,
      scrollTop,
      scrollIntoView,
      scrolledOver,
      scrollParents,
      getViewport: getViewport$1,
      getViewportClientHeight
    });
    function globalAPI(UIkit3) {
      var DATA = UIkit3.data;
      UIkit3.use = function(plugin) {
        if (plugin.installed) {
          return;
        }
        plugin.call(null, this);
        plugin.installed = true;
        return this;
      };
      UIkit3.mixin = function(mixin, component) {
        component = (isString(component) ? UIkit3.component(component) : component) || this;
        component.options = mergeOptions(component.options, mixin);
      };
      UIkit3.extend = function(options) {
        options = options || {};
        var Super = this;
        var Sub = function UIkitComponent(options2) {
          this._init(options2);
        };
        Sub.prototype = Object.create(Super.prototype);
        Sub.prototype.constructor = Sub;
        Sub.options = mergeOptions(Super.options, options);
        Sub.super = Super;
        Sub.extend = Super.extend;
        return Sub;
      };
      UIkit3.update = function(element, e) {
        element = element ? toNode(element) : document.body;
        parents(element).reverse().forEach(function(element2) {
          return update(element2[DATA], e);
        });
        apply$1(element, function(element2) {
          return update(element2[DATA], e);
        });
      };
      var container;
      Object.defineProperty(UIkit3, "container", {
        get: function() {
          return container || document.body;
        },
        set: function(element) {
          container = $(element);
        }
      });
      function update(data2, e) {
        if (!data2) {
          return;
        }
        for (var name in data2) {
          if (data2[name]._connected) {
            data2[name]._callUpdate(e);
          }
        }
      }
    }
    function hooksAPI(UIkit3) {
      UIkit3.prototype._callHook = function(hook) {
        var this$1$1 = this;
        var handlers = this.$options[hook];
        if (handlers) {
          handlers.forEach(function(handler) {
            return handler.call(this$1$1);
          });
        }
      };
      UIkit3.prototype._callConnected = function() {
        if (this._connected) {
          return;
        }
        this._data = {};
        this._computeds = {};
        this._initProps();
        this._callHook("beforeConnect");
        this._connected = true;
        this._initEvents();
        this._initObservers();
        this._callHook("connected");
        this._callUpdate();
      };
      UIkit3.prototype._callDisconnected = function() {
        if (!this._connected) {
          return;
        }
        this._callHook("beforeDisconnect");
        this._disconnectObservers();
        this._unbindEvents();
        this._callHook("disconnected");
        this._connected = false;
        delete this._watch;
      };
      UIkit3.prototype._callUpdate = function(e) {
        var this$1$1 = this;
        if (e === void 0)
          e = "update";
        if (!this._connected) {
          return;
        }
        if (e === "update" || e === "resize") {
          this._callWatches();
        }
        if (!this.$options.update) {
          return;
        }
        if (!this._updates) {
          this._updates = new Set();
          fastdom.read(function() {
            if (this$1$1._connected) {
              runUpdates.call(this$1$1, this$1$1._updates);
            }
            delete this$1$1._updates;
          });
        }
        this._updates.add(e.type || e);
      };
      UIkit3.prototype._callWatches = function() {
        var this$1$1 = this;
        if (this._watch) {
          return;
        }
        var initial = !hasOwn(this, "_watch");
        this._watch = fastdom.read(function() {
          if (this$1$1._connected) {
            runWatches.call(this$1$1, initial);
          }
          this$1$1._watch = null;
        });
      };
      function runUpdates(types) {
        var this$1$1 = this;
        var updates = this.$options.update;
        var loop = function(i2) {
          var ref2 = updates[i2];
          var read = ref2.read;
          var write = ref2.write;
          var events = ref2.events;
          if (!types.has("update") && (!events || !events.some(function(type) {
            return types.has(type);
          }))) {
            return;
          }
          var result = void 0;
          if (read) {
            result = read.call(this$1$1, this$1$1._data, types);
            if (result && isPlainObject(result)) {
              assign(this$1$1._data, result);
            }
          }
          if (write && result !== false) {
            fastdom.write(function() {
              return write.call(this$1$1, this$1$1._data, types);
            });
          }
        };
        for (var i = 0; i < updates.length; i++)
          loop(i);
      }
      function runWatches(initial) {
        var ref2 = this;
        var computed2 = ref2.$options.computed;
        var _computeds = ref2._computeds;
        for (var key2 in computed2) {
          var hasPrev = hasOwn(_computeds, key2);
          var prev = _computeds[key2];
          delete _computeds[key2];
          var ref$1 = computed2[key2];
          var watch2 = ref$1.watch;
          var immediate = ref$1.immediate;
          if (watch2 && (initial && immediate || hasPrev && !isEqual(prev, this[key2]))) {
            watch2.call(this, this[key2], prev);
          }
        }
      }
    }
    function stateAPI(UIkit3) {
      var uid = 0;
      UIkit3.prototype._init = function(options) {
        options = options || {};
        options.data = normalizeData(options, this.constructor.options);
        this.$options = mergeOptions(this.constructor.options, options, this);
        this.$el = null;
        this.$props = {};
        this._uid = uid++;
        this._initData();
        this._initMethods();
        this._initComputeds();
        this._callHook("created");
        if (options.el) {
          this.$mount(options.el);
        }
      };
      UIkit3.prototype._initData = function() {
        var ref2 = this.$options;
        var data2 = ref2.data;
        if (data2 === void 0)
          data2 = {};
        for (var key2 in data2) {
          this.$props[key2] = this[key2] = data2[key2];
        }
      };
      UIkit3.prototype._initMethods = function() {
        var ref2 = this.$options;
        var methods = ref2.methods;
        if (methods) {
          for (var key2 in methods) {
            this[key2] = methods[key2].bind(this);
          }
        }
      };
      UIkit3.prototype._initComputeds = function() {
        var ref2 = this.$options;
        var computed2 = ref2.computed;
        this._computeds = {};
        if (computed2) {
          for (var key2 in computed2) {
            registerComputed(this, key2, computed2[key2]);
          }
        }
      };
      UIkit3.prototype._initProps = function(props2) {
        var key2;
        props2 = props2 || getProps2(this.$options, this.$name);
        for (key2 in props2) {
          if (!isUndefined(props2[key2])) {
            this.$props[key2] = props2[key2];
          }
        }
        var exclude = [this.$options.computed, this.$options.methods];
        for (key2 in this.$props) {
          if (key2 in props2 && notIn(exclude, key2)) {
            this[key2] = this.$props[key2];
          }
        }
      };
      UIkit3.prototype._initEvents = function() {
        var this$1$1 = this;
        this._events = [];
        var ref2 = this.$options;
        var events = ref2.events;
        if (events) {
          events.forEach(function(event) {
            if (hasOwn(event, "handler")) {
              registerEvent(this$1$1, event);
            } else {
              for (var key2 in event) {
                registerEvent(this$1$1, event[key2], key2);
              }
            }
          });
        }
      };
      UIkit3.prototype._unbindEvents = function() {
        this._events.forEach(function(unbind) {
          return unbind();
        });
        delete this._events;
      };
      UIkit3.prototype._initObservers = function() {
        this._observers = [
          initChildListObserver(this),
          initPropsObserver(this)
        ];
      };
      UIkit3.prototype._disconnectObservers = function() {
        this._observers.forEach(function(observer) {
          return observer && observer.disconnect();
        });
      };
      function getProps2(opts, name) {
        var data$1 = {};
        var args = opts.args;
        if (args === void 0)
          args = [];
        var props2 = opts.props;
        if (props2 === void 0)
          props2 = {};
        var el = opts.el;
        if (!props2) {
          return data$1;
        }
        for (var key2 in props2) {
          var prop = hyphenate(key2);
          var value = data(el, prop);
          if (isUndefined(value)) {
            continue;
          }
          value = props2[key2] === Boolean && value === "" ? true : coerce(props2[key2], value);
          if (prop === "target" && (!value || startsWith(value, "_"))) {
            continue;
          }
          data$1[key2] = value;
        }
        var options = parseOptions(data(el, name), args);
        for (var key$1 in options) {
          var prop$1 = camelize(key$1);
          if (props2[prop$1] !== void 0) {
            data$1[prop$1] = coerce(props2[prop$1], options[key$1]);
          }
        }
        return data$1;
      }
      function registerComputed(component, key2, cb) {
        Object.defineProperty(component, key2, {
          enumerable: true,
          get: function() {
            var _computeds = component._computeds;
            var $props = component.$props;
            var $el = component.$el;
            if (!hasOwn(_computeds, key2)) {
              _computeds[key2] = (cb.get || cb).call(component, $props, $el);
            }
            return _computeds[key2];
          },
          set: function(value) {
            var _computeds = component._computeds;
            _computeds[key2] = cb.set ? cb.set.call(component, value) : value;
            if (isUndefined(_computeds[key2])) {
              delete _computeds[key2];
            }
          }
        });
      }
      function registerEvent(component, event, key2) {
        if (!isPlainObject(event)) {
          event = { name: key2, handler: event };
        }
        var name = event.name;
        var el = event.el;
        var handler = event.handler;
        var capture = event.capture;
        var passive = event.passive;
        var delegate2 = event.delegate;
        var filter2 = event.filter;
        var self2 = event.self;
        el = isFunction(el) ? el.call(component) : el || component.$el;
        if (isArray(el)) {
          el.forEach(function(el2) {
            return registerEvent(component, assign({}, event, { el: el2 }), key2);
          });
          return;
        }
        if (!el || filter2 && !filter2.call(component)) {
          return;
        }
        component._events.push(on(el, name, !delegate2 ? null : isString(delegate2) ? delegate2 : delegate2.call(component), isString(handler) ? component[handler] : handler.bind(component), { passive, capture, self: self2 }));
      }
      function notIn(options, key2) {
        return options.every(function(arr) {
          return !arr || !hasOwn(arr, key2);
        });
      }
      function coerce(type, value) {
        if (type === Boolean) {
          return toBoolean(value);
        } else if (type === Number) {
          return toNumber(value);
        } else if (type === "list") {
          return toList(value);
        }
        return type ? type(value) : value;
      }
      function toList(value) {
        return isArray(value) ? value : isString(value) ? value.split(/,(?![^(]*\))/).map(function(value2) {
          return isNumeric(value2) ? toNumber(value2) : toBoolean(value2.trim());
        }) : [value];
      }
      function normalizeData(ref2, ref$1) {
        var data2 = ref2.data;
        var args = ref$1.args;
        var props2 = ref$1.props;
        if (props2 === void 0)
          props2 = {};
        data2 = isArray(data2) ? !isEmpty(args) ? data2.slice(0, args.length).reduce(function(data3, value, index2) {
          if (isPlainObject(value)) {
            assign(data3, value);
          } else {
            data3[args[index2]] = value;
          }
          return data3;
        }, {}) : void 0 : data2;
        if (data2) {
          for (var key2 in data2) {
            if (isUndefined(data2[key2])) {
              delete data2[key2];
            } else {
              data2[key2] = props2[key2] ? coerce(props2[key2], data2[key2]) : data2[key2];
            }
          }
        }
        return data2;
      }
      function initChildListObserver(component) {
        var ref2 = component.$options;
        var el = ref2.el;
        var observer = new MutationObserver(function() {
          return component.$emit();
        });
        observer.observe(el, {
          childList: true,
          subtree: true
        });
        return observer;
      }
      function initPropsObserver(component) {
        var $name = component.$name;
        var $options = component.$options;
        var $props = component.$props;
        var attrs = $options.attrs;
        var props2 = $options.props;
        var el = $options.el;
        if (!props2 || attrs === false) {
          return;
        }
        var attributes = isArray(attrs) ? attrs : Object.keys(props2);
        var filter2 = attributes.map(function(key2) {
          return hyphenate(key2);
        }).concat($name);
        var observer = new MutationObserver(function(records) {
          var data2 = getProps2($options, $name);
          if (records.some(function(ref2) {
            var attributeName = ref2.attributeName;
            var prop = attributeName.replace("data-", "");
            return (prop === $name ? attributes : [camelize(prop), camelize(attributeName)]).some(function(prop2) {
              return !isUndefined(data2[prop2]) && data2[prop2] !== $props[prop2];
            });
          })) {
            component.$reset();
          }
        });
        observer.observe(el, {
          attributes: true,
          attributeFilter: filter2.concat(filter2.map(function(key2) {
            return "data-" + key2;
          }))
        });
        return observer;
      }
    }
    function instanceAPI(UIkit3) {
      var DATA = UIkit3.data;
      UIkit3.prototype.$create = function(component, element, data2) {
        return UIkit3[component](element, data2);
      };
      UIkit3.prototype.$mount = function(el) {
        var ref2 = this.$options;
        var name = ref2.name;
        if (!el[DATA]) {
          el[DATA] = {};
        }
        if (el[DATA][name]) {
          return;
        }
        el[DATA][name] = this;
        this.$el = this.$options.el = this.$options.el || el;
        if (within(el, document)) {
          this._callConnected();
        }
      };
      UIkit3.prototype.$reset = function() {
        this._callDisconnected();
        this._callConnected();
      };
      UIkit3.prototype.$destroy = function(removeEl) {
        if (removeEl === void 0)
          removeEl = false;
        var ref2 = this.$options;
        var el = ref2.el;
        var name = ref2.name;
        if (el) {
          this._callDisconnected();
        }
        this._callHook("destroy");
        if (!el || !el[DATA]) {
          return;
        }
        delete el[DATA][name];
        if (!isEmpty(el[DATA])) {
          delete el[DATA];
        }
        if (removeEl) {
          remove$1(this.$el);
        }
      };
      UIkit3.prototype.$emit = function(e) {
        this._callUpdate(e);
      };
      UIkit3.prototype.$update = function(element, e) {
        if (element === void 0)
          element = this.$el;
        UIkit3.update(element, e);
      };
      UIkit3.prototype.$getComponent = UIkit3.getComponent;
      var componentName = memoize(function(name) {
        return UIkit3.prefix + hyphenate(name);
      });
      Object.defineProperties(UIkit3.prototype, {
        $container: Object.getOwnPropertyDescriptor(UIkit3, "container"),
        $name: {
          get: function() {
            return componentName(this.$options.name);
          }
        }
      });
    }
    function componentAPI(UIkit3) {
      var DATA = UIkit3.data;
      var components2 = {};
      UIkit3.component = function(name, options) {
        var id = hyphenate(name);
        name = camelize(id);
        if (!options) {
          if (isPlainObject(components2[name])) {
            components2[name] = UIkit3.extend(components2[name]);
          }
          return components2[name];
        }
        UIkit3[name] = function(element, data2) {
          var i = arguments.length, argsArray = Array(i);
          while (i--)
            argsArray[i] = arguments[i];
          var component = UIkit3.component(name);
          return component.options.functional ? new component({ data: isPlainObject(element) ? element : [].concat(argsArray) }) : !element ? init(element) : $$(element).map(init)[0];
          function init(element2) {
            var instance = UIkit3.getComponent(element2, name);
            if (instance) {
              if (data2) {
                instance.$destroy();
              } else {
                return instance;
              }
            }
            return new component({ el: element2, data: data2 });
          }
        };
        var opt = isPlainObject(options) ? assign({}, options) : options.options;
        opt.name = name;
        if (opt.install) {
          opt.install(UIkit3, opt, name);
        }
        if (UIkit3._initialized && !opt.functional) {
          fastdom.read(function() {
            return UIkit3[name]("[uk-" + id + "],[data-uk-" + id + "]");
          });
        }
        return components2[name] = isPlainObject(options) ? opt : options;
      };
      UIkit3.getComponents = function(element) {
        return element && element[DATA] || {};
      };
      UIkit3.getComponent = function(element, name) {
        return UIkit3.getComponents(element)[name];
      };
      UIkit3.connect = function(node) {
        if (node[DATA]) {
          for (var name in node[DATA]) {
            node[DATA][name]._callConnected();
          }
        }
        for (var i = 0; i < node.attributes.length; i++) {
          var name$1 = getComponentName(node.attributes[i].name);
          if (name$1 && name$1 in components2) {
            UIkit3[name$1](node);
          }
        }
      };
      UIkit3.disconnect = function(node) {
        for (var name in node[DATA]) {
          node[DATA][name]._callDisconnected();
        }
      };
    }
    var getComponentName = memoize(function(attribute) {
      return startsWith(attribute, "uk-") || startsWith(attribute, "data-uk-") ? camelize(attribute.replace("data-uk-", "").replace("uk-", "")) : false;
    });
    var UIkit2 = function(options) {
      this._init(options);
    };
    UIkit2.util = util;
    UIkit2.data = "__uikit__";
    UIkit2.prefix = "uk-";
    UIkit2.options = {};
    UIkit2.version = "3.10.1";
    globalAPI(UIkit2);
    hooksAPI(UIkit2);
    stateAPI(UIkit2);
    componentAPI(UIkit2);
    instanceAPI(UIkit2);
    function Core(UIkit3) {
      if (!inBrowser) {
        return;
      }
      var pendingResize;
      var handleResize = function() {
        if (pendingResize) {
          return;
        }
        pendingResize = true;
        fastdom.write(function() {
          return pendingResize = false;
        });
        UIkit3.update(null, "resize");
      };
      on(window, "load resize", handleResize);
      on(document, "loadedmetadata load", handleResize, true);
      if ("ResizeObserver" in window) {
        new ResizeObserver(handleResize).observe(document.documentElement);
      }
      var pending;
      on(window, "scroll", function(e) {
        if (pending) {
          return;
        }
        pending = true;
        fastdom.write(function() {
          return pending = false;
        });
        UIkit3.update(null, e.type);
      }, { passive: true, capture: true });
      var started = 0;
      on(document, "animationstart", function(ref2) {
        var target = ref2.target;
        if ((css(target, "animationName") || "").match(/^uk-.*(left|right)/)) {
          started++;
          css(document.documentElement, "overflowX", "hidden");
          setTimeout(function() {
            if (!--started) {
              css(document.documentElement, "overflowX", "");
            }
          }, toMs(css(target, "animationDuration")) + 100);
        }
      }, true);
      on(document, pointerDown, function(e) {
        if (!isTouch(e)) {
          return;
        }
        var pos = getEventPos(e);
        var target = "tagName" in e.target ? e.target : parent(e.target);
        once(document, pointerUp + " " + pointerCancel + " scroll", function(e2) {
          var ref2 = getEventPos(e2);
          var x = ref2.x;
          var y = ref2.y;
          if (e2.type !== "scroll" && target && x && Math.abs(pos.x - x) > 100 || y && Math.abs(pos.y - y) > 100) {
            setTimeout(function() {
              trigger(target, "swipe");
              trigger(target, "swipe" + swipeDirection(pos.x, pos.y, x, y));
            });
          }
        });
      }, { passive: true });
    }
    function swipeDirection(x1, y1, x2, y2) {
      return Math.abs(x1 - x2) >= Math.abs(y1 - y2) ? x1 - x2 > 0 ? "Left" : "Right" : y1 - y2 > 0 ? "Up" : "Down";
    }
    function boot(UIkit3) {
      var connect = UIkit3.connect;
      var disconnect = UIkit3.disconnect;
      if (!inBrowser || !window.MutationObserver) {
        return;
      }
      fastdom.read(function() {
        if (document.body) {
          apply$1(document.body, connect);
        }
        new MutationObserver(function(records) {
          return records.forEach(applyChildListMutation);
        }).observe(document, {
          childList: true,
          subtree: true
        });
        new MutationObserver(function(records) {
          return records.forEach(applyAttributeMutation);
        }).observe(document, {
          attributes: true,
          subtree: true
        });
        UIkit3._initialized = true;
      });
      function applyChildListMutation(ref2) {
        var addedNodes = ref2.addedNodes;
        var removedNodes = ref2.removedNodes;
        for (var i = 0; i < addedNodes.length; i++) {
          apply$1(addedNodes[i], connect);
        }
        for (var i$1 = 0; i$1 < removedNodes.length; i$1++) {
          apply$1(removedNodes[i$1], disconnect);
        }
      }
      function applyAttributeMutation(ref2) {
        var target = ref2.target;
        var attributeName = ref2.attributeName;
        var name = getComponentName(attributeName);
        if (!name || !(name in UIkit3)) {
          return;
        }
        if (hasAttr(target, attributeName)) {
          UIkit3[name](target);
          return;
        }
        var component = UIkit3.getComponent(target, name);
        if (component) {
          component.$destroy();
        }
      }
    }
    var Class = {
      connected: function() {
        !hasClass(this.$el, this.$name) && addClass(this.$el, this.$name);
      }
    };
    var Togglable = {
      props: {
        cls: Boolean,
        animation: "list",
        duration: Number,
        origin: String,
        transition: String
      },
      data: {
        cls: false,
        animation: [false],
        duration: 200,
        origin: false,
        transition: "linear",
        clsEnter: "uk-togglabe-enter",
        clsLeave: "uk-togglabe-leave",
        initProps: {
          overflow: "",
          height: "",
          paddingTop: "",
          paddingBottom: "",
          marginTop: "",
          marginBottom: ""
        },
        hideProps: {
          overflow: "hidden",
          height: 0,
          paddingTop: 0,
          paddingBottom: 0,
          marginTop: 0,
          marginBottom: 0
        }
      },
      computed: {
        hasAnimation: function(ref2) {
          var animation = ref2.animation;
          return !!animation[0];
        },
        hasTransition: function(ref2) {
          var animation = ref2.animation;
          return this.hasAnimation && animation[0] === true;
        }
      },
      methods: {
        toggleElement: function(targets, toggle2, animate2) {
          var this$1$1 = this;
          return new Promise$1(function(resolve) {
            return Promise$1.all(toNodes(targets).map(function(el) {
              var show = isBoolean(toggle2) ? toggle2 : !this$1$1.isToggled(el);
              if (!trigger(el, "before" + (show ? "show" : "hide"), [this$1$1])) {
                return Promise$1.reject();
              }
              var promise = (isFunction(animate2) ? animate2 : animate2 === false || !this$1$1.hasAnimation ? this$1$1._toggle : this$1$1.hasTransition ? toggleHeight(this$1$1) : toggleAnimation(this$1$1))(el, show);
              var cls = show ? this$1$1.clsEnter : this$1$1.clsLeave;
              addClass(el, cls);
              trigger(el, show ? "show" : "hide", [this$1$1]);
              var done = function() {
                removeClass(el, cls);
                trigger(el, show ? "shown" : "hidden", [this$1$1]);
                this$1$1.$update(el);
              };
              return promise ? promise.then(done, function() {
                removeClass(el, cls);
                return Promise$1.reject();
              }) : done();
            })).then(resolve, noop);
          });
        },
        isToggled: function(el) {
          var assign2;
          if (el === void 0)
            el = this.$el;
          assign2 = toNodes(el), el = assign2[0];
          return hasClass(el, this.clsEnter) ? true : hasClass(el, this.clsLeave) ? false : this.cls ? hasClass(el, this.cls.split(" ")[0]) : isVisible(el);
        },
        _toggle: function(el, toggled) {
          if (!el) {
            return;
          }
          toggled = Boolean(toggled);
          var changed;
          if (this.cls) {
            changed = includes(this.cls, " ") || toggled !== hasClass(el, this.cls);
            changed && toggleClass(el, this.cls, includes(this.cls, " ") ? void 0 : toggled);
          } else {
            changed = toggled === el.hidden;
            changed && (el.hidden = !toggled);
          }
          $$("[autofocus]", el).some(function(el2) {
            return isVisible(el2) ? el2.focus() || true : el2.blur();
          });
          if (changed) {
            trigger(el, "toggled", [toggled, this]);
            this.$update(el);
          }
        }
      }
    };
    function toggleHeight(ref2) {
      var isToggled = ref2.isToggled;
      var duration = ref2.duration;
      var initProps = ref2.initProps;
      var hideProps = ref2.hideProps;
      var transition2 = ref2.transition;
      var _toggle = ref2._toggle;
      return function(el, show) {
        var inProgress2 = Transition.inProgress(el);
        var inner = el.hasChildNodes ? toFloat(css(el.firstElementChild, "marginTop")) + toFloat(css(el.lastElementChild, "marginBottom")) : 0;
        var currentHeight = isVisible(el) ? height(el) + (inProgress2 ? 0 : inner) : 0;
        Transition.cancel(el);
        if (!isToggled(el)) {
          _toggle(el, true);
        }
        height(el, "");
        fastdom.flush();
        var endHeight = height(el) + (inProgress2 ? 0 : inner);
        height(el, currentHeight);
        return (show ? Transition.start(el, assign({}, initProps, { overflow: "hidden", height: endHeight }), Math.round(duration * (1 - currentHeight / endHeight)), transition2) : Transition.start(el, hideProps, Math.round(duration * (currentHeight / endHeight)), transition2).then(function() {
          return _toggle(el, false);
        })).then(function() {
          return css(el, initProps);
        });
      };
    }
    function toggleAnimation(cmp) {
      return function(el, show) {
        Animation.cancel(el);
        var animation = cmp.animation;
        var duration = cmp.duration;
        var _toggle = cmp._toggle;
        if (show) {
          _toggle(el, true);
          return Animation.in(el, animation[0], duration, cmp.origin);
        }
        return Animation.out(el, animation[1] || animation[0], duration, cmp.origin).then(function() {
          return _toggle(el, false);
        });
      };
    }
    var Accordion = {
      mixins: [Class, Togglable],
      props: {
        targets: String,
        active: null,
        collapsible: Boolean,
        multiple: Boolean,
        toggle: String,
        content: String,
        transition: String,
        offset: Number
      },
      data: {
        targets: "> *",
        active: false,
        animation: [true],
        collapsible: true,
        multiple: false,
        clsOpen: "uk-open",
        toggle: "> .uk-accordion-title",
        content: "> .uk-accordion-content",
        transition: "ease",
        offset: 0
      },
      computed: {
        items: {
          get: function(ref2, $el) {
            var targets = ref2.targets;
            return $$(targets, $el);
          },
          watch: function(items, prev) {
            var this$1$1 = this;
            items.forEach(function(el) {
              return hide($(this$1$1.content, el), !hasClass(el, this$1$1.clsOpen));
            });
            if (prev || hasClass(items, this.clsOpen)) {
              return;
            }
            var active2 = this.active !== false && items[Number(this.active)] || !this.collapsible && items[0];
            if (active2) {
              this.toggle(active2, false);
            }
          },
          immediate: true
        },
        toggles: function(ref2) {
          var toggle2 = ref2.toggle;
          return this.items.map(function(item) {
            return $(toggle2, item);
          });
        }
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.targets + " " + this.$props.toggle;
          },
          handler: function(e) {
            e.preventDefault();
            this.toggle(index(this.toggles, e.current));
          }
        }
      ],
      methods: {
        toggle: function(item, animate2) {
          var this$1$1 = this;
          var items = [this.items[getIndex(item, this.items)]];
          var activeItems = filter$1(this.items, "." + this.clsOpen);
          if (!this.multiple && !includes(activeItems, items[0])) {
            items = items.concat(activeItems);
          }
          if (!this.collapsible && activeItems.length < 2 && !filter$1(items, ":not(." + this.clsOpen + ")").length) {
            return;
          }
          items.forEach(function(el) {
            return this$1$1.toggleElement(el, !hasClass(el, this$1$1.clsOpen), function(el2, show) {
              toggleClass(el2, this$1$1.clsOpen, show);
              attr($(this$1$1.$props.toggle, el2), "aria-expanded", show);
              var content = $("" + (el2._wrapper ? "> * " : "") + this$1$1.content, el2);
              if (animate2 === false || !this$1$1.hasTransition) {
                hide(content, !show);
                return;
              }
              if (!el2._wrapper) {
                el2._wrapper = wrapAll(content, "<div" + (show ? " hidden" : "") + ">");
              }
              hide(content, false);
              return toggleHeight(this$1$1)(el2._wrapper, show).then(function() {
                hide(content, !show);
                delete el2._wrapper;
                unwrap(content);
                if (show) {
                  var toggle2 = $(this$1$1.$props.toggle, el2);
                  if (!isInView(toggle2)) {
                    scrollIntoView(toggle2, { offset: this$1$1.offset });
                  }
                }
              });
            });
          });
        }
      }
    };
    function hide(el, hide2) {
      el && (el.hidden = hide2);
    }
    var alert = {
      mixins: [Class, Togglable],
      args: "animation",
      props: {
        close: String
      },
      data: {
        animation: [true],
        selClose: ".uk-alert-close",
        duration: 150,
        hideProps: assign({ opacity: 0 }, Togglable.data.hideProps)
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.selClose;
          },
          handler: function(e) {
            e.preventDefault();
            this.close();
          }
        }
      ],
      methods: {
        close: function() {
          var this$1$1 = this;
          this.toggleElement(this.$el).then(function() {
            return this$1$1.$destroy(true);
          });
        }
      }
    };
    var Video = {
      args: "autoplay",
      props: {
        automute: Boolean,
        autoplay: Boolean
      },
      data: {
        automute: false,
        autoplay: true
      },
      computed: {
        inView: function(ref2) {
          var autoplay = ref2.autoplay;
          return autoplay === "inview";
        }
      },
      connected: function() {
        if (this.inView && !hasAttr(this.$el, "preload")) {
          this.$el.preload = "none";
        }
        if (this.automute) {
          mute(this.$el);
        }
      },
      update: {
        read: function() {
          if (!isVideo(this.$el)) {
            return false;
          }
          return {
            visible: isVisible(this.$el) && css(this.$el, "visibility") !== "hidden",
            inView: this.inView && isInView(this.$el)
          };
        },
        write: function(ref2) {
          var visible = ref2.visible;
          var inView2 = ref2.inView;
          if (!visible || this.inView && !inView2) {
            pause(this.$el);
          } else if (this.autoplay === true || this.inView && inView2) {
            play(this.$el);
          }
        },
        events: ["resize", "scroll"]
      }
    };
    var cover = {
      mixins: [Video],
      props: {
        width: Number,
        height: Number
      },
      data: {
        automute: true
      },
      update: {
        read: function() {
          var el = this.$el;
          var ref2 = getPositionedParent(el) || parent(el);
          var height2 = ref2.offsetHeight;
          var width2 = ref2.offsetWidth;
          var dim = Dimensions.cover({
            width: this.width || el.naturalWidth || el.videoWidth || el.clientWidth,
            height: this.height || el.naturalHeight || el.videoHeight || el.clientHeight
          }, {
            width: width2 + (width2 % 2 ? 1 : 0),
            height: height2 + (height2 % 2 ? 1 : 0)
          });
          if (!dim.width || !dim.height) {
            return false;
          }
          return dim;
        },
        write: function(ref2) {
          var height2 = ref2.height;
          var width2 = ref2.width;
          css(this.$el, { height: height2, width: width2 });
        },
        events: ["resize"]
      }
    };
    function getPositionedParent(el) {
      while (el = parent(el)) {
        if (css(el, "position") !== "static") {
          return el;
        }
      }
    }
    var Container = {
      props: {
        container: Boolean
      },
      data: {
        container: true
      },
      computed: {
        container: function(ref2) {
          var container = ref2.container;
          return container === true && this.$container || container && $(container);
        }
      }
    };
    var Position = {
      props: {
        pos: String,
        offset: null,
        flip: Boolean,
        clsPos: String
      },
      data: {
        pos: "bottom-" + (isRtl ? "right" : "left"),
        flip: true,
        offset: false,
        clsPos: ""
      },
      computed: {
        pos: function(ref2) {
          var pos = ref2.pos;
          return pos.split("-").concat("center").slice(0, 2);
        },
        dir: function() {
          return this.pos[0];
        },
        align: function() {
          return this.pos[1];
        }
      },
      methods: {
        positionAt: function(element, target, boundary) {
          removeClasses(element, this.clsPos + "-(top|bottom|left|right)(-[a-z]+)?");
          var ref2 = this;
          var offset$1 = ref2.offset;
          var axis = this.getAxis();
          if (!isNumeric(offset$1)) {
            var node = $(offset$1);
            offset$1 = node ? offset(node)[axis === "x" ? "left" : "top"] - offset(target)[axis === "x" ? "right" : "bottom"] : 0;
          }
          var ref$1 = positionAt(element, target, axis === "x" ? flipPosition(this.dir) + " " + this.align : this.align + " " + flipPosition(this.dir), axis === "x" ? this.dir + " " + this.align : this.align + " " + this.dir, axis === "x" ? "" + (this.dir === "left" ? -offset$1 : offset$1) : " " + (this.dir === "top" ? -offset$1 : offset$1), null, this.flip, boundary).target;
          var x = ref$1.x;
          var y = ref$1.y;
          this.dir = axis === "x" ? x : y;
          this.align = axis === "x" ? y : x;
          toggleClass(element, this.clsPos + "-" + this.dir + "-" + this.align, this.offset === false);
        },
        getAxis: function() {
          return this.dir === "top" || this.dir === "bottom" ? "y" : "x";
        }
      }
    };
    var active$1;
    var drop = {
      mixins: [Container, Position, Togglable],
      args: "pos",
      props: {
        mode: "list",
        toggle: Boolean,
        boundary: Boolean,
        boundaryAlign: Boolean,
        delayShow: Number,
        delayHide: Number,
        clsDrop: String
      },
      data: {
        mode: ["click", "hover"],
        toggle: "- *",
        boundary: true,
        boundaryAlign: false,
        delayShow: 0,
        delayHide: 800,
        clsDrop: false,
        animation: ["uk-animation-fade"],
        cls: "uk-open",
        container: false
      },
      computed: {
        boundary: function(ref2, $el) {
          var boundary = ref2.boundary;
          return boundary === true ? window : query(boundary, $el);
        },
        clsDrop: function(ref2) {
          var clsDrop = ref2.clsDrop;
          return clsDrop || "uk-" + this.$options.name;
        },
        clsPos: function() {
          return this.clsDrop;
        }
      },
      created: function() {
        this.tracker = new MouseTracker();
      },
      connected: function() {
        addClass(this.$el, this.clsDrop);
        if (this.toggle && !this.target) {
          this.target = this.$create("toggle", query(this.toggle, this.$el), {
            target: this.$el,
            mode: this.mode
          }).$el;
          attr(this.target, "aria-haspopup", true);
        }
      },
      disconnected: function() {
        if (this.isActive()) {
          active$1 = null;
        }
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return "." + this.clsDrop + "-close";
          },
          handler: function(e) {
            e.preventDefault();
            this.hide(false);
          }
        },
        {
          name: "click",
          delegate: function() {
            return 'a[href^="#"]';
          },
          handler: function(ref2) {
            var defaultPrevented = ref2.defaultPrevented;
            var hash = ref2.current.hash;
            if (!defaultPrevented && hash && !within(hash, this.$el)) {
              this.hide(false);
            }
          }
        },
        {
          name: "beforescroll",
          handler: function() {
            this.hide(false);
          }
        },
        {
          name: "toggle",
          self: true,
          handler: function(e, toggle2) {
            e.preventDefault();
            if (this.isToggled()) {
              this.hide(false);
            } else {
              this.show(toggle2.$el, false);
            }
          }
        },
        {
          name: "toggleshow",
          self: true,
          handler: function(e, toggle2) {
            e.preventDefault();
            this.show(toggle2.$el);
          }
        },
        {
          name: "togglehide",
          self: true,
          handler: function(e) {
            e.preventDefault();
            if (!matches(this.$el, ":focus,:hover")) {
              this.hide();
            }
          }
        },
        {
          name: pointerEnter + " focusin",
          filter: function() {
            return includes(this.mode, "hover");
          },
          handler: function(e) {
            if (!isTouch(e)) {
              this.clearTimers();
            }
          }
        },
        {
          name: pointerLeave + " focusout",
          filter: function() {
            return includes(this.mode, "hover");
          },
          handler: function(e) {
            if (!isTouch(e) && e.relatedTarget) {
              this.hide();
            }
          }
        },
        {
          name: "toggled",
          self: true,
          handler: function(e, toggled) {
            if (!toggled) {
              return;
            }
            this.clearTimers();
            this.position();
          }
        },
        {
          name: "show",
          self: true,
          handler: function() {
            var this$1$1 = this;
            active$1 = this;
            this.tracker.init();
            once(this.$el, "hide", on(document, pointerDown, function(ref2) {
              var target = ref2.target;
              return !within(target, this$1$1.$el) && once(document, pointerUp + " " + pointerCancel + " scroll", function(ref3) {
                var defaultPrevented = ref3.defaultPrevented;
                var type = ref3.type;
                var newTarget = ref3.target;
                if (!defaultPrevented && type === pointerUp && target === newTarget && !(this$1$1.target && within(target, this$1$1.target))) {
                  this$1$1.hide(false);
                }
              }, true);
            }), { self: true });
            once(this.$el, "hide", on(document, "keydown", function(e) {
              if (e.keyCode === 27) {
                this$1$1.hide(false);
              }
            }), { self: true });
          }
        },
        {
          name: "beforehide",
          self: true,
          handler: function() {
            this.clearTimers();
          }
        },
        {
          name: "hide",
          handler: function(ref2) {
            var target = ref2.target;
            if (this.$el !== target) {
              active$1 = active$1 === null && within(target, this.$el) && this.isToggled() ? this : active$1;
              return;
            }
            active$1 = this.isActive() ? null : active$1;
            this.tracker.cancel();
          }
        }
      ],
      update: {
        write: function() {
          if (this.isToggled() && !hasClass(this.$el, this.clsEnter)) {
            this.position();
          }
        },
        events: ["resize"]
      },
      methods: {
        show: function(target, delay) {
          var this$1$1 = this;
          if (target === void 0)
            target = this.target;
          if (delay === void 0)
            delay = true;
          if (this.isToggled() && target && this.target && target !== this.target) {
            this.hide(false);
          }
          this.target = target;
          this.clearTimers();
          if (this.isActive()) {
            return;
          }
          if (active$1) {
            if (delay && active$1.isDelaying) {
              this.showTimer = setTimeout(this.show, 10);
              return;
            }
            var prev;
            while (active$1 && prev !== active$1 && !within(this.$el, active$1.$el)) {
              prev = active$1;
              active$1.hide(false);
            }
          }
          if (this.container && parent(this.$el) !== this.container) {
            append(this.container, this.$el);
          }
          this.showTimer = setTimeout(function() {
            return this$1$1.toggleElement(this$1$1.$el, true);
          }, delay && this.delayShow || 0);
        },
        hide: function(delay) {
          var this$1$1 = this;
          if (delay === void 0)
            delay = true;
          var hide2 = function() {
            return this$1$1.toggleElement(this$1$1.$el, false, false);
          };
          this.clearTimers();
          this.isDelaying = getPositionedElements(this.$el).some(function(el) {
            return this$1$1.tracker.movesTo(el);
          });
          if (delay && this.isDelaying) {
            this.hideTimer = setTimeout(this.hide, 50);
          } else if (delay && this.delayHide) {
            this.hideTimer = setTimeout(hide2, this.delayHide);
          } else {
            hide2();
          }
        },
        clearTimers: function() {
          clearTimeout(this.showTimer);
          clearTimeout(this.hideTimer);
          this.showTimer = null;
          this.hideTimer = null;
          this.isDelaying = false;
        },
        isActive: function() {
          return active$1 === this;
        },
        position: function() {
          removeClass(this.$el, this.clsDrop + "-stack");
          toggleClass(this.$el, this.clsDrop + "-boundary", this.boundaryAlign);
          var boundary = offset(this.boundary);
          var alignTo = this.boundaryAlign ? boundary : offset(this.target);
          if (this.align === "justify") {
            var prop = this.getAxis() === "y" ? "width" : "height";
            css(this.$el, prop, alignTo[prop]);
          } else if (this.boundary && this.$el.offsetWidth > Math.max(boundary.right - alignTo.left, alignTo.right - boundary.left)) {
            addClass(this.$el, this.clsDrop + "-stack");
          }
          this.positionAt(this.$el, this.boundaryAlign ? this.boundary : this.target, this.boundary);
        }
      }
    };
    function getPositionedElements(el) {
      var result = [];
      apply$1(el, function(el2) {
        return css(el2, "position") !== "static" && result.push(el2);
      });
      return result;
    }
    var formCustom = {
      mixins: [Class],
      args: "target",
      props: {
        target: Boolean
      },
      data: {
        target: false
      },
      computed: {
        input: function(_2, $el) {
          return $(selInput, $el);
        },
        state: function() {
          return this.input.nextElementSibling;
        },
        target: function(ref2, $el) {
          var target = ref2.target;
          return target && (target === true && parent(this.input) === $el && this.input.nextElementSibling || query(target, $el));
        }
      },
      update: function() {
        var ref2 = this;
        var target = ref2.target;
        var input = ref2.input;
        if (!target) {
          return;
        }
        var option;
        var prop = isInput(target) ? "value" : "textContent";
        var prev = target[prop];
        var value = input.files && input.files[0] ? input.files[0].name : matches(input, "select") && (option = $$("option", input).filter(function(el) {
          return el.selected;
        })[0]) ? option.textContent : input.value;
        if (prev !== value) {
          target[prop] = value;
        }
      },
      events: [
        {
          name: "change",
          handler: function() {
            this.$update();
          }
        },
        {
          name: "reset",
          el: function() {
            return closest(this.$el, "form");
          },
          handler: function() {
            this.$update();
          }
        }
      ]
    };
    var gif = {
      update: {
        read: function(data2) {
          var inview = isInView(this.$el);
          if (!inview || data2.isInView === inview) {
            return false;
          }
          data2.isInView = inview;
        },
        write: function() {
          this.$el.src = "" + this.$el.src;
        },
        events: ["scroll", "resize"]
      }
    };
    var Margin = {
      props: {
        margin: String,
        firstColumn: Boolean
      },
      data: {
        margin: "uk-margin-small-top",
        firstColumn: "uk-first-column"
      },
      update: {
        read: function() {
          var rows = getRows(this.$el.children);
          return {
            rows,
            columns: getColumns(rows)
          };
        },
        write: function(ref2) {
          var columns = ref2.columns;
          var rows = ref2.rows;
          for (var i = 0; i < rows.length; i++) {
            for (var j = 0; j < rows[i].length; j++) {
              toggleClass(rows[i][j], this.margin, i !== 0);
              toggleClass(rows[i][j], this.firstColumn, !!~columns[0].indexOf(rows[i][j]));
            }
          }
        },
        events: ["resize"]
      }
    };
    function getRows(items) {
      return sortBy(items, "top", "bottom");
    }
    function getColumns(rows) {
      var columns = [];
      for (var i = 0; i < rows.length; i++) {
        var sorted = sortBy(rows[i], "left", "right");
        for (var j = 0; j < sorted.length; j++) {
          columns[j] = !columns[j] ? sorted[j] : columns[j].concat(sorted[j]);
        }
      }
      return isRtl ? columns.reverse() : columns;
    }
    function sortBy(items, startProp, endProp) {
      var sorted = [[]];
      for (var i = 0; i < items.length; i++) {
        var el = items[i];
        if (!isVisible(el)) {
          continue;
        }
        var dim = getOffset(el);
        for (var j = sorted.length - 1; j >= 0; j--) {
          var current = sorted[j];
          if (!current[0]) {
            current.push(el);
            break;
          }
          var startDim = void 0;
          if (current[0].offsetParent === el.offsetParent) {
            startDim = getOffset(current[0]);
          } else {
            dim = getOffset(el, true);
            startDim = getOffset(current[0], true);
          }
          if (dim[startProp] >= startDim[endProp] - 1 && dim[startProp] !== startDim[startProp]) {
            sorted.push([el]);
            break;
          }
          if (dim[endProp] - 1 > startDim[startProp] || dim[startProp] === startDim[startProp]) {
            current.push(el);
            break;
          }
          if (j === 0) {
            sorted.unshift([el]);
            break;
          }
        }
      }
      return sorted;
    }
    function getOffset(element, offset2) {
      var assign2;
      if (offset2 === void 0)
        offset2 = false;
      var offsetTop = element.offsetTop;
      var offsetLeft = element.offsetLeft;
      var offsetHeight = element.offsetHeight;
      var offsetWidth = element.offsetWidth;
      if (offset2) {
        assign2 = offsetPosition(element), offsetTop = assign2[0], offsetLeft = assign2[1];
      }
      return {
        top: offsetTop,
        left: offsetLeft,
        bottom: offsetTop + offsetHeight,
        right: offsetLeft + offsetWidth
      };
    }
    var grid = {
      extends: Margin,
      mixins: [Class],
      name: "grid",
      props: {
        masonry: Boolean,
        parallax: Number
      },
      data: {
        margin: "uk-grid-margin",
        clsStack: "uk-grid-stack",
        masonry: false,
        parallax: 0
      },
      connected: function() {
        this.masonry && addClass(this.$el, "uk-flex-top uk-flex-wrap-top");
      },
      update: [
        {
          write: function(ref2) {
            var columns = ref2.columns;
            toggleClass(this.$el, this.clsStack, columns.length < 2);
          },
          events: ["resize"]
        },
        {
          read: function(data2) {
            var columns = data2.columns;
            var rows = data2.rows;
            if (!columns.length || !this.masonry && !this.parallax || positionedAbsolute(this.$el)) {
              data2.translates = false;
              return false;
            }
            var translates = false;
            var nodes = children(this.$el);
            var columnHeights = getColumnHeights(columns);
            var margin = getMarginTop(nodes, this.margin) * (rows.length - 1);
            var elHeight = Math.max.apply(Math, columnHeights) + margin;
            if (this.masonry) {
              columns = columns.map(function(column) {
                return sortBy$1(column, "offsetTop");
              });
              translates = getTranslates(rows, columns);
            }
            var padding = Math.abs(this.parallax);
            if (padding) {
              padding = columnHeights.reduce(function(newPadding, hgt, i) {
                return Math.max(newPadding, hgt + margin + (i % 2 ? padding : padding / 8) - elHeight);
              }, 0);
            }
            return { padding, columns, translates, height: translates ? elHeight : "" };
          },
          write: function(ref2) {
            var height2 = ref2.height;
            var padding = ref2.padding;
            css(this.$el, "paddingBottom", padding || "");
            height2 !== false && css(this.$el, "height", height2);
          },
          events: ["resize"]
        },
        {
          read: function(ref2) {
            var height$1 = ref2.height;
            if (positionedAbsolute(this.$el)) {
              return false;
            }
            return {
              scrolled: this.parallax ? scrolledOver(this.$el, height$1 ? height$1 - height(this.$el) : 0) * Math.abs(this.parallax) : false
            };
          },
          write: function(ref2) {
            var columns = ref2.columns;
            var scrolled = ref2.scrolled;
            var translates = ref2.translates;
            if (scrolled === false && !translates) {
              return;
            }
            columns.forEach(function(column, i) {
              return column.forEach(function(el, j) {
                return css(el, "transform", !scrolled && !translates ? "" : "translateY(" + ((translates && -translates[i][j]) + (scrolled ? i % 2 ? scrolled : scrolled / 8 : 0)) + "px)");
              });
            });
          },
          events: ["scroll", "resize"]
        }
      ]
    };
    function positionedAbsolute(el) {
      return children(el).some(function(el2) {
        return css(el2, "position") === "absolute";
      });
    }
    function getTranslates(rows, columns) {
      var rowHeights = rows.map(function(row) {
        return Math.max.apply(Math, row.map(function(el) {
          return el.offsetHeight;
        }));
      });
      return columns.map(function(elements) {
        var prev = 0;
        return elements.map(function(element, row) {
          return prev += row ? rowHeights[row - 1] - elements[row - 1].offsetHeight : 0;
        });
      });
    }
    function getMarginTop(nodes, cls) {
      var ref2 = nodes.filter(function(el) {
        return hasClass(el, cls);
      });
      var node = ref2[0];
      return toFloat(node ? css(node, "marginTop") : css(nodes[0], "paddingLeft"));
    }
    function getColumnHeights(columns) {
      return columns.map(function(column) {
        return column.reduce(function(sum, el) {
          return sum + el.offsetHeight;
        }, 0);
      });
    }
    var FlexBug = isIE ? {
      props: {
        selMinHeight: String
      },
      data: {
        selMinHeight: false,
        forceHeight: false
      },
      computed: {
        elements: function(ref2, $el) {
          var selMinHeight = ref2.selMinHeight;
          return selMinHeight ? $$(selMinHeight, $el) : [$el];
        }
      },
      update: [
        {
          read: function() {
            css(this.elements, "height", "");
          },
          order: -5,
          events: ["resize"]
        },
        {
          write: function() {
            var this$1$1 = this;
            this.elements.forEach(function(el) {
              var height2 = toFloat(css(el, "minHeight"));
              if (height2 && (this$1$1.forceHeight || Math.round(height2 + boxModelAdjust(el, "height", "content-box")) >= el.offsetHeight)) {
                css(el, "height", height2);
              }
            });
          },
          order: 5,
          events: ["resize"]
        }
      ]
    } : {};
    var heightMatch = {
      mixins: [FlexBug],
      args: "target",
      props: {
        target: String,
        row: Boolean
      },
      data: {
        target: "> *",
        row: true,
        forceHeight: true
      },
      computed: {
        elements: function(ref2, $el) {
          var target = ref2.target;
          return $$(target, $el);
        }
      },
      update: {
        read: function() {
          return {
            rows: (this.row ? getRows(this.elements) : [this.elements]).map(match$1)
          };
        },
        write: function(ref2) {
          var rows = ref2.rows;
          rows.forEach(function(ref3) {
            var heights = ref3.heights;
            var elements = ref3.elements;
            return elements.forEach(function(el, i) {
              return css(el, "minHeight", heights[i]);
            });
          });
        },
        events: ["resize"]
      }
    };
    function match$1(elements) {
      if (elements.length < 2) {
        return { heights: [""], elements };
      }
      var heights = elements.map(getHeight);
      var max = Math.max.apply(Math, heights);
      var hasMinHeight = elements.some(function(el) {
        return el.style.minHeight;
      });
      var hasShrunk = elements.some(function(el, i) {
        return !el.style.minHeight && heights[i] < max;
      });
      if (hasMinHeight && hasShrunk) {
        css(elements, "minHeight", "");
        heights = elements.map(getHeight);
        max = Math.max.apply(Math, heights);
      }
      heights = elements.map(function(el, i) {
        return heights[i] === max && toFloat(el.style.minHeight).toFixed(2) !== max.toFixed(2) ? "" : max;
      });
      return { heights, elements };
    }
    function getHeight(element) {
      var style = false;
      if (!isVisible(element)) {
        style = element.style.display;
        css(element, "display", "block", "important");
      }
      var height2 = dimensions(element).height - boxModelAdjust(element, "height", "content-box");
      if (style !== false) {
        css(element, "display", style);
      }
      return height2;
    }
    var heightViewport = {
      mixins: [FlexBug],
      props: {
        expand: Boolean,
        offsetTop: Boolean,
        offsetBottom: Boolean,
        minHeight: Number
      },
      data: {
        expand: false,
        offsetTop: false,
        offsetBottom: false,
        minHeight: 0
      },
      update: {
        read: function(ref2) {
          var prev = ref2.minHeight;
          if (!isVisible(this.$el)) {
            return false;
          }
          var minHeight = "";
          var box = boxModelAdjust(this.$el, "height", "content-box");
          if (this.expand) {
            minHeight = height(window) - (dimensions(document.documentElement).height - dimensions(this.$el).height) - box || "";
          } else {
            minHeight = "calc(100vh";
            if (this.offsetTop) {
              var ref$1 = offset(this.$el);
              var top = ref$1.top;
              minHeight += top > 0 && top < height(window) / 2 ? " - " + top + "px" : "";
            }
            if (this.offsetBottom === true) {
              minHeight += " - " + dimensions(this.$el.nextElementSibling).height + "px";
            } else if (isNumeric(this.offsetBottom)) {
              minHeight += " - " + this.offsetBottom + "vh";
            } else if (this.offsetBottom && endsWith(this.offsetBottom, "px")) {
              minHeight += " - " + toFloat(this.offsetBottom) + "px";
            } else if (isString(this.offsetBottom)) {
              minHeight += " - " + dimensions(query(this.offsetBottom, this.$el)).height + "px";
            }
            minHeight += (box ? " - " + box + "px" : "") + ")";
          }
          return { minHeight, prev };
        },
        write: function(ref2) {
          var minHeight = ref2.minHeight;
          var prev = ref2.prev;
          css(this.$el, { minHeight });
          if (minHeight !== prev) {
            this.$update(this.$el, "resize");
          }
          if (this.minHeight && toFloat(css(this.$el, "minHeight")) < this.minHeight) {
            css(this.$el, "minHeight", this.minHeight);
          }
        },
        events: ["resize"]
      }
    };
    var SVG = {
      args: "src",
      props: {
        id: Boolean,
        icon: String,
        src: String,
        style: String,
        width: Number,
        height: Number,
        ratio: Number,
        class: String,
        strokeAnimation: Boolean,
        focusable: Boolean,
        attributes: "list"
      },
      data: {
        ratio: 1,
        include: ["style", "class", "focusable"],
        class: "",
        strokeAnimation: false
      },
      beforeConnect: function() {
        this.class += " uk-svg";
      },
      connected: function() {
        var this$1$1 = this;
        var assign2;
        if (!this.icon && includes(this.src, "#")) {
          assign2 = this.src.split("#"), this.src = assign2[0], this.icon = assign2[1];
        }
        this.svg = this.getSvg().then(function(el) {
          if (this$1$1._connected) {
            var svg = insertSVG(el, this$1$1.$el);
            if (this$1$1.svgEl && svg !== this$1$1.svgEl) {
              remove$1(this$1$1.svgEl);
            }
            this$1$1.applyAttributes(svg, el);
            this$1$1.$emit();
            return this$1$1.svgEl = svg;
          }
        }, noop);
      },
      disconnected: function() {
        var this$1$1 = this;
        this.svg.then(function(svg) {
          if (!this$1$1._connected) {
            if (isVoidElement(this$1$1.$el)) {
              this$1$1.$el.hidden = false;
            }
            remove$1(svg);
            this$1$1.svgEl = null;
          }
        });
        this.svg = null;
      },
      update: {
        read: function() {
          return !!(this.strokeAnimation && this.svgEl && isVisible(this.svgEl));
        },
        write: function() {
          applyAnimation(this.svgEl);
        },
        type: ["resize"]
      },
      methods: {
        getSvg: function() {
          var this$1$1 = this;
          return loadSVG(this.src).then(function(svg) {
            return parseSVG(svg, this$1$1.icon) || Promise$1.reject("SVG not found.");
          });
        },
        applyAttributes: function(el, ref2) {
          var this$1$1 = this;
          for (var prop in this.$options.props) {
            if (includes(this.include, prop) && prop in this) {
              attr(el, prop, this[prop]);
            }
          }
          for (var attribute in this.attributes) {
            var ref$1 = this.attributes[attribute].split(":", 2);
            var prop$1 = ref$1[0];
            var value = ref$1[1];
            attr(el, prop$1, value);
          }
          if (!this.id) {
            removeAttr(el, "id");
          }
          var props2 = ["width", "height"];
          var dimensions2 = props2.map(function(prop2) {
            return this$1$1[prop2];
          });
          if (!dimensions2.some(function(val) {
            return val;
          })) {
            dimensions2 = props2.map(function(prop2) {
              return attr(ref2, prop2);
            });
          }
          var viewBox = attr(ref2, "viewBox");
          if (viewBox && !dimensions2.some(function(val) {
            return val;
          })) {
            dimensions2 = viewBox.split(" ").slice(2);
          }
          dimensions2.forEach(function(val, i) {
            return attr(el, props2[i], toFloat(val) * this$1$1.ratio || null);
          });
        }
      }
    };
    var loadSVG = memoize(function(src) {
      return new Promise$1(function(resolve, reject) {
        if (!src) {
          reject();
          return;
        }
        if (startsWith(src, "data:")) {
          resolve(decodeURIComponent(src.split(",")[1]));
        } else {
          ajax(src).then(function(xhr) {
            return resolve(xhr.response);
          }, function() {
            return reject("SVG not found.");
          });
        }
      });
    });
    function parseSVG(svg, icon) {
      if (icon && includes(svg, "<symbol")) {
        svg = parseSymbols(svg, icon) || svg;
      }
      svg = $(svg.substr(svg.indexOf("<svg")));
      return svg && svg.hasChildNodes() && svg;
    }
    var symbolRe = /<symbol([^]*?id=(['"])(.+?)\2[^]*?<\/)symbol>/g;
    var symbols = {};
    function parseSymbols(svg, icon) {
      if (!symbols[svg]) {
        symbols[svg] = {};
        symbolRe.lastIndex = 0;
        var match2;
        while (match2 = symbolRe.exec(svg)) {
          symbols[svg][match2[3]] = '<svg xmlns="http://www.w3.org/2000/svg"' + match2[1] + "svg>";
        }
      }
      return symbols[svg][icon];
    }
    function applyAnimation(el) {
      var length = getMaxPathLength(el);
      if (length) {
        el.style.setProperty("--uk-animation-stroke", length);
      }
    }
    function getMaxPathLength(el) {
      return Math.ceil(Math.max.apply(Math, [0].concat($$("[stroke]", el).map(function(stroke) {
        try {
          return stroke.getTotalLength();
        } catch (e) {
          return 0;
        }
      }))));
    }
    function insertSVG(el, root) {
      if (isVoidElement(root) || root.tagName === "CANVAS") {
        root.hidden = true;
        var next = root.nextElementSibling;
        return equals(el, next) ? next : after(root, el);
      }
      var last2 = root.lastElementChild;
      return equals(el, last2) ? last2 : append(root, el);
    }
    function equals(el, other) {
      return isSVG(el) && isSVG(other) && innerHTML(el) === innerHTML(other);
    }
    function isSVG(el) {
      return el && el.tagName === "svg";
    }
    function innerHTML(el) {
      return (el.innerHTML || new XMLSerializer().serializeToString(el).replace(/<svg.*?>(.*?)<\/svg>/g, "$1")).replace(/\s/g, "");
    }
    var closeIcon = '<svg width="14" height="14" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"/><line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"/></svg>';
    var closeLarge = '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" stroke-width="1.4" x1="1" y1="1" x2="19" y2="19"/><line fill="none" stroke="#000" stroke-width="1.4" x1="19" y1="1" x2="1" y2="19"/></svg>';
    var marker = '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="9" y="4" width="1" height="11"/><rect x="4" y="9" width="11" height="1"/></svg>';
    var navbarToggleIcon = '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect y="9" width="20" height="2"/><rect y="3" width="20" height="2"/><rect y="15" width="20" height="2"/></svg>';
    var overlayIcon = '<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><rect x="19" y="0" width="1" height="40"/><rect x="0" y="19" width="40" height="1"/></svg>';
    var paginationNext = '<svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.2" points="1 1 6 6 1 11"/></svg>';
    var paginationPrevious = '<svg width="7" height="12" viewBox="0 0 7 12" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.2" points="6 1 1 6 6 11"/></svg>';
    var searchIcon = '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"/><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"/></svg>';
    var searchLarge = '<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.8" cx="17.5" cy="17.5" r="16.5"/><line fill="none" stroke="#000" stroke-width="1.8" x1="38" y1="39" x2="29" y2="30"/></svg>';
    var searchNavbar = '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="10.5" cy="10.5" r="9.5"/><line fill="none" stroke="#000" stroke-width="1.1" x1="23" y1="23" x2="17" y2="17"/></svg>';
    var slidenavNext = '<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.4" points="1.225,23 12.775,12 1.225,1 "/></svg>';
    var slidenavNextLarge = '<svg width="25" height="40" viewBox="0 0 25 40" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="2" points="4.002,38.547 22.527,20.024 4,1.5 "/></svg>';
    var slidenavPrevious = '<svg width="14" height="24" viewBox="0 0 14 24" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.4" points="12.775,1 1.225,12 12.775,23 "/></svg>';
    var slidenavPreviousLarge = '<svg width="25" height="40" viewBox="0 0 25 40" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="2" points="20.527,1.5 2,20.024 20.525,38.547 "/></svg>';
    var spinner = '<svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" cx="15" cy="15" r="14"/></svg>';
    var totop = '<svg width="18" height="10" viewBox="0 0 18 10" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.2" points="1 9 9 1 17 9 "/></svg>';
    var icons = {
      spinner,
      totop,
      marker,
      "close-icon": closeIcon,
      "close-large": closeLarge,
      "navbar-toggle-icon": navbarToggleIcon,
      "overlay-icon": overlayIcon,
      "pagination-next": paginationNext,
      "pagination-previous": paginationPrevious,
      "search-icon": searchIcon,
      "search-large": searchLarge,
      "search-navbar": searchNavbar,
      "slidenav-next": slidenavNext,
      "slidenav-next-large": slidenavNextLarge,
      "slidenav-previous": slidenavPrevious,
      "slidenav-previous-large": slidenavPreviousLarge
    };
    var Icon = {
      install: install$3,
      extends: SVG,
      args: "icon",
      props: ["icon"],
      data: {
        include: ["focusable"]
      },
      isIcon: true,
      beforeConnect: function() {
        addClass(this.$el, "uk-icon");
      },
      methods: {
        getSvg: function() {
          var icon = getIcon(this.icon);
          if (!icon) {
            return Promise$1.reject("Icon not found.");
          }
          return Promise$1.resolve(icon);
        }
      }
    };
    var IconComponent = {
      args: false,
      extends: Icon,
      data: function(vm) {
        return {
          icon: hyphenate(vm.constructor.options.name)
        };
      },
      beforeConnect: function() {
        addClass(this.$el, this.$name);
      }
    };
    var Slidenav = {
      extends: IconComponent,
      beforeConnect: function() {
        addClass(this.$el, "uk-slidenav");
      },
      computed: {
        icon: function(ref2, $el) {
          var icon = ref2.icon;
          return hasClass($el, "uk-slidenav-large") ? icon + "-large" : icon;
        }
      }
    };
    var Search = {
      extends: IconComponent,
      computed: {
        icon: function(ref2, $el) {
          var icon = ref2.icon;
          return hasClass($el, "uk-search-icon") && parents($el, ".uk-search-large").length ? "search-large" : parents($el, ".uk-search-navbar").length ? "search-navbar" : icon;
        }
      }
    };
    var Close = {
      extends: IconComponent,
      computed: {
        icon: function() {
          return "close-" + (hasClass(this.$el, "uk-close-large") ? "large" : "icon");
        }
      }
    };
    var Spinner = {
      extends: IconComponent,
      connected: function() {
        var this$1$1 = this;
        this.svg.then(function(svg) {
          return svg && this$1$1.ratio !== 1 && css($("circle", svg), "strokeWidth", 1 / this$1$1.ratio);
        });
      }
    };
    var parsed = {};
    function install$3(UIkit3) {
      UIkit3.icon.add = function(name, svg) {
        var obj2;
        var added = isString(name) ? (obj2 = {}, obj2[name] = svg, obj2) : name;
        each(added, function(svg2, name2) {
          icons[name2] = svg2;
          delete parsed[name2];
        });
        if (UIkit3._initialized) {
          apply$1(document.body, function(el) {
            return each(UIkit3.getComponents(el), function(cmp) {
              cmp.$options.isIcon && cmp.icon in added && cmp.$reset();
            });
          });
        }
      };
    }
    function getIcon(icon) {
      if (!icons[icon]) {
        return null;
      }
      if (!parsed[icon]) {
        parsed[icon] = $((icons[applyRtl(icon)] || icons[icon]).trim());
      }
      return parsed[icon].cloneNode(true);
    }
    function applyRtl(icon) {
      return isRtl ? swap(swap(icon, "left", "right"), "previous", "next") : icon;
    }
    var img = {
      args: "dataSrc",
      props: {
        dataSrc: String,
        dataSrcset: Boolean,
        sizes: String,
        width: Number,
        height: Number,
        offsetTop: String,
        offsetLeft: String,
        target: String
      },
      data: {
        dataSrc: "",
        dataSrcset: false,
        sizes: false,
        width: false,
        height: false,
        offsetTop: "50vh",
        offsetLeft: "50vw",
        target: false
      },
      computed: {
        cacheKey: function(ref2) {
          var dataSrc = ref2.dataSrc;
          return this.$name + "." + dataSrc;
        },
        width: function(ref2) {
          var width2 = ref2.width;
          var dataWidth = ref2.dataWidth;
          return width2 || dataWidth;
        },
        height: function(ref2) {
          var height2 = ref2.height;
          var dataHeight = ref2.dataHeight;
          return height2 || dataHeight;
        },
        sizes: function(ref2) {
          var sizes = ref2.sizes;
          var dataSizes = ref2.dataSizes;
          return sizes || dataSizes;
        },
        isImg: function(_2, $el) {
          return isImg($el);
        },
        target: {
          get: function(ref2) {
            var target = ref2.target;
            return [this.$el].concat(queryAll(target, this.$el));
          },
          watch: function() {
            this.observe();
          }
        },
        offsetTop: function(ref2) {
          var offsetTop = ref2.offsetTop;
          return toPx(offsetTop, "height");
        },
        offsetLeft: function(ref2) {
          var offsetLeft = ref2.offsetLeft;
          return toPx(offsetLeft, "width");
        }
      },
      connected: function() {
        if (!window.IntersectionObserver) {
          setSrcAttrs(this.$el, this.dataSrc, this.dataSrcset, this.sizes);
          return;
        }
        if (storage[this.cacheKey]) {
          setSrcAttrs(this.$el, storage[this.cacheKey], this.dataSrcset, this.sizes);
        } else if (this.isImg && this.width && this.height) {
          setSrcAttrs(this.$el, getPlaceholderImage(this.width, this.height, this.sizes));
        }
        this.observer = new IntersectionObserver(this.load, {
          rootMargin: this.offsetTop + "px " + this.offsetLeft + "px"
        });
        requestAnimationFrame(this.observe);
      },
      disconnected: function() {
        this.observer && this.observer.disconnect();
      },
      update: {
        read: function(ref2) {
          var this$1$1 = this;
          var image = ref2.image;
          if (!this.observer) {
            return false;
          }
          if (!image && document.readyState === "complete") {
            this.load(this.observer.takeRecords());
          }
          if (this.isImg) {
            return false;
          }
          image && image.then(function(img2) {
            return img2 && img2.currentSrc !== "" && setSrcAttrs(this$1$1.$el, currentSrc(img2));
          });
        },
        write: function(data2) {
          if (this.dataSrcset && window.devicePixelRatio !== 1) {
            var bgSize = css(this.$el, "backgroundSize");
            if (bgSize.match(/^(auto\s?)+$/) || toFloat(bgSize) === data2.bgSize) {
              data2.bgSize = getSourceSize(this.dataSrcset, this.sizes);
              css(this.$el, "backgroundSize", data2.bgSize + "px");
            }
          }
        },
        events: ["resize"]
      },
      methods: {
        load: function(entries) {
          var this$1$1 = this;
          if (!entries.some(function(entry) {
            return isUndefined(entry.isIntersecting) || entry.isIntersecting;
          })) {
            return;
          }
          this._data.image = getImage(this.dataSrc, this.dataSrcset, this.sizes).then(function(img2) {
            setSrcAttrs(this$1$1.$el, currentSrc(img2), img2.srcset, img2.sizes);
            storage[this$1$1.cacheKey] = currentSrc(img2);
            return img2;
          }, function(e) {
            return trigger(this$1$1.$el, new e.constructor(e.type, e));
          });
          this.observer.disconnect();
        },
        observe: function() {
          var this$1$1 = this;
          if (this._connected && !this._data.image) {
            this.target.forEach(function(el) {
              return this$1$1.observer.observe(el);
            });
          }
        }
      }
    };
    function setSrcAttrs(el, src, srcset, sizes) {
      if (isImg(el)) {
        var set = function(prop, val) {
          return val && val !== el[prop] && (el[prop] = val);
        };
        set("sizes", sizes);
        set("srcset", srcset);
        set("src", src);
      } else if (src) {
        var change = !includes(el.style.backgroundImage, src);
        if (change) {
          css(el, "backgroundImage", "url(" + escape(src) + ")");
          trigger(el, createEvent("load", false));
        }
      }
    }
    function getPlaceholderImage(width2, height2, sizes) {
      var assign2;
      if (sizes) {
        assign2 = Dimensions.ratio({ width: width2, height: height2 }, "width", toPx(sizesToPixel(sizes))), width2 = assign2.width, height2 = assign2.height;
      }
      return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="' + width2 + '" height="' + height2 + '"></svg>';
    }
    var sizesRe = /\s*(.*?)\s*(\w+|calc\(.*?\))\s*(?:,|$)/g;
    function sizesToPixel(sizes) {
      var matches2;
      sizesRe.lastIndex = 0;
      while (matches2 = sizesRe.exec(sizes)) {
        if (!matches2[1] || window.matchMedia(matches2[1]).matches) {
          matches2 = evaluateSize(matches2[2]);
          break;
        }
      }
      return matches2 || "100vw";
    }
    var sizeRe = /\d+(?:\w+|%)/g;
    var additionRe = /[+-]?(\d+)/g;
    function evaluateSize(size) {
      return startsWith(size, "calc") ? size.slice(5, -1).replace(sizeRe, function(size2) {
        return toPx(size2);
      }).replace(/ /g, "").match(additionRe).reduce(function(a, b) {
        return a + +b;
      }, 0) : size;
    }
    var srcSetRe = /\s+\d+w\s*(?:,|$)/g;
    function getSourceSize(srcset, sizes) {
      var srcSize = toPx(sizesToPixel(sizes));
      var descriptors = (srcset.match(srcSetRe) || []).map(toFloat).sort(function(a, b) {
        return a - b;
      });
      return descriptors.filter(function(size) {
        return size >= srcSize;
      })[0] || descriptors.pop() || "";
    }
    function isImg(el) {
      return el.tagName === "IMG";
    }
    function currentSrc(el) {
      return el.currentSrc || el.src;
    }
    var key = "__test__";
    var storage;
    try {
      storage = window.sessionStorage || {};
      storage[key] = 1;
      delete storage[key];
    } catch (e) {
      storage = {};
    }
    var Media = {
      props: {
        media: Boolean
      },
      data: {
        media: false
      },
      computed: {
        matchMedia: function() {
          var media = toMedia(this.media);
          return !media || window.matchMedia(media).matches;
        }
      }
    };
    function toMedia(value) {
      if (isString(value)) {
        if (value[0] === "@") {
          var name = "breakpoint-" + value.substr(1);
          value = toFloat(getCssVar(name));
        } else if (isNaN(value)) {
          return value;
        }
      }
      return value && !isNaN(value) ? "(min-width: " + value + "px)" : false;
    }
    var leader = {
      mixins: [Class, Media],
      props: {
        fill: String
      },
      data: {
        fill: "",
        clsWrapper: "uk-leader-fill",
        clsHide: "uk-leader-hide",
        attrFill: "data-fill"
      },
      computed: {
        fill: function(ref2) {
          var fill = ref2.fill;
          return fill || getCssVar("leader-fill-content");
        }
      },
      connected: function() {
        var assign2;
        assign2 = wrapInner(this.$el, '<span class="' + this.clsWrapper + '">'), this.wrapper = assign2[0];
      },
      disconnected: function() {
        unwrap(this.wrapper.childNodes);
      },
      update: {
        read: function(ref2) {
          var changed = ref2.changed;
          var width2 = ref2.width;
          var prev = width2;
          width2 = Math.floor(this.$el.offsetWidth / 2);
          return {
            width: width2,
            fill: this.fill,
            changed: changed || prev !== width2,
            hide: !this.matchMedia
          };
        },
        write: function(data2) {
          toggleClass(this.wrapper, this.clsHide, data2.hide);
          if (data2.changed) {
            data2.changed = false;
            attr(this.wrapper, this.attrFill, new Array(data2.width).join(data2.fill));
          }
        },
        events: ["resize"]
      }
    };
    var active = [];
    var Modal = {
      mixins: [Class, Container, Togglable],
      props: {
        selPanel: String,
        selClose: String,
        escClose: Boolean,
        bgClose: Boolean,
        stack: Boolean
      },
      data: {
        cls: "uk-open",
        escClose: true,
        bgClose: true,
        overlay: true,
        stack: false
      },
      computed: {
        panel: function(ref2, $el) {
          var selPanel = ref2.selPanel;
          return $(selPanel, $el);
        },
        transitionElement: function() {
          return this.panel;
        },
        bgClose: function(ref2) {
          var bgClose = ref2.bgClose;
          return bgClose && this.panel;
        }
      },
      beforeDisconnect: function() {
        if (includes(active, this)) {
          this.toggleElement(this.$el, false, false);
        }
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.selClose;
          },
          handler: function(e) {
            e.preventDefault();
            this.hide();
          }
        },
        {
          name: "toggle",
          self: true,
          handler: function(e, toggle2) {
            if (e.defaultPrevented) {
              return;
            }
            e.preventDefault();
            if (this.isToggled() === includes(active, this)) {
              this.toggle();
            }
          }
        },
        {
          name: "beforeshow",
          self: true,
          handler: function(e) {
            if (includes(active, this)) {
              return false;
            }
            if (!this.stack && active.length) {
              Promise$1.all(active.map(function(modal2) {
                return modal2.hide();
              })).then(this.show);
              e.preventDefault();
            } else {
              active.push(this);
            }
          }
        },
        {
          name: "show",
          self: true,
          handler: function() {
            var this$1$1 = this;
            var docEl = document.documentElement;
            if (width(window) > docEl.clientWidth && this.overlay) {
              css(document.body, "overflowY", "scroll");
            }
            if (this.stack) {
              css(this.$el, "zIndex", toFloat(css(this.$el, "zIndex")) + active.length);
            }
            addClass(docEl, this.clsPage);
            if (this.bgClose) {
              once(this.$el, "hide", on(document, pointerDown, function(ref2) {
                var target = ref2.target;
                if (last(active) !== this$1$1 || this$1$1.overlay && !within(target, this$1$1.$el) || within(target, this$1$1.panel)) {
                  return;
                }
                once(document, pointerUp + " " + pointerCancel + " scroll", function(ref3) {
                  var defaultPrevented = ref3.defaultPrevented;
                  var type = ref3.type;
                  var newTarget = ref3.target;
                  if (!defaultPrevented && type === pointerUp && target === newTarget) {
                    this$1$1.hide();
                  }
                }, true);
              }), { self: true });
            }
            if (this.escClose) {
              once(this.$el, "hide", on(document, "keydown", function(e) {
                if (e.keyCode === 27 && last(active) === this$1$1) {
                  this$1$1.hide();
                }
              }), { self: true });
            }
          }
        },
        {
          name: "shown",
          self: true,
          handler: function() {
            if (!isFocusable(this.$el)) {
              attr(this.$el, "tabindex", "-1");
            }
            if (!$(":focus", this.$el)) {
              this.$el.focus();
            }
          }
        },
        {
          name: "hidden",
          self: true,
          handler: function() {
            var this$1$1 = this;
            if (includes(active, this)) {
              active.splice(active.indexOf(this), 1);
            }
            if (!active.length) {
              css(document.body, "overflowY", "");
            }
            css(this.$el, "zIndex", "");
            if (!active.some(function(modal2) {
              return modal2.clsPage === this$1$1.clsPage;
            })) {
              removeClass(document.documentElement, this.clsPage);
            }
          }
        }
      ],
      methods: {
        toggle: function() {
          return this.isToggled() ? this.hide() : this.show();
        },
        show: function() {
          var this$1$1 = this;
          if (this.container && parent(this.$el) !== this.container) {
            append(this.container, this.$el);
            return new Promise$1(function(resolve) {
              return requestAnimationFrame(function() {
                return this$1$1.show().then(resolve);
              });
            });
          }
          return this.toggleElement(this.$el, true, animate(this));
        },
        hide: function() {
          return this.toggleElement(this.$el, false, animate(this));
        }
      }
    };
    function animate(ref2) {
      var transitionElement = ref2.transitionElement;
      var _toggle = ref2._toggle;
      return function(el, show) {
        return new Promise$1(function(resolve, reject) {
          return once(el, "show hide", function() {
            el._reject && el._reject();
            el._reject = reject;
            _toggle(el, show);
            var off2 = once(transitionElement, "transitionstart", function() {
              once(transitionElement, "transitionend transitioncancel", resolve, { self: true });
              clearTimeout(timer);
            }, { self: true });
            var timer = setTimeout(function() {
              off2();
              resolve();
            }, toMs(css(transitionElement, "transitionDuration")));
          });
        }).then(function() {
          return delete el._reject;
        });
      };
    }
    var modal = {
      install: install$2,
      mixins: [Modal],
      data: {
        clsPage: "uk-modal-page",
        selPanel: ".uk-modal-dialog",
        selClose: ".uk-modal-close, .uk-modal-close-default, .uk-modal-close-outside, .uk-modal-close-full"
      },
      events: [
        {
          name: "show",
          self: true,
          handler: function() {
            if (hasClass(this.panel, "uk-margin-auto-vertical")) {
              addClass(this.$el, "uk-flex");
            } else {
              css(this.$el, "display", "block");
            }
            height(this.$el);
          }
        },
        {
          name: "hidden",
          self: true,
          handler: function() {
            css(this.$el, "display", "");
            removeClass(this.$el, "uk-flex");
          }
        }
      ]
    };
    function install$2(ref2) {
      var modal2 = ref2.modal;
      modal2.dialog = function(content, options) {
        var dialog = modal2('<div class="uk-modal"> <div class="uk-modal-dialog">' + content + "</div> </div>", options);
        dialog.show();
        on(dialog.$el, "hidden", function() {
          return Promise$1.resolve().then(function() {
            return dialog.$destroy(true);
          });
        }, { self: true });
        return dialog;
      };
      modal2.alert = function(message, options) {
        return openDialog(function(ref3) {
          var labels = ref3.labels;
          return '<div class="uk-modal-body">' + (isString(message) ? message : html(message)) + '</div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-primary uk-modal-close" autofocus>' + labels.ok + "</button> </div>";
        }, options, function(deferred) {
          return deferred.resolve();
        });
      };
      modal2.confirm = function(message, options) {
        return openDialog(function(ref3) {
          var labels = ref3.labels;
          return '<form> <div class="uk-modal-body">' + (isString(message) ? message : html(message)) + '</div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-default uk-modal-close" type="button">' + labels.cancel + '</button> <button class="uk-button uk-button-primary" autofocus>' + labels.ok + "</button> </div> </form>";
        }, options, function(deferred) {
          return deferred.reject();
        });
      };
      modal2.prompt = function(message, value, options) {
        return openDialog(function(ref3) {
          var labels = ref3.labels;
          return '<form class="uk-form-stacked"> <div class="uk-modal-body"> <label>' + (isString(message) ? message : html(message)) + '</label> <input class="uk-input" value="' + (value || "") + '" autofocus> </div> <div class="uk-modal-footer uk-text-right"> <button class="uk-button uk-button-default uk-modal-close" type="button">' + labels.cancel + '</button> <button class="uk-button uk-button-primary">' + labels.ok + "</button> </div> </form>";
        }, options, function(deferred) {
          return deferred.resolve(null);
        }, function(dialog) {
          return $("input", dialog.$el).value;
        });
      };
      modal2.labels = {
        ok: "Ok",
        cancel: "Cancel"
      };
      function openDialog(tmpl, options, hideFn, submitFn) {
        options = assign({ bgClose: false, escClose: true, labels: modal2.labels }, options);
        var dialog = modal2.dialog(tmpl(options), options);
        var deferred = new Deferred();
        var resolved = false;
        on(dialog.$el, "submit", "form", function(e) {
          e.preventDefault();
          deferred.resolve(submitFn && submitFn(dialog));
          resolved = true;
          dialog.hide();
        });
        on(dialog.$el, "hide", function() {
          return !resolved && hideFn(deferred);
        });
        deferred.promise.dialog = dialog;
        return deferred.promise;
      }
    }
    var nav = {
      extends: Accordion,
      data: {
        targets: "> .uk-parent",
        toggle: "> a",
        content: "> ul"
      }
    };
    var navItem = ".uk-navbar-nav > li > a, .uk-navbar-item, .uk-navbar-toggle";
    var navbar = {
      mixins: [Class, Container, FlexBug],
      props: {
        dropdown: String,
        mode: "list",
        align: String,
        offset: Number,
        boundary: Boolean,
        boundaryAlign: Boolean,
        clsDrop: String,
        delayShow: Number,
        delayHide: Number,
        dropbar: Boolean,
        dropbarMode: String,
        dropbarAnchor: Boolean,
        duration: Number
      },
      data: {
        dropdown: navItem,
        align: isRtl ? "right" : "left",
        clsDrop: "uk-navbar-dropdown",
        mode: void 0,
        offset: void 0,
        delayShow: void 0,
        delayHide: void 0,
        boundaryAlign: void 0,
        flip: "x",
        boundary: true,
        dropbar: false,
        dropbarMode: "slide",
        dropbarAnchor: false,
        duration: 200,
        forceHeight: true,
        selMinHeight: navItem,
        container: false
      },
      computed: {
        boundary: function(ref2, $el) {
          var boundary = ref2.boundary;
          var boundaryAlign = ref2.boundaryAlign;
          return boundary === true || boundaryAlign ? $el : boundary;
        },
        dropbarAnchor: function(ref2, $el) {
          var dropbarAnchor = ref2.dropbarAnchor;
          return query(dropbarAnchor, $el);
        },
        pos: function(ref2) {
          var align = ref2.align;
          return "bottom-" + align;
        },
        dropbar: {
          get: function(ref2) {
            var dropbar = ref2.dropbar;
            if (!dropbar) {
              return null;
            }
            dropbar = this._dropbar || query(dropbar, this.$el) || $("+ .uk-navbar-dropbar", this.$el);
            return dropbar ? dropbar : this._dropbar = $("<div></div>");
          },
          watch: function(dropbar) {
            addClass(dropbar, "uk-navbar-dropbar");
          },
          immediate: true
        },
        dropContainer: function(_2, $el) {
          return this.container || $el;
        },
        dropdowns: {
          get: function(ref2, $el) {
            var this$1$1 = this;
            var clsDrop = ref2.clsDrop;
            var dropdowns = $$("." + clsDrop, $el);
            if (this.dropContainer !== $el) {
              $$("." + clsDrop, this.dropContainer).forEach(function(el) {
                var dropdown = this$1$1.getDropdown(el);
                if (!includes(dropdowns, el) && dropdown && dropdown.target && within(dropdown.target, this$1$1.$el)) {
                  dropdowns.push(el);
                }
              });
            }
            return dropdowns;
          },
          watch: function(dropdowns) {
            var this$1$1 = this;
            this.$create("drop", dropdowns.filter(function(el) {
              return !this$1$1.getDropdown(el);
            }), assign({}, this.$props, { boundary: this.boundary, pos: this.pos, offset: this.dropbar || this.offset }));
          },
          immediate: true
        },
        toggles: function(ref2, $el) {
          var dropdown = ref2.dropdown;
          return $$(dropdown, $el);
        }
      },
      disconnected: function() {
        this.dropbar && remove$1(this.dropbar);
        delete this._dropbar;
      },
      events: [
        {
          name: "mouseover focusin",
          delegate: function() {
            return this.dropdown;
          },
          handler: function(ref2) {
            var current = ref2.current;
            var active2 = this.getActive();
            if (active2 && includes(active2.mode, "hover") && active2.target && !within(active2.target, current) && !active2.tracker.movesTo(active2.$el)) {
              active2.hide(false);
            }
          }
        },
        {
          name: "keydown",
          delegate: function() {
            return this.dropdown;
          },
          handler: function(e) {
            var current = e.current;
            var keyCode = e.keyCode;
            var active2 = this.getActive();
            if (keyCode === keyMap.DOWN && hasAttr(current, "aria-expanded")) {
              e.preventDefault();
              if (!active2 || active2.target !== current) {
                current.click();
                once(this.dropContainer, "show", function(ref2) {
                  var target = ref2.target;
                  return focusFirstFocusableElement(target);
                });
              } else {
                focusFirstFocusableElement(active2.$el);
              }
            }
            handleNavItemNavigation(e, this.toggles, active2);
          }
        },
        {
          name: "keydown",
          el: function() {
            return this.dropContainer;
          },
          delegate: function() {
            return "." + this.clsDrop;
          },
          handler: function(e) {
            var current = e.current;
            var keyCode = e.keyCode;
            if (!includes(this.dropdowns, current)) {
              return;
            }
            var active2 = this.getActive();
            var elements = $$(selFocusable, current);
            var i = findIndex(elements, function(el) {
              return matches(el, ":focus");
            });
            if (keyCode === keyMap.UP) {
              e.preventDefault();
              if (i > 0) {
                elements[i - 1].focus();
              }
            }
            if (keyCode === keyMap.DOWN) {
              e.preventDefault();
              if (i < elements.length - 1) {
                elements[i + 1].focus();
              }
            }
            if (keyCode === keyMap.ESC) {
              active2 && active2.target && active2.target.focus();
            }
            handleNavItemNavigation(e, this.toggles, active2);
          }
        },
        {
          name: "mouseleave",
          el: function() {
            return this.dropbar;
          },
          filter: function() {
            return this.dropbar;
          },
          handler: function() {
            var active2 = this.getActive();
            if (active2 && includes(active2.mode, "hover") && !this.dropdowns.some(function(el) {
              return matches(el, ":hover");
            })) {
              active2.hide();
            }
          }
        },
        {
          name: "beforeshow",
          el: function() {
            return this.dropContainer;
          },
          filter: function() {
            return this.dropbar;
          },
          handler: function() {
            if (!parent(this.dropbar)) {
              after(this.dropbarAnchor || this.$el, this.dropbar);
            }
          }
        },
        {
          name: "show",
          el: function() {
            return this.dropContainer;
          },
          filter: function() {
            return this.dropbar;
          },
          handler: function(_2, ref2) {
            var $el = ref2.$el;
            var dir = ref2.dir;
            if (!hasClass($el, this.clsDrop)) {
              return;
            }
            if (this.dropbarMode === "slide") {
              addClass(this.dropbar, "uk-navbar-dropbar-slide");
            }
            this.clsDrop && addClass($el, this.clsDrop + "-dropbar");
            if (dir === "bottom") {
              this.transitionTo($el.offsetHeight + toFloat(css($el, "marginTop")) + toFloat(css($el, "marginBottom")), $el);
            }
          }
        },
        {
          name: "beforehide",
          el: function() {
            return this.dropContainer;
          },
          filter: function() {
            return this.dropbar;
          },
          handler: function(e, ref2) {
            var $el = ref2.$el;
            var active2 = this.getActive();
            if (matches(this.dropbar, ":hover") && active2 && active2.$el === $el) {
              e.preventDefault();
            }
          }
        },
        {
          name: "hide",
          el: function() {
            return this.dropContainer;
          },
          filter: function() {
            return this.dropbar;
          },
          handler: function(_2, ref2) {
            var $el = ref2.$el;
            if (!hasClass($el, this.clsDrop)) {
              return;
            }
            var active2 = this.getActive();
            if (!active2 || active2 && active2.$el === $el) {
              this.transitionTo(0);
            }
          }
        }
      ],
      methods: {
        getActive: function() {
          return active$1 && within(active$1.target, this.$el) && active$1;
        },
        transitionTo: function(newHeight, el) {
          var this$1$1 = this;
          var ref2 = this;
          var dropbar = ref2.dropbar;
          var oldHeight = isVisible(dropbar) ? height(dropbar) : 0;
          el = oldHeight < newHeight && el;
          css(el, "clip", "rect(0," + el.offsetWidth + "px," + oldHeight + "px,0)");
          height(dropbar, oldHeight);
          Transition.cancel([el, dropbar]);
          return Promise$1.all([
            Transition.start(dropbar, { height: newHeight }, this.duration),
            Transition.start(el, { clip: "rect(0," + el.offsetWidth + "px," + newHeight + "px,0)" }, this.duration)
          ]).catch(noop).then(function() {
            css(el, { clip: "" });
            this$1$1.$update(dropbar);
          });
        },
        getDropdown: function(el) {
          return this.$getComponent(el, "drop") || this.$getComponent(el, "dropdown");
        }
      }
    };
    function handleNavItemNavigation(e, toggles, active2) {
      var current = e.current;
      var keyCode = e.keyCode;
      var target = active2 && active2.target || current;
      var i = toggles.indexOf(target);
      if (keyCode === keyMap.LEFT && i > 0) {
        active2 && active2.hide(false);
        toggles[i - 1].focus();
      }
      if (keyCode === keyMap.RIGHT && i < toggles.length - 1) {
        active2 && active2.hide(false);
        toggles[i + 1].focus();
      }
      if (keyCode === keyMap.TAB) {
        target.focus();
        active2 && active2.hide(false);
      }
    }
    function focusFirstFocusableElement(el) {
      if (!$(":focus", el)) {
        var focusEl = $(selFocusable, el);
        if (focusEl) {
          focusEl.focus();
        }
      }
    }
    var keyMap = {
      TAB: 9,
      ESC: 27,
      LEFT: 37,
      UP: 38,
      RIGHT: 39,
      DOWN: 40
    };
    var offcanvas = {
      mixins: [Modal],
      args: "mode",
      props: {
        mode: String,
        flip: Boolean,
        overlay: Boolean
      },
      data: {
        mode: "slide",
        flip: false,
        overlay: false,
        clsPage: "uk-offcanvas-page",
        clsContainer: "uk-offcanvas-container",
        selPanel: ".uk-offcanvas-bar",
        clsFlip: "uk-offcanvas-flip",
        clsContainerAnimation: "uk-offcanvas-container-animation",
        clsSidebarAnimation: "uk-offcanvas-bar-animation",
        clsMode: "uk-offcanvas",
        clsOverlay: "uk-offcanvas-overlay",
        selClose: ".uk-offcanvas-close",
        container: false
      },
      computed: {
        clsFlip: function(ref2) {
          var flip = ref2.flip;
          var clsFlip = ref2.clsFlip;
          return flip ? clsFlip : "";
        },
        clsOverlay: function(ref2) {
          var overlay = ref2.overlay;
          var clsOverlay = ref2.clsOverlay;
          return overlay ? clsOverlay : "";
        },
        clsMode: function(ref2) {
          var mode = ref2.mode;
          var clsMode = ref2.clsMode;
          return clsMode + "-" + mode;
        },
        clsSidebarAnimation: function(ref2) {
          var mode = ref2.mode;
          var clsSidebarAnimation = ref2.clsSidebarAnimation;
          return mode === "none" || mode === "reveal" ? "" : clsSidebarAnimation;
        },
        clsContainerAnimation: function(ref2) {
          var mode = ref2.mode;
          var clsContainerAnimation = ref2.clsContainerAnimation;
          return mode !== "push" && mode !== "reveal" ? "" : clsContainerAnimation;
        },
        transitionElement: function(ref2) {
          var mode = ref2.mode;
          return mode === "reveal" ? parent(this.panel) : this.panel;
        }
      },
      update: {
        read: function() {
          if (this.isToggled() && !isVisible(this.$el)) {
            this.hide();
          }
        },
        events: ["resize"]
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return 'a[href^="#"]';
          },
          handler: function(ref2) {
            var hash = ref2.current.hash;
            var defaultPrevented = ref2.defaultPrevented;
            if (!defaultPrevented && hash && $(hash, document.body)) {
              this.hide();
            }
          }
        },
        {
          name: "touchstart",
          passive: true,
          el: function() {
            return this.panel;
          },
          handler: function(ref2) {
            var targetTouches = ref2.targetTouches;
            if (targetTouches.length === 1) {
              this.clientY = targetTouches[0].clientY;
            }
          }
        },
        {
          name: "touchmove",
          self: true,
          passive: false,
          filter: function() {
            return this.overlay;
          },
          handler: function(e) {
            e.cancelable && e.preventDefault();
          }
        },
        {
          name: "touchmove",
          passive: false,
          el: function() {
            return this.panel;
          },
          handler: function(e) {
            if (e.targetTouches.length !== 1) {
              return;
            }
            var clientY = e.targetTouches[0].clientY - this.clientY;
            var ref2 = this.panel;
            var scrollTop2 = ref2.scrollTop;
            var scrollHeight = ref2.scrollHeight;
            var clientHeight = ref2.clientHeight;
            if (clientHeight >= scrollHeight || scrollTop2 === 0 && clientY > 0 || scrollHeight - scrollTop2 <= clientHeight && clientY < 0) {
              e.cancelable && e.preventDefault();
            }
          }
        },
        {
          name: "show",
          self: true,
          handler: function() {
            if (this.mode === "reveal" && !hasClass(parent(this.panel), this.clsMode)) {
              wrapAll(this.panel, "<div>");
              addClass(parent(this.panel), this.clsMode);
            }
            css(document.documentElement, "overflowY", this.overlay ? "hidden" : "");
            addClass(document.body, this.clsContainer, this.clsFlip);
            css(document.body, "touch-action", "pan-y pinch-zoom");
            css(this.$el, "display", "block");
            addClass(this.$el, this.clsOverlay);
            addClass(this.panel, this.clsSidebarAnimation, this.mode !== "reveal" ? this.clsMode : "");
            height(document.body);
            addClass(document.body, this.clsContainerAnimation);
            this.clsContainerAnimation && suppressUserScale();
          }
        },
        {
          name: "hide",
          self: true,
          handler: function() {
            removeClass(document.body, this.clsContainerAnimation);
            css(document.body, "touch-action", "");
          }
        },
        {
          name: "hidden",
          self: true,
          handler: function() {
            this.clsContainerAnimation && resumeUserScale();
            if (this.mode === "reveal") {
              unwrap(this.panel);
            }
            removeClass(this.panel, this.clsSidebarAnimation, this.clsMode);
            removeClass(this.$el, this.clsOverlay);
            css(this.$el, "display", "");
            removeClass(document.body, this.clsContainer, this.clsFlip);
            css(document.documentElement, "overflowY", "");
          }
        },
        {
          name: "swipeLeft swipeRight",
          handler: function(e) {
            if (this.isToggled() && endsWith(e.type, "Left") ^ this.flip) {
              this.hide();
            }
          }
        }
      ]
    };
    function suppressUserScale() {
      getViewport().content += ",user-scalable=0";
    }
    function resumeUserScale() {
      var viewport = getViewport();
      viewport.content = viewport.content.replace(/,user-scalable=0$/, "");
    }
    function getViewport() {
      return $('meta[name="viewport"]', document.head) || append(document.head, '<meta name="viewport">');
    }
    var overflowAuto = {
      mixins: [Class],
      props: {
        selContainer: String,
        selContent: String,
        minHeight: Number
      },
      data: {
        selContainer: ".uk-modal",
        selContent: ".uk-modal-dialog",
        minHeight: 150
      },
      computed: {
        container: function(ref2, $el) {
          var selContainer = ref2.selContainer;
          return closest($el, selContainer);
        },
        content: function(ref2, $el) {
          var selContent = ref2.selContent;
          return closest($el, selContent);
        }
      },
      connected: function() {
        css(this.$el, "minHeight", this.minHeight);
      },
      update: {
        read: function() {
          if (!this.content || !this.container || !isVisible(this.$el)) {
            return false;
          }
          return {
            current: toFloat(css(this.$el, "maxHeight")),
            max: Math.max(this.minHeight, height(this.container) - (dimensions(this.content).height - height(this.$el)))
          };
        },
        write: function(ref2) {
          var current = ref2.current;
          var max = ref2.max;
          css(this.$el, "maxHeight", max);
          if (Math.round(current) !== Math.round(max)) {
            trigger(this.$el, "resize");
          }
        },
        events: ["resize"]
      }
    };
    var responsive = {
      props: ["width", "height"],
      connected: function() {
        addClass(this.$el, "uk-responsive-width");
      },
      update: {
        read: function() {
          return isVisible(this.$el) && this.width && this.height ? { width: width(parent(this.$el)), height: this.height } : false;
        },
        write: function(dim) {
          height(this.$el, Dimensions.contain({
            height: this.height,
            width: this.width
          }, dim).height);
        },
        events: ["resize"]
      }
    };
    var scroll = {
      props: {
        offset: Number
      },
      data: {
        offset: 0
      },
      methods: {
        scrollTo: function(el) {
          var this$1$1 = this;
          el = el && $(el) || document.body;
          if (trigger(this.$el, "beforescroll", [this, el])) {
            scrollIntoView(el, { offset: this.offset }).then(function() {
              return trigger(this$1$1.$el, "scrolled", [this$1$1, el]);
            });
          }
        }
      },
      events: {
        click: function(e) {
          if (e.defaultPrevented) {
            return;
          }
          e.preventDefault();
          this.scrollTo("#" + escape(decodeURIComponent((this.$el.hash || "").substr(1))));
        }
      }
    };
    var stateKey = "_ukScrollspy";
    var scrollspy = {
      args: "cls",
      props: {
        cls: String,
        target: String,
        hidden: Boolean,
        offsetTop: Number,
        offsetLeft: Number,
        repeat: Boolean,
        delay: Number
      },
      data: function() {
        return {
          cls: false,
          target: false,
          hidden: true,
          offsetTop: 0,
          offsetLeft: 0,
          repeat: false,
          delay: 0,
          inViewClass: "uk-scrollspy-inview"
        };
      },
      computed: {
        elements: {
          get: function(ref2, $el) {
            var target = ref2.target;
            return target ? $$(target, $el) : [$el];
          },
          watch: function(elements) {
            if (this.hidden) {
              css(filter$1(elements, ":not(." + this.inViewClass + ")"), "visibility", "hidden");
            }
          },
          immediate: true
        }
      },
      disconnected: function() {
        var this$1$1 = this;
        this.elements.forEach(function(el) {
          removeClass(el, this$1$1.inViewClass, el[stateKey] ? el[stateKey].cls : "");
          delete el[stateKey];
        });
      },
      update: [
        {
          read: function(data$1) {
            var this$1$1 = this;
            if (!data$1.update) {
              Promise$1.resolve().then(function() {
                this$1$1.$emit();
                data$1.update = true;
              });
              return false;
            }
            this.elements.forEach(function(el) {
              if (!el[stateKey]) {
                el[stateKey] = { cls: data(el, "uk-scrollspy-class") || this$1$1.cls };
              }
              el[stateKey].show = isInView(el, this$1$1.offsetTop, this$1$1.offsetLeft);
            });
          },
          write: function(data2) {
            var this$1$1 = this;
            this.elements.forEach(function(el) {
              var state = el[stateKey];
              if (state.show && !state.inview && !state.queued) {
                state.queued = true;
                data2.promise = (data2.promise || Promise$1.resolve()).then(function() {
                  return new Promise$1(function(resolve) {
                    return setTimeout(resolve, this$1$1.delay);
                  });
                }).then(function() {
                  this$1$1.toggle(el, true);
                  setTimeout(function() {
                    state.queued = false;
                    this$1$1.$emit();
                  }, 300);
                });
              } else if (!state.show && state.inview && !state.queued && this$1$1.repeat) {
                this$1$1.toggle(el, false);
              }
            });
          },
          events: ["scroll", "resize"]
        }
      ],
      methods: {
        toggle: function(el, inview) {
          var state = el[stateKey];
          state.off && state.off();
          css(el, "visibility", !inview && this.hidden ? "hidden" : "");
          toggleClass(el, this.inViewClass, inview);
          toggleClass(el, state.cls);
          if (/\buk-animation-/.test(state.cls)) {
            state.off = once(el, "animationcancel animationend", function() {
              return removeClasses(el, "uk-animation-[\\w-]+");
            });
          }
          trigger(el, inview ? "inview" : "outview");
          state.inview = inview;
          this.$update(el);
        }
      }
    };
    var scrollspyNav = {
      props: {
        cls: String,
        closest: String,
        scroll: Boolean,
        overflow: Boolean,
        offset: Number
      },
      data: {
        cls: "uk-active",
        closest: false,
        scroll: false,
        overflow: true,
        offset: 0
      },
      computed: {
        links: {
          get: function(_2, $el) {
            return $$('a[href^="#"]', $el).filter(function(el) {
              return el.hash;
            });
          },
          watch: function(links) {
            if (this.scroll) {
              this.$create("scroll", links, { offset: this.offset || 0 });
            }
          },
          immediate: true
        },
        targets: function() {
          return $$(this.links.map(function(el) {
            return escape(el.hash).substr(1);
          }).join(","));
        },
        elements: function(ref2) {
          var selector = ref2.closest;
          return closest(this.links, selector || "*");
        }
      },
      update: [
        {
          read: function() {
            var this$1$1 = this;
            var ref2 = this.targets;
            var length = ref2.length;
            if (!length || !isVisible(this.$el)) {
              return false;
            }
            var ref$1 = scrollParents(this.targets, /auto|scroll/, true);
            var scrollElement = ref$1[0];
            var scrollTop2 = scrollElement.scrollTop;
            var scrollHeight = scrollElement.scrollHeight;
            var max = scrollHeight - getViewportClientHeight(scrollElement);
            var active2 = false;
            if (scrollTop2 === max) {
              active2 = length - 1;
            } else {
              this.targets.every(function(el, i) {
                if (offset(el).top - offset(getViewport$1(scrollElement)).top - this$1$1.offset <= 0) {
                  active2 = i;
                  return true;
                }
              });
              if (active2 === false && this.overflow) {
                active2 = 0;
              }
            }
            return { active: active2 };
          },
          write: function(ref2) {
            var active2 = ref2.active;
            var changed = active2 !== false && !hasClass(this.elements[active2], this.cls);
            this.links.forEach(function(el) {
              return el.blur();
            });
            removeClass(this.elements, this.cls);
            addClass(this.elements[active2], this.cls);
            if (changed) {
              trigger(this.$el, "active", [active2, this.elements[active2]]);
            }
          },
          events: ["scroll", "resize"]
        }
      ]
    };
    var sticky = {
      mixins: [Class, Media],
      props: {
        top: null,
        bottom: Boolean,
        offset: String,
        animation: String,
        clsActive: String,
        clsInactive: String,
        clsFixed: String,
        clsBelow: String,
        selTarget: String,
        widthElement: Boolean,
        showOnUp: Boolean,
        targetOffset: Number
      },
      data: {
        top: 0,
        bottom: false,
        offset: 0,
        animation: "",
        clsActive: "uk-active",
        clsInactive: "",
        clsFixed: "uk-sticky-fixed",
        clsBelow: "uk-sticky-below",
        selTarget: "",
        widthElement: false,
        showOnUp: false,
        targetOffset: false
      },
      computed: {
        offset: function(ref2) {
          var offset2 = ref2.offset;
          return toPx(offset2);
        },
        selTarget: function(ref2, $el) {
          var selTarget = ref2.selTarget;
          return selTarget && $(selTarget, $el) || $el;
        },
        widthElement: function(ref2, $el) {
          var widthElement = ref2.widthElement;
          return query(widthElement, $el) || this.placeholder;
        },
        isActive: {
          get: function() {
            return hasClass(this.selTarget, this.clsActive);
          },
          set: function(value) {
            if (value && !this.isActive) {
              replaceClass(this.selTarget, this.clsInactive, this.clsActive);
              trigger(this.$el, "active");
            } else if (!value && !hasClass(this.selTarget, this.clsInactive)) {
              replaceClass(this.selTarget, this.clsActive, this.clsInactive);
              trigger(this.$el, "inactive");
            }
          }
        }
      },
      connected: function() {
        this.placeholder = $("+ .uk-sticky-placeholder", this.$el) || $('<div class="uk-sticky-placeholder"></div>');
        this.isFixed = false;
        this.isActive = false;
      },
      disconnected: function() {
        if (this.isFixed) {
          this.hide();
          removeClass(this.selTarget, this.clsInactive);
        }
        remove$1(this.placeholder);
        this.placeholder = null;
        this.widthElement = null;
      },
      events: [
        {
          name: "load hashchange popstate",
          el: function() {
            return window;
          },
          handler: function() {
            var this$1$1 = this;
            if (!(this.targetOffset !== false && location.hash && window.pageYOffset > 0)) {
              return;
            }
            var target = $(location.hash);
            if (target) {
              fastdom.read(function() {
                var ref2 = offset(target);
                var top = ref2.top;
                var elTop = offset(this$1$1.$el).top;
                var elHeight = this$1$1.$el.offsetHeight;
                if (this$1$1.isFixed && elTop + elHeight >= top && elTop <= top + target.offsetHeight) {
                  scrollTop(window, top - elHeight - (isNumeric(this$1$1.targetOffset) ? this$1$1.targetOffset : 0) - this$1$1.offset);
                }
              });
            }
          }
        }
      ],
      update: [
        {
          read: function(ref2, types) {
            var height$1 = ref2.height;
            this.inactive = !this.matchMedia || !isVisible(this.$el);
            if (this.inactive) {
              return false;
            }
            if (this.isActive && types.has("resize")) {
              this.hide();
              height$1 = this.$el.offsetHeight;
              this.show();
            }
            height$1 = this.isActive ? height$1 : this.$el.offsetHeight;
            if (height$1 + this.offset > height(window)) {
              this.inactive = true;
              return false;
            }
            var referenceElement = this.isFixed ? this.placeholder : this.$el;
            this.topOffset = offset(referenceElement).top;
            this.bottomOffset = this.topOffset + height$1;
            this.offsetParentTop = offset(referenceElement.offsetParent).top;
            var bottom = parseProp("bottom", this);
            this.top = Math.max(toFloat(parseProp("top", this)), this.topOffset) - this.offset;
            this.bottom = bottom && bottom - this.$el.offsetHeight;
            this.width = dimensions(isVisible(this.widthElement) ? this.widthElement : this.$el).width;
            return {
              height: height$1,
              top: offsetPosition(this.placeholder)[0],
              margins: css(this.$el, ["marginTop", "marginBottom", "marginLeft", "marginRight"])
            };
          },
          write: function(ref2) {
            var height2 = ref2.height;
            var margins = ref2.margins;
            var ref$1 = this;
            var placeholder = ref$1.placeholder;
            css(placeholder, assign({ height: height2 }, margins));
            if (!within(placeholder, document)) {
              after(this.$el, placeholder);
              placeholder.hidden = true;
            }
            this.isActive = !!this.isActive;
          },
          events: ["resize"]
        },
        {
          read: function(ref2) {
            var scroll2 = ref2.scroll;
            if (scroll2 === void 0)
              scroll2 = 0;
            this.scroll = window.pageYOffset;
            return {
              dir: scroll2 <= this.scroll ? "down" : "up",
              scroll: this.scroll
            };
          },
          write: function(data2, types) {
            var this$1$1 = this;
            var now = Date.now();
            var isScrollUpdate = types.has("scroll");
            var initTimestamp = data2.initTimestamp;
            if (initTimestamp === void 0)
              initTimestamp = 0;
            var dir = data2.dir;
            var lastDir = data2.lastDir;
            var lastScroll = data2.lastScroll;
            var scroll2 = data2.scroll;
            var top = data2.top;
            data2.lastScroll = scroll2;
            if (scroll2 < 0 || scroll2 === lastScroll && isScrollUpdate || this.showOnUp && !isScrollUpdate && !this.isFixed) {
              return;
            }
            if (now - initTimestamp > 300 || dir !== lastDir) {
              data2.initScroll = scroll2;
              data2.initTimestamp = now;
            }
            data2.lastDir = dir;
            if (this.showOnUp && !this.isFixed && Math.abs(data2.initScroll - scroll2) <= 30 && Math.abs(lastScroll - scroll2) <= 10) {
              return;
            }
            if (this.inactive || scroll2 < this.top || this.showOnUp && (scroll2 <= this.top || dir === "down" && isScrollUpdate || dir === "up" && !this.isFixed && scroll2 <= this.bottomOffset)) {
              if (!this.isFixed) {
                if (Animation.inProgress(this.$el) && top > scroll2) {
                  Animation.cancel(this.$el);
                  this.hide();
                }
                return;
              }
              this.isFixed = false;
              if (this.animation && scroll2 > this.topOffset) {
                Animation.cancel(this.$el);
                Animation.out(this.$el, this.animation).then(function() {
                  return this$1$1.hide();
                }, noop);
              } else {
                this.hide();
              }
            } else if (this.isFixed) {
              this.update();
            } else if (this.animation) {
              Animation.cancel(this.$el);
              this.show();
              Animation.in(this.$el, this.animation).catch(noop);
            } else {
              this.show();
            }
          },
          events: ["resize", "scroll"]
        }
      ],
      methods: {
        show: function() {
          this.isFixed = true;
          this.update();
          this.placeholder.hidden = false;
        },
        hide: function() {
          this.isActive = false;
          removeClass(this.$el, this.clsFixed, this.clsBelow);
          css(this.$el, { position: "", top: "", width: "" });
          this.placeholder.hidden = true;
        },
        update: function() {
          var active2 = this.top !== 0 || this.scroll > this.top;
          var top = Math.max(0, this.offset);
          var position2 = "fixed";
          if (isNumeric(this.bottom) && this.scroll > this.bottom - this.offset) {
            top = this.bottom - this.offsetParentTop;
            position2 = "absolute";
          }
          css(this.$el, {
            position: position2,
            top: top + "px",
            width: this.width
          });
          this.isActive = active2;
          toggleClass(this.$el, this.clsBelow, this.scroll > this.bottomOffset);
          addClass(this.$el, this.clsFixed);
        }
      }
    };
    function parseProp(prop, ref2) {
      var $props = ref2.$props;
      var $el = ref2.$el;
      var propOffset = ref2[prop + "Offset"];
      var value = $props[prop];
      if (!value) {
        return;
      }
      if (isString(value) && value.match(/^-?\d/)) {
        return propOffset + toPx(value);
      } else {
        return offset(value === true ? parent($el) : query(value, $el)).bottom;
      }
    }
    var Switcher = {
      mixins: [Togglable],
      args: "connect",
      props: {
        connect: String,
        toggle: String,
        itemNav: String,
        active: Number,
        swiping: Boolean
      },
      data: {
        connect: "~.uk-switcher",
        toggle: "> * > :first-child",
        itemNav: false,
        active: 0,
        swiping: true,
        cls: "uk-active",
        attrItem: "uk-switcher-item"
      },
      computed: {
        connects: {
          get: function(ref2, $el) {
            var connect = ref2.connect;
            return queryAll(connect, $el);
          },
          watch: function(connects) {
            var this$1$1 = this;
            if (this.swiping) {
              css(connects, "touch-action", "pan-y pinch-zoom");
            }
            var index2 = this.index();
            this.connects.forEach(function(el) {
              return children(el).forEach(function(child, i) {
                return toggleClass(child, this$1$1.cls, i === index2);
              });
            });
          },
          immediate: true
        },
        toggles: {
          get: function(ref2, $el) {
            var toggle2 = ref2.toggle;
            return $$(toggle2, $el).filter(function(el) {
              return !matches(el, ".uk-disabled *, .uk-disabled, [disabled]");
            });
          },
          watch: function(toggles) {
            var active2 = this.index();
            this.show(~active2 ? active2 : toggles[this.active] || toggles[0]);
          },
          immediate: true
        },
        children: function() {
          var this$1$1 = this;
          return children(this.$el).filter(function(child) {
            return this$1$1.toggles.some(function(toggle2) {
              return within(toggle2, child);
            });
          });
        }
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.toggle;
          },
          handler: function(e) {
            e.preventDefault();
            this.show(e.current);
          }
        },
        {
          name: "click",
          el: function() {
            return this.connects.concat(this.itemNav ? queryAll(this.itemNav, this.$el) : []);
          },
          delegate: function() {
            return "[" + this.attrItem + "],[data-" + this.attrItem + "]";
          },
          handler: function(e) {
            e.preventDefault();
            this.show(data(e.current, this.attrItem));
          }
        },
        {
          name: "swipeRight swipeLeft",
          filter: function() {
            return this.swiping;
          },
          el: function() {
            return this.connects;
          },
          handler: function(ref2) {
            var type = ref2.type;
            this.show(endsWith(type, "Left") ? "next" : "previous");
          }
        }
      ],
      methods: {
        index: function() {
          var this$1$1 = this;
          return findIndex(this.children, function(el) {
            return hasClass(el, this$1$1.cls);
          });
        },
        show: function(item) {
          var this$1$1 = this;
          var prev = this.index();
          var next = getIndex(this.children[getIndex(item, this.toggles, prev)], children(this.$el));
          if (prev === next) {
            return;
          }
          this.children.forEach(function(child, i) {
            toggleClass(child, this$1$1.cls, next === i);
            attr(this$1$1.toggles[i], "aria-expanded", next === i);
          });
          this.connects.forEach(function(ref2) {
            var children2 = ref2.children;
            return this$1$1.toggleElement(toNodes(children2).filter(function(child) {
              return hasClass(child, this$1$1.cls);
            }), false, prev >= 0).then(function() {
              return this$1$1.toggleElement(children2[next], true, prev >= 0);
            });
          });
        }
      }
    };
    var tab = {
      mixins: [Class],
      extends: Switcher,
      props: {
        media: Boolean
      },
      data: {
        media: 960,
        attrItem: "uk-tab-item"
      },
      connected: function() {
        var cls = hasClass(this.$el, "uk-tab-left") ? "uk-tab-left" : hasClass(this.$el, "uk-tab-right") ? "uk-tab-right" : false;
        if (cls) {
          this.$create("toggle", this.$el, { cls, mode: "media", media: this.media });
        }
      }
    };
    var KEY_SPACE = 32;
    var toggle = {
      mixins: [Media, Togglable],
      args: "target",
      props: {
        href: String,
        target: null,
        mode: "list",
        queued: Boolean
      },
      data: {
        href: false,
        target: false,
        mode: "click",
        queued: true
      },
      connected: function() {
        if (!includes(this.mode, "media") && !isFocusable(this.$el)) {
          attr(this.$el, "tabindex", "0");
        }
      },
      computed: {
        target: {
          get: function(ref2, $el) {
            var href = ref2.href;
            var target = ref2.target;
            target = queryAll(target || href, $el);
            return target.length && target || [$el];
          },
          watch: function() {
            this.updateAria();
          },
          immediate: true
        }
      },
      events: [
        {
          name: pointerDown,
          filter: function() {
            return includes(this.mode, "hover");
          },
          handler: function(e) {
            var this$1$1 = this;
            if (!isTouch(e) || this._showState) {
              return;
            }
            trigger(this.$el, "focus");
            once(document, pointerDown, function() {
              return trigger(this$1$1.$el, "blur");
            }, true, function(e2) {
              return !within(e2.target, this$1$1.$el);
            });
            if (includes(this.mode, "click")) {
              this._preventClick = true;
            }
          }
        },
        {
          name: pointerEnter + " " + pointerLeave + " focus blur",
          filter: function() {
            return includes(this.mode, "hover");
          },
          handler: function(e) {
            if (isTouch(e)) {
              return;
            }
            var show = includes([pointerEnter, "focus"], e.type);
            var expanded = attr(this.$el, "aria-expanded");
            if (!show && (e.type === pointerLeave && matches(this.$el, ":focus") || e.type === "blur" && matches(this.$el, ":hover"))) {
              return;
            }
            if (this._showState && show === (expanded !== this._showState)) {
              if (!show) {
                this._showState = null;
              }
              return;
            }
            this._showState = show ? expanded : null;
            this.toggle("toggle" + (show ? "show" : "hide"));
          }
        },
        {
          name: "keydown",
          filter: function() {
            return includes(this.mode, "click") && this.$el.tagName !== "INPUT";
          },
          handler: function(e) {
            if (e.keyCode === KEY_SPACE) {
              e.preventDefault();
              this.$el.click();
            }
          }
        },
        {
          name: "click",
          filter: function() {
            return includes(this.mode, "click");
          },
          handler: function(e) {
            if (this._preventClick) {
              return this._preventClick = null;
            }
            var link;
            if (closest(e.target, 'a[href="#"], a[href=""]') || (link = closest(e.target, "a[href]")) && (attr(this.$el, "aria-expanded") !== "true" || link.hash && matches(this.target, link.hash))) {
              e.preventDefault();
            }
            this.toggle();
          }
        },
        {
          name: "toggled",
          self: true,
          el: function() {
            return this.target;
          },
          handler: function(e, toggled) {
            if (e.target === this.target[0]) {
              this.updateAria(toggled);
            }
          }
        }
      ],
      update: {
        read: function() {
          return includes(this.mode, "media") && this.media ? { match: this.matchMedia } : false;
        },
        write: function(ref2) {
          var match2 = ref2.match;
          var toggled = this.isToggled(this.target);
          if (match2 ? !toggled : toggled) {
            this.toggle();
          }
        },
        events: ["resize"]
      },
      methods: {
        toggle: function(type) {
          var this$1$1 = this;
          if (!trigger(this.target, type || "toggle", [this])) {
            return;
          }
          if (!this.queued) {
            return this.toggleElement(this.target);
          }
          var leaving = this.target.filter(function(el) {
            return hasClass(el, this$1$1.clsLeave);
          });
          if (leaving.length) {
            this.target.forEach(function(el) {
              var isLeaving = includes(leaving, el);
              this$1$1.toggleElement(el, isLeaving, isLeaving);
            });
            return;
          }
          var toggled = this.target.filter(this.isToggled);
          this.toggleElement(toggled, false).then(function() {
            return this$1$1.toggleElement(this$1$1.target.filter(function(el) {
              return !includes(toggled, el);
            }), true);
          });
        },
        updateAria: function(toggled) {
          if (includes(this.mode, "media")) {
            return;
          }
          attr(this.$el, "aria-expanded", isBoolean(toggled) ? toggled : this.isToggled(this.target));
        }
      }
    };
    var components$1 = /* @__PURE__ */ Object.freeze({
      __proto__: null,
      Accordion,
      Alert: alert,
      Cover: cover,
      Drop: drop,
      Dropdown: drop,
      FormCustom: formCustom,
      Gif: gif,
      Grid: grid,
      HeightMatch: heightMatch,
      HeightViewport: heightViewport,
      Icon,
      Img: img,
      Leader: leader,
      Margin,
      Modal: modal,
      Nav: nav,
      Navbar: navbar,
      Offcanvas: offcanvas,
      OverflowAuto: overflowAuto,
      Responsive: responsive,
      Scroll: scroll,
      Scrollspy: scrollspy,
      ScrollspyNav: scrollspyNav,
      Sticky: sticky,
      Svg: SVG,
      Switcher,
      Tab: tab,
      Toggle: toggle,
      Video,
      Close,
      Spinner,
      SlidenavNext: Slidenav,
      SlidenavPrevious: Slidenav,
      SearchIcon: Search,
      Marker: IconComponent,
      NavbarToggleIcon: IconComponent,
      OverlayIcon: IconComponent,
      PaginationNext: IconComponent,
      PaginationPrevious: IconComponent,
      Totop: IconComponent
    });
    each(components$1, function(component, name) {
      return UIkit2.component(name, component);
    });
    UIkit2.use(Core);
    boot(UIkit2);
    var countdown = {
      mixins: [Class],
      props: {
        date: String,
        clsWrapper: String
      },
      data: {
        date: "",
        clsWrapper: ".uk-countdown-%unit%"
      },
      computed: {
        date: function(ref2) {
          var date = ref2.date;
          return Date.parse(date);
        },
        days: function(ref2, $el) {
          var clsWrapper = ref2.clsWrapper;
          return $(clsWrapper.replace("%unit%", "days"), $el);
        },
        hours: function(ref2, $el) {
          var clsWrapper = ref2.clsWrapper;
          return $(clsWrapper.replace("%unit%", "hours"), $el);
        },
        minutes: function(ref2, $el) {
          var clsWrapper = ref2.clsWrapper;
          return $(clsWrapper.replace("%unit%", "minutes"), $el);
        },
        seconds: function(ref2, $el) {
          var clsWrapper = ref2.clsWrapper;
          return $(clsWrapper.replace("%unit%", "seconds"), $el);
        },
        units: function() {
          var this$1$1 = this;
          return ["days", "hours", "minutes", "seconds"].filter(function(unit) {
            return this$1$1[unit];
          });
        }
      },
      connected: function() {
        this.start();
      },
      disconnected: function() {
        var this$1$1 = this;
        this.stop();
        this.units.forEach(function(unit) {
          return empty(this$1$1[unit]);
        });
      },
      events: [
        {
          name: "visibilitychange",
          el: function() {
            return document;
          },
          handler: function() {
            if (document.hidden) {
              this.stop();
            } else {
              this.start();
            }
          }
        }
      ],
      update: {
        write: function() {
          var this$1$1 = this;
          var timespan = getTimeSpan(this.date);
          if (timespan.total <= 0) {
            this.stop();
            timespan.days = timespan.hours = timespan.minutes = timespan.seconds = 0;
          }
          this.units.forEach(function(unit) {
            var digits = String(Math.floor(timespan[unit]));
            digits = digits.length < 2 ? "0" + digits : digits;
            var el = this$1$1[unit];
            if (el.textContent !== digits) {
              digits = digits.split("");
              if (digits.length !== el.children.length) {
                html(el, digits.map(function() {
                  return "<span></span>";
                }).join(""));
              }
              digits.forEach(function(digit, i) {
                return el.children[i].textContent = digit;
              });
            }
          });
        }
      },
      methods: {
        start: function() {
          this.stop();
          if (this.date && this.units.length) {
            this.$update();
            this.timer = setInterval(this.$update, 1e3);
          }
        },
        stop: function() {
          if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
          }
        }
      }
    };
    function getTimeSpan(date) {
      var total = date - Date.now();
      return {
        total,
        seconds: total / 1e3 % 60,
        minutes: total / 1e3 / 60 % 60,
        hours: total / 1e3 / 60 / 60 % 24,
        days: total / 1e3 / 60 / 60 / 24
      };
    }
    var clsLeave = "uk-transition-leave";
    var clsEnter = "uk-transition-enter";
    function fade(action, target, duration, stagger) {
      if (stagger === void 0)
        stagger = 0;
      var index2 = transitionIndex(target, true);
      var propsIn = { opacity: 1 };
      var propsOut = { opacity: 0 };
      var wrapIndexFn = function(fn) {
        return function() {
          return index2 === transitionIndex(target) ? fn() : Promise$1.reject();
        };
      };
      var leaveFn = wrapIndexFn(function() {
        addClass(target, clsLeave);
        return Promise$1.all(getTransitionNodes(target).map(function(child, i) {
          return new Promise$1(function(resolve) {
            return setTimeout(function() {
              return Transition.start(child, propsOut, duration / 2, "ease").then(resolve);
            }, i * stagger);
          });
        })).then(function() {
          return removeClass(target, clsLeave);
        });
      });
      var enterFn = wrapIndexFn(function() {
        var oldHeight = height(target);
        addClass(target, clsEnter);
        action();
        css(children(target), { opacity: 0 });
        return new Promise$1(function(resolve) {
          return requestAnimationFrame(function() {
            var nodes = children(target);
            var newHeight = height(target);
            css(target, "alignContent", "flex-start");
            height(target, oldHeight);
            var transitionNodes = getTransitionNodes(target);
            css(nodes, propsOut);
            var transitions = transitionNodes.map(function(child, i) {
              return new Promise$1(function(resolve2) {
                return setTimeout(function() {
                  return Transition.start(child, propsIn, duration / 2, "ease").then(resolve2);
                }, i * stagger);
              });
            });
            if (oldHeight !== newHeight) {
              transitions.push(Transition.start(target, { height: newHeight }, duration / 2 + transitionNodes.length * stagger, "ease"));
            }
            Promise$1.all(transitions).then(function() {
              removeClass(target, clsEnter);
              if (index2 === transitionIndex(target)) {
                css(target, { height: "", alignContent: "" });
                css(nodes, { opacity: "" });
                delete target.dataset.transition;
              }
              resolve();
            });
          });
        });
      });
      return hasClass(target, clsLeave) ? waitTransitionend(target).then(enterFn) : hasClass(target, clsEnter) ? waitTransitionend(target).then(leaveFn).then(enterFn) : leaveFn().then(enterFn);
    }
    function transitionIndex(target, next) {
      if (next) {
        target.dataset.transition = 1 + transitionIndex(target);
      }
      return toNumber(target.dataset.transition) || 0;
    }
    function waitTransitionend(target) {
      return Promise$1.all(children(target).filter(Transition.inProgress).map(function(el) {
        return new Promise$1(function(resolve) {
          return once(el, "transitionend transitioncanceled", resolve);
        });
      }));
    }
    function getTransitionNodes(target) {
      return getRows(children(target)).reduce(function(nodes, row) {
        return nodes.concat(sortBy$1(row.filter(function(el) {
          return isInView(el);
        }), "offsetLeft"));
      }, []);
    }
    function slide(action, target, duration) {
      return new Promise$1(function(resolve) {
        return requestAnimationFrame(function() {
          var nodes = children(target);
          var currentProps = nodes.map(function(el) {
            return getProps(el, true);
          });
          var targetProps = css(target, ["height", "padding"]);
          Transition.cancel(target);
          nodes.forEach(Transition.cancel);
          reset(target);
          action();
          nodes = nodes.concat(children(target).filter(function(el) {
            return !includes(nodes, el);
          }));
          Promise$1.resolve().then(function() {
            fastdom.flush();
            var targetPropsTo = css(target, ["height", "padding"]);
            var ref2 = getTransitionProps(target, nodes, currentProps);
            var propsTo = ref2[0];
            var propsFrom = ref2[1];
            nodes.forEach(function(el, i) {
              return propsFrom[i] && css(el, propsFrom[i]);
            });
            css(target, assign({ display: "block" }, targetProps));
            requestAnimationFrame(function() {
              var transitions = nodes.map(function(el, i) {
                return parent(el) === target && Transition.start(el, propsTo[i], duration, "ease");
              }).concat(Transition.start(target, targetPropsTo, duration, "ease"));
              Promise$1.all(transitions).then(function() {
                nodes.forEach(function(el, i) {
                  return parent(el) === target && css(el, "display", propsTo[i].opacity === 0 ? "none" : "");
                });
                reset(target);
              }, noop).then(resolve);
            });
          });
        });
      });
    }
    function getProps(el, opacity) {
      var zIndex = css(el, "zIndex");
      return isVisible(el) ? assign({
        display: "",
        opacity: opacity ? css(el, "opacity") : "0",
        pointerEvents: "none",
        position: "absolute",
        zIndex: zIndex === "auto" ? index(el) : zIndex
      }, getPositionWithMargin(el)) : false;
    }
    function getTransitionProps(target, nodes, currentProps) {
      var propsTo = nodes.map(function(el, i) {
        return parent(el) && i in currentProps ? currentProps[i] ? isVisible(el) ? getPositionWithMargin(el) : { opacity: 0 } : { opacity: isVisible(el) ? 1 : 0 } : false;
      });
      var propsFrom = propsTo.map(function(props2, i) {
        var from = parent(nodes[i]) === target && (currentProps[i] || getProps(nodes[i]));
        if (!from) {
          return false;
        }
        if (!props2) {
          delete from.opacity;
        } else if (!("opacity" in props2)) {
          var opacity = from.opacity;
          if (opacity % 1) {
            props2.opacity = 1;
          } else {
            delete from.opacity;
          }
        }
        return from;
      });
      return [propsTo, propsFrom];
    }
    function reset(el) {
      css(el.children, {
        height: "",
        left: "",
        opacity: "",
        pointerEvents: "",
        position: "",
        top: "",
        marginTop: "",
        marginLeft: "",
        transform: "",
        width: "",
        zIndex: ""
      });
      css(el, { height: "", display: "", padding: "" });
    }
    function getPositionWithMargin(el) {
      var ref2 = offset(el);
      var height2 = ref2.height;
      var width2 = ref2.width;
      var ref$1 = position(el);
      var top = ref$1.top;
      var left = ref$1.left;
      var ref$2 = css(el, ["marginTop", "marginLeft"]);
      var marginLeft = ref$2.marginLeft;
      var marginTop = ref$2.marginTop;
      return { top, left, height: height2, width: width2, marginLeft, marginTop, transform: "" };
    }
    var Animate = {
      props: {
        duration: Number,
        animation: Boolean
      },
      data: {
        duration: 150,
        animation: "slide"
      },
      methods: {
        animate: function(action, target) {
          var this$1$1 = this;
          if (target === void 0)
            target = this.$el;
          var name = this.animation;
          var animationFn = name === "fade" ? fade : name === "delayed-fade" ? function() {
            var args = [], len = arguments.length;
            while (len--)
              args[len] = arguments[len];
            return fade.apply(void 0, args.concat([40]));
          } : name ? slide : function() {
            action();
            return Promise$1.resolve();
          };
          return animationFn(action, target, this.duration).then(function() {
            return this$1$1.$update(target, "resize");
          }, noop);
        }
      }
    };
    var filter = {
      mixins: [Animate],
      args: "target",
      props: {
        target: Boolean,
        selActive: Boolean
      },
      data: {
        target: null,
        selActive: false,
        attrItem: "uk-filter-control",
        cls: "uk-active",
        duration: 250
      },
      computed: {
        toggles: {
          get: function(ref2, $el) {
            var attrItem = ref2.attrItem;
            return $$("[" + attrItem + "],[data-" + attrItem + "]", $el);
          },
          watch: function() {
            var this$1$1 = this;
            this.updateState();
            if (this.selActive !== false) {
              var actives = $$(this.selActive, this.$el);
              this.toggles.forEach(function(el) {
                return toggleClass(el, this$1$1.cls, includes(actives, el));
              });
            }
          },
          immediate: true
        },
        children: {
          get: function(ref2, $el) {
            var target = ref2.target;
            return $$(target + " > *", $el);
          },
          watch: function(list, old) {
            if (old && !isEqualList(list, old)) {
              this.updateState();
            }
          },
          immediate: true
        }
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return "[" + this.attrItem + "],[data-" + this.attrItem + "]";
          },
          handler: function(e) {
            e.preventDefault();
            this.apply(e.current);
          }
        }
      ],
      methods: {
        apply: function(el) {
          var prevState = this.getState();
          var newState = mergeState(el, this.attrItem, this.getState());
          if (!isEqualState(prevState, newState)) {
            this.setState(newState);
          }
        },
        getState: function() {
          var this$1$1 = this;
          return this.toggles.filter(function(item) {
            return hasClass(item, this$1$1.cls);
          }).reduce(function(state, el) {
            return mergeState(el, this$1$1.attrItem, state);
          }, { filter: { "": "" }, sort: [] });
        },
        setState: function(state, animate2) {
          var this$1$1 = this;
          if (animate2 === void 0)
            animate2 = true;
          state = assign({ filter: { "": "" }, sort: [] }, state);
          trigger(this.$el, "beforeFilter", [this, state]);
          this.toggles.forEach(function(el) {
            return toggleClass(el, this$1$1.cls, !!matchFilter(el, this$1$1.attrItem, state));
          });
          Promise$1.all($$(this.target, this.$el).map(function(target) {
            var filterFn = function() {
              applyState(state, target, children(target));
              this$1$1.$update(this$1$1.$el);
            };
            return animate2 ? this$1$1.animate(filterFn, target) : filterFn();
          })).then(function() {
            return trigger(this$1$1.$el, "afterFilter", [this$1$1]);
          });
        },
        updateState: function() {
          var this$1$1 = this;
          fastdom.write(function() {
            return this$1$1.setState(this$1$1.getState(), false);
          });
        }
      }
    };
    function getFilter(el, attr2) {
      return parseOptions(data(el, attr2), ["filter"]);
    }
    function isEqualState(stateA, stateB) {
      return ["filter", "sort"].every(function(prop) {
        return isEqual(stateA[prop], stateB[prop]);
      });
    }
    function applyState(state, target, children2) {
      var selector = getSelector(state);
      children2.forEach(function(el) {
        return css(el, "display", selector && !matches(el, selector) ? "none" : "");
      });
      var ref2 = state.sort;
      var sort = ref2[0];
      var order = ref2[1];
      if (sort) {
        var sorted = sortItems(children2, sort, order);
        if (!isEqual(sorted, children2)) {
          append(target, sorted);
        }
      }
    }
    function mergeState(el, attr2, state) {
      var filterBy = getFilter(el, attr2);
      var filter2 = filterBy.filter;
      var group = filterBy.group;
      var sort = filterBy.sort;
      var order = filterBy.order;
      if (order === void 0)
        order = "asc";
      if (filter2 || isUndefined(sort)) {
        if (group) {
          if (filter2) {
            delete state.filter[""];
            state.filter[group] = filter2;
          } else {
            delete state.filter[group];
            if (isEmpty(state.filter) || "" in state.filter) {
              state.filter = { "": filter2 || "" };
            }
          }
        } else {
          state.filter = { "": filter2 || "" };
        }
      }
      if (!isUndefined(sort)) {
        state.sort = [sort, order];
      }
      return state;
    }
    function matchFilter(el, attr2, ref2) {
      var stateFilter = ref2.filter;
      if (stateFilter === void 0)
        stateFilter = { "": "" };
      var ref_sort = ref2.sort;
      var stateSort = ref_sort[0];
      var stateOrder = ref_sort[1];
      var ref$1 = getFilter(el, attr2);
      var filter2 = ref$1.filter;
      if (filter2 === void 0)
        filter2 = "";
      var group = ref$1.group;
      if (group === void 0)
        group = "";
      var sort = ref$1.sort;
      var order = ref$1.order;
      if (order === void 0)
        order = "asc";
      return isUndefined(sort) ? group in stateFilter && filter2 === stateFilter[group] || !filter2 && group && !(group in stateFilter) && !stateFilter[""] : stateSort === sort && stateOrder === order;
    }
    function isEqualList(listA, listB) {
      return listA.length === listB.length && listA.every(function(el) {
        return ~listB.indexOf(el);
      });
    }
    function getSelector(ref2) {
      var filter2 = ref2.filter;
      var selector = "";
      each(filter2, function(value) {
        return selector += value || "";
      });
      return selector;
    }
    function sortItems(nodes, sort, order) {
      return assign([], nodes).sort(function(a, b) {
        return data(a, sort).localeCompare(data(b, sort), void 0, { numeric: true }) * (order === "asc" || -1);
      });
    }
    var Animations$2 = {
      slide: {
        show: function(dir) {
          return [
            { transform: translate(dir * -100) },
            { transform: translate() }
          ];
        },
        percent: function(current) {
          return translated(current);
        },
        translate: function(percent2, dir) {
          return [
            { transform: translate(dir * -100 * percent2) },
            { transform: translate(dir * 100 * (1 - percent2)) }
          ];
        }
      }
    };
    function translated(el) {
      return Math.abs(css(el, "transform").split(",")[4] / el.offsetWidth) || 0;
    }
    function translate(value, unit) {
      if (value === void 0)
        value = 0;
      if (unit === void 0)
        unit = "%";
      value += value ? unit : "";
      return isIE ? "translateX(" + value + ")" : "translate3d(" + value + ", 0, 0)";
    }
    function scale3d(value) {
      return "scale3d(" + value + ", " + value + ", 1)";
    }
    var Animations$1 = assign({}, Animations$2, {
      fade: {
        show: function() {
          return [
            { opacity: 0 },
            { opacity: 1 }
          ];
        },
        percent: function(current) {
          return 1 - css(current, "opacity");
        },
        translate: function(percent2) {
          return [
            { opacity: 1 - percent2 },
            { opacity: percent2 }
          ];
        }
      },
      scale: {
        show: function() {
          return [
            { opacity: 0, transform: scale3d(1 - 0.2) },
            { opacity: 1, transform: scale3d(1) }
          ];
        },
        percent: function(current) {
          return 1 - css(current, "opacity");
        },
        translate: function(percent2) {
          return [
            { opacity: 1 - percent2, transform: scale3d(1 - 0.2 * percent2) },
            { opacity: percent2, transform: scale3d(1 - 0.2 + 0.2 * percent2) }
          ];
        }
      }
    });
    function Transitioner$1(prev, next, dir, ref2) {
      var animation = ref2.animation;
      var easing = ref2.easing;
      var percent2 = animation.percent;
      var translate2 = animation.translate;
      var show = animation.show;
      if (show === void 0)
        show = noop;
      var props2 = show(dir);
      var deferred = new Deferred();
      return {
        dir,
        show: function(duration, percent3, linear) {
          var this$1$1 = this;
          if (percent3 === void 0)
            percent3 = 0;
          var timing = linear ? "linear" : easing;
          duration -= Math.round(duration * clamp(percent3, -1, 1));
          this.translate(percent3);
          triggerUpdate$1(next, "itemin", { percent: percent3, duration, timing, dir });
          triggerUpdate$1(prev, "itemout", { percent: 1 - percent3, duration, timing, dir });
          Promise$1.all([
            Transition.start(next, props2[1], duration, timing),
            Transition.start(prev, props2[0], duration, timing)
          ]).then(function() {
            this$1$1.reset();
            deferred.resolve();
          }, noop);
          return deferred.promise;
        },
        cancel: function() {
          Transition.cancel([next, prev]);
        },
        reset: function() {
          for (var prop in props2[0]) {
            css([next, prev], prop, "");
          }
        },
        forward: function(duration, percent3) {
          if (percent3 === void 0)
            percent3 = this.percent();
          Transition.cancel([next, prev]);
          return this.show(duration, percent3, true);
        },
        translate: function(percent3) {
          this.reset();
          var props3 = translate2(percent3, dir);
          css(next, props3[1]);
          css(prev, props3[0]);
          triggerUpdate$1(next, "itemtranslatein", { percent: percent3, dir });
          triggerUpdate$1(prev, "itemtranslateout", { percent: 1 - percent3, dir });
        },
        percent: function() {
          return percent2(prev || next, next, dir);
        },
        getDistance: function() {
          return prev && prev.offsetWidth;
        }
      };
    }
    function triggerUpdate$1(el, type, data2) {
      trigger(el, createEvent(type, false, false, data2));
    }
    var SliderAutoplay = {
      props: {
        autoplay: Boolean,
        autoplayInterval: Number,
        pauseOnHover: Boolean
      },
      data: {
        autoplay: false,
        autoplayInterval: 7e3,
        pauseOnHover: true
      },
      connected: function() {
        this.autoplay && this.startAutoplay();
      },
      disconnected: function() {
        this.stopAutoplay();
      },
      update: function() {
        attr(this.slides, "tabindex", "-1");
      },
      events: [
        {
          name: "visibilitychange",
          el: function() {
            return document;
          },
          filter: function() {
            return this.autoplay;
          },
          handler: function() {
            if (document.hidden) {
              this.stopAutoplay();
            } else {
              this.startAutoplay();
            }
          }
        }
      ],
      methods: {
        startAutoplay: function() {
          var this$1$1 = this;
          this.stopAutoplay();
          this.interval = setInterval(function() {
            return (!this$1$1.draggable || !$(":focus", this$1$1.$el)) && (!this$1$1.pauseOnHover || !matches(this$1$1.$el, ":hover")) && !this$1$1.stack.length && this$1$1.show("next");
          }, this.autoplayInterval);
        },
        stopAutoplay: function() {
          this.interval && clearInterval(this.interval);
        }
      }
    };
    var SliderDrag = {
      props: {
        draggable: Boolean
      },
      data: {
        draggable: true,
        threshold: 10
      },
      created: function() {
        var this$1$1 = this;
        ["start", "move", "end"].forEach(function(key2) {
          var fn = this$1$1[key2];
          this$1$1[key2] = function(e) {
            var pos = getEventPos(e).x * (isRtl ? -1 : 1);
            this$1$1.prevPos = pos !== this$1$1.pos ? this$1$1.pos : this$1$1.prevPos;
            this$1$1.pos = pos;
            fn(e);
          };
        });
      },
      events: [
        {
          name: pointerDown,
          delegate: function() {
            return this.selSlides;
          },
          handler: function(e) {
            if (!this.draggable || !isTouch(e) && hasTextNodesOnly(e.target) || closest(e.target, selInput) || e.button > 0 || this.length < 2) {
              return;
            }
            this.start(e);
          }
        },
        {
          name: "dragstart",
          handler: function(e) {
            e.preventDefault();
          }
        }
      ],
      methods: {
        start: function() {
          this.drag = this.pos;
          if (this._transitioner) {
            this.percent = this._transitioner.percent();
            this.drag += this._transitioner.getDistance() * this.percent * this.dir;
            this._transitioner.cancel();
            this._transitioner.translate(this.percent);
            this.dragging = true;
            this.stack = [];
          } else {
            this.prevIndex = this.index;
          }
          on(document, pointerMove, this.move, { passive: false });
          on(document, pointerUp + " " + pointerCancel + " input", this.end, true);
          css(this.list, "userSelect", "none");
        },
        move: function(e) {
          var this$1$1 = this;
          var distance = this.pos - this.drag;
          if (distance === 0 || this.prevPos === this.pos || !this.dragging && Math.abs(distance) < this.threshold) {
            return;
          }
          css(this.list, "pointerEvents", "none");
          e.cancelable && e.preventDefault();
          this.dragging = true;
          this.dir = distance < 0 ? 1 : -1;
          var ref2 = this;
          var slides = ref2.slides;
          var ref$1 = this;
          var prevIndex = ref$1.prevIndex;
          var dis = Math.abs(distance);
          var nextIndex = this.getIndex(prevIndex + this.dir, prevIndex);
          var width2 = this._getDistance(prevIndex, nextIndex) || slides[prevIndex].offsetWidth;
          while (nextIndex !== prevIndex && dis > width2) {
            this.drag -= width2 * this.dir;
            prevIndex = nextIndex;
            dis -= width2;
            nextIndex = this.getIndex(prevIndex + this.dir, prevIndex);
            width2 = this._getDistance(prevIndex, nextIndex) || slides[prevIndex].offsetWidth;
          }
          this.percent = dis / width2;
          var prev = slides[prevIndex];
          var next = slides[nextIndex];
          var changed = this.index !== nextIndex;
          var edge = prevIndex === nextIndex;
          var itemShown;
          [this.index, this.prevIndex].filter(function(i) {
            return !includes([nextIndex, prevIndex], i);
          }).forEach(function(i) {
            trigger(slides[i], "itemhidden", [this$1$1]);
            if (edge) {
              itemShown = true;
              this$1$1.prevIndex = prevIndex;
            }
          });
          if (this.index === prevIndex && this.prevIndex !== prevIndex || itemShown) {
            trigger(slides[this.index], "itemshown", [this]);
          }
          if (changed) {
            this.prevIndex = prevIndex;
            this.index = nextIndex;
            !edge && trigger(prev, "beforeitemhide", [this]);
            trigger(next, "beforeitemshow", [this]);
          }
          this._transitioner = this._translate(Math.abs(this.percent), prev, !edge && next);
          if (changed) {
            !edge && trigger(prev, "itemhide", [this]);
            trigger(next, "itemshow", [this]);
          }
        },
        end: function() {
          off(document, pointerMove, this.move, { passive: false });
          off(document, pointerUp + " " + pointerCancel + " input", this.end, true);
          if (this.dragging) {
            this.dragging = null;
            if (this.index === this.prevIndex) {
              this.percent = 1 - this.percent;
              this.dir *= -1;
              this._show(false, this.index, true);
              this._transitioner = null;
            } else {
              var dirChange = (isRtl ? this.dir * (isRtl ? 1 : -1) : this.dir) < 0 === this.prevPos > this.pos;
              this.index = dirChange ? this.index : this.prevIndex;
              if (dirChange) {
                this.percent = 1 - this.percent;
              }
              this.show(this.dir > 0 && !dirChange || this.dir < 0 && dirChange ? "next" : "previous", true);
            }
          }
          css(this.list, { userSelect: "", pointerEvents: "" });
          this.drag = this.percent = null;
        }
      }
    };
    function hasTextNodesOnly(el) {
      return !el.children.length && el.childNodes.length;
    }
    var SliderNav = {
      data: {
        selNav: false
      },
      computed: {
        nav: function(ref2, $el) {
          var selNav = ref2.selNav;
          return $(selNav, $el);
        },
        selNavItem: function(ref2) {
          var attrItem = ref2.attrItem;
          return "[" + attrItem + "],[data-" + attrItem + "]";
        },
        navItems: function(_2, $el) {
          return $$(this.selNavItem, $el);
        }
      },
      update: {
        write: function() {
          var this$1$1 = this;
          if (this.nav && this.length !== this.nav.children.length) {
            html(this.nav, this.slides.map(function(_2, i) {
              return "<li " + this$1$1.attrItem + '="' + i + '"><a href></a></li>';
            }).join(""));
          }
          this.navItems.concat(this.nav).forEach(function(el) {
            return el && (el.hidden = !this$1$1.maxIndex);
          });
          this.updateNav();
        },
        events: ["resize"]
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.selNavItem;
          },
          handler: function(e) {
            e.preventDefault();
            this.show(data(e.current, this.attrItem));
          }
        },
        {
          name: "itemshow",
          handler: "updateNav"
        }
      ],
      methods: {
        updateNav: function() {
          var this$1$1 = this;
          var i = this.getValidIndex();
          this.navItems.forEach(function(el) {
            var cmd = data(el, this$1$1.attrItem);
            toggleClass(el, this$1$1.clsActive, toNumber(cmd) === i);
            toggleClass(el, "uk-invisible", this$1$1.finite && (cmd === "previous" && i === 0 || cmd === "next" && i >= this$1$1.maxIndex));
          });
        }
      }
    };
    var Slider = {
      mixins: [SliderAutoplay, SliderDrag, SliderNav],
      props: {
        clsActivated: Boolean,
        easing: String,
        index: Number,
        finite: Boolean,
        velocity: Number,
        selSlides: String
      },
      data: function() {
        return {
          easing: "ease",
          finite: false,
          velocity: 1,
          index: 0,
          prevIndex: -1,
          stack: [],
          percent: 0,
          clsActive: "uk-active",
          clsActivated: false,
          Transitioner: false,
          transitionOptions: {}
        };
      },
      connected: function() {
        this.prevIndex = -1;
        this.index = this.getValidIndex(this.$props.index);
        this.stack = [];
      },
      disconnected: function() {
        removeClass(this.slides, this.clsActive);
      },
      computed: {
        duration: function(ref2, $el) {
          var velocity = ref2.velocity;
          return speedUp($el.offsetWidth / velocity);
        },
        list: function(ref2, $el) {
          var selList = ref2.selList;
          return $(selList, $el);
        },
        maxIndex: function() {
          return this.length - 1;
        },
        selSlides: function(ref2) {
          var selList = ref2.selList;
          var selSlides = ref2.selSlides;
          return selList + " " + (selSlides || "> *");
        },
        slides: {
          get: function() {
            return $$(this.selSlides, this.$el);
          },
          watch: function() {
            this.$reset();
          }
        },
        length: function() {
          return this.slides.length;
        }
      },
      events: {
        itemshown: function() {
          this.$update(this.list);
        }
      },
      methods: {
        show: function(index2, force) {
          var this$1$1 = this;
          if (force === void 0)
            force = false;
          if (this.dragging || !this.length) {
            return;
          }
          var ref2 = this;
          var stack = ref2.stack;
          var queueIndex = force ? 0 : stack.length;
          var reset2 = function() {
            stack.splice(queueIndex, 1);
            if (stack.length) {
              this$1$1.show(stack.shift(), true);
            }
          };
          stack[force ? "unshift" : "push"](index2);
          if (!force && stack.length > 1) {
            if (stack.length === 2) {
              this._transitioner.forward(Math.min(this.duration, 200));
            }
            return;
          }
          var prevIndex = this.getIndex(this.index);
          var prev = hasClass(this.slides, this.clsActive) && this.slides[prevIndex];
          var nextIndex = this.getIndex(index2, this.index);
          var next = this.slides[nextIndex];
          if (prev === next) {
            reset2();
            return;
          }
          this.dir = getDirection(index2, prevIndex);
          this.prevIndex = prevIndex;
          this.index = nextIndex;
          if (prev && !trigger(prev, "beforeitemhide", [this]) || !trigger(next, "beforeitemshow", [this, prev])) {
            this.index = this.prevIndex;
            reset2();
            return;
          }
          var promise = this._show(prev, next, force).then(function() {
            prev && trigger(prev, "itemhidden", [this$1$1]);
            trigger(next, "itemshown", [this$1$1]);
            return new Promise$1(function(resolve) {
              fastdom.write(function() {
                stack.shift();
                if (stack.length) {
                  this$1$1.show(stack.shift(), true);
                } else {
                  this$1$1._transitioner = null;
                }
                resolve();
              });
            });
          });
          prev && trigger(prev, "itemhide", [this]);
          trigger(next, "itemshow", [this]);
          return promise;
        },
        getIndex: function(index2, prev) {
          if (index2 === void 0)
            index2 = this.index;
          if (prev === void 0)
            prev = this.index;
          return clamp(getIndex(index2, this.slides, prev, this.finite), 0, this.maxIndex);
        },
        getValidIndex: function(index2, prevIndex) {
          if (index2 === void 0)
            index2 = this.index;
          if (prevIndex === void 0)
            prevIndex = this.prevIndex;
          return this.getIndex(index2, prevIndex);
        },
        _show: function(prev, next, force) {
          this._transitioner = this._getTransitioner(prev, next, this.dir, assign({
            easing: force ? next.offsetWidth < 600 ? "cubic-bezier(0.25, 0.46, 0.45, 0.94)" : "cubic-bezier(0.165, 0.84, 0.44, 1)" : this.easing
          }, this.transitionOptions));
          if (!force && !prev) {
            this._translate(1);
            return Promise$1.resolve();
          }
          var ref2 = this.stack;
          var length = ref2.length;
          return this._transitioner[length > 1 ? "forward" : "show"](length > 1 ? Math.min(this.duration, 75 + 75 / (length - 1)) : this.duration, this.percent);
        },
        _getDistance: function(prev, next) {
          return this._getTransitioner(prev, prev !== next && next).getDistance();
        },
        _translate: function(percent2, prev, next) {
          if (prev === void 0)
            prev = this.prevIndex;
          if (next === void 0)
            next = this.index;
          var transitioner = this._getTransitioner(prev !== next ? prev : false, next);
          transitioner.translate(percent2);
          return transitioner;
        },
        _getTransitioner: function(prev, next, dir, options) {
          if (prev === void 0)
            prev = this.prevIndex;
          if (next === void 0)
            next = this.index;
          if (dir === void 0)
            dir = this.dir || 1;
          if (options === void 0)
            options = this.transitionOptions;
          return new this.Transitioner(isNumber(prev) ? this.slides[prev] : prev, isNumber(next) ? this.slides[next] : next, dir * (isRtl ? -1 : 1), options);
        }
      }
    };
    function getDirection(index2, prevIndex) {
      return index2 === "next" ? 1 : index2 === "previous" ? -1 : index2 < prevIndex ? -1 : 1;
    }
    function speedUp(x) {
      return 0.5 * x + 300;
    }
    var Slideshow = {
      mixins: [Slider],
      props: {
        animation: String
      },
      data: {
        animation: "slide",
        clsActivated: "uk-transition-active",
        Animations: Animations$2,
        Transitioner: Transitioner$1
      },
      computed: {
        animation: function(ref2) {
          var animation = ref2.animation;
          var Animations2 = ref2.Animations;
          return assign(Animations2[animation] || Animations2.slide, { name: animation });
        },
        transitionOptions: function() {
          return { animation: this.animation };
        }
      },
      events: {
        "itemshow itemhide itemshown itemhidden": function(ref2) {
          var target = ref2.target;
          this.$update(target);
        },
        beforeitemshow: function(ref2) {
          var target = ref2.target;
          addClass(target, this.clsActive);
        },
        itemshown: function(ref2) {
          var target = ref2.target;
          addClass(target, this.clsActivated);
        },
        itemhidden: function(ref2) {
          var target = ref2.target;
          removeClass(target, this.clsActive, this.clsActivated);
        }
      }
    };
    var LightboxPanel = {
      mixins: [Container, Modal, Togglable, Slideshow],
      functional: true,
      props: {
        delayControls: Number,
        preload: Number,
        videoAutoplay: Boolean,
        template: String
      },
      data: function() {
        return {
          preload: 1,
          videoAutoplay: false,
          delayControls: 3e3,
          items: [],
          cls: "uk-open",
          clsPage: "uk-lightbox-page",
          selList: ".uk-lightbox-items",
          attrItem: "uk-lightbox-item",
          selClose: ".uk-close-large",
          selCaption: ".uk-lightbox-caption",
          pauseOnHover: false,
          velocity: 2,
          Animations: Animations$1,
          template: '<div class="uk-lightbox uk-overflow-hidden"> <ul class="uk-lightbox-items"></ul> <div class="uk-lightbox-toolbar uk-position-top uk-text-right uk-transition-slide-top uk-transition-opaque"> <button class="uk-lightbox-toolbar-icon uk-close-large" type="button" uk-close></button> </div> <a class="uk-lightbox-button uk-position-center-left uk-position-medium uk-transition-fade" href uk-slidenav-previous uk-lightbox-item="previous"></a> <a class="uk-lightbox-button uk-position-center-right uk-position-medium uk-transition-fade" href uk-slidenav-next uk-lightbox-item="next"></a> <div class="uk-lightbox-toolbar uk-lightbox-caption uk-position-bottom uk-text-center uk-transition-slide-bottom uk-transition-opaque"></div> </div>'
        };
      },
      created: function() {
        var $el = $(this.template);
        var list = $(this.selList, $el);
        this.items.forEach(function() {
          return append(list, "<li>");
        });
        this.$mount(append(this.container, $el));
      },
      computed: {
        caption: function(ref2, $el) {
          var selCaption = ref2.selCaption;
          return $(selCaption, $el);
        }
      },
      events: [
        {
          name: pointerMove + " " + pointerDown + " keydown",
          handler: "showControls"
        },
        {
          name: "click",
          self: true,
          delegate: function() {
            return this.selSlides;
          },
          handler: function(e) {
            if (e.defaultPrevented) {
              return;
            }
            this.hide();
          }
        },
        {
          name: "shown",
          self: true,
          handler: function() {
            this.showControls();
          }
        },
        {
          name: "hide",
          self: true,
          handler: function() {
            this.hideControls();
            removeClass(this.slides, this.clsActive);
            Transition.stop(this.slides);
          }
        },
        {
          name: "hidden",
          self: true,
          handler: function() {
            this.$destroy(true);
          }
        },
        {
          name: "keyup",
          el: function() {
            return document;
          },
          handler: function(e) {
            if (!this.isToggled(this.$el) || !this.draggable) {
              return;
            }
            switch (e.keyCode) {
              case 37:
                this.show("previous");
                break;
              case 39:
                this.show("next");
                break;
            }
          }
        },
        {
          name: "beforeitemshow",
          handler: function(e) {
            if (this.isToggled()) {
              return;
            }
            this.draggable = false;
            e.preventDefault();
            this.toggleElement(this.$el, true, false);
            this.animation = Animations$1["scale"];
            removeClass(e.target, this.clsActive);
            this.stack.splice(1, 0, this.index);
          }
        },
        {
          name: "itemshow",
          handler: function() {
            html(this.caption, this.getItem().caption || "");
            for (var j = -this.preload; j <= this.preload; j++) {
              this.loadItem(this.index + j);
            }
          }
        },
        {
          name: "itemshown",
          handler: function() {
            this.draggable = this.$props.draggable;
          }
        },
        {
          name: "itemload",
          handler: function(_2, item) {
            var this$1$1 = this;
            var src = item.source;
            var type = item.type;
            var alt = item.alt;
            if (alt === void 0)
              alt = "";
            var poster = item.poster;
            var attrs = item.attrs;
            if (attrs === void 0)
              attrs = {};
            this.setItem(item, "<span uk-spinner></span>");
            if (!src) {
              return;
            }
            var matches2;
            var iframeAttrs = {
              frameborder: "0",
              allow: "autoplay",
              allowfullscreen: "",
              style: "max-width: 100%; box-sizing: border-box;",
              "uk-responsive": "",
              "uk-video": "" + this.videoAutoplay
            };
            if (type === "image" || src.match(/\.(avif|jpe?g|a?png|gif|svg|webp)($|\?)/i)) {
              getImage(src, attrs.srcset, attrs.size).then(function(ref2) {
                var width2 = ref2.width;
                var height2 = ref2.height;
                return this$1$1.setItem(item, createEl("img", assign({ src, width: width2, height: height2, alt }, attrs)));
              }, function() {
                return this$1$1.setError(item);
              });
            } else if (type === "video" || src.match(/\.(mp4|webm|ogv)($|\?)/i)) {
              var video = createEl("video", assign({
                src,
                poster,
                controls: "",
                playsinline: "",
                "uk-video": "" + this.videoAutoplay
              }, attrs));
              on(video, "loadedmetadata", function() {
                attr(video, { width: video.videoWidth, height: video.videoHeight });
                this$1$1.setItem(item, video);
              });
              on(video, "error", function() {
                return this$1$1.setError(item);
              });
            } else if (type === "iframe" || src.match(/\.(html|php)($|\?)/i)) {
              this.setItem(item, createEl("iframe", assign({
                src,
                frameborder: "0",
                allowfullscreen: "",
                class: "uk-lightbox-iframe"
              }, attrs)));
            } else if (matches2 = src.match(/\/\/(?:.*?youtube(-nocookie)?\..*?[?&]v=|youtu\.be\/)([\w-]{11})[&?]?(.*)?/)) {
              this.setItem(item, createEl("iframe", assign({
                src: "https://www.youtube" + (matches2[1] || "") + ".com/embed/" + matches2[2] + (matches2[3] ? "?" + matches2[3] : ""),
                width: 1920,
                height: 1080
              }, iframeAttrs, attrs)));
            } else if (matches2 = src.match(/\/\/.*?vimeo\.[a-z]+\/(\d+)[&?]?(.*)?/)) {
              ajax("https://vimeo.com/api/oembed.json?maxwidth=1920&url=" + encodeURI(src), {
                responseType: "json",
                withCredentials: false
              }).then(function(ref2) {
                var ref_response = ref2.response;
                var height2 = ref_response.height;
                var width2 = ref_response.width;
                return this$1$1.setItem(item, createEl("iframe", assign({
                  src: "https://player.vimeo.com/video/" + matches2[1] + (matches2[2] ? "?" + matches2[2] : ""),
                  width: width2,
                  height: height2
                }, iframeAttrs, attrs)));
              }, function() {
                return this$1$1.setError(item);
              });
            }
          }
        }
      ],
      methods: {
        loadItem: function(index2) {
          if (index2 === void 0)
            index2 = this.index;
          var item = this.getItem(index2);
          if (!this.getSlide(item).childElementCount) {
            trigger(this.$el, "itemload", [item]);
          }
        },
        getItem: function(index2) {
          if (index2 === void 0)
            index2 = this.index;
          return this.items[getIndex(index2, this.slides)];
        },
        setItem: function(item, content) {
          trigger(this.$el, "itemloaded", [this, html(this.getSlide(item), content)]);
        },
        getSlide: function(item) {
          return this.slides[this.items.indexOf(item)];
        },
        setError: function(item) {
          this.setItem(item, '<span uk-icon="icon: bolt; ratio: 2"></span>');
        },
        showControls: function() {
          clearTimeout(this.controlsTimer);
          this.controlsTimer = setTimeout(this.hideControls, this.delayControls);
          addClass(this.$el, "uk-active", "uk-transition-active");
        },
        hideControls: function() {
          removeClass(this.$el, "uk-active", "uk-transition-active");
        }
      }
    };
    function createEl(tag, attrs) {
      var el = fragment("<" + tag + ">");
      attr(el, attrs);
      return el;
    }
    var lightbox = {
      install: install$1,
      props: { toggle: String },
      data: { toggle: "a" },
      computed: {
        toggles: {
          get: function(ref2, $el) {
            var toggle2 = ref2.toggle;
            return $$(toggle2, $el);
          },
          watch: function() {
            this.hide();
          }
        }
      },
      disconnected: function() {
        this.hide();
      },
      events: [
        {
          name: "click",
          delegate: function() {
            return this.toggle + ":not(.uk-disabled)";
          },
          handler: function(e) {
            e.preventDefault();
            this.show(e.current);
          }
        }
      ],
      methods: {
        show: function(index2) {
          var this$1$1 = this;
          var items = uniqueBy(this.toggles.map(toItem), "source");
          if (isElement(index2)) {
            var ref2 = toItem(index2);
            var source = ref2.source;
            index2 = findIndex(items, function(ref3) {
              var src = ref3.source;
              return source === src;
            });
          }
          this.panel = this.panel || this.$create("lightboxPanel", assign({}, this.$props, { items }));
          on(this.panel.$el, "hidden", function() {
            return this$1$1.panel = false;
          });
          return this.panel.show(index2);
        },
        hide: function() {
          return this.panel && this.panel.hide();
        }
      }
    };
    function install$1(UIkit3, Lightbox) {
      if (!UIkit3.lightboxPanel) {
        UIkit3.component("lightboxPanel", LightboxPanel);
      }
      assign(Lightbox.props, UIkit3.component("lightboxPanel").options.props);
    }
    function toItem(el) {
      var item = {};
      ["href", "caption", "type", "poster", "alt", "attrs"].forEach(function(attr2) {
        item[attr2 === "href" ? "source" : attr2] = data(el, attr2);
      });
      item.attrs = parseOptions(item.attrs);
      return item;
    }
    var obj$1;
    var notification = {
      mixins: [Container],
      functional: true,
      args: ["message", "status"],
      data: {
        message: "",
        status: "",
        timeout: 5e3,
        group: null,
        pos: "top-center",
        clsContainer: "uk-notification",
        clsClose: "uk-notification-close",
        clsMsg: "uk-notification-message"
      },
      install,
      computed: {
        marginProp: function(ref2) {
          var pos = ref2.pos;
          return "margin" + (startsWith(pos, "top") ? "Top" : "Bottom");
        },
        startProps: function() {
          var obj2;
          return obj2 = { opacity: 0 }, obj2[this.marginProp] = -this.$el.offsetHeight, obj2;
        }
      },
      created: function() {
        var container = $("." + this.clsContainer + "-" + this.pos, this.container) || append(this.container, '<div class="' + this.clsContainer + " " + this.clsContainer + "-" + this.pos + '" style="display: block"></div>');
        this.$mount(append(container, '<div class="' + this.clsMsg + (this.status ? " " + this.clsMsg + "-" + this.status : "") + '"> <a href class="' + this.clsClose + '" data-uk-close></a> <div>' + this.message + "</div> </div>"));
      },
      connected: function() {
        var this$1$1 = this;
        var obj2;
        var margin = toFloat(css(this.$el, this.marginProp));
        Transition.start(css(this.$el, this.startProps), (obj2 = { opacity: 1 }, obj2[this.marginProp] = margin, obj2)).then(function() {
          if (this$1$1.timeout) {
            this$1$1.timer = setTimeout(this$1$1.close, this$1$1.timeout);
          }
        });
      },
      events: (obj$1 = {
        click: function(e) {
          if (closest(e.target, 'a[href="#"],a[href=""]')) {
            e.preventDefault();
          }
          this.close();
        }
      }, obj$1[pointerEnter] = function() {
        if (this.timer) {
          clearTimeout(this.timer);
        }
      }, obj$1[pointerLeave] = function() {
        if (this.timeout) {
          this.timer = setTimeout(this.close, this.timeout);
        }
      }, obj$1),
      methods: {
        close: function(immediate) {
          var this$1$1 = this;
          var removeFn = function(el) {
            var container = parent(el);
            trigger(el, "close", [this$1$1]);
            remove$1(el);
            if (container && !container.hasChildNodes()) {
              remove$1(container);
            }
          };
          if (this.timer) {
            clearTimeout(this.timer);
          }
          if (immediate) {
            removeFn(this.$el);
          } else {
            Transition.start(this.$el, this.startProps).then(removeFn);
          }
        }
      }
    };
    function install(UIkit3) {
      UIkit3.notification.closeAll = function(group, immediate) {
        apply$1(document.body, function(el) {
          var notification2 = UIkit3.getComponent(el, "notification");
          if (notification2 && (!group || group === notification2.group)) {
            notification2.close(immediate);
          }
        });
      };
    }
    var props = ["x", "y", "bgx", "bgy", "rotate", "scale", "color", "backgroundColor", "borderColor", "opacity", "blur", "hue", "grayscale", "invert", "saturate", "sepia", "fopacity", "stroke"];
    var Parallax = {
      mixins: [Media],
      props: props.reduce(function(props2, prop) {
        props2[prop] = "list";
        return props2;
      }, {}),
      data: props.reduce(function(data2, prop) {
        data2[prop] = void 0;
        return data2;
      }, {}),
      computed: {
        props: function(properties, $el) {
          var this$1$1 = this;
          return props.reduce(function(props2, prop) {
            if (isUndefined(properties[prop])) {
              return props2;
            }
            var isColor = prop.match(/color/i);
            var isCssProp = isColor || prop === "opacity";
            var pos, bgPos, diff;
            var steps = properties[prop].slice();
            if (isCssProp) {
              css($el, prop, "");
            }
            if (steps.length < 2) {
              steps.unshift((prop === "scale" ? 1 : isCssProp ? css($el, prop) : 0) || 0);
            }
            var unit = getUnit(steps);
            if (isColor) {
              var ref2 = $el.style;
              var color = ref2.color;
              steps = steps.map(function(step) {
                return parseColor($el, step);
              });
              $el.style.color = color;
            } else if (startsWith(prop, "bg")) {
              var attr2 = prop === "bgy" ? "height" : "width";
              steps = steps.map(function(step) {
                return toPx(step, attr2, this$1$1.$el);
              });
              css($el, "background-position-" + prop[2], "");
              bgPos = css($el, "backgroundPosition").split(" ")[prop[2] === "x" ? 0 : 1];
              if (this$1$1.covers) {
                var min = Math.min.apply(Math, steps);
                var max = Math.max.apply(Math, steps);
                var down = steps.indexOf(min) < steps.indexOf(max);
                diff = max - min;
                steps = steps.map(function(step) {
                  return step - (down ? min : max);
                });
                pos = (down ? -diff : 0) + "px";
              } else {
                pos = bgPos;
              }
            } else {
              steps = steps.map(toFloat);
            }
            if (prop === "stroke") {
              if (!steps.some(function(step) {
                return step;
              })) {
                return props2;
              }
              var length = getMaxPathLength(this$1$1.$el);
              css($el, "strokeDasharray", length);
              if (unit === "%") {
                steps = steps.map(function(step) {
                  return step * length / 100;
                });
              }
              steps = steps.reverse();
              prop = "strokeDashoffset";
            }
            props2[prop] = { steps, unit, pos, bgPos, diff };
            return props2;
          }, {});
        },
        bgProps: function() {
          var this$1$1 = this;
          return ["bgx", "bgy"].filter(function(bg) {
            return bg in this$1$1.props;
          });
        },
        covers: function(_2, $el) {
          return covers($el);
        }
      },
      disconnected: function() {
        delete this._image;
      },
      update: {
        read: function(data2) {
          var this$1$1 = this;
          if (!this.matchMedia) {
            return;
          }
          if (!data2.image && this.covers && this.bgProps.length) {
            var src = css(this.$el, "backgroundImage").replace(/^none|url\(["']?(.+?)["']?\)$/, "$1");
            if (src) {
              var img2 = new Image();
              img2.src = src;
              data2.image = img2;
              if (!img2.naturalWidth) {
                img2.onload = function() {
                  return this$1$1.$update();
                };
              }
            }
          }
          var image = data2.image;
          if (!image || !image.naturalWidth) {
            return;
          }
          var dimEl = {
            width: this.$el.offsetWidth,
            height: this.$el.offsetHeight
          };
          var dimImage = {
            width: image.naturalWidth,
            height: image.naturalHeight
          };
          var dim = Dimensions.cover(dimImage, dimEl);
          this.bgProps.forEach(function(prop) {
            var ref2 = this$1$1.props[prop];
            var diff = ref2.diff;
            var bgPos = ref2.bgPos;
            var steps = ref2.steps;
            var attr2 = prop === "bgy" ? "height" : "width";
            var span = dim[attr2] - dimEl[attr2];
            if (span < diff) {
              dimEl[attr2] = dim[attr2] + diff - span;
            } else if (span > diff) {
              var posPercentage = dimEl[attr2] / toPx(bgPos, attr2, this$1$1.$el);
              if (posPercentage) {
                this$1$1.props[prop].steps = steps.map(function(step) {
                  return step - (span - diff) / posPercentage;
                });
              }
            }
            dim = Dimensions.cover(dimImage, dimEl);
          });
          data2.dim = dim;
        },
        write: function(ref2) {
          var dim = ref2.dim;
          if (!this.matchMedia) {
            css(this.$el, { backgroundSize: "", backgroundRepeat: "" });
            return;
          }
          dim && css(this.$el, {
            backgroundSize: dim.width + "px " + dim.height + "px",
            backgroundRepeat: "no-repeat"
          });
        },
        events: ["resize"]
      },
      methods: {
        reset: function() {
          var this$1$1 = this;
          each(this.getCss(0), function(_2, prop) {
            return css(this$1$1.$el, prop, "");
          });
        },
        getCss: function(percent2) {
          var ref2 = this;
          var props2 = ref2.props;
          return Object.keys(props2).reduce(function(css2, prop) {
            var ref3 = props2[prop];
            var steps = ref3.steps;
            var unit = ref3.unit;
            var pos = ref3.pos;
            var value = getValue(steps, percent2);
            switch (prop) {
              case "x":
              case "y": {
                unit = unit || "px";
                css2.transform += " translate" + ucfirst(prop) + "(" + toFloat(value).toFixed(unit === "px" ? 0 : 2) + unit + ")";
                break;
              }
              case "rotate":
                unit = unit || "deg";
                css2.transform += " rotate(" + (value + unit) + ")";
                break;
              case "scale":
                css2.transform += " scale(" + value + ")";
                break;
              case "bgy":
              case "bgx":
                css2["background-position-" + prop[2]] = "calc(" + pos + " + " + value + "px)";
                break;
              case "color":
              case "backgroundColor":
              case "borderColor": {
                var ref$1 = getStep(steps, percent2);
                var start = ref$1[0];
                var end = ref$1[1];
                var p2 = ref$1[2];
                css2[prop] = "rgba(" + start.map(function(value2, i) {
                  value2 += p2 * (end[i] - value2);
                  return i === 3 ? toFloat(value2) : parseInt(value2, 10);
                }).join(",") + ")";
                break;
              }
              case "blur":
                unit = unit || "px";
                css2.filter += " blur(" + (value + unit) + ")";
                break;
              case "hue":
                unit = unit || "deg";
                css2.filter += " hue-rotate(" + (value + unit) + ")";
                break;
              case "fopacity":
                unit = unit || "%";
                css2.filter += " opacity(" + (value + unit) + ")";
                break;
              case "grayscale":
              case "invert":
              case "saturate":
              case "sepia":
                unit = unit || "%";
                css2.filter += " " + prop + "(" + (value + unit) + ")";
                break;
              default:
                css2[prop] = value;
            }
            return css2;
          }, { transform: "", filter: "" });
        }
      }
    };
    function parseColor(el, color) {
      return css(css(el, "color", color), "color").split(/[(),]/g).slice(1, -1).concat(1).slice(0, 4).map(toFloat);
    }
    function getStep(steps, percent2) {
      var count = steps.length - 1;
      var index2 = Math.min(Math.floor(count * percent2), count - 1);
      var step = steps.slice(index2, index2 + 2);
      step.push(percent2 === 1 ? 1 : percent2 % (1 / count) * count);
      return step;
    }
    function getValue(steps, percent2, digits) {
      if (digits === void 0)
        digits = 2;
      var ref2 = getStep(steps, percent2);
      var start = ref2[0];
      var end = ref2[1];
      var p2 = ref2[2];
      return (isNumber(start) ? start + Math.abs(start - end) * p2 * (start < end ? 1 : -1) : +end).toFixed(digits);
    }
    function getUnit(steps) {
      return steps.reduce(function(unit, step) {
        return isString(step) && step.replace(/-|\d/g, "").trim() || unit;
      }, "");
    }
    function covers(el) {
      var ref2 = el.style;
      var backgroundSize = ref2.backgroundSize;
      var covers2 = css(css(el, "backgroundSize", ""), "backgroundSize") === "cover";
      el.style.backgroundSize = backgroundSize;
      return covers2;
    }
    var parallax = {
      mixins: [Parallax],
      props: {
        target: String,
        viewport: Number,
        easing: Number
      },
      data: {
        target: false,
        viewport: 1,
        easing: 1
      },
      computed: {
        target: function(ref2, $el) {
          var target = ref2.target;
          return getOffsetElement(target && query(target, $el) || $el);
        }
      },
      update: {
        read: function(ref2, types) {
          var percent2 = ref2.percent;
          if (!types.has("scroll")) {
            percent2 = false;
          }
          if (!this.matchMedia) {
            return;
          }
          var prev = percent2;
          percent2 = ease(scrolledOver(this.target) / (this.viewport || 1), this.easing);
          return {
            percent: percent2,
            style: prev !== percent2 ? this.getCss(percent2) : false
          };
        },
        write: function(ref2) {
          var style = ref2.style;
          if (!this.matchMedia) {
            this.reset();
            return;
          }
          style && css(this.$el, style);
        },
        events: ["scroll", "resize"]
      }
    };
    function ease(percent2, easing) {
      return clamp(percent2 * (1 - (easing - easing * percent2)));
    }
    function getOffsetElement(el) {
      return el ? "offsetTop" in el ? el : getOffsetElement(parent(el)) : document.body;
    }
    var SliderReactive = {
      update: {
        write: function() {
          if (this.stack.length || this.dragging) {
            return;
          }
          var index2 = this.getValidIndex(this.index);
          if (!~this.prevIndex || this.index !== index2) {
            this.show(index2);
          }
        },
        events: ["resize"]
      }
    };
    function Transitioner(prev, next, dir, ref2) {
      var center = ref2.center;
      var easing = ref2.easing;
      var list = ref2.list;
      var deferred = new Deferred();
      var from = prev ? getLeft(prev, list, center) : getLeft(next, list, center) + dimensions(next).width * dir;
      var to = next ? getLeft(next, list, center) : from + dimensions(prev).width * dir * (isRtl ? -1 : 1);
      return {
        dir,
        show: function(duration, percent2, linear) {
          if (percent2 === void 0)
            percent2 = 0;
          var timing = linear ? "linear" : easing;
          duration -= Math.round(duration * clamp(percent2, -1, 1));
          this.translate(percent2);
          percent2 = prev ? percent2 : clamp(percent2, 0, 1);
          triggerUpdate(this.getItemIn(), "itemin", { percent: percent2, duration, timing, dir });
          prev && triggerUpdate(this.getItemIn(true), "itemout", { percent: 1 - percent2, duration, timing, dir });
          Transition.start(list, { transform: translate(-to * (isRtl ? -1 : 1), "px") }, duration, timing).then(deferred.resolve, noop);
          return deferred.promise;
        },
        cancel: function() {
          Transition.cancel(list);
        },
        reset: function() {
          css(list, "transform", "");
        },
        forward: function(duration, percent2) {
          if (percent2 === void 0)
            percent2 = this.percent();
          Transition.cancel(list);
          return this.show(duration, percent2, true);
        },
        translate: function(percent2) {
          var distance = this.getDistance() * dir * (isRtl ? -1 : 1);
          css(list, "transform", translate(clamp(-to + (distance - distance * percent2), -getWidth(list), dimensions(list).width) * (isRtl ? -1 : 1), "px"));
          var actives = this.getActives();
          var itemIn = this.getItemIn();
          var itemOut = this.getItemIn(true);
          percent2 = prev ? clamp(percent2, -1, 1) : 0;
          children(list).forEach(function(slide2) {
            var isActive = includes(actives, slide2);
            var isIn2 = slide2 === itemIn;
            var isOut = slide2 === itemOut;
            var translateIn = isIn2 || !isOut && (isActive || dir * (isRtl ? -1 : 1) === -1 ^ getElLeft(slide2, list) > getElLeft(prev || next));
            triggerUpdate(slide2, "itemtranslate" + (translateIn ? "in" : "out"), {
              dir,
              percent: isOut ? 1 - percent2 : isIn2 ? percent2 : isActive ? 1 : 0
            });
          });
        },
        percent: function() {
          return Math.abs((css(list, "transform").split(",")[4] * (isRtl ? -1 : 1) + from) / (to - from));
        },
        getDistance: function() {
          return Math.abs(to - from);
        },
        getItemIn: function(out) {
          if (out === void 0)
            out = false;
          var actives = this.getActives();
          var nextActives = inView(list, getLeft(next || prev, list, center));
          if (out) {
            var temp = actives;
            actives = nextActives;
            nextActives = temp;
          }
          return nextActives[findIndex(nextActives, function(el) {
            return !includes(actives, el);
          })];
        },
        getActives: function() {
          return inView(list, getLeft(prev || next, list, center));
        }
      };
    }
    function getLeft(el, list, center) {
      var left = getElLeft(el, list);
      return center ? left - centerEl(el, list) : Math.min(left, getMax(list));
    }
    function getMax(list) {
      return Math.max(0, getWidth(list) - dimensions(list).width);
    }
    function getWidth(list) {
      return children(list).reduce(function(right, el) {
        return dimensions(el).width + right;
      }, 0);
    }
    function centerEl(el, list) {
      return dimensions(list).width / 2 - dimensions(el).width / 2;
    }
    function getElLeft(el, list) {
      return el && (position(el).left + (isRtl ? dimensions(el).width - dimensions(list).width : 0)) * (isRtl ? -1 : 1) || 0;
    }
    function inView(list, listLeft) {
      listLeft -= 1;
      var listWidth = dimensions(list).width;
      var listRight = listLeft + listWidth + 2;
      return children(list).filter(function(slide2) {
        var slideLeft = getElLeft(slide2, list);
        var slideRight = slideLeft + Math.min(dimensions(slide2).width, listWidth);
        return slideLeft >= listLeft && slideRight <= listRight;
      });
    }
    function triggerUpdate(el, type, data2) {
      trigger(el, createEvent(type, false, false, data2));
    }
    var slider = {
      mixins: [Class, Slider, SliderReactive],
      props: {
        center: Boolean,
        sets: Boolean
      },
      data: {
        center: false,
        sets: false,
        attrItem: "uk-slider-item",
        selList: ".uk-slider-items",
        selNav: ".uk-slider-nav",
        clsContainer: "uk-slider-container",
        Transitioner
      },
      computed: {
        avgWidth: function() {
          return getWidth(this.list) / this.length;
        },
        finite: function(ref2) {
          var finite = ref2.finite;
          return finite || Math.ceil(getWidth(this.list)) < dimensions(this.list).width + getMaxElWidth(this.list) + this.center;
        },
        maxIndex: function() {
          if (!this.finite || this.center && !this.sets) {
            return this.length - 1;
          }
          if (this.center) {
            return last(this.sets);
          }
          var lft = 0;
          var max = getMax(this.list);
          var index2 = findIndex(this.slides, function(el) {
            if (lft >= max) {
              return true;
            }
            lft += dimensions(el).width;
          });
          return ~index2 ? index2 : this.length - 1;
        },
        sets: function(ref2) {
          var this$1$1 = this;
          var sets = ref2.sets;
          if (!sets) {
            return;
          }
          var width2 = dimensions(this.list).width / (this.center ? 2 : 1);
          var left = 0;
          var leftCenter = width2;
          var slideLeft = 0;
          sets = sortBy$1(this.slides, "offsetLeft").reduce(function(sets2, slide2, i) {
            var slideWidth = dimensions(slide2).width;
            var slideRight = slideLeft + slideWidth;
            if (slideRight > left) {
              if (!this$1$1.center && i > this$1$1.maxIndex) {
                i = this$1$1.maxIndex;
              }
              if (!includes(sets2, i)) {
                var cmp = this$1$1.slides[i + 1];
                if (this$1$1.center && cmp && slideWidth < leftCenter - dimensions(cmp).width / 2) {
                  leftCenter -= slideWidth;
                } else {
                  leftCenter = width2;
                  sets2.push(i);
                  left = slideLeft + width2 + (this$1$1.center ? slideWidth / 2 : 0);
                }
              }
            }
            slideLeft += slideWidth;
            return sets2;
          }, []);
          return !isEmpty(sets) && sets;
        },
        transitionOptions: function() {
          return {
            center: this.center,
            list: this.list
          };
        }
      },
      connected: function() {
        toggleClass(this.$el, this.clsContainer, !$("." + this.clsContainer, this.$el));
      },
      update: {
        write: function() {
          var this$1$1 = this;
          this.navItems.forEach(function(el) {
            var index2 = toNumber(data(el, this$1$1.attrItem));
            if (index2 !== false) {
              el.hidden = !this$1$1.maxIndex || index2 > this$1$1.maxIndex || this$1$1.sets && !includes(this$1$1.sets, index2);
            }
          });
          if (this.length && !this.dragging && !this.stack.length) {
            this.reorder();
            this._translate(1);
          }
          var actives = this._getTransitioner(this.index).getActives();
          this.slides.forEach(function(slide2) {
            return toggleClass(slide2, this$1$1.clsActive, includes(actives, slide2));
          });
          if (this.clsActivated && (!this.sets || includes(this.sets, toFloat(this.index)))) {
            this.slides.forEach(function(slide2) {
              return toggleClass(slide2, this$1$1.clsActivated || "", includes(actives, slide2));
            });
          }
        },
        events: ["resize"]
      },
      events: {
        beforeitemshow: function(e) {
          if (!this.dragging && this.sets && this.stack.length < 2 && !includes(this.sets, this.index)) {
            this.index = this.getValidIndex();
          }
          var diff = Math.abs(this.index - this.prevIndex + (this.dir > 0 && this.index < this.prevIndex || this.dir < 0 && this.index > this.prevIndex ? (this.maxIndex + 1) * this.dir : 0));
          if (!this.dragging && diff > 1) {
            for (var i = 0; i < diff; i++) {
              this.stack.splice(1, 0, this.dir > 0 ? "next" : "previous");
            }
            e.preventDefault();
            return;
          }
          var index2 = this.dir < 0 || !this.slides[this.prevIndex] ? this.index : this.prevIndex;
          this.duration = speedUp(this.avgWidth / this.velocity) * (dimensions(this.slides[index2]).width / this.avgWidth);
          this.reorder();
        },
        itemshow: function() {
          if (~this.prevIndex) {
            addClass(this._getTransitioner().getItemIn(), this.clsActive);
          }
        }
      },
      methods: {
        reorder: function() {
          var this$1$1 = this;
          if (this.finite) {
            css(this.slides, "order", "");
            return;
          }
          var index2 = this.dir > 0 && this.slides[this.prevIndex] ? this.prevIndex : this.index;
          this.slides.forEach(function(slide3, i) {
            return css(slide3, "order", this$1$1.dir > 0 && i < index2 ? 1 : this$1$1.dir < 0 && i >= this$1$1.index ? -1 : "");
          });
          if (!this.center) {
            return;
          }
          var next = this.slides[index2];
          var width2 = dimensions(this.list).width / 2 - dimensions(next).width / 2;
          var j = 0;
          while (width2 > 0) {
            var slideIndex = this.getIndex(--j + index2, index2);
            var slide2 = this.slides[slideIndex];
            css(slide2, "order", slideIndex > index2 ? -2 : -1);
            width2 -= dimensions(slide2).width;
          }
        },
        getValidIndex: function(index2, prevIndex) {
          if (index2 === void 0)
            index2 = this.index;
          if (prevIndex === void 0)
            prevIndex = this.prevIndex;
          index2 = this.getIndex(index2, prevIndex);
          if (!this.sets) {
            return index2;
          }
          var prev;
          do {
            if (includes(this.sets, index2)) {
              return index2;
            }
            prev = index2;
            index2 = this.getIndex(index2 + this.dir, prevIndex);
          } while (index2 !== prev);
          return index2;
        }
      }
    };
    function getMaxElWidth(list) {
      return Math.max.apply(Math, [0].concat(children(list).map(function(el) {
        return dimensions(el).width;
      })));
    }
    var sliderParallax = {
      mixins: [Parallax],
      data: {
        selItem: "!li"
      },
      computed: {
        item: function(ref2, $el) {
          var selItem = ref2.selItem;
          return query(selItem, $el);
        }
      },
      events: [
        {
          name: "itemin itemout",
          self: true,
          el: function() {
            return this.item;
          },
          handler: function(ref2) {
            var this$1$1 = this;
            var type = ref2.type;
            var ref_detail = ref2.detail;
            var percent2 = ref_detail.percent;
            var duration = ref_detail.duration;
            var timing = ref_detail.timing;
            var dir = ref_detail.dir;
            fastdom.read(function() {
              var propsFrom = this$1$1.getCss(getCurrentPercent(type, dir, percent2));
              var propsTo = this$1$1.getCss(isIn(type) ? 0.5 : dir > 0 ? 1 : 0);
              fastdom.write(function() {
                css(this$1$1.$el, propsFrom);
                Transition.start(this$1$1.$el, propsTo, duration, timing).catch(noop);
              });
            });
          }
        },
        {
          name: "transitioncanceled transitionend",
          self: true,
          el: function() {
            return this.item;
          },
          handler: function() {
            Transition.cancel(this.$el);
          }
        },
        {
          name: "itemtranslatein itemtranslateout",
          self: true,
          el: function() {
            return this.item;
          },
          handler: function(ref2) {
            var this$1$1 = this;
            var type = ref2.type;
            var ref_detail = ref2.detail;
            var percent2 = ref_detail.percent;
            var dir = ref_detail.dir;
            fastdom.read(function() {
              var props2 = this$1$1.getCss(getCurrentPercent(type, dir, percent2));
              fastdom.write(function() {
                return css(this$1$1.$el, props2);
              });
            });
          }
        }
      ]
    };
    function isIn(type) {
      return endsWith(type, "in");
    }
    function getCurrentPercent(type, dir, percent2) {
      percent2 /= 2;
      return isIn(type) ^ dir < 0 ? percent2 : 1 - percent2;
    }
    var Animations = assign({}, Animations$2, {
      fade: {
        show: function() {
          return [
            { opacity: 0, zIndex: 0 },
            { zIndex: -1 }
          ];
        },
        percent: function(current) {
          return 1 - css(current, "opacity");
        },
        translate: function(percent2) {
          return [
            { opacity: 1 - percent2, zIndex: 0 },
            { zIndex: -1 }
          ];
        }
      },
      scale: {
        show: function() {
          return [
            { opacity: 0, transform: scale3d(1 + 0.5), zIndex: 0 },
            { zIndex: -1 }
          ];
        },
        percent: function(current) {
          return 1 - css(current, "opacity");
        },
        translate: function(percent2) {
          return [
            { opacity: 1 - percent2, transform: scale3d(1 + 0.5 * percent2), zIndex: 0 },
            { zIndex: -1 }
          ];
        }
      },
      pull: {
        show: function(dir) {
          return dir < 0 ? [
            { transform: translate(30), zIndex: -1 },
            { transform: translate(), zIndex: 0 }
          ] : [
            { transform: translate(-100), zIndex: 0 },
            { transform: translate(), zIndex: -1 }
          ];
        },
        percent: function(current, next, dir) {
          return dir < 0 ? 1 - translated(next) : translated(current);
        },
        translate: function(percent2, dir) {
          return dir < 0 ? [
            { transform: translate(30 * percent2), zIndex: -1 },
            { transform: translate(-100 * (1 - percent2)), zIndex: 0 }
          ] : [
            { transform: translate(-percent2 * 100), zIndex: 0 },
            { transform: translate(30 * (1 - percent2)), zIndex: -1 }
          ];
        }
      },
      push: {
        show: function(dir) {
          return dir < 0 ? [
            { transform: translate(100), zIndex: 0 },
            { transform: translate(), zIndex: -1 }
          ] : [
            { transform: translate(-30), zIndex: -1 },
            { transform: translate(), zIndex: 0 }
          ];
        },
        percent: function(current, next, dir) {
          return dir > 0 ? 1 - translated(next) : translated(current);
        },
        translate: function(percent2, dir) {
          return dir < 0 ? [
            { transform: translate(percent2 * 100), zIndex: 0 },
            { transform: translate(-30 * (1 - percent2)), zIndex: -1 }
          ] : [
            { transform: translate(-30 * percent2), zIndex: -1 },
            { transform: translate(100 * (1 - percent2)), zIndex: 0 }
          ];
        }
      }
    });
    var slideshow = {
      mixins: [Class, Slideshow, SliderReactive],
      props: {
        ratio: String,
        minHeight: Number,
        maxHeight: Number
      },
      data: {
        ratio: "16:9",
        minHeight: false,
        maxHeight: false,
        selList: ".uk-slideshow-items",
        attrItem: "uk-slideshow-item",
        selNav: ".uk-slideshow-nav",
        Animations
      },
      update: {
        read: function() {
          if (!this.list) {
            return false;
          }
          var ref2 = this.ratio.split(":").map(Number);
          var width2 = ref2[0];
          var height2 = ref2[1];
          height2 = height2 * this.list.offsetWidth / width2 || 0;
          if (this.minHeight) {
            height2 = Math.max(this.minHeight, height2);
          }
          if (this.maxHeight) {
            height2 = Math.min(this.maxHeight, height2);
          }
          return { height: height2 - boxModelAdjust(this.list, "height", "content-box") };
        },
        write: function(ref2) {
          var height2 = ref2.height;
          height2 > 0 && css(this.list, "minHeight", height2);
        },
        events: ["resize"]
      }
    };
    var sortable = {
      mixins: [Class, Animate],
      props: {
        group: String,
        threshold: Number,
        clsItem: String,
        clsPlaceholder: String,
        clsDrag: String,
        clsDragState: String,
        clsBase: String,
        clsNoDrag: String,
        clsEmpty: String,
        clsCustom: String,
        handle: String
      },
      data: {
        group: false,
        threshold: 5,
        clsItem: "uk-sortable-item",
        clsPlaceholder: "uk-sortable-placeholder",
        clsDrag: "uk-sortable-drag",
        clsDragState: "uk-drag",
        clsBase: "uk-sortable",
        clsNoDrag: "uk-sortable-nodrag",
        clsEmpty: "uk-sortable-empty",
        clsCustom: "",
        handle: false,
        pos: {}
      },
      created: function() {
        var this$1$1 = this;
        ["init", "start", "move", "end"].forEach(function(key2) {
          var fn = this$1$1[key2];
          this$1$1[key2] = function(e) {
            assign(this$1$1.pos, getEventPos(e));
            fn(e);
          };
        });
      },
      events: {
        name: pointerDown,
        passive: false,
        handler: "init"
      },
      computed: {
        target: function() {
          return (this.$el.tBodies || [this.$el])[0];
        },
        items: function() {
          return children(this.target);
        },
        isEmpty: {
          get: function() {
            return isEmpty(this.items);
          },
          watch: function(empty2) {
            toggleClass(this.target, this.clsEmpty, empty2);
          },
          immediate: true
        },
        handles: {
          get: function(ref2, el) {
            var handle = ref2.handle;
            return handle ? $$(handle, el) : this.items;
          },
          watch: function(handles, prev) {
            css(prev, { touchAction: "", userSelect: "" });
            css(handles, { touchAction: hasTouch ? "none" : "", userSelect: "none" });
          },
          immediate: true
        }
      },
      update: {
        write: function(data2) {
          if (!this.drag || !parent(this.placeholder)) {
            return;
          }
          var ref2 = this;
          var ref_pos = ref2.pos;
          var x = ref_pos.x;
          var y = ref_pos.y;
          var ref_origin = ref2.origin;
          var offsetTop = ref_origin.offsetTop;
          var offsetLeft = ref_origin.offsetLeft;
          var placeholder = ref2.placeholder;
          css(this.drag, {
            top: y - offsetTop,
            left: x - offsetLeft
          });
          var sortable2 = this.getSortable(document.elementFromPoint(x, y));
          if (!sortable2) {
            return;
          }
          var items = sortable2.items;
          if (items.some(Transition.inProgress)) {
            return;
          }
          var target = findTarget(items, { x, y });
          if (items.length && (!target || target === placeholder)) {
            return;
          }
          var previous = this.getSortable(placeholder);
          var insertTarget = findInsertTarget(sortable2.target, target, placeholder, x, y, sortable2 === previous && data2.moved !== target);
          if (insertTarget === false) {
            return;
          }
          if (insertTarget && placeholder === insertTarget) {
            return;
          }
          if (sortable2 !== previous) {
            previous.remove(placeholder);
            data2.moved = target;
          } else {
            delete data2.moved;
          }
          sortable2.insert(placeholder, insertTarget);
          this.touched.add(sortable2);
        },
        events: ["move"]
      },
      methods: {
        init: function(e) {
          var target = e.target;
          var button = e.button;
          var defaultPrevented = e.defaultPrevented;
          var ref2 = this.items.filter(function(el) {
            return within(target, el);
          });
          var placeholder = ref2[0];
          if (!placeholder || defaultPrevented || button > 0 || isInput(target) || within(target, "." + this.clsNoDrag) || this.handle && !within(target, this.handle)) {
            return;
          }
          e.preventDefault();
          this.touched = new Set([this]);
          this.placeholder = placeholder;
          this.origin = assign({ target, index: index(placeholder) }, this.pos);
          on(document, pointerMove, this.move);
          on(document, pointerUp, this.end);
          if (!this.threshold) {
            this.start(e);
          }
        },
        start: function(e) {
          this.drag = appendDrag(this.$container, this.placeholder);
          var ref2 = this.placeholder.getBoundingClientRect();
          var left = ref2.left;
          var top = ref2.top;
          assign(this.origin, { offsetLeft: this.pos.x - left, offsetTop: this.pos.y - top });
          addClass(this.drag, this.clsDrag, this.clsCustom);
          addClass(this.placeholder, this.clsPlaceholder);
          addClass(this.items, this.clsItem);
          addClass(document.documentElement, this.clsDragState);
          trigger(this.$el, "start", [this, this.placeholder]);
          trackScroll(this.pos);
          this.move(e);
        },
        move: function(e) {
          if (this.drag) {
            this.$emit("move");
          } else if (Math.abs(this.pos.x - this.origin.x) > this.threshold || Math.abs(this.pos.y - this.origin.y) > this.threshold) {
            this.start(e);
          }
        },
        end: function() {
          var this$1$1 = this;
          off(document, pointerMove, this.move);
          off(document, pointerUp, this.end);
          off(window, "scroll", this.scroll);
          if (!this.drag) {
            return;
          }
          untrackScroll();
          var sortable2 = this.getSortable(this.placeholder);
          if (this === sortable2) {
            if (this.origin.index !== index(this.placeholder)) {
              trigger(this.$el, "moved", [this, this.placeholder]);
            }
          } else {
            trigger(sortable2.$el, "added", [sortable2, this.placeholder]);
            trigger(this.$el, "removed", [this, this.placeholder]);
          }
          trigger(this.$el, "stop", [this, this.placeholder]);
          remove$1(this.drag);
          this.drag = null;
          this.touched.forEach(function(ref2) {
            var clsPlaceholder = ref2.clsPlaceholder;
            var clsItem = ref2.clsItem;
            return this$1$1.touched.forEach(function(sortable3) {
              return removeClass(sortable3.items, clsPlaceholder, clsItem);
            });
          });
          this.touched = null;
          removeClass(document.documentElement, this.clsDragState);
        },
        insert: function(element, target) {
          var this$1$1 = this;
          addClass(this.items, this.clsItem);
          var insert = function() {
            return target ? before(target, element) : append(this$1$1.target, element);
          };
          this.animate(insert);
        },
        remove: function(element) {
          if (!within(element, this.target)) {
            return;
          }
          this.animate(function() {
            return remove$1(element);
          });
        },
        getSortable: function(element) {
          do {
            var sortable2 = this.$getComponent(element, "sortable");
            if (sortable2 && (sortable2 === this || this.group !== false && sortable2.group === this.group)) {
              return sortable2;
            }
          } while (element = parent(element));
        }
      }
    };
    var trackTimer;
    function trackScroll(pos) {
      var last2 = Date.now();
      trackTimer = setInterval(function() {
        var x = pos.x;
        var y = pos.y;
        y += window.pageYOffset;
        var dist = (Date.now() - last2) * 0.3;
        last2 = Date.now();
        scrollParents(document.elementFromPoint(x, pos.y), /auto|scroll/).reverse().some(function(scrollEl) {
          var scroll2 = scrollEl.scrollTop;
          var scrollHeight = scrollEl.scrollHeight;
          var ref2 = offset(getViewport$1(scrollEl));
          var top = ref2.top;
          var bottom = ref2.bottom;
          var height2 = ref2.height;
          if (top < y && top + 35 > y) {
            scroll2 -= dist;
          } else if (bottom > y && bottom - 35 < y) {
            scroll2 += dist;
          } else {
            return;
          }
          if (scroll2 > 0 && scroll2 < scrollHeight - height2) {
            scrollTop(scrollEl, scroll2);
            return true;
          }
        });
      }, 15);
    }
    function untrackScroll() {
      clearInterval(trackTimer);
    }
    function appendDrag(container, element) {
      var clone = append(container, element.outerHTML.replace(/(^<)(?:li|tr)|(?:li|tr)(\/>$)/g, "$1div$2"));
      css(clone, "margin", "0", "important");
      css(clone, assign({
        boxSizing: "border-box",
        width: element.offsetWidth,
        height: element.offsetHeight
      }, css(element, ["paddingLeft", "paddingRight", "paddingTop", "paddingBottom"])));
      height(clone.firstElementChild, height(element.firstElementChild));
      return clone;
    }
    function findTarget(items, point) {
      return items[findIndex(items, function(item) {
        return pointInRect(point, item.getBoundingClientRect());
      })];
    }
    function findInsertTarget(list, target, placeholder, x, y, sameList) {
      if (!children(list).length) {
        return;
      }
      var rect = target.getBoundingClientRect();
      if (!sameList) {
        if (!isHorizontal(list, placeholder)) {
          return y < rect.top + rect.height / 2 ? target : target.nextElementSibling;
        }
        return target;
      }
      var placeholderRect = placeholder.getBoundingClientRect();
      var sameRow = linesIntersect([rect.top, rect.bottom], [placeholderRect.top, placeholderRect.bottom]);
      var pointerPos = sameRow ? x : y;
      var lengthProp = sameRow ? "width" : "height";
      var startProp = sameRow ? "left" : "top";
      var endProp = sameRow ? "right" : "bottom";
      var diff = placeholderRect[lengthProp] < rect[lengthProp] ? rect[lengthProp] - placeholderRect[lengthProp] : 0;
      if (placeholderRect[startProp] < rect[startProp]) {
        if (diff && pointerPos < rect[startProp] + diff) {
          return false;
        }
        return target.nextElementSibling;
      }
      if (diff && pointerPos > rect[endProp] - diff) {
        return false;
      }
      return target;
    }
    function isHorizontal(list, placeholder) {
      var single = children(list).length === 1;
      if (single) {
        append(list, placeholder);
      }
      var items = children(list);
      var isHorizontal2 = items.some(function(el, i) {
        var rectA = el.getBoundingClientRect();
        return items.slice(i + 1).some(function(el2) {
          var rectB = el2.getBoundingClientRect();
          return !linesIntersect([rectA.left, rectA.right], [rectB.left, rectB.right]);
        });
      });
      if (single) {
        remove$1(placeholder);
      }
      return isHorizontal2;
    }
    function linesIntersect(lineA, lineB) {
      return lineA[1] > lineB[0] && lineB[1] > lineA[0];
    }
    var obj;
    var tooltip = {
      mixins: [Container, Togglable, Position],
      args: "title",
      props: {
        delay: Number,
        title: String
      },
      data: {
        pos: "top",
        title: "",
        delay: 0,
        animation: ["uk-animation-scale-up"],
        duration: 100,
        cls: "uk-active",
        clsPos: "uk-tooltip"
      },
      beforeConnect: function() {
        this._hasTitle = hasAttr(this.$el, "title");
        attr(this.$el, "title", "");
        this.updateAria(false);
        makeFocusable(this.$el);
      },
      disconnected: function() {
        this.hide();
        attr(this.$el, "title", this._hasTitle ? this.title : null);
      },
      methods: {
        show: function() {
          var this$1$1 = this;
          if (this.isToggled(this.tooltip || null) || !this.title) {
            return;
          }
          this._unbind = once(document, "show keydown " + pointerDown, this.hide, false, function(e) {
            return e.type === pointerDown && !within(e.target, this$1$1.$el) || e.type === "keydown" && e.keyCode === 27 || e.type === "show" && e.detail[0] !== this$1$1 && e.detail[0].$name === this$1$1.$name;
          });
          clearTimeout(this.showTimer);
          this.showTimer = setTimeout(this._show, this.delay);
        },
        hide: function() {
          var this$1$1 = this;
          if (matches(this.$el, "input:focus")) {
            return;
          }
          clearTimeout(this.showTimer);
          if (!this.isToggled(this.tooltip || null)) {
            return;
          }
          this.toggleElement(this.tooltip, false, false).then(function() {
            remove$1(this$1$1.tooltip);
            this$1$1.tooltip = null;
            this$1$1._unbind();
          });
        },
        _show: function() {
          var this$1$1 = this;
          this.tooltip = append(this.container, '<div class="' + this.clsPos + '"> <div class="' + this.clsPos + '-inner">' + this.title + "</div> </div>");
          on(this.tooltip, "toggled", function(e, toggled) {
            this$1$1.updateAria(toggled);
            if (!toggled) {
              return;
            }
            this$1$1.positionAt(this$1$1.tooltip, this$1$1.$el);
            this$1$1.origin = this$1$1.getAxis() === "y" ? flipPosition(this$1$1.dir) + "-" + this$1$1.align : this$1$1.align + "-" + flipPosition(this$1$1.dir);
          });
          this.toggleElement(this.tooltip, true);
        },
        updateAria: function(toggled) {
          attr(this.$el, "aria-expanded", toggled);
        }
      },
      events: (obj = {
        focus: "show",
        blur: "hide"
      }, obj[pointerEnter + " " + pointerLeave] = function(e) {
        if (!isTouch(e)) {
          this[e.type === pointerEnter ? "show" : "hide"]();
        }
      }, obj[pointerDown] = function(e) {
        if (isTouch(e)) {
          this.show();
        }
      }, obj)
    };
    function makeFocusable(el) {
      if (!isFocusable(el)) {
        attr(el, "tabindex", "0");
      }
    }
    var upload = {
      props: {
        allow: String,
        clsDragover: String,
        concurrent: Number,
        maxSize: Number,
        method: String,
        mime: String,
        msgInvalidMime: String,
        msgInvalidName: String,
        msgInvalidSize: String,
        multiple: Boolean,
        name: String,
        params: Object,
        type: String,
        url: String
      },
      data: {
        allow: false,
        clsDragover: "uk-dragover",
        concurrent: 1,
        maxSize: 0,
        method: "POST",
        mime: false,
        msgInvalidMime: "Invalid File Type: %s",
        msgInvalidName: "Invalid File Name: %s",
        msgInvalidSize: "Invalid File Size: %s Kilobytes Max",
        multiple: false,
        name: "files[]",
        params: {},
        type: "",
        url: "",
        abort: noop,
        beforeAll: noop,
        beforeSend: noop,
        complete: noop,
        completeAll: noop,
        error: noop,
        fail: noop,
        load: noop,
        loadEnd: noop,
        loadStart: noop,
        progress: noop
      },
      events: {
        change: function(e) {
          if (!matches(e.target, 'input[type="file"]')) {
            return;
          }
          e.preventDefault();
          if (e.target.files) {
            this.upload(e.target.files);
          }
          e.target.value = "";
        },
        drop: function(e) {
          stop(e);
          var transfer = e.dataTransfer;
          if (!transfer || !transfer.files) {
            return;
          }
          removeClass(this.$el, this.clsDragover);
          this.upload(transfer.files);
        },
        dragenter: function(e) {
          stop(e);
        },
        dragover: function(e) {
          stop(e);
          addClass(this.$el, this.clsDragover);
        },
        dragleave: function(e) {
          stop(e);
          removeClass(this.$el, this.clsDragover);
        }
      },
      methods: {
        upload: function(files) {
          var this$1$1 = this;
          if (!files.length) {
            return;
          }
          trigger(this.$el, "upload", [files]);
          for (var i = 0; i < files.length; i++) {
            if (this.maxSize && this.maxSize * 1e3 < files[i].size) {
              this.fail(this.msgInvalidSize.replace("%s", this.maxSize));
              return;
            }
            if (this.allow && !match(this.allow, files[i].name)) {
              this.fail(this.msgInvalidName.replace("%s", this.allow));
              return;
            }
            if (this.mime && !match(this.mime, files[i].type)) {
              this.fail(this.msgInvalidMime.replace("%s", this.mime));
              return;
            }
          }
          if (!this.multiple) {
            files = [files[0]];
          }
          this.beforeAll(this, files);
          var chunks = chunk(files, this.concurrent);
          var upload2 = function(files2) {
            var data2 = new FormData();
            files2.forEach(function(file) {
              return data2.append(this$1$1.name, file);
            });
            for (var key2 in this$1$1.params) {
              data2.append(key2, this$1$1.params[key2]);
            }
            ajax(this$1$1.url, {
              data: data2,
              method: this$1$1.method,
              responseType: this$1$1.type,
              beforeSend: function(env) {
                var xhr = env.xhr;
                xhr.upload && on(xhr.upload, "progress", this$1$1.progress);
                ["loadStart", "load", "loadEnd", "abort"].forEach(function(type) {
                  return on(xhr, type.toLowerCase(), this$1$1[type]);
                });
                return this$1$1.beforeSend(env);
              }
            }).then(function(xhr) {
              this$1$1.complete(xhr);
              if (chunks.length) {
                upload2(chunks.shift());
              } else {
                this$1$1.completeAll(xhr);
              }
            }, function(e) {
              return this$1$1.error(e);
            });
          };
          upload2(chunks.shift());
        }
      }
    };
    function match(pattern, path) {
      return path.match(new RegExp("^" + pattern.replace(/\//g, "\\/").replace(/\*\*/g, "(\\/[^\\/]+)*").replace(/\*/g, "[^\\/]+").replace(/((?!\\))\?/g, "$1.") + "$", "i"));
    }
    function chunk(files, size) {
      var chunks = [];
      for (var i = 0; i < files.length; i += size) {
        var chunk2 = [];
        for (var j = 0; j < size; j++) {
          chunk2.push(files[i + j]);
        }
        chunks.push(chunk2);
      }
      return chunks;
    }
    function stop(e) {
      e.preventDefault();
      e.stopPropagation();
    }
    var components = /* @__PURE__ */ Object.freeze({
      __proto__: null,
      Countdown: countdown,
      Filter: filter,
      Lightbox: lightbox,
      LightboxPanel,
      Notification: notification,
      Parallax: parallax,
      Slider: slider,
      SliderParallax: sliderParallax,
      Slideshow: slideshow,
      SlideshowParallax: sliderParallax,
      Sortable: sortable,
      Tooltip: tooltip,
      Upload: upload
    });
    each(components, function(component, name) {
      return UIkit2.component(name, component);
    });
    return UIkit2;
  });
})(uikit);
var UIkit = uikit.exports;
var uikitIcons = { exports: {} };
/*! UIkit 3.10.1 | https://www.getuikit.com | (c) 2014 - 2022 YOOtheme | MIT License */
(function(module, exports) {
  (function(global, factory) {
    module.exports = factory();
  })(commonjsGlobal, function() {
    function plugin(UIkit2) {
      if (plugin.installed) {
        return;
      }
      UIkit2.icon.add({
        "500px": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.624,11.866c-0.141,0.132,0.479,0.658,0.662,0.418c0.051-0.046,0.607-0.61,0.662-0.664c0,0,0.738,0.719,0.814,0.719 c0.1,0,0.207-0.055,0.322-0.17c0.27-0.269,0.135-0.416,0.066-0.495l-0.631-0.616l0.658-0.668c0.146-0.156,0.021-0.314-0.1-0.449 c-0.182-0.18-0.359-0.226-0.471-0.125l-0.656,0.654l-0.654-0.654c-0.033-0.034-0.08-0.045-0.124-0.045 c-0.079,0-0.191,0.068-0.307,0.181c-0.202,0.202-0.247,0.351-0.133,0.462l0.665,0.665L9.624,11.866z"/><path d="M11.066,2.884c-1.061,0-2.185,0.248-3.011,0.604c-0.087,0.034-0.141,0.106-0.15,0.205C7.893,3.784,7.919,3.909,7.982,4.066 c0.05,0.136,0.187,0.474,0.452,0.372c0.844-0.326,1.779-0.507,2.633-0.507c0.963,0,1.9,0.191,2.781,0.564 c0.695,0.292,1.357,0.719,2.078,1.34c0.051,0.044,0.105,0.068,0.164,0.068c0.143,0,0.273-0.137,0.389-0.271 c0.191-0.214,0.324-0.395,0.135-0.575c-0.686-0.654-1.436-1.138-2.363-1.533C13.24,3.097,12.168,2.884,11.066,2.884z"/><path d="M16.43,15.747c-0.092-0.028-0.242,0.05-0.309,0.119l0,0c-0.652,0.652-1.42,1.169-2.268,1.521 c-0.877,0.371-1.814,0.551-2.779,0.551c-0.961,0-1.896-0.189-2.775-0.564c-0.848-0.36-1.612-0.879-2.268-1.53 c-0.682-0.688-1.196-1.455-1.529-2.268c-0.325-0.799-0.471-1.643-0.471-1.643c-0.045-0.24-0.258-0.249-0.567-0.203 c-0.128,0.021-0.519,0.079-0.483,0.36v0.01c0.105,0.644,0.289,1.284,0.545,1.895c0.417,0.969,1.002,1.849,1.756,2.604 c0.757,0.754,1.636,1.34,2.604,1.757C8.901,18.785,9.97,19,11.088,19c1.104,0,2.186-0.215,3.188-0.645 c1.838-0.896,2.604-1.757,2.604-1.757c0.182-0.204,0.227-0.317-0.1-0.643C16.779,15.956,16.525,15.774,16.43,15.747z"/><path d="M5.633,13.287c0.293,0.71,0.723,1.341,1.262,1.882c0.54,0.54,1.172,0.971,1.882,1.264c0.731,0.303,1.509,0.461,2.298,0.461 c0.801,0,1.578-0.158,2.297-0.461c0.711-0.293,1.344-0.724,1.883-1.264c0.543-0.541,0.971-1.172,1.264-1.882 c0.314-0.721,0.463-1.5,0.463-2.298c0-0.79-0.148-1.569-0.463-2.289c-0.293-0.699-0.721-1.329-1.264-1.881 c-0.539-0.541-1.172-0.959-1.867-1.263c-0.721-0.303-1.5-0.461-2.299-0.461c-0.802,0-1.613,0.159-2.322,0.461 c-0.577,0.25-1.544,0.867-2.119,1.454v0.012V2.108h8.16C15.1,2.104,15.1,1.69,15.1,1.552C15.1,1.417,15.1,1,14.809,1H5.915 C5.676,1,5.527,1.192,5.527,1.384v6.84c0,0.214,0.273,0.372,0.529,0.428c0.5,0.105,0.614-0.056,0.737-0.224l0,0 c0.18-0.273,0.776-0.884,0.787-0.894c0.901-0.905,2.117-1.408,3.416-1.408c1.285,0,2.5,0.501,3.412,1.408 c0.914,0.914,1.408,2.122,1.408,3.405c0,1.288-0.508,2.496-1.408,3.405c-0.9,0.896-2.152,1.406-3.438,1.406 c-0.877,0-1.711-0.229-2.433-0.671v-4.158c0-0.553,0.237-1.151,0.643-1.614c0.462-0.519,1.094-0.799,1.782-0.799 c0.664,0,1.293,0.253,1.758,0.715c0.459,0.459,0.709,1.071,0.709,1.723c0,1.385-1.094,2.468-2.488,2.468 c-0.273,0-0.769-0.121-0.781-0.125c-0.281-0.087-0.405,0.306-0.438,0.436c-0.159,0.496,0.079,0.585,0.123,0.607 c0.452,0.137,0.743,0.157,1.129,0.157c1.973,0,3.572-1.6,3.572-3.57c0-1.964-1.6-3.552-3.572-3.552c-0.97,0-1.872,0.36-2.546,1.038 c-0.656,0.631-1.027,1.487-1.027,2.322v3.438v-0.011c-0.372-0.42-0.732-1.041-0.981-1.682c-0.102-0.248-0.315-0.202-0.607-0.113 c-0.135,0.035-0.519,0.157-0.44,0.439C5.372,12.799,5.577,13.164,5.633,13.287z"/></svg>',
        "album": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="2" width="10" height="1"/><rect x="3" y="4" width="14" height="1"/><rect fill="none" stroke="#000" x="1.5" y="6.5" width="17" height="11"/></svg>',
        "arrow-down": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="10.5,16.08 5.63,10.66 6.37,10 10.5,14.58 14.63,10 15.37,10.66"/><line fill="none" stroke="#000" x1="10.5" y1="4" x2="10.5" y2="15"/></svg>',
        "arrow-left": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="10 14 5 9.5 10 5"/><line fill="none" stroke="#000" x1="16" y1="9.5" x2="5" y2="9.52"/></svg>',
        "arrow-right": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="10 5 15 9.5 10 14"/><line fill="none" stroke="#000" x1="4" y1="9.5" x2="15" y2="9.5"/></svg>',
        "arrow-up": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="10.5,4 15.37,9.4 14.63,10.08 10.5,5.49 6.37,10.08 5.63,9.4"/><line fill="none" stroke="#000" x1="10.5" y1="16" x2="10.5" y2="5"/></svg>',
        "bag": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M7.5,7.5V4A2.48,2.48,0,0,1,10,1.5,2.54,2.54,0,0,1,12.5,4V7.5"/><polygon fill="none" stroke="#000" points="16.5 7.5 3.5 7.5 2.5 18.5 17.5 18.5 16.5 7.5"/></svg>',
        "ban": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/><line fill="none" stroke="#000" stroke-width="1.1" x1="4" y1="3.5" x2="16" y2="16.5"/></svg>',
        "behance": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.5,10.6c-0.4-0.5-0.9-0.9-1.6-1.1c1.7-1,2.2-3.2,0.7-4.7C7.8,4,6.3,4,5.2,4C3.5,4,1.7,4,0,4v12c1.7,0,3.4,0,5.2,0 c1,0,2.1,0,3.1-0.5C10.2,14.6,10.5,12.3,9.5,10.6L9.5,10.6z M5.6,6.1c1.8,0,1.8,2.7-0.1,2.7c-1,0-2,0-2.9,0V6.1H5.6z M2.6,13.8v-3.1 c1.1,0,2.1,0,3.2,0c2.1,0,2.1,3.2,0.1,3.2L2.6,13.8z"/><path d="M19.9,10.9C19.7,9.2,18.7,7.6,17,7c-4.2-1.3-7.3,3.4-5.3,7.1c0.9,1.7,2.8,2.3,4.7,2.1c1.7-0.2,2.9-1.3,3.4-2.9h-2.2 c-0.4,1.3-2.4,1.5-3.5,0.6c-0.4-0.4-0.6-1.1-0.6-1.7H20C20,11.7,19.9,10.9,19.9,10.9z M13.5,10.6c0-1.6,2.3-2.7,3.5-1.4 c0.4,0.4,0.5,0.9,0.6,1.4H13.5L13.5,10.6z"/><rect x="13" y="4" width="5" height="1.4"/></svg>',
        "bell": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M17,15.5 L3,15.5 C2.99,14.61 3.79,13.34 4.1,12.51 C4.58,11.3 4.72,10.35 5.19,7.01 C5.54,4.53 5.89,3.2 7.28,2.16 C8.13,1.56 9.37,1.5 9.81,1.5 L9.96,1.5 C9.96,1.5 11.62,1.41 12.67,2.17 C14.08,3.2 14.42,4.54 14.77,7.02 C15.26,10.35 15.4,11.31 15.87,12.52 C16.2,13.34 17.01,14.61 17,15.5 L17,15.5 Z"/><path fill="none" stroke="#000" d="M12.39,16 C12.39,17.37 11.35,18.43 9.91,18.43 C8.48,18.43 7.42,17.37 7.42,16"/></svg>',
        "bold": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5,15.3 C5.66,15.3 5.9,15 5.9,14.53 L5.9,5.5 C5.9,4.92 5.56,4.7 5,4.7 L5,4 L8.95,4 C12.6,4 13.7,5.37 13.7,6.9 C13.7,7.87 13.14,9.17 10.86,9.59 L10.86,9.7 C13.25,9.86 14.29,11.28 14.3,12.54 C14.3,14.47 12.94,16 9,16 L5,16 L5,15.3 Z M9,9.3 C11.19,9.3 11.8,8.5 11.85,7 C11.85,5.65 11.3,4.8 9,4.8 L7.67,4.8 L7.67,9.3 L9,9.3 Z M9.185,15.22 C11.97,15 12.39,14 12.4,12.58 C12.4,11.15 11.39,10 9,10 L7.67,10 L7.67,15 L9.18,15 Z"/></svg>',
        "bolt": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4.74,20 L7.73,12 L3,12 L15.43,1 L12.32,9 L17.02,9 L4.74,20 L4.74,20 L4.74,20 Z M9.18,11 L7.1,16.39 L14.47,10 L10.86,10 L12.99,4.67 L5.61,11 L9.18,11 L9.18,11 L9.18,11 Z"/></svg>',
        "bookmark": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="5.5 1.5 15.5 1.5 15.5 17.5 10.5 12.5 5.5 17.5"/></svg>',
        "calendar": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M 2,3 2,17 18,17 18,3 2,3 Z M 17,16 3,16 3,8 17,8 17,16 Z M 17,7 3,7 3,4 17,4 17,7 Z"/><rect width="1" height="3" x="6" y="2"/><rect width="1" height="3" x="13" y="2"/></svg>',
        "camera": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10.8" r="3.8"/><path fill="none" stroke="#000" d="M1,4.5 C0.7,4.5 0.5,4.7 0.5,5 L0.5,17 C0.5,17.3 0.7,17.5 1,17.5 L19,17.5 C19.3,17.5 19.5,17.3 19.5,17 L19.5,5 C19.5,4.7 19.3,4.5 19,4.5 L13.5,4.5 L13.5,2.9 C13.5,2.6 13.3,2.5 13,2.5 L7,2.5 C6.7,2.5 6.5,2.6 6.5,2.9 L6.5,4.5 L1,4.5 L1,4.5 Z"/></svg>',
        "cart": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="7.3" cy="17.3" r="1.4"/><circle cx="13.3" cy="17.3" r="1.4"/><polyline fill="none" stroke="#000" points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg>',
        "check": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.1" points="4,10 8,15 17,4"/></svg>',
        "chevron-double-left": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="10 14 6 10 10 6"/><polyline fill="none" stroke="#000" stroke-width="1.03" points="14 14 10 10 14 6"/></svg>',
        "chevron-double-right": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="10 6 14 10 10 14"/><polyline fill="none" stroke="#000" stroke-width="1.03" points="6 6 10 10 6 14"/></svg>',
        "chevron-down": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="16 7 10 13 4 7"/></svg>',
        "chevron-left": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="13 16 7 10 13 4"/></svg>',
        "chevron-right": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="7 4 13 10 7 16"/></svg>',
        "chevron-up": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.03" points="4 13 10 7 16 13"/></svg>',
        "clock": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/><rect x="9" y="4" width="1" height="7"/><path fill="none" stroke="#000" stroke-width="1.1" d="M13.018,14.197 L9.445,10.625"/></svg>',
        "close": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.06" d="M16,16 L4,4"/><path fill="none" stroke="#000" stroke-width="1.06" d="M16,4 L4,16"/></svg>',
        "cloud-download": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M6.5,14.61 L3.75,14.61 C1.96,14.61 0.5,13.17 0.5,11.39 C0.5,9.76 1.72,8.41 3.3,8.2 C3.38,5.31 5.75,3 8.68,3 C11.19,3 13.31,4.71 13.89,7.02 C14.39,6.8 14.93,6.68 15.5,6.68 C17.71,6.68 19.5,8.45 19.5,10.64 C19.5,12.83 17.71,14.6 15.5,14.6 L12.5,14.6"/><polyline fill="none" stroke="#000" points="11.75 16 9.5 18.25 7.25 16"/><path fill="none" stroke="#000" d="M9.5,18 L9.5,9.5"/></svg>',
        "cloud-upload": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M6.5,14.61 L3.75,14.61 C1.96,14.61 0.5,13.17 0.5,11.39 C0.5,9.76 1.72,8.41 3.31,8.2 C3.38,5.31 5.75,3 8.68,3 C11.19,3 13.31,4.71 13.89,7.02 C14.39,6.8 14.93,6.68 15.5,6.68 C17.71,6.68 19.5,8.45 19.5,10.64 C19.5,12.83 17.71,14.6 15.5,14.6 L12.5,14.6"/><polyline fill="none" stroke="#000" points="7.25 11.75 9.5 9.5 11.75 11.75"/><path fill="none" stroke="#000" d="M9.5,18 L9.5,9.5"/></svg>',
        "code": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" stroke-width="1.01" points="13,4 19,10 13,16"/><polyline fill="none" stroke="#000" stroke-width="1.01" points="7,4 1,10 7,16"/></svg>',
        "cog": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" cx="9.997" cy="10" r="3.31"/><path fill="none" stroke="#000" d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z"/></svg>',
        "comment": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6,18.71 L6,14 L1,14 L1,1 L19,1 L19,14 L10.71,14 L6,18.71 L6,18.71 Z M2,13 L7,13 L7,16.29 L10.29,13 L18,13 L18,2 L2,2 L2,13 L2,13 Z"/></svg>',
        "commenting": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="1.5,1.5 18.5,1.5 18.5,13.5 10.5,13.5 6.5,17.5 6.5,13.5 1.5,13.5"/><circle cx="10" cy="8" r="1"/><circle cx="6" cy="8" r="1"/><circle cx="14" cy="8" r="1"/></svg>',
        "comments": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="2 0.5 19.5 0.5 19.5 13"/><path d="M5,19.71 L5,15 L0,15 L0,2 L18,2 L18,15 L9.71,15 L5,19.71 L5,19.71 L5,19.71 Z M1,14 L6,14 L6,17.29 L9.29,14 L17,14 L17,3 L1,3 L1,14 L1,14 L1,14 Z"/></svg>',
        "copy": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" x="3.5" y="2.5" width="12" height="16"/><polyline fill="none" stroke="#000" points="5 0.5 17.5 0.5 17.5 17"/></svg>',
        "credit-card": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" x="1.5" y="4.5" width="17" height="12"/><rect x="1" y="7" width="18" height="3"/></svg>',
        "database": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><ellipse fill="none" stroke="#000" cx="10" cy="4.64" rx="7.5" ry="3.14"/><path fill="none" stroke="#000" d="M17.5,8.11 C17.5,9.85 14.14,11.25 10,11.25 C5.86,11.25 2.5,9.84 2.5,8.11"/><path fill="none" stroke="#000" d="M17.5,11.25 C17.5,12.99 14.14,14.39 10,14.39 C5.86,14.39 2.5,12.98 2.5,11.25"/><path fill="none" stroke="#000" d="M17.49,4.64 L17.5,14.36 C17.5,16.1 14.14,17.5 10,17.5 C5.86,17.5 2.5,16.09 2.5,14.36 L2.5,4.64"/></svg>',
        "desktop": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="8" y="15" width="1" height="2"/><rect x="11" y="15" width="1" height="2"/><rect x="5" y="16" width="10" height="1"/><rect fill="none" stroke="#000" x="1.5" y="3.5" width="17" height="11"/></svg>',
        "discord": '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M16.074,4.361a14.243,14.243,0,0,0-3.61-1.134,10.61,10.61,0,0,0-.463.96,13.219,13.219,0,0,0-4,0,10.138,10.138,0,0,0-.468-.96A14.206,14.206,0,0,0,3.919,4.364,15.146,15.146,0,0,0,1.324,14.5a14.435,14.435,0,0,0,4.428,2.269A10.982,10.982,0,0,0,6.7,15.21a9.294,9.294,0,0,1-1.494-.727c.125-.093.248-.19.366-.289a10.212,10.212,0,0,0,8.854,0c.119.1.242.2.366.289a9.274,9.274,0,0,1-1.5.728,10.8,10.8,0,0,0,.948,1.562,14.419,14.419,0,0,0,4.431-2.27A15.128,15.128,0,0,0,16.074,4.361Zm-8.981,8.1a1.7,1.7,0,0,1-1.573-1.79A1.689,1.689,0,0,1,7.093,8.881a1.679,1.679,0,0,1,1.573,1.791A1.687,1.687,0,0,1,7.093,12.462Zm5.814,0a1.7,1.7,0,0,1-1.573-1.79,1.689,1.689,0,0,1,1.573-1.791,1.679,1.679,0,0,1,1.573,1.791A1.688,1.688,0,0,1,12.907,12.462Z"/></svg>',
        "download": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="14,10 9.5,14.5 5,10"/><rect x="3" y="17" width="13" height="1"/><line fill="none" stroke="#000" x1="9.5" y1="13.91" x2="9.5" y2="3"/></svg>',
        "dribbble": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.4" d="M1.3,8.9c0,0,5,0.1,8.6-1c1.4-0.4,2.6-0.9,4-1.9 c1.4-1.1,2.5-2.5,2.5-2.5"/><path fill="none" stroke="#000" stroke-width="1.4" d="M3.9,16.6c0,0,1.7-2.8,3.5-4.2 c1.8-1.3,4-2,5.7-2.2C16,10,19,10.6,19,10.6"/><path fill="none" stroke="#000" stroke-width="1.4" d="M6.9,1.6c0,0,3.3,4.6,4.2,6.8 c0.4,0.9,1.3,3.1,1.9,5.2c0.6,2,0.9,4.4,0.9,4.4"/><circle fill="none" stroke="#000" stroke-width="1.4" cx="10" cy="10" r="9"/></svg>',
        "etsy": '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M8,4.26C8,4.07,8,4,8.31,4h4.46c.79,0,1.22.67,1.53,1.91l.25,1h.76c.14-2.82.26-4,.26-4S13.65,3,12.52,3H6.81L3.75,2.92v.84l1,.2c.73.11.9.27,1,1,0,0,.06,2,.06,5.17s-.06,5.14-.06,5.14c0,.59-.23.81-1,.94l-1,.2v.84l3.06-.1h5.11c1.15,0,3.82.1,3.82.1,0-.7.45-3.88.51-4.22h-.73l-.76,1.69a2.25,2.25,0,0,1-2.45,1.47H9.4c-1,0-1.44-.4-1.44-1.24V10.44s2.16,0,2.86.06c.55,0,.85.19,1.06,1l.23,1H13L12.9,9.94,13,7.41h-.85l-.28,1.13c-.16.74-.28.84-1,1-1,.1-2.89.09-2.89.09Z"/></svg>',
        "expand": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="13 2 18 2 18 7 17 7 17 3 13 3"/><polygon points="2 13 3 13 3 17 7 17 7 18 2 18"/><path fill="none" stroke="#000" stroke-width="1.1" d="M11,9 L17,3"/><path fill="none" stroke="#000" stroke-width="1.1" d="M3,17 L9,11"/></svg>',
        "facebook": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M11,10h2.6l0.4-3H11V5.3c0-0.9,0.2-1.5,1.5-1.5H14V1.1c-0.3,0-1-0.1-2.1-0.1C9.6,1,8,2.4,8,5v2H5.5v3H8v8h3V10z"/></svg>',
        "file-edit": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M18.65,1.68 C18.41,1.45 18.109,1.33 17.81,1.33 C17.499,1.33 17.209,1.45 16.98,1.68 L8.92,9.76 L8,12.33 L10.55,11.41 L18.651,3.34 C19.12,2.87 19.12,2.15 18.65,1.68 L18.65,1.68 L18.65,1.68 Z"/><polyline fill="none" stroke="#000" points="16.5 8.482 16.5 18.5 3.5 18.5 3.5 1.5 14.211 1.5"/></svg>',
        "file-pdf": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" width="13" height="17" x="3.5" y="1.5"/><path d="M14.65 11.67c-.48.3-1.37-.19-1.79-.37a4.65 4.65 0 0 1 1.49.06c.35.1.36.28.3.31zm-6.3.06l.43-.79a14.7 14.7 0 0 0 .75-1.64 5.48 5.48 0 0 0 1.25 1.55l.2.15a16.36 16.36 0 0 0-2.63.73zM9.5 5.32c.2 0 .32.5.32.97a1.99 1.99 0 0 1-.23 1.04 5.05 5.05 0 0 1-.17-1.3s0-.71.08-.71zm-3.9 9a4.35 4.35 0 0 1 1.21-1.46l.24-.22a4.35 4.35 0 0 1-1.46 1.68zm9.23-3.3a2.05 2.05 0 0 0-1.32-.3 11.07 11.07 0 0 0-1.58.11 4.09 4.09 0 0 1-.74-.5 5.39 5.39 0 0 1-1.32-2.06 10.37 10.37 0 0 0 .28-2.62 1.83 1.83 0 0 0-.07-.25.57.57 0 0 0-.52-.4H9.4a.59.59 0 0 0-.6.38 6.95 6.95 0 0 0 .37 3.14c-.26.63-1 2.12-1 2.12-.3.58-.57 1.08-.82 1.5l-.8.44A3.11 3.11 0 0 0 5 14.16a.39.39 0 0 0 .15.42l.24.13c1.15.56 2.28-1.74 2.66-2.42a23.1 23.1 0 0 1 3.59-.85 4.56 4.56 0 0 0 2.91.8.5.5 0 0 0 .3-.21 1.1 1.1 0 0 0 .12-.75.84.84 0 0 0-.14-.25z"/></svg>',
        "file-text": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" width="13" height="17" x="3.5" y="1.5"/><line fill="none" stroke="#000" x1="6" x2="12" y1="12.5" y2="12.5"/><line fill="none" stroke="#000" x1="6" x2="14" y1="8.5" y2="8.5"/><line fill="none" stroke="#000" x1="6" x2="14" y1="6.5" y2="6.5"/><line fill="none" stroke="#000" x1="6" x2="14" y1="10.5" y2="10.5"/></svg>',
        "file": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" x="3.5" y="1.5" width="13" height="17"/></svg>',
        "flickr": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="5.5" cy="9.5" r="3.5"/><circle cx="14.5" cy="9.5" r="3.5"/></svg>',
        "folder": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="9.5 5.5 8.5 3.5 1.5 3.5 1.5 16.5 18.5 16.5 18.5 5.5"/></svg>',
        "forward": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.47,13.11 C4.02,10.02 6.27,7.85 9.04,6.61 C9.48,6.41 10.27,6.13 11,5.91 L11,2 L18.89,9 L11,16 L11,12.13 C9.25,12.47 7.58,13.19 6.02,14.25 C3.03,16.28 1.63,18.54 1.63,18.54 C1.63,18.54 1.38,15.28 2.47,13.11 L2.47,13.11 Z M5.3,13.53 C6.92,12.4 9.04,11.4 12,10.92 L12,13.63 L17.36,9 L12,4.25 L12,6.8 C11.71,6.86 10.86,7.02 9.67,7.49 C6.79,8.65 4.58,10.96 3.49,13.08 C3.18,13.7 2.68,14.87 2.49,16 C3.28,15.05 4.4,14.15 5.3,13.53 L5.3,13.53 Z"/></svg>',
        "foursquare": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.23,2 C15.96,2 16.4,2.41 16.5,2.86 C16.57,3.15 16.56,3.44 16.51,3.73 C16.46,4.04 14.86,11.72 14.75,12.03 C14.56,12.56 14.16,12.82 13.61,12.83 C13.03,12.84 11.09,12.51 10.69,13 C10.38,13.38 7.79,16.39 6.81,17.53 C6.61,17.76 6.4,17.96 6.08,17.99 C5.68,18.04 5.29,17.87 5.17,17.45 C5.12,17.28 5.1,17.09 5.1,16.91 C5.1,12.4 4.86,7.81 5.11,3.31 C5.17,2.5 5.81,2.12 6.53,2 L15.23,2 L15.23,2 Z M9.76,11.42 C9.94,11.19 10.17,11.1 10.45,11.1 L12.86,11.1 C13.12,11.1 13.31,10.94 13.36,10.69 C13.37,10.64 13.62,9.41 13.74,8.83 C13.81,8.52 13.53,8.28 13.27,8.28 C12.35,8.29 11.42,8.28 10.5,8.28 C9.84,8.28 9.83,7.69 9.82,7.21 C9.8,6.85 10.13,6.55 10.5,6.55 C11.59,6.56 12.67,6.55 13.76,6.55 C14.03,6.55 14.23,6.4 14.28,6.14 C14.34,5.87 14.67,4.29 14.67,4.29 C14.67,4.29 14.82,3.74 14.19,3.74 L7.34,3.74 C7,3.75 6.84,4.02 6.84,4.33 C6.84,7.58 6.85,14.95 6.85,14.99 C6.87,15 8.89,12.51 9.76,11.42 L9.76,11.42 Z"/></svg>',
        "future": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline points="19 2 18 2 18 6 14 6 14 7 19 7 19 2"/><path fill="none" stroke="#000" stroke-width="1.1" d="M18,6.548 C16.709,3.29 13.354,1 9.6,1 C4.6,1 0.6,5 0.6,10 C0.6,15 4.6,19 9.6,19 C14.6,19 18.6,15 18.6,10"/><rect x="9" y="4" width="1" height="7"/><path d="M13.018,14.197 L9.445,10.625" fill="none" stroke="#000" stroke-width="1.1"/></svg>',
        "git-branch": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.2" cx="7" cy="3" r="2"/><circle fill="none" stroke="#000" stroke-width="1.2" cx="14" cy="6" r="2"/><circle fill="none" stroke="#000" stroke-width="1.2" cx="7" cy="17" r="2"/><path fill="none" stroke="#000" stroke-width="2" d="M14,8 C14,10.41 12.43,10.87 10.56,11.25 C9.09,11.54 7,12.06 7,15 L7,5"/></svg>',
        "git-fork": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.2" cx="5.79" cy="2.79" r="1.79"/><circle fill="none" stroke="#000" stroke-width="1.2" cx="14.19" cy="2.79" r="1.79"/><circle fill="none" stroke="#000" stroke-width="1.2" cx="10.03" cy="16.79" r="1.79"/><path fill="none" stroke="#000" stroke-width="2" d="M5.79,4.57 L5.79,6.56 C5.79,9.19 10.03,10.22 10.03,13.31 C10.03,14.86 10.04,14.55 10.04,14.55 C10.04,14.37 10.04,14.86 10.04,13.31 C10.04,10.22 14.2,9.19 14.2,6.56 L14.2,4.57"/></svg>',
        "github-alt": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10,0.5 C4.75,0.5 0.5,4.76 0.5,10.01 C0.5,15.26 4.75,19.51 10,19.51 C15.24,19.51 19.5,15.26 19.5,10.01 C19.5,4.76 15.25,0.5 10,0.5 L10,0.5 Z M12.81,17.69 C12.81,17.69 12.81,17.7 12.79,17.69 C12.47,17.75 12.35,17.59 12.35,17.36 L12.35,16.17 C12.35,15.45 12.09,14.92 11.58,14.56 C12.2,14.51 12.77,14.39 13.26,14.21 C13.87,13.98 14.36,13.69 14.74,13.29 C15.42,12.59 15.76,11.55 15.76,10.17 C15.76,9.25 15.45,8.46 14.83,7.8 C15.1,7.08 15.07,6.29 14.75,5.44 L14.51,5.42 C14.34,5.4 14.06,5.46 13.67,5.61 C13.25,5.78 12.79,6.03 12.31,6.35 C11.55,6.16 10.81,6.05 10.09,6.05 C9.36,6.05 8.61,6.15 7.88,6.35 C7.28,5.96 6.75,5.68 6.26,5.54 C6.07,5.47 5.9,5.44 5.78,5.44 L5.42,5.44 C5.06,6.29 5.04,7.08 5.32,7.8 C4.7,8.46 4.4,9.25 4.4,10.17 C4.4,11.94 4.96,13.16 6.08,13.84 C6.53,14.13 7.05,14.32 7.69,14.43 C8.03,14.5 8.32,14.54 8.55,14.55 C8.07,14.89 7.82,15.42 7.82,16.16 L7.82,17.51 C7.8,17.69 7.7,17.8 7.51,17.8 C4.21,16.74 1.82,13.65 1.82,10.01 C1.82,5.5 5.49,1.83 10,1.83 C14.5,1.83 18.17,5.5 18.17,10.01 C18.18,13.53 15.94,16.54 12.81,17.69 L12.81,17.69 Z"/></svg>',
        "github": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10,1 C5.03,1 1,5.03 1,10 C1,13.98 3.58,17.35 7.16,18.54 C7.61,18.62 7.77,18.34 7.77,18.11 C7.77,17.9 7.76,17.33 7.76,16.58 C5.26,17.12 4.73,15.37 4.73,15.37 C4.32,14.33 3.73,14.05 3.73,14.05 C2.91,13.5 3.79,13.5 3.79,13.5 C4.69,13.56 5.17,14.43 5.17,14.43 C5.97,15.8 7.28,15.41 7.79,15.18 C7.87,14.6 8.1,14.2 8.36,13.98 C6.36,13.75 4.26,12.98 4.26,9.53 C4.26,8.55 4.61,7.74 5.19,7.11 C5.1,6.88 4.79,5.97 5.28,4.73 C5.28,4.73 6.04,4.49 7.75,5.65 C8.47,5.45 9.24,5.35 10,5.35 C10.76,5.35 11.53,5.45 12.25,5.65 C13.97,4.48 14.72,4.73 14.72,4.73 C15.21,5.97 14.9,6.88 14.81,7.11 C15.39,7.74 15.73,8.54 15.73,9.53 C15.73,12.99 13.63,13.75 11.62,13.97 C11.94,14.25 12.23,14.8 12.23,15.64 C12.23,16.84 12.22,17.81 12.22,18.11 C12.22,18.35 12.38,18.63 12.84,18.54 C16.42,17.35 19,13.98 19,10 C19,5.03 14.97,1 10,1 L10,1 Z"/></svg>',
        "gitter": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="3.5" y="1" width="1.531" height="11.471"/><rect x="7.324" y="4.059" width="1.529" height="15.294"/><rect x="11.148" y="4.059" width="1.527" height="15.294"/><rect x="14.971" y="4.059" width="1.529" height="8.412"/></svg>',
        "google": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.86,9.09 C18.46,12.12 17.14,16.05 13.81,17.56 C9.45,19.53 4.13,17.68 2.47,12.87 C0.68,7.68 4.22,2.42 9.5,2.03 C11.57,1.88 13.42,2.37 15.05,3.65 C15.22,3.78 15.37,3.93 15.61,4.14 C14.9,4.81 14.23,5.45 13.5,6.14 C12.27,5.08 10.84,4.72 9.28,4.98 C8.12,5.17 7.16,5.76 6.37,6.63 C4.88,8.27 4.62,10.86 5.76,12.82 C6.95,14.87 9.17,15.8 11.57,15.25 C13.27,14.87 14.76,13.33 14.89,11.75 L10.51,11.75 L10.51,9.09 L17.86,9.09 L17.86,9.09 Z"/></svg>',
        "grid": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="2" width="3" height="3"/><rect x="8" y="2" width="3" height="3"/><rect x="14" y="2" width="3" height="3"/><rect x="2" y="8" width="3" height="3"/><rect x="8" y="8" width="3" height="3"/><rect x="14" y="8" width="3" height="3"/><rect x="2" y="14" width="3" height="3"/><rect x="8" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>',
        "happy": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="13" cy="7" r="1"/><circle cx="7" cy="7" r="1"/><circle fill="none" stroke="#000" cx="10" cy="10" r="8.5"/><path fill="none" stroke="#000" d="M14.6,11.4 C13.9,13.3 12.1,14.5 10,14.5 C7.9,14.5 6.1,13.3 5.4,11.4"/></svg>',
        "hashtag": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.431,8 L15.661,7 L12.911,7 L13.831,3 L12.901,3 L11.98,7 L9.29,7 L10.21,3 L9.281,3 L8.361,7 L5.23,7 L5,8 L8.13,8 L7.21,12 L4.23,12 L4,13 L6.98,13 L6.061,17 L6.991,17 L7.911,13 L10.601,13 L9.681,17 L10.611,17 L11.531,13 L14.431,13 L14.661,12 L11.76,12 L12.681,8 L15.431,8 Z M10.831,12 L8.141,12 L9.061,8 L11.75,8 L10.831,12 Z"/></svg>',
        "heart": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.03" d="M10,4 C10,4 8.1,2 5.74,2 C3.38,2 1,3.55 1,6.73 C1,8.84 2.67,10.44 2.67,10.44 L10,18 L17.33,10.44 C17.33,10.44 19,8.84 19,6.73 C19,3.55 16.62,2 14.26,2 C11.9,2 10,4 10,4 L10,4 Z"/></svg>',
        "history": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="#000" points="1 2 2 2 2 6 6 6 6 7 1 7 1 2"/><path fill="none" stroke="#000" stroke-width="1.1" d="M2.1,6.548 C3.391,3.29 6.746,1 10.5,1 C15.5,1 19.5,5 19.5,10 C19.5,15 15.5,19 10.5,19 C5.5,19 1.5,15 1.5,10"/><rect x="9" y="4" width="1" height="7"/><path fill="none" stroke="#000" stroke-width="1.1" d="M13.018,14.197 L9.445,10.625"/></svg>',
        "home": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="18.65 11.35 10 2.71 1.35 11.35 0.65 10.65 10 1.29 19.35 10.65"/><polygon points="15 4 18 4 18 7 17 7 17 5 15 5"/><polygon points="3 11 4 11 4 18 7 18 7 12 12 12 12 18 16 18 16 11 17 11 17 19 11 19 11 13 8 13 8 19 3 19"/></svg>',
        "image": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="16.1" cy="6.1" r="1.1"/><rect fill="none" stroke="#000" x=".5" y="2.5" width="19" height="15"/><polyline fill="none" stroke="#000" stroke-width="1.01" points="4,13 8,9 13,14"/><polyline fill="none" stroke="#000" stroke-width="1.01" points="11,12 12.5,10.5 16,14"/></svg>',
        "info": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M12.13,11.59 C11.97,12.84 10.35,14.12 9.1,14.16 C6.17,14.2 9.89,9.46 8.74,8.37 C9.3,8.16 10.62,7.83 10.62,8.81 C10.62,9.63 10.12,10.55 9.88,11.32 C8.66,15.16 12.13,11.15 12.14,11.18 C12.16,11.21 12.16,11.35 12.13,11.59 C12.08,11.95 12.16,11.35 12.13,11.59 L12.13,11.59 Z M11.56,5.67 C11.56,6.67 9.36,7.15 9.36,6.03 C9.36,5 11.56,4.54 11.56,5.67 L11.56,5.67 Z"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/></svg>',
        "instagram": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M13.55,1H6.46C3.45,1,1,3.44,1,6.44v7.12c0,3,2.45,5.44,5.46,5.44h7.08c3.02,0,5.46-2.44,5.46-5.44V6.44 C19.01,3.44,16.56,1,13.55,1z M17.5,14c0,1.93-1.57,3.5-3.5,3.5H6c-1.93,0-3.5-1.57-3.5-3.5V6c0-1.93,1.57-3.5,3.5-3.5h8 c1.93,0,3.5,1.57,3.5,3.5V14z"/><circle cx="14.87" cy="5.26" r="1.09"/><path d="M10.03,5.45c-2.55,0-4.63,2.06-4.63,4.6c0,2.55,2.07,4.61,4.63,4.61c2.56,0,4.63-2.061,4.63-4.61 C14.65,7.51,12.58,5.45,10.03,5.45L10.03,5.45L10.03,5.45z M10.08,13c-1.66,0-3-1.34-3-2.99c0-1.65,1.34-2.99,3-2.99s3,1.34,3,2.99 C13.08,11.66,11.74,13,10.08,13L10.08,13L10.08,13z"/></svg>',
        "italic": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M12.63,5.48 L10.15,14.52 C10,15.08 10.37,15.25 11.92,15.3 L11.72,16 L6,16 L6.2,15.31 C7.78,15.26 8.19,15.09 8.34,14.53 L10.82,5.49 C10.97,4.92 10.63,4.76 9.09,4.71 L9.28,4 L15,4 L14.81,4.69 C13.23,4.75 12.78,4.91 12.63,5.48 L12.63,5.48 Z"/></svg>',
        "joomla": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M7.8,13.4l1.7-1.7L5.9,8c-0.6-0.5-0.6-1.5,0-2c0.6-0.6,1.4-0.6,2,0l1.7-1.7c-1-1-2.3-1.3-3.6-1C5.8,2.2,4.8,1.4,3.7,1.4 c-1.3,0-2.3,1-2.3,2.3c0,1.1,0.8,2,1.8,2.3c-0.4,1.3-0.1,2.8,1,3.8L7.8,13.4L7.8,13.4z"/><path d="M10.2,4.3c1-1,2.5-1.4,3.8-1c0.2-1.1,1.1-2,2.3-2c1.3,0,2.3,1,2.3,2.3c0,1.2-0.9,2.2-2,2.3c0.4,1.3,0,2.8-1,3.8L13.9,8 c0.6-0.5,0.6-1.5,0-2c-0.5-0.6-1.5-0.6-2,0L8.2,9.7L6.5,8"/><path d="M14.1,16.8c-1.3,0.4-2.8,0.1-3.8-1l1.7-1.7c0.6,0.6,1.5,0.6,2,0c0.5-0.6,0.6-1.5,0-2l-3.7-3.7L12,6.7l3.7,3.7 c1,1,1.3,2.4,1,3.6c1.1,0.2,2,1.1,2,2.3c0,1.3-1,2.3-2.3,2.3C15.2,18.6,14.3,17.8,14.1,16.8"/><path d="M13.2,12.2l-3.7,3.7c-1,1-2.4,1.3-3.6,1c-0.2,1-1.2,1.8-2.2,1.8c-1.3,0-2.3-1-2.3-2.3c0-1.1,0.8-2,1.8-2.3 c-0.3-1.3,0-2.7,1-3.7l1.7,1.7c-0.6,0.6-0.6,1.5,0,2c0.6,0.6,1.4,0.6,2,0l3.7-3.7"/></svg>',
        "laptop": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect y="16" width="20" height="1"/><rect fill="none" stroke="#000" x="2.5" y="4.5" width="15" height="10"/></svg>',
        "lifesaver": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10,0.5 C4.76,0.5 0.5,4.76 0.5,10 C0.5,15.24 4.76,19.5 10,19.5 C15.24,19.5 19.5,15.24 19.5,10 C19.5,4.76 15.24,0.5 10,0.5 L10,0.5 Z M10,1.5 C11.49,1.5 12.89,1.88 14.11,2.56 L11.85,4.82 C11.27,4.61 10.65,4.5 10,4.5 C9.21,4.5 8.47,4.67 7.79,4.96 L5.58,2.75 C6.87,1.95 8.38,1.5 10,1.5 L10,1.5 Z M4.96,7.8 C4.67,8.48 4.5,9.21 4.5,10 C4.5,10.65 4.61,11.27 4.83,11.85 L2.56,14.11 C1.88,12.89 1.5,11.49 1.5,10 C1.5,8.38 1.95,6.87 2.75,5.58 L4.96,7.79 L4.96,7.8 L4.96,7.8 Z M10,18.5 C8.25,18.5 6.62,17.97 5.27,17.06 L7.46,14.87 C8.22,15.27 9.08,15.5 10,15.5 C10.79,15.5 11.53,15.33 12.21,15.04 L14.42,17.25 C13.13,18.05 11.62,18.5 10,18.5 L10,18.5 Z M10,14.5 C7.52,14.5 5.5,12.48 5.5,10 C5.5,7.52 7.52,5.5 10,5.5 C12.48,5.5 14.5,7.52 14.5,10 C14.5,12.48 12.48,14.5 10,14.5 L10,14.5 Z M15.04,12.21 C15.33,11.53 15.5,10.79 15.5,10 C15.5,9.08 15.27,8.22 14.87,7.46 L17.06,5.27 C17.97,6.62 18.5,8.25 18.5,10 C18.5,11.62 18.05,13.13 17.25,14.42 L15.04,12.21 L15.04,12.21 Z"/></svg>',
        "link": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M10.625,12.375 L7.525,15.475 C6.825,16.175 5.925,16.175 5.225,15.475 L4.525,14.775 C3.825,14.074 3.825,13.175 4.525,12.475 L7.625,9.375"/><path fill="none" stroke="#000" stroke-width="1.1" d="M9.325,7.375 L12.425,4.275 C13.125,3.575 14.025,3.575 14.724,4.275 L15.425,4.975 C16.125,5.675 16.125,6.575 15.425,7.275 L12.325,10.375"/><path fill="none" stroke="#000" stroke-width="1.1" d="M7.925,11.875 L11.925,7.975"/></svg>',
        "linkedin": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5.77,17.89 L5.77,7.17 L2.21,7.17 L2.21,17.89 L5.77,17.89 L5.77,17.89 Z M3.99,5.71 C5.23,5.71 6.01,4.89 6.01,3.86 C5.99,2.8 5.24,2 4.02,2 C2.8,2 2,2.8 2,3.85 C2,4.88 2.77,5.7 3.97,5.7 L3.99,5.7 L3.99,5.71 L3.99,5.71 Z"/><path d="M7.75,17.89 L11.31,17.89 L11.31,11.9 C11.31,11.58 11.33,11.26 11.43,11.03 C11.69,10.39 12.27,9.73 13.26,9.73 C14.55,9.73 15.06,10.71 15.06,12.15 L15.06,17.89 L18.62,17.89 L18.62,11.74 C18.62,8.45 16.86,6.92 14.52,6.92 C12.6,6.92 11.75,7.99 11.28,8.73 L11.3,8.73 L11.3,7.17 L7.75,7.17 C7.79,8.17 7.75,17.89 7.75,17.89 L7.75,17.89 L7.75,17.89 Z"/></svg>',
        "list": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="6" y="4" width="12" height="1"/><rect x="6" y="9" width="12" height="1"/><rect x="6" y="14" width="12" height="1"/><rect x="2" y="4" width="2" height="1"/><rect x="2" y="9" width="2" height="1"/><rect x="2" y="14" width="2" height="1"/></svg>',
        "location": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.01" d="M10,0.5 C6.41,0.5 3.5,3.39 3.5,6.98 C3.5,11.83 10,19 10,19 C10,19 16.5,11.83 16.5,6.98 C16.5,3.39 13.59,0.5 10,0.5 L10,0.5 Z"/><circle fill="none" stroke="#000" cx="10" cy="6.8" r="2.3"/></svg>',
        "lock": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" height="10" width="13" y="8.5" x="3.5"/><path fill="none" stroke="#000" d="M6.5,8 L6.5,4.88 C6.5,3.01 8.07,1.5 10,1.5 C11.93,1.5 13.5,3.01 13.5,4.88 L13.5,8"/></svg>',
        "mail": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="1.4,6.5 10,11 18.6,6.5"/><path d="M 1,4 1,16 19,16 19,4 1,4 Z M 18,15 2,15 2,5 18,5 18,15 Z"/></svg>',
        "menu": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="2" y="4" width="16" height="1"/><rect x="2" y="9" width="16" height="1"/><rect x="2" y="14" width="16" height="1"/></svg>',
        "microphone": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" x1="10" x2="10" y1="16.44" y2="18.5"/><line fill="none" stroke="#000" x1="7" x2="13" y1="18.5" y2="18.5"/><path fill="none" stroke="#000" stroke-width="1.1" d="M13.5 4.89v5.87a3.5 3.5 0 0 1-7 0V4.89a3.5 3.5 0 0 1 7 0z"/><path fill="none" stroke="#000" stroke-width="1.1" d="M15.5 10.36V11a5.5 5.5 0 0 1-11 0v-.6"/></svg>',
        "minus-circle": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9.5" cy="9.5" r="9"/><line fill="none" stroke="#000" x1="5" y1="9.5" x2="14" y2="9.5"/></svg>',
        "minus": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect height="1" width="18" y="9" x="1"/></svg>',
        "more-vertical": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="3" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="10" cy="17" r="2"/></svg>',
        "more": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="3" cy="10" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="17" cy="10" r="2"/></svg>',
        "move": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="4,5 1,5 1,9 2,9 2,6 4,6"/><polygon points="1,16 2,16 2,18 4,18 4,19 1,19"/><polygon points="14,16 14,19 11,19 11,18 13,18 13,16"/><rect fill="none" stroke="#000" x="5.5" y="1.5" width="13" height="13"/><rect x="1" y="11" width="1" height="3"/><rect x="6" y="18" width="3" height="1"/></svg>',
        "nut": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="2.5,5.7 10,1.3 17.5,5.7 17.5,14.3 10,18.7 2.5,14.3"/><circle fill="none" stroke="#000" cx="10" cy="10" r="3.5"/></svg>',
        "pagekit": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="3,1 17,1 17,16 10,16 10,13 14,13 14,4 6,4 6,16 10,16 10,19 3,19"/></svg>',
        "paint-bucket": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.21,1 L0,11.21 L8.1,19.31 L18.31,9.1 L10.21,1 L10.21,1 Z M16.89,9.1 L15,11 L1.7,11 L10.21,2.42 L16.89,9.1 Z"/><path fill="none" stroke="#000" stroke-width="1.1" d="M6.42,2.33 L11.7,7.61"/><path d="M18.49,12 C18.49,12 20,14.06 20,15.36 C20,16.28 19.24,17 18.49,17 L18.49,17 C17.74,17 17,16.28 17,15.36 C17,14.06 18.49,12 18.49,12 L18.49,12 Z"/></svg>',
        "pencil": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M17.25,6.01 L7.12,16.1 L3.82,17.2 L5.02,13.9 L15.12,3.88 C15.71,3.29 16.66,3.29 17.25,3.88 C17.83,4.47 17.83,5.42 17.25,6.01 L17.25,6.01 Z"/><path fill="none" stroke="#000" d="M15.98,7.268 L13.851,5.148"/></svg>',
        "phone-landscape": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M17,5.5 C17.8,5.5 18.5,6.2 18.5,7 L18.5,14 C18.5,14.8 17.8,15.5 17,15.5 L3,15.5 C2.2,15.5 1.5,14.8 1.5,14 L1.5,7 C1.5,6.2 2.2,5.5 3,5.5 L17,5.5 L17,5.5 L17,5.5 Z"/><circle cx="3.8" cy="10.5" r=".8"/></svg>',
        "phone": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M15.5,17 C15.5,17.8 14.8,18.5 14,18.5 L7,18.5 C6.2,18.5 5.5,17.8 5.5,17 L5.5,3 C5.5,2.2 6.2,1.5 7,1.5 L14,1.5 C14.8,1.5 15.5,2.2 15.5,3 L15.5,17 L15.5,17 L15.5,17 Z"/><circle cx="10.5" cy="16.5" r=".8"/></svg>',
        "pinterest": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.21,1 C5.5,1 3,4.16 3,7.61 C3,9.21 3.85,11.2 5.22,11.84 C5.43,11.94 5.54,11.89 5.58,11.69 C5.62,11.54 5.8,10.8 5.88,10.45 C5.91,10.34 5.89,10.24 5.8,10.14 C5.36,9.59 5,8.58 5,7.65 C5,5.24 6.82,2.91 9.93,2.91 C12.61,2.91 14.49,4.74 14.49,7.35 C14.49,10.3 13,12.35 11.06,12.35 C9.99,12.35 9.19,11.47 9.44,10.38 C9.75,9.08 10.35,7.68 10.35,6.75 C10.35,5.91 9.9,5.21 8.97,5.21 C7.87,5.21 6.99,6.34 6.99,7.86 C6.99,8.83 7.32,9.48 7.32,9.48 C7.32,9.48 6.24,14.06 6.04,14.91 C5.7,16.35 6.08,18.7 6.12,18.9 C6.14,19.01 6.26,19.05 6.33,18.95 C6.44,18.81 7.74,16.85 8.11,15.44 C8.24,14.93 8.79,12.84 8.79,12.84 C9.15,13.52 10.19,14.09 11.29,14.09 C14.58,14.09 16.96,11.06 16.96,7.3 C16.94,3.7 14,1 10.21,1"/></svg>',
        "play-circle": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" stroke-width="1.1" points="8.5 7 13.5 10 8.5 13"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/></svg>',
        "play": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="6.5,5 14.5,10 6.5,15"/></svg>',
        "plus-circle": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9.5" cy="9.5" r="9"/><line fill="none" stroke="#000" x1="9.5" y1="5" x2="9.5" y2="14"/><line fill="none" stroke="#000" x1="5" y1="9.5" x2="14" y2="9.5"/></svg>',
        "plus": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="9" y="1" width="1" height="17"/><rect x="1" y="9" width="17" height="1"/></svg>',
        "print": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="4.5 13.5 1.5 13.5 1.5 6.5 18.5 6.5 18.5 13.5 15.5 13.5"/><polyline fill="none" stroke="#000" points="15.5 6.5 15.5 2.5 4.5 2.5 4.5 6.5"/><rect fill="none" stroke="#000" width="11" height="6" x="4.5" y="11.5"/><rect width="8" height="1" x="6" y="13"/><rect width="8" height="1" x="6" y="15"/></svg>',
        "pull": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="6.85,8 9.5,10.6 12.15,8 12.85,8.7 9.5,12 6.15,8.7"/><line fill="none" stroke="#000" x1="9.5" y1="11" x2="9.5" y2="2"/><polyline fill="none" stroke="#000" points="6,5.5 3.5,5.5 3.5,18.5 15.5,18.5 15.5,5.5 13,5.5"/></svg>',
        "push": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="12.15,4 9.5,1.4 6.85,4 6.15,3.3 9.5,0 12.85,3.3"/><line fill="none" stroke="#000" x1="9.5" y1="10" x2="9.5" y2="1"/><polyline fill="none" stroke="#000" points="6 5.5 3.5 5.5 3.5 18.5 15.5 18.5 15.5 5.5 13 5.5"/></svg>',
        "question": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/><circle cx="10.44" cy="14.42" r="1.05"/><path fill="none" stroke="#000" stroke-width="1.2" d="M8.17,7.79 C8.17,4.75 12.72,4.73 12.72,7.72 C12.72,8.67 11.81,9.15 11.23,9.75 C10.75,10.24 10.51,10.73 10.45,11.4 C10.44,11.53 10.43,11.64 10.43,11.75"/></svg>',
        "quote-right": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.27,7.79 C17.27,9.45 16.97,10.43 15.99,12.02 C14.98,13.64 13,15.23 11.56,15.97 L11.1,15.08 C12.34,14.2 13.14,13.51 14.02,11.82 C14.27,11.34 14.41,10.92 14.49,10.54 C14.3,10.58 14.09,10.6 13.88,10.6 C12.06,10.6 10.59,9.12 10.59,7.3 C10.59,5.48 12.06,4 13.88,4 C15.39,4 16.67,5.02 17.05,6.42 C17.19,6.82 17.27,7.27 17.27,7.79 L17.27,7.79 Z"/><path d="M8.68,7.79 C8.68,9.45 8.38,10.43 7.4,12.02 C6.39,13.64 4.41,15.23 2.97,15.97 L2.51,15.08 C3.75,14.2 4.55,13.51 5.43,11.82 C5.68,11.34 5.82,10.92 5.9,10.54 C5.71,10.58 5.5,10.6 5.29,10.6 C3.47,10.6 2,9.12 2,7.3 C2,5.48 3.47,4 5.29,4 C6.8,4 8.08,5.02 8.46,6.42 C8.6,6.82 8.68,7.27 8.68,7.79 L8.68,7.79 Z"/></svg>',
        "receiver": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.01" d="M6.189,13.611C8.134,15.525 11.097,18.239 13.867,18.257C16.47,18.275 18.2,16.241 18.2,16.241L14.509,12.551L11.539,13.639L6.189,8.29L7.313,5.355L3.76,1.8C3.76,1.8 1.732,3.537 1.7,6.092C1.667,8.809 4.347,11.738 6.189,13.611"/></svg>',
        "reddit": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M19 9.05a2.56 2.56 0 0 0-2.56-2.56 2.59 2.59 0 0 0-1.88.82 10.63 10.63 0 0 0-4.14-1v-.08c.58-1.62 1.58-3.89 2.7-4.1.38-.08.77.12 1.19.57a1.15 1.15 0 0 0-.06.37 1.48 1.48 0 1 0 1.51-1.45 1.43 1.43 0 0 0-.76.19A2.29 2.29 0 0 0 12.91 1c-2.11.43-3.39 4.38-3.63 5.19 0 0 0 .11-.06.11a10.65 10.65 0 0 0-3.75 1A2.56 2.56 0 0 0 1 9.05a2.42 2.42 0 0 0 .72 1.76A5.18 5.18 0 0 0 1.24 13c0 3.66 3.92 6.64 8.73 6.64s8.74-3 8.74-6.64a5.23 5.23 0 0 0-.46-2.13A2.58 2.58 0 0 0 19 9.05zm-16.88 0a1.44 1.44 0 0 1 2.27-1.19 7.68 7.68 0 0 0-2.07 1.91 1.33 1.33 0 0 1-.2-.72zM10 18.4c-4.17 0-7.55-2.4-7.55-5.4S5.83 7.53 10 7.53 17.5 10 17.5 13s-3.38 5.4-7.5 5.4zm7.69-8.61a7.62 7.62 0 0 0-2.09-1.91 1.41 1.41 0 0 1 .84-.28 1.47 1.47 0 0 1 1.44 1.45 1.34 1.34 0 0 1-.21.72z"/><path d="M6.69 12.58a1.39 1.39 0 1 1 1.39-1.39 1.38 1.38 0 0 1-1.38 1.39z"/><path d="M14.26 11.2a1.39 1.39 0 1 1-1.39-1.39 1.39 1.39 0 0 1 1.39 1.39z"/><path d="M13.09 14.88a.54.54 0 0 1-.09.77 5.3 5.3 0 0 1-3.26 1.19 5.61 5.61 0 0 1-3.4-1.22.55.55 0 1 1 .73-.83 4.09 4.09 0 0 0 5.25 0 .56.56 0 0 1 .77.09z"/></svg>',
        "refresh": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M17.08,11.15 C17.09,11.31 17.1,11.47 17.1,11.64 C17.1,15.53 13.94,18.69 10.05,18.69 C6.16,18.68 3,15.53 3,11.63 C3,7.74 6.16,4.58 10.05,4.58 C10.9,4.58 11.71,4.73 12.46,5"/><polyline fill="none" stroke="#000" points="9.9 2 12.79 4.89 9.79 7.9"/></svg>',
        "reply": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.7,13.11 C16.12,10.02 13.84,7.85 11.02,6.61 C10.57,6.41 9.75,6.13 9,5.91 L9,2 L1,9 L9,16 L9,12.13 C10.78,12.47 12.5,13.19 14.09,14.25 C17.13,16.28 18.56,18.54 18.56,18.54 C18.56,18.54 18.81,15.28 17.7,13.11 L17.7,13.11 Z M14.82,13.53 C13.17,12.4 11.01,11.4 8,10.92 L8,13.63 L2.55,9 L8,4.25 L8,6.8 C8.3,6.86 9.16,7.02 10.37,7.49 C13.3,8.65 15.54,10.96 16.65,13.08 C16.97,13.7 17.48,14.86 17.68,16 C16.87,15.05 15.73,14.15 14.82,13.53 L14.82,13.53 Z"/></svg>',
        "rss": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="3.12" cy="16.8" r="1.85"/><path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,8.2 C1.78,8.18 2.06,8.16 2.35,8.16 C7.57,8.16 11.81,12.37 11.81,17.57 C11.81,17.89 11.79,18.19 11.76,18.5"/><path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,2.52 C1.78,2.51 2.06,2.5 2.35,2.5 C10.72,2.5 17.5,9.24 17.5,17.57 C17.5,17.89 17.49,18.19 17.47,18.5"/></svg>',
        "search": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"/><path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"/></svg>',
        "server": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="1" height="2"/><rect x="5" y="3" width="1" height="2"/><rect x="7" y="3" width="1" height="2"/><rect x="16" y="3" width="1" height="1"/><rect x="16" y="10" width="1" height="1"/><circle fill="none" stroke="#000" cx="9.9" cy="17.4" r="1.4"/><rect x="3" y="10" width="1" height="2"/><rect x="5" y="10" width="1" height="2"/><rect x="9.5" y="14" width="1" height="2"/><rect x="3" y="17" width="6" height="1"/><rect x="11" y="17" width="6" height="1"/><rect fill="none" stroke="#000" x="1.5" y="1.5" width="17" height="5"/><rect fill="none" stroke="#000" x="1.5" y="8.5" width="17" height="5"/></svg>',
        "settings": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><ellipse fill="none" stroke="#000" cx="6.11" cy="3.55" rx="2.11" ry="2.15"/><ellipse fill="none" stroke="#000" cx="6.11" cy="15.55" rx="2.11" ry="2.15"/><circle fill="none" stroke="#000" cx="13.15" cy="9.55" r="2.15"/><rect x="1" y="3" width="3" height="1"/><rect x="10" y="3" width="8" height="1"/><rect x="1" y="9" width="8" height="1"/><rect x="15" y="9" width="3" height="1"/><rect x="1" y="15" width="3" height="1"/><rect x="10" y="15" width="8" height="1"/></svg>',
        "shrink": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="11 4 12 4 12 8 16 8 16 9 11 9"/><polygon points="4 11 9 11 9 16 8 16 8 12 4 12"/><path fill="none" stroke="#000" stroke-width="1.1" d="M12,8 L18,2"/><path fill="none" stroke="#000" stroke-width="1.1" d="M2,18 L8,12"/></svg>',
        "sign-in": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="7 2 17 2 17 17 7 17 7 16 16 16 16 3 7 3"/><polygon points="9.1 13.4 8.5 12.8 11.28 10 4 10 4 9 11.28 9 8.5 6.2 9.1 5.62 13 9.5"/></svg>',
        "sign-out": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="13.1 13.4 12.5 12.8 15.28 10 8 10 8 9 15.28 9 12.5 6.2 13.1 5.62 17 9.5"/><polygon points="13 2 3 2 3 17 13 17 13 16 4 16 4 3 13 3"/></svg>',
        "social": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><line fill="none" stroke="#000" stroke-width="1.1" x1="13.4" y1="14" x2="6.3" y2="10.7"/><line fill="none" stroke="#000" stroke-width="1.1" x1="13.5" y1="5.5" x2="6.5" y2="8.8"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="15.5" cy="4.6" r="2.3"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="15.5" cy="14.8" r="2.3"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="4.5" cy="9.8" r="2.3"/></svg>',
        "soundcloud": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.2,9.4c-0.4,0-0.8,0.1-1.101,0.2c-0.199-2.5-2.399-4.5-5-4.5c-0.6,0-1.2,0.1-1.7,0.3C9.2,5.5,9.1,5.6,9.1,5.6V15h8 c1.601,0,2.801-1.2,2.801-2.8C20,10.7,18.7,9.4,17.2,9.4L17.2,9.4z"/><rect x="6" y="6.5" width="1.5" height="8.5"/><rect x="3" y="8" width="1.5" height="7"/><rect y="10" width="1.5" height="5"/></svg>',
        "star": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" stroke-width="1.01" points="10 2 12.63 7.27 18.5 8.12 14.25 12.22 15.25 18 10 15.27 4.75 18 5.75 12.22 1.5 8.12 7.37 7.27"/></svg>',
        "strikethrough": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6,13.02 L6.65,13.02 C7.64,15.16 8.86,16.12 10.41,16.12 C12.22,16.12 12.92,14.93 12.92,13.89 C12.92,12.55 11.99,12.03 9.74,11.23 C8.05,10.64 6.23,10.11 6.23,7.83 C6.23,5.5 8.09,4.09 10.4,4.09 C11.44,4.09 12.13,4.31 12.72,4.54 L13.33,4 L13.81,4 L13.81,7.59 L13.16,7.59 C12.55,5.88 11.52,4.89 10.07,4.89 C8.84,4.89 7.89,5.69 7.89,7.03 C7.89,8.29 8.89,8.78 10.88,9.45 C12.57,10.03 14.38,10.6 14.38,12.91 C14.38,14.75 13.27,16.93 10.18,16.93 C9.18,16.93 8.17,16.69 7.46,16.39 L6.52,17 L6,17 L6,13.02 L6,13.02 Z"/><rect x="3" y="10" width="15" height="1"/></svg>',
        "table": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="3" width="18" height="1"/><rect x="1" y="7" width="18" height="1"/><rect x="1" y="11" width="18" height="1"/><rect x="1" y="15" width="18" height="1"/></svg>',
        "tablet-landscape": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M1.5,5 C1.5,4.2 2.2,3.5 3,3.5 L17,3.5 C17.8,3.5 18.5,4.2 18.5,5 L18.5,16 C18.5,16.8 17.8,17.5 17,17.5 L3,17.5 C2.2,17.5 1.5,16.8 1.5,16 L1.5,5 L1.5,5 L1.5,5 Z"/><circle cx="3.7" cy="10.5" r=".8"/></svg>',
        "tablet": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M5,18.5 C4.2,18.5 3.5,17.8 3.5,17 L3.5,3 C3.5,2.2 4.2,1.5 5,1.5 L16,1.5 C16.8,1.5 17.5,2.2 17.5,3 L17.5,17 C17.5,17.8 16.8,18.5 16,18.5 L5,18.5 L5,18.5 L5,18.5 Z"/><circle cx="10.5" cy="16.3" r=".8"/></svg>',
        "tag": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" stroke-width="1.1" d="M17.5,3.71 L17.5,7.72 C17.5,7.96 17.4,8.2 17.21,8.39 L8.39,17.2 C7.99,17.6 7.33,17.6 6.93,17.2 L2.8,13.07 C2.4,12.67 2.4,12.01 2.8,11.61 L11.61,2.8 C11.81,2.6 12.08,2.5 12.34,2.5 L16.19,2.5 C16.52,2.5 16.86,2.63 17.11,2.88 C17.35,3.11 17.48,3.4 17.5,3.71 L17.5,3.71 Z"/><circle cx="14" cy="6" r="1"/></svg>',
        "thumbnails": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" x="3.5" y="3.5" width="5" height="5"/><rect fill="none" stroke="#000" x="11.5" y="3.5" width="5" height="5"/><rect fill="none" stroke="#000" x="11.5" y="11.5" width="5" height="5"/><rect fill="none" stroke="#000" x="3.5" y="11.5" width="5" height="5"/></svg>',
        "tiktok": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.24,6V8.82a6.79,6.79,0,0,1-4-1.28v5.81A5.26,5.26,0,1,1,8,8.1a4.36,4.36,0,0,1,.72.05v2.9A2.57,2.57,0,0,0,7.64,11a2.4,2.4,0,1,0,2.77,2.38V2h2.86a4,4,0,0,0,1.84,3.38A4,4,0,0,0,17.24,6Z"/></svg>',
        "trash": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="6.5 3 6.5 1.5 13.5 1.5 13.5 3"/><polyline fill="none" stroke="#000" points="4.5 4 4.5 18.5 15.5 18.5 15.5 4"/><rect x="8" y="7" width="1" height="9"/><rect x="11" y="7" width="1" height="9"/><rect x="2" y="3" width="16" height="1"/></svg>',
        "triangle-down": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="5 7 15 7 10 12"/></svg>',
        "triangle-left": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="12 5 7 10 12 15"/></svg>',
        "triangle-right": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="8 5 13 10 8 15"/></svg>',
        "triangle-up": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="5 13 10 8 15 13"/></svg>',
        "tripadvisor": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M19.021,7.866C19.256,6.862,20,5.854,20,5.854h-3.346C14.781,4.641,12.504,4,9.98,4C7.363,4,4.999,4.651,3.135,5.876H0	c0,0,0.738,0.987,0.976,1.988c-0.611,0.837-0.973,1.852-0.973,2.964c0,2.763,2.249,5.009,5.011,5.009	c1.576,0,2.976-0.737,3.901-1.879l1.063,1.599l1.075-1.615c0.475,0.611,1.1,1.111,1.838,1.451c1.213,0.547,2.574,0.612,3.825,0.15	c2.589-0.963,3.913-3.852,2.964-6.439c-0.175-0.463-0.4-0.876-0.675-1.238H19.021z M16.38,14.594	c-1.002,0.371-2.088,0.328-3.06-0.119c-0.688-0.317-1.252-0.817-1.657-1.438c-0.164-0.25-0.313-0.52-0.417-0.811	c-0.124-0.328-0.186-0.668-0.217-1.014c-0.063-0.689,0.037-1.396,0.339-2.043c0.448-0.971,1.251-1.71,2.25-2.079	c2.075-0.765,4.375,0.3,5.14,2.366c0.762,2.066-0.301,4.37-2.363,5.134L16.38,14.594L16.38,14.594z M8.322,13.066	c-0.72,1.059-1.935,1.76-3.309,1.76c-2.207,0-4.001-1.797-4.001-3.996c0-2.203,1.795-4.002,4.001-4.002	c2.204,0,3.999,1.8,3.999,4.002c0,0.137-0.024,0.261-0.04,0.396c-0.067,0.678-0.284,1.313-0.648,1.853v-0.013H8.322z M2.472,10.775	c0,1.367,1.112,2.479,2.476,2.479c1.363,0,2.472-1.11,2.472-2.479c0-1.359-1.11-2.468-2.472-2.468	C3.584,8.306,2.473,9.416,2.472,10.775L2.472,10.775z M12.514,10.775c0,1.367,1.104,2.479,2.471,2.479	c1.363,0,2.474-1.108,2.474-2.479c0-1.359-1.11-2.468-2.474-2.468c-1.364,0-2.477,1.109-2.477,2.468H12.514z M3.324,10.775	c0-0.893,0.726-1.618,1.614-1.618c0.889,0,1.625,0.727,1.625,1.618c0,0.898-0.725,1.627-1.625,1.627	c-0.901,0-1.625-0.729-1.625-1.627H3.324z M13.354,10.775c0-0.893,0.726-1.618,1.627-1.618c0.886,0,1.61,0.727,1.61,1.618	c0,0.898-0.726,1.627-1.626,1.627s-1.625-0.729-1.625-1.627H13.354z M9.977,4.875c1.798,0,3.425,0.324,4.849,0.968	c-0.535,0.015-1.061,0.108-1.586,0.3c-1.264,0.463-2.264,1.388-2.815,2.604c-0.262,0.551-0.398,1.133-0.448,1.72	C9.79,7.905,7.677,5.873,5.076,5.82C6.501,5.208,8.153,4.875,9.94,4.875H9.977z"/></svg>',
        "tumblr": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6.885,8.598c0,0,0,3.393,0,4.996c0,0.282,0,0.66,0.094,0.942c0.377,1.509,1.131,2.545,2.545,3.11 c1.319,0.472,2.356,0.472,3.676,0c0.565-0.188,1.132-0.659,1.132-0.659l-0.849-2.263c0,0-1.036,0.378-1.603,0.283 c-0.565-0.094-1.226-0.66-1.226-1.508c0-1.603,0-4.902,0-4.902h2.828V5.771h-2.828V2H8.205c0,0-0.094,0.66-0.188,0.942 C7.828,3.791,7.262,4.733,6.603,5.394C5.848,6.147,5,6.43,5,6.43v2.168H6.885z"/></svg>',
        "tv": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect x="7" y="16" width="6" height="1"/><rect fill="none" stroke="#000" x=".5" y="3.5" width="19" height="11"/></svg>',
        "twitch": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5.23,1,2,4.23V15.85H5.88v3.23L9.1,15.85h2.59L17.5,10V1Zm11,8.4L13.62,12H11L8.78,14.24V12H5.88V2.29H16.21Z"/><rect x="12.98" y="4.55" width="1.29" height="3.88"/><rect x="9.43" y="4.55" width="1.29" height="3.88"/></svg>',
        "twitter": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M19,4.74 C18.339,5.029 17.626,5.229 16.881,5.32 C17.644,4.86 18.227,4.139 18.503,3.28 C17.79,3.7 17.001,4.009 16.159,4.17 C15.485,3.45 14.526,3 13.464,3 C11.423,3 9.771,4.66 9.771,6.7 C9.771,6.99 9.804,7.269 9.868,7.539 C6.795,7.38 4.076,5.919 2.254,3.679 C1.936,4.219 1.754,4.86 1.754,5.539 C1.754,6.82 2.405,7.95 3.397,8.61 C2.79,8.589 2.22,8.429 1.723,8.149 L1.723,8.189 C1.723,9.978 2.997,11.478 4.686,11.82 C4.376,11.899 4.049,11.939 3.713,11.939 C3.475,11.939 3.245,11.919 3.018,11.88 C3.49,13.349 4.852,14.419 6.469,14.449 C5.205,15.429 3.612,16.019 1.882,16.019 C1.583,16.019 1.29,16.009 1,15.969 C2.635,17.019 4.576,17.629 6.662,17.629 C13.454,17.629 17.17,12 17.17,7.129 C17.17,6.969 17.166,6.809 17.157,6.649 C17.879,6.129 18.504,5.478 19,4.74"/></svg>',
        "uikit": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="14.4,3.1 11.3,5.1 15,7.3 15,12.9 10,15.7 5,12.9 5,8.5 2,6.8 2,14.8 9.9,19.5 18,14.8 18,5.3"/><polygon points="9.8,4.2 6.7,2.4 9.8,0.4 12.9,2.3"/></svg>',
        "unlock": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><rect fill="none" stroke="#000" x="3.5" y="8.5" width="13" height="10"/><path fill="none" stroke="#000" d="M6.5,8.5 L6.5,4.9 C6.5,3 8.1,1.5 10,1.5 C11.9,1.5 13.5,3 13.5,4.9"/></svg>',
        "upload": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polyline fill="none" stroke="#000" points="5 8 9.5 3.5 14 8"/><rect x="3" y="17" width="13" height="1"/><line fill="none" stroke="#000" x1="9.5" y1="15" x2="9.5" y2="4"/></svg>',
        "user": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="9.9" cy="6.4" r="4.4"/><path fill="none" stroke="#000" stroke-width="1.1" d="M1.5,19 C2.3,14.5 5.8,11.2 10,11.2 C14.2,11.2 17.7,14.6 18.5,19.2"/></svg>',
        "users": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle fill="none" stroke="#000" stroke-width="1.1" cx="7.7" cy="8.6" r="3.5"/><path fill="none" stroke="#000" stroke-width="1.1" d="M1,18.1 C1.7,14.6 4.4,12.1 7.6,12.1 C10.9,12.1 13.7,14.8 14.3,18.3"/><path fill="none" stroke="#000" stroke-width="1.1" d="M11.4,4 C12.8,2.4 15.4,2.8 16.3,4.7 C17.2,6.6 15.7,8.9 13.6,8.9 C16.5,8.9 18.8,11.3 19.2,14.1"/></svg>',
        "video-camera": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon fill="none" stroke="#000" points="17.5 6.9 17.5 13.1 13.5 10.4 13.5 14.5 2.5 14.5 2.5 5.5 13.5 5.5 13.5 9.6 17.5 6.9"/></svg>',
        "vimeo": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M2.065,7.59C1.84,7.367,1.654,7.082,1.468,6.838c-0.332-0.42-0.137-0.411,0.274-0.772c1.026-0.91,2.004-1.896,3.127-2.688 c1.017-0.713,2.365-1.173,3.286-0.039c0.849,1.045,0.869,2.629,1.084,3.891c0.215,1.309,0.421,2.648,0.88,3.901 c0.127,0.352,0.37,1.018,0.81,1.074c0.567,0.078,1.145-0.917,1.408-1.289c0.684-0.987,1.611-2.317,1.494-3.587 c-0.115-1.349-1.572-1.095-2.482-0.773c0.146-1.514,1.555-3.216,2.912-3.792c1.439-0.597,3.579-0.587,4.302,1.036 c0.772,1.759,0.078,3.802-0.763,5.396c-0.918,1.731-2.1,3.333-3.363,4.829c-1.114,1.329-2.432,2.787-4.093,3.422 c-1.897,0.723-3.021-0.686-3.667-2.318c-0.705-1.777-1.056-3.771-1.565-5.621C4.898,8.726,4.644,7.836,4.136,7.191 C3.473,6.358,2.72,7.141,2.065,7.59C1.977,7.502,2.115,7.551,2.065,7.59L2.065,7.59z"/></svg>',
        "warning": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="14" r="1"/><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"/><path d="M10.97,7.72 C10.85,9.54 10.56,11.29 10.56,11.29 C10.51,11.87 10.27,12 9.99,12 C9.69,12 9.49,11.87 9.43,11.29 C9.43,11.29 9.16,9.54 9.03,7.72 C8.96,6.54 9.03,6 9.03,6 C9.03,5.45 9.46,5.02 9.99,5 C10.53,5.01 10.97,5.44 10.97,6 C10.97,6 11.04,6.54 10.97,7.72 L10.97,7.72 Z"/></svg>',
        "whatsapp": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M16.7,3.3c-1.8-1.8-4.1-2.8-6.7-2.8c-5.2,0-9.4,4.2-9.4,9.4c0,1.7,0.4,3.3,1.3,4.7l-1.3,4.9l5-1.3c1.4,0.8,2.9,1.2,4.5,1.2 l0,0l0,0c5.2,0,9.4-4.2,9.4-9.4C19.5,7.4,18.5,5,16.7,3.3 M10.1,17.7L10.1,17.7c-1.4,0-2.8-0.4-4-1.1l-0.3-0.2l-3,0.8l0.8-2.9 l-0.2-0.3c-0.8-1.2-1.2-2.7-1.2-4.2c0-4.3,3.5-7.8,7.8-7.8c2.1,0,4.1,0.8,5.5,2.3c1.5,1.5,2.3,3.4,2.3,5.5 C17.9,14.2,14.4,17.7,10.1,17.7 M14.4,11.9c-0.2-0.1-1.4-0.7-1.6-0.8c-0.2-0.1-0.4-0.1-0.5,0.1c-0.2,0.2-0.6,0.8-0.8,0.9 c-0.1,0.2-0.3,0.2-0.5,0.1c-0.2-0.1-1-0.4-1.9-1.2c-0.7-0.6-1.2-1.4-1.3-1.6c-0.1-0.2,0-0.4,0.1-0.5C8,8.8,8.1,8.7,8.2,8.5 c0.1-0.1,0.2-0.2,0.2-0.4c0.1-0.2,0-0.3,0-0.4C8.4,7.6,7.9,6.5,7.7,6C7.5,5.5,7.3,5.6,7.2,5.6c-0.1,0-0.3,0-0.4,0 c-0.2,0-0.4,0.1-0.6,0.3c-0.2,0.2-0.8,0.8-0.8,2c0,1.2,0.8,2.3,1,2.4c0.1,0.2,1.7,2.5,4,3.5c0.6,0.2,1,0.4,1.3,0.5 c0.6,0.2,1.1,0.2,1.5,0.1c0.5-0.1,1.4-0.6,1.6-1.1c0.2-0.5,0.2-1,0.1-1.1C14.8,12.1,14.6,12,14.4,11.9"/></svg>',
        "wordpress": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10,0.5c-5.2,0-9.5,4.3-9.5,9.5s4.3,9.5,9.5,9.5c5.2,0,9.5-4.3,9.5-9.5S15.2,0.5,10,0.5L10,0.5L10,0.5z M15.6,3.9h-0.1 c-0.8,0-1.4,0.7-1.4,1.5c0,0.7,0.4,1.3,0.8,1.9c0.3,0.6,0.7,1.3,0.7,2.3c0,0.7-0.3,1.5-0.6,2.7L14.1,15l-3-8.9 c0.5,0,0.9-0.1,0.9-0.1C12.5,6,12.5,5.3,12,5.4c0,0-1.3,0.1-2.2,0.1C9,5.5,7.7,5.4,7.7,5.4C7.2,5.3,7.2,6,7.6,6c0,0,0.4,0.1,0.9,0.1 l1.3,3.5L8,15L5,6.1C5.5,6.1,5.9,6,5.9,6C6.4,6,6.3,5.3,5.9,5.4c0,0-1.3,0.1-2.2,0.1c-0.2,0-0.3,0-0.5,0c1.5-2.2,4-3.7,6.9-3.7 C12.2,1.7,14.1,2.6,15.6,3.9L15.6,3.9L15.6,3.9z M2.5,6.6l3.9,10.8c-2.7-1.3-4.6-4.2-4.6-7.4C1.8,8.8,2,7.6,2.5,6.6L2.5,6.6L2.5,6.6 z M10.2,10.7l2.5,6.9c0,0,0,0.1,0.1,0.1C11.9,18,11,18.2,10,18.2c-0.8,0-1.6-0.1-2.3-0.3L10.2,10.7L10.2,10.7L10.2,10.7z M14.2,17.1 l2.5-7.3c0.5-1.2,0.6-2.1,0.6-2.9c0-0.3,0-0.6-0.1-0.8c0.6,1.2,1,2.5,1,4C18.3,13,16.6,15.7,14.2,17.1L14.2,17.1L14.2,17.1z"/></svg>',
        "world": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#000" d="M1,10.5 L19,10.5"/><path fill="none" stroke="#000" d="M2.35,15.5 L17.65,15.5"/><path fill="none" stroke="#000" d="M2.35,5.5 L17.523,5.5"/><path fill="none" stroke="#000" d="M10,19.46 L9.98,19.46 C7.31,17.33 5.61,14.141 5.61,10.58 C5.61,7.02 7.33,3.83 10,1.7 C10.01,1.7 9.99,1.7 10,1.7 L10,1.7 C12.67,3.83 14.4,7.02 14.4,10.58 C14.4,14.141 12.67,17.33 10,19.46 L10,19.46 L10,19.46 L10,19.46 Z"/><circle fill="none" stroke="#000" cx="10" cy="10.5" r="9"/></svg>',
        "xing": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M4.4,4.56 C4.24,4.56 4.11,4.61 4.05,4.72 C3.98,4.83 3.99,4.97 4.07,5.12 L5.82,8.16 L5.82,8.17 L3.06,13.04 C2.99,13.18 2.99,13.33 3.06,13.44 C3.12,13.55 3.24,13.62 3.4,13.62 L6,13.62 C6.39,13.62 6.57,13.36 6.71,13.12 C6.71,13.12 9.41,8.35 9.51,8.16 C9.49,8.14 7.72,5.04 7.72,5.04 C7.58,4.81 7.39,4.56 6.99,4.56 L4.4,4.56 L4.4,4.56 Z"/><path d="M15.3,1 C14.91,1 14.74,1.25 14.6,1.5 C14.6,1.5 9.01,11.42 8.82,11.74 C8.83,11.76 12.51,18.51 12.51,18.51 C12.64,18.74 12.84,19 13.23,19 L15.82,19 C15.98,19 16.1,18.94 16.16,18.83 C16.23,18.72 16.23,18.57 16.16,18.43 L12.5,11.74 L12.5,11.72 L18.25,1.56 C18.32,1.42 18.32,1.27 18.25,1.16 C18.21,1.06 18.08,1 17.93,1 L15.3,1 L15.3,1 Z"/></svg>',
        "yelp": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.175,14.971c-0.112,0.77-1.686,2.767-2.406,3.054c-0.246,0.1-0.487,0.076-0.675-0.069	c-0.122-0.096-2.446-3.859-2.446-3.859c-0.194-0.293-0.157-0.682,0.083-0.978c0.234-0.284,0.581-0.393,0.881-0.276	c0.016,0.01,4.21,1.394,4.332,1.482c0.178,0.148,0.263,0.379,0.225,0.646L17.175,14.971L17.175,14.971z M11.464,10.789	c-0.203-0.307-0.199-0.666,0.009-0.916c0,0,2.625-3.574,2.745-3.657c0.203-0.135,0.452-0.141,0.69-0.025	c0.691,0.335,2.085,2.405,2.167,3.199v0.027c0.024,0.271-0.082,0.491-0.273,0.623c-0.132,0.083-4.43,1.155-4.43,1.155	c-0.322,0.096-0.68-0.06-0.882-0.381L11.464,10.789z M9.475,9.563C9.32,9.609,8.848,9.757,8.269,8.817c0,0-3.916-6.16-4.007-6.351	c-0.057-0.212,0.011-0.455,0.202-0.65C5.047,1.211,8.21,0.327,9.037,0.529c0.27,0.069,0.457,0.238,0.522,0.479	c0.047,0.266,0.433,5.982,0.488,7.264C10.098,9.368,9.629,9.517,9.475,9.563z M9.927,19.066c-0.083,0.225-0.273,0.373-0.54,0.421	c-0.762,0.13-3.15-0.751-3.647-1.342c-0.096-0.131-0.155-0.262-0.167-0.394c-0.011-0.095,0-0.189,0.036-0.272	c0.061-0.155,2.917-3.538,2.917-3.538c0.214-0.272,0.595-0.355,0.952-0.213c0.345,0.13,0.56,0.428,0.536,0.749	C10.014,14.479,9.977,18.923,9.927,19.066z M3.495,13.912c-0.235-0.009-0.444-0.148-0.568-0.382c-0.089-0.17-0.151-0.453-0.19-0.794	C2.63,11.701,2.761,10.144,3.07,9.648c0.145-0.226,0.357-0.345,0.592-0.336c0.154,0,4.255,1.667,4.255,1.667	c0.321,0.118,0.521,0.453,0.5,0.833c-0.023,0.37-0.236,0.655-0.551,0.738L3.495,13.912z"/></svg>',
        "youtube": '<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15,4.1c1,0.1,2.3,0,3,0.8c0.8,0.8,0.9,2.1,0.9,3.1C19,9.2,19,10.9,19,12c-0.1,1.1,0,2.4-0.5,3.4c-0.5,1.1-1.4,1.5-2.5,1.6 c-1.2,0.1-8.6,0.1-11,0c-1.1-0.1-2.4-0.1-3.2-1c-0.7-0.8-0.7-2-0.8-3C1,11.8,1,10.1,1,8.9c0-1.1,0-2.4,0.5-3.4C2,4.5,3,4.3,4.1,4.2 C5.3,4.1,12.6,4,15,4.1z M8,7.5v6l5.5-3L8,7.5z"/></svg>'
      });
    }
    if (typeof window !== "undefined" && window.UIkit) {
      window.UIkit.use(plugin);
    }
    return plugin;
  });
})(uikitIcons);
var Icons = uikitIcons.exports;
const _hoisted_1$7 = { "uk-slider": "finite: true" };
const _hoisted_2$6 = { class: "uk-position-relative" };
const _hoisted_3$5 = { class: "uk-slider-container mx-5" };
const _hoisted_4$5 = { class: "uk-slider-items" };
const _hoisted_5$4 = ["onClick"];
const _hoisted_6$4 = /* @__PURE__ */ createBaseVNode("a", {
  class: "uk-position-center-left",
  href: "#",
  "uk-slidenav-previous": "",
  "uk-slider-item": "previous"
}, null, -1);
const _hoisted_7$4 = /* @__PURE__ */ createBaseVNode("a", {
  class: "uk-position-center-right",
  href: "#",
  "uk-slidenav-next": "",
  "uk-slider-item": "next"
}, null, -1);
const _sfc_main$7 = /* @__PURE__ */ defineComponent({
  emits: ["selectcategory"],
  setup(__props, { emit }) {
    UIkit.use(Icons);
    const productSession = useProductSession();
    const state = reactive({
      categories: productSession.categories
    });
    const goTo = (index) => {
      const objIndexOld = state.categories.findIndex((obj) => obj.selected == true);
      state.categories[objIndexOld].selected = false;
      state.categories[index].selected = true;
      const id = state.categories[index].id;
      emit("selectcategory", id);
    };
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$7, [
        createBaseVNode("div", _hoisted_2$6, [
          createBaseVNode("div", _hoisted_3$5, [
            createBaseVNode("ul", _hoisted_4$5, [
              (openBlock(true), createElementBlock(Fragment, null, renderList(unref(state).categories, (item, index) => {
                return openBlock(), createElementBlock("li", {
                  key: item.id
                }, [
                  createBaseVNode("div", {
                    class: normalizeClass(["tag is-medium m-3 is-rounded is-clickable", [
                      item.selected ? "is-primary" : "is-white",
                      item.selected ? "is-primary" : "is-dark-bg-1"
                    ]]),
                    onClick: ($event) => goTo(index)
                  }, [
                    createBaseVNode("span", null, toDisplayString(item.name), 1)
                  ], 10, _hoisted_5$4)
                ]);
              }), 128))
            ])
          ]),
          _hoisted_6$4,
          _hoisted_7$4
        ])
      ]);
    };
  }
});
var ProductPos_vue_vue_type_style_index_0_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-3f95b9f9"), n = n(), popScopeId(), n);
const _hoisted_1$6 = {
  class: "image-container",
  style: { "position": "relative" }
};
const _hoisted_2$5 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("i", { class: "fas fa-star" }, null, -1));
const _hoisted_3$4 = ["src"];
const _hoisted_4$4 = { class: "meta-container" };
const _hoisted_5$3 = { class: "meta-content" };
const _hoisted_6$3 = { class: "columns price-name-content is-justify-content-space-between px-3 mt-1" };
const _hoisted_7$3 = { class: "column is-half truncate-content pl-0 py-1 pr-1" };
const _hoisted_8$3 = { class: "item-internalid price-name-description" };
const _hoisted_9$3 = { class: "column is-half truncate-content p-1" };
const _hoisted_10$3 = { class: "mb-2" };
const _hoisted_11$3 = ["title"];
const _sfc_main$6 = /* @__PURE__ */ defineComponent({
  props: {
    product: { type: null, required: true, default: Object },
    loading: { type: Boolean, required: true }
  },
  emits: ["clickAddProduct", "clickAddFavoriteProduct"],
  setup(__props, { emit }) {
    const props = __props;
    const click = (item) => {
      emit("clickAddProduct", item);
    };
    const clickFavorite = (id) => {
      emit("clickAddFavoriteProduct", id);
    };
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$i;
      const _directive_tooltip = resolveDirective("tooltip");
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["restaurants-list-item", [__props.loading ? "adding" : ""]]),
        onClick: _cache[2] || (_cache[2] = ($event) => click(props.product))
      }, [
        createBaseVNode("div", _hoisted_1$6, [
          withDirectives(createVNode(_component_VButton, {
            class: normalizeClass(["btn-sm is-light", [props.product.favorite ? "favorite" : ""]]),
            raised: "",
            onClick: _cache[0] || (_cache[0] = withModifiers(($event) => clickFavorite(props.product.id), ["stop"]))
          }, {
            default: withCtx(() => [
              _hoisted_2$5
            ]),
            _: 1
          }, 8, ["class"]), [
            [
              _directive_tooltip,
              props.product.favorite ? "Quitar de Favoritos" : "A\xF1adir a Favoritos",
              void 0,
              {
                bottom: true,
                bubble: true
              }
            ]
          ]),
          createBaseVNode("img", {
            src: props.product.imageUrl,
            alt: "",
            onErrorOnce: _cache[1] || (_cache[1] = (event) => unref(useViaPlaceholderError)(event, "800x450"))
          }, null, 40, _hoisted_3$4)
        ]),
        createBaseVNode("div", _hoisted_4$4, [
          createBaseVNode("div", _hoisted_5$3, [
            createBaseVNode("h4", null, toDisplayString(props.product.name), 1),
            createBaseVNode("div", _hoisted_6$3, [
              createBaseVNode("div", _hoisted_7$3, [
                createBaseVNode("p", _hoisted_8$3, [
                  createBaseVNode("b", null, toDisplayString(props.product.internalId), 1)
                ])
              ]),
              createBaseVNode("div", _hoisted_9$3, [
                createBaseVNode("p", _hoisted_10$3, [
                  createBaseVNode("span", {
                    class: "meta-price px-1 price-name-description ml-auto",
                    title: props.product.currencyTypeSymbol + props.product.price.toFixed(2)
                  }, [
                    createTextVNode(toDisplayString(props.product.currencyTypeSymbol) + " ", 1),
                    createBaseVNode("b", null, toDisplayString(props.product.price.toFixed(2)), 1)
                  ], 8, _hoisted_11$3)
                ])
              ])
            ])
          ])
        ])
      ], 2);
    };
  }
});
var __unplugin_components_5 = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-3f95b9f9"]]);
const _hoisted_1$5 = { class: "section-placeholder" };
const _hoisted_2$4 = { class: "placeholder-content" };
const _hoisted_3$3 = { class: "dark-inverted" };
const _hoisted_4$3 = { key: 0 };
const _sfc_main$5 = /* @__PURE__ */ defineComponent({
  props: {
    title: { type: String, required: true },
    subtitle: { type: String, required: false, default: void 0 }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1$5, [
        createBaseVNode("div", _hoisted_2$4, [
          renderSlot(_ctx.$slots, "image"),
          createBaseVNode("h3", _hoisted_3$3, toDisplayString(props.title), 1),
          props.subtitle ? (openBlock(), createElementBlock("p", _hoisted_4$3, toDisplayString(props.subtitle), 1)) : createCommentVNode("", true),
          renderSlot(_ctx.$slots, "action")
        ])
      ]);
    };
  }
});
const userSession = useUserSession();
const useBagStore = defineStore("bagStore", () => {
  const defaultData = {
    products: [],
    total: 0,
    barman: userSession.name,
    total_quantity: 0
  };
  const saved = localStorage.getItem("bagStore");
  const bag = reactive(saved ? JSON.parse(saved) : defaultData);
  function set(data) {
    bag.products = data.products;
    bag.total = data.total;
    bag.barman = data.barman;
    bag.total_quantity = data.total_quantity;
  }
  function reset() {
    bag.products = [];
    bag.total = 0;
    bag.barman = userSession.name;
    bag.total_quantity = 0;
  }
  function calculaTotalBag() {
    let total2 = 0;
    bag.products.forEach((x) => {
      total2 += Number(x.price) * x.quantity;
    });
    bag.total = total2;
  }
  function calculateTotalquantity() {
    let total_quantity = 0;
    bag.products.forEach((x) => {
      total_quantity += x.quantity;
    });
    bag.total_quantity = total_quantity;
  }
  function addProduct(item) {
    const duplicateIndex = bag.products.findIndex((x) => x.id == item.id);
    if (duplicateIndex >= 0) {
      bag.products[duplicateIndex].quantity += 1;
    } else {
      const add = __spreadProps(__spreadValues({}, item), { quantity: 1, price: parseFloat(item.price.toFixed(2)) });
      bag.products.push(add);
    }
    calculaTotalBag();
    calculateTotalquantity();
  }
  function removeProduct(index) {
    bag.products.splice(index, 1);
    calculaTotalBag();
    calculateTotalquantity();
  }
  function changeQuantityToProduct(itemId, operation) {
    const itemIndex = bag.products.findIndex((x) => x.id == itemId);
    if (itemIndex >= 0) {
      if (operation) {
        bag.products[itemIndex].quantity += 1;
        bag.products[itemIndex].quantity_pending += 1;
      } else {
        if (bag.products[itemIndex].quantity > 1) {
          bag.products[itemIndex].quantity -= 1;
          bag.products[itemIndex].quantity_pending -= 1;
        }
      }
    }
    calculaTotalBag();
    calculateTotalquantity();
  }
  function changeDinamicQuantityToProduct(itemId, quantity) {
    const itemIndex = bag.products.findIndex((x) => x.id == itemId);
    const product = bag.products[itemIndex];
    const unitTypeIdNotFloat = ["NIU", "ZZ"];
    if (itemIndex >= 0) {
      if (unitTypeIdNotFloat.some((element) => element === product.unitTypeId)) {
        const price = parseInt(quantity.toString());
        product.quantity = price < 1 ? 1 : price;
      } else {
        product.quantity = quantity;
      }
    }
    calculaTotalBag();
    calculateTotalquantity();
  }
  function addNoteToProduct(itemId, note) {
    const itemIndex = bag.products.findIndex((x) => x.id == itemId);
    if (itemIndex >= 0) {
      bag.products[itemIndex].note = note;
    }
  }
  function getNoteToProduct(itemId) {
    const itemIndex = bag.products.findIndex((x) => x.id == itemId);
    return bag.products[itemIndex].note;
  }
  function getTotal() {
    return bag.total;
  }
  function getTotalQuantity() {
    return bag.total_quantity;
  }
  function getProducts() {
    return bag.products;
  }
  const products = computed(() => {
    return bag.products;
  });
  const total = computed(() => {
    return bag.products.reduce(function(acc, obj) {
      return acc + Number(obj.price) * obj.quantity;
    }, 0);
  });
  const unitTypeSummary = computed(() => {
    const summary = {};
    bag.products.forEach((product) => {
      const unitType = product.unitTypeId || "NIU";
      if (summary[unitType]) {
        summary[unitType] += product.quantity;
      } else {
        summary[unitType] = product.quantity;
      }
    });
    return summary;
  });
  watch(bag, (newValue) => {
    localStorage.setItem("bagStore", JSON.stringify(newValue));
  }, { deep: true });
  return {
    set,
    reset,
    addProduct,
    removeProduct,
    addNoteToProduct,
    getNoteToProduct,
    getProducts,
    getTotal,
    getTotalQuantity,
    changeQuantityToProduct,
    changeDinamicQuantityToProduct,
    calculaTotalBag,
    total,
    products,
    unitTypeSummary
  };
});
var CartMobile_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$4 = { class: "cart-widget side-section is-active" };
const _hoisted_2$3 = { class: "cart-items cart-items-new has-slimscroll" };
const _hoisted_3$2 = {
  class: "column is-one-seconds-tablet",
  style: { "min-width": "200px" }
};
const _hoisted_4$2 = { class: "columns is-mobile is-gapless mb-0" };
const _hoisted_5$2 = {
  class: "column is-11 ml-1 mr-1",
  style: { "display": "flex", "align-items": "center" }
};
const _hoisted_6$2 = { class: "m-0 product-description" };
const _hoisted_7$2 = { class: "column is-one-seconds-tablet" };
const _hoisted_8$2 = { class: "columns is-mobile is-gapless is-0-tablet has-text-centered mb-1 is-justify-content-flex-end" };
const _hoisted_9$2 = {
  class: "m-1 has-text-weight-normal",
  style: { "display": "flex", "align-items": "center" }
};
const _hoisted_10$2 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-minus",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_11$2 = ["onUpdate:modelValue", "onChange"];
const _hoisted_12$2 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-plus",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_13$2 = { class: "column content-btn-delete-product" };
const _hoisted_14$2 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-trash",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_15$2 = { key: 1 };
const _hoisted_16$2 = { class: "cart-button cart-button-new pt-0" };
const _hoisted_17$2 = /* @__PURE__ */ createBaseVNode("span", null, "Finalizar", -1);
const _hoisted_18$2 = {
  key: 0,
  class: "has-text-centered my-0 text-finishing",
  style: { "display": "flex", "color": "#ffff" }
};
const _hoisted_19$2 = /* @__PURE__ */ createBaseVNode("span", { class: "has-text-weight-normal" }, "TOTAL: ", -1);
const _hoisted_20$2 = { class: "has-text-weight-bold ml-1" };
const _sfc_main$4 = /* @__PURE__ */ defineComponent({
  props: {
    parent: { type: null, required: false, default: void 0 }
  },
  emits: ["finalizeSale"],
  setup(__props, { emit }) {
    const props = __props;
    const bagStore = useBagStore();
    const isOnLine = ref(navigator.onLine);
    const isOnLineDescription = ref(navigator.onLine ? "ONLINE" : "OFFLINE");
    const finalizeSale = () => {
      emit("finalizeSale");
    };
    const clickRemoveProduct = (index) => {
      if (props.parent == void 0) {
        bagStore.removeProduct(index);
      }
    };
    const acums = (id, operation) => {
      if (props.parent == void 0) {
        bagStore.changeQuantityToProduct(id, operation);
      }
    };
    const changeQuantity = (id, quantity) => {
      if (quantity < 1 || quantity == null) {
        quantity = 1;
      }
      if (props.parent == void 0) {
        bagStore.changeDinamicQuantityToProduct(id, quantity);
      }
    };
    function setOnlineStatus() {
      isOnLine.value = navigator.onLine;
      isOnLineDescription.value = navigator.onLine ? "ONLINE" : "OFFLINE";
    }
    onMounted(async () => {
      window.addEventListener("online", setOnlineStatus);
      window.addEventListener("offline", setOnlineStatus);
    });
    onBeforeUnmount(() => {
      window.removeEventListener("online", setOnlineStatus);
      window.removeEventListener("offline", setOnlineStatus);
    });
    return (_ctx, _cache) => {
      const _component_VButton = _sfc_main$i;
      const _component_VPlaceholderSection = _sfc_main$5;
      const _component_VControl = __unplugin_components_1;
      const _component_VFlex = _sfc_main$a;
      return openBlock(), createElementBlock("div", _hoisted_1$4, [
        createBaseVNode("div", _hoisted_2$3, [
          props.parent == void 0 && unref(bagStore).getProducts().length > 0 ? (openBlock(true), createElementBlock(Fragment, { key: 0 }, renderList(unref(bagStore).getProducts(), (item, index) => {
            return openBlock(), createElementBlock("div", {
              key: item.id,
              class: "cart-item cart-item-new columns is-multiline is-gapless"
            }, [
              createBaseVNode("div", _hoisted_3$2, [
                createBaseVNode("div", _hoisted_4$2, [
                  createBaseVNode("div", _hoisted_5$2, [
                    createBaseVNode("p", _hoisted_6$2, toDisplayString(item.name), 1)
                  ])
                ])
              ]),
              createBaseVNode("div", _hoisted_7$2, [
                createBaseVNode("div", _hoisted_8$2, [
                  createBaseVNode("p", _hoisted_9$2, [
                    createVNode(_component_VButton, {
                      size: "small",
                      class: "btn-sm is-light mr-1 is-danger second-buton",
                      onClick: ($event) => acums(item.id, false)
                    }, {
                      default: withCtx(() => [
                        _hoisted_10$2
                      ]),
                      _: 2
                    }, 1032, ["onClick"]),
                    withDirectives(createBaseVNode("input", {
                      "onUpdate:modelValue": ($event) => item.quantity = $event,
                      type: "number",
                      style: { "font-size": "14px", "width": "70px", "text-align": "center", "border": "1px solid #f9f9f9" },
                      placeholder: "0",
                      min: "0",
                      class: "input-quantity",
                      onChange: ($event) => changeQuantity(item.id, item.quantity)
                    }, null, 40, _hoisted_11$2), [
                      [vModelText, item.quantity]
                    ]),
                    createVNode(_component_VButton, {
                      size: "small",
                      class: "btn-sm is-light ml-1 primary-buton btn-more-product",
                      onClick: ($event) => acums(item.id, true)
                    }, {
                      default: withCtx(() => [
                        _hoisted_12$2
                      ]),
                      _: 2
                    }, 1032, ["onClick"]),
                    createTextVNode(" S/ " + toDisplayString(item.price.toFixed(2)), 1)
                  ]),
                  createBaseVNode("div", _hoisted_13$2, [
                    createVNode(_component_VButton, {
                      size: "small",
                      color: "danger",
                      class: "btn-sm",
                      onClick: ($event) => clickRemoveProduct(index)
                    }, {
                      default: withCtx(() => [
                        _hoisted_14$2
                      ]),
                      _: 2
                    }, 1032, ["onClick"])
                  ])
                ])
              ])
            ]);
          }), 128)) : (openBlock(), createElementBlock("div", _hoisted_15$2, [
            createVNode(_component_VPlaceholderSection, {
              title: "No has agregado platillos",
              subtitle: "A\xF1ade productos para realizar la venta"
            })
          ]))
        ]),
        createBaseVNode("div", _hoisted_16$2, [
          createVNode(_component_VFlex, {
            "justify-content": "center",
            "flex-wrap": "wrap"
          }, {
            default: withCtx(() => [
              createVNode(_component_VControl, { style: { "width": "100%" } }, {
                default: withCtx(() => [
                  createVNode(_component_VButton, {
                    color: "primary",
                    outline: "",
                    bold: "",
                    class: "px-2 mini-btn btn-finishing",
                    onClick: finalizeSale,
                    disabled: !isOnLine.value
                  }, {
                    default: withCtx(() => [
                      _hoisted_17$2,
                      props.parent == void 0 && unref(bagStore).getProducts().length > 0 ? (openBlock(), createElementBlock("p", _hoisted_18$2, [
                        _hoisted_19$2,
                        createBaseVNode("span", _hoisted_20$2, " S/ " + toDisplayString(unref(bagStore).total.toFixed(2)), 1)
                      ])) : createCommentVNode("", true)
                    ]),
                    _: 1
                  }, 8, ["disabled"])
                ]),
                _: 1
              })
            ]),
            _: 1
          })
        ])
      ]);
    };
  }
});
const _sfc_main$3 = {};
const _hoisted_1$3 = /* @__PURE__ */ createBaseVNode("h5", null, null, -1);
const _hoisted_2$2 = [
  _hoisted_1$3
];
function _sfc_render(_ctx, _cache) {
  return openBlock(), createElementBlock("div", null, _hoisted_2$2);
}
var __unplugin_components_8 = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render]]);
var _imports_0 = "/vendeya/assets/cart-placeholder.95c4f12d.svg";
var Bag_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$2 = { class: "right fixed-parent is-hidden-mobile h-hidden-tablet-p" };
const _hoisted_2$1 = { class: "sticky-panel fixed-child" };
const _hoisted_3$1 = { class: "cart-widget side-section is-active p-0" };
const _hoisted_4$1 = /* @__PURE__ */ createBaseVNode("img", {
  class: "light-image",
  src: _imports_0,
  alt: ""
}, null, -1);
const _hoisted_5$1 = /* @__PURE__ */ createBaseVNode("img", {
  class: "dark-image",
  src: _imports_0,
  alt: ""
}, null, -1);
const _hoisted_6$1 = { class: "cart-items has-slimscroll pt-4" };
const _hoisted_7$1 = { class: "item columns mb-0 is-hoverable pt-2" };
const _hoisted_8$1 = { class: "column is-8 py-1 ml-2" };
const _hoisted_9$1 = { class: "m-0 item-name" };
const _hoisted_10$1 = {
  class: "m-1 has-text-weight-normal item-price",
  style: { "display": "flex", "align-items": "center" }
};
const _hoisted_11$1 = { class: "quantity-container mr-2" };
const _hoisted_12$1 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-minus",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_13$1 = ["onUpdate:modelValue", "onChange"];
const _hoisted_14$1 = /* @__PURE__ */ createBaseVNode("i", {
  class: "fas fa-plus",
  style: { "font-size": "0.8rem" }
}, null, -1);
const _hoisted_15$1 = ["value", "onFocus", "onBlur", "onInput"];
const _hoisted_16$1 = { class: "unit-label" };
const _hoisted_17$1 = /* @__PURE__ */ createTextVNode("X");
const _hoisted_18$1 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _hoisted_19$1 = { class: "column y-space has-text-right py-1 mr-2" };
const _hoisted_20$1 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "20",
  height: "20",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "currentColor",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon icon-tabler icons-tabler-outline icon-tabler-trash"
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
], -1);
const _hoisted_21$1 = ["title"];
const _hoisted_22$1 = { class: "cart-button px-2 pt-0" };
const _hoisted_23$1 = { class: "total" };
const _hoisted_24$1 = /* @__PURE__ */ createBaseVNode("span", { class: "label" }, "Total productos", -1);
const _hoisted_25$1 = { class: "items-count" };
const _hoisted_26$1 = { key: 0 };
const _hoisted_27$1 = /* @__PURE__ */ createBaseVNode("span", null, "Finalizar", -1);
const _hoisted_28$1 = {
  key: 0,
  class: "has-text-centered my-0 text-finishing",
  style: { "display": "flex", "color": "#ffff" }
};
const _hoisted_29$1 = /* @__PURE__ */ createBaseVNode("span", { class: "has-text-weight-normal" }, "TOTAL: ", -1);
const _hoisted_30$1 = { class: "has-text-weight-bold ml-1" };
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    parent: { type: null, required: false, default: void 0 }
  },
  emits: ["finalizeSale"],
  setup(__props, { emit }) {
    const props = __props;
    const bagStore = useBagStore();
    const isOnLine = ref(navigator.onLine);
    const isOnLineDescription = ref(navigator.onLine ? "ONLINE" : "OFFLINE");
    const editingPriceId = ref(null);
    const ensureTwoDecimals = () => {
      bagStore.getProducts().forEach((product) => {
        product.price = parseFloat(product.price.toFixed(2));
      });
    };
    const clickRemoveProduct = (index) => {
      if (props.parent == void 0) {
        bagStore.removeProduct(index);
      }
    };
    const finalizeSale = () => {
      emit("finalizeSale");
    };
    const acums = (id, operation) => {
      if (props.parent == void 0) {
        bagStore.changeQuantityToProduct(id, operation);
      }
    };
    const changeQuantity = (id, quantity) => {
      if (quantity < 0) {
        quantity = 1;
      }
      if (props.parent == void 0) {
        bagStore.changeDinamicQuantityToProduct(id, quantity);
      }
    };
    const startEditingPrice = (id, event) => {
      editingPriceId.value = id;
      const target = event.target;
      target.select();
    };
    const formatPrice = (id, event) => {
      const target = event.target;
      let price = parseFloat(target.value);
      if (isNaN(price) || price <= 0) {
        price = 0.01;
      }
      const itemIndex = bagStore.getProducts().findIndex((x) => x.id == id);
      if (itemIndex >= 0) {
        const formattedPrice = parseFloat(price.toFixed(2));
        bagStore.getProducts()[itemIndex].price = formattedPrice;
        target.value = formattedPrice.toFixed(2);
        bagStore.calculaTotalBag();
      }
      editingPriceId.value = null;
    };
    const getPriceDisplay = (item) => {
      if (editingPriceId.value === item.id) {
        return item.price.toString();
      }
      return item.price.toFixed(2);
    };
    const unitLabels = {
      "ZZ": "SERV",
      "BX": "CAJ",
      "GLL": "GL",
      "GRM": "GR",
      "KGM": "KG",
      "LTR": "LT",
      "MTR": "M",
      "FOT": "PIE",
      "INH": "INCH",
      "NIU": "UND",
      "YRD": "YD",
      "HUR": "HR",
      "TNE": "TNL",
      "DZN": "DOC",
      "QD": "1/4 DOC",
      "PK": "PQT",
      "MTQ": "M3",
      "HD": "1/2 DOC",
      "PR": "PAR",
      "JG": "JARR",
      "JR": "FCO",
      "KT": "KIT",
      "CH": "ENV",
      "AV": "CAPS",
      "CT": "CTON",
      "CY": "CIL",
      "BE": "FARD",
      "BG": "BOLS",
      "BJ": "BALD",
      "SET": "JGO",
      "BLL": "BRL",
      "RM": "RESM",
      "BO": "BOT",
      "SA": "SCO",
      "BT": "TORN",
      "C62": "PZ",
      "U2": "BLIST",
      "CA": "LT",
      "CEN": "CTO",
      "CMT": "CM",
      "CMK": "CM3",
      "CMQ": "CM2",
      "DZP": "DOC2",
      "FTK": "PIE2",
      "FTQ": "PIE3",
      "GLI": "GL",
      "HT": "1/2 H",
      "KTM": "KM",
      "KWH": "KWxH",
      "MWH": "MWxH",
      "LBR": "LB",
      "LEF": "HOJA",
      "MGM": "MG",
      "MIL": "MIL",
      "MLT": "ML",
      "MMT": "ML",
      "MMK": "ML2",
      "MMQ": "ML3",
      "MTK": "M2",
      "ONZ": "ONZ",
      "PF": "PAL",
      "PG": "PLAC",
      "RD": "VAR",
      "RL": "CRR",
      "SEC": "SEG",
      "ST": "PLGO",
      "TU": "TB",
      "UM": "MILL"
    };
    const getUnitLabel = (unitTypeId) => {
      return unitLabels[unitTypeId] || "UND";
    };
    const getUnitSummaryText = () => {
      const summary = bagStore.unitTypeSummary;
      const parts = [];
      for (const [unitType, quantity] of Object.entries(summary)) {
        const label = unitLabels[unitType] || unitType.toLowerCase();
        const displayQuantity = Number.isInteger(quantity) ? quantity : quantity.toFixed(2);
        const pluralLabel = quantity > 1 && label === "unidad" ? "unidades" : label;
        parts.push(`${displayQuantity} ${pluralLabel}`);
      }
      return parts.join(" & ");
    };
    function setOnlineStatus() {
      isOnLine.value = navigator.onLine;
      isOnLineDescription.value = navigator.onLine ? "ONLINE" : "OFFLINE";
    }
    onMounted(async () => {
      window.addEventListener("online", setOnlineStatus);
      window.addEventListener("offline", setOnlineStatus);
      ensureTwoDecimals();
    });
    onBeforeUnmount(() => {
      window.removeEventListener("online", setOnlineStatus);
      window.removeEventListener("offline", setOnlineStatus);
    });
    return (_ctx, _cache) => {
      const _component_VPlaceholderSection = _sfc_main$5;
      const _component_VButton = _sfc_main$i;
      return openBlock(), createElementBlock("div", _hoisted_1$2, [
        createBaseVNode("div", _hoisted_2$1, [
          createBaseVNode("div", _hoisted_3$1, [
            props.parent == void 0 && !unref(bagStore).getProducts().length ? (openBlock(), createBlock(_component_VPlaceholderSection, {
              key: 0,
              title: "No has agregado productos",
              subtitle: "no tienes productos agregados"
            }, {
              image: withCtx(() => [
                _hoisted_4$1,
                _hoisted_5$1
              ]),
              _: 1
            })) : createCommentVNode("", true),
            createBaseVNode("div", _hoisted_6$1, [
              (openBlock(true), createElementBlock(Fragment, null, renderList(unref(bagStore).getProducts(), (item, index) => {
                return openBlock(), createElementBlock("div", {
                  key: item.id,
                  class: "my-3 mx-2"
                }, [
                  createBaseVNode("div", _hoisted_7$1, [
                    createBaseVNode("div", _hoisted_8$1, [
                      createBaseVNode("p", _hoisted_9$1, [
                        createBaseVNode("span", null, toDisplayString(item.name), 1)
                      ]),
                      createBaseVNode("p", _hoisted_10$1, [
                        createBaseVNode("div", _hoisted_11$1, [
                          createVNode(_component_VButton, {
                            size: "small",
                            class: "btn-sm is-light mr-0 is-danger second-buton",
                            onClick: ($event) => acums(item.id, false)
                          }, {
                            default: withCtx(() => [
                              _hoisted_12$1
                            ]),
                            _: 2
                          }, 1032, ["onClick"]),
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": ($event) => item.quantity = $event,
                            type: "number",
                            class: "input-quantity",
                            placeholder: "0",
                            min: "0",
                            onChange: ($event) => changeQuantity(item.id, item.quantity)
                          }, null, 40, _hoisted_13$1), [
                            [vModelText, item.quantity]
                          ]),
                          createVNode(_component_VButton, {
                            class: "btn-sm is-light ml-0 is-primary primary-buton btn-more-product mr-0",
                            onClick: ($event) => acums(item.id, true)
                          }, {
                            default: withCtx(() => [
                              _hoisted_14$1
                            ]),
                            _: 2
                          }, 1032, ["onClick"])
                        ]),
                        createBaseVNode("input", {
                          value: getPriceDisplay(item),
                          type: "text",
                          inputmode: "decimal",
                          class: "input-quantity input-price",
                          onFocus: ($event) => startEditingPrice(item.id, $event),
                          onBlur: ($event) => formatPrice(item.id, $event),
                          onInput: (e) => {
                            const val = e.target.value;
                            item.price = parseFloat(val) || item.price;
                            unref(bagStore).calculaTotalBag();
                          }
                        }, null, 40, _hoisted_15$1),
                        createBaseVNode("span", _hoisted_16$1, [
                          _hoisted_17$1,
                          _hoisted_18$1,
                          createBaseVNode("b", null, toDisplayString(getUnitLabel(item.unitTypeId)), 1)
                        ])
                      ])
                    ]),
                    createBaseVNode("div", _hoisted_19$1, [
                      createVNode(_component_VButton, {
                        color: "danger",
                        class: "btn-sm is-light second-buton btn-remove-product",
                        onClick: ($event) => clickRemoveProduct(index)
                      }, {
                        default: withCtx(() => [
                          _hoisted_20$1
                        ]),
                        _: 2
                      }, 1032, ["onClick"]),
                      createBaseVNode("p", {
                        class: "m-1 has-text-weight-bold item-price item-price-total",
                        title: "S/" + (item.price * item.quantity).toFixed(2)
                      }, " S/" + toDisplayString((item.price * item.quantity).toFixed(2)), 9, _hoisted_21$1)
                    ])
                  ])
                ]);
              }), 128))
            ]),
            createBaseVNode("div", _hoisted_22$1, [
              createBaseVNode("div", _hoisted_23$1, [
                _hoisted_24$1,
                createBaseVNode("span", _hoisted_25$1, [
                  createBaseVNode("span", null, toDisplayString(unref(bagStore).getProducts().length) + " Items", 1),
                  unref(bagStore).getProducts().length > 0 ? (openBlock(), createElementBlock("span", _hoisted_26$1, " | " + toDisplayString(getUnitSummaryText()), 1)) : createCommentVNode("", true)
                ])
              ]),
              props.parent == void 0 ? (openBlock(), createBlock(_component_VButton, {
                key: 0,
                color: "primary",
                class: "px-4 btn-finishing",
                raised: "",
                bold: "",
                fullwidth: "",
                disabled: !isOnLine.value,
                onClick: finalizeSale
              }, {
                default: withCtx(() => [
                  _hoisted_27$1,
                  props.parent == void 0 && unref(bagStore).getProducts().length > 0 ? (openBlock(), createElementBlock("p", _hoisted_28$1, [
                    _hoisted_29$1,
                    createBaseVNode("span", _hoisted_30$1, " S/ " + toDisplayString(unref(bagStore).total.toFixed(2)), 1)
                  ])) : createCommentVNode("", true)
                ]),
                _: 1
              }, 8, ["disabled"])) : createCommentVNode("", true)
            ])
          ])
        ])
      ]);
    };
  }
});
const sectionsData = [
  {
    id: 0,
    icon: "/images/icons/food/icon-1.svg",
    label: "Todos",
    selected: true
  },
  {
    id: 1,
    icon: "/images/icons/food/icon-2.svg",
    label: "Criollo",
    selected: false
  },
  {
    id: 2,
    icon: "/images/icons/food/icon-3.svg",
    label: "Marino",
    selected: false
  },
  {
    id: 3,
    icon: "/images/icons/food/icon-4.svg",
    label: "Postres",
    selected: false
  },
  {
    id: 4,
    icon: "/images/icons/food/icon-5.svg",
    label: "Bebidas",
    selected: false
  },
  {
    id: 5,
    icon: "/images/icons/food/icon-6.svg",
    label: "Carnes",
    selected: false
  },
  {
    id: 6,
    icon: "/images/icons/food/icon-7.svg",
    label: "Pastas",
    selected: false
  },
  {
    id: 7,
    icon: "/images/icons/food/icon-8.svg",
    label: "Pizzas",
    selected: false
  }
];
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
const _hoisted_7 = { class: "pt-5 searchCategory-content columns" };
const _hoisted_8 = {
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 14 14"
};
const _hoisted_9 = ["stroke"];
const _hoisted_10 = { class: "tooltip-text" };
const _hoisted_11 = { class: "column is-2 px-0 tab-list-container" };
const _hoisted_12 = { class: "tabs-wrapper" };
const _hoisted_13 = { class: "tabs-inner" };
const _hoisted_14 = { class: "tabs tabs-list is-centered mb-1" };
const _hoisted_15 = {
  style: { "border": "none" },
  class: "m-0"
};
const _hoisted_16 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:grid"
  })
], -1);
const _hoisted_17 = [
  _hoisted_16
];
const _hoisted_18 = /* @__PURE__ */ createBaseVNode("span", null, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "iconify",
    "data-icon": "feather:list"
  })
], -1);
const _hoisted_19 = [
  _hoisted_18
];
const _hoisted_20 = /* @__PURE__ */ createBaseVNode("li", { class: "tab-naver" }, null, -1);
const _hoisted_21 = {
  key: 0,
  class: "restaurants-list"
};
const _hoisted_22 = {
  key: 1,
  style: { "padding-top": "1%" },
  class: "flex-table"
};
const _hoisted_23 = /* @__PURE__ */ createBaseVNode("div", { class: "flex-table-header" }, [
  /* @__PURE__ */ createBaseVNode("span", { class: "is-grow" }, "Producto"),
  /* @__PURE__ */ createBaseVNode("span", null, "Codigo"),
  /* @__PURE__ */ createBaseVNode("span", { class: "cell-end" }, "Precio")
], -1);
const _hoisted_24 = { class: "flex-list-inner" };
const _hoisted_25 = ["onClick"];
const _hoisted_26 = { class: "flex-table-cell is-media is-grow" };
const _hoisted_27 = { class: "item-name dark-inverted" };
const _hoisted_28 = {
  class: "flex-table-cell",
  "data-th": "C\xF3digo"
};
const _hoisted_29 = { class: "light-text" };
const _hoisted_30 = {
  class: "flex-table-cell cell-end",
  "data-th": "Precio"
};
const _hoisted_31 = { class: "item-price-list" };
const _hoisted_32 = /* @__PURE__ */ createBaseVNode("div", {
  class: "is-hidden-desktop",
  style: { "min-height": "150px" }
}, null, -1);
const _hoisted_33 = {
  key: 0,
  class: "is-hidden-desktop cart-bottom-fixed h-hidden-tablet-l"
};
const _hoisted_34 = { class: "columns columns-new is-mobile is-centered m-custom" };
const _hoisted_35 = { class: "column is-11" };
const _hoisted_36 = { class: "right fixed-parent is-hidden-mobile h-hidden-tablet-p only-pos" };
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const userSession2 = useUserSession();
    const productSession = useProductSession();
    const notif = useNotyf();
    const textSearch = ref("");
    const searchByBarcode = ref(false);
    const bagStore = useBagStore();
    const isListItems = ref(false);
    const isBarcodeActive = ref(false);
    const tooltipMessage = ref("Activar b\xFAsqueda por c\xF3digo de barras");
    const chunks = ref([]);
    const chunkSize = 50;
    const visibleProductCount = ref(chunkSize);
    const loadingFavorite = ref(false);
    const idProductFavorite = ref();
    const posBag = reactive({
      products: [],
      total: 0,
      barman: userSession2.name,
      total_quantity: 0
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
        let hasProduct = productIndexByBarcode.has(product.barcode);
        if (hasProduct) {
          let item = productIndexByBarcode.get(product.barcode);
          let array = Array.isArray(item) ? [...item] : [item];
          array.push(product);
          productIndexByBarcode.set(product.barcode, array);
        } else {
          productIndexByBarcode.set(product.barcode, product);
        }
      }
      if (product.internalId) {
        productIndexByInternalId.set(product.internalId.toLowerCase(), product);
      }
    });
    const clickAddProduct = (item) => {
      bagStore.addProduct(item);
    };
    const clickAddFavoriteProduct = async (id) => {
      loadingFavorite.value = true;
      idProductFavorite.value = id;
      let payload = {
        id
      };
      try {
        const response = await provideApi().post(`/sellnow/favoriteitem`, payload);
        const data = response.data;
        if (!data.success) {
          notif.error(data.message);
          loadingFavorite.value = false;
          return false;
        }
        notif.success(data.message);
        await MasterService.saveDataProducts();
        window.location.reload();
        loadingFavorite.value = false;
        return true;
      } catch (error) {
        notif.error("Producto no encontrado");
        loadingFavorite.value = false;
        return false;
      }
    };
    const openDialogDocument = () => {
      if (bagStore.getProducts().length > 0) {
        posBag.products = bagStore.getProducts();
        posBag.total = bagStore.getTotal();
        posBag.total_quantity = bagStore.getTotalQuantity();
        state.openDialogDocument = true;
      }
    };
    const closeDialogDocument = () => {
      posBag.products = [];
      posBag.total = 0;
      posBag.enterAmount = 0;
      bagStore.reset();
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
      const allProducts = state.products;
      chunks.value = [];
      for (let i = 0; i < allProducts.length; i += chunkSize) {
        chunks.value.push(allProducts.slice(i, i + chunkSize));
      }
    };
    const removeAccents = (name) => {
      return name ? name.normalize("NFD").replace(/[\u0300-\u036f]/g, "") : "";
    };
    const products_filter = computed(() => filteredProducts.value);
    watch(textSearch, () => {
      if (textSearch.value.length > 0) {
        if (searchByBarcode.value) {
          debouncedSearchBarcode(() => {
            let itemFound;
            let value = textSearch.value.toUpperCase();
            itemFound = productIndexByBarcode.get(value);
            if (!itemFound) {
              value = textSearch.value.toLowerCase();
              itemFound = productIndexByBarcode.get(value);
            }
            let isIterableItemFound = Array.isArray(itemFound);
            if (itemFound) {
              if (!isIterableItemFound) {
                bagStore.addProduct(itemFound);
                notif.success("Producto a\xF1adido!");
              }
            } else {
              notif.error("Producto no encontrado");
            }
            if (!isIterableItemFound) {
              textSearch.value = "";
            }
            filteredProducts.value = isIterableItemFound ? [...itemFound] : [itemFound];
          });
        } else {
          debouncedSearch(() => {
            const lowerSearchText = textSearch.value.toLowerCase();
            filteredProducts.value = chunks.value.flatMap((chunk) => chunk.filter((item) => {
              var _a;
              return removeAccents(item.name || "").toLowerCase().includes(removeAccents(lowerSearchText)) || productIndexByInternalId.has(lowerSearchText) && ((_a = productIndexByInternalId.get(lowerSearchText)) == null ? void 0 : _a.internalId) === item.internalId || findSimilarWords(lowerSearchText, item.name);
            }));
          });
        }
      } else {
        debounceTimeout && clearTimeout(debounceTimeout);
        filteredProductsDefault();
      }
    });
    const findSimilarWords = (text, name) => {
      let arrayText = text.split(" ");
      if (name) {
        return arrayText.every((element) => removeAccents(name || "").toLowerCase().search(new RegExp(element)) !== -1);
      }
      return false;
    };
    const back = () => {
      console.log("back");
      state.openDialogDocument = false;
    };
    const selectCategory = (id) => {
      textSearch.value = "";
      if (id === 0) {
        filteredProducts.value = state.products_filter;
        filteredProductsDefault();
      } else {
        filteredProducts.value = state.products_filter.filter((x) => x.categoryId === id);
      }
    };
    const changeSearchByBarcode = () => {
      isBarcodeActive.value = !isBarcodeActive.value;
      tooltipMessage.value = isBarcodeActive.value ? "Desactivar b\xFAsqueda por c\xF3digo de barras" : "Activar b\xFAsqueda por c\xF3digo de barras";
      let byBarcode = searchByBarcode.value ? 1 : 0;
      userSession2.setSearchByBarcode(byBarcode);
    };
    const loadMoreProducts = () => {
      if (visibleProductCount.value < state.products_filter.length) {
        visibleProductCount.value += chunkSize;
        filteredProducts.value = state.products_filter.slice(0, visibleProductCount.value);
      }
    };
    const filteredProductsDefault = () => {
      filteredProducts.value = state.products_filter.slice(0, visibleProductCount.value);
    };
    const onScroll = () => {
      const scrollTop = window.scrollY;
      const windowHeight = window.innerHeight;
      const docHeight = document.documentElement.scrollHeight;
      if (scrollTop + windowHeight >= docHeight - 100 && textSearch.value.length <= 0) {
        loadMoreProducts();
      }
    };
    onMounted(() => {
      searchByBarcode.value = userSession2.searchByBarcode ? true : false;
      isBarcodeActive.value = userSession2.searchByBarcode ? true : false;
      filteredProductsDefault();
      const searchCategoryContent = document.querySelector(".searchCategory-content");
      const categoryContent = document.querySelector(".category-content");
      if (!searchCategoryContent || !categoryContent)
        return;
      const offsetTop = searchCategoryContent.offsetTop;
      const handleScroll = () => {
        const shouldStick = window.scrollY >= offsetTop;
        searchCategoryContent.classList.toggle("is-sticky", shouldStick);
        if (shouldStick) {
          categoryContent.style.marginBottom = "50px";
        } else {
          categoryContent.style.marginBottom = "0";
        }
      };
      window.addEventListener("scroll", handleScroll);
      window.addEventListener("scroll", onScroll);
      loadProducts();
    });
    onUnmounted(() => {
      window.removeEventListener("scroll", onScroll);
    });
    return (_ctx, _cache) => {
      const _component_CashDialog = _sfc_main$f;
      const _component_DocumentDialog = _sfc_main$8;
      const _component_CategoriesNavBar = _sfc_main$7;
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$h;
      const _component_ProductPos = __unplugin_components_5;
      const _component_VAvatar = _sfc_main$m;
      const _component_CartMobile = _sfc_main$4;
      const _component_Cart = __unplugin_components_8;
      const _component_Bag = _sfc_main$2;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_CashDialog),
        createVNode(_component_DocumentDialog, {
          "pos-bag": unref(posBag),
          open: unref(state).openDialogDocument,
          onClose: closeDialogDocument,
          onBack: back
        }, null, 8, ["pos-bag", "open"]),
        createBaseVNode("div", _hoisted_1$1, [
          createBaseVNode("div", _hoisted_2, [
            createBaseVNode("div", _hoisted_3, [
              createBaseVNode("div", _hoisted_4, [
                createBaseVNode("div", _hoisted_5, [
                  createBaseVNode("div", _hoisted_6, [
                    createVNode(_component_CategoriesNavBar, { onSelectcategory: selectCategory })
                  ])
                ]),
                createBaseVNode("div", _hoisted_7, [
                  createVNode(_component_VField, { class: "search-conteiner column is-10" }, {
                    default: withCtx(() => [
                      createVNode(_component_VControl, {
                        class: "input-container",
                        icon: "feather:search"
                      }, {
                        default: withCtx(() => [
                          withDirectives(createBaseVNode("input", {
                            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => textSearch.value = $event),
                            type: "text",
                            class: "input is-rounded",
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
                              (openBlock(), createElementBlock("svg", _hoisted_8, [
                                createBaseVNode("path", {
                                  fill: "none",
                                  stroke: isBarcodeActive.value ? "#4caf50" : "#cfcfcf",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M5.11.656H1.208a.49.49 0 0 0-.488.488v3.904m12.686 0V1.144a.49.49 0 0 0-.488-.488H9.014m0 12.688h3.904a.49.49 0 0 0 .488-.488V8.952m-12.687 0v3.904a.49.49 0 0 0 .488.488H5.11m5.696-9.552v6.416M3.194 3.792v6.416m5.709-6.416v4.666m0 1.75v-.291M7 3.792v4.666m0 1.75v-.291M5.097 3.792v4.666m0 1.75v-.291"
                                }, null, 8, _hoisted_9)
                              ]))
                            ], 2),
                            createBaseVNode("span", _hoisted_10, toDisplayString(tooltipMessage.value), 1)
                          ])
                        ]),
                        _: 1
                      })
                    ]),
                    _: 1
                  }),
                  createBaseVNode("div", _hoisted_11, [
                    createBaseVNode("div", _hoisted_12, [
                      createBaseVNode("div", _hoisted_13, [
                        createBaseVNode("div", _hoisted_14, [
                          createBaseVNode("ul", _hoisted_15, [
                            createBaseVNode("li", {
                              class: normalizeClass([isListItems.value == false && "is-active"])
                            }, [
                              createBaseVNode("a", {
                                onClick: _cache[2] || (_cache[2] = ($event) => isListItems.value = false)
                              }, _hoisted_17)
                            ], 2),
                            createBaseVNode("li", {
                              class: normalizeClass([isListItems.value == true && "is-active"])
                            }, [
                              createBaseVNode("a", {
                                onClick: _cache[3] || (_cache[3] = ($event) => isListItems.value = true)
                              }, _hoisted_19)
                            ], 2),
                            _hoisted_20
                          ])
                        ])
                      ])
                    ])
                  ])
                ]),
                !isListItems.value ? (openBlock(), createElementBlock("div", _hoisted_21, [
                  createBaseVNode("div", {
                    class: normalizeClass(["product-container", unref(productGridClass)])
                  }, [
                    (openBlock(true), createElementBlock(Fragment, null, renderList(unref(products_filter), (product) => {
                      return openBlock(), createElementBlock("div", {
                        key: product.id,
                        class: ""
                      }, [
                        createVNode(_component_ProductPos, {
                          product,
                          loading: loadingFavorite.value && idProductFavorite.value == product.id ? true : false,
                          onClickAddProduct: clickAddProduct,
                          onClickAddFavoriteProduct: clickAddFavoriteProduct
                        }, null, 8, ["product", "loading"])
                      ]);
                    }), 128))
                  ], 2)
                ])) : (openBlock(), createElementBlock("div", _hoisted_22, [
                  _hoisted_23,
                  createBaseVNode("div", _hoisted_24, [
                    createVNode(TransitionGroup, {
                      name: "list",
                      tag: "div"
                    }, {
                      default: withCtx(() => [
                        (openBlock(true), createElementBlock(Fragment, null, renderList(unref(products_filter), (item) => {
                          return openBlock(), createElementBlock("div", {
                            key: item.id,
                            class: "flex-table-item",
                            style: { "cursor": "pointer" },
                            onClick: ($event) => clickAddProduct(item)
                          }, [
                            createBaseVNode("div", _hoisted_26, [
                              createVNode(_component_VAvatar, {
                                picture: item.imageUrl,
                                size: "medium"
                              }, null, 8, ["picture"]),
                              createBaseVNode("div", null, [
                                createBaseVNode("span", _hoisted_27, toDisplayString(item.name), 1)
                              ])
                            ]),
                            createBaseVNode("div", _hoisted_28, [
                              createBaseVNode("span", _hoisted_29, toDisplayString(item.internalId), 1)
                            ]),
                            createBaseVNode("div", _hoisted_30, [
                              createBaseVNode("span", _hoisted_31, toDisplayString(item.currencyTypeSymbol) + " " + toDisplayString(item.price.toFixed(2)), 1)
                            ])
                          ], 8, _hoisted_25);
                        }), 128))
                      ]),
                      _: 1
                    })
                  ])
                ]))
              ])
            ]),
            _hoisted_32
          ]),
          unref(bagStore).getProducts().length > 0 ? (openBlock(), createElementBlock("div", _hoisted_33, [
            createBaseVNode("div", _hoisted_34, [
              createBaseVNode("div", _hoisted_35, [
                createVNode(_component_CartMobile, { onFinalizeSale: openDialogDocument }),
                createVNode(_component_Cart)
              ])
            ])
          ])) : createCommentVNode("", true),
          createBaseVNode("div", _hoisted_36, [
            createVNode(_component_Bag, { onFinalizeSale: openDialogDocument })
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
      const _component_LockedScreen = _sfc_main$g;
      const _component_PosRestaurant = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_LockedScreen),
        createVNode(_component_PosRestaurant)
      ]);
    };
  }
});
export { _sfc_main as default };
