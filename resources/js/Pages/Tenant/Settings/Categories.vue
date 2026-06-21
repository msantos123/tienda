<template>
  <div class="min-h-screen bg-slate-950 pb-20">
    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.settings.index')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Categorías</h1>
        <Link :href="route('tenant.settings.categories.create')" class="text-indigo-400 p-2 -mr-2">
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
      <div v-if="categories.length === 0" class="text-center py-10 bg-slate-900/60 rounded-3xl border border-slate-800">
        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        <p class="text-slate-400">No hay categorías registradas.</p>
        <Link :href="route('tenant.settings.categories.create')" class="mt-4 inline-block bg-indigo-600/20 text-indigo-400 px-6 py-2 rounded-full font-bold text-sm">Crear primera categoría</Link>
      </div>

      <!-- Lista de categorías -->
      <div v-else class="space-y-4">
        <div v-for="category in categories" :key="category.id" class="bg-slate-900 border border-slate-800 rounded-3xl p-4 flex gap-4 relative overflow-hidden">
          <!-- Icono -->
          <div class="w-16 h-16 rounded-2xl bg-amber-950/60 border border-amber-900/50 shrink-0 flex items-center justify-center">
            <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between gap-2">
                <h3 class="text-white font-bold truncate">{{ category.name }}</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase shrink-0 bg-amber-500/20 text-amber-400">#{{ category.id }}</span>
              </div>
              <p class="text-slate-500 text-xs mt-1 font-mono truncate">{{ category.slug }}</p>
            </div>

            <div class="flex items-center justify-end mt-3 gap-2">
              <Link :href="route('tenant.settings.categories.edit', category.id)" class="w-8 h-8 flex items-center justify-center bg-slate-800 rounded-full text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </Link>
              <button @click="deleteCategory(category.id)" class="w-8 h-8 flex items-center justify-center bg-rose-500/10 rounded-full text-rose-400 hover:text-rose-300 hover:bg-rose-500/20 transition">
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
  categories: Array
})

const deleteCategory = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
    router.delete(route('tenant.settings.categories.destroy', id), { preserveScroll: true })
  }
}
</script>
