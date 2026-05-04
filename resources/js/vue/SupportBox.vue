<template>
  <a
    v-if="habilitado"
    :href="hrefFinal"
    target="_blank"
    rel="noopener noreferrer"
    class="fixed bottom-6 left-6 z-[60] w-72 rounded-2xl bg-emerald-500 p-4 text-slate-950 shadow-2xl shadow-emerald-900/40 transition hover:-translate-y-0.5"
  >
    <div class="text-xs font-semibold uppercase tracking-wide">Suporte Premium</div>
    <div class="mt-1 text-sm font-medium">Atendimento comercial e técnico</div>
    <div class="mt-2 text-xs opacity-90">Clique para falar no WhatsApp</div>
  </a>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  whatsapp: { type: String, default: '' },
});

const digits = computed(() => (props.whatsapp || '').replace(/\D/g, ''));
const habilitado = computed(() => digits.value.length >= 12);
const hrefFinal = computed(() => `https://wa.me/${digits.value}?text=Ol%C3%A1%2C%20preciso%20de%20suporte.`);
</script>
