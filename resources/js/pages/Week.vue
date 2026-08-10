<script setup>
import { ref, onMounted, computed } from 'vue';

const loading = ref(true);
const children = ref([]);

const ALL_DAYS = [
    { value: 1, label: 'Lun' },
    { value: 2, label: 'Mar' },
    { value: 3, label: 'Mer' },
    { value: 4, label: 'Gio' },
    { value: 5, label: 'Ven' },
    { value: 6, label: 'Sab' },
];

function daysFor(child) {
    return child.include_saturday ? ALL_DAYS : ALL_DAYS.slice(0, 5);
}

function periodsFor(child) {
    return Array.from({ length: child.periods_count }, (_, i) => i + 1);
}

function entryMap(child) {
    const map = {};
    for (const entry of child.entries) {
        map[`${entry.day_of_week}-${entry.period}`] = entry.subject;
    }
    return map;
}

const entryMaps = computed(() => Object.fromEntries(children.value.map((c) => [c.id, entryMap(c)])));

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
            Tutte le ore della settimana, figlio per figlio — utile per pianificare o comprare le copertine giuste.
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
                    <p v-if="!child.entries.length" class="text-medium-emphasis">
                        Orario non ancora impostato.
                    </p>
                    <div v-else class="table-wrap">
                        <table class="week-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th v-for="day in daysFor(child)" :key="day.value">{{ day.label }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="period in periodsFor(child)" :key="period">
                                    <td class="period-label">{{ period }}ª ora</td>
                                    <td v-for="day in daysFor(child)" :key="day.value">
                                        <v-chip
                                            v-if="entryMaps[child.id][`${day.value}-${period}`]"
                                            :color="entryMaps[child.id][`${day.value}-${period}`].color"
                                            variant="flat"
                                            size="small"
                                        >
                                            {{ entryMaps[child.id][`${day.value}-${period}`].name }}
                                        </v-chip>
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
    min-width: 640px;
}
.week-table th,
.week-table td {
    padding: 8px;
    text-align: left;
    vertical-align: middle;
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
    min-width: 118px;
}
.period-label {
    font-size: 0.85rem;
    white-space: nowrap;
    color: rgb(var(--v-theme-on-surface));
    opacity: 0.7;
}
</style>
