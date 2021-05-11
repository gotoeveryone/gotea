<template>
  <button type="button" country="country" class="button button-secondary" @click="newPlayer()">
    新規作成
  </button>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  props: {
    url: {
      type: String,
      required: true,
    },
    countryId: {
      type: String,
      required: true,
    },
    paramId: {
      type: String,
      required: true,
    },
    changed: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    targetId(): string {
      if (!this.changed) {
        return this.paramId;
      }
      return this.countryId;
    },
  },
  methods: {
    newPlayer() {
      this.$store.dispatch('openModal', {
        url: `${this.url}?country_id=${this.targetId}`,
      });
    },
  },
});
</script>
