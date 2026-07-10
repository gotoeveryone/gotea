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

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import axios from 'axios';

import { Country, DropDown } from '@/types';

const emit = defineEmits<{ search: [params: { country: string }] }>();
const countries = ref<DropDown[]>([]);
const selectedCountry = ref('');
const search = () => emit('search', { country: selectedCountry.value });
onMounted(() => {
  axios
    .get('/api/countries/?has_title=1')
    .then((res) =>
      res.data.response.map((obj: Country) => ({
        value: obj.id,
        text: obj.name,
      })),
    )
    .then((countryOptions) => {
      countries.value = countryOptions;
      selectedCountry.value = countries.value[0].value.toString() || '';
      search();
    });
});
const changeValue = ($event: Event) => {
  const target = $event.target as HTMLInputElement;
  selectedCountry.value = target.value;
  search();
};
</script>
