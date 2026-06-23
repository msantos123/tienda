<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 pb-20">
    <!-- Header -->
    <nav class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-4">
          <Link :href="route('tenant.dashboard')" class="text-slate-400 hover:text-white transition p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          </Link>
          <p class="text-white font-bold text-sm leading-none flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            Mini-CRM WhatsApp
          </p>
        </div>
      </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-6">
      <!-- Buscador principal (prominente y cómodo en móvil) -->
      <div class="mb-6">
        <div class="relative rounded-2xl shadow-xl">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar REF-XXXX, cliente o teléfono..."
            @input="debouncedSearch"
            class="block w-full pl-11 pr-4 py-3.5 bg-slate-900 border border-slate-800 rounded-2xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium"
          />
          <button 
            v-if="searchQuery" 
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-white transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <p class="text-[11px] text-slate-500 mt-1.5 px-1 font-mono">Tip: Al buscar una referencia exacta (ej. REF-1234), su estado pasará automáticamente a "en conversación".</p>
      </div>

      <!-- Tabs horizontales deslizables por estado -->
      <div class="mb-6 -mx-4 px-4 overflow-x-auto whitespace-nowrap scrollbar-none flex gap-2">
        <button
          @click="selectStatus(null)"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold border transition-all duration-150 active:scale-95"
          :class="!selectedStatus 
            ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg shadow-indigo-900/30' 
            : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
        >
          <span>Todos</span>
          <span class="px-1.5 py-0.5 rounded-md text-[10px]" :class="!selectedStatus ? 'bg-indigo-700' : 'bg-slate-800'">
            {{ totalLeadsCount }}
          </span>
        </button>

        <button
          v-for="status in statusList"
          :key="status.key"
          @click="selectStatus(status.key)"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-xs font-bold border transition-all duration-150 active:scale-95"
          :class="selectedStatus === status.key 
            ? `${status.bgActive} ${status.borderActive} text-white shadow-lg` 
            : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
        >
          <span class="w-1.5 h-1.5 rounded-full" :class="status.dotColor"></span>
          <span>{{ status.label }}</span>
          <span class="px-1.5 py-0.5 rounded-md text-[10px]" :class="selectedStatus === status.key ? status.badgeActive : 'bg-slate-800'">
            {{ statusCounts[status.key] }}
          </span>
        </button>
      </div>

      <!-- Lista de Prospectos (Leads) -->
      <div v-if="leads.data.length === 0" class="py-16 text-center bg-slate-900/30 border border-slate-800/60 rounded-3xl">
        <div class="w-16 h-16 bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-800">
          <svg class="w-8 h-8 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-slate-400 font-medium">No se encontraron prospectos</p>
        <p class="text-xs text-slate-600 mt-1">Los intentos de compra en el catálogo aparecerán aquí.</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="lead in leads.data"
          :key="lead.id"
          class="bg-slate-900 border border-slate-800/80 rounded-2xl p-4 flex flex-col gap-4 relative hover:border-slate-700/80 transition-all shadow-md overflow-hidden group"
        >
          <!-- Fila superior: Referencia y Fecha -->
          <div class="flex items-center justify-between border-b border-slate-800/60 pb-3">
            <div class="flex items-center gap-2">
              <span class="font-mono text-sm font-black text-white bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800/60 tracking-wider">
                {{ lead.reference }}
              </span>
              <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full" :class="getStatusBadgeClass(lead.current_status)">
                {{ getStatusLabel(lead.current_status) }}
              </span>
            </div>
            <span class="text-xs text-slate-500 font-medium">
              {{ formatRelativeTime(lead.created_at) }}
            </span>
          </div>

          <!-- Fila de Producto -->
          <div class="flex gap-3">
            <div class="w-14 h-14 bg-slate-950 rounded-xl overflow-hidden shrink-0 border border-slate-800/60 flex items-center justify-center">
              <img 
                v-if="lead.product.images && lead.product.images.length > 0" 
                :src="lead.product.image_urls?.[0] || `/storage/${lead.product.images[0]}`" 
                class="w-full h-full object-cover"
              />
              <svg v-else class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <div class="flex-1 min-w-0 flex flex-col justify-between">
              <div>
                <h4 class="text-sm font-bold text-white leading-tight truncate">{{ lead.product.name }}</h4>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-xs text-slate-500 font-mono">{{ lead.product.sku }}</span>
                  <span class="text-xs text-indigo-400 font-bold">Cant: {{ lead.quantity }}</span>
                </div>
              </div>
              <div class="text-sm font-black text-emerald-400 mt-1">
                Total: ${{ Number(lead.total_amount).toFixed(2) }}
              </div>
            </div>
          </div>

          <!-- Opciones seleccionadas (Variantes) -->
          <div v-if="lead.buyer_choices && Object.keys(lead.buyer_choices).length > 0" class="flex flex-wrap gap-1.5 bg-slate-950/40 p-2 rounded-xl border border-slate-800/40">
            <span 
              v-for="(val, name) in lead.buyer_choices" 
              :key="name" 
              class="text-[10px] font-bold text-slate-400 bg-slate-900 border border-slate-800 px-2 py-0.5 rounded-lg"
            >
              {{ name }}: <span class="text-white">{{ val }}</span>
            </span>
          </div>

          <!-- Fila de Cliente (Nombre y WhatsApp) -->
          <div class="bg-slate-950/30 border border-slate-800/40 rounded-xl p-3 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Comprador</p>
              <div class="flex items-center gap-1.5 mt-0.5">
                <span class="text-sm font-bold text-white truncate max-w-[180px]">
                  {{ lead.customer_name || 'Sin nombre registrado' }}
                </span>
                <button @click="openEditModal(lead)" class="text-indigo-400 hover:text-indigo-300 p-0.5" title="Editar cliente">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>
              </div>
              <p class="text-xs text-slate-400 font-mono mt-0.5">
                {{ lead.phone_number || 'Sin teléfono registrado' }}
              </p>
            </div>
            
            <a 
              v-if="lead.phone_number" 
              :href="`https://wa.me/${cleanPhone(lead.phone_number)}`" 
              target="_blank" 
              rel="noopener"
              class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600/35 border border-emerald-500/30 text-emerald-400 rounded-xl text-xs font-bold transition active:scale-95 shrink-0"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Chat
            </a>
            <button
              v-else
              @click="openEditModal(lead)"
              class="inline-flex items-center gap-1.5 px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600/35 border border-indigo-500/30 text-indigo-400 rounded-xl text-xs font-bold transition active:scale-95 shrink-0"
            >
              Registrar Tlf
            </button>
          </div>

          <!-- Fila de Acciones de un Toque ("one-touch status transitions") -->
          <div class="flex items-center gap-2 pt-1 border-t border-slate-800/40">
            <!-- Botón principal para avanzar de estado de forma secuencial -->
            <button
              v-if="hasNextTransition(lead.current_status)"
              @click="advanceStatus(lead)"
              class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-extrabold text-white transition active:scale-95 shadow-md"
              :class="getNextTransitionClass(lead.current_status)"
            >
              <span>{{ getNextTransitionLabel(lead.current_status) }}</span>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <!-- Botón de archivar si no está archivado -->
            <button
              v-if="lead.current_status !== 'archivado' && lead.current_status !== 'entregado'"
              @click="changeStatus(lead.id, 'archivado')"
              class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition active:scale-95"
              title="Archivar"
            >
              Archivar
            </button>

            <!-- Botón de eliminar -->
            <button
              @click="deleteLead(lead.id)"
              class="p-2.5 bg-rose-950/20 hover:bg-rose-950/50 border border-rose-900/40 text-rose-400 hover:text-rose-300 rounded-xl text-xs font-bold transition active:scale-95"
              title="Eliminar"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Paginación Simple -->
      <div v-if="leads.links && leads.links.length > 3" class="mt-8 flex justify-center gap-1.5">
        <template v-for="(link, i) in leads.links" :key="i">
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            class="px-3.5 py-2 text-xs font-bold rounded-xl border transition-all active:scale-95"
            :class="link.active 
              ? 'bg-indigo-600 border-indigo-500 text-white shadow-lg' 
              : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white hover:border-slate-700'"
          />
          <span 
            v-else 
            v-html="link.label" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-900/20 border border-slate-900 rounded-xl select-none"
          />
        </template>
      </div>
    </main>

    <!-- Modal Rápido de Edición Premium -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/75 backdrop-blur-sm transition-opacity">
      <div @click="closeEditModal" class="absolute inset-0"></div>
      
      <div class="relative bg-slate-900 border-t sm:border border-slate-800 rounded-t-3xl sm:rounded-3xl w-full sm:max-w-md overflow-hidden shadow-2xl flex flex-col max-h-[90vh] transition-all animate-slide-up">
        <!-- Barra de arrastre visual para móvil -->
        <div class="w-12 h-1 bg-slate-700 rounded-full mx-auto my-3 sm:hidden"></div>

        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
          <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Editar Prospecto {{ editingLead?.reference }}
          </h3>
          <button @click="closeEditModal" class="p-1 text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-xl transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <form @submit.prevent="submitEdit" class="p-6 space-y-4 overflow-y-auto">
          <!-- Nombre Cliente -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nombre del Cliente</label>
            <input
              v-model="editForm.customer_name"
              type="text"
              placeholder="Ej. Juan Pérez"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
            />
          </div>

          <!-- Teléfono -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Número de WhatsApp (con código de país)</label>
            <input
              v-model="editForm.phone_number"
              type="text"
              placeholder="Ej. 5491122334455"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-medium"
            />
            <p class="text-[10px] text-slate-500 mt-1">Escribe solo números, incluyendo el código de país. Ej. 549... para Argentina o 521... para México.</p>
          </div>

          <!-- Monto Venta -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Monto de Venta ($)</label>
            <input
              v-model="editForm.total_amount"
              type="number"
              step="0.01"
              min="0"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono font-bold"
            />
          </div>

          <!-- Estado -->
          <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Estado del Embudo</label>
            <select
              v-model="editForm.current_status"
              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
            >
              <option value="nuevo">Nuevo</option>
              <option value="en_conversacion">En conversación</option>
              <option value="esperando_pago">Esperando Pago</option>
              <option value="pagado_pendiente">Pagado (Pendiente de envío)</option>
              <option value="entregado">Entregado con éxito</option>
              <option value="archivado">Archivado</option>
            </select>
          </div>

          <!-- Botones de Acción -->
          <div class="flex gap-3 pt-4 border-t border-slate-800/60">
            <button
              type="button"
              @click="closeEditModal"
              class="flex-1 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-sm transition active:scale-95"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="editForm.processing"
              class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold rounded-xl text-sm transition active:scale-95 shadow-lg shadow-indigo-950/40"
            >
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  leads: Object,
  filters: Object,
  statusCounts: Object,
})

