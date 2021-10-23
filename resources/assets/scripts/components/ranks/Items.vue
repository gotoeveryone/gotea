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

<script lang="ts">
import Vue, { PropType } from 'vue';

import { RanksItem } from '@/types/ranks';

export default Vue.extend({
  props: {
    items: {
      type: Array as PropType<RanksItem[]>,
      default: () => [],
    },
  },
  methods: {
    count(_item: RanksItem) {
      return `${_item.count}人`;
    },
  },
});
</script>
