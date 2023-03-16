import './bootstrap';
import '../css/app.css';
import '../css/elplus_update.css';
import '../css/scroll.css';
import '../css/action_button.css';

import {createApp} from 'vue/dist/vue.esm-bundler.js';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'

import DetailClient from "./Pages/Client/DetailClient.vue";
import StageSettings from "./Pages/Stage/StageSettings.vue";
import ActionButton from "./Components/ActionButton.vue";
import DetailDeal from "./Pages/Deal/DetailDeal.vue";
import DetailDealButton from "./Pages/Deal/DetailDealButton.vue";
import DetailTaskButton from "./Pages/Task/DetailTaskButton.vue";
import DetailTask from "./Pages/Task/DetailTask.vue";
import DetailManagerActions from "./Pages/User/DetailManagerActions.vue";
import DetailReport from "./Pages/Report/DetailReport.vue";
import DetailIntegration from "./Pages/Integration/DetailIntegration.vue";

if (document.getElementById("vue-app")) {

    const app = createApp({})
        .use(ElementPlus)
        .mixin({
            components: {
                ActionButton,
                DetailDeal,
                DetailClient,
                StageSettings,
                DetailDealButton,
                DetailTask,
                DetailTaskButton,
                DetailManagerActions,
                DetailReport,
                DetailIntegration
            }
        });

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }

    app.mount('#vue-app')
}
