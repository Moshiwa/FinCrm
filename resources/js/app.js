import './bootstrap';
import '../css/app.css';

import {createApp} from 'vue/dist/vue.esm-bundler.js';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import DetailDeal from "./Pages/Deal/DetailDeal.vue";
import SettingsMain from "./Pages/Setting/SettingsMain.vue";
import ClientEdit from "./Pages/Client/Edit.vue";
import StageSettings from "./Pages/Stage/StageSettings.vue";


if (document.getElementById("vue-app")) {

    const app = createApp({})
        .use(ElementPlus)
        .mixin({
            components: {
                DetailDeal, SettingsMain, ClientEdit, StageSettings
            }
        });

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }

    app.mount('#vue-app')
}
