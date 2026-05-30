<template>
    <div>
        <div id="styleSwitcher" class="style-switcher" style="z-index: 1040;">
            <!-- <a id="styleSwitcherOpen" class="style-switcher-open" href="#">
                <i class="fas fa-paint-brush"></i>
            </a> -->

            <form class="style-switcher-wrap p-0" autocomplete="off">
            <div class="support-header px-3">
                <h5 class="m-0 d-flex align-items-center title-visual">
                    Estilos y Temas                    
                </h5>
                <a class="style-switcher-open close-config" href="#" style="transform: none;">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                </a>
            </div>

            <div v-if="visual == null">
                <h5 class="">No posee ajustes actualmente</h5>
                <a href="" class="text-warning" v-if="typeUser != 'integrator'"
                    >cargar ajustes por defecto</a
                >
                <br />
            </div>
            <div v-if="typeUser != 'integrator'" class="p-3">
                <div class="visual-bg-container" style="background-color: #283046;">
                    <a
                        v-if="visuals.bg == 'white'"
                        href="/configurations/change-mode"
                        class="notification-icon btn btn-dark btn-sm btn-block w-100"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Modo oscuro"
                        style="text-decoration: none; color: #fff !important;"
                    >
                        <i class="fas fa-moon"></i> Modo oscuro
                    </a>
                    <a
                        v-if="visuals.bg == 'dark'"
                        href="/configurations/change-mode"
                        class="notification-icon btn btn-light btn-sm btn-block btn-light-mode w-100"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Modo día"
                        style="text-decoration: none;"
                    >
                        <i class="fas fa-sun"></i> Modo Claro
                    </a>
                </div>
                <!-- <div class="pt-3">
                    <h5>Color de fondo del sidebar</h5>
                    <div class="form-group el-custom-control">
                        <button :class="{ 'active': visuals.sidebar_theme === 'white' }" type="button" @click="onChangeBgSidebar('white')" class="btn flex-fill" style="background-color: #ffffff;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'blue' }" type="button" @click="onChangeBgSidebar('blue')" class="btn flex-fill" style="background-color: #7367f0;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'gray' }" type="button" @click="onChangeBgSidebar('gray')" class="btn" style="background-color: #82868b;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'green' }" type="button" @click="onChangeBgSidebar('green')" class="btn flex-fill" style="background-color: #28c76f;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'red' }" type="button" @click="onChangeBgSidebar('red')" class="btn flex-fill" style="background-color: #ea5455;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'warning' }" type="button" @click="onChangeBgSidebar('warning')" class="btn" style="background-color: #ff9f43;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'ligth-blue' }" type="button" @click="onChangeBgSidebar('ligth-blue')" class="btn" style="background-color: #00cfe8;"></button>
                        <button :class="{ 'active': visuals.sidebar_theme === 'dark' }" type="button" @click="onChangeBgSidebar('dark')" class="btn flex-fill" style="background-color: #283046;"></button>
                    </div>
                </div> -->

                <div class="mt-3 theme-color-selector">
                    <h5>Selecciona un color de tema:</h5>
                    <div class="color-selector">
                        <button
                            type="button"
                            class="btn-theme-white"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'white' }"
                            @click="onChangeTheme('white')"
                        ></button>
                        <button
                            type="button"
                            class="btn-theme-white"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'aqua' }"
                            @click="onChangeTheme('aqua')"
                            style="background-color: #90dad9;"
                        ></button>
                        <button
                            type="button"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'acid' }"
                            @click="onChangeTheme('acid')"
                            style="background-color: #c1b1f1;"
                        ></button>
                        <button
                            type="button"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'cupcake' }"
                            @click="onChangeTheme('cupcake')"
                            style="background-color: #e7dad0;"
                        ></button>
                        <button
                            type="button"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'retro' }"
                            @click="onChangeTheme('retro')"
                            style="background-color: #ebddb7;"
                        ></button>
                        <button
                            type="button"
                            :class="{ 'theme-selected': visuals.sidebar_theme === 'lemonade' }"
                            @click="onChangeTheme('lemonade')"
                            style="background-color: #cddfae;"
                        ></button>
                    </div>
                </div>

                <div
                    v-if="isBlackSkinSelected"
                    class="mt-3 theme-color-selector-black"
                >
                    <h5>Selecciona un color de tema:</h5>
                    <div class="color-selector">
                        <button
                            type="button"
                            class="btn-theme-white"
                            :class="{ 'black-theme-selected': visuals.black_theme === 'default' }"
                            @click="onChangeBlackTheme('default')"
                            style="background-color: oklch(0.34 0.08 259.24);"
                        ></button>
                        <button
                            type="button"
                            class="btn-theme-white"
                            :class="{ 'black-theme-selected': visuals.black_theme === 'greenvoid' }"
                            @click="onChangeBlackTheme('greenvoid')"
                            style="background-color: oklch(0.4 0.08 148.33);"
                        ></button>
                        <button
                            type="button"
                            :class="{ 'black-theme-selected': visuals.black_theme === 'purplevoid' }"
                            @click="onChangeBlackTheme('purplevoid')"
                            style="background-color: oklch(0.34 0.09 319.44);"
                        ></button>
                    </div>
                </div>

                <div v-if="!isBlackSkinSelected" class="pt-3 sidebar-compact-selector-container d-none d-md-block">
                    <h5>Menú lateral contraído</h5>
                    <div :class="{ 'has-danger': errors.compact_sidebar }">
                        <el-switch
                            v-model="form.compact_sidebar"
                            active-text="Si"
                            inactive-text="No"
                            @change="submitForm"
                        >
                        </el-switch>
                        <br />
                        <small
                            class="form-control-feedback"
                            v-if="errors.compact_sidebar"
                            v-text="errors.compact_sidebar[0]"
                        ></small>
                    </div>
                </div>

                <div v-if="isBlackSkinSelected" class="pt-3 sidebar-margin-selector-container d-none d-md-block">
                    <h5>Estilo de Sidebar</h5>
                    <div class="d-flex justify-content-between gap-3 sidebar-margin-selector">
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': form.compact_sidebar === false }"
                            role="button"
                            tabindex="0"
                            @click="form.compact_sidebar = false; submitForm()"
                        >
                            <div>
                                <svg data-name="con-layout-default" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><path d="M39.22 15.99h-8.16c-.79 0-1.43-.67-1.43-1.5s.64-1.5 1.43-1.5h8.16c.79 0 1.43.67 1.43 1.5s-.64 1.5-1.43 1.5z" opacity="0.75"></path><rect x="29.63" y="18.39" width="16.72" height="2.73" rx="1.36" ry="1.36" opacity="0.5"></rect><path d="M75.1 6.68v1.45c0 .63-.49 1.14-1.09 1.14H30.72c-.6 0-1.09-.51-1.09-1.14V6.68c0-.62.49-1.14 1.09-1.14h43.29c.6 0 1.09.52 1.09 1.14z" opacity="0.9"></path><rect x="29.63" y="24.22" width="21.8" height="19.95" rx="2.11" ry="2.11" opacity="0.4"></rect><g stroke-linecap="round" stroke-miterlimit="10"><rect x="61.06" y="38.15" width="2.01" height="3.42" rx="0.33" ry="0.33" opacity="0.32"></rect><rect x="56.78" y="34.99" width="2.01" height="6.58" rx="0.33" ry="0.33" opacity="0.44"></rect><rect x="65.17" y="32.86" width="2.01" height="8.7" rx="0.33" ry="0.33" opacity="0.53"></rect><rect x="69.55" y="29.17" width="2.01" height="12.4" rx="0.33" ry="0.33" opacity="0.66"></rect></g><g opacity="0.5"><circle cx="63.17" cy="18.63" r="7.5"></circle><path d="M63.17 11.63c3.86 0 7 3.14 7 7s-3.14 7-7 7-7-3.14-7-7 3.14-7 7-7m0-1c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8z"></path></g><g opacity="0.74"><path d="M64.05 18.13l3.38-5.67c.93.64 1.7 1.48 2.26 2.47.56.98.89 2.08.96 3.21h-6.6z"></path><path d="M67.57 13.19a6.977 6.977 0 012.52 4.44h-5.17l2.65-4.44m-.31-1.43l-4.1 6.87h8c0-1.39-.36-2.75-1.04-3.95a8.007 8.007 0 00-2.86-2.92z"></path></g><g stroke-linecap="round" stroke-miterlimit="10"><rect x="5.84" y="5.02" width="19.14" height="40" rx="2" ry="2" opacity="0.8"></rect><g stroke="#fff"><path fill="none" opacity="0.72" stroke-width="2px" d="M9.02 17.39L21.25 17.39"></path><path fill="none" opacity="0.48" stroke-width="2px" d="M9.02 24.6L19.54 24.6"></path><path fill="none" opacity="0.55" stroke-width="2px" d="M9.02 20.88L18.4 20.88"></path><circle cx="10.98" cy="9.91" r="2.54" fill="#fff" opacity="0.8"></circle><path fill="none" opacity="0.8" stroke-width="2px" d="M15.53 8.65L21.25 8.65"></path><path fill="none" opacity="0.6" d="M15.32 11.3L20.38 11.3"></path></g></g></svg>
                            </div>
                            <span class="text-center">Defecto</span>
                        </div>
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': form.compact_sidebar === true }"
                            role="button"
                            tabindex="0"
                            @click="form.compact_sidebar = true; submitForm()"
                        >
                            <div>
                                <svg data-name="icon-layout-compact" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><rect x="5.84" y="5.2" width="4" height="40" rx="2" ry="2" stroke-linecap="round" stroke-miterlimit="10"></rect><g stroke="#fff" stroke-linecap="round" stroke-miterlimit="10"><path fill="none" opacity="0.66" stroke-width="2px" d="M7.26 11.56L8.37 11.56"></path><path fill="none" opacity="0.51" stroke-width="2px" d="M7.26 14.49L8.37 14.49"></path><path fill="none" opacity="0.52" stroke-width="2px" d="M7.26 17.39L8.37 17.39"></path><circle cx="7.81" cy="7.25" r="1.16" fill="#fff" opacity="0.8"></circle></g><path fill="none" opacity="0.75" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3px" d="M15.81 14.49L22.89 14.49"></path><rect x="14.93" y="18.39" width="22.19" height="2.73" rx="0.64" ry="0.64" opacity="0.5" stroke-linecap="round" stroke-miterlimit="10"></rect><rect x="14.93" y="5.89" width="59.16" height="2.73" rx="0.64" ry="0.64" opacity="0.9" stroke-linecap="round" stroke-miterlimit="10"></rect><rect x="14.93" y="24.22" width="32.68" height="19.95" rx="2.11" ry="2.11" opacity="0.4" stroke-linecap="round" stroke-miterlimit="10"></rect><g stroke-linecap="round" stroke-miterlimit="10"><rect x="59.05" y="38.15" width="2.01" height="3.42" rx="0.33" ry="0.33" opacity="0.32"></rect><rect x="54.78" y="34.99" width="2.01" height="6.58" rx="0.33" ry="0.33" opacity="0.44"></rect><rect x="63.17" y="32.86" width="2.01" height="8.7" rx="0.33" ry="0.33" opacity="0.53"></rect><rect x="67.54" y="29.17" width="2.01" height="12.4" rx="0.33" ry="0.33" opacity="0.66"></rect></g><g opacity="0.5"><circle cx="62.16" cy="18.63" r="7.5"></circle><path d="M62.16 11.63c3.86 0 7 3.14 7 7s-3.14 7-7 7-7-3.14-7-7 3.14-7 7-7m0-1c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8z"></path></g><g opacity="0.74"><path d="M63.04 18.13l3.38-5.67c.93.64 1.7 1.48 2.26 2.47.56.98.89 2.08.96 3.21h-6.6z"></path><path d="M66.57 13.19a6.977 6.977 0 012.52 4.44h-5.17l2.65-4.44m-.31-1.43l-4.1 6.87h8c0-1.39-.36-2.75-1.04-3.95a8.007 8.007 0 00-2.86-2.92z"></path></g></svg>
                            </div>
                            <span class="text-center">Contraido</span>
                        </div>
                    </div>
                </div>

                <div v-if="isBlackSkinSelected" class="pt-3 sidebar-margin-selector-container sidebar-theme-selector-container d-none d-md-block">
                    <h5>Tema del Sidebar</h5>
                    <div class="d-flex justify-content-between gap-3 sidebar-margin-selector">
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': form.sidebar_mode === 'light' }"
                            role="button"
                            tabindex="0"
                            @click="submitSidebarMode('light')"
                        >
                            <div>
                                <svg data-name="icon-sidebar-sidebar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><path d="M23.42.51h51.99c2.21 0 4 1.79 4 4v42.18c0 2.21-1.79 4-4 4H23.42s-.04-.02-.04-.04V.55s.02-.04.04-.04z" opacity="0.2" stroke-linecap="round" stroke-miterlimit="10"></path><path fill="none" opacity="0.72" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 14.88L17.78 14.88"></path><path fill="none" opacity="0.48" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 22.09L16.08 22.09"></path><path fill="none" opacity="0.55" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 18.38L14.93 18.38"></path><g stroke-linecap="round" stroke-miterlimit="10"><circle cx="7.51" cy="7.4" r="2.54" opacity="0.8"></circle><path fill="none" opacity="0.8" stroke-width="2px" d="M12.06 6.14L17.78 6.14"></path><path fill="none" opacity="0.6" d="M11.85 8.79L16.91 8.79"></path></g></svg>
                            </div>
                            <span class="text-center">Claro</span>
                        </div>
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': form.sidebar_mode === 'dark' }"
                            role="button"
                            tabindex="0"
                            @click="submitSidebarMode('dark')"
                        >
                            <div class="svg-dark">
                                <svg data-name="icon-sidebar-sidebar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><path d="M23.42.51h51.99c2.21 0 4 1.79 4 4v42.18c0 2.21-1.79 4-4 4H23.42s-.04-.02-.04-.04V.55s.02-.04.04-.04z" opacity="0.2" stroke-linecap="round" stroke-miterlimit="10"></path><path fill="none" opacity="0.72" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 14.88L17.78 14.88"></path><path fill="none" opacity="0.48" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 22.09L16.08 22.09"></path><path fill="none" opacity="0.55" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 18.38L14.93 18.38"></path><g stroke-linecap="round" stroke-miterlimit="10"><circle cx="7.51" cy="7.4" r="2.54" opacity="0.8"></circle><path fill="none" opacity="0.8" stroke-width="2px" d="M12.06 6.14L17.78 6.14"></path><path fill="none" opacity="0.6" d="M11.85 8.79L16.91 8.79"></path></g></svg>
                            </div>
                            <span class="text-center">Oscuro</span>
                        </div>
                    </div>
                </div>

                <div v-if="isBlackSkinSelected" class="pt-3 sidebar-margin-selector-container d-none d-md-block">
                    <h5>Sidebar</h5>
                    <div class="d-flex justify-content-between gap-3 sidebar-margin-selector">
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': visuals.sidebar_margin === true }"
                            role="button"
                            tabindex="0"
                            @click="onChangeSidebarMargin(true)"
                        >
                            <div>
                                <svg data-name="icon-sidebar-floating" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><rect x="5.89" y="5.15" width="19.74" height="40" rx="2" ry="2" opacity="0.8" stroke-linecap="round" stroke-miterlimit="10"></rect><g stroke="#fff" stroke-linecap="round" stroke-miterlimit="10"><path fill="none" opacity="0.72" stroke-width="2px" d="M9.81 18.36L22.04 18.36"></path><path fill="none" opacity="0.48" stroke-width="2px" d="M9.81 25.57L20.33 25.57"></path><path fill="none" opacity="0.55" stroke-width="2px" d="M9.81 21.85L19.18 21.85"></path><circle cx="11.76" cy="10.88" r="2.54" fill="#fff" opacity="0.8"></circle><path fill="none" opacity="0.8" stroke-width="2px" d="M16.31 9.62L22.04 9.62"></path><path fill="none" opacity="0.6" d="M16.1 12.27L21.16 12.27"></path></g><path fill="none" opacity="0.62" stroke-linecap="round" stroke-miterlimit="10" stroke-width="3px" d="M30.59 9.62L35.85 9.62"></path><rect x="29.94" y="13.42" width="26.03" height="2.73" rx="0.64" ry="0.64" opacity="0.44" stroke-linecap="round" stroke-miterlimit="10"></rect><rect x="29.94" y="19.28" width="43.11" height="25.87" rx="2" ry="2" opacity="0.3" stroke-linecap="round" stroke-miterlimit="10"></rect></svg>
                            </div>
                            <span class="text-center">Flotando</span>
                        </div>
                        <div
                            class="sidebar-example"
                            :class="{ 'sidebar-example-selected': visuals.sidebar_margin === false }"
                            role="button"
                            tabindex="0"
                            @click="onChangeSidebarMargin(false)"
                        >
                            <div>
                                <svg data-name="icon-sidebar-sidebar" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.86 51.14" class="fill-primary stroke-primary group-data-[state=unchecked]:fill-muted-foreground group-data-[state=unchecked]:stroke-muted-foreground w-100" aria-hidden="true"><path d="M23.42.51h51.99c2.21 0 4 1.79 4 4v42.18c0 2.21-1.79 4-4 4H23.42s-.04-.02-.04-.04V.55s.02-.04.04-.04z" opacity="0.2" stroke-linecap="round" stroke-miterlimit="10"></path><path fill="none" opacity="0.72" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 14.88L17.78 14.88"></path><path fill="none" opacity="0.48" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 22.09L16.08 22.09"></path><path fill="none" opacity="0.55" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2px" d="M5.56 18.38L14.93 18.38"></path><g stroke-linecap="round" stroke-miterlimit="10"><circle cx="7.51" cy="7.4" r="2.54" opacity="0.8"></circle><path fill="none" opacity="0.8" stroke-width="2px" d="M12.06 6.14L17.78 6.14"></path><path fill="none" opacity="0.6" d="M11.85 8.79L16.91 8.79"></path></g></svg>
                            </div>
                            <span class="text-center">Fijo</span>
                        </div>
                    </div>

                </div>

                <div class="mt-3">
                    <h5>Mostrar panel de bienvenida en el dashboard</h5>
                    <div>
                        <el-switch
                            v-model="showWelcome"
                            active-text="Si"
                            inactive-text="No"
                            @change="updateConfig"
                        >
                        </el-switch>
                    </div>
                </div>

                <div class="mt-3">
                    <h5>Permitir cambiar de empresa y sucursal desde el sidebar</h5>
                    <div>
                        <el-switch
                            v-model="branchSelectorInSidebar"
                            active-text="Si"
                            inactive-text="No"
                            @change="updateBranchSelectorConfig"
                        >
                        </el-switch>
                    </div>
                </div>

                <div class="pt-3 form-modern">
                    <label class="control-label"
                        >Visualización de productos en POS</label
                    >
                    <div
                        :class="{
                            'has-danger': errors.amount_plastic_bag_taxes
                        }"
                    >
                        <el-select
                            v-model="form.layout_mode"
                            @change="submitViewPos"
                        >
                            <el-option
                                label="Predeterminado"
                                value="default"
                            ></el-option>
                            <el-option
                                label="Cómodo"
                                value="comfortable"
                            ></el-option>
                            <el-option
                                label="Compacto"
                                value="compact"
                            ></el-option>
                            <el-option
                                label="Apilado"
                                value="stacked"
                            ></el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.amount_plastic_bag_taxes"
                            v-text="errors.amount_plastic_bag_taxes[0]"
                        ></small>
                    </div>
                </div>
                <div class="pt-3 form-modern">
                    <label class="control-label">Imagen predeterminada de productos
                        <el-tooltip class="item" content="Para un mejor resultado visual, sube una imagen cuadrada (ej. 215x215 px). Formatos permitidos: PNG o JPG."
                            effect="dark" placement="top-start">
                            <i class="fas fa-info-circle"></i>
                        </el-tooltip>
                    </label>
                    <el-input v-model="fileName" :readonly="true" placeholder="Ninguna imagen subida">
                        <el-upload
                            slot="append"
                            :on-success="successUploadDefaultImage"
                            :on-error="errorUpload"
                            :show-file-list="false"
                            :action="`/api/configurations/default-image`"
                            :with-credentials="true"
                            name="image"
                        >
                            <el-button class="p-2" icon="el-icon-upload" type="primary"></el-button>
                        </el-upload>
                    </el-input>
                </div>
                <div class="pt-3 form-modern">
                    <label class="control-label">Cambiar tema</label>
                    <div :class="{ 'has-danger': errors.compact_sidebar }">
                        <el-select
                            v-model="form.skin_id"
                            placeholder="Tema"
                            @change="submitForm"
                            class="pb-3"
                        >
                            <el-option
                                v-for="item in skins"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                        </el-select>
                        <small
                            class="form-control-feedback"
                            v-if="errors.compact_sidebar"
                            v-text="errors.compact_sidebar[0]"
                        ></small>
                        <el-button
                            class="second-buton"
                            type="button"
                            @click="dialogSkins()"
                            color="primary"
                            >Subir tema</el-button
                        >
                    </div>
                </div>
            </div>
        </form>
            <dialog-skins
                :showDialog.sync="dialogSkinsVisible"
                :skins.sync="skins"
            />
        </div>
        <div class="style-switcher-backdrop" @click="closeStyleSwitcher"></div>
    </div>
