<template>
  <Head>
    <title>{{ product.name }} — {{ $page.props.tenant?.name || 'Catálogo' }}</title>
    <meta name="description" :content="product.description ? product.description.substring(0, 160) : `Compra ${product.name} al mejor precio.`" />
    <meta property="og:title" :content="product.name" />
    <meta property="og:image" :content="product.image_urls?.[0] || (product.images?.length ? `/storage/${product.images[0]}` : '')" />
  </Head>

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

      <!-- ── GALERÍA DE IMÁGENES con swipe táctil y flechas ── -->
      <div
        class="relative bg-slate-900 aspect-square sm:aspect-video overflow-hidden select-none"
        @touchstart.passive="handleTouchStart"
        @touchend.passive="handleTouchEnd"
      >
        <template v-if="product.images && product.images.length > 0">

          <!-- Imagen con transición fade al cambiar -->
          <Transition name="img-fade" mode="out-in">
            <img
              :key="activeImage"
              :src="product.image_urls?.[activeImage] || `/storage/${product.images[activeImage]}`"
              :alt="product.name"
              class="w-full h-full object-contain"
            />
          </Transition>

          <!-- Flechas de navegación lateral (desktop + cuando hay varias imágenes) -->
          <template v-if="product.images.length > 1">
            <button
              @click="prevImage"
              v-show="activeImage > 0"
              class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center bg-slate-900/70 hover:bg-slate-800 backdrop-blur-sm rounded-full text-white shadow-lg transition border border-slate-700/50 z-10"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button
              @click="nextImage"
              v-show="activeImage < product.images.length - 1"
              class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center bg-slate-900/70 hover:bg-slate-800 backdrop-blur-sm rounded-full text-white shadow-lg transition border border-slate-700/50 z-10"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Dots indicadores (pill activo, círculo inactivo) -->
            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
              <button
                v-for="(_, i) in product.images"
                :key="i"
                @click="activeImage = i"
                :class="[
                  'rounded-full transition-all duration-300',
                  i === activeImage
                    ? 'w-5 h-2 bg-white'
                    : 'w-2 h-2 bg-white/40 hover:bg-white/60'
                ]"
              />
            </div>

            <!-- Contador de imagen -->
            <div class="absolute top-3 right-3 px-2 py-0.5 bg-slate-900/70 backdrop-blur-sm rounded-full text-slate-300 text-xs font-semibold z-10">
              {{ activeImage + 1 }}/{{ product.images.length }}
            </div>
          </template>
        </template>

        <!-- Sin imagen -->
        <div v-else class="w-full h-full flex items-center justify-center">
          <svg class="w-20 h-20 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>

        <!-- Badge oferta -->
        <div v-if="product.sale_price && product.show_price" class="absolute top-4 left-4 px-3 py-1 bg-rose-500 text-white text-xs font-bold rounded-full shadow-lg z-10">OFERTA</div>
      </div>

      <!-- ── CONTENIDO DETALLE ── -->
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
          <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-slate-500 font-mono">
            <span>SKU: {{ product.sku }}</span>
            <span v-if="product.views > 0" class="flex items-center gap-1 bg-slate-800/50 px-2 py-0.5 rounded-md">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              {{ product.views }} vistas
            </span>
            <span v-if="product.likes > 0" class="flex items-center gap-1 bg-rose-500/10 text-rose-400 px-2 py-0.5 rounded-md">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
              {{ product.likes }}
            </span>
          </div>

          <div class="flex items-baseline gap-3 mt-4">
            <template v-if="product.show_price">
              <span class="text-3xl font-black text-white">Bs. {{ Number(product.sale_price || product.price).toFixed(2) }}</span>
              <span v-if="product.sale_price" class="text-lg text-slate-500 line-through">Bs. {{ Number(product.price).toFixed(2) }}</span>
              <span v-if="product.sale_price" class="px-2 py-0.5 bg-rose-500/20 text-rose-400 text-xs font-bold rounded-full">
                {{ discountPercent }}% OFF
              </span>
            </template>
            <template v-else>
              <span class="px-3 py-1 bg-indigo-500/20 text-indigo-400 text-sm font-bold uppercase tracking-wider rounded-lg border border-indigo-500/30">Precio a cotizar</span>
            </template>
          </div>
        </div>

        <!-- Estado de stock + selector de cantidad -->
        <div class="flex items-center justify-between gap-4">
          <div>
            <template v-if="product.stock > 0">
              <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-950/50 border border-emerald-900/50 rounded-full">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"/>
                <span class="text-emerald-400 text-sm font-medium">
                  {{ product.show_stock ? `${product.stock} en stock` : 'Disponible' }}
                </span>
              </div>
            </template>
            <template v-else>
              <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-950/50 border border-rose-900/50 rounded-full">
                <span class="w-2 h-2 rounded-full bg-rose-400"/>
                <span class="text-rose-400 text-sm font-medium">Agotado</span>
              </div>
            </template>
          </div>

          <!-- Selector de cantidad (solo si hay stock) -->
          <div v-if="product.stock > 0" class="flex items-center gap-1 bg-slate-900 border border-slate-800 rounded-xl p-1">
            <button @click="quantity > 1 ? quantity-- : null" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition text-lg font-bold">−</button>
            <span class="text-sm font-bold w-8 text-center text-white">{{ quantity }}</span>
            <button @click="quantity < product.stock ? quantity++ : null" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition text-lg font-bold">+</button>
          </div>
        </div>

        <!-- Descripción -->
        <div v-if="product.description" class="bg-slate-900/50 border border-slate-800/60 rounded-2xl p-4">
          <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Descripción</h2>
          <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ product.description }}</p>
        </div>

        <!-- Especificaciones (atributos EAV del admin) -->
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

        <!-- Opciones interactivas del comprador (select / textarea) -->
        <div v-if="buyerAttributes && buyerAttributes.length > 0" class="space-y-4">
          <div v-for="attr in buyerAttributes" :key="attr.id">

            <!-- SELECT: chips de opciones -->
            <div v-if="attr.type === 'select'">
              <div class="flex items-center justify-between mb-2.5">
                <h2 class="text-sm font-bold text-white">{{ attr.name }}</h2>
                <span v-if="attr.is_required" class="text-xs text-rose-400 font-medium">Requerido</span>
              </div>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="option in (attr.options || [])" :key="option"
                  type="button"
                  @click="selectBuyerOption(attr.id, option)"
                  class="px-4 py-2 rounded-full text-sm font-semibold border-2 transition-all duration-150"
                  :class="buyerSelections[attr.id] === option
                    ? 'bg-indigo-600 border-indigo-500 text-white shadow-md'
                    : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-indigo-500 hover:text-white'"
                >{{ option }}</button>
              </div>
              <p v-if="buyerErrors[attr.id]" class="text-rose-400 text-xs mt-2">{{ buyerErrors[attr.id] }}</p>
            </div>

            <!-- TEXTAREA: campo libre -->
            <div v-else-if="attr.type === 'textarea'">
              <div class="flex items-center justify-between mb-2.5">
                <h2 class="text-sm font-bold text-white">{{ attr.name }}</h2>
                <span v-if="attr.is_required" class="text-xs text-rose-400 font-medium">Requerido</span>
              </div>
              <textarea
                v-model="buyerSelections[attr.id]"
                rows="3"
                :placeholder="`Escribe tu ${attr.name.toLowerCase()}...`"
                class="w-full bg-slate-900 border-2 border-slate-700 rounded-2xl p-3 text-white text-sm focus:border-indigo-500 focus:outline-none transition resize-none"
                :class="{'border-rose-500': buyerErrors[attr.id]}"
              ></textarea>
              <p v-if="buyerErrors[attr.id]" class="text-rose-400 text-xs mt-2">{{ buyerErrors[attr.id] }}</p>
            </div>
          </div>
        </div>

        <!-- Espaciado al fondo para que no tape la barra fija -->
        <div class="h-4"></div>
      </div>
    </main>

    <!-- ── BARRA DE ACCIONES FIJA ── -->
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800/60 p-4">
      <div class="max-w-3xl mx-auto flex gap-2">

        <!-- Me gusta -->
        <button
          @click="toggleLike"
          class="w-12 h-12 flex items-center justify-center rounded-2xl transition shrink-0 shadow-lg border relative"
          :class="isLiked ? 'bg-rose-500/20 border-rose-500/50 text-rose-500' : 'bg-slate-800 border-slate-700 hover:bg-slate-700 text-slate-300'"
          title="Me gusta"
        >
          <svg class="w-6 h-6 transition-transform" :class="{'scale-110': isLiked}" :fill="isLiked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        </button>

        <!-- Compartir -->
        <button
          @click="shareProduct"
          class="w-12 h-12 flex items-center justify-center bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-2xl transition shrink-0 shadow-lg border border-slate-700"
          title="Compartir"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
        </button>

        <!-- CON STOCK: carrito + WhatsApp directo -->
        <template v-if="product.stock > 0">
          <button
            @click="addToCartAndNotify"
            class="w-12 h-12 flex items-center justify-center bg-indigo-600/20 hover:bg-indigo-600/30 border border-indigo-500/30 text-indigo-400 rounded-2xl transition shrink-0 relative overflow-hidden shadow-lg"
            title="Añadir al carrito"
          >
            <span v-if="showAddedNotification" class="absolute inset-0 bg-emerald-500 flex items-center justify-center text-white font-bold text-lg z-10">✓</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </button>

          <button
            @click="buyOnWhatsApp"
            :disabled="isSubmitting"
            class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] disabled:opacity-50 disabled:pointer-events-none text-white font-extrabold rounded-2xl shadow-lg shadow-emerald-950/40 transition-all text-xs uppercase tracking-wider"
          >
            <svg v-if="isSubmitting" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            <span>{{ isSubmitting ? 'Procesando...' : 'Pedir por WhatsApp' }}</span>
          </button>
        </template>

        <!-- AGOTADO: consultar disponibilidad vía WhatsApp -->
        <template v-else>
          <button
            v-if="adminPhone"
            @click="consultWhatsApp"
            class="flex-1 flex items-center justify-center gap-2 py-3.5 bg-slate-700 hover:bg-slate-600 active:scale-[0.98] text-white font-bold rounded-2xl transition-all text-xs uppercase tracking-wider"
          >
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Consultar disponibilidad
          </button>
          <div v-else class="flex-1 flex items-center justify-center py-3.5 bg-slate-800/50 text-slate-500 font-bold rounded-2xl text-xs uppercase tracking-wider">
            Producto Agotado
          </div>
        </template>
      </div>
    </div>

  </div>

  <CartDrawer :adminPhone="adminPhone" />
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import CartDrawer from '../../../Components/Tenant/CartDrawer.vue'
import { useCart } from '../../../composables/useCart'

