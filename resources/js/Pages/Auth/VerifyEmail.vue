<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email — Pelita Air" />

        <div class="mb-4 pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Verifikasi Alamat Email</h3>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Terima kasih telah mendaftar. Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email dinas Anda.
            </p>
        </div>

        <div
            class="mb-4 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 p-2.5 rounded-md"
            v-if="verificationLinkSent"
        >
            Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <PrimaryButton
                class="w-full py-2.5 text-xs font-semibold shadow-xs"
                :class="{ 'opacity-50': form.processing }"
                :disabled="form.processing"
            >
                Kirim Ulang Email Verifikasi
            </PrimaryButton>

            <div class="text-center pt-2 border-t border-slate-100">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-xs text-slate-500 hover:text-slate-900 underline"
                >
                    Keluar (Logout)
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
