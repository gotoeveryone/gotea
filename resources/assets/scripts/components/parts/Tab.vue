<template>
  <ul class="tabs">
    <li
      v-for="(tab, idx) in tabs"
      :key="idx"
      :class="{ selectTab: isSelect(tab) }"
      class="tab"
      @click="select(tab)"
      v-text="tab"
    />
  </ul>
</template>

<script lang="ts">
import Vue, { PropType } from 'vue';

export default Vue.extend({
  props: {
    tabs: {
      type: Array as PropType<string[]>,
      default: () => [],
    },
  },
  data: () => {
    return {
      selectTab: '',
    };
  },
  mounted() {
    this.selectTab = this.tabs[0];
  },
  methods: {
    select(tab: string) {
      this.selectTab = tab;
    },
    isSelect(tab: string) {
      return this.selectTab === tab;
    },
  },
});
</script>