// Estados Reactivos
const searchQuery = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || null)
const isEditModalOpen = ref(false)
const editingLead = ref(null)

// Lista de estados del embudo y sus estilos
const statusList = [
  { key: 'nuevo', label: 'Nuevos', dotColor: 'bg-blue-400', bgActive: 'bg-blue-600', borderActive: 'border-blue-500', badgeActive: 'bg-blue-700' },
  { key: 'en_conversacion', label: 'En conversación', dotColor: 'bg-yellow-400', bgActive: 'bg-yellow-600', borderActive: 'border-yellow-500', badgeActive: 'bg-yellow-700' },
  { key: 'esperando_pago', label: 'Esperando Pago', dotColor: 'bg-purple-400', bgActive: 'bg-purple-600', borderActive: 'border-purple-500', badgeActive: 'bg-purple-700' },
  { key: 'pagado_pendiente', label: 'Por Enviar', dotColor: 'bg-orange-400', bgActive: 'bg-orange-600', borderActive: 'border-orange-500', badgeActive: 'bg-orange-700' },
  { key: 'entregado', label: 'Entregados', dotColor: 'bg-emerald-400', bgActive: 'bg-emerald-600', borderActive: 'border-emerald-500', badgeActive: 'bg-emerald-700' },
  { key: 'archivado', label: 'Archivados', dotColor: 'bg-slate-400', bgActive: 'bg-slate-600', borderActive: 'border-slate-500', badgeActive: 'bg-slate-700' },
]

