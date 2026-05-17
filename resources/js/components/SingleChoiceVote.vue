<script setup>
import { computed, ref } from 'vue';
import { useFetchApi } from '../composables/useFetchApi';

const props = defineProps({
    poll: { type: Object, default: null },
    votedIds: {type: Array, default: null },
    isAuthenticated: {type: Boolean, default: false}
  });

  const poll = ref(props.poll);
  const votedIds = ref(props.votedIds);
  const votedOption = computed(() => props.poll.options.find(o => votedIds.value.includes(o.id)));

  const selectedOption = ref(votedOption.value);
  const { fetchApi } = useFetchApi();

  const canVote = computed(() => {
    return Date.parse(poll.value.ends_at) > Date.now() && (poll.value.allow_vote_change || votedIds.value.length === 0);
  });

  const message = computed(() => {
    if (Date.parse(poll.value.ends_at) < Date.now()) {
        return 'Le sondage est terminé.';
    } else if (!props.isAuthenticated) {
        return 'Connectez-vous pour voter.';
    } else if (!poll.value.allow_vote_change && votedIds.value.length !== 0) {
        return 'Vous avez déjà voté.';
    } else if (poll.value.allow_vote_change && votedIds.value.length !== 0) {
        return 'Modifier le vote';
    } else {
        return 'Voter';
    }
  })

  console.log(canVote.value, message.value)

  function select(option) {
    if (canVote.value) {
        selectedOption.value = selectedOption.value === option ? null : option;
        console.log(selectedOption.value)
    }
  }

  async function vote() {
    try {
        const data = {
            votes: [selectedOption.value]
        }
        const result = await fetchApi({url: `/polls/${poll.value.id}/vote`, method: 'POST', data: data});
        if (result) {
            poll.value = result;
            votedIds.value = [selectedOption.value?.id]
        }
    } catch (error) {
        console.error(error);
    }
  }

</script>

<template>
    <h2 class="text-lg mb-1">{{ props.poll.question }}</h2>
    <p v-if="votedOption" class="italic mb-5">Vous avez voté : {{ votedOption.label }}</p>
    <p v-else class="italic mb-5">Vous n'avez rien voté</p>
    <div class="flex flex-col gap-2">
        <div v-for="option in props.poll.options" class="py-2 px-3 border-2 rounded-md flex items-center justify-between"
        :class="{ 
            'bg-green-200 border-green-800 text-green-800': selectedOption === option,
            'cursor-pointer': canVote
        }" @click="select(option)">
            <p>{{ option.label }}</p>
            <p v-if="votedOption === option">🗸</p>
        </div>
        <button class="px-4 py-2 mt-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md"    
            :class="{ 'hover:bg-teal-700 dark:hover:bg-purple-800 cursor-pointer' : canVote,
            'opacity-40': !canVote }" :disabled="!canVote" @click="vote">
                {{ message }}
        </button>
    </div>

    
</template>
