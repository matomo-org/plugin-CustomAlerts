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

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--12-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=template&id=38f90d41

var _hoisted_1 = {
  key: 0
};
var _hoisted_2 = {
  colspan: "6"
};

var _hoisted_3 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);

var _hoisted_4 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);

var _hoisted_5 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("br", null, null, -1);

var _hoisted_6 = {
  class: "name"
};
var _hoisted_7 = {
  class: "site"
};
var _hoisted_8 = {
  class: "period"
};
var _hoisted_9 = {
  class: "reportName"
};
var _hoisted_10 = {
  class: "edit"
};
var _hoisted_11 = ["href", "title"];
var _hoisted_12 = {
  class: "delete"
};
var _hoisted_13 = ["onClick", "id", "title"];

var _hoisted_14 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-delete"
}, null, -1);

var _hoisted_15 = [_hoisted_14];
var _hoisted_16 = {
  class: "tableActionBar"
};
var _hoisted_17 = ["href"];

var _hoisted_18 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);

var _hoisted_19 = ["href"];

var _hoisted_20 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-table"
}, null, -1);

function render(_ctx, _cache, $props, $setup, $data, $options) {
  var _ctx$alerts;

  var _directive_content_table = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("content-table");

  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("table", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Name')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Website')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Period')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Report')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Edit')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Delete')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [!((_ctx$alerts = _ctx.alerts) !== null && _ctx$alerts !== void 0 && _ctx$alerts.length) ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", _hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_2, [_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_NoAlertsDefined')) + " ", 1), _hoisted_4, _hoisted_5])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.alerts, function (alert) {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
      key: alert.idalert
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_6, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(alert.name), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.decode(alert.siteName)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.ucfirst(_ctx.translate("Intl_Period".concat(_ctx.ucfirst(alert.period))))), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_9, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(alert.reportName || '-'), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
      class: "table-action icon-edit",
      href: _ctx.linkTo({
        'module': 'CustomAlerts',
        'action': 'editAlert',
        'idAlert': alert.idalert
      }),
      title: _ctx.translate('General_Edit')
    }, null, 8, _hoisted_11)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_12, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      class: "deleteAlert table-action",
      onClick: function onClick($event) {
        return _ctx.deleteAlert(alert.idalert);
      },
      id: alert.idalert,
      title: _ctx.translate('General_Delete')
    }, _hoisted_15, 8, _hoisted_13)])]);
  }), 128))])], 512), [[_directive_content_table]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_16, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
    href: _ctx.linkTo({
      'module': 'CustomAlerts',
      'action': 'addNewAlert'
    })
  }, [_hoisted_18, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_CreateNewAlert')), 1)], 8, _hoisted_17), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
    href: _ctx.linkTo({
      'module': 'CustomAlerts',
      'action': 'historyTriggeredAlerts'
    })
  }, [_hoisted_20, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_AlertsHistory')), 1)], 8, _hoisted_19)])]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=template&id=38f90d41

// EXTERNAL MODULE: external "CoreHome"
var external_CoreHome_ = __webpack_require__("19dc");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--14-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--14-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=script&lang=ts


/* harmony default export */ var ListAlertsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    alerts: {
      type: Array,
      default: function _default() {
        return [];
      }
    }
  },
  directives: {
    ContentTable: external_CoreHome_["ContentTable"]
  },
  methods: {
    deleteAlert: function deleteAlert(idAlert) {
      external_CoreHome_["Matomo"].helper.modalConfirm('#confirm', {
        yes: function yes() {
          external_CoreHome_["AjaxHelper"].fetch({
            method: 'CustomAlerts.deleteAlert',
            idAlert: idAlert
          }).then(function () {
            external_CoreHome_["Matomo"].helper.redirect();
          });
        }
      });
    },
    ucfirst: function ucfirst(s) {
      return "".concat(s[0].toUpperCase()).concat(s.substr(1));
    },
    linkTo: function linkTo(params) {
      return "?".concat(external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), params)));
    },
    decode: function decode(s) {
      return external_CoreHome_["Matomo"].helper.htmlDecode(s);
    }
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlerts.vue



