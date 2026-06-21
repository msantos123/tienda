<template>
  <div class="min-h-screen bg-slate-950 pb-20">
    <!-- Navbar / Header Mobile -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.dashboard')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Nuevo Producto</h1>
        <div class="w-8"></div> <!-- Spacer for centering -->
      </div>
    </header>

    <main class="p-4 max-w-lg mx-auto">
      <!-- Mensajes de estado -->
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
        
        <!-- Bloque de Subida de Imagen al Inicio -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-3">
          <label class="block text-slate-300 text-sm font-semibold mb-1">Imagen Principal del Producto</label>
          
          <div 
            @click="triggerFileInput"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop"
            :class="[
              'relative border-2 border-dashed rounded-2xl h-48 flex flex-col items-center justify-center cursor-pointer transition overflow-hidden group',
              dragOver ? 'border-indigo-500 bg-indigo-950/20' : 'border-slate-800 bg-slate-950 hover:border-slate-700 hover:bg-slate-900/40',
              form.errors.image ? 'border-rose-500 bg-rose-950/5' : ''
            ]"
          >
            <input 
              ref="fileInput"
              type="file" 
              @input="onFileSelected"
              accept="image/*"
              class="hidden"
            >

            <!-- Vista previa de la imagen cargada -->
            <div v-if="imagePreview" class="absolute inset-0 w-full h-full">
              <img :src="imagePreview" class="w-full h-full object-cover transition duration-300 group-hover:scale-105" alt="Vista previa del producto">
              <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-3">
                <button 
                  type="button" 
                  @click.stop="triggerFileInput" 
                  class="p-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-white shadow-lg transition"
                  title="Cambiar imagen"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                </button>
                <button 
                  type="button" 
                  @click.stop="removeImage" 
                  class="p-2.5 bg-rose-600 hover:bg-rose-500 rounded-xl text-white shadow-lg transition"
                  title="Eliminar imagen"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Contenido cuando no hay imagen -->
            <div v-else class="text-center p-4 space-y-2 relative z-10 flex flex-col items-center">
              <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-1 group-hover:scale-110 transition duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <p class="text-slate-200 text-sm font-semibold">Toca para subir o arrastra una imagen</p>
                <p class="text-slate-500 text-xs mt-1">Formatos admitidos: JPG, PNG, WEBP (Máx. 2MB)</p>
              </div>
            </div>
          </div>
          <p v-if="form.errors.image" class="text-rose-400 text-xs mt-1">{{ form.errors.image }}</p>
        </div>

        <!-- Bloque Principal -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">Información Básica</h2>
          
          <!-- Nombre -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Nombre del producto *</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Ej. Zapatillas Deportivas" 
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
              :class="{'border-rose-500': form.errors.name}"
            >
          </div>

          <!-- Categoría (Amigable para Móvil) -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Categoría *</label>
            <button
              type="button"
              @click="openCategorySelector"
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-left text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition flex items-center justify-between"
              :class="{'border-rose-500': form.errors.category_id}"
            >
              <span v-if="selectedCategoryName" class="font-medium text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                {{ selectedCategoryName }}
              </span>
              <span v-else class="text-slate-500">Selecciona una categoría</span>
              <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4 4 4-4"/>
              </svg>
            </button>
            <p v-if="form.errors.category_id" class="text-rose-400 text-xs mt-2">{{ form.errors.category_id }}</p>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <!-- SKU -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">SKU *</label>
              <input 
                v-model="form.sku" 
                @input="onSkuInput"
                type="text" 
                placeholder="SKU-123" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.sku}"
              >
            </div>
            <!-- Slug -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Slug *</label>
              <input 
                v-model="form.slug" 
                @input="onSlugInput"
                type="text" 
                placeholder="zapatillas-deportivas" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.slug}"
              >
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="3"
              placeholder="Detalles del producto..."
              class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
            ></textarea>
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

            <!-- Render Type: select (Amigable para Móvil) -->
            <div v-else-if="attr.type === 'select'" class="relative">
              <button
                type="button"
                @click="openAttributeSelector(attr)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-left text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition flex items-center justify-between"
                :class="{'border-rose-500': form.errors[`attributes.${attr.id}`]}"
              >
                <span v-if="form.attributes[attr.id]" class="font-medium text-white flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                  {{ form.attributes[attr.id] }}
                </span>
                <span v-else class="text-slate-500">Selecciona una opción</span>
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4 4 4-4"/>
                </svg>
              </button>
            </div>

            <!-- Render Type: boolean -->
            <label v-else-if="attr.type === 'boolean'" class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative">
                <input type="checkbox" v-model="form.attributes[attr.id]" class="sr-only peer">
                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
              </div>
              <span class="text-white text-sm font-medium">Sí / Activo</span>
            </label>

            <!-- Error del atributo -->
            <p v-if="form.errors[`attributes.${attr.id}`]" class="text-rose-400 text-xs mt-2">{{ form.errors[`attributes.${attr.id}`] }}</p>
          </div>
        </div>

        <!-- Bloque Inventario y Precio -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider mb-2">Comercial</h2>
          
          <div class="grid grid-cols-2 gap-4">
            <!-- Precio -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Precio *</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <span class="text-slate-500 font-bold">$</span>
                </div>
                <input 
                  v-model="form.price" 
                  type="number" 
                  step="0.01"
                  placeholder="0.00" 
                  class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-8 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                  :class="{'border-rose-500': form.errors.price}"
                >
              </div>
            </div>
            
            <!-- Sale Precio -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Oferta</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                  <span class="text-slate-500 font-bold">$</span>
                </div>
                <input 
                  v-model="form.sale_price" 
                  type="number" 
                  step="0.01"
                  placeholder="0.00" 
                  class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-8 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
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
            <!-- Stock -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Stock *</label>
              <input 
                v-model="form.stock" 
                type="number" 
                placeholder="10" 
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white text-base focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
                :class="{'border-rose-500': form.errors.stock}"
              >
            </div>
            
            <!-- Estado -->
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
            <span>{{ form.processing ? 'Guardando...' : 'Guardar Producto' }}</span>
          </button>
        </div>
      </form>

      <!-- Modal / Bottom Sheet de Selección de Categoría -->
      <div v-if="showCategoryModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="closeCategorySelector">
        <div 
          class="w-full sm:max-w-md bg-slate-900 border-t sm:border border-slate-800 rounded-t-3xl sm:rounded-3xl p-6 space-y-4 max-h-[85vh] sm:max-h-[75vh] flex flex-col shadow-2xl"
          @click.stop
        >
          <!-- Indicador para arrastrar en móvil -->
          <div class="w-12 h-1.5 bg-slate-800 rounded-full mx-auto sm:hidden mb-2"></div>

          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-white font-bold text-lg">Selecciona una Categoría</h3>
            <button @click="closeCategorySelector" class="p-1.5 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Buscador de Categorías -->
          <div class="relative">
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Buscar categoría..."
              class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
            >
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <button v-if="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-white">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Lista de Categorías -->
          <div class="overflow-y-auto flex-1 space-y-2 pr-1 custom-scrollbar">
            <button
              v-for="category in filteredCategories"
              :key="category.id"
              type="button"
              @click="selectCategory(category)"
              class="w-full flex items-center justify-between p-4 rounded-2xl border transition text-left"
              :class="[
                form.category_id === category.id 
                  ? 'bg-indigo-500/10 border-indigo-500/50 text-indigo-400 font-semibold shadow-lg shadow-indigo-500/5' 
                  : 'bg-slate-950/60 border-slate-800/80 hover:border-slate-700 text-slate-300 hover:bg-slate-900/60'
              ]"
            >
              <div class="flex items-center gap-3">
                <div 
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                  :class="[
                    form.category_id === category.id 
                      ? 'bg-indigo-500/20 text-indigo-400' 
                      : 'bg-slate-900 text-slate-400'
                  ]"
                >
                  {{ category.name.substring(0, 2).toUpperCase() }}
                </div>
                <span>{{ category.name }}</span>
              </div>
              <svg v-if="form.category_id === category.id" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </button>

            <!-- Sin resultados -->
            <div v-if="filteredCategories.length === 0" class="text-center py-8 text-slate-500">
              <svg class="w-10 h-10 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              No se encontraron categorías
            </div>
          </div>
        </div>
      </div>

      <!-- Modal / Bottom Sheet de Selección de Opción de Atributo -->
      <div v-if="showAttributeModal && activeSelectAttribute" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="closeAttributeSelector">
        <div 
          class="w-full sm:max-w-md bg-slate-900 border-t sm:border border-slate-800 rounded-t-3xl sm:rounded-3xl p-6 space-y-4 max-h-[85vh] sm:max-h-[75vh] flex flex-col shadow-2xl"
          @click.stop
        >
          <!-- Indicador para arrastrar en móvil -->
          <div class="w-12 h-1.5 bg-slate-800 rounded-full mx-auto sm:hidden mb-2"></div>

          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-white font-bold text-lg">Selecciona {{ activeSelectAttribute.name }}</h3>
            <button @click="closeAttributeSelector" class="p-1.5 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Buscador de Opciones (solo si hay más de 5 opciones) -->
          <div v-if="activeSelectAttribute.options && activeSelectAttribute.options.length > 5" class="relative">
            <input 
              v-model="searchAttributeQuery" 
              type="text" 
              placeholder="Buscar opción..."
              class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"
            >
            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <button v-if="searchAttributeQuery" @click="searchAttributeQuery = ''" class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-white">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Lista de Opciones -->
          <div class="overflow-y-auto flex-1 space-y-2 pr-1 custom-scrollbar">
            <button
              v-for="option in filteredAttributeOptions"
              :key="option"
              type="button"
              @click="selectAttributeOption(option)"
              class="w-full flex items-center justify-between p-4 rounded-2xl border transition text-left"
              :class="[
                form.attributes[activeSelectAttribute.id] === option 
                  ? 'bg-emerald-500/10 border-emerald-500/50 text-emerald-400 font-semibold shadow-lg shadow-emerald-500/5' 
                  : 'bg-slate-950/60 border-slate-800/80 hover:border-slate-700 text-slate-300 hover:bg-slate-900/60'
              ]"
            >
              <div class="flex items-center gap-3">
                <div 
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-sm"
                  :class="[
                    form.attributes[activeSelectAttribute.id] === option 
                      ? 'bg-emerald-500/20 text-emerald-400' 
                      : 'bg-slate-900 text-slate-400'
                  ]"
                >
                  {{ option.substring(0, 2).toUpperCase() }}
                </div>
                <span>{{ option }}</span>
              </div>
              <svg v-if="form.attributes[activeSelectAttribute.id] === option" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </button>

            <!-- Sin resultados -->
            <div v-if="filteredAttributeOptions.length === 0" class="text-center py-8 text-slate-500">
              <svg class="w-10 h-10 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              No se encontraron opciones
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  categories: {
    type: Array,
    required: true
  }
})

