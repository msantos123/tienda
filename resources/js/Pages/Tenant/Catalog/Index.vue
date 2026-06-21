<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 pb-24">

    <!-- ── HEADER ── -->
    <header class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/60">
      <div class="max-w-5xl mx-auto px-4 h-16 flex items-center gap-3">
        <!-- Logo / Nombre tenant -->
        <div class="flex-1 flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <span class="font-bold text-white text-base">Catálogo</span>
        </div>

        <!-- Buscador desktop -->
        <div class="hidden md:flex flex-1 max-w-xs relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input v-model="searchInput" @input="debouncedSearch" type="text" placeholder="Buscar producto..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition"/>
        </div>

        <!-- Botón filtros móvil -->
        <div class="flex items-center gap-2">
          <button @click="isCartOpen = true" class="p-2 text-slate-400 hover:text-white transition bg-slate-800/50 rounded-xl relative">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span v-if="totalItemsCount > 0" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">{{ totalItemsCount }}</span>
          </button>
          
          <button @click="drawerOpen = true" class="md:hidden flex items-center gap-1.5 px-3 py-2 bg-slate-800 rounded-xl text-slate-300 text-sm font-medium relative">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filtros
            <span v-if="activeFiltersCount > 0" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-indigo-500 text-white text-[10px] font-bold flex items-center justify-center">{{ activeFiltersCount }}</span>
          </button>
        </div>
      </div>

      <!-- Buscador móvil (debajo del header) -->
      <div class="md:hidden px-4 pb-3">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
          <input v-model="searchInput" @input="debouncedSearch" type="text" placeholder="Buscar por nombre o SKU..." class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-9 pr-3 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition"/>
        </div>
      </div>
    </header>

    <!-- ── DRAWER FILTROS MÓVIL ── -->
    <Transition name="fade">
      <div v-if="drawerOpen" class="fixed inset-0 z-[60] bg-black/60 backdrop-blur-sm md:hidden" @click="drawerOpen = false"/>
    </Transition>
    <Transition name="slide-up">
      <div v-if="drawerOpen" class="fixed bottom-0 left-0 right-0 z-[70] bg-slate-900 border-t border-slate-800 rounded-t-3xl p-6 md:hidden max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-white font-bold text-lg">Filtros</h3>
          <button @click="drawerOpen = false" class="text-slate-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <FilterPanel :categories="categories" :priceRange="priceRange" :filters="localFilters" @update="applyFilters" @clear="clearFilters"/>
        <button @click="drawerOpen = false" class="mt-6 w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl transition">
          Ver resultados ({{ products.total }})
        </button>
      </div>
    </Transition>

    <!-- ── LAYOUT PRINCIPAL ── -->
    <div class="max-w-5xl mx-auto px-4 pt-6">
      <div class="flex gap-6">
        <!-- Sidebar filtros desktop -->
        <aside class="hidden md:block w-64 shrink-0">
          <div class="bg-slate-900/50 border border-slate-800/60 rounded-2xl p-5 sticky top-24">
            <div class="flex items-center justify-between mb-5">
              <h3 class="text-white font-semibold text-sm">Filtros</h3>
              <button v-if="activeFiltersCount > 0" @click="clearFilters" class="text-xs text-indigo-400 hover:text-indigo-300 transition">Limpiar</button>
            </div>
            <FilterPanel :categories="categories" :priceRange="priceRange" :filters="localFilters" @update="applyFilters" @clear="clearFilters"/>
          </div>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 min-w-0">
          <!-- Resultado info -->
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-400">
              <span class="text-white font-semibold">{{ products.total }}</span> productos encontrados
            </p>
            <button v-if="activeFiltersCount > 0" @click="clearFilters" class="md:hidden text-xs text-indigo-400 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              Limpiar filtros
            </button>
          </div>

          <!-- Grid de productos -->
          <div v-if="products.data.length > 0" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
            <div v-for="product in products.data" :key="product.id" class="group bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden hover:border-indigo-700/60 transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-900/20 flex flex-col relative">
              <a :href="productUrl(product.slug)" class="block aspect-square bg-slate-800 relative overflow-hidden">
                <img v-if="product.images && product.images.length > 0" :src="`/storage/${product.images[0]}`" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"/>
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-10 h-10 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <!-- Badge agotado -->
                <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                  <span class="px-3 py-1 bg-slate-900/90 text-rose-400 text-xs font-bold rounded-full border border-rose-900">Agotado</span>
                </div>
                <!-- Badge oferta -->
                <div v-if="product.sale_price && product.show_price" class="absolute top-2 left-2 px-2 py-0.5 bg-rose-500 text-white text-[10px] font-bold rounded-full">OFERTA</div>
              </a>
              <!-- Info -->
              <div class="p-3 flex-1 flex flex-col">
                <a :href="productUrl(product.slug)" class="block flex-1">
                  <p class="text-[10px] font-medium text-indigo-400 uppercase tracking-wider truncate">{{ product.category?.name }}</p>
                  <h3 class="text-white font-semibold text-sm leading-tight mt-0.5 line-clamp-2">{{ product.name }}</h3>
                  <div class="mt-2 flex items-baseline gap-2">
                    <template v-if="product.show_price">
                      <span class="text-white font-bold">${{ Number(product.sale_price || product.price).toFixed(2) }}</span>
                      <span v-if="product.sale_price" class="text-slate-500 text-xs line-through">${{ Number(product.price).toFixed(2) }}</span>
                    </template>
                    <template v-else>
                      <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">A Cotizar</span>
                    </template>
                  </div>
                </a>
                
                <button 
                  v-if="product.stock > 0"
                  @click="addToCart(product)"
                  class="mt-3 w-full py-2 bg-slate-800 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1 group-hover:bg-slate-700"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                  Añadir al Carrito
                </button>
              </div>
            </div>
          </div>

          <!-- Estado vacío -->
          <div v-else class="text-center py-16 bg-slate-900/40 rounded-2xl border border-slate-800">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <p class="text-slate-400 font-medium">No se encontraron productos</p>
            <button @click="clearFilters" class="mt-4 px-5 py-2 bg-indigo-600/20 text-indigo-400 rounded-full text-sm font-medium hover:bg-indigo-600/30 transition">Limpiar filtros</button>
          </div>

          <!-- Paginación -->
          <div v-if="products.last_page > 1" class="flex justify-center items-center gap-2 mt-8">
            <a v-if="products.prev_page_url" :href="products.prev_page_url" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-indigo-700 transition text-sm font-medium">
              ← Anterior
            </a>
            <span class="text-slate-500 text-sm px-3">{{ products.current_page }} / {{ products.last_page }}</span>
            <a v-if="products.next_page_url" :href="products.next_page_url" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-indigo-700 transition text-sm font-medium">
              Siguiente →
            </a>
          </div>
        </main>
      </div>
    </div>
  </div>

  <CartDrawer :adminPhone="adminPhone" />
  
  <!-- ── COMPONENTE DE FILTROS (inline) ── -->
