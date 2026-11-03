<template>
  <div v-if="links && links.length > 3" class="flex flex-wrap items-center justify-center gap-1.5 mt-6">
    <template v-for="(link, index) in links" :key="index">
      <div
        v-if="link.url === null"
        class="px-3.5 py-2 text-xs font-medium text-slate-400 bg-slate-100 rounded-lg select-none cursor-not-allowed"
        v-html="cleanLabel(link.label)"
      />
      <Link
        v-else
        :href="link.url"
        class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all"
        :class="link.active 
          ? 'bg-blue-600 text-white shadow-xs' 
          : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
        v-html="cleanLabel(link.label)"
      />
    </template>
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

