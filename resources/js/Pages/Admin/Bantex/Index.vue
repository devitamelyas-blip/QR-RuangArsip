<template>
  <AuthenticatedLayout :breadcrumbs="[{ label: 'Data Dokumen & Bantex SCM' }]">
    <Head title="Data Bantex & Dokumen Pengadaan SCM — Pelita Air" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 class="text-lg font-bold text-[#0A2540] tracking-tight">
            Data Dokumen & Lokasi Bantex SCM
          </h1>
          <p class="text-xs text-slate-500 mt-0.5">
            Daftar seluruh bantex pengadaan fisik divisi Supply Chain Management (SCM), kode posisi rak, dan manajemen label QR Code.
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <button
            v-if="selectedIds.length > 0"
            type="button"
            @click="printBatch"
            class="inline-flex items-center px-3.5 py-2 bg-emerald-700 text-white font-semibold text-xs rounded-md hover:bg-emerald-800 transition focus:outline-none shadow-2xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Label ({{ selectedIds.length }})</span>
          </button>

          <Link
            :href="route('admin.bantex.create')"
            class="inline-flex items-center px-3.5 py-2 bg-[#0A2540] text-white font-semibold text-xs rounded-md hover:bg-[#14385C] transition focus:outline-none shadow-2xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Bantex Baru</span>
          </Link>
        </div>
      </div>
    </template>

    <div class="space-y-4">
      <!-- Filter Toolbar -->
      <div class="bg-white rounded-lg p-3.5 border border-slate-200 shadow-2xs">
        <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5">
          <div class="flex-1 relative">
            <input
              type="text"
              v-model="filterForm.search"
              @input="onSearchInput"
              placeholder="Cari kode bantex, lokasi rak, atau nomor berkas pengadaan..."
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
              v-model="filterForm.tim"
              @change="applyFilters"
              class="text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
            >
              <option value="semua">Semua Tim / Unit</option>
              <option v-for="t in availableTeams" :key="t" :value="t">
                Tim {{ t }}
              </option>
            </select>

            <select
              v-model="filterForm.status"
              @change="applyFilters"
              class="text-xs rounded-md border-slate-300 focus:border-[#0A2540] focus:ring-[#0A2540] py-1.5 pl-2.5 pr-8"
            >
              <option value="semua">Semua Status</option>
              <option value="tersedia">Tersedia di Rak</option>
              <option value="dipinjam">Sedang Dipinjam</option>
            </select>

            <button
              type="submit"
              class="px-3.5 py-1.5 bg-[#0A2540] text-white rounded-md text-xs font-semibold hover:bg-[#14385C] transition focus:outline-none shadow-2xs"
            >
              Filter
            </button>
            <button
              v-if="filterForm.search || filterForm.tim !== 'semua' || filterForm.status !== 'semua'"
              type="button"
              @click="resetFilters"
              class="px-2.5 py-1.5 text-xs text-slate-600 hover:text-slate-900 transition"
            >
              Reset
            </button>
          </div>
        </form>
      </div>

      <!-- Professional Bantex Table -->
      <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-2xs">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
          <div>
            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
              Tabel Master Data Bantex Dokumen
            </h2>
            <p class="text-[11px] text-slate-500">
              Total {{ bantex.total || bantex.data.length }} unit bantex terdaftar
            </p>
          </div>
          <div v-if="selectedIds.length > 0" class="text-xs text-slate-600 font-medium">
            <span class="text-emerald-700 font-bold">{{ selectedIds.length }}</span> baris dipilih untuk cetak label
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                <th class="py-3 px-3.5 w-10 text-center">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="rounded border-slate-300 text-[#0A2540] focus:ring-[#0A2540]"
                    title="Pilih Semua di Halaman Ini"
                  />
                </th>
                <th class="py-3 px-4">Kode Bantex</th>
                <th class="py-3 px-4">Kelompok / Tim</th>
                <th class="py-3 px-4">Posisi Rak & Baris</th>
                <th class="py-3 px-4">Keterangan Lokasi</th>
                <th class="py-3 px-4 text-center">Jumlah Dokumen</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="bantex.data.length === 0">
                <td colspan="8" class="text-center py-12 text-slate-500">
                  <div class="max-w-xs mx-auto text-center space-y-1">
                    <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="font-medium text-slate-700">Tidak ada data bantex yang ditemukan</p>
                    <p class="text-[11px] text-slate-400">Silakan ubah kata kunci atau tambahkan data bantex baru.</p>
                  </div>
                </td>
              </tr>

              <tr
                v-for="item in bantex.data"
                :key="item.id"
                class="hover:bg-slate-50/80 transition"
                :class="{ 'bg-blue-50/20': selectedIds.includes(item.id) }"
              >
                <!-- Checkbox -->
                <td class="py-3 px-3.5 text-center">
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="selectedIds"
                    class="rounded border-slate-300 text-[#0A2540] focus:ring-[#0A2540]"
                  />
                </td>

                <!-- Kode Bantex -->
                <td class="py-3 px-4">
                  <div class="font-mono font-bold text-slate-900 text-xs">
                    <Link :href="route('admin.bantex.edit', item.id)" class="hover:text-blue-700 hover:underline">
                      {{ item.kode_bantex }}
                    </Link>
                  </div>
                  <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                    slug: {{ item.slug }}
                  </div>
                </td>

                <!-- Kelompok / Tim -->
                <td class="py-3 px-4 text-slate-700">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                    Tim {{ item.tim }}
                  </span>
                </td>

                <!-- Posisi Rak & Baris -->
                <td class="py-3 px-4 text-slate-700">
                  <div class="font-medium text-slate-800">{{ item.nomor_rak }}, {{ item.baris }}</div>
                  <div class="text-[11px] text-slate-500">{{ item.urutan_bantex }}</div>
                </td>

                <!-- Keterangan Lokasi -->
                <td class="py-3 px-4 text-slate-600 max-w-xs truncate" :title="item.keterangan_lokasi || '-'">
                  {{ item.keterangan_lokasi || '-' }}
                </td>

                <!-- Jumlah Dokumen -->
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ item.dokumen_count }} berkas
                  </span>
                </td>

                <!-- Status -->
                <td class="py-3 px-4">
                  <StatusBadge :status="item.status" />
                </td>

                <!-- Aksi -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end space-x-1.5">
                    <!-- Cetak Label QR -->
                    <Link
                      :href="route('admin.labels.print-single', item.id)"
                      target="_blank"
                      class="p-1.5 rounded-md text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition border border-transparent hover:border-emerald-200"
                      title="Cetak Label QR Code Bantex"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </Link>

                    <!-- Edit Bantex -->
                    <Link
                      :href="route('admin.bantex.edit', item.id)"
                      class="p-1.5 rounded-md text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition border border-transparent hover:border-blue-200"
                      title="Edit Data Bantex & Daftar Dokumen"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </Link>

                    <!-- Hapus Bantex -->
                    <button
                      type="button"
                      @click="confirmDelete(item)"
                      :disabled="item.status === 'dipinjam'"
                      class="p-1.5 rounded-md text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition border border-transparent hover:border-rose-200 disabled:opacity-30 disabled:cursor-not-allowed"
                      :title="item.status === 'dipinjam' ? 'Tidak dapat dihapus karena status masih dipinjam' : 'Hapus Data Bantex'"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Section -->
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50/50">
          <Pagination :links="bantex.links" />
        </div>
      </div>
    </div>

    <!-- Confirm Modal for Deletion -->
    <ConfirmModal
      :show="showDeleteModal"
      title="Konfirmasi Hapus Data Bantex"
      :message="itemToDelete ? `Apakah Anda yakin ingin menghapus data bantex '${itemToDelete.kode_bantex}'? Seluruh ${itemToDelete.dokumen_count} berkas dokumen di dalamnya akan ikut dihapus secara permanen.` : ''"
      confirm-text="Ya, Hapus Data"
      cancel-text="Batal"
      variant="rose"
      :loading="deleteForm.processing"
      @close="showDeleteModal = false"
      @confirm="submitDelete"
    />
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
  bantex: {
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
});