// Sumar todos los leads
const totalLeadsCount = computed(() => {
  return Object.values(props.statusCounts).reduce((sum, val) => sum + val, 0)
})

// Formulario reactivo para la edición rápida
const editForm = useForm({
  customer_name: '',
  phone_number: '',
  total_amount: 0,
  current_status: '',
})

// Buscador con debounce simple
let searchTimeout = null
const debouncedSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 400)
}

const clearSearch = () => {
  searchQuery.value = ''
  applyFilters()
}

const selectStatus = (statusKey) => {
  selectedStatus.value = statusKey
  applyFilters()
}

// Aplicar filtros e ir a la URL del tenant
const applyFilters = () => {
  router.get(route('tenant.whatsapp-leads.index'), {
    search: searchQuery.value || undefined,
    status: selectedStatus.value || undefined,
  }, {
    preserveState: true,
    replace: true,
  })
}

// Helpers visuales de estados
const getStatusLabel = (status) => {
  const found = statusList.find(s => s.key === status)
  return found ? found.label : status
}

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'nuevo': return 'bg-blue-500/10 text-blue-400 border border-blue-500/20'
    case 'en_conversacion': return 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20'
    case 'esperando_pago': return 'bg-purple-500/10 text-purple-400 border border-purple-500/20'
    case 'pagado_pendiente': return 'bg-orange-500/10 text-orange-400 border border-orange-500/20'
    case 'entregado': return 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
    case 'archivado': return 'bg-slate-500/10 text-slate-400 border border-slate-500/20'
    default: return 'bg-slate-500/10 text-slate-400 border border-slate-500/20'
  }
}

