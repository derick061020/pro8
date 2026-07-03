<template>
  <div class="cf-wrapper" :class="{ 'cf-embedded': embedded }">

    <!-- Encabezado -->
    <div class="cf-header">
      <!-- Logo / ícono -->
      <div class="cf-header-icon" v-if="showCompany && tables.company && tables.company.logo">
        <img :src="tables.company.logo" :alt="tables.company.trade_name || tables.company.name" style="width:148px; height:148px; object-fit:contain;" />
      </div>
      <div class="cf-header-icon" v-else>
        <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M19 4v16h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12" /><path d="M19 16h-12a2 2 0 0 0 -2 2" /><path d="M9 8h6" /></svg>
      </div>

      <!-- Nombre y datos de empresa -->
      <template v-if="showCompany && tables.company">
        <p class="cf-subtitle" style="margin-bottom:16px;">
          <span v-if="tables.company.trade_name && tables.company.name !== tables.company.trade_name">{{ tables.company.name }}</span>
          <span v-if="tables.company.trade_name && tables.company.name !== tables.company.trade_name && tables.company.ruc" style="margin:0 5px;">·</span>
          <span v-if="tables.company.ruc">RUC {{ tables.company.ruc }}</span>
        </p>
      </template>

      <h3 class="cf-title">Libro de Reclamaciones</h3>
      <p class="cf-subtitle">
        Conforme a lo establecido en el Código de Protección y Defensa del Consumidor
        (Ley N° 29571), usted tiene derecho a presentar su queja o reclamo.
      </p>
    </div>

    <!-- Código de reclamo previo -->
    <div v-if="showPrevCodeSection" class="cf-prev-code-card">
      <p class="cf-prev-code-hint" style="cursor:pointer; user-select:none; display:flex; align-items:center; justify-content:space-between; margin:0;" @click="showPrevCodeForm = !showPrevCodeForm">
        <span>¿Ya presentó un reclamo? Consulte su estado aqui</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" :style="showPrevCodeForm ? 'transform:rotate(180deg)' : ''">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </p>
      <div v-if="showPrevCodeForm" class="cf-inline" style="margin-top:12px;">
        <el-input v-model="form.previous_code" placeholder="Ej: RJL76KQ2WO37U" size="small"
          :disabled="lookingUp"></el-input>
        <button class="cf-btn cf-btn-primary" :disabled="lookingUp" @click="lookupPreviousCode">
          <span v-if="lookingUp" class="cf-spinner"></span>
          <span v-else>Verificar</span>
        </button>
        <button class="cf-btn cf-btn-outline" :disabled="lookingUp" @click="clearForm">Limpiar</button>
      </div>
    </div>

    <!-- Stepper custom -->
    <div v-if="!showPrevCodeForm" class="cf-stepper">
      <template v-if="previousClaim">
        <div class="cf-step-item" :class="{ active: activeStep === 0, done: activeStep > 0 }">
          <div class="cf-step-circle">
            <svg v-if="activeStep > 0" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
              fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span v-else>1</span>
          </div>
          <span class="cf-step-label">Reclamo previo</span>
        </div>
        <div class="cf-step-connector" :class="{ done: activeStep > 0 }"></div>
      </template>

      <div class="cf-step-item"
        :class="{ active: activeStep === (previousClaim ? 1 : 0), done: activeStep > (previousClaim ? 1 : 0) }">
        <div class="cf-step-circle">
          <svg v-if="activeStep > (previousClaim ? 1 : 0)" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          <span v-else>{{ previousClaim ? 2 : 1 }}</span>
        </div>
        <span class="cf-step-label">Datos</span>
      </div>
      <div class="cf-step-connector" :class="{ done: activeStep > (previousClaim ? 1 : 0) }"></div>

      <div class="cf-step-item"
        :class="{ active: activeStep === (previousClaim ? 2 : 1), done: activeStep > (previousClaim ? 2 : 1) }">
        <div class="cf-step-circle">
          <svg v-if="activeStep > (previousClaim ? 2 : 1)" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          <span v-else>{{ previousClaim ? 3 : 2 }}</span>
        </div>
        <span class="cf-step-label">Bien</span>
      </div>
      <div class="cf-step-connector" :class="{ done: activeStep > (previousClaim ? 2 : 1) }"></div>

      <div class="cf-step-item"
        :class="{ active: activeStep === (previousClaim ? 3 : 2), done: activeStep > (previousClaim ? 3 : 2) }">
        <div class="cf-step-circle">
          <svg v-if="activeStep > (previousClaim ? 3 : 2)" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
            stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          <span v-else>{{ previousClaim ? 4 : 3 }}</span>
        </div>
        <span class="cf-step-label">Detalle</span>
      </div>
    </div>

    <!-- ──────────── Paso 0: Código de reclamo previo ──────────── -->
    <div v-if="currentStep === 0" class="cf-step-content">
      <div v-if="previousClaim" class="cf-prev-result">

        <!-- Banner de estado: color dinámico según si está cerrado o en proceso -->
        <div
          class="cf-alert"
          :class="[
            previousClaim.is_closed ? 'cf-alert--closed' : 'cf-alert--open',
            previousClaim.status ? 'cf-alert--' + statusSlug(previousClaim.status.description) : ''
          ]"
          :style="previousClaim.status && previousClaim.status.color ? statusAlertStyle(previousClaim.status.color) : {}"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <div>
            <div>Reclamo <strong>{{ previousClaim.code }}</strong> — Estado: <strong>{{ previousClaim.status ? previousClaim.status.description : 'Sin estado' }}</strong></div>
            <div style="font-size:12px;margin-top:3px;opacity:.85">
              <span v-if="previousClaim.is_closed">Reclamo cerrado y resuelto. Puede vincular un nuevo reclamo a continuación.</span>
              <span v-else>Su reclamo está en proceso de atención. Puede realizar seguimiento con el código indicado.</span>
            </div>
          </div>
        </div>

        <!-- Resolución (solo cuando está cerrado) -->
        <div v-if="previousClaim.is_closed" class="cf-resolution-box" style="margin-top:12px">
          <p class="cf-resolution-label">Resolución de la empresa</p>
          <div v-if="previousClaim.resolution" class="cf-resolution-text cf-resolution-html" v-html="previousClaim.resolution"></div>
          <p v-else class="cf-resolution-text">Sin resolución registrada.</p>
        </div>

         <!-- Adjuntos de respuesta de la empresa -->
        <div v-if="previousClaim.response_attachments && previousClaim.response_attachments.length" class="cf-receipt-section" style="margin-top:12px">
          <p class="cf-receipt-section-title">Archivos adjuntos por la empresa</p>
          <ul class="cf-file-list">
            <li v-for="(url, idx) in previousClaim.response_attachments" :key="'resp-'+idx">
              <a :href="url" @click.prevent="downloadUrl(url)" :title="getFilenameFromUrl(url)" rel="noopener noreferrer">{{ getFilenameFromUrl(url) }}</a>
            </li>
          </ul>
        </div>

        <!-- Toggle detalles -->
        <div style="margin-top:12px; text-align:center;">
          <button class="cf-btn cf-btn-link" style="font-size:13px;" @click="showClaimDetails = !showClaimDetails">
            {{ showClaimDetails ? 'Ocultar detalles' : 'Ver todos los detalles del reclamo' }}
          </button>
        </div>

        <!-- Resumen compacto y detalles colapsables -->
        <div v-if="showClaimDetails">
          <div class="cf-prev-summary">
            <div class="cf-summary-row" v-if="previousClaim.name">
              <span class="cf-summary-label">Nombre</span>
              <span>{{ previousClaim.name }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.email">
              <span class="cf-summary-label">Email</span>
              <span>{{ maskEmail(previousClaim.email) }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.phone">
              <span class="cf-summary-label">Teléfono</span>
              <span>{{ previousClaim.phone }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.claim_type">
              <span class="cf-summary-label">Tipo</span>
              <span>{{ previousClaim.claim_type === 'queja' ? 'Queja' : 'Reclamo' }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.channel">
              <span class="cf-summary-label">Canal</span>
              <span>{{ previousClaim.channel }}</span>
            </div>
          </div>

          <!-- Bien/servicio reclamado -->
          <div v-if="previousClaim.asset_type || previousClaim.asset_description" class="cf-info-block" style="margin-top:12px">
            <p class="cf-info-block-title">Bien o servicio reclamado</p>
            <div class="cf-summary-row" v-if="previousClaim.asset_type">
              <span class="cf-summary-label">Tipo</span>
              <span>{{ previousClaim.asset_type === 'producto' ? 'Producto' : 'Servicio' }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.asset_description">
              <span class="cf-summary-label">Descripción</span>
              <span style="white-space:pre-wrap">{{ previousClaim.asset_description }}</span>
            </div>
          </div>

          <!-- Detalle del reclamo -->
          <div v-if="previousClaim.detail || previousClaim.expected_result" class="cf-info-block" style="margin-top:12px">
            <p class="cf-info-block-title">Detalle del reclamo</p>
            <div class="cf-summary-row" v-if="previousClaim.detail">
              <span class="cf-summary-label">Detalle</span>
              <span style="white-space:pre-wrap">{{ previousClaim.detail }}</span>
            </div>
            <div class="cf-summary-row" v-if="previousClaim.expected_result">
              <span class="cf-summary-label">Pedido</span>
              <span style="white-space:pre-wrap">{{ previousClaim.expected_result }}</span>
            </div>
          </div>

          <!-- Adjuntos del reclamante -->
          <div v-if="previousClaim.attachments && previousClaim.attachments.length" class="cf-receipt-section" style="margin-top:12px">
            <p class="cf-receipt-section-title">Archivos adjuntos del reclamante</p>
            <ul class="cf-file-list">
              <li v-for="(url, idx) in previousClaim.attachments" :key="'att-'+idx">
                <a :href="url" @click.prevent="downloadUrl(url)" :title="getFilenameFromUrl(url)" rel="noopener noreferrer">{{ getFilenameFromUrl(url) }}</a>
              </li>
            </ul>
          </div>

          <!-- Constancia PDF -->
          <div v-if="previousClaim.pdf_url" class="cf-receipt-section" style="margin-top:12px">
            <p class="cf-receipt-section-title">Constancia (PDF)</p>
            <ul class="cf-file-list">
              <li>
                <a :href="previousClaim.pdf_url" @click.prevent="downloadUrl(previousClaim.pdf_url)" :title="getFilenameFromUrl(previousClaim.pdf_url)" rel="noopener noreferrer">{{ getFilenameFromUrl(previousClaim.pdf_url) }}</a>
              </li>
            </ul>
          </div>

          <!-- Aviso cuando está en proceso (no se puede continuar) -->
          <div v-if="!previousClaim.is_closed">
          </div>
        </div>

      </div>
      <!-- Solo muestra Continuar cuando el reclamo previo fue cerrado -->
      <div v-if="previousClaim && previousClaim.is_closed" class="cf-step-actions">
         <p style="text-align: right;">
            <small>¿La resolución no responde a tu reclamo? Puedes enviarnos tu respuesta y revisaremos el caso nuevamente.</small>
            <button class="cf-btn cf-btn-link" @click="showPrevCodeSection = false; showPrevCodeForm = false; goNext()">Enviar respuesta
            </button>
        </p>
      </div>
    </div>

    <!-- ───────��──── Paso 1: Datos del reclamante ──────────── -->
    <div v-if="!showPrevCodeForm && currentStep === 1" class="cf-step-content">
      <p class="cf-section-title">Datos del reclamante</p>
      <el-form ref="formStep1" :model="form" :rules="rulesStep1" label-position="top" size="small">
        <el-row :gutter="16">
          <el-col :span="12" :xs="24">
            <el-form-item label="Tipo de documento" prop="identity_document_type">
              <el-select v-model="form.identity_document_type" placeholder="Seleccione" style="width:100%">
                <el-option v-for="item in tables.identity_document_types" :key="item.id" :label="item.description"
                  :value="item.id"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12" :xs="24">
            <el-form-item label="Número de documento" prop="identity_document_number">
              <el-input v-model="form.identity_document_number" placeholder="12345678"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Nombre completo" prop="name">
              <el-input v-model="form.name" placeholder="Nombres y apellidos"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="16" :xs="24">
            <el-form-item label="Correo electrónico" prop="email">
              <el-input v-model="form.email" placeholder="correo@ejemplo.com" type="email"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8" :xs="24">
            <el-form-item label="Teléfono">
              <el-input v-model="form.phone" placeholder="987654321"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="16" :xs="24">
            <el-form-item label="Dirección">
              <el-input v-model="form.address" placeholder="Av. Ejemplo 123..."></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8" :xs="24">
            <el-form-item label="Departamento / Provincia / Distrito">
              <el-cascader v-model="form.location_cascade" :options="tables.locations"
                :props="{ expandTrigger: 'hover', value: 'value', label: 'label', children: 'children' }"
                placeholder="Seleccione su ubicación" filterable clearable style="width:100%"></el-cascader>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div class="cf-step-actions">
        <button class="cf-btn cf-btn-outline" @click="goPrev">Atrás</button>
        <button class="cf-btn cf-btn-primary" @click="validateAndNext('formStep1')">Continuar</button>
      </div>
    </div>

    <!-- ──────────── Paso 2: Bien contratado ──────────── -->
    <div v-if="!showPrevCodeForm && currentStep === 2" class="cf-step-content">
      <p class="cf-section-title">Bien o servicio contratado</p>
      <el-form ref="formStep2" :model="form" :rules="rulesStep2" label-position="top" size="small">
        <el-row :gutter="16">
          <el-col :span="12" :xs="24">
            <el-form-item label="Tipo de bien" prop="asset_type">
              <el-select v-model="form.asset_type" placeholder="Seleccione" style="width:100%">
                <el-option value="producto" label="Producto"></el-option>
                <el-option value="servicio" label="Servicio"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12" :xs="24">
            <el-form-item label="Fecha de contratación" prop="asset_date">
              <el-date-picker v-model="form.asset_date" type="date" placeholder="Seleccione fecha"
                value-format="yyyy-MM-dd" style="width:100%"
                :picker-options="{ disabledDate: d => d > new Date() }"></el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Descripción del bien o servicio" prop="asset_description">
              <el-input v-model="form.asset_description" type="textarea" :rows="3"
                placeholder="Describa el producto o servicio contratado..."></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item>
              <div class="cf-switch-row">
                <el-switch v-model="form.has_receipt"></el-switch>
                <span class="cf-switch-label">Cuenta con comprobante de pago</span>
              </div>
            </el-form-item>
          </el-col>

          <!-- Campos de comprobante condicionales -->
          <template v-if="form.has_receipt">
            <el-col :span="24">
              <div class="cf-receipt-section">
                <p class="cf-receipt-section-title">Datos del comprobante</p>
                <el-row :gutter="16">
                  <el-col :span="5" :xs="24">
                    <el-form-item label="Moneda">
                      <el-select v-model="form.receipt_currency" style="width:100%">
                        <el-option value="PEN" label="Soles (PEN)"></el-option>
                        <el-option value="USD" label="Dólares (USD)"></el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="4" :xs="24">
                    <el-form-item label="Monto" prop="receipt_amount">
                      <el-input v-model="form.receipt_amount" type="number" min="0" placeholder="0.00"></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="6" :xs="24">
                    <el-form-item label="Tipo de documento">
                      <el-select v-model="form.receipt_document_type" placeholder="Seleccione" style="width:100%">
                        <el-option value="01" label="Factura"></el-option>
                        <el-option value="03" label="Boleta de venta"></el-option>
                        <el-option value="07" label="Nota de crédito"></el-option>
                        <el-option value="08" label="Nota de débito"></el-option>
                      </el-select>
                    </el-form-item>
                  </el-col>
                  <el-col :span="3" :xs="24">
                    <el-form-item label="Serie">
                      <el-input v-model="form.receipt_series" placeholder="B001"></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="6" :xs="24">
                    <el-form-item label="N° de comprobante">
                      <el-input v-model="form.receipt_number" placeholder="00000001"></el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </el-col>
          </template>
        </el-row>
      </el-form>
      <div class="cf-step-actions">
        <button class="cf-btn cf-btn-outline" @click="goPrev">Atrás</button>
        <button class="cf-btn cf-btn-primary" @click="validateAndNext('formStep2')">Continuar</button>
      </div>
    </div>

    <!-- ──────────── Paso 3: Detalle del reclamo ──────────── -->
    <div v-if="!showPrevCodeForm && currentStep === 3" class="cf-step-content">
      <div v-if="!showPrevCodeSection" style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#166534; line-height:1.6;">
        Ya tenemos guardada toda su información personal y los datos del reclamo anterior. Solo complete los campos a continuación con la nueva información que desea agregar.
      </div>
      <p class="cf-section-title">Detalle del reclamo</p>
      <el-form ref="formStep3" :model="form" :rules="rulesStep3" label-position="top" size="small">
        <el-row :gutter="16">
          <el-col v-if="showPrevCodeSection" :span="12" :xs="24">
            <el-form-item label="Tipo de registro" prop="claim_type">
              <el-select v-model="form.claim_type" placeholder="Seleccione" style="width:100%">
                <el-option value="queja" label="Queja"></el-option>
                <el-option value="reclamo" label="Reclamo"></el-option>
              </el-select>
              <div style="margin-top:6px; font-size:12px; color:#9298a5; line-height:1.5;">
                <div><strong>RECLAMO:</strong> Disconformidad relacionada a los productos o servicios.</div>
                <div style="margin-top:3px;"><strong>QUEJA:</strong> Disconformidad NO relacionada a los productos o servicios; si no al descontento respecto a la atención al público.</div>
              </div>
            </el-form-item>
          </el-col>
          <el-col v-if="showPrevCodeSection" :span="12" :xs="24">
            <el-form-item label="Canal de atención">
              <el-select v-model="form.channel" placeholder="Seleccione un canal" clearable style="width:100%">
                <el-option v-for="ch in tables.claim_channels" :key="ch.id" :label="ch.name"
                  :value="ch.name"></el-option>
              </el-select>
              <div v-if="selectedChannelInfo" class="cf-channel-info">
                {{ selectedChannelInfo }}
              </div>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Detalle de la queja o reclamo" prop="detail">
              <el-input v-model="form.detail" type="textarea" :rows="4"
                placeholder="Describa con detalle el motivo de su queja o reclamo..."></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Resultado esperado" prop="expected_result">
              <el-input v-model="form.expected_result" type="textarea" :rows="3"
                placeholder="¿Qué solución espera recibir?"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="Adjunto (opcional · máx. 5 archivos · máx. 1 MB por archivo: jpg, jpeg, png, pdf)">
              <el-upload ref="uploader" action="#"
                :http-request="() => { }"
                :on-change="onFileChange"
                :on-remove="onFileRemove"
                :file-list="fileList"
                :limit="5"
                multiple
                :auto-upload="false"
                :on-exceed="() => $message.warning('Máximo 5 archivos permitidos')"
                accept=".jpg,.jpeg,.png,.pdf">
                <button type="button" class="cf-btn cf-btn-outline cf-btn-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                  </svg>
                  Adjuntar archivos
                </button>
              </el-upload>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item prop="terms_accepted">
              <div class="cf-checkbox-row">
                <el-checkbox v-model="form.terms_accepted"></el-checkbox>
                <span class="cf-checkbox-label">
                  Acepto que los datos brindados son verídicos y me responsabilizo
                  de la información consignada en esta declaración.
                </span>
              </div>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div class="cf-step-actions">
        <button v-if="!showPrevCodeSection" class="cf-btn cf-btn-link" style="margin-right:auto;" @click="reset">← Enviar un nuevo reclamo</button>
        <button class="cf-btn cf-btn-outline" @click="goPrev">Atrás</button>
        <button class="cf-btn cf-btn-primary" :disabled="submitting" @click="submit">
          <span v-if="submitting" class="cf-spinner cf-spinner-white"></span>
          <span v-else>Enviar reclamo</span>
        </button>
      </div>
    </div>

    <div class="cf-alert cf-alert-default">
        <div>
            <p class="cf-prev-code-hint">La formulación de la queja o reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</p>
            <p class="cf-prev-code-hint">El proveedor debe dar respuesta al reclamo o queja en un plazo no mayor a quince (15) días hábiles, el cual es improrrogable.</p>
            <p class="cf-prev-code-hint" style="margin-bottom: 0;">Si no se consigna de manera adecuada toda la información mínima obligatoria, la queja o el reclamo se considerará no presentado.</p>
        </div>
    </div>
    <div>
        <small class="cf-prev-code-hint">
            Este libro de reclamaciones virtual es un servicio proporcionado por <a href="https://buho.la" target="_blank">Digital Buho</a> en cumplimiento de la Ley N° 29571, Código de Protección y Defensa del Consumidor.
        </small>
    </div>
    <!-- ──────────── Modal de resultado (éxito / error) ──────────── -->
    <claim-result-dialog
      :visible.sync="showResultDialog"
      :success="resultSuccess"
      :code="submittedCode"
      :pdf-url="resultPdfUrl"
      :email="resultEmail"
      :error-msg="resultErrorMsg"
      @closed="reset"
    ></claim-result-dialog>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&display=swap');
.cf-channel-info {
  margin-top: 4px;
  font-size: 12px;
  color: #909399;
  line-height: 1.4;
}
/* ── Variables ── */
.cf-wrapper {
  /* Color base */
  --base-l: 0.2103;
  --base-c: 0.0059;
  --base-h: 285.89;

  --_l: clamp(0.22, var(--base-l), 0.50);
  --_c: min(var(--base-c), 0.18);

  --cf-primary:    oklch(var(--base-l) var(--base-c) var(--base-h));
  --cf-bg:         oklch(0.978 calc(var(--_c) * 0.08) var(--base-h));
  --cf-border:     oklch(0.872 calc(var(--_c) * 0.14) var(--base-h) / 0.45);
  --cf-ring:       oklch(calc(var(--_l) + 0.14) calc(var(--_c) * 0.85) var(--base-h));
  --cf-foreground: oklch(0.145 calc(var(--_c) * 0.04) var(--base-h));
  --cf-muted:      oklch(0.960 calc(var(--_c) * 0.07) var(--base-h) / 0.55);
  --cf-muted-fg:   oklch(0.510 calc(var(--_c) * 0.22) var(--base-h));
  --cf-primary-fg: oklch(0.970 0.003 var(--base-h));

  --cf-radius:     0.5rem;
  --cf-shadow:     0 1px 3px 0 rgb(0 0 0/.08), 0 1px 2px -1px rgb(0 0 0/.08);

  max-width: 680px;
  margin: 0 auto;
  padding: 32px 24px;
  font-family: 'IBM Plex Sans', sans-serif;
  color: var(--cf-foreground);
  font-size: 14px;
  line-height: 1.5;
}

.cf-embedded { padding: 20px 16px; }

/* ── Header ── */
.cf-header {
  text-align: center;
  margin-bottom: 28px;
}

/* ── Tarjeta de empresa ── */
.cf-company-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: var(--cf-muted);
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  padding: 14px 16px;
  margin-bottom: 20px;
  text-align: left;
}
.cf-company-logo {
  width: 56px;
  height: 56px;
  object-fit: contain;
  border-radius: 6px;
  border: 1px solid var(--cf-border);
  background: #fff;
  flex-shrink: 0;
}
.cf-company-info {
  min-width: 0;
}
.cf-company-name {
  font-size: 15px;
  font-weight: 600;
  color: var(--cf-foreground);
  margin: 0 0 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cf-company-razon {
  font-size: 12px;
  color: var(--cf-muted-fg);
  margin: 0 0 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cf-company-meta {
  font-size: 12px;
  color: var(--cf-muted-fg);
  margin: 0;
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  align-items: center;
}
.cf-company-sep {
  color: var(--cf-ring);
}

.cf-header-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 148px;
  height: 148px;
  border-radius: var(--cf-radius);
  background: var(--cf-muted);
  color: var(--cf-foreground);
  margin-bottom: 14px;
  padding: 10px;
}

.cf-header-icon:has(svg) {
    width: 36px !important;
    height: 36px !important;
}
.cf-header-icon svg {
    width: 32px;
}

.cf-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--cf-foreground);
  margin: 0 0 6px;
  letter-spacing: -0.02em;
}

.cf-subtitle {
  font-size: 13px;
  color: var(--cf-muted-fg);
  margin: 0;
  line-height: 1.6;
  max-width: 500px;
  margin-inline: auto;
}

/* ── Código de reclamo previo ── */
.cf-prev-code-card,
.cf-alert.cf-alert-default  {
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  padding: 16px;
  background: #fff;
  margin-bottom: 24px;
}

.cf-prev-code-hint {
  font-size: 12.5px;
  color: var(--cf-muted-fg);
  margin: 0 0 10px;
}

.cf-inline {
  display: flex;
  gap: 8px;
  align-items: center;
}

/* ── Stepper custom ── */
.cf-stepper {
  display: flex;
  align-items: center;
  margin-bottom: 28px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.cf-step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  min-width: 64px;
}

.cf-step-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1.5px solid var(--cf-border);
  background: var(--cf-bg);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 500;
  color: var(--cf-muted-fg);
  transition: all 0.2s;
  flex-shrink: 0;
}

.cf-step-item.active .cf-step-circle {
  background: var(--cf-primary);
  border-color: var(--cf-primary);
  color: var(--cf-primary-fg);
}

.cf-step-item.done .cf-step-circle {
  background: var(--cf-primary);
  border-color: var(--cf-primary);
  color: var(--cf-primary-fg);
}

.cf-step-label {
  font-size: 11px;
  color: var(--cf-muted-fg);
  white-space: nowrap;
  font-weight: 400;
}

.cf-step-item.active .cf-step-label,
.cf-step-item.done .cf-step-label {
  color: var(--cf-foreground);
  font-weight: 500;
}

.cf-step-connector {
  flex: 1;
  height: 1px;
  background: var(--cf-border);
  min-width: 24px;
  margin-bottom: 18px;
  transition: background 0.2s;
}

.cf-step-connector.done {
  background: var(--cf-primary);
}

/* ── Step content ── */
.cf-step-content {
  animation: cf-fade-in 0.18s ease;
}

@keyframes cf-fade-in {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.cf-section-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--cf-muted-fg);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0 0 16px;
}

/* ── Alertas ── */
.cf-alert {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 14px;
  border-radius: var(--cf-radius);
  font-size: 13px;
  line-height: 1.5;
  border: 1px solid transparent;
}

.cf-alert svg { flex-shrink: 0; margin-top: 1px; }

.cf-alert-info {
  background: #eff6ff;
  border-color: #bfdbfe;
  color: #1d4ed8;
}

.cf-alert-warning {
  background: #fffbeb;
  border-color: #fde68a;
  color: #92400e;
}

.cf-prev-result { margin-bottom: 16px; }

/* ── Resumen compacto del reclamo previo (paso 0) ── */
.cf-prev-summary {
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 5px;
  padding: 12px 14px;
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  background: var(--cf-bg);
}

.cf-summary-row {
  display: flex;
  gap: 8px;
  align-items: baseline;
  font-size: 13px;
}

.cf-summary-label {
  min-width: 72px;
  color: var(--cf-muted-fg);
  font-weight: 500;
  font-size: 12px;
  flex-shrink: 0;
}

/* ── Lista de archivos en paso 0 ── */
.cf-file-list {
  margin: 0;
  padding-left: 0;
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cf-file-list li a {
  font-size: 12.5px;
  color: #2563eb;
  text-decoration: none;
  word-break: break-all;
}

.cf-file-list li a:hover {
  text-decoration: underline;
}

/* ── Variante success para alerta de reclamo cerrado ── */
.cf-alert-success {
  background: #f0fdf4;
  border-color: #bbf7d0;
  color: #15803d;
}

/* ── Bloque de info expandido (bien, detalle) ── */
.cf-info-block {
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  padding: 12px 14px;
  background: var(--cf-bg);
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.cf-info-block-title {
  font-size: 11px;
  font-weight: 600;
  color: var(--cf-muted-fg);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0 0 6px;
}

.cf-resolution-box {
  margin-top: 12px;
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  padding: 12px 14px;
  background: var(--cf-bg);
}

.cf-resolution-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--cf-muted-fg);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0 0 4px;
}

.cf-resolution-text {
  font-size: 13px;
  color: var(--cf-foreground);
  margin: 0;
}

.cf-resolution-html {
  font-size: 13px;
  color: var(--cf-foreground);
  line-height: 1.6;
}

.cf-resolution-html p { margin: 0 0 6px; }
.cf-resolution-html ul,
.cf-resolution-html ol { padding-left: 20px; margin: 0 0 6px; }
.cf-resolution-html strong { font-weight: 600; }
.cf-resolution-html em { font-style: italic; }
.cf-resolution-html a { color: #2563eb; text-decoration: underline; }
.cf-resolution-html blockquote {
  border-left: 3px solid var(--cf-border);
  margin: 4px 0;
  padding-left: 10px;
  color: var(--cf-muted-fg);
}

/* ── Switch & Checkbox rows ── */
.cf-switch-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.cf-switch-label {
  font-size: 13.5px;
  color: var(--cf-foreground);
}

.cf-checkbox-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.cf-checkbox-label {
  font-size: 13px;
  color: var(--cf-foreground);
  line-height: 1.5;
  padding-top: 2px;
}

/* ── Receipt section ── */
.cf-receipt-section {
  border: 1px solid var(--cf-border);
  border-radius: var(--cf-radius);
  padding: 16px;
  background: var(--cf-muted);
  margin-bottom: 8px;
}

.cf-receipt-section-title {
  font-size: 12px;
  font-weight: 600;
  color: var(--cf-muted-fg);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin: 0 0 14px;
}

/* ── Botones ── */
.cf-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 0 14px;
  height: 36px;
  border-radius: var(--cf-radius);
  font-size: 13.5px;
  font-weight: 500;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all 0.15s;
  white-space: nowrap;
  font-family: inherit;
}
.cf-btn.cf-btn-link {
    padding: 0;
    height: 0;
    text-decoration: underline;
}

.cf-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.cf-btn-primary {
  background: var(--cf-primary);
  color: var(--cf-primary-fg);
  border-color: var(--cf-primary);
}

.cf-btn-primary:hover:not(:disabled) {
  background: #27272a;
  border-color: #27272a;
}

.cf-btn-outline {
  background: var(--cf-bg);
  color: var(--cf-foreground);
  border-color: var(--cf-border);
}

.cf-btn-outline:hover:not(:disabled) {
  background: var(--cf-muted);
}

.cf-btn-sm {
  height: 30px;
  padding: 0 10px;
  font-size: 12.5px;
}

/* ── Acciones del paso ── */
.cf-step-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 24px;
  padding: 16px 0;
  border-top: 1px solid var(--cf-border);
  align-items: center;
}

