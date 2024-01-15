<!--
  Matomo - free/libre analytics platform
  @link https://matomo.org
  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <ContentBlock
    class="alerts"
    :content-title="headline"
  >
    <div v-form>
      <div>
        <Field
          uicontrol="text"
          name="alertName"
          v-model="actualAlert.name"
          :maxlength="100"
          :title="translate('CustomAlerts_AlertName')"
        >
        </Field>
      </div>
      <div>
        <Field
          uicontrol="site"
          name="idSite"
          :model-value="{ id: actualAlert.id_sites?.[0], name: actualCurrentSite.name }"
          @update:model-value="actualAlert.id_sites = [$event.id]; actualCurrentSite = $event;
            changeReport()"
          :title="translate('General_Website')"
          :introduction="translate('CustomAlerts_ApplyTo')"
        >
        </Field>
      </div>
      <div
        id="customAlertPeriodHelp"
        class="inline-help-node"
      >
        {{ translate('CustomAlerts_YouCanChoosePeriodFrom') }}:
        <ul>
          <li>&bull; {{ translate('CustomAlerts_PeriodDayDescription') }}</li>
          <li>&bull; {{ translate('CustomAlerts_PeriodWeekDescription') }}</li>
          <li>&bull; {{ translate('CustomAlerts_PeriodMonthDescription') }}</li>
        </ul>
      </div>
      <div>
        <Field
          uicontrol="select"
          name="period"
          inline-help="#customAlertPeriodHelp"
          :model-value="actualAlert.period"
          @update:model-value="actualAlert.period = $event; changeReport()"
          :title="translate('General_Period')"
          :options="periodOptions"
        >
        </Field>
      </div>
      <div>
        <Field
          uicontrol="checkbox"
          name="report_email_me"
          v-model="actualAlert.email_me"
          :introduction="translate('ScheduledReports_SendReportTo')"
          :title="`${translate('ScheduledReports_SentToMe')} (${currentUserEmail})`"
        >
        </Field>
      </div>
      <div>
        <Field
          uicontrol="textarea"
          v-model="actualAlert.additional_emails"
          var-type="array"
          :title="translate('ScheduledReports_AlsoSendReportToTheseEmails')"
        >
        </Field>
      </div>
      <span v-if="supportsSMS">
        <SelectPhoneNumbers
          :phone-numbers="phoneNumbers || []"
          v-model="actualAlert.phone_numbers"
        />
      </span>
      <div class="row" v-else>
        <div class="col s12">
          <Alert severity="info">
            <strong>{{ translate('MobileMessaging_PhoneNumbers') }}</strong>:
            <span v-html="$sanitize(mobileMessagingNotActivated)"></span>
          </Alert>
        </div>
      </div>
      <div>
        <Field
          uicontrol="expandable-select"
          name="report"
          :model-value="actualAlert.report"
          @update:model-value="actualAlert.report = $event; changeReport()"
          :options="reportOptions"
          :title="`${translate('CustomAlerts_ThisAppliesTo')}: ${actualReportMetadata?.name}`"
          :introduction="translate('CustomAlerts_AlertCondition')"
          :inline-help="thisAppliesToInlineHelp"
        >
        </Field>
      </div>
      <div
        class="row"
        v-show="isLoadingReport"
      >
        <div class="col s12">
          <ActivityIndicator :loading="isLoadingReport" />
        </div>
      </div>
      <div
        class="row conditionAndValue"
        v-show="hasReportDimension"
      >
        <div class="col s12 m6">
          <div>
            <Field
              uicontrol="select"
              name="reportCondition"
              v-model="actualAlert.report_condition"
              :full-width="true"
              :title="reportConditionTitle"
              :options="alertGroupConditions"
            />
          </div>
        </div>
        <div class="col s12 m6">
          <div class="ui-autocomplete-input" ref="reportValue">
            <Field
              uicontrol="text"
              role="textbox"
              name="reportValue"
              v-show="actualAlert.report_condition !== 'matches_any'"
              v-model="actualAlert.report_matched"
              :full-width="true"
              :autocomplete="'off'"
              :maxlength="255"
              :title="translate('General_Value')"
            >
            </Field>
          </div>
        </div>
      </div>
      <div>
        <Field
          uicontrol="select"
          name="metric"
          :model-value="actualAlert.metric"
          @update:model-value="actualAlert.metric = $event"
          :options="metricOptions"
          :introduction="translate('CustomAlerts_AlertMeWhen')"
        >
        </Field>
      </div>
      <div class="row conditionAndValue">
        <div class="col s12 m6">
          <div>
            <Field
              uicontrol="select"
              name="metricCondition"
              :model-value="actualAlert.metric_condition"
              @update:model-value="actualAlert.metric_condition = $event"
              :full-width="true"
              :options="metricConditionOptions"
            >
            </Field>
          </div>
        </div>
        <div class="col s12 m6">
          <div>
            <Field
              uicontrol="text"
              name="metricValue"
              :class="{ invalid: isMetricValueInvalid }"
              v-model="actualAlert.metric_matched"
              :title="`<span>${metricDescription}</span>`"
              :full-width="true"
            >
            </Field>
          </div>
        </div>
      </div>
      <div v-for="(comparablesDatesPeriod, period) in comparablesDates" :key="period">
        <Field
          uicontrol="select"
          name="compared_to"
          v-show="period === actualAlert.period && isComparable"
          v-model="comparedTo[period]"
          :disabled="Object.keys(comparablesDatesPeriod).length <= 1"
          :options="comparablesDatesPeriod"
          :introduction="translate('CustomAlerts_ComparedToThe')"
        >
        </Field>
      </div>
      <SaveButton
        v-if="actualAlert?.idalert"
        @click="updateAlert(actualAlert.idalert)"
        :saving="isLoading"
      />
      <SaveButton
        v-else
        @click="createAlert()"
        :saving="isLoading"
      />
      <div class="entityCancel" v-html="$sanitize(cancelLink)">
      </div>
    </div>
  </ContentBlock>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  translate,
  NotificationsStore,
  AjaxHelper,
  Matomo,
  Alert,
  ActivityIndicator,
  MatomoUrl,
  SiteRef,
  useExternalPluginComponent,
  ContentBlock,
} from 'CoreHome';
import { Form, Field, SaveButton } from 'CorePluginsAdmin';
import { Alert as AlertType } from '../types';

