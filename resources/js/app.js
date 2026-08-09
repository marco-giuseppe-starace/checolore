import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import App from './App.vue';
import vuetify from './plugins/vuetify';
import router from './router';

createApp(App).use(vuetify).use(router).mount('#app');

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // PWA install support is a progressive enhancement — a failed
            // registration shouldn't break the app.
        });
    });
}
