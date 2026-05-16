<script setup>
import { computed, ref } from 'vue';
import { usePolling } from '../composables/usePolling';

const props = defineProps({
    poll: { type: Object, default: null }
  });
  
  const dateNow = ref(Date.now());

  const pollStatus = computed(() =>
  props.poll.is_draft
  ? 'Brouillon'
  : Date.parse(props.poll.ends_at) < dateNow.value
  ? 'Terminé'
  : 'En cours');

  usePolling(() => dateNow.value = Date.now());


</script>

<template>
    <div class="py-1 px-3 text-sm rounded-full w-auto inline-block" 
    :class="{ 'bg-green-200 text-green-800' : pollStatus === 'En cours',
        'bg-slate-200 text-slate-800' : pollStatus === 'Brouillon',
        'bg-red-200 text-red-800' : pollStatus === 'Terminé'
     }">{{ pollStatus }}</div>
</template>
