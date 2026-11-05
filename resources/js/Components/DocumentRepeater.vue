<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <div>
        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
          Daftar Dokumen di dalam Bantex
        </h4>
        <p class="text-[11px] text-slate-500">
          Masukkan nomor berkas pengadaan, uraian nama pekerjaan, dan tahun anggaran.
        </p>
      </div>
      <button
        type="button"
        @click="addDocument"
        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition active:scale-95"
      >
        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Baris Dokumen
      </button>
    </div>

    <div
      v-if="modelValue.length === 0"
      class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center text-xs text-slate-400"
    >
      Belum ada dokumen yang ditambahkan. Klik tombol "Tambah Baris Dokumen" di atas.
    </div>

    <div v-else class="space-y-2.5">
      <div
        v-for="(doc, idx) in modelValue"
        :key="idx"
        class="bg-slate-50 rounded-xl p-3 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center gap-2.5"
      >
        <span class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0">
          {{ idx + 1 }}
        </span>

        <div class="w-full sm:w-44 shrink-0">
          <label class="block text-[10px] font-semibold text-slate-500 mb-0.5 sm:hidden">
            No Pengadaan
          </label>
          <input
            type="text"
            v-model="doc.no_pengadaan"
            placeholder="No Pengadaan (cth: S260072A)"
            required
            class="w-full text-xs font-mono rounded-lg border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-1.5 px-2.5"
          />
        </div>

        <div class="w-full flex-1">
          <label class="block text-[10px] font-semibold text-slate-500 mb-0.5 sm:hidden">
            Nama Pengadaan
          </label>
          <input
            type="text"
            v-model="doc.nama_pengadaan"
            placeholder="Uraian Nama Pengadaan Dokumen..."
            required
            class="w-full text-xs rounded-lg border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-1.5 px-2.5"
          />
        </div>

        <div class="w-full sm:w-24 shrink-0">
          <label class="block text-[10px] font-semibold text-slate-500 mb-0.5 sm:hidden">
            Tahun
          </label>
          <input
            type="text"
            v-model="doc.tahun"
            placeholder="Tahun"
            required
            class="w-full text-xs rounded-lg border-slate-300 focus:border-blue-600 focus:ring-blue-600 py-1.5 px-2.5 text-center font-semibold"
          />
        </div>

        <button
          type="button"
          @click="removeDocument(idx)"
          class="self-end sm:self-center p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
          title="Hapus baris dokumen ini"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update:modelValue']);

function addDocument() {
  const currentYear = new Date().getFullYear().toString();
  const updated = [...props.modelValue, { no_pengadaan: '', nama_pengadaan: '', tahun: currentYear }];
  emit('update:modelValue', updated);
}

function removeDocument(index) {
  const updated = props.modelValue.filter((_, i) => i !== index);
  emit('update:modelValue', updated);
}
</script>
