<template>
  <div :class="blockClass" class="block-ui">
    <div class="loader" />
  </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
  props: {
    hide: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    blockClass(): string {
      return this.hide ? 'blocked' : '';
    },
  },
});
</script>

<style lang="scss" scoped>
.block-ui {
  @include display-center();
  @include flex-center();

  z-index: 300;
  background-color: rgba($c_black, 0.5);
  visibility: hidden;
  opacity: 0;
  transition: visibility, opacity 0.2s ease-in-out;

  &.blocked {
    visibility: visible;
    opacity: 1;
  }
}

.loader,
.loader::before,
.loader::after {
  background-color: $c_white;
  animation: load 1s infinite ease-in-out;
  width: 0.8rem;
  height: 4rem;
}

.loader {
  color: $c_white;
  text-indent: -9999em;
  margin: 88px auto;
  position: relative;
  font-size: 11px;
  transform: translateZ(0);
  animation-delay: -0.16s;

  &::before,
  &::after {
    position: absolute;
    top: 0;
    content: '';
  }

  &::before {
    left: -1.5em;
    animation-delay: -0.32s;
  }

  &::after {
    left: 1.5em;
  }
}

@keyframes load {
  0%,
  80%,
  100% {
    box-shadow: 0 0;
    height: 4em;
  }

  40% {
    box-shadow: 0 -2em;
    height: 5em;
  }
}
</style>
