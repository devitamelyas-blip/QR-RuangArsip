<template>
  <div v-if="links && links.length > 3" class="flex flex-wrap items-center justify-center sm:justify-between gap-2 pt-3">
    <div class="text-xs text-slate-500 hidden sm:block">
      Navigasi halaman data
    </div>
    <div class="inline-flex items-center space-x-1">
      <template v-for="(link, index) in links" :key="index">
        <div
          v-if="link.url === null"
          class="px-2.5 py-1.5 text-xs font-medium text-slate-400 bg-slate-50 border border-slate-200 rounded-md select-none cursor-not-allowed opacity-60"
          v-html="cleanLabel(link.label)"
        />
        <Link
          v-else
          :href="link.url"
          class="px-3 py-1.5 text-xs font-medium rounded-md transition border"
          :class="link.active 
            ? 'bg-[#0A2540] text-white border-[#0A2540] font-semibold' 
            : 'bg-white text-slate-700 hover:bg-slate-50 border-slate-200'"
          v-html="cleanLabel(link.label)"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: {
    type: Array,
    default: () => [],
  },
});

function cleanLabel(label) {
  if (label.includes('&laquo;') || label.includes('Previous')) {
    return '&larr; Sebelumnya';
  }
  if (label.includes('&raquo;') || label.includes('Next')) {
    return 'Berikutnya &rarr;';
  }
  return label;
}
</script>
