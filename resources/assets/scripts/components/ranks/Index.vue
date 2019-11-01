<template>
  <section class="categories">
    <ranks-header @search="onSearch" />
    <ranks-items :items="items" />
  </section>
</template>

<script lang="ts">
import Vue from 'vue';
import axios from 'axios';

import RanksHeader from '@/components/ranks/Header.vue';
import RanksItems from '@/components/ranks/Items.vue';
import { RanksCondition } from '@/types/ranks';

export default Vue.extend({
  components: {
    RanksHeader,
    RanksItems,
  },
  data: () => {
    return {
      items: [],
    };
  },
  methods: {
    onSearch(_params: RanksCondition) {
      axios
        .get(`/api/players/ranks/${_params.country}`)
        .then(res => (this.items = res.data.response));
    },
  },
});
</script>
