<template>
  <ul class="search-header">
    <li class="search-row">
      <fieldset class="search-box">
        <label class="search-box_label">対象国</label>
        <select v-model="select.country" @change="changeValue($event)" class="country">
          <option
            v-for="(country, idx) in countries"
            :key="idx"
            :value="country.value"
            v-text="country.text"
          />
        </select>
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
        country: '',
      },
    };
  },
  mounted() {
    axios
      .get('/api/countries/?has_title=1')
      .then(res =>
        res.data.response.map((obj: Country) => ({
          value: obj.id,
          text: obj.name,
        }))
      )
      .then(countries => {
        this.countries = countries;
        this.select = {
          country: this.countries[0].value.toString() || '',
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
  },
});
</script>
