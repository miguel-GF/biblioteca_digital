<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3';

// Campos
const titulo = ref('')
const codigoBarras = ref('')
const anio = ref('')
const descripcion = ref('')
const autorId = ref('')
const editorialId = ref('')
const areaId = ref('')
const archivoPDF = ref(null)
const inputFileRef = ref(null)

// Flag para activar validaciones
const wasSubmitted = ref(false)

// Mensajes flash
const page = usePage()
const errorMessage = ref('')
const successMessage = ref('')

// Leer desde `page.props.flash`
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.error) {
      errorMessage.value = flash.error
      setTimeout(() => (errorMessage.value = ''), 3500)
    }
    if (flash?.success) {
      successMessage.value = flash.success
      setTimeout(() => (successMessage.value = ''), 3500)
    }
  },
  { immediate: true, deep: true }
)

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

const tituloError = computed(() => {
  if (!wasSubmitted.value) return ''
  if (!titulo.value) return 'El título es obligatorio.'
  if (titulo.value.length > 50) return 'Máximo 50 caracteres.'
  return ''
})

const areaError = computed(() => {
  if (!wasSubmitted.value) return ''
  if (!areaId.value) return 'Selecciona un área.'
  return ''
})

const archivoError = computed(() => {
  if (!wasSubmitted.value) return ''
  if (!archivoPDF.value) return 'Debes subir el libro en PDF.'
  if (archivoPDF.value.type !== 'application/pdf') return 'Debe estar en formato PDF.'
  return ''
})

const hasErrors = computed(() =>
  tituloError.value || areaError.value || archivoError.value
)

function handleFile(event) {
  archivoPDF.value = event.target.files[0]
}

function guardarLibro() {
  wasSubmitted.value = true

  if (hasErrors.value) {
    if (tituloError.value) {
      alert('El título es requerido');
      return;
    }
    if (areaError.value) {
      alert('El área es requerida');
      return;
    }
    if (archivoError.value) {
      alert('El archivo PDF es requerido');
      return;
    }
    return
  }

  // Validaciones archivo pdf
  if (!archivoPDF.value || archivoPDF.value == null) {
    alert('Debes subir el libro en PDF.');
    return;
  }

  if (archivoPDF.value.type !== 'application/pdf') {
    alert('Debe estar en formato PDF el libro.');
    return;
  }

  // Aquí harías Inertia.post() o llamada a la API
  console.log('Datos válidos:', {
    titulo: titulo.value,
    areaId: areaId.value,
    archivoPDF: archivoPDF.value,
    codigoBarras: codigoBarras.value ?? '',
    anio: anio.value ?? '',
    descripcion: descripcion.value ?? '',
    autorId: autorId.value ?? '',
    editorialId: editorialId.value ?? '',
  })

  const formData = new FormData()
  formData.append('titulo', titulo.value)
  formData.append('areaId', areaId.value)
  formData.append('archivoPDF', archivoPDF.value)
  formData.append('codigoBarras', codigoBarras.value ?? '')
  formData.append('anio', anio.value ?? '')
  formData.append('descripcion', descripcion.value ?? '')
  formData.append('autorId', autorId.value ?? '')
  formData.append('editorialId', editorialId.value ?? '')

  router.post(route('libro.guardar'), formData, {
    onSuccess: () => {
      // limpiar el formulario o redirigir si quieres
      titulo.value = ''
      codigoBarras.value = ''
      anio.value = ''
      descripcion.value = ''
      autorId.value = ''
      editorialId.value = ''
      areaId.value = ''
      archivoPDF.value = null
      wasSubmitted.value = false
      if (inputFileRef.value) {
        inputFileRef.value.value = ''
      }
    },
    onError: (err) => {
      alert(JSON.stringify(err))
    }
  })
}
</script>

<template>
  <AppLayout title="Alta de Libro">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Alta de Libro</h2>
        <button @click="guardarLibro"
                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-900">
          Guardar Libro
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Mensaje de éxito -->
        <p v-if="successMessage" class="text-green-600 bg-green-100 border border-green-400 p-2 rounded mb-4">
          {{ successMessage }}
        </p>

        <!-- Mensaje de error -->
        <p v-if="errorMessage" class="text-red-600 bg-red-100 border border-red-400 p-2 rounded mb-4">
          {{ errorMessage }}
        </p>
        <div class="bg-white p-6 shadow rounded-xl space-y-6">
          <!-- Título -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Título *</label>
            <input v-model="titulo" type="text" maxlength="50"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && tituloError }" />
            <p v-if="wasSubmitted && tituloError" class="text-sm text-red-600 mt-1">{{ tituloError }}</p>
          </div>

          <!-- AREAS -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Área *</label>
            <select v-model="areaId"
              class="mt-1 block w-full border rounded-md shadow-sm"
              :class="{ 'border-red-500': wasSubmitted && areaError }">
              <option value="" disabled>Selecciona un área</option>
              <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area }}</option>
            </select>
            <p v-if="wasSubmitted && areaError" class="text-sm text-red-600 mt-1">{{ areaError }}</p>
          </div>

          <!-- ARCHIVO PDF -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Archivo PDF *</label>
            <input ref="inputFileRef" type="file" @change="handleFile" accept=".pdf" class="w-full" />
            <p v-if="wasSubmitted && archivoError" class="text-sm text-red-600 mt-1">{{ archivoError }}</p>
          </div>

          <!-- OPCIONALES DE AQUI HACIA ABAJO -->

          <!-- Código de barras -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Código de Barras</label>
            <input v-model="codigoBarras" type="text" maxlength="15"
              class="mt-1 block w-full border rounded-md shadow-sm"
            />
            <!-- :class="{ 'border-red-500': wasSubmitted && errors.codigoBarras }"  -->
            <!-- <p v-if="wasSubmitted && errors.codigoBarras" class="text-sm text-red-600 mt-1">{{ errors.codigoBarras }}</p> -->
          </div>

          <!-- Año -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Año</label>
            <input v-model="anio" type="text" maxlength="4"
              class="mt-1 block w-full border rounded-md shadow-sm"
              />
            <!-- :class="{ 'border-red-500': wasSubmitted && errors.anio }" 
            <p v-if="wasSubmitted && errors.anio" class="text-sm text-red-600 mt-1">{{ errors.anio }}</p> -->
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
            >
            <!-- :class="{ 'border-red-500': wasSubmitted && errors.autorId }" -->
              <option value="" disabled>Selecciona un autor</option>
              <option v-for="autor in autores" :key="autor.id" :value="autor.id">{{ autor.autor }}</option>
            </select>
            <!-- <p v-if="wasSubmitted && errors.autorId" class="text-sm text-red-600 mt-1">{{ errors.autorId }}</p> -->
          </div>

          <!-- EDITORIALES -->
          <div>
            <label class="block text-sm font-medium text-gray-700">Editorial</label>
            <select v-model="editorialId"
              class="mt-1 block w-full border rounded-md shadow-sm"
            >
            <!-- :class="{ 'border-red-500': wasSubmitted && errors.editorialId }" -->
              <option value="" disabled>Selecciona una editorial</option>
              <option v-for="editorial in editoriales" :key="editorial.id" :value="editorial.id">{{ editorial.editorial }}</option>
            </select>
            <!-- <p v-if="wasSubmitted && errors.editorialId" class="text-sm text-red-600 mt-1">{{ errors.editorialId }}</p> -->
          </div>

          <!-- Botón -->
          <div class="text-right">
            <button @click="guardarLibro"
              class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-900"
            >
              Guardar Libro
            </button>
          </div>

        </div>
      </div>
    </div>
  </AppLayout>
</template>
