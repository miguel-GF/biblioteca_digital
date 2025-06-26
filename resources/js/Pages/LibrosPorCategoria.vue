<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <header class="bg-primary text-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Biblioteca Digital</h1>
        <nav class="space-x-4">
          <a href="/home" class="hover:underline">Inicio</a>
          <a href="#" class="hover:underline">Áreas</a>
          <a href="/login" class="hover:underline">Mi cuenta</a>
        </nav>
      </div>
    </header>

    <!-- BÚSQUEDA -->
    <section class="bg-white py-6 px-4 shadow">
      <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-4">
        <h3 class="text-2xl font-semibold text-gray-800 flex-shrink-0">Resultados para: {{ query || '--' }}</h3>
        <input v-model="busqueda" @keyup.enter="buscar" type="text" placeholder="Buscar libros..."
          class="flex-grow px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" />
        <button @click="buscar"
          class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark shadow">Buscar</button>
      </div>
    </section>

    <!-- LIBROS AGRUPADOS POR ÁREA -->
    <section class="py-12 px-4">
      <div v-for="area in agrupados" :key="area.area" class="max-w-6xl mx-auto mb-12">
        <h3 class="text-2xl font-semibold text-neutral mb-6 border-b pb-2">{{ area.area || '--' }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="libro in area.libros" :key="libro.id"
            class="bg-white border rounded-xl shadow hover:shadow-lg transition p-4 flex flex-col items-center cursor-pointer">
             <!-- Vista previa del PDF -->
              {{ `/storage/${libro.archivo_ruta}` }}
            <PdfPreview :src="`/storage/${libro.archivo_ruta}`" />
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

const { agrupados, query } = defineProps({
  agrupados: Array,
  query: String
})

const busqueda = ref('')
const previewMap = ref({})

function buscar() {
  if (busqueda.value.trim()) {
    router.get('/libros', { busqueda: busqueda.value })
  }
}

onMounted(() => {
  agrupados.forEach(area => {
    area.libros.forEach(libro => {
      const url = `/storage/${libro.archivo_ruta}` // asegúrate de que sea accesible
      renderPreview(url).then(dataUrl => {
        previewMap.value[libro.id] = dataUrl
      })
    })
  })
})

async function renderPreview(pdfUrl) {
  try {
    const pdf = await pdfjsLib.getDocument(pdfUrl).promise
    const page = await pdf.getPage(1)
    const viewport = page.getViewport({ scale: 1.5 })

    const canvas = document.createElement('canvas')
    const context = canvas.getContext('2d')
    canvas.height = viewport.height
    canvas.width = viewport.width

    await page.render({ canvasContext: context, viewport }).promise
    return canvas.toDataURL()
  } catch (e) {
    console.error('Error cargando preview:', e)
    return null
  }
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
