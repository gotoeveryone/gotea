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

<script lang="ts">
import axios from 'axios';
import { defineComponent } from 'vue';

import Paginator from '@/components/Paginator.vue';
import ListItem from '@/components/notifications/Item.vue';
import { Notification as Item, NotificationListResponse as Response } from '@/types/notification';
import { isPositiveNumber } from '@/libs/validator';

export default defineComponent({
  components: {
    Paginator,
    ListItem,
  },
  props: {
    csrfToken: {
      type: String,
      default: '',
    },
  },
  data: () => ({
    total: 0,
    items: [] as Item[],
    currentPage: 1,
    perPage: 30,
  }),
  mounted() {
    const url = new URL(location.href);
    const limit = url.searchParams.get('limit');
    const page = url.searchParams.get('page');
    if (isPositiveNumber(limit)) {
      this.perPage = Number.parseInt(limit as string, 10);
    }
    if (isPositiveNumber(page)) {
      this.currentPage = Number.parseInt(page as string, 10);
    }
    this.onSearch(this.currentPage);
  },
  methods: {
    onSearch(page: number) {
      this.currentPage = page;
      return axios
        .get<Response>('/api/notifications', { params: { page, limit: this.perPage } })
        .then((res) => res.data)
        .then(({ response: { total, items } }) => {
          this.total = total;
          this.items = items;
          const url = new URL(location.href);
          url.searchParams.set('limit', this.perPage.toString());
          url.searchParams.set('page', this.currentPage.toString());
          if (url.toString() !== location.href) {
            history.replaceState({ page: this.currentPage, limit: this.perPage }, '', url);
          }
        });
    },
  },
});
</script>
