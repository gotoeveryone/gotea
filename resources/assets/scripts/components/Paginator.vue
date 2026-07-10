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

<script setup lang="ts">
import { computed, toRefs } from 'vue';

const props = defineProps({
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
});
const { currentPage, perPage, total } = toRefs(props);
const emit = defineEmits<{ 'change-page': [page: number] }>();
const lastPage = computed(() => Math.ceil(total.value / perPage.value));
const pageCount = computed(() => Math.min(lastPage.value, 9));
const isFirstPage = computed(() => currentPage.value === 1);
const isLastPage = computed(() => currentPage.value === lastPage.value);
const toPage = computed(() => {
  if (currentPage.value <= 5) {
    return pageCount.value;
  }
  if (currentPage.value + 4 <= lastPage.value) {
    return currentPage.value + 4;
  }
  return lastPage.value;
});
const fromPage = computed(() => {
  if (lastPage.value <= pageCount.value) {
    return 1;
  }
  if (currentPage.value <= 5) {
    return 1;
  }
  // 5ページを起点に足りない後ページ分前ページへ戻す
  // 現在ページはカウントしないため+1している
  return 5 - (5 - (toPage.value - pageCount.value + 1));
});
const pages = computed(() => {
  const pages: number[] = [];
  for (let p = fromPage.value; p <= toPage.value; p++) {
    pages.push(p);
  }
  return pages;
});
const onChangePage = (page: number): void => emit('change-page', page);
</script>
