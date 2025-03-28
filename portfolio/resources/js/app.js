import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './routes.js';

import {
    Alert,
    Upload,
    Modal,
    Steps,
} from 'ant-design-vue';

import 'ant-design-vue/dist/reset.css';

const app = createApp(App);
const pinia = createPinia();

// Sử dụng router và mount ứng dụng
app.use(pinia);
app.use(Alert);
app.use(Steps);
app.use(Upload);
app.use(Modal);

app.use(router).mount("#app");
