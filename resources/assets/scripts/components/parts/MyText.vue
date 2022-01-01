<template>
  <div class="input text">
    <label
      :for="name"
      v-text="label"
      class="label-row"
    />
    <input
      :id="name"
      :value="value"
      :class="inputClass"
      :disabled="disabled"
      :required="required"
      :maxlength="maxlength"
      @change="onChange"
      type="text"
      data-validity-message="このフィールドは空欄にできません"
      oninvalid="this.setCustomValidity(''); if (!this.value) this.setCustomValidity(this.dataset.validityMessage)"
      oninput="this.setCustomValidity('')"
      aria-required="true"
      class="input-row"
    >
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
    value: {
      type: [String, Number],
      default: '',
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
  methods: {
    onChange($event: Event) {
      this.$emit('change', this.name, ($event.target as HTMLInputElement).value);
    },
  },
});
</script>
