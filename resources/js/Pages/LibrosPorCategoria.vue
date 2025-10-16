<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <Header />

    <!-- BÚSQUEDA -->
    <section class="bg-white py-6 px-4 shadow">
      <div class="max-w-6xl mx-auto flex flex-col gap-4 md:flex-row md:items-center">
        <!-- Título -->
        <h3 class="text-2xl font-semibold text-gray-800 md:w-auto">
          Búsqueda
        </h3>

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
        <button @click="buscar" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-900 shadow">
          Buscar
        </button>

        <!-- Botón Limpiar -->
        <button @click="limpiar" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 shadow">
          Limpiar
        </button>
      </div>
    </section>

    <!-- LIBROS AGRUPADOS POR ÁREA -->
    <div class="bg-fixed bg-cover bg-center" style="background-image: url('/images/bgbiblioteca.jpg')">
      <section class="py-12 px-4">
        <section v-if="agrupados.length === 0" class="py-20 bg-white/70 text-center">
          <div class="max-w-xl mx-auto px-4">
            <img src="/images/emptybookshelf.png" alt="Sin libros"
              class="mx-auto w-40 h-40 object-contain mb-6 opacity-80" />
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">
              No hay libros disponibles
            </h2>
            <p class="text-gray-600">
              Intenta ajustar tu búsqueda o filtrar por otra área.
            </p>
          </div>
        </section>
        <div v-else v-for="area in agrupados" :key="area.area" class="max-w-6xl mx-auto mb-12">
          <h3 class="text-2xl font-semibold text-white/90 mb-6 border-b pb-2">
            {{ area.area || "--" }}
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- <div @click="solicitarLibro(libro.id, libro.archivo_nombre)" v-for="libro in area.libros" :key="libro.id"
          class="relative rounded-xl overflow-hidden p-4 flex flex-col bg-white/90 backdrop-blur-md transition duration-300 transform hover:scale-105 hover:ring-2 hover:ring-blue-300 hover:bg-white shadow-md cursor-pointer"> -->
            <div v-for="libro in area.libros" :key="libro.id"
              class="relative rounded-xl overflow-hidden p-4 flex flex-col bg-white/90 backdrop-blur-md transition duration-300 hover:ring-2 hover:ring-blue-300 hover:bg-yellow-300 shadow-mds">
              <div class="flex-1">
                <div class="text-left line-clamp-2 text-lg font-bold text-gray-800">
                  {{ `${libro.titulo || "--"}` }}
                </div>
                <div class="text-left truncate text-gray-600 text-sm">
                  {{ `${libro.autor || "Sin autor"}` }}
                </div>
                <div class="text-left text-gray-600 text-sm">
                  {{
                    `${libro.editorial || "Sin editorial"}`
                  }}
                </div>
                <div class="text-left text-gray-600 text-sm mb-3">
                  {{ `${libro.anio || "Sin año"}` }}
                </div>
                <PdfPreview :src="libro.archivo_url" />
                <h5 class="text-left font-medium text-gray-800 text-lg line-clamp-4">
                  {{ libro.descripcion || "Sin descripción" }}
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

                <button @click.stop.prevent="
                  solicitarDescarga(
                    libro.id,
                    libro.archivo_nombre
                  )
                  " title="Descargar PDF"
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
        </div>
      </section>
    </div>

    <DescargarLibroModal ref="modalRef" />

    <!-- FOOTER -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import PdfPreview from "@/Components/PdfPreview.vue";
import Header from "@/Components/Header.vue";
import Footer from "@/Components/Footer.vue";
import DescargarLibroModal from "@/Components/DescargarLibroModal.vue";

const modalRef = ref(null);
function solicitarLibro(libroId, libroArchivoNombre) {
  // modalRef.value.abrir(libroId, libroArchivoNombre)
  modalRef.value.descargarLibro(libroId, libroArchivoNombre);
}

/**
 * Función que ahora solo se encarga de llamar a la descarga a través del modal.
 */
function solicitarDescarga(libroId, libroArchivoNombre) {
  // Esto es lo que hacía tu función original, ahora con un nombre más claro
  modalRef.value.descargarLibro(libroId, libroArchivoNombre);
}

/**
 * Función que abre el PDF en una nueva pestaña para visualización.
 * Requiere que el campo `archivo_url` del libro sea la URL accesible del PDF.
 */
function visualizarLibro(libro) {
  if (libro.archivo_url) {
    // Abre la URL en una nueva pestaña. El navegador se encargará de renderizar el PDF.
    window.open(libro.archivo_url, "_blank");
  } else {
    alert("No se encontró la URL del archivo para visualizar.");
  }
}

const { agrupados, query, areas, areaId } = defineProps({
  agrupados: {
    type: Array,
    default: () => [],
  },
  query: {
    type: String,
    default: "",
  },
  areas: {
    type: Array,
    default: () => [],
  },
  areaId: {
    type: [String, Number],
    default: "",
  },
});

const busqueda = ref("");
const areaBusquedaId = ref("");

function buscar() {
  router.get("/libros", {
    busqueda: busqueda.value ? busqueda.value.trim() : "",
    areaId: areaBusquedaId.value ?? "",
  });
}

onMounted(() => {
  areaBusquedaId.value = areaId || "";
  busqueda.value = query || "";
});

function limpiar() {
  busqueda.value = "";
  areaBusquedaId.value = "";
  buscar();
}
</script>
