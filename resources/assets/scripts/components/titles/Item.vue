<template>
  <li :class="rowClass" class="table-row">
    <span class="table-column table-column_name">
      <input v-model="localItem.name" :disabled="!isAdmin" type="text" @change="save" />
    </span>
    <span class="table-column table-column_name">
      <input v-model="localItem.nameEnglish" :disabled="!isAdmin" type="text" @change="save" />
    </span>
    <span class="table-column table-column_holding">
      <input
        v-model="localItem.holding"
        :disabled="!isAdmin"
        type="text"
        class="table-column_holding-input"
        @change="save"
      />
    </span>
    <span class="table-column table-column_winner" v-text="winnerName" />
    <span class="table-column table-column_order">
      <input
        v-model="localItem.sortOrder"
        :disabled="!isAdmin"
        type="text"
        class="table-column_order-input"
        @change="save"
      />
    </span>
    <span class="table-column table-column_team">
      <input v-model="localItem.isTeam" :disabled="!isAdmin" type="checkbox" @change="save" />
    </span>
    <span class="table-column table-column_filename">
      <input v-model="localItem.htmlFileName" :disabled="!isAdmin" type="text" @change="save" />
    </span>
    <span class="table-column table-column_holding">
      <input
        v-model="localItem.htmlFileHolding"
        :disabled="!isAdmin"
        type="text"
        class="table-column_holding-input"
        @change="save"
      />
    </span>
    <span class="table-column table-column_modified">
      <input
        v-model="localItem.htmlFileModified"
        :disabled="!isAdmin"
        type="date"
        class="table-column_modified-input"
        autocomplete="off"
        @change="save"
      />
    </span>
    <span class="table-column table-column_closed">
      <input
        v-model="localItem.isClosed"
        :disabled="!isAdmin || !isSaved"
        type="checkbox"
        @change="save"
      />
    </span>
    <span class="table-column table-column_output">
      <input v-model="localItem.isOutput" :disabled="!isAdmin" type="checkbox" @change="save" />
    </span>
    <span class="table-column table-column_official">
      <input v-model="localItem.isOfficial" :disabled="!isAdmin" type="checkbox" @change="save" />
    </span>
    <span class="table-column table-column_open-detail">
      <a class="view-link" @click="select()" v-text="label" />
    </span>
  </li>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import axios from 'axios';

import { TitleResultItem } from '@/types/titles';

export default defineComponent({
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
    item: {
      type: Object as PropType<TitleResultItem>,
      required: true,
    },
  },
  data: () => {
    return {
      localItem: {} as TitleResultItem,
    };
  },
  computed: {
    winnerName(): string {
      return this.localItem.winnerName || '';
    },
    rowClass(): string {
      return this.localItem.isClosed ? 'table-row-closed' : '';
    },
    label(): string {
      return this.isSaved ? '開く' : '登録';
    },
    isSaved(): boolean {
      return this.localItem.id !== null && this.localItem.id !== undefined;
    },
  },
  watch: {
    item(newVal) {
      this.localItem = newVal;
    },
  },
  mounted() {
    this.localItem = this.item;
  },
  methods: {
    save() {
      // 未登録なら何もしない
      if (!this.isSaved) {
        return;
      }
      // 更新処理
      return axios.put(`/api/titles/${this.localItem.id}`, this.item).catch((res) => {
        const message = res.data.response.message;
        this.$store.dispatch('openDialog', {
          messages: message || '更新に失敗しました…。',
          type: 'error',
        });
      });
    },
    select() {
      if (!this.isSaved) {
        this.add();
      } else {
        this.$emit('openModal', {
          url: this.localItem.url,
        });
      }
    },
    add() {
      // 登録処理
      return axios
        .post('/api/titles/', this.item)
        .then(() => {
          this.$emit('refresh');
          this.$store.dispatch('openDialog', {
            messages: `タイトル【${this.localItem.name}】を登録しました。`,
          });
        })
        .catch((res) => {
          const message = res.data.response.message;
          this.$store.dispatch('openDialog', {
            messages: message || '登録に失敗しました…。',
            type: 'error',
          });
        });
    },
  },
});
</script>
