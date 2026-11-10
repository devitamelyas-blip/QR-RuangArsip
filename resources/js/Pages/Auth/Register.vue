<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrasi Petugas Baru — Pelita Air" />

        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Registrasi Petugas Arsip</h3>
            <p class="text-xs text-slate-500 mt-1">
                Daftarkan akun petugas baru untuk pengelolaan dokumen ruang arsip.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="name"
                    type="text"
                    class="block w-full text-xs"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Contoh: Ahmad Fauzi"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Alamat Email Dinas" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full text-xs"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="nama@pelita-air.com"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi (Password)" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Kata Sandi"
                    class="text-xs font-semibold text-slate-700 mb-1"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                />

                <InputError
                    class="mt-1 text-xs"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 text-xs font-semibold shadow-xs"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mendaftarkan Akun...</span>
                    <span v-else>Daftarkan Akun Petugas</span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-2 border-t border-slate-100">
                <Link
                    :href="route('login')"
                    class="text-xs text-slate-500 hover:text-slate-900 inline-flex items-center"
                >
                    Sudah memiliki akun? <span class="text-[#0A2540] font-semibold ml-1">Masuk di sini</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
