<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <header class="bg-primary text-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Biblioteca Digital</h1>
        <nav class="space-x-4">
          <a href="/home" class="hover:underline">Inicio</a>
          <a href="/libros" class="hover:underline">Libros</a>
          <a href="/login" class="hover:underline">Mi cuenta</a>
        </nav>
      </div>
    </header>

    <!-- BÚSQUEDA -->
    <section class="bg-white py-6 px-4 shadow">
      <div class="max-w-6xl mx-auto flex flex-col gap-4 md:flex-row md:items-center">

        <!-- Título -->
        <h3 class="text-2xl font-semibold text-gray-800 md:w-auto">Búsqueda</h3>

        <!-- Input de texto (ocupa más espacio) -->
        <input v-model="busqueda" @keyup.enter="buscar" type="text" placeholder="Buscar por Título / Autor / Editorial"
          class="flex-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" />

        <!-- Select de área -->
        <select v-model="areaBusquedaId" class="md:w-60 px-4 py-2 border rounded-md shadow-sm">
          <option value="">Todas las áreas</option>
          <option v-for="area in areas" :key="area.id" :value="area.id">
            {{ area.area }}
          </option>
        </select>

        <!-- Botón Buscar -->
        <button @click="buscar" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark shadow">
          Buscar
        </button>

        <!-- Botón Limpiar -->
        <button @click="limpiar" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 shadow">
          Limpiar
        </button>
      </div>
    </section>

    <!-- LIBROS AGRUPADOS POR ÁREA -->
    <section class="py-12 px-4">
      <div v-for="area in agrupados" :key="area.area" class="max-w-6xl mx-auto mb-12">
        <h3 class="text-2xl font-semibold text-neutral mb-6 border-b pb-2">{{ area.area || '--' }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="libro in area.libros" :key="libro.id"
            class="bg-white border rounded-xl ellipsis shadow hover:shadow-lg transition p-4 flex flex-col items-center cursor-pointer">
            <!-- Vista previa del PDF -->
            {{ `${libro.titulo}` }}
            <PdfPreview :src="libro.archivo_url" />
            <h4 class="text-center font-medium text-gray-800 text-lg">{{ libro.titulo || '--' }}</h4>
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="mt-auto bg-neutral text-white py-6 text-center">
      <p>© 2025 Biblioteca Digital - Todos los derechos reservados</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import PdfPreview from '@/Components/PdfPreview.vue'

const { agrupados, query, areas, areaId } = defineProps({
  agrupados: Array,
  query: String,
  areas: Array,
  areaId: String,
})

const busqueda = ref('')
const areaBusquedaId = ref('')

function buscar() {
  router.get('/libros', {
    busqueda: busqueda.value ? busqueda.value.trim() : '',
    areaId: areaBusquedaId.value ?? '',
  })
}

onMounted(() => {
  areaBusquedaId.value = areaId ?? '';
  busqueda.value = query ?? '';
})

function limpiar() {
  busqueda.value = ''
  areaBusquedaId.value = ''
  buscar()
}

</script>

<style scoped>
.bg-primary {
  @apply bg-blue-600;
}

.bg-primary-dark {
  @apply bg-blue-800;
}

.text-neutral {
  @apply text-gray-800;
}
</style>
