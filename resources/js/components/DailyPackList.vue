<script setup>
const props = defineProps({
    children: { type: Array, required: true },
    when: { type: String, default: 'today' }, // 'today' | 'tomorrow'
});

function packedCount(child) {
    return child.subjects.filter((s) => s.confirmed).length;
}

async function toggle(child, subject) {
    // Flip immediately — waiting for the round-trip before showing the
    // check makes the tap feel unresponsive, which defeats the point of
    // an instant-feedback ritual.
    subject.confirmed = !subject.confirmed;
    try {
        const { data } = await window.axios.post(`/api/children/${child.id}/pack/${subject.id}`, {
            when: props.when,
        });
        subject.confirmed = data.confirmed;
    } catch (e) {
        subject.confirmed = !subject.confirmed;
    }
}
</script>

<template>
    <v-card v-for="child in children" :key="child.id" variant="outlined" class="mb-4">
        <v-card-title class="d-flex align-center justify-space-between">
            {{ child.name }}
            <span v-if="child.subjects.length" class="text-caption text-medium-emphasis">
                {{ packedCount(child) }} di {{ child.subjects.length }} pronti
            </span>
        </v-card-title>
        <v-card-text>
            <p v-if="!child.subjects.length" class="text-medium-emphasis">
                Nessuna materia — niente da preparare, o l'orario non è ancora impostato.
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

<style scoped>
.pack-chip {
    cursor: pointer;
}
.pack-chip--done {
    opacity: 0.55;
}
</style>
