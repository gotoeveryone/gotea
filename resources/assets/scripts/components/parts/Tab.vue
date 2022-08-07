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

<script lang="ts" setup>
import { defineProps, onMounted } from 'vue';
import type { PropType } from 'vue';

let selectTab = '';

const props = defineProps({
  tabs: {
    type: Array as PropType<string[]>,
    default: () => [],
  },
});

onMounted(() => {
  selectTab = props.tabs[0];
});

const select = (tab: string) => {
  selectTab = tab;
};
const isSelect = (tab: string) => selectTab === tab;
</script>
