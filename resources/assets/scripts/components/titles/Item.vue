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
        class="date-input table-column_modified-input"
        @change="onChangeHtmlFileModified()"
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

<script setup lang="ts">
import { computed, onMounted, ref, toRefs, watch } from 'vue';
import type { PropType } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

import { TitleResultItem } from '@/types/titles';

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false,
  },
  item: {
    type: Object as PropType<TitleResultItem>,
    required: true,
  },
});
const { isAdmin, item } = toRefs(props);
const emit = defineEmits<{ openModal: [options: { url: string | null }]; refresh: [] }>();
const store = useStore();
const localItem = ref<TitleResultItem>(item.value);
const winnerName = computed(() => localItem.value.winnerName || '');
const rowClass = computed(() => (localItem.value.isClosed ? 'table-row-closed' : ''));
const isSaved = computed(() => localItem.value.id !== null && localItem.value.id !== undefined);
const label = computed(() => (isSaved.value ? '開く' : '登録'));
watch(item, (newVal) => (localItem.value = newVal));
const save = () => {
  // 未登録なら何もしない
  if (!isSaved.value) {
    return;
  }
  // 更新処理
  return axios.put(`/api/titles/${localItem.value.id}`, props.item).catch((res) => {
    const message = res.data.response.message;
    store.dispatch('openDialog', {
      messages: message || '更新に失敗しました…。',
      type: 'error',
    });
  });
};
const select = () => {
  if (!isSaved.value) {
    add();
  } else {
    emit('openModal', {
      url: localItem.value.url,
    });
  }
};
const add = () => {
  // 登録処理
  return axios
    .post('/api/titles/', props.item)
    .then(() => {
      emit('refresh');
      store.dispatch('openDialog', {
        messages: `タイトル【${localItem.value.name}】を登録しました。`,
      });
    })
    .catch((res) => {
      const message = res.data.response.message;
      store.dispatch('openDialog', {
        messages: message || '登録に失敗しました…。',
        type: 'error',
      });
    });
};
const onChangeHtmlFileModified = () => save();
</script>
