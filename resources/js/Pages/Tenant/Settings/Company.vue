<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 pb-20">
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-4">
          <Link :href="route('tenant.settings.index')" class="text-slate-400 hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          </Link>
          <p class="text-white font-bold text-sm leading-none flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Ajustes de Empresa
          </p>
        </div>
      </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8">
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-300 rounded-2xl flex items-center gap-3 text-sm">
        <svg class="w-6 h-6 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ $page.props.flash.success }}
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl">
        <h2 class="text-xl font-extrabold text-white mb-6">Información Pública de tu Empresa</h2>
        
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Logotipo -->
          <div>
            <label class="block text-sm font-bold text-slate-300 mb-3">Logotipo del Sistema</label>
            
            <div class="flex flex-col md:flex-row gap-6 items-start">
              <div 
                class="w-32 h-32 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center shrink-0 relative overflow-hidden transition-colors"
                :class="isDragging ? 'border-indigo-500 bg-indigo-500/10' : 'border-slate-700 bg-slate-950 hover:border-slate-500'"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
              >
                <!-- Vista previa -->
                <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover z-10" />
                <img v-else-if="$page.props.tenant?.logo" :src="$page.props.tenant.logo" class="w-full h-full object-cover z-10" />
                
                <div v-else class="text-slate-500 flex flex-col items-center z-0">
                  <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <span class="text-[10px] font-bold uppercase tracking-wider">Subir Logo</span>
                </div>
                
                <!-- Overlay en hover o drag -->
                <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 z-20 flex items-center justify-center transition-opacity cursor-pointer" @click="$refs.fileInput.click()">
                  <span class="text-xs font-bold text-white uppercase tracking-wider">Cambiar</span>
                </div>
              </div>
              
              <div class="flex-1">
                <p class="text-sm text-slate-400 mb-4">
                  El logotipo aparecerá en la cabecera de la tienda pública y en el panel administrativo. Recomendamos una imagen cuadrada en formato PNG con fondo transparente.
                </p>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="hidden"
                  @change="handleFileChange"
                />
                <button type="button" @click="$refs.fileInput.click()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm font-bold text-slate-300 rounded-xl transition">
                  Seleccionar Archivo
                </button>
                <div v-if="form.errors.company_logo" class="text-rose-400 text-xs mt-2 font-medium">
                  {{ form.errors.company_logo }}
                </div>
              </div>
            </div>
          </div>

          <hr class="border-slate-800/80">

          <!-- Nombre de Empresa -->
          <div>
            <label class="block text-sm font-bold text-slate-300 mb-2">Nombre Comercial</label>
            <input
              v-model="form.company_name"
              type="text"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-shadow"
              placeholder="Ej. Mi Tienda Increíble"
            />
            <p class="text-xs text-slate-500 mt-2">
              Este nombre es tu identidad principal frente a los clientes.
            </p>
            <div v-if="form.errors.company_name" class="text-rose-400 text-xs mt-2 font-medium">
              {{ form.errors.company_name }}
            </div>
          </div>

          <div class="pt-4 flex items-center justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-900/30 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>{{ form.processing ? 'Guardando...' : 'Guardar Ajustes' }}</span>
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

const page = usePage()

const form = useForm({
  company_name: page.props.tenant?.name || '',
  company_logo: null,
})

const fileInput = ref(null)
const previewUrl = ref(null)
const isDragging = ref(false)

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.company_logo = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    form.company_logo = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const submit = () => {
  // Inertia handles file uploads automatically if method is POST
  // We use post to the update route because HTML forms don't support PUT with files easily
  // Actually, Inertia supports files in POST. Let's make sure our route is POST.
  form.post(route('tenant.settings.company.update'), {
    preserveScroll: true,
    onSuccess: () => {
      // Clear file input to avoid re-uploading if not changed again
      form.company_logo = null
    }
  })
}
</script>
