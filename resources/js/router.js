import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    { path: '/', name: 'today', component: () => import('./pages/Today.vue'), meta: { title: 'Oggi' } },
    { path: '/children', name: 'children', component: () => import('./pages/Children.vue'), meta: { title: 'Figli' } },
    { path: '/children/:id', name: 'child-detail', component: () => import('./pages/ChildDetail.vue'), meta: { title: 'Materie e orario' } },
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
