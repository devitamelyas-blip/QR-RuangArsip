<template>
  <div class="min-h-screen bg-[#F5F6F8] font-sans text-slate-800 print:bg-white print:p-0">
    <Head title="Cetak Label QR Code Bantex SCM — Pelita Air" />

    <header class="no-print bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <button
            type="button"
            @click="goBack"
            class="p-2 rounded-md text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition border border-slate-200"
            title="Kembali"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div>
            <h1 class="text-sm sm:text-base font-bold text-[#0A2540]">
              Pratinjau Label QR Code SCM ({{ labels.length }} Unit Bantex)
            </h1>
            <p class="text-[11px] text-slate-500">
              Garis putus-putus abu-abu berfungsi sebagai panduan pemotongan stiker fisik bantex arsip SCM.
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <button
            type="button"
            @click="printNow"
            class="inline-flex items-center px-4 py-2 bg-[#0A2540] text-white font-semibold text-xs rounded-md hover:bg-[#14385C] transition focus:outline-none shadow-2xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Sekarang (Print)</span>
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-5xl mx-auto p-4 sm:p-6 print:p-0 print:max-w-none">
      <div class="label-grid grid grid-cols-1 md:grid-cols-2 gap-5 print:gap-3">
        <div
          v-for="label in labels"
          :key="label.id"
          class="label-card bg-white rounded-lg border-2 border-dashed border-slate-300 print:border-slate-400 p-5 flex flex-col justify-between shadow-2xs print:shadow-none break-inside-avoid"
        >
          <div class="text-center pb-2.5 border-b border-slate-200">
            <div class="flex items-center justify-center space-x-1.5 mb-0.5">
              <img src="/images/pelita-symbol.png" alt="Pelita Air" class="h-4 w-auto object-contain" />
              <span class="font-black text-xs tracking-wider text-[#003865]">PELITA AIR</span>
              <span class="w-1 h-2.5 rounded-full bg-[#D71920]"></span>
              <span class="text-[9.5px] font-bold tracking-wider text-slate-700 uppercase">DIVISI SCM</span>
            </div>
            <div class="flex items-center justify-center space-x-2 mt-1">
              <span
                v-if="label.ruang_arsip"
                class="px-2 py-0.5 rounded text-[9.5px] font-bold uppercase tracking-wider"
                :class="label.ruang_key === 'gp' ? 'bg-blue-100 text-blue-900 border border-blue-200' : 'bg-indigo-100 text-indigo-900 border border-indigo-200'"
              >
                {{ label.ruang_arsip }}
              </span>
              <span class="text-[10px] text-slate-600 font-medium">
                {{ label.nomor_rak }} &bull; {{ label.baris }}
              </span>
            </div>
          </div>

          <div class="py-3 flex flex-col items-center justify-center text-center space-y-2.5">
            <div
              class="w-36 h-36 flex items-center justify-center p-1.5 bg-white border border-slate-300 rounded-md"
              v-html="label.qr_svg"
            ></div>

            <div class="w-full">
              <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">
                Kode Lokasi Bantex SCM
              </span>
              <div class="font-mono text-sm sm:text-base print:text-sm font-extrabold text-slate-900 tracking-tight leading-snug px-2.5 py-1 bg-slate-100 rounded border border-slate-200 break-all select-all">
                {{ label.kode_bantex }}
              </div>
            </div>

            <div v-if="label.keterangan_lokasi" class="text-[11px] text-slate-700 font-medium px-2 leading-tight">
              {{ label.keterangan_lokasi }}
            </div>
            <div v-else class="text-[11px] text-slate-600 font-medium leading-tight">
              {{ label.nomor_rak }}, {{ label.baris }}, {{ label.urutan_bantex }}
            </div>
          </div>

          <div class="pt-2 border-t border-slate-200 text-center">
            <span class="inline-block px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-semibold uppercase tracking-wider border border-slate-200">
              Pindai (Scan) QR untuk Peminjaman / Pengembalian
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
    gap: 10px !important;
  }

  .label-card {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
    border: 1.5px dashed #64748b !important;
    border-radius: 6px !important;
    padding: 12px !important;
    box-shadow: none !important;
  }

  @page {
    size: A4 portrait;
    margin: 8mm;
  }
}
</style>
