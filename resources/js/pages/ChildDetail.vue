<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const childId = computed(() => route.params.id);

const child = ref(null);
const subjects = ref([]);
const entries = ref([]);
const loading = ref(true);

const subjectDialog = ref(false);
const editingSubject = ref(null);
const subjectName = ref('');
const subjectColor = ref('#c1503f');
const customColor = ref(false);
const savingSubject = ref(false);
const subjectError = ref('');

// A curated set of real notebook/book-cover colors — tapping a swatch is
// far more reliable on a touchscreen than dragging an RGB picker, and it's
// truer to the product anyway: covers come in a limited set of colors.
const PALETTE = [
    '#e63946', '#c1503f', '#d98a34', '#f2b705',
    '#8ac926', '#3e7f5b', '#1b998b', '#2e5e8c',
    '#457b9d', '#6a4c93', '#b5838d', '#ff6f91',
    '#6f4e37', '#4a4e69', '#202a3b', '#fbfaf6',
];

const days = computed(() => {
    const base = [
        { value: 1, label: 'Lun' },
        { value: 2, label: 'Mar' },
        { value: 3, label: 'Mer' },
        { value: 4, label: 'Gio' },
        { value: 5, label: 'Ven' },
    ];
    if (child.value?.include_saturday) {
        base.push({ value: 6, label: 'Sab' });
    }
    return base;
});

const periodsCount = ref(6);
const periods = computed(() => Array.from({ length: periodsCount.value }, (_, i) => i + 1));

function addPeriod() {
    if (periodsCount.value < 12) periodsCount.value++;
}

const entryMap = computed(() => {
    const map = {};
    for (const entry of entries.value) {
        map[`${entry.day_of_week}-${entry.period}`] = entry;
    }
    return map;
});

// Vuetify auto-contrasts text on its own color-aware components, but these
// grid cells are plain chips driven by an arbitrary hex, so contrast is
// computed by hand — otherwise a pale color (e.g. the white/cream swatch)
// would print unreadable white-on-white text.
function textOn(hex) {
    if (!hex) return undefined;
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    const yiq = (r * 299 + g * 587 + b * 114) / 1000;
    return yiq >= 160 ? '#202a3b' : '#ffffff';
}

async function load() {
    loading.value = true;
    const [childRes, subjectsRes, entriesRes] = await Promise.all([
        window.axios.get(`/api/children/${childId.value}`),
        window.axios.get(`/api/children/${childId.value}/subjects`),
        window.axios.get(`/api/children/${childId.value}/timetable`),
    ]);
    child.value = childRes.data;
    subjects.value = subjectsRes.data;
    entries.value = entriesRes.data;
    periodsCount.value = Math.max(6, ...entries.value.map((e) => e.period), 6);
    loading.value = false;
}

async function toggleSaturday(value) {
    await window.axios.put(`/api/children/${childId.value}`, {
        name: child.value.name,
        include_saturday: value,
    });
    child.value.include_saturday = value;
}

function openAddSubject() {
    editingSubject.value = null;
    subjectName.value = '';
    subjectColor.value = PALETTE[0];
    customColor.value = false;
    subjectError.value = '';
    subjectDialog.value = true;
}

function openEditSubject(subject) {
    editingSubject.value = subject;
    subjectName.value = subject.name;
    subjectColor.value = subject.color;
    customColor.value = !PALETTE.includes(subject.color);
    subjectError.value = '';
    subjectDialog.value = true;
}

async function saveSubject() {
    savingSubject.value = true;
    subjectError.value = '';
    try {
        const payload = { name: subjectName.value, color: subjectColor.value };
        if (editingSubject.value) {
            await window.axios.put(`/api/subjects/${editingSubject.value.id}`, payload);
        } else {
            await window.axios.post(`/api/children/${childId.value}/subjects`, payload);
        }
        subjectDialog.value = false;
        await load();
    } catch (e) {
        subjectError.value = e.response?.data?.message ?? 'Errore, riprova.';
    } finally {
        savingSubject.value = false;
    }
}

async function removeSubject(subject) {
    if (!confirm(`Eliminare la materia "${subject.name}"? Verrà rimossa anche dall'orario.`)) return;
    await window.axios.delete(`/api/subjects/${subject.id}`);
    await load();
}

async function setCell(day, period, subjectId) {
    if (subjectId) {
        await window.axios.post(`/api/children/${childId.value}/timetable`, {
            subject_id: subjectId,
            day_of_week: day,
            period,
        });
    } else {
        const existing = entryMap.value[`${day}-${period}`];
        if (existing) {
            await window.axios.delete(`/api/timetable/${existing.id}`);
        }
    }
    await load();
}

onMounted(load);
</script>

