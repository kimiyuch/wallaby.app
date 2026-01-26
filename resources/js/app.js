import './bootstrap';
import { initCounters } from './counter';
import collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(collapse);
});

document.addEventListener('DOMContentLoaded', initCounters);
