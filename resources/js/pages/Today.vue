<script setup>
import { ref, onMounted } from 'vue';
import DailyPackList from '../components/DailyPackList.vue';

const loading = ref(true);
const children = ref([]);
const dayLabel = ref('');
const tomorrow = ref({ dayLabel: '', children: [] });

const DAY_NAMES = {
    1: 'Lunedì', 2: 'Martedì', 3: 'Mercoledì', 4: 'Giovedì',
    5: 'Venerdì', 6: 'Sabato', 7: 'Domenica',
};

async function load() {
    loading.value = true;
    const { data } = await window.axios.get('/api/today');
    children.value = data.children;
    dayLabel.value = DAY_NAMES[data.day_of_week] ?? '';
    tomorrow.value = {
        dayLabel: DAY_NAMES[data.tomorrow.day_of_week] ?? '',
        children: data.tomorrow.children,
    };
    loading.value = false;
}

onMounted(load);
</script>

<template>
    <v-container class="py-8" style="max-width: 720px">
        <p class="text-overline mb-1">{{ dayLabel }}</p>
        <h1 class="text-h4 mb-2">Zaino di oggi</h1>
        <p v-if="!loading && children.some((c) => c.subjects.length)" class="instruction mb-6">
            <v-icon icon="mdi-gesture-tap" size="22" class="mr-1" />
            Tocca ogni colore quando lo metti nello zaino
        </p>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <v-alert v-else-if="!children.length" type="info" variant="tonal">
            Non hai ancora aggiunto nessun figlio.
            <router-link :to="{ name: 'children' }">Aggiungine uno</router-link> per iniziare.
        </v-alert>

        <template v-else>
            <DailyPackList :children="children" when="today" />

            <v-expansion-panels class="mt-6" variant="accordion">
                <v-expansion-panel>
                    <v-expansion-panel-title>
                        <span class="tomorrow-title">
                            <v-icon icon="mdi-backpack" class="mr-2" />
                            Prepara lo zaino di domani ({{ tomorrow.dayLabel }})
                        </span>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                        <p class="instruction mb-4">
                            <v-icon icon="mdi-gesture-tap" size="22" class="mr-1" />
                            Tocca ogni colore quando lo metti nello zaino
                        </p>
                        <DailyPackList :children="tomorrow.children" when="tomorrow" />
                    </v-expansion-panel-text>
                </v-expansion-panel>
            </v-expansion-panels>
        </template>
    </v-container>
</template>

<style scoped>
.instruction {
    display: flex;
    align-items: center;
    font-size: 1.15rem;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
}
.tomorrow-title {
    display: flex;
    align-items: center;
    font-size: 1.05rem;
    font-weight: 600;
}
</style>
