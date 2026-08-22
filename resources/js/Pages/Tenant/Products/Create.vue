<template>
  <div class="min-h-screen bg-slate-950 pb-24">
    <!-- Navbar / Header -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
      <div class="px-4 h-16 flex items-center justify-between">
        <Link :href="route('tenant.products.index')" class="text-indigo-400 hover:text-indigo-300 transition flex items-center gap-1 p-2 -ml-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </Link>
        <h1 class="text-white font-bold text-lg">Nuevo Producto</h1>
        <div class="w-8"></div>
      </div>
    </header>

    <main class="p-4 max-w-lg mx-auto space-y-5">
      <!-- Mensajes de estado -->
      <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-2xl flex items-center gap-3 text-sm">
        <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <!-- Selector de modo -->
      <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-1.5 flex gap-1">
        <button
          type="button"
          @click="mode = 'single'"
          :class="[
            'flex-1 py-3 px-4 rounded-2xl text-sm font-semibold transition-all duration-200',
            mode === 'single'
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25'
              : 'text-slate-400 hover:text-slate-200'
          ]"
        >
          <span class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Un Producto
          </span>
        </button>
        <button
          type="button"
          @click="mode = 'bulk'"
          :class="[
            'flex-1 py-3 px-4 rounded-2xl text-sm font-semibold transition-all duration-200',
            mode === 'bulk'
              ? 'bg-violet-600 text-white shadow-lg shadow-violet-600/25'
              : 'text-slate-400 hover:text-slate-200'
          ]"
        >
          <span class="flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h7"/></svg>
            Creación Masiva
          </span>
        </button>
      </div>

      <!-- ═══════════════════════════════════════════════════════════ -->
      <!-- MODO: UN SOLO PRODUCTO                                      -->
      <!-- ═══════════════════════════════════════════════════════════ -->
      <form v-if="mode === 'single'" @submit.prevent="submitSingle" class="space-y-5">
        <div v-if="Object.keys(form.errors).length > 0" class="p-4 bg-rose-950/50 border border-rose-500/30 text-rose-300 rounded-2xl text-sm">
          <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
          <ul class="list-disc pl-5 space-y-1">
            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <!-- Imagen -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-3">
          <label class="block text-slate-300 text-sm font-semibold mb-1">Imagen Principal del Producto</label>
          <div
            @click="triggerFileInput(null)"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event, null)"
            :class="[
              'relative border-2 border-dashed rounded-2xl h-48 flex flex-col items-center justify-center cursor-pointer transition overflow-hidden group',
              dragOver ? 'border-indigo-500 bg-indigo-950/20' : 'border-slate-800 bg-slate-950 hover:border-slate-700',
              form.errors.image ? 'border-rose-500' : ''
            ]"
          >
            <input ref="fileInputSingle" type="file" @input="onFileSelectedSingle" accept="image/*" class="hidden">
            <div v-if="imagePreview" class="absolute inset-0">
              <img :src="imagePreview" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
              <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-3">
                <button type="button" @click.stop="triggerFileInput(null)" class="p-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-white shadow-lg transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
                <button type="button" @click.stop="removeImageSingle" class="p-2.5 bg-rose-600 hover:bg-rose-500 rounded-xl text-white shadow-lg transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
            <div v-else class="text-center p-4 space-y-2 flex flex-col items-center">
              <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center group-hover:scale-110 transition duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <p class="text-slate-200 text-sm font-semibold">Toca para subir o arrastra una imagen</p>
              <p class="text-slate-500 text-xs">JPG, PNG, WEBP · Máx. 2MB</p>
            </div>
          </div>
          <p v-if="form.errors.image" class="text-rose-400 text-xs">{{ form.errors.image }}</p>
        </div>

        <!-- Información básica -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider">Información Básica</h2>
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Nombre *</label>
            <input v-model="form.name" type="text" placeholder="Ej. Zapatillas Deportivas" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" :class="{'border-rose-500': form.errors.name}">
          </div>
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Categoría *</label>
            <button type="button" @click="openCategorySelector" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-left text-white focus:border-indigo-500 transition flex items-center justify-between" :class="{'border-rose-500': form.errors.category_id}">
              <span v-if="selectedCategoryName" class="flex items-center gap-2 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>{{ selectedCategoryName }}</span>
              <span v-else class="text-slate-500">Selecciona una categoría</span>
              <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4 4 4-4"/></svg>
            </button>
            <p v-if="form.errors.category_id" class="text-rose-400 text-xs mt-2">{{ form.errors.category_id }}</p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">SKU *</label>
              <input v-model="form.sku" @input="onSkuInput" type="text" placeholder="SKU-123" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" :class="{'border-rose-500': form.errors.sku}">
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Slug *</label>
              <input v-model="form.slug" @input="onSlugInput" type="text" placeholder="zapatillas" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" :class="{'border-rose-500': form.errors.slug}">
            </div>
          </div>
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Descripción</label>
            <textarea v-model="form.description" rows="3" placeholder="Detalles del producto..." class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"></textarea>
          </div>
        </div>

        <!-- Atributos dinámicos (modo single) -->
        <div v-if="loadingAttributes" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-8 flex flex-col items-center">
          <svg class="animate-spin w-8 h-8 text-indigo-500 mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-slate-400 text-sm">Cargando características...</p>
        </div>
        <div v-else-if="attributes.length > 0" class="bg-indigo-950/20 border border-indigo-900/50 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Características Específicas
          </h2>
          <div v-for="attr in attributes" :key="attr.id">
            <label class="block text-slate-300 text-sm font-semibold mb-2">{{ attr.name }}<span v-if="attr.is_required" class="text-rose-500 ml-1">*</span></label>
            <input v-if="attr.type === 'text'" v-model="form.attributes[attr.id]" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            <input v-else-if="attr.type === 'number'" v-model="form.attributes[attr.id]" type="number" step="any" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
            <label v-else-if="attr.type === 'boolean'" class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative"><input type="checkbox" v-model="form.attributes[attr.id]" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div></div>
              <span class="text-white text-sm font-medium">Sí / Activo</span>
            </label>
          </div>
        </div>

        <!-- Comercial (modo single) -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-indigo-400 text-xs font-bold uppercase tracking-wider">Comercial</h2>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Precio *</label>
              <div class="relative"><span class="absolute inset-y-0 left-4 flex items-center text-slate-500 font-bold text-xs pointer-events-none">Bs.</span><input v-model="form.price" type="number" step="0.01" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" :class="{'border-rose-500': form.errors.price}"></div>
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Oferta</label>
              <div class="relative"><span class="absolute inset-y-0 left-4 flex items-center text-slate-500 font-bold text-xs pointer-events-none">Bs.</span><input v-model="form.sale_price" type="number" step="0.01" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition"></div>
            </div>
          </div>
          <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
            <div class="relative"><input type="checkbox" v-model="form.show_price" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div></div>
            <div><span class="text-white text-sm font-medium block">Mostrar Precio en Catálogo</span><span class="text-slate-500 text-xs">Si se desactiva, los clientes deberán consultar el precio.</span></div>
          </label>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Stock *</label>
              <input v-model="form.stock" type="number" placeholder="10" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" :class="{'border-rose-500': form.errors.stock}">
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Estado</label>
              <select v-model="form.status" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white appearance-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>
          <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
            <div class="relative"><input type="checkbox" v-model="form.show_stock" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div></div>
            <div><span class="text-white text-sm font-medium block">Mostrar Stock en Catálogo</span><span class="text-slate-500 text-xs">Si se desactiva, solo se mostrará "Disponible" o "Agotado".</span></div>
          </label>
        </div>
      </form>

      <!-- ═══════════════════════════════════════════════════════════ -->
      <!-- MODO: CREACIÓN MASIVA                                       -->
      <!-- ═══════════════════════════════════════════════════════════ -->
      <form v-if="mode === 'bulk'" @submit.prevent="submitBulk" class="space-y-5">
        <div v-if="Object.keys(bulkForm.errors).length > 0" class="p-4 bg-rose-950/50 border border-rose-500/30 text-rose-300 rounded-2xl text-sm">
          <p class="font-bold mb-2">Por favor corrige los siguientes errores:</p>
          <ul class="list-disc pl-5 space-y-1">
            <li v-for="(error, key) in bulkForm.errors" :key="key">{{ error }}</li>
          </ul>
        </div>

        <!-- Banner informativo -->
        <div class="bg-violet-950/30 border border-violet-700/30 rounded-2xl p-4 flex gap-3 items-start">
          <svg class="w-5 h-5 text-violet-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-violet-300 text-xs leading-relaxed">Añade múltiples variantes (cada una con su imagen y nombre). El <strong>precio, descripción y categoría</strong> serán iguales para todas.</p>
        </div>

        <!-- Campos compartidos -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-5">
          <h2 class="text-violet-400 text-xs font-bold uppercase tracking-wider flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Campos Compartidos (todas las variantes)
          </h2>

          <!-- Categoría -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Categoría *</label>
            <button type="button" @click="openCategorySelector" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-left text-white transition flex items-center justify-between" :class="{'border-rose-500': bulkForm.errors.category_id}">
              <span v-if="selectedCategoryName" class="flex items-center gap-2 font-medium"><span class="w-2.5 h-2.5 rounded-full bg-violet-500 animate-pulse"></span>{{ selectedCategoryName }}</span>
              <span v-else class="text-slate-500">Selecciona una categoría</span>
              <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4 4 4-4"/></svg>
            </button>
          </div>

          <!-- Descripción -->
          <div>
            <label class="block text-slate-300 text-sm font-semibold mb-2">Descripción</label>
            <textarea v-model="bulkForm.description" rows="3" placeholder="Detalles del producto..." class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition"></textarea>
          </div>

          <!-- Precio / Oferta -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Precio *</label>
              <div class="relative"><span class="absolute inset-y-0 left-4 flex items-center text-slate-500 font-bold text-xs pointer-events-none">Bs.</span><input v-model="bulkForm.price" type="number" step="0.01" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition" :class="{'border-rose-500': bulkForm.errors.price}"></div>
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Oferta</label>
              <div class="relative"><span class="absolute inset-y-0 left-4 flex items-center text-slate-500 font-bold text-xs pointer-events-none">Bs.</span><input v-model="bulkForm.sale_price" type="number" step="0.01" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-2xl py-4 pr-4 pl-12 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition"></div>
            </div>
          </div>

          <!-- Stock / Estado -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Stock *</label>
              <input v-model="bulkForm.stock" type="number" placeholder="10" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Estado</label>
              <select v-model="bulkForm.status" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white appearance-none focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
                <option :value="true">Activo</option>
                <option :value="false">Inactivo</option>
              </select>
            </div>
          </div>

          <!-- Switches -->
          <div class="space-y-3">
            <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative"><input type="checkbox" v-model="bulkForm.show_price" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-500"></div></div>
              <div><span class="text-white text-sm font-medium block">Mostrar Precio</span><span class="text-slate-500 text-xs">Aplica a todas las variantes</span></div>
            </label>
            <label class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative"><input type="checkbox" v-model="bulkForm.show_stock" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-500"></div></div>
              <div><span class="text-white text-sm font-medium block">Mostrar Stock</span><span class="text-slate-500 text-xs">Aplica a todas las variantes</span></div>
            </label>
          </div>
        </div>

        <!-- Atributos dinámicos (modo bulk) -->
        <div v-if="loadingAttributes" class="bg-slate-900/60 border border-slate-800 rounded-3xl p-8 flex flex-col items-center">
          <svg class="animate-spin w-8 h-8 text-violet-500 mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
          <p class="text-slate-400 text-sm">Cargando características...</p>
        </div>
        <div v-else-if="attributes.length > 0" class="bg-violet-950/20 border border-violet-900/50 rounded-3xl p-5 space-y-5">
          <h2 class="text-violet-400 text-xs font-bold uppercase tracking-wider">Características Compartidas</h2>
          <div v-for="attr in attributes" :key="attr.id">
            <label class="block text-slate-300 text-sm font-semibold mb-2">{{ attr.name }}<span v-if="attr.is_required" class="text-rose-500 ml-1">*</span></label>
            <input v-if="attr.type === 'text'" v-model="bulkForm.attributes[attr.id]" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
            <input v-else-if="attr.type === 'number'" v-model="bulkForm.attributes[attr.id]" type="number" step="any" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
            <label v-else-if="attr.type === 'boolean'" class="flex items-center gap-4 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer">
              <div class="relative"><input type="checkbox" v-model="bulkForm.attributes[attr.id]" class="sr-only peer"><div class="w-11 h-6 bg-slate-800 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-300 peer-checked:after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-violet-500"></div></div>
              <span class="text-white text-sm font-medium">Sí / Activo</span>
            </label>
          </div>
        </div>

        <!-- ─── VARIANTES ─────────────────────────────────────────── -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-violet-600 text-white text-xs font-bold flex items-center justify-center">{{ variants.length }}</span>
              Variantes
            </h2>
            <button type="button" @click="addVariant" class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white text-sm font-semibold rounded-xl transition shadow-lg shadow-violet-600/20">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              Añadir
            </button>
          </div>

          <!-- Card de cada variante -->
          <div
            v-for="(variant, i) in variants"
            :key="variant._id"
            class="bg-slate-900/60 border border-slate-800 rounded-3xl p-5 space-y-4 relative"
          >
            <!-- Número y botón eliminar -->
            <div class="flex items-center justify-between mb-1">
              <span class="text-violet-400 text-xs font-bold uppercase tracking-wider">Variante {{ i + 1 }}</span>
              <button
                type="button"
                @click="removeVariant(i)"
                v-if="variants.length > 1"
                class="p-1.5 hover:bg-rose-500/10 rounded-xl text-slate-500 hover:text-rose-400 transition"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>

            <!-- Imagen de la variante -->
            <div
              @click="triggerVariantFileInput(i)"
              @dragover.prevent="variant.dragOver = true"
              @dragleave.prevent="variant.dragOver = false"
              @drop.prevent="handleVariantDrop($event, i)"
              :class="[
                'relative border-2 border-dashed rounded-2xl h-36 flex flex-col items-center justify-center cursor-pointer transition overflow-hidden group',
                variant.dragOver ? 'border-violet-500 bg-violet-950/20' : 'border-slate-800 bg-slate-950 hover:border-slate-700'
              ]"
            >
              <input :ref="el => variantFileInputs[i] = el" type="file" @input="onVariantFileSelected($event, i)" accept="image/*" class="hidden">
              <div v-if="variant.preview" class="absolute inset-0">
                <img :src="variant.preview" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                  <button type="button" @click.stop="triggerVariantFileInput(i)" class="p-2 bg-violet-600 hover:bg-violet-500 rounded-xl text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                  </button>
                  <button type="button" @click.stop="removeVariantImage(i)" class="p-2 bg-rose-600 hover:bg-rose-500 rounded-xl text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>
              <div v-else class="text-center flex flex-col items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 flex items-center justify-center group-hover:scale-110 transition">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-slate-400 text-xs">Subir imagen</span>
              </div>
            </div>

            <!-- Nombre de la variante -->
            <div>
              <label class="block text-slate-300 text-sm font-semibold mb-2">Nombre *</label>
              <input
                v-model="variant.name"
                @input="autoFillVariantSkuSlug(i)"
                type="text"
                :placeholder="`Ej. Color Rojo, Talla L...`"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-white focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition"
              >
            </div>

            <!-- SKU / Slug -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-300 text-sm font-semibold mb-2">SKU *</label>
                <input v-model="variant.sku" type="text" placeholder="SKU-001" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white text-sm focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
              </div>
              <div>
                <label class="block text-slate-300 text-sm font-semibold mb-2">Slug *</label>
                <input v-model="variant.slug" type="text" placeholder="color-rojo" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-white text-sm focus:border-violet-500 focus:ring-1 focus:ring-violet-500 transition">
              </div>
            </div>
          </div>

          <!-- Botón añadir variante (al final de lista) -->
          <button
            type="button"
            @click="addVariant"
            class="w-full border-2 border-dashed border-violet-800/50 hover:border-violet-600 rounded-2xl p-4 text-violet-400 hover:text-violet-300 text-sm font-medium transition flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Añadir otra variante
          </button>
        </div>
      </form>
    </main>

    <!-- ── Barra inferior sticky ── -->
    <div class="fixed bottom-0 left-0 right-0 p-4 bg-slate-950/80 backdrop-blur-xl border-t border-slate-800 z-50">
      <button
        type="button"
        @click="mode === 'single' ? submitSingle() : submitBulk()"
        :disabled="mode === 'single' ? form.processing : bulkForm.processing"
        :class="[
          'w-full max-w-lg mx-auto flex items-center justify-center gap-2 font-bold text-lg rounded-2xl p-4 transition shadow-lg disabled:opacity-50',
          mode === 'single'
            ? 'bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white shadow-indigo-600/25'
            : 'bg-violet-600 hover:bg-violet-500 active:bg-violet-700 text-white shadow-violet-600/25'
        ]"
      >
        <svg v-if="form.processing || bulkForm.processing" class="animate-spin w-6 h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
        <span v-if="mode === 'single'">{{ form.processing ? 'Guardando...' : 'Guardar Producto' }}</span>
        <span v-else>{{ bulkForm.processing ? 'Creando...' : `Crear ${variants.length} Producto(s)` }}</span>
      </button>
    </div>

    <!-- ── Modal Selector de Categoría ── -->
    <div v-if="showCategoryModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/80 backdrop-blur-sm" @click="closeCategorySelector">
      <div class="w-full sm:max-w-md bg-slate-900 border-t sm:border border-slate-800 rounded-t-3xl sm:rounded-3xl p-6 space-y-4 max-h-[85vh] flex flex-col shadow-2xl" @click.stop>
        <div class="w-12 h-1.5 bg-slate-800 rounded-full mx-auto sm:hidden mb-2"></div>
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-white font-bold text-lg">Selecciona una Categoría</h3>
          <button @click="closeCategorySelector" class="p-1.5 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="relative">
          <input v-model="searchQuery" type="text" placeholder="Buscar categoría..." class="w-full bg-slate-950 border border-slate-800 rounded-xl py-3 pl-10 pr-4 text-white text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></div>
        </div>
        <div class="overflow-y-auto flex-1 space-y-2 pr-1">
          <button v-for="category in filteredCategories" :key="category.id" type="button" @click="selectCategory(category)" class="w-full flex items-center justify-between p-4 rounded-2xl border transition text-left" :class="[activeCategoryId === category.id ? 'bg-indigo-500/10 border-indigo-500/50 text-indigo-400 font-semibold' : 'bg-slate-950/60 border-slate-800/80 hover:border-slate-700 text-slate-300']">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm" :class="[activeCategoryId === category.id ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-900 text-slate-400']">{{ category.name.substring(0, 2).toUpperCase() }}</div>
              <span>{{ category.name }}</span>
            </div>
            <svg v-if="activeCategoryId === category.id" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </button>
          <div v-if="filteredCategories.length === 0" class="text-center py-8 text-slate-500">No se encontraron categorías</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, reactive } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  categories: { type: Array, required: true }
})

