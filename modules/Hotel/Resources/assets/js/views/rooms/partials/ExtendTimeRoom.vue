<template>
  <div>

    <el-dialog
      :title="title"
      :visible="visible"
      @close="closeDialog"
      @open="create"
      width="60%"
    >
      <form autocomplete="off" @submit.prevent="onSubmit">
        <div class="form-body">
          
          <!-- Mensaje con características actuales de la habitación -->
          <div class="alert alert-info mb-4" v-if="room">
            <h6 class="mb-2">
              <i class="el-icon-info"></i>
              Características Actuales de la Habitación
            </h6>
            <div class="row">
              <div class="col-md-6">
                <strong>Habitación:</strong> {{ room.description || room.name || 'N/A' }}<br>
                <strong>Tipo:</strong> {{ room.category?.description || room.room_type || 'N/A' }}<br>
                <strong>Tarifa actual:</strong> {{ getRateTypeLabel(room.rent?.rate_type) || 'Estándar' }}<br>
                <strong>Precio por noche:</strong> S/ {{ getCurrentRoomPrice().toFixed(2) }}
              </div>
              <div class="col-md-6">
                <strong>Entrada:</strong> {{ room.rent?.input_date || 'N/A' }} {{ room.rent?.input_time || '' }}<br>
                <strong>Salida actual:</strong> {{ room.rent?.output_date || 'N/A' }} {{ room.rent?.output_time || '' }}<br>
                <strong>Noches actuales:</strong> {{ room.rent?.duration || 0 }}<br>
                <strong>Total actual:</strong> S/ {{ (getCurrentRoomPrice() * (room.rent?.duration || 0)).toFixed(2) }}
              </div>
            </div>
          </div>

          <!-- Primera Fila: Cantidad noches/horas/meses, Fecha salida, Hora salida -->
          <div class="row">
            <div class="col-12 col-md-4 form-group" :class="{ 'has-danger': errors.duration }">
              <label class="control-label" for="duration">Cant. {{ periodUnitLabel }}</label>
              <el-input-number
                v-model="form.duration"
                controls-position="right"
                @change="updateDuration"
                :min="1"
                style="width: 100%"
              ></el-input-number>
              <small class="form-control-feedback" v-if="errors.duration" v-text="errors.duration[0]"></small>
            </div>
            
            <div class="col-12 col-md-4 form-group" :class="{ 'has-danger': errors.output_date }">
              <label class="control-label">Fecha de salida</label>
              <el-date-picker
                v-model="form.output_date"
                type="date"
                placeholder="Seleccione una fecha"
                value-format="yyyy-MM-dd"
                format="dd-MM-yyyy"
                readonly
                style="width: 100%"
              ></el-date-picker>
              <small class="form-control-feedback" v-if="errors.output_date" v-text="errors.output_date[0]"></small>
            </div>
            
            <div class="col-12 col-md-4 form-group" :class="{ 'has-danger': errors.output_time }">
              <label class="control-label">Hora de salida</label>
              <el-input v-model="form.output_time" placeholder="HH:MM"></el-input>
              <small class="form-control-feedback" v-if="errors.output_time" v-text="errors.output_time[0]"></small>
            </div>
          </div>

          <!-- Segunda Fila: Tarifa, Precio por día, Total extensión -->
          <div class="row">
            <div class="col-12 col-md-4 form-group">
              <label class="control-label">Tarifa</label>
              <el-select v-model="selectedRateId" @change="onRateChange" style="width: 100%" placeholder="Seleccionar tarifa">
                <el-option label="Precio actual" value="current"></el-option>
                <el-option 
                  v-for="rate in (room.rates || [])" 
                  :key="rate.id"
                  :label="`${rate.description || rate.name || getRateTypeLabel(rate.type) || 'Tarifa'} - S/ ${parseFloat(rate.price || 0).toFixed(2)}`"
                  :value="rate.id"
                ></el-option>
              </el-select>
            </div>
            
            <div class="col-12 col-md-4 form-group" :class="{ 'has-danger': errors.price_per_day }">
              <label class="control-label">Precio por {{ periodSingularLabel }}</label>
              <el-input
                v-model="form.price_per_day"
                type="number"
                placeholder="0.00"
                @input="calculateTotal"
                :readonly="selectedRateId && selectedRateId !== 'current'"
              >
                <template slot="prepend">S/</template>
              </el-input>
              <small class="form-control-feedback" v-if="errors.price_per_day" v-text="errors.price_per_day[0]"></small>
            </div>
            
            <div class="col-12 col-md-4 form-group">
              <label class="control-label">Total extensión</label>
              <el-input
                :value="extensionTotal"
                type="number"
                readonly
              >
                <template slot="prepend">S/</template>
              </el-input>
            </div>
          </div>

          <!-- Tarifa final: total que pagará el huésped al cierre, considerando
               consumo previo + extensión - pagos ya hechos. -->
          <div class="row">
            <div class="col-12">
              <div class="alert alert-warning mb-2 d-flex justify-content-between align-items-center">
                <div>
                  <strong>Tarifa final tras la extensión:</strong>
                  <small class="d-block text-muted">
                    Consumo previo (S/ {{ getCurrentRoomCons().toFixed(2) }})
                    + extensión (S/ {{ extensionTotal.toFixed(2) }})
                    − pagado (S/ {{ savedPaymentsTotal.toFixed(2) }})
                  </small>
                </div>
                <h4 class="m-0 text-danger">
                  S/ {{ finalRateAfterExtension.toFixed(2) }}
                </h4>
              </div>
            </div>
          </div>

          <!-- Sección de pago -->
          <div class="row mt-4">
            <div class="col-12">
                <div class="form-group">
                    <el-checkbox v-model="form.include_payment" @change="onIncludePaymentChange">
                        Incluir pago por extensión
                    </el-checkbox>
                </div>
            </div>
            <template v-if="form.include_payment">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="control-label">Monto a pagar</label>
                        <el-input
                            v-model="form.payment_amount"
                            type="number"
                            placeholder="0.00"
                        >
                            <template slot="prepend">S/</template>
                        </el-input>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="control-label">Método de pago</label>
                        <el-select v-model="form.payment_method" style="width: 100%">
                            <el-option label="Efectivo" value="cash"></el-option>
                            <el-option label="Tarjeta de Crédito" value="credit_card"></el-option>
                            <el-option label="Tarjeta de Débito" value="debit_card"></el-option>
                            <el-option label="Transferencia" value="transfer"></el-option>
                            <el-option label="Yape/Plin" value="yape_plin"></el-option>
                        </el-select>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label class="control-label">Referencia</label>
                        <el-input
                            v-model="form.payment_reference"
                            placeholder="Opcional"
                        ></el-input>
                    </div>
                </div>
            </template>
            
            <!-- Tabla de resumen de pago -->
            <div class="col-12" v-if="form.include_payment">
              <hr>
              <h5 class="mb-3">
                <i class="el-icon-money"></i>
                Resumen de Pago
              </h5>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <tbody>
                    <tr class="table-active">
                      <td width="60%"><strong>Total Consumo:</strong></td>
                      <td width="40%" class="text-right"><strong>S/ {{ totalConsumption.toFixed(2) }}</strong></td>
                    </tr>
                    <tr>
                      <td><strong>Total Pagado:</strong></td>
                      <td class="text-right text-success"><strong>S/ {{ totalPaid.toFixed(2) }}</strong></td>
                    </tr>
                    <tr class="table-danger">
                      <td><strong>Total a Pagar:</strong></td>
                      <td class="text-right text-danger"><strong>S/ {{ totalToPay.toFixed(2) }}</strong></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
        </div>

          <div class="alert alert-info mt-3">
            <strong>Información:</strong> Al extender la estadía, se generarán cargos adicionales según el precio por día configurado.
          </div>

          <div class="d-flex justify-content-end text-center ml-auto">
            <div class="mx-1">
              <el-button class="btn-block second-buton btn" @click="closeDialog" style="min-width: 78px;">Cancelar</el-button>
            </div>
            <div class="mx-1">
              <el-button
                native-type="submit"
                type="primary"
                class="btn-block btn"
                :disabled="loading"
                style="min-width: 78px;"
                >Guardar</el-button
              >
            </div>            
          </div>
        </div>
      </form>
    </el-dialog>
  </div>
