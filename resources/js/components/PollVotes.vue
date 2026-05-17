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

  const canVote = computed(() => {
    return Date.parse(poll.value.ends_at) > Date.now() && (poll.value.allow_vote_change || votedOptions.value.length === 0);
  });

  const message = computed(() => {
    if (Date.parse(poll.value.ends_at) < Date.now()) {
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

  async function vote() {
    if (votedOptions.value.length === 0 && selectedOptions.value.length === 0) {
        
    }
    console.log(selectedOptions.value)

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
        <button class="px-4 py-2 mt-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md"    
            :class="{ 'hover:bg-teal-700 dark:hover:bg-purple-800 cursor-pointer' : canVote,
            'opacity-40': !canVote }" :disabled="!canVote" @click="vote">
                {{ message }}
        </button>
    </div>

    
</template>
