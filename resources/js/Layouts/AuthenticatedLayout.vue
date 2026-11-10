<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const mobileSidebarOpen = ref(false);

const navigationItems = [
  {
    name: 'Dashboard',
    route: 'admin.dashboard',
    activePattern: 'admin.dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
  },
  {
    name: 'Data Dokumen / Bantex',
    route: 'admin.bantex.index',
    activePattern: 'admin.bantex.*',
    icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
  },
  {
    name: 'Peminjaman Dokumen',
    route: 'public.search',
    targetBlank: true,
    activePattern: 'public.scan*',
    icon: 'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2',
    badge: 'Scan / Form',
  },
  {
    name: 'Riwayat Peminjaman',
    route: 'admin.loans.index',
    activePattern: 'admin.loans.*',
    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  {
    name: 'Monitoring Dokumen',
    route: 'admin.dashboard',
    query: { status: 'dipinjam' },
    activeCondition: () => route().current('admin.dashboard') && (new URLSearchParams(window.location.search).get('status') === 'dipinjam'),
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
  },
  {
    name: 'QR Ruang Arsip',
    route: 'admin.labels.rooms',
    activePattern: 'admin.labels.rooms',
    icon: 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
    badge: '2 Ruang',
  },
];

const secondaryItems = [
  {
    name: 'Manajemen Pengguna',
    route: 'profile.edit',
    activePattern: 'profile.*',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
  },
];

function isItemActive(item) {
  if (item.activeCondition) {
    return item.activeCondition();
  }
  if (item.activePattern) {
    return route().current(item.activePattern);
  }
  return false;
}
</script>

<template>
  <div class="min-h-screen bg-[#F5F6F8] font-sans antialiased text-slate-800 flex flex-col md:flex-row">
    <div
      v-if="mobileSidebarOpen"
      class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs md:hidden"
      @click="mobileSidebarOpen = false"
    ></div>

    <aside
      class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0A2540] text-slate-200 flex flex-col transition-transform duration-200 ease-in-out md:translate-x-0 border-r border-[#081E34]"
      :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >
      <div class="h-20 px-4 flex items-center justify-between border-b border-white/10 shrink-0 bg-[#071B2F]">
        <Link :href="route('admin.dashboard')" class="flex items-center space-x-2.5">
          <ApplicationLogo :is-dark="true" size="md" />
        </Link>
        <button
          type="button"
          class="md:hidden p-1.5 rounded text-slate-400 hover:text-white hover:bg-white/10"
          @click="mobileSidebarOpen = false"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        <div>
          <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            Menu Operasional SCM
          </div>
          <nav class="space-y-1">
            <template v-for="item in navigationItems" :key="item.name">
              <a
                v-if="item.targetBlank"
                :href="item.query ? route(item.route, item.query) : route(item.route)"
                target="_blank"
                class="flex items-center justify-between px-3 py-2 rounded-md text-xs font-medium transition group text-slate-300 hover:text-white hover:bg-white/10"
              >
                <div class="flex items-center space-x-2.5 min-w-0">
                  <svg class="w-4 h-4 text-slate-400 group-hover:text-white shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
                  </svg>
                  <span class="truncate">{{ item.name }}</span>
                </div>
                <span v-if="item.badge" class="text-[9px] font-semibold uppercase tracking-wider bg-white/10 text-slate-300 px-1.5 py-0.5 rounded">
                  {{ item.badge }}
                </span>
              </a>
              <Link
                v-else
                :href="item.query ? route(item.route, item.query) : route(item.route)"
                class="flex items-center justify-between px-3 py-2 rounded-md text-xs font-medium transition group"
                :class="isItemActive(item) 
                  ? 'bg-blue-600/90 text-white font-semibold shadow-xs' 
                  : 'text-slate-300 hover:text-white hover:bg-white/10'"
              >
                <div class="flex items-center space-x-2.5 min-w-0">
                  <svg
                    class="w-4 h-4 shrink-0 transition"
                    :class="isItemActive(item) ? 'text-white' : 'text-slate-400 group-hover:text-white'"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
                  </svg>
                  <span class="truncate">{{ item.name }}</span>
                </div>
                <span v-if="item.badge" class="text-[9px] font-semibold uppercase tracking-wider bg-white/10 text-slate-300 px-1.5 py-0.5 rounded">
                  {{ item.badge }}
                </span>
              </Link>
            </template>
          </nav>
        </div>

        <div>
          <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            Sistem & Otoritas
          </div>
          <nav class="space-y-1">
            <Link
              v-for="item in secondaryItems"
              :key="item.name"
              :href="route(item.route)"
              class="flex items-center space-x-2.5 px-3 py-2 rounded-md text-xs font-medium transition group"
              :class="isItemActive(item) 
                ? 'bg-blue-600/90 text-white font-semibold shadow-xs' 
                : 'text-slate-300 hover:text-white hover:bg-white/10'"
            >
              <svg
                class="w-4 h-4 shrink-0 transition"
                :class="isItemActive(item) ? 'text-white' : 'text-slate-400 group-hover:text-white'"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" :d="item.icon" />
              </svg>
              <span class="truncate">{{ item.name }}</span>
            </Link>
          </nav>
        </div>
      </div>

      <div class="p-3 border-t border-white/10 bg-[#071B2F] shrink-0">
        <div class="flex items-center justify-between p-2 rounded-md bg-white/5 border border-white/5">
          <div class="flex items-center space-x-2.5 min-w-0">
            <div class="w-8 h-8 rounded bg-[#163A64] text-white font-bold text-xs flex items-center justify-center shrink-0 uppercase border border-white/10">
              {{ $page.props.auth.user.name.charAt(0) }}
            </div>
            <div class="min-w-0 leading-tight">
              <div class="text-xs font-semibold text-white truncate">
                {{ $page.props.auth.user.name }}
              </div>
              <div class="text-[10px] text-slate-300 truncate">
                Petugas Arsip SCM
              </div>
            </div>
          </div>
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            class="p-1.5 rounded text-slate-400 hover:text-rose-400 hover:bg-white/10 transition"
            title="Keluar (Logout)"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </Link>
        </div>
      </div>
    </aside>

    <div class="flex-1 md:pl-64 flex flex-col min-w-0">
      <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between shadow-2xs">
        <div class="flex items-center space-x-3 min-w-0">
          <button
            type="button"
            class="md:hidden p-2 rounded-md text-slate-600 hover:bg-slate-100"
            @click="mobileSidebarOpen = true"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <nav class="flex items-center space-x-1.5 text-xs text-slate-500 truncate" aria-label="Breadcrumb">
            <Link :href="route('admin.dashboard')" class="hover:text-slate-900 transition flex items-center">
              <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span>Ruang Arsip SCM</span>
            </Link>

            <template v-if="breadcrumbs.length > 0">
              <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                <span class="text-slate-300">/</span>
                <Link
                  v-if="crumb.url"
                  :href="crumb.url"
                  class="hover:text-slate-900 transition truncate"
                >
                  {{ crumb.label }}
                </Link>
                <span v-else class="font-medium text-slate-800 truncate">
                  {{ crumb.label }}
                </span>
              </template>
            </template>
            <template v-else>
              <span class="text-slate-300">/</span>
              <span class="font-medium text-slate-800 truncate">
                {{ route().current('admin.dashboard') ? 'Dashboard' : (route().current('admin.bantex.*') ? 'Data Bantex' : (route().current('admin.loans.*') ? 'Riwayat Peminjaman' : (route().current('admin.labels.*') ? 'QR & Label Arsip' : 'Sistem Arsip'))) }}
              </span>
            </template>
          </nav>
        </div>

        <div class="flex items-center space-x-3">
          <a
            :href="route('public.search')"
            target="_blank"
            class="inline-flex items-center text-xs font-medium text-slate-700 hover:text-[#0A2540] bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-md border border-slate-200 transition"
          >
            <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span class="hidden sm:inline">Pencarian / Scan Publik</span>
            <span class="sm:hidden">Scan</span>
          </a>

          <Dropdown align="right" width="48">
            <template #trigger>
              <button
                type="button"
                class="inline-flex items-center space-x-2 rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 focus:outline-none transition"
              >
                <span class="w-5 h-5 rounded bg-slate-100 text-[#003865] font-bold text-[10px] flex items-center justify-center uppercase">
                  {{ $page.props.auth.user.name.charAt(0) }}
                </span>
                <span class="hidden md:inline">{{ $page.props.auth.user.name }}</span>
                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </template>

            <template #content>
              <div class="px-4 py-2 border-b border-slate-100">
                <div class="text-xs font-semibold text-slate-800">{{ $page.props.auth.user.name }}</div>
                <div class="text-[11px] text-slate-500 truncate">{{ $page.props.auth.user.email }}</div>
                <div class="text-[10px] text-blue-700 font-semibold mt-0.5">Divisi SCM</div>
              </div>
              <DropdownLink :href="route('profile.edit')">
                Profil Pengguna
              </DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button">
                Keluar (Logout)
              </DropdownLink>
            </template>
          </Dropdown>
        </div>
      </header>

      <section v-if="$slots.header" class="bg-white border-b border-slate-200 px-4 sm:px-6 py-4">
        <slot name="header" />
      </section>

      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <div
          v-if="$page.props.flash?.success"
          class="mb-5 p-3.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-2.5 text-xs font-medium"
          role="alert"
        >
          <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="flex-1 leading-relaxed">
            {{ $page.props.flash.success }}
          </div>
        </div>

        <div
          v-if="$page.props.flash?.error"
          class="mb-5 p-3.5 rounded-md bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-2.5 text-xs font-medium"
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

      <footer class="mt-auto border-t border-slate-200 bg-white px-4 sm:px-6 py-3 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center space-x-2">
          <span class="font-semibold text-slate-700">Pelita Air</span>
          <span>&bull;</span>
          <span>Divisi Supply Chain Management (SCM)</span>
          <span>&bull;</span>
          <span>Document Management System</span>
        </div>
        <div class="text-[11px] text-slate-400">
          Internal Enterprise Application &bull; Operasional Terintegrasi
        </div>
      </footer>
    </div>
  </div>
</template>
