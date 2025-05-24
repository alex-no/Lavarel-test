import { createApp } from 'vue'
import App from './App.vue'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

import { createI18n } from 'vue-i18n'
import messages from './locales'

const i18n = createI18n({
  locale: 'en',
  fallbackLocale: 'en',
  messages
})

const app = createApp(App)
app.use(i18n)
app.mount('#app')

// createApp(App).mount('#app')
