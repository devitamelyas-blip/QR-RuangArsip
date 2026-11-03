<template>
  <span
    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border"
    :class="classes"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="dotClass"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: {
    type: String,
    required: true,
  },
  isOverdue: {
    type: Boolean,
    default: false,
  },
});

const label = computed(() => {
  if (props.isOverdue) {
    return 'Terlambat';
  }
  if (props.status === 'tersedia') {
    return 'Tersedia di Rak';
  }
  if (props.status === 'dipinjam') {
    return 'Sedang Dipinjam';
  }
  return props.status;
});

const classes = computed(() => {
  if (props.isOverdue) {
    return 'bg-rose-50 text-rose-700 border-rose-300';
  }
  if (props.status === 'tersedia') {
    return 'bg-emerald-50 text-emerald-700 border-emerald-300';
  }
  if (props.status === 'dipinjam') {
    return 'bg-amber-50 text-amber-800 border-amber-300';
  }
  return 'bg-slate-100 text-slate-700 border-slate-300';
});

const dotClass = computed(() => {
  if (props.isOverdue) {
    return 'bg-rose-500 animate-pulse';
  }
  if (props.status === 'tersedia') {
    return 'bg-emerald-500';
  }
  if (props.status === 'dipinjam') {
    return 'bg-amber-500';
  }
  return 'bg-slate-400';
});
</script>

