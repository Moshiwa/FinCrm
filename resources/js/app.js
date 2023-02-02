import './bootstrap';
import {createApp} from 'vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import App from './App.vue'

if (document.getElementById("vue-app")) {

    const app = createApp({})
        .use(ElementPlus)
        .mixin({
            components: {

            }
        });
    //app.config.globalProperties.$dayjs = dayjs

    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }

    app.mount('#vue-app')
}
