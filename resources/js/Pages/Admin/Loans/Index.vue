<template>
  <AuthenticatedLayout :breadcrumbs="[{ label: 'Riwayat Peminjaman SCM' }]">
    <Head title="Riwayat Peminjaman Dokumen SCM — Pelita Air" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
            Riwayat Peminjaman Dokumen SCM
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Log audit seluruh transaksi peminjaman fisik berkas pengadaan divisi Supply Chain Management (SCM), durasi sirkulasi, dan kepatuhan pengembalian.
          </p>
        </div>

        <!-- Export Buttons Group -->
        <div class="flex items-center space-x-2">
          <!-- Primary: Excel XLSX -->
          <button
            type="button"
            @click="exportReport('xlsx')"
            class="inline-flex items-center px-3.5 py-2 bg-emerald-700 text-white font-semibold text-xs rounded-md hover:bg-emerald-800 transition focus:outline-none shadow-2xs"
            title="Unduh laporan dalam format resmi Microsoft Excel (.xlsx)"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Ekspor Excel (.xlsx)</span>
          </button>

          <!-- Secondary: CSV -->
          <button
            type="button"
            @click="exportReport('csv')"
            class="inline-flex items-center px-3 py-2 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-md hover:bg-slate-50 transition focus:outline-none shadow-2xs"
            title="Unduh data mentah dalam format CSV"
          >
            <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>CSV</span>
          </button>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Filter Card -->
      <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-2xs">
        <form @submit.prevent="applyFilters">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Keyword with Live Typing Debounce -->
            <div class="lg:col-span-2">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                Kata Kunci Pencarian (Otomatis Filter)
              </label>
              <div class="relative">
                <input
                  type="text"
                  v-model="filterForm.search"
                  @input="onSearchInput"
                  placeholder="Cari peminjam, perihal, nomor/nama pengadaan, atau kode bantex..."
                  class="w-full pl-9 pr-8 py-1.5 text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540]"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <button
                  v-if="filterForm.search"
                  type="button"
                  @click="clearSearch"
                  class="absolute right-2 top-2 text-slate-400 hover:text-slate-600 transition"
                  title="Hapus kata kunci"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                Status Transaksi
              </label>
              <select
                v-model="filterForm.status"
                @change="applyFilters"
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
              >
                <option value="semua">Semua Status</option>
                <option value="dipinjam">Sedang Dipinjam</option>
                <option value="kembali">Sudah Kembali</option>
                <option value="terlambat">Terlambat (&gt; {{ dueDaysLimit }} hari)</option>
              </select>
            </div>

            <!-- Bantex Selector -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                Unit Bantex Spesifik
              </label>
              <select
                v-model="filterForm.bantex_id"
                @change="applyFilters"
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
              >
                <option value="semua">Semua Unit Bantex</option>
                <option v-for="b in bantexOptions" :key="b.id" :value="b.id">
                  {{ b.kode_bantex }}
                </option>
              </select>
            </div>

            <!-- Date From -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                Tanggal Pinjam Dari
              </label>
              <input
                type="date"
                v-model="filterForm.date_from"
                @change="applyFilters"
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 px-2.5"
              />
            </div>

            <!-- Date To -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                Sampai Tanggal
              </label>
              <input
                type="date"
                v-model="filterForm.date_to"
                @change="applyFilters"
                class="w-full text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 px-2.5"
              />
            </div>

            <!-- Filter Buttons -->
            <div class="sm:col-span-2 flex items-end justify-end space-x-2 pt-1">
              <button
                type="submit"
                class="px-4 py-1.5 bg-[#0A2540] text-white rounded-md text-xs font-semibold hover:bg-[#14385C] transition focus:outline-none shadow-2xs"
              >
                Terapkan Filter
              </button>
              <button
                type="button"
                @click="resetFilters"
                class="px-3 py-1.5 text-xs text-slate-600 hover:text-slate-900 transition"
              >
                Reset Semua
              </button>
            </div>
          </div>
        </form>

        <!-- Active Filter Chips -->
        <div v-if="hasActiveFilters" class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            Filter Aktif:
          </span>

          <span
            v-if="filterForm.search"
            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-700 border border-slate-200"
          >
            Kata Kunci: "{{ filterForm.search }}"
            <button type="button" @click="clearSearch" class="ml-1 text-slate-400 hover:text-slate-700">&times;</button>
          </span>

          <span
            v-if="filterForm.status !== 'semua'"
            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-blue-50 text-blue-700 border border-blue-200"
          >
            Status: {{ statusLabel(filterForm.status) }}
            <button type="button" @click="filterForm.status = 'semua'; applyFilters()" class="ml-1 text-blue-400 hover:text-blue-700">&times;</button>
          </span>

          <span
            v-if="filterForm.bantex_id !== 'semua'"
            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-700 border border-slate-200"
          >
            Bantex: {{ selectedBantexLabel }}
            <button type="button" @click="filterForm.bantex_id = 'semua'; applyFilters()" class="ml-1 text-slate-400 hover:text-slate-700">&times;</button>
          </span>

          <span
            v-if="filterForm.date_from || filterForm.date_to"
            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-slate-100 text-slate-700 border border-slate-200"
          >
            Periode: {{ filterForm.date_from || 'Awal' }} s/d {{ filterForm.date_to || 'Sekarang' }}
            <button type="button" @click="filterForm.date_from = ''; filterForm.date_to = ''; applyFilters()" class="ml-1 text-slate-400 hover:text-slate-700">&times;</button>
          </span>

          <button
            type="button"
            @click="resetFilters"
            class="text-[11px] text-rose-600 hover:text-rose-800 font-semibold underline ml-1"
          >
            Bersihkan Semua
          </button>
        </div>
      </div>

      <!-- History Table Card -->
      <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-2xs">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
          <div class="flex items-center space-x-2">
            <div>
              <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Tabel Catatan Transaksi Peminjaman SCM
              </h2>
              <p class="text-[11px] text-slate-500">
                Total {{ loans.total || loans.data.length }} riwayat transaksi tercatat di database
              </p>
            </div>
            <div v-if="isFiltering" class="inline-flex items-center text-xs text-blue-700 font-medium">
              <svg class="animate-spin h-3.5 w-3.5 mr-1 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <span>Memperbarui...</span>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto" :class="{ 'opacity-60 pointer-events-none transition-opacity': isFiltering }">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Bantex & Lokasi Rak</th>
                <th class="py-3 px-4">Nama Peminjam</th>
                <th class="py-3 px-4">Keperluan Peminjaman</th>
                <th class="py-3 px-4">Waktu Pinjam</th>
                <th class="py-3 px-4">Waktu Pengembalian</th>
                <th class="py-3 px-4 text-center">Durasi</th>
                <th class="py-3 px-4 text-right">Status Sirkulasi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-if="loans.data.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400">
                  <div class="max-w-xs mx-auto space-y-2">
                    <svg class="w-8 h-8 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="font-medium text-slate-600 text-xs">Tidak ada data transaksi yang sesuai filter.</p>
                    <p class="text-[11px] text-slate-400">Silakan ubah kata kunci pencarian atau bersihkan filter.</p>
                    <button
                      v-if="hasActiveFilters"
                      type="button"
                      @click="resetFilters"
                      class="mt-2 inline-flex items-center px-3 py-1 rounded bg-[#0A2540] text-white text-xs font-semibold hover:bg-[#14385C] transition"
                    >
                      Reset Semua Filter
                    </button>
                  </div>
                </td>
              </tr>

              <tr
                v-for="item in loans.data"
                :key="item.id"
                class="hover:bg-slate-50/80 transition"
                :class="{ 'bg-rose-50/20': item.is_overdue }"
              >
                <!-- Bantex & Lokasi -->
                <td class="py-3 px-4 font-mono">
                  <span v-if="item.bantex">
                    <Link
                      v-if="item.bantex.slug"
                      :href="route('public.scan', item.bantex.slug)"
                      target="_blank"
                      class="font-bold text-slate-900 hover:text-blue-700 hover:underline"
                    >
                      {{ item.bantex.kode_bantex }}
                    </Link>
                    <span v-else class="font-bold text-slate-900">
                      {{ item.bantex.kode_bantex }}
                    </span>
                    <span class="block text-[11px] font-sans font-normal text-slate-500 mt-0.5">
                      {{ item.bantex.nomor_rak }}, {{ item.bantex.baris }} (Tim {{ item.bantex.tim }})
                    </span>
                  </span>
                  <span v-else class="text-slate-400 italic font-sans">Unit bantex telah dihapus</span>
                </td>

                <!-- Peminjam -->
                <td class="py-3 px-4 font-semibold text-slate-900">
                  {{ item.nama_peminjam }}
                </td>

                <!-- Keperluan -->
                <td class="py-3 px-4 text-slate-600 max-w-xs truncate" :title="item.keperluan">
                  {{ item.keperluan }}
                </td>

                <!-- Waktu Pinjam -->
                <td class="py-3 px-4 text-slate-600 whitespace-nowrap">
                  {{ item.tanggal_pinjam }}
                </td>

                <!-- Waktu Kembali -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <span v-if="item.tanggal_kembali" class="text-emerald-700 font-medium">
                    {{ item.tanggal_kembali }}
                  </span>
                  <span v-else class="text-amber-700 italic">
                    Belum dikembalikan
                  </span>
                </td>

                <!-- Durasi -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    {{ item.lama_pinjam_hari }} hari
                  </span>
                </td>

                <!-- Status -->
                <td class="py-3 px-4 text-right">
                  <span
                    v-if="item.is_overdue"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                    Terlambat
                  </span>
                  <span
                    v-else-if="item.tanggal_kembali"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                    Selesai
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                    Dipinjam
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Section -->
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
          <Pagination :links="loans.links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  loans: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: true,
  },
  allBantex: {
    type: Array,
    default: () => [],
  },
  availableBantex: {
    type: Array,
    default: () => [],
  },
  loan_due_days: {
    type: Number,
    default: 7,
  },
  loanDueDays: {
    type: Number,
    default: 7,
  },
});