</template>

<script setup>
import { ref, computed } from 'vue'
import CartDrawer from '../../../Components/Tenant/CartDrawer.vue'
import { useCart } from '../../../composables/useCart'

// Componente inline de panel de filtros
const FilterPanel = {
  props: ['categories', 'priceRange', 'filters'],
  emits: ['update', 'clear'],
  setup(props, { emit }) {
    const local = ref({
      category: props.filters.category || '',
      min_price: props.filters.min_price || '',
      max_price: props.filters.max_price || '',
      in_stock: props.filters.in_stock || false,
    })

    const update = () => emit('update', { ...local.value })

    return { local, update }
  },
  template: `
    <div class="space-y-6">
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Categoría</p>
        <div class="flex flex-wrap gap-2">
          <button @click="local.category = ''; update()" :class="['px-3 py-1.5 rounded-full text-xs font-medium border transition', !local.category ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-indigo-700']">Todas</button>
          <button v-for="cat in categories" :key="cat.id" @click="local.category = cat.slug; update()" :class="['px-3 py-1.5 rounded-full text-xs font-medium border transition', local.category === cat.slug ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-indigo-700']">{{ cat.name }}</button>
        </div>
      </div>
      <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Rango de Precio</p>
        <div class="flex items-center gap-2">
          <input v-model="local.min_price" @change="update" type="number" placeholder="Mín" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition"/>
          <span class="text-slate-600">—</span>
          <input v-model="local.max_price" @change="update" type="number" placeholder="Máx" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition"/>
        </div>
      </div>
      <div>
        <label class="flex items-center gap-3 cursor-pointer group">
          <div @click="local.in_stock = !local.in_stock; update()" :class="['w-10 h-6 rounded-full transition relative', local.in_stock ? 'bg-indigo-600' : 'bg-slate-700']">
            <span :class="['absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform', local.in_stock ? 'translate-x-4' : 'translate-x-0']"/>
          </div>
          <span class="text-sm text-slate-300">Solo disponibles (en stock)</span>
        </label>
      </div>
    </div>
  `
}

const props = defineProps({
  products: Object,
  categories: Array,
  priceRange: Object,
  filters: Object,
  adminPhone: String,
})

const { addToCart, isCartOpen, totalItemsCount } = useCart()

const drawerOpen = ref(false)
const searchInput = ref(props.filters.q || '')
const localFilters = ref({ ...props.filters })

let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters({ ...localFilters.value, q: searchInput.value })
  }, 400)
}

const activeFiltersCount = computed(() => {
  let count = 0
  if (props.filters.q) count++
  if (props.filters.category) count++
  if (props.filters.min_price) count++
  if (props.filters.max_price) count++
  if (props.filters.in_stock) count++
  return count
})

const applyFilters = (newFilters) => {
  localFilters.value = { ...newFilters }
  const params = new URLSearchParams()
  if (searchInput.value) params.set('q', searchInput.value)
  if (newFilters.category) params.set('category', newFilters.category)
  if (newFilters.min_price) params.set('min_price', newFilters.min_price)
  if (newFilters.max_price) params.set('max_price', newFilters.max_price)
  if (newFilters.in_stock) params.set('in_stock', '1')
  const qs = params.toString()
  window.location.href = window.location.pathname + (qs ? '?' + qs : '')
}

const clearFilters = () => {
  searchInput.value = ''
  localFilters.value = {}
  window.location.href = window.location.pathname
}

const productUrl = (slug) => {
  // Construye la URL del detalle preservando el dominio del tenant
  const base = window.location.origin
  return `${base}/catalogo/${slug}`
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-up-enter-active, .slide-up-leave-active { transition: transform 0.3s cubic-bezier(0.32,0.72,0,1); }
.slide-up-enter-from, .slide-up-leave-to { transform: translateY(100%); }
</style>
