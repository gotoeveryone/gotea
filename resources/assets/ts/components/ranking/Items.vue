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
            @click="select(item)"
            v-text="item.name"
            class="view-link"
          />
        </span>
        <span v-text="item.win" class="table-column table-column_point" />
        <span v-text="item.lose" class="table-column table-column_point" />
        <span v-text="item.draw" class="table-column table-column_point" />
        <span v-text="item.percentage" class="table-column table-column_percent" />
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

import { Prop } from '@/types';
import { RankingResultItem } from '@/types/ranking';

export default Vue.extend({
  props: {
    items: {
      type: Array as Prop<RankingResultItem[]>,
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
