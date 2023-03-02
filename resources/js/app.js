import './bootstrap';
import '../css/app.css';

import {createApp} from 'vue/dist/vue.esm-bundler.js';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

import DetailClient from "./Pages/Client/DetailClient.vue";
import StageSettings from "./Pages/Stage/StageSettings.vue";
import DetailDeal from "./Pages/Deal/DetailDeal.vue";
import DetailButton from "./Pages/Button/DetailButton.vue";
import DetailTask from "./Pages/Task/DetailTask.vue";

if (document.getElementById("vue-app")) {

    const app = createApp({})
        .use(ElementPlus)
        .mixin({
            components: {
                DetailDeal, DetailClient, StageSettings, DetailButton, DetailTask
            }
        });

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }

    app.mount('#vue-app')
}
