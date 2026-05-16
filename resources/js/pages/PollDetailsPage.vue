<script setup>
    import PollTable from '../components/PollTable.vue';
    import { usePollStore } from '@/stores/usePollStore';
    import { useHashRoute } from '../composables/useHashRoute';
    import PollForm from '../components/PollForm.vue';
    import PollStatusBadge from '../components/PollStatusBadge.vue';
    import { useDateFormatting } from '../composables/useDateFormatting';
import { ref } from 'vue';

    const props = defineProps({
        poll: { type: Object, default: null },
        isAuthenticated: { type: Boolean, default: null },
        isOwner: { type: Boolean, default: null },
        votedIds: { type: Array, default: null },
        loginUrl: { type: String, default: null },
    })
    const { toFormattedDate } = useDateFormatting();

    const emit = defineEmits('godashboard');
    const poll = props.poll;
    const isOwner = props.isOwner;
    const showCopied = ref(false);

    async function copyLink() {
        const link = window.location.href;
        console.log(link);
        await navigator.clipboard.writeText(link);
        showCopied.value = true;
        setTimeout(() => showCopied.value = false, 3000);
    }

</script>

<template>
    <div class="flex align-items-center mb-2">
        <a v-if="isOwner" href="/polls#dashboard" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">← Retour</a>
        <h1 class="text-2xl font-bold dark:text-white ms-4">{{ poll.title ?? poll.question }}</h1>
    </div>
    <article class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
        <div class="p-6">
            <p class="inline-block me-3">Fin : {{ toFormattedDate(poll.ends_at) }}</p>
            <PollStatusBadge :poll="poll"></PollStatusBadge>
            <ul class="my-2">
                <li>Choix multiple : {{ poll.allow_multiple_choices ? 'Oui' : 'Non' }}</li>
                <li>Modification du vote : {{ poll.allow_vote_change ? 'Oui' : 'Non' }}</li>
                <li>Résultats : {{ poll.results_public ? 'Publics' : 'Privés' }}</li>
            </ul>
            <div v-if="isOwner" class="flex gap-3 items-center">
                <p>Actions :</p>
                <button class="px-3 py-1 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer" @click="copyLink">{{ showCopied? '✓  Lien copié !' : 'Partager le lien' }}</button>
                <button v-if="!poll.ends_at" class="px-3 py-1 rounded-md bg-red-600 dark:bg-red-800 text-white hover:bg-red-700 dark:hover:bg-red-900 cursor-pointer">Terminer</button>
            </div>
        </div>
    </article>

</template>
