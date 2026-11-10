<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Pengaturan Profil Pengguna — Pelita Air" />

    <AuthenticatedLayout :breadcrumbs="[{ label: 'Manajemen Pengguna & Profil' }]">
        <template #header>
            <div>
                <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
                    Pengaturan Profil & Akun Petugas
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Kelola informasi identitas akun, perbarui kata sandi, dan setelan keamanan internal.
                </p>
            </div>
        </template>

        <div class="max-w-4xl space-y-5">
            <!-- Informasi Profil -->
            <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-2xs">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                />
            </div>

            <!-- Perbarui Kata Sandi -->
            <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-2xs">
                <UpdatePasswordForm />
            </div>

            <!-- Hapus Akun -->
            <div class="bg-white p-5 border border-slate-200 rounded-lg shadow-2xs">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
