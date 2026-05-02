/**
 * FinanceiroSaaS — JavaScript principal
 * Importações globais e módulos Vue/React
 */

import './bootstrap';

// Vue 3 — componentes do dashboard
import { createApp } from 'vue';

// Importa componentes Vue
import DashboardKpis  from '~vue/DashboardKpis.vue';
import SaudeGauge     from '~vue/SaudeGauge.vue';
import FiltrosPeriodo from '~vue/FiltrosPeriodo.vue';

// Monta componentes Vue em elementos com data-vue-component
document.querySelectorAll('[data-vue-component]').forEach(el => {
    const componente = el.dataset.vueComponent;
    const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
    const mapa = { DashboardKpis, SaudeGauge, FiltrosPeriodo };
    if (mapa[componente]) {
        createApp(mapa[componente], props).mount(el);
    }
});

// Exporta utilitários globais
export { default as ajax } from './modules/ajax.js';
