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
import { computed, defineProps, nextTick, onMounted, ref, watch } from 'vue';
import { useStore } from 'vuex';
import type { PropType } from 'vue';
import { DialogOption } from '@/types';

const closeButton = ref<HTMLButtonElement>();

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
const modalColor = computed(() => options.value.modalColor || '');
const headerColor = computed(() => options.value.headerColor || '');
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
  nextTick(() => closeButton.value?.focus());
});

const close = () => store.dispatch('closeDialog');

watch(isShow, (newVal, oldVal) => {
  if (!oldVal && newVal) {
    nextTick(() => closeButton.value?.focus());
  }
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
  background-color: $c_modal_backdrop;

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
  border: 1px solid $c_border;
  box-shadow: 3px 3px 5px 3px $c_main;
  z-index: 10;

  &-header {
    @include flex-center();

    height: 35px;
    border-radius: 5px 5px 0 0;
    background-color: $c_modal_header;
  }

  &-title {
    margin-left: 10px;
    color: $c_white;
    font-size: 16px;
    font-weight: bold;
  }

  &-body {
    padding: 1rem;
    height: calc(100% - 35px - 48px);
    overflow-y: auto;
    font-size: 14px;
    background-color: $c_modal;
  }

  &-text {
    margin: 10px;
  }

  &-footer {
    @include flex-justify-end();

    height: 48px;
    background-color: $c_footer;
    border-radius: 0 0 5px 5px;
  }

  &-button {
    margin-right: 0.5rem;
    font-size: 14px;
    cursor: pointer;
  }
}

.message {
  margin: 0;
}

.message-info {
  color: $c_text;
}

.message-warning {
  color: $c_warning;
}

.message-error {
  color: $c_error;
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
