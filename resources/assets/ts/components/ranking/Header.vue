<template>
  <ul class="search-header">
    <li class="search-row">
      <fieldset class="search-box">
        <label class="search-box_label ranking_label">抽出対象</label>
        <select
          v-model="select.year"
          @change="changeValue($event)"
          name="year"
          class="ranking_year"
        >
          <option v-for="(year, idx) in years" :key="idx" :value="year.value" v-text="year.text" />
        </select>
        <select
          v-model="select.country"
          @change="changeValue($event)"
          name="country"
          class="ranking_country"
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
          @change="changeValue($event)"
          name="limit"
          class="ranking_limit"
        >
          <option
            v-for="(limit, idx) in limits"
            :key="idx"
            :value="limit.value"
            v-text="limit.text"
          />
        </select>
      </fieldset>
    </li>
    <li class="search-row">
      <fieldset class="search-box">
        <label class="search-box_label ranking_label">対局日</label>
        <input
          v-model="select.from"
          @change="changeValue($event)"
          :disabled="!useInputDate()"
          type="text"
          name="from"
          class="ranking_date datepicker"
        >
        <span class="ranking_date-duration">～</span>
        <input
          v-model="select.to"
          @change="changeValue($event)"
          :disabled="!useInputDate()"
          type="text"
          name="to"
          class="ranking_date datepicker"
        >
      </fieldset>
    </li>
    <li class="search-row">
      <fieldset class="search-box">
        <label class="search-box_label ranking_label">最終更新日</label>
        <span v-text="lastUpdate" class="lastUpdate" />
      </fieldset>
      <fieldset class="search-box search-box-right">
        <button @click="clearDate()" type="button">
          日付をクリア
        </button>
        <button @click="json()" type="button" class="button button-primary">
          JSON出力
        </button>
      </fieldset>
    </li>
  </ul>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import { Country, DropDown, Year } from '@/types';

export default Vue.extend({
  props: {
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
  },
  mounted() {
    // 所属国
    Promise.all([axios.get('/api/countries'), axios.get('/api/years')])
      .then(res => {
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
      const selected = this.years.find(y => y.value === parseInt(this.select.year, 10));
      return selected ? !selected.old : false;
    },
  },
});
</script>
