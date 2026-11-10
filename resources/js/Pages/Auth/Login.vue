<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk Akun Petugas Arsip" />

        <div v-if="status" class="mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 p-2.5 rounded-md">
            {{ status }}
        </div>

        <div class="mb-5 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Autentikasi Petugas</h3>
            <p class="text-xs text-slate-500 mt-0.5">Masukkan kredensial akun internal Anda untuk melanjutkan.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Alamat Email Dinas" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full text-xs"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@pelita-air.com"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <InputLabel for="password" value="Kata Sandi (Password)" class="text-xs font-semibold text-slate-700" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[11px] text-[#0A2540] hover:underline"
                    >
                        Lupa sandi?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-300 text-[#0A2540] focus:ring-[#0A2540]" />
                    <span class="ms-2 text-xs text-slate-600">Ingat sesi saya</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 text-xs font-semibold shadow-xs"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memverifikasi...</span>
                    <span v-else>Masuk ke Sistem</span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-2 border-t border-slate-100">
                <Link
                    :href="route('public.search')"
                    class="text-xs text-slate-500 hover:text-slate-900 inline-flex items-center"
                >
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Halaman Pencarian Publik
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
