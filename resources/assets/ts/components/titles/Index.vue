<template>
  <section class="titles">
    <title-header @add="addRow" @search="onSearch" @json="outputJson" />
    <div class="search-results">
      <ul class="table-header">
        <li class="table-row">
          <span class="table-column table-column_name">タイトル名</span>
          <span class="table-column table-column_name">タイトル名（英語）</span>
          <span class="table-column table-column_holding">期</span>
          <span class="table-column table-column_winner">優勝者</span>
          <span class="table-column table-column_order">並び順</span>
          <span class="table-column table-column_team">団体</span>
          <span class="table-column table-column_filename">ファイル名</span>
          <span class="table-column table-column_modified">修正日</span>
          <span class="table-column table-column_closed">終了<br>棋戦</span>
          <span class="table-column table-column_open-detail">詳細</span>
        </li>
      </ul>
      <ul v-if="items.length" class="table-body">
        <title-item
          v-for="(item, idx) in items"
          :key="idx"
          :item="item"
          @openModal="openWithCallback"
        />
      </ul>
    </div>
  </section>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import Header from '@/components/titles/Header.vue';
import Item from '@/components/titles/Item.vue';
import { ModalOption } from '@/types';
import { TitleCondition, TitleResultItem } from '@/types/titles';

export default Vue.extend({
  components: {
    titleHeader: Header,
    titleItem: Item,
  },
  data: () => {
    return {
      params: {},
      items: [] as TitleResultItem[],
    };
  },
  methods: {
    onSearch(params: TitleCondition) {
      this.params = params;
      axios
        .get('/api/titles', { params: this.params })
        .then(res => (this.items = res.data.response));
    },
    addRow(_params: TitleCondition) {
      this.items.push({
        id: null,
        countryId: _params.countryId,
        holding: 1,
        name: '',
        sortOrder: this.items.length + 1,
        winnerName: null,
        htmlFileModified: '',
        url: null,
        isClosed: false,
      });
    },
    outputJson() {
      axios
        .post('/api/titles/news')
        .then(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSONを出力しました。',
          })
        )
        .catch(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSON出力に失敗しました…。',
            type: 'error',
          })
        );
    },
    openWithCallback(options: ModalOption) {
      this.$store.dispatch(
        'openModal',
        Object.assign(options, {
          callback: () =>
            axios
              .get('/api/titles', { params: this.params })
              .then(res => (this.items = res.data.response)),
        })
      );
    },
  },
});
</script>
