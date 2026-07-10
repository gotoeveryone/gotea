<template>
  <div class="notifications index large-9 medium-8 columns content">
    <ul class="search-header">
      <li class="search-row">
        <div class="search-box search-box-right">
          <a href="/notifications/new" class="layout-button button-secondary">新規登録</a>
        </div>
      </li>
    </ul>
    <paginator
      v-if="items.length"
      :current-page="currentPage"
      :per-page="perPage"
      :total="total"
      @change-page="onSearch"
    />
    <div class="search-results">
      <ul class="table-header">
        <li class="table-row">
          <span class="table-column table-column_title">タイトル</span>
          <span class="table-column table-column_content">本文</span>
          <span class="table-column table-column_status">状態</span>
          <span class="table-column table-column_permanent">常時表示</span>
          <span class="table-column table-column_published">公開日時</span>
          <span class="table-column table-column_actions">操作</span>
        </li>
      </ul>
      <ul v-if="items.length" class="table-body">
        <list-item v-for="item in items" :key="item.id" :item="item" :csrf-token="csrfToken" />
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { onMounted, onBeforeUnmount, ref, toRefs } from 'vue';

import Paginator from '@/components/Paginator.vue';
import ListItem from '@/components/notifications/Item.vue';
import { Notification as Item, NotificationListResponse as Response } from '@/types/notification';
import {
  listenPaginationPopState,
  pushPaginationState,
  readPaginationFromUrl,
} from '@/libs/pagination-url';

const props = defineProps({
  csrfToken: {
    type: String,
    default: '',
  },
});
const { csrfToken } = toRefs(props);
const total = ref(0);
const items = ref<Item[]>([]);
const currentPage = ref(1);
const perPage = ref(30);
let cleanupPopStateListener: null | (() => void) = null;
const onSearch = (page: number) => {
  currentPage.value = page;
  return axios
    .get<Response>('/api/notifications', { params: { page, limit: perPage.value } })
    .then((res) => res.data)
    .then(({ response: result }) => {
      total.value = result.total;
      items.value = result.items;
      pushPaginationState({ page: currentPage.value, limit: perPage.value });
    });
};
onMounted(() => {
  const { page, limit } = readPaginationFromUrl(perPage.value);
  perPage.value = limit;
  currentPage.value = page;
  cleanupPopStateListener = listenPaginationPopState(perPage.value, ({ page, limit }) => {
    if (currentPage.value === page && perPage.value === limit) {
      return;
    }
    perPage.value = limit;
    onSearch(page);
  });
  onSearch(currentPage.value);
});
onBeforeUnmount(() => {
  cleanupPopStateListener?.();
  cleanupPopStateListener = null;
});
</script>
