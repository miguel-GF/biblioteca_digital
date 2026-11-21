<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <Header />

    <div class="bg-fixed bg-cover bg-center" style="background-image: url('/images/bgbiblioteca.jpg');">

      <!-- HERO -->
      <section class="relative bg-secondary py-16 px-4">
        <!-- LOGO fijo arriba a la izquierda -->
        <img src="/images/logo.png" alt="Logo"
          class="h-16 sm:h-16 md:h-20 lg:h-24 object-contain absolute top-1 left-1" />

        <!-- CONTENEDOR PARA EL TEXTO -->
        <div class="flex flex-col items-center justify-center text-center">
          <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 text-white/90 break-words">
            Bienvenido a la Biblioteca Digital
          </h2>
          <p class="text-base sm:text-lg text-white mb-6 max-w-xl">
            Encuentra y consulta libros digitales desde cualquier lugar con tu matrícula escolar.
          </p>

          <!-- INPUT -->
          <input v-model="busqueda" @keyup.enter="buscarPorInput()" type="text"
            placeholder="Buscar por Título / Autor / Editorial" class="w-full md:w-2/3 px-4 py-3 border rounded-xl shadow-sm 
             focus:outline-none focus:ring-2 focus:ring-accent" />
        </div>
      </section>


      <!-- AREAS DESTACADAS -->
      <section class="py-12 px-4 sm:px-6 lg:px-8 bg-blue">
        <div class="max-w-6xl mx-auto">
          <h3 class="text-2xl text-white/90 font-semibold mb-6 text-neutral">Áreas destacadas</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="area in areas" :key="area.id" @click="buscarPorArea(area.id)"
              class="rounded-xl overflow-hidden cursor-pointer text-center p-4 flex flex-col items-center backdrop-blur-md transition duration-300 transform hover:scale-105 hover:ring-2 hover:ring-blue-300 bg-yellow-100 hover:bg-yellow-300">
              <img :src="obtenerNombreImagenArea(area.area)" alt="Imagen del área"
                class="h-24 w-24 object-contain mb-4" />
              <div class="text-lg font-semibold text-gray-800">
                {{ area.area ? area.area.toUpperCase() : '--' }}
              </div>
            </div>
          </div>

          <!-- LIBROS SUGERIDOS (solo móvil/tablet, en escritorio es botón flotante) -->
          <aside class="mt-10 lg:hidden">
            <div class="rounded-2xl bg-white/95 shadow-xl overflow-hidden">
              <div class="bg-primary text-white px-5 py-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.366 2.446c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.075 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.274-3.958z" />
                </svg>
                <h3 class="text-lg font-semibold">Libros sugeridos</h3>
              </div>
              <div class="p-5">
                <LibrosSugeridosLista :sugeridos="sugeridos" :total-sugeridos="totalSugeridos" />
              </div>
            </div>
          </aside>
        </div>
      </section>

    </div>

    <!-- BOTÓN FLOTANTE DE SUGERIDOS (escritorio) -->
    <div class="hidden lg:block">
      <div v-if="mostrarSugeridos" class="fixed inset-0 z-30" @click="mostrarSugeridos = false"></div>

      <div class="fixed top-24 right-6 z-40 flex flex-col items-end gap-3">
        <button type="button" @click="mostrarSugeridos = !mostrarSugeridos"
          class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-primary text-white shadow-2xl hover:bg-blue-900 transition"
          :aria-expanded="mostrarSugeridos">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 20 20"
            fill="currentColor">
            <path
              d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.366 2.446c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.075 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.274-3.958z" />
          </svg>
          <span class="text-sm font-semibold">Sugeridos</span>
          <span v-if="totalSugeridos"
            class="inline-flex items-center justify-center rounded-full bg-white/20 px-2 py-0.5 text-xs font-bold">
            {{ totalSugeridos }}
          </span>
        </button>

        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="-translate-y-2 opacity-0"
          enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
          leave-from-class="translate-y-0 opacity-100" leave-to-class="-translate-y-2 opacity-0">
          <div v-if="mostrarSugeridos"
            class="w-96 max-h-[70vh] overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/10 flex flex-col">
            <div class="bg-primary text-white px-5 py-4 flex items-center justify-between shrink-0">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path
                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.539 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.366 2.446c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.075 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.274-3.958z" />
                </svg>
                <h3 class="text-lg font-semibold">Libros sugeridos</h3>
              </div>
              <button type="button" @click="mostrarSugeridos = false" title="Cerrar"
                class="p-1 rounded-full text-white/80 hover:bg-white/10 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </div>

            <div class="p-5 flex-1 min-h-0 flex flex-col">
              <LibrosSugeridosLista class="flex-1 min-h-0" :sugeridos="sugeridos" :total-sugeridos="totalSugeridos" />
            </div>
          </div>
        </Transition>
      </div>
    </div>

    <!-- FOOTER -->
    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import LibrosSugeridosLista from '@/Components/LibrosSugeridosLista.vue';
import { obtenerNombreImagenArea } from '/resources/js/areaImages.js'


defineProps({
  areas: {
    type: Array,
    default: () => [],
  },
  sugeridos: {
    type: Array,
    default: () => [],
  },
  totalSugeridos: {
    type: Number,
    default: 0,
  },
});
const busqueda = ref('')
const mostrarSugeridos = ref(false)

function buscarPorInput() {
  router.get('/libros', {
    busqueda: busqueda.value.trim(),
    areaId: '',
  })
}

function buscarPorArea(areaId) {
  router.get('/libros', {
    busqueda: '',
    areaId: areaId,
  })
}
</script>