const props = defineProps({
  product: Object,
  adminPhone: String,
  buyerAttributes: { type: Array, default: () => [] },
})

const { addToCart, isCartOpen, totalItemsCount } = useCart()

// ── Lógica de Me Gusta (Likes) ────────────────────────────────────────────────
const isLiked = ref(false)

const loadLikeState = () => {
  try {
    const stored = localStorage.getItem('tenant_liked_products')
    if (stored) {
      const likedSet = new Set(JSON.parse(stored))
      isLiked.value = likedSet.has(props.product.id)
    }
  } catch (e) {
    console.error('Error loading likes', e)
  }
}

const toggleLike = async () => {
  const isCurrentlyLiked = isLiked.value
  const action = isCurrentlyLiked ? 'unlike' : 'like'
  
  // Optimistic update
  isLiked.value = !isCurrentlyLiked
  if (isCurrentlyLiked) {
    if (props.product.likes > 0) props.product.likes--
  } else {
    props.product.likes++
  }
  
  // Update localStorage
  try {
    const stored = localStorage.getItem('tenant_liked_products')
    let likedSet = stored ? new Set(JSON.parse(stored)) : new Set()
    if (isLiked.value) likedSet.add(props.product.id)
    else likedSet.delete(props.product.id)
    localStorage.setItem('tenant_liked_products', JSON.stringify([...likedSet]))
  } catch (e) {}
  
  try {
    const res = await axios.post(route('tenant.catalog.toggle-like', { id: props.product.id }), { action })
    props.product.likes = res.data.likes
  } catch (e) {
    console.error('Error toggling like:', e)
    // Rollback
    isLiked.value = isCurrentlyLiked
    if (isCurrentlyLiked) props.product.likes++
    else if (props.product.likes > 0) props.product.likes--
  }
}

