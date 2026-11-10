<template>
  <AuthenticatedLayout>
    <Head title="Dashboard Ruang Arsip SCM — Pelita Air" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
            Dashboard Ruang Arsip SCM
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Monitoring ketersediaan fisik bantex, status sirkulasi dokumen, dan pelacakan peminjaman aktif divisi Supply Chain Management (SCM).
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <Link
            :href="route('admin.bantex.create')"
            class="inline-flex items-center px-3.5 py-2 bg-[#0A2540] text-white font-semibold text-xs rounded-md hover:bg-[#14385C] transition focus:outline-none shadow-xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Bantex Baru</span>
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-5">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-2xs">
          <div class="flex items-center justify-between text-slate-500 mb-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-600">Total Bantex</span>
            <div class="w-7 h-7 rounded bg-slate-100 flex items-center justify-center text-slate-600">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
            </div>
          </div>
          <div class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ stats.total_bantex }}
          </div>
          <span class="text-[11px] text-slate-400 mt-0.5 block">Seluruh unit terdaftar</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-2xs">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-800">Dokumen Tersedia</span>
            <div class="w-7 h-7 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>
          <div class="text-2xl font-bold text-emerald-700 tracking-tight">
            {{ stats.tersedia }}
          </div>
          <span class="text-[11px] text-slate-400 mt-0.5 block">Siap dipinjam di rak</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-2xs">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-800">Sedang Dipinjam</span>
            <div class="w-7 h-7 rounded bg-amber-50 text-amber-700 flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl font-bold text-amber-700 tracking-tight">
            {{ stats.dipinjam }}
          </div>
          <span class="text-[11px] text-slate-400 mt-0.5 block">Dokumen aktif di luar rak</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-2xs">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-rose-800">Terlambat Pinjam</span>
            <div class="w-7 h-7 rounded bg-rose-50 text-rose-700 flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl font-bold text-rose-700 tracking-tight">
            {{ stats.terlambat }}
          </div>
          <span class="text-[11px] text-slate-400 mt-0.5 block">&gt; {{ stats.loan_due_days }} hari belum kembali</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-2xs">
        <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5">
          <div class="flex-1 relative">
            <input
              type="text"
              v-model="filterForm.search"
              @input="onSearchInput"
              placeholder="Cari kode bantex, nomor rak, uraian lokasi, atau nama peminjam..."
              class="w-full pl-9 pr-8 py-1.5 text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540]"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button
              v-if="filterForm.search"
              type="button"
              @click="filterForm.search = ''; applyFilters()"
              class="absolute right-2 top-2 text-slate-400 hover:text-slate-600 transition"
              title="Hapus pencarian"
            >
              <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <select
              v-model="filterForm.status"
              @change="applyFilters"
              class="text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
            >
              <option value="semua">Semua Status Fisik</option>
              <option value="tersedia">Tersedia di Rak</option>
              <option value="dipinjam">Sedang Dipinjam</option>
              <option value="terlambat">Hanya Terlambat</option>
            </select>

            <select
              v-model="filterForm.tim"
              @change="applyFilters"
              class="text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
            >
              <option value="semua">Semua Kelompok / Tim</option>
              <option v-for="t in availableTeams" :key="t" :value="t">
                Tim {{ t }}
              </option>
            </select>

            <button
              type="submit"
              class="px-3.5 py-1.5 bg-[#0A2540] text-white rounded-md text-xs font-semibold hover:bg-[#14385C] transition focus:outline-none shadow-2xs"
            >
              Filter
            </button>
            <button
              v-if="filterForm.search || filterForm.status !== 'semua' || filterForm.tim !== 'semua'"
              type="button"
              @click="resetFilters"
              class="px-2.5 py-1.5 text-xs text-slate-600 hover:text-slate-900 transition"
            >
              Reset
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-2xs">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
          <div>
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
              Monitoring Ketersediaan Fisik Bantex SCM
            </h2>
            <p class="text-[11px] text-slate-500">
              Menampilkan {{ bantexList.data.length }} dari {{ bantexList.total || bantexList.data.length }} data bantex
            </p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Kode Bantex</th>
                <th class="py-3 px-4">Lokasi Rak & Posisi</th>
                <th class="py-3 px-4">Jumlah Berkas</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Peminjam Saat Ini</th>
                <th class="py-3 px-4 text-right">Aksi Operasional</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="bantexList.data.length === 0">
                <td colspan="6" class="text-center py-12 text-slate-500">
                  <div class="max-w-xs mx-auto text-center space-y-1">
                    <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="font-medium text-slate-700">Tidak ada data ditemukan</p>
                    <p class="text-[11px] text-slate-400">Silakan sesuaikan kata kunci atau filter pencarian Anda.</p>
                  </div>
                </td>
              </tr>

              <tr
                v-for="item in bantexList.data"
                :key="item.id"
                class="hover:bg-slate-50/80 transition"
                :class="{ 'bg-amber-50/20': item.status === 'dipinjam' }"
              >
                <td class="py-3 px-4">
                  <div class="font-mono font-bold text-slate-900 text-xs">
                    <Link :href="route('admin.bantex.edit', item.id)" class="hover:text-blue-700 hover:underline">
                      {{ item.kode_bantex }}
                    </Link>
                  </div>
                  <div class="text-[11px] text-slate-500 mt-0.5">
                    Kelompok / Tim: <span class="font-semibold text-slate-700">{{ item.tim }}</span>
                  </div>
                </td>

                <td class="py-3 px-4 text-slate-700">
                  <div class="font-medium text-slate-800">{{ item.nomor_rak }}, {{ item.baris }}</div>
                  <div class="text-[11px] text-slate-500">{{ item.urutan_bantex }}</div>
                </td>

                <td class="py-3 px-4 text-slate-600">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    {{ item.dokumen_count }} berkas
                  </span>
                </td>

                <td class="py-3 px-4">
                  <StatusBadge
                    :status="item.status"
                    :is-overdue="item.peminjaman_aktif?.is_overdue || false"
                  />
                </td>

                <td class="py-3 px-4">
                  <div v-if="item.peminjaman_aktif" class="space-y-0.5">
                    <div class="font-semibold text-slate-900">{{ item.peminjaman_aktif.nama_peminjam }}</div>
                    <div class="text-[11px] text-slate-600 truncate max-w-xs" :title="item.peminjaman_aktif.keperluan">
                      {{ item.peminjaman_aktif.keperluan }}
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center space-x-1.5">
                      <span>{{ item.peminjaman_aktif.tanggal_pinjam }}</span>
                      <span
                        v-if="item.peminjaman_aktif.is_overdue"
                        class="text-rose-700 font-bold bg-rose-50 px-1.5 py-0.2 rounded border border-rose-200"
                      >
                        Terlambat ({{ item.peminjaman_aktif.lama_pinjam_hari }} hari)
                      </span>
                      <span v-else class="text-slate-500">
                        ({{ item.peminjaman_aktif.lama_pinjam_hari }} hari)
                      </span>
                    </div>
                  </div>
                  <span v-else class="text-slate-400 text-xs italic">
                    Tersedia di rak arsip
                  </span>
                </td>

                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end space-x-1.5">
                    <button
                      v-if="item.status === 'dipinjam'"
                      type="button"
                      @click="openManualReturnModal(item)"
                      class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 transition"
                      title="Proses pengembalian fisik bantex ke rak"
                    >
                      Kembalikan
                    </button>

                    <Link
                      :href="route('admin.labels.print-single', item.id)"
                      target="_blank"
                      class="p-1.5 rounded-md text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition border border-transparent hover:border-slate-200"
                      title="Cetak Label QR Code"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </Link>

                    <Link
                      :href="route('admin.bantex.edit', item.id)"
                      class="p-1.5 rounded-md text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition border border-transparent hover:border-blue-100"
                      title="Edit Data Bantex"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </Link>

                    <Link
                      :href="route('public.scan', item.slug)"
                      target="_blank"
                      class="p-1.5 rounded-md text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition border border-transparent hover:border-blue-100"
                      title="Buka Halaman Scan Publik"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
          <Pagination :links="bantexList.links" />
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showConfirmModal"
      title="Konfirmasi Pengembalian Bantex Manual"
      :message="selectedBantex ? `Pastikan bantex '${selectedBantex.kode_bantex}' telah benar-benar dikembalikan ke posisi ${selectedBantex.nomor_rak}, ${selectedBantex.baris}. Waktu pengembalian akan dicatat sekarang.` : ''"
      confirm-text="Konfirmasi Pengembalian"
      cancel-text="Batal"
      variant="emerald"
      :loading="manualReturnForm.processing"
      @close="showConfirmModal = false"
      @confirm="submitManualReturn"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
  stats: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: true,
  },
  availableTeams: {
    type: Array,
    required: true,
  },
  bantexList: {
    type: Object,
    required: true,
  },
});

const filterForm = ref({
  search: props.filters.search || '',
  status: props.filters.status || 'semua',
  tim: props.filters.tim || 'semua',
});

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilters();
  }, 350);
}

function applyFilters() {
  router.get(route('admin.dashboard'), filterForm.value, {
    preserveState: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.value = { search: '', status: 'semua', tim: 'semua' };
  applyFilters();
}

const showConfirmModal = ref(false);
const selectedBantex = ref(null);
const manualReturnForm = useForm({});

function openManualReturnModal(item) {
  selectedBantex.value = item;
  showConfirmModal.value = true;
}

function submitManualReturn() {
  if (!selectedBantex.value) return;
  manualReturnForm.post(route('admin.dashboard.return-manual', selectedBantex.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showConfirmModal.value = false;
      selectedBantex.value = null;
    },
  });
}
</script>