<template>
    <v-container class="py-8" style="max-width: 960px">
        <div class="d-flex align-center mb-6">
            <v-btn icon="mdi-arrow-left" variant="text" :to="{ name: 'children' }" class="mr-2" />
            <h1 class="text-h5">{{ child?.name ?? '...' }}</h1>
        </div>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <template v-else>
            <v-card class="mb-6" variant="outlined">
                <v-card-title class="d-flex align-center justify-space-between">
                    Materie e colori
                    <v-btn size="small" color="primary" prepend-icon="mdi-plus" @click="openAddSubject">Aggiungi</v-btn>
                </v-card-title>
                <v-card-text>
                    <v-alert v-if="!subjects.length" type="info" variant="tonal">
                        Aggiungi le materie di {{ child?.name }} e abbina un colore a ciascuna — lo stesso della copertina del quaderno.
                    </v-alert>
                    <div v-else class="d-flex flex-wrap ga-2">
                        <v-chip
                            v-for="subject in subjects"
                            :key="subject.id"
                            :color="subject.color"
                            variant="flat"
                            closable
                            @click="openEditSubject(subject)"
                            @click:close="removeSubject(subject)"
                        >
                            {{ subject.name }}
                        </v-chip>
                    </div>
                </v-card-text>
            </v-card>

            <v-card variant="outlined">
                <v-card-title class="d-flex align-center justify-space-between flex-wrap ga-2">
                    Orario settimanale
                    <v-switch
                        :model-value="child.include_saturday"
                        label="Includi il sabato"
                        color="primary"
                        density="compact"
                        hide-details
                        class="flex-grow-0"
                        @update:model-value="toggleSaturday"
                    />
                </v-card-title>
                <v-card-text>
                    <v-alert v-if="!subjects.length" type="info" variant="tonal">
                        Aggiungi prima almeno una materia per poter compilare l'orario.
                    </v-alert>
                    <template v-else>
                        <div class="table-wrap">
                            <table class="timetable">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th v-for="day in days" :key="day.value">{{ day.label }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="period in periods" :key="period">
                                        <td class="period-label">{{ period }}ª ora</td>
                                        <td v-for="day in days" :key="day.value">
                                            <v-menu>
                                                <template #activator="{ props: menuProps }">
                                                    <v-chip
                                                        v-bind="menuProps"
                                                        class="cell-chip"
                                                        :color="entryMap[`${day.value}-${period}`]?.subject.color"
                                                        :style="{ color: textOn(entryMap[`${day.value}-${period}`]?.subject.color) }"
                                                        variant="flat"
                                                        label
                                                    >
                                                        {{ entryMap[`${day.value}-${period}`]?.subject.name ?? '—' }}
                                                    </v-chip>
                                                </template>
                                                <v-list density="compact">
                                                    <v-list-item
                                                        v-for="subject in subjects"
                                                        :key="subject.id"
                                                        @click="setCell(day.value, period, subject.id)"
                                                    >
                                                        <template #prepend>
                                                            <span class="swatch-dot" :style="{ background: subject.color }" />
                                                        </template>
                                                        <v-list-item-title>{{ subject.name }}</v-list-item-title>
                                                    </v-list-item>
                                                    <v-divider />
                                                    <v-list-item @click="setCell(day.value, period, null)">
                                                        <v-list-item-title class="text-medium-emphasis">Nessuna materia</v-list-item-title>
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <v-btn
                            variant="tonal"
                            size="small"
                            prepend-icon="mdi-plus"
                            class="mt-3"
                            :disabled="periodsCount >= 12"
                            @click="addPeriod"
                        >
                            Aggiungi ora
                        </v-btn>
                    </template>
                </v-card-text>
            </v-card>
        </template>

        <v-dialog v-model="subjectDialog" max-width="420">
            <v-card>
                <v-card-title>{{ editingSubject ? 'Modifica materia' : 'Aggiungi materia' }}</v-card-title>
                <v-card-text>
                    <v-alert v-if="subjectError" type="error" variant="tonal" class="mb-4">{{ subjectError }}</v-alert>
                    <v-text-field v-model="subjectName" label="Materia" class="mb-3" autofocus />

                    <p class="text-caption mb-2">Colore (stesso della copertina del quaderno)</p>
                    <div class="palette-grid mb-3">
                        <button
                            v-for="hex in PALETTE"
                            :key="hex"
                            type="button"
                            class="palette-swatch"
                            :class="{ selected: !customColor && subjectColor === hex }"
                            :style="{ background: hex }"
                            :aria-label="hex"
                            @click="subjectColor = hex; customColor = false"
                        />
                    </div>

                    <v-btn variant="text" size="small" class="mb-2" @click="customColor = !customColor">
                        {{ customColor ? 'Usa uno dei colori sopra' : 'Colore personalizzato…' }}
                    </v-btn>
                    <div v-if="customColor" class="d-flex align-center ga-3">
                        <input v-model="subjectColor" type="color" class="color-input" />
                        <span class="text-body-2">{{ subjectColor }}</span>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="subjectDialog = false">Annulla</v-btn>
                    <v-btn color="primary" :loading="savingSubject" @click="saveSubject">Salva</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<style scoped>
.table-wrap {
    overflow-x: auto;
}
.timetable {
    border-collapse: collapse;
    width: 100%;
    min-width: 640px;
}
.timetable th,
.timetable td {
    padding: 6px;
    text-align: left;
}
.timetable td {
    min-width: 108px;
}
.period-label {
    font-size: 0.85rem;
    white-space: nowrap;
    color: rgb(var(--v-theme-on-surface));
    opacity: 0.7;
}
.cell-chip {
    width: 100%;
    justify-content: center;
    cursor: pointer;
}
.swatch-dot {
    width: 14px;
    height: 14px;
    border-radius: 4px;
    display: inline-block;
    margin-right: 4px;
}
.palette-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 8px;
}
.palette-swatch {
    aspect-ratio: 1;
    border-radius: 50%;
    border: 2px solid rgba(0, 0, 0, 0.12);
    cursor: pointer;
    padding: 0;
}
.palette-swatch.selected {
    outline: 2.5px solid var(--v-theme-primary, #ab3324);
    outline-offset: 2px;
}
.color-input {
    width: 48px;
    height: 40px;
    border: none;
    background: none;
    cursor: pointer;
}
</style>
