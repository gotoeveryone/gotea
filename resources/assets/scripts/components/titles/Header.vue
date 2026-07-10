<template>
  <ul class="search-header">
    <li class="search-row">
      <div class="search-box">
        <label class="search-box_label">対象国</label>
        <select v-model="select.countryId" class="titles-country" @change="changeValue($event)">
          <option
            v-for="(country, idx) in countries"
            :key="idx"
            :value="country.value"
            v-text="country.text"
          />
        </select>
      </div>
      <div class="search-box">
        <label class="search-box_label">非出力対象</label>
        <select
          v-model="select.searchNonOutput"
          class="titles-output"
          @change="changeValue($event)"
        >
          <option
            v-for="(option, idx) in searchNonOutputOptions"
            :key="idx"
            :value="option.value"
            v-text="option.text"
          />
        </select>
      </div>
      <div class="search-box">
        <label class="search-box_label">終了棋戦</label>
        <select v-model="select.searchClosed" class="titles-closed" @change="changeValue($event)">
          <option
            v-for="(option, idx) in searchClosedOptions"
            :key="idx"
            :value="option.value"
            v-text="option.text"
          />
        </select>
      </div>
      <div v-if="isAdmin" class="search-box search-box-right">
        <button class="button button-secondary" type="button" @click="add()">行追加</button>
        <button class="button button-primary" type="button" @click="json()">JSON出力</button>
      </div>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, toRefs } from 'vue';
import axios from 'axios';

import { Country, DropDown } from '@/types';

const props = defineProps({
  isAdmin: { type: Boolean, default: false },
});
const { isAdmin } = toRefs(props);
const emit = defineEmits<{
  search: [value: typeof select];
  add: [value: typeof select];
  json: [value: typeof select];
}>();
const countries = ref<DropDown[]>([]);
const select = reactive({ countryId: '', searchClosed: 0, searchNonOutput: 1 });
const searchNonOutputOptions = computed(() => {
  return [
    {
      value: 0,
      text: '含めない',
    },
    {
      value: 1,
      text: '含める',
    },
  ];
});
const searchClosedOptions = computed(() => {
  return [
    {
      value: 1,
      text: '検索する',
    },
    {
      value: 0,
      text: '検索しない',
    },
  ];
});
onMounted(() => {
  axios
    .get('/api/countries/')
    .then((res) =>
      res.data.response.map((obj: Country) => ({
        value: obj.id,
        text: `${obj.name}棋戦`,
      })),
    )
    .then((countryOptions) => {
      countries.value = countryOptions;
      Object.assign(select, {
        countryId: countries.value[0].value.toString() || '',
        searchNonOutput: searchNonOutputOptions.value[0].value,
        searchClosed: searchClosedOptions.value[0].value,
      });
      search();
    });
});
const changeValue = ($event: Event) => {
  const target = $event.target as HTMLInputElement;
  (select as Record<string, string | number>)[target.name] = target.value;
  search();
};
const search = () => emit('search', select);
const add = () => emit('add', select);
const json = () => emit('json', select);
</script>
