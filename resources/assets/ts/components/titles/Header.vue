<template>
  <ul class="search-header">
    <li class="search-row">
      <fieldset class="search-box">
        <label class="search-box_label">対象国</label>
        <select @change="changeValue($event)" v-model="select.countryId" class="titles-country">
          <option
            :key="idx"
            :value="country.value"
            v-for="(country, idx) in countries"
            v-text="country.text"
          />
        </select>
      </fieldset>
      <fieldset class="search-box">
        <label class="search-box_label">終了棋戦</label>
        <select @change="changeValue($event)" v-model="select.isClosed" class="titles-closed">
          <option :key="idx" :value="type.value" v-for="(type, idx) in types" v-text="type.text" />
        </select>
      </fieldset>
      <fieldset class="search-box search-box-right">
        <button @click="add()" class="button button-secondary" type="button">
          行追加
        </button>
        <button @click="json()" class="button button-primary" type="button">
          JSON出力
        </button>
      </fieldset>
    </li>
  </ul>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import { Country, DropDown } from '@/types';

export default Vue.extend({
  data: () => {
    return {
      countries: [] as DropDown[],
      select: {
        countryId: '',
        isClosed: 0,
      },
    };
  },
  computed: {
    types() {
      return [
        {
          value: 0,
          text: '検索しない',
        },
        {
          value: 1,
          text: '検索する',
        },
      ];
    },
  },
  mounted() {
    axios
      .get('/api/countries/')
      .then(res =>
        res.data.response.map((obj: Country) => ({
          value: obj.id,
          text: `${obj.name}棋戦`,
        }))
      )
      .then(countries => {
        this.countries = countries;
        this.select = {
          countryId: this.countries[0].value.toString() || '',
          isClosed: this.types[0].value,
        };
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
    add() {
      this.$emit('add', this.select);
    },
    json() {
      this.$emit('json', this.select);
    },
  },
});
</script>
