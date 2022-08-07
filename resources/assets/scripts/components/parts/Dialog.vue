<template>
  <transition name="dialog">
    <div v-if="isShow" :style="{ backgroundColor: modalColor }" class="dialog" @click="close">
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
          <button ref="closeButton" class="dialog-content-button" @click="close">閉じる</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts" setup>
import { computed, defineProps, onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import type { PropType } from 'vue';
import { DialogOption } from '@/types';

const closeButtonRef = ref<HTMLButtonElement>();

const props = defineProps({
  servType: {
    type: String,
    default: '',
  },
  servMessages: {
    type: Array as PropType<string[]>,
    default: () => [],
  },
});

const store = useStore();

const options = computed(() => store.getters.dialogOptions() as DialogOption);
const modalColor = computed(() => options.value.modalColor || 'rgba(204, 204, 204, 0.6)');
const headerColor = computed(() => options.value.headerColor || '#4ba');
const title = computed(() => options.value.title || 'メッセージ');
const messages = computed(() => {
  const m = options.value.messages;
  if (!Array.isArray(m)) {
    return m ? [m] : [];
  }
  return m;
});
const messageClass = computed(() => {
  if (options.value.type) {
    return `message-${options.value.type}`;
  }
  return 'message-info';
});
const isServ = computed(() => props.servMessages && props.servMessages.length > 0);
const isShow = computed(
  () =>
    messages.value.length > 0 &&
    ((isServ.value && options.value.server) || (!isServ.value && !options.value.server)),
);

onMounted(() => {
  // サーバからのメッセージを保持している場合、それをオプションに設定
  if (isServ.value) {
    store.dispatch('openDialog', {
      title: title.value,
      messages: props.servMessages,
      type: props.servType,
      server: true,
    });
  }
  closeButtonRef.value?.focus();
});

const close = () => store.dispatch('closeDialog');
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