// ── Modo de creación ──────────────────────────────────────────────────────────
const mode = ref('single') // 'single' | 'bulk'

// ── Estado compartido ─────────────────────────────────────────────────────────
const attributes     = ref([])
const loadingAttributes = ref(false)
const showCategoryModal = ref(false)
const searchQuery    = ref('')
const selectedCategoryName = ref('')

// category_id activa (compartida entre los dos modos)
const activeCategoryId = ref('')

// ── MODO SINGLE ───────────────────────────────────────────────────────────────
const form = useForm({
  category_id: '',
  sku: '', name: '', slug: '', description: '',
  price: '', sale_price: '', show_price: true,
  stock: 0, show_stock: true, status: true,
  image: null,
  attributes: {}
})

const imagePreview   = ref(null)
const dragOver       = ref(false)
const fileInputSingle = ref(null)
const skuManuallyEdited  = ref(false)
const slugManuallyEdited = ref(false)

const triggerFileInput = (dummy) => fileInputSingle.value?.click()

const processFile = (file) => {
  if (!file) return
  form.image = file
  const reader = new FileReader()
  reader.onload = e => { imagePreview.value = e.target.result }
  reader.readAsDataURL(file)
}
const onFileSelectedSingle = e => processFile(e.target.files[0])
const handleDrop = (e, dummy) => {
  dragOver.value = false
  const f = e.dataTransfer.files[0]
  if (f && f.type.startsWith('image/')) processFile(f)
}
const removeImageSingle = () => {
  form.image = null; imagePreview.value = null
  if (fileInputSingle.value) fileInputSingle.value.value = ''
}

