<template>
  <button
    v-show="visible"
    @click="scrollTop"
    class="fixed bottom-6 right-6 z-[60] rounded-full bg-slate-900/90 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/20 backdrop-blur transition hover:bg-blue-500"
    aria-label="Voltar ao topo"
  >
    Topo
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

