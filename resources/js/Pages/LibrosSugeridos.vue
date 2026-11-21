<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <Header />

    <!-- TÍTULO -->
    <section class="bg-primary py-10 px-4">
      <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
          <p class="text-sm uppercase tracking-widest text-yellow-300 font-semibold">Selección de la biblioteca</p>
          <h1 class="text-3xl md:text-4xl font-bold text-white mt-2">Libros sugeridos</h1>
          <p class="text-white/80 mt-2">
            Ordenados por importancia y nombre. Total: {{ sugeridos.length }}
          </p>
        </div>
        <Link href="/home"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 text-white text-sm font-semibold hover:bg-white/20 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
              clip-rule="evenodd" />
          </svg>
          Volver al inicio
        </Link>
      </div>
    </section>

    <div class="flex-1 bg-fixed bg-cover bg-center" style="background-image: url('/images/bgbiblioteca.jpg')">
      <section class="py-12 px-4">
        <section v-if="sugeridos.length === 0" class="py-20 bg-white/70 text-center rounded-xl max-w-3xl mx-auto">
          <div class="max-w-xl mx-auto px-4">
            <img src="/images/emptybookshelf.png" alt="Sin libros" class="mx-auto w-40 h-40 object-contain mb-6 opacity-80" />
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Aún no hay libros sugeridos</h2>
            <p class="text-gray-600">Cuando se marquen libros como sugeridos aparecerán aquí.</p>
          </div>
        </section>

        <div v-else class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="(libro, index) in sugeridos" :key="libro.id"
            class="relative rounded-xl overflow-hidden p-4 flex flex-col bg-white/95 backdrop-blur-md transition duration-300 hover:ring-2 hover:ring-blue-300 shadow-md">
            <!-- Ranking -->
            <span
              class="absolute top-2 right-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white text-sm font-bold shadow">
              {{ index + 1 }}
            </span>

            <div class="flex-1">
              <div class="text-left line-clamp-2 text-lg font-bold text-gray-800 pr-6">
                {{ libro.titulo || '--' }}
              </div>
              <div class="text-left truncate text-gray-600 text-sm">
                {{ libro.autor || 'Sin autor' }}
              </div>
              <div class="text-left text-gray-600 text-sm">
                {{ libro.editorial || 'Sin editorial' }}
              </div>
              <div class="text-left text-gray-600 text-sm mb-3">
                {{ libro.anio || 'Sin año' }}
              </div>
              <PdfPreview :src="libro.archivo_url" />
              <h5 class="text-left font-medium text-gray-800 text-lg line-clamp-4 mt-2">
                {{ libro.descripcion || 'Sin descripción' }}
              </h5>
            </div>

            <div class="flex justify-end gap-3 mt-4 pt-3 border-t">
              <button @click.stop.prevent="visualizarLibro(libro)" title="Visualizar en línea"
                class="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>

              <button @click.stop.prevent="solicitarDescarga(libro.id, libro.archivo_nombre)" title="Descargar PDF"
                class="p-2 rounded-full text-primary hover:bg-blue-100 transition focus:outline-none focus:ring-2 focus:ring-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>

    <DescargarLibroModal ref="modalRef" />

    <!-- FOOTER -->
    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import PdfPreview from '@/Components/PdfPreview.vue';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import DescargarLibroModal from '@/Components/DescargarLibroModal.vue';

defineProps({
  sugeridos: {
    type: Array,
    default: () => [],
  },
});

const modalRef = ref(null);

function solicitarDescarga(libroId, libroArchivoNombre) {
  modalRef.value.descargarLibro(libroId, libroArchivoNombre);
}

function visualizarLibro(libro) {
  if (libro.archivo_url) {
    window.open(libro.archivo_url, '_blank');
  } else {
    alert('No se encontró la URL del archivo para visualizar.');
  }
}
</script>
