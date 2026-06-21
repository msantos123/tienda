<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4 h-16">
        <Link :href="route('tenant.settings.index')" class="text-slate-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </Link>
        <p class="text-white font-bold text-sm">Perfil de Usuario</p>
      </div>
    </nav>
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 rounded-xl bg-emerald-950/50 border border-emerald-900/50 text-emerald-400 text-sm flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <div class="space-y-5">
        <!-- Info card -->
        <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
          <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-indigo-900/50 border border-indigo-800 flex items-center justify-center text-indigo-400 text-2xl font-bold uppercase shrink-0">
              {{ user.name.charAt(0) }}
            </div>
            <div>
              <h2 class="text-xl font-bold text-white">{{ user.name }}</h2>
              <p class="text-sm text-slate-400">Administrador del tenant</p>
            </div>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Nombre Completo</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"/>
              <p v-if="form.errors.name" class="mt-1 text-xs text-rose-400">{{ form.errors.name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Correo Electrónico</label>
              <div class="px-4 py-3 bg-slate-950/50 border border-slate-800/50 rounded-xl text-slate-400 font-mono text-sm">{{ user.email }}</div>
              <p class="text-xs text-slate-600 mt-1">El correo no puede modificarse desde aquí.</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                Número de WhatsApp
                <span class="ml-2 px-2 py-0.5 bg-emerald-900/30 text-emerald-400 text-[10px] rounded-full border border-emerald-800/40 normal-case tracking-normal">Para el catálogo público</span>
              </label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </span>
                <input v-model="form.phone" type="tel" placeholder="Ej. +50212345678 (con código de país)" class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition font-mono"/>
              </div>
              <p v-if="form.errors.phone" class="mt-1 text-xs text-rose-400">{{ form.errors.phone }}</p>
              <p class="text-xs text-slate-600 mt-1.5">Incluye el código de país (Ej. +502 para Guatemala). Se mostrará como botón de contacto en el catálogo.</p>
            </div>

            <div class="pt-4 border-t border-slate-800/80 flex justify-end">
              <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition disabled:opacity-50">
                <svg v-if="form.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                Guardar Cambios
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: Object
})

const form = useForm({
  name: props.user.name,
  phone: props.user.phone || '',
})

const submit = () => {
  form.put(route('tenant.settings.profile.update'))
}
</script>
