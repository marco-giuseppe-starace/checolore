<script setup>
import { ref, onMounted } from 'vue';

const loading = ref(true);
const children = ref([]);

const ALL_DAYS = [
    { value: 1, label: 'Lunedì' },
    { value: 2, label: 'Martedì' },
    { value: 3, label: 'Mercoledì' },
    { value: 4, label: 'Giovedì' },
    { value: 5, label: 'Venerdì' },
    { value: 6, label: 'Sabato' },
];

function daysFor(child) {
    return child.include_saturday ? ALL_DAYS : ALL_DAYS.slice(0, 5);
}

async function load() {
    loading.value = true;
    const { data } = await window.axios.get('/api/week');
    children.value = data;
    loading.value = false;
}

onMounted(load);
</script>

<template>
    <v-container class="py-8" style="max-width: 960px">
        <h1 class="text-h4 mb-1">Settimana</h1>
        <p class="text-body-2 text-medium-emphasis mb-6">
            Tutti i colori della settimana, figlio per figlio — utile per pianificare o comprare le copertine giuste.
        </p>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <v-alert v-else-if="!children.length" type="info" variant="tonal">
            Non hai ancora aggiunto nessun figlio.
            <router-link :to="{ name: 'children' }">Aggiungine uno</router-link> per iniziare.
        </v-alert>

        <template v-else>
            <v-card v-for="child in children" :key="child.id" variant="outlined" class="mb-4">
                <v-card-title>{{ child.name }}</v-card-title>
                <v-card-text>
                    <div class="table-wrap">
                        <table class="week-table">
                            <thead>
                                <tr>
                                    <th v-for="day in daysFor(child)" :key="day.value">{{ day.label }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td v-for="day in daysFor(child)" :key="day.value">
                                        <div v-if="(child.days[day.value] ?? []).length" class="d-flex flex-column ga-1">
                                            <v-chip
                                                v-for="subject in child.days[day.value]"
                                                :key="subject.id"
                                                :color="subject.color"
                                                variant="flat"
                                                size="small"
                                            >
                                                {{ subject.name }}
                                            </v-chip>
                                        </div>
                                        <span v-else class="text-medium-emphasis">—</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </v-card-text>
            </v-card>
        </template>
    </v-container>
</template>

<style scoped>
.table-wrap {
    overflow-x: auto;
}
.week-table {
    border-collapse: collapse;
    width: 100%;
    min-width: 560px;
}
.week-table th,
.week-table td {
    padding: 8px;
    text-align: left;
    vertical-align: top;
}
.week-table th {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: rgb(var(--v-theme-on-surface));
    opacity: 0.65;
    white-space: nowrap;
}
.week-table td {
    min-width: 130px;
}
</style>
