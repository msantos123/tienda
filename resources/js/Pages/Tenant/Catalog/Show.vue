<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 pb-28">

    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-slate-900/80 backdrop-blur-xl border-b border-slate-800/60">
      <div class="max-w-3xl mx-auto px-4 h-14 flex items-center gap-3">
        <a :href="catalogUrl" class="text-slate-400 hover:text-white transition p-1 -ml-1">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="text-white font-semibold text-sm truncate flex-1">{{ product.name }}</span>
        <button @click="shareProduct" class="text-slate-400 hover:text-white transition p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        </button>
        <button @click="isCartOpen = true" class="text-slate-400 hover:text-white transition p-1 relative">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <span v-if="totalItemsCount > 0" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border-2 border-slate-900">
            {{ totalItemsCount }}
          </span>
        </button>
      </div>
    </header>

    <main class="max-w-3xl mx-auto">
      <!-- GALERÍA DE IMÁGENES -->
      <div class="relative bg-slate-900 aspect-square sm:aspect-video overflow-hidden">
        <template v-if="product.images && product.images.length > 0">
          <img :src="product.image_urls?.[activeImage] || `/storage/${product.images[activeImage]}`" :alt="product.name" class="w-full h-full object-contain transition-opacity duration-300"/>
          <!-- Thumbnails si hay más de 1 imagen -->
          <div v-if="product.images.length > 1" class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
            <button v-for="(img, i) in product.images" :key="i" @click="activeImage = i" :class="['w-2 h-2 rounded-full transition', i === activeImage ? 'bg-indigo-400' : 'bg-slate-600 hover:bg-slate-400']"/>
          </div>
        </template>
        <div v-else class="w-full h-full flex items-center justify-center">
          <svg class="w-20 h-20 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <!-- Badge oferta -->
        <div v-if="product.sale_price && product.show_price" class="absolute top-4 left-4 px-3 py-1 bg-rose-500 text-white text-xs font-bold rounded-full shadow-lg">OFERTA</div>
      </div>

      <!-- CONTENIDO DETALLE -->
      <div class="px-4 pt-5 space-y-5">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1.5 text-xs text-slate-500">
          <a :href="catalogUrl" class="hover:text-indigo-400 transition">Catálogo</a>
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          <span class="text-indigo-400">{{ product.category?.name }}</span>
        </nav>

        <!-- Nombre y Precio -->
        <div>
          <h1 class="text-2xl font-extrabold text-white leading-tight">{{ product.name }}</h1>
          <p class="text-xs text-slate-500 mt-1 font-mono">SKU: {{ product.sku }}</p>

          <div class="flex items-baseline gap-3 mt-4">
            <template v-if="product.show_price">
              <span class="text-3xl font-black text-white">${{ Number(product.sale_price || product.price).toFixed(2) }}</span>
              <span v-if="product.sale_price" class="text-lg text-slate-500 line-through">${{ Number(product.price).toFixed(2) }}</span>
              <span v-if="product.sale_price" class="px-2 py-0.5 bg-rose-500/20 text-rose-400 text-xs font-bold rounded-full">
                {{ discountPercent }}% OFF
              </span>
            </template>
            <template v-else>
              <span class="px-3 py-1 bg-indigo-500/20 text-indigo-400 text-sm font-bold uppercase tracking-wider rounded-lg border border-indigo-500/30">Precio a cotizar</span>
            </template>
          </div>
        </div>

        <!-- Stock -->
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-2">
            <template v-if="product.stock > 0">
              <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-950/50 border border-emerald-900/50 rounded-full">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"/>
                <span class="text-emerald-400 text-sm font-medium">
                  {{ product.show_stock ? `${product.stock} en stock` : 'Disponible' }}
                </span>
              </div>
            </template>
            <template v-else>
              <div class="flex items-center gap-2 px-3 py-1.5 bg-rose-950/50 border border-rose-900/50 rounded-full">
                <span class="w-2 h-2 rounded-full bg-rose-400"/>
                <span class="text-rose-400 text-sm font-medium">Agotado</span>
              </div>
            </template>
          </div>
          
          <!-- Selector de cantidad -->
          <div v-if="product.stock > 0" class="flex items-center gap-2 bg-slate-900 border border-slate-800 rounded-xl p-1">
            <button @click="quantity > 1 ? quantity-- : null" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">-</button>
            <span class="text-sm font-bold w-6 text-center text-white">{{ quantity }}</span>
            <button @click="quantity < product.stock ? quantity++ : null" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition">+</button>
          </div>
        </div>

        <!-- Descripción -->
        <div v-if="product.description" class="bg-slate-900/50 border border-slate-800/60 rounded-2xl p-4">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Descripción</h2>
          <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ product.description }}</p>
        </div>

          <!-- Atributos dinámicos EAV (Solo lectura - información del producto) -->
          <div v-if="product.attribute_values && product.attribute_values.length > 0" class="bg-slate-900/50 border border-slate-800/60 rounded-2xl overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-800/60">
              <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Especificaciones</h2>
            </div>
            <div class="divide-y divide-slate-800/60">
              <div v-for="av in product.attribute_values" :key="av.id" class="flex items-center justify-between px-4 py-3">
                <span class="text-sm text-slate-400 font-medium">{{ av.attribute?.name }}</span>
                <span class="text-sm text-white font-semibold text-right max-w-[55%]">{{ av.value }}</span>
              </div>
            </div>
          </div>

          <!-- Atributos Interactivos del Comprador (select y textarea) -->
          <div v-if="buyerAttributes && buyerAttributes.length > 0" class="space-y-4">
            <div v-for="attr in buyerAttributes" :key="attr.id">
              <!-- SELECT: Chips de opciones -->
              <div v-if="attr.type === 'select'">
                <div class="flex items-center justify-between mb-2">
                  <h2 class="text-sm font-semibold text-white">{{ attr.name }}</h2>
                  <span v-if="attr.is_required" class="text-xs text-rose-400 font-medium">Requerido</span>
                </div>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="option in (attr.options || [])" :key="option"
                    type="button"
                    @click="selectBuyerOption(attr.id, option)"
                    class="px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all duration-150"
                    :class="buyerSelections[attr.id] === option
                      ? 'bg-indigo-600 border-indigo-500 text-white shadow-md shadow-indigo-900/40'
                      : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-indigo-600 hover:text-white'"
                  >
                    {{ option }}
                  </button>
                </div>
                <p v-if="buyerErrors[attr.id]" class="text-rose-400 text-xs mt-1.5">{{ buyerErrors[attr.id] }}</p>
              </div>

              <!-- TEXTAREA: Campo de texto libre -->
              <div v-else-if="attr.type === 'textarea'">
                <div class="flex items-center justify-between mb-2">
                  <h2 class="text-sm font-semibold text-white">{{ attr.name }}</h2>
                  <span v-if="attr.is_required" class="text-xs text-rose-400 font-medium">Requerido</span>
                </div>
                <textarea
                  v-model="buyerSelections[attr.id]"
                  rows="3"
                  :placeholder="`Escribe aqui tu ${attr.name.toLowerCase()}...`"
                  class="w-full bg-slate-900 border-2 border-slate-700 rounded-2xl p-3 text-white text-sm focus:border-indigo-500 focus:outline-none transition resize-none"
                  :class="{'border-rose-500': buyerErrors[attr.id]}"
                ></textarea>
                <p v-if="buyerErrors[attr.id]" class="text-rose-400 text-xs mt-1.5">{{ buyerErrors[attr.id] }}</p>
              </div>
            </div>
          </div>
        </div>
    </main>

    <!-- ── BARRA DE ACCIONES FIJA EN LA PARTE INFERIOR ── -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800/60 p-4 safe-area-inset-bottom">
      <div class="max-w-3xl mx-auto flex gap-3">
        <!-- Añadir al carrito -->
        <button 
          v-if="product.stock > 0"
          @click="addToCartAndNotify"
          class="flex-[2] flex items-center justify-center gap-2 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-900/30 transition text-sm relative overflow-hidden group"
        >
          <span v-if="showAddedNotification" class="absolute inset-0 bg-emerald-500 flex items-center justify-center text-white z-10 font-bold transition-all duration-300">
            ¡Añadido!
          </span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          Añadir al Carrito
        </button>

        <!-- Compartir -->
        <button @click="shareProduct" class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-2xl transition text-sm">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
          <span>Compartir</span>
        </button>
      </div>
    </div>
  </div>

  <CartDrawer :adminPhone="adminPhone" />
