<script setup>
import PollDetailsPage from './pages/PollDetailsPage.vue';


    const props = defineProps({
        poll: { type: Object, default: null },
        isAuthenticated: { type: Boolean, default: null },
        isOwner: { type: Boolean, default: null },
        votedIds: { type: Array, default: null },
        loginUrl: { type: String, default: null },
    })

</script>

<template>
    <PollDetailsPage :poll="props.poll" :is-authenticated="props
    .isAuthenticated" :is-owner="props.isOwner" :voted-ids="props.votedIds" :login-url="props.loginUrl"></PollDetailsPage>    
</template>