/* ── Spinner ── */
.cf-spinner {
  width: 14px;
  height: 14px;
  border: 2px solid var(--cf-border);
  border-top-color: var(--cf-foreground);
  border-radius: 50%;
  animation: cf-spin 0.6s linear infinite;
  display: inline-block;
}

.cf-spinner-white {
  border-color: rgba(255,255,255,0.35);
  border-top-color: #fff;
}

@keyframes cf-spin {
  to { transform: rotate(360deg); }
}

/* ── Override Element UI para match shadcn ── */
.cf-wrapper :deep(.el-input__inner),
.cf-wrapper :deep(.el-textarea__inner) {
  border-color: var(--cf-border);
  border-radius: var(--cf-radius);
  font-size: 15px;
  color: var(--cf-foreground);
  background: var(--cf-bg);
  transition: border-color 0.15s, box-shadow 0.15s;
}

.cf-wrapper :deep(.el-input__inner) {
  height: 36px;
  line-height: 36px;
}

.cf-wrapper :deep(.el-textarea__inner) {
  font-family: 'IBM Plex Sans', sans-serif;
  min-height: 128px !important;
  height: auto;
  line-height: 1.6;
  field-sizing: content;
  resize: none;
}

.cf-wrapper :deep(.el-input__inner:focus),
.cf-wrapper :deep(.el-textarea__inner:focus) {
  border-color: var(--cf-primary);
  box-shadow: 0 0 0 2px rgb(24 24 27 / 0.08);
  outline: none;
}