const filterForm = ref({
  search: props.filters.search || '',
  tim: props.filters.tim || 'semua',
  status: props.filters.status || 'semua',
});

let searchTimer = null;
function onSearchInput() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    applyFilters();
  }, 350);
}

function applyFilters() {
  router.get(route('admin.bantex.index'), filterForm.value, {
    preserveState: true,
    replace: true,
  });
}

function resetFilters() {
  filterForm.value = { search: '', tim: 'semua', status: 'semua' };
  applyFilters();
}

const selectedIds = ref([]);

const isAllSelected = computed(() => {
  if (props.bantex.data.length === 0) return false;
  return props.bantex.data.every((item) => selectedIds.value.includes(item.id));
});

function toggleSelectAll() {
  if (isAllSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = props.bantex.data.map((item) => item.id);
  }
}

function printBatch() {
  if (selectedIds.value.length === 0) return;
  const url = route('admin.labels.print-batch') + '?ids=' + selectedIds.value.join(',');
  window.open(url, '_blank');
}

const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const deleteForm = useForm({});

function confirmDelete(item) {
  itemToDelete.value = item;
  showDeleteModal.value = true;
}

function submitDelete() {
  if (!itemToDelete.value) return;
  deleteForm.delete(route('admin.bantex.destroy', itemToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false;
      itemToDelete.value = null;
    },
  });
}
</script>
