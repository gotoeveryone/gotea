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
          class="ranking_date"
          autocomplete="off"
          @change="changeValue($event)"
        />
        <span class="ranking_date-duration">～</span>
        <input
          v-model="select.to"
          :disabled="!useInputDate()"
          type="date"
          name="to"
          class="ranking_date"
          autocomplete="off"
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

<script lang="ts">
import { defineComponent } from 'vue';
import axios from 'axios';

import { Country, DropDown, Year } from '@/types';

export default defineComponent({
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
    lastUpdate: {
      type: String,
      default: '',
    },
  },
  data: () => {
    return {
      countries: [] as DropDown[],
      years: [] as DropDown[],
      select: {
        year: '',
        country: '',
        limit: 0,
        from: '',
        to: '',
        type: 'point',
      },
    };
  },
  computed: {
    limits() {
      const limits = [];
      for (let l = 20; l <= 50; l = l + 10) {
        limits.push({
          value: l,
          text: `～${l}位`,
        });
      }
      return limits;
    },
    types() {
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
    },
  },
  mounted() {
    // 所属国
    Promise.all([axios.get('/api/countries'), axios.get('/api/years')])
      .then((res) => {
        this.countries = res[0].data.response.map((obj: Country) => ({
          value: obj.code,
          text: `${obj.name}棋戦`,
        }));
        this.years = res[1].data.response.map((obj: Year) => ({
          value: obj.year,
          text: `${obj.year}年度`,
          old: obj.old,
        }));
      })
      .then(() => {
        this.select.year = this.years[0].value.toString();
        this.select.country = this.countries[0].value.toString() || '';
        this.select.limit = this.limits[0].value;
        this.search();
      });
  },
  methods: {
    changeValue($event: Event) {
      const target = $event.target as HTMLInputElement;
      this.select[target.name] = target.value;
      this.search();
    },
    search() {
      this.$emit('search', this.select);
    },
    clearDate() {
      this.select.from = '';
      this.select.to = '';
      this.search();
    },
    json() {
      this.$emit('json', this.select);
    },
    useInputDate() {
      const selected = this.years.find((y) => y.value === parseInt(this.select.year, 10));
      return selected ? !selected.old : false;
    },
  },
});
</script>