onMounted(() => {
  loadLikeState()
})

// ── Selecciones del comprador ─────────────────────────────────────────────────
const buyerSelections = ref({})
const buyerErrors     = ref({})
const isSubmitting    = ref(false)

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

// ── Estado de galería ─────────────────────────────────────────────────────────
const activeImage        = ref(0)
const quantity           = ref(1)
const showAddedNotification = ref(false)

const nextImage = () => {
  if (props.product.images && activeImage.value < props.product.images.length - 1) {
    activeImage.value++
  }
}
const prevImage = () => {
  if (activeImage.value > 0) activeImage.value--
}

// ── Swipe táctil ──────────────────────────────────────────────────────────────
const touchStartX = ref(0)
const touchStartY = ref(0)

const handleTouchStart = (e) => {
  touchStartX.value = e.touches[0].clientX
  touchStartY.value = e.touches[0].clientY
}

const handleTouchEnd = (e) => {
  if (!props.product.images || props.product.images.length <= 1) return
  const deltaX = touchStartX.value - e.changedTouches[0].clientX
  const deltaY = Math.abs(touchStartY.value - e.changedTouches[0].clientY)
  // Ignorar si el movimiento es más vertical que horizontal o muy corto
  if (Math.abs(deltaX) < 40 || deltaY > Math.abs(deltaX)) return
  if (deltaX > 0) nextImage()
  else prevImage()
}

