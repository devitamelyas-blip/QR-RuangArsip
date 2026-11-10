<template>
  <AuthenticatedLayout :breadcrumbs="[
    { label: 'Data Dokumen & Bantex', url: route('admin.bantex.index') },
    { label: bantex.kode_bantex }
  ]">
    <Head :title="`Detail & Edit Bantex ${bantex.kode_bantex} — Pelita Air`" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
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
            <h1 class="text-lg font-bold text-[#0A2540] tracking-tight flex items-center space-x-2">
              <span>Detail & Edit Bantex:</span>
              <span class="font-mono text-blue-700 font-extrabold">{{ bantex.kode_bantex }}</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
              Perbarui kode lokasi fisik rak, susunan penomoran, atau sesuaikan daftar berkas dokumen di dalamnya.
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <Link
            :href="route('admin.labels.print-single', bantex.id)"
            target="_blank"
            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition"
          >
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Label QR</span>
          </Link>

          <Link
            :href="route('public.scan', bantex.slug)"
            target="_blank"
            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition"
          >
            <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Halaman Scan Publik</span>
          </Link>
        </div>
      </div>
    </template>

    <div class="max-w-4xl mx-auto space-y-5">
      <div class="bg-white rounded-lg p-4 sm:p-5 border border-slate-200 shadow-2xs">
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
          <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ringkasan Fisik Bantex</span>
            <span class="text-xs text-slate-300">|</span>
            <span class="font-mono text-xs text-slate-500">Slug: {{ bantex.slug }}</span>
          </div>
          <StatusBadge :status="bantex.status" />
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3">
          <div class="bg-slate-50/80 rounded-md p-2.5 border border-slate-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Tim Kerja</span>
            <span class="text-sm font-bold text-slate-800">Tim {{ bantex.tim }}</span>
          </div>
          <div class="bg-slate-50/80 rounded-md p-2.5 border border-slate-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Nomor Rak</span>
            <span class="text-sm font-bold text-slate-800">{{ bantex.nomor_rak }}</span>
          </div>
          <div class="bg-slate-50/80 rounded-md p-2.5 border border-slate-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Baris Rak</span>
            <span class="text-sm font-bold text-slate-800">{{ bantex.baris }}</span>
          </div>
          <div class="bg-slate-50/80 rounded-md p-2.5 border border-slate-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Posisi / Urutan</span>
            <span class="text-sm font-bold text-slate-800">{{ bantex.urutan_bantex }}</span>
          </div>
        </div>

        <div v-if="bantex.keterangan_lokasi" class="mt-3 p-2.5 bg-blue-50/60 rounded-md border border-blue-100 text-xs text-slate-700 flex items-start space-x-2">
          <svg class="w-4 h-4 text-blue-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <span class="font-semibold text-blue-900">Petunjuk Lokasi Rak: </span>
            <span>{{ bantex.keterangan_lokasi }}</span>
          </div>
        </div>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs space-y-4">
          <div class="pb-3 border-b border-slate-100">
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
              Ubah Data Lokasi & Penomoran Rak
            </h2>
            <p class="text-[11px] text-slate-500">
              Sesuaikan data jika bantex dipindahkan ke nomor rak atau baris penyimpanan lain.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <div class="md:col-span-2">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kode Bantex <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.kode_bantex"
                required
                class="w-full text-xs font-mono font-bold rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
                :class="{ 'border-rose-400': form.errors.kode_bantex }"
              />
              <p v-if="form.errors.kode_bantex" class="text-xs text-rose-600 mt-1">
                {{ form.errors.kode_bantex }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kelompok / Tim Kerja SCM <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.tim"
                required
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3 uppercase"
                :class="{ 'border-rose-400': form.errors.tim }"
              />
              <div class="flex items-center space-x-1.5 mt-1.5 text-[11px]">
                <span class="text-slate-400">Pilihan:</span>
                <button
                  type="button"
                  @click="form.tim = 'GP'"
                  class="px-2 py-0.5 rounded text-[10px] font-bold border transition"
                  :class="form.tim?.toUpperCase() === 'GP' ? 'bg-blue-800 text-white border-blue-900' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200'"
                >
                  GP (Ruang 1)
                </button>
                <button
                  type="button"
                  @click="form.tim = 'AC'"
                  class="px-2 py-0.5 rounded text-[10px] font-bold border transition"
                  :class="form.tim?.toUpperCase() === 'AC' ? 'bg-indigo-800 text-white border-indigo-900' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200'"
                >
                  AC (Ruang 2)
                </button>
                <button
                  type="button"
                  @click="form.tim = 'ML'"
                  class="px-2 py-0.5 rounded text-[10px] font-bold border transition"
                  :class="form.tim?.toUpperCase() === 'ML' ? 'bg-indigo-800 text-white border-indigo-900' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200'"
                >
                  ML (Ruang 2)
                </button>
              </div>
              <p v-if="form.errors.tim" class="text-xs text-rose-600 mt-1">
                {{ form.errors.tim }}
              </p>
              <p v-else-if="form.tim" class="text-[11px] font-medium mt-1" :class="form.tim.toUpperCase() === 'GP' ? 'text-blue-700' : 'text-indigo-700'">
                &rarr; Ditempatkan di: <strong>{{ form.tim.toUpperCase() === 'GP' ? 'Ruang Arsip 1 (Khusus GP)' : (['AC', 'ML'].includes(form.tim.toUpperCase()) ? 'Ruang Arsip 2 (AC & ML)' : 'Ruang Arsip ' + form.tim.toUpperCase()) }}</strong>
              </p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Nomor Rak Fisik <span class="text-rose-600">*</span>
              </label>
              <input
                type="text"
                v-model="form.nomor_rak"
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
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-2 px-3"
              />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs">
          <DocumentRepeater v-model="form.dokumen" />
          <p v-if="form.errors.dokumen" class="text-xs text-rose-600 mt-2">
            {{ form.errors.dokumen }}
          </p>
        </div>

        <div class="flex items-center justify-between pt-2">
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
            <span>{{ form.processing ? 'Menyimpan Perubahan...' : 'Simpan Perubahan' }}</span>
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
import StatusBadge from '@/Components/StatusBadge.vue';

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