.cf-wrapper :deep(.el-select .el-input__inner) {
  border-color: var(--cf-border);
}

.cf-wrapper :deep(.el-form-item__label) {
  font-size: 13px;
  font-weight: 500;
  color: var(--cf-foreground);
  padding-bottom: 6px;
  line-height: 1.4;
}

.cf-wrapper :deep(.el-form-item) {
  margin-bottom: 16px;
}

.cf-wrapper :deep(.el-form-item__error) {
  font-size: 12px;
  color: #ef4444;
}

.cf-wrapper :deep(.el-cascader .el-input__inner) {
  border-color: var(--cf-border);
}

.cf-wrapper :deep(.el-date-editor .el-input__inner) {
  border-color: var(--cf-border);
}

.cf-wrapper :deep(.el-upload .el-button) {
  display: none;
}

.cf-wrapper :deep(.el-checkbox__inner) {
  border-color: var(--cf-border);
  border-radius: 3px;
}

.cf-wrapper :deep(.el-checkbox__input.is-checked .el-checkbox__inner) {
  background-color: var(--cf-primary);
  border-color: var(--cf-primary);
}

.cf-wrapper :deep(.el-switch.is-checked .el-switch__core) {
  background-color: var(--cf-primary);
  border-color: var(--cf-primary);
}
</style>

