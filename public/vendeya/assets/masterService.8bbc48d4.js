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
import { u as useUserSession, p as provideApi } from "./index.ea91e044.js";
import { d as defineStore, u as useStorage } from "./vendor.80d481a8.js";
const useCompanySession = defineStore("companySession", () => {
  const logo = useStorage("logo", "");
  const ruc = useStorage("ruc", "");
  const address = useStorage("address", "");
  const defaulPaymentMethod = [
    {
      id: "01",
      description: "Efectivo",
      has_card: false,
      is_credit: 0,
      is_cash: 1
    }
  ];
  const defaulPaymentDestinations = [];
  const paymentsMethods = useStorage("paymentsMethods", defaulPaymentMethod);
  const paymentsDestinations = useStorage("paymentsDestinations", defaulPaymentDestinations);
  const seriesDefaultData = [];
  const series = useStorage("series", seriesDefaultData);
  const establishments = useStorage("establishments", Array());
  const waiters = useStorage("waiters", Array());
  const companyDefault = {
    id: 0,
    identity_document_type_id: "0",
    logo: "",
    name: "",
    trade_name: "",
    number: 0
  };
  const menuDefault = {
    POS: "app",
    TABLES: "app-mesas",
    ORDER: "app-orders",
    COMMANDS: "app-commands"
  };
  const configurationDefault = {
    menu_pos: true,
    menu_order: true,
    menu_tables: true,
    menu_bar: true,
    menu_kitchen: true,
    first_menu: "POS",
    tables_quantity: 25,
    tables_quantity_environment_2: 25,
    tables_quantity_environment_3: 25,
    tables_quantity_environment_4: 25,
    enabled_environment_1: true,
    enabled_environment_2: false,
    enabled_environment_3: false,
    enabled_environment_4: false,
    items_maintenance: false,
    enabled_send_command: false,
    enabled_print_command: false,
    enabled_printsend_command: false,
    enabled_command_waiter: false,
    enabled_pos_waiter: false,
    enabled_close_table: true
  };
  const company = useStorage("company", companyDefault);
  const configuration = useStorage("configuration", configurationDefault);
  const firstMenu = useStorage("firstMenu", "app");
  const customerDefault = {
    id: 0,
    codigo_tipo_documento_identidad: "0",
    numero_documento: "99999999",
    apellidos_y_nombres_o_razon_social: "Clientes - Varios",
    codigo_pais: "PE",
    direccion: "Av. 2 de Mayo",
    correo_electronico: "vlientesvarios@gmail.com",
    telefono: "427-1148"
  };
  const customersDefaultData = [];
  const customers = useStorage("customers", customersDefaultData);
  function setLogo(value) {
    logo.value = value;
  }
  function setRuc(value) {
    ruc.value = value;
  }
  function setAddress(value) {
    address.value = value;
  }
  function setSeries(value) {
    series.value = value;
  }
  function setEstablishments(value) {
    establishments.value = value;
  }
  function setCompany(value) {
    company.value = value;
  }
  function setCustomers(value) {
    customers.value = value;
    const customerDefaultExist = customers.value.find((c) => c.codigo_tipo_documento_identidad == "0" && c.numero_documento == "99999999");
    if (!customerDefaultExist) {
      customers.value.push(customerDefault);
    }
  }
  function savePaymentMethods(data) {
    paymentsMethods.value = data;
  }
  function savePaymentDestinations(data) {
    paymentsDestinations.value = data;
  }
  function getCustomerDefault() {
    const customerDefaultExist = customers.value.find((c) => c.codigo_tipo_documento_identidad == "0" && c.numero_documento == "99999999");
    return customerDefaultExist ? customerDefaultExist : customerDefault;
  }
  function getCustomerById(id) {
    const customer = customers.value.find((customer2) => customer2.id == id);
    return customer ? customer : customerDefault;
  }
  function setConfiguration(value) {
    configuration.value = value;
    getFirstOptionMenu();
  }
  function getFirstOptionMenu() {
    const currentMenu = configuration.value.first_menu;
    const getOption = menuDefault[currentMenu];
    firstMenu.value = getOption;
  }
  function setWaiters(records) {
    waiters.value = records;
  }
  return {
    logo,
    ruc,
    address,
    series,
    establishments,
    company,
    customers,
    configuration,
    firstMenu,
    paymentsMethods,
    paymentsDestinations,
    waiters,
    setLogo,
    setRuc,
    setAddress,
    setSeries,
    setEstablishments,
    setCompany,
    setCustomers,
    getCustomerDefault,
    getCustomerById,
    setConfiguration,
    savePaymentMethods,
    savePaymentDestinations,
    setWaiters
  };
});
const useProductSession = defineStore("productSession", () => {
  const defaultProductsData = [];
  const products = useStorage("products", defaultProductsData);
  const defaultCategoriesData = [
    {
      id: 0,
      name: "Todos",
      selected: true
    }
  ];
  const categories = useStorage("categories", defaultCategoriesData);
  function setProducts(data) {
    products.value = data;
  }
  function setCategories(data) {
    categories.value = data;
    categories.value.unshift({
      id: 0,
      name: "Todos",
      selected: true
    });
  }
  const getProducts = () => {
    return products.value;
  };
  return {
    products,
    setProducts,
    categories,
    setCategories,
    getProducts
  };
});
const STATUS_NEW = {
  description: "Nuevo",
  id: 0
};
const userSession = useUserSession();
const companySession = useCompanySession();
const productSession = useProductSession();
const saveDataCompany = async () => {
  const { data } = await provideApi().get(`${userSession.ssl + userSession.url}/api/company`);
  companySession.setCompany(data.company);
  companySession.setEstablishments(data.establishments);
  companySession.setSeries(data.series);
  companySession.setCustomers(data.customers);
  companySession.savePaymentMethods(data.payment_method_types);
  companySession.savePaymentDestinations(data.payment_destinations);
};
const saveDataProducts = async () => {
  const { data } = await provideApi().get("/sellnow/items");
  const products = data.data;
  const productsStorage = [];
  products.forEach((element) => {
    const item = {
      internalId: element.internal_id,
      barcode: element.barcode,
      id: element.id,
      name: element.description,
      imageUrl: element.image_url,
      price: Number(element.price),
      currencyTypeSymbol: element.currency_type_symbol,
      quantity: 0,
      categoryId: element.category_id,
      itemCode: element.item_code,
      unitTypeId: element.unit_type_id,
      statusBar: STATUS_NEW.id,
      statusKitchen: STATUS_NEW.id,
      sale_affectation_igv_type_id: element.sale_affectation_igv_type_id,
      quantity_send: 0,
      quantity_pending: 0,
      purchase_unit_price: Number(element.purchase_unit_price),
      favorite: element.favorite
    };
    productsStorage.push(item);
  });
  productSession.setProducts(productsStorage);
};
const saveDataCategories = async () => {
  const { data } = await provideApi().get("/sellnow/categories");
  const catagories = data.data;
  const categoriessStorage = catagories.map((row) => {
    return __spreadProps(__spreadValues({}, row), { selected: false });
  });
  productSession.setCategories(categoriessStorage);
};
const saveDataConfiguration = async () => {
  const { data } = await provideApi().get("/restaurant/configurations");
  const configurations = data.data;
  companySession.setConfiguration(configurations);
};
const syncData = async () => {
  await saveDataCompany();
  await saveDataProducts();
  await saveDataCategories();
  await saveDataConfiguration();
};
const MasterService = { syncData, saveDataProducts };
var masterService = /* @__PURE__ */ Object.freeze({
  __proto__: null,
  [Symbol.toStringTag]: "Module",
  MasterService
});
export { MasterService as M, useProductSession as a, masterService as m, useCompanySession as u };
