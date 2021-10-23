<template>
  <div class="search-results">
    <ul class="table-header">
      <li class="table-row">
        <span class="table-column table-column_no">No.</span>
        <span class="table-column table-column_player">棋士名</span>
        <span class="table-column table-column_point">勝</span>
        <span class="table-column table-column_point">敗</span>
        <span class="table-column table-column_point">分</span>
        <span class="table-column table-column_percent">勝率</span>
      </li>
    </ul>
    <ul v-if="items.length" class="table-body">
      <li v-for="(item, idx) in items" :key="idx" class="table-row">
        <span class="table-column table-column_no">
          <span v-text="getRank(idx, item)" />
        </span>
        <span class="table-column table-column_player">
          <a
            :class="getSexClass(item)"
            class="view-link"
            @click="select(item)"
            v-text="item.name"
          />
        </span>
        <span class="table-column table-column_point" v-text="item.win" />
        <span class="table-column table-column_point" v-text="item.lose" />
        <span class="table-column table-column_point" v-text="item.draw" />
        <span class="table-column table-column_percent" v-text="item.percentage" />
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';

import { RankingResultItem } from '@/types/ranking';

export default defineComponent({
  props: {
    items: {
      type: Array as PropType<RankingResultItem[]>,
      default: () => [],
    },
  },
  methods: {
    getRank(_idx: number, _row: RankingResultItem) {
      if (this.items[_idx - 1]) {
        const beforeRank = this.items[_idx - 1].rank;
        return _row.rank === beforeRank ? '' : `${_row.rank}`;
      }
      return _row.rank;
    },
    getSexClass(_row: RankingResultItem) {
      return _row.sex === '女性' ? 'female' : 'male';
    },
    select(_row: RankingResultItem) {
      this.$store.dispatch('openModal', {
        url: _row.url,
      });
    },
  },
});
</script>
