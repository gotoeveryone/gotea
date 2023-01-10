export const isPositiveNumber = (value?: string | null) => value != null && /^\d+$/.test(value);