const isFiltering = ref(false);

const bantexOptions = computed(() => {
  if (props.allBantex && props.allBantex.length > 0) {
    return props.allBantex;
  }
  if (props.availableBantex && props.availableBantex.length > 0) {
    return props.availableBantex;
  }
  return [];
});

const dueDaysLimit = computed(() => {
  return props.loan_due_days || props.loanDueDays || 7;
});

const filterForm = ref({
  search: props.filters.search || '',
  status: props.filters.status || 'semua',
  bantex_id: props.filters.bantex_id || 'semua',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const hasActiveFilters = computed(() => {
  return (
    !!filterForm.value.search ||
    filterForm.value.status !== 'semua' ||
    filterForm.value.bantex_id !== 'semua' ||
    !!filterForm.value.date_from ||
    !!filterForm.value.date_to
  );
});

const selectedBantexLabel = computed(() => {
  const found = bantexOptions.value.find((b) => String(b.id) === String(filterForm.value.bantex_id));
  return found ? found.kode_bantex : filterForm.value.bantex_id;
});

function statusLabel(status) {
  if (status === 'dipinjam') return 'Sedang Dipinjam';
  if (status === 'kembali') return 'Sudah Kembali';
  if (status === 'terlambat') return 'Terlambat';
  return status;
}

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilters();
  }, 350);
}

function clearSearch() {
  filterForm.value.search = '';
  applyFilters();
}

function applyFilters() {
  isFiltering.value = true;
  router.get(route('admin.loans.index'), filterForm.value, {
    preserveState: true,
    replace: true,
    onFinish: () => {
      isFiltering.value = false;
    },
  });
}

function resetFilters() {
  filterForm.value = {
    search: '',
    status: 'semua',
    bantex_id: 'semua',
    date_from: '',
    date_to: '',
  };
  applyFilters();
}

function exportReport(format = 'xlsx') {
  const queryParams = new URLSearchParams();
  Object.keys(filterForm.value).forEach((key) => {
    if (filterForm.value[key] && filterForm.value[key] !== 'semua') {
      queryParams.append(key, filterForm.value[key]);
    }
  });
  queryParams.append('format', format);

  window.location.href = route('admin.loans.export') + '?' + queryParams.toString();
}
</script>