const onSkuInput  = () => { skuManuallyEdited.value  = true }
const onSlugInput = () => { slugManuallyEdited.value = true }

const generateSku = () => {
  if (skuManuallyEdited.value) return
  let catPart = 'CAT'; let namePart = 'PRO'
  if (form.category_id) {
    const cat = props.categories.find(c => c.id === form.category_id)
    if (cat?.name) catPart = cat.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-zA-Z0-9]/g,'').substring(0,3).toUpperCase()
  }
  if (form.name) namePart = form.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-zA-Z0-9]/g,'').substring(0,3).toUpperCase()
  if (!form.category_id && !form.name) { form.sku = ''; return }
  while (catPart.length < 3) catPart += 'X'
  while (namePart.length < 3) namePart += 'X'
  form.sku = `${catPart}-${namePart}-001`
}
const generateSlug = () => {
  if (slugManuallyEdited.value) return
  if (!form.name) { form.slug = ''; return }
  form.slug = form.name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-')
}

watch(() => form.name, () => { generateSku(); generateSlug() })
watch(() => form.category_id, () => { updateSelectedCategoryName(); generateSku(); fetchAttributes() })

const submitSingle = () => {
  form.post(route('tenant.products.store'), { preserveScroll: true })
}

// ── MODO BULK ─────────────────────────────────────────────────────────────────
const bulkForm = useForm({
  category_id: '',
  description: '', price: '', sale_price: '',
  show_price: true, stock: 0, show_stock: true, status: true,
  attributes: {}
})