<script>
import ClaimResultDialog from './partials/ClaimResultDialog.vue'

function hexToOklch(hex) {
  const r = parseInt(hex.slice(1,3),16)/255, g = parseInt(hex.slice(3,5),16)/255, b = parseInt(hex.slice(5,7),16)/255
  const toLinear = c => c <= 0.04045 ? c/12.92 : ((c+0.055)/1.055)**2.4
  const lr = toLinear(r), lg = toLinear(g), lb = toLinear(b)
  const l = 0.4122214708*lr + 0.5363325363*lg + 0.0514459929*lb
  const m = 0.2119034982*lr + 0.6806995451*lg + 0.1073969566*lb
  const s = 0.0883024619*lr + 0.2817188376*lg + 0.6299787005*lb
  const l_ = Math.cbrt(l), m_ = Math.cbrt(m), s_ = Math.cbrt(s)
  const L  = 0.2104542553*l_ + 0.7936177850*m_ - 0.0040720468*s_
  const a  = 1.9779984951*l_ - 2.4285922050*m_ + 0.4505937099*s_
  const bv = 0.0259040371*l_ + 0.7827717662*m_ - 0.8086757660*s_
  const H = ((Math.atan2(bv,a)*180/Math.PI) % 360 + 360) % 360
  return { l: +L.toFixed(4), c: +Math.sqrt(a*a+bv*bv).toFixed(4), h: +H.toFixed(2) }
}

