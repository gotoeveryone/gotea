<template>
  <div class="pagination">
    <ul class="pagination-item">
      <li class="result-count"><span v-text="total" />件のレコードが該当しました。</li>
    </ul>
    <ul class="pagination-item pager">
      <li class="pager-item prev" :class="{ disabled: isFirstPage }">
        <span v-if="isFirstPage" class="pager-item-link">&lt;</span>
        <a v-else class="pager-item-link" @click="onChangePage(currentPage - 1)">&lt;</a>
      </li>
      <li
        v-for="page in pages"
        :key="page"
        class="pager-item"
        :class="{ active: currentPage === page }"
      >
        <a class="pager-item-link" @click="onChangePage(page)" v-text="page" />
      </li>
      <li class="pager-item next" :class="{ disabled: isLastPage }">
        <span v-if="isLastPage" class="pager-item-link">&gt;</span>
        <a v-else class="pager-item-link" @click="onChangePage(currentPage + 1)">&gt;</a>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  props: {
    currentPage: {
      type: Number,
      default: 1,
    },
    perPage: {
      type: Number,
      default: 20,
    },
    total: {
      type: Number,
      default: 1,
    },
  },
  computed: {
    isFirstPage(): boolean {
      return this.currentPage === 1;
    },
    isLastPage(): boolean {
      return this.currentPage === this.lastPage;
    },
    pageCount(): number {
      return Math.min(this.lastPage, 9);
    },
    lastPage(): number {
      return Math.ceil(this.total / this.perPage);
    },
    fromPage(): number {
      if (this.lastPage <= this.pageCount) {
        return 1;
      }
      if (this.currentPage <= 5) {
        return 1;
      }
      // 5ページを起点に足りない後ページ分前ページへ戻す
      // 現在ページはカウントしないため+1している
      return 5 - (5 - (this.toPage - this.pageCount + 1));
    },
    toPage(): number {
      if (this.currentPage <= 5) {
        return this.pageCount;
      }
      if (this.currentPage + 4 <= this.lastPage) {
        return this.currentPage + 4;
      }
      return this.lastPage;
    },
    pages(): number[] {
      const pages = [];
      for (let p = this.fromPage; p <= this.toPage; p++) {
        pages.push(p);
      }
      return pages;
    },
  },
  methods: {
    onChangePage(page: number): void {
      this.$emit('change-page', page);
    },
  },
});
</script>
