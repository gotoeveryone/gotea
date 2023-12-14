<template>
  <ul class="search-header">
    <li class="search-row">
      <div class="search-box">
        <label class="search-box_label">対象国</label>
        <select v-model="selectedCountry" class="country" @change="changeValue($event)">
          <option
            v-for="(country, idx) in countries"
            :key="idx"
            :value="country.value"
            v-text="country.text"
          />
        </select>
      </div>
    </li>
  </ul>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import axios from 'axios';

import { Country, DropDown } from '@/types';

export default defineComponent({
  data: () => {
    return {
      countries: [] as DropDown[],
      selectedCountry: '',
    };
  },
  mounted() {
    axios
      .get('/api/countries/?has_title=1')
      .then((res) =>
        res.data.response.map((obj: Country) => ({
          value: obj.id,
          text: obj.name,
        })),
      )
      .then((countries) => {
        this.countries = countries;
        this.selectedCountry = this.countries[0].value.toString() || '';
        this.search();
      });
  },
  methods: {
    changeValue($event: Event) {
      const target = $event.target as HTMLInputElement;
      this.selectedCountry = target.value;
      this.search();
    },
    search() {
      this.$emit('search', { country: this.selectedCountry });
    },
  },
});
</script>
