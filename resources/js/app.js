import './bootstrap';
import { createApp } from 'vue';
import { createRoot } from 'react-dom/client';
import PremiumLanding from '~react/PremiumLanding.jsx';
import DashboardKpis from '~vue/DashboardKpis.vue';
import SaudeGauge from '~vue/SaudeGauge.vue';
import FiltrosPeriodo from '~vue/FiltrosPeriodo.vue';
import BackToTop from '~vue/BackToTop.vue';
import SupportBox from '~vue/SupportBox.vue';

const rootLanding = document.getElementById('premium-landing-root');
if (rootLanding) {
    const propsRaw = rootLanding.getAttribute('data-props');
    const props = propsRaw ? JSON.parse(propsRaw) : {};
    createRoot(rootLanding).render(PremiumLanding({ props }));
}

document.querySelectorAll('[data-vue-component]').forEach((el) => {
    const componente = el.dataset.vueComponent;
    const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
    const mapa = { DashboardKpis, SaudeGauge, FiltrosPeriodo };
    if (mapa[componente]) createApp(mapa[componente], props).mount(el);
});

const backToTopRoot = document.getElementById('vue-back-to-top');
if (backToTopRoot) createApp(BackToTop).mount(backToTopRoot);

const supportRoot = document.getElementById('vue-support-box');
if (supportRoot) {
    createApp(SupportBox, { whatsapp: supportRoot.dataset.whatsapp || '' }).mount(supportRoot);
}

export { default as ajax } from './modules/ajax.js';

