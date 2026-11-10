<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="pb-3 border-b border-slate-100">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Perbarui Kata Sandi (Password)
            </h2>
            <p class="mt-0.5 text-xs text-slate-500">
                Pastikan akun Anda menggunakan kombinasi kata sandi yang aman untuk menjaga kerahasiaan data arsip.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-4 space-y-4 max-w-xl">
            <div>
                <InputLabel for="current_password" value="Kata Sandi Saat Ini" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="block w-full text-xs"
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-1 text-xs"
                />
            </div>

            <div>
                <InputLabel for="password" value="Kata Sandi Baru" class="text-xs font-semibold text-slate-700 mb-1" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="block w-full text-xs"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter kombinasi"
                />

                <InputError :message="form.errors.password" class="mt-1 text-xs" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Kata Sandi Baru"
                    class="text-xs font-semibold text-slate-700 mb-1"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="block w-full text-xs"
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-1 text-xs"
                />
            </div>

            <div class="flex items-center gap-3 pt-1">
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}
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
                        Kata sandi berhasil diperbarui.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
