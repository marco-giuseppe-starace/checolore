import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    { path: '/', name: 'dashboard', component: () => import('./pages/Dashboard.vue'), meta: { title: 'Home' } },
    // Catch-all: an unmatched path lands back on Home instead of a blank
    // <router-view>.
    { path: '/:pathMatch(.*)*', redirect: '/' },
];

export default createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to) {
        if (to.hash) {
            return { el: to.hash, behavior: 'smooth' };
        }
        return { top: 0 };
    },
});
