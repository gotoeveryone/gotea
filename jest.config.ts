import { resolve } from 'path';

export default {
  testEnvironment: 'jsdom',
  testEnvironmentOptions: {
    customExportConditions: ['node', 'node-addons'],
  },
  transform: {
    '.*\\.(vue)$': '@vue/vue3-jest',
    '^.+\\.tsx?$': 'ts-jest',
  },
  moduleNameMapper: {
    '^axios$': require.resolve('axios'),
    '^@/(.*)$': resolve(__dirname, 'resources/assets/scripts', '$1'),
  },
  moduleFileExtensions: ['js', 'ts', 'json', 'vue'],
  verbose: true,
};
