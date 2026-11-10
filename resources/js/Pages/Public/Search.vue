<template>
  <PublicLayout>
    <Head title="Pencarian Dokumen Pengadaan SCM — Pelita Air" />

    <div class="space-y-4 pb-8">
      <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
          <div>
            <h1 class="text-base font-bold text-[#0A2540]">
              Pencarian Dokumen Fisik Pengadaan SCM
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
              Cari berkas pengadaan fisik divisi Supply Chain Management (SCM) berdasarkan nomor pengadaan, nama barang/jasa, atau kode bantex.
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center p-1 bg-slate-100 rounded-lg mb-3.5 gap-1 text-xs font-semibold">
          <button
            type="button"
            @click="selectRoom('semua')"
            class="flex-1 py-1.5 px-3 rounded-md transition text-center flex items-center justify-center space-x-1.5"
            :class="selectedRoom === 'semua' ? 'bg-white text-[#0A2540] shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
          >
            <span>Semua Ruang Arsip</span>
            <span class="text-[10px] py-0.5 px-1.5 rounded-full" :class="selectedRoom === 'semua' ? 'bg-slate-100 text-slate-800' : 'bg-slate-200/70 text-slate-600'">
              {{ roomCounts?.all || 0 }}
            </span>
          </button>

          <button
            type="button"
            @click="selectRoom('gp')"
            class="flex-1 py-1.5 px-3 rounded-md transition text-center flex items-center justify-center space-x-1.5"
            :class="selectedRoom === 'gp' ? 'bg-[#0A2540] text-white shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
          >
            <span>Ruang Arsip 1 (Khusus GP)</span>
            <span class="text-[10px] py-0.5 px-1.5 rounded-full" :class="selectedRoom === 'gp' ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600'">
              {{ roomCounts?.gp || 0 }}
            </span>
          </button>

          <button
            type="button"
            @click="selectRoom('ac-ml')"
            class="flex-1 py-1.5 px-3 rounded-md transition text-center flex items-center justify-center space-x-1.5"
            :class="selectedRoom === 'ac-ml' ? 'bg-[#003865] text-white shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
          >
            <span>Ruang Arsip 2 (AC & ML)</span>
            <span class="text-[10px] py-0.5 px-1.5 rounded-full" :class="selectedRoom === 'ac-ml' ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-slate-600'">
              {{ roomCounts?.ac_ml || 0 }}
            </span>
          </button>
        </div>

        <div
          v-if="selectedRoom === 'gp'"
          class="mb-3.5 p-2.5 rounded-md bg-blue-50/80 border border-blue-200 flex items-center justify-between text-xs"
        >
          <div class="flex items-center space-x-2 text-blue-950">
            <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
            <span class="font-bold">Ruang Arsip 1:</span>
            <span class="text-blue-800">Khusus Dokumen General Procurement (GP)</span>
          </div>
          <span class="text-[11px] font-semibold text-blue-700 bg-white/70 px-2 py-0.5 rounded border border-blue-100">
            Fasilitas 1
          </span>
        </div>

        <div
          v-else-if="selectedRoom === 'ac-ml'"
          class="mb-3.5 p-2.5 rounded-md bg-indigo-50/80 border border-indigo-200 flex items-center justify-between text-xs"
        >
          <div class="flex items-center space-x-2 text-indigo-950">
            <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0"></span>
            <span class="font-bold">Ruang Arsip 2:</span>
            <span class="text-indigo-800">Gabungan Dokumen Aircraft Commercial (AC) & Material Logistics (ML)</span>
          </div>
          <span class="text-[11px] font-semibold text-indigo-700 bg-white/70 px-2 py-0.5 rounded border border-indigo-100">
            Fasilitas 2
          </span>
        </div>

        <form @submit.prevent="performSearch" class="relative">
          <div class="relative flex items-center">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="search"
              v-model="searchQuery"
              @input="onSearchInput"
              :placeholder="searchPlaceholder"
              class="w-full pl-9 pr-20 py-2.5 rounded-md border-slate-300 text-xs focus:border-[#0A2540] focus:ring-[#0A2540]"
            />
            <div class="absolute inset-y-0 right-1.5 flex items-center">
              <button
                type="submit"
                class="px-3.5 py-1.5 rounded-md bg-[#0A2540] text-white font-semibold text-xs hover:bg-[#14385C] active:bg-[#07182C] transition focus:outline-none shadow-2xs"
              >
                Cari
              </button>
            </div>
          </div>
        </form>

        <div class="flex flex-wrap items-center gap-1.5 mt-3 text-[11px] text-slate-500">
          <span class="font-medium text-slate-600">Kata Kunci Cepat:</span>
          <template v-if="selectedRoom === 'gp'">
            <button
              v-for="kw in ['Atap', 'Genset', 'Forklift', 'Baut', 'SCM/GP']"
              :key="kw"
              type="button"
              @click="quickSearch(kw)"
              class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200"
            >
              {{ kw }}
            </button>
          </template>
          <template v-else-if="selectedRoom === 'ac-ml'">
            <button
              v-for="kw in ['ERP', 'Asuransi', 'Coil Baja', 'Pelumas', 'SCM/AC', 'SCM/ML']"
              :key="kw"
              type="button"
              @click="quickSearch(kw)"
              class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200"
            >
              {{ kw }}
            </button>
          </template>
          <template v-else>
            <button
              v-for="kw in ['Atap', 'Genset', 'ERP', 'Asuransi', 'SCM/GP', 'SCM/AC']"
              :key="kw"
              type="button"
              @click="quickSearch(kw)"
              class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200"
            >
              {{ kw }}
            </button>
          </template>
        </div>
      </div>

      <div v-if="results !== null" class="space-y-3">
        <div class="flex items-center justify-between px-1">
          <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
            Hasil:
            <span v-if="filters.q" class="text-blue-800 font-mono normal-case font-bold">"{{ filters.q }}"</span>
            <span v-else class="text-slate-800 font-semibold normal-case">Daftar Dokumen di {{ activeRoomLabel }}</span>
          </h2>
          <span class="text-xs text-slate-500 font-medium">
            {{ results.total }} berkas dokumen terdaftar
          </span>
        </div>

        <div
          v-if="results.data.length === 0"
          class="bg-white rounded-lg p-8 border border-slate-200 text-center shadow-2xs"
        >
          <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2.5">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-sm font-bold text-slate-800 mb-1">
            Tidak ada dokumen yang sesuai di {{ activeRoomLabel }}
          </h3>
          <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
            Periksa ejaan kata kunci pencarian Anda, atau beralih ke tab <strong>Semua Ruang Arsip</strong> untuk mencari di ruangan lainnya.
          </p>
        </div>

        <div v-else class="space-y-2.5">
          <div
            v-for="item in results.data"
            :key="item.id"
            class="bg-white rounded-lg p-4 border border-slate-200 hover:border-slate-300 transition shadow-2xs"
          >
            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
              <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-2">
                  <span class="font-mono text-xs font-bold text-blue-800 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                    {{ item.no_pengadaan }}
                  </span>
                  <span class="text-xs font-medium text-slate-500">
                    Tahun {{ item.tahun }}
                  </span>
                  <span
                    v-if="item.bantex?.ruang_arsip"
                    class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                    :class="item.bantex.ruang_key === 'gp' ? 'bg-blue-100 text-blue-800' : 'bg-indigo-100 text-indigo-800'"
                  >
                    {{ item.bantex.ruang_arsip }}
                  </span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mt-1 leading-snug">
                  {{ item.nama_pengadaan }}
                </h3>
              </div>

              <div v-if="item.bantex" class="shrink-0">
                <StatusBadge :status="item.bantex.status" />
              </div>
            </div>

            <div v-if="item.bantex" class="mt-2.5 pt-2.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
              <div class="text-xs text-slate-600 space-y-0.5">
                <div class="flex items-center space-x-1.5 font-mono text-[11px] font-bold text-slate-800">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  <span>{{ item.bantex.kode_bantex }}</span>
                </div>
                <p class="text-[11px] text-slate-500">
                  <span class="font-semibold text-slate-700">Posisi Rak:</span> {{ item.bantex.keterangan_lokasi || `${item.bantex.nomor_rak}, ${item.bantex.baris}, ${item.bantex.urutan_bantex}` }}
                </p>
              </div>

              <Link
                :href="route('public.scan', item.bantex.slug)"
                class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-md text-xs font-semibold bg-[#0A2540] text-white hover:bg-[#14385C] active:bg-[#07182C] transition shadow-2xs"
              >
                <span>Buka Bantex / Form Peminjaman</span>
                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </Link>
            </div>
          </div>

          <Pagination :links="results.links" />
        </div>
      </div>

      <div
        v-else
        class="bg-white rounded-lg p-8 border border-slate-200 text-center shadow-2xs"
      >
        <div class="w-11 h-11 rounded-md bg-slate-100 text-[#0A2540] mx-auto flex items-center justify-center mb-2.5">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
          </svg>
        </div>
        <h2 class="text-sm font-bold text-slate-800 mb-1">
          Sistem Lokasi Berkas & QR Code Ruang Arsip SCM
        </h2>
        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
          Pilih salah satu tab ruangan di atas atau ketik kata kunci nomor/nama pengadaan. Setiap ruang arsip memiliki QR Code fisik yang dapat dipindai langsung dengan smartphone.
        </p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({ q: '', ruang: 'semua' }),
  },
  results: {
    type: Object,
    default: null,
  },
  roomCounts: {
    type: Object,
    default: () => ({ all: 0, gp: 0, ac_ml: 0 }),
  },
});

