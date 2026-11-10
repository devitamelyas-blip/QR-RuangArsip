<template>
  <PublicLayout>
    <Head :title="`Bantex ${bantex.kode_bantex} — Ruang Arsip SCM Pelita Air`" />

    <div class="space-y-4 pb-8">
      <!-- Location & Identity Card -->
      <div class="bg-white rounded-lg p-5 shadow-2xs border border-slate-200">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
          <div class="min-w-0">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1">
              Kode Lokasi Bantex SCM
            </span>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#0A2540] tracking-tight break-all font-mono">
              {{ bantex.kode_bantex }}
            </h1>
          </div>
          <StatusBadge
            :status="bantex.status"
            :is-overdue="bantex.peminjaman_aktif?.is_overdue || false"
          />
        </div>

        <!-- 4-Col Location Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-3 border-t border-slate-100 text-xs">
          <div class="bg-slate-50 rounded-md p-2.5 border border-slate-200">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Kelompok / Tim</span>
            <span class="font-bold text-slate-800 text-sm">Tim {{ bantex.tim }}</span>
          </div>
          <div class="bg-slate-50 rounded-md p-2.5 border border-slate-200">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Nomor Rak</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.nomor_rak }}</span>
          </div>
          <div class="bg-slate-50 rounded-md p-2.5 border border-slate-200">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Baris Rak</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.baris }}</span>
          </div>
          <div class="bg-slate-50 rounded-md p-2.5 border border-slate-200">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Posisi Urutan</span>
            <span class="font-bold text-slate-800 text-sm">{{ bantex.urutan_bantex }}</span>
          </div>
        </div>

        <div v-if="bantex.keterangan_lokasi" class="mt-3 text-xs text-slate-700 bg-blue-50/70 border border-blue-100 rounded-md p-2.5 flex items-start space-x-2">
          <svg class="w-4 h-4 text-blue-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <span class="font-semibold text-blue-900">Petunjuk Lokasi Fisik: </span>
            <span>{{ bantex.keterangan_lokasi }}</span>
          </div>
        </div>
      </div>

      <!-- State E: Pengembalian Dokumen (Jika Status: Sedang Dipinjam) -->
      <div
        v-if="bantex.status === 'dipinjam' && bantex.peminjaman_aktif"
        class="bg-white rounded-lg p-5 border border-amber-200 shadow-2xs"
      >
        <div class="flex items-center justify-between pb-3 border-b border-amber-100 mb-3">
          <div class="flex items-center space-x-2">
            <div class="w-6 h-6 rounded bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs">
              !
            </div>
            <h2 class="text-sm font-bold text-amber-900">
              Dokumen Sedang Dipinjam
            </h2>
          </div>
          <span
            v-if="bantex.peminjaman_aktif.is_overdue"
            class="text-[11px] font-bold px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200"
          >
            Terlambat ({{ bantex.peminjaman_aktif.lama_pinjam_hari }} hari)
          </span>
          <span v-else class="text-[11px] text-amber-800 font-medium bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
            Dipinjam {{ bantex.peminjaman_aktif.lama_pinjam_hari }} hari lalu
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50 rounded-md p-3.5 border border-slate-200">
          <div>
            <span class="text-slate-500 font-medium block text-[11px]">Nama Peminjam:</span>
            <p class="font-bold text-slate-900 text-sm mt-0.5">{{ bantex.peminjaman_aktif.nama_peminjam }}</p>
          </div>
          <div>
            <span class="text-slate-500 font-medium block text-[11px]">Waktu Peminjaman:</span>
            <p class="text-slate-800 font-medium mt-0.5">{{ bantex.peminjaman_aktif.tanggal_pinjam }}</p>
          </div>
          <div class="sm:col-span-2">
            <span class="text-slate-500 font-medium block text-[11px]">Keperluan Peminjaman:</span>
            <p class="text-slate-700 mt-0.5 whitespace-pre-line leading-relaxed">{{ bantex.peminjaman_aktif.keperluan }}</p>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100">
          <button
            type="button"
            @click="showReturnModal = true"
            class="w-full py-2.5 px-4 rounded-md font-semibold text-xs text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 transition shadow-2xs flex items-center justify-center space-x-1.5 focus:outline-none"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Konfirmasi Pengembalian ke Rak</span>
          </button>
          <p class="text-[11px] text-center text-slate-500 mt-2">
            Pastikan berkas bantex sudah diletakkan kembali pada rak dan baris yang tepat sebelum mengonfirmasi.
          </p>
        </div>
      </div>

      <!-- State D: Form Peminjaman Dokumen (Jika Status: Tersedia) -->
      <div
        v-if="bantex.status === 'tersedia'"
        class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs"
      >
        <div class="pb-3 border-b border-slate-100 mb-4">
          <h2 class="text-sm font-bold text-[#0A2540]">
            Formulir Peminjaman Dokumen
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Lengkapi data peminjam sebelum mengambil fisik bantex dari ruang arsip.
          </p>
        </div>

        <form @submit.prevent="submitBorrow" class="space-y-3.5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Kode Bantex (Otomatis dari Sistem)
              </label>
              <input
                type="text"
                :value="bantex.kode_bantex"
                readonly
                tabindex="-1"
                class="w-full rounded-md bg-slate-50 border border-slate-200 text-slate-700 text-xs font-mono font-bold px-3 py-2 cursor-not-allowed select-none focus:outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                Tanggal & Waktu Peminjaman
              </label>
              <input
                type="text"
                :value="todayFormatted"
                readonly
                tabindex="-1"
                class="w-full rounded-md bg-slate-50 border border-slate-200 text-slate-600 text-xs px-3 py-2 cursor-not-allowed select-none focus:outline-none font-medium"
              />
            </div>
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
              class="w-full rounded-md border-slate-300 text-xs focus:border-[#0A2540] focus:ring-[#0A2540] px-3 py-2 transition"
              :class="{ 'border-rose-300': borrowForm.errors.nama_peminjam }"
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
              placeholder="Contoh: Audit berkas pengadaan fisik bersama tim auditor eksternal"
              class="w-full rounded-md border-slate-300 text-xs focus:border-[#0A2540] focus:ring-[#0A2540] px-3 py-2 transition"
              :class="{ 'border-rose-300': borrowForm.errors.keperluan }"
            ></textarea>
            <p v-if="borrowForm.errors.keperluan" class="text-xs text-rose-600 mt-1">
              {{ borrowForm.errors.keperluan }}
            </p>
          </div>

          <div class="pt-1">
            <button
              type="submit"
              :disabled="borrowForm.processing"
              class="w-full py-2.5 px-4 rounded-md font-semibold text-xs text-white bg-[#0A2540] hover:bg-[#14385C] active:bg-[#07182C] transition shadow-2xs flex items-center justify-center space-x-2 disabled:opacity-50 focus:outline-none"
            >
              <svg
                v-if="borrowForm.processing"
                class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <span>{{ borrowForm.processing ? 'Menyimpan Transaksi...' : 'Konfirmasi Peminjaman Dokumen' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Daftar Dokumen di dalam Bantex -->
      <div class="bg-white rounded-lg p-5 border border-slate-200 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
          <div>
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
              Daftar Berkas Dokumen di dalam Bantex
            </h3>
            <p class="text-[11px] text-slate-500">
              Daftar seluruh berkas pengadaan yang tercatat dalam unit bantex ini.
            </p>
          </div>
          <span class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
            {{ bantex.dokumen.length }} Berkas
          </span>
        </div>

        <div v-if="bantex.dokumen.length === 0" class="text-center py-8 text-xs text-slate-400">
          Belum ada berkas dokumen yang terdata di dalam bantex ini.
        </div>

        <div v-else class="space-y-2">
          <div
            v-for="(doc, idx) in bantex.dokumen"
            :key="doc.id"
            class="bg-slate-50/70 rounded-md p-3 border border-slate-200 flex items-start space-x-3"
          >
            <div class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
              {{ idx + 1 }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-baseline justify-between gap-2">
                <span class="font-mono text-xs font-bold text-blue-800">
                  {{ doc.no_pengadaan }}
                </span>
                <span class="text-[11px] font-medium text-slate-500">
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

    <!-- Confirm Modal Pengembalian -->
    <ConfirmModal
      :show="showReturnModal"
      title="Konfirmasi Pengembalian Bantex"
      :message="`Apakah berkas bantex '${bantex.kode_bantex}' telah benar-benar dikembalikan ke posisi rak ${bantex.nomor_rak}, ${bantex.baris}? Transaksi pengembalian akan dicatat sekarang.`"
      confirm-text="Ya, Dokumen Sudah Kembali"
      cancel-text="Batal"
      variant="emerald"
      :loading="returnForm.processing"
      @close="showReturnModal = false"
      @confirm="submitReturn"
    />
  </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
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

const todayFormatted = computed(() => {
  const d = new Date();
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
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