let _variantId = 0
const newVariant = () => ({
  _id: ++_variantId,
  name: '', sku: '', slug: '',
  image: null, preview: null, dragOver: false
})
const variants = reactive([newVariant()])
const variantFileInputs = ref([])

const addVariant = () => variants.push(newVariant())
const removeVariant = (i) => variants.splice(i, 1)

const triggerVariantFileInput = (i) => variantFileInputs.value[i]?.click()

const onVariantFileSelected = (e, i) => {
  const file = e.target.files[0]
  if (!file) return
  variants[i].image = file
  const reader = new FileReader()
  reader.onload = ev => { variants[i].preview = ev.target.result }
  reader.readAsDataURL(file)
}
const handleVariantDrop = (e, i) => {
  variants[i].dragOver = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    variants[i].image = file
    const reader = new FileReader()
    reader.onload = ev => { variants[i].preview = ev.target.result }
    reader.readAsDataURL(file)
  }
}
const removeVariantImage = (i) => {
  variants[i].image = null; variants[i].preview = null
  if (variantFileInputs.value[i]) variantFileInputs.value[i].value = ''
}

const autoFillVariantSkuSlug = (i) => {
  const v = variants[i]
  if (!v.name) return
  // Slug
  v.slug = v.name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-')
  // SKU
  const cat = props.categories.find(c => c.id === activeCategoryId.value)
  const catPart = cat?.name ? cat.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-zA-Z0-9]/g,'').substring(0,3).toUpperCase().padEnd(3,'X') : 'CAT'
  const namePart = v.name.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-zA-Z0-9]/g,'').substring(0,3).toUpperCase().padEnd(3,'X')
  v.sku = `${catPart}-${namePart}-${String(i + 1).padStart(3,'0')}`
}

