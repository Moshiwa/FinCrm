import './bootstrap';
import '../css/app.css';

import {createApp} from 'vue/dist/vue.esm-bundler.js';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import Contenteditable from "./Components/Ui/Contenteditable.vue";
import FileUpload from "./Components/Ui/FileUpload.vue";
import SelectedField from "./Components/Ui/SelectedField.vue";
import DetailDeal from "./Components/DetailDeal.vue";

if (document.getElementById("vue-app")) {

    const app = createApp({})
        .use(ElementPlus)
        .mixin({
            components: {
                DetailDeal, Contenteditable, FileUpload, SelectedField
            }
        });

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }

    app.mount('#vue-app')
}
