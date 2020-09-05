<template>
  <li :class="rowClass" class="table-row">
    <span class="table-column table-column_name">
      <input :disabled="!isAdmin" @change="save" v-model="item.name" type="text">
    </span>
    <span class="table-column table-column_name">
      <input :disabled="!isAdmin" @change="save" v-model="item.nameEnglish" type="text">
    </span>
    <span class="table-column table-column_holding">
      <input :disabled="!isAdmin" @change="save" v-model="item.holding" type="text" class="table-column_holding-input">
    </span>
    <span v-text="winnerName" class="table-column table-column_winner" />
    <span class="table-column table-column_order">
      <input :disabled="!isAdmin" @change="save" v-model="item.sortOrder" type="text" class="table-column_order-input">
    </span>
    <span class="table-column table-column_team">
      <input :disabled="!isAdmin" @change="save" v-model="item.isTeam" type="checkbox">
    </span>
    <span class="table-column table-column_filename">
      <input :disabled="!isAdmin" @change="save" v-model="item.htmlFileName" type="text">
    </span>
    <span class="table-column table-column_holding">
      <input :disabled="!isAdmin" @change="save" v-model="item.htmlFileHolding" type="text" class="table-column_holding-input">
    </span>
    <span class="table-column table-column_modified">
      <input
        :disabled="!isAdmin"
        @change="saveDatepicker($event)"
        v-model="item.htmlFileModified"
        type="text"
        class="datepicker table-column_modified-input"
      >
    </span>
    <span class="table-column table-column_closed">
      <input :disabled="!isAdmin || !isSaved" @change="save" v-model="item.isClosed" type="checkbox">
    </span>
    <span class="table-column table-column_output">
      <input :disabled="!isAdmin" @change="save" v-model="item.isOutput" type="checkbox">
    </span>
    <span class="table-column table-column_official">
      <input :disabled="!isAdmin" @change="save" v-model="item.isOfficial" type="checkbox">
    </span>
    <span class="table-column table-column_open-detail">
      <a @click="select()" v-text="label" class="view-link" />
    </span>
  </li>
</template>

<script lang="ts">
import Vue, { PropType } from 'vue';
import axios from 'axios';

import { TitleResultItem } from '@/types/titles';

export default Vue.extend({
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
  computed: {
    winnerName(): string {
      return this.item.winnerName || '';
    },
    rowClass(): string {
      return this.item.isClosed ? 'table-row-closed' : '';
    },
    label(): string {
      return this.isSaved ? '開く' : '登録';
    },
    isSaved(): boolean {
      return this.item.id !== null && this.item.id !== undefined;
    },
  },
  methods: {
    save() {
      // 未登録なら何もしない
      if (!this.isSaved) {
        return;
      }
      // 更新処理
      return axios.put(`/api/titles/${this.item.id}`, this.item).catch(res => {
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
          url: this.item.url,
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
            messages: `タイトル【${this.item.name}】を登録しました。`,
          });
        })
        .catch(res => {
          const message = res.data.response.message;
          this.$store.dispatch('openDialog', {
            messages: message || '登録に失敗しました…。',
            type: 'error',
          });
        });
    },
    saveDatepicker($event: Event) {
      const target = $event.target as HTMLInputElement;
      if (this.item.htmlFileModified !== target.value) {
        this.item.htmlFileModified = target.value;
        this.save();
      }
    },
  },
});
</script>