ListAlertsvue_type_script_lang_ts.render = render

/* harmony default export */ var ListAlerts = (ListAlertsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--12-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=template&id=15dff483

var EditAlertvue_type_template_id_15dff483_hoisted_1 = {
  id: "customAlertPeriodHelp",
  class: "inline-help-node"
};
var EditAlertvue_type_template_id_15dff483_hoisted_2 = {
  key: 0
};
var EditAlertvue_type_template_id_15dff483_hoisted_3 = {
  key: 1,
  class: "row"
};
var EditAlertvue_type_template_id_15dff483_hoisted_4 = {
  class: "col s12"
};

var EditAlertvue_type_template_id_15dff483_hoisted_5 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(": ");

var EditAlertvue_type_template_id_15dff483_hoisted_6 = ["innerHTML"];
var EditAlertvue_type_template_id_15dff483_hoisted_7 = {
  class: "row"
};
var EditAlertvue_type_template_id_15dff483_hoisted_8 = {
  class: "col s12"
};
var EditAlertvue_type_template_id_15dff483_hoisted_9 = {
  class: "row conditionAndValue"
};
var EditAlertvue_type_template_id_15dff483_hoisted_10 = {
  class: "col s12 m6"
};
var EditAlertvue_type_template_id_15dff483_hoisted_11 = {
  class: "col s12 m6"
};
var EditAlertvue_type_template_id_15dff483_hoisted_12 = {
  class: "ui-autocomplete-input",
  ref: "reportValue"
};
var EditAlertvue_type_template_id_15dff483_hoisted_13 = {
  class: "row conditionAndValue"
};
var EditAlertvue_type_template_id_15dff483_hoisted_14 = {
  class: "col s12 m6"
};
var EditAlertvue_type_template_id_15dff483_hoisted_15 = {
  class: "col s12 m6"
};
var EditAlertvue_type_template_id_15dff483_hoisted_16 = ["innerHTML"];
function EditAlertvue_type_template_id_15dff483_render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");

  var _component_SelectPhoneNumbers = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SelectPhoneNumbers");

  var _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");

  var _component_ActivityIndicator = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ActivityIndicator");

  var _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");

  var _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");

  var _directive_form = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("form");

  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.headline
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(function () {
      var _ctx$actualAlert$id_s, _ctx$actualReportMeta, _ctx$actualAlert;

      return [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        name: "alertName",
        modelValue: _ctx.actualAlert.name,
        "onUpdate:modelValue": _cache[0] || (_cache[0] = function ($event) {
          return _ctx.actualAlert.name = $event;
        }),
        maxlength: 100,
        title: _ctx.translate('CustomAlerts_AlertName')
      }, null, 8, ["modelValue", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "site",
        name: "idSite",
        "model-value": {
          id: (_ctx$actualAlert$id_s = _ctx.actualAlert.id_sites) === null || _ctx$actualAlert$id_s === void 0 ? void 0 : _ctx$actualAlert$id_s[0],
          name: _ctx.actualCurrentSite.name
        },
        "onUpdate:modelValue": _cache[1] || (_cache[1] = function ($event) {
          _ctx.actualAlert.id_sites = [$event.id];
          _ctx.actualCurrentSite = $event;

          _ctx.changeReport();
        }),
        title: _ctx.translate('General_Website'),
        introduction: _ctx.translate('CustomAlerts_ApplyTo')
      }, null, 8, ["model-value", "title", "introduction"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_YouCanChoosePeriodFrom')) + ": ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodDayDescription')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodWeekDescription')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, "• " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_PeriodMonthDescription')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "period",
        "inline-help": "#customAlertPeriodHelp",
        "model-value": _ctx.actualAlert.period,
        "onUpdate:modelValue": _cache[2] || (_cache[2] = function ($event) {
          _ctx.actualAlert.period = $event;

          _ctx.changeReport();
        }),
        title: _ctx.translate('General_Period'),
        options: _ctx.periodOptions
      }, null, 8, ["model-value", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "checkbox",
        name: "report_email_me",
        modelValue: _ctx.actualAlert.email_me,
        "onUpdate:modelValue": _cache[3] || (_cache[3] = function ($event) {
          return _ctx.actualAlert.email_me = $event;
        }),
        introduction: _ctx.translate('ScheduledReports_SendReportTo'),
        title: "".concat(_ctx.translate('ScheduledReports_SentToMe'), " (").concat(_ctx.currentUserEmail, ")")
      }, null, 8, ["modelValue", "introduction", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "textarea",
        modelValue: _ctx.actualAlert.additional_emails,
        "onUpdate:modelValue": _cache[4] || (_cache[4] = function ($event) {
          return _ctx.actualAlert.additional_emails = $event;
        }),
        "var-type": "array",
        title: _ctx.translate('ScheduledReports_AlsoSendReportToTheseEmails')
      }, null, 8, ["modelValue", "title"])]), _ctx.supportsSMS ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", EditAlertvue_type_template_id_15dff483_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SelectPhoneNumbers, {
        "phone-numbers": _ctx.phoneNumbers || [],
        modelValue: _ctx.actualAlert.phone_numbers,
        "onUpdate:modelValue": _cache[5] || (_cache[5] = function ($event) {
          return _ctx.actualAlert.phone_numbers = $event;
        })
      }, null, 8, ["phone-numbers", "modelValue"])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", EditAlertvue_type_template_id_15dff483_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_4, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Alert, {
        severity: "info"
      }, {
        default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(function () {
          return [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('MobileMessaging_PhoneNumbers')), 1), EditAlertvue_type_template_id_15dff483_hoisted_5, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
            innerHTML: _ctx.$sanitize(_ctx.mobileMessagingNotActivated)
          }, null, 8, EditAlertvue_type_template_id_15dff483_hoisted_6)];
        }),
        _: 1
      })])])), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "expandable-select",
        name: "report",
        "model-value": _ctx.actualAlert.report,
        "onUpdate:modelValue": _cache[6] || (_cache[6] = function ($event) {
          _ctx.actualAlert.report = $event;

          _ctx.changeReport();
        }),
        options: _ctx.reportOptions,
        title: "".concat(_ctx.translate('CustomAlerts_ThisAppliesTo'), ": ").concat((_ctx$actualReportMeta = _ctx.actualReportMetadata) === null || _ctx$actualReportMeta === void 0 ? void 0 : _ctx$actualReportMeta.name),
        introduction: _ctx.translate('CustomAlerts_AlertCondition'),
        "inline-help": _ctx.thisAppliesToInlineHelp
      }, null, 8, ["model-value", "options", "title", "introduction", "inline-help"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_8, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ActivityIndicator, {
        loading: _ctx.isLoadingReport
      }, null, 8, ["loading"])])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.isLoadingReport]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_9, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "reportCondition",
        modelValue: _ctx.actualAlert.report_condition,
        "onUpdate:modelValue": _cache[7] || (_cache[7] = function ($event) {
          return _ctx.actualAlert.report_condition = $event;
        }),
        "full-width": true,
        title: _ctx.reportConditionTitle,
        options: _ctx.alertGroupConditions
      }, null, 8, ["modelValue", "title", "options"])])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_11, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_12, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        role: "textbox",
        name: "reportValue",
        modelValue: _ctx.actualAlert.report_matched,
        "onUpdate:modelValue": _cache[8] || (_cache[8] = function ($event) {
          return _ctx.actualAlert.report_matched = $event;
        }),
        "full-width": true,
        autocomplete: 'off',
        maxlength: 255,
        title: _ctx.translate('General_Value')
      }, null, 8, ["modelValue", "title"]), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.actualAlert.report_condition !== 'matches_any']])], 512)])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.hasReportDimension]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "metric",
        "model-value": _ctx.actualAlert.metric,
        "onUpdate:modelValue": _cache[9] || (_cache[9] = function ($event) {
          return _ctx.actualAlert.metric = $event;
        }),
        options: _ctx.metricOptions,
        introduction: _ctx.translate('CustomAlerts_AlertMeWhen')
      }, null, 8, ["model-value", "options", "introduction"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_13, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_14, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: "metricCondition",
        "model-value": _ctx.actualAlert.metric_condition,
        "onUpdate:modelValue": _cache[10] || (_cache[10] = function ($event) {
          return _ctx.actualAlert.metric_condition = $event;
        }),
        "full-width": true,
        options: _ctx.metricConditionOptions
      }, null, 8, ["model-value", "options"])])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", EditAlertvue_type_template_id_15dff483_hoisted_15, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        name: "metricValue",
        class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])({
          invalid: _ctx.isMetricValueInvalid
        }),
        modelValue: _ctx.actualAlert.metric_matched,
        "onUpdate:modelValue": _cache[11] || (_cache[11] = function ($event) {
          return _ctx.actualAlert.metric_matched = $event;
        }),
        title: "<span>".concat(_ctx.metricDescription, "</span>"),
        "full-width": true
      }, null, 8, ["class", "modelValue", "title"])])])]), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.comparablesDates, function (comparablesDatesPeriod, period) {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
          key: period
        }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
          uicontrol: "select",
          name: "compared_to",
          modelValue: _ctx.comparedTo[period],
          "onUpdate:modelValue": function onUpdateModelValue($event) {
            return _ctx.comparedTo[period] = $event;
          },
          disabled: Object.keys(comparablesDatesPeriod).length <= 1,
          options: comparablesDatesPeriod,
          introduction: _ctx.translate('CustomAlerts_ComparedToThe')
        }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "options", "introduction"]), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], period === _ctx.actualAlert.period && _ctx.isComparable]])]);
      }), 128)), (_ctx$actualAlert = _ctx.actualAlert) !== null && _ctx$actualAlert !== void 0 && _ctx$actualAlert.idalert ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_SaveButton, {
        key: 2,
        onClick: _cache[12] || (_cache[12] = function ($event) {
          return _ctx.updateAlert(_ctx.actualAlert.idalert);
        }),
        saving: _ctx.isLoading
      }, null, 8, ["saving"])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_SaveButton, {
        key: 3,
        onClick: _cache[13] || (_cache[13] = function ($event) {
          return _ctx.createAlert();
        }),
        saving: _ctx.isLoading
      }, null, 8, ["saving"])), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", {
        class: "entityCancel",
        innerHTML: _ctx.$sanitize(_ctx.cancelLink)
      }, null, 8, EditAlertvue_type_template_id_15dff483_hoisted_16)], 512), [[_directive_form]])];
    }),
    _: 1
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=template&id=15dff483