const attributes = ref([])
const loadingAttributes = ref(false)

const form = useForm({
  category_id: '',
  sku: '',
  name: '',
  slug: '',
  description: '',
  price: '',
  sale_price: '',
  show_price: true,
  stock: 0,
  show_stock: true,
  status: true,
  image: null,
  attributes: {} // Almacenará los atributos dinámicos: { "1": "Valor", "2": true }
})

// --- LÓGICA DE SUBIDA DE IMAGEN ---
const imagePreview = ref(null)
const dragOver = ref(false)
const fileInput = ref(null)

const triggerFileInput = () => {
  fileInput.value.click()
}

const processFile = (file) => {
  if (!file) return
  form.image = file
  
  // Generar preview
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const onFileSelected = (e) => {
  const file = e.target.files[0]
  processFile(file)
}

const handleDrop = (e) => {
  dragOver.value = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    processFile(file)
  }
}

const removeImage = () => {
  form.image = null
  imagePreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// --- LÓGICA DEL SELECTOR DE CATEGORÍA INTERACTIVO ---
const showCategoryModal = ref(false)
const searchQuery = ref('')
const selectedCategoryName = ref('')

const openCategorySelector = () => {
  showCategoryModal.value = true
}

const closeCategorySelector = () => {
  showCategoryModal.value = false
  searchQuery.value = ''
}

const selectCategory = (category) => {
  form.category_id = category.id
  closeCategorySelector()
}

const filteredCategories = computed(() => {
  if (!searchQuery.value) return props.categories
  return props.categories.filter(cat => 
    cat.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const updateSelectedCategoryName = () => {
  if (form.category_id) {
    const cat = props.categories.find(c => c.id === form.category_id)
    selectedCategoryName.value = cat ? cat.name : ''
  } else {
    selectedCategoryName.value = ''
  }
}

// --- LÓGICA DEL SELECTOR DE OPCIONES DE ATRIBUTO INTERACTIVO ---
const activeSelectAttribute = ref(null)
const showAttributeModal = ref(false)
const searchAttributeQuery = ref('')

const openAttributeSelector = (attr) => {
  activeSelectAttribute.value = attr
  showAttributeModal.value = true
}

const closeAttributeSelector = () => {
  showAttributeModal.value = false
  activeSelectAttribute.value = null
  searchAttributeQuery.value = ''
}

const selectAttributeOption = (option) => {
  if (activeSelectAttribute.value) {
    form.attributes[activeSelectAttribute.value.id] = option
  }
  closeAttributeSelector()
}

const filteredAttributeOptions = computed(() => {
  if (!activeSelectAttribute.value) return []
  const options = activeSelectAttribute.value.options || []
  if (!searchAttributeQuery.value) return options
  return options.filter(opt => 
    opt.toLowerCase().includes(searchAttributeQuery.value.toLowerCase())
  )
})

// --- LÓGICA DE SKU Y SLUG AUTOGENERADOS ---
const skuManuallyEdited = ref(false)
const slugManuallyEdited = ref(false)

const onSkuInput = () => {
  skuManuallyEdited.value = true
}

const onSlugInput = () => {
  slugManuallyEdited.value = true
}

const generateSku = () => {
  if (skuManuallyEdited.value) return

  let catPart = 'CAT'
  if (form.category_id) {
    const cat = props.categories.find(c => c.id === form.category_id)
    if (cat && cat.name) {
      catPart = cat.name
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "") // quitar tildes
        .replace(/[^a-zA-Z0-9]/g, "") // quitar no alfanuméricos
        .substring(0, 3)
        .toUpperCase()
    }
  }

  let namePart = 'PRO'
  if (form.name) {
    namePart = form.name
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-zA-Z0-9]/g, "")
      .substring(0, 3)
      .toUpperCase()
  }

  if (!form.category_id && !form.name) {
    form.sku = ''
    return
  }

  while (catPart.length < 3) catPart += 'X'
  while (namePart.length < 3) namePart += 'X'

  form.sku = `${catPart}-${namePart}-001`
}

const generateSlug = () => {
  if (slugManuallyEdited.value) return
  if (!form.name) {
    form.slug = ''
    return
  }
  form.slug = form.name
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

// Fetch dynamic attributes when category changes
const fetchAttributes = async () => {
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
      newAttributesObj[attr.id] = attr.type === 'boolean' ? false : ''
    })
    
    form.attributes = newAttributesObj
    
  } catch (error) {
    console.error("Error cargando atributos:", error)
  } finally {
    loadingAttributes.value = false
  }
}

// Watchers para automatizar los procesos
watch(() => form.name, () => {
  generateSku()
  generateSlug()
})

watch(() => form.category_id, () => {
  updateSelectedCategoryName()
  generateSku()
  fetchAttributes()
})

const submit = () => {
  form.post(route('tenant.products.store'), {
    preserveScroll: true,
  })
}
</script>
