<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="rounded-md border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:border-[#0A2540] focus:ring-[#0A2540] transition"
        v-model="model"
        ref="input"
    />
</template>
