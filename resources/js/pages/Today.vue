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

function packedCount(child) {
    return child.subjects.filter((s) => s.confirmed).length;
}

async function toggle(child, subject) {
    // Flip immediately — waiting for the round-trip before showing the
    // check makes the tap feel unresponsive, which defeats the point of
    // an instant-feedback ritual.
    subject.confirmed = !subject.confirmed;
    try {
        const { data } = await window.axios.post(`/api/children/${child.id}/pack/${subject.id}`);
        subject.confirmed = data.confirmed;
    } catch (e) {
        subject.confirmed = !subject.confirmed;
    }
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
            <v-card v-for="child in children" :key="child.id" variant="outlined" class="mb-4">
                <v-card-title class="d-flex align-center justify-space-between">
                    {{ child.name }}
                    <span v-if="child.subjects.length" class="text-caption text-medium-emphasis">
                        {{ packedCount(child) }} di {{ child.subjects.length }} pronti
                    </span>
                </v-card-title>
                <v-card-text>
                    <p v-if="!child.subjects.length" class="text-medium-emphasis">
                        Nessuna materia oggi — niente da preparare, o l'orario non è ancora impostato.
                    </p>
                    <div v-else class="d-flex flex-wrap ga-2">
                        <v-chip
                            v-for="subject in child.subjects"
                            :key="subject.id"
                            :color="subject.color"
                            variant="flat"
                            size="large"
                            class="pack-chip"
                            :class="{ 'pack-chip--done': subject.confirmed }"
                            @click="toggle(child, subject)"
                        >
                            <v-icon v-if="subject.confirmed" icon="mdi-check-bold" start size="18" />
                            <span :class="{ 'text-decoration-line-through': subject.confirmed }">{{ subject.name }}</span>
                        </v-chip>
                    </div>
                </v-card-text>
            </v-card>
        </template>
    </v-container>
</template>

<style scoped>
.pack-chip {
    cursor: pointer;
}
.pack-chip--done {
    opacity: 0.55;
}
.instruction {
    display: flex;
    align-items: center;
    font-size: 1.15rem;
    font-weight: 600;
    color: rgb(var(--v-theme-primary));
}
</style>