// EXTERNAL MODULE: external "CorePluginsAdmin"
var external_CorePluginsAdmin_ = __webpack_require__("a5a2");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--14-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--14-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/EditAlert/EditAlert.vue?vue&type=script&lang=ts
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }




var SelectPhoneNumbers = Object(external_CoreHome_["useExternalPluginComponent"])('MobileMessaging', 'SelectPhoneNumbers');

function isBlockedReportApiMethod(apiMethodUniqueId) {
  return apiMethodUniqueId === 'MultiSites_getOne' || apiMethodUniqueId === 'MultiSites_getAll';
}

var _window = window,
    $ = _window.$;
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
    currentUserEmail: {
      type: String,
      required: true
    },
    supportsSMS: Boolean,
    phoneNumbers: [Array, Object],
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
    SelectPhoneNumbers: SelectPhoneNumbers,
    ContentBlock: external_CoreHome_["ContentBlock"]
  },
  directives: {
    Form: external_CorePluginsAdmin_["Form"]
  },
  data: function data() {
    var currentSite = this.currentSite;
    var alert = this.alert;
    var reportMetadata = this.reportMetadata; // set comparedTo for each comparison (defaulting to first available value)

    var comparedTo = Object.fromEntries(Object.entries(this.comparablesDates).map(function (_ref) {
      var _dates$;

      var _ref2 = _slicedToArray(_ref, 2),
          period = _ref2[0],
          dates = _ref2[1];

      return [period, dates === null || dates === void 0 ? void 0 : (_dates$ = dates[0]) === null || _dates$ === void 0 ? void 0 : _dates$.key];
    }));

    if (this.alert) {
      comparedTo[this.alert.period] = "".concat(alert.compared_to);
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
        id_sites: [(currentSite === null || currentSite === void 0 ? void 0 : currentSite.id) || external_CoreHome_["Matomo"].idSite]
      },
      comparedTo: comparedTo,
      actualCurrentSite: {
        id: currentSite.id,
        // in PHP, currentSite's name is the value in the DB, which is encoded
        name: external_CoreHome_["Matomo"].helper.htmlDecode(currentSite.name)
      }
    };
  },
  watch: {
    actualReportMetadata: function actualReportMetadata() {
      var _this$actualReportMet;

      var metrics = (_this$actualReportMet = this.actualReportMetadata) === null || _this$actualReportMet === void 0 ? void 0 : _this$actualReportMet.metrics;

      if (!metrics) {
        return;
      }

      if (!this.actualAlert.metric || !metrics[this.actualAlert.metric]) {
        var _Object$keys = Object.keys(metrics);

        var _Object$keys2 = _slicedToArray(_Object$keys, 1);

        this.actualAlert.metric = _Object$keys2[0];
      }
    },
    isMetricValueInvalid: function isMetricValueInvalid(newValue) {
      if (!newValue) {
        return;
      }

      var notificationInstanceId = external_CoreHome_["NotificationsStore"].show({
        message: Object(external_CoreHome_["translate"])('CustomAlerts_InvalidMetricValue'),
        id: 'CustomAlertsMetricValueError',
        context: 'error',
        type: 'toast'
      });
      external_CoreHome_["NotificationsStore"].scrollToNotification(notificationInstanceId);
    }
  },
  created: function created() {
    var _this = this;

    this.changeReport();
    setTimeout(function () {
      $(_this.$refs.reportValue).find('input').autocomplete({
        source: _this.getValuesForReportAndMetric.bind(_this),
        minLength: 1,
        delay: 300
      });
    }, 1000);
  },
  methods: {
    renderForm: function renderForm(data) {
      var _this2 = this;

      var options = [];
      this.actualReportMetadata = null;
      data.forEach(function (reportMetadata) {
        var reportApiMethod = reportMetadata.uniqueId;

        if (isBlockedReportApiMethod(reportApiMethod)) {
          return;
        }

        if (!_this2.actualAlert.report) {
          _this2.actualAlert.report = reportApiMethod;
        }

        options.push({
          key: reportApiMethod,
          value: reportMetadata.name,
          group: reportMetadata.category
        });

        if (reportApiMethod === _this2.actualAlert.report) {
          _this2.actualReportMetadata = reportMetadata;
        }
      });
      this.reportOptions = options;
    },
    sendApiRequest: function sendApiRequest(method, postParams) {
      var _this3 = this;

      this.isLoading = true;
      var period = this.actualAlert.period;
      external_CoreHome_["AjaxHelper"].post({
        period: period,
        method: method
      }, postParams).then(function () {
        external_CoreHome_["Matomo"].helper.redirect({
          module: 'CustomAlerts',
          action: 'index'
        });
      }).finally(function () {
        _this3.isLoading = false;
      });
    },
    getValuesForReportAndMetric: function getValuesForReportAndMetric(request, response) {
      var _this$actualAlert$id_,
          _this4 = this;

      var metric = this.actualAlert.metric; // eslint-disable-next-line @typescript-eslint/no-explicit-any

      function sendFeedback(values) {
        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
        response($.grep(values, function (value) {
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
      var report = this.actualReportMetadata;

      if (!report) {
        return;
      }

      var apiModule = report.module;
      var apiAction = report.action;

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
        apiModule: apiModule,
        apiAction: apiAction,
        idSite: (_this$actualAlert$id_ = this.actualAlert.id_sites) === null || _this$actualAlert$id_ === void 0 ? void 0 : _this$actualAlert$id_[0],
        format: 'JSON'
      }).then(function (data) {
        if (data !== null && data !== void 0 && data.reportData) {
          _this4.reportValuesAutoComplete = data.reportData;
          sendFeedback(data.reportData);
        } else {
          sendFeedback([]);
        }
      }).catch(function () {
        sendFeedback([]);
      });
    },
    changeReport: function changeReport() {
      var _this$actualAlert$id_2,
          _this5 = this;

      this.isLoadingReport = true;
      this.reportValuesAutoComplete = null;
      external_CoreHome_["AjaxHelper"].fetch({
        method: 'API.getReportMetadata',
        date: external_CoreHome_["Matomo"].currentDateString,
        period: this.actualAlert.period,
        idSite: (_this$actualAlert$id_2 = this.actualAlert.id_sites) === null || _this$actualAlert$id_2 === void 0 ? void 0 : _this$actualAlert$id_2[0],
        filter_limit: '-1'
      }).then(function (data) {
        _this5.renderForm(data);
      }).finally(function () {
        _this5.isLoadingReport = false;
      });
    },
    createAlert: function createAlert() {
      if (this.isMetricValueInvalid) {
        return false;
      }

      this.sendApiRequest('CustomAlerts.addAlert', this.apiParameters);
      return true;
    },
    updateAlert: function updateAlert() {
      if (this.isMetricValueInvalid) {
        return false;
      }

      this.sendApiRequest('CustomAlerts.editAlert', this.apiParameters);
      return true;
    }
  },
  computed: {
    apiParameters: function apiParameters() {
      var _this$actualAlert$add, _this$actualAlert$pho;

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
        reportUniqueId: this.actualAlert.report,
        reportCondition: this.actualAlert.report_condition,
        reportValue: this.actualAlert.report_matched,
        idSites: this.actualAlert.id_sites,
        comparedTo: this.comparedTo[this.actualAlert.period]
      };
    },
    isMetricValueInvalid: function isMetricValueInvalid() {
      return !$.isNumeric(this.actualAlert.metric_matched);
    },
    mobileMessagingNotActivated: function mobileMessagingNotActivated() {
      var link = "?".concat(external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CorePluginsAdmin',
        action: 'plugins',
        updated: null
      })));
      return Object(external_CoreHome_["translate"])('CustomAlerts_MobileMessagingPluginNotActivated', "<a href=\"".concat(link, "#MobileMessaging\">"), '</a>');
    },
    cancelLink: function cancelLink() {
      var backlink = "?".concat(external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CustomAlerts',
        action: 'index'
      })));
      return Object(external_CoreHome_["translate"])('General_OrCancel', "<a class=\"entityCancelLink\" href=\"".concat(backlink, "\">"), '</a>');
    },
    thisAppliesToInlineHelp: function thisAppliesToInlineHelp() {
      var link1 = 'https://matomo.org/guide/manage-matomo/custom-alerts/';
      var link2 = 'https://matomo.org/faq/general/examples-of-custom-alerts#events';
      return Object(external_CoreHome_["translate"])('CustomAlerts_ThisAppliesToHelp', "<a target=\"_blank\" href=\"".concat(link1, "\" rel=\"noreferrer noopener\">"), '</a>', '<strong>', '</strong>', "<a target=\"_blank\" href=\"".concat(link2, "\" rel=\"noreferrer noopener\">"), '</a>');
    },
    metricOptions: function metricOptions() {
      var _this$actualReportMet2;

      return Object.entries(((_this$actualReportMet2 = this.actualReportMetadata) === null || _this$actualReportMet2 === void 0 ? void 0 : _this$actualReportMet2.metrics) || {}).map(function (_ref3) {
        var _ref4 = _slicedToArray(_ref3, 2),
            key = _ref4[0],
            value = _ref4[1];

        return {
          key: key,
          value: value
        };
      });
    },
    hasReportDimension: function hasReportDimension() {
      var _this$actualReportMet3;

      return !!((_this$actualReportMet3 = this.actualReportMetadata) !== null && _this$actualReportMet3 !== void 0 && _this$actualReportMet3.dimension);
    },
    reportConditionTitle: function reportConditionTitle() {
      var _this$actualReportMet4;

      var dim = (_this$actualReportMet4 = this.actualReportMetadata) === null || _this$actualReportMet4 === void 0 ? void 0 : _this$actualReportMet4.dimension;
      return "".concat(Object(external_CoreHome_["translate"])('CustomAlerts_When'), " <span>").concat(dim, "</span>");
    },
    isComparable: function isComparable() {
      var condition = this.actualAlert.metric_condition;
      return !!condition && condition.indexOf('_more_than') !== -1;
    },
    metricDescription: function metricDescription() {
      var condition = this.actualAlert.metric_condition;
      var metric = this.actualAlert.metric;
      var isPercentageCondition = condition && condition.indexOf('percentage_') === 0;
      var isPercentageMetric = metric && metric.indexOf('_rate') !== -1;
      var isSecondsMetric = metric && metric.indexOf('_time_') !== -1;

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



EditAlertvue_type_script_lang_ts.render = EditAlertvue_type_template_id_15dff483_render

/* harmony default export */ var EditAlert = (EditAlertvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--12-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=template&id=50fdd954

var HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_1 = {
  class: "tableActionBar"
};
var HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_2 = ["href"];

var HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_3 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-table"
}, null, -1);

