import { b as defineComponent, N as Notyf, t as reactive, a as computed, r as ref, L as watch, o as onMounted, P as onUnmounted, f as openBlock, g as createElementBlock, w as createVNode, Z as unref, X as createBaseVNode, Y as normalizeClass, D as createTextVNode, z as toDisplayString, B as withCtx, C as createCommentVNode, v as createBlock, a6 as withDirectives, ar as vShow, S as isRef, F as Fragment, G as pushScopeId, H as popScopeId, _ as createStaticVNode, e as useHead } from "./vendor.05b8b5b5.js";
import { p as pageTitle } from "./sidebarLayoutState.6ddf5d4e.js";
import { _ as _sfc_main$2, M as MoveAmbienteModal, a as _sfc_main$4, b as _sfc_main$7, c as _sfc_main$8 } from "./MoveAmbienteModal.7351a753.js";
import { _ as _sfc_main$3, a as _sfc_main$5 } from "./ProductModifiersDialog.637fd331.js";
import { _ as _sfc_main$6, a as __unplugin_components_4 } from "./VButton.b4d67cab.js";
import { _ as __unplugin_components_5 } from "./VPlaceloadWrap.d37210d3.js";
import { a as useMesaSession, D as DEFAULT_ENVIRONMENT, d as MESA_NO_DISPONIBLE, e as MESA_DISPONIBLE, c as MesaService, u as use } from "./masterService.8a758d8d.js";
import { a as useCompanySession, u as useUserSession } from "./index.2bb13d6f.js";
import { P as PedidoEnvironment, B as BagDetails } from "./PedidoEnvironment.a458edfb.js";
import { _ as _export_sfc } from "./plugin-vue_export-helper.5a098b48.js";
import "./VControl.9555ec96.js";
import "./VModal.9533b2d3.js";
import "./VIcon.f7f680f8.js";
import "./CategoriesNavBar.5044304a.js";
import "./VField.152a42ff.js";
import "./VAvatar.0f0bd550.js";
import "./restaurantService.f8e320da.js";
import "./VIconButton.dcb74fc8.js";
import "./VLoader.a784d703.js";
var TakeawayRestaurant_vue_vue_type_style_index_0_lang = "";
var TakeawayRestaurant_vue_vue_type_style_index_1_scoped_true_lang = "";
const _withScopeId = (n) => (pushScopeId("data-v-46ccd37c"), n = n(), popScopeId(), n);
const _hoisted_1$1 = {
  key: 0,
  class: "ecommerce-dashboard ecommerce-dashboard-v1"
};
const _hoisted_2 = { class: "columns is-multiline" };
const _hoisted_3 = { class: "column is-9" };
const _hoisted_4 = { class: "stat-widget line-stats-widget is-straight" };
const _hoisted_5 = {
  key: 0,
  class: "notification is-warning",
  style: { "margin-bottom": "1rem" }
};
const _hoisted_6 = { style: { "display": "flex", "justify-content": "space-between", "align-items": "center" } };
const _hoisted_7 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("br", null, null, -1));
const _hoisted_8 = { style: { "font-size": "13px" } };
const _hoisted_9 = { style: { "display": "flex", "gap": "0.5rem" } };
const _hoisted_10 = /* @__PURE__ */ createTextVNode(" Cancelar ");
const _hoisted_11 = {
  key: 2,
  class: "mesa-bottom-bar mt-3"
};
const _hoisted_12 = { class: "mesa-legend" };
const _hoisted_13 = { class: "legend-item" };
const _hoisted_14 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator available" }, null, -1));
const _hoisted_15 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Mesa disponible", -1));
const _hoisted_16 = [
  _hoisted_14,
  _hoisted_15
];
const _hoisted_17 = /* @__PURE__ */ createStaticVNode('<div class="legend-item" data-v-46ccd37c><span class="legend-indicator notavailableandorderpending" data-v-46ccd37c></span><span class="legend-text" data-v-46ccd37c>Ordenes pendientes</span></div><div class="legend-item" data-v-46ccd37c><span class="legend-indicator notavailableandorderserved" data-v-46ccd37c></span><span class="legend-text" data-v-46ccd37c>Ordenes servidas</span></div>', 2);
const _hoisted_19 = { class: "legend-item" };
const _hoisted_20 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator notavailableandprecuenta" }, null, -1));
const _hoisted_21 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Precuenta generada", -1));
const _hoisted_22 = [
  _hoisted_20,
  _hoisted_21
];
const _hoisted_23 = { class: "legend-item" };
const _hoisted_24 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator moved" }, null, -1));
const _hoisted_25 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Mesa reubicada", -1));
const _hoisted_26 = [
  _hoisted_24,
  _hoisted_25
];
const _hoisted_27 = { class: "legend-item" };
const _hoisted_28 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-indicator is-delivered" }, null, -1));
const _hoisted_29 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("span", { class: "legend-text" }, "Despachado", -1));
const _hoisted_30 = [
  _hoisted_28,
  _hoisted_29
];
const _hoisted_31 = {
  key: 3,
  class: "mesa-legend-edit"
};
const _hoisted_32 = /* @__PURE__ */ createStaticVNode('<div class="legend-item" data-v-46ccd37c><span class="legend-icon-edit disponible" data-v-46ccd37c><i class="fa-solid fa-check" data-v-46ccd37c></i></span><span class="legend-text" data-v-46ccd37c>Disponible</span></div><div class="legend-item" data-v-46ccd37c><span class="legend-icon-edit ocupada" data-v-46ccd37c><i class="fa-solid fa-check" data-v-46ccd37c></i></span><span class="legend-text" data-v-46ccd37c>Ocupada (no se puede desactivar)</span></div><div class="legend-item" data-v-46ccd37c><span class="legend-icon-edit fuera-servicio" data-v-46ccd37c><i class="fa-solid fa-ban" data-v-46ccd37c></i></span><span class="legend-text" data-v-46ccd37c>Fuera de servicio</span></div><div class="legend-item" data-v-46ccd37c><span class="legend-indicator moved" data-v-46ccd37c></span><span class="legend-text" data-v-46ccd37c>Mesa reubicada</span></div>', 4);
const _hoisted_36 = [
  _hoisted_32
];
const _hoisted_37 = { class: "column is-3" };
const _hoisted_38 = {
  key: 0,
  class: "stat-widget area-stats-widget is-straight"
};
const _hoisted_39 = {
  key: 1,
  class: "stat-widget area-stats-widget is-straight"
};
const _hoisted_40 = /* @__PURE__ */ _withScopeId(() => /* @__PURE__ */ createBaseVNode("h3", null, "SELECCIONE UN PEDIDO", -1));
const _hoisted_41 = [
  _hoisted_40
];
const _sfc_main$1 = /* @__PURE__ */ defineComponent({
  setup(__props) {
    var _a;
    const STORAGE_KEY_VISUALIZACION = "restaurant_visualizacion_mesas";
    const notyf = new Notyf();
    const mesasSession = reactive(useMesaSession());
    const companySession = reactive(useCompanySession());
    const mesaSelected = computed(() => mesasSession.mesaSelected);
    let mesas = ref([]);
    const userSession = useUserSession();
    userSession.getRole();
    let viewPos = ref(false);
    let canUpdate = ref(false);
    let openModalEditMesa = ref(false);
    let loadingRefresh = ref(false);
    let intervalId = void 0;
    const environments = mesasSession.environments;
    const deliveryAndTakeaway = computed(() => {
      return environments.filter((env) => env.is_takeaway);
    });
    const sortedEnvironments = computed(() => {
      return [...environments].sort((a, b) => {
        const aRight = a.is_takeaway;
        const bRight = b.is_takeaway;
        if (aRight === bRight)
          return 0;
        return aRight ? 1 : -1;
      });
    });
    const activeTab = ref(((_a = sortedEnvironments.value[0]) == null ? void 0 : _a.name) || "");
    const isDeliveryOrTakeaway = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_delivery || env.is_takeaway : false;
    });
    const isDelivery = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_delivery : false;
    });
    const isTakeaway = computed(() => {
      const env = environments.find((e) => e.name === activeTab.value);
      return env ? env.is_takeaway : false;
    });
    let stateFormMesa = reactive({
      id: 0,
      label: "",
      shape: "",
      environment: DEFAULT_ENVIRONMENT
    });
    let modeEditMesas = ref(false);
    const opcionesVisualizacion = ref({
      mozo: { activo: true, label: "Mozo", icono: "fa-user-tie" },
      tiempo: { activo: true, label: "Tiempo", icono: "fa-clock" },
      pedidos: { activo: true, label: "N\xB0 Pedidos", icono: "fa-receipt" },
      monto: { activo: false, label: "Monto", icono: "fa-dollar-sign" },
      personas: { activo: false, label: "Personas", icono: "fa-users" },
      cliente: { activo: false, label: "Cliente", icono: "fa-user" }
    });
    const cargarConfiguracionVisualizacion = () => {
      const saved = localStorage.getItem(STORAGE_KEY_VISUALIZACION);
      if (saved) {
        try {
          const parsed = JSON.parse(saved);
          Object.keys(parsed).forEach((key) => {
            if (opcionesVisualizacion.value[key]) {
              opcionesVisualizacion.value[key].activo = parsed[key];
            }
          });
        } catch (error) {
          console.error("Error al cargar configuraci\xF3n de visualizaci\xF3n:", error);
        }
      }
    };
    watch(opcionesVisualizacion, (newVal) => {
      const toSave = {};
      Object.keys(newVal).forEach((key) => {
        toSave[key] = newVal[key].activo;
      });
      localStorage.setItem(STORAGE_KEY_VISUALIZACION, JSON.stringify(toSave));
    }, { deep: true });
    const mostrarConfig = computed(() => {
      return {
        mozo: opcionesVisualizacion.value.mozo.activo,
        tiempo: opcionesVisualizacion.value.tiempo.activo,
        pedidos: opcionesVisualizacion.value.pedidos.activo,
        monto: opcionesVisualizacion.value.monto.activo,
        personas: opcionesVisualizacion.value.personas.activo,
        cliente: opcionesVisualizacion.value.cliente.activo
      };
    });
    const filtros = ref({
      disponibles: true,
      ocupadas: true,
      fueraServicio: true
    });
    const todasLasMesas = computed(() => mesas.value);
    const mesasVisibles = computed(() => {
      return mesas.value.filter((mesa) => {
        if (mesa.group_id && !mesa.is_main_table) {
          return false;
        }
        if (mesa.is_active === false) {
          return filtros.value.fueraServicio;
        }
        if (mesa.status === MESA_NO_DISPONIBLE) {
          return filtros.value.ocupadas;
        }
        if (mesa.status === MESA_DISPONIBLE) {
          return filtros.value.disponibles;
        }
        return true;
      });
    });
    const modoSeleccionUnion = ref(false);
    const mesaPrincipalParaUnion = ref(null);
    const mesasSecundariasSeleccionadas = ref(new Set());
    const grupoExistenteParaAgregar = ref(null);
    const iniciarModoUnion = (mesa) => {
      if (mesa.is_active === false) {
        notyf.error("No puedes unir una mesa fuera de servicio");
        return;
      }
      if (mesa.group_id) {
        if (mesa.status !== MESA_NO_DISPONIBLE) {
          notyf.error("Solo puedes agregar mesas a un grupo que tenga pedidos");
          return;
        }
        grupoExistenteParaAgregar.value = mesa.group_id;
        mesaPrincipalParaUnion.value = mesa;
        modoSeleccionUnion.value = true;
        mesasSecundariasSeleccionadas.value = new Set();
        notyf.success(`Agrega mesas disponibles al grupo de Mesa ${mesa.label}`);
        return;
      }
      mesaPrincipalParaUnion.value = mesa;
      modoSeleccionUnion.value = true;
      mesasSecundariasSeleccionadas.value = new Set();
      grupoExistenteParaAgregar.value = null;
      const estadoMesa = mesa.status === MESA_NO_DISPONIBLE ? "ocupada" : "disponible";
      notyf.success(`Mesa ${mesa.label} (${estadoMesa}) seleccionada. Selecciona las mesas secundarias.`);
    };
    const toggleMesaSecundaria = (mesa) => {
      var _a2;
      if (!modoSeleccionUnion.value) {
        selectMesa(mesa);
        return;
      }
      if (mesa.id === ((_a2 = mesaPrincipalParaUnion.value) == null ? void 0 : _a2.id)) {
        notyf.error("Esta es la mesa principal");
        return;
      }
      if (mesa.is_active === false) {
        notyf.error("No puedes unir una mesa fuera de servicio");
        return;
      }
      if (mesa.group_id && mesa.group_id !== grupoExistenteParaAgregar.value) {
        notyf.error("Esta mesa ya pertenece a otro grupo");
        return;
      }
      if (mesa.status !== MESA_DISPONIBLE) {
        notyf.error("Solo puedes agregar mesas disponibles (vac\xEDas)");
        return;
      }
      const seleccionadas = new Set(mesasSecundariasSeleccionadas.value);
      if (seleccionadas.has(mesa.id)) {
        seleccionadas.delete(mesa.id);
        notyf.success(`Mesa ${mesa.label} deseleccionada`);
      } else {
        seleccionadas.add(mesa.id);
        notyf.success(`Mesa ${mesa.label} seleccionada`);
      }
      mesasSecundariasSeleccionadas.value = seleccionadas;
    };
    const confirmarUnion = async () => {
      if (!mesaPrincipalParaUnion.value) {
        notyf.error("No hay mesa principal seleccionada");
        return;
      }
      if (mesasSecundariasSeleccionadas.value.size === 0) {
        notyf.error("Debes seleccionar al menos una mesa secundaria");
        return;
      }
      try {
        notyf.success("Procesando uni\xF3n de mesas...");
        let grupoId;
        if (grupoExistenteParaAgregar.value) {
          grupoId = grupoExistenteParaAgregar.value;
          console.log(`Agregando mesas al grupo existente ${grupoId}`);
        } else {
          console.log("Creando nuevo grupo con mesa principal:", mesaPrincipalParaUnion.value.id);
          try {
            const grupo = await MesaService.crearGrupoMesas(mesaPrincipalParaUnion.value.id);
            grupoId = grupo.id;
            console.log("Grupo creado con ID:", grupoId);
          } catch (error) {
            notyf.error(error.message || "Error al crear el grupo");
            return;
          }
        }
        const mesasArray = Array.from(mesasSecundariasSeleccionadas.value);
        console.log(`Agregando ${mesasArray.length} mesa(s) al grupo ${grupoId}`);
        let mesasAgregadas = 0;
        let mesasFallidas = 0;
        for (const mesaId of mesasArray) {
          try {
            console.log(`Agregando mesa ${mesaId}...`);
            await MesaService.agregarMesaAGrupo(grupoId, mesaId);
            console.log(`Mesa ${mesaId} agregada correctamente`);
            mesasAgregadas++;
          } catch (error) {
            console.error(`Error al agregar mesa ${mesaId}:`, error);
            notyf.error(error.message || `Error al agregar mesa ${mesaId}`);
            mesasFallidas++;
          }
        }
        console.log(`Resumen: ${mesasAgregadas} agregadas, ${mesasFallidas} fallidas`);
        await MesaService.syncMesasAndEnvironments();
        modoSeleccionUnion.value = false;
        mesaPrincipalParaUnion.value = null;
        mesasSecundariasSeleccionadas.value = new Set();
        grupoExistenteParaAgregar.value = null;
        if (mesasAgregadas > 0) {
          notyf.success(` ${mesasAgregadas} mesa(s) unida(s) correctamente`);
        }
      } catch (error) {
        console.error("Error general al unir mesas:", error);
        notyf.error("Error al unir mesas. Intenta nuevamente.");
      }
    };
    const cancelarUnion = () => {
      modoSeleccionUnion.value = false;
      mesaPrincipalParaUnion.value = null;
      mesasSecundariasSeleccionadas.value = new Set();
      grupoExistenteParaAgregar.value = null;
      notyf.success("Uni\xF3n cancelada");
    };
    const handleSepararMesa = async (mesaId) => {
      const mesa = mesas.value.find((m) => m.id === mesaId);
      if (!(mesa == null ? void 0 : mesa.group_id)) {
        notyf.error("Esta mesa no tiene grupo");
        return;
      }
      try {
        notyf.success("Separando mesas...");
        await MesaService.disolverGrupo(mesa.group_id);
        await MesaService.syncMesasAndEnvironments();
        notyf.success(`Grupo disuelto correctamente`);
      } catch (error) {
        console.error("Error al separar mesas:", error);
        notyf.error("Error al separar mesas");
      }
    };
    const separarMesaEspecifica = async (mesaId) => {
      const mesa = mesas.value.find((m) => m.id === mesaId);
      if (!(mesa == null ? void 0 : mesa.group_id)) {
        notyf.error("Esta mesa no tiene grupo");
        return;
      }
      try {
        notyf.success("Separando mesa...");
        await MesaService.separarMesaDeGrupo(mesaId);
        await MesaService.syncMesasAndEnvironments();
        notyf.success(`Mesa ${mesa.label} separada`);
      } catch (error) {
        console.error("Error al separar mesa:", error);
        notyf.error("Error al separar mesa");
      }
    };
    const handleToggleActive = async (mesaId) => {
      try {
        const result = await MesaService.toggleMesaActive(mesaId);
        notyf.success(result.message);
        await syncMesasEnviroments();
      } catch (error) {
        console.error("Error al cambiar estado:", error);
        notyf.error(error.message || "Error al cambiar estado de la mesa");
      }
    };
    let openMoveAmbienteModal = ref(false);
    let mesaToMoveAmbiente = ref(null);
    const handleMoveAmbiente = (mesa) => {
      if (mesa.status === MESA_NO_DISPONIBLE) {
        return;
      }
      mesaToMoveAmbiente.value = mesa;
      openMoveAmbienteModal.value = true;
    };
    const confirmMoveAmbiente = async (nuevoAmbiente) => {
      if (!mesaToMoveAmbiente.value)
        return;
      try {
        await MesaService.cambiarAmbienteMesa(mesaToMoveAmbiente.value.id, nuevoAmbiente);
        notyf.success(`Mesa ${mesaToMoveAmbiente.value.label} movida a ${nuevoAmbiente}`);
        await syncMesasEnviroments();
        openMoveAmbienteModal.value = false;
        mesaToMoveAmbiente.value = null;
      } catch (error) {
        notyf.error(error.message || "Error al mover la mesa");
        console.error(error);
      }
    };
    const handleRestaurarAmbiente = async (mesaId) => {
      try {
        const mesa = todasLasMesas.value.find((m) => m.id === mesaId);
        if (!mesa)
          return;
        await MesaService.restaurarAmbienteMesa(mesaId);
        notyf.success(`Mesa ${mesa.label} restaurada a ${mesa.original_environment}`);
        await syncMesasEnviroments();
      } catch (error) {
        notyf.error(error.message || "Error al restaurar la mesa");
        console.error(error);
      }
    };
    const loggedInWaiterName = userSession.sellerName;
    companySession.getCustomerDefault();
    let openMoveOrderModal = ref(false);
    let mesaToMoveOrder = ref(null);
    const openDocumentDialog = ref(false);
    const mesaForPayment = ref(null);
    const posBagForPayment = reactive({
      products: [],
      total: 0,
      barman: userSession.name,
      waiter: "",
      enterAmount: 0
    });
    const handleMoveOrder = (mesa) => {
      mesaToMoveOrder.value = mesa;
      openMoveOrderModal.value = true;
    };
    const confirmMoveOrder = async (mesaDestinoId) => {
      var _a2;
      if (mesaToMoveOrder.value) {
        try {
          await MesaService.changeTablePedido(mesaToMoveOrder.value.id, mesaDestinoId);
          const mesaDestino = ((_a2 = mesas.value.find((m) => m.id === mesaDestinoId)) == null ? void 0 : _a2.label) || mesaDestinoId;
          notyf.success(`Pedido movido de ${mesaToMoveOrder.value.label} a la mesa ${mesaDestino}.`);
          mesaToMoveOrder.value = null;
          openMoveOrderModal.value = false;
          await syncMesasEnviroments();
        } catch (error) {
          notyf.error("Error al mover el pedido. Intente nuevamente.");
          console.error(error);
          openMoveOrderModal.value = false;
        }
      }
    };
    const selectMesa = (mesa) => {
      if (modeEditMesas.value)
        return;
      if (modoSeleccionUnion.value) {
        toggleMesaSecundaria(mesa);
        return;
      }
      if (mesa.group_id && !mesa.is_main_table) {
        const mesaPrincipal = mesas.value.find((m) => m.group_id === mesa.group_id && m.is_main_table);
        if (mesaPrincipal) {
          console.log(`Mesa ${mesa.label} est\xE1 agrupada. Redirigiendo a mesa principal ${mesaPrincipal.label}`);
          notyf.success(`Mesa ${mesa.label} est\xE1 agrupada con ${mesaPrincipal.label}`);
          mesa = mesaPrincipal;
        } else {
          console.error(`No se encontr\xF3 la mesa principal del grupo ${mesa.group_id}`);
          notyf.error("Error: No se encontr\xF3 la mesa principal del grupo");
          return;
        }
      }
      if (mesa.status == MESA_DISPONIBLE) {
        mesasSession.setMesaSelected(mesa.id);
        if (mesaSelected.value) {
          mesaSelected.value.waiter = loggedInWaiterName;
        }
        canUpdate.value = true;
      } else {
        mesasSession.setMesaSelected(mesa.id);
        viewPos.value = true;
      }
    };
    const handleShipped = async (mesa) => {
      try {
        await mesasSession.setShippedMesa(mesa.id);
        notyf.success(`Pedido de ${mesa.label} en camino`);
      } catch (error) {
        notyf.error("Error al actualizar estado");
      }
    };
    const handleDelivered = async (mesa) => {
      try {
        await mesasSession.setDeliveredMesa(mesa.id);
        notyf.success(`Pedido de ${mesa.label} despachado`);
      } catch (error) {
        notyf.error("Error al actualizar estado");
      }
    };
    const handlePayment = async (mesa) => {
      var _a2;
      mesaForPayment.value = mesa;
      await mesasSession.setMesaSelected(mesa.id);
      await mesasSession.calculaTotalBag();
      const products = mesa.products || [];
      Object.assign(posBagForPayment, {
        products: JSON.parse(JSON.stringify(products)),
        total: mesa.total || 0,
        totalDiscount: (_a2 = mesa.totalDiscount) != null ? _a2 : 0,
        waiter: mesa.waiter || "",
        barman: userSession.name,
        enterAmount: 0
      });
      openDocumentDialog.value = true;
    };
    const viewMesas = () => {
      viewPos.value = false;
    };
    const closeDocumentDialog = () => {
      openDocumentDialog.value = false;
      mesaForPayment.value = null;
    };
    const backFromPayment = () => {
      openDocumentDialog.value = false;
    };
    const handlePaymentSuccess = async (data) => {
      if (mesaForPayment.value) {
        const mesaIndex = mesas.value.findIndex((m) => {
          var _a2;
          return m.id === ((_a2 = mesaForPayment.value) == null ? void 0 : _a2.id);
        });
        if (mesaIndex !== -1) {
          mesas.value[mesaIndex].is_paid = true;
          await MesaService.saveMesa(mesas.value[mesaIndex]);
          notyf.success(`Pago registrado para ${mesaForPayment.value.label}`);
        }
      }
      mesasSession.setMesaSelected(0);
      openDocumentDialog.value = false;
      mesaForPayment.value = null;
    };
    const handleCloseMesa = async (mesa) => {
      try {
        const index = mesas.value.findIndex((m) => m.id === mesa.id);
        if (index !== -1) {
          mesas.value[index].status = MESA_DISPONIBLE;
          mesas.value[index].products = [];
          mesas.value[index].personas = 0;
          mesas.value[index].cliente = "";
          mesas.value[index].comentarios = "";
          mesas.value[index].waiter = "";
          mesas.value[index].open = false;
          mesas.value[index].close = true;
          mesas.value[index].order_status = "pending";
          mesas.value[index].is_paid = false;
          mesas.value[index].total = 0;
          mesas.value[index].closed_forced = true;
          await MesaService.saveMesa(mesas.value[index]);
          use.resetTable(mesas.value[index].id);
          mesasSession.setMesaSelected(0);
          notyf.success(`Mesa ${mesa.label} cerrada correctamente`);
        }
      } catch (error) {
        notyf.error("Error al cerrar la mesa");
      }
    };
    const editMesaForm = (item) => {
      const { id, label, shape, environment } = item;
      stateFormMesa.id = id;
      stateFormMesa.label = label;
      stateFormMesa.shape = shape;
      stateFormMesa.environment = environment;
      openModalEditMesa.value = true;
    };
    const setMesas = () => {
      mesas.value = mesasSession.mesas;
    };
    const closeFormMesa = () => {
      openModalEditMesa.value = false;
    };
    const syncMesasEnviroments = async () => {
      await MesaService.syncMesasAndEnvironments();
      setMesas();
    };
    watch(() => [...mesasSession.mesas], () => {
      setMesas();
    });
    onMounted(() => {
      cargarConfiguracionVisualizacion();
      setMesas();
      syncMesasEnviroments();
      const TakeawayEnv = environments.find((env) => env.is_takeaway);
      if (TakeawayEnv) {
        activeTab.value = TakeawayEnv.name;
      }
      intervalId = window.setInterval(syncMesasEnviroments, 5e3);
    });
    const mesaSelectedDetails = ref(null);
    function showDetails(mesa) {
      mesaSelectedDetails.value = mesa;
    }
    onUnmounted(() => {
      if (intervalId !== void 0) {
        clearInterval(intervalId);
      }
    });
    return (_ctx, _cache) => {
      const _component_MesaForm = _sfc_main$4;
      const _component_CashDialog = _sfc_main$5;
      const _component_VButton = _sfc_main$6;
      const _component_VTabs = _sfc_main$7;
      const _component_VPlaceload = __unplugin_components_4;
      const _component_VPlaceloadWrap = __unplugin_components_5;
      const _component_MesaRestaurantPos = _sfc_main$8;
      return openBlock(), createElementBlock(Fragment, null, [
        createVNode(_component_MesaForm, {
          open: unref(openModalEditMesa),
          model: unref(stateFormMesa),
          onClose: closeFormMesa
        }, null, 8, ["open", "model"]),
        createVNode(_component_CashDialog),
        !unref(viewPos) ? (openBlock(), createElementBlock("div", _hoisted_1$1, [
          createBaseVNode("div", _hoisted_2, [
            createBaseVNode("div", _hoisted_3, [
              createBaseVNode("div", _hoisted_4, [
                modoSeleccionUnion.value && mesaPrincipalParaUnion.value ? (openBlock(), createElementBlock("div", _hoisted_5, [
                  createBaseVNode("div", _hoisted_6, [
                    createBaseVNode("div", null, [
                      createBaseVNode("strong", null, [
                        createBaseVNode("i", {
                          class: normalizeClass(grupoExistenteParaAgregar.value ? "fa-solid fa-plus" : "fa-solid fa-link")
                        }, null, 2),
                        createTextVNode(" " + toDisplayString(grupoExistenteParaAgregar.value ? " Agregando mesas" : " Uniendo Mesa") + " " + toDisplayString(mesaPrincipalParaUnion.value.label), 1)
                      ]),
                      _hoisted_7,
                      createBaseVNode("span", _hoisted_8, " Selecciona las mesas " + toDisplayString(grupoExistenteParaAgregar.value ? "adicionales" : "secundarias") + " (" + toDisplayString(mesasSecundariasSeleccionadas.value.size) + " seleccionadas) ", 1)
                    ]),
                    createBaseVNode("div", _hoisted_9, [
                      createVNode(_component_VButton, {
                        color: "success",
                        disabled: mesasSecundariasSeleccionadas.value.size === 0,
                        onClick: confirmarUnion
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(grupoExistenteParaAgregar.value ? "Agregar mesas" : "Confirmar uni\xF3n"), 1)
                        ]),
                        _: 1
                      }, 8, ["disabled"]),
                      createVNode(_component_VButton, {
                        color: "danger",
                        onClick: cancelarUnion
                      }, {
                        default: withCtx(() => [
                          _hoisted_10
                        ]),
                        _: 1
                      })
                    ])
                  ])
                ])) : createCommentVNode("", true),
                createVNode(_component_VTabs, {
                  selected: activeTab.value,
                  "onUpdate:selected": _cache[1] || (_cache[1] = ($event) => activeTab.value = $event),
                  tabs: unref(deliveryAndTakeaway).map((item) => ({
                    label: item.name,
                    value: item.name,
                    alignRight: item.is_delivery || item.is_takeaway
                  }))
                }, {
                  tab: withCtx(({ activeValue }) => [
                    createVNode(PedidoEnvironment, {
                      mesas: unref(mesasVisibles).filter((x) => x.environment == activeValue),
                      "todas-las-mesas": unref(todasLasMesas),
                      selected: unref(mesaSelected),
                      "mode-edit": unref(modeEditMesas),
                      "modo-union": modoSeleccionUnion.value,
                      "mesa-principal-union": mesaPrincipalParaUnion.value,
                      "mesas-seleccionadas": mesasSecundariasSeleccionadas.value,
                      "mostrar-config": unref(mostrarConfig),
                      "view-list": unref(isDeliveryOrTakeaway),
                      "is-delivery": unref(isDelivery),
                      "is-takeaway": unref(isTakeaway),
                      onSelect: _cache[0] || (_cache[0] = ($event) => modoSeleccionUnion.value ? toggleMesaSecundaria($event) : selectMesa($event)),
                      onDetails: showDetails,
                      onEdit: editMesaForm,
                      onSeparar: handleSepararMesa,
                      onSepararEspecifica: separarMesaEspecifica,
                      onIniciarUnion: iniciarModoUnion,
                      onAgregarMesa: iniciarModoUnion,
                      onToggleActive: handleToggleActive,
                      onMoverPedido: handleMoveOrder,
                      onShipped: handleShipped,
                      onDelivered: handleDelivered,
                      onPayment: handlePayment,
                      onCloseTable: handleCloseMesa,
                      onMoverAmbiente: handleMoveAmbiente,
                      onRestaurarAmbiente: handleRestaurarAmbiente
                    }, null, 8, ["mesas", "todas-las-mesas", "selected", "mode-edit", "modo-union", "mesa-principal-union", "mesas-seleccionadas", "mostrar-config", "view-list", "is-delivery", "is-takeaway"])
                  ]),
                  _: 1
                }, 8, ["selected", "tabs"]),
                unref(loadingRefresh) ? (openBlock(), createBlock(_component_VPlaceloadWrap, { key: 1 }, {
                  default: withCtx(() => [
                    createVNode(_component_VPlaceload, {
                      height: "25px",
                      width: "20%",
                      class: "mx-2"
                    })
                  ]),
                  _: 1
                })) : createCommentVNode("", true),
                !unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_11, [
                  createBaseVNode("div", _hoisted_12, [
                    withDirectives(createBaseVNode("div", _hoisted_13, _hoisted_16, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway)]
                    ]),
                    _hoisted_17,
                    withDirectives(createBaseVNode("div", _hoisted_19, _hoisted_22, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway)]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_23, _hoisted_26, 512), [
                      [vShow, !unref(isDeliveryOrTakeaway)]
                    ]),
                    withDirectives(createBaseVNode("div", _hoisted_27, _hoisted_30, 512), [
                      [vShow, unref(isDeliveryOrTakeaway)]
                    ])
                  ])
                ])) : createCommentVNode("", true),
                unref(modeEditMesas) ? (openBlock(), createElementBlock("div", _hoisted_31, _hoisted_36)) : createCommentVNode("", true)
              ])
            ]),
            createBaseVNode("div", _hoisted_37, [
              mesaSelectedDetails.value && mesaSelectedDetails.value.id !== 0 && mesaSelectedDetails.value.environment == "Para Llevar" ? (openBlock(), createElementBlock("div", _hoisted_38, [
                createVNode(BagDetails, { "mesa-select-details": mesaSelectedDetails.value }, null, 8, ["mesa-select-details"])
              ])) : createCommentVNode("", true),
              !mesaSelectedDetails.value || mesaSelectedDetails.value.id == 0 || mesaSelectedDetails.value.environment != "Para Llevar" ? (openBlock(), createElementBlock("div", _hoisted_39, _hoisted_41)) : createCommentVNode("", true)
            ])
          ])
        ])) : createCommentVNode("", true),
        createVNode(_sfc_main$2, {
          open: unref(openMoveOrderModal),
          "mesa-origen": unref(mesaToMoveOrder),
          "mesas-disponibles": unref(mesas).filter((x) => {
            var _a2;
            return x.status === unref(MESA_DISPONIBLE) && x.id !== ((_a2 = unref(mesaToMoveOrder)) == null ? void 0 : _a2.id);
          }),
          onClose: _cache[2] || (_cache[2] = ($event) => isRef(openMoveOrderModal) ? openMoveOrderModal.value = false : openMoveOrderModal = false),
          onConfirmMove: confirmMoveOrder
        }, null, 8, ["open", "mesa-origen", "mesas-disponibles"]),
        mesaForPayment.value ? (openBlock(), createBlock(_sfc_main$3, {
          key: 1,
          "pos-bag": unref(posBagForPayment),
          open: openDocumentDialog.value,
          "products-for-view": mesaForPayment.value.products || [],
          onClose: closeDocumentDialog,
          onBack: backFromPayment,
          onPaymentSuccess: handlePaymentSuccess
        }, null, 8, ["pos-bag", "open", "products-for-view"])) : createCommentVNode("", true),
        createVNode(MoveAmbienteModal, {
          open: unref(openMoveAmbienteModal),
          mesa: unref(mesaToMoveAmbiente),
          "ambientes-disponibles": unref(environments).map((e) => e.name),
          onClose: _cache[3] || (_cache[3] = ($event) => isRef(openMoveAmbienteModal) ? openMoveAmbienteModal.value = false : openMoveAmbienteModal = false),
          onConfirm: confirmMoveAmbiente
        }, null, 8, ["open", "mesa", "ambientes-disponibles"]),
        unref(viewPos) ? (openBlock(), createBlock(_component_MesaRestaurantPos, {
          key: 2,
          onViewMesas: viewMesas
        })) : createCommentVNode("", true)
      ], 64);
    };
  }
});
var TakeawayRestaurant = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-46ccd37c"]]);
const _hoisted_1 = { class: "page-content-inner" };
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    pageTitle.value = "Takeaway";
    useHead({
      title: "Takeaway"
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div", _hoisted_1, [
        createVNode(TakeawayRestaurant)
      ]);
    };
  }
});
export { _sfc_main as default };
