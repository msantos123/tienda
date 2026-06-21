<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4 h-16">
        <Link :href="route('tenant.settings.attributes.index')" class="text-slate-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </Link>
        <p class="text-white font-bold text-sm">Nuevo Atributo Dinámico</p>
      </div>
    </nav>
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-2">Nombre del Atributo <span class="text-rose-500">*</span></label>
              <input v-model="form.name" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500 transition" placeholder="Ej. Color, Talla, Procesador" required />
              <p v-if="form.errors.name" class="mt-2 text-sm text-rose-400">{{ form.errors.name }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-slate-300 mb-2">Tipo de Dato <span class="text-rose-500">*</span></label>
              <select v-model="form.type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500 transition appearance-none" required>
                <option value="text">Texto Corto (Text)</option>
                <option value="number">Número (Number)</option>
                <option value="select">Selección Múltiple (Select/Opciones)</option>
                <option value="date">Fecha (Date)</option>
                <option value="boolean">Casilla de verificación (Boolean)</option>
              </select>
              <p v-if="form.errors.type" class="mt-2 text-sm text-rose-400">{{ form.errors.type }}</p>
            </div>
          </div>

          <div v-if="form.type === 'select'" class="bg-slate-950/50 p-4 rounded-xl border border-slate-800">
            <label class="block text-sm font-medium text-slate-300 mb-2">Opciones Disponibles (Separadas por coma)</label>
            <input v-model="optionsString" type="text" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-fuchsia-500 focus:ring-1 focus:ring-fuchsia-500 transition" placeholder="Rojo, Verde, Azul" />
            <p class="text-xs text-slate-500 mt-2">Solo aplica si el tipo de dato es "Selección Múltiple".</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Categorías Asociadas</label>
            <p class="text-xs text-slate-400 mb-3">Si no seleccionas ninguna categoría, este atributo estará disponible para <b>todas</b> las categorías globales.</p>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              <label v-for="category in categories" :key="category.id" class="flex items-center gap-3 p-3 rounded-xl border border-slate-800 bg-slate-900 hover:bg-slate-800/80 cursor-pointer transition">
                <input type="checkbox" :value="category.id" v-model="form.category_ids" class="w-4 h-4 rounded border-slate-700 bg-slate-950 text-fuchsia-500 focus:ring-fuchsia-500 focus:ring-offset-slate-900" />
                <span class="text-sm font-medium text-slate-300">{{ category.name }}</span>
              </label>
            </div>
            <p v-if="form.errors.category_ids" class="mt-2 text-sm text-rose-400">{{ form.errors.category_ids }}</p>
          </div>

          <div class="pt-4 border-t border-slate-800/80 flex justify-end">
            <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 px-6 py-2.5 bg-fuchsia-600 hover:bg-fuchsia-500 text-white font-semibold rounded-xl shadow-lg shadow-fuchsia-600/20 transition disabled:opacity-50">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Guardar Atributo
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  categories: Array
})

const optionsString = ref('')

const form = useForm({
  name: '',
  type: 'text',
  options: [],
  category_ids: []
})

watch(optionsString, (val) => {
  if (val) {
    form.options = val.split(',').map(s => s.trim()).filter(s => s)
  } else {
    form.options = []
  }
})

const submit = () => {
  form.post(route('tenant.settings.attributes.store'))
}
</script>
