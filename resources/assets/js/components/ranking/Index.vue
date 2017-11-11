<template>
    <section class="ranking">
        <ranking-header :lastUpdate="lastUpdate" @search="onSearch" @json="outputJson"></ranking-header>
        <ranking-items :items="items"></ranking-items>
    </section>
</template>

<script>
import Header from "./Header.vue";
import Items from "./Items.vue";

export default {
  data: () => {
    return {
      lastUpdate: null,
      items: []
    };
  },
  components: {
    rankingHeader: Header,
    rankingItems: Items
  },
  methods: {
    onSearch(_params) {
      this.$http
        .get(this.getUrl(_params), { params: { withJa: "1" } })
        .then(res => {
          const json = res.body.response;
          const dateObj = new Date(json.lastUpdate);
          this.lastUpdate = `${dateObj.getFullYear()}年${dateObj.getMonth() +
            1}月${dateObj.getDate()}日`;
          this.items = json.ranking;
        });
    },
    outputJson(_params) {
      this.$http
        .post(this.getUrl(_params))
        .then(() =>
          this.$store.dispatch("openDialog", {
            messages: "JSONを出力しました。"
          })
        )
        .catch(() =>
          this.$store.dispatch("openDialog", {
            messages: "JSON出力に失敗しました…。",
            type: "error"
          })
        );
    },
    getUrl(_params) {
      return `/api/rankings/${_params.country}/${_params.year}/${_params.limit}`;
    }
  }
};
</script>
