import { createApp } from 'vue'

import App from './app/App.vue'
import { pinia } from './app/pinia'
import { router } from './app/router'
import './assets/styles/main.css'

createApp(App).use(pinia).use(router).mount('#app')
