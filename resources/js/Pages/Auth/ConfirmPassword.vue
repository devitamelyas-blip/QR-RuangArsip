<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Konfirmasi Kata Sandi — Pelita Air" />

        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Konfirmasi Keamanan</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Ini adalah area dengan akses aman. Mohon konfirmasikan kata sandi Anda sebelum melanjutkan.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="password" value="Kata Sandi" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 text-xs font-semibold shadow-xs"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    Konfirmasi Akses
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
