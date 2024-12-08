import {createApp} from 'vue';
import App from './App.vue';
import router from './routes.js';

import {
    Alert,

} from 'ant-design-vue';

import 'ant-design-vue/dist/reset.css';

const app = createApp(App);

// Sử dụng router và mount ứng dụng
app.use(Alert);

app.use(router).mount("#app");


