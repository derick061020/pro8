import { _ as __unplugin_components_1 } from "./VControl.9555ec96.js";
import { _ as _sfc_main$4 } from "./VButton.b4d67cab.js";
import { _ as _sfc_main$5 } from "./KanbanDropdown.7d294c92.js";
import { _ as _sfc_main$3 } from "./VLoader.a784d703.js";
import { b as defineComponent, r as ref, f as openBlock, g as createElementBlock, X as createBaseVNode, z as toDisplayString, w as createVNode, B as withCtx, a6 as withDirectives, ar as vShow, F as Fragment, t as reactive, a as computed, o as onMounted, a7 as vModelText, Z as unref, Y as normalizeClass, C as createCommentVNode, A as renderList, D as createTextVNode, e as useHead } from "./vendor.05b8b5b5.js";
import { u as useUserSession, p as provideApi, c as useNotyf } from "./index.2bb13d6f.js";
import { p as pageTitle } from "./sidebarLayoutState.6ddf5d4e.js";
import "./plugin-vue_export-helper.5a098b48.js";
import "./VDropdown.53f4f138.js";
import "./VIcon.f7f680f8.js";
const _hoisted_1$2 = { class: "card-header" };
const _hoisted_2$1 = { class: "card-header-title m-0 is-dark-light" };
const _hoisted_3$1 = { class: "card-body p-0" };
const _hoisted_4$1 = { class: "table-container" };
const _hoisted_5$1 = { class: "table is-striped is-narrow is-hoverable is-fullwidth has-text-grey" };
const _hoisted_6$1 = /* @__PURE__ */ createBaseVNode("td", {
  width: "15%",
  class: "has-text-centered"
}, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "18",
    height: "18",
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
  ])
], -1);
const _hoisted_7$1 = { colspan: "3" };
const _hoisted_8$1 = /* @__PURE__ */ createBaseVNode("td", { class: "has-text-centered" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "18",
    height: "18",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-mail"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 7l9 6l9 -6" })
  ])
], -1);
const _hoisted_9$1 = { colspan: "3" };
const _hoisted_10$1 = /* @__PURE__ */ createBaseVNode("td", { class: "has-text-centered" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "18",
    height: "18",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-clock-hour-4"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 12l3 2" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M12 7v5" })
  ])
], -1);
const _hoisted_11$1 = { colspan: "3" };
const _hoisted_12$1 = /* @__PURE__ */ createBaseVNode("td", { class: "has-text-centered" }, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "18",
    height: "18",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-shopping-cart"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M4 19a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M15 19a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M17 17h-11v-14h-2" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M6 5l14 1l-1 7h-13" })
  ])
], -1);
const _hoisted_13$1 = /* @__PURE__ */ createBaseVNode("td", null, [
  /* @__PURE__ */ createBaseVNode("svg", {
    xmlns: "http://www.w3.org/2000/svg",
    width: "18",
    height: "18",
    viewBox: "0 0 24 24",
    fill: "none",
    stroke: "currentColor",
    "stroke-width": "2",
    "stroke-linecap": "round",
    "stroke-linejoin": "round",
    class: "icon-tabler icons-tabler-outline icon-tabler-credit-card"
  }, [
    /* @__PURE__ */ createBaseVNode("path", {
      stroke: "none",
      d: "M0 0h24v24H0z",
      fill: "none"
    }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M3 10l18 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M7 15l.01 0" }),
    /* @__PURE__ */ createBaseVNode("path", { d: "M11 15l2 0" })
  ])
], -1);
const _hoisted_14$1 = { class: "is-lowercase" };
const _hoisted_15$1 = { class: "card-footer" };
const _hoisted_16$1 = /* @__PURE__ */ createBaseVNode("br", null, null, -1);
const _sfc_main$2 = /* @__PURE__ */ defineComponent({
  props: {
    record: { type: null, required: true, default: Object },
    type: { type: null, required: true, default: String }
  },
  emits: ["send", "submit"],
  setup(__props, { emit }) {
    const props = __props;
    const isLoaderActive = ref(false);
    return (_ctx, _cache) => {
      const _component_VLoader = _sfc_main$3;
      return openBlock(), createElementBlock(Fragment, null, [
        createBaseVNode("div", _hoisted_1$2, [
          createBaseVNode("p", _hoisted_2$1, " Cod. " + toDisplayString(props.record.order_id), 1)
        ]),
        createVNode(_component_VLoader, {
          size: "small",
          active: isLoaderActive.value,
          translucent: ""
        }, {
          default: withCtx(() => [
            createBaseVNode("div", _hoisted_3$1, [
              createBaseVNode("div", _hoisted_4$1, [
                createBaseVNode("table", _hoisted_5$1, [
                  createBaseVNode("tr", null, [
                    _hoisted_6$1,
                    createBaseVNode("td", _hoisted_7$1, toDisplayString(props.record.customer), 1)
                  ]),
                  createBaseVNode("tr", null, [
                    _hoisted_8$1,
                    createBaseVNode("td", _hoisted_9$1, toDisplayString(props.record.customer_email), 1)
                  ]),
                  createBaseVNode("tr", null, [
                    _hoisted_10$1,
                    createBaseVNode("td", _hoisted_11$1, toDisplayString(props.record.created_at), 1)
                  ]),
                  createBaseVNode("tr", null, [
                    _hoisted_12$1,
                    createBaseVNode("td", null, toDisplayString(props.record.total), 1),
                    _hoisted_13$1,
                    createBaseVNode("td", _hoisted_14$1, toDisplayString(props.record.reference_payment), 1)
                  ])
                ])
              ])
            ]),
            createBaseVNode("footer", _hoisted_15$1, [
              withDirectives(createBaseVNode("a", {
                class: "card-footer-item has-text-secondary",
                onClick: _cache[0] || (_cache[0] = ($event) => emit("send", props.record.id))
              }, " Enviar ", 512), [
                [vShow, props.type == "send"]
              ]),
              withDirectives(createBaseVNode("a", {
                class: "card-footer-item has-text-primary",
                onClick: _cache[1] || (_cache[1] = ($event) => emit("submit", props.record.id))
              }, " Entregado ", 512), [
                [vShow, props.type == "submit"]
              ]),
              _hoisted_16$1
            ])
          ]),
          _: 1
        }, 8, ["active"])
      ], 64);
    };
  }
});
const STATUS_UNVERIFIED_PAYMENT = {
  description: "Pago sin verificar",
  id: 1
};
const STATUS_VERIFIED_PAYMENT = {
  description: "Pago verificado",
  id: 2
};
const STATUS_DISPACHED = {
  description: "Despachado",
  id: 3
};
const STATUS_CONFIRMED_CUSTOMER = {
  description: "Confirmado por el cliente",
  id: 4
};
var OrdersKanban_vue_vue_type_style_index_0_lang = "";
const _hoisted_1$1 = { class: "page-content kanban-content is-relative" };
const _hoisted_2 = { class: "kanban-toolbar" };
const _hoisted_3 = /* @__PURE__ */ createTextVNode("Ir a Menu digital");
const _hoisted_4 = { class: "columns is-kanban-wrapper" };
const _hoisted_5 = { class: "collapsed-content" };
const _hoisted_6 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_7 = [
  _hoisted_6
];
const _hoisted_8 = { class: "task-count" };
const _hoisted_9 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "En preparaci\xF3n", -1);
const _hoisted_10 = { class: "expanded-content" };
const _hoisted_11 = { class: "column-title" };
const _hoisted_12 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_13 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, [
  /* @__PURE__ */ createBaseVNode("i", { class: "fas fa-circle has-text-secondary" }),
  /* @__PURE__ */ createTextVNode(" En preparaci\xF3n ")
], -1);
const _hoisted_14 = { class: "task-count" };
const _hoisted_15 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_16 = { class: "empty-text" };
const _hoisted_17 = ["data-id"];
const _hoisted_18 = { class: "collapsed-content" };
const _hoisted_19 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_20 = [
  _hoisted_19
];
const _hoisted_21 = { class: "task-count" };
const _hoisted_22 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Enviados", -1);
const _hoisted_23 = { class: "expanded-content" };
const _hoisted_24 = { class: "column-title" };
const _hoisted_25 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_26 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, [
  /* @__PURE__ */ createBaseVNode("i", { class: "fas fa-circle has-text-primary" }),
  /* @__PURE__ */ createTextVNode(" Enviados ")
], -1);
const _hoisted_27 = { class: "task-count" };
const _hoisted_28 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_29 = { class: "empty-text" };
const _hoisted_30 = ["data-id"];
const _hoisted_31 = { class: "collapsed-content" };
const _hoisted_32 = /* @__PURE__ */ createBaseVNode("i", {
  "aria-hidden": "true",
  class: "iconify",
  "data-icon": "feather:plus"
}, null, -1);
const _hoisted_33 = [
  _hoisted_32
];
const _hoisted_34 = { class: "task-count" };
const _hoisted_35 = /* @__PURE__ */ createBaseVNode("div", { class: "collapsed-text" }, "Entregados", -1);
const _hoisted_36 = { class: "expanded-content" };
const _hoisted_37 = { class: "column-title" };
const _hoisted_38 = /* @__PURE__ */ createBaseVNode("input", {
  type: "text",
  class: "input is-small rename-input is-hidden"
}, null, -1);
const _hoisted_39 = /* @__PURE__ */ createBaseVNode("span", { class: "column-name" }, [
  /* @__PURE__ */ createBaseVNode("i", { class: "fas fa-circle has-text-success" }),
  /* @__PURE__ */ createTextVNode(" Entregados ")
], -1);
const _hoisted_40 = { class: "task-count" };
const _hoisted_41 = {
  key: 0,
  class: "kanban-empty"
};
const _hoisted_42 = { class: "empty-text" };
const _hoisted_43 = ["data-id"];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const search = ref("");
    const filterRecords = reactive({ records: [] });
    const isColumnPreparationCollapsed = ref(false);
    const isColumnDispatchedCollapsed = ref(false);
    const isColumnDeliveredCollapsed = ref(false);
    const isLoaderActive = ref(false);
    const emptyText = ref("No existe registro actualmente");
    const notif = useNotyf();
    const userSession = useUserSession();
    const STATE = reactive({ records: [] });
    const getRecords = async () => {
      isLoaderActive.value = true;
      const { data } = await provideApi().get("/orders");
      STATE.records = data.data;
      filterRecords.records = data.data;
      isLoaderActive.value = false;
      emptyText.value = "No existe registro actualmente";
    };
    const saveOrder = async (payload) => {
      emptyText.value = "Cargando registros";
      const { data } = await provideApi().post("/orders", payload);
      notif.success(data.message);
    };
    const recordsInPreparation = computed(() => {
      return STATE.records.filter((record) => [STATUS_UNVERIFIED_PAYMENT.id, STATUS_VERIFIED_PAYMENT.id].includes(record.status_order_id));
    });
    const recordsDispatched = computed(() => {
      return STATE.records.filter((record) => [STATUS_DISPACHED.id].includes(record.status_order_id));
    });
    const recordsDelivered = computed(() => {
      return STATE.records.filter((record) => [STATUS_CONFIRMED_CUSTOMER.id].includes(record.status_order_id));
    });
    const send = async (id) => {
      await saveOrder({
        id,
        status_order_id: STATUS_DISPACHED.id
      });
      getRecords();
    };
    const submit = async (id) => {
      await saveOrder({
        id,
        status_order_id: STATUS_CONFIRMED_CUSTOMER.id
      });
      getRecords();
    };
    computed(() => {
      let newRecords = filterRecords.records.filter((record) => {
        return record.order_id.indexOf(search.value) != -1;
      });
      return STATE.records = newRecords;
    });
    onMounted(async () => {
      await getRecords();
    });
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1;
      const _component_VButton = _sfc_main$4;
      const _component_KanbanDropdown = _sfc_main$5;
      const _component_OrderItem = _sfc_main$2;
      const _component_VLoader = _sfc_main$3;
      return openBlock(), createElementBlock("div", _hoisted_1$1, [
        createBaseVNode("div", _hoisted_2, [
          createVNode(_component_VControl, { icon: "feather:search" }, {
            default: withCtx(() => [
              withDirectives(createBaseVNode("input", {
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => search.value = $event),
                class: "input is-rounded",
                placeholder: "Buscar c\xF3digo de orden"
              }, null, 512), [
                [vModelText, search.value]
              ])
            ]),
            _: 1
          }),
          createVNode(_component_VButton, {
            href: unref(userSession).ssl + unref(userSession).url + "/menu",
            target: "BLANK",
            color: "primary"
          }, {
            default: withCtx(() => [
              _hoisted_3
            ]),
            _: 1
          }, 8, ["href"])
        ]),
        createBaseVNode("div", _hoisted_4, [
          createBaseVNode("div", {
            class: normalizeClass(["column", [
              isColumnPreparationCollapsed.value && "is-1 is-mini",
              !isColumnPreparationCollapsed.value && "is-one-third"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column", [isColumnPreparationCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_5, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[1] || (_cache[1] = ($event) => isColumnPreparationCollapsed.value = false)
                    }, _hoisted_7),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_8, toDisplayString(unref(recordsInPreparation).length), 1)
                    ]),
                    _hoisted_9
                  ]),
                  createBaseVNode("div", _hoisted_10, [
                    createBaseVNode("div", _hoisted_11, [
                      _hoisted_12,
                      createBaseVNode("h3", null, [
                        _hoisted_13,
                        createBaseVNode("span", _hoisted_14, toDisplayString(unref(recordsInPreparation).length), 1)
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[2] || (_cache[2] = ($event) => isColumnPreparationCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "preparation"
                    }, [
                      unref(recordsInPreparation).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_15, [
                        createBaseVNode("p", _hoisted_16, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(recordsInPreparation), (task, index) => {
                        return openBlock(), createElementBlock("div", {
                          key: index,
                          "data-id": task.id,
                          class: "kanban-card is-new"
                        }, [
                          createVNode(_component_OrderItem, {
                            record: task,
                            type: "send",
                            onSend: send
                          }, null, 8, ["record"])
                        ], 8, _hoisted_17);
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
              !isColumnDispatchedCollapsed.value && "is-one-third"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column", [isColumnDispatchedCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_18, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[3] || (_cache[3] = ($event) => isColumnDispatchedCollapsed.value = false)
                    }, _hoisted_20),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_21, toDisplayString(unref(recordsDispatched).length), 1)
                    ]),
                    _hoisted_22
                  ]),
                  createBaseVNode("div", _hoisted_23, [
                    createBaseVNode("div", _hoisted_24, [
                      _hoisted_25,
                      createBaseVNode("h3", null, [
                        _hoisted_26,
                        createBaseVNode("span", _hoisted_27, toDisplayString(unref(recordsDispatched).length), 1)
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[4] || (_cache[4] = ($event) => isColumnDispatchedCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "dispatched"
                    }, [
                      unref(recordsDispatched).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_28, [
                        createBaseVNode("p", _hoisted_29, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(recordsDispatched), (task, index) => {
                        return openBlock(), createElementBlock("div", {
                          key: index,
                          "data-id": task.id,
                          class: "kanban-card is-new"
                        }, [
                          createVNode(_component_OrderItem, {
                            record: task,
                            type: "submit",
                            onSubmit: submit
                          }, null, 8, ["record"])
                        ], 8, _hoisted_30);
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
              !isColumnDeliveredCollapsed.value && "is-one-third"
            ]])
          }, [
            createVNode(_component_VLoader, {
              size: "small",
              active: isLoaderActive.value,
              translucent: ""
            }, {
              default: withCtx(() => [
                createBaseVNode("div", {
                  class: normalizeClass(["kanban-column", [isColumnDeliveredCollapsed.value && "is-collapsed"]])
                }, [
                  createBaseVNode("div", _hoisted_31, [
                    createBaseVNode("div", {
                      class: "expand-button",
                      onClick: _cache[5] || (_cache[5] = ($event) => isColumnDeliveredCollapsed.value = false)
                    }, _hoisted_33),
                    createBaseVNode("div", null, [
                      createBaseVNode("span", _hoisted_34, toDisplayString(unref(recordsDelivered).length), 1)
                    ]),
                    _hoisted_35
                  ]),
                  createBaseVNode("div", _hoisted_36, [
                    createBaseVNode("div", _hoisted_37, [
                      _hoisted_38,
                      createBaseVNode("h3", null, [
                        _hoisted_39,
                        createBaseVNode("span", _hoisted_40, toDisplayString(unref(recordsDelivered).length), 1)
                      ]),
                      createVNode(_component_KanbanDropdown, {
                        onCollapse: _cache[6] || (_cache[6] = ($event) => isColumnDeliveredCollapsed.value = true)
                      })
                    ]),
                    createBaseVNode("div", {
                      ref: (_value, _refs) => {
                        _refs["newContainer"] = _value;
                      },
                      "data-state": "delivered"
                    }, [
                      unref(recordsDelivered).length === 0 ? (openBlock(), createElementBlock("div", _hoisted_41, [
                        createBaseVNode("p", _hoisted_42, toDisplayString(emptyText.value), 1)
                      ])) : createCommentVNode("", true),
                      (openBlock(true), createElementBlock(Fragment, null, renderList(unref(recordsDelivered), (task, index) => {
                        return openBlock(), createElementBlock("div", {
                          key: index,
                          "data-id": task.id,
                          class: "kanban-card is-new"
                        }, [
                          createVNode(_component_OrderItem, {
                            record: task,
                            type: "delivered"
                          }, null, 8, ["record"])
                        ], 8, _hoisted_43);
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
      const _component_OrdersKanban = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(_component_OrdersKanban)
      ]);
    };
  }
});
export { _sfc_main as default };
