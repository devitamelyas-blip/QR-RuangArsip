<template>
  <div class="min-h-screen bg-[#F5F6F8] text-slate-800 font-sans antialiased flex flex-col">
    <!-- Top Corporate Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <Link :href="route('public.search')" class="flex items-center space-x-2.5 group">
          <img
            src="/images/pelita-symbol.png"
            alt="Pelita Air"
            class="h-8 w-auto object-contain transition group-hover:scale-105"
          />
          <div class="flex flex-col leading-tight">
            <div class="flex items-center space-x-1.5">
              <span class="font-black text-sm tracking-wider text-[#003865]">PELITA AIR</span>
              <span class="w-1 h-3 rounded-full bg-[#D71920]"></span>
              <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider hidden sm:inline">Divisi SCM</span>
            </div>
            <span class="text-[9.5px] uppercase font-semibold text-slate-500 tracking-wider">
              Ruang Arsip Dokumen Pengadaan
            </span>
          </div>
        </Link>

        <nav class="flex items-center space-x-2">
          <Link
            :href="route('public.search')"
            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium transition"
            :class="route().current('public.search') ? 'bg-[#0A2540] text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
          >
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Cari Dokumen</span>
          </Link>

          <template v-if="$page.props.auth?.user">
            <Link
              :href="route('dashboard')"
              class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold bg-[#0A2540] text-white hover:bg-[#14385C] transition shadow-xs"
            >
              <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span>Dashboard Admin</span>
            </Link>
          </template>
          <template v-else>
            <Link
              :href="route('login')"
              class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition border border-slate-200"
              title="Portal Khusus Petugas Arsip"
            >
              <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>Login Petugas</span>
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
      <!-- Flash Alert: Success -->
      <div
        v-if="$page.props.flash?.success"
        class="mb-5 p-3.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-2.5 text-xs font-medium shadow-xs"
        role="alert"
      >
        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1 leading-relaxed">
          {{ $page.props.flash.success }}
        </div>
      </div>

      <!-- Flash Alert: Error -->
      <div
        v-if="$page.props.flash?.error"
        class="mb-5 p-3.5 rounded-md bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-2.5 text-xs font-medium shadow-xs"
        role="alert"
      >
        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="flex-1 leading-relaxed">
          {{ $page.props.flash.error }}
        </div>
      </div>

      <slot />
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
      <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-1">
        <span>Pelita Air &bull; Divisi Supply Chain Management (SCM) &bull; Ruang Arsip Dokumen</span>
        <span class="text-[11px] text-slate-400">Jaringan Internal Operasional Kantor</span>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
