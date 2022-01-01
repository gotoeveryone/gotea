<template>
  <div class="input text">
    <label
      :for="name"
      v-text="label"
      class="label-row"
    />
    <textarea
      :id="name"
      :class="inputClass"
      :disabled="disabled"
      :required="required"
      :maxlength="maxlength"
      class="input-row"
    />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  props: {
    name: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
    inputClass: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    maxlength: {
      type: Number,
      default: null,
    },
  },
});
</script>