export default {
  components: { ClaimResultDialog },

  props: {
    // Modo embebido para el widget (iframe en sitio externo)
    embedded: {
      type: Boolean,
      default: false
    },
    // Slug del tenant — requerido cuando embedded = true
    tenantSlug: {
      type: String,
      default: ''
    },
    primaryColor: {
      type: String,
      default: ''
    },
    colorL: { type: [Number, String], default: null },
    colorC: { type: [Number, String], default: null },
    colorH: { type: [Number, String], default: null },
    // Mostrar u ocultar los datos de la empresa en el encabezado
    showCompany: {
      type: Boolean,
      default: true
    }
  },

  data() {
    return {
      currentStep: 1,
      submitting: false,
      showClaimDetails: false,
      showPrevCodeForm: false,
      showPrevCodeSection: true,
      // Control del modal de resultado
      showResultDialog: false,
      resultSuccess: false,
      submittedCode: '',
      resultErrorMsg: '',
      resultPdfUrl: '',
      resultEmail: '',
      lookingUp: false,
      previousClaim: null,
      fileList: [],
      attachmentFile: null,

      // Tablas auxiliares
      tables: {
        identity_document_types: [],
        claim_channels: [],
        locations: [],
        company: null,
      },

      // Datos del formulario (todos los pasos)
      form: {
        previous_code: '',
        // Paso 1
        identity_document_type: '',
        identity_document_number: '',
        name: '',
        email: '',
        phone: '',
        location_cascade: [],
        address: '',
        // Paso 2
        asset_type: '',
        asset_description: '',
        asset_date: '',
        has_receipt: false,
        receipt_amount: '',
        receipt_currency: 'PEN',
        receipt_document_type: '03',
        receipt_series: '',
        receipt_number: '',
        // Paso 3
        claim_type: '',
        detail: '',
        expected_result: '',
        channel: '',
        terms_accepted: false,
      },

      // Reglas de validación por paso
      rulesStep1: {
        identity_document_type: [{ required: true, message: 'Seleccione un tipo de documento', trigger: 'change' }],
        identity_document_number: [{ required: true, message: 'Ingrese el número de documento', trigger: 'blur' }],
        name: [{ required: true, message: 'Ingrese su nombre completo', trigger: 'blur' }],
        email: [
          { required: true, message: 'Ingrese su email', trigger: 'blur' },
          { type: 'email', message: 'Formato de email inválido', trigger: 'blur' },
        ],
      },
      rulesStep2: {
        asset_type: [{ required: true, message: 'Seleccione el tipo de bien', trigger: 'change' }],
        asset_date: [{ required: true, message: 'Seleccione la fecha', trigger: 'change' }],
        asset_description: [{ required: true, message: 'Describa el bien o servicio', trigger: 'blur' }],
        receipt_amount: [{ required: true, message: 'Ingrese el monto reclamado', trigger: 'blur' }],
      },
      rulesStep3: {
        claim_type: [{ required: true, message: 'Seleccione el tipo de registro', trigger: 'change' }],
        detail: [{ required: true, message: 'Describa su queja o reclamo', trigger: 'blur' }],
        expected_result: [{ required: true, message: 'Indique el resultado esperado', trigger: 'blur' }],
        terms_accepted: [
          {
            validator: (rule, value, callback) => {
              if (!value) callback(new Error('Debe aceptar la declaración'))
              else callback()
            },
            trigger: 'change'
          }
        ],
      },
    }
  },

  computed: {
    selectedChannelInfo() {
      if (!this.form.channel) return null
      const ch = this.tables.claim_channels.find(c => c.name === this.form.channel)
      if (!ch) return null
      const parts = [ch.address, ch.telephone, ch.email].filter(Boolean)
      if (parts.length) return parts.join(' · ')
      return ch.description || null
    },

    // Determina la URL base de los endpoints según el modo (widget o panel)
    baseUrl() {
      return this.embedded ? '' : ''
    },

    // Calcula el índice activo del stepper según si el paso 0 está visible
    activeStep() {
      return this.previousClaim ? this.currentStep : this.currentStep - 1
    },

    // Extrae el district_id final de la cascada de ubicación
    districtId() {
      return this.form.location_cascade && this.form.location_cascade.length === 3
        ? this.form.location_cascade[2]
        : null
    },
  },

  mounted() {
    let l = this.colorL, c = this.colorC, h = this.colorH
    if (l === null && this.primaryColor && /^#[0-9A-Fa-f]{6}$/.test(this.primaryColor)) {
      const ok = hexToOklch(this.primaryColor)
      l = ok.l; c = ok.c; h = ok.h
    }
    if (l !== null && c !== null && h !== null) {
      this.$el.style.setProperty('--base-l', l)
      this.$el.style.setProperty('--base-c', c)
      this.$el.style.setProperty('--base-h', h)
    }
  },

  created() {
    this.loadTables()
  },

  methods: {
    // Carga los datos auxiliares desde el endpoint correspondiente
    loadTables() {
      if (this.embedded && this.tenantSlug) {
        // Widget: endpoint público con tenant_slug — usar URL absoluta
        const remoteBase = window.location.protocol + '//' + this.tenantSlug
        this.$http.get(remoteBase + '/claims/remote/tables/' + encodeURIComponent(this.tenantSlug))
          .then(response => { this.tables = response.data })
      } else if (!this.embedded) {
        // Panel admin: endpoint autenticado
        this.$http.get('/claims/tables')
          .then(response => {
            this.tables = response.data
          })
      }
    },

    // Verifica el código de reclamo previo en el backend
    lookupPreviousCode() {
      const code = (this.form.previous_code || '').trim()
      if (!code) {
        this.previousClaim = null
        return
      }
      this.lookingUp = true
      this.$http.get(`/claims/lookup/${encodeURIComponent(code)}`)
        .then(response => {
          this.previousClaim = response.data
          const d = response.data

          // Siempre autorrellenar paso 1
          if (d.identity_document_type)   this.form.identity_document_type   = d.identity_document_type
          if (d.identity_document_number) this.form.identity_document_number = d.identity_document_number
          if (d.name)                     this.form.name                     = d.name
          if (d.email)                    this.form.email                    = d.email
          if (d.phone)                    this.form.phone                    = d.phone
          if (d.address)                  this.form.address                  = d.address
          // Reconstruir cascada de ubicación (ubigeo 6 dígitos)
          if (d.district_id) {
            const distStr = String(d.district_id)
            this.form.location_cascade = [
              distStr.substring(0, 2),
              distStr.substring(0, 4),
              distStr,
            ]
          }

          if (d.is_closed) {
            // Reclamo cerrado: autorrellenar pasos 2 y parcialmente 3, saltar al paso 3
            if (d.asset_type)        this.form.asset_type        = d.asset_type
            if (d.asset_description) this.form.asset_description = d.asset_description
            if (d.asset_date)        this.form.asset_date        = d.asset_date
            this.form.has_receipt = !!d.has_receipt
            if (d.has_receipt) {
              if (d.receipt_amount)   this.form.receipt_amount   = d.receipt_amount
              if (d.receipt_currency) this.form.receipt_currency = d.receipt_currency
              if (d.receipt_series)   this.form.receipt_series   = d.receipt_series
              if (d.receipt_number)   this.form.receipt_number   = d.receipt_number
            }
            // Paso 3 parcial: tipo y canal; detail, expected_result, archivos y aceptación quedan vacíos
            if (d.claim_type) this.form.claim_type = d.claim_type
            if (d.channel)    this.form.channel    = d.channel
            // Mostrar paso 0 primero (igual que en proceso) para que el usuario vea el resumen
            this.currentStep = 0
          } else {
            // Reclamo en proceso: mostrar info en paso 0, no permite continuar
            this.currentStep = 0
          }
        })
        .catch(() => {
          this.previousClaim = null
          if (this.currentStep === 0) this.currentStep = 1
          this.$message.warning('Código no encontrado')
        })
        .finally(() => { this.lookingUp = false })
    },

    goNext() {
      // Reclamo previo cerrado en paso 0: saltar directo al paso 3 (1 y 2 bloqueados)
      if (this.currentStep === 0 && this.previousClaim && this.previousClaim.is_closed) {
        this.currentStep = 3
        return
      }
      this.currentStep++
    },

    goPrev() {
      const minStep = this.previousClaim ? 0 : 1
      // Reclamo cerrado: desde paso 3, volver al 0 para ver info del reclamo previo
      if (this.previousClaim && this.previousClaim.is_closed && this.currentStep === 3) {
        this.currentStep = 0
        return
      }
      if (this.currentStep > minStep) this.currentStep--
    },

    // Reinicia completamente el formulario (borra código previo, previous claim y todos los campos)
    clearForm() {
      this.previousClaim  = null
      this.lookingUp      = false
      this.fileList       = []
      this.attachmentFile = null
      this.currentStep    = 1
      this.form = {
        previous_code: '',
        identity_document_type: '',
        identity_document_number: '',
        name: '',
        email: '',
        phone: '',
        location_cascade: [],
        address: '',
        asset_type: '',
        asset_description: '',
        asset_date: '',
        has_receipt: false,
        receipt_amount: '',
        receipt_currency: 'PEN',
        receipt_document_type: '03',
        receipt_series: '',
        receipt_number: '',
        claim_type: '',
        detail: '',
        expected_result: '',
        channel: '',
        terms_accepted: false,
      }
      if (this.$refs.uploader)  this.$refs.uploader.clearFiles()
      if (this.$refs.formStep1) this.$refs.formStep1.clearValidate()
      if (this.$refs.formStep2) this.$refs.formStep2.clearValidate()
      if (this.$refs.formStep3) this.$refs.formStep3.clearValidate()
    },

    // Valida el formulario del paso actual antes de avanzar
    validateAndNext(refName) {
      this.$refs[refName].validate(valid => {
        if (valid) this.currentStep++
      })
    },

    onFileChange(file) {
      // Verificar tamaño: máx. 1 MB
      if (file.size > 1024 * 1024) {
        this.$message.error('El archivo no debe superar 1 MB')
        this.$refs.uploader.clearFiles()
        this.attachmentFile = null
        this.fileList = []
        return false
      }
      this.attachmentFile = file.raw
    },

    onFileRemove() {
      this.attachmentFile = null
    },

    // Construye FormData y envía el reclamo al backend
    submit() {
      this.$refs.formStep3.validate(valid => {
        if (!valid) return

        this.submitting = true
        const fd = this.buildFormData()

        // Seleccionar endpoint según modo. Cuando está embebido, usar URL absoluta
        const url = this.embedded
          ? (window.location.protocol + '//' + this.tenantSlug + '/api/claims/remote/store')
          : '/claims/store'

        this.$http.post(url, fd, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
          .then(response => {
            if (response.data.success) {
              this.submittedCode    = response.data.code
              this.resultPdfUrl     = response.data.pdf_url || ''
              this.resultEmail      = response.data.email   || ''
              this.resultSuccess    = true
              this.showResultDialog = true
            }
          })
          .catch(error => {
            // Extraer el primer mensaje de error de validación del backend
            if (error.response && error.response.data && error.response.data.errors) {
              const firstError = Object.values(error.response.data.errors)[0]
              this.resultErrorMsg = firstError[0] || 'Error al enviar el reclamo'
            } else {
              this.resultErrorMsg = 'Ocurrió un error al registrar el reclamo. Inténtelo nuevamente.'
            }
            this.resultSuccess   = false
            this.showResultDialog = true
          })
          .finally(() => { this.submitting = false })
      })
    },

    // Construye el FormData con todos los campos del formulario
    buildFormData() {
      const fd = new FormData()

      // Datos del modo widget
      if (this.embedded && this.tenantSlug) {
        fd.append('tenant_slug', this.tenantSlug)
      }

      // Reclamo previo
      if (this.form.previous_code) {
        fd.append('previous_code', this.form.previous_code)
      }

      // Paso 1
      fd.append('identity_document_type', this.form.identity_document_type)
      fd.append('identity_document_number', this.form.identity_document_number)
      fd.append('name', this.form.name)
      fd.append('email', this.form.email)
      if (this.form.phone) fd.append('phone', this.form.phone)
      if (this.districtId) fd.append('district_id', this.districtId)
      if (this.form.address) fd.append('address', this.form.address)

      // Paso 2
      fd.append('asset_type', this.form.asset_type)
      fd.append('asset_description', this.form.asset_description)
      fd.append('asset_date', this.form.asset_date)
      fd.append('has_receipt', this.form.has_receipt ? '1' : '0')

      if (this.form.has_receipt) {
        fd.append('receipt_amount', this.form.receipt_amount)
        fd.append('receipt_currency', this.form.receipt_currency)
        fd.append('receipt_document_type', this.form.receipt_document_type)
        if (this.form.receipt_series) fd.append('receipt_series', this.form.receipt_series)
        if (this.form.receipt_number) fd.append('receipt_number', this.form.receipt_number)
      }

      // Paso 3
      fd.append('claim_type', this.form.claim_type)
      fd.append('detail', this.form.detail)
      fd.append('expected_result', this.form.expected_result)
      if (this.form.channel) fd.append('channel', this.form.channel)
      fd.append('terms_accepted', '1')

      if (this.attachmentFile) {
        fd.append('attachment', this.attachmentFile)
      }

      return fd
    },

    // Copia el código de seguimiento al portapapeles
    copyCode() {
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(this.submittedCode).then(() => {
          this.$message.success('Código copiado')
        }).catch(() => this.fallbackCopyCode())
      } else {
        this.fallbackCopyCode()
      }
    },

    fallbackCopyCode() {
      const el = document.createElement('textarea')
      el.value = this.submittedCode
      el.style.cssText = 'position:fixed;opacity:0'
      document.body.appendChild(el)
      el.select()
      try {
        document.execCommand('copy')
        this.$message.success('Código copiado')
      } catch {
        this.$message.error('No se pudo copiar automáticamente')
      }
      document.body.removeChild(el)
    },

    // Genera un slug CSS a partir del nombre del estado
    statusSlug(description) {
      if (!description) return ''
      return description
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9-]/g, '')
    },

    // Estilos inline derivados del color del estado
    statusAlertStyle(color) {
      if (!color) return {}
      return {
        borderColor: color,
        backgroundColor: color + '22',
        color: color,
      }
    },

    // Enmascarar email: car****@gmail.com
    maskEmail(email) {
      if (!email) return ''
      const [local, domain] = String(email).split('@')
      if (!domain) return email
      const visible = local.length > 3 ? local.substring(0, 3) : local.substring(0, 1)
      return `${visible}****@${domain}`
    },

    // Obtener nombre de archivo desde una URL
    getFilenameFromUrl(url) {
      try {
        const parts = String(url).split('/')
        const last = parts[parts.length - 1] || ''
        return decodeURIComponent((last.split('?')[0]) || 'archivo')
      } catch {
        return 'archivo'
      }
    },

    // Forzar descarga del recurso (genera un enlace y dispara click)
    downloadUrl(url) {
      try {
        const a = document.createElement('a')
        a.href = url
        a.target = '_blank'
        a.download = this.getFilenameFromUrl(url) || ''
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
      } catch (e) {
        // fallback: abrir en nueva pestaña
        window.open(url, '_blank')
      }
    },

    // Reinicia el formulario para registrar otro reclamo
    reset() {
      this.showResultDialog = false
      this.resultSuccess    = false
      this.submittedCode    = ''
      this.resultErrorMsg   = ''
      this.currentStep = 1
      this.previousClaim = null
      this.fileList = []
      this.attachmentFile = null
      this.form = {
        previous_code: '',
        identity_document_type: '',
        identity_document_number: '',
        name: '',
        email: '',
        phone: '',
        location_cascade: [],
        address: '',
        asset_type: '',
        asset_description: '',
        asset_date: '',
        has_receipt: false,
        receipt_amount: '',
        receipt_currency: 'PEN',
        receipt_document_type: '03',
        receipt_series: '',
        receipt_number: '',
        claim_type: '',
        detail: '',
        expected_result: '',
        channel: '',
        terms_accepted: false,
      }
      if (this.$refs.formStep1) this.$refs.formStep1.clearValidate()
      if (this.$refs.formStep2) this.$refs.formStep2.clearValidate()
      if (this.$refs.formStep3) this.$refs.formStep3.clearValidate()
    },
  }
}
</script>
