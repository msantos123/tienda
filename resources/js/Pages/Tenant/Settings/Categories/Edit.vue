<template>
  <div class="min-h-screen bg-slate-950 text-slate-100">
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-4 h-16">
        <Link :href="route('tenant.settings.categories.index')" class="text-slate-400 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </Link>
        <p class="text-white font-bold text-sm">Editar Categoría</p>
      </div>
    </nav>
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre de la Categoría <span class="text-rose-500">*</span></label>
            <input v-model="form.name" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition" required />
            <p v-if="form.errors.name" class="mt-2 text-sm text-rose-400">{{ form.errors.name }}</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Slug</label>
            <input v-model="form.slug" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition font-mono text-sm" />
            <p v-if="form.errors.slug" class="mt-2 text-sm text-rose-400">{{ form.errors.slug }}</p>
          </div>

          <div class="pt-4 border-t border-slate-800/80 flex justify-end">
            <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition disabled:opacity-50">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Actualizar Categoría
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  category: Object
})

const form = useForm({
  name: props.category.name,
  slug: props.category.slug,
})

const submit = () => {
  form.put(route('tenant.settings.categories.update', props.category.id))
}
</script>
