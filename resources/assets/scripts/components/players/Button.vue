<template>
  <button type="button" country="country" class="button button-secondary" @click="newPlayer()">
    新規作成
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useStore } from 'vuex';

const props = defineProps({
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
});
const store = useStore();
const targetId = computed(() => (props.changed ? props.countryId : props.paramId));
const newPlayer = () =>
  store.dispatch('openModal', { url: `${props.url}?country_id=${targetId.value}` });
</script>
