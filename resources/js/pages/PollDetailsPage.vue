<script setup>
    import PollTable from '../components/PollTable.vue';
    import { usePollStore } from '@/stores/usePollStore';
    import { useHashRoute } from '../composables/useHashRoute';
    import PollForm from '../components/PollForm.vue';
    import PollStatusBadge from '../components/PollStatusBadge.vue';
    import { useDateFormatting } from '../composables/useDateFormatting';

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

</script>

<template>
    <div class="flex gap-3 align-items-center mb-2">
        <a v-if="props.isOwner" href="/polls#dashboard" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">← Retour</a>
        <h1 class="text-2xl font-bold dark:text-white">{{ poll.title ?? poll.question }}</h1>
    </div>
    <div class="p-6">
        <p class="inline-block me-3">Fin : {{ toFormattedDate(poll.ends_at) }}</p>
        <PollStatusBadge :poll="poll"></PollStatusBadge>
        <ul>
            <li>Choix multiple : {{ poll.allow_multiple_choices ? 'Oui' : 'Non' }}</li>
            <li>Modification du vote : {{ poll.allow_vote_change ? 'Oui' : 'Non' }}</li>
            <li>Résultats : {{ poll.results_public ? 'Publics' : 'Privés' }}</li>
        </ul>
    </div>
    

</template>