const searchQuery = ref(props.filters.q || '');
const selectedRoom = ref(props.filters.ruang || 'semua');

const searchPlaceholder = computed(() => {
  if (selectedRoom.value === 'gp') {
    return 'Cari dokumen di Ruang Arsip 1 (Khusus GP)...';
  }
  if (selectedRoom.value === 'ac-ml') {
    return 'Cari dokumen di Ruang Arsip 2 (AC & ML)...';
  }
  return 'Masukkan nomor pengadaan, uraian barang/jasa, atau kode bantex...';
});

const activeRoomLabel = computed(() => {
  if (selectedRoom.value === 'gp') return 'Ruang Arsip 1 (GP)';
  if (selectedRoom.value === 'ac-ml') return 'Ruang Arsip 2 (AC & ML)';
  return 'Semua Ruang Arsip';
});

let searchTimeout = null;
function onSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    performSearch();
  }, 350);
}

function selectRoom(roomKey) {
  selectedRoom.value = roomKey;
  performSearch();
}

function performSearch() {
  const params = {};
  if (searchQuery.value.trim()) {
    params.q = searchQuery.value.trim();
  }
  if (selectedRoom.value && selectedRoom.value !== 'semua') {
    params.ruang = selectedRoom.value;
  }

  router.get(route('public.search'), params, {
    preserveState: true,
    replace: true,
  });
}

function quickSearch(keyword) {
  searchQuery.value = keyword;
  performSearch();
}
</script>
