import { flushPromises, shallowMount } from '@vue/test-utils';
import axios from 'axios';
import ListItem from '@/components/table-templates/Item.vue';
import Component from '@/pages/table-templates/index.vue';
import { TableTemplate as Item } from '@/types/table-template';
import { MockInstance } from 'vitest';

vi.mock('axios');

const createWrapper = () =>
  shallowMount(Component, {
    global: {
      mocks: { axios },
    },
  });

const items = [
  {
    id: 1,
    title: 'test title',
    content: 'test content',
  },
  {
    id: 2,
    title: 'test title 2',
    content: 'test\ncontent',
  },
] as Item[];

describe('表テンプレート一覧', () => {
  beforeEach(() => {
    vi.clearAllMocks();

    (axios.get as unknown as MockInstance).mockResolvedValue(
      Promise.resolve({
        data: {
          response: {
            count: items.length,
            items: items,
          },
        },
      }),
    );
  });

  describe('mounted', () => {
    test('件数分 ListItem タグが描画される', async () => {
      const wrapper = createWrapper();
      await flushPromises();
      expect(wrapper.findAllComponents(ListItem)).toHaveLength(items.length);
    });
    describe('limit/page の変更', () => {
      test.each([
        { limit: 10, page: 1 },
        { limit: 10, page: 5 },
        { limit: 5, page: 2 },
        { limit: 20, page: 3 },
      ])(
        'URL で指定した limit: $limit / page: $page が data に設定される',
        async ({ limit, page }) => {
          /* eslint-disable @typescript-eslint/ban-ts-comment */
          // @ts-ignore
          delete window.location;
          // @ts-ignore
          window.location = {
            href: `https://hoge.example.com/?limit=${limit}&page=${page}`,
          };
          /* eslint-enable */
          const wrapper = createWrapper();
          expect(wrapper.vm.perPage).toBe(limit);
          expect(wrapper.vm.currentPage).toBe(page);
        },
      );
    });
  });
});
