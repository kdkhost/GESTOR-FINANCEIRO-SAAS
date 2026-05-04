<template>
  <button
    v-show="visible"
    @click="scrollTop"
    class="fixed bottom-6 right-6 z-[60] flex h-12 w-12 items-center justify-center rounded-full bg-slate-900/90 text-white ring-1 ring-white/20 shadow-xl shadow-black/40 backdrop-blur transition-all duration-200 hover:-translate-y-0.5 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300/70"
    aria-label="Voltar ao topo"
    title="Voltar ao topo"
  >
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V6m0 0-5 5m5-5 5 5" />
    </svg>
  </button>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const visible = ref(false);
const onScroll = () => { visible.value = window.scrollY > 320; };
const scrollTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
});

onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>
