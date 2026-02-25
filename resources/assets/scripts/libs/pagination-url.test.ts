import {
  listenPaginationPopState,
  pushPaginationState,
  readPaginationFromUrl,
} from '@/libs/pagination-url';

const setLocation = (href: string) => {
  /* eslint-disable @typescript-eslint/ban-ts-comment */
  // @ts-ignore
  delete window.location;
  // @ts-ignore
  window.location = { href };
  /* eslint-enable */
};

describe('pagination-url', () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  describe('readPaginationFromUrl', () => {
    test('URL の limit/page を数値として読み取る', () => {
      setLocation('https://hoge.example.com/notifications?limit=15&page=4');

      expect(readPaginationFromUrl(30)).toStrictEqual({
        limit: 15,
        page: 4,
      });
    });

    test('不正な値の場合はデフォルト値を返す', () => {
      setLocation('https://hoge.example.com/notifications?limit=abc&page=-1');

      expect(readPaginationFromUrl(50)).toStrictEqual({
        limit: 50,
        page: 1,
      });
    });
  });

  describe('pushPaginationState', () => {
    test('URL が変わる場合のみ pushState を呼び出す', () => {
      setLocation('https://hoge.example.com/notifications?limit=10&page=1');
      const pushStateSpy = vi.spyOn(window.history, 'pushState').mockImplementation(() => {});

      pushPaginationState({ page: 2, limit: 30 });

      expect(pushStateSpy).toHaveBeenCalledTimes(1);
      expect(pushStateSpy).toHaveBeenCalledWith(
        { page: 2, limit: 30 },
        '',
        new URL('https://hoge.example.com/notifications?limit=30&page=2'),
      );
    });

    test('URL が同じ場合は pushState を呼び出さない', () => {
      setLocation('https://hoge.example.com/notifications?limit=30&page=2');
      const pushStateSpy = vi.spyOn(window.history, 'pushState').mockImplementation(() => {});

      pushPaginationState({ page: 2, limit: 30 });

      expect(pushStateSpy).not.toHaveBeenCalled();
    });
  });

  describe('listenPaginationPopState', () => {
    test('popstate 時に URL の値を読み取ってコールバックへ渡す', () => {
      setLocation('https://hoge.example.com/notifications?limit=10&page=1');
      const callback = vi.fn();
      const cleanup = listenPaginationPopState(30, callback);

      setLocation('https://hoge.example.com/notifications?limit=50&page=3');
      window.dispatchEvent(new PopStateEvent('popstate'));

      expect(callback).toHaveBeenCalledTimes(1);
      expect(callback).toHaveBeenCalledWith({ page: 3, limit: 50 });
      cleanup();
    });

    test('解除後は popstate でコールバックが呼ばれない', () => {
      setLocation('https://hoge.example.com/notifications?limit=10&page=1');
      const callback = vi.fn();
      const cleanup = listenPaginationPopState(30, callback);

      cleanup();
      setLocation('https://hoge.example.com/notifications?limit=20&page=2');
      window.dispatchEvent(new PopStateEvent('popstate'));

      expect(callback).not.toHaveBeenCalled();
    });

    test('pageshow 時にも URL の値を読み取ってコールバックへ渡す', () => {
      setLocation('https://hoge.example.com/notifications?limit=10&page=1');
      const callback = vi.fn();
      const cleanup = listenPaginationPopState(30, callback);

      setLocation('https://hoge.example.com/notifications?limit=25&page=5');
      window.dispatchEvent(new Event('pageshow'));

      expect(callback).toHaveBeenCalledTimes(1);
      expect(callback).toHaveBeenCalledWith({ page: 5, limit: 25 });
      cleanup();
    });

    test('解除後は pageshow でコールバックが呼ばれない', () => {
      setLocation('https://hoge.example.com/notifications?limit=10&page=1');
      const callback = vi.fn();
      const cleanup = listenPaginationPopState(30, callback);

      cleanup();
      setLocation('https://hoge.example.com/notifications?limit=25&page=5');
      window.dispatchEvent(new Event('pageshow'));

      expect(callback).not.toHaveBeenCalled();
    });
  });
});