function HistoryTriggeredAlertsvue_type_template_id_50fdd954_render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");

  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.translate('CustomAlerts_AlertsHistory')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(function () {
      return [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderSlot"])(_ctx.$slots, "default"), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
        href: _ctx.customAlertsIndexLink
      }, [HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_ManageAlerts')), 1)], 8, HistoryTriggeredAlertsvue_type_template_id_50fdd954_hoisted_2)])];
    }),
    _: 3
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=template&id=50fdd954

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--14-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--14-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=script&lang=ts


/* harmony default export */ var HistoryTriggeredAlertsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {},
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"]
  },
  computed: {
    customAlertsIndexLink: function customAlertsIndexLink() {
      return "?".concat(external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].urlParsed.value), {}, {
        module: 'CustomAlerts',
        action: 'index'
      })));
    }
  }
}));
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/HistoryTriggeredAlerts/HistoryTriggeredAlerts.vue



HistoryTriggeredAlertsvue_type_script_lang_ts.render = HistoryTriggeredAlertsvue_type_template_id_50fdd954_render

/* harmony default export */ var HistoryTriggeredAlerts = (HistoryTriggeredAlertsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--12-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=template&id=1a8fe452

var ListAlertsPagevue_type_template_id_1a8fe452_hoisted_1 = {
  class: "ui-confirm",
  id: "confirm"
};
var ListAlertsPagevue_type_template_id_1a8fe452_hoisted_2 = ["value"];
var ListAlertsPagevue_type_template_id_1a8fe452_hoisted_3 = ["value"];
function ListAlertsPagevue_type_template_id_1a8fe452_render(_ctx, _cache, $props, $setup, $data, $options) {
  var _component_ListAlerts = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ListAlerts");

  var _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");

  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    class: "alerts",
    "content-title": _ctx.title
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(function () {
      return [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ListAlerts, {
        alerts: _ctx.alerts
      }, null, 8, ["alerts"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", ListAlertsPagevue_type_template_id_1a8fe452_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CustomAlerts_AreYouSureDeleteAlert')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
        role: "yes",
        type: "button",
        value: _ctx.translate('General_Yes')
      }, null, 8, ListAlertsPagevue_type_template_id_1a8fe452_hoisted_2), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
        role: "no",
        type: "button",
        value: _ctx.translate('General_No')
      }, null, 8, ListAlertsPagevue_type_template_id_1a8fe452_hoisted_3)])];
    }),
    _: 1
  }, 8, ["content-title"]);
}
// CONCATENATED MODULE: ./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=template&id=1a8fe452

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--14-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--14-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--0-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--0-1!./plugins/CustomAlerts/vue/src/ListAlerts/ListAlertsPage.vue?vue&type=script&lang=ts



/* harmony default export */ var ListAlertsPagevue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    title: {
      type: String,
      required: true
    },
    alerts: {
      type: Array,
      default: function _default() {
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