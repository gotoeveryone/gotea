<template>
  <ul class="search-header">
    <li class="search-row">
      <div class="search-box">
        <label class="search-box_label ranking_label">抽出対象</label>
      </div>
      <div class="search-box">
        <select
          v-model="select.year"
          name="year"
          class="ranking_year"
          @change="changeValue($event)"
        >
          <option v-for="(year, idx) in years" :key="idx" :value="year.value" v-text="year.text" />
        </select>
        <select
          v-model="select.country"
          name="country"
          class="ranking_country"
          @change="changeValue($event)"
        >
          <option
            v-for="(country, idx) in countries"
            :key="idx"
            :value="country.value"
            v-text="country.text"
          />
        </select>
        <select
          v-model="select.limit"
          name="limit"
          class="ranking_limit"
          @change="changeValue($event)"
        >
          <option
            v-for="(limit, idx) in limits"
            :key="idx"
            :value="limit.value"
            v-text="limit.text"
          />
        </select>
        <select
          v-model="select.type"
          name="type"
          class="ranking_type"
          @change="changeValue($event)"
        >
          <option v-for="type in types" :key="type.value" :value="type.value" v-text="type.text" />
        </select>
      </div>
    </li>
    <li class="search-row">
      <div class="search-box">
        <label class="search-box_label ranking_label">対局日</label>
      </div>
      <div class="search-box">
        <input
          v-model="select.from"
          :disabled="!useInputDate()"
          type="date"
          name="from"
          class="ranking_date date-input"
          @change="changeValue($event)"
        />
        <span class="ranking_date-duration">～</span>
        <input
          v-model="select.to"
          :disabled="!useInputDate()"
          type="date"
          name="to"
          class="ranking_date date-input"
          @change="changeValue($event)"
        />
      </div>
    </li>
    <li class="search-row">
      <div class="search-box search-box-between">
        <div>
          <label class="search-box_label ranking_label">最終更新日</label>
          <span class="lastUpdate" v-text="lastUpdate" />
        </div>
        <div>
          <button type="button" @click="clearDate()">日付をクリア</button>
          <button v-if="isAdmin" type="button" class="button button-primary" @click="json()">
            JSON出力
          </button>
        </div>
      </div>
    </li>
  </ul>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, toRefs } from 'vue';
import axios from 'axios';

import { Country, DropDown, Year } from '@/types';

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false,
  },
  lastUpdate: {
    type: String,
    default: '',
  },
});
const { isAdmin, lastUpdate } = toRefs(props);
const emit = defineEmits<{ search: [value: typeof select]; json: [value: typeof select] }>();
const countries = ref<DropDown[]>([]);
const years = ref<DropDown[]>([]);
const select = reactive({ year: '', country: '', limit: 0, from: '', to: '', type: 'point' });
const limits = computed(() => {
  const values = [];
  for (let l = 20; l <= 50; l += 10) {
    values.push({
      value: l,
      text: `～${l}位`,
    });
  }
  return values;
});
const types = computed(() => {
  return [
    {
      value: 'point',
      text: '勝数',
    },
    {
      value: 'percent',
      text: '勝率',
    },
  ];
});
const search = () => emit('search', select);
onMounted(() => {
  // 所属国
  Promise.all([axios.get('/api/countries'), axios.get('/api/years')])
    .then((res) => {
      countries.value = res[0].data.response.map((obj: Country) => ({
        value: obj.code,
        text: `${obj.name}棋戦`,
      }));
      years.value = res[1].data.response.map((obj: Year) => ({
        value: obj.year,
        text: `${obj.year}年度`,
        old: obj.old,
      }));
    })
    .then(() => {
      select.year = years.value[0].value.toString();
      select.country = countries.value[0].value.toString() || '';
      select.limit = limits.value[0].value;
      search();
    });
});
const changeValue = ($event: Event) => {
  const target = $event.target as HTMLInputElement;
  (select as Record<string, string | number>)[target.name] = target.value;
  search();
};
const clearDate = () => {
  select.from = '';
  select.to = '';
  search();
};
const json = () => emit('json', select);
const useInputDate = () => {
  const selected = years.value.find((y) => y.value === parseInt(select.year, 10));
  return selected ? !selected.old : false;
};
</script>