watch(() => bulkForm.category_id, () => { updateSelectedCategoryName(); fetchAttributes() })

const submitBulk = () => {
  // Construir FormData manualmente porque tenemos archivos en array
  const data = new FormData()
  data.append('category_id',  bulkForm.category_id)
  data.append('description',  bulkForm.description ?? '')
  data.append('price',        bulkForm.price)
  if (bulkForm.sale_price) data.append('sale_price', bulkForm.sale_price)
  data.append('show_price',   bulkForm.show_price  ? '1' : '0')
  data.append('stock',        bulkForm.stock)
  data.append('show_stock',   bulkForm.show_stock  ? '1' : '0')
  data.append('status',       bulkForm.status      ? '1' : '0')

  Object.entries(bulkForm.attributes).forEach(([k, v]) => {
    data.append(`attributes[${k}]`, v)
  })

  variants.forEach((v, i) => {
    data.append(`variants[${i}][name]`, v.name)
    data.append(`variants[${i}][sku]`,  v.sku)
    data.append(`variants[${i}][slug]`, v.slug)
    if (v.image) data.append(`variants[${i}][image]`, v.image)
  })

  bulkForm.transform(() => ({})) // reset transform
  router.post(route('tenant.products.bulk'), data, {
    onStart: () => { bulkForm.processing = true },
    onFinish: () => { bulkForm.processing = false },
    preserveScroll: true,
  })
}

