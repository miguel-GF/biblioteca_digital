<template>
  <div
    class="w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl h-72 bg-gray-100 rounded-md flex items-center justify-center mx-auto relative">

    <!-- SKELETON overlay mientras carga -->
    <div v-if="!previewSrc"
      class="absolute inset-0 p-4 flex flex-col justify-center gap-2 bg-gray-100 animate-pulse z-10">
      <div class="h-4 bg-gray-300 rounded w-3/4"></div>
      <div class="h-4 bg-gray-300 rounded w-full"></div>
      <div class="h-4 bg-gray-300 rounded w-5/6"></div>
      <div class="h-4 bg-gray-300 rounded w-4/6"></div>
    </div>

    <!-- CANVAS (siempre presente, solo oculto mientras carga) -->
    <canvas ref="canvas" v-show="previewSrc" class="rounded-md max-w-full max-h-full object-contain z-0"></canvas>

  </div>
</template>


<script setup>
import { ref, watch, nextTick } from 'vue'
import * as pdfjsLib from 'pdfjs-dist'
import pdfjsWorker from 'pdfjs-dist/build/pdf.worker.min.js?url'

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker

const props = defineProps({
  src: String // URL del PDF
})

const canvas = ref(null)
const previewSrc = ref(false)

async function renderPDF() {
  try {
    previewSrc.value = false

    await nextTick()

    if (!canvas.value) {
      console.warn('Canvas element is not available. Retrying render PDF...')
      return
    }

    const loadingTask = pdfjsLib.getDocument(props.src)
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

    await page.render(renderContext).promise
    previewSrc.value = true
  } catch (e) {
    console.error('Error al generar la vista previa del PDF:', e)
    previewSrc.value = false
  }
}

watch(() => props.src, (newSrc) => {
  if (newSrc) {
    renderPDF()
  } else {
    previewSrc.value = false
  }
}, { immediate: true })
</script>
