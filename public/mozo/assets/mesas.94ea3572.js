import { _ as __unplugin_components_1$1 } from "./VControl.9555ec96.js";
import { _ as __unplugin_components_0 } from "./VDropdown.53f4f138.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import { f as openBlock, v as createBlock, B as withCtx, X as createBaseVNode, b as defineComponent, g as createElementBlock, y as renderSlot, Y as normalizeClass, r as ref, t as reactive, o as onMounted, x as resolveComponent, F as Fragment, A as renderList, z as toDisplayString, Z as unref, w as createVNode, D as createTextVNode, _ as createStaticVNode, E as mergeProps, $ as toHandlers } from "./vendor.05b8b5b5.js";
import { p as provideApi } from "./index.2bb13d6f.js";
import "./VIcon.f7f680f8.js";
const _sfc_main$2 = {};
const _hoisted_1$1 = /* @__PURE__ */ createBaseVNode("a", {
  role: "menuitem",
  href: "#",
  class: "dropdown-item is-media"
}, [
  /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "lnil lnil-reload"
    })
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
    /* @__PURE__ */ createBaseVNode("span", null, "Reload"),
    /* @__PURE__ */ createBaseVNode("span", null, "Refresh search results")
  ])
], -1);
const _hoisted_2$1 = /* @__PURE__ */ createBaseVNode("a", {
  role: "menuitem",
  href: "#",
  class: "dropdown-item is-media"
}, [
  /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "lnil lnil-save"
    })
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
    /* @__PURE__ */ createBaseVNode("span", null, "Save"),
    /* @__PURE__ */ createBaseVNode("span", null, "Save this search")
  ])
], -1);
const _hoisted_3$1 = /* @__PURE__ */ createBaseVNode("hr", { class: "dropdown-divider" }, null, -1);
const _hoisted_4$1 = /* @__PURE__ */ createBaseVNode("a", {
  role: "menuitem",
  href: "#",
  class: "dropdown-item is-media"
}, [
  /* @__PURE__ */ createBaseVNode("div", { class: "icon" }, [
    /* @__PURE__ */ createBaseVNode("i", {
      "aria-hidden": "true",
      class: "lnil lnil-cog"
    })
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
    /* @__PURE__ */ createBaseVNode("span", null, "Settings"),
    /* @__PURE__ */ createBaseVNode("span", null, "configuration options")
  ])
], -1);
function _sfc_render(_ctx, _cache) {
  const _component_VDropdown = __unplugin_components_0;
  return openBlock(), createBlock(_component_VDropdown, {
    icon: "feather:more-vertical",
    spaced: "",
    right: ""
  }, {
    content: withCtx(() => [
      _hoisted_1$1,
      _hoisted_2$1,
      _hoisted_3$1,
      _hoisted_4$1
    ]),
    _: 1
  });
}
var __unplugin_components_1 = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render]]);
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  props: {
    straight: { type: Boolean, required: false }
  },
  setup(__props) {
    const props = __props;
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", {
        class: normalizeClass(["widget", [props.straight && "is-straight"]])
      }, [
        renderSlot(_ctx.$slots, "header"),
        renderSlot(_ctx.$slots, "body")
      ], 2);
    };
  }
});
var _imports_0 = "/mozo/assets/travel.8a2cd3ba.svg";
var _imports_1 = "/mozo/assets/travel-dark.38421fca.svg";
var _imports_2 = "/mozo/assets/company1.2f76f7fd.svg";
var _imports_3 = "/mozo/assets/company2.a64c0280.svg";
var _imports_4 = "/mozo/assets/company3.e22635d3.svg";
const _hoisted_1 = { class: "business-dashboard flights-dashboard" };
const _hoisted_2 = { class: "columns" };
const _hoisted_3 = { class: "column is-9" };
const _hoisted_4 = { class: "booking-bar-wrapper" };
const _hoisted_5 = /* @__PURE__ */ createBaseVNode("img", {
  class: "travel-illustration light-image",
  src: _imports_0,
  alt: ""
}, null, -1);
const _hoisted_6 = /* @__PURE__ */ createBaseVNode("img", {
  class: "travel-illustration dark-image",
  src: _imports_1,
  alt: ""
}, null, -1);
const _hoisted_7 = /* @__PURE__ */ createBaseVNode("div", { class: "booking-bar-info" }, [
  /* @__PURE__ */ createBaseVNode("i", {
    "aria-hidden": "true",
    class: "lnil lnil-plane-alt"
  }),
  /* @__PURE__ */ createBaseVNode("div", { class: "inner" }, [
    /* @__PURE__ */ createBaseVNode("h2", { class: "booking-bar-heading" }, [
      /* @__PURE__ */ createTextVNode(" Paris "),
      /* @__PURE__ */ createBaseVNode("small", null, "[PAR]"),
      /* @__PURE__ */ createTextVNode(" - New York "),
      /* @__PURE__ */ createBaseVNode("small", null, "[NY]")
    ]),
    /* @__PURE__ */ createBaseVNode("p", { class: "booking-bar-sub-heading" }, "1 adult - Business")
  ])
], -1);
const _hoisted_8 = { class: "booking-bar" };
const _hoisted_9 = { class: "booking-bar-inputs" };
const _hoisted_10 = ["value"];
const _hoisted_11 = ["value"];
const _hoisted_12 = { class: "flights-toolbar" };
const _hoisted_13 = /* @__PURE__ */ createBaseVNode("h3", { class: "dark-inverted" }, "74 results", -1);
const _hoisted_14 = /* @__PURE__ */ createBaseVNode("div", { class: "flights-summary-wrapper" }, [
  /* @__PURE__ */ createBaseVNode("div", { class: "columns is-flex-tablet-p" }, [
    /* @__PURE__ */ createBaseVNode("div", { class: "column is-4" }, [
      /* @__PURE__ */ createBaseVNode("a", { class: "flight-summary" }, [
        /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "312"),
        /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
          /* @__PURE__ */ createBaseVNode("span", null, "Cheapest"),
          /* @__PURE__ */ createBaseVNode("span", null, "7h32min")
        ])
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "column is-4" }, [
      /* @__PURE__ */ createBaseVNode("a", { class: "flight-summary is-featured" }, [
        /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "428"),
        /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
          /* @__PURE__ */ createBaseVNode("span", null, "Best"),
          /* @__PURE__ */ createBaseVNode("span", null, "7h16min")
        ])
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "column is-4" }, [
      /* @__PURE__ */ createBaseVNode("a", { class: "flight-summary" }, [
        /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "549"),
        /* @__PURE__ */ createBaseVNode("div", { class: "meta" }, [
          /* @__PURE__ */ createBaseVNode("span", null, "Fastest"),
          /* @__PURE__ */ createBaseVNode("span", null, "5h36min")
        ])
      ])
    ])
  ])
], -1);
const _hoisted_15 = /* @__PURE__ */ createBaseVNode("div", { class: "flights" }, [
  /* @__PURE__ */ createBaseVNode("a", { class: "flight-card" }, [
    /* @__PURE__ */ createBaseVNode("img", {
      src: _imports_2,
      alt: ""
    }),
    /* @__PURE__ */ createBaseVNode("div", { class: "start" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "11:30 am"),
      /* @__PURE__ */ createBaseVNode("span", null, "Paris ORLY"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "route" }, [
      /* @__PURE__ */ createBaseVNode("div", { class: "departure" }),
      /* @__PURE__ */ createBaseVNode("div", {
        class: "line",
        "data-content": "1 stop"
      }),
      /* @__PURE__ */ createBaseVNode("div", { class: "arrival" }, [
        /* @__PURE__ */ createBaseVNode("i", {
          "aria-hidden": "true",
          class: "lnil lnil-plane-alt"
        })
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "end" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "06:59 pm"),
      /* @__PURE__ */ createBaseVNode("span", null, "New York JFK"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "374")
  ]),
  /* @__PURE__ */ createBaseVNode("a", { class: "flight-card" }, [
    /* @__PURE__ */ createBaseVNode("img", {
      src: _imports_3,
      alt: ""
    }),
    /* @__PURE__ */ createBaseVNode("div", { class: "start" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "09:30 am"),
      /* @__PURE__ */ createBaseVNode("span", null, "Paris ORLY"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "route" }, [
      /* @__PURE__ */ createBaseVNode("div", { class: "departure" }),
      /* @__PURE__ */ createBaseVNode("div", {
        class: "line",
        "data-content": "1 stop"
      }),
      /* @__PURE__ */ createBaseVNode("div", { class: "arrival" }, [
        /* @__PURE__ */ createBaseVNode("i", {
          "aria-hidden": "true",
          class: "lnil lnil-plane-alt"
        })
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "end" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "04:18 pm"),
      /* @__PURE__ */ createBaseVNode("span", null, "New York JFK"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "392")
  ]),
  /* @__PURE__ */ createBaseVNode("a", { class: "flight-card" }, [
    /* @__PURE__ */ createBaseVNode("img", {
      src: _imports_2,
      alt: ""
    }),
    /* @__PURE__ */ createBaseVNode("div", { class: "start" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "06:42 am"),
      /* @__PURE__ */ createBaseVNode("span", null, "Paris ORLY"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "route" }, [
      /* @__PURE__ */ createBaseVNode("div", { class: "departure" }),
      /* @__PURE__ */ createBaseVNode("div", {
        class: "line",
        "data-content": "direct"
      }),
      /* @__PURE__ */ createBaseVNode("div", { class: "arrival" }, [
        /* @__PURE__ */ createBaseVNode("i", {
          "aria-hidden": "true",
          class: "lnil lnil-plane-alt"
        })
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "end" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "02:11 pm"),
      /* @__PURE__ */ createBaseVNode("span", null, "New York JFK"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "398")
  ]),
  /* @__PURE__ */ createBaseVNode("a", { class: "flight-card" }, [
    /* @__PURE__ */ createBaseVNode("img", {
      src: _imports_4,
      alt: ""
    }),
    /* @__PURE__ */ createBaseVNode("div", { class: "start" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "07:23 am"),
      /* @__PURE__ */ createBaseVNode("span", null, "Paris ORLY"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "route" }, [
      /* @__PURE__ */ createBaseVNode("div", { class: "departure" }),
      /* @__PURE__ */ createBaseVNode("div", {
        class: "line",
        "data-content": "direct"
      }),
      /* @__PURE__ */ createBaseVNode("div", { class: "arrival" }, [
        /* @__PURE__ */ createBaseVNode("i", {
          "aria-hidden": "true",
          class: "lnil lnil-plane-alt"
        })
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "end" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "01:46 pm"),
      /* @__PURE__ */ createBaseVNode("span", null, "New York JFK"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "407")
  ]),
  /* @__PURE__ */ createBaseVNode("a", { class: "flight-card" }, [
    /* @__PURE__ */ createBaseVNode("img", {
      src: _imports_2,
      alt: ""
    }),
    /* @__PURE__ */ createBaseVNode("div", { class: "start" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "10:12 am"),
      /* @__PURE__ */ createBaseVNode("span", null, "Paris ORLY"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "route" }, [
      /* @__PURE__ */ createBaseVNode("div", { class: "departure" }),
      /* @__PURE__ */ createBaseVNode("div", {
        class: "line",
        "data-content": "1 stop"
      }),
      /* @__PURE__ */ createBaseVNode("div", { class: "arrival" }, [
        /* @__PURE__ */ createBaseVNode("i", {
          "aria-hidden": "true",
          class: "lnil lnil-plane-alt"
        })
      ])
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "end" }, [
      /* @__PURE__ */ createBaseVNode("span", null, "03:39 pm"),
      /* @__PURE__ */ createBaseVNode("span", null, "New York JFK"),
      /* @__PURE__ */ createBaseVNode("span", null, "Monday, Jul 7th")
    ]),
    /* @__PURE__ */ createBaseVNode("div", { class: "flight-price" }, "431")
  ])
], -1);
const _hoisted_16 = { class: "column is-3" };
const _hoisted_17 = /* @__PURE__ */ createBaseVNode("div", { class: "field" }, [
  /* @__PURE__ */ createBaseVNode("div", { class: "control" }, [
    /* @__PURE__ */ createBaseVNode("input", {
      type: "text",
      class: "input",
      placeholder: "Search..."
    }),
    /* @__PURE__ */ createBaseVNode("button", { class: "searcv-button" }, [
      /* @__PURE__ */ createBaseVNode("i", {
        "aria-hidden": "true",
        class: "iconify",
        "data-icon": "feather:search"
      })
    ])
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "topics" }, [
    /* @__PURE__ */ createBaseVNode("a", null, "#Paris"),
    /* @__PURE__ */ createBaseVNode("a", null, "#Berlin"),
    /* @__PURE__ */ createBaseVNode("a", null, "#Chicago")
  ])
], -1);
const _hoisted_18 = /* @__PURE__ */ createBaseVNode("div", { class: "widget-toolbar" }, [
  /* @__PURE__ */ createBaseVNode("div", { class: "left" }, [
    /* @__PURE__ */ createBaseVNode("a", { class: "action-icon" }, [
      /* @__PURE__ */ createBaseVNode("i", {
        "aria-hidden": "true",
        class: "iconify",
        "data-icon": "feather:chevron-left"
      })
    ])
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "center" }, [
    /* @__PURE__ */ createBaseVNode("h3", null, "October 2020")
  ]),
  /* @__PURE__ */ createBaseVNode("div", { class: "right" }, [
    /* @__PURE__ */ createBaseVNode("a", { class: "action-icon" }, [
      /* @__PURE__ */ createBaseVNode("i", {
        "aria-hidden": "true",
        class: "iconify",
        "data-icon": "feather:chevron-right"
      })
    ])
  ])
], -1);
const _hoisted_19 = /* @__PURE__ */ createBaseVNode("table", { class: "calendar" }, [
  /* @__PURE__ */ createBaseVNode("thead", null, [
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Mon"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Tue"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Wed"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Thu"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Fri"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Sat"),
      /* @__PURE__ */ createBaseVNode("th", { scope: "col" }, "Sun")
    ])
  ]),
  /* @__PURE__ */ createBaseVNode("tbody", null, [
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("td", { class: "prev-month" }, "29"),
      /* @__PURE__ */ createBaseVNode("td", { class: "prev-month" }, "30"),
      /* @__PURE__ */ createBaseVNode("td", { class: "prev-month" }, "31"),
      /* @__PURE__ */ createBaseVNode("td", null, "1"),
      /* @__PURE__ */ createBaseVNode("td", null, "2"),
      /* @__PURE__ */ createBaseVNode("td", null, "3"),
      /* @__PURE__ */ createBaseVNode("td", null, "4")
    ]),
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("td", null, "5"),
      /* @__PURE__ */ createBaseVNode("td", null, "6"),
      /* @__PURE__ */ createBaseVNode("td", null, "7"),
      /* @__PURE__ */ createBaseVNode("td", null, "8"),
      /* @__PURE__ */ createBaseVNode("td", null, "9"),
      /* @__PURE__ */ createBaseVNode("td", null, "10"),
      /* @__PURE__ */ createBaseVNode("td", null, "11")
    ]),
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("td", null, "12"),
      /* @__PURE__ */ createBaseVNode("td", null, "13"),
      /* @__PURE__ */ createBaseVNode("td", null, "14"),
      /* @__PURE__ */ createBaseVNode("td", null, "15"),
      /* @__PURE__ */ createBaseVNode("td", null, "16"),
      /* @__PURE__ */ createBaseVNode("td", null, "17"),
      /* @__PURE__ */ createBaseVNode("td", { class: "current-day" }, "18")
    ]),
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("td", null, "19"),
      /* @__PURE__ */ createBaseVNode("td", null, "20"),
      /* @__PURE__ */ createBaseVNode("td", null, "21"),
      /* @__PURE__ */ createBaseVNode("td", null, "22"),
      /* @__PURE__ */ createBaseVNode("td", null, "23"),
      /* @__PURE__ */ createBaseVNode("td", null, "24"),
      /* @__PURE__ */ createBaseVNode("td", null, "25")
    ]),
    /* @__PURE__ */ createBaseVNode("tr", null, [
      /* @__PURE__ */ createBaseVNode("td", null, "26"),
      /* @__PURE__ */ createBaseVNode("td", null, "27"),
      /* @__PURE__ */ createBaseVNode("td", null, "28"),
      /* @__PURE__ */ createBaseVNode("td", null, "29"),
      /* @__PURE__ */ createBaseVNode("td", null, "30"),
      /* @__PURE__ */ createBaseVNode("td", null, "31"),
      /* @__PURE__ */ createBaseVNode("td", { class: "next-month" }, "1")
    ])
  ])
], -1);
const _hoisted_20 = /* @__PURE__ */ createStaticVNode('<div class="filters-card"><a class="button v-button is-primary is-fullwidth is-bold is-raised"> Add To Favorites </a><div class="checkboxes-list"><div class="field"><h5 class="dark-inverted">Stops</h5><div class="control"><label class="checkbox"><input type="radio" name="flight_stops" checked><span></span> All Flights </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_stops"><span></span> No Stops </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_stops"><span></span> 1 Stop </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_stops"><span></span> 2 Stops </label></div></div></div><div class="checkboxes-list"><div class="field"><h5 class="dark-inverted">Luggage</h5><div class="control"><label class="checkbox"><input type="radio" name="flight_luggage" checked><span></span> All Options </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_luggage"><span></span> 1 Cabin luggage </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_luggage"><span></span> 2 Cabin luggage </label></div><div class="control"><label class="checkbox"><input type="radio" name="flight_luggage"><span></span> None </label></div></div></div></div>', 1);
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const date = ref({
      start: new Date(),
      end: new Date()
    });
    let estate = reactive({ products: [] });
    const getDataProducts = async () => {
      const { data } = await provideApi().get("/document/tables");
      estate.products = data["data"]["items"];
    };
    onMounted(() => {
      getDataProducts();
    });
    return (_ctx, _cache) => {
      const _component_VControl = __unplugin_components_1$1;
      const _component_v_date_picker = resolveComponent("v-date-picker");
      const _component_FlightResultsDropdown = __unplugin_components_1;
      const _component_UIWidget = _sfc_main$1;
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createBaseVNode("div", _hoisted_2, [
          createBaseVNode("div", _hoisted_3, [
            (openBlock(true), createElementBlock(Fragment, null, renderList(unref(estate).products, (product) => {
              return openBlock(), createElementBlock("div", {
                key: product.id
              }, toDisplayString(product.name), 1);
            }), 128)),
            createBaseVNode("div", _hoisted_4, [
              _hoisted_5,
              _hoisted_6,
              _hoisted_7,
              createBaseVNode("div", _hoisted_8, [
                createVNode(_component_v_date_picker, {
                  modelValue: date.value,
                  "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => date.value = $event),
                  "is-range": "",
                  color: "green",
                  "trim-weeks": ""
                }, {
                  default: withCtx(({ inputValue, inputEvents }) => [
                    createBaseVNode("div", _hoisted_9, [
                      createVNode(_component_VControl, { icon: "feather:calendar" }, {
                        default: withCtx(() => [
                          createBaseVNode("input", mergeProps({
                            type: "text",
                            class: "input flight-datepicker",
                            placeholder: "Departure",
                            value: inputValue.start
                          }, toHandlers(inputEvents.start)), null, 16, _hoisted_10)
                        ]),
                        _: 2
                      }, 1024),
                      createVNode(_component_VControl, { icon: "feather:calendar" }, {
                        default: withCtx(() => [
                          createBaseVNode("input", mergeProps({
                            type: "text",
                            class: "input flight-datepicker",
                            placeholder: "Return",
                            value: inputValue.end
                          }, toHandlers(inputEvents.end)), null, 16, _hoisted_11)
                        ]),
                        _: 2
                      }, 1024)
                    ])
                  ]),
                  _: 1
                }, 8, ["modelValue"])
              ])
            ]),
            createBaseVNode("div", _hoisted_12, [
              _hoisted_13,
              createVNode(_component_FlightResultsDropdown)
            ]),
            _hoisted_14,
            _hoisted_15
          ]),
          createBaseVNode("div", _hoisted_16, [
            createVNode(_component_UIWidget, {
              class: "search-widget",
              straight: ""
            }, {
              body: withCtx(() => [
                _hoisted_17
              ]),
              _: 1
            }),
            createVNode(_component_UIWidget, { class: "picker-widget" }, {
              header: withCtx(() => [
                _hoisted_18
              ]),
              body: withCtx(() => [
                _hoisted_19
              ]),
              _: 1
            }),
            _hoisted_20
          ])
        ])
      ]);
    };
  }
});
export { _sfc_main as default };
