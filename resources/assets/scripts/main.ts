import { createApp } from 'vue';

import App from '@/app';
import store from '@/store';

createApp(App).use(store).mount('.container');
