<template>
  <div class="min-h-screen bg-slate-100 font-sans text-slate-900 print:bg-white print:p-0 selection:bg-blue-600 selection:text-white">
    <Head title="Cetak Label QR Code Bantex" />

    <header class="no-print bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <button
            type="button"
            @click="goBack"
            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
            title="Kembali"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div>
            <h1 class="text-sm sm:text-base font-bold text-slate-900">
              Pratinjau Cetak Label QR ({{ labels.length }} Bantex)
            </h1>
            <p class="text-[11px] text-slate-500">
              Gunakan kertas stiker atau HVS tebal. Garis putus-putus sebagai panduan potong.
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <button
            type="button"
            @click="printNow"
            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition active:scale-95 shadow-md"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Sekarang (Print)
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-5xl mx-auto p-4 sm:p-6 print:p-0 print:max-w-none">
      <div class="label-grid grid grid-cols-1 md:grid-cols-2 gap-6 print:gap-4">
        <div
          v-for="label in labels"
          :key="label.id"
          class="label-card bg-white rounded-xl border-2 border-dashed border-slate-300 print:border-slate-400 p-5 flex flex-col justify-between shadow-xs print:shadow-none break-inside-avoid"
        >
          <div class="text-center pb-2.5 border-b border-slate-200">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-800">
              Ruang Arsip Dokumen Pengadaan
            </h2>
            <p class="text-[10px] text-slate-500 font-semibold mt-0.5">
              Tim {{ label.tim }} &bull; {{ label.nomor_rak }} &bull; {{ label.baris }}
            </p>
          </div>

          <div class="py-4 flex flex-col items-center justify-center text-center space-y-3">
            <div
              class="w-40 h-40 flex items-center justify-center p-1.5 bg-white border border-slate-200 rounded-xl"
              v-html="label.qr_svg"
            ></div>

            <div class="w-full">
              <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                Kode Lokasi
              </div>
              <div class="font-mono text-base sm:text-lg print:text-base font-black text-slate-900 tracking-tight leading-snug px-2 py-1 bg-slate-100 rounded-lg border border-slate-200 break-all">
                {{ label.kode_bantex }}
              </div>
            </div>

            <div v-if="label.keterangan_lokasi" class="text-[11px] text-slate-700 font-medium px-2">
              {{ label.keterangan_lokasi }}
            </div>
            <div v-else class="text-[11px] text-slate-600 font-medium">
              {{ label.nomor_rak }}, {{ label.baris }}, {{ label.urutan_bantex }}
            </div>
          </div>

          <div class="pt-2.5 border-t border-slate-200 text-center">
            <span class="inline-block px-3 py-1 rounded-md bg-slate-100 text-slate-800 text-[10px] font-bold uppercase tracking-wider">
              Scan untuk pinjam / kembali
            </span>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
  labels: {
    type: Array,
    required: true,
  },
});

function printNow() {
  window.print();
}

function goBack() {
  window.history.back();
}
</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }

  body {
    background-color: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .label-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 12px !important;
  }

  .label-card {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
    border: 1.5px dashed #94a3b8 !important;
    border-radius: 8px !important;
    padding: 14px !important;
    box-shadow: none !important;
  }

  @page {
    size: A4 portrait;
    margin: 10mm;
  }
}
</style>
