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
            <div v-if="typeUser != 'integrator'" class="p-3 body-visual">
                <div class="mode-switch-wrap">
                    <h5 class="mb-2">Modo de color</h5>
                    <div class="mode-switch" role="group" aria-label="Modo de color">
                        <a
                            href="/configurations/change-mode"
                            class="mode-switch__opt"
                            :class="{ 'is-active': visuals.bg == 'white' }"
                            @click.prevent="setMode('white')"
                            title="Modo claro"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                            </svg>
                            <span>Claro</span>
                        </a>
                        <a
                            href="/configurations/change-mode"
                            class="mode-switch__opt"
                            :class="{ 'is-active': visuals.bg == 'dark' }"
                            @click.prevent="setMode('dark')"
                            title="Modo oscuro"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                            </svg>
                            <span>Oscuro</span>
                        </a>
                    </div>
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

                <div v-if="!isBlackSkinSelected" class="mt-3 theme-color-selector">
                    <h5>Selecciona un color de tema:</h5>
                    <div class="theme-select" :class="{ open: themeMenuOpen }">
                        <button type="button" class="theme-select-trigger" @click="themeMenuOpen = !themeMenuOpen">
                            <span class="theme-swatch" :style="{ background: activeTheme.bg }">
                                <i v-for="(c, i) in activeTheme.dots" :key="i" :style="{ background: c }"></i>
                            </span>
                            <span class="theme-row-info">
                                <span class="theme-row-name">{{ activeTheme.label }}</span>
                                <span class="theme-row-color">{{ activeTheme.color }}</span>
                            </span>
                            <i class="fas fa-chevron-down theme-select-caret"></i>
                        </button>
                        <div class="theme-select-menu">
                            <button
                                v-for="t in themeList"
                                :key="t.key"
                                type="button"
                                class="btn-theme-row"
                                :class="{ 'theme-selected': visuals.sidebar_theme === t.key }"
                                @click="selectTheme(t.key)"
                                :title="t.label"
                            >
                                <span class="theme-swatch" :style="{ background: t.bg }">
                                    <i v-for="(c, i) in t.dots" :key="i" :style="{ background: c }"></i>
                                </span>
                                <span class="theme-row-info">
                                    <span class="theme-row-name">{{ t.label }}</span>
                                    <span class="theme-row-color">{{ t.color }}</span>
                                </span>
                                <i class="fas fa-check theme-row-check"></i>
                            </button>
                        </div>
                        <div v-if="themeMenuOpen" class="theme-select-backdrop" @click="themeMenuOpen = false"></div>
                    </div>
                </div>

                <div
                    v-if="isBlackSkinSelected"
                    class="mt-3 theme-color-selector-black"
                >
                    <h5>Selecciona un color de tema:</h5>
                    <div class="theme-select" :class="{ open: blackMenuOpen }">
                        <button type="button" class="theme-select-trigger" @click="blackMenuOpen = !blackMenuOpen">
                            <span class="theme-swatch theme-swatch-dark" :style="{ background: 'oklch(0.2 0.0417 ' + activeBlackTheme.h + ')' }">
                                <i v-for="(c, i) in blackDots(activeBlackTheme)" :key="i" :style="{ background: c }"></i>
                            </span>
                            <span class="theme-row-info">
                                <span class="theme-row-name">{{ activeBlackTheme.label }}</span>
                                <span class="theme-row-color">{{ activeBlackTheme.color }}</span>
                            </span>
                            <i class="fas fa-chevron-down theme-select-caret"></i>
                        </button>
                        <div class="theme-select-menu">
                            <button
                                v-for="t in blackThemeList"
                                :key="t.key"
                                type="button"
                                class="btn-theme-row"
                                :class="{ 'theme-selected': visuals.black_theme === t.key }"
                                @click="selectBlackTheme(t.key)"
                                :title="t.label"
                            >
                                <span class="theme-swatch theme-swatch-dark" :style="{ background: 'oklch(0.2 0.0417 ' + t.h + ')' }">
                                    <i v-for="(c, i) in blackDots(t)" :key="i" :style="{ background: c }"></i>
                                </span>
                                <span class="theme-row-info">
                                    <span class="theme-row-name">{{ t.label }}</span>
                                    <span class="theme-row-color">{{ t.color }}</span>
                                </span>
                                <i class="fas fa-check theme-row-check"></i>
                            </button>
                        </div>
                        <div v-if="blackMenuOpen" class="theme-select-backdrop" @click="blackMenuOpen = false"></div>
                    </div>
                </div>

                <div v-if="!isBlackSkinSelected" class="pt-3 sidebar-compact-selector-container d-none d-md-block">
                    <label class="control-label">Menú lateral contraído</label>
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
                    <label class="control-label">Mostrar panel de bienvenida en el dashboard</label>
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
                    <label class="control-label">Permitir cambiar de empresa y sucursal desde el sidebar</label>
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
                            v-model="form.colums_grid_item"
                            @change="submitViewPos"
                        >
                            <el-option
                                label="Predeterminado"
                                :value="2"
                            ></el-option>
                            <el-option
                                label="Cómodo"
                                :value="3"
                            ></el-option>
                            <el-option
                                label="Compacto"
                                :value="4"
                            ></el-option>
                            <el-option
                                label="Apilado"
                                :value="5"
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
            themeMenuOpen: false,
            blackMenuOpen: false,
            blackThemeList: [
                { key: 'default',    label: 'Medianoche', color: 'Índigo',  h: 266.36 },
                { key: 'ocean',      label: 'Océano',     color: 'Teal',    h: 200 },
                { key: 'greenvoid',  label: 'Esmeralda',  color: 'Verde',   h: 154.7 },
                { key: 'bronze',     label: 'Bronce',     color: 'Ámbar',   h: 75 },
                { key: 'crimson',    label: 'Carmesí',    color: 'Rojo',    h: 25 },
                { key: 'purplevoid', label: 'Orquídea',   color: 'Magenta', h: 326.07 },
            ],
            themeList: [
                { key: 'white',     label: 'Clásico',     color: 'Azul',        bg: '#f5f8ff', dots: ['#3d6bf5','#8aa4f7','#3a4658','#8492a6'] },
                { key: 'corporate', label: 'Corporativo', color: 'Azul',        bg: '#f4f7fc', dots: ['#2d5bb9','#8aa6da','#31435c','#7d8ba3'] },
                { key: 'navy',      label: 'Marino',      color: 'Azul marino', bg: '#f3f6f9', dots: ['#28527a','#7ba0c4','#2b3a4d','#7a8798'] },
                { key: 'slate',     label: 'Pizarra',     color: 'Gris',        bg: '#f5f6f8', dots: ['#4a5b73','#94a1b8','#3a4453','#828d9e'] },
                { key: 'indigo',    label: 'Índigo',      color: 'Índigo',      bg: '#f5f5fb', dots: ['#4a45a8','#9995d6','#37374f','#807e9c'] },
                { key: 'forest',    label: 'Bosque',      color: 'Verde',       bg: '#f2f8f4', dots: ['#2f6d52','#7cb598','#324338','#7a8a80'] },
                { key: 'burgundy',  label: 'Borgoña',     color: 'Vino',        bg: '#faf4f5', dots: ['#8a3c4e','#c78896','#43333a','#94818a'] },
                { key: 'aqua',      label: 'Aqua',        color: 'Turquesa',    bg: '#f0f9f9', dots: ['#0e94a8','#63cdd8','#294a54','#6f9098'] },
                { key: 'acid',      label: 'Ácido',       color: 'Violeta',     bg: '#f6f4fe', dots: ['#6f52e8','#a99ff0','#39335a','#867fa4'] },
                { key: 'cupcake',   label: 'Cupcake',     color: 'Rosa',        bg: '#fdf5f9', dots: ['#db4f8b','#f2a3c4','#4a3340','#9a8290'] },
                { key: 'retro',     label: 'Retro',       color: 'Ámbar',       bg: '#fbf5e9', dots: ['#d1791f','#eec471','#463c30','#8f8371'] },
                { key: 'lemonade',  label: 'Limonada',    color: 'Verde',       bg: '#f6faec', dots: ['#659f2b','#b6d97f','#3f4634','#7f8570'] },
            ],
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
        },
        activeTheme() {
            return this.themeList.find(t => t.key === (this.visuals && this.visuals.sidebar_theme))
                || this.themeList[0];
        },
        activeBlackTheme() {
            return this.blackThemeList.find(t => t.key === (this.visuals && this.visuals.black_theme))
                || this.blackThemeList[0];
        }
    },
    methods: {
        // El endpoint change-mode alterna claro/oscuro: solo navegamos si el modo es distinto al actual
        setMode(mode) {
            if (this.visuals && this.visuals.bg === mode) return;
            window.location.href = '/configurations/change-mode';
        },
        selectTheme(key) {
            this.onChangeTheme(key);
            this.themeMenuOpen = false;
        },
        selectBlackTheme(key) {
            this.onChangeBlackTheme(key);
            this.blackMenuOpen = false;
        },
        // Puntos OKLCH del swatch para un tema oscuro (mismo matiz, distinta luminosidad)
        blackDots(t) {
            return [
                `oklch(0.70 0.0417 ${t.h})`,
                `oklch(0.82 0.02 ${t.h})`,
                `oklch(0.92 0.012 ${t.h})`,
                `oklch(0.45 0.0417 ${t.h})`,
            ];
        },
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
                const response = await fetch("/json/themes/themes.json?v=" + Date.now());
                this.themes = await response.json();
            } catch (error) {
                console.error("Error loading themes:", error);
            }
        },
        async loadBlackThemes() {
            try {
                const response = await fetch("/json/themes/black-themes.json?v=" + Date.now());
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
                detail: { showInSidebar: this.branchSelectorInSidebar }
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
            let colors = this.themes[theme];
            if (!colors) {
                console.error(`Theme "${theme}" not found.`);
                return;
            }
            // Normaliza la estructura anidada de themes.json (ej. "white" -> { light, default })
            if (typeof colors === 'object' && !colors['--primary-color']) {
                colors = colors.default || colors.light || colors;
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
            this.$set(this.visuals, 'sidebar_theme', theme);
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
                        this.$set(this.visuals, 'show_welcome_panel', false);
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

                    if (!this.form.colums_grid_item) {
                        this.form.colums_grid_item = 2;
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
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
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

/* ===== Control segmentado de modo claro/oscuro (adaptativo a claro/oscuro) ===== */
.mode-switch-wrap h5 {
    font-size: 14px;
    font-weight: 600;
    color: inherit;
}
.mode-switch {
    display: flex;
    gap: 4px;
    padding: 4px;
    background: rgba(130, 130, 130, .14);
    border-radius: 12px;
}
.mode-switch__opt {
    flex: 1 1 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 10px;
    border-radius: 9px;
    font-size: 13.5px;
    font-weight: 600;
    color: inherit;
    opacity: .6;
    text-decoration: none;
    cursor: pointer;
    user-select: none;
    transition: background .18s ease, opacity .18s ease, box-shadow .18s ease, color .18s ease;
}
.mode-switch__opt:hover {
    opacity: 1;
}
/* La pastilla activa usa el color del tema seleccionado.
   En skin black el color real está en --black-primary (--primary-color lo pisa el tema claro). */
.mode-switch__opt.is-active {
    background: var(--black-vivid, var(--primary-color));
    color: #fff !important;
    opacity: 1;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
    cursor: default;
}
.mode-switch__opt svg {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
}

/* ===== Selector de temas tipo campo desplegable (adaptativo) ===== */
.theme-select {
    position: relative;
}
.theme-select-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 9px 12px;
    background: rgba(130, 130, 130, .10);
    border: 1px solid rgba(130, 130, 130, .20);
    border-radius: 12px;
    cursor: pointer;
    outline: none;
    color: inherit;
}
.theme-select-caret {
    margin-left: auto;
    opacity: .55;
    transition: transform .2s ease;
}
.theme-select.open .theme-select-caret {
    transform: rotate(180deg);
}
/* El campo (trigger) hereda el color de texto del panel (claro en oscuro, oscuro en claro) */
.theme-select-trigger .theme-row-name {
    color: inherit;
}
.theme-select-trigger .theme-row-color {
    color: inherit;
    opacity: .6;
}

/* El menú es un popover claro, legible en ambos modos */
.theme-select-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    z-index: 30;
    background: #fff;
    border: 1px solid #e6e9ef;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, .22);
    padding: 6px;
    max-height: 340px;
    overflow-y: auto;
    display: none;
}
.theme-select.open .theme-select-menu {
    display: block;
}
.theme-select-backdrop {
    position: fixed;
    inset: 0;
    z-index: 20;
}

/* Filas del selector (swatch + nombre + color + check) */
.theme-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.btn-theme-row {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    height: auto;
    padding: 9px 12px;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 10px;
    cursor: pointer;
    outline: none;
    text-align: left;
    transition: background .15s ease, border-color .15s ease;
}
.theme-select-menu .btn-theme-row:hover {
    background: #f1f3f7;
}
/* Fila activa: tinte + borde con el color del tema (--black-primary en skin black) */
.theme-select-menu .btn-theme-row.theme-selected {
    background: color-mix(in srgb, var(--black-vivid, var(--primary-color)) 12%, #fff);
    border-color: var(--black-vivid, var(--primary-color));
}
.theme-swatch {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2px;
    width: 34px;
    height: 34px;
    padding: 4px;
    border-radius: 9px;
    border: 1px solid rgba(0, 0, 0, .08);
    flex-shrink: 0;
}
.theme-swatch i {
    display: block;
    width: 100%;
    height: 100%;
    border-radius: 50%;
}
/* Swatch para temas oscuros: borde claro para que resalte sobre el fondo negro */
.theme-swatch-dark {
    border-color: rgba(255, 255, 255, .18);
}
.theme-row-info {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
    flex: 1;
}
.theme-row-name {
    font-size: 14px;
    font-weight: 600;
}
.theme-row-color {
    font-size: 11px;
}
/* Texto de las filas dentro del menú claro */
.theme-select-menu .theme-row-name {
    color: #1f2733;
}
.theme-select-menu .theme-row-color {
    color: #7a8699;
}
.theme-row-check {
    color: var(--black-vivid, var(--primary-color));
    font-size: 15px;
    opacity: 0;
    flex-shrink: 0;
}
.btn-theme-row.theme-selected .theme-row-check {
    opacity: 1;
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
