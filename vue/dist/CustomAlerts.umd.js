(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"));
	else if(typeof define === 'function' && define.amd)
		define(["CoreHome", , "CorePluginsAdmin"], factory);
	else if(typeof exports === 'object')
		exports["CustomAlerts"] = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"));
	else
		root["CustomAlerts"] = factory(root["CoreHome"], root["Vue"], root["CorePluginsAdmin"]);
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE__19dc__, __WEBPACK_EXTERNAL_MODULE__8bbf__, __WEBPACK_EXTERNAL_MODULE_a5a2__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "plugins/CustomAlerts/vue/dist/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fae3");
/******/ })
/************************************************************************/
/******/ ({

/***/ "19dc":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__19dc__;

/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "a5a2":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_a5a2__;

/***/ }),

/***/ "fae3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "ListAlerts", function() { return /* reexport */ ListAlerts; });
__webpack_require__.d(__webpack_exports__, "EditAlert", function() { return /* reexport */ EditAlert; });
__webpack_require__.d(__webpack_exports__, "HistoryTriggeredAlerts", function() { return /* reexport */ HistoryTriggeredAlerts; });
__webpack_require__.d(__webpack_exports__, "ListAlertsPage", function() { return /* reexport */ ListAlertsPage; });

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var currentScript = window.document.currentScript
  if (false) { var getCurrentScript; }

  var src = currentScript && currentScript.src.match(/(.+\/)[^/]+\.js(\?.*)?$/)
  if (src) {
    __webpack_require__.p = src[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__("8bbf");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=template&id=c6027cfa

const _hoisted_1 = {
  key: 0
};
const _hoisted_2 = {
  colspan: "6"
};
const _hoisted_3 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);
const _hoisted_4 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);
const _hoisted_5 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);
const _hoisted_6 = {
  class: "name"
};
const _hoisted_7 = {
  class: "site"
};
const _hoisted_8 = {
  class: "period"
};
const _hoisted_9 = {
  class: "reportName"
};
const _hoisted_10 = {
  class: "edit"
};
const _hoisted_11 = ["href", "title"];
const _hoisted_12 = ["onClick", "id", "title"];
const _hoisted_13 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-delete"
}, null, -1);
const _hoisted_14 = [_hoisted_13];
const _hoisted_15 = {
  class: "tableActionBar"
};
const _hoisted_16 = ["href"];
const _hoisted_17 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);
const _hoisted_18 = ["href"];
const _hoisted_19 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-table"
}, null, -1);
function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _ctx$alerts;
  const _directive_content_table = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("content-table");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("table", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Name')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Website')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Period')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Report')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Actions')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [!((_ctx$alerts = _ctx.alerts) !== null && _ctx$alerts !== void 0 && _ctx$alerts.length) ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", _hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_2, [_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_NoAlertsDefined')) + " ", 1), _hoisted_4, _hoisted_5])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.alerts, alert => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
      key: alert.idalert
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_6, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(alert.name), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.decode(alert.siteName)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.ucfirst(_ctx.translate(`Intl_Period${_ctx.ucfirst(alert.period)}`))), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_9, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(alert.reportName || '-'), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
      class: "table-action icon-edit",
      href: _ctx.linkTo({
        'module': 'CustomAlerts',
        'action': 'editAlert',
        'idAlert': alert.idalert
      }),
      title: _ctx.translate('General_Edit')
    }, null, 8, _hoisted_11), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      class: "deleteAlert table-action",
      onClick: $event => _ctx.deleteAlert(alert.idalert),
      id: alert.idalert,
      title: _ctx.translate('General_Delete')
    }, _hoisted_14, 8, _hoisted_12)])]);
  }), 128))])])), [[_directive_content_table]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_15, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
    href: _ctx.linkTo({
      'module': 'CustomAlerts',
      'action': 'addNewAlert'
    })
  }, [_hoisted_17, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_CreateNewAlert')), 1)], 8, _hoisted_16), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
    href: _ctx.linkTo({
      'module': 'CustomAlerts',
      'action': 'historyTriggeredAlerts'
    })
  }, [_hoisted_19, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_AlertsHistory')), 1)], 8, _hoisted_18)])]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=template&id=c6027cfa

// EXTERNAL MODULE: external "CoreHome"
var external_CoreHome_ = __webpack_require__("19dc");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=script&lang=ts


/* harmony default export */ var ListAlertsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    alerts: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  directives: {
    ContentTable: external_CoreHome_["ContentTable"]
  },
  methods: {
    deleteAlert(idAlert) {
      external_CoreHome_["Matomo"].helper.modalConfirm('#confirm', {
        yes: () => {
          external_CoreHome_["AjaxHelper"].fetch({
            method: 'CustomAlerts.deleteAlert',
            idAlert
          }).then(() => {
            external_CoreHome_["Matomo"].helper.redirect();
          });
        }
      });
    },
    ucfirst(s) {
      return `${s[0].toUpperCase()}${s.substr(1)}`;
    },
    linkTo(params) {
      return `?${external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), params))}`;
    },
    decode(s) {
      return external_CoreHome_["Matomo"].helper.htmlDecode(s);
    }
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue



ListAlertsvue_type_script_lang_ts.render = render

/* harmony default export */ var ListAlerts = (ListAlertsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=template&id=58071656

const EditAlertvue_type_template_id_58071656_hoisted_1 = {
  id: "customAlertPeriodHelp",
  class: "inline-help-node"
};
const EditAlertvue_type_template_id_58071656_hoisted_2 = {
  class: "report-mediums"
};
const EditAlertvue_type_template_id_58071656_hoisted_3 = {
  key: 0
};
const EditAlertvue_type_template_id_58071656_hoisted_4 = {
  key: 1
};
const EditAlertvue_type_template_id_58071656_hoisted_5 = {
  key: 0
};
const EditAlertvue_type_template_id_58071656_hoisted_6 = {
  key: 1,
  class: "row"
};
const EditAlertvue_type_template_id_58071656_hoisted_7 = {
  class: "col s12"
};
const EditAlertvue_type_template_id_58071656_hoisted_8 = ["innerHTML"];
const EditAlertvue_type_template_id_58071656_hoisted_9 = {
  key: 2
};
const EditAlertvue_type_template_id_58071656_hoisted_10 = {
  key: 3
};
const EditAlertvue_type_template_id_58071656_hoisted_11 = {
  class: "row"
};
const EditAlertvue_type_template_id_58071656_hoisted_12 = {
  class: "col s12"
};
const EditAlertvue_type_template_id_58071656_hoisted_13 = {
  class: "row conditionAndValue"
};
const EditAlertvue_type_template_id_58071656_hoisted_14 = {
  class: "col s12 m6"
};
const EditAlertvue_type_template_id_58071656_hoisted_15 = {
  class: "col s12 m6"
};
const EditAlertvue_type_template_id_58071656_hoisted_16 = {
  class: "ui-autocomplete-input",
  ref: "reportValue"
};
const EditAlertvue_type_template_id_58071656_hoisted_17 = {
  class: "row conditionAndValue"
};
const EditAlertvue_type_template_id_58071656_hoisted_18 = {
  class: "col s12 m6"
};
const EditAlertvue_type_template_id_58071656_hoisted_19 = {
  class: "col s12 m6"
};
const _hoisted_20 = ["innerHTML"];
function EditAlertvue_type_template_id_58071656_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_SelectPhoneNumbers = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SelectPhoneNumbers");
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  const _component_SelectSlackChannel = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SelectSlackChannel");
  const _component_SelectMicrosoftTeamsWebhookUrl = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SelectMicrosoftTeamsWebhookUrl");
  const _component_ActivityIndicator = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ActivityIndicator");
  const _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _directive_form = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("form");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.headline
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => {
      var _ctx$actualAlert$id_s, _ctx$actualReportMeta, _ctx$actualAlert;
      return [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        name: "alertName",
        modelValue: _ctx.actualAlert.name,
        "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.actualAlert.name = $event),
        maxlength: 100,
        title: _ctx.translate('CustomAlerts_AlertName')
      }, null, 8, ["modelValue", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "site",
        name: "idSite",
        "model-value": {
          id: (_ctx$actualAlert$id_s = _ctx.actualAlert.id_sites) === null || _ctx$actualAlert$id_s === void 0 ? void 0 : _ctx$actualAlert$id_s[0],
          name: _ctx.actualCurrentSite.name
        },
        "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => {
          _ctx.actualAlert.id_sites = [$event.id];
          _ctx.actualCurrentSite = $event;
          _ctx.changeReport();
        }),
        title: _ctx.translate('General_Website'),
        introduction: _ctx.translate('CustomAlerts_ApplyTo')
      }, null, 8, ["model-value", "title", "introduction"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_YouCanChoosePeriodFrom')) + ": ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodDayDescription')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodWeekDescription')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodMonthDescription')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "period",
        "inline-help": "#customAlertPeriodHelp",
        "model-value": _ctx.actualAlert.period,
        "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => {
          _ctx.actualAlert.period = $event;
          _ctx.changeReport();
        }),
        title: _ctx.translate('General_Period'),
        options: _ctx.periodOptions
      }, null, 8, ["model-value", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "multiselect",
        name: "report_mediums",
        id: "report_mediums",
        title: _ctx.translate('CustomAlerts_MediumTitle'),
        "inline-help": _ctx.translate('CustomAlerts_MediumDescription'),
        options: _ctx.alertReportMediumOptions,
        "model-value": _ctx.actualAlert.report_mediums,
        "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => {
          _ctx.actualAlert.report_mediums = $event;
        })
      }, null, 8, ["title", "inline-help", "options", "model-value"])]), _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes('email') ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_58071656_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "checkbox",
        name: "report_email_me",
        modelValue: _ctx.actualAlert.email_me,
        "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => _ctx.actualAlert.email_me = $event),
        introduction: _ctx.translate('ScheduledReports_SendReportTo'),
        title: `${_ctx.translate('ScheduledReports_SentToMe')} (${_ctx.currentUserEmail})`
      }, null, 8, ["modelValue", "introduction", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "textarea",
        modelValue: _ctx.actualAlert.additional_emails,
        "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.actualAlert.additional_emails = $event),
        "var-type": "array",
        title: _ctx.translate('ScheduledReports_AlsoSendReportToTheseEmails')
      }, null, 8, ["modelValue", "title"])])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes('mobile') ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_58071656_hoisted_4, [_ctx.supportsSMS ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", EditAlertvue_type_template_id_58071656_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SelectPhoneNumbers, {
        "phone-numbers": _ctx.phoneNumbers || [],
        modelValue: _ctx.actualAlert.phone_numbers,
        "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => _ctx.actualAlert.phone_numbers = $event)
      }, null, 8, ["phone-numbers", "modelValue"])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_58071656_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Alert, {
        severity: "info"
      }, {
        default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('MobileMessaging_PhoneNumbers')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(": "), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
          innerHTML: _ctx.$sanitize(_ctx.mobileMessagingNotActivated)
        }, null, 8, EditAlertvue_type_template_id_58071656_hoisted_8)]),
        _: 1
      })])]))])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes('slack') ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_58071656_hoisted_9, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SelectSlackChannel, {
        "model-value": _ctx.actualAlert.slack_channel_id || '',
        "is-slack-oauth-token-added": _ctx.isSlackOauthTokenAdded,
        modelValue: _ctx.actualAlert.slack_channel_id,
        "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => _ctx.actualAlert.slack_channel_id = $event)
      }, null, 8, ["model-value", "is-slack-oauth-token-added", "modelValue"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes('teams') ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_58071656_hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SelectMicrosoftTeamsWebhookUrl, {
        "is-required-fields-set": true,
        "model-value": _ctx.actualAlert.ms_teams_webhook_url || '',
        modelValue: _ctx.actualAlert.ms_teams_webhook_url,
        "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => _ctx.actualAlert.ms_teams_webhook_url = $event)
      }, null, 8, ["model-value", "modelValue"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "expandable-select",
        name: "report",
        "model-value": _ctx.actualAlert.report,
        "onUpdate:modelValue": _cache[9] || (_cache[9] = $event => {
          _ctx.actualAlert.report = $event;
          _ctx.changeReport();
        }),
        options: _ctx.reportOptions,
        title: `${_ctx.translate('CustomAlerts_ThisAppliesTo')}: ${(_ctx$actualReportMeta = _ctx.actualReportMetadata) === null || _ctx$actualReportMeta === void 0 ? void 0 : _ctx$actualReportMeta.name}`,
        introduction: _ctx.translate('CustomAlerts_AlertCondition'),
        "inline-help": _ctx.thisAppliesToInlineHelp
      }, null, 8, ["model-value", "options", "title", "introduction", "inline-help"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_11, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_12, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ActivityIndicator, {
        loading: _ctx.isLoadingReport
      }, null, 8, ["loading"])])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.isLoadingReport]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_13, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_14, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "reportCondition",
        modelValue: _ctx.actualAlert.report_condition,
        "onUpdate:modelValue": _cache[10] || (_cache[10] = $event => _ctx.actualAlert.report_condition = $event),
        "full-width": true,
        title: _ctx.reportConditionTitle,
        options: _ctx.alertGroupConditions
      }, null, 8, ["modelValue", "title", "options"])])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_15, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_16, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        role: "textbox",
        name: "reportValue",
        modelValue: _ctx.actualAlert.report_matched,
        "onUpdate:modelValue": _cache[11] || (_cache[11] = $event => _ctx.actualAlert.report_matched = $event),
        "full-width": true,
        autocomplete: 'off',
        maxlength: 255,
        title: _ctx.translate('General_Value')
      }, null, 8, ["modelValue", "title"]), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.actualAlert.report_condition !== 'matches_any']])], 512)])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.hasReportDimension]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "metric",
        "model-value": _ctx.actualAlert.metric,
        "onUpdate:modelValue": _cache[12] || (_cache[12] = $event => _ctx.actualAlert.metric = $event),
        options: _ctx.metricOptions,
        introduction: _ctx.translate('CustomAlerts_AlertMeWhen')
      }, null, 8, ["model-value", "options", "introduction"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_17, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_18, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "metricCondition",
        "model-value": _ctx.actualAlert.metric_condition,
        "onUpdate:modelValue": _cache[13] || (_cache[13] = $event => _ctx.actualAlert.metric_condition = $event),
        "full-width": true,
        options: _ctx.metricConditionOptions
      }, null, 8, ["model-value", "options"])])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_58071656_hoisted_19, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        name: "metricValue",
        class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])({
          invalid: _ctx.isMetricValueInvalid
        }),
        modelValue: _ctx.actualAlert.metric_matched,
        "onUpdate:modelValue": _cache[14] || (_cache[14] = $event => _ctx.actualAlert.metric_matched = $event),
        title: `<span>${_ctx.metricDescription}</span>`,
        "full-width": true
      }, null, 8, ["class", "modelValue", "title"])])])]), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.comparablesDates, (comparablesDatesPeriod, period) => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
          key: period
        }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
          uicontrol: "select",
          name: "compared_to",
          modelValue: _ctx.comparedTo[period],
          "onUpdate:modelValue": $event => _ctx.comparedTo[period] = $event,
          disabled: Object.keys(comparablesDatesPeriod).length <= 1,
          options: comparablesDatesPeriod,
          introduction: _ctx.translate('CustomAlerts_ComparedToThe')
        }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "options", "introduction"]), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], period === _ctx.actualAlert.period && _ctx.isComparable]])]);
      }), 128)), (_ctx$actualAlert = _ctx.actualAlert) !== null && _ctx$actualAlert !== void 0 && _ctx$actualAlert.idalert ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_SaveButton, {
        key: 4,
        onClick: _cache[15] || (_cache[15] = $event => _ctx.updateAlert(_ctx.actualAlert.idalert)),
        saving: _ctx.isLoading
      }, null, 8, ["saving"])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_SaveButton, {
        key: 5,
        onClick: _cache[16] || (_cache[16] = $event => _ctx.createAlert()),
        saving: _ctx.isLoading
      }, null, 8, ["saving"])), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", {
        class: "entityCancel",
        innerHTML: _ctx.$sanitize(_ctx.cancelLink)
      }, null, 8, _hoisted_20)])), [[_directive_form]])];
    }),
    _: 1
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=template&id=58071656

