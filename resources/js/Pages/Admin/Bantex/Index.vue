<template>
  <AuthenticatedLayout>
    <Head title="Master Data Bantex Ruang Arsip" />

    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="text-xl font-bold leading-tight text-slate-900">
            Master Data Bantex
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Kelola kode lokasi fisik, data berkas dokumen pengadaan, dan cetak label QR Code.
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <button
            v-if="selectedIds.length > 0"
            type="button"
            @click="printBatch"
            class="inline-flex items-center px-3.5 py-2 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition active:scale-95 shadow-xs"
          >
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Label Terpilih ({{ selectedIds.length }})
          </button>

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

    <div class="space-y-5">
      <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
        <form @submit.prevent="applyFilters" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
          <div class="flex-1 relative">
            <input
              type="text"
              v-model="filterForm.search"
              placeholder="Cari kode bantex, lokasi rak, atau nomor dokumen..."
              class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <div class="flex flex-wrap items-center gap-2">
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

            <select
              v-model="filterForm.status"
              @change="applyFilters"
              class="text-xs rounded-xl border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-2 pl-3 pr-8"
            >
              <option value="semua">Semua Status</option>
              <option value="tersedia">Tersedia di Rak</option>
              <option value="dipinjam">Sedang Dipinjam</option>
            </select>

            <button
              type="submit"
              class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition shadow-xs"
            >
              Filter
            </button>
            <button
              v-if="filterForm.search || filterForm.tim !== 'semua' || filterForm.status !== 'semua'"
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
                <th class="py-3.5 px-4 w-10 text-center">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                </th>
                <th class="py-3.5 px-4">Kode Bantex</th>
                <th class="py-3.5 px-4">Kelompok / Tim</th>
                <th class="py-3.5 px-4">Posisi Rak & Baris</th>
                <th class="py-3.5 px-4">Jumlah Dokumen</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="bantex.data.length === 0">
                <td colspan="7" class="text-center py-10 text-slate-400">
                  Tidak ada data bantex yang ditemukan.
                </td>
              </tr>
              <tr
                v-for="item in bantex.data"
                :key="item.id"
                class="hover:bg-slate-50/70 transition"
                :class="{ 'bg-blue-50/30': selectedIds.includes(item.id) }"
              >
                <td class="py-3.5 px-4 text-center">
                  <input
                    type="checkbox"
                    :value="item.id"
                    v-model="selectedIds"
                    class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                  />
                </td>

                <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                  <Link :href="route('admin.bantex.edit', item.id)" class="hover:text-blue-600 hover:underline">
                    {{ item.kode_bantex }}
                  </Link>
                  <span class="block text-[11px] font-sans font-normal text-slate-400 mt-0.5">
                    Slug: {{ item.slug }}
                  </span>
                </td>

                <td class="py-3.5 px-4 text-slate-700 font-semibold">
                  Tim {{ item.tim }}
                </td>

                <td class="py-3.5 px-4 text-slate-700">
                  <span class="font-semibold">{{ item.nomor_rak }}, {{ item.baris }}</span>
                  <span class="block text-[11px] text-slate-400">{{ item.urutan_bantex }}</span>
                </td>

                <td class="py-3.5 px-4 text-slate-600">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium text-[11px]">
                    {{ item.dokumen_count }} dokumen
                  </span>
                </td>

                <td class="py-3.5 px-4">
                  <StatusBadge :status="item.status" />
                </td>

                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end space-x-1.5">
                    <Link
                      :href="route('admin.labels.print-single', item.id)"
                      target="_blank"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition"
                      title="Cetak Label QR Code"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                      </svg>
                    </Link>

                    <Link
                      :href="route('admin.bantex.edit', item.id)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition"
                      title="Edit Bantex & Dokumen"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </Link>

                    <button
                      type="button"
                      @click="confirmDelete(item)"
                      :disabled="item.status === 'dipinjam'"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition disabled:opacity-30 disabled:cursor-not-allowed"
                      :title="item.status === 'dipinjam' ? 'Tidak dapat dihapus karena sedang dipinjam' : 'Hapus Bantex'"
                    >
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 border-t border-slate-200">
          <Pagination :links="bantex.links" />
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showDeleteModal"
      title="Hapus Bantex"
      :message="itemToDelete ? `Apakah Anda yakin ingin menghapus bantex '${itemToDelete.kode_bantex}'? Seluruh data dokumen di dalamnya akan ikut terhapus permanen.` : ''"
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
