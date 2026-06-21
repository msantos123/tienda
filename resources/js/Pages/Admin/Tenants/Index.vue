<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <!-- Topbar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <div>
            <p class="text-white font-bold text-sm leading-none">Multi-Tenant Admin</p>
            <p class="text-indigo-400 text-[11px] mt-0.5">Panel Central de Control</p>
          </div>
        </div>
        <Link :href="route('admin.tenants.create')"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-95 rounded-xl transition shadow-lg shadow-indigo-500/20">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Nuevo Tenant
        </Link>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- Page title -->
      <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-white">Tenants Registrados</h1>
        <p class="text-slate-400 text-sm mt-1">Cada tenant tiene su propio schema aislado en PostgreSQL.</p>
      </div>

      <!-- Flash message -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-xl flex items-center gap-3 text-sm">
        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <!-- Tenants Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="tenant in tenants"
          :key="tenant.id"
          class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700 transition duration-200 flex flex-col justify-between group"
        >
          <div>
            <div class="flex items-start justify-between gap-3 mb-4">
              <h2 class="text-lg font-bold text-white leading-snug">{{ tenant.company_name }}</h2>
              <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-950 text-indigo-300 border border-indigo-800 font-mono">
                tenant_{{ tenant.id }}
              </span>
            </div>

            <p class="text-xs text-slate-500 mb-4">
              ID: <span class="font-mono text-indigo-400">{{ tenant.id }}</span>
            </p>

            <!-- Domains -->
            <div class="bg-slate-950/60 rounded-xl p-3.5 border border-slate-800/50 space-y-2">
              <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Dominios</p>
              <a
                v-for="domain in tenant.domains"
                :key="domain.id"
                :href="`http://${domain.domain}:8000`"
                target="_blank"
                class="flex items-center justify-between text-slate-300 hover:text-indigo-400 text-sm transition group/link"
              >
                <span class="underline decoration-dotted underline-offset-2 font-medium">{{ domain.domain }}</span>
                <svg class="w-3.5 h-3.5 text-slate-500 group-hover/link:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
              </a>
            </div>
          </div>

          <div class="mt-5 pt-4 border-t border-slate-800/50 text-xs text-slate-500">
            Creado: {{ new Date(tenant.created_at).toLocaleString('es', { dateStyle: 'medium', timeStyle: 'short' }) }}
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="!tenants.length" class="col-span-full bg-slate-900/20 border-2 border-dashed border-slate-800 rounded-2xl p-16 text-center">
          <div class="w-14 h-14 bg-slate-800/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
          </div>
          <h3 class="text-lg font-semibold text-slate-300 mb-1">Sin tenants aún</h3>
          <p class="text-sm text-slate-500 max-w-xs mx-auto mb-6">Registra tu primer cliente para inicializar automáticamente su schema en PostgreSQL.</p>
          <Link :href="route('admin.tenants.create')" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl transition">
            Crear primer tenant
          </Link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  tenants: {
    type: Array,
    default: () => []
  }
})
</script>
