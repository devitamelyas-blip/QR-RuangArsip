<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto p-4 flex items-center justify-center bg-slate-900/40 backdrop-blur-[2px]"
  >
    <div
      class="bg-white rounded-lg overflow-hidden shadow-lg transform transition-all w-full max-w-md border border-slate-200 text-left"
    >
      <div class="p-5 border-b border-slate-100 flex items-start space-x-3.5">
        <div class="w-9 h-9 rounded-md flex items-center justify-center shrink-0 border" :class="iconBgClass">
          <slot name="icon">
            <svg class="w-5 h-5" :class="iconColorClass" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </slot>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="text-sm font-semibold text-slate-900">
            {{ title }}
          </h3>
          <p class="text-xs text-slate-500 mt-1 leading-relaxed">
            {{ message }}
          </p>
        </div>
      </div>

      <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end space-x-2.5">
        <button
          type="button"
          @click="$emit('close')"
          class="px-3.5 py-2 rounded-md border border-slate-300 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition focus:outline-none"
        >
          {{ cancelText }}
        </button>
        <button
          type="button"
          @click="$emit('confirm')"
          :disabled="loading"
          class="px-4 py-2 rounded-md text-xs font-semibold text-white transition focus:outline-none disabled:opacity-50 flex items-center shadow-xs"
          :class="confirmButtonClass"
        >
          <svg
            v-if="loading"
            class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          <span>{{ loading ? 'Memproses...' : confirmText }}</span>
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
  if (props.variant === 'rose') return 'bg-rose-50 border-rose-100';
  if (props.variant === 'emerald') return 'bg-emerald-50 border-emerald-100';
  return 'bg-blue-50 border-blue-100';
});

const iconColorClass = computed(() => {
  if (props.variant === 'rose') return 'text-rose-600';
  if (props.variant === 'emerald') return 'text-emerald-600';
  return 'text-blue-600';
});

const confirmButtonClass = computed(() => {
  if (props.variant === 'rose') return 'bg-rose-600 hover:bg-rose-700';
  if (props.variant === 'emerald') return 'bg-emerald-600 hover:bg-emerald-700';
  return 'bg-[#0A2540] hover:bg-[#14385C]';
});
</script>
