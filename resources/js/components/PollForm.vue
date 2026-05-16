<script setup>
import { ref } from 'vue';
import { usePollStore } from '@/stores/usePollStore';
import { useFetchApi } from '../composables/useFetchApi';
import { useHashRoute } from '../composables/useHashRoute';

  const props = defineProps({
    poll: { type: Object, default: null }
  });

  const editMode = props.poll !== null;
  const pollDisabled = props.poll?.is_draft === 0;
  const { fetchApi } = useFetchApi();
  const emit = defineEmits('formsubmitted');

  console.log(props.poll)

  function initForm() {
    const form = editMode
    ? {
        id : props.poll.id,
        title : props.poll.title ?? '',
        question : props.poll.question ?? '',
        options : props.poll.options ?? [{label : ''}, {label : ''}],
        allow_multiple_choices : props.poll.allow_multiple_choices ?? false,
        allow_vote_change : props.poll.allow_vote_change ?? false,
        results_public : props.poll.results_public ?? false,
        duration : props.poll.duration ?? null,
        ends_at : props.poll.ends_at ?? null,
        is_draft : props.poll.is_draft ?? true,
    }
    : {
        title : '',
        question : '',
        options : [{label : ''}, {label : ''}],
        allow_multiple_choices : false,
        allow_vote_change : false,
        results_public : false,
        duration : null,
        ends_at : null,
        is_draft : true,
    }

    return form;

  }

  const form = ref(initForm());
  const errors = ref({});
  const loading = ref(false);

  const end_date = defineModel('date', {
    get: () => form.value.ends_at ? form.value.ends_at.split('T')[0] : null,
    set: (val) => {
        const dateTime = `${val}T${end_time.value ?? '00:00'}`;
        form.value.ends_at = dateTime;
        console.log(val, dateTime, form.value);
    }
  });

  const end_time = defineModel('time', {
    get: () => form.value.ends_at ? form.value.ends_at.split('T')[1] : null,
    set: (val) => {
        const date = end_date.value ?? new Date.now().toDateString();
        const dateTime = date + 'T' + val;
        form.value.ends_at = dateTime;
        console.log(form.value);
    }
  });

  function delOption(i) {
    if (form.value.options.length > 2) {
        form.value.options.splice(i, 1);
    }
  }

  function addOption() {
    form.value.options.push({label : ''});
  }

  function validateForm() {
    errors.value = {};

    if (!form.value.question) {
        errors.value.question = 'La question est obligatoire.';
    } else if (form.value.question.length < 3) {
        errors.value.question = 'La question doit faire min. 3 caractères.';
    } else if (form.value.question.length > 255) {
        errors.value.question = 'La question doit faire max. 255 caractères.';
    }

    if (form.value.title.length > 255) {
        errors.value.title = 'La question doit faire max. 255 caractères.';
    }

    if (form.value.options.length < 2) {
        errors.value.options = 'Il faut au moins 2 options.';
    }

    let optionErrorFound = false;
    form.value.options.forEach(option => {
        if (!optionErrorFound) {
            if (!option.label || option.label.length < 1) {
                errors.value.options = 'Les options ne peuvent pas être vides.';
                optionErrorFound = true;
            } else if (option.label.length > 255) {
                errors.value.options = 'Les options doivent faire max. 255 caractères.';
                optionErrorFound = true;
            }
        }
    });

    return Object.keys(errors.value).length === 0;
  }

  const method = editMode? 'PUT' : 'POST';
  const url = editMode? `/polls/${form.value.id}` : `/polls` ;

  async function submitForm() {
    if (validateForm()) {
        loading.value = true;
        console.log(form.value);

        try {
            const poll = await fetchApi({ url: url, method: method, data: form.value });
            emit('formsubmitted', poll);
        } catch (error) {
            console.error(error);
        } finally {
            loading.value = false;
        }
    }
  }

  function saveDraft() {
    form.value.is_draft = true;
    submitForm();
  }

  function publishPoll() {
    form.value.is_draft = false;
    submitForm();
  }


</script>

