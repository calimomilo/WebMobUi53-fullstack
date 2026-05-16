<script setup>
  import PollTable from './components/PollTable.vue';
  import { usePollStore } from '@/stores/usePollStore';
  import { useHashRoute } from './composables/useHashRoute';
  import PollDashboardPage from './pages/PollDashboardPage.vue';
  import CreatePollPage from './pages/CreatePollPage.vue';
  import EditPollPage from './pages/EditPollPage.vue';
  import { computed } from 'vue';
import PollDetailsPage from './pages/PollDetailsPage.vue';

  const props = defineProps({
    polls: { type: Array, default: () => [] },
    loginUrl: { type: String, default: null },
    username: { type: String, default: null },
  });

  const { setPolls } = usePollStore();
  setPolls(props.polls);

  const routes = [
    { hash: '#dashboard', component: PollDashboardPage },
    { hash: '#create', component: CreatePollPage },
    { hash: '#edit', component: EditPollPage },
    { hash: '#vote', component: PollDetailsPage }
  ]

  const { currentComponent, currentHashs, currentRoute, navigateTo } = useHashRoute(routes);

</script>

<template>
  <component :is="currentComponent" :pollId="currentHashs[1]" 
  @godashboard="navigateTo('#dashboard')" @editpoll="pollId => navigateTo(`#edit/${pollId}`)" @polldetails="pollToken => navigateTo(`#vote/${pollToken}`)"></component>
</template>
