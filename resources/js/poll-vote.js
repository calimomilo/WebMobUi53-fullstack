import './bootstrap';
import { createApp } from 'vue';
import App from './AppVote.vue';

const el = document.getElementById('app');
const props = JSON.parse(el.dataset.props ?? '{}');

createApp(App, props).mount(el);
