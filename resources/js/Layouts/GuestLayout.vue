<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="min-h-screen flex flex-col justify-center items-center bg-[#F5F6F8] p-4 sm:p-6 text-slate-800 font-sans">
    <div class="w-full max-w-md">
      <div class="text-center mb-6">
        <div class="inline-flex flex-col items-center mb-2">
          <Link :href="route('public.search')" class="inline-block transition hover:opacity-90">
            <img src="/images/pelita-logo.png" alt="Pelita Air" class="h-10 sm:h-12 w-auto mx-auto" />
          </Link>
          <div class="mt-2.5 inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded bg-slate-100 text-[#003865] border border-slate-200 text-[10px] font-bold uppercase tracking-wider">
            <span>Divisi Supply Chain Management (SCM)</span>
          </div>
        </div>
        <h2 class="text-sm font-bold text-[#0A2540] tracking-tight mt-1">
          Document Management System (DMS)
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
          Portal Administrasi & Sirkulasi Arsip Dokumen Pengadaan
        </p>
      </div>

      <div class="bg-white border border-slate-200 rounded-lg p-6 sm:p-7 shadow-xs">
        <slot />
      </div>

      <div class="text-center mt-6 text-[11px] text-slate-400 space-y-1">
        <div>Akses terbatas khusus personil berwenang Pelita Air.</div>
        <div>&copy; {{ new Date().getFullYear() }} Pelita Air &bull; Supply Chain Management.</div>
      </div>
    </div>
  </div>
</template>
