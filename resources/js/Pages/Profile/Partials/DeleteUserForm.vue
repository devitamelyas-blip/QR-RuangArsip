<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-4">
        <header class="pb-3 border-b border-slate-100">
            <h2 class="text-xs font-bold text-rose-800 uppercase tracking-wider">
                Hapus Akun Pengguna
            </h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Tindakan ini akan menghapus akun petugas secara permanen dari basis data sistem manajemen arsip.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="text-xs">
            Hapus Akun Saya
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h3 class="text-sm font-bold text-slate-900">
                    Konfirmasi Penghapusan Akun
                </h3>

                <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                    Setelah akun Anda dihapus, semua riwayat otorisasi dan akses Anda ke sistem manajemen dokumen ruang arsip akan dibatalkan. Masukkan kata sandi Anda untuk mengonfirmasi.
                </p>

                <div class="mt-4">
                    <InputLabel
                        for="password"
                        value="Kata Sandi"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full text-xs"
                        placeholder="Masukkan kata sandi untuk verifikasi"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-1 text-xs" />
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <SecondaryButton @click="closeModal" class="text-xs">
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        class="text-xs"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Ya, Hapus Akun
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
