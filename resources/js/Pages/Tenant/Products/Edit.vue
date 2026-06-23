<template>
  <div class="min-h-screen bg-slate-950 pb-20">
    <!-- Navbar / Header Mobile -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.products.index')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Editar Producto</h1>
        <div class="w-8"></div>
      </div>
    </header>

    <main class="p-4 max-w-lg mx-auto">
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-2xl flex items-center gap-3 text-sm">
        <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>
      
      <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-rose-950/50 border border-rose-500/30 text-rose-300 rounded-2xl text-sm">
        <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
        <ul class="list-disc pl-5 space-y-1">
          <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
        </ul>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        
        <!-- Bloque Principal -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">Información Básica</h2>
          
          <!-- Nombre -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Nombre del producto *</label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
              :class="{'border-rose-500': form.errors.name}"
            >
          </div>

          <!-- Categoría -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Categoría *</label>
            <div class="relative">
              <select 
                v-model="form.category_id" 
                @change="fetchAttributes"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base appearance-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition pr-10"
                :class="{'border-rose-500': form.errors.category_id}"
              >
                <option value="" disabled>Selecciona una categoría</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">SKU *</label>
              <input 
                v-model="form.sku" 
                type="text" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.sku}"
              >
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Slug *</label>
              <input 
                v-model="form.slug" 
                type="text" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.slug}"
              >
            </div>
          </div>

          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
            ></textarea>
          </div>

          <!-- Imagen del Producto -->
          <div class="mt-4 border-t border-slate-800 pt-5">
            <label class="block text-slate-300 text-sm font-semibold mb-2">Imagen Principal (Max: 2MB)</label>
            
            <div v-if="product.images && product.images.length > 0 && !form.image" class="mb-4 relative w-32 h-32 rounded-xl overflow-hidden border border-slate-700">
              <img :src="product.image_urls?.[0] || `/storage/${product.images[0]}`" class="w-full h-full object-cover">
              <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 hover:opacity-100 transition">
                <span class="text-white text-xs font-bold">Imagen Actual</span>
              </div>
            </div>

            <div class="relative">
              <input 
                type="file" 
                @input="form.image = $event.target.files[0]"
                accept="image/*"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-slate-400 text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-500/20 file:text-indigo-400 hover:file:bg-indigo-500/30"
                :class="{'border-rose-500': form.errors.image}"
              >
            </div>
            <p v-if="form.errors.image" class="text-rose-400 text-xs mt-2">{{ form.errors.image }}</p>
          </div>
        </div>

        <!-- Bloque Atributos Dinámicos -->
        <div v-if="loadingAttributes" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-8 flex flex-col items-center justify-center">
          <svg class="animate-spin w-8 h-8 text-indigo-500 mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-slate-400 text-sm">Cargando características...</p>
        </div>
        
        <div v-else-if="attributes.length > 0" class="bg-indigo-950/20 border border-indigo-900/50 rounded-3xl p-5 space-y-5 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/10 blur-3xl rounded-full"></div>
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2 relative z-10 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Características Específicas
          </h2>
          
          <div v-for="attr in attributes" :key="attr.id" class="relative z-10">
            <label class="block text-slate-300 text-sm font-semibold mb-2">
              {{ attr.name }}
              <span v-if="attr.is_required" class="text-rose-500 ml-1 font-bold text-lg leading-none">*</span>
            </label>

            <!-- Render Type: text -->
            <input 
              v-if="attr.type === 'text'"
              v-model="form.attributes[attr.id]" 
              type="text" 
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
              :class="{'border-rose-500': form.errors[`attributes.${attr.id}`]}"
            >

            <!-- Render Type: number -->
            <input 
              v-else-if="attr.type === 'number'"
              v-model="form.attributes[attr.id]" 
              type="number" 
              step="any"
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
              :class="{'border-rose-500': form.errors[`attributes.${attr.id}`]}"
            >

            <!-- Render Type: select -->
            <div v-else-if="attr.type === 'select'" class="relative">
              <select 
                v-model="form.attributes[attr.id]" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base appearance-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition pr-10"
                :class="{'border-rose-500': form.errors[`attributes.${attr.id}`]}"
              >
                <option value="" disabled>Selecciona una opción</option>
                <option v-for="option in attr.options" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </div>
            </div>

            <!-- Render Type: boolean -->
            <label v-else-if="attr.type === 'boolean'" class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative">
                <input type="checkbox" v-model="form.attributes[attr.id]" class="sr-only peer">
                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
              </div>
              <span class="text-white text-sm font-medium">Sí / Activo</span>
            </label>

            <p v-if="form.errors[`attributes.${attr.id}`]" class="text-rose-400 text-xs mt-2">{{ form.errors[`attributes.${attr.id}`] }}</p>
          </div>
        </div>

        <!-- Bloque Inventario y Precio -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">Comercial</h2>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Precio *</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <span class="text-slate-500 font-bold text-xs">Bs.</span>
                </div>
                <input 
                  v-model="form.price" 
                  type="number" 
                  step="0.01"
                  class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                  :class="{'border-rose-500': form.errors.price}"
                >
              </div>
            </div>
            
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Oferta</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <span class="text-slate-500 font-bold text-xs">Bs.</span>
                </div>
                <input 
                  v-model="form.sale_price" 
                  type="number" 
                  step="0.01"
                  class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                >
              </div>
            </div>
          </div>

          <!-- Switch Mostrar Precio -->
          <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
            <div class="relative">
              <input type="checkbox" v-model="form.show_price" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
            </div>
            <div>
              <span class="text-white text-sm font-medium block">Mostrar Precio en Catálogo</span>
              <span class="text-slate-500 text-xs">Si se desactiva, los clientes deberán consultar el precio.</span>
            </div>
          </label>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Stock *</label>
              <input 
                v-model="form.stock" 
                type="number" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.stock}"
              >
            </div>
            
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Estado</label>
              <select 
                v-model="form.status" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base appearance-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
              >
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>

          <!-- Switch Mostrar Stock -->
          <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
            <div class="relative">
              <input type="checkbox" v-model="form.show_stock" class="sr-only peer">
              <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
            </div>
            <div>
              <span class="text-white text-sm font-medium block">Mostrar Stock en Catálogo</span>
              <span class="text-slate-500 text-xs">Si se desactiva, solo se mostrará "Disponible" o "Agotado".</span>
            </div>
          </label>
        </div>

        <!-- Sticky Bottom Bar -->
        <div class="fixed bottom-0 left-0 right-0 p-4 bg-slate-950/80 backdrop-blur-xl border-t border-slate-800 z-50">
          <button 
            type="submit" 
            :disabled="form.processing"
            class="w-full max-w-lg mx-auto flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-bold text-lg rounded-2xl p-4 transition shadow-lg shadow-indigo-600/25 disabled:opacity-50"
          >
            <svg v-if="form.processing" class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            <span>{{ form.processing ? 'Actualizando...' : 'Actualizar Producto' }}</span>
          </button>
        </div>
      </form>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  categories: Array,
  product: Object
})

