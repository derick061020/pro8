import { b as brandName, _ as __vitePreload } from "./index.2bb13d6f.js";
import { ap as defineAsyncComponent, r as ref, a as computed } from "./vendor.05b8b5b5.js";
const NavbarLayout = defineAsyncComponent(() => __vitePreload(() => import("./AppLayout.c5f8dd87.js"), true ? ["assets/AppLayout.c5f8dd87.js","assets/AppLayout.a694544e.css","assets/VButton.b4d67cab.js","assets/VButton.4bd674d0.css","assets/vendor.05b8b5b5.js","assets/plugin-vue_export-helper.5a098b48.js","assets/VIconButton.dcb74fc8.js","assets/IsotipoMozoOficial.76c4accc.js","assets/IsotipoMozoOficial.6fad2d75.css","assets/VAvatar.0f0bd550.js","assets/VControl.9555ec96.js","assets/VControl.66ed690d.css","assets/VField.152a42ff.js","assets/VModal.9533b2d3.js","assets/VModal.d8de09e0.css","assets/index.2bb13d6f.js","assets/index.784c2c73.css","assets/VDropdown.53f4f138.js","assets/VDropdown.6c1d270c.css","assets/VIcon.f7f680f8.js","assets/masterService.8a758d8d.js"] : void 0));
const NavbarDropdownLayout = defineAsyncComponent(() => __vitePreload(() => import("./AppLayout.c5f8dd87.js"), true ? ["assets/AppLayout.c5f8dd87.js","assets/AppLayout.a694544e.css","assets/VButton.b4d67cab.js","assets/VButton.4bd674d0.css","assets/vendor.05b8b5b5.js","assets/plugin-vue_export-helper.5a098b48.js","assets/VIconButton.dcb74fc8.js","assets/IsotipoMozoOficial.76c4accc.js","assets/IsotipoMozoOficial.6fad2d75.css","assets/VAvatar.0f0bd550.js","assets/VControl.9555ec96.js","assets/VControl.66ed690d.css","assets/VField.152a42ff.js","assets/VModal.9533b2d3.js","assets/VModal.d8de09e0.css","assets/index.2bb13d6f.js","assets/index.784c2c73.css","assets/VDropdown.53f4f138.js","assets/VDropdown.6c1d270c.css","assets/VIcon.f7f680f8.js","assets/masterService.8a758d8d.js"] : void 0));
const NavbarSearchLayout = defineAsyncComponent(() => __vitePreload(() => import("./AppLayout.c5f8dd87.js"), true ? ["assets/AppLayout.c5f8dd87.js","assets/AppLayout.a694544e.css","assets/VButton.b4d67cab.js","assets/VButton.4bd674d0.css","assets/vendor.05b8b5b5.js","assets/plugin-vue_export-helper.5a098b48.js","assets/VIconButton.dcb74fc8.js","assets/IsotipoMozoOficial.76c4accc.js","assets/IsotipoMozoOficial.6fad2d75.css","assets/VAvatar.0f0bd550.js","assets/VControl.9555ec96.js","assets/VControl.66ed690d.css","assets/VField.152a42ff.js","assets/VModal.9533b2d3.js","assets/VModal.d8de09e0.css","assets/index.2bb13d6f.js","assets/index.784c2c73.css","assets/VDropdown.53f4f138.js","assets/VDropdown.6c1d270c.css","assets/VIcon.f7f680f8.js","assets/masterService.8a758d8d.js"] : void 0));
const layoutsComponents = {
  "navbar-default": NavbarLayout,
  "navbar-fade": NavbarLayout,
  "navbar-colored": NavbarLayout,
  "navbar-dropdown": NavbarDropdownLayout,
  "navbar-dropdown-colored": NavbarDropdownLayout,
  "navbar-clean": NavbarSearchLayout,
  "navbar-clean-center": NavbarSearchLayout,
  "navbar-clean-fade": NavbarSearchLayout
};
const navbarLayoutId = ref("navbar-default");
computed(() => {
  return layoutsComponents[navbarLayoutId.value] || NavbarLayout;
});
computed(() => {
  switch (navbarLayoutId.value) {
    case "navbar-fade":
    case "navbar-clean-fade":
      return "fade";
    case "navbar-colored":
    case "navbar-dropdown-colored":
      return "colored";
    case "navbar-clean-center":
      return "center";
    default:
      return "default";
  }
});
const pageTitle = computed(() => brandName.value);
export { pageTitle as p };