// ── Carrito ────────────────────────────────────────────────────────────────────
const { addToCart, isCartOpen, totalItemsCount } = useCart()

const addToCartAndNotify = () => {
  if (!validateBuyerSelections()) return

  const buyerChoicesFormatted = {}
  props.buyerAttributes.forEach(attr => {
    if (buyerSelections.value[attr.id]) {
      buyerChoicesFormatted[attr.name] = buyerSelections.value[attr.id]
    }
  })

  addToCart(props.product, quantity.value, buyerChoicesFormatted)
  showAddedNotification.value = true
  setTimeout(() => { showAddedNotification.value = false }, 1500)
}

// ── WhatsApp: pedir producto ──────────────────────────────────────────────────
const buyOnWhatsApp = async () => {
  if (!validateBuyerSelections()) return
  isSubmitting.value = true

  const buyerChoicesFormatted = {}
  props.buyerAttributes.forEach(attr => {
    if (buyerSelections.value[attr.id]) {
      buyerChoicesFormatted[attr.name] = buyerSelections.value[attr.id]
    }
  })

  try {
    const response = await axios.post(route('tenant.whatsapp-leads.store-public'), {
      product_id:    props.product.id,
      quantity:      quantity.value,
      buyer_choices: buyerChoicesFormatted,
    })
    if (response.data?.whatsapp_url) {
      window.location.href = response.data.whatsapp_url
    }
  } catch (err) {
    console.error('Error al iniciar compra por WhatsApp:', err)
    alert(err.response?.data?.error || 'Ocurrió un error. Por favor intenta de nuevo.')
  } finally {
    isSubmitting.value = false
  }
}

// ── WhatsApp: consultar disponibilidad (agotado) ──────────────────────────────
const consultWhatsApp = () => {
  if (!props.adminPhone) return
  const phone = props.adminPhone.replace(/\D/g, '')
  const msg = encodeURIComponent(
    `Hola! Me interesa el producto *${props.product.name}* (SKU: ${props.product.sku}) pero aparece agotado. ¿Cuándo estará disponible?`
  )
  window.open(`https://wa.me/${phone}?text=${msg}`, '_blank')
}

// ── Compartir ─────────────────────────────────────────────────────────────────
const shareProduct = async () => {
  const shareData = {
    title: props.product.name,
    text: `Mira este producto: ${props.product.name}`,
    url: window.location.href,
  }
  if (navigator.share) {
    try { await navigator.share(shareData) } catch {}
  } else {
    await navigator.clipboard.writeText(window.location.href)
    alert('¡Enlace copiado al portapapeles!')
  }
}

const catalogUrl = computed(() => route('tenant.catalog.index'))

const discountPercent = computed(() => {
  if (!props.product.sale_price) return 0
  return Math.round(((props.product.price - props.product.sale_price) / props.product.price) * 100)
})
</script>

<style scoped>
/* Transición suave al cambiar imagen */
.img-fade-enter-active, .img-fade-leave-active { transition: opacity 0.2s ease; }
.img-fade-enter-from, .img-fade-leave-to { opacity: 0; }
</style>
