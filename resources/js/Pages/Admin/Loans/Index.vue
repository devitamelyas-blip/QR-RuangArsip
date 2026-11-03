<template>
  <AuthenticatedLayout>
    <Head title="Riwayat Peminjaman Ruang Arsip" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold leading-tight text-slate-900">
            Riwayat Peminjaman Dokumen
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Log seluruh transaksi pengambilan dan pengembalian bantex beserta analisis durasi peminjaman.
          </p>
        </div>

        <div>
          <!-- Tombol Export CSV -->
          <button
            type="button"
            @click="exportCsv"
            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition active:scale-95 shadow-xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Laporan CSV
          </button>
        </div>
      </div>
    </template>

    <div class="space-y-5">
      <!-- Filter Bar Lengkap -->
      <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-3">
        <form @submit.prevent="applyFilters">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Pencarian Bebas -->
            <div class="lg:col-span-2 relative">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                Pencarian
              </label>
              <div class="relative">
                <input
                  type="text"
                  v-model="filterForm.search"
                  placeholder="Nama peminjam, keperluan, atau kode bantex..."
                  class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Filter Status Peminjaman -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                Status
              </label>
              <select
                v-model="filterForm.status"
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 pl-3 pr-8"
              >
                <option value="semua">Semua Status</option>
                <option value="dipinjam">Sedang Dipinjam</option>
                <option value="kembali">Sudah Kembali</option>
                <option value="terlambat">Terlambat (&gt; {{ loanDueDays }} hari)</option>
              </select>
            </div>

            <!-- Filter Bantex Tertentu -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                Pilih Bantex
              </label>
              <select
                v-model="filterForm.bantex_id"
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 pl-3 pr-8"
              >
                <option value="semua">Semua Bantex</option>
                <option v-for="b in availableBantex" :key="b.id" :value="b.id">
                  {{ b.kode_bantex }}
                </option>
              </select>
            </div>

            <!-- Rentang Tanggal Mulai -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                Tanggal Pinjam Dari
              </label>
              <input
                type="date"
                v-model="filterForm.date_from"
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-1.5 px-3"
              />
            </div>

            <!-- Rentang Tanggal Sampai -->
            <div>
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                Sampai Tanggal
              </label>
              <input
                type="date"
                v-model="filterForm.date_to"
                class="w-full text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-1.5 px-3"
              />
            </div>

            <!-- Tombol Submit & Reset Filter -->
            <div class="sm:col-span-2 flex items-end justify-end space-x-2 pt-1">
              <button
                type="submit"
                class="px-5 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition shadow-xs"
              >
                Terapkan Filter
              </button>
              <button
                type="button"
                @click="resetFilters"
                class="px-3.5 py-2 text-xs text-slate-600 hover:text-slate-900 transition"
              >
                Reset
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3.5 px-4">Bantex & Lokasi</th>
                <th class="py-3.5 px-4">Peminjam</th>
                <th class="py-3.5 px-4">Keperluan</th>
                <th class="py-3.5 px-4">Waktu Pinjam</th>
                <th class="py-3.5 px-4">Waktu Kembali</th>
                <th class="py-3.5 px-4 text-center">Durasi</th>
                <th class="py-3.5 px-4 text-right">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="loans.data.length === 0">
                <td colspan="7" class="text-center py-10 text-slate-400">
                  Tidak ada riwayat peminjaman yang cocok dengan filter.
                </td>
              </tr>
              <tr
                v-for="item in loans.data"
                :key="item.id"
                class="hover:bg-slate-50/70 transition"
                :class="{ 'bg-rose-50/20': item.is_overdue }"
              >
                <!-- Bantex -->
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                  <span v-if="item.bantex">
                    <Link :href="route('public.scan', item.bantex.slug)" target="_blank" class="hover:text-blue-600 hover:underline">
                      {{ item.bantex.kode_bantex }}
                    </Link>
                    <span class="block text-[11px] font-sans font-normal text-slate-400">
                      {{ item.bantex.nomor_rak }}, {{ item.bantex.baris }} (Tim {{ item.bantex.tim }})
                    </span>
                  </span>
                  <span v-else class="text-slate-400 italic">Bantex dihapus</span>
                </td>

                <!-- Peminjam -->
                <td class="py-3.5 px-4 font-bold text-slate-800">
                  {{ item.nama_peminjam }}
                </td>

                <!-- Keperluan -->
                <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" :title="item.keperluan">
                  {{ item.keperluan }}
                </td>

                <!-- Tanggal Pinjam -->
                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap">
                  {{ item.tanggal_pinjam }}
                </td>

                <!-- Tanggal Kembali -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <span v-if="item.tanggal_kembali" class="text-emerald-700 font-medium">
                    {{ item.tanggal_kembali }}
                  </span>
                  <span v-else class="text-amber-700 italic">
                    Belum kembali
                  </span>
                </td>

                <!-- Durasi Pinjam -->
                <td class="py-3.5 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                    {{ item.lama_pinjam_hari }} hari
                  </span>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-4 text-right">
                  <span
                    v-if="item.is_overdue"
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-300"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse mr-1.5"></span>
                    Terlambat
                  </span>
                  <span
                    v-else-if="item.is_returned"
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-300"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                    Selesai
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-300"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                    Dipinjam
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200">
          <Pagination :links="loans.links" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
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
  availableBantex: {
    type: Array,
    required: true,
  },
  loanDueDays: {
    type: Number,
    default: 7,
  },
});

const filterForm = ref({
  search: props.filters.search || '',
  status: props.filters.status || 'semua',
  bantex_id: props.filters.bantex_id || 'semua',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

function applyFilters() {
  router.get(route('admin.loans.index'), filterForm.value, {
    preserveState: true,
    replace: true,
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

function exportCsv() {
  const queryParams = new URLSearchParams();
  Object.keys(filterForm.value).forEach((key) => {
    if (filterForm.value[key]) {
      queryParams.append(key, filterForm.value[key]);
    }
  });

  window.location.href = route('admin.loans.export') + '?' + queryParams.toString();
}
</script>

