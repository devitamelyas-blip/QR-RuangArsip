<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Atur Ulang Kata Sandi — Pelita Air" />

        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Atur Ulang Kata Sandi</h3>
            <p class="text-xs text-slate-500 mt-1">
                Buat kata sandi baru untuk akun petugas arsip Anda.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Alamat Email Dinas" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full text-xs bg-slate-50"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    readonly
                />

                <InputError class="mt-1 text-xs" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi Baru" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter kombinasi"
                />

                <InputError class="mt-1 text-xs" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Kata Sandi Baru"
                    class="text-xs font-semibold text-slate-700 mb-1"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="block w-full text-xs"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
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
                    <span v-if="form.processing">Menyimpan Sandi...</span>
                    <span v-else>Simpan Kata Sandi Baru</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