// EXTERNAL MODULE: external "CorePluginsAdmin"
var external_CorePluginsAdmin_ = __webpack_require__("a5a2");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=script&lang=ts



const SelectPhoneNumbers = Object(external_CoreHome_["useExternalPluginComponent"])('MobileMessaging', 'SelectPhoneNumbers');
const SelectSlackChannel = Object(external_CoreHome_["useExternalPluginComponent"])('Slack', 'SelectSlackChannel');
const SelectMicrosoftTeamsWebhookUrl = Object(external_CoreHome_["useExternalPluginComponent"])('MicrosoftTeams', 'SelectMicrosoftTeamsWebhookUrl');
function isBlockedReportApiMethod(apiMethodUniqueId) {
  return apiMethodUniqueId === 'MultiSites_getOne' || apiMethodUniqueId === 'MultiSites_getAll';
}
const {
  $
} = window;
/* harmony default export */ var EditAlertvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    alert: Object,
    headline: {
      type: String,
      required: true
    },
    currentSite: {
      type: Object,
      required: true
    },
    periodOptions: {
      type: Array,
      required: true
    },
    alertReportMediumOptions: {
      type: Array,
      required: true
    },
    currentUserEmail: {
      type: String,
      required: true
    },
    supportsSMS: Boolean,
    phoneNumbers: [Array, Object],
    isSlackOauthTokenAdded: Boolean,
    reportMetadata: Object,
    alertGroupConditions: {
      type: Array,
      required: true
    },
    metricConditionOptions: {
      type: Array,
      required: true
    },
    comparablesDates: {
      type: Object,
      required: true
    }
  },
  components: {
    Field: external_CorePluginsAdmin_["Field"],
    Alert: external_CoreHome_["Alert"],
    ActivityIndicator: external_CoreHome_["ActivityIndicator"],
    SaveButton: external_CorePluginsAdmin_["SaveButton"],
    SelectPhoneNumbers,
    SelectSlackChannel,
    SelectMicrosoftTeamsWebhookUrl,
    ContentBlock: external_CoreHome_["ContentBlock"]
  },
  directives: {
    Form: external_CorePluginsAdmin_["Form"]
  },
  data() {
    const currentSite = this.currentSite;
    const alert = this.alert;
    const reportMetadata = this.reportMetadata;
    // set comparedTo for each comparison (defaulting to first available value)
    const comparedTo = Object.fromEntries(Object.entries(this.comparablesDates).map(([period, dates]) => {
      var _dates$;
      return [period, dates === null || dates === void 0 || (_dates$ = dates[0]) === null || _dates$ === void 0 ? void 0 : _dates$.key];
    }));
    if (this.alert) {
      comparedTo[this.alert.period] = `${alert.compared_to}`;
    }
    return {
      isLoading: false,
      isLoadingReport: false,
      showReportConditionField: false,
      reportOptions: [],
      actualReportMetadata: reportMetadata,
      reportValuesAutoComplete: null,
      actualAlert: alert ? Object.assign({}, alert) : {
        period: 'day',
        id_sites: [(currentSite === null || currentSite === void 0 ? void 0 : currentSite.id) || external_CoreHome_["Matomo"].idSite],
        report_mediums: []
      },
      comparedTo,
      actualCurrentSite: {
        id: currentSite.id,
        // in PHP, currentSite's name is the value in the DB, which is encoded
        name: external_CoreHome_["Matomo"].helper.htmlDecode(currentSite.name)
      }
    };
  },
  watch: {
    actualReportMetadata() {
      var _this$actualReportMet;
      const metrics = (_this$actualReportMet = this.actualReportMetadata) === null || _this$actualReportMet === void 0 ? void 0 : _this$actualReportMet.metrics;
      if (!metrics) {
        return;
      }
      if (!this.actualAlert.metric || !metrics[this.actualAlert.metric]) {
        [this.actualAlert.metric] = Object.keys(metrics);
      }
    },
    isMetricValueInvalid(newValue) {
      if (!newValue) {
        return;
      }
      const notificationInstanceId = external_CoreHome_["NotificationsStore"].show({
        message: Object(external_CoreHome_["translate"])('CustomAlerts_InvalidMetricValue'),
        id: 'CustomAlertsMetricValueError',
        context: 'error',
        type: 'toast'
      });
      external_CoreHome_["NotificationsStore"].scrollToNotification(notificationInstanceId);
    }
  },
  created() {
    this.changeReport();
    setTimeout(() => {
      $(this.$refs.reportValue).find('input').autocomplete({
        source: this.getValuesForReportAndMetric.bind(this),
        minLength: 1,
        delay: 300
      });
    }, 1000);
  },
  methods: {
    renderForm(data) {
      const options = [];
      this.actualReportMetadata = null;
      data.forEach(reportMetadata => {
        const reportApiMethod = reportMetadata.uniqueId;
        if (isBlockedReportApiMethod(reportApiMethod)) {
          return;
        }
        if (!this.actualAlert.report) {
          this.actualAlert.report = reportApiMethod;
        }
        options.push({
          key: reportApiMethod,
          value: reportMetadata.name,
          group: reportMetadata.category
        });
        if (reportApiMethod === this.actualAlert.report) {
          this.actualReportMetadata = reportMetadata;
        }
      });
      this.reportOptions = options;
    },
    sendApiRequest(method, postParams) {
      this.isLoading = true;
      const {
        period
      } = this.actualAlert;
      external_CoreHome_["AjaxHelper"].post({
        period,
        method
      }, postParams).then(() => {
        external_CoreHome_["Matomo"].helper.redirect({
          module: 'CustomAlerts',
          action: 'index'
        });
      }).finally(() => {
        this.isLoading = false;
      });
    },
    getValuesForReportAndMetric(request, response) {
      var _this$actualAlert$id_;
      const {
        metric
      } = this.actualAlert;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      function sendFeedback(values) {
        const matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
        response($.grep(values, value => {
          if (!value) {
            return false;
          }
          return matcher.test(value.label || value.value || value[metric] || value);
        }));
      }
      if (this.reportValuesAutoComplete) {
        sendFeedback(this.reportValuesAutoComplete);
        return;
      }
      this.reportValuesAutoComplete = [];
      const report = this.actualReportMetadata;
      if (!report) {
        return;
      }
      const apiModule = report.module;
      const apiAction = report.action;
      if (!metric || !apiModule || !apiAction) {
        sendFeedback(this.reportValuesAutoComplete);
      }
      external_CoreHome_["AjaxHelper"].fetch({
        method: 'API.getProcessedReport',
        date: 'yesterday',
        period: 'month',
        disable_queued_filters: 1,
        flat: 1,
        filter_limit: -1,
        showColumns: metric,
        language: 'en',
        apiModule,
        apiAction,
        idSite: (_this$actualAlert$id_ = this.actualAlert.id_sites) === null || _this$actualAlert$id_ === void 0 ? void 0 : _this$actualAlert$id_[0],
        format: 'JSON'
      }).then(data => {
        if (data !== null && data !== void 0 && data.reportData) {
          this.reportValuesAutoComplete = data.reportData;
          sendFeedback(data.reportData);
        } else {
          sendFeedback([]);
        }
      }).catch(() => {
        sendFeedback([]);
      });
    },
    changeReport() {
      var _this$actualAlert$id_2;
      this.isLoadingReport = true;
      this.reportValuesAutoComplete = null;
      external_CoreHome_["AjaxHelper"].fetch({
        method: 'API.getReportMetadata',
        date: external_CoreHome_["Matomo"].currentDateString,
        period: this.actualAlert.period,
        idSite: (_this$actualAlert$id_2 = this.actualAlert.id_sites) === null || _this$actualAlert$id_2 === void 0 ? void 0 : _this$actualAlert$id_2[0],
        filter_limit: '-1'
      }).then(data => {
        this.renderForm(data);
      }).finally(() => {
        this.isLoadingReport = false;
      });
    },
    createAlert() {
      if (this.isMetricValueInvalid) {
        return false;
      }
      this.sendApiRequest('CustomAlerts.addAlert', this.apiParameters);
      return true;
    },
    updateAlert() {
      if (this.isMetricValueInvalid) {
        return false;
      }
      this.sendApiRequest('CustomAlerts.editAlert', this.apiParameters);
      return true;
    }
  },
  computed: {
    apiParameters() {
      var _this$actualAlert$add, _this$actualAlert$pho, _this$actualAlert, _this$actualAlert2;
      return {
        idAlert: this.actualAlert.idalert,
        format: 'json',
        name: this.actualAlert.name,
        metric: this.actualAlert.metric,
        metricCondition: this.actualAlert.metric_condition,
        metricValue: this.actualAlert.metric_matched,
        emailMe: this.actualAlert.email_me ? 1 : 0,
        additionalEmails: (_this$actualAlert$add = this.actualAlert.additional_emails) !== null && _this$actualAlert$add !== void 0 && _this$actualAlert$add.length ? this.actualAlert.additional_emails : [''],
        phoneNumbers: (_this$actualAlert$pho = this.actualAlert.phone_numbers) !== null && _this$actualAlert$pho !== void 0 && _this$actualAlert$pho.length ? this.actualAlert.phone_numbers : [''],
        slackChannelID: (_this$actualAlert = this.actualAlert) !== null && _this$actualAlert !== void 0 && _this$actualAlert.slack_channel_id ? this.actualAlert.slack_channel_id : '',
        msTeamsWebhookUrl: (_this$actualAlert2 = this.actualAlert) !== null && _this$actualAlert2 !== void 0 && _this$actualAlert2.ms_teams_webhook_url ? this.actualAlert.ms_teams_webhook_url : '',
        reportUniqueId: this.actualAlert.report,
        reportCondition: this.actualAlert.report_condition,
        reportValue: this.actualAlert.report_matched,
        reportMediums: this.actualAlert.report_mediums,
        idSites: this.actualAlert.id_sites,
        comparedTo: this.comparedTo[this.actualAlert.period]
      };
    },
    isMetricValueInvalid() {
      return !$.isNumeric(this.actualAlert.metric_matched);
    },
    mobileMessagingNotActivated() {
      const link = `?${external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CorePluginsAdmin',
        action: 'plugins',
        updated: null
      }))}`;
      return Object(external_CoreHome_["translate"])('CustomAlerts_MobileMessagingPluginNotActivated', `<a href="${link}#MobileMessaging">`, '</a>');
    },
    cancelLink() {
      const backlink = `?${external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CustomAlerts',
        action: 'index'
      }))}`;
      return Object(external_CoreHome_["translate"])('General_OrCancel', `<a class="entityCancelLink" href="${backlink}">`, '</a>');
    },
    thisAppliesToInlineHelp() {
      const link1 = 'https://matomo.org/guide/manage-matomo/custom-alerts/';
      const link2 = 'https://matomo.org/faq/general/examples-of-custom-alerts#events';
      return Object(external_CoreHome_["translate"])('CustomAlerts_ThisAppliesToHelp', `<a target="_blank" href="${link1}" rel="noreferrer noopener">`, '</a>', '<strong>', '</strong>', `<a target="_blank" href="${link2}" rel="noreferrer noopener">`, '</a>');
    },
    metricOptions() {
      var _this$actualReportMet2;
      return Object.entries(((_this$actualReportMet2 = this.actualReportMetadata) === null || _this$actualReportMet2 === void 0 ? void 0 : _this$actualReportMet2.metrics) || {}).map(([key, value]) => ({
        key,
        value
      }));
    },
    hasReportDimension() {
      var _this$actualReportMet3;
      return !!((_this$actualReportMet3 = this.actualReportMetadata) !== null && _this$actualReportMet3 !== void 0 && _this$actualReportMet3.dimension);
    },
    reportConditionTitle() {
      var _this$actualReportMet4;
      const dim = (_this$actualReportMet4 = this.actualReportMetadata) === null || _this$actualReportMet4 === void 0 ? void 0 : _this$actualReportMet4.dimension;
      return `${Object(external_CoreHome_["translate"])('CustomAlerts_When')} <span>${dim}</span>`;
    },
    isComparable() {
      const condition = this.actualAlert.metric_condition;
      return !!condition && condition.indexOf('_more_than') !== -1;
    },
    metricDescription() {
      const condition = this.actualAlert.metric_condition;
      const {
        metric
      } = this.actualAlert;
      const isPercentageCondition = condition && condition.indexOf('percentage_') === 0;
      const isPercentageMetric = metric && metric.indexOf('_rate') !== -1;
      const isSecondsMetric = metric && metric.indexOf('_time_') !== -1;
      if (isPercentageCondition || isPercentageMetric) {
        return '%';
      }
      if (isSecondsMetric) {
        return 's';
      }
      return Object(external_CoreHome_["translate"])('General_Value');
    }
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue



EditAlertvue_type_script_lang_ts.render = EditAlertvue_type_template_id_58071656_render

/* harmony default export */ var EditAlert = (EditAlertvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=template&id=50fdd954

const HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_1 = {
  class: "tableActionBar"
};
const HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_2 = ["href"];
const HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_3 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-table"
}, null, -1);
function HistoryTriggeredAlertsvue_type_template_id_50fdd954_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.translate('CustomAlerts_AlertsHistory')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderSlot"])(_ctx.$slots, "default"), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
      href: _ctx.customAlertsIndexLink
    }, [HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_ManageAlerts')), 1)], 8, HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_2)])]),
    _: 3
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=template&id=50fdd954

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=script&lang=ts


/* harmony default export */ var HistoryTriggeredAlertsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {},
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"]
  },
  computed: {
    customAlertsIndexLink() {
      return `?${external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CustomAlerts',
        action: 'index'
      }))}`;
    }
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue



HistoryTriggeredAlertsvue_type_script_lang_ts.render = HistoryTriggeredAlertsvue_type_template_id_50fdd954_render

/* harmony default export */ var HistoryTriggeredAlerts = (HistoryTriggeredAlertsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=template&id=1a8fe452

const ListAlertsPagevue_type_template_id_1a8fe452_hoisted_1 = {
  class: "ui-confirm",
  id: "confirm"
};
const ListAlertsPagevue_type_template_id_1a8fe452_hoisted_2 = ["value"];
const ListAlertsPagevue_type_template_id_1a8fe452_hoisted_3 = ["value"];
function ListAlertsPagevue_type_template_id_1a8fe452_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_ListAlerts = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ListAlerts");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.title
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ListAlerts, {
      alerts: _ctx.alerts
    }, null, 8, ["alerts"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", ListAlertsPagevue_type_template_id_1a8fe452_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_AreYouSureDeleteAlert')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      role: "yes",
      type: "button",
      value: _ctx.translate('General_Yes')
    }, null, 8, ListAlertsPagevue_type_template_id_1a8fe452_hoisted_2), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      role: "no",
      type: "button",
      value: _ctx.translate('General_No')
    }, null, 8, ListAlertsPagevue_type_template_id_1a8fe452_hoisted_3)])]),
    _: 1
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=template&id=1a8fe452

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=script&lang=ts



/* harmony default export */ var ListAlertsPagevue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    title: {
      type: String,
      required: true
    },
    alerts: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    ListAlerts: ListAlerts
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue



ListAlertsPagevue_type_script_lang_ts.render = ListAlertsPagevue_type_template_id_1a8fe452_render

/* harmony default export */ var ListAlertsPage = (ListAlertsPagevue_type_script_lang_ts);
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/index.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */




// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib-no-default.js




/***/ })

/******/ });
});
//# sourceMappingURL=CustomAlerts.umd.js.map