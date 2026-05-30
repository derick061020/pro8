<template>
    <div class="container container-tag-editor p-0">
      <!-- HEADER -->
      <div class="header header-tag-editor">
        <div>
          <h2 v-if="showTitle" class="tag-title m-0 d-none d-md-block">Editor de Etiquetas</h2>
          <h3 v-if="showTitle" class="tag-title m-0 d-none d-sm-block d-md-none">Editor de Etiquetas</h3>
          <h4 v-if="showTitle" class="tag-title m-0 d-block d-sm-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-tags me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8v4.172a2 2 0 0 0 .586 1.414l5.71 5.71a2.41 2.41 0 0 0 3.408 0l3.592 -3.592a2.41 2.41 0 0 0 0 -3.408l-5.71 -5.71a2 2 0 0 0 -1.414 -.586h-4.172a2 2 0 0 0 -2 2" /><path d="M18 19l1.592 -1.592a4.82 4.82 0 0 0 0 -6.816l-4.592 -4.592" /><path d="M7 10h-.01" /></svg>
            Etiquetas
          </h4>
        </div>
  
        <div class="header-center">
          <!-- Dimensiones -->
          <div class="dimensions-controls">
            <input
              type="number"
              v-model.number="labelWidth"
              @change="updateCanvasSize"
            />
            <span class="unit-label">x</span>
            <input
              type="number"
              v-model.number="labelHeight"
              @change="updateCanvasSize"
            />
            <span class="unit-label">mm</span>
            <span>|</span>
            <div class="d-flex align-items-center gap-1">            
              <span class="zoom-level">{{ zoomPercent }}%</span>
              <button class="zoom-btn" @click="changeZoom(-0.1)" title="Alejar">-</button>
              <button class="zoom-btn" @click="changeZoom(0.1)" title="Acercar">+</button>                          
            </div>
          </div> 
          
          <div class="settings-button" ref="settingsButton">
            <el-button @click.stop="toggleSettingsDropdown">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
              Ajustes
            </el-button>

            <!-- Dropdown para mobile: muestra las dimensiones y controles de zoom -->
            <div v-if="isSettingsDropdownOpen" class="settings-dropdown" @click.stop>
              <div class="dropdown-section">
                <h5 class="dropdown-title">Dimensiones</h5>
                <div class="row mx-0">
                  <div class="col-5">
                    <input type="number" v-model.number="labelWidth" @change="updateCanvasSize" class="form-control small-input w-100" />
                  </div>
                  <div class="col-1 d-flex align-items-center justify-content-center px-0">
                    <span class="unit-label">x</span>
                  </div>
                  <div class="col-5">
                    <input type="number" v-model.number="labelHeight" @change="updateCanvasSize" class="form-control small-input w-100" />
                  </div>
                  <div class="col-1 px-0 d-flex align-items-center justify-content-center">
                    <span class="unit-label">mm</span>
                  </div>
                </div>
              </div>

              <div class="dropdown-section mt-2">
                <h5 class="dropdown-title">Zoom</h5>
                <div class="d-flex align-items-center justify-content-between gap-1 dimension-controls">                  
                  <button class="zoom-btn" @click="changeZoom(-0.1)" title="Alejar">-</button>
                  <span class="zoom-level">{{ zoomPercent }}%</span>
                  <button class="zoom-btn" @click="changeZoom(0.1)" title="Acercar">+</button>                  
                </div>
              </div>

              <div class="mt-3 d-flex align-items-center justify-content-center">
                <button class="btn btn-sm second-buton" @click="resetZoom" title="Resetear">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rotate" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.95 11a8 8 0 1 0 -.5 4m.5 5v-5h-5" /></svg>
                  Restablecer valores
                </button>
              </div>
            </div>
          </div>
        </div>        
  
        <div class="header-actions">
          <el-popover placement="bottom" width="300" trigger="click" :visible.sync="downloadPopoverVisible">
            <div class="">
              <div class="form-group">
                <label class="control-label">Formato de descarga</label>
                <el-select v-model="downloadFormat" placeholder="Selecciona formato">
                  <el-option label="PNG" value="png" />
                  <el-option label="PDF" value="pdf" />
                </el-select>                
              </div>

              <el-button type="primary" class="w-100 mt-3" @click="downloadFromDropdown" :disabled="!downloadFormat">
                Descargar
              </el-button>
            </div>

            <template #reference>
              <button class="el-button d-flex align-items-center" @click.stop>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                <span class="d-none d-sm-block">
                  Descargar
                </span>
              </button>
            </template>
          </el-popover>
        </div>
      </div>
  
      <!-- MAIN -->
      <div class="main-content">
        <!-- LEFT SIDEBAR -->
        <div :class="['sidebar','col-3',{ 'mobile-open': isLeftSidebarOpen }]">
          <!-- Datos del sistema -->
          <div class="tool-section">
            <h4 class="mt-0 d-flex justify-content-between">
              Datos del Sistema
              <button class="bg-transparent border-0 btn-close-sidebar" @click="toggleLeftSidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
              </button>
            </h4>
            <div class="input-group">
              <label>Seleccionar datos</label>
  
              <div class="w-100">
                <!-- Tags seleccionados -->
                <div class="mb-1">
                  <el-tag
                    v-for="tagKey in selectedTags"
                    :key="tagKey"
                    closable
                    @close="removeTag(tagKey)"
                    class="mb-1"
                  >
                    {{ dataLabels[tagKey] }}
                  </el-tag>
                </div>
  
                <!-- Buscador de tags -->
                <div
                  class="tag-input-container"
                  v-if="selectedTags.length < availableTags.length"
                >
                  <el-select
                    v-model="tagSelectValue"
                    filterable
                    :filter-method="handleTagFilter"
                    placeholder="Buscar"
                    style="width: 100%;"
                    @change="handleTagSelect"
                    prefix-icon="el-icon-search"
                  >
                    <el-option
                      v-for="tag in filteredTags"
                      :key="tag.key"
                      :label="tag.label + ' - ' + tag.value"
                      :value="tag.key"
                    />
                  </el-select>
                </div>
              </div>
            </div>
  
          </div>
  
          <!-- Agregar elementos -->
          <div class="tool-section">
            <h4>Agregar Elementos</h4>
            <div style="display: flex; gap: 0.25rem;">
              <!-- <button class="btn btn-secondary btn-full" @click="addField('text')">
                Texto
              </button> -->
              <el-button
                type="primary"
                class="btn btn-primary btn-full"
                @click="$refs.imageUpload.click()"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-photo-up me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v6.5" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l3.5 3.5" /><path d="M14 14l1 -1c.679 -.653 1.473 -.829 2.214 -.526" /><path d="M19 22v-6" /><path d="M22 19l-3 -3l-3 3" /></svg>
                Subir Imagen
              </el-button>
              <input
                ref="imageUpload"
                type="file"
                class="file-upload"
                accept="image/*"
                @change="handleImageUpload"
              />
            </div>
            <!-- <div style="margin-top: 0.5rem;">
              <button
                class="btn btn-secondary btn-full"
                @click="addField('barcode','barcode')"
              >
                Código de Barras
              </button>
            </div> -->
          </div>
  
          <!-- Propiedades del campo -->
          <div
            class="field-properties-section"
            v-show="showFieldProperties"
          >
            <h4>Propiedades del Campo</h4>
  
            <!-- TEXTO -->
            <div v-if="fieldType === 'text'">
              <div class="input-group">
                <label>Contenido</label>
                <el-input
                  type="text"
                  v-model="fieldContent"
                  @input="updateFieldContent"
                />
              </div>
  
              <div class="input-group">
                <label>Tamaño de Letra</label>
                <div class="d-flex gap-1">
                  <el-input
                    type="number"
                    v-model.number="fontSize"
                    min="8"
                    max="72"
                    @change="updateFieldStyle"
                    style="width: 30%;"
                  />
  
                  <button
                    class="btn-toggle"
                    :class="{ active: fontBold }"
                    @click="toggleFontWeight"
                    title="Negrita"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bold"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 5h6a3.5 3.5 0 0 1 0 7h-6l0 -7" /><path d="M13 12h1a3.5 3.5 0 0 1 0 7h-7v-7" /></svg>
                  </button>
  
                  <button
                    class="btn-align"
                    :class="{ active: textAlign === 'left' }"
                    @click="setAlignment('left')"
                    title="Izquierda"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-align-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M4 12l10 0" /><path d="M4 18l14 0" /></svg>
                  </button>
  
                  <button
                    class="btn-align"
                    :class="{ active: textAlign === 'center' }"
                    @click="setAlignment('center')"
                    title="Centro"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-align-center"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M8 12l8 0" /><path d="M6 18l12 0" /></svg>
                  </button>
  
                  <button
                    class="btn-align"
                    :class="{ active: textAlign === 'right' }"
                    @click="setAlignment('right')"
                    title="Derecha"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-align-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M10 12l10 0" /><path d="M6 18l14 0" /></svg>
                  </button>
                </div>
              </div>
            </div>
  
            <!-- BARRAS -->
            <div v-if="fieldType === 'barcode'">
              <div class="input-group">
                <label>Contenido del Código</label>
                <el-input
                  type="text"
                  v-model="barcodeContent"
                  @input="handleBarcodeInput"
                />
              </div>
  
              <div class="input-group">
                <label>Formato</label>
                <el-select v-model="barcodeFormat" @change="updateBarcodeStyle">
                  <el-option value="CODE128">CODE128</el-option>
                  <el-option value="EAN13">EAN13</el-option>
                  <el-option value="EAN8">EAN8</el-option>
                  <el-option value="UPC">UPC</el-option>
                  <el-option value="CODE39">CODE39</el-option>
                </el-select>
              </div>
  
              <div class="input-group">
                <label>Configuración</label>
                <div style="display: flex; gap: 0.25rem;">
                  <div style="flex: 1;">
                    <label style="font-size: 0.8rem; margin-bottom: 0.1rem;">Altura</label>
                    <el-input
                      type="number"
                      v-model.number="barcodeHeight"
                      min="20"
                      max="150"
                      @change="updateBarcodeStyle"
                      style="width: 100%;"
                    />
                  </div>
                  <div style="flex: 1;">
                    <label style="font-size: 0.8rem; margin-bottom: 0.1rem;">Mostrar Valor</label>
                    <el-select
                      v-model="barcodeDisplayValue"
                      @change="updateBarcodeStyle"
                      style="width: 100%;"
                    >
                      <el-option :value="'true'" label="Sí">Sí</el-option>
                      <el-option :value="'false'" label="No">No</el-option>
                    </el-select>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- IMAGEN (ahora solo borrado) -->
            <div v-if="fieldType === 'image'">
              <div class="input-group">
                <label>Acciones de Imagen</label>
                <el-button type="danger" class="btn btn-full" @click="deleteSelectedField">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-photo-x me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M13 21h-7a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v7" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l3 3" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0" /><path d="M22 22l-5 -5" /><path d="M17 22l5 -5" /></svg>
                  Eliminar imagen
                </el-button>
              </div>
            </div>
  
            <div class="tool-section">
              <el-button class="btn btn-outline-danger btn-full" @click="deleteSelectedField">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash-x me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7h16" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /><path d="M10 12l4 4m0 -4l-4 4" /></svg>
                Eliminar Campo
              </el-button>
            </div>
          </div>
  
          <!-- Limpiar todo -->
          <div class="tool-section">
            <el-button class="btn btn-full" @click="clearCanvas()">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
              Limpiar Todo
            </el-button>
          </div>
        </div>

        <!-- sidebar copsado -->
        <div class="sidebar-collapsed d-flex flex-column">
          <div class="sidebar-option" :class="{'selected' : isLeftSidebarOpen}">
            <div class="d-flex flex-column align-items-center justify-content-center" title="Abrir panel izquierdo" @click="toggleLeftSidebar">
              <div class="p-2 icon-container">
                <svg v-if="!isLeftSidebarOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-template"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1l0 -2" /><path d="M4 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1l0 -6" /><path d="M14 12l6 0" /><path d="M14 16l6 0" /><path d="M14 20l6 0" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-template"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 3a2 2 0 0 1 2 2v2a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-2a2 2 0 0 1 2 -2z" /><path d="M9 11a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-4a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2z" /><path d="M20 11a1 1 0 0 1 0 2h-6a1 1 0 0 1 0 -2z" /><path d="M20 15a1 1 0 0 1 0 2h-6a1 1 0 0 1 0 -2z" /><path d="M20 19a1 1 0 0 1 0 2h-6a1 1 0 0 1 0 -2z" /></svg>
              </div>
              Elementos
            </div>
          </div>
          <div class="sidebar-option" :class="{'selected' : isRightSidebarOpen}">
            <div class="d-flex flex-column align-items-center justify-content-center" title="Abrir panel derecho" @click="toggleRightSidebar">
              <div class="p-2 icon-container">
                <svg v-if="!isRightSidebarOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-layout-board-split"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" /><path d="M4 12h8" /><path d="M12 15h8" /><path d="M12 9h8" /><path d="M12 4v16" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-layout-board-split"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 3h5a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1v-5a2 2 0 0 1 2 -2" /><path d="M14 3h5a2 2 0 0 1 2 2v2a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" /><path d="M13 11a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z" /><path d="M14 16h6a1 1 0 0 1 1 1v2a2 2 0 0 1 -2 2h-5a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" /><path d="M4 13h6a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-5a2 2 0 0 1 -2 -2v-5a1 1 0 0 1 1 -1" /></svg>
              </div>
              Diseños
            </div>
          </div>
        </div>
  
        <!-- CANVAS -->
        <div
          class="canvas-container col-6"
          ref="canvasContainer"
          @click="handleCanvasContainerClick"
          @wheel.prevent="onCanvasWheel"
        >
          <div
            id="labelCanvas"
            ref="labelCanvas"
          ></div>
        </div>
  
        <!-- RIGHT SIDEBAR -->
        <div :class="['right-sidebar','col-3',{ 'mobile-open': isRightSidebarOpen }]">
          <div class="tool-section">
            <h4 class="mt-0 d-flex justify-content-between">
              Diseños Guardados

              <button class="bg-transparent border-0 btn-close-sidebar" @click="toggleRightSidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
              </button>
            </h4>
            <div class="input-group">
              <label>Nombre del diseño</label>
              <el-input
                type="text"
                v-model="templateName"
                placeholder="Ingresa un nombre para el diseño"
              />
            </div>
            <el-button type="primary" class="btn btn-primary btn-full" @click="saveCurrentTemplateAction">
              <svg v-if="selectTemplate === null" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
              
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-check me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /><path d="M15 19l2 2l4 -4" /></svg>

              {{ selectTemplate !== null ? 'Actualizar Diseño' : 'Guardar Diseño' }}
            </el-button>

            <el-button v-if="selectTemplate !== null" class="btn btn-secondary btn-full mt-2" @click="saveTemplate(null)">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy me-1" style="margin-top: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
              Guardar Nuevo
            </el-button>
  
            <div style="margin-top: 1rem;">
              <div
                v-for="(tpl, index) in templates"
                :key="tpl.timestamp"
                class="template-card"
                :data-template-index="index"
                @click="applyTemplate(index)"
                :class="{ select: index === selectTemplate, default: index === defaultTemplateIndex, 'default': tpl.is_default }"
              >

                <div class="template-name">
                  <span class="template-text me-2">
                    {{ tpl.name }}
                  </span>
                
                  <span v-if="tpl.is_default" class="default-badge">
                    POR DEFECTO
                  </span>
                </div>
                <div class="template-actions">
                  <button
                    class="btn-icon btn-icon-tags"
                    :class="{ default: tpl.is_default }"
                    @click.stop="isDefault(tpl.id)"
                    :title="index === defaultTemplateIndex ? 'Diseño predeterminado' : 'Establecer como predeterminado'"
                  >
                    <svg v-if="tpl.is_default" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-star"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg>

                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-star"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873l-6.158 -3.245" /></svg>
                  </button>
                  <button
                    class="btn-icon btn-icon-tags primary"
                    @click.stop="saveTemplate(tpl.id)"
                    title="Guardar diseño"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                  </button>
                  <button
                    class="btn-icon btn-icon-tags destructive"
                    @click.stop="deleteTemplate(tpl.id)"
                    title="Eliminar diseño"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                  </button>
                </div>
              </div>
            </div>
  
          </div>
        </div>
      </div>
    </div>
  </template>

  <style scoped>
  .select {
    border-color: hsl(142.1 76.2% 36.3%);
    background: hsl(142.1 76.2% 36.3% / 0.05);
  }
  .default-badge {
    background: hsl(142.1 76.2% 36.3%);
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    display: inline-block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .container-tag-editor .sidebar-collapsed{display:none !important}
  .container-tag-editor .settings-button,
  .container-tag-editor .btn-close-sidebar{
    display: none;
  }

  @media (max-width: 900px) {
    .container-tag-editor .sidebar, .container-tag-editor .right-sidebar{display:none !important}

    .container-tag-editor .sidebar-collapsed{
      display:flex !important;
      position:fixed;
      left:0;
      top:calc(50% + 60px);
      transform:translateY(-50%);
      width:100px;
      z-index:2;
      gap:0.5rem;
      padding:0.25rem;
      background:#fff;
      align-items:center;
    }
    .container-tag-editor .sidebar-collapsed .sidebar-option{cursor:pointer}

    .container-tag-editor .sidebar.mobile-open{
      display:block !important;
      position:fixed;
      left:100px;
      top:60px;
      bottom:20px;
      overflow:auto;
      z-index:1300;
      width:calc(90% - 90px);
      max-width: 355px;
      background:#fff;
      padding:0.75rem;
      height: calc(100% - 60px);
    }
    .container-tag-editor .right-sidebar.mobile-open{
      display:block !important;
      position:fixed;
      left:100px;
      top:60px;
      bottom:20px;
      right:20px;
      overflow:auto;
      z-index:1300;
      width:calc(90% - 90px);
      max-width: 355px;
      padding:0.75rem;
      background:#fff;
      height: calc(100% - 60px);
    }
    .container-tag-editor .header-center .dimensions-controls{
      display: none;
    }
    .container-tag-editor .settings-button,
    .container-tag-editor .btn-close-sidebar {
      display: block;
    }

    .settings-button{ position: relative; }
    .settings-dropdown{
      display: none;
      position: absolute;
      right: 50%;
      transform: translateX(50%);
      top: calc(100% + 8px);
      min-width: 280px;
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.12);
      padding: 0.75rem;
      z-index: 3;
    }
    .settings-dropdown .dropdown-title{ font-size: 0.9rem; margin: 0 0 0.5rem 0; }
    .settings-dropdown .small-input{ width: 70px; padding: 0.25rem; }
    .settings-dropdown .zoom-level{ font-weight:600; margin-right:0.5rem }

    .settings-dropdown{ display: block; }
    .container-tag-editor .dimension-controls{
      background-color: hsl(0 0% 98%);
      border: 1px solid hsl(0 0% 89.8%);
      border-radius: 0.5rem;
      padding: .5rem;
    }
    .container-tag-editor .canvas-container{
      margin-left: 100px;
    }
    .container-tag-editor .sidebar-option.selected .icon-container{
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      color: var(--success);
    }
  }
