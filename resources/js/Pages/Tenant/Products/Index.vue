<template>
  <div class="min-h-screen bg-slate-950 pb-20">
    <!-- Navbar / Header Mobile -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.dashboard')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Productos</h1>
        <Link :href="route('tenant.products.create')" class="text-indigo-400 p-2 -mr-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </Link>
      </div>
    </header>

    <main class="p-4 max-w-lg mx-auto">
      <!-- Mensajes de estado -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-2xl flex items-center gap-3 text-sm">
        <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <div v-if="products.data.length === 0" class="text-center py-10 bg-slate-900/60 rounded-3xl border border-slate-800">
        <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p class="text-slate-400">No hay productos registrados.</p>
        <Link :href="route('tenant.products.create')" class="mt-4 inline-block bg-indigo-600/20 text-indigo-400 px-6 py-2 rounded-full font-bold text-sm">Crear tu primer producto</Link>
      </div>

      <div v-else class="space-y-4">
        <!-- Lista de productos -->
        <div v-for="product in products.data" :key="product.id" class="bg-slate-900 border border-slate-800 rounded-3xl p-4 flex gap-4 relative overflow-hidden">
          
          <div class="w-24 h-24 rounded-2xl bg-slate-800 shrink-0 overflow-hidden border border-slate-700 relative flex items-center justify-center">
            <img v-if="product.images && product.images.length > 0" :src="`/storage/${product.images[0]}`" class="w-full h-full object-cover" :alt="product.name">
            <svg v-else class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          </div>
          
          <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between gap-2">
                <h3 class="text-white font-bold truncate">{{ product.name }}</h3>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase shrink-0" :class="product.status ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'">
                  {{ product.status ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <p class="text-slate-400 text-xs mt-1 truncate">{{ product.category?.name }} | {{ product.sku }}</p>
              <p class="text-slate-300 text-xs mt-1">Stock: <span class="font-bold text-white">{{ product.stock }}</span></p>
            </div>
            
            <div class="flex items-center justify-between mt-2">
              <span class="text-indigo-400 font-bold">${{ Number(product.price).toFixed(2) }}</span>
              <div class="flex items-center gap-2">
                <Link :href="route('tenant.products.edit', product.id)" class="w-8 h-8 flex items-center justify-center bg-slate-800 rounded-full text-slate-300 hover:text-white hover:bg-slate-700 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </Link>
                <button @click="deleteProduct(product.id)" class="w-8 h-8 flex items-center justify-center bg-rose-500/10 rounded-full text-rose-400 hover:text-rose-300 hover:bg-rose-500/20 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paginación Simple -->
        <div class="flex justify-between items-center mt-6 p-4">
          <Link v-if="products.prev_page_url" :href="products.prev_page_url" class="text-indigo-400 font-bold text-sm">← Anterior</Link>
          <span v-else class="text-slate-600 text-sm">← Anterior</span>
          <span class="text-slate-400 text-sm text-center">Página {{ products.current_page }} de {{ products.last_page }}</span>
          <Link v-if="products.next_page_url" :href="products.next_page_url" class="text-indigo-400 font-bold text-sm">Siguiente →</Link>
          <span v-else class="text-slate-600 text-sm">Siguiente →</span>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  products: {
    type: Object,
    required: true
  }
})

const deleteProduct = (id) => {
  if (confirm('¿Estás seguro de que quieres eliminar este producto? Esta acción no se puede deshacer.')) {
    router.delete(route('tenant.products.destroy', id), {
      preserveScroll: true
    })
  }
}
</script>
