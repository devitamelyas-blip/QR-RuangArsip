<template>
  <span
    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium border"
    :class="classes"
  >
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 shrink-0" :class="dotClass"></span>
    <span>{{ label }}</span>
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
    return 'bg-rose-50/80 text-rose-700 border-rose-200';
  }
  if (props.status === 'tersedia') {
    return 'bg-emerald-50/80 text-emerald-700 border-emerald-200';
  }
  if (props.status === 'dipinjam') {
    return 'bg-amber-50/80 text-amber-700 border-amber-200';
  }
  return 'bg-slate-50 text-slate-700 border-slate-200';
});

const dotClass = computed(() => {
  if (props.isOverdue) {
    return 'bg-rose-500';
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