// ── Categoría (compartida) ────────────────────────────────────────────────────
const openCategorySelector  = () => { showCategoryModal.value = true }
const closeCategorySelector = () => { showCategoryModal.value = false; searchQuery.value = '' }
const filteredCategories = computed(() => {
  if (!searchQuery.value) return props.categories
  return props.categories.filter(c => c.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
})

const selectCategory = (category) => {
  activeCategoryId.value = category.id
  selectedCategoryName.value = category.name
  // Aplicar al formulario activo
  if (mode.value === 'single') form.category_id = category.id
  else bulkForm.category_id = category.id
  closeCategorySelector()
}

const updateSelectedCategoryName = () => {
  const id = mode.value === 'single' ? form.category_id : bulkForm.category_id
  if (id) {
    const cat = props.categories.find(c => c.id === id)
    selectedCategoryName.value = cat ? cat.name : ''
    activeCategoryId.value = id
  } else {
    selectedCategoryName.value = ''
    activeCategoryId.value = ''
  }
}

// ── Atributos dinámicos ────────────────────────────────────────────────────────
const fetchAttributes = async () => {
  const catId = mode.value === 'single' ? form.category_id : bulkForm.category_id
  if (!catId) { attributes.value = []; return }
  loadingAttributes.value = true
  try {
    const res = await axios.get(route('tenant.api.categories.attributes', catId))
    attributes.value = res.data.attributes
    const defaults = {}
    attributes.value.forEach(a => { defaults[a.id] = a.type === 'boolean' ? false : '' })
    if (mode.value === 'single') form.attributes = defaults
    else bulkForm.attributes = defaults
  } catch (e) {
    console.error('Error cargando atributos:', e)
  } finally {
    loadingAttributes.value = false
  }
}

// Cuando cambia el modo, sincronizar categoría si ya se había elegido una
watch(mode, () => {
  if (activeCategoryId.value) {
    if (mode.value === 'single' && !form.category_id) {
      form.category_id = activeCategoryId.value
      fetchAttributes()
    } else if (mode.value === 'bulk' && !bulkForm.category_id) {
      bulkForm.category_id = activeCategoryId.value
      fetchAttributes()
    }
  }
  attributes.value = []
})
</script>
