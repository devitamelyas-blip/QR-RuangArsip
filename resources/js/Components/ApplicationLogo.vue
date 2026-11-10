<template>
  <div class="flex items-center space-x-3 select-none">
    <!-- Official Pelita Air Symbol (Red, Olive Green, Blue) -->
    <div
      class="shrink-0 flex items-center justify-center transition-transform duration-200"
      :class="wrapperClass"
    >
      <img
        src="/images/pelita-symbol.png"
        alt="Pelita Air"
        class="object-contain"
        :class="imageClass"
      />
    </div>

    <!-- Official Brand & Division Typography -->
    <div v-if="!iconOnly" class="flex flex-col leading-tight min-w-0">
      <div class="flex items-center space-x-1.5">
        <span
          class="font-black tracking-wider text-sm sm:text-base font-sans leading-none"
          :class="isDark ? 'text-white' : 'text-[#003865]'"
        >
          PELITA AIR
        </span>
        <span class="w-1 h-3 rounded-full bg-[#D71920]"></span>
      </div>

      <span
        v-if="showDivision"
        class="text-[9.5px] uppercase font-bold tracking-wider mt-0.5"
        :class="isDark ? 'text-slate-200' : 'text-slate-700'"
      >
        Supply Chain Management
      </span>

      <span
        class="text-[8.5px] uppercase font-medium tracking-widest truncate mt-0.2"
        :class="isDark ? 'text-slate-400' : 'text-slate-500'"
      >
        Document Management System
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  iconOnly: {
    type: Boolean,
    default: false,
  },
  isDark: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'md', // sm, md, lg
  },
  showDivision: {
    type: Boolean,
    default: true,
  },
});

const wrapperClass = computed(() => {
  if (props.size === 'sm') return 'w-7 h-7';
  if (props.size === 'lg') return 'w-11 h-11';
  return 'w-9 h-9';
});

const imageClass = computed(() => {
  if (props.size === 'sm') return 'w-7 h-7';
  if (props.size === 'lg') return 'w-11 h-11';
  return 'w-9 h-9';
});
</script>