</template>

<script setup>
import { ref, computed } from 'vue'
import CartDrawer from '../../../Components/Tenant/CartDrawer.vue'
import { useCart } from '../../../composables/useCart'

const props = defineProps({
  product: Object,
  adminPhone: String,
  buyerAttributes: { type: Array, default: () => [] },
})

// Selecciones del comprador
const buyerSelections = ref({})
const buyerErrors = ref({})

const selectBuyerOption = (attrId, option) => {
  buyerSelections.value[attrId] = option
  if (buyerErrors.value[attrId]) delete buyerErrors.value[attrId]
}

const validateBuyerSelections = () => {
  const errors = {}
  props.buyerAttributes.forEach(attr => {
    if (attr.is_required && !buyerSelections.value[attr.id]) {
      errors[attr.id] = `Por favor, selecciona o ingresa ${attr.name}.`
    }
  })
  buyerErrors.value = errors
  return Object.keys(errors).length === 0
}

const activeImage = ref(0)
const quantity = ref(1)
const showAddedNotification = ref(false)

const { addToCart, isCartOpen, totalItemsCount } = useCart()

const addToCartAndNotify = () => {
  if (!validateBuyerSelections()) return

  addToCart(props.product, quantity.value, { ...buyerSelections.value })
  showAddedNotification.value = true
  setTimeout(() => {
    showAddedNotification.value = false
  }, 1500)
}

const catalogUrl = computed(() => {
  return route('tenant.catalog.index')
})

const discountPercent = computed(() => {
  if (!props.product.sale_price) return 0
  const diff = props.product.price - props.product.sale_price
  return Math.round((diff / props.product.price) * 100)
})



const shareProduct = async () => {
  const shareData = {
    title: props.product.name,
    text: `Mira este producto: ${props.product.name}`,
    url: window.location.href,
  }
  if (navigator.share) {
    try {
      await navigator.share(shareData)
    } catch {}
  } else {
    await navigator.clipboard.writeText(window.location.href)
    alert('¡Enlace copiado al portapapeles!')
  }
}
</script>
