<template>
  <transition name="dialog">
    <div v-if="isShow()" :style="{ backgroundColor: modalColor }" class="dialog">
      <div class="dialog-content">
        <div :style="{ backgroundColor: headerColor }" class="dialog-content-header">
          <div v-text="title" class="dialog-content-title" />
        </div>
        <div class="dialog-content-body">
          <div class="dialog-content-body-text">
            <ul :class="messageClass">
              <li v-for="(message, idx) in messages" :key="idx" v-html="message" />
            </ul>
          </div>
        </div>
        <div class="dialog-content-footer">
          <button v-focus @click="close()" class="dialog-content-button">
            閉じる
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import Vue from 'vue';

import { Prop, DialogOption } from '@/types';

export default Vue.extend({
  directives: {
    focus: {
      inserted: el => {
        el.focus();
      },
    },
  },
  props: {
    options: {
      type: Object as Prop<DialogOption>,
      default: () => ({
        modalColor: '',
        headerColor: '',
        type: '',
        title: '',
        messages: [],
        server: false,
      }),
    },
    servType: {
      type: String,
      default: '',
    },
    servMessages: {
      type: Array as Prop<string[]>,
      default: () => [],
    },
  },
  computed: {
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
  },
  mounted() {
    // サーバからのメッセージを保持している場合、それをオプションに設定
    if (this.isServ()) {
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
    isShow(): boolean {
      return (
        this.messages.length > 0 &&
        ((this.isServ() && this.options.server) || (!this.isServ() && !this.options.server))
      );
    },
    isServ(): boolean {
      return this.servMessages && this.servMessages.length > 0;
    },
  },
});
</script>

<style lang="scss" scoped>
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
