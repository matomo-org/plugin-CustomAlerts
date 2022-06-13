<!--
  Matomo - free/libre analytics platform
  @link https://matomo.org
  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <ContentBlock
    class="alerts"
    :content-title="translate('CustomAlerts_AlertsHistory')"
  >
    <div v-html="$sanitize(alertsFormatted)"></div>

    <div class="tableActionBar">
      <a :href="customAlertsIndexLink">
        <span class="icon-table"></span> {{ translate('CustomAlerts_ManageAlerts') }}
      </a>
    </div>
  </ContentBlock>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { ContentBlock, MatomoUrl } from 'CoreHome';

export default defineComponent({
  props: {
    alertsFormatted: {
      type: String,
      required: true,
    },
  },
  components: {
    ContentBlock,
  },
  computed: {
    customAlertsIndexLink() {
      return `?${MatomoUrl.stringify({
        ...MatomoUrl.urlParsed.value,
        module: 'CustomAlerts',
        action: 'index',
      })}`;
    },
  },
});
</script>