</template>

<script>
import DialogSkins from "./partials/dialog_skins.vue";
export default {
    props: ["visual", "typeUser"],
    components: {
        DialogSkins
    },
    data() {
        return {
            themes: {},
            blackThemes: {},
            showWelcome: false,
            branchSelectorInSidebar: false,
            loading_submit: false,
            resource: "configurations",
            errors: {},
            form: {},
            visuals: {},
            skins: {},
            dialogSkinsVisible: false,
            fileName: '',
            headers:{},
        };
    },
    async created() {
        await this.loadThemes();
        await this.loadBlackThemes();
        await this.initForm();
        await this.getRecords();
    },
    computed: {
        isBlackSkinSelected() {
            const skinId = this.form && this.form.skin_id;
            const skins = Array.isArray(this.skins) ? this.skins : [];
            const selected = skins.find(skin => String(skin.id) === String(skinId));

            if (!selected) {
                return false;
            }

            const name = (selected.name || selected.slug || selected.code || "")
                .toString()
                .toLowerCase();

            return name.includes("black");
        }
    },
    methods: {
        successUploadDefaultImage(response, file) {
            if (response.message) {
                this.$message.success(response.message);
                this.form.default_image = response.file;
                this.fileName = response.file;
            }
        },

        errorUpload(err) {
          this.$message.error("Error al subir la imagen");
          console.error("Error upload:", err);
        },
        async loadThemes() {
            try {
                const response = await fetch("/json/themes/themes.json");
                this.themes = await response.json();
            } catch (error) {
                console.error("Error loading themes:", error);
            }
        },
        async loadBlackThemes() {
            try {
                const response = await fetch("/json/themes/black-themes.json");
                this.blackThemes = await response.json();
            } catch (error) {
                console.error("Error loading black themes:", error);
            }
        },
        updateConfig() {
            this.$set(this.visuals, 'show_welcome_panel', this.showWelcome);
            this.submit();
            this.toggleWelcomeComponent();
        },
        updateBranchSelectorConfig() {
            this.$set(this.visuals, 'branch_selector_in_sidebar', this.branchSelectorInSidebar);
            this.submit();
            
            const event = new CustomEvent('branchSelectorVisibilityChanged', {
                detail: { showInHeader: !this.branchSelectorInSidebar }
            });
            window.dispatchEvent(event);
        },
        toggleWelcomeComponent() {
            try {
                // log para debug
                // console.log('[visual] toggleWelcomeComponent: showWelcome=', this.showWelcome);

                // buscar por clase (según tu plantilla)
                const el = document.querySelector('.welcome-component');

                if (!el) {
                    // console.log('[visual] toggleWelcomeComponent: .welcome-component NO encontrada');
                    return;
                }

                // cambiar visibilidad de forma segura
                el.style.display = this.showWelcome ? 'block' : 'none';
                console.log('[visual] toggleWelcomeComponent: aplicada visibilidad', el.style.display);
            } catch (e) {
                
                console.warn('[visual] toggleWelcomeComponent error:', e);
            }
        },

        applyTheme(theme) {
            const colors = this.themes[theme];
            if (!colors) {
                console.error(`Theme "${theme}" not found.`);
                return;
            }

            let styleTag = document.getElementById("theme-styles");
            if (!styleTag) {
                styleTag = document.createElement("style");
                styleTag.id = "theme-styles";
                document.head.appendChild(styleTag);
            }

            let cssString = ":root {";
            Object.keys(colors).forEach(variable => {
                cssString += `${variable}: ${colors[variable]}; `;
            });

            cssString += "}";

            styleTag.innerHTML = cssString;
            localStorage.setItem('current_theme', theme);
            localStorage.setItem(`theme_colors_${theme}`, JSON.stringify(colors));
        },
        applyBlackTheme(theme) {
            const colors = this.blackThemes[theme];
            if (!colors) {
                console.error(`Black theme "${theme}" not found.`);
                return;
            }

            let styleTag = document.getElementById("black-theme-styles");
            if (!styleTag) {
                styleTag = document.createElement("style");
                styleTag.id = "black-theme-styles";
                document.head.appendChild(styleTag);
            }

            let cssString = ":root {";
            Object.keys(colors).forEach(variable => {
                cssString += `${variable}: ${colors[variable]}; `;
            });

            cssString += "}";

            styleTag.innerHTML = cssString;
            localStorage.setItem('black_theme', theme);
            localStorage.setItem(`black_theme_colors_${theme}`, JSON.stringify(colors));
        },
        onChangeTheme(theme) {
            this.visuals.sidebar_theme = theme;
            this.submit();
            this.applyTheme(theme);
        },
        onChangeBlackTheme(theme) {
            this.$set(this.visuals, 'black_theme', theme);
            this.submit();
            this.applyBlackTheme(theme);
        },
        onChangeBgSidebar(theme) {
            this.visuals.sidebar_theme = theme;
            this.submit();
        },
        onChangeSidebarMargin(value) {
            this.$set(this.visuals, 'sidebar_margin', value);
            this.applySidebarMargin(value);
            this.submit();
        },
        applySidebarMargin(value) {
            const htmlElement = document.documentElement;
            if (!htmlElement) return;

            if (value === true) {
                htmlElement.classList.add('sidebar-left-floating');
                htmlElement.classList.remove('sidebar-left-fixed');
            } else {
                htmlElement.classList.add('sidebar-left-fixed');
                htmlElement.classList.remove('sidebar-left-floating');
            }
        },
        submitSidebarMode(mode) {
            if (mode) {
                this.form.sidebar_mode = mode;
            }
            this.$http
                .post(`/${this.resource}/visual_settings`, {
                    bg: this.visuals.bg,
                    header: this.visuals.header,
                    sidebars: this.visuals.sidebars,
                    navbar: this.visuals.navbar,
                    sidebar_theme: this.visuals.sidebar_theme,
                    sidebar_mode: this.form.sidebar_mode,
                    black_theme: this.visuals.black_theme
                })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        // Aplicar la clase inmediatamente
                        const htmlElement = document.documentElement;
                        if (this.form.sidebar_mode === 'dark') {
                            htmlElement.classList.remove('sidebarMode-light');
                            htmlElement.classList.add('sidebarMode-dark');
                        } else {
                            htmlElement.classList.remove('sidebarMode-dark');
                            htmlElement.classList.add('sidebarMode-light');
                        }
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        initForm() {
            this.errors = {};
            this.form = {
                id: 1,
                compact_sidebar: true,
                colums_grid_item: 4,
                enable_whatsapp: true,
                phone_whatsapp: "",
                skins: 1,
                sidebar_mode: "light"
            };
        },
        async getRecords() {
            this.$http.get(`/${this.resource}/record`).then(response => {
                if (response.data !== "") {
                    this.visuals = response.data.data.visual;
                    this.form = response.data.data;
                    this.skins = response.data.data.skins;

                    if (typeof this.visuals.sidebar_margin === 'undefined') {
                        this.$set(this.visuals, 'sidebar_margin', true);
                    }

                    this.applySidebarMargin(this.visuals.sidebar_margin);

                    if (this.form.default_image) {
                        this.fileName = this.form.default_image;
                    }

                    if (!this.visuals.black_theme) {
                        this.$set(this.visuals, 'black_theme', 'default');
                    }

                    if (typeof this.visuals.show_welcome_panel === 'undefined') {
                        this.$set(this.visuals, 'show_welcome_panel', true);
                    }

                    this.showWelcome = !!this.visuals.show_welcome_panel;
                    this.toggleWelcomeComponent();

                    if (typeof this.visuals.branch_selector_in_sidebar === 'undefined') {
                        this.$set(this.visuals, 'branch_selector_in_sidebar', false);
                    }

                    this.branchSelectorInSidebar = !!this.visuals.branch_selector_in_sidebar;

                    if (this.visual.sidebar_theme) {
                        this.applyTheme(this.visual.sidebar_theme);
                    }

                    if (this.visuals.black_theme) {
                        this.applyBlackTheme(this.visuals.black_theme);
                    }

                    const storedLayoutMode = localStorage.getItem(
                        "layout_mode"
                    );

                    if (!this.form.layout_mode && !storedLayoutMode) {
                        this.form.layout_mode = "default";
                        localStorage.setItem("layout_mode", "default");
                        this.submitViewPos(); // Enviar solo si nunca se ha establecido antes
                    } else if (storedLayoutMode) {
                        this.form.layout_mode = storedLayoutMode; // Usar el valor guardado localmente
                    }
                }
            });
        },
        submit() {
            this.visuals.navbar = "fixed";
            this.$http
                .post(`/${this.resource}/visual_settings`, this.visuals)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {
                    // location.reload();
                });
        },
        submitForm() {
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        location.reload();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        submitViewPos() {
            if (this.form.layout_mode === localStorage.getItem("layout_mode")) {
                return;
            }

            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        localStorage.setItem(
                            "layout_mode",
                            this.form.layout_mode
                        );
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },
        dialogSkins() {
            this.dialogSkinsVisible = true;
        },
        closeStyleSwitcher() {
            const styleSwitcher = document.getElementById('styleSwitcher');
            if (styleSwitcher) {
                styleSwitcher.classList.remove('active');
                styleSwitcher.style.right = '-325px';
            }
        }
    },
    mounted() {}
};
</script>
<style lang="scss">
.style-switcher-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1039;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    pointer-events: none;
}

.style-switcher.active ~ .style-switcher-backdrop {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}
</style>

<style scoped lang="scss">

.el-custom-control {
    display: flex;
    align-content: center;
    .btn {
        margin-right: 0.5rem;
        $size: 20px;
        width: $size;
        height: $size;
        border-radius: 4px;
        padding: 0;
        &.active {
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);
        }
    }
}
.color-selector {
    display: flex;
    gap: 10px;
}
.color-selector button {
    width: 48px;
    height: 25px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    outline: none;
    transition: border 0.2s ease;
}

.color-selector button.theme-selected {
   box-shadow: 0 0 0 4px var(--highlight-color);
}
.sidebar-example.sidebar-example-selected > div::after,
.black-theme-selected::after {
    background-image: url("data:image/svg+xml;utf8,\
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'>\
        <polyline points='20 6 9 17 4 12' />\
        </svg>");

    background-repeat: no-repeat;
    background-position: center;
    background-size: 12px;
}
html.dark .sidebar-example.sidebar-example-selected > div::after,
html.dark .black-theme-selected::after {
    background-image: url("data:image/svg+xml;utf8,\
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'>\
        <polyline points='20 6 9 17 4 12' />\
        </svg>");
}
</style>
