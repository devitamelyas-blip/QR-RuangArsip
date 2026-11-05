<template>
  <PublicLayout>
    <Head :title="`Bantex ${bantex.kode_bantex}`" />

    <div class="space-y-5 pb-8">
      <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
          <div class="min-w-0">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1">
              Kode Lokasi Bantex
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight break-all font-mono">
              {{ bantex.kode_bantex }}
            </h2>
          </div>
          <StatusBadge
            :status="bantex.status"
            :is-overdue="bantex.peminjaman_aktif?.is_overdue || false"
          />
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-3 border-t border-slate-100 text-xs">
          <div class="bg-slate-50 rounded-xl p-2.5">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Tim</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.tim }}</span>
          </div>
          <div class="bg-slate-50 rounded-xl p-2.5">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Nomor Rak</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.nomor_rak }}</span>
          </div>
          <div class="bg-slate-50 rounded-xl p-2.5">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Baris</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.baris }}</span>
          </div>
          <div class="bg-slate-50 rounded-xl p-2.5">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Posisi</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.urutan_bantex }}</span>
          </div>
        </div>

        <div v-if="bantex.keterangan_lokasi" class="mt-3 text-xs text-slate-600 bg-blue-50/70 border border-blue-100/80 rounded-xl p-2.5 flex items-start space-x-2">
          <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <span class="font-semibold text-blue-900">Petunjuk Lokasi: </span>
            <span>{{ bantex.keterangan_lokasi }}</span>
          </div>
        </div>
      </div>

      <div
        v-if="bantex.status === 'dipinjam' && bantex.peminjaman_aktif"
        class="bg-amber-50/80 rounded-2xl p-5 border border-amber-200/90 shadow-xs"
      >
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-bold text-amber-900 flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Sedang Dipinjam
          </h3>
          <span
            v-if="bantex.peminjaman_aktif.is_overdue"
            class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 border border-rose-300"
          >
            Terlambat ({{ bantex.peminjaman_aktif.lama_pinjam_hari }} hari)
          </span>
          <span v-else class="text-[11px] text-amber-800 font-medium">
            Dipinjam {{ bantex.peminjaman_aktif.lama_pinjam_hari }} hari lalu
          </span>
        </div>

        <div class="space-y-2 text-xs text-slate-800 bg-white/80 rounded-xl p-3 border border-amber-100">
          <div>
            <span class="text-slate-500 font-medium">Nama Peminjam:</span>
            <p class="font-bold text-slate-900 text-sm mt-0.5">{{ bantex.peminjaman_aktif.nama_peminjam }}</p>
          </div>
          <div>
            <span class="text-slate-500 font-medium">Keperluan:</span>
            <p class="text-slate-700 mt-0.5 whitespace-pre-line">{{ bantex.peminjaman_aktif.keperluan }}</p>
          </div>
          <div>
            <span class="text-slate-500 font-medium">Waktu Pinjam:</span>
            <p class="text-slate-700 mt-0.5">{{ bantex.peminjaman_aktif.tanggal_pinjam }}</p>
          </div>
        </div>

        <div class="mt-4">
          <button
            type="button"
            @click="showReturnModal = true"
            class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 active:scale-98 transition shadow-xs flex items-center justify-center space-x-2"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Kembalikan Dokumen ke Rak</span>
          </button>
          <p class="text-[11px] text-center text-slate-500 mt-2">
            Pastikan bantex sudah dikembalikan tepat pada posisi raknya sebelum mengonfirmasi.
          </p>
        </div>
      </div>

      <div
        v-if="bantex.status === 'tersedia'"
        class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs"
      >
        <div class="mb-4">
          <h3 class="text-base font-bold text-slate-900 flex items-center">
            <svg class="w-5 h-5 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Form Peminjaman Bantex
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Isi identitas Anda untuk mencatat pengambilan dokumen dari rak arsip.
          </p>
        </div>

        <form @submit.prevent="submitBorrow" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              Kode Bantex (Otomatis)
            </label>
            <input
              type="text"
              :value="bantex.kode_bantex"
              readonly
              tabindex="-1"
              class="w-full rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-mono font-bold px-3.5 py-2.5 cursor-not-allowed select-none focus:outline-none"
            />
          </div>

          <div>
            <label for="nama_peminjam" class="block text-xs font-semibold text-slate-700 mb-1">
              Nama Lengkap Peminjam <span class="text-rose-600">*</span>
            </label>
            <input
              id="nama_peminjam"
              v-model="borrowForm.nama_peminjam"
              type="text"
              required
              placeholder="Contoh: Budi Santoso (HCGA)"
              class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600 px-3.5 py-2.5 transition"
              :class="{ 'border-rose-300 focus:border-rose-500 focus:ring-rose-500': borrowForm.errors.nama_peminjam }"
            />
            <p v-if="borrowForm.errors.nama_peminjam" class="text-xs text-rose-600 mt-1">
              {{ borrowForm.errors.nama_peminjam }}
            </p>
          </div>

          <div>
            <label for="keperluan" class="block text-xs font-semibold text-slate-700 mb-1">
              Keperluan Peminjaman <span class="text-rose-600">*</span>
            </label>
            <textarea
              id="keperluan"
              v-model="borrowForm.keperluan"
              rows="3"
              required
              placeholder="Contoh: Audit berkas pengadaan genset 2025 bersama auditor eksternal"
              class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600 px-3.5 py-2.5 transition"
              :class="{ 'border-rose-300 focus:border-rose-500 focus:ring-rose-500': borrowForm.errors.keperluan }"
            ></textarea>
            <p v-if="borrowForm.errors.keperluan" class="text-xs text-rose-600 mt-1">
              {{ borrowForm.errors.keperluan }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="borrowForm.processing"
            class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-blue-600 hover:bg-blue-700 active:scale-98 transition shadow-xs flex items-center justify-center space-x-2 disabled:opacity-50"
          >
            <span v-if="borrowForm.processing" class="inline-flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              Menyimpan Peminjaman...
            </span>
            <span v-else class="inline-flex items-center space-x-1.5">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span>Pinjam Dokumen Sekarang</span>
            </span>
          </button>
        </form>
      </div>

      <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-bold text-slate-900 flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            Daftar Dokumen dalam Bantex
          </h3>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
            {{ bantex.dokumen.length }} Dokumen
          </span>
        </div>

        <div v-if="bantex.dokumen.length === 0" class="text-center py-6 text-xs text-slate-500">
          Belum ada berkas dokumen yang dicatat di dalam bantex ini.
        </div>

        <div v-else class="space-y-2.5">
          <div
            v-for="(doc, idx) in bantex.dokumen"
            :key="doc.id"
            class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex items-start space-x-3"
          >
            <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
              {{ idx + 1 }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-baseline justify-between gap-2">
                <span class="font-mono text-xs font-bold text-blue-700">
                  {{ doc.no_pengadaan }}
                </span>
                <span class="text-[11px] font-semibold text-slate-500">
                  Tahun {{ doc.tahun }}
                </span>
              </div>
              <p class="text-xs font-medium text-slate-800 mt-0.5">
                {{ doc.nama_pengadaan }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ConfirmModal
      :show="showReturnModal"
      title="Konfirmasi Pengembalian Bantex"
      :message="`Apakah bantex '${bantex.kode_bantex}' telah dikembalikan ke posisi ${bantex.nomor_rak}, ${bantex.baris}? Tindakan ini akan mengubah status kembali menjadi 'Tersedia'.`"
      confirm-text="Ya, Sudah Dikembalikan"
      cancel-text="Batal"
      variant="emerald"
      :loading="returnForm.processing"
      @close="showReturnModal = false"
      @confirm="submitReturn"
    />
  </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
  bantex: {
    type: Object,
    required: true,
  },
});

const showReturnModal = ref(false);

const borrowForm = useForm({
  nama_peminjam: '',
  keperluan: '',
});

const returnForm = useForm({});

function submitBorrow() {
  borrowForm.post(route('public.scan.borrow', props.bantex.slug), {
    preserveScroll: true,
    onSuccess: () => {
      borrowForm.reset();
    },
  });
}

function submitReturn() {
  returnForm.post(route('public.scan.return', props.bantex.slug), {
    preserveScroll: true,
    onSuccess: () => {
      showReturnModal.value = false;
    },
  });
}
</script>
