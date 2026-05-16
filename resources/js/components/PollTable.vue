<script setup>
  import { usePollStore } from '@/stores/usePollStore';
import { onMounted } from 'vue';
import PollStatusBadge from './PollStatusBadge.vue';

  const { polls, deletePoll } = usePollStore();

  async function delPoll(id) {
    console.log('delete Poll ID:', id);
    await deletePoll(id);
  }

  const emit = defineEmits('editpoll');

  const dateLocale = 'fr-CH';
  const dateOptions = {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "numeric",
    minute: "numeric",
  };

  onMounted(() => {
    const { updatePolls } = usePollStore();
    updatePolls();
  })
</script>

<template>
  <p v-if="polls.length === 0">Aucun sondage.</p>

  <table v-else class="w-full border-collapse text-left">
    <thead>
      <tr>
        <th class="border px-3 py-2">Actions</th>
        <th class="border px-3 py-2">ID</th>
        <th class="border px-3 py-2">Titre</th>
        <th class="border px-3 py-2">Question</th>
        <th class="border px-3 py-2">Statut</th>
        <th class="border px-3 py-2">Debut</th>
        <th class="border px-3 py-2">Fin</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="poll in polls" :key="poll.id">
        <td class="border px-3 py-2">
          <button class="bg-red-500" @click="delPoll(poll.id)">🗑️</button>
          <button v-if="poll.is_draft" class="bg-slate-50 border" @click="emit('editpoll', poll.id)">✏️</button>
          <button v-else class="bg-slate-50 border" @click="emit('polldetails', poll.id)">📊</button>
        </td>
        <td class="border px-3 py-2">{{ poll.id }}</td>
        <td class="border px-3 py-2">{{ poll.title || '-' }}</td>
        <td class="border px-3 py-2">{{ poll.question }}</td>
        <td class="border px-3 py-2">
          <PollStatusBadge :poll="poll"></PollStatusBadge>
        </td>
        <td class="border px-3 py-2">{{ poll.started_at? new Date(poll.started_at).toLocaleString(dateLocale, dateOptions) : '-' }}</td>
        <td class="border px-3 py-2">{{ poll.ends_at? new Date(poll.ends_at).toLocaleString(dateLocale, dateOptions) : '-' }}</td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
  button {
    padding: 0.25rem 0.5rem;
    border: none;
    border-radius: 0.25rem;
    cursor: pointer;
  }
</style>