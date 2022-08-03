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

<script lang="ts">
import { defineComponent } from 'vue';
import axios from 'axios';
import dayjs from 'dayjs';

import RankingHeader from '@/components/ranking/Header.vue';
import RankingItems from '@/components/ranking/Items.vue';
import { RankingCondition } from '@/types/ranking';

export default defineComponent({
  components: {
    RankingHeader,
    RankingItems,
  },
  props: {
    isAdmin: {
      type: Boolean,
      default: false,
    },
  },
  data: () => {
    return {
      lastUpdate: '',
      items: [],
    };
  },
  methods: {
    onSearch(_params: RankingCondition) {
      const params = {
        from: _params.from || '',
        to: _params.to || '',
        type: _params.type,
      };

      axios.get(this.createUrl(_params), { params: params }).then((res) => {
        const json = res.data.response;
        if (json.lastUpdate) {
          const dateObj = dayjs(json.lastUpdate);
          this.lastUpdate = dateObj.format('YYYY年MM月DD日');
        } else {
          this.lastUpdate = '';
        }
        this.items = json.ranking;
      });
    },
    outputJson(_params: RankingCondition) {
      const params = {
        from: _params.from || '',
        to: _params.to || '',
        type: _params.type,
      };

      axios
        .post(this.createUrl(_params), params)
        .then(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSONを出力しました。',
          }),
        )
        .catch(() =>
          this.$store.dispatch('openDialog', {
            messages: 'JSON出力に失敗しました…。',
            type: 'error',
          }),
        );
    },
    createUrl(_params: RankingCondition) {
      return `/api/players/ranking/${_params.country}/${_params.year}/${_params.limit}`;
    },
  },
});
</script>
