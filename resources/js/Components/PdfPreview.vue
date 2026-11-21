<template>
  <div
    ref="container"
    class="w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl h-72 bg-gray-100 rounded-md flex items-center justify-center mx-auto relative">

    <!-- SKELETON / PLACEHOLDER mientras no es visible o mientras carga -->
    <div v-if="!previewSrc"
      class="absolute inset-0 p-4 flex flex-col justify-center gap-2 bg-gray-100 rounded-md z-10 overflow-hidden">
      <div class="absolute inset-0 flex items-center justify-center opacity-40">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
        </svg>
      </div>
      <template v-if="isVisible">
        <div class="h-4 bg-gray-300 rounded w-3/4 animate-pulse"></div>
        <div class="h-4 bg-gray-300 rounded w-full animate-pulse"></div>
        <div class="h-4 bg-gray-300 rounded w-5/6 animate-pulse"></div>
        <div class="h-4 bg-gray-300 rounded w-4/6 animate-pulse"></div>
      </template>
    </div>

    <!-- CANVAS (siempre presente, solo oculto mientras carga) -->
    <canvas ref="canvas" v-show="previewSrc" class="rounded-md max-w-full max-h-full object-contain z-0"></canvas>

  </div>
</template>


<script setup>
import { ref, watch, nextTick, onMounted, onBeforeUnmount } from 'vue'
import * as pdfjsLib from 'pdfjs-dist'
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.js?url'

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker

const props = defineProps({
  src: String // URL del PDF
})

const container = ref(null)
const canvas = ref(null)
const previewSrc = ref(false)
const isVisible = ref(false)

let observer = null
let loadingTask = null
let renderTask = null

onMounted(() => {
  // Si el navegador no soporta IntersectionObserver, cargamos normal.
  if (typeof IntersectionObserver === 'undefined') {
    isVisible.value = true
    return
  }

  observer = new IntersectionObserver((entries) => {
    if (entries.some((entry) => entry.isIntersecting)) {
      isVisible.value = true
      observer?.disconnect()
      observer = null
    }
  }, {
    // Empieza a cargar un poco antes de que la tarjeta sea visible.
    rootMargin: '400px 0px',
    threshold: 0.01,
  })

  if (container.value) {
    observer.observe(container.value)
  }
})

onBeforeUnmount(() => {
  observer?.disconnect()
  observer = null
  cancelarCarga()
})

function cancelarCarga() {
  try {
    renderTask?.cancel()
  } catch (e) { /* ignorar */ }
  try {
    loadingTask?.destroy()
  } catch (e) { /* ignorar */ }
  renderTask = null
  loadingTask = null
}

async function renderPDF() {
  if (!props.src || !isVisible.value) return

  try {
    cancelarCarga()
    previewSrc.value = false

    await nextTick()

    if (!canvas.value) {
      console.warn('Canvas element is not available. Retrying render PDF...')
      return
    }

    loadingTask = pdfjsLib.getDocument(props.src)
    const pdf = await loadingTask.promise
    const page = await pdf.getPage(1)

    // Obtenemos el viewport original
    const viewport = page.getViewport({ scale: 1 })

    // Definimos el tamaño máximo que queremos en el canvas (ajustado al contenedor)
    const maxWidth = canvas.value.parentElement.clientWidth
    const maxHeight = canvas.value.parentElement.clientHeight

    // Calculamos escala para que el PDF quepa sin desbordar (manteniendo proporción)
    const scale = Math.min(maxWidth / viewport.width, maxHeight / viewport.height)

    const scaledViewport = page.getViewport({ scale })

    const context = canvas.value.getContext('2d')

    if (!context) {
      console.error('Failed to get 2D canvas context.')
      return
    }

    canvas.value.height = scaledViewport.height
    canvas.value.width = scaledViewport.width

    const renderContext = {
      canvasContext: context,
      viewport: scaledViewport
    }

    renderTask = page.render(renderContext)
    await renderTask.promise
    previewSrc.value = true

    // Ya solo necesitamos la portada: liberamos el PDF y el worker.
    cancelarCarga()
  } catch (e) {
    // Los errores por cancelación/destrucción al salir de la pantalla son normales.
    if (e?.name !== 'RenderingCancelledException' && e?.name !== 'AbortException') {
      console.error('Error al generar la vista previa del PDF:', e)
    }
    previewSrc.value = false
  }
}

watch(
  [() => props.src, isVisible],
  ([src, visible]) => {
    if (src && visible) {
      renderPDF()
    } else if (!src) {
      previewSrc.value = false
    }
  },
  { immediate: true }
)
</script>
