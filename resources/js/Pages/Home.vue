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


      <!-- AREAS -->
      <section class="py-12 px-4 bg-blue">
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
        </div>
      </section>

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
import { obtenerNombreImagenArea } from '/resources/js/areaImages.js'


defineProps({
  areas: Array
});
const busqueda = ref('')

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
