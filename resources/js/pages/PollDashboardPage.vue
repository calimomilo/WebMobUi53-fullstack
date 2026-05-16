<script setup>
  import PollTable from '../components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';
import { useHashRoute } from '../composables/useHashRoute';

  const emit = defineEmits('editpoll','polldetails');

</script>

<template>
  <h1 class="text-2xl font-bold dark:text-white">Dashboard des sondages</h1>
  <a href="#create" class="mt-6 block w-full px-4 py-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md hover:bg-teal-700 dark:hover:bg-purple-800 text-center">
            Créer un nouveau sondage
        </a>
  <PollTable class="mt-8" @editpoll="pollId => emit('editpoll', pollId)" @polldetails="pollToken => emit('polldetails', pollToken)"/>
</template>
