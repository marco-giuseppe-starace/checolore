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
const savingSubject = ref(false);
const subjectError = ref('');

const days = [
    { value: 1, label: 'Lun' },
    { value: 2, label: 'Mar' },
    { value: 3, label: 'Mer' },
    { value: 4, label: 'Gio' },
    { value: 5, label: 'Ven' },
];
const periods = [1, 2, 3, 4, 5, 6];

const entryMap = computed(() => {
    const map = {};
    for (const entry of entries.value) {
        map[`${entry.day_of_week}-${entry.period}`] = entry;
    }
    return map;
});

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
    loading.value = false;
}

function openAddSubject() {
    editingSubject.value = null;
    subjectName.value = '';
    subjectColor.value = '#c1503f';
    subjectError.value = '';
    subjectDialog.value = true;
}

function openEditSubject(subject) {
    editingSubject.value = subject;
    subjectName.value = subject.name;
    subjectColor.value = subject.color;
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
                <v-card-title>Orario settimanale</v-card-title>
                <v-card-text>
                    <v-alert v-if="!subjects.length" type="info" variant="tonal">
                        Aggiungi prima almeno una materia per poter compilare l'orario.
                    </v-alert>
                    <div v-else class="table-wrap">
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
                                        <v-select
                                            :model-value="entryMap[`${day.value}-${period}`]?.subject_id ?? null"
                                            :items="subjects"
                                            item-title="name"
                                            item-value="id"
                                            density="compact"
                                            variant="outlined"
                                            hide-details
                                            clearable
                                            placeholder="—"
                                            @update:model-value="(val) => setCell(day.value, period, val)"
                                        >
                                            <template #selection="{ item }">
                                                <v-chip size="small" :color="item.raw.color" variant="flat">{{ item.raw.name }}</v-chip>
                                            </template>
                                        </v-select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </v-card-text>
            </v-card>
        </template>

        <v-dialog v-model="subjectDialog" max-width="420">
            <v-card>
                <v-card-title>{{ editingSubject ? 'Modifica materia' : 'Aggiungi materia' }}</v-card-title>
                <v-card-text>
                    <v-alert v-if="subjectError" type="error" variant="tonal" class="mb-4">{{ subjectError }}</v-alert>
                    <v-text-field v-model="subjectName" label="Materia" class="mb-2" autofocus />
                    <p class="text-caption mb-2">Colore (stesso della copertina del quaderno)</p>
                    <input v-model="subjectColor" type="color" class="color-input" />
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
    min-width: 120px;
}
.period-label {
    font-size: 0.85rem;
    white-space: nowrap;
    color: rgb(var(--v-theme-on-surface));
    opacity: 0.7;
}
.color-input {
    width: 100%;
    height: 40px;
    border: none;
    background: none;
    cursor: pointer;
}
</style>
