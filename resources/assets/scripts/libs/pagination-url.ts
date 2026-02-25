import { isPositiveNumber } from '@/libs/validator';

type Pagination = {
  page: number;
  limit: number;
};

export const readPaginationFromUrl = (defaultLimit = 30): Pagination => {
  const url = new URL(location.href);
  const limit = url.searchParams.get('limit') || '';
  const page = url.searchParams.get('page') || '';

  return {
    limit: isPositiveNumber(limit) ? Number.parseInt(limit, 10) : defaultLimit,
    page: isPositiveNumber(page) ? Number.parseInt(page, 10) : 1,
  };
};

export const pushPaginationState = ({ page, limit }: Pagination): void => {
  const url = new URL(location.href);
  url.searchParams.set('limit', limit.toString());
  url.searchParams.set('page', page.toString());

  if (url.toString() !== location.href) {
    history.pushState({ page, limit }, '', url);
  }
};

export const listenPaginationPopState = (
  defaultLimit: number,
  callback: (pagination: Pagination) => void,
): (() => void) => {
  const onLocationChanged = () => {
    callback(readPaginationFromUrl(defaultLimit));
  };
  window.addEventListener('popstate', onLocationChanged);
  window.addEventListener('pageshow', onLocationChanged);

  return () => {
    window.removeEventListener('popstate', onLocationChanged);
    window.removeEventListener('pageshow', onLocationChanged);
  };
};