<template>
    <div v-if="pollDisabled" class="w-full text-center py-3 my-3 rounded-md bg-red-200 text-red-900 dark:bg-red-900 dark:text-white">
        <p>Ce formulaire a été publié et ne peut plus être modifié.</p>
    </div>

    <article class="bg-white dark:bg-slate-800 rounded-lg shadow-md p-6">
        <form @submit.prevent="submitForm" :class="{ 'opacity-40' : pollDisabled }">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Titre (optionnel)
                </label>
                <input id="title" type="text" name="title" v-model="form.title" :disabled="pollDisabled"
                    placeholder="Entrez un titre (optionnel)"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500"
                    :class="{'border-red-500 focus:ring-red-500' : errors.title}">
                    <p v-if="errors.title" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.title }}</p>
            </div>

            <div class="mb-4">
                <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Question
                </label>
                <input id="question" type="text" name="question" v-model="form.question" :disabled="pollDisabled"
                    placeholder="Quelle est votre question ?"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500"
                    :class="{'border-red-500 focus:ring-red-500' : errors.question}">
                    <p v-if="errors.question" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.question }}</p>
            </div>

            <div class="mb-4">
                <label for="options" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Options
                </label>
                <div v-for="(option, index) in form.options" :key="index" class="w-full flex gap-2 mb-3 align-items-center">
                    <input id="options" type="text" name="options" v-model="form.options[index].label" :disabled="pollDisabled"
                        :placeholder="`Option ${index+1}`"
                        class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500"
                        :class="{'border-red-500 focus:ring-red-500' : errors.options}">
                    <button @click="delOption(index)" :disabled="form.options.length <= 2 || pollDisabled" 
                        class="ps-2 text-red-600 dark:text-red-400 disabled:opacity-40">✕</button>
                </div>
                <p v-if="errors.options" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors?.options }}</p>
                <button @click="addOption()" :disabled="pollDisabled" 
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md"
                :class="{ 'hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer' : !pollDisabled }">Ajouter une option</button>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Paramètres
                </label>
                <label class="w-full px-3 py-2 flex gap-3 align-items-center text-gray-900 dark:text-white">
                    <input id="allow_multiple_choices" type="checkbox" name="allow_multiple_choices" v-model="form.allow_multiple_choices" :disabled="pollDisabled"
                    class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500">
                    Plusieurs réponses possibles
                </label>

                <label class="w-full px-3 py-2 flex gap-3 align-items-center text-gray-900 dark:text-white">
                    <input id="allow_vote_change" type="checkbox" name="allow_vote_change" v-model="form.allow_vote_change" :disabled="pollDisabled"
                    class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500">
                    Modification du vote possible
                </label>

                <label class="w-full px-3 py-2 flex gap-3 align-items-center text-gray-900 dark:text-white">
                    <input id="results_public" type="checkbox" name="results_public" v-model="form.results_public" :disabled="pollDisabled"
                    class="bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500">
                    Résultats publics
                </label>
            </div>

            <div class="mb-4">           
                <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fin
                </label>
                <input id="ends_at_date" type="date" name="ends_at_date" v-model="end_date" :disabled="pollDisabled"
                    placeholder="Quelle est votre question ?"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500"
                    :class="{'border-red-500 focus:ring-red-500' : errors.ends_at}">
                <input id="ends_at_time" type="time" name="ends_at_time" v-model="end_time" :disabled="pollDisabled"
                    placeholder="Quelle est votre question ?"
                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:ring-2 focus:border-transparent border-gray-300 dark:border-gray-600 focus:ring-teal-500 dark:focus:ring-purple-500"
                    :class="{'border-red-500 focus:ring-red-500' : errors.ends_at}">
                    <p v-if="errors.ends_at" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ errors.ends_at }}</p>
            </div>

            <div class="flex justify-end gap-3">
                <button @click="saveDraft" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md"
                :class="{ 'hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer' : !pollDisabled }" :disabled="loading || pollDisabled">Enregistrer le Brouillon</button>
                <button @click="publishPoll" class="px-4 py-2 bg-teal-600 dark:bg-purple-900 text-white rounded-md"
                :class="{ 'hover:bg-gray-300 dark:hover:bg-gray-600 cursor-pointer' : !pollDisabled }" :disabled="loading || pollDisabled">Publier le formulaire</button>
            </div>
        </form>
    </article>
  
</template>
