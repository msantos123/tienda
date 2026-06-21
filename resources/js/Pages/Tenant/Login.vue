<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <!-- Brand -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-xl shadow-indigo-500/30 mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <!-- Tenant indicator badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/60 border border-indigo-800/50 mb-3">
          <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
          <span class="text-xs font-mono font-medium text-indigo-300">{{ tenantId }}.localhost</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Portal de Empresa</h1>
        <p class="text-slate-400 text-sm mt-1">Ingresa tus credenciales para acceder</p>
      </div>

      <!-- Card -->
      <div class="bg-slate-900/50 border border-slate-800/80 rounded-2xl p-8 shadow-2xl backdrop-blur-md">
        <!-- Status message -->
        <div v-if="status" class="mb-5 text-sm text-emerald-400 bg-emerald-950/50 border border-emerald-800/50 px-4 py-3 rounded-xl">
          {{ status }}
        </div>

        <!-- Errors -->
        <div v-if="form.errors.email" class="mb-5 p-3.5 bg-rose-950/50 border border-rose-500/30 text-rose-300 rounded-xl text-sm">
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Correo Electrónico</label>
            <input
              v-model="form.email"
              id="email"
              type="email"
              required
              autofocus
              autocomplete="username"
              placeholder="admin@empresa.com"
              class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
              :class="{ 'border-rose-500': form.errors.email }"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Contraseña</label>
            <input
              v-model="form.password"
              id="password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-sm transition"
              :class="{ 'border-rose-500': form.errors.password }"
            />
          </div>

          <div class="flex items-center gap-2">
            <input
              v-model="form.remember"
              id="remember"
              type="checkbox"
              class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900"
            />
            <label for="remember" class="text-sm text-slate-400 cursor-pointer">Recordarme</label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed rounded-xl transition duration-150 shadow-md shadow-indigo-500/20 mt-1"
          >
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Verificando...
            </span>
            <span v-else>Iniciar Sesión</span>
          </button>
        </form>
      </div>

      <p class="text-center text-xs text-slate-600 mt-6">
        Powered by Multi-Tenant &bull; Schema: <span class="font-mono">tenant_{{ tenantId }}</span>
      </p>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  status: String,
  tenantId: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('tenant.login.store'), {
    onFinish: () => form.reset('password'),
  })
}
</script>
