<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased flex flex-col selection:bg-blue-600 selection:text-white">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <Link :href="route('public.search')" class="flex items-center space-x-2.5 group">
          <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs group-hover:bg-blue-700 transition">
            <!-- Box / Archive Icon SVG -->
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
          </div>
          <div>
            <h1 class="font-bold text-slate-900 text-sm sm:text-base leading-tight">Ruang Arsip</h1>
            <p class="text-[10px] sm:text-xs text-slate-500 font-medium">Sistem QR Code Lokasi</p>
          </div>
        </Link>

        <!-- Nav actions -->
        <nav class="flex items-center space-x-2">
          <Link
            :href="route('public.search')"
            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition"
            :class="route().current('public.search') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100'"
          >
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Cari Dokumen
          </Link>

          <template v-if="$page.props.auth?.user">
            <Link
              :href="route('dashboard')"
              class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition shadow-xs"
            >
              Dashboard
            </Link>
          </template>
          <template v-else>
            <Link
              :href="route('login')"
              class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
              title="Khusus Petugas Arsip"
            >
              <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Admin
            </Link>
          </template>
        </nav>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-3xl w-full mx-auto px-4 sm:px-6 py-5 sm:py-7">
      <!-- Flash Alert: Success -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="$page.props.flash?.success"
          class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-xs"
          role="alert"
        >
          <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="flex-1 text-sm font-medium leading-relaxed">
            {{ $page.props.flash.success }}
          </div>
        </div>
      </transition>

      <!-- Flash Alert: Error -->
      <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="$page.props.flash?.error"
          class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-3 shadow-xs"
          role="alert"
        >
          <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="flex-1 text-sm font-medium leading-relaxed">
            {{ $page.props.flash.error }}
          </div>
        </div>
      </transition>

      <!-- Slot Content -->
      <slot />
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
      <div class="max-w-3xl mx-auto px-4">
        Sistem Arsip Internal &bull; Akses Lokal WiFi Kantor
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

