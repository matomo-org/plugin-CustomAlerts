<!--
  Matomo - free/libre analytics platform
  @link https://matomo.org
  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <div>
    <table v-content-table>
      <thead>
        <tr>
          <th>{{ translate('General_Name') }}</th>
          <th>{{ translate('General_Website') }}</th>
          <th>{{ translate('General_Period') }}</th>
          <th>{{ translate('General_Report') }}</th>
          <th>{{ translate('General_Edit') }}</th>
          <th>{{ translate('General_Delete') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="!alerts?.length">
          <td colspan="6">
            <br />
            {{ translate('CustomAlerts_NoAlertsDefined') }}
            <br /><br />
          </td>
        </tr>
        <tr v-for="alert in alerts" :key="alert.idalert">
          <td class="name">{{ alert.name }}</td>
          <td class="site">{{ decode(alert.siteName) }}</td>
          <td class="period">{{ ucfirst(translate(`Intl_Period${ucfirst(alert.period)}`)) }}</td>
          <td class="reportName">{{ alert.reportName || '-' }}</td>
          <td class="edit">
            <a
              class="table-action"
              :href="linkTo({
                'module': 'CustomAlerts',
                'action': 'editAlert',
                'idAlert': alert.idalert,
              })"
              :title="translate('General_Edit')"
            ><span class="icon-edit" /></a>
          </td>
          <td class="delete">
            <button
              class="deleteAlert table-action"
              @click="deleteAlert(alert.idalert)"
              :id="alert.idalert"
              :title="translate('General_Delete')"
            >
              <span class="icon-delete" />
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="tableActionBar">
      <a :href="linkTo({'module': 'CustomAlerts', 'action': 'addNewAlert'})">
        <span class="icon-add"></span>
        {{ translate('CustomAlerts_CreateNewAlert') }}
      </a>
      <a :href="linkTo({'module': 'CustomAlerts', 'action': 'historyTriggeredAlerts'})">
        <span class="icon-table"></span> {{ translate('CustomAlerts_AlertsHistory') }}
      </a>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import {
  AjaxHelper,
  Matomo,
  ContentTable,
  MatomoUrl,
} from 'CoreHome';

export default defineComponent({
  props: {
    alerts: {
      type: Array,
      default() { return []; },
    },
  },
  directives: {
    ContentTable,
  },
  methods: {
    deleteAlert(idAlert: string|number) {
      Matomo.helper.modalConfirm('#confirm', {
        yes: () => {
          AjaxHelper.fetch({
            method: 'CustomAlerts.deleteAlert',
            idAlert,
          }).then(() => {
            Matomo.helper.redirect();
          });
        },
      });
    },
    ucfirst(s: string) {
      return `${s[0].toUpperCase()}${s.substr(1)}`;
    },
    linkTo(params: QueryParameters) {
      return `?${MatomoUrl.stringify({
        ...MatomoUrl.urlParsed.value,
        ...params,
      })}`;
    },
    decode(s: string) {
      return Matomo.helper.htmlDecode(s);
    },
  },
});
</script>
