<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <!-- Topbar -->
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <div>
            <p class="text-white font-bold text-sm leading-none">Portal de Empresa</p>
            <p class="text-indigo-400 text-[11px] mt-0.5 font-mono">{{ tenantId }}.localhost</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex flex-col items-end">
            <span class="text-sm font-semibold text-white">{{ user.name }}</span>
            <span class="text-xs text-slate-400">{{ user.email }}</span>
          </div>
          <button @click="logout" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-300 bg-rose-950/30 hover:bg-rose-950/50 border border-rose-900/60 rounded-lg transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Cerrar Sesión
          </button>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- Welcome banner -->
      <div class="bg-gradient-to-r from-indigo-600/20 via-purple-600/10 to-transparent border border-indigo-800/30 rounded-2xl px-8 py-8 mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600/5 rounded-full -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
        <div class="relative">
          <p class="text-indigo-400 font-semibold text-sm mb-1">¡Bienvenido de vuelta!</p>
          <h1 class="text-3xl font-extrabold text-white">{{ user.name }}</h1>
          <p class="text-slate-400 text-sm mt-2 max-w-xl">
            Estás en el portal exclusivo de tu empresa. Todos tus datos están aislados en el schema
            <span class="font-mono text-indigo-400">tenant_{{ tenantId }}</span> de PostgreSQL.
          </p>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 mb-8">
        <!-- Schema -->
        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-indigo-800/50 transition group">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Schema PostgreSQL</span>
            <div class="w-9 h-9 rounded-lg bg-indigo-950/60 border border-indigo-800/40 flex items-center justify-center group-hover:bg-indigo-900/40 transition">
              <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4-8 4"/></svg>
            </div>
          </div>
          <p class="text-xl font-bold text-white font-mono">tenant_{{ tenantId }}</p>
          <p class="text-xs text-slate-500 mt-1">Base de datos aislada en PostgreSQL</p>
        </div>

        <!-- Domain -->
        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-purple-800/50 transition group">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dominio Exclusivo</span>
            <div class="w-9 h-9 rounded-lg bg-purple-950/60 border border-purple-800/40 flex items-center justify-center group-hover:bg-purple-900/40 transition">
              <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
          </div>
          <p class="text-xl font-bold text-white font-mono">{{ tenantId }}.localhost</p>
          <p class="text-xs text-slate-500 mt-1">Subdominio único del tenant</p>
        </div>

        <!-- Products -->
        <Link :href="route('tenant.products.index')" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-emerald-800/50 transition group block cursor-pointer relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="flex items-center justify-between mb-4 relative z-10">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Catálogo</span>
            <div class="flex items-center gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                {{ productsCount }} {{ productsCount === 1 ? 'Producto' : 'Productos' }}
              </span>
              <div class="w-9 h-9 rounded-lg bg-emerald-950/60 border border-emerald-800/40 flex items-center justify-center group-hover:bg-emerald-900/40 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              </div>
            </div>
          </div>
          <div class="relative z-10">
            <p class="text-xl font-bold text-white">Gestionar Productos</p>
            <p class="text-xs text-slate-500 mt-1">Ver, editar y crear catálogo EAV</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-emerald-400 group-hover:text-emerald-300 transition-colors">
              <span>Ir a productos</span>
              <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </Link>
        
        <!-- Settings -->
        <Link :href="route('tenant.settings.index')" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-purple-800/50 transition group block cursor-pointer relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="flex items-center justify-between mb-4 relative z-10">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ajustes</span>
            <div class="w-9 h-9 rounded-lg bg-purple-950/60 border border-purple-800/40 flex items-center justify-center group-hover:bg-purple-900/40 transition">
              <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
          </div>
          <div class="relative z-10">
            <p class="text-xl font-bold text-white">Configuraciones</p>
            <p class="text-xs text-slate-500 mt-1">Perfil, categorías y atributos EAV</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-purple-400 group-hover:text-purple-300 transition-colors">
              <span>Gestionar ajustes</span>
              <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </Link>

        <!-- Catálogo Público -->
        <a :href="`/catalogo`" class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-teal-800/50 transition group block cursor-pointer relative overflow-hidden">
          <div class="absolute inset-0 bg-gradient-to-br from-teal-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
          <div class="flex items-center justify-between mb-4 relative z-10">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Público</span>
            <div class="w-9 h-9 rounded-lg bg-teal-950/60 border border-teal-800/40 flex items-center justify-center group-hover:bg-teal-900/40 transition">
              <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
          </div>
          <div class="relative z-10">
            <p class="text-xl font-bold text-white">Catálogo</p>
            <p class="text-xs text-slate-500 mt-1">Vista pública compartible</p>
            <div class="mt-4 flex items-center gap-1 text-xs font-semibold text-teal-400 group-hover:text-teal-300 transition-colors">
              <span>Ver catálogo</span>
              <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </div>
        </a>
      </div>

      <!-- Confirmation -->
      <div class="bg-slate-900/30 border border-slate-800/50 rounded-2xl p-8 text-center">
        <div class="w-14 h-14 rounded-full bg-emerald-950/50 border border-emerald-800/40 flex items-center justify-center mx-auto mb-4">
          <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h2 class="text-xl font-bold text-white mb-2">Multi-Tenancy Funcionando ✓</h2>
        <p class="text-slate-400 text-sm max-w-xl mx-auto leading-relaxed">
          Este dashboard pertenece exclusivamente al tenant
          <strong class="text-indigo-400 font-mono">{{ tenantId }}</strong>.
          Cada tenant tiene su propio schema en PostgreSQL con usuarios, datos y sesiones completamente
          independientes. Prueba abriendo otro subdominio para comprobarlo.
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  tenantId: String,
  productsCount: Number,
})

const logout = () => {
  router.post(route('tenant.logout'))
}
</script>
