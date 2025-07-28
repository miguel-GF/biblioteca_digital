<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <Header />

    <div class="bg-fixed bg-cover bg-center" style="background-image: url('/images/bgbiblioteca.jpg');">

      <!-- HERO -->
      <section class="bg-secondary py-16 px-4 text-center">
        <div class="max-w-2xl mx-auto">
          <h2 class="text-4xl font-bold mb-4 text-white/90 text-neutral">Bienvenido a la Biblioteca Digital</h2>
          <p class="text-lg text-white mb-6">
            Encuentra y consulta libros digitales desde cualquier lugar con tu matrícula escolar.
          </p>
          <input v-model="busqueda" @keyup.enter="buscarPorInput()" type="text"
            placeholder="Buscar por Título / Autor / Editorial"
            class="w-full md:w-2/3 px-4 py-3 border rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-accent" />
        </div>
      </section>

      <!-- AREAS -->
      <section class="py-12 px-4 bg-blue">
        <div class="max-w-6xl mx-auto">
          <h3 class="text-2xl text-white/90 font-semibold mb-6 text-neutral">Áreas destacadas</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div v-for="area in areas" :key="area.id" @click="buscarPorArea(area.id)"
              class="rounded-xl overflow-hidden cursor-pointer text-center p-4 flex flex-col items-center backdrop-blur-md transition duration-300 transform hover:scale-105 hover:ring-2 hover:ring-blue-300"
              style="background-color: #F7F7F7;">
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
