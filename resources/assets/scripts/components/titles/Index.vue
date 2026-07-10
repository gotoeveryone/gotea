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
          <span class="table-column table-column_closed">終了<br />棋戦</span>
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

<script setup lang="ts">
import { ref, toRefs } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

import TitleHeader from '@/components/titles/Header.vue';
import TitleItem from '@/components/titles/Item.vue';
import { ModalOption } from '@/types';
import { TitleCondition, TitleResultItem } from '@/types/titles';

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false,
  },
});
const { isAdmin } = toRefs(props);
const store = useStore();
const params = ref<Record<string, string | number>>({});
const items = ref<TitleResultItem[]>([]);
const onSearch = (condition: TitleCondition) => {
  params.value = {
    country_id: condition.countryId,
    search_non_output: condition.searchNonOutput,
    search_closed: condition.searchClosed,
  };
  refresh();
};
const addRow = (params: TitleCondition) => {
  items.value.push({
    id: null,
    countryId: params.countryId,
    holding: 1,
    name: '',
    sortOrder: items.value.length + 1,
    winnerName: null,
    htmlFileName: '',
    htmlFileHolding: null,
    htmlFileModified: '',
    url: null,
    isClosed: false,
    isOutput: true,
    isOfficial: true,
  });
};
const outputJson = () => {
  return axios
    .post('/api/titles/news')
    .then(() =>
      store.dispatch('openDialog', {
        messages: 'JSONを出力しました。',
      }),
    )
    .catch(() =>
      store.dispatch('openDialog', {
        messages: 'JSON出力に失敗しました…。',
        type: 'error',
      }),
    );
};
const openWithCallback = (options: ModalOption) => {
  store.dispatch(
    'openModal',
    Object.assign(options, {
      callback: () => refresh(),
    }),
  );
};
const refresh = () => {
  return axios
    .get('/api/titles', { params: params.value })
    .then((res) => (items.value = res.data.response));
};
</script>
