(function(global, factory) {
  typeof exports === "object" && typeof module !== "undefined" ? factory(exports, require("vue"), require("CoreHome"), require("CorePluginsAdmin")) : typeof define === "function" && define.amd ? define(["exports", "vue", "CoreHome", "CorePluginsAdmin"], factory) : (global = typeof globalThis !== "undefined" ? globalThis : global || self, factory(global.CustomAlerts = {}, global.Vue, global.CoreHome, global.CorePluginsAdmin));
})(this, (function(exports2, vue, CoreHome, CorePluginsAdmin) {
  "use strict";var __defProp = Object.defineProperty;
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

  const _sfc_main$3 = vue.defineComponent({
    props: {
      alerts: {
        type: Array,
        default() {
          return [];
        }
      }
    },
    directives: {
      ContentTable: CoreHome.ContentTable
    },
    methods: {
      deleteAlert(idAlert) {
        CoreHome.Matomo.helper.modalConfirm("#confirm", {
          yes: () => {
            CoreHome.AjaxHelper.fetch({
              method: "CustomAlerts.deleteAlert",
              idAlert
            }).then(() => {
              CoreHome.Matomo.helper.redirect();
            });
          }
        });
      },
      ucfirst(s) {
        return `${s[0].toUpperCase()}${s.substr(1)}`;
      },
      linkTo(params) {
        return `?${CoreHome.MatomoUrl.stringify(__spreadValues(__spreadValues({}, CoreHome.MatomoUrl.urlParsed.value), params))}`;
      },
      decode(s) {
        return CoreHome.Matomo.helper.htmlDecode(s);
      }
    }
  });
  const _export_sfc = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
      target[key] = val;
    }
    return target;
  };
  const _hoisted_1$3 = { key: 0 };
  const _hoisted_2$3 = { colspan: "6" };
  const _hoisted_3$2 = { class: "name" };
  const _hoisted_4$1 = { class: "site" };
  const _hoisted_5$1 = { class: "period" };
  const _hoisted_6$1 = { class: "reportName" };
  const _hoisted_7$1 = { class: "edit" };
  const _hoisted_8$1 = ["href", "title"];
  const _hoisted_9$1 = ["onClick", "id", "title"];
  const _hoisted_10$1 = { class: "tableActionBar" };
  const _hoisted_11$1 = ["href"];
  const _hoisted_12$1 = ["href"];
  function _sfc_render$3(_ctx, _cache, $props, $setup, $data, $options) {
    var _a;
    const _directive_content_table = vue.resolveDirective("content-table");
    return vue.openBlock(), vue.createElementBlock("div", null, [
      vue.withDirectives((vue.openBlock(), vue.createElementBlock("table", null, [
        vue.createElementVNode("thead", null, [
          vue.createElementVNode("tr", null, [
            vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("General_Name")), 1),
            vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("General_Website")), 1),
            vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("General_Period")), 1),
            vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("General_Report")), 1),
            vue.createElementVNode("th", null, vue.toDisplayString(_ctx.translate("General_Actions")), 1)
          ])
        ]),
        vue.createElementVNode("tbody", null, [
          !((_a = _ctx.alerts) == null ? void 0 : _a.length) ? (vue.openBlock(), vue.createElementBlock("tr", _hoisted_1$3, [
            vue.createElementVNode("td", _hoisted_2$3, [
              _cache[0] || (_cache[0] = vue.createElementVNode("br", null, null, -1)),
              vue.createTextVNode(" " + vue.toDisplayString(_ctx.translate("CustomAlerts_NoAlertsDefined")) + " ", 1),
              _cache[1] || (_cache[1] = vue.createElementVNode("br", null, null, -1)),
              _cache[2] || (_cache[2] = vue.createElementVNode("br", null, null, -1))
            ])
          ])) : vue.createCommentVNode("", true),
          (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(_ctx.alerts, (alert) => {
            return vue.openBlock(), vue.createElementBlock("tr", {
              key: alert.idalert
            }, [
              vue.createElementVNode("td", _hoisted_3$2, vue.toDisplayString(alert.name), 1),
              vue.createElementVNode("td", _hoisted_4$1, vue.toDisplayString(_ctx.decode(alert.siteName)), 1),
              vue.createElementVNode("td", _hoisted_5$1, vue.toDisplayString(_ctx.ucfirst(_ctx.translate(`Intl_Period${_ctx.ucfirst(alert.period)}`))), 1),
              vue.createElementVNode("td", _hoisted_6$1, vue.toDisplayString(alert.reportName || "-"), 1),
              vue.createElementVNode("td", _hoisted_7$1, [
                vue.createElementVNode("a", {
                  class: "table-action icon-edit",
                  href: _ctx.linkTo({
                    "module": "CustomAlerts",
                    "action": "editAlert",
                    "idAlert": alert.idalert
                  }),
                  title: _ctx.translate("General_Edit")
                }, null, 8, _hoisted_8$1),
                vue.createElementVNode("button", {
                  class: "deleteAlert table-action",
                  onClick: ($event) => _ctx.deleteAlert(alert.idalert),
                  id: alert.idalert,
                  title: _ctx.translate("General_Delete")
                }, [..._cache[3] || (_cache[3] = [
                  vue.createElementVNode("span", { class: "icon-delete" }, null, -1)
                ])], 8, _hoisted_9$1)
              ])
            ]);
          }), 128))
        ])
      ])), [
        [_directive_content_table]
      ]),
      vue.createElementVNode("div", _hoisted_10$1, [
        vue.createElementVNode("a", {
          href: _ctx.linkTo({ "module": "CustomAlerts", "action": "addNewAlert" })
        }, [
          _cache[4] || (_cache[4] = vue.createElementVNode("span", { class: "icon-add" }, null, -1)),
          vue.createTextVNode(" " + vue.toDisplayString(_ctx.translate("CustomAlerts_CreateNewAlert")), 1)
        ], 8, _hoisted_11$1),
        vue.createElementVNode("a", {
          href: _ctx.linkTo({ "module": "CustomAlerts", "action": "historyTriggeredAlerts" })
        }, [
          _cache[5] || (_cache[5] = vue.createElementVNode("span", { class: "icon-table" }, null, -1)),
          vue.createTextVNode(" " + vue.toDisplayString(_ctx.translate("CustomAlerts_AlertsHistory")), 1)
        ], 8, _hoisted_12$1)
      ])
    ]);
  }
  const ListAlerts = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["render", _sfc_render$3]]);
  const SelectPhoneNumbers = CoreHome.useExternalPluginComponent("MobileMessaging", "SelectPhoneNumbers");
  const SelectSlackChannel = CoreHome.useExternalPluginComponent("Slack", "SelectSlackChannel");
  const SelectMicrosoftTeamsWebhookUrl = CoreHome.useExternalPluginComponent("MicrosoftTeams", "SelectMicrosoftTeamsWebhookUrl");
  function isBlockedReportApiMethod(apiMethodUniqueId) {
    return apiMethodUniqueId === "MultiSites_getOne" || apiMethodUniqueId === "MultiSites_getAll";
  }
  const { $ } = window;
  const _sfc_main$2 = vue.defineComponent({
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
      Field: CorePluginsAdmin.Field,
      Alert: CoreHome.Alert,
      ActivityIndicator: CoreHome.ActivityIndicator,
      SaveButton: CorePluginsAdmin.SaveButton,
      SelectPhoneNumbers,
      SelectSlackChannel,
      SelectMicrosoftTeamsWebhookUrl,
      ContentBlock: CoreHome.ContentBlock
    },
    directives: {
      Form: CorePluginsAdmin.Form
    },
    data() {
      const currentSite = this.currentSite;
      const alert = this.alert;
      const reportMetadata = this.reportMetadata;
      const comparedTo = Object.fromEntries(
        Object.entries(this.comparablesDates).map(([period, dates]) => {
          var _a;
          return [period, (_a = dates == null ? void 0 : dates[0]) == null ? void 0 : _a.key];
        })
      );
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
        actualAlert: alert ? __spreadValues({}, alert) : {
          description: "",
          period: "day",
          id_sites: [(currentSite == null ? void 0 : currentSite.id) || CoreHome.Matomo.idSite],
          report_mediums: []
        },
        comparedTo,
        actualCurrentSite: {
          id: currentSite.id,
          // in PHP, currentSite's name is the value in the DB, which is encoded
          name: CoreHome.Matomo.helper.htmlDecode(currentSite.name)
        }
      };
    },
    watch: {
      actualReportMetadata() {
        var _a;
        const metrics = (_a = this.actualReportMetadata) == null ? void 0 : _a.metrics;
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
        const notificationInstanceId = CoreHome.NotificationsStore.show({
          message: CoreHome.translate("CustomAlerts_InvalidMetricValue"),
          id: "CustomAlertsMetricValueError",
          context: "error",
          type: "toast"
        });
        CoreHome.NotificationsStore.scrollToNotification(notificationInstanceId);
      }
    },
    created() {
      this.changeReport();
      setTimeout(() => {
        $(this.$refs.reportValue).find("input").autocomplete({
          source: this.getValuesForReportAndMetric.bind(this),
          minLength: 1,
          delay: 300
        });
      }, 1e3);
    },
    methods: {
      renderForm(data) {
        const options = [];
        this.actualReportMetadata = null;
        data.forEach((reportMetadata) => {
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
        const { period } = this.actualAlert;
        CoreHome.AjaxHelper.post(
          {
            period,
            method
          },
          postParams
        ).then(() => {
          CoreHome.Matomo.helper.redirect({
            module: "CustomAlerts",
            action: "index"
          });
        }).finally(() => {
          this.isLoading = false;
        });
      },
      getValuesForReportAndMetric(request, response) {
        var _a;
        const { metric } = this.actualAlert;
        function sendFeedback(values) {
          const matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
          response($.grep(values, (value) => {
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
        CoreHome.AjaxHelper.fetch({
          method: "API.getProcessedReport",
          date: "yesterday",
          period: "month",
          disable_queued_filters: 1,
          flat: 1,
          filter_limit: -1,
          showColumns: metric,
          language: "en",
          apiModule,
          apiAction,
          idSite: (_a = this.actualAlert.id_sites) == null ? void 0 : _a[0],
          format: "JSON"
        }).then((data) => {
          if (data == null ? void 0 : data.reportData) {
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
        var _a;
        this.isLoadingReport = true;
        this.reportValuesAutoComplete = null;
        CoreHome.AjaxHelper.fetch({
          method: "API.getReportMetadata",
          date: CoreHome.Matomo.currentDateString,
          period: this.actualAlert.period,
          idSite: (_a = this.actualAlert.id_sites) == null ? void 0 : _a[0],
          filter_limit: "-1"
        }).then((data) => {
          this.renderForm(data);
        }).finally(() => {
          this.isLoadingReport = false;
        });
      },
      createAlert() {
        if (this.isMetricValueInvalid) {
          return false;
        }
        this.sendApiRequest("CustomAlerts.addAlert", this.apiParameters);
        return true;
      },
      updateAlert() {
        if (this.isMetricValueInvalid) {
          return false;
        }
        this.sendApiRequest("CustomAlerts.editAlert", this.apiParameters);
        return true;
      }
    },
    computed: {
      apiParameters() {
        var _a, _b, _c, _d;
        return {
          idAlert: this.actualAlert.idalert,
          format: "json",
          name: this.actualAlert.name,
          description: this.actualAlert.description,
          metric: this.actualAlert.metric,
          metricCondition: this.actualAlert.metric_condition,
          metricValue: this.actualAlert.metric_matched,
          emailMe: this.actualAlert.email_me ? 1 : 0,
          additionalEmails: ((_a = this.actualAlert.additional_emails) == null ? void 0 : _a.length) ? this.actualAlert.additional_emails : [""],
          phoneNumbers: ((_b = this.actualAlert.phone_numbers) == null ? void 0 : _b.length) ? this.actualAlert.phone_numbers : [""],
          slackChannelID: ((_c = this.actualAlert) == null ? void 0 : _c.slack_channel_id) ? this.actualAlert.slack_channel_id : "",
          msTeamsWebhookUrl: ((_d = this.actualAlert) == null ? void 0 : _d.ms_teams_webhook_url) ? this.actualAlert.ms_teams_webhook_url : "",
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
        const link = `?${CoreHome.MatomoUrl.stringify(__spreadProps(__spreadValues({}, CoreHome.MatomoUrl.urlParsed.value), {
          module: "CorePluginsAdmin",
          action: "plugins",
          updated: null
        }))}`;
        return CoreHome.translate(
          "CustomAlerts_MobileMessagingPluginNotActivated",
          `<a href="${link}#MobileMessaging">`,
          "</a>"
        );
      },
      cancelLink() {
        const backlink = `?${CoreHome.MatomoUrl.stringify(__spreadProps(__spreadValues({}, CoreHome.MatomoUrl.urlParsed.value), {
          module: "CustomAlerts",
          action: "index"
        }))}`;
        return CoreHome.translate(
          "General_OrCancel",
          `<a class="entityCancelLink" href="${backlink}">`,
          "</a>"
        );
      },
      thisAppliesToInlineHelp() {
        const link1 = "https://matomo.org/guide/manage-matomo/custom-alerts/";
        const link2 = "https://matomo.org/faq/general/examples-of-custom-alerts#events";
        return CoreHome.translate(
          "CustomAlerts_ThisAppliesToHelp",
          `<a target="_blank" href="${link1}" rel="noreferrer noopener">`,
          "</a>",
          "<strong>",
          "</strong>",
          `<a target="_blank" href="${link2}" rel="noreferrer noopener">`,
          "</a>"
        );
      },
      getDeliveryMediumInlineTooltip() {
        return `${CoreHome.translate("CustomAlerts_CreateTooltip")} ${CoreHome.externalLink("https://matomo.org/faq/general/create-and-manage-custom-alerts/")}  ${CoreHome.translate("CustomAlerts_LearnMore")}.`;
      },
      metricOptions() {
        var _a;
        return Object.entries(((_a = this.actualReportMetadata) == null ? void 0 : _a.metrics) || {}).map(([key, value]) => ({
          key,
          value
        }));
      },
      hasReportDimension() {
        var _a;
        return !!((_a = this.actualReportMetadata) == null ? void 0 : _a.dimension);
      },
      reportConditionTitle() {
        var _a;
        const dim = (_a = this.actualReportMetadata) == null ? void 0 : _a.dimension;
        return `${CoreHome.translate("CustomAlerts_When")} <span>${dim}</span>`;
      },
      isComparable() {
        const condition = this.actualAlert.metric_condition;
        return !!condition && condition.indexOf("_more_than") !== -1;
      },
      metricDescription() {
        const condition = this.actualAlert.metric_condition;
        const { metric } = this.actualAlert;
        const isPercentageCondition = condition && condition.indexOf("percentage_") === 0;
        const isPercentageMetric = metric && metric.indexOf("_rate") !== -1;
        const isSecondsMetric = metric && metric.indexOf("_time_") !== -1;
        if (isPercentageCondition || isPercentageMetric) {
          return "%";
        }
        if (isSecondsMetric) {
          return "s";
        }
        return CoreHome.translate("General_Value");
      }
    }
  });
  const _hoisted_1$2 = {
    id: "customAlertPeriodHelp",
    class: "inline-help-node"
  };
  const _hoisted_2$2 = { class: "report-mediums" };
  const _hoisted_3$1 = { key: 0 };
  const _hoisted_4 = { key: 1 };
  const _hoisted_5 = { key: 0 };
  const _hoisted_6 = {
    key: 1,
    class: "row"
  };
  const _hoisted_7 = { class: "col s12" };
  const _hoisted_8 = ["innerHTML"];
  const _hoisted_9 = { key: 2 };
  const _hoisted_10 = { key: 3 };
  const _hoisted_11 = { class: "row" };
  const _hoisted_12 = { class: "col s12" };
  const _hoisted_13 = { class: "row conditionAndValue" };
  const _hoisted_14 = { class: "col s12 m6" };
  const _hoisted_15 = { class: "col s12 m6" };
  const _hoisted_16 = {
    class: "ui-autocomplete-input",
    ref: "reportValue"
  };
  const _hoisted_17 = { class: "row conditionAndValue" };
  const _hoisted_18 = { class: "col s12 m6" };
  const _hoisted_19 = { class: "col s12 m6" };
  const _hoisted_20 = ["innerHTML"];
  function _sfc_render$2(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_Field = vue.resolveComponent("Field");
    const _component_SelectPhoneNumbers = vue.resolveComponent("SelectPhoneNumbers");
    const _component_Alert = vue.resolveComponent("Alert");
    const _component_SelectSlackChannel = vue.resolveComponent("SelectSlackChannel");
    const _component_SelectMicrosoftTeamsWebhookUrl = vue.resolveComponent("SelectMicrosoftTeamsWebhookUrl");
    const _component_ActivityIndicator = vue.resolveComponent("ActivityIndicator");
    const _component_SaveButton = vue.resolveComponent("SaveButton");
    const _component_ContentBlock = vue.resolveComponent("ContentBlock");
    const _directive_form = vue.resolveDirective("form");
    return vue.openBlock(), vue.createBlock(_component_ContentBlock, {
      class: "alerts",
      "content-title": _ctx.headline
    }, {
      default: vue.withCtx(() => {
        var _a, _b, _c;
        return [
          vue.createElementVNode("p", null, vue.toDisplayString(_ctx.translate("CustomAlerts_CreateTooltip")), 1),
          vue.withDirectives((vue.openBlock(), vue.createElementBlock("div", null, [
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "text",
                name: "alertName",
                modelValue: _ctx.actualAlert.name,
                "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => _ctx.actualAlert.name = $event),
                maxlength: 100,
                title: _ctx.translate("CustomAlerts_AlertName"),
                placeholder: _ctx.translate("CustomAlerts_AlertNamePlaceholder"),
                "inline-help": _ctx.translate("CustomAlerts_AlertNameInlineHelp")
              }, null, 8, ["modelValue", "title", "placeholder", "inline-help"])
            ]),
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "textarea",
                name: "alertDescription",
                modelValue: _ctx.actualAlert.description,
                "onUpdate:modelValue": _cache[1] || (_cache[1] = ($event) => _ctx.actualAlert.description = $event),
                maxlength: 255,
                rows: 3,
                "ui-control-attributes": { class: "compact-textarea" },
                title: _ctx.translate("CustomAlerts_AlertDescriptionOptional"),
                placeholder: _ctx.translate("CustomAlerts_AlertDescriptionPlaceholder"),
                "inline-help": _ctx.translate("CustomAlerts_AlertDescriptionInlineHelp")
              }, null, 8, ["modelValue", "title", "placeholder", "inline-help"])
            ]),
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "site",
                name: "idSite",
                "model-value": { id: (_a = _ctx.actualAlert.id_sites) == null ? void 0 : _a[0], name: _ctx.actualCurrentSite.name },
                "onUpdate:modelValue": _cache[2] || (_cache[2] = ($event) => {
                  _ctx.actualAlert.id_sites = [$event.id];
                  _ctx.actualCurrentSite = $event;
                  _ctx.changeReport();
                }),
                title: _ctx.translate("General_Website"),
                introduction: _ctx.translate("CustomAlerts_ApplyTo")
              }, null, 8, ["model-value", "title", "introduction"])
            ]),
            vue.createElementVNode("div", _hoisted_1$2, [
              vue.createTextVNode(vue.toDisplayString(_ctx.translate("CustomAlerts_YouCanChoosePeriodFrom")) + ": ", 1),
              vue.createElementVNode("ul", null, [
                vue.createElementVNode("li", null, "• " + vue.toDisplayString(_ctx.translate("CustomAlerts_PeriodDayDescription")), 1),
                vue.createElementVNode("li", null, "• " + vue.toDisplayString(_ctx.translate("CustomAlerts_PeriodWeekDescription")), 1),
                vue.createElementVNode("li", null, "• " + vue.toDisplayString(_ctx.translate("CustomAlerts_PeriodMonthDescription")), 1)
              ])
            ]),
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "select",
                name: "period",
                "inline-help": "#customAlertPeriodHelp",
                "model-value": _ctx.actualAlert.period,
                "onUpdate:modelValue": _cache[3] || (_cache[3] = ($event) => {
                  _ctx.actualAlert.period = $event;
                  _ctx.changeReport();
                }),
                title: _ctx.translate("General_Period"),
                options: _ctx.periodOptions
              }, null, 8, ["model-value", "title", "options"])
            ]),
            vue.createElementVNode("div", _hoisted_2$2, [
              vue.createVNode(_component_Field, {
                uicontrol: "multiselect",
                name: "report_mediums",
                id: "report_mediums",
                title: _ctx.translate("CustomAlerts_MediumTitle"),
                "inline-help": _ctx.$sanitize(_ctx.getDeliveryMediumInlineTooltip),
                options: _ctx.alertReportMediumOptions,
                "model-value": _ctx.actualAlert.report_mediums,
                "onUpdate:modelValue": _cache[4] || (_cache[4] = ($event) => {
                  _ctx.actualAlert.report_mediums = $event;
                })
              }, null, 8, ["title", "inline-help", "options", "model-value"])
            ]),
            _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes("email") ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_3$1, [
              vue.createElementVNode("div", null, [
                vue.createVNode(_component_Field, {
                  uicontrol: "checkbox",
                  name: "report_email_me",
                  modelValue: _ctx.actualAlert.email_me,
                  "onUpdate:modelValue": _cache[5] || (_cache[5] = ($event) => _ctx.actualAlert.email_me = $event),
                  introduction: _ctx.translate("ScheduledReports_SendReportTo"),
                  title: `${_ctx.translate("ScheduledReports_SentToMe")} (${_ctx.currentUserEmail})`
                }, null, 8, ["modelValue", "introduction", "title"])
              ]),
              vue.createElementVNode("div", null, [
                vue.createVNode(_component_Field, {
                  uicontrol: "textarea",
                  modelValue: _ctx.actualAlert.additional_emails,
                  "onUpdate:modelValue": _cache[6] || (_cache[6] = ($event) => _ctx.actualAlert.additional_emails = $event),
                  "var-type": "array",
                  title: _ctx.translate("ScheduledReports_AlsoSendReportToTheseEmails")
                }, null, 8, ["modelValue", "title"])
              ])
            ])) : vue.createCommentVNode("", true),
            _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes("mobile") ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_4, [
              _ctx.supportsSMS ? (vue.openBlock(), vue.createElementBlock("span", _hoisted_5, [
                vue.createVNode(_component_SelectPhoneNumbers, {
                  "phone-numbers": _ctx.phoneNumbers || [],
                  modelValue: _ctx.actualAlert.phone_numbers,
                  "onUpdate:modelValue": _cache[7] || (_cache[7] = ($event) => _ctx.actualAlert.phone_numbers = $event)
                }, null, 8, ["phone-numbers", "modelValue"])
              ])) : (vue.openBlock(), vue.createElementBlock("div", _hoisted_6, [
                vue.createElementVNode("div", _hoisted_7, [
                  vue.createVNode(_component_Alert, { severity: "info" }, {
                    default: vue.withCtx(() => [
                      vue.createElementVNode("strong", null, vue.toDisplayString(_ctx.translate("MobileMessaging_PhoneNumbers")), 1),
                      _cache[18] || (_cache[18] = vue.createTextVNode(": ", -1)),
                      vue.createElementVNode("span", {
                        innerHTML: _ctx.$sanitize(_ctx.mobileMessagingNotActivated)
                      }, null, 8, _hoisted_8)
                    ]),
                    _: 1
                  })
                ])
              ]))
            ])) : vue.createCommentVNode("", true),
            _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes("slack") ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_9, [
              vue.createVNode(_component_SelectSlackChannel, {
                "model-value": _ctx.actualAlert.slack_channel_id || "",
                "is-slack-oauth-token-added": _ctx.isSlackOauthTokenAdded,
                modelValue: _ctx.actualAlert.slack_channel_id,
                "onUpdate:modelValue": _cache[8] || (_cache[8] = ($event) => _ctx.actualAlert.slack_channel_id = $event)
              }, null, 8, ["model-value", "is-slack-oauth-token-added", "modelValue"])
            ])) : vue.createCommentVNode("", true),
            _ctx.actualAlert.report_mediums && _ctx.actualAlert.report_mediums.includes("teams") ? (vue.openBlock(), vue.createElementBlock("div", _hoisted_10, [
              vue.createVNode(_component_SelectMicrosoftTeamsWebhookUrl, {
                "is-required-fields-set": true,
                "model-value": _ctx.actualAlert.ms_teams_webhook_url || "",
                modelValue: _ctx.actualAlert.ms_teams_webhook_url,
                "onUpdate:modelValue": _cache[9] || (_cache[9] = ($event) => _ctx.actualAlert.ms_teams_webhook_url = $event)
              }, null, 8, ["model-value", "modelValue"])
            ])) : vue.createCommentVNode("", true),
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "expandable-select",
                name: "report",
                "model-value": _ctx.actualAlert.report,
                "onUpdate:modelValue": _cache[10] || (_cache[10] = ($event) => {
                  _ctx.actualAlert.report = $event;
                  _ctx.changeReport();
                }),
                options: _ctx.reportOptions,
                title: `${_ctx.translate("CustomAlerts_ThisAppliesTo")}: ${(_b = _ctx.actualReportMetadata) == null ? void 0 : _b.name}`,
                introduction: _ctx.translate("CustomAlerts_AlertCondition"),
                "inline-help": _ctx.thisAppliesToInlineHelp
              }, null, 8, ["model-value", "options", "title", "introduction", "inline-help"])
            ]),
            vue.withDirectives(vue.createElementVNode("div", _hoisted_11, [
              vue.createElementVNode("div", _hoisted_12, [
                vue.createVNode(_component_ActivityIndicator, { loading: _ctx.isLoadingReport }, null, 8, ["loading"])
              ])
            ], 512), [
              [vue.vShow, _ctx.isLoadingReport]
            ]),
            vue.withDirectives(vue.createElementVNode("div", _hoisted_13, [
              vue.createElementVNode("div", _hoisted_14, [
                vue.createElementVNode("div", null, [
                  vue.createVNode(_component_Field, {
                    uicontrol: "select",
                    name: "reportCondition",
                    modelValue: _ctx.actualAlert.report_condition,
                    "onUpdate:modelValue": _cache[11] || (_cache[11] = ($event) => _ctx.actualAlert.report_condition = $event),
                    "full-width": true,
                    title: _ctx.reportConditionTitle,
                    options: _ctx.alertGroupConditions
                  }, null, 8, ["modelValue", "title", "options"])
                ])
              ]),
              vue.createElementVNode("div", _hoisted_15, [
                vue.createElementVNode("div", _hoisted_16, [
                  vue.withDirectives(vue.createVNode(_component_Field, {
                    uicontrol: "text",
                    role: "textbox",
                    name: "reportValue",
                    modelValue: _ctx.actualAlert.report_matched,
                    "onUpdate:modelValue": _cache[12] || (_cache[12] = ($event) => _ctx.actualAlert.report_matched = $event),
                    "full-width": true,
                    autocomplete: "off",
                    maxlength: 255,
                    title: _ctx.translate("General_Value")
                  }, null, 8, ["modelValue", "title"]), [
                    [vue.vShow, _ctx.actualAlert.report_condition !== "matches_any"]
                  ])
                ], 512)
              ])
            ], 512), [
              [vue.vShow, _ctx.hasReportDimension]
            ]),
            vue.createElementVNode("div", null, [
              vue.createVNode(_component_Field, {
                uicontrol: "select",
                name: "metric",
                "model-value": _ctx.actualAlert.metric,
                "onUpdate:modelValue": _cache[13] || (_cache[13] = ($event) => _ctx.actualAlert.metric = $event),
                options: _ctx.metricOptions,
                introduction: _ctx.translate("CustomAlerts_AlertMeWhen")
              }, null, 8, ["model-value", "options", "introduction"])
            ]),
            vue.createElementVNode("div", _hoisted_17, [
              vue.createElementVNode("div", _hoisted_18, [
                vue.createElementVNode("div", null, [
                  vue.createVNode(_component_Field, {
                    uicontrol: "select",
                    name: "metricCondition",
                    "model-value": _ctx.actualAlert.metric_condition,
                    "onUpdate:modelValue": _cache[14] || (_cache[14] = ($event) => _ctx.actualAlert.metric_condition = $event),
                    "full-width": true,
                    options: _ctx.metricConditionOptions
                  }, null, 8, ["model-value", "options"])
                ])
              ]),
              vue.createElementVNode("div", _hoisted_19, [
                vue.createElementVNode("div", null, [
                  vue.createVNode(_component_Field, {
                    uicontrol: "text",
                    name: "metricValue",
                    class: vue.normalizeClass({ invalid: _ctx.isMetricValueInvalid }),
                    modelValue: _ctx.actualAlert.metric_matched,
                    "onUpdate:modelValue": _cache[15] || (_cache[15] = ($event) => _ctx.actualAlert.metric_matched = $event),
                    title: `<span>${_ctx.metricDescription}</span>`,
                    "full-width": true
                  }, null, 8, ["class", "modelValue", "title"])
                ])
              ])
            ]),
            (vue.openBlock(true), vue.createElementBlock(vue.Fragment, null, vue.renderList(_ctx.comparablesDates, (comparablesDatesPeriod, period) => {
              return vue.openBlock(), vue.createElementBlock("div", { key: period }, [
                vue.withDirectives(vue.createVNode(_component_Field, {
                  uicontrol: "select",
                  name: "compared_to",
                  modelValue: _ctx.comparedTo[period],
                  "onUpdate:modelValue": ($event) => _ctx.comparedTo[period] = $event,
                  disabled: Object.keys(comparablesDatesPeriod).length <= 1,
                  options: comparablesDatesPeriod,
                  introduction: _ctx.translate("CustomAlerts_ComparedToThe")
                }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled", "options", "introduction"]), [
                  [vue.vShow, period === _ctx.actualAlert.period && _ctx.isComparable]
                ])
              ]);
            }), 128)),
            ((_c = _ctx.actualAlert) == null ? void 0 : _c.idalert) ? (vue.openBlock(), vue.createBlock(_component_SaveButton, {
              key: 4,
              onClick: _cache[16] || (_cache[16] = ($event) => _ctx.updateAlert(_ctx.actualAlert.idalert)),
              saving: _ctx.isLoading
            }, null, 8, ["saving"])) : (vue.openBlock(), vue.createBlock(_component_SaveButton, {
              key: 5,
              onClick: _cache[17] || (_cache[17] = ($event) => _ctx.createAlert()),
              saving: _ctx.isLoading
            }, null, 8, ["saving"])),
            vue.createElementVNode("div", {
              class: "entityCancel",
              innerHTML: _ctx.$sanitize(_ctx.cancelLink)
            }, null, 8, _hoisted_20)
          ])), [
            [_directive_form]
          ])
        ];
      }),
      _: 1
    }, 8, ["content-title"]);
  }
  const EditAlert = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["render", _sfc_render$2]]);
  const _sfc_main$1 = vue.defineComponent({
    props: {},
    components: {
      ContentBlock: CoreHome.ContentBlock
    },
    computed: {
      customAlertsIndexLink() {
        return `?${CoreHome.MatomoUrl.stringify(__spreadProps(__spreadValues({}, CoreHome.MatomoUrl.urlParsed.value), {
          module: "CustomAlerts",
          action: "index"
        }))}`;
      }
    }
  });
  const _hoisted_1$1 = { class: "tableActionBar" };
  const _hoisted_2$1 = ["href"];
  function _sfc_render$1(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ContentBlock = vue.resolveComponent("ContentBlock");
    return vue.openBlock(), vue.createBlock(_component_ContentBlock, {
      class: "alerts",
      "content-title": _ctx.translate("CustomAlerts_AlertsHistory")
    }, {
      default: vue.withCtx(() => [
        vue.renderSlot(_ctx.$slots, "default"),
        vue.createElementVNode("div", _hoisted_1$1, [
          vue.createElementVNode("a", { href: _ctx.customAlertsIndexLink }, [
            _cache[0] || (_cache[0] = vue.createElementVNode("span", { class: "icon-table" }, null, -1)),
            vue.createTextVNode(" " + vue.toDisplayString(_ctx.translate("CustomAlerts_ManageAlerts")), 1)
          ], 8, _hoisted_2$1)
        ])
      ]),
      _: 3
    }, 8, ["content-title"]);
  }
  const HistoryTriggeredAlerts = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["render", _sfc_render$1]]);
  const _sfc_main = vue.defineComponent({
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
      ContentBlock: CoreHome.ContentBlock,
      ListAlerts
    }
  });
  const _hoisted_1 = {
    class: "ui-confirm",
    id: "confirm"
  };
  const _hoisted_2 = ["value"];
  const _hoisted_3 = ["value"];
  function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
    const _component_ListAlerts = vue.resolveComponent("ListAlerts");
    const _component_ContentBlock = vue.resolveComponent("ContentBlock");
    return vue.openBlock(), vue.createBlock(_component_ContentBlock, {
      class: "alerts",
      "content-title": _ctx.title
    }, {
      default: vue.withCtx(() => [
        vue.createElementVNode("p", null, vue.toDisplayString(_ctx.translate("CustomAlerts_ManageTooltip")), 1),
        vue.createVNode(_component_ListAlerts, { alerts: _ctx.alerts }, null, 8, ["alerts"]),
        vue.createElementVNode("div", _hoisted_1, [
          vue.createElementVNode("h2", null, vue.toDisplayString(_ctx.translate("CustomAlerts_AreYouSureDeleteAlert")), 1),
          vue.createElementVNode("input", {
            role: "yes",
            type: "button",
            value: _ctx.translate("General_Yes")
          }, null, 8, _hoisted_2),
          vue.createElementVNode("input", {
            role: "no",
            type: "button",
            value: _ctx.translate("General_No")
          }, null, 8, _hoisted_3)
        ])
      ]),
      _: 1
    }, 8, ["content-title"]);
  }
  const ListAlertsPage = /* @__PURE__ */ _export_sfc(_sfc_main, [["render", _sfc_render]]);
  exports2.EditAlert = EditAlert;
  exports2.HistoryTriggeredAlerts = HistoryTriggeredAlerts;
  exports2.ListAlerts = ListAlerts;
  exports2.ListAlertsPage = ListAlertsPage;
  Object.defineProperty(exports2, Symbol.toStringTag, { value: "Module" });
}));