</style>
  
  <script>
  
  export default {
    name: 'LabelDesigner',
  
    data () {
      return {
        resource: 'item-editor-tag',
        // Tamaño etiqueta
        labelWidth: 100,
        labelHeight: 60,
  
        // Zoom
        currentZoom: 1,
        minZoom: 0.25,
        maxZoom: 3,
  
        // Drag/resize
        selectedField: null,
        isDragging: false,
        isResizing: false,
        startX: 0,
        startY: 0,
        startWidth: 0,
        startHeight: 0,
        startLeft: 0,
        startTop: 0,
        fieldCounter: 0,
        uploadedImages: {},
        isDirty: false,
  
        // Datos del sistema
        systemData: {
          internal_id: 'PROD001',
          name: 'Producto Ejemplo',
          barcode: '1234567890123',
          category: 'Electrónicos',
          unit_type: 'Piezas',
          brand: 'Marca Ejemplo',
          sale_unit_price: '$99.99',
          attribute_5013: 'M',
          attribute_5014: 'Azul, Rojo',
          status: 'Activo'
        },
        dataLabels: {
          internal_id: 'Código Interno',
          name: 'Nombre',
          barcode: 'Código de Barras',
          category: 'Categoría',
          unit_type: 'Unidad',
          brand: 'Marca',
          sale_unit_price: 'Precio',
          attribute_5013: 'Talla',
          attribute_5014: 'Colores',
          status: 'Status'
        },
  
        // Tags
        selectedTags: [],
        infoFields: [], // Información de los campos en el canvas
        infoCanva: {},
        tagSearch: '',
        tagSelectValue: null,
        selectTemplate:  null,
  
        // Propiedades del campo seleccionado
        showFieldProperties: false,
        fieldType: null,
        fieldContent: '',
        fontSize: 14,
        fontBold: false,
        textAlign: 'left',
        barcodeContent: '',
        barcodeFormat: 'CODE128',
        barcodeHeight: 50,
        barcodeDisplayValue: 'true',
        barcodeDisplayValueText: 'Sí',
        // Descarga
        downloadFormat: null,
        downloadPopoverVisible: false,
  
        // Plantillas
        templates: [],
        templateName: '',
        defaultTemplateIndex: null,
        isLeftSidebarOpen: false,
        isRightSidebarOpen: false,
        isSettingsDropdownOpen: false,
      }
    },
  
    computed: {
      zoomPercent () {
        return Math.round(this.currentZoom * 100)
      },
      showTitle () {
        // Oculta el título cuando se ejecuta dentro de un iframe (p. ej. diálogo) o cuando la ruta es /items
        const inIframe = (typeof window !== 'undefined') && window.self !== window.top
        if (inIframe) return false
        const path = (this.$route && this.$route.path) || (typeof window !== 'undefined' && window.location && window.location.pathname) || ''
        return path !== '/items'
      },
      availableTags () {
        return Object.keys(this.dataLabels).map(key => ({
          key,
          label: this.dataLabels[key],
          value: this.systemData[key]
        }))
      },
  
      filteredTags () {
        const term = (this.tagSearch || '').toLowerCase()
        return this.availableTags.filter(tag => {
          if (this.selectedTags.includes(tag.key)) return false
          if (!term) return true
          return (
            tag.label.toLowerCase().includes(term) ||
            String(tag.value).toLowerCase().includes(term)
          )
        })
      }
      ,
      canExport () {
        return (this.infoFields && this.infoFields.length > 0) || this.selectTemplate !== null
      }
    },
  
    mounted () {
    this.updateCanvasSize(false)
      this.initCanvasListeners()
      this.getRecords()
  
      // Cerrar dropdown de tags al hacer click fuera
      document.addEventListener('click', this.handleGlobalClick)
      document.addEventListener('click', this.handleSettingsClickOutside)
      document.addEventListener('mousemove', this.onMouseMove)
      document.addEventListener('mouseup', this.onMouseUp)
      document.addEventListener('keydown', this.onKeyDown)
      window.addEventListener('beforeunload', this.beforeUnloadHandler)
    },
  
    beforeDestroy () {
      document.removeEventListener('click', this.handleGlobalClick)
      document.removeEventListener('click', this.handleSettingsClickOutside)
      document.removeEventListener('mousemove', this.onMouseMove)
      document.removeEventListener('mouseup', this.onMouseUp)
      document.removeEventListener('keydown', this.onKeyDown)
      window.removeEventListener('beforeunload', this.beforeUnloadHandler)
    },
  
    methods: {
      handleSettingsClickOutside (e) {
        try {
          const btn = this.$refs.settingsButton
          if (!btn) return
          if (!btn.contains(e.target)) {
            this.isSettingsDropdownOpen = false
          }
        } catch (err) {}
      },

      toggleSettingsDropdown () {
        this.isSettingsDropdownOpen = !this.isSettingsDropdownOpen
        if (this.isSettingsDropdownOpen) {
          if (typeof window !== 'undefined' && window.innerWidth <= 900) {
            this.isLeftSidebarOpen = false
            this.isRightSidebarOpen = false
          }
        }
      },

      async getRecords()
      {
        await this.$http.get(`${this.resource}/records`)
          .then(response => {
            this.templates = response.data.templates;
          })
          .catch(error => {
            console.error('Error fetching records:', error);
          });
      },
      mmToPx () {
        return 3.7795275591
      },
  
      updateCanvasSize (markDirty = true) {
        if (markDirty) this.isDirty = true
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
        const baseWidth = this.labelWidth * this.mmToPx()
        const baseHeight = this.labelHeight * this.mmToPx()
        canvas.style.width = baseWidth + 'px'
        canvas.style.height = baseHeight + 'px'
        canvas.style.transformOrigin = 'center center'
        canvas.style.transform = `scale(${this.currentZoom})`
      },
  
      changeZoom (delta) {
        const newZoom = Math.max(
          this.minZoom,
          Math.min(this.maxZoom, this.currentZoom + delta)
        )
        if (newZoom !== this.currentZoom) {
          this.currentZoom = newZoom
          this.updateCanvasSize(false)
        }
      },
  
      resetZoom () {
        this.currentZoom = 1
        this.updateCanvasSize(false)
      },
  
      onCanvasWheel (e) {
        const delta = e.deltaY > 0 ? -0.1 : 0.1
        this.changeZoom(delta)
      },
  
      // ---- TAGS ----
      handleGlobalClick (e) {
        if (!e.target.closest('.tag-selector')) {
          this.showTagDropdown = false
        }
      },
  
      selectTag (key) {
        
        if (!this.selectedTags.includes(key)) {
          this.selectedTags.push(key)
        }
        this.tagSearch = ''
        this.showTagDropdown = true


        this.addField(key === 'barcode' ? 'barcode' : 'text', key)
      },
  
      removeTag (key) {
        const canvas = this.$refs.labelCanvas
        if (canvas) {
          const fields = Array.from(canvas.querySelectorAll('.field'))
          fields.forEach(f => {
            if (f.dataset.systemData === key) {
              this.removeFieldByElement(f)
            }
          })
        }
        this.selectedTags = this.selectedTags.filter(k => k !== key)
      },

      handleTagSelect (key) {
        if (!key) return
        this.selectTag(key)
        this.tagSelectValue = null
      },

      handleTagFilter (query) {
        this.tagSearch = query
      },
  
      selectFirstVisibleTag () {
        if (!this.filteredTags.length) return
        this.selectTag(this.filteredTags[0].key)
      },

      toggleLeftSidebar () {
        this.isLeftSidebarOpen = !this.isLeftSidebarOpen
        if (this.isLeftSidebarOpen) this.isRightSidebarOpen = false
      },

      toggleRightSidebar () {
        this.isRightSidebarOpen = !this.isRightSidebarOpen
        if (this.isRightSidebarOpen) this.isLeftSidebarOpen = false
      },
  
      addSystemFields () {
        this.selectedTags.forEach(key => {
          // Texto por cada tag
          this.addField('text', key)
        })
      },
  
      // ---- CAMPOS ----
      initCanvasListeners () {
        const container = this.$refs.canvasContainer
        if (!container) return
        // resto de listeners globales ya están en mounted()
      },
  
      handleCanvasContainerClick (e) {
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
        if (e.target === this.$refs.canvasContainer || e.target === canvas) {
          if (this.selectedField) {
            this.selectedField.classList.remove('selected')
            this.selectedField = null
            this.showFieldProperties = false
          }

          try {
            if (window && window.innerWidth && window.innerWidth <= 900) {
              this.isLeftSidebarOpen = false
              this.isRightSidebarOpen = false
            }
          } catch (e) {}
        }
      },
  
      addField (type, systemDataKey = null) {
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
  
        const field = document.createElement('div')
        let fieldId   = 'field_' + (++this.fieldCounter)
        field.className = 'field'
        field.id  = fieldId
        field.dataset.type = type
        if (systemDataKey) {
          field.dataset.systemData = systemDataKey
        }
  
        field.style.left = '20px'
        field.style.top = '20px'
        field.style.width = type === 'image' ? '100px' : '150px'
        field.style.height = type === 'image' ? '100px' : '40px'

        
        let infoField = {
          id: fieldId,
          type: '', 
          x:  field.style.left,
          y:  field.style.top,
          width: field.style.width,
          height: field.style.height,
        }

  
        const content = document.createElement('div')
        content.className = 'field-content'
  
        if (type === 'text') {
          const textValue = systemDataKey
            ? (this.systemData[systemDataKey] || '')
            : 'Texto de ejemplo'

            infoField.type = 'text';
          content.textContent = textValue
          content.style.fontSize = '14px'
          content.style.color = '#000000'
          content.style.fontWeight = 'normal'
          content.style.textAlign = 'left'
        } else if (type === 'barcode') {
          const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
          svg.setAttribute('id', 'barcode_' + this.fieldCounter)
          content.appendChild(svg)
          field.style.height = '80px'
          field.style.width = '200px'

          infoField.type = 'barcode';
  
          const barcodeValue =
            systemDataKey === 'barcode'
              ? this.systemData.barcode
              : '123456789012'

  
          field.dataset.barcodeValue = barcodeValue
          field.dataset.barcodeFormat = 'CODE128'
          field.dataset.barcodeHeight = '50'
          field.dataset.barcodeDisplayValue = 'true'
  
          setTimeout(() => {
            try {
              if (window.JsBarcode) {
                window.JsBarcode(svg, barcodeValue, {
                  format: 'CODE128',
                  height: 50,
                  displayValue: true,
                  fontSize: 14,
                  margin: 5
                })
              }
            } catch (e) {
              console.error('Error generando código de barras:', e)
            }
          }, 100)
        }
  
        // Botón borrar
        const deleteBtn = document.createElement('button')
        deleteBtn.className = 'delete-btn'
        deleteBtn.textContent = '×'
        deleteBtn.onclick = ev => {
          ev.stopPropagation()
          this.confirmRemoveField(field)
        }
  
        // Resize handle
        const resizeHandle = document.createElement('div')
        resizeHandle.className = 'resize-handle'
  
        // Badge
        const badge = document.createElement('div')
        badge.className = 'field-badge'
        const typeLabels = {
          text: systemDataKey ? this.dataLabels[systemDataKey] : 'Texto',
          barcode: systemDataKey ? this.dataLabels[systemDataKey] : 'Código'
        }
        badge.textContent = typeLabels[type] || type
  
        field.appendChild(content)
        field.appendChild(badge)
        field.appendChild(deleteBtn)
        field.appendChild(resizeHandle)
        canvas.appendChild(field)
  
        field.addEventListener('mousedown', e => {
          if (e.target === resizeHandle) {
            this.startResize(e, field)
          } else if (e.target === field || e.target === content) {
            this.startDrag(e, field)
          }
        })
  
        this.infoFields.push(infoField);
        this.selectField(field)
        this.isDirty = true
        
      },
  
      handleImageUpload (event) {
        const file = event.target.files[0]
        if (!file) return
  
          const canvas = this.$refs.labelCanvas
          if (!canvas) return
  
          const field = document.createElement('div')
          field.className = 'field'
          field.id = 'field_' + (++this.fieldCounter)
          field.dataset.type = 'image'
  
          field.style.left = '20px'
          field.style.top = '20px'
          field.style.width = '100px'
          field.style.height = '100px'
  
          const content = document.createElement('div')
          content.className = 'field-content'
  
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          content.appendChild(img)
          this.uploadedImages[field.id] = file 
          const deleteBtn = document.createElement('button')
          deleteBtn.className = 'delete-btn'
          deleteBtn.textContent = '×'
          deleteBtn.onclick = ev => {
            ev.stopPropagation()
            this.confirmRemoveField(field)
          }
  
          const resizeHandle = document.createElement('div')
          resizeHandle.className = 'resize-handle'
  
          const badge = document.createElement('div')
          badge.className = 'field-badge'
          badge.textContent = 'Imagen'
  
          field.appendChild(content)
          field.appendChild(badge)
          field.appendChild(deleteBtn)
          field.appendChild(resizeHandle)
          canvas.appendChild(field)
  
          field.addEventListener('mousedown', ev => {
            if (ev.target === resizeHandle) {
              this.startResize(ev, field)
            } else if (!ev.target.classList.contains('delete-btn')) {
              this.startDrag(ev, field)
            }
          })
  
          this.selectField(field)
          this.isDirty = true
          const infoField = {
            id: field.id,
            type: 'image',
            x: field.style.left,
            y: field.style.top,
            width: field.style.width,
            height: field.style.height
          }
          this.infoFields.push(infoField)
        event.target.value = ''
      },
  
      startDrag (e, field) {
        if (e.target.classList.contains('delete-btn')) return
        this.isDragging = true
        this.selectField(field)
  
        this.startX = e.clientX
        this.startY = e.clientY
        this.startLeft = field.offsetLeft
        this.startTop = field.offsetTop
        e.preventDefault()
      },
  
      startResize (e, field) {
        this.isResizing = true
        this.selectField(field)
  
        this.startX = e.clientX
        this.startY = e.clientY
        this.startWidth = field.offsetWidth
        this.startHeight = field.offsetHeight
        e.preventDefault()
        e.stopPropagation()
      },
  
      onMouseMove (e) {
        if (!this.selectedField) return
  
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
  
        const baseWidth = this.labelWidth * this.mmToPx()
        const baseHeight = this.labelHeight * this.mmToPx()
  
        // WIDTH
        let indexField = this.infoFields.findIndex(f => f.id === this.selectedField.id);
        if (this.isDragging) {


          
            
          const dx = (e.clientX - this.startX) / this.currentZoom
          const dy = (e.clientY - this.startY) / this.currentZoom
  
          const newLeft = Math.max(
            0,
            Math.min(this.startLeft + dx, baseWidth - this.selectedField.offsetWidth)
          )
          const newTop = Math.max(
            0,
            Math.min(this.startTop + dy, baseHeight - this.selectedField.offsetHeight)
          )
          if (indexField !== -1) {
              this.infoFields[indexField].x = newLeft + 'px';
              this.infoFields[indexField].y = newTop + 'px';
          }
  
          this.selectedField.style.left = newLeft + 'px'
          this.selectedField.style.top = newTop + 'px'
          
        }
  
        if (this.isResizing) {
          const dx = (e.clientX - this.startX) / this.currentZoom
          const dy = (e.clientY - this.startY) / this.currentZoom
  
          const newWidth = Math.max(50, this.startWidth + dx)
          const newHeight = Math.max(20, this.startHeight + dy)
  
          this.selectedField.style.width = newWidth + 'px'
          this.selectedField.style.height = newHeight + 'px'

          if (indexField !== -1) {
              this.infoFields[indexField].width = newWidth + 'px';
              this.infoFields[indexField].height = newHeight + 'px';
          }

        }
      },
  
      onMouseUp () {
        const wasDragging = this.isDragging
        const wasResizing = this.isResizing
        this.isDragging = false
        this.isResizing = false
        if (wasDragging || wasResizing) this.isDirty = true
      },
  
      onKeyDown (e) {
        if ((e.ctrlKey || e.metaKey) && this.$refs.canvasContainer) {
          if (e.key === '+' || e.key === '=') {
            e.preventDefault()
            this.changeZoom(0.1)
          } else if (e.key === '-') {
            e.preventDefault()
            this.changeZoom(-0.1)
          } else if (e.key === '0') {
            e.preventDefault()
            this.resetZoom()
          }
        }
      },
  
      selectField (field) {
        const allFields = this.$refs.labelCanvas
          ? this.$refs.labelCanvas.querySelectorAll('.field')
          : []
        Array.from(allFields).forEach(f => f.classList.remove('selected'))
  
        field.classList.add('selected')
        this.selectedField = field
        this.showFieldProperties = true
  
        const content = field.querySelector('.field-content')
        const type = field.dataset.type
        this.fieldType = type
  
        if (type === 'image') {
          
          // nada especial
          return
        }
  
        if (type === 'barcode') {
          const value =
            field.dataset.barcodeValue ||
            this.systemData[field.dataset.systemData] ||
            '123456789012'
          this.barcodeContent = value
          this.barcodeFormat = field.dataset.barcodeFormat || 'CODE128'
          this.barcodeHeight = parseInt(field.dataset.barcodeHeight || '50')
          this.barcodeDisplayValue = field.dataset.barcodeDisplayValue || 'true'
          return
        }
  
        // Texto
        this.fieldContent = content.textContent || ''
        this.fontSize = parseInt(content.style.fontSize || '14')
        this.textAlign = content.style.textAlign || 'left'
        this.fontBold = content.style.fontWeight === 'bold'
      },
  
      toggleFontWeight () {
        if (!this.selectedField || this.fieldType !== 'text') return
        this.fontBold = !this.fontBold
        const content = this.selectedField.querySelector('.field-content')
        content.style.fontWeight = this.fontBold ? 'bold' : 'normal'
      },
  
      setAlignment (align) {
        if (!this.selectedField || this.fieldType !== 'text') return
        this.textAlign = align
        const content = this.selectedField.querySelector('.field-content')
        content.style.textAlign = align
      },
  
      updateFieldContent () {
        if (!this.selectedField || this.fieldType === 'image') return
        const content = this.selectedField.querySelector('.field-content')
        content.textContent = this.fieldContent
  
        if (this.selectedField.dataset.systemData) {
          this.systemData[this.selectedField.dataset.systemData] = this.fieldContent
        }
        this.isDirty = true
      },
  
      updateFieldStyle () {
        if (!this.selectedField || this.fieldType !== 'text') return
        const content = this.selectedField.querySelector('.field-content')
        content.style.fontSize = (this.fontSize || 14) + 'px'
        this.isDirty = true
      },

      removeFieldByElement(field) {
        if (!field) return

        const canvas = this.$refs.labelCanvas
        const systemKey = field.dataset.systemData

        // remove uploaded image reference if any
        if (field.dataset.type === 'image') {
          delete this.uploadedImages[field.id]
        }

        // remove DOM element
        try {
          field.remove()
        } catch (e) {
          // ignore
        }

        // remove from infoFields
        const idx = this.infoFields.findIndex(f => f.id === field.id)
        if (idx !== -1) this.infoFields.splice(idx, 1)

        this.isDirty = true

        // if no other field exists for this systemKey, remove the selected tag
        if (systemKey && canvas) {
          const remaining = canvas.querySelectorAll(`.field[data-system-data="${systemKey}"]`)
          if (remaining.length === 0) {
            this.selectedTags = this.selectedTags.filter(k => k !== systemKey)
          }
        }

        if (this.selectedField === field) {
          this.selectedField = null
          this.showFieldProperties = false
        }
      },

      confirmRemoveField(field) {
        if (!field) return
        this.$confirm('¿Estás seguro de eliminar este campo? Esta acción no se puede deshacer.', 'Confirmar', {
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          type: 'warning'
        }).then(() => {
          this.removeFieldByElement(field)
        }).catch(() => {})
      },
  
      deleteSelectedField () {
        if (!this.selectedField) return
        this.$confirm('¿Estás seguro de eliminar este campo? Esta acción no se puede deshacer.', 'Confirmar', {
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          type: 'warning'
        }).then(() => {
          this.removeFieldByElement(this.selectedField)
        }).catch(() => {})
      },
  
      clearCanvas (skipConfirm = false) {
        const doClear = () => {
          const canvas = this.$refs.labelCanvas
          if (canvas) canvas.innerHTML = ''
          this.uploadedImages = {}
          this.selectedField = null
          this.fieldCounter = 0
          this.selectedTags = []
          this.showFieldProperties = false
          this.infoFields = []
        }

        if (skipConfirm) {
          doClear()
          return
        }

        this.$confirm('¿Estás seguro de limpiar todo? Se perderán los cambios no guardados.', 'Confirmar', {
          confirmButtonText: 'Limpiar',
          cancelButtonText: 'Cancelar',
          type: 'warning'
        }).then(() => {
          doClear()
        }).catch(() => {})
      },
  
      // ---- BARRAS ----
      handleBarcodeInput () {
        // Solo números
        this.barcodeContent = this.barcodeContent.replace(/[^0-9]/g, '')
        this.updateBarcode()
      },
  
      updateBarcode () {
        if (!this.selectedField || this.fieldType !== 'barcode') return
        let value = this.barcodeContent
        const format = this.barcodeFormat
        const height = this.barcodeHeight
        const displayValue = this.barcodeDisplayValue === 'true'

        this.barcodeDisplayValueText = this.barcodeDisplayValue === 'true' ? 'Sí' : 'No'

        if (!value) return

        let isValid = true
        let errorMsg = ''

        switch (format) {
          case 'EAN13':
            if (value.length !== 13 && value.length !== 12) {
              isValid = false
              errorMsg = 'EAN13 requiere 12 o 13 dígitos'
            }
            break
          case 'EAN8':
            if (value.length !== 8 && value.length !== 7) {
              isValid = false
              errorMsg = 'EAN8 requiere 7 u 8 dígitos'
            }
            break
          case 'UPC':
            if (value.length !== 12 && value.length !== 11) {
              isValid = false
              errorMsg = 'UPC requiere 11 o 12 dígitos'
            }
            break
        }

        if (!isValid) {
          alert(errorMsg)
          return
        }

        const svg = this.selectedField.querySelector('svg')
        if (svg && window.JsBarcode) {
          try {
            window.JsBarcode(svg, value, {
              format,
              height,
              displayValue,
              fontSize: 14,
              margin: 5,
              width: 2
            })
            this.selectedField.dataset.barcodeValue = value
            this.selectedField.dataset.barcodeFormat = format
            this.selectedField.dataset.barcodeHeight = height
            this.selectedField.dataset.barcodeDisplayValue = displayValue
          } catch (e) {
            console.error('Error generando código de barras:', e)
            alert('Error al generar código de barras: ' + e.message)
          }
        }
      },
  
      updateBarcodeStyle () {
        this.updateBarcode()
      },

      beforeUnloadHandler (e) {
        if (this.isDirty) {
          const msg = 'Tienes una etiqueta en construcción. Si sales, se perderán los cambios.'
          e.preventDefault()
          e.returnValue = msg
          return msg
        }

        const hasFields = this.infoFields && this.infoFields.length > 0
        if (hasFields && this.selectTemplate === null) {
          const msg = 'Tienes una etiqueta en construcción. Si sales, se perderán los cambios.'
          e.preventDefault()
          e.returnValue = msg
          return msg
        }

        return undefined
      },
  
      // ---- EXPORTAR ----
      async downloadAsImage () {
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
        if (!window.html2canvas) {
          alert('html2canvas no está disponible')
          return
        }
  
        const originalTransform = canvas.style.transform
        canvas.style.transform = 'scale(1)'
  
        const fields = canvas.querySelectorAll('.field')
        fields.forEach(field => {
          field.style.border = 'none'
          field.style.background = 'transparent'
          field.style.boxShadow = 'none'
          const handle = field.querySelector('.resize-handle')
          const btn = field.querySelector('.delete-btn')
          const badge = field.querySelector('.field-badge')
          if (handle) handle.style.display = 'none'
          if (btn) btn.style.display = 'none'
          if (badge) badge.style.display = 'none'
        })
  
        try {
            const canvasImg = await window.html2canvas(canvas, {
            scale: 3,
            backgroundColor: '#ffffff',
            logging: false,
            useCORS: true,
            allowTaint: true,
            onclone: (clonedDoc) => {
              try {
                const links = clonedDoc.querySelectorAll('link[rel="stylesheet"]')
                links.forEach(l => {
                  try {
                    if (l.href && l.href.includes('black.css')) l.remove()
                  } catch (e) {}
                })

                const styles = clonedDoc.querySelectorAll('style')
                styles.forEach(s => {
                  try {
                    if (s.textContent && s.textContent.includes('oklch')) s.remove()
                  } catch (e) {}
                })
              } catch (e) {}
            }
          })
  
          canvasImg.toBlob(blob => {
            const url = URL.createObjectURL(blob)
            const link = document.createElement('a')
            link.download = 'etiqueta_' + Date.now() + '.png'
            link.href = url
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
            URL.revokeObjectURL(url)
  
            this.restoreEditingElements(fields)
            canvas.style.transform = originalTransform
          }, 'image/png')
        } catch (error) {
          console.error('Error al generar imagen:', error)
          alert('Error al generar la imagen: ' + error.message)
          this.restoreEditingElements(fields)
          canvas.style.transform = originalTransform
        }
      },
  
      async downloadAsPDF () {
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
        if (!window.html2canvas) {
          alert('html2canvas no está disponible')
          return
        }
        if (!window.jspdf || !window.jspdf.jsPDF) {
          alert('jsPDF no está disponible')
          return
        }
  
        const originalTransform = canvas.style.transform
        canvas.style.transform = 'scale(1)'
  
        const fields = canvas.querySelectorAll('.field')
        fields.forEach(field => {
          field.style.border = 'none'
          field.style.background = 'transparent'
          field.style.boxShadow = 'none'
          const handle = field.querySelector('.resize-handle')
          const btn = field.querySelector('.delete-btn')
          const badge = field.querySelector('.field-badge')
          if (handle) handle.style.display = 'none'
          if (btn) btn.style.display = 'none'
          if (badge) badge.style.display = 'none'
        })
  
        try {
          const canvasImg = await window.html2canvas(canvas, {
            scale: 3,
            backgroundColor: '#ffffff',
            logging: false,
            useCORS: true,
            allowTaint: true,
            onclone: (clonedDoc) => {
              try {
                const links = clonedDoc.querySelectorAll('link[rel="stylesheet"]')
                links.forEach(l => {
                  try {
                    if (l.href && l.href.includes('black.css')) l.remove()
                  } catch (e) {}
                })
                const styles = clonedDoc.querySelectorAll('style')
                styles.forEach(s => {
                  try {
                    if (s.textContent && s.textContent.includes('oklch')) s.remove()
                  } catch (e) {}
                })
              } catch (e) {}
            }
          })
  
          const imgData = canvasImg.toDataURL('image/png')
          const { jsPDF } = window.jspdf
          const width = this.labelWidth
          const height = this.labelHeight
  
          const pdf = new jsPDF({
            orientation: width > height ? 'landscape' : 'portrait',
            unit: 'mm',
            format: [width, height]
          })
  
          pdf.addImage(imgData, 'PNG', 0, 0, width, height)
          pdf.save('etiqueta_' + Date.now() + '.pdf')
  
          this.restoreEditingElements(fields)
          canvas.style.transform = originalTransform
        } catch (error) {
          console.error('Error al generar PDF:', error)
          alert('Error al generar el PDF: ' + error.message)
          this.restoreEditingElements(fields)
          canvas.style.transform = originalTransform
        }
      },

      async downloadFromDropdown () {
        if (!this.downloadFormat) return
        this.downloadPopoverVisible = false
        if (this.downloadFormat === 'png') {
          await this.downloadAsImage()
        } else if (this.downloadFormat === 'pdf') {
          await this.downloadAsPDF()
        }
      },
  
      restoreEditingElements (fields) {
        fields.forEach(field => {
          field.style.border = ''
          field.style.background = ''
          field.style.boxShadow = ''
          const handle = field.querySelector('.resize-handle')
          const btn = field.querySelector('.delete-btn')
          const badge = field.querySelector('.field-badge')
          if (handle) handle.style.display = ''
          if (btn && field.classList.contains('selected')) btn.style.display = ''
          if (badge) badge.style.display = ''
        })
      },
  
      // ---- PLANTILLAS ----
      async saveTemplate (id = null) {
        const name = (this.templateName || '').trim()
        if (!name && !id) {
          this.$message.warning('Por favor ingresa un nombre para el diseño')
          return
        }

        if (!id) {
          const exists = (this.templates || []).some(t => (t.name || '').trim().toLowerCase() === name.toLowerCase())
          if (exists) {
            this.$message.warning('Ya existe una plantilla con ese nombre')
            return
          }
        } else {
          const conflict = (this.templates || []).some(t => t.id !== id && (t.name || '').trim().toLowerCase() === name.toLowerCase())
          if (conflict) {
            this.$message.warning('Otro diseño ya utiliza ese nombre')
            return
          }
        }
  
        const canvas = this.$refs.labelCanvas
        if (!canvas) return
  
        const fields = Array.from(canvas.querySelectorAll('.field')).map(field => {
          
          const content = field.querySelector('.field-content')
          const template = {
            b_id: field.dataset.bId || null,
            html_id: field.id,
            type: field.dataset.type,
            systemData: field.dataset.systemData || null,
            position: {
              left: field.style.left,
              top: field.style.top,
              width: field.style.width,
              height: field.style.height
            },
            has_image: false,
            content: {
              text: content.textContent,
              fontSize: content.style.fontSize,
              fontWeight: content.style.fontWeight,
              textAlign: content.style.textAlign,
              color: content.style.color
            }
          }
  
          if (field.dataset.type === 'barcode') {
            template.barcode = {
              value: field.dataset.barcodeValue,
              format: field.dataset.barcodeFormat,
              height: field.dataset.barcodeHeight,
              displayValue: field.dataset.barcodeDisplayValue
            }
          }
  
            let d_has_image = Boolean(field.dataset.hasImage);
            let d_path = field.dataset.path || '';
            if (id && d_has_image && d_path ) {
              template.systemData = "image";
              template.has_image = true;
              template.path = d_path            
            }
          if (field.dataset.type === 'image' && this.uploadedImages[field.id]) {
            
              template.systemData = "image";
              template.has_image = true;
          }
  
          return template
        })
  
        const templateData = {
          name,
          timestamp: Date.now(),
          canvas: {
            width: this.labelWidth,
            height: this.labelHeight
          },
          fields
        }
        
        if (id) {
          await this.$http.post('item-editor-tag\\tags\\update\\' + id, templateData)
            .then(async (response) => {
              if (response.data.success) {
                await this.saveImages(response.data.fields_image || []);
                await this.getRecords();
                this.$message.success(response.data.message || 'Plantilla actualizada');
                const idx = (this.templates || []).findIndex(t => t.id === id)
                if (idx !== -1) {
                  this.selectTemplate = idx
                  this.templateName = this.templates[idx].name || name
                } else {
                  this.selectTemplate = null
                }
                this.isDirty = false
              }
            })
            .catch(err => {
              console.error('Error updating template:', err)
            })
        } else {
          await this.$http.post('item-editor-tag\\tags\\save', templateData)
            .then(async (response) => {
              if (response.data.success) {
                await this.saveImages(response.data.fields_image || []);
                await this.getRecords();
                this.$message.success(response.data.message || 'Plantilla guardada');
                const idx = (this.templates || []).findIndex(t => (t.name || '').trim().toLowerCase() === name.toLowerCase())
                if (idx !== -1) {
                  this.selectTemplate = idx
                  this.templateName = this.templates[idx].name || name
                } else {
                  this.selectTemplate = null
                }
                this.isDirty = false
              }
            })
            .catch(err => {
              console.error('Error saving template:', err)
            })
        }
      },
      async saveImages(f_images){
        let promises = f_images.map(async (f )=> {
          let image = this.uploadedImages[f.html_id];
          
          let fmdata = new FormData();

          fmdata.append('id', f.id);
          fmdata.append('image', image);

          return this.$http.post('item-editor-tag\\tags\\save-image',fmdata )
        })
        await Promise.all(promises);
      },

      applyTemplate (index) {

        if (this.selectTemplate === index) {
          if (this.isDirty) {
            this.$confirm('Se perderán los cambios no guardados. ¿Deseas continuar?', 'Confirmar', {
              confirmButtonText: 'Desechar cambios',
              cancelButtonText: 'Cancelar',
              type: 'warning'
            }).then(() => {
              this.clearCanvas(true)
              this.selectTemplate = null
              this.templateName = ''
              this.isDirty = false
            }).catch(() => {
              
            })
            return
          }
          this.clearCanvas(true)
          this.selectTemplate = null
          this.templateName = ''
          return
        }

        // Si no hay plantilla seleccionada actualmente pero ya existen campos
        const canvas = this.$refs.labelCanvas
        const hasFields = canvas && canvas.querySelectorAll('.field').length > 0
        if (this.selectTemplate === null && hasFields) {
          this.$confirm('Se perderán los cambios no guardados. ¿Deseas continuar?', 'Confirmar', {
            confirmButtonText: 'Desechar cambios',
            cancelButtonText: 'Cancelar',
            type: 'warning'
          }).then(() => {
            this.proceedToApply(index)
          }).catch(() => {})
          return
        }

        if (this.selectTemplate !== null && this.selectTemplate !== index && this.isDirty) {
          this.$confirm('Se perderán los cambios no guardados. ¿Deseas continuar?', 'Confirmar', {
            confirmButtonText: 'Desechar cambios',
            cancelButtonText: 'Cancelar',
            type: 'warning'
          }).then(() => {
            this.proceedToApply(index)
          }).catch(() => {
            
          })
          return
        }

        this.proceedToApply(index)
      },

      proceedToApply (index) {
        this.selectedTags = []
        const template = this.templates[index]

        if (!template) return

        this.labelWidth = parseFloat(template.canvas.width || 100)
        this.labelHeight = parseFloat(template.canvas.height || 60)
        this.updateCanvasSize(false)

        const canvas = this.$refs.labelCanvas
        if (!canvas) return
        canvas.innerHTML = ''
        this.uploadedImages = {}
        this.selectedField = null
        this.fieldCounter = 0
        this.infoFields = []

        template.fields.forEach((fieldData, index) => {
          const field = document.createElement('div')
          field.className = 'field'
          field.dataset.bId = fieldData.id
          field.id = 'field_' + index
          field.dataset.type = fieldData.type
          if (fieldData.systemData) {
            field.dataset.systemData = fieldData.systemData
          }

          field.style.left = fieldData.position.left
          field.style.top = fieldData.position.top
          field.style.width = fieldData.position.width
          field.style.height = fieldData.position.height

          const content = document.createElement('div')
          content.className = 'field-content'
          if (!this.selectedTags.includes(fieldData.systemData) && fieldData.systemData !== 'image') {
            this.selectedTags.push(fieldData.systemData)
          }
          
          if (fieldData.type === 'text') {
            content.textContent = fieldData.content.text
            content.style.fontSize = fieldData.content.fontSize || '14px'
            content.style.fontWeight = fieldData.content.fontWeight || 'normal'
            content.style.textAlign = fieldData.content.textAlign || 'left'
            content.style.color = fieldData.content.color || '#000'
          } else if (fieldData.type === 'barcode' && fieldData.barcode) {
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg')
            content.appendChild(svg)

            field.dataset.barcodeValue = fieldData.barcode.value
            field.dataset.barcodeFormat = fieldData.barcode.format
            field.dataset.barcodeHeight = fieldData.barcode.height
            field.dataset.barcodeDisplayValue = fieldData.barcode.displayValue
            setTimeout(() => {
              try {
                if (window.JsBarcode) {
                  window.JsBarcode(svg, fieldData.barcode.value, {
                    format: fieldData.barcode.format,
                    height: parseInt(fieldData.barcode.height || 50),
                    displayValue: fieldData.barcode.displayValue === 'true' || fieldData.barcode.displayValue === true,
                    fontSize: 14,
                    margin: 5
                  })
                }
              } catch (e) {
                console.error('Error generando código de barras:', e)
              }
            }, 100)
          } else if (fieldData.type === 'image' && fieldData.image) {
            const img = document.createElement('img')
            field.dataset.hasImage = fieldData.has_image
            field.dataset.path = fieldData.path || ''
            img.src = fieldData.image
            content.appendChild(img)
          }

          const deleteBtn = document.createElement('button')
          deleteBtn.className = 'delete-btn'
          deleteBtn.textContent = '×'
          deleteBtn.onclick = e => {
            e.stopPropagation()
            this.confirmRemoveField(field)
          }

          const resizeHandle = document.createElement('div')
          resizeHandle.className = 'resize-handle'

          const badge = document.createElement('div')
          badge.className = 'field-badge'
          const typeLabels = {
            text: fieldData.systemData ? this.dataLabels[fieldData.systemData] : 'Texto',
            barcode: 'Código',
            image: 'Imagen'
          }
          badge.textContent = typeLabels[fieldData.type] || fieldData.type

          field.appendChild(content)
          field.appendChild(badge)
          field.appendChild(deleteBtn)
          field.appendChild(resizeHandle)
          canvas.appendChild(field)

          field.addEventListener('mousedown', e => {
            if (e.target === resizeHandle) {
              this.startResize(e, field)
            } else if (e.target === field || e.target === content) {
              this.startDrag(e, field)
            }
          })

          const fieldNumber = parseInt(field.id.split('_')[1]) || 0
          if (fieldNumber >= this.fieldCounter) {
            this.fieldCounter = fieldNumber
          }

          this.infoFields.push({
            id: field.id,
            type: fieldData.type,
            x: field.style.left,
            y: field.style.top,
            width: field.style.width,
            height: field.style.height
          })
        })

        this.templateName = template.name || ''
        this.selectTemplate = index

        this.isDirty = false
      },

      saveCurrentTemplateAction () {
        if (this.selectTemplate === null) {
          
          this.saveTemplate(null)
          return
        }

        const tpl = this.templates[this.selectTemplate]
        if (!tpl || !tpl.id) {
          
          this.saveTemplate(null)
          return
        }

        this.saveTemplate(tpl.id)
      },
  
      deleteTemplate (id) {
        this.$confirm('¿Estás seguro de eliminar este diseño? Esta acción no se puede deshacer.', 'Confirmar', {
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          type: 'warning'
        }).then(() => {
          this.$http.get(`${this.resource}/tags/delete/${id}`)
            .then(response => {
              if (response.data.success) {
                this.$message.success(response.data.message);
                
                if (this.selectTemplate !== null) {
                  const tpl = this.templates[this.selectTemplate]
                  if (tpl && tpl.id === id) {
                    this.clearCanvas(true)
                    this.selectTemplate = null
                  }
                }
                this.getRecords();
              }
            })
            .catch(error => {
              console.error('Error al eliminar la plantilla:', error);
            });
        }).catch(() => {

        });
      },
      isDefault(id) {
        this.$http.get(`${this.resource}/tags/default/${id}`)
          .then(response => {
            if (response.data.success) {
              this.$message.success(response.data.message);
              this.getRecords();
            }
          })
          .catch(error => {
          });

      },
      setDefaultTemplate (index) {
        this.defaultTemplateIndex = index
        // Solo visual; si quieres persistir, guarda este índice en localStorage
      }
    }
  }
  </script>
  