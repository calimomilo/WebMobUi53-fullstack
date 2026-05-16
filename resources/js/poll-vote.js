import './bootstrap';
import { createApp } from 'vue';
import App from './AppVote.vue';

const el = document.getElementById('app');
console.log(el.dataset.props)
const props = JSON.parse(el.dataset.props ?? '{}');

createApp(App, props).mount(el);
