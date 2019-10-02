<template>
  <transition name="modal">
    <div @click="close()" v-if="isShow()" class="iframe-modal">
      <div :style="{ height: height, width: width }" class="modal-parent">
        <iframe :src="options.url" class="modal-body" />
        <div @click="close()" v-if="isShow()" class="modal-close">
          <span class="modal-close-mark">×</span>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import Vue, { PropType } from 'vue';

import { ModalOption } from '@/types';

export default Vue.extend({
  props: {
    options: {
      type: Object as PropType<ModalOption>,
      default: () => ({
        url: '',
        height: '',
        width: '',
        callback: () => null,
      }),
    },
  },
  computed: {
    height(): string {
      return this.options.height ? this.options.height.toString() : '90%';
    },
    width(): string {
      return this.options.width ? this.options.width.toString() : '90%';
    },
  },
  methods: {
    close() {
      this.$store.dispatch('closeModal').then(() => {
        if (typeof this.options.callback === 'function') {
          this.options.callback();
        }
      });
    },
    isShow() {
      return this.options.url !== '';
    },
  },
});
</script>

<style lang="scss" scoped>
.iframe-modal {
  @include display-center();

  background-color: rgba($c_black, 0.7);
  z-index: 200;
  cursor: pointer;
  visibility: visible;
  opacity: 1;
  transition-property: visibility, opacity, height;
  transition-timing-function: ease-in;
  transition-duration: 0.2s;

  &.hide {
    visibility: hidden;
    opacity: 0;
  }
}

.modal-parent {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  margin: auto;
  border-radius: 10px;
  transition-property: height;
  transition-timing-function: ease-in;
  transition-duration: 0.2s;

  &.hide {
    height: 0;
  }
}

.modal-body {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 10px;
}

.modal-close {
  @include flex-center();

  position: absolute;
  width: 30px;
  height: 25px;
  top: 0;
  right: 5px;
  border-radius: 0 0 5px 5px;
  background-color: $c_black;
  color: $c_white;
  cursor: pointer;

  &:hover {
    background-color: #333;
  }

  &-mark {
    margin: -3px auto 0;
    font-size: 20px;
    text-align: center;
  }
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.5s;
}

.modal-enter,
.modal-leave-to {
  opacity: 0;
}
</style>
