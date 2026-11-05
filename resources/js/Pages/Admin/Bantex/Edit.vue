<template>
  <AuthenticatedLayout>
    <Head :title="`Edit Bantex ${bantex.kode_bantex}`" />

    <template #header>
      <div class="flex items-center space-x-3">
        <Link
          :href="route('admin.bantex.index')"
          class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
        <div>
          <h2 class="text-xl font-bold leading-tight text-slate-900">
            Edit Bantex: <span class="font-mono text-blue-600">{{ bantex.kode_bantex }}</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Perbarui kode lokasi fisik, susunan posisi, atau ubah daftar berkas dokumen di dalamnya.
          </p>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto">
      <form @submit.prevent="submit" class="space-y-6">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center">
              <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
              Informasi & Posisi Rak Bantex
            </h3>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
              :class="bantex.status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'"
            >
              Status: {{ bantex.status }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kode Bantex <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.kode_bantex"
                required
                class="w-full text-xs font-mono font-bold rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2.5 px-3.5"
                :class="{ 'border-rose-400': form.errors.kode_bantex }"
              />
              <p v-if="form.errors.kode_bantex" class="text-xs text-rose-600 mt-1">
                {{ form.errors.kode_bantex }}
              </p>
              <p class="text-[11px] text-slate-500 mt-1">
                Slug URL saat ini: <span class="font-mono text-blue-600 font-semibold">{{ bantex.slug }}</span>
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kelompok / Tim <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.tim"
                required
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 px-3"
                :class="{ 'border-rose-400': form.errors.tim }"
              />
              <p v-if="form.errors.tim" class="text-xs text-rose-600 mt-1">
                {{ form.errors.tim }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Nomor Rak <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.nomor_rak"
                required
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 px-3"
                :class="{ 'border-rose-400': form.errors.nomor_rak }"
              />
              <p v-if="form.errors.nomor_rak" class="text-xs text-rose-600 mt-1">
                {{ form.errors.nomor_rak }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Baris Rak <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.baris"
                required
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 px-3"
                :class="{ 'border-rose-400': form.errors.baris }"
              />
              <p v-if="form.errors.baris" class="text-xs text-rose-600 mt-1">
                {{ form.errors.baris }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Urutan Bantex <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.urutan_bantex"
                required
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 px-3"
                :class="{ 'border-rose-400': form.errors.urutan_bantex }"
              />
              <p v-if="form.errors.urutan_bantex" class="text-xs text-rose-600 mt-1">
                {{ form.errors.urutan_bantex }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Keterangan Lokasi / Cara Baca (Opsional)
              </label>
              <input
                type="text"
                v-model="form.keterangan_lokasi"
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 px-3"
              />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs">
          <DocumentRepeater v-model="form.dokumen" />
          <p v-if="form.errors.dokumen" class="text-xs text-rose-600 mt-2">
            {{ form.errors.dokumen }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2">
          <Link
            :href="route('admin.labels.print-single', bantex.id)"
            target="_blank"
            class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Label Bantex Ini
          </Link>

          <div class="flex items-center space-x-3">
            <Link
              :href="route('admin.bantex.index')"
              class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
            >
              Batal
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 active:scale-95 transition shadow-xs disabled:opacity-50"
            >
              <span v-if="form.processing" class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Menyimpan Perubahan...
              </span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DocumentRepeater from '@/Components/DocumentRepeater.vue';

const props = defineProps({
  bantex: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  kode_bantex: props.bantex.kode_bantex,
  tim: props.bantex.tim,
  nomor_rak: props.bantex.nomor_rak,
  baris: props.bantex.baris,
  urutan_bantex: props.bantex.urutan_bantex,
  keterangan_lokasi: props.bantex.keterangan_lokasi || '',
  dokumen: props.bantex.dokumen.length > 0
    ? props.bantex.dokumen.map((doc) => ({
        id: doc.id,
        no_pengadaan: doc.no_pengadaan,
        nama_pengadaan: doc.nama_pengadaan,
        tahun: doc.tahun,
      }))
    : [{ no_pengadaan: '', nama_pengadaan: '', tahun: new Date().getFullYear().toString() }],
});

function submit() {
  form.put(route('admin.bantex.update', props.bantex.id));
}
</script>
