<template>
  <PublicLayout>
    <Head title="Pencarian Dokumen & Lokasi Arsip" />

    <div class="space-y-5 pb-8">
      <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
        <h2 class="text-lg font-bold text-slate-900 mb-1">
          Cari Dokumen Pengadaan
        </h2>
        <p class="text-xs text-slate-500 mb-4">
          Cari berdasarkan Nama Pengadaan, Nomor Pengadaan (Sxxxxxx), Tahun, atau Kode Bantex.
        </p>

        <form @submit.prevent="performSearch" class="relative">
          <div class="relative flex items-center">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="search"
              v-model="searchQuery"
              placeholder="Ketik kata kunci pencarian..."
              class="w-full pl-10 pr-24 py-3 rounded-xl border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600 shadow-2xs"
            />
            <div class="absolute inset-y-0 right-1.5 flex items-center">
              <button
                type="submit"
                class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 active:scale-95 transition shadow-xs"
              >
                Cari
              </button>
            </div>
          </div>
        </form>

        <div class="flex flex-wrap items-center gap-1.5 mt-3 text-[11px] text-slate-500">
          <span class="font-medium">Contoh:</span>
          <button
            type="button"
            @click="quickSearch('Atap')"
            class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
          >
            Atap
          </button>
          <button
            type="button"
            @click="quickSearch('Genset')"
            class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
          >
            Genset
          </button>
          <button
            type="button"
            @click="quickSearch('2026')"
            class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
          >
            2026
          </button>
          <button
            type="button"
            @click="quickSearch('SCM/GP')"
            class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
          >
            SCM/GP
          </button>
        </div>
      </div>

      <div v-if="results !== null" class="space-y-3">
        <div class="flex items-center justify-between px-1">
          <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
            Hasil Pencarian:
            <span class="text-blue-600 font-mono normal-case">"{{ filters.q }}"</span>
          </h3>
          <span class="text-xs font-semibold text-slate-500">
            {{ results.total }} dokumen ditemukan
          </span>
        </div>

        <div
          v-if="results.data.length === 0"
          class="bg-white rounded-2xl p-8 border border-slate-200 text-center shadow-xs"
        >
          <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h4 class="text-sm font-bold text-slate-800 mb-1">
            Tidak ada dokumen yang cocok
          </h4>
          <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Coba gunakan kata kunci yang lebih umum, periksa ejaan nomor pengadaan, atau cari berdasarkan nama tim / tahun.
          </p>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="item in results.data"
            :key="item.id"
            class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 hover:border-blue-300 transition shadow-xs"
          >
            <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
              <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-2">
                  <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">
                    {{ item.no_pengadaan }}
                  </span>
                  <span class="text-xs font-semibold text-slate-500">
                    Tahun {{ item.tahun }}
                  </span>
                </div>
                <h4 class="text-sm sm:text-base font-bold text-slate-900 mt-1 leading-snug">
                  {{ item.nama_pengadaan }}
                </h4>
              </div>

              <div v-if="item.bantex" class="shrink-0">
                <StatusBadge :status="item.bantex.status" />
              </div>
            </div>

            <div v-if="item.bantex" class="mt-3 pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div class="text-xs text-slate-600 space-y-1">
                <div class="flex items-center space-x-1.5 font-mono text-[11px] font-bold text-slate-800">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  <span>{{ item.bantex.kode_bantex }}</span>
                </div>
                <p class="text-[11px] text-slate-500">
                  <span class="font-semibold text-slate-700">Lokasi:</span> {{ item.bantex.keterangan_lokasi || `${item.bantex.nomor_rak}, ${item.bantex.baris}, ${item.bantex.urutan_bantex}` }}
                </p>
              </div>

              <Link
                :href="route('public.scan', item.bantex.slug)"
                class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 text-white hover:bg-blue-600 transition active:scale-95 shadow-2xs"
              >
                <span>Buka Bantex / Scan</span>
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
        class="bg-white rounded-2xl p-8 border border-slate-200 text-center shadow-xs"
      >
        <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-3">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
          </svg>
        </div>
        <h3 class="text-base font-bold text-slate-800 mb-1">
          Pencarian Dokumen Cepat
        </h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
          Masukkan kata kunci atau scan langsung label QR Code yang tertera pada bantex dokumen di rak ruang arsip.
        </p>
      </div>
    </div>
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({ q: '' }),
  },
  results: {
    type: Object,
    default: null,
  },
});

const searchQuery = ref(props.filters.q || '');

function performSearch() {
  if (!searchQuery.value.trim()) return;
  router.get(
    route('public.search'),
    { q: searchQuery.value.trim() },
    { preserveState: true, replace: true }
  );
}

function quickSearch(keyword) {
  searchQuery.value = keyword;
  performSearch();
}
</script>