const SelectPhoneNumbers = useExternalPluginComponent('MobileMessaging', 'SelectPhoneNumbers');

interface Option {
  key: string;
  value: string;
  group?: string;
}

interface ReportMetadata {
  uniqueId: string;
  name: string;
  category: string;
  dimension?: string;
  metrics: Record<string, string>;
  module: string;
  action: string;
}

interface ProcessedReport {
  reportData: unknown[];
}

interface EditAlertState {
  isLoading: boolean;
  isLoadingReport: boolean;
  showReportConditionField: boolean;
  reportOptions: Option[];
  actualReportMetadata: ReportMetadata|null;
  reportValuesAutoComplete: unknown[]|null;
  actualAlert: AlertType;
  comparedTo: Record<string, string>;
  actualCurrentSite: SiteRef;
}

function isBlockedReportApiMethod(apiMethodUniqueId: string) {
  return apiMethodUniqueId === 'MultiSites_getOne' || apiMethodUniqueId === 'MultiSites_getAll';
}

const { $ } = window;

export default defineComponent({
  props: {
    alert: Object,
    headline: {
      type: String,
      required: true,
    },
    currentSite: {
      type: Object,
      required: true,
    },
    periodOptions: {
      type: Array,
      required: true,
    },
    currentUserEmail: {
      type: String,
      required: true,
    },
    supportsSMS: Boolean,
    phoneNumbers: [Array, Object],
    reportMetadata: Object,
    alertGroupConditions: {
      type: Array,
      required: true,
    },
    metricConditionOptions: {
      type: Array,
      required: true,
    },
    comparablesDates: {
      type: Object,
      required: true,
    },
  },
  components: {
    Field,
    Alert,
    ActivityIndicator,
    SaveButton,
    SelectPhoneNumbers,
    ContentBlock,
  },
  directives: {
    Form,
  },
  data(): EditAlertState {
    const currentSite = this.currentSite as SiteRef;
    const alert = this.alert as AlertType;
    const reportMetadata = this.reportMetadata as ReportMetadata;

    // set comparedTo for each comparison (defaulting to first available value)
    const comparedTo: Record<string, string> = Object.fromEntries(
      Object.entries(this.comparablesDates).map(([period, dates]) => [period, dates?.[0]?.key]),
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
      actualAlert: alert ? { ...alert } : {
        period: 'day',
        id_sites: [currentSite?.id || Matomo.idSite],
      } as unknown as AlertType,
      comparedTo,
      actualCurrentSite: {
        id: currentSite.id,
        // in PHP, currentSite's name is the value in the DB, which is encoded
        name: Matomo.helper.htmlDecode(currentSite.name),
      },
    };
  },
  watch: {
    actualReportMetadata() {
      const metrics = this.actualReportMetadata?.metrics;
      if (!metrics) {
        return;
      }

      if (!this.actualAlert.metric || !metrics[this.actualAlert.metric]) {
        [this.actualAlert.metric] = Object.keys(metrics);
      }
    },
    isMetricValueInvalid(newValue: boolean) {
      if (!newValue) {
        return;
      }

      const notificationInstanceId = NotificationsStore.show({
        message: translate('CustomAlerts_InvalidMetricValue'),
        id: 'CustomAlertsMetricValueError',
        context: 'error',
        type: 'toast',
      });
      NotificationsStore.scrollToNotification(notificationInstanceId);
    },
  },
  created() {
    this.changeReport();

    setTimeout(() => {
      $(this.$refs.reportValue as HTMLInputElement).find('input').autocomplete({
        source: this.getValuesForReportAndMetric.bind(this),
        minLength: 1,
        delay: 300,
      });
    }, 1000);
  },
  methods: {
    renderForm(data: ReportMetadata[]) {
      const options: Option[] = [];
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
          group: reportMetadata.category,
        });

        if (reportApiMethod === this.actualAlert.report) {
          this.actualReportMetadata = reportMetadata;
        }
      });

      this.reportOptions = options;
    },
    sendApiRequest(method: string, postParams: QueryParameters) {
      this.isLoading = true;

      const { period } = this.actualAlert;
      AjaxHelper.post(
        {
          period,
          method,
        },
        postParams,
      ).then(() => {
        Matomo.helper.redirect({
          module: 'CustomAlerts',
          action: 'index',
        });
      }).finally(() => {
        this.isLoading = false;
      });
    },
    getValuesForReportAndMetric(request: { term: string }, response: (v: unknown) => void) {
      const { metric } = this.actualAlert;

      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      function sendFeedback(values: any[]) {
        const matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
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

      AjaxHelper.fetch<ProcessedReport>({
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
        idSite: this.actualAlert.id_sites?.[0],
        format: 'JSON',
      }).then((data) => {
        if (data?.reportData) {
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
      this.isLoadingReport = true;
      this.reportValuesAutoComplete = null;
      AjaxHelper.fetch({
        method: 'API.getReportMetadata',
        date: Matomo.currentDateString,
        period: this.actualAlert.period,
        idSite: this.actualAlert.id_sites?.[0],
        filter_limit: '-1',
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

      this.sendApiRequest('CustomAlerts.addAlert', this.apiParameters);
      return true;
    },
    updateAlert() {
      if (this.isMetricValueInvalid) {
        return false;
      }

      this.sendApiRequest('CustomAlerts.editAlert', this.apiParameters);
      return true;
    },
  },
  computed: {
    apiParameters(): QueryParameters {
      return {
        idAlert: this.actualAlert.idalert,
        format: 'json',
        name: this.actualAlert.name,
        metric: this.actualAlert.metric,
        metricCondition: this.actualAlert.metric_condition,
        metricValue: this.actualAlert.metric_matched,
        emailMe: this.actualAlert.email_me ? 1 : 0,
        additionalEmails: this.actualAlert.additional_emails?.length
          ? this.actualAlert.additional_emails : [''],
        phoneNumbers: this.actualAlert.phone_numbers?.length
          ? this.actualAlert.phone_numbers : [''],
        reportUniqueId: this.actualAlert.report,
        reportCondition: this.actualAlert.report_condition,
        reportValue: this.actualAlert.report_matched,
        idSites: this.actualAlert.id_sites,
        comparedTo: this.comparedTo[this.actualAlert.period],
      };
    },
    isMetricValueInvalid(): boolean {
      return !$.isNumeric(this.actualAlert.metric_matched);
    },
    mobileMessagingNotActivated(): string {
      const link = `?${MatomoUrl.stringify({
        ...MatomoUrl.urlParsed.value,
        module: 'CorePluginsAdmin',
        action: 'plugins',
        updated: null,
      })}`;
      return translate(
        'CustomAlerts_MobileMessagingPluginNotActivated',
        `<a href="${link}#MobileMessaging">`,
        '</a>',
      );
    },
    cancelLink(): string {
      const backlink = `?${MatomoUrl.stringify({
        ...MatomoUrl.urlParsed.value,
        module: 'CustomAlerts',
        action: 'index',
      })}`;
      return translate(
        'General_OrCancel',
        `<a class="entityCancelLink" href="${backlink}">`,
        '</a>',
      );
    },
    thisAppliesToInlineHelp(): string {
      const link1 = 'https://matomo.org/guide/manage-matomo/custom-alerts/';
      const link2 = 'https://matomo.org/faq/general/examples-of-custom-alerts#events';
      return translate(
        'CustomAlerts_ThisAppliesToHelp',
        `<a target="_blank" href="${link1}" rel="noreferrer noopener">`,
        '</a>',
        '<strong>',
        '</strong>',
        `<a target="_blank" href="${link2}" rel="noreferrer noopener">`,
        '</a>',
      );
    },
    metricOptions(): Option[] {
      return Object.entries(this.actualReportMetadata?.metrics || {}).map(([key, value]) => ({
        key,
        value,
      }));
    },
    hasReportDimension(): boolean {
      return !!this.actualReportMetadata?.dimension;
    },
    reportConditionTitle(): string {
      const dim = this.actualReportMetadata?.dimension;
      return `${translate('CustomAlerts_When')} <span>${dim}</span>`;
    },
    isComparable(): boolean {
      const condition = this.actualAlert.metric_condition;
      return !!condition && condition.indexOf('_more_than') !== -1;
    },
    metricDescription(): string {
      const condition = this.actualAlert.metric_condition;
      const { metric } = this.actualAlert;

      const isPercentageCondition = condition && condition.indexOf('percentage_') === 0;
      const isPercentageMetric = metric && metric.indexOf('_rate') !== -1;
      const isSecondsMetric = metric && metric.indexOf('_time_') !== -1;

      if (isPercentageCondition || isPercentageMetric) {
        return '%';
      }

      if (isSecondsMetric) {
        return 's';
      }

      return translate('General_Value');
    },
  },
});
</script>
