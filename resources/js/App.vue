<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

async function logout() {
    await window.axios.post('/logout');
    window.location.href = '/';
}
</script>

<template>
    <v-app>
        <v-app-bar flat color="surface" border>
            <v-app-bar-title>
                <router-link to="/" class="brand-link">CheColore</router-link>
            </v-app-bar-title>
            <v-btn variant="text" :to="{ name: 'today' }">Oggi</v-btn>
            <v-btn variant="text" :to="{ name: 'week' }">Settimana</v-btn>
            <v-btn variant="text" :to="{ name: 'children' }">Figli</v-btn>
            <v-btn variant="text" @click="logout">Esci</v-btn>
        </v-app-bar>

        <v-main>
            <router-view />
        </v-main>
    </v-app>
</template>

<style scoped>
.brand-link {
    color: inherit;
    text-decoration: none;
    font-weight: 600;
}
</style>
