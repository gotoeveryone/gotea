<template>
  <div class="table-templates index large-9 medium-8 columns content">
    <ul class="search-header">
      <li class="search-row">
        <div class="search-box search-box-right">
          <a href="/title-templates/new" class="layout-button button-secondary">新規登録</a>
        </div>
      </li>
    </ul>
    <paginator v-if="items.length" :current-page="currentPage" :per-page="perPage" :total="total" @change-page="onSearch" />
    <div class="search-results">
      <ul class="table-header">
        <li class="table-row">
          <span class="table-column table-column_title">タイトル</span>
          <span class="table-column table-column_created">作成日時</span>
          <span class="table-column table-column_modified"> 修正日時</span>
          <span class="table-column table-column_actions">操作</span>
        </li>
      </ul>
      <ul v-if="items.length" class="table-body">
        <template v-for="item in items">
          <list-item :key="item.id" :item="item" :csrf-token="csrfToken" />
        </template>
      </ul>
    </div>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import Paginator from '@/components/Paginator.vue';
import ListItem from '@/components/table-templates/Item.vue';
import {
  TableTemplate as Item,
  TableTemplateListResponse as Response,
} from '@/types/table-template';

export default Vue.extend({
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
  }),
  computed: {
    perPage(): number {
      return 20;
    },
  },
  mounted() {
    this.onSearch(1);
  },
  methods: {
    onSearch(page: number) {
      this.currentPage = page;
      return axios.get<Response>('/api/table-templates', { params: { page, limit: this.perPage } })
        .then(res => res.data)
        .then(({ response: { total, items } }) => {
          this.total = total;
          this.items = items;
        });
    },
  },
});
</script>
