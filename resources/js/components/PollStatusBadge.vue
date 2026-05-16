<script setup>
import { ref } from 'vue';

const props = defineProps({
    poll: { type: Object, default: null }
  });

  const pollStatus = ref();
  pollStatus.value = props.poll.is_draft
  ? 'Brouillon'
  : Date.parse(props.poll.ends_at) < Date.now()
  ? 'Terminé'
  : 'En cours';

</script>

<template>
    <div class="py-1 px-3 text-sm rounded-full w-auto inline-block" 
    :class="{ 'bg-green-200 text-green-800' : pollStatus === 'En cours',
        'bg-slate-200 text-slate-800' : pollStatus === 'Brouillon',
        'bg-red-200 text-red-800' : pollStatus === 'Terminé'
     }">{{ pollStatus }}</div>
</template>
