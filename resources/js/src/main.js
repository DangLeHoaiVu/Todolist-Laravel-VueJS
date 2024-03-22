import './bootstrap';
import { createApp } from 'vue'
import router from './routers';
import App from './App.vue'
// Vuetify
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import axios from 'axios'
import authenticated from './store/authenticated';
window.axios = axios

const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
    },
})

const app = createApp(App);
app.use(authenticated)
app.use(vuetify)
app.use(router)
app.mount("#app");