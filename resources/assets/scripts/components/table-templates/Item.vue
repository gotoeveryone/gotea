<template>
  <li class="table-row">
    <span class="table-column table-column_title" :title="item.title" v-text="item.title" />
    <span class="table-column table-column_created" v-text="created" />
    <span class="table-column table-column_modified" v-text="modified" />
    <span class="table-column table-column_actions">
      <a :href="editPageLink" class="layout-button button-secondary">編集</a>
      <form :name="formName" method="post" :action="editPageLink" style="display: none;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_csrfToken" autocomplete="off" :value="csrfToken">
      </form>
      <a href="#" :data-confirm-message="confirmMessage" :onclick="`if (confirm(this.dataset.confirmMessage)) { document.${formName}.submit(); } event.returnValue = false; return false;`" class="layout-button button-danger">削除</a>
    </span>
  </li>
</template>

<script lang="ts">
import dayjs from 'dayjs';
import { defineComponent } from 'vue';
import type { PropType } from 'vue';

import { TableTemplate as Item } from '@/types/table-template';

export default defineComponent({
  props: {
    item: {
      type: Object as PropType<Item>,
      required: true,
    },
    csrfToken: {
      type: String,
      default: '',
    },
  },
  computed: {
    created(): string {
      return dayjs(this.item.created).format('YYYY年MM月DD日 HH時mm分ss秒');
    },
    modified(): string {
      return dayjs(this.item.modified).format('YYYY年MM月DD日 HH時mm分ss秒');
    },
    editPageLink(): string {
      return `/table-templates/${this.item.id}`;
    },
    formName(): string {
      return `table_templates_${this.item.id}`;
    },
    confirmMessage(): string {
      return `ID ${this.item.id} を削除します。よろしいですか?`;
    },
  },
});
</script>
