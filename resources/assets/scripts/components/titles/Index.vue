<template>
  <section class="titles">
    <title-header :is-admin="isAdmin" @add="addRow" @search="onSearch" @json="outputJson" />
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
          <span class="table-column table-column_holding">期</span>
          <span class="table-column table-column_modified">修正日</span>
          <span class="table-column table-column_closed">終了<br>棋戦</span>
          <span class="table-column table-column_output">出力</span>
          <span class="table-column table-column_official">公式戦</span>
          <span class="table-column table-column_open-detail">詳細</span>
        </li>
      </ul>
      <ul v-if="items.length" class="table-body">
        <title-item
          v-for="(item, idx) in items"
          :key="idx"
          :item="item"
          :is-admin="isAdmin"
          @openModal="openWithCallback"
          @refresh="refresh"
        />
      </ul>
    </div>
  </section>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import axios from 'axios';

import Header from '@/components/titles/Header.vue';
import Item from '@/components/titles/Item.vue';
import { ModalOption } from '@/types';
import { TitleCondition, TitleResultItem } from '@/types/titles';

export default defineComponent({
  components: {
    titleHeader: Header,
    titleItem: Item,
  },
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
  },
  data: () => {
    return {
      params: {},
      items: [] as TitleResultItem[],
    };
  },
  methods: {
    onSearch(params: TitleCondition) {
      this.params = {
        country_id: params.countryId,
        search_non_output: params.searchNonOutput,
        search_closed: params.searchClosed,
      };
      this.refresh();
    },
    addRow(params: TitleCondition) {
      this.items.push({
        id: null,
        countryId: params.countryId,
        holding: 1,
        name: '',
        sortOrder: this.items.length + 1,
        winnerName: null,
        htmlFileName: '',
        htmlFileHolding: null,
        htmlFileModified: '',
        url: null,
        isClosed: false,
        isOutput: true,
        isOfficial: true,
      });
    },
    outputJson() {
      return axios
        .post('/api/titles/news')
        .then(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSONを出力しました。',
          }),
        )
        .catch(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSON出力に失敗しました…。',
            type: 'error',
          }),
        );
    },
    openWithCallback(options: ModalOption) {
      this.$store.dispatch(
        'openModal',
        Object.assign(options, {
          callback: () => this.refresh(),
        }),
      );
    },
    refresh() {
      return axios
        .get('/api/titles', { params: this.params })
        .then((res) => (this.items = res.data.response));
    },
  },
});
</script>
