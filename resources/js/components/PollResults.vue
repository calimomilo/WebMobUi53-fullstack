<script setup>

const props = defineProps({
    poll: { type: Object, default: null },
  });

</script>

<template>
    <h2 class="text-lg mb-1">{{ props.poll.question }} : résultats</h2>
    

    
</template>