</template>

<script>
export default {
  props: ['room','visible'],
  data() {
    return {
      form: {
        output_date: moment().format("YYYY-MM-DD"),
        output_time: moment().format("HH:mm:ss"),
        duration: 1,
        price_per_day: 0,
        include_payment: false,
        payment_amount: 0,
        payment_method: 'cash',
        payment_reference: ''
      },
      showDialog: false,
      title: 'Extender Estadía',
      errors: {},
      loading: false,
      item: {},
      item_debt: {},
      selectedRateId: null,
      selectedRate: null,
      savedPayments: [],
    }
  },
  computed: {
    periodType() {
      return this.room?.rent?.rental_period_type || 'day';
    },
    periodUnitLabel() {
      if (this.periodType === 'hour') return 'horas';
      if (this.periodType === 'month') return 'meses';
      return 'noches';
    },
    periodSingularLabel() {
      if (this.periodType === 'hour') return 'hora';
      if (this.periodType === 'month') return 'mes';
      return 'día';
    },
    extensionTotal() {
      // Calcular solo los períodos adicionales (form.duration son períodos adicionales)
      const total = (parseFloat(this.form.price_per_day) || 0) * Math.max(0, this.form.duration || 0);
      return total;
    },
    totalConsumption() {
      // Consumo total tras la extensión = consumo previo (items actuales sin
      // mutar, excluyendo pseudo-items PAY) + la extensión que se está creando
      // + arrears. getCurrentRoomCons() ya NO incluye la extensión porque el
      // item original ya no se muta, por eso la sumamos aquí explícitamente.
      const previo = this.getCurrentRoomCons();
      const arrears = parseFloat(this.room?.rent?.arrears) || 0;
      return previo + this.extensionTotal + arrears;
    },
    savedPaymentsTotal() {
      // Suma neta (positivos - devoluciones); los amounts ya vienen con signo
      // desde loadExistingPayments.
      return (this.savedPayments || []).reduce((total, payment) => {
        return total + (parseFloat(payment.amount) || 0);
      }, 0);
    },
    totalPaid() {
      // Total pagado = pagos ya hechos (neto) + monto que el usuario ingresa para pagar
      const currentPayment = this.form.include_payment ? (parseFloat(this.form.payment_amount) || 0) : 0;
      return this.savedPaymentsTotal + currentPayment;
    },
    totalToPay() {
      // Total a pagar = total consumo - total pagado
      return this.totalConsumption - this.totalPaid;
    },
    finalRateAfterExtension() {
      // Tarifa final = consumo previo + extensión - pagos hechos
      const consumed = parseFloat(this.getCurrentRoomCons()) || 0;
      const ext = parseFloat(this.extensionTotal) || 0;
      const paid = parseFloat(this.savedPaymentsTotal) || 0;
      return Math.max(0, consumed + ext - paid);
    }
  },
  methods: {
    getCurrentRoomPrice() {
      // Usar la misma lógica que getItem() para obtener el precio actual
      if (this.room && this.room.rent && this.room.rent.items) {
        // Buscar el item principal (generalmente el primero o el que corresponde a la habitación)
        const mainItem = this.room.rent.items.find(item => 
          item && item.item && item.item.description && 
          (item.item.description.toLowerCase().includes('habitación') || 
           item.item.description.toLowerCase().includes('room') ||
           item.item.description.toLowerCase().includes('alojamiento'))
        ) || this.room.rent.items[0];
        
        if (mainItem && mainItem.item) {
          return parseFloat(mainItem.item.unit_price) || parseFloat(mainItem.item.unit_value) || 0;
        }
      }
      
      // Fallbacks
      return parseFloat(this.room.rent?.unit_price) || parseFloat(this.room.rent?.price_per_day) || 0;
    },
    getCurrentRoomDebt() {
      // Calcular deuda actual usando la misma lógica que reception.vue
      if (!this.room || !this.room.rent || !this.room.rent.items) {
        console.log('No hay room, rent o items, retornando 0');
        return 0;
      }
      
      console.log('Items disponibles:', this.room.rent.items);
      const debtItems = this.room.rent.items.filter(item => item && item.payment_status === 'DEBT');
      console.log('Items con deuda:', debtItems);
      
      const total = debtItems.reduce((sum, item) => {
        const itemTotal = parseFloat(item.item?.total) || 0;
        console.log(`Item ${item.id}: payment_status=${item.payment_status}, total=${itemTotal}`);
        return sum + itemTotal;
      }, 0);
      
      console.log('Deuda total calculada:', total);
      return total;
    },
    getCurrentRoomCons() {
      // Total de consumo: suma de items del rent excluyendo los pseudo-items
      // de tipo PAY (pagos), igual que Checkout.vue (onCalculatePaidAndDebts).
      if (!this.room || !this.room.rent || !Array.isArray(this.room.rent.items)) {
        return 0;
      }

      return this.room.rent.items
        .filter(item => item && item.type !== 'PAY')
        .reduce((sum, item) => sum + (parseFloat(item.item?.total) || 0), 0);
    },
    initForm(){
      this.form = {
        output_date: moment().format("YYYY-MM-DD"),
        output_time: moment().format("HH:mm:ss"),
        duration: 1,
        price_per_day: 0,
        include_payment: false,
        payment_amount: 0,
        payment_method: 'cash',
        payment_reference: '',
        item: {}
      }
      this.item = {}
      this.selectedRateId = null;
      this.selectedRate = null;
    },
    onRateChange(rateId) {
      if (rateId === 'current') {
        this.selectedRateId = 'current';
        this.selectedRate = null;
        // Mantener el precio actual
      } else {
        const rate = this.room.rates.find(r => r.id === rateId);
        if (rate) {
          this.selectedRateId = rate.id;
          this.selectedRate = rate;
          this.form.price_per_day = parseFloat(rate.price);
        }
      }
      // Propagar el nuevo precio al item (clon) que se enviará al backend.
      this.updateItemWithNewPrice();
    },
    getRateTypeLabel(type) {
      const types = {
        'hourly': 'Por Hora',
        'daily': 'Diaria', 
        'weekly': 'Semanal',
        'monthly': 'Mensual',
        'standard': 'Estándar',
        'weekend': 'Fin de Semana',
        'holiday': 'Festivo'
      };
      return types[type] || type;
    },
    closeDialog() {
      this.initForm()
      this.$emit("onRefresh")
      this.$emit("update:visible", false)
    },
    async create() {
      // Debug: Ver estructura del room
      console.log('=== CREATE EXTENSION ===');
      console.log('Método create() llamado correctamente');
      console.log('Room completo:', this.room);
      console.log('Rent data:', this.room.rent);
      
      // Inicializar formulario con datos actuales
      this.form = {
        output_date: this.room.rent?.output_date || moment().format("YYYY-MM-DD"),
        output_time: this.room.rent?.output_time || moment().format("HH:mm:ss"),
        duration: 1, // Días adicionales (no total)
        price_per_day: 0,
        include_payment: false,
        payment_amount: 0,
        payment_method: 'cash',
        payment_reference: ''
      }

      // Establecer tarifa seleccionada por defecto
      this.setDefaultRate();

      // Obtener precio actual de la habitación
      await this.getItem();
      
      // Establecer precio por día si está disponible
      if(this.item && this.item.item) {
        this.form.price_per_day = parseFloat(this.item.item.unit_price) || 0;
      } else if(this.room.rent && this.room.rent.price_per_day) {
        // Usar precio del rent como fallback
        this.form.price_per_day = parseFloat(this.room.rent.price_per_day) || 0;
      }
      
      // Calcular la extensión inicial (1 día adicional)
      this.updateDuration();
      
      // Cargar pagos existentes de la habitación
      await this.loadExistingPayments();
      
      console.log('Extensión inicializada correctamente');
      console.log('========================');
    },
    setDefaultRate() {
      // Buscar la tarifa que coincide con la tarifa actual de la reserva
      if (this.room.rent && this.room.rates) {
        const currentRateType = this.room.rent.rate_type;
        const currentPrice = parseFloat(this.getCurrentRoomPrice());
        
        console.log('Buscando tarifa actual:', { rateType: currentRateType, price: currentPrice });
        
        // Buscar tarifa exacta por tipo y precio
        const matchingRate = this.room.rates.find(rate => {
          const ratePrice = parseFloat(rate.price);
          const rateType = rate.type;
          
          // Prioridad 1: Coincide tipo y precio
          if (rateType === currentRateType && Math.abs(ratePrice - currentPrice) < 0.01) {
            console.log('Tarifa encontrada por tipo y precio:', rate);
            return true;
          }
          
          // Prioridad 2: Coincide solo el precio (si no hay tipo)
          if (!currentRateType && Math.abs(ratePrice - currentPrice) < 0.01) {
            console.log('Tarifa encontrada por precio:', rate);
            return true;
          }
          
          return false;
        });
        
        if (matchingRate) {
          this.selectedRateId = matchingRate.id;
          this.selectedRate = matchingRate;
          console.log('Tarifa seleccionada por defecto:', matchingRate);
        } else {
          // Si no encuentra coincidencia, usar "Precio actual"
          this.selectedRateId = 'current';
          this.selectedRate = null;
          console.log('Usando precio actual por defecto');
        }
      } else {
        // Si no hay tarifas, usar "Precio actual"
        this.selectedRateId = 'current';
        this.selectedRate = null;
      }
    },
    updateDuration() {
      // Calcular nueva fecha de salida según rental_period_type
      const period = this.periodType;
      const currentOutputDateTime = moment(
        `${this.room.rent.output_date} ${this.room.rent.output_time || '12:00'}`,
        'YYYY-MM-DD HH:mm'
      );
      const additional = this.form.duration || 0;
      const unit = period === 'hour' ? 'hours' : (period === 'month' ? 'months' : 'days');
      const newDateTime = currentOutputDateTime.clone().add(additional, unit);

      this.form.output_date = newDateTime.format('YYYY-MM-DD');
      this.form.output_time = newDateTime.format('HH:mm');

      // Actualizar el item (clon) con la nueva duración. No volvemos a llamar a
      // getItem() aquí: reclonaría desde room.rent.items y reiniciaría
      // price_per_day, descartando la tarifa que el usuario haya seleccionado.
      this.updateItemWithNewDuration();
    },
    updateItemWithNewDuration() {
      // Calcular duración total (actual + adicionales)
      const currentDuration = this.room.rent?.duration || 0;
      const additionalDays = this.form.duration || 0;
      const totalDuration = currentDuration + additionalDays;
      
      console.log('Actualizando item con duración total:', totalDuration);
      
      // Si ya hay un item configurado, actualizarlo
      if (this.form.item && this.form.item.item) {
        const unitPrice = parseFloat(this.form.price_per_day) || 0;
        const total = totalDuration * unitPrice;
        
        // Actualizar quantity y total en el item
        this.form.item.item.quantity = totalDuration;
        this.form.item.item.total = total;
        this.form.item.item.unit_price = unitPrice;
        
        console.log('Item actualizado:', {
          quantity: this.form.item.item.quantity,
          unit_price: this.form.item.item.unit_price,
          total: this.form.item.item.total
        });
      }
    },
    calculateTotal() {
      // Este método se llama cuando cambia el precio por día
      // Actualizar el item con el nuevo precio
      this.updateItemWithNewPrice();
    },
    updateItemWithNewPrice() {
      // Calcular duración total (actual + adicionales)
      const currentDuration = this.room.rent?.duration || 0;
      const additionalDays = this.form.duration || 0;
      const totalDuration = currentDuration + additionalDays;
      
      console.log('Actualizando item con nuevo precio:', this.form.price_per_day);
      
      // Si ya hay un item configurado, actualizarlo
      if (this.form.item && this.form.item.item) {
        const unitPrice = parseFloat(this.form.price_per_day) || 0;
        const total = totalDuration * unitPrice;
        
        // Actualizar unit_price y total en el item
        this.form.item.item.unit_price = unitPrice;
        this.form.item.item.total = total;
        
        console.log('Item actualizado con nuevo precio:', {
          quantity: this.form.item.item.quantity,
          unit_price: this.form.item.item.unit_price,
          total: this.form.item.item.total
        });
      }
    },
    onIncludePaymentChange(value) {
      if (value) {
        // Si se incluye pago, establecer el monto por defecto al total de la extensión
        this.form.payment_amount = this.extensionTotal;
      } else {
        // Si no se incluye pago, limpiar los campos
        this.form.payment_amount = 0;
        this.form.payment_method = 'cash';
        this.form.payment_reference = '';
      }
    },
    getItem() {
      console.log('Obteniendo item para rent ID:', this.room.rent?.id);
      
      // Usar la misma lógica que reception.vue para obtener el precio
      if (this.room && this.room.rent && this.room.rent.items) {
        console.log('Usando room.rent.items directamente');
        console.log('Items:', this.room.rent.items);
        
        // Buscar el item principal (generalmente el primero o el que corresponde a la habitación)
        const mainItem = this.room.rent.items.find(item => 
          item && item.item && item.item.description && 
          (item.item.description.toLowerCase().includes('habitación') || 
           item.item.description.toLowerCase().includes('room') ||
           item.item.description.toLowerCase().includes('alojamiento'))
        ) || this.room.rent.items[0];
        
        if (mainItem && mainItem.item) {
          const precio = parseFloat(mainItem.item.unit_price) || parseFloat(mainItem.item.unit_value) || 0;
          console.log('Precio encontrado en room.rent.items:', precio);
          console.log('Item usado:', mainItem);
          this.form.price_per_day = precio;

          // IMPORTANTE: Clonar el item para el envío. NUNCA asignar la referencia
          // directa de room.rent.items, porque updateItemWithNewDuration /
          // updateItemWithNewPrice mutan item.total para reflejar la extensión y
          // eso corrompería el "consumo previo" (getCurrentRoomCons lee de
          // room.rent.items) provocando un doble conteo en la tarifa final.
          const clone = JSON.parse(JSON.stringify(mainItem));
          this.item = clone;
          this.item_debt = clone;
          this.form.item = clone;

          console.log('Item configurado para envío:', this.form.item);
          return;
        }
      }
      
      // Fallback: hacer la llamada HTTP como antes
      if (this.room && this.room.rent && this.room.rent.id) {
        this.$http
            .get(`/hotels/reception/${this.room.rent.id}/rent/get-item`)
            .then(response => {
              console.log('Respuesta completa de API:', response.data);
              console.log('Item data:', response.data.data.item);
              console.log('Item_debt data:', response.data.data.item_debt);
              
              this.item = response.data.data.item
              this.item_debt = response.data.data.item_debt
              
              // Intentar obtener precio de múltiples fuentes
              let precio = 0;
              if(this.item && this.item.item && this.item.item.unit_price) {
                precio = parseFloat(this.item.item.unit_price);
                console.log('Precio encontrado en item.item.unit_price:', precio);
              } else if(this.item_debt && this.item_debt.item && this.item_debt.item.unit_price) {
                precio = parseFloat(this.item_debt.item.unit_price);
                console.log('Precio encontrado en item_debt.item.unit_price:', precio);
              } else if(this.room.rent && this.room.rent.unit_price) {
                precio = parseFloat(this.room.rent.unit_price);
                console.log('Precio encontrado en room.rent.unit_price:', precio);
              } else if(this.room.rent && this.room.rent.price_per_day) {
                precio = parseFloat(this.room.rent.price_per_day);
                console.log('Precio encontrado en room.rent.price_per_day:', precio);
              }
              
              this.form.price_per_day = precio;
              console.log('Precio final establecido:', this.form.price_per_day);
              
              // IMPORTANTE: Configurar el item para el envío
              if(this.item_debt && this.item_debt.payment_status=='DEBT'){
                this.changeJsonItem()
              }else{
                this.addJsonItem()
              }
              
              console.log('Item final configurado:', this.form.item);
              
            })
            .catch(error => {
              console.error('Error obteniendo item:', error);
            });
      }
    },
    changeJsonItem() {

      // Calcular duración total (actual + adicionales)
      const currentDuration = this.room.rent?.duration || 0;
      const additionalDays = this.form.duration || 0;
      const totalDuration = currentDuration + additionalDays;
      
      console.log('Duración actual:', currentDuration);
      console.log('Días adicionales:', additionalDays);
      console.log('Duración total:', totalDuration);
      
      // NOTA: Ya no restamos las noches actuales cuando está pagado
      // porque queremos el TOTAL de noches, no solo las adicionales
      
      // Validar que item_debt y item existan antes de acceder
      if (!this.item_debt || !this.item_debt.item) {
        console.error('item_debt o item_debt.item es null');
        return;
      }

      let percentage_igv = this.item_debt.item.percentage_igv
      let unit_price = this.item_debt.item.unit_price
      let total = totalDuration * unit_price
      let total_base_igv = total / (1 + (percentage_igv / 100))
      let total_igv = total - total_base_igv

      this.item_debt.item.quantity = totalDuration
      this.item_debt.item.unit_value = _.round(unit_price, 2)
      this.item_debt.item.input_unit_price_value = _.round(unit_price, 2)
      this.item_debt.item.total = _.round(total, 2)
      this.item_debt.item.total_base_igv = _.round(total_base_igv, 2)
      this.item_debt.item.total_value = _.round(total_base_igv, 2)
      this.item_debt.item.total_taxes = _.round(total_igv, 2)
      this.item_debt.item.total_igv = _.round(total_igv, 2)

      this.item_debt.item.total_value_without_rounding = total_base_igv
      this.item_debt.item.total_base_igv_without_rounding = total_base_igv
      this.item_debt.item.total_igv_without_rounding = total_igv
      this.item_debt.item.total_taxes_without_rounding = total_igv
      this.item_debt.item.total_without_rounding = total

      this.form.item = this.item_debt
      
      console.log('Total calculado:', total);
      console.log('Cantidad final:', this.item_debt.item.quantity);
    },
    addJsonItem() {

      // Calcular duración total (actual + adicionales)
      const currentDuration = this.room.rent?.duration || 0;
      const additionalDays = this.form.duration || 0;
      let totalDuration = currentDuration + additionalDays;
      
      console.log('Duración actual:', currentDuration);
      console.log('Días adicionales:', additionalDays);
      console.log('Duración total:', totalDuration);
      
      // Validar que item existe antes de acceder
      if (!this.item || !this.item.item) {
        console.error('item o item.item es null');
        return;
      }

      // NOTA: Ya no restamos las noches actuales cuando está pagado
      // porque queremos el TOTAL de noches, no solo las adicionales

      let percentage_igv = this.item.item.percentage_igv
      let unit_price = this.item.item.unit_price
      let total = totalDuration * unit_price
      let total_base_igv = total / (1 + (percentage_igv / 100))
      let total_igv = total - total_base_igv

      this.item.item.quantity = totalDuration
      this.item.item.unit_value = _.round(unit_price, 2)
      this.item.item.input_unit_price_value = _.round(unit_price, 2)
      this.item.item.total = _.round(total, 2)
      this.item.item.total_base_igv = _.round(total_base_igv, 2)
      this.item.item.total_value = _.round(total_base_igv, 2)
      this.item.item.total_taxes = _.round(total_igv, 2)
      this.item.item.total_igv = _.round(total_igv, 2)

      this.item.item.total_value_without_rounding = total_base_igv
      this.item.item.total_base_igv_without_rounding = total_base_igv
      this.item.item.total_igv_without_rounding = total_igv
      this.item.item.total_taxes_without_rounding = total_igv
      this.item.item.total_without_rounding = total
      this.form.item = this.item
      
      console.log('Total calculado:', total);
      console.log('Cantidad final:', this.item.item.quantity);
    },
    onSubmit() {
      this.loading = true
      
      // Asegurarse de que el item esté configurado antes de enviar
      if (!this.form.item) {
        console.error('No hay item configurado para enviar');
        this.$message.error('Error: No se ha configurado el item correctamente');
        this.loading = false;
        return;
      }
      
      // Preparar datos para enviar con duración total
      const currentDuration = this.room.rent?.duration || 0;
      const additionalDays = this.form.duration || 0;
      const totalDuration = currentDuration + additionalDays;

      // Asegurar que el precio que verá el backend (request.item.item.unit_price)
      // sea exactamente el price_per_day mostrado en pantalla, así la tarifa
      // guardada coincide con la calculada en la vista.
      if (this.form.item && this.form.item.item) {
        this.form.item.item.unit_price = parseFloat(this.form.price_per_day) || 0;
      }

      const formData = {
        ...this.form,
        duration: totalDuration, // Enviar duración total, no solo adicionales
      };
      
      console.log('Duración actual:', currentDuration);
      console.log('Días adicionales:', additionalDays);
      console.log('Duración total a enviar:', totalDuration);
      console.log('Enviando formulario:', formData);
      console.log('Item a enviar:', formData.item);
      
      this.$http
        .post(`/hotels/reception/${this.room.rent.id}/rent/extend-time`, formData)
        .then((response) => {
          this.$message.success(response.data.message);
          this.closeDialog()
        })
        .catch((error) => {
          console.error('Error en onSubmit:', error);
          this.axiosError(error);
        })
        .finally(() => (this.loading = false));
    },
    getPaymentMethodFromId(methodId) {
      const methodMap = {
        '01': 'cash',
        '02': 'credit_card',
        '03': 'debit_card',
        '04': 'transfer',
        '05': 'yape_plin'
      };
      return methodMap[methodId] || 'cash';
    },
    async loadExistingPayments() {
      console.log('=== CARGANDO PAGOS EXISTENTES ===');
      this.savedPayments = [];

      if (!this.room || !this.room.rent || !this.room.rent.id) {
        console.log('No hay rent.id, abortando carga de pagos');
        return;
      }

      try {
        const response = await this.$http.get(
          `/hotels/reception/${this.room.rent.id}/rent/checkout-data`
        );

        console.log('Respuesta checkout-data:', response.data);

        if (!response.data || !response.data.success) {
          console.warn('checkout-data devolvió success=false');
          return;
        }

        const payments = response.data.data?.payments;

        if (!Array.isArray(payments)) {
          console.log('No hay array de pagos en la respuesta');
          return;
        }

        this.savedPayments = payments.map(payment => ({
          id: payment.id,
          amount: parseFloat(payment.payment) || 0,
          method: this.getPaymentMethodFromId(payment.payment_method_type_id),
          reference: payment.reference || null,
          created_at: payment.date_of_payment
        }));

        console.log('savedPayments cargados:', this.savedPayments);
        console.log('Total pagado:', this.savedPaymentsTotal);
      } catch (error) {
        console.error('Error al cargar pagos existentes:', error);
        this.savedPayments = [];
      }
    }
  },
}


</script>

<style scoped>
/* Estilos simples y limpios */
.form-body {
  background: white;
  border-radius: 8px;
  padding: 24px;
}

.el-dialog__body {
  padding: 0 20px 20px 20px;
}
</style>