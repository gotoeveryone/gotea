<template>
  <section class="ranking">
    <ranking-header
      :is-admin="isAdmin"
      :last-update="lastUpdate"
      @search="onSearch"
      @json="outputJson"
    />
    <ranking-items :items="items" />
  </section>
</template>

<script setup lang="ts">
import { ref, toRefs } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';
import dayjs from 'dayjs';

import RankingHeader from '@/components/ranking/Header.vue';
import RankingItems from '@/components/ranking/Items.vue';
import { RankingCondition } from '@/types/ranking';

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false,
  },
});
const { isAdmin } = toRefs(props);
const store = useStore();
const lastUpdate = ref('');
const items = ref([]);
const createUrl = (_params: RankingCondition) =>
  `/api/players/ranking/${_params.country}/${_params.year}/${_params.limit}`;
const onSearch = (_params: RankingCondition) => {
  const params = {
    from: _params.from || '',
    to: _params.to || '',
    type: _params.type,
  };

  axios.get(createUrl(_params), { params: params }).then((res) => {
    const json = res.data.response;
    if (json.lastUpdate) {
      const dateObj = dayjs(json.lastUpdate);
      lastUpdate.value = dateObj.format('YYYY年MM月DD日');
    } else {
      lastUpdate.value = '';
    }
    items.value = json.ranking;
  });
};
const outputJson = (_params: RankingCondition) => {
  const params = {
    from: _params.from || '',
    to: _params.to || '',
    type: _params.type,
  };

  axios
    .post(createUrl(_params), params)
    .then(() =>
      store.dispatch('openDialog', {
        messages: 'JSONを出力しました。',
      }),
    )
    .catch(() =>
      store.dispatch('openDialog', {
        messages: 'JSON出力に失敗しました…。',
        type: 'error',
      }),
    );
};
</script>
