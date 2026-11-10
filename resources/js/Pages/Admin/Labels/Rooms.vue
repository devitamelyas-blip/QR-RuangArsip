<template>
  <AuthenticatedLayout :breadcrumbs="[{ label: 'QR Ruang Arsip' }]">
    <Head title="Cetak Poster QR Code Ruang Arsip — Pelita Air SCM" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 no-print">
        <div>
          <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
            Poster QR Code Ruang Arsip SCM
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Cetak poster label QR Code resmi untuk ditempel pada pintu masuk atau dinding Ruang Arsip 1 (Khusus GP) dan Ruang Arsip 2 (Gabungan AC & ML).
          </p>
        </div>

        <div class="flex items-center space-x-2">
          <button
            type="button"
            @click="printAll"
            class="inline-flex items-center px-4 py-2 bg-[#0A2540] text-white font-semibold text-xs rounded-md hover:bg-[#14385C] transition shadow-2xs focus:outline-none"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Semua Poster (A4)</span>
          </button>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-900 no-print flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="space-y-1">
          <p class="font-bold">Panduan Fisik Penempatan QR Code:</p>
          <ul class="list-disc list-inside space-y-0.5 text-blue-800">
            <li><strong>Ruang Arsip 1:</strong> Khusus berkas pengadaan fisik kelompok <strong>General Procurement (GP)</strong>.</li>
            <li><strong>Ruang Arsip 2:</strong> Gabungan berkas pengadaan fisik kelompok <strong>Aircraft Commercial (AC)</strong> dan <strong>Material & Logistics (ML)</strong>.</li>
            <li>Tempelkan poster cetak pada posisi sejajar mata (140–160 cm dari lantai) di samping pintu masuk atau meja inventaris arsip agar mudah dipindai staf menggunakan kamera smartphone.</li>
          </ul>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print:block print:space-y-0">
        <div
          v-for="room in rooms"
          :key="room.key"
          :id="'room-card-' + room.key"
          class="room-poster-sheet bg-white rounded-xl border border-slate-300 shadow-sm overflow-hidden flex flex-col justify-between print:border-none print:shadow-none print:rounded-none"
        >
          <div class="p-6 sm:p-8 border-b-4 border-[#0A2540] bg-white text-center flex flex-col items-center">
            <img
              src="/images/pelita-logo.png"
              alt="Pelita Air"
              class="h-10 sm:h-12 w-auto mb-3 object-contain"
            />
            <div class="text-[11px] font-bold tracking-widest uppercase text-slate-500">
              DIVISI SUPPLY CHAIN MANAGEMENT (SCM)
            </div>
            <div class="text-[9px] font-semibold tracking-wider uppercase text-slate-400 mt-0.5">
              DOCUMENT MANAGEMENT SYSTEM — RUANG PENYIMPANAN ARSIP
            </div>
          </div>

          <div class="px-6 py-5 sm:px-8 text-center bg-slate-50/70 border-b border-slate-200">
            <div class="inline-block px-3 py-1 rounded bg-[#0A2540] text-white font-extrabold text-sm sm:text-base tracking-wider uppercase mb-2">
              {{ room.title }}
            </div>
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight uppercase">
              {{ room.tim_label }}
            </h2>
            <p class="text-xs text-slate-600 mt-1 max-w-sm mx-auto leading-relaxed">
              {{ room.tim_description }}
            </p>
          </div>

          <div class="p-6 sm:p-8 text-center flex flex-col items-center justify-center flex-1 bg-white">
            <div class="p-4 bg-white border-2 border-slate-900 rounded-xl shadow-xs inline-block">
              <div
                v-html="room.qr_svg"
                class="w-56 h-56 sm:w-64 sm:h-64 mx-auto flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
              ></div>
            </div>

            <div class="mt-4 space-y-1">
              <div class="text-xs sm:text-sm font-black uppercase tracking-wider text-[#0A2540]">
                PINDAI QR UNTUK CARI & PINJAM BERKAS
              </div>
              <p class="text-[11px] text-slate-500 max-w-xs mx-auto">
                Arahkan kamera smartphone ke kode di atas untuk langsung membuka katalog dokumen pada ruangan ini.
              </p>
            </div>
          </div>

          <div class="px-6 py-4 sm:px-8 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-600">
            <div class="text-center sm:text-left">
              <span class="font-bold text-slate-700">Tautan Akses Langsung:</span>
              <a :href="room.url" target="_blank" class="block font-mono text-[10px] text-blue-700 underline truncate max-w-xs">
                {{ room.url }}
              </a>
            </div>

            <div class="shrink-0 text-center sm:text-right">
              <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                {{ room.total_bantex }} Unit Bantex Terdaftar
              </span>
            </div>
          </div>

          <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-center no-print">
            <button
              type="button"
              @click="printSingleRoom(room.key)"
              class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md transition"
            >
              <svg class="w-3.5 h-3.5 mr-1 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              <span>Cetak Poster {{ room.title }} Ini Saja</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  rooms: {
    type: Array,
    required: true,
  },
});

function printAll() {
  window.print();
}

function printSingleRoom(roomKey) {
  const cards = document.querySelectorAll('.room-poster-sheet');
  cards.forEach((card) => {
    if (card.id === 'room-card-' + roomKey) {
      card.classList.remove('print:hidden');
    } else {
      card.classList.add('print:hidden');
    }
  });

  window.print();

  setTimeout(() => {
    cards.forEach((card) => card.classList.remove('print:hidden'));
  }, 1000);
}
</script>

<style>
@media print {
  @page {
    size: A4 portrait;
    margin: 1.5cm;
  }

  body {
    background: white !important;
    color: black !important;
  }

  .no-print {
    display: none !important;
  }

  .room-poster-sheet {
    page-break-after: always;
    page-break-inside: avoid;
    height: 94vh;
    border: 2px solid #0A2540 !important;
    border-radius: 12px !important;
  }

  .room-poster-sheet:last-child {
    page-break-after: auto;
  }
}
</style>
