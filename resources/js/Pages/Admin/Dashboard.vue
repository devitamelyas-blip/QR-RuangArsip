<template>
  <AuthenticatedLayout>
    <Head title="Dashboard Monitoring Ruang Arsip" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold leading-tight text-slate-900">
            Dashboard Monitoring Arsip
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Pantau ketersediaan fisik bantex, peminjam aktif, dan peringatan keterlambatan.
          </p>
        </div>
        <div class="flex items-center space-x-2.5">
          <Link
            :href="route('admin.bantex.create')"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold text-xs rounded-xl hover:bg-blue-700 transition active:scale-95 shadow-xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Bantex
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="flex items-center justify-between text-slate-500 mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider">Total Bantex</span>
            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">
            {{ stats.total_bantex }}
          </div>
          <span class="text-[11px] text-slate-400 mt-1 block">Seluruh unit terdaftar</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="flex items-center justify-between text-slate-500 mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Tersedia di Rak</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">
            {{ stats.tersedia }}
          </div>
          <span class="text-[11px] text-slate-400 mt-1 block">Siap dipinjam</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="flex items-center justify-between text-slate-500 mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Sedang Dipinjam</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">
            {{ stats.dipinjam }}
          </div>
          <span class="text-[11px] text-slate-400 mt-1 block">Dokumen di luar rak</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
          <div class="flex items-center justify-between text-slate-500 mb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-700">Terlambat Pinjam</span>
            <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
          </div>
          <div class="text-2xl sm:text-3xl font-extrabold text-rose-600">
            {{ stats.terlambat }}
          </div>
          <span class="text-[11px] text-slate-400 mt-1 block">&gt; {{ stats.loan_due_days }} hari belum kembali</span>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
        <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
          <div class="flex-1 relative">
            <input
              type="text"
              v-model="filterForm.search"
              placeholder="Cari kode bantex, keterangan lokasi, nama peminjam..."
              class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <select
              v-model="filterForm.status"
              @change="applyFilters"
              class="text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 pl-3 pr-8"
            >
              <option value="semua">Semua Status</option>
              <option value="tersedia">Tersedia di Rak</option>
              <option value="dipinjam">Sedang Dipinjam</option>
              <option value="terlambat">Hanya Terlambat</option>
            </select>

            <select
              v-model="filterForm.tim"
              @change="applyFilters"
              class="text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 pl-3 pr-8"
            >
              <option value="semua">Semua Tim</option>
              <option v-for="t in availableTeams" :key="t" :value="t">
                Tim {{ t }}
              </option>
            </select>

            <button
              type="submit"
              class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition shadow-xs"
            >
              Terapkan
            </button>
            <button
              v-if="filterForm.search || filterForm.status !== 'semua' || filterForm.tim !== 'semua'"
              type="button"
              @click="resetFilters"
              class="px-3 py-2 text-xs text-slate-600 hover:text-slate-900 transition"
            >
              Reset
            </button>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4">Kode Bantex</th>
                <th class="py-3.5 px-4">Posisi Rak</th>
                <th class="py-3.5 px-4">Isi Dokumen</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4">Peminjam Saat Ini</th>
                <th class="py-3.5 px-4 text-right">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="bantexList.data.length === 0">
                <td colspan="6" class="text-center py-10 text-slate-400">
                  Tidak ada data bantex yang sesuai dengan filter pencarian.
                </td>
              </tr>
              <tr
                v-for="item in bantexList.data"
                :key="item.id"
                class="hover:bg-slate-50/70 transition"
                :class="{ 'bg-amber-50/20': item.status === 'dipinjam' }"
              >
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                  <Link :href="route('admin.bantex.edit', item.id)" class="hover:text-blue-600 hover:underline">
                    {{ item.kode_bantex }}
                  </Link>
                  <span class="block text-[11px] font-sans font-normal text-slate-400 mt-0.5">
                    Tim {{ item.tim }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-slate-700">
                  <div class="font-semibold">{{ item.nomor_rak }}, {{ item.baris }}</div>
                  <div class="text-[11px] text-slate-500">{{ item.urutan_bantex }}</div>
                </td>

                <td class="py-3.5 px-4 text-slate-600">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium text-[11px]">
                    {{ item.dokumen_count }} berkas
                  </span>
                </td>

                <td class="py-3.5 px-4">
                  <StatusBadge
                    :status="item.status"
                    :is-overdue="item.peminjaman_aktif?.is_overdue || false"
                  />
                </td>

                <td class="py-3.5 px-4">
                  <div v-if="item.peminjaman_aktif" class="space-y-0.5">
                    <div class="font-bold text-slate-900">{{ item.peminjaman_aktif.nama_peminjam }}</div>
                    <div class="text-[11px] text-slate-500 truncate max-w-xs" :title="item.peminjaman_aktif.keperluan">
                      {{ item.peminjaman_aktif.keperluan }}
                    </div>
                    <div class="text-[10px] text-slate-400">
                      {{ item.peminjaman_aktif.tanggal_pinjam }}
                      <span
                        v-if="item.peminjaman_aktif.is_overdue"
                        class="text-rose-600 font-bold ml-1"
                      >
                        ({{ item.peminjaman_aktif.lama_pinjam_hari }} hari)
                      </span>
                    </div>
                  </div>
                  <span v-else class="text-slate-400 text-xs italic">
                    Di dalam rak
                  </span>
                </td>

                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end space-x-1.5">
                    <button
                      v-if="item.status === 'dipinjam'"
                      type="button"
                      @click="openManualReturnModal(item)"
                      class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition"
                      title="Kembalikan manual bila peminjam lupa scan QR"
                    >
                      Kembalikan
                    </button>

                    <Link
                      :href="route('admin.labels.print-single', item.id)"
                      target="_blank"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition"
                      title="Cetak Label QR Code"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </Link>

                    <Link
                      :href="route('public.scan', item.slug)"
                      target="_blank"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 transition"
                      title="Buka Halaman Scan Publik"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t border-slate-200">
          <Pagination :links="bantexList.links" />
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showConfirmModal"
      title="Kembalikan Bantex Manual"
      :message="selectedBantex ? `Kembalikan bantex '${selectedBantex.kode_bantex}' secara manual ke rak? Tanggal kembali akan dicatat sekarang.` : ''"
      confirm-text="Ya, Kembalikan ke Rak"
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
