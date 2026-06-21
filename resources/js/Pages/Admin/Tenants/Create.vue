<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center py-12 px-4">
    <div class="max-w-2xl w-full">
      <!-- Back -->
      <Link :href="route('admin.tenants.index')" class="inline-flex items-center gap-1.5 text-xs text-indigo-400 hover:text-indigo-300 transition mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Volver al panel central
      </Link>

      <div class="bg-slate-900/40 border border-slate-800/80 rounded-3xl p-8 shadow-2xl backdrop-blur-md">
        <!-- Header -->
        <div class="mb-8 pb-6 border-b border-slate-800/80">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/25">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-extrabold text-white">Registrar Nuevo Tenant</h1>
              <p class="text-slate-400 text-xs mt-0.5">Se aprovisionará automáticamente el schema en PostgreSQL</p>
            </div>
          </div>
        </div>

        <!-- Errors -->
        <div v-if="Object.keys(errors).length" class="mb-6 p-4 bg-rose-950/50 border border-rose-500/30 text-rose-300 rounded-xl text-sm space-y-1">
          <p class="font-semibold text-rose-200 mb-1">Por favor corrige los errores:</p>
          <p v-for="(error, key) in errors" :key="key">• {{ error }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-7">
          <!-- Tenant Config -->
          <div>
            <h3 class="text-indigo-400 font-semibold text-xs uppercase tracking-widest mb-4">1. Configuración del Tenant</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">ID / Subdominio *</label>
                <input
                  v-model="form.id"
                  type="text"
                  placeholder="mi-negocio"
                  class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                  :class="{ 'border-rose-500': errors.id }"
                />
                <p class="text-[10px] text-slate-500 mt-1.5">URL: <span class="font-mono text-indigo-400">id.localhost:8000</span> | Schema: <span class="font-mono text-indigo-400">tenant_id</span></p>
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nombre de la Empresa *</label>
                <input
                  v-model="form.company_name"
                  type="text"
                  placeholder="Mi Empresa S.A.S"
                  class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                  :class="{ 'border-rose-500': errors.company_name }"
                />
              </div>
            </div>
          </div>

          <!-- Admin Config -->
          <div class="pt-6 border-t border-slate-800/60">
            <h3 class="text-indigo-400 font-semibold text-xs uppercase tracking-widest mb-4">2. Cuenta del Administrador</h3>
            <div class="space-y-5">
              <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nombre completo *</label>
                <input
                  v-model="form.admin_name"
                  type="text"
                  placeholder="Juan Pérez"
                  class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                  :class="{ 'border-rose-500': errors.admin_name }"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Correo Electrónico *</label>
                <input
                  v-model="form.admin_email"
                  type="email"
                  placeholder="admin@empresa.com"
                  class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                  :class="{ 'border-rose-500': errors.admin_email }"
                />
                <p class="text-[10px] text-slate-500 mt-1.5">Con este email iniciará sesión en su portal.</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Contraseña *</label>
                  <input
                    v-model="form.admin_password"
                    type="password"
                    placeholder="••••••••"
                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                    :class="{ 'border-rose-500': errors.admin_password }"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Confirmar Contraseña *</label>
                  <input
                    v-model="form.admin_password_confirmation"
                    type="password"
                    placeholder="••••••••"
                    class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Submit -->
          <div class="pt-6 border-t border-slate-800/60 flex justify-end gap-3">
            <Link :href="route('admin.tenants.index')" class="px-5 py-3 text-sm font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition">
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed rounded-xl transition shadow-lg shadow-indigo-500/20"
            >
              <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              {{ form.processing ? 'Aprovisionando...' : 'Aprovisionar Tenant' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

defineProps({ errors: { type: Object, default: () => ({}) } })

const form = useForm({
  id: '',
  company_name: '',
  admin_name: '',
  admin_email: '',
  admin_password: '',
  admin_password_confirmation: '',
})

const submit = () => {
  form.post(route('admin.tenants.store'), {
    onError: () => form.reset('admin_password', 'admin_password_confirmation'),
  })
}
</script>
