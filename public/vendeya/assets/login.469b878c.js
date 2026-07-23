import { i as isDark, t as toggleDarkModeHandler, _ as __unplugin_components_1 } from "./IsotipoMozoOficial.415c8e31.js";
import { _ as __unplugin_components_0 } from "./LogoMozoOficial.2c735f28.js";
import { _ as __unplugin_components_1$1 } from "./VControl.55044e40.js";
import { _ as _sfc_main$1 } from "./VField.dd3df504.js";
import { _ as _sfc_main$2 } from "./VButton.ec3d6c22.js";
import { b as defineComponent, r as ref, a4 as useRouter, a5 as useRoute, t as reactive, e as useHead, L as watch, o as onMounted, x as resolveComponent, f as openBlock, g as createElementBlock, X as createBaseVNode, w as createVNode, Z as unref, B as withCtx, I as withModifiers, j as axios, a6 as withDirectives, a7 as vModelText, D as createTextVNode } from "./vendor.80d481a8.js";
import { u as useUserSession, b as brandName, N as NAME_ROUTE_POS, R as ROLES, a as useNotyf } from "./index.ea91e044.js";
import { u as useCompanySession, M as MasterService } from "./masterService.8bbc48d4.js";
import "./plugin-vue_export-helper.5a098b48.js";
var login_vue_vue_type_style_index_0_lang = "";
const _hoisted_1 = { class: "auth-wrapper-inner columns is-gapless" };
const _hoisted_2 = { class: "column login-column is-7-desktop is-6-tablet h-hidden-mobile h-hidden-tablet-p hero-banner" };
const _hoisted_3 = { class: "hero login-hero is-fullheight is-app-grey" };
const _hoisted_4 = { class: "hero-body is-justify-content-center" };
const _hoisted_5 = { class: "columns" };
const _hoisted_6 = { class: "column is-12 has-text-centered" };
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("div", { class: "hero-footer" }, [
  /* @__PURE__ */ createBaseVNode("p", { class: "has-text-centered" })
], -1);
const _hoisted_8 = { class: "column is-5-desktop is-6-tablet" };
const _hoisted_9 = { class: "hero is-fullheight is-white" };
const _hoisted_10 = { class: "hero-heading" };
const _hoisted_11 = { class: "dark-mode ml-auto" };
const _hoisted_12 = ["checked"];
const _hoisted_13 = /* @__PURE__ */ createBaseVNode("span", null, null, -1);
const _hoisted_14 = { class: "auth-logo" };
const _hoisted_15 = { class: "hero-body" };
const _hoisted_16 = { class: "container" };
const _hoisted_17 = { class: "columns" };
const _hoisted_18 = { class: "column is-12" };
const _hoisted_19 = /* @__PURE__ */ createBaseVNode("div", { class: "auth-content" }, [
  /* @__PURE__ */ createBaseVNode("h2", null, "Bienvenido."),
  /* @__PURE__ */ createBaseVNode("p", null, "Por favor inicia sesi\xF3n en tu cuenta")
], -1);
const _hoisted_20 = { class: "auth-form-wrapper" };
const _hoisted_21 = ["onSubmit"];
const _hoisted_22 = { class: "login-form" };
const _hoisted_23 = /* @__PURE__ */ createTextVNode(" Ingresar ");
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const isLoading = ref(false);
    const router = useRouter();
    const route = useRoute();
    const notif = useNotyf();
    const userSession = useUserSession();
    const companySession = useCompanySession();
    const redirect = route.query.redirect;
    const user = reactive({
      email: "demo@gmail.com",
      password: "123456",
      domain: "restaurante.facturalo.pro",
      ssl: "https://"
    });
    if (userSession.url) {
      user.domain = userSession.url;
    }
    if (userSession.email) {
      user.email = userSession.email;
    }
    if (userSession.pwd) {
      user.password = userSession.pwd;
    }
    const handleLogin = async () => {
      try {
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
            sellerId
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
            sellerId
          };
          userSession.setToken(dataUser.token);
          userSession.setEmail(dataUser.email);
          userSession.setName(dataUser.name);
          userSession.setUrl(user.domain);
          userSession.setPwd(user.password);
          userSession.setSsl(user.ssl);
          userSession.setEstablishmentId(dataUser.establishment_id);
          userSession.setIsBlockedPin(0);
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
            userSession.setIsBusinessTurnTap(company.is_business_turn_tap);
          }
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
    useHead({
      title: `Ingreso - ${brandName.value}`
    });
    watch(brandName, (newName) => {
      useHead({
        title: `Ingreso - ${newName}`
      });
    });
    onMounted(async () => {
      const url = userSession.url;
      if (url) {
        user.domain = url;
      }
    });
    return (_ctx, _cache) => {
      const _component_IsotipoMozoOficial = __unplugin_components_1;
      const _component_LogoMozoOficial = __unplugin_components_0;
      const _component_RouterLink = resolveComponent("RouterLink");
      const _component_VControl = __unplugin_components_1$1;
      const _component_VField = _sfc_main$1;
      const _component_VButton = _sfc_main$2;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createBaseVNode("div", _hoisted_2, [
          createBaseVNode("div", _hoisted_3, [
            createBaseVNode("div", _hoisted_4, [
              createBaseVNode("div", _hoisted_5, [
                createBaseVNode("div", _hoisted_6, [
                  createVNode(_component_IsotipoMozoOficial)
                ])
              ])
            ]),
            _hoisted_7
          ])
        ]),
        createBaseVNode("div", _hoisted_8, [
          createBaseVNode("div", _hoisted_9, [
            createBaseVNode("div", _hoisted_10, [
              createBaseVNode("label", _hoisted_11, [
                createBaseVNode("input", {
                  type: "checkbox",
                  checked: !unref(isDark),
                  onChange: _cache[0] || (_cache[0] = (...args) => unref(toggleDarkModeHandler) && unref(toggleDarkModeHandler)(...args))
                }, null, 40, _hoisted_12),
                _hoisted_13
              ]),
              createBaseVNode("div", _hoisted_14, [
                createVNode(_component_RouterLink, { to: { name: "index" } }, {
                  default: withCtx(() => [
                    createVNode(_component_LogoMozoOficial)
                  ]),
                  _: 1
                })
              ])
            ]),
            createBaseVNode("div", _hoisted_15, [
              createBaseVNode("div", _hoisted_16, [
                createBaseVNode("div", _hoisted_17, [
                  createBaseVNode("div", _hoisted_18, [
                    _hoisted_19,
                    createBaseVNode("div", _hoisted_20, [
                      createBaseVNode("form", {
                        onSubmit: withModifiers(handleLogin, ["prevent"])
                      }, [
                        createBaseVNode("div", _hoisted_22, [
                          createVNode(_component_VField, null, {
                            default: withCtx(() => [
                              createVNode(_component_VControl, { icon: "feather:user" }, {
                                default: withCtx(() => [
                                  withDirectives(createBaseVNode("input", {
                                    "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => unref(user).email = $event),
                                    class: "input email-input",
                                    type: "text",
                                    placeholder: "Usuario",
                                    autocomplete: "username"
                                  }, null, 512), [
                                    [vModelText, unref(user).email]
                                  ])
                                ]),
                                _: 1
                              })
                            ]),
                            _: 1
                          }),
                          createVNode(_component_VField, null, {
                            default: withCtx(() => [
                              createVNode(_component_VControl, { icon: "feather:lock" }, {
                                default: withCtx(() => [
                                  withDirectives(createBaseVNode("input", {
                                    "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => unref(user).password = $event),
                                    class: "input password-input",
                                    type: "password",
                                    placeholder: "Password",
                                    autocomplete: "current-password"
                                  }, null, 512), [
                                    [vModelText, unref(user).password]
                                  ])
                                ]),
                                _: 1
                              })
                            ]),
                            _: 1
                          }),
                          createVNode(_component_VControl, { class: "login" }, {
                            default: withCtx(() => [
                              createVNode(_component_VButton, {
                                class: "buton-login",
                                loading: isLoading.value,
                                type: "submit",
                                bold: "",
                                fullwidth: "",
                                raised: ""
                              }, {
                                default: withCtx(() => [
                                  _hoisted_23
                                ]),
                                _: 1
                              }, 8, ["loading"])
                            ]),
                            _: 1
                          })
                        ])
                      ], 40, _hoisted_21)
                    ])
                  ])
                ])
              ])
            ])
          ])
        ])
      ]);
    };
  }
});
export { _sfc_main as default };
