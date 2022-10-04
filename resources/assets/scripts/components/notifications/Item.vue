<template>
  <li class="table-row">
    <span class="table-column table-column_title" :title="item.title" v-text="item.title" />
    <span class="table-column table-column_content" :title="item.content" v-text="item.content" />
    <span class="table-column table-column_status" v-text="item.status" />
    <span class="table-column table-column_permanent">
      <span v-if="item.is_permanent">✓</span>
    </span>
    <span class="table-column table-column_published" v-text="published" />
    <span class="table-column table-column_actions">
      <a :href="editPageLink" class="layout-button button-primary">編集</a>
      <a :href="copyPageLink" class="layout-button button-secondary">コピー</a>
      <form :name="formName" method="post" :action="editPageLink" style="display: none">
        <input type="hidden" name="_method" value="DELETE" />
        <input type="hidden" name="_csrfToken" autocomplete="off" :value="csrfToken" />
      </form>
      <a
        href="#"
        :data-confirm-message="confirmMessage"
        :onclick="`if (confirm(this.dataset.confirmMessage)) { document.${formName}.submit(); } event.returnValue = false; return false;`"
        class="layout-button button-danger"
        >削除</a
      >
    </span>
  </li>
</template>

<script setup lang="ts">
import dayjs from 'dayjs';
import { computed, defineProps } from 'vue';
import type { PropType } from 'vue';
import { Notification as Item } from '@/types/notification';

const props = defineProps({
  item: {
    type: Object as PropType<Item>,
    required: true,
  },
  csrfToken: {
    type: String,
    default: '',
  },
});

const published = computed(() => dayjs(props.item.published).format('YYYY-MM-DD HH:mm:ss'));
const editPageLink = computed(() => `/notifications/${props.item.id}`);
const copyPageLink = computed(() => `/notifications/new?from=${props.item.id}`);
const formName = computed(() => `post_${props.item.id}`);
const confirmMessage = computed(() => `ID ${props.item.id} を削除します。よろしいですか?`);
</script>
