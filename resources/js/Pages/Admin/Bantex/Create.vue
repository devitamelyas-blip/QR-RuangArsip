<template>
  <AuthenticatedLayout :breadcrumbs="[
    { label: 'Data Dokumen & Bantex', url: route('admin.bantex.index') },
    { label: 'Tambah Bantex Baru' }
  ]">
    <Head title="Tambah Bantex Dokumen Baru — Pelita Air" />

    <template #header>
      <div class="flex items-center space-x-3">
        <Link
          :href="route('admin.bantex.index')"
          class="p-1.5 rounded-md text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition border border-slate-200"
          title="Kembali ke Daftar Bantex"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </Link>
        <div>
          <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
            Tambah Bantex Dokumen Baru
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Daftarkan kode lokasi fisik bantex baru beserta rincian berkas pengadaan fisik di dalamnya.
          </p>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto">
      <form @submit.prevent="submit" class="space-y-5">
        <!-- Informasi Lokasi Fisik Rak -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs space-y-4">
          <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <div class="w-7 h-7 rounded bg-blue-50 text-[#0A2540] flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <div>
                <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                  Informasi Identitas & Lokasi Fisik Rak
                </h2>
                <p class="text-[11px] text-slate-500">
                  Pastikan penomoran rak dan baris sesuai dengan denah ruang arsip.
                </p>
              </div>
            </div>
            <span class="text-[11px] font-semibold text-slate-400">
              Status Awal: <span class="text-emerald-700 font-bold">Tersedia</span>
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kode Bantex <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.kode_bantex"
                placeholder="Contoh: SCM/GP/R-01/01/HCGA/GA/02"
                required
                class="w-full text-xs font-mono font-bold rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
                :class="{ 'border-rose-400': form.errors.kode_bantex }"
              />
              <p v-if="form.errors.kode_bantex" class="text-xs text-rose-600 mt-1">
                {{ form.errors.kode_bantex }}
              </p>
              <p class="text-[11px] text-slate-500 mt-1">
                Struktur standar: <span class="font-mono text-slate-700">SCM/[Tim]/R-[NomorRak]/[Baris]/[Fungsi]/[SubTim]/[Urutan]</span>
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kelompok / Tim Kerja <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.tim"
                placeholder="Contoh: GP, AC, atau HCGA"
                required
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
                :class="{ 'border-rose-400': form.errors.tim }"
              />
              <p v-if="form.errors.tim" class="text-xs text-rose-600 mt-1">
                {{ form.errors.tim }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Nomor Rak Fisik <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.nomor_rak"
                placeholder="Contoh: Rak 1 atau R-01"
                required
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
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
                placeholder="Contoh: Baris 1 atau B-01"
                required
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
                :class="{ 'border-rose-400': form.errors.baris }"
              />
              <p v-if="form.errors.baris" class="text-xs text-rose-600 mt-1">
                {{ form.errors.baris }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Urutan / Posisi Bantex <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.urutan_bantex"
                placeholder="Contoh: Bantex 02 atau Urutan 2"
                required
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
                :class="{ 'border-rose-400': form.errors.urutan_bantex }"
              />
              <p v-if="form.errors.urutan_bantex" class="text-xs text-rose-600 mt-1">
                {{ form.errors.urutan_bantex }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Keterangan Lokasi / Cara Baca Lokasi (Opsional)
              </label>
              <input
                type="text"
                v-model="form.keterangan_lokasi"
                placeholder="Contoh: Kelompok GP, Rak 1, Baris 1, Fungsi HCGA, Tim GA, Bantex ke-2"
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
              />
              <p class="text-[11px] text-slate-500 mt-1">
                Uraian kalimat penunjuk lokasi untuk mempermudah petugas arsip menemukan fisik bantex di rak.
              </p>
            </div>
          </div>
        </div>

        <!-- Berkas Dokumen Repeater -->
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs">
          <DocumentRepeater v-model="form.dokumen" />
          <p v-if="form.errors.dokumen" class="text-xs text-rose-600 mt-2">
            {{ form.errors.dokumen }}
          </p>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-2.5 pt-2">
          <Link
            :href="route('admin.bantex.index')"
            class="px-4 py-2 rounded-md border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-50 transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="px-5 py-2 rounded-md bg-[#0A2540] text-white text-xs font-semibold hover:bg-[#14385C] transition focus:outline-none shadow-2xs disabled:opacity-50 inline-flex items-center"
          >
            <svg
              v-if="form.processing"
              class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>{{ form.processing ? 'Menyimpan Data...' : 'Simpan Bantex' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DocumentRepeater from '@/Components/DocumentRepeater.vue';

const currentYear = new Date().getFullYear().toString();

const form = useForm({
  kode_bantex: '',
  tim: '',
  nomor_rak: '',
  baris: '',
  urutan_bantex: '',
  keterangan_lokasi: '',
  dokumen: [
    { no_pengadaan: '', nama_pengadaan: '', tahun: currentYear },
  ],
});

function submit() {
  form.post(route('admin.bantex.store'));
}
</script>
