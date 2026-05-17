<script setup>
import { computed, ref } from 'vue';
import { useFetchApi } from '../composables/useFetchApi';

const props = defineProps({
    poll: { type: Object, default: null },
    votedIds: {type: Array, default: null },
    isAuthenticated: {type: Boolean, default: false}
  });

  const poll = ref(props.poll);
  const votedOptions = ref(props.poll.options.filter(o => props.votedIds.includes(o.id)));

  const selectedOptions = ref(votedOptions.value);
  const { fetchApi } = useFetchApi();
  const errors = ref({});
  const loading = ref(false);

  const canVote = computed(() => {
    return Date.parse(poll.value.ends_at) > Date.now() && (poll.value.allow_vote_change || votedOptions.value.length === 0);
  });

  const message = computed(() => {
    if (poll.value.is_draft) {
        return 'Le sondage n\'est pas encore publié.';
    } else if (Date.parse(poll.value.ends_at) < Date.now()) {
        return 'Le sondage est terminé.';
    } else if (!props.isAuthenticated) {
        return 'Connectez-vous pour voter.';
    } else if (!poll.value.allow_vote_change && votedOptions.value.length !== 0) {
        return 'Vous avez déjà voté.';
    } else if (poll.value.allow_vote_change && votedOptions.value.length !== 0) {
        return 'Modifier le vote';
    } else {
        return 'Voter';
    }
  })

  const votedLabels = computed(() => {
    let str = '';
    votedOptions.value.forEach((o, i) => {
        str += i === votedOptions.value.length - 1? o.label : o.label + ', ';
    })
    return str;
  })

  // bug found : modifying directly selectedOptions (.push(), .splice()) caused votedOptions to change too ; found a workaround by reassigning value but didn't figure out why
  function select(option) {
    if (canVote.value) {
        if (poll.value.allow_multiple_choices) {
            if (selectedOptions.value.includes(option)) {
                selectedOptions.value = selectedOptions.value.filter(o => o !== option);
            } else {
                selectedOptions.value = [...selectedOptions.value, option];
            }
        } else {
            selectedOptions.value = selectedOptions.value.includes(option) ? [] : [option];
        }
    }
  }

  function validateVote() {
    errors.value = {};
    if (poll.value.is_draft) {
        errors.value.votes = 'Le sondage n\'est pas encore publié.';
    } else if (Date.parse(poll.value.ends_at) < Date.now()) {
        errors.value.votes = 'Le sondage est terminé.';
    } else if (!props.isAuthenticated) {
        errors.value.votes = 'Connectez-vous pour voter.';
    } else if (!poll.value.allow_vote_change && votedOptions.value.length !== 0) {
        errors.value.votes = 'Vous ne pouvez voter qu\'une seule fois.';
    } else if (votedOptions.value.length === 0 && selectedOptions.value.length === 0) {
        errors.value.votes = 'Au moins une option doit être sélectionnée.';
    } else if (!poll.value.allow_multiple_choices && selectedOptions.value.length > 1) {
        errors.value.votes = 'Vous ne pouvez sélectionner qu\'une seule option.';
    }

    return Object.keys(errors.value).length === 0;
  }

  async function vote() {
    if (validateVote()) {
        loading.value = true;
        try {
            const data = {
                votes: selectedOptions.value
            }
            const result = await fetchApi({url: `/polls/${poll.value.id}/vote`, method: 'POST', data: data});
            if (result) {
                poll.value = result;
                votedOptions.value = selectedOptions.value
            }
        } catch (error) {
            console.error(error);
        } finally {
            loading.value = false;
        }
    }
  }

</script>

<template>
    <h2 class="text-lg mb-1">{{ props.poll.question }}</h2>
    <p v-if="votedOptions.length !== 0" class="italic mb-5">Vous avez voté : {{ votedLabels }}</p>
    <p v-else class="italic mb-5">Vous n'avez rien voté</p>
    <div class="flex flex-col gap-2">
        <div v-for="option in props.poll.options" class="py-2 px-3 border-2 rounded-md flex items-center justify-between"
        :class="{ 
            'bg-green-200 dark:bg-green-900 border-green-800 dark:border-green-300 text-green-800 dark:text-green-300': selectedOptions.includes(option),
            'cursor-pointer': canVote
        }" @click="select(option)">
            <p>{{ option.label }}</p>
            <p v-if="votedOptions.includes(option)">🗸</p>
        </div>
        <p v-if="errors.votes" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.votes }}</p>
        <button class="px-4 py-2 mt-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md"    
            :class="{ 'hover:bg-teal-700 dark:hover:bg-purple-800 cursor-pointer' : canVote,
            'opacity-40': !canVote }" :disabled="!canVote" @click="vote">
                {{ message }}
        </button>
    </div>

    
</template>
