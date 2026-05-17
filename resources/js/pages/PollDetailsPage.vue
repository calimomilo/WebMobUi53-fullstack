<script setup>
    import PollTable from '../components/PollTable.vue';
    import { usePollStore } from '@/stores/usePollStore';
    import { useHashRoute } from '../composables/useHashRoute';
    import PollForm from '../components/PollForm.vue';
    import PollStatusBadge from '../components/PollStatusBadge.vue';
    import { useDateFormatting } from '../composables/useDateFormatting';
    import { ref } from 'vue';
    import { useFetchApi } from '../composables/useFetchApi';
    import SingleChoiceVote from '../components/PollVotes.vue';

    const props = defineProps({
        poll: { type: Object, default: null },
        isAuthenticated: { type: Boolean, default: false },
        isOwner: { type: Boolean, default: false },
        votedIds: { type: Array, default: null },
        loginUrl: { type: String, default: null },
    })
    const { toFormattedDate } = useDateFormatting();

    const emit = defineEmits('godashboard');
    const poll = ref(props.poll);
    const isOwner = props.isOwner;
    const resultsArePublic = props.poll.results_public;

    const showCopied = ref(false);
    const { fetchApi } = useFetchApi();
    const loading = ref(false);

    async function copyLink() {
        const link = window.location.href;
        console.log(link);
        await navigator.clipboard.writeText(link);
        showCopied.value = true;
        setTimeout(() => showCopied.value = false, 3000);
    }

    async function closePoll() {
        loading.value = true;

        try {
            poll.value = await fetchApi({ url: `/polls/${poll.value.id}/close`, method: 'PUT' });
        } catch (error) {
            console.error(error);
        } finally {
            loading.value = false;
        }
    }

</script>

<template>
    <div class="flex align-items-center mb-2">
        <a v-if="isOwner" href="/polls#dashboard" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">← Retour</a>
        <h1 class="text-2xl font-bold dark:text-white ms-4">{{ poll.title ?? poll.question }}</h1>
    </div>
    <article class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
        <div class="p-2 pb-4 mb-4 border-b-2 border-gray-200">
            <p class="inline-block me-3">Fin : {{ toFormattedDate(poll.ends_at) }}</p>
            <PollStatusBadge :poll="poll"></PollStatusBadge>
            <ul class="my-2">
                <li>Choix multiple : {{ poll.allow_multiple_choices ? 'Oui' : 'Non' }}</li>
                <li>Modification du vote : {{ poll.allow_vote_change ? 'Oui' : 'Non' }}</li>
                <li>Résultats : {{ poll.results_public ? 'Publics' : 'Privés' }}</li>
            </ul>
            <div v-if="isOwner || resultsArePublic" class="flex gap-3 items-center">
                <button v-if="isOwner" class="px-3 py-1 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer" @click="copyLink">{{ showCopied? '✓  Lien copié !' : 'Partager le lien' }}</button>
                <button v-if="isOwner || resultsArePublic" class="px-3 py-1 rounded-md bg-teal-600 dark:bg-purple-900 text-white hover:bg-teal-700 dark:hover:bg-purple-800 cursor-pointer" @click="seeResults">Voir les résultats</button>
                <button v-if="isOwner && !poll.ends_at" class="px-3 py-1 rounded-md bg-red-600 dark:bg-red-800 text-white hover:bg-red-700 dark:hover:bg-red-900 cursor-pointer" @click="closePoll">Terminer</button>
            </div>
        </div>
        <SingleChoiceVote :poll="poll" :votedIds="props.votedIds" :isAuthenticated="props.isAuthenticated"></SingleChoiceVote>
    </article>

</template>
