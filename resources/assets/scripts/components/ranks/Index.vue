<template>
  <section class="categories">
    <ranks-header @search="onSearch" />
    <ranks-items :items="items" />
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

import RanksHeader from '@/components/ranks/Header.vue';
import RanksItems from '@/components/ranks/Items.vue';
import { RanksCondition } from '@/types/ranks';

const items = ref([]);
const onSearch = (_params: RanksCondition) => {
  axios
    .get(`/api/players/ranks/${_params.country}`)
    .then((res) => (items.value = res.data.response));
};
</script>
