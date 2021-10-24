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
        <button class="button button-secondary" type="button" @click="add()">
          行追加
        </button>
        <button class="button button-primary" type="button" @click="json()">
          JSON出力
        </button>
      </div>
    </li>
  </ul>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import axios from 'axios';

import { Country, DropDown } from '@/types';

export default defineComponent({
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
  },
  data: () => {
    return {
      countries: [] as DropDown[],
      select: {
        countryId: '',
        searchClosed: 0,
        searchNonOutput: 1,
      },
    };
  },
  computed: {
    searchNonOutputOptions() {
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
    },
    searchClosedOptions() {
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
    },
  },
  mounted() {
    axios
      .get('/api/countries/')
      .then((res) =>
        res.data.response.map((obj: Country) => ({
          value: obj.id,
          text: `${obj.name}棋戦`,
        })),
      )
      .then((countries) => {
        this.countries = countries;
        this.select = {
          countryId: this.countries[0].value.toString() || '',
          searchNonOutput: this.searchNonOutputOptions[0].value,
          searchClosed: this.searchClosedOptions[0].value,
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
