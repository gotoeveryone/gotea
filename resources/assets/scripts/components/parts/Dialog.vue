<template>
  <transition name="dialog">
    <div v-if="isShow" :style="{ backgroundColor: modalColor }" class="dialog" @click="close()">
      <div class="dialog-content">
        <div :style="{ backgroundColor: headerColor }" class="dialog-content-header">
          <div class="dialog-content-title" v-text="title" />
        </div>
        <div class="dialog-content-body">
          <div class="dialog-content-body-text">
            <ul :class="messageClass">
              <!-- eslint-disable vue/no-v-html -->
              <li v-for="(message, idx) in messages" :key="idx" v-html="message" />
              <!-- eslint-enable vue/no-v-html -->
            </ul>
          </div>
        </div>
        <div class="dialog-content-footer">
          <button v-focus class="dialog-content-button" @click="close()">
            閉じる
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';

import { DialogOption } from '@/types';

export default defineComponent({
  directives: {
    focus: {
      mounted: (el) => {
        el.focus();
      },
    },
  },
  props: {
    servType: {
      type: String,
      default: '',
    },
    servMessages: {
      type: Array as PropType<string[]>,
      default: () => [],
    },
  },
  computed: {
    options(): DialogOption {
      return this.$store.getters.dialogOptions();
    },
    modalColor(): string {
      return this.options.modalColor || 'rgba(204, 204, 204, 0.6)';
    },
    headerColor(): string {
      return this.options.headerColor || '#4ba';
    },
    title(): string {
      return this.options.title || 'メッセージ';
    },
    messages(): string[] {
      const m = this.options.messages;
      if (!Array.isArray(m)) {
        return m ? [m] : [];
      }
      return m;
    },
    messageClass(): string {
      if (this.options.type) {
        return `message-${this.options.type}`;
      }
      return 'message-info';
    },
    isShow(): boolean {
      return (
        this.messages.length > 0 &&
        ((this.isServ && this.options.server) || (!this.isServ && !this.options.server))
      );
    },
    isServ(): boolean {
      return this.servMessages && this.servMessages.length > 0;
    },
  },
  mounted() {
    // サーバからのメッセージを保持している場合、それをオプションに設定
    if (this.isServ) {
      this.$store.dispatch('openDialog', {
        title: this.title,
        messages: this.servMessages,
        type: this.servType,
        server: true,
      });
    }
  },
  methods: {
    close() {
      this.$store.dispatch('closeDialog');
    },
  },
});
</script>

<style lang="scss" scoped>
@import 'resources/assets/styles/base/variables';
@import 'resources/assets/styles/base/mixin';

.dialog {
  display: block;
  position: fixed;
  width: 100%;
  height: 100%;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 5000;

  &.hide {
    display: none;
  }
}

.dialog-content {
  position: fixed;
  height: auto;
  margin: auto;
  width: 400px;
  max-width: 80%;
  min-height: 200px;
  max-height: 200px;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  border-radius: 5px;
  box-shadow: 3px 3px 5px 3px $c_gray;
  z-index: 10;

  &-header {
    @include flex-center();

    height: 35px;
    border-radius: 5px 5px 0 0;
  }

  &-title {
    margin-left: 10px;
    color: $c_white;
    font-size: 15px;
    font-weight: bold;
  }

  &-body {
    padding: 1rem;
    height: calc(100% - 30px - 40px);
    overflow-y: auto;
    font-size: 15px;
    background-color: #eee;
    border-bottom: 1px solid #aaa;
  }

  &-text {
    margin: 10px;
  }

  &-footer {
    @include flex-justify-end();

    height: 50px;
    background-color: #eee;
    border-radius: 0 0 5px 5px;
  }

  &-button {
    font-size: 15px;
    cursor: pointer;
  }
}

.message {
  margin: 0;
}

.message-info {
  color: #000;
}

.message-warning {
  color: #00f;
}

.message-error {
  color: #f00;
}

.dialog-enter-active,
.dialog-leave-active {
  transition: opacity 0.5s;
}

.dialog-enter,
.dialog-leave-to {
  opacity: 0;
}
</style>
