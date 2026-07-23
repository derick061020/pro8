import { b as defineComponent, a4 as useRouter, r as ref, f as openBlock, g as createElementBlock, a5 as useRoute, t as reactive, e as useHead, o as onMounted, x as resolveComponent, X as createBaseVNode, w as createVNode, B as withCtx, F as Fragment, A as renderList, v as createBlock, Y as normalizeClass, D as createTextVNode, z as toDisplayString, I as withModifiers, Z as unref, C as createCommentVNode, j as axios } from "./vendor.05b8b5b5.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$2 } from "./VField.152a42ff.js";
import { _ as _sfc_main$4 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$3 } from "./VModal.9533b2d3.js";
import { u as useUserSession, a as useCompanySession, p as provideApi, N as NAME_ROUTE_POS, R as ROLES, c as useNotyf } from "./index.2bb13d6f.js";
import { u as use, M as MasterService } from "./masterService.8a758d8d.js";
var _imports_0$1 = "/mozo/isotipo-oficial.png";
var AnimatedLogo_vue_vue_type_style_index_0_scoped_true_lang = "";
const _hoisted_1$1 = {
  src: _imports_0$1,
  alt: "logo"
};
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    light: { type: Boolean, required: false }
  },
  setup(__props) {
    const router = useRouter();
    const isLoading = ref(false);
    router.beforeEach(() => {
      isLoading.value = true;
    });
    router.afterEach(() => {
      setTimeout(() => {
        isLoading.value = false;
      }, 200);
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("img", _hoisted_1$1);
    };
  }
});
var __unplugin_components_0 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-427b10f0"]]);
var _imports_0 = "/mozo/assets/team-mozo.b539f4ed.svg";
var mozo_vue_vue_type_style_index_0_lang = "";
const _hoisted_1 = { class: "auth-wrapper-inner is-single" };
const _hoisted_2 = { class: "auth-nav" };
const _hoisted_3 = /* @__PURE__ */ createBaseVNode("div", { class: "left" }, null, -1);
const _hoisted_4 = { class: "center" };
const _hoisted_5 = /* @__PURE__ */ createBaseVNode("div", { class: "right" }, null, -1);
const _hoisted_6 = { class: "saas-billing-wrapper" };
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("div", { style: { "text-align": "center" } }, [
  /* @__PURE__ */ createBaseVNode("img", {
    src: _imports_0,
    alt: "Lista de Mozos",
    style: { "width": "480px", "filter": "drop-shadow(0 5px 10px rgba(1, 167, 105, 0.24))" }
  }),
  /* @__PURE__ */ createBaseVNode("h2", { class: "p-5" }, "Seleccione su nombre:")
], -1);
const _hoisted_8 = { class: "plans-wrapper" };
const _hoisted_9 = { class: "left" };
const _hoisted_10 = { class: "inner-wrap" };
const _hoisted_11 = { class: "meta" };
const _hoisted_12 = /* @__PURE__ */ createBaseVNode("svg", {
  xmlns: "http://www.w3.org/2000/svg",
  width: "24",
  height: "24",
  viewBox: "0 0 24 24",
  fill: "none",
  stroke: "#01a769",
  "stroke-width": "2",
  "stroke-linecap": "round",
  "stroke-linejoin": "round",
  class: "icon icon-tabler icons-tabler-outline icon-tabler-circle-check"
}, [
  /* @__PURE__ */ createBaseVNode("path", {
    stroke: "none",
    d: "M0 0h24v24H0z",
    fill: "none"
  }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }),
  /* @__PURE__ */ createBaseVNode("path", { d: "M9 12l2 2l4 -4" })
], -1);
const _hoisted_13 = { class: "columns is-multiline is-mobile" };
const _hoisted_14 = { class: "column is-12" };
const _hoisted_15 = { class: "single-form-wrap is-relative" };
const _hoisted_16 = { class: "inner-wrap" };
const _hoisted_17 = { class: "form-card" };
const _hoisted_18 = ["onSubmit"];
const _hoisted_19 = { class: "login-form" };
const _hoisted_20 = /* @__PURE__ */ createBaseVNode("div", { class: "has-text-centered mb-4" }, [
  /* @__PURE__ */ createBaseVNode("p", null, "Ingrese su PIN:")
], -1);
const _hoisted_21 = { class: "column is-12 has-text-centered is-flex is-flex-direction-column is-align-self-flex-end" };
const _hoisted_22 = {
  key: 0,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_23 = {
  key: 1,
  class: "title is-3 is-narrow toc-ignore"
};
const _hoisted_24 = {
  key: 0,
  class: "pin-loading-overlay is-overlay is-flex is-justify-content-center is-align-items-center",
  style: { "z-index": "10", "background-color": "rgba(255, 255, 255, 0.7)", "top": "0", "left": "0", "right": "0", "bottom": "0", "position": "absolute" }
};
const _hoisted_25 = /* @__PURE__ */ createBaseVNode("i", {
  class: "loader is-loading is-large",
  style: { "font-size": "48px" }
}, null, -1);
const _hoisted_26 = [
  _hoisted_25
];
const _hoisted_27 = { class: "column is-12" };
const _hoisted_28 = { class: "columns is-multiline is-mobile has-text-centered" };
const _hoisted_29 = { class: "column is-4" };
const _hoisted_30 = /* @__PURE__ */ createTextVNode("1");
const _hoisted_31 = { class: "column is-4" };
const _hoisted_32 = /* @__PURE__ */ createTextVNode("2");
const _hoisted_33 = { class: "column is-4" };
const _hoisted_34 = /* @__PURE__ */ createTextVNode("3");
const _hoisted_35 = { class: "column is-4 pt-0" };
const _hoisted_36 = /* @__PURE__ */ createTextVNode("4");
const _hoisted_37 = { class: "column is-4 pt-0" };
const _hoisted_38 = /* @__PURE__ */ createTextVNode("5");
const _hoisted_39 = { class: "column is-4 pt-0" };
const _hoisted_40 = /* @__PURE__ */ createTextVNode("6");
const _hoisted_41 = { class: "column is-4 pt-0" };
const _hoisted_42 = /* @__PURE__ */ createTextVNode("7");
const _hoisted_43 = { class: "column is-4 pt-0" };
const _hoisted_44 = /* @__PURE__ */ createTextVNode("8");
const _hoisted_45 = { class: "column is-4 pt-0" };
const _hoisted_46 = /* @__PURE__ */ createTextVNode("9");
const _hoisted_47 = { class: "column is-4 pt-0" };
const _hoisted_48 = /* @__PURE__ */ createTextVNode("Limpiar");
const _hoisted_49 = { class: "column is-4 pt-0" };
const _hoisted_50 = /* @__PURE__ */ createTextVNode("0");
const _hoisted_51 = { class: "column is-4 pt-0" };
const _hoisted_52 = /* @__PURE__ */ createTextVNode("Borrar");
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const isLoading = ref(false);
    const router = useRouter();
    const route = useRoute();
    const notif = useNotyf();
    const userSession = useUserSession();
    const companySession = useCompanySession();
    const redirect = route.query.redirect;
    const waiters = ref([]);
    const selectedWaiterEmail = ref("");
    const dialogPinAvailable = ref(false);
    const modalTitle = ref("Bienvenido");
    const user = reactive({
      email: "",
      password: "",
      domain: "",
      ssl: "https://"
    });
    if (userSession.url) {
      user.domain = userSession.url;
    }
    if (userSession.email) {
      user.email = userSession.email;
    }
    if (userSession.pwd && userSession.getRole() == "MOZO") {
      user.password = userSession.pwd;
    }
    if (userSession.ssl) {
      user.ssl = userSession.ssl;
    }
    const handleLogin = async () => {
      try {
        user.email = selectedWaiterEmail.value;
        isLoading.value = true;
        notif.sync(`Sincronizando informaci\xF3n`);
        const response = await axios.post(`${user.ssl + user.domain}/api/login`, user);
        if (response.data.success) {
          const {
            email,
            logo,
            name,
            ruc,
            restaurant_role_code,
            establishment_id,
            seriedefault,
            success,
            token,
            company,
            sellerId,
            permission_edit_item_prices
          } = response.data;
          const dataUser = {
            email,
            logo,
            name,
            ruc,
            restaurant_role_code,
            establishment_id,
            seriedefault,
            success,
            token,
            sellerId,
            permission_edit_item_prices
          };
          userSession.setToken(dataUser.token);
          userSession.setEmail(dataUser.email);
          userSession.setName(dataUser.name);
          userSession.setUrl(user.domain);
          userSession.setPwd(user.password);
          userSession.setSsl(user.ssl);
          userSession.setEstablishmentId(dataUser.establishment_id);
          userSession.setIsBlockedPin(0);
          if (dataUser.permission_edit_item_prices) {
            userSession.setPermissionEditItemPrices(dataUser.permission_edit_item_prices);
          } else {
            userSession.setPermissionEditItemPrices(0);
          }
          if (dataUser.sellerId) {
            userSession.setSellerId(dataUser.sellerId);
            userSession.setSellerName(dataUser.name);
          }
          if (dataUser.restaurant_role_code) {
            userSession.setRole(dataUser.restaurant_role_code);
          } else {
            userSession.setRole("NOTHING");
          }
          if (company) {
            userSession.setUrlLogo(company.url_logo);
            userSession.setLogoBase64(company.logo_base64);
          }
          use.connect();
          use.sendCompany();
          await MasterService.syncData();
          notif.dismissAll();
          notif.success(`Bienvenido, ${dataUser.name}`);
          let role = {
            id: 0,
            name: "POS",
            code: "POS",
            menu: NAME_ROUTE_POS
          };
          role = ROLES.find((role2) => role2.code === dataUser.restaurant_role_code);
          if (redirect) {
            router.push(redirect);
          } else if (dataUser.restaurant_role_code && role.code != "ADM") {
            router.push({
              name: role.menu
            });
          } else {
            router.push({
              name: companySession.firstMenu
            });
          }
        } else {
          notif.dismissAll();
          notif.error("Datos de usuario incorrectos.");
        }
      } catch (error) {
        notif.error("Ocurri\xF3 un error al ingresar.");
      } finally {
        isLoading.value = false;
      }
    };
    const getListWaiter = async () => {
      try {
        const response = await provideApi().get(`/restaurant/list-waiter`);
        const data = response.data;
        if (data.success && data.data.length > 0) {
          waiters.value = data.data;
        }
      } catch (error) {
        console.error("Error data:", error);
      }
    };
    const selectWaiter = (email, name) => {
      selectedWaiterEmail.value = email;
      modalTitle.value = `Bienvenido ${name}`;
      user.password = "";
      dialogPinAvailable.value = true;
    };
    useHead({
      title: "Ingreso - Mozo.pe"
    });
    onMounted(async () => {
      const url = userSession.url;
      if (url) {
        user.domain = url;
      }
      getListWaiter();
    });
    const formLocked = reactive({
      pinText: "",
      pinSecret: "",
      userId: ""
    });
    const addPinText = (value) => {
      if (formLocked.pinText.length < 4) {
        formLocked.pinText += value;
        formLocked.pinSecret += "*";
        if (formLocked.pinText.length == 4) {
          user.password = formLocked.pinText;
          handleLogin();
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
    return (_ctx, _cache) => {
      const _component_LandingGrids = resolveComponent("LandingGrids");
      const _component_AnimatedLogo = __unplugin_components_0;
      const _component_RouterLink = resolveComponent("RouterLink");
      const _component_VLabel = resolveComponent("VLabel");
      const _component_VControl = __unplugin_components_1;
      const _component_VField = _sfc_main$2;
      const _component_VButton = _sfc_main$4;
      const _component_VModal = _sfc_main$3;
      return openBlock(), createElementBlock(Fragment, null, [
        createBaseVNode("div", _hoisted_1, [
          createVNode(_component_LandingGrids, { class: "is-contrasted" }),
          createBaseVNode("div", _hoisted_2, [
            _hoisted_3,
            createBaseVNode("div", _hoisted_4, [
              createVNode(_component_RouterLink, {
                to: "/",
                class: "header-item"
              }, {
                default: withCtx(() => [
                  createVNode(_component_AnimatedLogo, {
                    width: "38px",
                    height: "38px"
                  })
                ]),
                _: 1
              })
            ]),
            _hoisted_5
          ]),
          createBaseVNode("div", _hoisted_6, [
            _hoisted_7,
            createBaseVNode("div", _hoisted_8, [
              createBaseVNode("div", _hoisted_9, [
                createBaseVNode("div", _hoisted_10, [
                  createVNode(_component_VField, { class: "plans" }, {
                    default: withCtx(() => [
                      (openBlock(true), createElementBlock(Fragment, null, renderList(waiters.value, (waiter) => {
                        return openBlock(), createBlock(_component_VControl, {
                          key: waiter.email,
                          class: normalizeClass(["plan column", { selected: selectedWaiterEmail.value === waiter.email }]),
                          subcontrol: "",
                          style: { "cursor": "pointer" },
                          onClick: ($event) => selectWaiter(waiter.email, waiter.name)
                        }, {
                          default: withCtx(() => [
                            createVNode(_component_VLabel, {
                              raw: "",
                              class: normalizeClass(["plan-inner", { selected: selectedWaiterEmail.value === waiter.email }]),
                              onClick: ($event) => selectedWaiterEmail.value = waiter.email
                            }, {
                              default: withCtx(() => [
                                createBaseVNode("div", _hoisted_11, [
                                  createBaseVNode("h5", null, [
                                    _hoisted_12,
                                    createTextVNode(" " + toDisplayString(waiter.name), 1)
                                  ])
                                ])
                              ]),
                              _: 2
                            }, 1032, ["class", "onClick"])
                          ]),
                          _: 2
                        }, 1032, ["class", "onClick"]);
                      }), 128))
                    ]),
                    _: 1
                  })
                ])
              ])
            ])
          ])
        ]),
        createVNode(_component_VModal, {
          open: dialogPinAvailable.value,
          title: modalTitle.value,
          size: "small",
          actions: "center",
          "cancel-label": "Cerrar",
          onClose: _cache[12] || (_cache[12] = ($event) => dialogPinAvailable.value = false)
        }, {
          content: withCtx(() => [
            createBaseVNode("div", _hoisted_13, [
              createBaseVNode("div", _hoisted_14, [
                createBaseVNode("div", _hoisted_15, [
                  createBaseVNode("div", _hoisted_16, [
                    createBaseVNode("div", _hoisted_17, [
                      createBaseVNode("form", {
                        method: "post",
                        novalidate: "",
                        onSubmit: withModifiers(handleLogin, ["prevent"])
                      }, [
                        createBaseVNode("div", _hoisted_19, [
                          _hoisted_20,
                          createBaseVNode("div", _hoisted_21, [
                            unref(formLocked).pinSecret != "" ? (openBlock(), createElementBlock("h4", _hoisted_22, toDisplayString(unref(formLocked).pinSecret), 1)) : (openBlock(), createElementBlock("h4", _hoisted_23, "----"))
                          ]),
                          isLoading.value ? (openBlock(), createElementBlock("div", _hoisted_24, _hoisted_26)) : createCommentVNode("", true),
                          createBaseVNode("div", _hoisted_27, [
                            createBaseVNode("div", _hoisted_28, [
                              createBaseVNode("div", _hoisted_29, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[0] || (_cache[0] = ($event) => addPinText("1"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_30
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_31, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[1] || (_cache[1] = ($event) => addPinText("2"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_32
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_33, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[2] || (_cache[2] = ($event) => addPinText("3"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_34
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_35, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[3] || (_cache[3] = ($event) => addPinText("4"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_36
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_37, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[4] || (_cache[4] = ($event) => addPinText("5"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_38
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_39, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[5] || (_cache[5] = ($event) => addPinText("6"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_40
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_41, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[6] || (_cache[6] = ($event) => addPinText("7"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_42
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_43, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[7] || (_cache[7] = ($event) => addPinText("8"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_44
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_45, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[8] || (_cache[8] = ($event) => addPinText("9"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_46
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_47, [
                                createVNode(_component_VButton, {
                                  color: "danger",
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[9] || (_cache[9] = ($event) => clearPinText())
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_48
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_49, [
                                createVNode(_component_VButton, {
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[10] || (_cache[10] = ($event) => addPinText("0"))
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_50
                                  ]),
                                  _: 1
                                })
                              ]),
                              createBaseVNode("div", _hoisted_51, [
                                createVNode(_component_VButton, {
                                  color: "danger",
                                  size: "big",
                                  class: "is-fullwidth",
                                  onClick: _cache[11] || (_cache[11] = ($event) => deletePinText())
                                }, {
                                  default: withCtx(() => [
                                    _hoisted_52
                                  ]),
                                  _: 1
                                })
                              ])
                            ])
                          ])
                        ])
                      ], 40, _hoisted_18)
                    ])
                  ])
                ])
              ])
            ])
          ]),
          _: 1
        }, 8, ["open", "title"])
      ], 64);
    };
  }
});
export { _sfc_main as default };