const attributes = ref([])
const loadingAttributes = ref(false)

const form = useForm({
  category_id: props.product.category_id,
  sku: props.product.sku,
  name: props.product.name,
  slug: props.product.slug,
  description: props.product.description || '',
  price: props.product.price,
  sale_price: props.product.sale_price || '',
  show_price: props.product.show_price ?? true,
  stock: props.product.stock,
  show_stock: props.product.show_stock ?? true,
  status: props.product.status,
  image: null,
  attributes: {},
  _method: 'put' // Requerido por Laravel para enviar archivos en una actualización
})

onMounted(() => {
  if (form.category_id) {
    fetchAttributes(true) // true = es la primera carga, intentar rellenar valores
  }
})

const fetchAttributes = async (isInitialLoad = false) => {
  if (!form.category_id) {
    attributes.value = []
    form.attributes = {}
    return
  }

  loadingAttributes.value = true
  
  try {
    const response = await axios.get(route('tenant.api.categories.attributes', form.category_id))
    attributes.value = response.data.attributes
    
    const newAttributesObj = {}
    
    attributes.value.forEach(attr => {
      let existingValue = ''
      if (attr.type === 'boolean') existingValue = false
      
      // Si es carga inicial, buscar si el producto ya tenía este atributo guardado
      if (isInitialLoad && props.product.attribute_values) {
        const savedAttr = props.product.attribute_values.find(v => v.attribute_id === attr.id)
        if (savedAttr) {
          existingValue = attr.type === 'boolean' ? (savedAttr.value === 'true' || savedAttr.value === '1') : savedAttr.value
        }
      }
      
      newAttributesObj[attr.id] = existingValue
    })
    
    form.attributes = newAttributesObj
    
  } catch (error) {
    console.error("Error cargando atributos:", error)
  } finally {
    loadingAttributes.value = false
  }
}

const submit = () => {
  form.post(route('tenant.products.update', props.product.id), {
    preserveScroll: true,
  })
}
</script>
