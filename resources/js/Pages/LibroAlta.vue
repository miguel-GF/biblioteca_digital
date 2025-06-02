<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed } from 'vue'

// Campos
const titulo = ref('')
const codigoBarras = ref('')
const anio = ref('')
const descripcion = ref('')
const autorId = ref('')
const editorialId = ref('')
const areaId = ref('')

// Flag para activar validaciones
const wasSubmitted = ref(false)

const props = defineProps({
  autores: Array,
  editoriales: Array,
  areas: Array,
})

// Validaciones reactivas
const errors = computed(() => {
  const e = {}

  if (!titulo.value) e.titulo = 'El título es obligatorio.'
  else if (titulo.value.length > 50) e.titulo = 'Máximo 50 caracteres.'

  if (!codigoBarras.value) e.codigoBarras = 'El código de barras es obligatorio.'
  else if (codigoBarras.value.length > 15) e.codigoBarras = 'Máximo 15 caracteres.'

  if (!anio.value) e.anio = 'El año es obligatorio.'
  else if (!/^\d{4}$/.test(anio.value)) e.anio = 'Debe tener 4 dígitos.'

  if (!autorId.value) e.autorId = 'Selecciona un autor.'
  if (!editorialId.value) e.editorialId = 'Selecciona una editorial.'
  if (!areaId.value) e.areaId = 'Selecciona un área.'

  return e
})

const hasErrors = computed(() => Object.keys(errors.value).length > 0)

function guardarLibro() {
  wasSubmitted.value = true

  if (hasErrors.value) {
    console.warn('Corrige los errores antes de enviar.')
    return
  }

  // Aquí harías Inertia.post() o llamada a la API
  console.log('Datos válidos:', {
    titulo: titulo.value,
    codigoBarras: codigoBarras.value,
    anio: anio.value,
    descripcion: descripcion.value,
    autorId: autorId.value,
    editorialId: editorialId.value,
    areaId: areaId.value
  })
}
</script>

<template>
  <AppLayout title="Alta de Libro">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Alta de Libro</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow rounded-xl space-y-6">

          <!-- Título -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Título</label>
            <input v-model="titulo" type="text" maxlength="50"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.titulo }" />
            <p v-if="wasSubmitted && errors.titulo" class="text-sm text-red-600 mt-1">{{ errors.titulo }}</p>
          </div>

          <!-- Código de barras -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Código de Barras</label>
            <input v-model="codigoBarras" type="text" maxlength="15"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.codigoBarras }" />
            <p v-if="wasSubmitted && errors.codigoBarras" class="text-sm text-red-600 mt-1">{{ errors.codigoBarras }}</p>
          </div>

          <!-- Año -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Año</label>
            <input v-model="anio" type="text" maxlength="4"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.anio }" />
            <p v-if="wasSubmitted && errors.anio" class="text-sm text-red-600 mt-1">{{ errors.anio }}</p>
          </div>

          <!-- Descripción -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea v-model="descripcion"
              class="mt-1 block w-full border rounded-md shadow-sm"></textarea>
          </div>

          <!-- Selects -->
          <!-- AUTORES -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Autor</label>
            <select v-model="autorId"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.autorId }">
              <option value="" disabled>Selecciona un autor</option>
              <option v-for="autor in autores" :key="autor.id" :value="autor.id">{{ autor.autor }}</option>
            </select>
            <p v-if="wasSubmitted && errors.autorId" class="text-sm text-red-600 mt-1">{{ errors.autorId }}</p>
          </div>

          <!-- EDITORIALES -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Editorial</label>
            <select v-model="editorialId"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.editorialId }">
              <option value="" disabled>Selecciona una editorial</option>
              <option v-for="editorial in editoriales" :key="editorial.id" :value="editorial.id">{{ editorial.editorial }}</option>
            </select>
            <p v-if="wasSubmitted && errors.editorialId" class="text-sm text-red-600 mt-1">{{ errors.editorialId }}</p>
          </div>

          <!-- AREAS -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Área</label>
            <select v-model="areaId"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && errors.areaId }">
              <option value="" disabled>Selecciona un área</option>
              <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area }}</option>
            </select>
            <p v-if="wasSubmitted && errors.areaId" class="text-sm text-red-600 mt-1">{{ errors.areaId }}</p>
          </div>

          <!-- Botón -->
          <div class="text-right">
            <button @click="guardarLibro"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            >
              Guardar Libro
            </button>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>
