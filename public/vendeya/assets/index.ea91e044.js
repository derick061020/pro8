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
import { M as Module, u as useStorage, c as createI18n$1, d as defineStore, r as ref, a as computed, b as defineComponent, e as useHead, o as onMounted, f as openBlock, g as createElementBlock, h as createRouter$1, i as createWebHistory, n as nprogress$1, j as axios, p as provide, N as Notyf, k as createHead, l as createPinia, m as createApp$1, q as h, R as RouterView, s as resolveDynamicComponent, T as Transition } from "./vendor.80d481a8.js";
const p = function polyfill() {
  const relList = document.createElement("link").relList;
  if (relList && relList.supports && relList.supports("modulepreload")) {
    return;
  }
  for (const link of document.querySelectorAll('link[rel="modulepreload"]')) {
    processPreload(link);
  }
  new MutationObserver((mutations) => {
    for (const mutation of mutations) {
      if (mutation.type !== "childList") {
        continue;
      }
      for (const node of mutation.addedNodes) {
        if (node.tagName === "LINK" && node.rel === "modulepreload")
          processPreload(node);
      }
    }
  }).observe(document, { childList: true, subtree: true });
  function getFetchOpts(script) {
    const fetchOpts = {};
    if (script.integrity)
      fetchOpts.integrity = script.integrity;
    if (script.referrerpolicy)
      fetchOpts.referrerPolicy = script.referrerpolicy;
    if (script.crossorigin === "use-credentials")
      fetchOpts.credentials = "include";
    else if (script.crossorigin === "anonymous")
      fetchOpts.credentials = "omit";
    else
      fetchOpts.credentials = "same-origin";
    return fetchOpts;
  }
  function processPreload(link) {
    if (link.ep)
      return;
    link.ep = true;
    const fetchOpts = getFetchOpts(link);
    fetch(link.href, fetchOpts);
  }
};
p();
const Iconify = Module.default || Module;
const collections = JSON.parse('[{"prefix":"ion","width":512,"height":512,"icons":{"reload-outline":{"body":"<path d=\\"M400 148l-21.12-24.57A191.43 191.43 0 0 0 240 64C134 64 48 150 48 256s86 192 192 192a192.09 192.09 0 0 0 181.07-128\\" fill=\\"none\\" stroke=\\"currentColor\\" stroke-linecap=\\"round\\" stroke-miterlimit=\\"10\\" stroke-width=\\"32\\"/><path d=\\"M464 97.42V208a16 16 0 0 1-16 16H337.42c-14.26 0-21.4-17.23-11.32-27.31L436.69 86.1C446.77 76 464 83.16 464 97.42z\\" fill=\\"currentColor\\"/>"}}},{"prefix":"feather","width":24,"height":24,"icons":{"activity":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M22 12h-4l-3 9L9 3l-3 9H2\\"/></g>"},"airplay":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1\\"/><path d=\\"M12 15l5 6H7l5-6z\\"/></g>"},"arrow-right":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M5 12h14\\"/><path d=\\"M12 5l7 7l-7 7\\"/></g>"},"bell":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9\\"/><path d=\\"M13.73 21a2 2 0 0 1-3.46 0\\"/></g>"},"book":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M4 19.5A2.5 2.5 0 0 1 6.5 17H20\\"/><path d=\\"M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z\\"/></g>"},"briefcase":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"2\\" y=\\"7\\" width=\\"20\\" height=\\"14\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16\\"/></g>"},"calendar":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"3\\" y=\\"4\\" width=\\"18\\" height=\\"18\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M16 2v4\\"/><path d=\\"M8 2v4\\"/><path d=\\"M3 10h18\\"/></g>"},"check":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M20 6L9 17l-5-5\\"/></g>"},"check-square":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M9 11l3 3L22 4\\"/><path d=\\"M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11\\"/></g>"},"chevron-down":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M6 9l6 6l6-6\\"/></g>"},"chevron-left":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M15 18l-6-6l6-6\\"/></g>"},"chevron-right":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M9 18l6-6l-6-6\\"/></g>"},"circle":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"10\\"/></g>"},"clock":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"10\\"/><path d=\\"M12 6v6l4 2\\"/></g>"},"credit-card":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"1\\" y=\\"4\\" width=\\"22\\" height=\\"16\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M1 10h22\\"/></g>"},"edit":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\\"/><path d=\\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1l1-4l9.5-9.5z\\"/></g>"},"edit-2":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5L2 22l1.5-5.5L17 3z\\"/></g>"},"feather":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z\\"/><path d=\\"M16 8L2 22\\"/><path d=\\"M17.5 15H9\\"/></g>"},"file":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z\\"/><path d=\\"M13 2v7h7\\"/></g>"},"github":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77A5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22\\"/></g>"},"gitlab":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M22.65 14.39L12 22.13L1.35 14.39a.84.84 0 0 1-.3-.94l1.22-3.78l2.44-7.51A.42.42 0 0 1 4.82 2a.43.43 0 0 1 .58 0a.42.42 0 0 1 .11.18l2.44 7.49h8.1l2.44-7.51A.42.42 0 0 1 18.6 2a.43.43 0 0 1 .58 0a.42.42 0 0 1 .11.18l2.44 7.51L23 13.45a.84.84 0 0 1-.35.94z\\"/></g>"},"globe":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"10\\"/><path d=\\"M2 12h20\\"/><path d=\\"M12 2a15.3 15.3 0 0 1 4 10a15.3 15.3 0 0 1-4 10a15.3 15.3 0 0 1-4-10a15.3 15.3 0 0 1 4-10z\\"/></g>"},"grid":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M3 3h7v7H3z\\"/><path d=\\"M14 3h7v7h-7z\\"/><path d=\\"M14 14h7v7h-7z\\"/><path d=\\"M3 14h7v7H3z\\"/></g>"},"layout":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"3\\" y=\\"3\\" width=\\"18\\" height=\\"18\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M3 9h18\\"/><path d=\\"M9 21V9\\"/></g>"},"link":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71\\"/><path d=\\"M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71\\"/></g>"},"list":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M8 6h13\\"/><path d=\\"M8 12h13\\"/><path d=\\"M8 18h13\\"/><path d=\\"M3 6h.01\\"/><path d=\\"M3 12h.01\\"/><path d=\\"M3 18h.01\\"/></g>"},"lock":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"3\\" y=\\"11\\" width=\\"18\\" height=\\"11\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M7 11V7a5 5 0 0 1 10 0v4\\"/></g>"},"log-out":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4\\"/><path d=\\"M16 17l5-5l-5-5\\"/><path d=\\"M21 12H9\\"/></g>"},"mail":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z\\"/><path d=\\"M22 6l-10 7L2 6\\"/></g>"},"map-pin":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z\\"/><circle cx=\\"12\\" cy=\\"10\\" r=\\"3\\"/></g>"},"message-circle":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M21 11.5a8.38 8.38 0 0 1-.9 3.8a8.5 8.5 0 0 1-7.6 4.7a8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8a8.5 8.5 0 0 1 4.7-7.6a8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z\\"/></g>"},"moon":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M21 12.79A9 9 0 1 1 11.21 3A7 7 0 0 0 21 12.79z\\"/></g>"},"more-vertical":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"1\\"/><circle cx=\\"12\\" cy=\\"5\\" r=\\"1\\"/><circle cx=\\"12\\" cy=\\"19\\" r=\\"1\\"/></g>"},"phone-call":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M15.05 5A5 5 0 0 1 19 8.95M15.05 1A9 9 0 0 1 23 8.94m-1 7.98v3a2 2 0 0 1-2.18 2a19.79 19.79 0 0 1-8.63-3.07a19.5 19.5 0 0 1-6-6a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72a12.84 12.84 0 0 0 .7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45a12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z\\"/></g>"},"play":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M5 3l14 9l-14 9V3z\\"/></g>"},"plus":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M12 5v14\\"/><path d=\\"M5 12h14\\"/></g>"},"search":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"11\\" cy=\\"11\\" r=\\"8\\"/><path d=\\"M21 21l-4.35-4.35\\"/></g>"},"settings":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"3\\"/><path d=\\"M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33a1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2a2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0a2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2a2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83a2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0a2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2a2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z\\"/></g>"},"shopping-cart":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"9\\" cy=\\"21\\" r=\\"1\\"/><circle cx=\\"20\\" cy=\\"21\\" r=\\"1\\"/><path d=\\"M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6\\"/></g>"},"sidebar":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><rect x=\\"3\\" y=\\"3\\" width=\\"18\\" height=\\"18\\" rx=\\"2\\" ry=\\"2\\"/><path d=\\"M9 3v18\\"/></g>"},"smile":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"10\\"/><path d=\\"M8 14s1.5 2 4 2s4-2 4-2\\"/><path d=\\"M9 9h.01\\"/><path d=\\"M15 9h.01\\"/></g>"},"sun":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><circle cx=\\"12\\" cy=\\"12\\" r=\\"5\\"/><path d=\\"M12 1v2\\"/><path d=\\"M12 21v2\\"/><path d=\\"M4.22 4.22l1.42 1.42\\"/><path d=\\"M18.36 18.36l1.42 1.42\\"/><path d=\\"M1 12h2\\"/><path d=\\"M21 12h2\\"/><path d=\\"M4.22 19.78l1.42-1.42\\"/><path d=\\"M18.36 5.64l1.42-1.42\\"/></g>"},"user":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2\\"/><circle cx=\\"12\\" cy=\\"7\\" r=\\"4\\"/></g>"},"x":{"body":"<g fill=\\"none\\" stroke=\\"currentColor\\" stroke-width=\\"2\\" stroke-linecap=\\"round\\" stroke-linejoin=\\"round\\"><path d=\\"M18 6L6 18\\"/><path d=\\"M6 6l12 12\\"/></g>"}}},{"prefix":"fa","width":1536,"height":1536,"icons":{"angle-down":{"body":"<path d=\\"M1011 480q0 13-10 23L535 969q-10 10-23 10t-23-10L23 503q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l393 393l393-393q10-10 23-10t23 10l50 50q10 10 10 23z\\" fill=\\"currentColor\\"/>","width":1024,"height":1280,"inlineTop":-256},"angle-up":{"body":"<path d=\\"M1011 928q0 13-10 23l-50 50q-10 10-23 10t-23-10L512 608l-393 393q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l466 466q10 10 10 23z\\" fill=\\"currentColor\\"/>","width":1024,"height":1280,"inlineTop":-256}}},{"prefix":"et","width":32,"height":32,"icons":{}},{"prefix":"fa-brands","width":448,"height":512,"icons":{"amazon":{"body":"<path d=\\"M257.2 162.7c-48.7 1.8-169.5 15.5-169.5 117.5c0 109.5 138.3 114 183.5 43.2c6.5 10.2 35.4 37.5 45.3 46.8l56.8-56S341 288.9 341 261.4V114.3C341 89 316.5 32 228.7 32C140.7 32 94 87 94 136.3l73.5 6.8c16.3-49.5 54.2-49.5 54.2-49.5c40.7-.1 35.5 29.8 35.5 69.1zm0 86.8c0 80-84.2 68-84.2 17.2c0-47.2 50.5-56.7 84.2-57.8v40.6zm136 163.5c-7.7 10-70 67-174.5 67S34.2 408.5 9.7 379c-6.8-7.7 1-11.3 5.5-8.3C88.5 415.2 203 488.5 387.7 401c7.5-3.7 13.3 2 5.5 12zm39.8 2.2c-6.5 15.8-16 26.8-21.2 31c-5.5 4.5-9.5 2.7-6.5-3.8s19.3-46.5 12.7-55c-6.5-8.3-37-4.3-48-3.2c-10.8 1-13 2-14-.3c-2.3-5.7 21.7-15.5 37.5-17.5c15.7-1.8 41-.8 46 5.7c3.7 5.1 0 27.1-6.5 43.1z\\" fill=\\"currentColor\\"/>"},"dribbble":{"body":"<path d=\\"M256 8C119.252 8 8 119.252 8 256s111.252 248 248 248s248-111.252 248-248S392.748 8 256 8zm163.97 114.366c29.503 36.046 47.369 81.957 47.835 131.955c-6.984-1.477-77.018-15.682-147.502-6.818c-5.752-14.041-11.181-26.393-18.617-41.614c78.321-31.977 113.818-77.482 118.284-83.523zM396.421 97.87c-3.81 5.427-35.697 48.286-111.021 76.519c-34.712-63.776-73.185-116.168-79.04-124.008c67.176-16.193 137.966 1.27 190.061 47.489zm-230.48-33.25c5.585 7.659 43.438 60.116 78.537 122.509c-99.087 26.313-186.36 25.934-195.834 25.809C62.38 147.205 106.678 92.573 165.941 64.62zM44.17 256.323c0-2.166.043-4.322.108-6.473c9.268.19 111.92 1.513 217.706-30.146c6.064 11.868 11.857 23.915 17.174 35.949c-76.599 21.575-146.194 83.527-180.531 142.306C64.794 360.405 44.17 310.73 44.17 256.323zm81.807 167.113c22.127-45.233 82.178-103.622 167.579-132.756c29.74 77.283 42.039 142.053 45.189 160.638c-68.112 29.013-150.015 21.053-212.768-27.882zm248.38 8.489c-2.171-12.886-13.446-74.897-41.152-151.033c66.38-10.626 124.7 6.768 131.947 9.055c-9.442 58.941-43.273 109.844-90.795 141.978z\\" fill=\\"currentColor\\"/>","width":512},"facebook-f":{"body":"<path d=\\"M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\\" fill=\\"currentColor\\"/>","width":320},"github-alt":{"body":"<path d=\\"M186.1 328.7c0 20.9-10.9 55.1-36.7 55.1s-36.7-34.2-36.7-55.1s10.9-55.1 36.7-55.1s36.7 34.2 36.7 55.1zM480 278.2c0 31.9-3.2 65.7-17.5 95c-37.9 76.6-142.1 74.8-216.7 74.8c-75.8 0-186.2 2.7-225.6-74.8c-14.6-29-20.2-63.1-20.2-95c0-41.9 13.9-81.5 41.5-113.6c-5.2-15.8-7.7-32.4-7.7-48.8c0-21.5 4.9-32.3 14.6-51.8c45.3 0 74.3 9 108.8 36c29-6.9 58.8-10 88.7-10c27 0 54.2 2.9 80.4 9.2c34-26.7 63-35.2 107.8-35.2c9.8 19.5 14.6 30.3 14.6 51.8c0 16.4-2.6 32.7-7.7 48.2c27.5 32.4 39 72.3 39 114.2zm-64.3 50.5c0-43.9-26.7-82.6-73.5-82.6c-18.9 0-37 3.4-56 6c-14.9 2.3-29.8 3.2-45.1 3.2c-15.2 0-30.1-.9-45.1-3.2c-18.7-2.6-37-6-56-6c-46.8 0-73.5 38.7-73.5 82.6c0 87.8 80.4 101.3 150.4 101.3h48.2c70.3 0 150.6-13.4 150.6-101.3zm-82.6-55.1c-25.8 0-36.7 34.2-36.7 55.1s10.9 55.1 36.7 55.1s36.7-34.2 36.7-55.1s-10.9-55.1-36.7-55.1z\\" fill=\\"currentColor\\"/>","width":480},"google-plus-g":{"body":"<path d=\\"M386.061 228.496c1.834 9.692 3.143 19.384 3.143 31.956C389.204 370.205 315.599 448 204.8 448c-106.084 0-192-85.915-192-192s85.916-192 192-192c51.864 0 95.083 18.859 128.611 50.292l-52.126 50.03c-14.145-13.621-39.028-29.599-76.485-29.599c-65.484 0-118.92 54.221-118.92 121.277c0 67.056 53.436 121.277 118.92 121.277c75.961 0 104.513-54.745 108.965-82.773H204.8v-66.009h181.261zm185.406 6.437V179.2h-56.001v55.733h-55.733v56.001h55.733v55.733h56.001v-55.733H627.2v-56.001h-55.733z\\" fill=\\"currentColor\\"/>","width":640},"instagram":{"body":"<path d=\\"M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9S287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7s74.7 33.5 74.7 74.7s-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8c-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8s26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9c-26.2-26.2-58-34.4-93.9-36.2c-37-2.1-147.9-2.1-184.9 0c-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9c1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0c35.9-1.7 67.7-9.9 93.9-36.2c26.2-26.2 34.4-58 36.2-93.9c2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6c-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6c-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6c29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6c11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z\\" fill=\\"currentColor\\"/>"},"invision":{"body":"<path d=\\"M407.4 32H40.6C18.2 32 0 50.2 0 72.6v366.8C0 461.8 18.2 480 40.6 480h366.8c22.4 0 40.6-18.2 40.6-40.6V72.6c0-22.4-18.2-40.6-40.6-40.6zM176.1 145.6c.4 23.4-22.4 27.3-26.6 27.4c-14.9 0-27.1-12-27.1-27c.1-35.2 53.1-35.5 53.7-.4zM332.8 377c-65.6 0-34.1-74-25-106.6c14.1-46.4-45.2-59-59.9.7l-25.8 103.3H177l8.1-32.5c-31.5 51.8-94.6 44.4-94.6-4.3c.1-14.3.9-14 23-104.1H81.7l9.7-35.6h76.4c-33.6 133.7-32.6 126.9-32.9 138.2c0 20.9 40.9 13.5 57.4-23.2l19.8-79.4h-32.3l9.7-35.6h68.8l-8.9 40.5c40.5-75.5 127.9-47.8 101.8 38c-14.2 51.1-14.6 50.7-14.9 58.8c0 15.5 17.5 22.6 31.8-16.9L386 325c-10.5 36.7-29.4 52-53.2 52z\\" fill=\\"currentColor\\"/>"},"linkedin-in":{"body":"<path d=\\"M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2c-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3c94 0 111.28 61.9 111.28 142.3V448z\\" fill=\\"currentColor\\"/>"},"reddit-alien":{"body":"<path d=\\"M440.3 203.5c-15 0-28.2 6.2-37.9 15.9c-35.7-24.7-83.8-40.6-137.1-42.3L293 52.3l88.2 19.8c0 21.6 17.6 39.2 39.2 39.2c22 0 39.7-18.1 39.7-39.7s-17.6-39.7-39.7-39.7c-15.4 0-28.7 9.3-35.3 22l-97.4-21.6c-4.9-1.3-9.7 2.2-11 7.1L246.3 177c-52.9 2.2-100.5 18.1-136.3 42.8c-9.7-10.1-23.4-16.3-38.4-16.3c-55.6 0-73.8 74.6-22.9 100.1c-1.8 7.9-2.6 16.3-2.6 24.7c0 83.8 94.4 151.7 210.3 151.7c116.4 0 210.8-67.9 210.8-151.7c0-8.4-.9-17.2-3.1-25.1c49.9-25.6 31.5-99.7-23.8-99.7zM129.4 308.9c0-22 17.6-39.7 39.7-39.7c21.6 0 39.2 17.6 39.2 39.7c0 21.6-17.6 39.2-39.2 39.2c-22 .1-39.7-17.6-39.7-39.2zm214.3 93.5c-36.4 36.4-139.1 36.4-175.5 0c-4-3.5-4-9.7 0-13.7c3.5-3.5 9.7-3.5 13.2 0c27.8 28.5 120 29 149 0c3.5-3.5 9.7-3.5 13.2 0c4.1 4 4.1 10.2.1 13.7zm-.8-54.2c-21.6 0-39.2-17.6-39.2-39.2c0-22 17.6-39.7 39.2-39.7c22 0 39.7 17.6 39.7 39.7c-.1 21.5-17.7 39.2-39.7 39.2z\\" fill=\\"currentColor\\"/>","width":512},"tumblr":{"body":"<path d=\\"M309.8 480.3c-13.6 14.5-50 31.7-97.4 31.7c-120.8 0-147-88.8-147-140.6v-144H17.9c-5.5 0-10-4.5-10-10v-68c0-7.2 4.5-13.6 11.3-16c62-21.8 81.5-76 84.3-117.1c.8-11 6.5-16.3 16.1-16.3h70.9c5.5 0 10 4.5 10 10v115.2h83c5.5 0 10 4.4 10 9.9v81.7c0 5.5-4.5 10-10 10h-83.4V360c0 34.2 23.7 53.6 68 35.8c4.8-1.9 9-3.2 12.7-2.2c3.5.9 5.8 3.4 7.4 7.9l22 64.3c1.8 5 3.3 10.6-.4 14.5z\\" fill=\\"currentColor\\"/>","width":320},"twitter":{"body":"<path d=\\"M459.37 151.716c.325 4.548.325 9.097.325 13.645c0 138.72-105.583 298.558-298.558 298.558c-59.452 0-114.68-17.219-161.137-47.106c8.447.974 16.568 1.299 25.34 1.299c49.055 0 94.213-16.568 130.274-44.832c-46.132-.975-84.792-31.188-98.112-72.772c6.498.974 12.995 1.624 19.818 1.624c9.421 0 18.843-1.3 27.614-3.573c-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319c-28.264-18.843-46.781-51.005-46.781-87.391c0-19.492 5.197-37.36 14.294-52.954c51.655 63.675 129.3 105.258 216.365 109.807c-1.624-7.797-2.599-15.918-2.599-24.04c0-57.828 46.782-104.934 104.934-104.934c30.213 0 57.502 12.67 76.67 33.137c23.715-4.548 46.456-13.32 66.599-25.34c-7.798 24.366-24.366 44.833-46.132 57.827c21.117-2.273 41.584-8.122 60.426-16.243c-14.292 20.791-32.161 39.308-52.628 54.253z\\" fill=\\"currentColor\\"/>","width":512},"youtube":{"body":"<path d=\\"M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597c-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821c11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205l-142.739 81.201z\\" fill=\\"currentColor\\"/>","width":576}}}]');
collections.forEach((c) => Iconify.addCollection(c));
var nprogress = "";
var _default$1 = "";
var _default = "";
var simplebar = "";
var tinySlider = "";
var notyf_min = "";
var tippy = "";
var svgArrow = "";
var border = "";
var backdrop = "";
var light = "";
var main = "";
const scriptRel = "modulepreload";
const seen = {};
const base = "/vendeya/";
const __vitePreload = function preload(baseModule, deps) {
  if (!deps || deps.length === 0) {
    return baseModule();
  }
  return Promise.all(deps.map((dep) => {
    dep = `${base}${dep}`;
    if (dep in seen)
      return;
    seen[dep] = true;
    const isCss = dep.endsWith(".css");
    const cssSelector = isCss ? '[rel="stylesheet"]' : "";
    if (document.querySelector(`link[href="${dep}"]${cssSelector}`)) {
      return;
    }
    const link = document.createElement("link");
    link.rel = isCss ? "stylesheet" : scriptRel;
    if (!isCss) {
      link.as = "script";
      link.crossOrigin = "";
    }
    link.href = dep;
    document.head.appendChild(link);
    if (isCss) {
      return new Promise((res, rej) => {
        link.addEventListener("load", res);
        link.addEventListener("error", rej);
      });
    }
  })).then(() => baseModule());
};
var messages = {
  "de": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["Sprache ausw\xE4hlen"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Jetzt mitmachen"]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Beginnen Sie mit der Erstellung Ihres Kontos"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Erhalten Sie Werbeangebote"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Ich habe bereits ein Konto"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Registrieren"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Name"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Mailadresse"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Passwort"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Passwort\xFCberpr\xFCfung"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Ihr Name, Vorname ist erforderlich"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Geben Sie Ihre E-Mail ein, sie wird f\xFCr die Anmeldung ben\xF6tigt"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Bitte geben Sie eine g\xFCltige E-Mail ein"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Geben Sie Ihr Passwort mit mindestens 8 Zeichen ein, es wird f\xFCr die Anmeldung ben\xF6tigt"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Das Passwort sollte mindestens 8 Zeichen enthalten"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Bitte best\xE4tigen Sie Ihr Passwort"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Das Passwort stimmt nicht \xFCberein"]);
          }
        }
      }
    }
  },
  "en": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["Select Language"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Join Us Now."]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Start by creating your account"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Receive promotional offers"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["I already have an account"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Sign Up"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Name"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Email Address"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Password"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Password Verification"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Your name first name is required"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Enter your email, it will be required to login"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Please, enter a valid email"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Enter your password with at least 8 characters, it will be required to login"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["The password should contains at leat 8 characters"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Please, confirm your password"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["The password does not match"]);
          }
        }
      }
    }
  },
  "es-MX": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["Seleccione el idioma"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["\xDAnete a nosotros ahora"]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Empieza creando tu cuenta"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recibe ofertas promocionales"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Ya tengo una cuenta"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Registrarse"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Nombre"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Direcci\xF3n de correo electr\xF3nico"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Contrase\xF1a"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Verificaci\xF3n de contrase\xF1a"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Su nombre es obligatorio"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su correo electr\xF3nico, ser\xE1 necesario para iniciar la sesi\xF3n"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Por favor, introduzca un correo electr\xF3nico v\xE1lido"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su contrase\xF1a con al menos 8 caracteres, ser\xE1 necesaria para iniciar la sesi\xF3n"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["La contrase\xF1a debe contener al menos 8 caracteres"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Por favor, confirme su contrase\xF1a"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["La contrase\xF1a no coincide"]);
          }
        }
      }
    }
  },
  "es": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["Seleccione el idioma"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["\xDAnete a nosotros ahora"]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Empieza creando tu cuenta"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recibe ofertas promocionales"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Ya tengo una cuenta"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Registrarse"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Nombre"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Direcci\xF3n de correo electr\xF3nico"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Contrase\xF1a"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Verificaci\xF3n de contrase\xF1a"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Su nombre es obligatorio"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su correo electr\xF3nico, ser\xE1 necesario para iniciar la sesi\xF3n"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Por favor, introduzca un correo electr\xF3nico v\xE1lido"]);
          }
        },
        "gitlab": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su usuario de gitlab, ser\xE1 necesario para confirmar su acceso"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Un usuario mayormente no contiene m\xE1s de 20 caracteres"]);
          }
        },
        "domain": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su nombre de dominio o URL, ser\xE1 necesario para confirmar su acceso"]);
          }
        },
        "department": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Seleccione un departamento"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Introduzca su contrase\xF1a con al menos 8 caracteres, ser\xE1 necesaria para iniciar la sesi\xF3n"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["La contrase\xF1a debe contener al menos 8 caracteres"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Por favor, confirme su contrase\xF1a"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["La contrase\xF1a no coincide"]);
          }
        }
      }
    }
  },
  "fr": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["S\xE9lectionnez une langue"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Rejoignez-nous maintenant"]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["Commencez par cr\xE9er votre compte"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Recevez des offres promotionnelles"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["J'ai d\xE9j\xE0 un compte"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Cr\xE9er un compte"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Nom"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Adresse \xE9lectronique"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["Mot de passe"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["V\xE9rification du mot de passe"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Votre nom, pr\xE9nom est obligatoire"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Entrez votre email, il sera n\xE9cessaire pour vous connecter"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Veuillez entrer une adresse \xE9lectronique valide"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Entrez votre mot de passe avec au moins 8 caract\xE8res, il vous sera demand\xE9 pour vous connecter"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Le mot de passe doit contenir au moins 8 caract\xE8res"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Veuillez confirmer votre mot de passe"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["Le mot de passe ne correspond pas"]);
          }
        }
      }
    }
  },
  "zh-CN": {
    "select-language": (ctx) => {
      const { normalize: _normalize } = ctx;
      return _normalize(["\u9009\u62E9\u8BED\u8A00"]);
    },
    "auth": {
      "title": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["\u73B0\u5728\u5C31\u52A0\u5165\u6211\u4EEC"]);
      },
      "subtitle": (ctx) => {
        const { normalize: _normalize } = ctx;
        return _normalize(["\u4ECE\u521B\u5EFA\u4F60\u7684\u8D26\u6237\u5F00\u59CB"]);
      },
      "label": {
        "promotional": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u63A5\u6536\u4FC3\u9500\u4F18\u60E0"]);
        }
      },
      "action": {
        "login": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u6211\u5DF2\u7ECF\u6709\u4E00\u4E2A\u8D26\u6237"]);
        },
        "signup": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u6CE8\u518C"]);
        }
      },
      "placeholder": {
        "name": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u59D3\u540D"]);
        },
        "email": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u7535\u5B50\u90AE\u4EF6\u5730\u5740"]);
        },
        "password": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u5BC6\u7801"]);
        },
        "passwordCheck": (ctx) => {
          const { normalize: _normalize } = ctx;
          return _normalize(["\u5BC6\u7801\u9A8C\u8BC1"]);
        }
      },
      "errors": {
        "name": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u60A8\u7684\u59D3\u540D\u662F\u5FC5\u586B\u7684"]);
          }
        },
        "email": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u8F93\u5165\u4F60\u7684\u7535\u5B50\u90AE\u4EF6\uFF0C\u5B83\u5C06\u662F\u767B\u5F55\u7684\u5FC5\u8981\u6761\u4EF6"]);
          },
          "format": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u8BF7\u8F93\u5165\u4E00\u4E2A\u6709\u6548\u7684\u7535\u5B50\u90AE\u4EF6"]);
          }
        },
        "password": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u8BF7\u8F93\u5165\u4F60\u7684\u5BC6\u7801\uFF0C\u81F3\u5C11\u67098\u4E2A\u5B57\u7B26\uFF0C\u767B\u5F55\u65F6\u5FC5\u987B\u8F93\u5165"]);
          },
          "length": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u5BC6\u7801\u5E94\u81F3\u5C11\u5305\u542B8\u4E2A\u5B57\u7B26"]);
          }
        },
        "passwordCheck": {
          "required": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u8BF7\u786E\u8BA4\u60A8\u7684\u5BC6\u7801"]);
          },
          "match": (ctx) => {
            const { normalize: _normalize } = ctx;
            return _normalize(["\u8BE5\u5BC6\u7801\u4E0D\u5339\u914D"]);
          }
        }
      }
    }
  }
};
function createI18n() {
  const defaultLocale = useStorage("locale", (navigator == null ? void 0 : navigator.language) || "en");
  const i18n = createI18n$1({
    locale: defaultLocale.value,
    messages
  });
  return i18n;
}
const useUserSession = defineStore("userSession", () => {
  const token = useStorage("token", "");
  const email = useStorage("email", "");
  const name = useStorage("name", "");
  const url = useStorage("url", "");
  const ssl = useStorage("ssl", "");
  const pwd = useStorage("pwd", "");
  const userRole = useStorage("userRole", "");
  const establishmentId = useStorage("establishmentId", "");
  const socketSessionId = useStorage("socketSessionId", "");
  const urlLogo = useStorage("urlLogo", "");
  const logoBase64 = useStorage("logoBase64", "");
  const cashId = useStorage("cashId", 0);
  const cashDescription = useStorage("cashDescription", "");
  const printerName = useStorage("printerName", "");
  const printerNameCommand = useStorage("printerNameCommand", "");
  const printerNamePreOrder = useStorage("printerNamePreOrder", "");
  const printerNameDocument = useStorage("printerNameDocument", "");
  const sellerId = useStorage("sellerId", 1);
  const sellerName = useStorage("sellerName", "");
  const isBlockedPin = useStorage("isBlockedPin", 0);
  const searchByBarcode = useStorage("searchByBarcode", 0);
  const isCheckLetterZise = useStorage("isCheckLetterZise", 0);
  const urlLanding = useStorage("urlLanding", "");
  const isBusinessTurnTap = useStorage("isBusinessTurnTap", 0);
  const brandName2 = useStorage("brandName", "");
  const isStoreEnabled = useStorage("isStoreEnabled", "false");
  const user = ref();
  const loading = ref(true);
  const isLoggedIn = computed(() => token.value !== void 0 && token.value !== "");
  function setUser(newUser) {
    user.value = newUser;
  }
  function setToken(newToken) {
    token.value = newToken;
  }
  function setEmail(value) {
    email.value = value;
  }
  function setName(value) {
    name.value = value;
  }
  function setUrl(value) {
    url.value = value;
  }
  function setSsl(value) {
    ssl.value = value;
  }
  function setUrlLanding(value) {
    urlLanding.value = value;
  }
  function setPwd(value) {
    pwd.value = value;
  }
  function setLoading(newLoading) {
    loading.value = newLoading;
  }
  function setRole(value) {
    userRole.value = value;
  }
  function setSocketSessionId(value) {
    socketSessionId.value = value;
  }
  function setEstablishmentId(value) {
    establishmentId.value = value;
  }
  function setUrlLogo(value) {
    urlLogo.value = value;
  }
  function setLogoBase64(value) {
    logoBase64.value = value;
  }
  async function logoutUser() {
    token.value = void 0;
    user.value = void 0;
  }
  function setCashId(value) {
    cashId.value = value;
  }
  function setPrinterName(value) {
    printerName.value = value;
  }
  function setPrinterNameCommand(value) {
    printerNameCommand.value = value;
  }
  function setPrinterNamePreOrder(value) {
    printerNamePreOrder.value = value;
  }
  function setPrinterNameDocument(value) {
    printerNameDocument.value = value;
  }
  function setCashDescription(value) {
    cashDescription.value = value;
  }
  function setBrandName(value) {
    brandName2.value = value;
  }
  function setIsStoreEnabled(value) {
    isStoreEnabled.value = value;
  }
  function getRole() {
    return userRole.value;
  }
  function getCashId() {
    return cashId.value;
  }
  function setSellerId(value) {
    sellerId.value = value;
  }
  function setIsBlockedPin(value) {
    isBlockedPin.value = value;
  }
  function setSellerName(value) {
    sellerName.value = value;
  }
  function setSearchByBarcode(value) {
    searchByBarcode.value = value;
  }
  function setIsCheckLetterZise(value) {
    isCheckLetterZise.value = value;
  }
  function setIsBusinessTurnTap(value) {
    isBusinessTurnTap.value = value;
  }
  function getBrandName() {
    return brandName2.value;
  }
  function getIsStoreEnabled() {
    return isStoreEnabled.value;
  }
  return {
    user,
    token,
    email,
    name,
    url,
    ssl,
    urlLanding,
    pwd,
    isLoggedIn,
    loading,
    userRole,
    socketSessionId,
    establishmentId,
    urlLogo,
    logoBase64,
    printerName,
    printerNameCommand,
    printerNamePreOrder,
    printerNameDocument,
    sellerId,
    isBlockedPin,
    sellerName,
    searchByBarcode,
    cashDescription,
    isCheckLetterZise,
    isBusinessTurnTap,
    logoutUser,
    setUser,
    setToken,
    setLoading,
    setEmail,
    setName,
    setSsl,
    setUrl,
    setUrlLanding,
    setPwd,
    setRole,
    setSocketSessionId,
    setEstablishmentId,
    setUrlLogo,
    setLogoBase64,
    setCashId,
    getRole,
    getCashId,
    setPrinterName,
    setPrinterNameCommand,
    setPrinterNamePreOrder,
    setPrinterNameDocument,
    setIsBlockedPin,
    setSellerId,
    setSellerName,
    setSearchByBarcode,
    setCashDescription,
    setIsCheckLetterZise,
    setIsBusinessTurnTap,
    getBrandName,
    getIsStoreEnabled,
    setBrandName,
    setIsStoreEnabled
  };
});
var index_vue_vue_type_style_index_0_lang = "";
const _sfc_main = /* @__PURE__ */ defineComponent({
  setup(__props) {
    const userSession = useUserSession();
    ref(false);
    userSession.getBrandName();
    const isStoreEnabled = userSession.getIsStoreEnabled();
    useHead({
      title: "Vendeya.pe"
    });
    onMounted(() => {
      if (isStoreEnabled === "false") {
        location.href = "/vendeya/auth/login";
      }
    });
    return (_ctx, _cache) => {
      return openBlock(), createElementBlock("div");
    };
  }
});
const routes = [{ "name": "restaurant-pos", "path": "/restaurant/pos", "component": () => __vitePreload(() => import("./pos.e051667d.js"), true ? ["assets/pos.e051667d.js","assets/plugin-vue_export-helper.5a098b48.js","assets/vendor.80d481a8.js"] : void 0), "props": true }, { "name": "restaurant-mesas", "path": "/restaurant/mesas", "component": () => __vitePreload(() => import("./mesas.3a3b578a.js"), true ? ["assets/mesas.3a3b578a.js","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/plugin-vue_export-helper.5a098b48.js","assets/vendor.80d481a8.js","assets/VDropdown.5d6ea697.js","assets/VDropdown.ecf63e77.css"] : void 0), "props": true }, { "path": "/auth", "component": () => __vitePreload(() => import("./auth.5ea35500.js"), true ? ["assets/auth.5ea35500.js","assets/auth.7dbfcfa4.css","assets/plugin-vue_export-helper.5a098b48.js","assets/vendor.80d481a8.js"] : void 0), "children": [{ "name": "auth", "path": "", "component": () => __vitePreload(() => import("./index.34e397f1.js"), true ? [] : void 0), "props": true, "redirect": { "name": "auth-login" } }, { "name": "auth-login", "path": "login", "component": () => __vitePreload(() => import("./login.469b878c.js"), true ? ["assets/login.469b878c.js","assets/login.848618dd.css","assets/IsotipoMozoOficial.415c8e31.js","assets/IsotipoMozoOficial.2a73f5e0.css","assets/vendor.80d481a8.js","assets/plugin-vue_export-helper.5a098b48.js","assets/LogoMozoOficial.2c735f28.js","assets/LogoMozoOficial.fc4913e9.css","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/VField.dd3df504.js","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css","assets/masterService.8bbc48d4.js"] : void 0), "props": true }, { "name": "auth-signup", "path": "signup", "component": () => __vitePreload(() => import("./signup.91b3f196.js"), true ? ["assets/signup.91b3f196.js","assets/signup.4b6fdd91.css","assets/LogoMozoOficial.2c735f28.js","assets/LogoMozoOficial.fc4913e9.css","assets/vendor.80d481a8.js","assets/IsotipoMozoOficial.415c8e31.js","assets/IsotipoMozoOficial.2a73f5e0.css","assets/plugin-vue_export-helper.5a098b48.js","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/VField.dd3df504.js","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css"] : void 0), "props": true }], "props": true }, { "path": "/app", "component": () => __vitePreload(() => import("./app.3070d9de.js"), true ? ["assets/app.3070d9de.js","assets/AppLayout.c1d2308e.js","assets/AppLayout.023c4d96.css","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css","assets/vendor.80d481a8.js","assets/plugin-vue_export-helper.5a098b48.js","assets/VIconButton.f9e7ed06.js","assets/IsotipoMozoOficial.415c8e31.js","assets/IsotipoMozoOficial.2a73f5e0.css","assets/VModal.976b2731.js","assets/VModal.d8de09e0.css","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/VField.dd3df504.js","assets/VDropdown.5d6ea697.js","assets/VDropdown.ecf63e77.css","assets/masterService.8bbc48d4.js"] : void 0), "children": [{ "name": "app", "path": "", "component": () => __vitePreload(() => import("./index.0a7a7bb9.js"), true ? ["assets/index.0a7a7bb9.js","assets/index.869160db.css","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/plugin-vue_export-helper.5a098b48.js","assets/vendor.80d481a8.js","assets/VField.dd3df504.js","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css","assets/VModal.976b2731.js","assets/VModal.d8de09e0.css","assets/masterService.8bbc48d4.js","assets/VIconButton.f9e7ed06.js"] : void 0), "props": true }, { "name": "app-pos", "path": "pos", "component": () => __vitePreload(() => import("./pos.53ff784d.js"), true ? ["assets/pos.53ff784d.js","assets/vendor.80d481a8.js","assets/sidebarLayoutState.1370c93c.js"] : void 0), "props": true }, { "name": "app-prices", "path": "prices", "component": () => __vitePreload(() => import("./prices.2c0f989d.js"), true ? ["assets/prices.2c0f989d.js","assets/prices.454d4dd8.css","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css","assets/vendor.80d481a8.js","assets/plugin-vue_export-helper.5a098b48.js","assets/VModal.976b2731.js","assets/VModal.d8de09e0.css","assets/masterService.8bbc48d4.js","assets/VControl.55044e40.js","assets/VControl.66ed690d.css","assets/VField.dd3df504.js","assets/sidebarLayoutState.1370c93c.js"] : void 0), "props": true }], "props": true, "meta": { "requiresAuth": true } }, { "name": "index", "path": "/", "component": _sfc_main, "props": true }, { "name": "all", "path": "/:all(.*)*", "component": () => __vitePreload(() => import("./[...all].f904cb9d.js"), true ? ["assets/[...all].f904cb9d.js","assets/[...all].cd05e41e.css","assets/VButton.ec3d6c22.js","assets/VButton.4bd674d0.css","assets/vendor.80d481a8.js","assets/plugin-vue_export-helper.5a098b48.js"] : void 0), "props": true }];
const NAME_ROUTE_MESAS = "app-mesas";
const NAME_ROUTE_POS = "app";
const NAME_ROUTE_ORDERS = "app-orders";
const NAME_ROUTE_COMMANDS = "app-commands";
const NAME_ROUTE_PRICES = "app-prices";
const ROLES = [
  {
    id: 1,
    name: "Mozo",
    code: "MOZO",
    menu: NAME_ROUTE_MESAS,
    permissions: [NAME_ROUTE_MESAS]
  },
  {
    id: 2,
    code: "CAJA",
    name: "Caja",
    menu: NAME_ROUTE_MESAS,
    permissions: [NAME_ROUTE_MESAS, NAME_ROUTE_POS, NAME_ROUTE_ORDERS]
  },
  {
    id: 3,
    code: "ADM",
    name: "Administrador",
    menu: NAME_ROUTE_MESAS,
    permissions: [
      NAME_ROUTE_MESAS,
      NAME_ROUTE_POS,
      NAME_ROUTE_ORDERS,
      NAME_ROUTE_COMMANDS,
      NAME_ROUTE_PRICES
    ]
  },
  {
    id: 4,
    code: "KITBAR",
    name: "Bar/Cocina",
    menu: NAME_ROUTE_COMMANDS,
    permissions: [NAME_ROUTE_COMMANDS]
  }
];
function createRouter() {
  const router = createRouter$1({
    history: createWebHistory("/vendeya/"),
    routes
  });
  router.beforeEach(async (to, form, next) => {
    nprogress$1.exports.start();
    const userSession = useUserSession();
    const isLogin = userSession.isLoggedIn;
    const roleCurrent = userSession.userRole;
    const role = ROLES.find((role2) => role2.id === roleCurrent);
    if (isLogin) {
      if (to.path === "/vendeya/auth/login") {
        next({ path: "/vendeya/app" });
      }
      if (userSession.token === "") {
        const { MasterService } = await __vitePreload(() => import("./masterService.8bbc48d4.js").then(function(n) {
          return n.m;
        }), true ? ["assets/masterService.8bbc48d4.js","assets/vendor.80d481a8.js"] : void 0);
        await MasterService.syncData();
        console.log("Datos de company sincronizados");
      }
      if (role) {
        let isPermission = role.permissions.filter((permission) => permission == to.name);
        if (isPermission.length == 0) {
          next({ name: role.menu });
        }
      }
    }
    next();
  });
  router.afterEach(() => {
    nprogress$1.exports.done();
  });
  return router;
}
const apiSymbol = Symbol();
function provideApi() {
  const userSession = useUserSession();
  const api = axios.create({
    baseURL: `${userSession.ssl + userSession.url}/api`
  });
  api.interceptors.request.use((config) => {
    if (userSession.isLoggedIn) {
      config.headers = __spreadProps(__spreadValues({}, config.headers), {
        Authorization: `Bearer ${userSession.token}`
      });
    }
    return config;
  });
  provide(apiSymbol, api);
  return api;
}
const hslRe = /hsl\(\s*(\d+)((?:deg)|(?:turn)|(?:rad))?\s*,?\s*(\d+(?:\.\d+)?%)\s*,?\s*(\d+(?:\.\d+)?%)\s*\)/;
function HSLToHex(hslCss) {
  if (!hslCss) {
    return "#fff";
  }
  const res = hslRe.exec(hslCss);
  if (res === null) {
    return "#fff";
  }
  const [hueString, hueUnit, saturationString, luminanceString] = res.slice(1);
  if (!hueString || !saturationString || !luminanceString) {
    return "#fff";
  }
  let h2 = 0;
  let s = parseFloat(saturationString != null ? saturationString : "0");
  let l = parseFloat(luminanceString != null ? luminanceString : "0");
  switch (hueUnit) {
    case "deg":
      h2 = parseFloat(hueString.substr(0, hueString.length - 3));
      break;
    case "turn":
      h2 = Math.round(parseFloat(hueString.substr(0, hueString.length - 4)) * 360);
      break;
    case "rad":
      h2 = Math.round(parseFloat(hueString.substr(0, hueString.length - 3)) * (180 / Math.PI));
      break;
    default:
      h2 = parseFloat(hueString);
      break;
  }
  if (h2 >= 360)
    h2 %= 360;
  s /= 100;
  l /= 100;
  const c = (1 - Math.abs(2 * l - 1)) * s;
  const x = c * (1 - Math.abs(h2 / 60 % 2 - 1));
  const m = l - c / 2;
  let r = 0;
  let g = 0;
  let b = 0;
  if (0 <= h2 && h2 < 60) {
    r = c;
    g = x;
    b = 0;
  } else if (60 <= h2 && h2 < 120) {
    r = x;
    g = c;
    b = 0;
  } else if (120 <= h2 && h2 < 180) {
    r = 0;
    g = c;
    b = x;
  } else if (180 <= h2 && h2 < 240) {
    r = 0;
    g = x;
    b = c;
  } else if (240 <= h2 && h2 < 300) {
    r = x;
    g = 0;
    b = c;
  } else if (300 <= h2 && h2 < 360) {
    r = c;
    g = 0;
    b = x;
  }
  let rString = Math.round((r + m) * 255).toString(16);
  let gString = Math.round((g + m) * 255).toString(16);
  let bString = Math.round((b + m) * 255).toString(16);
  if (rString.length == 1)
    rString = "0" + rString;
  if (gString.length == 1)
    gString = "0" + gString;
  if (bString.length == 1)
    bString = "0" + bString;
  return "#" + rString + gString + bString;
}
const style = getComputedStyle(document.documentElement);
const themeColors = {
  primary: HSLToHex(style.getPropertyValue("--primary")),
  primaryMedium: "#b4e4ce",
  primaryLight: "#f7fcfa",
  secondary: "#ff227d",
  accent: "#797bf2",
  accentMedium: "#d4b3ff",
  accentLight: "#b8ccff",
  success: HSLToHex(style.getPropertyValue("--success")),
  info: HSLToHex(style.getPropertyValue("--info")),
  warning: HSLToHex(style.getPropertyValue("--warning")),
  danger: HSLToHex(style.getPropertyValue("--danger")),
  purple: HSLToHex(style.getPropertyValue("--purple")),
  blue: HSLToHex(style.getPropertyValue("--blue")),
  green: HSLToHex(style.getPropertyValue("--green")),
  yellow: HSLToHex(style.getPropertyValue("--yellow")),
  orange: HSLToHex(style.getPropertyValue("--orange")),
  lightText: "#a2a5b9",
  fadeGrey: "#ededed"
};
const notyf = new Notyf({
  duration: 2e3,
  position: {
    x: "right",
    y: "bottom"
  },
  types: [
    {
      type: "warning",
      background: themeColors.warning,
      icon: {
        className: "fas fa-hand-paper",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "info",
      background: themeColors.info,
      icon: {
        className: "fas fa-info-circle",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "primary",
      background: themeColors.primary,
      icon: {
        className: "fas fa-car-crash",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "accent",
      background: themeColors.accent,
      icon: {
        className: "fas fa-car-crash",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "purple",
      background: themeColors.purple,
      icon: {
        className: "fas fa-check",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "blue",
      background: themeColors.blue,
      icon: {
        className: "fas fa-check",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "green",
      background: themeColors.green,
      icon: {
        className: "fas fa-check",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "orange",
      background: themeColors.orange,
      icon: {
        className: "fas fa-check",
        tagName: "i",
        text: ""
      }
    },
    {
      type: "sync",
      background: themeColors.blue,
      icon: {
        className: "fas fa-sync",
        tagName: "i",
        text: ""
      }
    }
  ]
});
function useNotyf() {
  return {
    dismiss: (notification) => {
      notyf.dismiss(notification);
    },
    dismissAll: () => {
      notyf.dismissAll();
    },
    success: (payload) => {
      return notyf.success(payload);
    },
    error: (payload) => {
      return notyf.error(payload);
    },
    info: (payload) => {
      const options = {
        type: "info"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    warning: (payload) => {
      const options = {
        type: "warning"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    primary: (payload) => {
      const options = {
        type: "primary"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    purple: (payload) => {
      const options = {
        type: "purple"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    blue: (payload) => {
      const options = {
        type: "blue"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    green: (payload) => {
      const options = {
        type: "green"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    orange: (payload) => {
      const options = {
        type: "orange"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    },
    sync: (payload) => {
      const options = {
        type: "sync"
      };
      if (typeof payload === "string") {
        options.message = payload;
      } else {
        Object.assign(options, payload);
      }
      return notyf.open(options);
    }
  };
}
async function createApp({ enhanceApp }) {
  const head = createHead();
  const i18n = createI18n();
  const router = createRouter();
  const pinia = createPinia();
  const app = createApp$1({
    setup() {
      provideApi();
      return () => {
        const defaultSlot = ({ Component: _Component }) => {
          const Component = resolveDynamicComponent(_Component);
          return [
            h(Transition, { name: "fade-slow", mode: "out-in" }, {
              default: () => [h(Component)]
            })
          ];
        };
        return [
          h(RouterView, null, {
            default: defaultSlot
          })
        ];
      };
    }
  });
  router.beforeEach((to, from) => {
    const userSession = useUserSession();
    if (to.meta.requiresAuth && !userSession.isLoggedIn) {
      const notif = useNotyf();
      notif.error({
        message: "Lo sentimos, debe iniciar sesi\xF3n para acceder a esta secci\xF3n (cualquier cosa funcionar\xE1)",
        duration: 7e3
      });
      return {
        name: "auth",
        query: { redirect: to.fullPath }
      };
    }
  });
  app.use(head);
  app.use(router);
  app.use(i18n);
  app.use(pinia);
  if (enhanceApp) {
    await enhanceApp(app);
  }
  return {
    app,
    router,
    head,
    i18n
  };
}
let currentConfig = {};
const loadThemeConfig = async () => {
  try {
    const response = await fetch("/config.json");
    const config = await response.json();
    currentConfig = {
      Primary: config.Primary,
      Secondary: config.Secondary,
      Background: config.Background,
      White: config.White,
      Text: config.Text,
      lightText: config.lightText,
      darkPrimary: config.darkPrimary,
      darkSecondary: config.darkSecondary,
      darkAccent: config.darkAccent,
      darkBackground: config.darkBackground,
      darkLightText: config.darkLightText
    };
    applyThemeColors();
  } catch (error) {
    console.error("Error loading theme config:", error);
    currentConfig = {
      Primary: "#ff7d00",
      Secondary: "#d5e8e8",
      Background: "#eef5f5",
      White: "#ffffff",
      Text: "#004850",
      lightText: "#a2a5b9",
      darkPrimary: "#121c22",
      darkSecondary: "#1c2a32",
      darkAccent: "#253945",
      darkBackground: "#1b262c",
      darkLightText: "#a9a9b2"
    };
    applyThemeColors();
  }
};
const applyThemeColors = () => {
  const root = document.documentElement;
  root.style.setProperty("--primary", currentConfig.Primary || "#ff7d00");
  root.style.setProperty("--secondary", currentConfig.Secondary || "#d5e8e8");
  root.style.setProperty("--background", currentConfig.Background || "#eef5f5");
  root.style.setProperty("--white", currentConfig.White || "#ffffff");
  root.style.setProperty("--dark-text", currentConfig.Text || "#004850");
  root.style.setProperty("--light-text", currentConfig.lightText || "#a2a5b9");
  root.style.setProperty("--dark-primary", currentConfig.darkPrimary || "#121c22");
  root.style.setProperty("--dark-secondary", currentConfig.darkSecondary || "#1c2a32");
  root.style.setProperty("--dark-accent", currentConfig.darkAccent || "#253945");
  root.style.setProperty("--dark-background", currentConfig.darkBackground || "#1b262c");
  root.style.setProperty("--dark-light-text", currentConfig.darkLightText || "#a9a9b2");
};
const brandName = ref("");
const loadBrandConfig = async () => {
  try {
    const response = await fetch("/config.json");
    const config = await response.json();
    brandName.value = config.brandName || "Nombre por defecto";
  } catch (error) {
    console.error("Error al cargar brandName desde config.json:", error);
    brandName.value = "Nombre por defecto";
  }
};
async function loadAppConfig() {
  try {
    const storedUrl = localStorage.getItem("url");
    const storedSsl = localStorage.getItem("ssl");
    const isStoreEnabled = localStorage.getItem("isStoreEnabled");
    const brandName2 = localStorage.getItem("brandName");
    if (storedUrl && storedSsl) {
      return {
        ssl: storedSsl,
        url: storedUrl,
        isStoreEnabled,
        brandName: brandName2
      };
    }
    const response = await fetch("/config.json");
    const config = await response.json();
    return {
      ssl: config.apiSsl,
      url: config.apiUrl,
      isStoreEnabled: config.isStoreEnabled,
      brandName: config.brandName
    };
  } catch (error) {
    console.error("Error loading config:", error);
    return {
      ssl: "https://",
      url: "vendeya.facturalo.pro",
      isStoreEnabled: "false",
      brandName: "Vendeya"
    };
  }
}
createApp({
  async enhanceApp(app) {
    const [apiConfig] = await Promise.all([
      loadAppConfig(),
      loadThemeConfig(),
      loadBrandConfig()
    ]);
    const userSession = useUserSession();
    if (apiConfig) {
      userSession.setSsl(apiConfig.ssl);
      userSession.setUrl(apiConfig.url);
      userSession.setIsStoreEnabled(apiConfig.isStoreEnabled);
      userSession.setBrandName(apiConfig.brandName);
    }
    const VCalendar = (await __vitePreload(() => import("./index.a8b90ae7.js"), true ? ["assets/index.a8b90ae7.js","assets/vendor.80d481a8.js"] : void 0)).default;
    const VueMultiselect = (await __vitePreload(() => import("./multiselect.4f4159f3.js"), true ? ["assets/multiselect.4f4159f3.js","assets/vendor.80d481a8.js"] : void 0)).default;
    const VueSlider = (await __vitePreload(() => import("./slider.2875fd6f.js"), true ? ["assets/slider.2875fd6f.js","assets/vendor.80d481a8.js"] : void 0)).default;
    const VueTippy = (await __vitePreload(() => import("./vue-tippy.esm-bundler.ddd9460e.js"), true ? ["assets/vue-tippy.esm-bundler.ddd9460e.js","assets/vendor.80d481a8.js"] : void 0)).default;
    const hasNestedRouterLink = (await __vitePreload(() => import("./has-nested-router-link.4fe8dab1.js"), true ? [] : void 0)).default;
    const background = (await __vitePreload(() => import("./background.5bd78b71.js"), true ? [] : void 0)).default;
    const tooltip = (await __vitePreload(() => import("./tooltip.24128ff9.js"), true ? [] : void 0)).default;
    app.use(VCalendar);
    app.use(VueTippy, {
      defaultProps: {
        theme: "light"
      }
    });
    app.component(VueMultiselect.name, VueMultiselect);
    app.component(VueSlider.name, VueSlider);
    app.directive("has-nested-router-link", hasNestedRouterLink);
    app.directive("background", background);
    app.directive("tooltip", tooltip);
  }
}).then(async ({ app, router }) => {
  await router.isReady();
  app.mount("#app");
});
export { NAME_ROUTE_POS as N, ROLES as R, __vitePreload as _, useNotyf as a, brandName as b, NAME_ROUTE_COMMANDS as c, NAME_ROUTE_MESAS as d, provideApi as p, themeColors as t, useUserSession as u };