// Tiempos relativos bonitos
const formatRelativeTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffMin = Math.round(diffMs / 60000)
  const diffHrs = Math.round(diffMin / 60)
  const diffDays = Math.round(diffHrs / 24)

  if (diffMin < 1) return 'Hace un momento'
  if (diffMin < 60) return `Hace ${diffMin} min`
  if (diffHrs < 24) return `Hace ${diffHrs} ${diffHrs === 1 ? 'hora' : 'horas'}`
  return `Hace ${diffDays} ${diffDays === 1 ? 'día' : 'días'}`
}

const cleanPhone = (phone) => {
  return phone.replace(/\D/g, '')
}

// Controladores del Modal de Edición
const openEditModal = (lead) => {
  editingLead.value = lead
  editForm.customer_name = lead.customer_name || ''
  editForm.phone_number = lead.phone_number || ''
  editForm.total_amount = Number(lead.total_amount) || 0
  editForm.current_status = lead.current_status
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  editingLead.value = null
}

const submitEdit = () => {
  editForm.put(route('tenant.whatsapp-leads.update', editingLead.value.id), {
    onSuccess: () => {
      closeEditModal()
    }
  })
}

// Transiciones de Un Toque ("One-Touch")
const hasNextTransition = (status) => {
  return ['nuevo', 'en_conversacion', 'esperando_pago', 'pagado_pendiente'].includes(status)
}

const getNextTransitionLabel = (status) => {
  switch (status) {
    case 'nuevo': return 'Iniciar Conversación'
    case 'en_conversacion': return 'Solicitar Pago'
    case 'esperando_pago': return 'Confirmar Pago'
    case 'pagado_pendiente': return 'Marcar Entregado'
    default: return ''
  }
}

const getNextTransitionClass = (status) => {
  switch (status) {
    case 'nuevo': return 'bg-yellow-600 hover:bg-yellow-500 shadow-yellow-950/20'
    case 'en_conversacion': return 'bg-purple-600 hover:bg-purple-500 shadow-purple-950/20'
    case 'esperando_pago': return 'bg-orange-600 hover:bg-orange-500 shadow-orange-950/20'
    case 'pagado_pendiente': return 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-950/20'
    default: return 'bg-indigo-600'
  }
}

const getNextStatusKey = (status) => {
  switch (status) {
    case 'nuevo': return 'en_conversacion'
    case 'en_conversacion': return 'esperando_pago'
    case 'esperando_pago': return 'pagado_pendiente'
    case 'pagado_pendiente': return 'entregado'
    default: return status
  }
}

const advanceStatus = (lead) => {
  const nextStatus = getNextStatusKey(lead.current_status)
  changeStatus(lead.id, nextStatus)
}

const changeStatus = (leadId, statusKey) => {
  router.post(route('tenant.whatsapp-leads.update-status', leadId), {
    status: statusKey
  })
}

const deleteLead = (leadId) => {
  if (confirm('¿Estás seguro de que deseas eliminar este prospecto permanentemente?')) {
    router.delete(route('tenant.whatsapp-leads.destroy', leadId))
  }
}
</script>

<style scoped>
/* Eliminar scrollbar de la barra de pestañas */
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

@keyframes slideUp {
  from { transform: translateY(100%); }
  to { transform: translateY(0); }
}

.animate-slide-up {
  animation: slideUp 0.3s cubic-bezier(0.32, 0.72, 0, 1) forwards;
}
</style>
