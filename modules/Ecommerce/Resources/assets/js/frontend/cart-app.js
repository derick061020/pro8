// Cart Application - Ecommerce Module
// Main Vue instance for shopping cart detail page

var app_cart = new Vue({
    el: '#app',
    data: {
        form_contact: {
            address:   '',
            telephone:   '',
        },
        addressModal: {
            address: '',
            reference: '',
            latitude: -12.046374,
            longitude: -77.042793,
            preventSearch: false
        },
        map: null,
        marker: null,
        geocoder: null,
        addressSearchTimeout: null,
        payment_cash: {
            amount: '',
            clicked: false
        },
        response_search: {},
        text_search: '',
        loading_search: false,
        identity_document_types: [{
            id: '1',
            description: 'DNI'
        }, {
            id: '6',
            description: 'RUC'
        }],
        formIdentity: {
            identity_document_type_id: ''
        },
        records: [],
        records_old: [],
        couponField: '',
        couponMessage: null,
        couponLoading: false,
        appliedCoupon: null,
        order_generated: {},
        summary: {
            subtotal: '0.0',
            tax: '0.0',
            total: '0.0'
        },
        aux_totals: {},
        form_document: {},
        user: {},
        typeDocumentSelected: '',
        response_order_total:0,
        errors: {},
        exchange_rate_sale: '',
        typeDocuments: '',
        typeDocumentList: [],
        numberDocument: '',
        phone_whatsapp: window.__ecommerce_config?.phone_whatsapp || '',
        global_discount_type: window.__ecommerce_config?.global_discount_type || {},
        all_identity_document_types : [{id: '6', name: 'RUC'}, {id: '0', name: 'DOC'},{id: '4', name: 'CE'},{id: '1', name: 'DNI'}],
        addressSuggestions: [],
        departments: [],
        provinces: [],
        districts: [],
        selectedDepartment: '',
        selectedProvince: '',
        selectedDistrict: '',
        highlightedIndex: -1,
        addressSearchTimeout: null,
        // Zona de delivery seleccionada (usada en cálculos y documento)
        deliveryZone: null,
        // Todas las zonas que hacen match con la dirección del cliente
        availableDeliveryZones: [],
        deliveryMessage: '',
        // Primera dirección guardada del cliente (cargada desde el servidor)
        userDefaultAddress: window.__ecommerce_config?.userAddress || null,
        // Método de pago seleccionado: 'culqi' | 'cash' | null
        selectedPaymentMethod: null,
        // Controla si se emiten documentos electrónicos (factura/boleta) o solo notas de venta
        enable_electronic_documents: window.__ecommerce_config?.enable_electronic_documents || false,
        // Recojo en tienda
        enableStorePickup: window.__ecommerce_config?.enable_store_pickup || false,
        pickupBranches: window.__ecommerce_config?.pickup_branches || [],
        selectedPickupBranch: null,
        isPickupMode: false,
        // Métodos de pago adicionales
        enableYape: window.__ecommerce_config?.enable_yape || false,
        enableTransfer: window.__ecommerce_config?.enable_transfer || false,
    },
    computed: {
        maxLength: function () {
            if (this.typeDocuments === '6') {
                return 11
            }
            if (this.typeDocuments === '1') {
                return 8
            }
            return 15
        },
        ubigeoLabel: function () {
            if (!this.selectedDepartment || !this.selectedProvince || !this.selectedDistrict) return '';
            const dept = this.departments.find(d => d.value === this.selectedDepartment);
            const prov = this.provinces.find(p => p.value === this.selectedProvince);
            const dist = this.districts.find(d => d.value === this.selectedDistrict);
            if (!dept || !prov || !dist) return '';
            return dept.label.toUpperCase() + ' / ' + prov.label.toUpperCase() + ' / ' + dist.label.toUpperCase() + ' (' + this.selectedDistrict + ')';
        },
        // Etiqueta del tipo de documento inferido del número del usuario (usado en modo solo-lectura)
        invoiceTypeLabel: function () {
            const num = this.user && this.user.number ? String(this.user.number).trim() : '';
            if (num.length === 8)  return 'Boleta de Venta';
            if (num.length === 11) return 'Factura';
            return 'Nota de Venta';
        },
    },
    watch: {
        'addressModal.address': function(newValue) {
        },
        selectedPaymentMethod(val) {
            // Mostrar u ocultar el widget de PayPal que está fuera del scope de Vue
            const el = document.getElementById('paypal-widget-container');
            if (el) el.style.display = (val === 'paypal') ? 'block' : 'none';
        }
    },
    async mounted() {
        await this.changeExchangeRate(moment().format("YYYY-MM-DD"))

        let exchange_rate_sale = this.exchange_rate_sale
        let contex = this

        $(".input_quantity").change(function (e) {
            let value = parseFloat($(this).val())
            let id = $(this).data('product')
            let row = contex.records.find(x => x.id == id)

            if(row.currency_type_id === 'USD') {
                row.sub_total = ((parseFloat(row.sale_unit_price) * value) * exchange_rate_sale).toFixed(2)
            } else {
                row.sub_total = (parseFloat(row.sale_unit_price) * value).toFixed(2)
            }

            row.cantidad = value
            contex.calculateSummary()
        })

        this.records.forEach(function (item) {
            if(item.currency_type_id === 'USD') {
                item.sub_total = (parseFloat(item.sub_total) * exchange_rate_sale).toFixed(2)
                item.exchange_rate_sale = exchange_rate_sale
            }
            item.sale_unit_price = parseFloat(item.sale_unit_price).toFixed(2)
        })

        this.calculateSummary()

        // Inicializar Google Maps cuando esté disponible
        if (typeof google !== 'undefined') {
            this.initMap()
        }

        // Cargar ubicaciones y autocompletar si el usuario tiene una dirección guardada
        this.fetchLocations().then(() => {
            this.loadDefaultAddress();
        });
    },
    created() {
        let array = localStorage.getItem('products_cart');
        array = JSON.parse(array)
        if (array) {
            this.records = array.map(function (item) {
                let obj = item
                obj.cantidad = item.quantity ? parseInt(item.quantity) : 1
                obj.sub_total = (parseFloat(item.sale_unit_price) * obj.cantidad).toFixed(2)
                obj.exchange_rate_sale = ''
                return obj
            })
        }
        this.initForm();
    },
    methods: {
        extractAndSetUbigeoFromComponents(components) {
            if (!components) return;

            let department = '';
            let province   = '';
            let district   = '';

            components.forEach(component => {
                const types    = component.types || [];
                const longName = (component.long_name || '').toUpperCase();

                if (types.includes('administrative_area_level_1')) {
                    department = longName
                        .replace('PROVINCIA DE ', '')
                        .replace('DEPARTAMENTO DE ', '')
                        .replace(' REGION', '')
                        .trim();
                }
                if (types.includes('administrative_area_level_2')) {
                    province = longName
                        .replace('PROVINCIA DE ', '')
                        .trim();
                }
                if (types.includes('locality') ||
                    types.includes('sublocality_level_1') ||
                    types.includes('administrative_area_level_3')) {
                    if (!district) {
                        district = longName
                            .replace('DISTRITO DE ', '')
                            .trim();
                    }
                }
            });

            this.setDepartmentByName(department);
            this.setProvinceByName(province);
            this.setDistrictByName(district);
        },
        initAutocomplete() {
            console.log('Autocomplete listo con nueva API de Google Places');
        },

        async onAddressInputChange() {
            const query = this.addressModal.address;
            if (!query || query.length < 3) {
                this.addressSuggestions = [];
                return;
            }

            clearTimeout(this.addressSearchTimeout);
            this.addressSearchTimeout = setTimeout(async () => {
                try {
                    const { AutocompleteSuggestion } = await google.maps.importLibrary("places");

                    const request = {
                        input: query,
                        includedRegionCodes: ['pe'],
                        language: 'es'
                    };

                    const { suggestions } = await AutocompleteSuggestion.fetchAutocompleteSuggestions(request);

                    this.addressSuggestions = suggestions.map(s => {
                        const pred = s.placePrediction;
                        return {
                            placeId: pred.placeId,
                            mainText: pred.mainText?.toString() || pred.text?.toString() || '',
                            secondaryText: pred.secondaryText?.toString() || '',
                            fullText: pred.text?.toString() || ''
                        };
                    });

                    this.highlightedIndex = -1;
                } catch (error) {
                    console.error('Error obteniendo sugerencias:', error);
                    this.addressSuggestions = [];
                }
            }, 300);
        },

        async selectSuggestionFromList(suggestion) {
            this.addressModal.address = suggestion.fullText;
            this.addressSuggestions = [];
            this.highlightedIndex = -1;

            try {
                const { Place } = await google.maps.importLibrary("places");

                const place = new Place({
                    id: suggestion.placeId,
                    requestedLanguage: 'es'
                });

                await place.fetchFields({
                    fields: ['displayName', 'formattedAddress', 'location', 'addressComponents']
                });

                const loc = place.location;
                this.addressModal.latitude = loc.lat();
                this.addressModal.longitude = loc.lng();

                if (this.map && this.marker) {
                    this.map.setCenter(loc);
                    this.map.setZoom(17);
                    this.marker.setPosition(loc);
                }

                this.addressModal.address = place.formattedAddress || suggestion.fullText;

                const normalizedComponents = (place.addressComponents || []).map(c => ({
                    long_name: c.longText || c.long_name || '',
                    types: c.types || []
                }));

                this.extractAndSetUbigeoFromComponents(normalizedComponents);

                console.log('📍 Sugerencia seleccionada:');
                console.log('   Dirección    :', this.addressModal.address);
                console.log('   Latitud      :', this.addressModal.latitude);
                console.log('   Longitud     :', this.addressModal.longitude);

            } catch (error) {
                console.error('Error obteniendo detalles del lugar:', error);
            }
        },

        extractAndSetUbigeo(addressComponents) {
            if (!addressComponents) return;

            let department = '';
            let province  = '';
            let district  = '';

            addressComponents.forEach(component => {
                const types = component.types || [];
                const longName = (component.longText || component.long_name || '').toUpperCase();

                if (types.includes('administrative_area_level_1')) {
                    department = longName;
                }
                if (types.includes('administrative_area_level_2')) {
                    province = longName;
                }
                if (types.includes('locality') || types.includes('sublocality_level_1') || types.includes('administrative_area_level_3')) {
                    if (!district) district = longName;
                }
            });

            this.setDepartmentByName(department);
            this.setProvinceByName(province);
            this.setDistrictByName(district);

            console.log('📍 Ubicación seleccionada:');
            console.log('   Departamento:', department);
            console.log('   Provincia   :', province);
            console.log('   Distrito    :', district);
        },

        setDepartmentByName(name) {
            if (!name) return;
            const found = this.departments.find(d => d.label.toUpperCase() === name);
            if (found) {
                this.selectedDepartment = found.value;
                this.updateProvinces();
            }
        },

        setProvinceByName(name) {
            if (!name) return;
            this.$nextTick(() => {
                const found = this.provinces.find(p => p.label.toUpperCase() === name);
                if (found) {
                    this.selectedProvince = found.value;
                    this.updateDistricts();
                }
            });
        },

        setDistrictByName(name) {
            if (!name) return;
            this.$nextTick(() => {
                setTimeout(() => {
                    const found = this.districts.find(d => d.label.toUpperCase() === name);
                    if (found) {
                        this.selectedDistrict = found.value;
                    }
                }, 100);
            });
        },

        moveSuggestion(dir) {
            if (!this.addressSuggestions.length) return;
            this.highlightedIndex = Math.max(0, Math.min(this.addressSuggestions.length - 1, this.highlightedIndex + dir));
        },

        selectHighlighted() {
            if (this.highlightedIndex >= 0 && this.addressSuggestions[this.highlightedIndex]) {
                this.selectSuggestionFromList(this.addressSuggestions[this.highlightedIndex]);
            }
        },

        clearSuggestions() {
            this.addressSuggestions = [];
            this.highlightedIndex = -1;
        },
        incrementQuantity(row) {
            if (typeof row.cantidad !== 'number' || isNaN(row.cantidad)) {
                row.cantidad = 1;
            } else {
                row.cantidad++;
            }
            this.updateRowSubtotal(row);
            this.calculateSummary();
            this.saveCartToLocalStorage();
        },
        decrementQuantity(row) {
            if (typeof row.cantidad !== 'number' || isNaN(row.cantidad) || row.cantidad <= 1) {
                row.cantidad = 1;
            } else {
                row.cantidad--;
            }
            this.updateRowSubtotal(row);
            this.calculateSummary();
            this.saveCartToLocalStorage();
        },
        updateRowSubtotal(row) {
            let exchange_rate_sale = this.exchange_rate_sale;
            if(row.currency_type_id === 'USD') {
                row.sub_total = ((parseFloat(row.sale_unit_price) * row.cantidad) * exchange_rate_sale).toFixed(2);
            } else {
                row.sub_total = (parseFloat(row.sale_unit_price) * row.cantidad).toFixed(2);
            }
        },
        saveCartToLocalStorage() {
            localStorage.setItem('products_cart', JSON.stringify(this.records));
        },
        async changeExchangeRate(exchange_rate_date){
            var response = await axios.get(`/exchange_rate/ecommence/${exchange_rate_date}`)
            this.exchange_rate_sale = parseFloat(response.data.sale)
        },
        optionDocument() {
            this.typeDocumentList = []
            this.typeDocuments = null

            if(this.form_document.codigo_tipo_documento == '01')
            {
                this.typeDocumentList = this.getIdentityDocumentTypes(['6'])
            }
            else if (this.form_document.codigo_tipo_documento == '03' && this.payment_cash.amount >= 700)
            {
                this.typeDocumentList = this.getIdentityDocumentTypes(['1'])
            }
            else if (this.form_document.codigo_tipo_documento == '80')
            {
                this.typeDocumentList = (this.payment_cash.amount >= 700) ? this.getIdentityDocumentTypes(['6', '1']) : this.getIdentityDocumentTypes()
            }
            else {
                this.typeDocumentList = this.getIdentityDocumentTypes(['0', '1', '4'])
            }
        },
        getIdentityDocumentTypes(identity_document_types_id = null){
            if(!identity_document_types_id) return this.all_identity_document_types
            return this.all_identity_document_types.filter((item) => {
                return identity_document_types_id.includes(item.id)
            })
        },
        refreshSetDataCustomer() {
            this.form_document.datos_del_cliente_o_receptor.direccion = this.form_contact.address
            this.form_document.datos_del_cliente_o_receptor.telefono = this.form_contact.telephone
            this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = this.typeDocuments
            this.form_document.datos_del_cliente_o_receptor.numero_documento = this.numberDocument
            this.form_document.datos_del_cliente_o_receptor.identity_document_type_id = this.typeDocuments
        },
        async getFormPaymentCash() {
            this.refreshSetDataCustomer()

            // Calcular la dirección de envio según el modo seleccionado
            let shippingAddress = '';
            if (this.isPickupMode && this.selectedPickupBranch) {
                shippingAddress = 'Recojo en tienda: ' + this.selectedPickupBranch.name +
                    (this.selectedPickupBranch.address ? ' — ' + this.selectedPickupBranch.address : '');
            } else {
                shippingAddress = this.form_contact.address || '';
            }

            let precio = Math.round(Number(this.summary.total) * 100).toFixed(2);
            let precio_culqi = Number(this.summary.total)
            return {
                producto: 'Compras Ecommerce Facturador Pro',
                precio: precio,
                precio_culqi: precio_culqi,
                customer: this.form_document.datos_del_cliente_o_receptor,
                items: this.records,
                purchase: await this.getDocument(),
                discount_coupon_code: this.appliedCoupon ? this.appliedCoupon.code : null,
                discount_coupon_id: this.appliedCoupon ? this.appliedCoupon.id : null,
                total_discount: this.appliedCoupon ? this.appliedCoupon.discount : 0,
                shipping_address: shippingAddress,
                reference_payment: this.getSelectedReferencePayment(),
            }
        },
        // Mapea el método de pago seleccionado al valor que se guarda en la orden
        getSelectedReferencePayment() {
            const map = {
                cash:     'efectivo',
                yape:     'yape',
                transfer: 'transferencia',
                culqi:    'culqi',
                paypal:   'paypal',
            };
            return map[this.selectedPaymentMethod] || 'efectivo';
        },
        showSwalMessage(title, text, type){
            swal({
                title: title,
                text: text,
                type: type
            })
        },
        executePayment() {
            if (this.selectedPaymentMethod === 'culqi') {
                execCulqi();
            } else if (['cash', 'yape', 'transfer'].includes(this.selectedPaymentMethod)) {
                this.paymentCash();
            }
        },
        async paymentCash() {
            if(!this.form_document.codigo_tipo_documento) {
                return this.showSwalMessage('Ocurrió un error!', 'El campo tipo de comprobante es obligatorio', 'error')
            }

            if(!this.form_contact.address) {
                return this.showSwalMessage('Ocurrió un error!', 'El campo dirección es obligatorio', 'error')
            }

            if(!this.form_contact.telephone) {
                return this.showSwalMessage('Ocurrió un error!', 'El campo teléfono es obligatorio', 'error')
            }

            let product = JSON.parse(localStorage.getItem('products_cart'));

            if (product.length < 1){
                swal({
                    title: "No se han encontrado productos",
                    text: "Por favor seleccione algún producto de la tienda.",
                    type: "error"
                })
                return
            }

            swal({
                title: "Estamos generando el Pago.",
                text: `Por favor no cierre esta ventana hasta que el proceso termine.`,
                focusConfirm: false,
                onOpen: () => {
                    Swal.showLoading()
                }
            });

            let url_finally = window.__routes?.payment_cash || '/ecommerce/payment/cash';
            let response = await axios.post(url_finally, await this.getFormPaymentCash(), this.getHeaderConfig()).then(response => {
                    if (response.data.success) {
                        this.saveContactDataUser()
                        this.clearShoppingCart()
                        this.response_order_total = response.data.order.total
                        swal({
                            title: "Gracias por su pago!",
                            text: "En breve le enviaremos un correo electrónico con los detalles de su compra.",
                            type: "success"
                        }).then((x) => {
                            app_cart.order_generated = order
                        })
                    }
                }).catch(error => {
                    swal("Pago No realizado", 'Sucedió algo inesperado.', "error");
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.log(error);
                    }
                });
        },
        redirectHome() {
            window.location = window.__routes?.home || "/ecommerce";
        },
        getHeaderConfig() {
            let token = this.user.api_token
            let axiosConfig = {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                }
            };
            return axiosConfig;
        },
        async getDocument() {
            this.form_document.items = await this.getItemsDocument();
            const descuentos = await this.getDescuentos();
            const totales = await this.getTotales(descuentos);
            let doc = Object.assign({}, this.form_document);
            doc.totales = totales;
            if (descuentos.length > 0) {
                doc.descuentos = descuentos;
            }
            if (doc.codigo_tipo_documento == '01') {
                doc.serie_documento = 'F001';
            } else if (doc.codigo_tipo_documento == '03') {
                doc.serie_documento = 'B001';
            } else {
                doc.serie_documento = null;
            }
            return doc;
        },
        async getDescuentos() {
            if(!this.appliedCoupon || !this.global_discount_type) return [];

            let montoEntrada = parseFloat(this.appliedCoupon.discount || 0);
            let codigo = this.global_discount_type.id;
            let descripcion = this.global_discount_type.description;
            let base = 0;
            let montoCalculado = 0;

            if(this.global_discount_type.base == 1) {
                montoCalculado = montoEntrada / 1.18;
                base = parseFloat(this.summary.total_taxed);
            } else {
                montoCalculado = montoEntrada;
                base = parseFloat(this.summary.total_value) + parseFloat(this.summary.total_igv);
            }

            let factor = base > 0 ? (montoCalculado / base) : 0;

            return [{
                codigo: codigo,
                descripcion: descripcion,
                factor: parseFloat(factor.toFixed(5)),
                monto: parseFloat(montoCalculado.toFixed(2)),
                base: parseFloat(base.toFixed(2))
            }];
        },
        async getTotales(descuentos = []) {
            let total_descuentos_monto = 0.00;
            if (descuentos.length > 0) {
                total_descuentos_monto = descuentos.reduce((sum, d) => sum + (parseFloat(d.monto) || 0), 0);
            }

            let base_antes = parseFloat(this.aux_totals.total_taxed);
            let igv_antes  = parseFloat(this.aux_totals.total_igv);

            let delivery_price = (this.deliveryZone && this.deliveryZone.price) ? parseFloat(this.deliveryZone.price) : 0;
            let delivery_base  = parseFloat((delivery_price / 1.18).toFixed(2));
            let delivery_igv   = parseFloat((delivery_price - delivery_base).toFixed(2));

            let total_operaciones_gravadas = base_antes + delivery_base;
            let total_igv                  = igv_antes  + delivery_igv;
            let total_venta                = total_operaciones_gravadas + total_igv;

            if (descuentos.length > 0 && this.global_discount_type) {
                if (this.global_discount_type.base == 1) {
                    total_operaciones_gravadas = parseFloat((total_operaciones_gravadas - total_descuentos_monto).toFixed(2));
                    total_igv   = parseFloat((total_operaciones_gravadas * 0.18).toFixed(2));
                    total_venta = parseFloat((total_operaciones_gravadas + total_igv).toFixed(2));
                } else {
                    total_venta = parseFloat((total_venta - total_descuentos_monto).toFixed(2));
                }
            }

            return {
                total_descuentos:               total_descuentos_monto,
                total_exportacion:              0.00,
                total_operaciones_gravadas:     total_operaciones_gravadas,
                total_operaciones_inafectas:    parseFloat(this.aux_totals.total_exonerated || 0),
                total_operaciones_exoneradas:   0.00,
                total_operaciones_gratuitas:    0.00,
                total_igv:                      total_igv,
                total_impuestos:                total_igv,
                total_valor:                    total_operaciones_gravadas,
                total_venta:                    total_venta
            };
        },
        openAddressModal() {
            $('#addressModal').modal('show')
            setTimeout(() => {
                if (!this.map) {
                    this.initMap()
                } else {
                    google.maps.event.trigger(this.map, 'resize')
                    this.map.setCenter({lat: this.addressModal.latitude, lng: this.addressModal.longitude})
                }
            }, 300)
        },
        closeAddressModal() {
            const modalElement = document.getElementById('addressModal');
            if (modalElement) {
                $(modalElement).modal('hide');
            } else {
                console.error('No se encontró el elemento del modal.');
            }
        },
        confirmAddress() {
            let fullAddress = ''
            if (this.addressModal.address) {
                fullAddress = this.addressModal.address
            }
            if (this.addressModal.reference) {
                fullAddress += ' - Ref: ' + this.addressModal.reference
            }

            this.form_contact.address = fullAddress
            this.closeAddressModal()
            this.checkDeliveryZone()
        },
        initMap() {
            const defaultLocation = { lat: -12.046374, lng: -77.042793 };

            this.map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 15
            });

            this.marker = new google.maps.Marker({
                position: defaultLocation,
                map: this.map,
                draggable: true
            });

            this.geocoder = new google.maps.Geocoder();

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        this.map.setCenter(userLocation);
                        this.marker.setPosition(userLocation);
                        this.addressModal.latitude = userLocation.lat;
                        this.addressModal.longitude = userLocation.lng;
                    },
                    (error) => {
                        console.error('Error obteniendo la ubicación actual:', error);
                    }
                );
            } else {
                console.warn('La geolocalización no está soportada por este navegador.');
            }

            this.map.addListener('click', (event) => {
                const clickedLocation = {
                    lat: event.latLng.lat(),
                    lng: event.latLng.lng()
                };

                this.marker.setPosition(clickedLocation);
                this.addressModal.latitude = clickedLocation.lat;
                this.addressModal.longitude = clickedLocation.lng;
                this.addressModal.preventSearch = true;

                this.geocoder.geocode({ location: clickedLocation }, (results, status) => {
                    if (status === google.maps.GeocoderStatus.OK && results[0]) {
                        this.addressModal.address = results[0].formatted_address;
                        this.extractAndSetUbigeoFromComponents(results[0].address_components);
                    }

                    setTimeout(() => {
                        this.addressModal.preventSearch = false;
                    }, 1000);
                });
            });
        },
        getAddressFromLatLng(latLng) {
            if (!this.geocoder) return

            this.geocoder.geocode({'location': latLng}, (results, status) => {
                if (status === 'OK' && results[0]) {
                    this.addressModal.address = results[0].formatted_address
                }
            })
        },
        searchAddressInMap(address) {
            if (!this.geocoder || !this.map) {
                console.warn('Geocoder o Map no inicializados');
                return;
            }

            console.log('Buscando dirección:', address);
            this.geocoder.geocode({'address': address}, (results, status) => {
                if (status === 'OK' && results[0]) {
                    console.log('Dirección encontrada:', results[0].formatted_address);
                    const location = results[0].geometry.location;
                    this.addressModal.latitude = location.lat();
                    this.addressModal.longitude = location.lng();

                    this.map.setCenter(location);
                    this.map.setZoom(16);
                    if (this.marker) {
                        this.marker.setPosition(location);
                    }
                } else {
                    console.warn('Dirección no encontrada. Estado:', status);
                }
            })
        },
        async getItemsDocument() {
            let rec = await this.records.map((item) => {
                let sale_unit_price = 0
                let total_exonerated = 0
                let total_igv = 0
                let total_val = 0
                let total = 0
                let percentage_igv = 18
                let nombre_producto_pdf = item.promotion_id ? item.description : null

                if (item.sale_affectation_igv_type_id === '10') {
                    if(item.currency_type_id === 'USD') {
                        sale_unit_price = (parseFloat(item.sale_unit_price) * this.exchange_rate_sale).toFixed(2)
                    } else {
                        sale_unit_price = item.sale_unit_price
                    }

                    let unit_value = sale_unit_price / (1 + percentage_igv / 100)
                    total_igv = item.cantidad * parseFloat(sale_unit_price - unit_value)
                    total = (item.cantidad * sale_unit_price)
                    total_val = (unit_value * item.cantidad)

                    return {
                        "codigo_interno": (item.internal_id) ? item.internal_id:"",
                        "descripcion": item.description,
                        "codigo_producto_sunat": "",
                        "unidad_de_medida": item.unit_type_id,
                        "cantidad": item.cantidad,
                        "valor_unitario": unit_value,
                        "codigo_tipo_precio": "01",
                        "precio_unitario": sale_unit_price,
                        "codigo_tipo_afectacion_igv": "10",
                        "total_base_igv": total_val,
                        "porcentaje_igv": percentage_igv,
                        "total_igv": total_igv,
                        "total_impuestos": total_igv,
                        "total_valor_item": total_val,
                        "total_item": total,
                        "actualizar_descripcion": false,
                        "nombre_producto_pdf": nombre_producto_pdf
                    }
                }

                if (item.sale_affectation_igv_type_id === '20') {
                    if(item.currency_type_id === 'USD') {
                        sale_unit_price = (parseFloat(item.sale_unit_price) * this.exchange_rate_sale).toFixed(2)
                    } else {
                        sale_unit_price = item.sale_unit_price
                    }

                    let unit_value = parseFloat(sale_unit_price)
                    total_igv = 0
                    total = (parseFloat(item.cantidad) * parseFloat(sale_unit_price))
                    total_val = (parseFloat(unit_value) * parseFloat(item.cantidad))

                    return {
                        "codigo_interno": (item.internal_id) ? item.internal_id:"",
                        "descripcion": item.description,
                        "codigo_producto_sunat": "",
                        "unidad_de_medida": item.unit_type_id,
                        "cantidad": item.cantidad,
                        "valor_unitario": unit_value,
                        "codigo_tipo_precio": "01",
                        "precio_unitario": sale_unit_price,
                        "codigo_tipo_afectacion_igv": "20",
                        "total_base_igv": total_val,
                        "porcentaje_igv": percentage_igv,
                        "total_igv": 0,
                        "total_impuestos": 0,
                        "total_valor_item": total_val,
                        "total_item": total,
                        "actualizar_descripcion": false,
                        "nombre_producto_pdf": nombre_producto_pdf
                    }
                }
            })

            if (this.deliveryZone && parseFloat(this.deliveryZone.price) > 0) {
                const delivery_price  = parseFloat(this.deliveryZone.price);
                const percentage_igv = 18;
                const unit_value   = delivery_price / (1 + percentage_igv / 100);
                const igv_val      = delivery_price - unit_value;
                rec.push({
                    "codigo_interno":              "DELIVERY-ECOM",
                    "descripcion":                 "Costo de Envío - " + this.deliveryZone.name,
                    "codigo_producto_sunat":       "",
                    "unidad_de_medida":            "ZZ",
                    "cantidad":                    1,
                    "valor_unitario":              parseFloat(unit_value.toFixed(6)),
                    "codigo_tipo_precio":          "01",
                    "precio_unitario":             delivery_price,
                    "codigo_tipo_afectacion_igv":  "10",
                    "total_base_igv":              parseFloat(unit_value.toFixed(2)),
                    "porcentaje_igv":              percentage_igv,
                    "total_igv":                   parseFloat(igv_val.toFixed(2)),
                    "total_impuestos":             parseFloat(igv_val.toFixed(2)),
                    "total_valor_item":            parseFloat(unit_value.toFixed(2)),
                    "total_item":                  delivery_price,
                    "actualizar_descripcion":      false,
                    "nombre_producto_pdf":         this.deliveryZone.name
                });
            }

            return rec
        },
        initForm() {
            this.errors = {}
            this.user = window.__ecommerce_config?.user || {};
            if(!this.user){
                return false
            }

            this.form_document = {
                "acciones": {
                    "enviar_email": true,
                    "formato_pdf": "a4"
                },
                "serie_documento": "",
                "numero_documento": "#",
                "fecha_de_emision": moment().format('YYYY-MM-DD'),
                "hora_de_emision": moment().format('HH:mm:ss'),
                "codigo_tipo_operacion": "0101",
                "codigo_tipo_documento": "03",
                "codigo_tipo_moneda": "PEN",
                "fecha_de_vencimiento": moment().format('YYYY-MM-DD'),
                "datos_del_cliente_o_receptor": {
                    "codigo_tipo_documento_identidad": "0",
                    "numero_documento": "0",
                    "apellidos_y_nombres_o_razon_social": this.user.name,
                    "codigo_pais": "PE",
                    "ubigeo": "150101",
                    "direccion": this.user.address,
                    "correo_electronico": this.user.email,
                    "telefono": this.user.telephone
                },
                "totales": {},
                "items": [],
            }

            this.form_contact.address =  this.user.address
            this.form_contact.telephone =  this.user.telephone

            this.optionDocument()
            // Aplicar defaults según configuración de documentos electrónicos
            this.applyDocumentDefaults()
        },
        // Establece el tipo de documento y los datos del cliente según la configuración de documentos electrónicos.
        // Si está desactivado: fuerza nota de venta (código 80) con los datos del usuario.
        // Si está activado: infiere el tipo según la longitud del número (8 dígitos=boleta, 11=factura).
        applyDocumentDefaults() {
            if (!this.enable_electronic_documents) {
                const userNumber = (this.user && this.user.number) ? String(this.user.number).trim() : '0';
                this.form_document.codigo_tipo_documento = '80';
                this.typeDocuments = '0';
                this.numberDocument = userNumber;
                if (this.form_document.datos_del_cliente_o_receptor) {
                    this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = '0';
                    this.form_document.datos_del_cliente_o_receptor.numero_documento = userNumber;
                }
                this.typeDocumentList = this.getIdentityDocumentTypes(['0']);
                return;
            }

            // Modo electrónico: inferir tipo según longitud del número del usuario
            if (!this.user || !this.user.number) return;
            const numStr = String(this.user.number).trim();

            if (numStr.length === 8) {
                // DNI → Boleta
                this.form_document.codigo_tipo_documento = '03';
                this.typeDocuments = '1';
                this.numberDocument = numStr;
                if (this.form_document.datos_del_cliente_o_receptor) {
                    this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = '1';
                    this.form_document.datos_del_cliente_o_receptor.numero_documento = numStr;
                }
                this.typeDocumentList = this.getIdentityDocumentTypes(['1']);
            } else if (numStr.length === 11) {
                // RUC → Factura
                this.form_document.codigo_tipo_documento = '01';
                this.typeDocuments = '6';
                this.numberDocument = numStr;
                if (this.form_document.datos_del_cliente_o_receptor) {
                    this.form_document.datos_del_cliente_o_receptor.codigo_tipo_documento_identidad = '6';
                    this.form_document.datos_del_cliente_o_receptor.numero_documento = numStr;
                }
                this.typeDocumentList = this.getIdentityDocumentTypes(['6']);
            }
        },
        deleteItem(id, index) {
            this.records.splice(index, 1)
            let array = localStorage.getItem('products_cart');
            array = JSON.parse(array);
            let indexFound = array.findIndex(x => x.id == id)
            array.splice(indexFound, 1);
            localStorage.setItem('products_cart', JSON.stringify(array));

            this.calculateSummary()
        },
        clearShoppingCart() {
            this.errors = {}
            this.records_old = this.records
            this.records = []
            localStorage.setItem('products_cart', JSON.stringify([]))

            this.summary = {
                subtotal: '0.0',
                tax: '0.0',
                total: '0.00',
                total_taxed: '0.0',
                total_value: '0.0',
                total_exonerated: '0.0',
                total_igv: '0.0'
            }
            this.payment_cash.amount = '0.00'
            location.reload()
        },
        calculateSummary() {
            let total_taxed = 0
            let total_value = 0
            let total_exonerated = 0
            let total_igv = 0
            let total = 0

            this.records.forEach(function (item) {
                let unit_price = item.sub_total
                let unit_value = unit_price
                let percentage_igv = 18

                if (item.sale_affectation_igv_type_id === '10') {
                    unit_value = item.sub_total / (1 + percentage_igv / 100)
                    total_taxed += parseFloat(unit_value)
                    total_igv += parseFloat(unit_price - unit_value)
                }
                if (item.sale_affectation_igv_type_id === '20') {
                    total_exonerated += parseFloat(unit_value)
                }

                total_value = total_taxed + total_exonerated
                total += parseFloat(unit_price)
            })

            this.summary.total_taxed = total_taxed.toFixed(2)
            this.summary.total_exonerated = total_exonerated.toFixed(2)
            this.summary.total_igv = total_igv.toFixed(2)
            this.summary.total_value = total_value.toFixed(2)

            let computedTotal = total;
            if (this.appliedCoupon && this.appliedCoupon.discount) {
                computedTotal = Math.max(0, computedTotal - parseFloat(this.appliedCoupon.discount));
            }

            let deliveryPrice = (this.deliveryZone && this.deliveryZone.price) ? parseFloat(this.deliveryZone.price) : 0;
            // Si el modo es recojo en tienda, no se cobra delivery
            if (this.isPickupMode) {
                deliveryPrice = 0;
            }
            computedTotal += deliveryPrice;
            let deliveryIgv = parseFloat((deliveryPrice / 1.18 * 0.18).toFixed(2));
            this.summary.delivery         = deliveryPrice.toFixed(2);
            this.summary.delivery_igv     = deliveryIgv.toFixed(2);
            this.summary.total            = computedTotal.toFixed(2)
            this.aux_totals               = Object.assign({}, this.summary)

            $("#total_amount").data('total', this.summary.total);

            this.form_document.codigo_tipo_documento = null
            this.optionDocument()
            // Re-aplicar defaults tras cada cálculo para mantener el tipo forzado
            this.applyDocumentDefaults()

            this.payment_cash.amount = this.summary.total;
        },
        saveContactDataUser() {
            let url_finally = window.__routes?.user_data || '/ecommerce/user/data';

            let payload = Object.assign({}, this.form_contact, {
                department_id:    this.selectedDepartment   || null,
                province_id:      this.selectedProvince     || null,
                district_id:      this.selectedDistrict     || null,
                delivery_address: this.addressModal.address || this.form_contact.address || null,
            });

            axios.post(url_finally, payload, this.getHeaderConfig())
                .then(response => {
                   console.log(response.data)
                })
                .catch(error => {

                });
        },
        clickSendWhatsapp(order_id) {
            window.open(`https://wa.me/51${this.phone_whatsapp}?text=Se ha generado un nuevo pedido con código nro. ${order_id}`, '_blank');
        },
        onAddressInput() {
            console.log('Input detectado:', this.addressModal.address);
        },
        selectAddressSuggestion(suggestion) {
            this.addressModal.address = suggestion.description;
            this.addressSuggestions = [];
            console.log('Dirección seleccionada:', this.addressModal.address);
        },
        fetchLocations() {
            return axios.get(window.__routes?.locations || '/locations/cascade')
                .then(response => {
                    this.departments = response.data;
                })
                .catch(error => {
                    console.error('Error fetching locations:', error);
                });
        },
        loadDefaultAddress() {
            const addr = this.userDefaultAddress;
            if (!addr || !addr.address) return;

            this.addressModal.address = addr.address;

            if (!addr.department_id) return;

            const dept = this.departments.find(d => d.value === addr.department_id);
            if (!dept) return;

            this.selectedDepartment = dept.value;
            this.provinces = dept.children || [];
            this.selectedProvince = '';
            this.districts = [];
            this.selectedDistrict = '';

            if (!addr.province_id) return;

            this.$nextTick(() => {
                const prov = this.provinces.find(p => p.value === addr.province_id);
                if (!prov) return;

                this.selectedProvince = prov.value;
                this.districts = prov.children || [];
                this.selectedDistrict = '';

                if (!addr.district_id) return;

                this.$nextTick(() => {
                    setTimeout(() => {
                        const dist = this.districts.find(d => d.value === addr.district_id);
                        if (!dist) return;

                        this.selectedDistrict = dist.value;
                        this.checkDeliveryZone();
                    }, 100);
                });
            });
        },
        updateProvinces() {
            const department = this.departments.find(dep => dep.value === this.selectedDepartment);
            this.provinces = department ? department.children : [];
            this.selectedProvince = '';
            this.districts = [];
            this.selectedDistrict = '';
            this.deliveryZone = null;
            this.availableDeliveryZones = [];
            this.deliveryMessage = '';
            this.calculateSummary();
        },
        updateDistricts() {
            const province = this.provinces.find(prov => prov.value === this.selectedProvince);
            this.districts = province ? province.children : [];
            this.selectedDistrict = '';
            this.deliveryZone = null;
            this.availableDeliveryZones = [];
            this.deliveryMessage = '';
            this.calculateSummary();
        },
        async checkDeliveryZone() {
            if (!this.selectedDepartment) {
                this.deliveryZone           = null;
                this.availableDeliveryZones = [];
                this.deliveryMessage        = '';
                this.calculateSummary();
                return;
            }

            try {
                const params = {
                    department: this.selectedDepartment,
                    province:   this.selectedProvince   || undefined,
                    district:   this.selectedDistrict   || undefined,
                };
                const res = await axios.get('/ecommerce/delivery-zones/check', { params });

                if (res.data.found && res.data.zones && res.data.zones.length > 0) {
                    this.availableDeliveryZones = res.data.zones;
                    this.deliveryMessage        = '';
                    // Auto-seleccionar la primera zona disponible
                    this.deliveryZone = res.data.zones[0];
                } else if (res.data.configured === false) {
                    this.deliveryZone           = null;
                    this.availableDeliveryZones = [];
                    this.deliveryMessage        = '';
                } else {
                    this.deliveryZone           = null;
                    this.availableDeliveryZones = [];
                    this.deliveryMessage        = res.data.message || 'Lo sentimos, no contamos con delivery en tu zona por el momento.';
                }
            } catch (e) {
                this.deliveryZone           = null;
                this.availableDeliveryZones = [];
                this.deliveryMessage        = '';
            }

            this.calculateSummary();
        },
        // Cambia la zona de delivery seleccionada y recalcula los totales
        selectDeliveryZone(zone) {
            this.deliveryZone = zone;
            this.calculateSummary();
        },
        // Selecciona una sucursal de recojo en tienda
        selectPickupBranch(branch) {
            this.selectedPickupBranch = branch;
            this.calculateSummary();
        },
        // Alterna el modo de recojo en tienda y resetea la selección contraria
        togglePickupMode() {
            this.isPickupMode = !this.isPickupMode;
            if (this.isPickupMode) {
                // Al activar recojo: limpiar zona de delivery
                this.deliveryZone = null;
                this.availableDeliveryZones = [];
                this.deliveryMessage = '';
                // Auto-seleccionar la primera sucursal si hay disponibles
                if (this.pickupBranches.length > 0) {
                    this.selectedPickupBranch = this.pickupBranches[0];
                }
            } else {
                // Al desactivar recojo: limpiar sucursal y disparar búsqueda de zona
                this.selectedPickupBranch = null;
                this.checkDeliveryZone();
            }
            this.calculateSummary();
        },
        async applyCoupon() {
            if (!this.couponField || this.couponLoading) return;
            this.couponLoading = true;
            this.couponMessage = null;

            try {
                const payload = { code: this.couponField, order_total: this.summary.total };
                const res = await axios.post('/ecommerce/validate-coupon', payload, this.getHeaderConfig());
                if (res.data && res.data.success) {
                    const d = res.data.data;
                    this.appliedCoupon = {
                        id: d.id,
                        code: d.code,
                        discount: parseFloat(d.discount),
                        free_shipping: d.free_shipping
                    };
                    if (typeof d.new_total !== 'undefined') {
                        this.summary.total = parseFloat(d.new_total).toFixed(2);
                        this.payment_cash.amount = this.summary.total;
                    }
                    this.couponMessage = null;
                } else {
                    this.couponMessage = (res.data && res.data.message) ? res.data.message : 'cupon no valido';
                }
            } catch (err) {
                if (err.response && err.response.data && err.response.data.message) {
                    this.couponMessage = err.response.data.message;
                } else {
                    this.couponMessage = 'cupon no valido';
                }
            } finally {
                this.couponLoading = false;
            }
        },

        removeCoupon() {
            this.appliedCoupon = null;
            this.couponField = '';
            this.couponMessage = null;
            this.calculateSummary();
        },
    },
})

// Exponer la instancia globalmente para que los scripts inline del blade puedan accederla
window.app_cart = app_cart;
