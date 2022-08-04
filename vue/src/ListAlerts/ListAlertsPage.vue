<!--
  Matomo - free/libre analytics platform
  @link https://matomo.org
  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
-->

<template>
  <ContentBlock
    class="alerts"
    :content-title="title"
  >
    <ListAlerts :alerts="alerts"/>

    <div class="ui-confirm" id="confirm">
      <h2>{{ translate('CustomAlerts_AreYouSureDeleteAlert') }}</h2>
      <input role="yes" type="button" :value="translate('General_Yes')"/>
      <input role="no" type="button" :value="translate('General_No')"/>
    </div>
  </ContentBlock>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { ContentBlock } from 'CoreHome';
import ListAlerts from './ListAlerts.vue';

export default defineComponent({
  props: {
    title: {
      type: String,
      required: true,
    },
    alerts: {
      type: Array,
      default() { return []; },
    },
  },
  components: {
    ContentBlock,
    ListAlerts,
  },
});
</script>
