<template>
  <div class="min-h-screen bg-slate-950 pb-20">
    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.settings.index')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Atributos</h1>
        <Link :href="route('tenant.settings.attributes.create')" class="text-fuchsia-400 p-2 -mr-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </Link>
      </div>
    </header>

    <main class="p-4 max-w-lg mx-auto">
      <!-- Flash -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-2xl flex items-center gap-3 text-sm">
        <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <!-- Estado vacío -->
      <div v-if="attributes.length === 0" class="text-center py-10 bg-slate-900/60 rounded-3xl border border-slate-800">
        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
        <p class="text-slate-400">No hay atributos registrados.</p>
        <Link :href="route('tenant.settings.attributes.create')" class="mt-4 inline-block bg-fuchsia-600/20 text-fuchsia-400 px-6 py-2 rounded-full font-bold text-sm">Crear primer atributo</Link>
      </div>

      <!-- Lista de atributos -->
      <div v-else class="space-y-4">
        <div v-for="attr in attributes" :key="attr.id" class="bg-slate-900 border border-slate-800 rounded-3xl p-4 flex gap-4 relative overflow-hidden">
          <!-- Icono -->
          <div class="w-16 h-16 rounded-2xl bg-fuchsia-950/60 border border-fuchsia-900/50 shrink-0 flex items-center justify-center">
            <svg class="w-7 h-7 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between gap-2">
                <h3 class="text-white font-bold truncate">{{ attr.name }}</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase shrink-0 bg-fuchsia-500/20 text-fuchsia-400">{{ attr.type }}</span>
              </div>
              <!-- Categorías asociadas -->
              <div class="flex flex-wrap gap-1.5 mt-2">
                <span v-for="cat in attr.categories" :key="cat.id" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-indigo-900/40 text-indigo-300 border border-indigo-800/50">
                  {{ cat.name }}
                </span>
                <span v-if="!attr.categories.length" class="text-xs text-slate-500 italic">Global</span>
              </div>
            </div>

            <div class="flex items-center justify-end mt-3 gap-2">
              <Link :href="route('tenant.settings.attributes.edit', attr.id)" class="w-8 h-8 flex items-center justify-center bg-slate-800 rounded-full text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </Link>
              <button @click="deleteAttribute(attr.id)" class="w-8 h-8 flex items-center justify-center bg-rose-500/10 rounded-full text-rose-400 hover:text-rose-300 hover:bg-rose-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
  attributes: Array
})

const deleteAttribute = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar este atributo? Los productos podrían perder estos datos.')) {
    router.delete(route('tenant.settings.attributes.destroy', id), { preserveScroll: true })
  }
}
</script>
