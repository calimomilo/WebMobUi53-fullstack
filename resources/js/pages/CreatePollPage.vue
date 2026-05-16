<script setup>
  import PollTable from '../components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';
import { useHashRoute } from '../composables/useHashRoute';
import PollForm from '../components/PollForm.vue';

  const emit = defineEmits('formsubmitted');

</script>

<template>
    <div class="flex gap-3 align-items-center mb-2">
        <a href="#dashboard" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer">← Retour</a>
        <h1 class="text-2xl font-bold dark:text-white">Nouveau sondage</h1>
    </div>

  <PollForm @formsubmitted="(poll) => emit('formsubmitted', poll)"/>
</template>
