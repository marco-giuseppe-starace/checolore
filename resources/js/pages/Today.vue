<script setup>
import { ref, onMounted } from 'vue';

const loading = ref(true);
const children = ref([]);
const dayLabel = ref('');

const DAY_NAMES = {
    1: 'Lunedì', 2: 'Martedì', 3: 'Mercoledì', 4: 'Giovedì',
    5: 'Venerdì', 6: 'Sabato', 7: 'Domenica',
};

async function load() {
    loading.value = true;
    const { data } = await window.axios.get('/api/today');
    children.value = data.children;
    dayLabel.value = DAY_NAMES[data.day_of_week] ?? '';
    loading.value = false;
}

onMounted(load);
</script>

<template>
    <v-container class="py-8" style="max-width: 720px">
        <p class="text-overline mb-1">{{ dayLabel }}</p>
        <h1 class="text-h4 mb-6">Zaino di oggi</h1>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <v-alert v-else-if="!children.length" type="info" variant="tonal">
            Non hai ancora aggiunto nessun figlio.
            <router-link :to="{ name: 'children' }">Aggiungine uno</router-link> per iniziare.
        </v-alert>

        <template v-else>
            <v-card v-for="child in children" :key="child.id" variant="outlined" class="mb-4">
                <v-card-title>{{ child.name }}</v-card-title>
                <v-card-text>
                    <p v-if="!child.subjects.length" class="text-medium-emphasis">
                        Nessuna materia oggi — niente da preparare, o l'orario non è ancora impostato.
                    </p>
                    <div v-else class="d-flex flex-wrap ga-2">
                        <v-chip
                            v-for="(subject, i) in child.subjects"
                            :key="i"
                            :color="subject.color"
                            variant="flat"
                            size="large"
                        >
                            {{ subject.name }}
                        </v-chip>
                    </div>
                </v-card-text>
            </v-card>
        </template>
    </v-container>
</template>
