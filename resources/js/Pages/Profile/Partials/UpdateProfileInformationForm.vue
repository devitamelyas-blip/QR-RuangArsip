<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="pb-3 border-b border-slate-100">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Informasi Akun Petugas
            </h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Perbarui nama lengkap dan alamat email operasional Pelita Air yang terdaftar.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-4 space-y-4 max-w-xl"
        >
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
                />

                <InputError class="mt-1 text-xs" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-xs text-slate-700 bg-amber-50 p-2.5 rounded border border-amber-200">
                    Alamat email belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="text-xs text-blue-800 underline font-semibold hover:text-blue-900 ml-1"
                    >
                        Kirim ulang tautan verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-xs font-medium text-emerald-700 bg-emerald-50 p-2 rounded border border-emerald-200"
                >
                    Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                </div>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out duration-200"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-200"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs text-emerald-700 font-medium"
                    >
                        Perubahan berhasil disimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
