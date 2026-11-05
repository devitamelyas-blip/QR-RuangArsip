<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs"
  >
    <div
      class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-md border border-slate-100 p-6 text-left"
    >
      <div class="flex items-center space-x-3 mb-4">
        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" :class="iconBgClass">
          <slot name="icon">
            <svg class="w-6 h-6" :class="iconColorClass" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </slot>
        </div>
        <h3 class="text-lg font-bold text-slate-900">
          {{ title }}
        </h3>
      </div>

      <p class="text-sm text-slate-600 mb-6 leading-relaxed">
        {{ message }}
      </p>

      <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 gap-2">
        <button
          type="button"
          @click="$emit('close')"
          class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition active:scale-95"
        >
          {{ cancelText }}
        </button>
        <button
          type="button"
          @click="$emit('confirm')"
          :disabled="loading"
          class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition active:scale-95 shadow-sm disabled:opacity-50"
          :class="confirmButtonClass"
        >
          <span v-if="loading" class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Memproses...
          </span>
          <span v-else>{{ confirmText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Konfirmasi Tindakan',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
  },
  confirmText: {
    type: String,
    default: 'Ya, Lanjutkan',
  },
  cancelText: {
    type: String,
    default: 'Batal',
  },
  variant: {
    type: String,
    default: 'emerald',
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['close', 'confirm']);

const iconBgClass = computed(() => {
  if (props.variant === 'rose') return 'bg-rose-100';
  if (props.variant === 'emerald') return 'bg-emerald-100';
  return 'bg-blue-100';
});

const iconColorClass = computed(() => {
  if (props.variant === 'rose') return 'text-rose-600';
  if (props.variant === 'emerald') return 'text-emerald-600';
  return 'text-blue-600';
});

const confirmButtonClass = computed(() => {
  if (props.variant === 'rose') return 'bg-rose-600 hover:bg-rose-700';
  if (props.variant === 'emerald') return 'bg-emerald-600 hover:bg-emerald-700';
  return 'bg-blue-600 hover:bg-blue-700';
});
</script>

