<template>
  <div class="search-results">
    <ul class="table-header">
      <li class="table-row">
        <span class="table-column table-column_rank">段位</span>
        <span class="table-column table-column_count">人数</span>
      </li>
    </ul>
    <ul v-if="items.length" class="table-body">
      <li v-for="(item, idx) in items" :key="idx" class="table-row">
        <span class="table-column table-column_rank" v-text="item.name" />
        <span class="table-column table-column_count" v-text="count(item)" />
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { PropType, toRefs } from 'vue';

import { RanksItem } from '@/types/ranks';

const props = defineProps({
  items: {
    type: Array as PropType<RanksItem[]>,
    default: () => [],
  },
});
const { items } = toRefs(props);
const count = (_item: RanksItem) => `${_item.count}人`;
</script>
