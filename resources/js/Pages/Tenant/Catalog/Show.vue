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
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-slate-905/95 backdrop-blur-xl border-t border-slate-800/60 p-4 safe-area-inset-bottom">
      <div class="max-w-3xl mx-auto flex gap-2">
        <!-- Compartir (Icono compacto) -->
        <button @click="shareProduct" class="w-12 h-12 flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-2xl transition shrink-0 shadow-lg" title="Compartir">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        </button>

        <!-- Añadir al carrito (Icono compacto) -->
        <button 
          v-if="product.stock > 0"
          @click="addToCartAndNotify"
          class="w-12 h-12 flex items-center justify-center bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 text-indigo-400 font-bold rounded-2xl transition shrink-0 relative overflow-hidden group shadow-lg"
          title="Añadir al carrito"
        >
          <span v-if="showAddedNotification" class="absolute inset-0 bg-emerald-500 flex items-center justify-center text-white z-10 font-bold transition-all duration-300">
            ✓
          </span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </button>

        <!-- Comprar por WhatsApp (Principal, llamativo y cómodo para pulgar) -->
        <button 
          v-if="product.stock > 0"
          @click="buyOnWhatsApp"
          :disabled="isSubmitting"
          class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-emerald-600 hover:bg-emerald-500 active:scale-95 disabled:opacity-50 disabled:pointer-events-none text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-950/40 transition-all text-xs uppercase tracking-wider"
        >
          <svg v-if="isSubmitting" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
          <span>Escribir por WhatsApp</span>
        </button>
      </div>
    </div>
  </div>

  <CartDrawer :adminPhone="adminPhone" />
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
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
const isSubmitting = ref(false)

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

const buyOnWhatsApp = async () => {
  if (!validateBuyerSelections()) return

  isSubmitting.value = true
  
  // Formatear selecciones del comprador para mandar nombres legibles
  const buyerChoicesFormatted = {}
  props.buyerAttributes.forEach(attr => {
    if (buyerSelections.value[attr.id]) {
      buyerChoicesFormatted[attr.name] = buyerSelections.value[attr.id]
    }
  })

  try {
    const response = await axios.post(route('tenant.whatsapp-leads.store-public'), {
      product_id: props.product.id,
      quantity: quantity.value,
      buyer_choices: buyerChoicesFormatted
    })

    if (response.data?.whatsapp_url) {
      window.location.href = response.data.whatsapp_url
    }
  } catch (err) {
    console.error('Error al iniciar compra por WhatsApp:', err)
    alert(err.response?.data?.error || 'Ocurrió un error al procesar el pedido. Por favor intenta de nuevo.')
  } finally {
    isSubmitting.value = false
  }
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
