<template>
  <div>
    <!-- Overlay oscuro -->
    <Transition name="fade">
      <div v-if="isCartOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm" @click="isCartOpen = false"></div>
    </Transition>

    <!-- Drawer lateral -->
    <Transition name="slide">
      <div v-if="isCartOpen" class="fixed inset-y-0 right-0 z-50 w-full md:w-96 bg-slate-900 border-l border-slate-800 shadow-2xl flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/90 backdrop-blur-md">
          <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Mi Carrito
          </h2>
          <button @click="isCartOpen = false" class="text-slate-400 hover:text-white transition p-2 bg-slate-800 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Lista de productos -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
          <div v-if="cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-center space-y-4">
            <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <p class="text-slate-400">Tu carrito está vacío.</p>
            <button @click="isCartOpen = false" class="px-6 py-2 bg-indigo-600/20 text-indigo-400 font-bold rounded-xl text-sm">Explorar Catálogo</button>
          </div>

          <div v-else class="space-y-4">
            <div v-for="item in cartItems" :key="item.product.id" class="bg-slate-950 border border-slate-800 rounded-2xl p-3 flex gap-3 relative">
              <!-- Botón Eliminar -->
              <button @click="removeFromCart(item.product.id)" class="absolute -top-2 -right-2 bg-slate-800 text-rose-400 hover:bg-rose-500 hover:text-white w-6 h-6 rounded-full flex items-center justify-center border border-slate-700 transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>

              <div class="w-16 h-16 bg-slate-800 rounded-xl overflow-hidden shrink-0">
                <img v-if="item.product.images && item.product.images.length > 0" :src="item.product.image_urls?.[0] || `/storage/${item.product.images[0]}`" class="w-full h-full object-cover"/>
                <svg v-else class="w-full h-full p-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>

              <div class="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                  <h4 class="text-sm font-bold text-white leading-tight line-clamp-2">{{ item.product.name }}</h4>
                  <p class="text-xs text-slate-500 font-mono mt-0.5">{{ item.product.sku }}</p>
                </div>
                
                <div class="flex items-center justify-between mt-2">
                  <div class="flex items-center gap-1 bg-slate-900 border border-slate-800 rounded-lg p-0.5">
                    <button @click="updateQuantity(item.product.id, item.quantity - 1)" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-md">-</button>
                    <span class="text-xs font-bold w-6 text-center text-white">{{ item.quantity }}</span>
                    <button @click="updateQuantity(item.product.id, item.quantity + 1)" :disabled="item.quantity >= item.product.stock" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-md disabled:opacity-50">+</button>
                  </div>
                  
                  <div class="text-right">
                    <p v-if="item.product.show_price" class="text-sm font-bold text-emerald-400">
                      ${{ (Number(item.product.sale_price || item.product.price) * item.quantity).toFixed(2) }}
                    </p>
                    <p v-else class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">A Cotizar</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer / Checkout -->
        <div v-if="cartItems.length > 0" class="p-6 border-t border-slate-800 bg-slate-900/90 backdrop-blur-md space-y-4">
          <div class="flex items-end justify-between">
            <span class="text-slate-400 text-sm font-semibold">Total estimado:</span>
            <div class="text-right">
              <span class="text-2xl font-black text-white">${{ totalAmountWithPrice.toFixed(2) }}</span>
              <p v-if="hasItemsWithoutPrice" class="text-[10px] text-indigo-400 font-bold uppercase mt-1 tracking-wider">+ Productos a cotizar</p>
            </div>
          </div>

          <a :href="whatsappUrl" target="_blank" rel="noopener" class="w-full flex items-center justify-center gap-2 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-900/30 transition text-sm">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Enviar pedido por WhatsApp
          </a>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useCart } from '../../composables/useCart'

const props = defineProps({
  adminPhone: String
})

const { cartItems, totalItemsCount, removeFromCart, updateQuantity, generateWhatsAppMessage, isCartOpen } = useCart()

const totalAmountWithPrice = computed(() => {
  return cartItems.value
    .filter(item => item.product.show_price)
    .reduce((sum, item) => sum + (Number(item.product.sale_price || item.product.price) * item.quantity), 0)
})

const hasItemsWithoutPrice = computed(() => {
  return cartItems.value.some(item => !item.product.show_price)
})

const whatsappUrl = computed(() => {
  if (!props.adminPhone) return '#'
  const phone = props.adminPhone.replace(/\D/g, '')
  const msg = generateWhatsAppMessage()
  return `https://wa.me/${phone}?text=${msg}`
})
</script>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: transform 0.3s cubic-bezier(0.32,0.72,0,1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
