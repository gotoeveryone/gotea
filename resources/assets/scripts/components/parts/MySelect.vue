<template>
  <div class="input select">
    <label
      :for="name"
      v-text="label"
      class="label-row"
    />
    <select
      :disabled="disabled"
      :required="required"
      :class="inputClass"
      class="input-row"
    >
      <option v-for="item in items" :key="item.value" :value="item.value" v-text="item.label" />
    </select>
  </div>
</template>

<script lang="ts">
import Vue, { PropType } from 'vue';

interface OptionItem {
  value: string;
  label: string;
}

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
    options: {
      type: Array as PropType<OptionItem[]>,
      default: () => [],
    },
  },
  computed: {
    items() {
      if (!this.required) {
        return [
          { value: '', label: '-' },
        ].concat(this.options);
      }
      return this.options;
    },
  },
});
</script>
