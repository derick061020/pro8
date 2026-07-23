import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$1 } from "./VField.152a42ff.js";
import { _ as _sfc_main$2 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$3 } from "./VModal.9533b2d3.js";
import { b as defineComponent, N as Notyf, a5 as useRoute, r as ref, t as reactive, a as computed, o as onMounted, f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, w as createVNode, a6 as withDirectives, al as vModelSelect, g as createElementBlock, A as renderList, z as toDisplayString, F as Fragment, Z as unref, C as createCommentVNode, D as createTextVNode } from "./vendor.05b8b5b5.js";
import { j as useCashSession } from "./ProductModifiersDialog.637fd331.js";
import { u as useUserSession, p as provideApi } from "./index.2bb13d6f.js";
var LockedScreen_vue_vue_type_style_index_0_lang = "";
const _hoisted_1 = { class: "columns is-multiline is-mobile" };
const _hoisted_2 = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, "Seleccione un vendedor")
], -1);
const _hoisted_3 = { class: "column is-12" };
const _hoisted_4 = { class: "select" };
const _hoisted_5 = ["value"];
const _hoisted_6 = { class: "columns is-multiline is-mobile" };
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("div", { class: "column is-12" }, [
  /* @__PURE__ */ createBaseVNode("h6", { class: "has-text-centered" }, "Ingresar PIN")
], -1);
const _hoisted_8 = { class: "column is-12 has-text-centered is-flex is-flex-direction-column is-align-self-flex-end" };
const _hoisted_9 = {
  key: 0,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_10 = {
  key: 1,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_11 = {
  key: 0,
  class: "pin-loading-overlay is-overlay is-flex is-justify-content-center is-align-items-center",
  style: { "z-index": "10", "background-color": "rgba(255, 255, 255, 0.7)", "top": "0", "left": "0", "right": "0", "bottom": "0", "position": "absolute" }
};
const _hoisted_12 = /* @__PURE__ */ createBaseVNode("i", {
  class: "loader is-loading is-large",
  style: { "font-size": "48px" }
}, null, -1);
const _hoisted_13 = [
  _hoisted_12
];
const _hoisted_14 = { class: "column is-12" };
const _hoisted_15 = { class: "columns is-multiline is-mobile has-text-centered" };
const _hoisted_16 = { class: "column is-4" };
const _hoisted_17 = /* @__PURE__ */ createTextVNode("1");
const _hoisted_18 = { class: "column is-4" };
const _hoisted_19 = /* @__PURE__ */ createTextVNode("2");
const _hoisted_20 = { class: "column is-4" };
const _hoisted_21 = /* @__PURE__ */ createTextVNode("3");
const _hoisted_22 = { class: "column is-4 pt-0" };
const _hoisted_23 = /* @__PURE__ */ createTextVNode("4");
const _hoisted_24 = { class: "column is-4 pt-0" };
const _hoisted_25 = /* @__PURE__ */ createTextVNode("5");
const _hoisted_26 = { class: "column is-4 pt-0" };
const _hoisted_27 = /* @__PURE__ */ createTextVNode("6");
const _hoisted_28 = { class: "column is-4 pt-0" };
const _hoisted_29 = /* @__PURE__ */ createTextVNode("7");
const _hoisted_30 = { class: "column is-4 pt-0" };
const _hoisted_31 = /* @__PURE__ */ createTextVNode("8");
const _hoisted_32 = { class: "column is-4 pt-0" };
const _hoisted_33 = /* @__PURE__ */ createTextVNode("9");
const _hoisted_34 = { class: "column is-4 pt-0" };
const _hoisted_35 = /* @__PURE__ */ createTextVNode("Limpiar");
const _hoisted_36 = { class: "column is-4 pt-0" };
const _hoisted_37 = /* @__PURE__ */ createTextVNode("0");
const _hoisted_38 = { class: "column is-4 pt-0" };
const _hoisted_39 = /* @__PURE__ */ createTextVNode("Borrar");
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const notyf = new Notyf();
    useCashSession();
    useRoute();
    const userSession = useUserSession();
    const sellerId = userSession.sellerId;
    const isLoading = ref(false);
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
      const isBlocked = userSession.isBlockedPin;
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
        isLoading.value = true;
        const response = await provideApi().get(`/restaurant/correct_pin_check/${id}/${pin2}`);
        const data = response.data;
        if (!data.success) {
          userSession.setIsBlockedPin(1);
          notyf.error("Pin incorrecto");
        } else {
          userSession.setIsBlockedPin(0);
          userSession.setSellerId(id);
          userSession.setSellerName(data.data.name);
          isDialogOpen.value = false;
          notyf.success("Pin correcto");
          formLocked.pinSecret = "";
          formLocked.pinText = "";
        }
      } catch (error) {
        console.error("Error data:", error);
        userSession.setIsBlockedPin(1);
      } finally {
        isLoading.value = false;
      }
    };
    onMounted(() => {
      user_id.value = sellerId;
      availableSellers();
    });
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$1;
      const _component_VButton = _sfc_main$2;
      const _component_VModal = _sfc_main$3;
      return openBlock(), createBlock(_component_VModal, {
        open: unref(openDialogForm),
        title: "",
        size: "small",
        actions: "right",
        hidefooter: "",
        bglight: "",
        withmenu: ""
      }, {
        content: withCtx(() => [
          createBaseVNode("div", _hoisted_1, [
            _hoisted_2,
            createBaseVNode("div", _hoisted_3, [
              createVNode(_component_VField, null, {
                default: withCtx(() => [
                  createVNode(_component_VControl, null, {
                    default: withCtx(() => [
                      createBaseVNode("div", _hoisted_4, [
                        withDirectives(createBaseVNode("select", {
                          "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => user_id.value = $event)
                        }, [
                          (openBlock(true), createElementBlock(Fragment, null, renderList(users.value, (user) => {
                            return openBlock(), createElementBlock("option", {
                              key: user.id,
                              value: user.id
                            }, toDisplayString(user.name), 9, _hoisted_5);
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
          createBaseVNode("div", _hoisted_6, [
            _hoisted_7,
            createBaseVNode("div", _hoisted_8, [
              unref(formLocked).pinSecret != "" ? (openBlock(), createElementBlock("h4", _hoisted_9, toDisplayString(unref(formLocked).pinSecret), 1)) : (openBlock(), createElementBlock("h4", _hoisted_10, "----"))
            ]),
            isLoading.value ? (openBlock(), createElementBlock("div", _hoisted_11, _hoisted_13)) : createCommentVNode("", true),
            createBaseVNode("div", _hoisted_14, [
              createBaseVNode("div", _hoisted_15, [
                createBaseVNode("div", _hoisted_16, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[1] || (_cache[1] = ($event) => addPinText("1"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_17
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_18, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[2] || (_cache[2] = ($event) => addPinText("2"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_19
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_20, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[3] || (_cache[3] = ($event) => addPinText("3"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_21
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_22, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[4] || (_cache[4] = ($event) => addPinText("4"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_23
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_24, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[5] || (_cache[5] = ($event) => addPinText("5"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_25
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_26, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[6] || (_cache[6] = ($event) => addPinText("6"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_27
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_28, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[7] || (_cache[7] = ($event) => addPinText("7"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_29
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_30, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[8] || (_cache[8] = ($event) => addPinText("8"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_31
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_32, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[9] || (_cache[9] = ($event) => addPinText("9"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_33
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_34, [
                  createVNode(_component_VButton, {
                    color: "danger",
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[10] || (_cache[10] = ($event) => clearPinText())
                  }, {
                    default: withCtx(() => [
                      _hoisted_35
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_36, [
                  createVNode(_component_VButton, {
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[11] || (_cache[11] = ($event) => addPinText("0"))
                  }, {
                    default: withCtx(() => [
                      _hoisted_37
                    ]),
                    _: 1
                  })
                ]),
                createBaseVNode("div", _hoisted_38, [
                  createVNode(_component_VButton, {
                    color: "danger",
                    size: "big",
                    class: "is-fullwidth",
                    onClick: _cache[12] || (_cache[12] = ($event) => deletePinText())
                  }, {
                    default: withCtx(() => [
                      _hoisted_39
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
export { _sfc_main as _ };
