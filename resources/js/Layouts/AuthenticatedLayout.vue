<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
  <div>
    <div class="min-h-screen bg-slate-100 flex flex-col font-sans antialiased text-slate-900 selection:bg-blue-600 selection:text-white">
      <nav class="border-b border-slate-200 bg-white sticky top-0 z-30 shadow-xs">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div class="flex h-16 justify-between items-center">
            <div class="flex items-center">
              <!-- Logo -->
              <div class="flex shrink-0 items-center">
                <Link :href="route('admin.dashboard')" class="flex items-center space-x-2.5">
                  <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                  </div>
                  <span class="font-bold text-slate-900 text-sm hidden md:inline">Admin Ruang Arsip</span>
                </Link>
              </div>

              <!-- Navigation Links -->
              <div class="hidden space-x-2 sm:ms-8 sm:flex">
                <NavLink
                  :href="route('admin.dashboard')"
                  :active="route().current('admin.dashboard')"
                >
                  Dashboard
                </NavLink>
                <NavLink
                  :href="route('admin.bantex.index')"
                  :active="route().current('admin.bantex.*')"
                >
                  Data Bantex
                </NavLink>
                <NavLink
                  :href="route('admin.loans.index')"
                  :active="route().current('admin.loans.*')"
                >
                  Riwayat Peminjaman
                </NavLink>
              </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-4">
              <!-- Link ke Halaman Publik -->
              <a
                :href="route('public.search')"
                target="_blank"
                class="inline-flex items-center text-xs font-semibold text-slate-600 hover:text-blue-600 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 transition"
              >
                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Halaman Publik
              </a>

              <!-- Settings Dropdown -->
              <div class="relative">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      type="button"
                      class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none transition"
                    >
                      <span>{{ $page.props.auth.user.name }}</span>
                      <svg
                        class="ms-1.5 h-4 w-4 text-slate-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')">
                      Profil Saya
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button">
                      Keluar (Logout)
                    </DropdownLink>
                  </template>
                </Dropdown>
              </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
              <button
                @click="showingNavigationDropdown = !showingNavigationDropdown"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100 focus:outline-none"
              >
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path
                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden border-t border-slate-200 bg-white">
          <div class="space-y-1 pb-3 pt-2">
            <ResponsiveNavLink :href="route('admin.dashboard')" :active="route().current('admin.dashboard')">
              Dashboard
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.bantex.index')" :active="route().current('admin.bantex.*')">
              Data Bantex
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('admin.loans.index')" :active="route().current('admin.loans.*')">
              Riwayat Peminjaman
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('public.search')" target="_blank">
              Buka Halaman Publik
            </ResponsiveNavLink>
          </div>

          <div class="border-t border-slate-200 pb-3 pt-3 px-4">
            <div class="text-xs font-semibold text-slate-800">{{ $page.props.auth.user.name }}</div>
            <div class="text-[11px] text-slate-500">{{ $page.props.auth.user.email }}</div>
            <div class="mt-2 space-y-1">
              <ResponsiveNavLink :href="route('profile.edit')">
                Profil Saya
              </ResponsiveNavLink>
              <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                Keluar
              </ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white border-b border-slate-200" v-if="$slots.header">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <!-- Flash Alert: Success -->
          <div
            v-if="$page.props.flash?.success"
            class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-xs"
            role="alert"
          >
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 text-sm font-medium leading-relaxed">
              {{ $page.props.flash.success }}
            </div>
          </div>

          <!-- Flash Alert: Error -->
          <div
            v-if="$page.props.flash?.error"
            class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-3 shadow-xs"
            role="alert"
          >
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1 text-sm font-medium leading-relaxed">
              {{ $page.props.flash.error }}
            </div>
          </div>

          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
