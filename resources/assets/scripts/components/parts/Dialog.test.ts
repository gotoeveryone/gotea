import { shallowMount } from '@vue/test-utils';
import Component from '@/components/parts/Dialog.vue';
import { createStore, Store } from 'vuex';

const messages = ['hoge', 'fuga'];

const mockOpenDialog = jest.fn();
const mockCloseDialog = jest.fn();
const createStoreMock = (type = 'info') =>
  createStore({
    getters: {
      dialogOptions: () => () => {
        return {
          type,
          messages,
          modalColor: '#f00',
        };
      },
    },
    actions: {
      openDialog: mockOpenDialog,
      closeDialog: mockCloseDialog,
    },
  });

const createWrapper = (
  store: Store<unknown>,
  props: {
    servMessages?: string[];
  } = {},
) =>
  shallowMount(Component, {
    props,
    global: { plugins: [store] },
  });

describe('ダイアログ', () => {
  beforeEach(() => {
    jest.clearAllMocks();
  });

  describe('mounted', () => {
    test('コンテンツが表示される', () => {
      const wrapper = createWrapper(createStoreMock());
      expect(wrapper.findAll('li')).toHaveLength(messages.length);
    });
    test.each(['info', 'warn', 'error'])(
      'メッセージに type: %s を利用したクラスが設定される',
      (type) => {
        const wrapper = createWrapper(createStoreMock(type));
        expect(wrapper.find('ul').classes()).toContain(`message-${type}`);
      },
    );
    test('サーバからのメッセージを保持している場合、オープンイベントがコールされる', async () => {
      const servMessages = ['server message 1', 'server message 2', 'server message 3'];
      createWrapper(createStoreMock(), {
        servMessages,
      });
      expect(mockOpenDialog).toBeCalled();
    });
  });

  describe('event', () => {
    test('閉じるボタンを押下した際、クローズイベントがコールされる', () => {
      const wrapper = createWrapper(createStoreMock());
      wrapper.find('.dialog-content-button').trigger('click');
      expect(mockCloseDialog).toBeCalled();
    });
  });
});
