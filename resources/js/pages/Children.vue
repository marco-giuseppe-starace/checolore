<script setup>
import { ref, onMounted } from 'vue';

const children = ref([]);
const loading = ref(true);
const dialog = ref(false);
const saving = ref(false);
const editing = ref(null);
const name = ref('');
const error = ref('');

async function load() {
    loading.value = true;
    const { data } = await window.axios.get('/api/children');
    children.value = data;
    loading.value = false;
}

function openAdd() {
    editing.value = null;
    name.value = '';
    error.value = '';
    dialog.value = true;
}

function openEdit(child) {
    editing.value = child;
    name.value = child.name;
    error.value = '';
    dialog.value = true;
}

async function save() {
    saving.value = true;
    error.value = '';
    try {
        if (editing.value) {
            await window.axios.put(`/api/children/${editing.value.id}`, { name: name.value });
        } else {
            await window.axios.post('/api/children', { name: name.value });
        }
        dialog.value = false;
        await load();
    } catch (e) {
        error.value = e.response?.data?.message ?? 'Errore, riprova.';
    } finally {
        saving.value = false;
    }
}

async function remove(child) {
    if (!confirm(`Eliminare ${child.name} e tutto il suo orario/materie?`)) return;
    await window.axios.delete(`/api/children/${child.id}`);
    await load();
}

onMounted(load);
</script>

<template>
    <v-container class="py-8" style="max-width: 720px">
        <div class="d-flex align-center justify-space-between mb-4">
            <h1 class="text-h5">I tuoi figli</h1>
            <v-btn color="primary" prepend-icon="mdi-plus" @click="openAdd">Aggiungi</v-btn>
        </div>

        <v-progress-linear v-if="loading" indeterminate class="mb-4" />

        <v-alert v-else-if="!children.length" type="info" variant="tonal">
            Nessun figlio ancora. Aggiungine uno per iniziare a impostare orario e colori.
        </v-alert>

        <v-list v-else lines="two" class="rounded border">
            <v-list-item
                v-for="child in children"
                :key="child.id"
                :to="{ name: 'child-detail', params: { id: child.id } }"
                :title="child.name"
                subtitle="Materie, colori e orario"
            >
                <template #append>
                    <v-btn icon="mdi-pencil" variant="text" size="small" @click.stop.prevent="openEdit(child)" />
                    <v-btn icon="mdi-delete" variant="text" size="small" @click.stop.prevent="remove(child)" />
                </template>
            </v-list-item>
        </v-list>

        <v-dialog v-model="dialog" max-width="420">
            <v-card>
                <v-card-title>{{ editing ? 'Modifica figlio' : 'Aggiungi figlio' }}</v-card-title>
                <v-card-text>
                    <v-alert v-if="error" type="error" variant="tonal" class="mb-4">{{ error }}</v-alert>
                    <v-text-field v-model="name" label="Nome" autofocus @keyup.enter="save" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn variant="text" @click="dialog = false">Annulla</v-btn>
                    <v-btn color="primary" :loading="saving" @click="save">Salva</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
