<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-4">
          <Link :href="route('tenant.dashboard')" class="text-slate-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          </Link>
          <p class="text-white font-bold text-sm leading-none">Configuraciones</p>
        </div>
      </div>
    </nav>
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Perfil -->
        <Link :href="route('tenant.settings.profile')" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-blue-800/50 transition group block cursor-pointer">
          <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-blue-950/60 border border-blue-800/40 flex items-center justify-center group-hover:bg-blue-900/40 transition mb-4">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <p class="text-lg font-bold text-white">Perfil</p>
            <p class="text-xs text-slate-500 mt-1">Configuración de usuario</p>
          </div>
        </Link>

        <!-- Categorias -->
        <Link :href="route('tenant.settings.categories.index')" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-amber-800/50 transition group block cursor-pointer">
          <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-amber-950/60 border border-amber-800/40 flex items-center justify-center group-hover:bg-amber-900/40 transition mb-4">
              <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <p class="text-lg font-bold text-white">Categorías</p>
            <p class="text-xs text-slate-500 mt-1">Gestionar familias</p>
          </div>
        </Link>

        <!-- Atributos -->
        <Link :href="route('tenant.settings.attributes.index')" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-fuchsia-800/50 transition group block cursor-pointer">
          <div class="flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-fuchsia-950/60 border border-fuchsia-800/40 flex items-center justify-center group-hover:bg-fuchsia-900/40 transition mb-4">
              <svg class="w-6 h-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            </div>
            <p class="text-lg font-bold text-white">Atributos</p>
            <p class="text-xs text-slate-500 mt-1">Campos dinámicos EAV</p>
          </div>
        </Link>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
