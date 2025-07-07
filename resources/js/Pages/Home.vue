<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <Header />

    <!-- HERO -->
    <section class="bg-secondary py-16 px-4 text-center">
      <div class="max-w-2xl mx-auto">
        <h2 class="text-4xl font-bold mb-4 text-neutral">Bienvenido a la Biblioteca Digital</h2>
        <p class="text-lg text-gray-700 mb-6">
          Encuentra y consulta libros digitales desde cualquier lugar con tu matrícula escolar.
        </p>
        <input
          v-model="busqueda"
          @keyup.enter="buscar"
          type="text"
          placeholder="Buscar por Título / Autor / Editorial"
          class="w-full md:w-2/3 px-4 py-3 border rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-accent"
        />
      </div>
    </section>

    <!-- AREAS -->
    <section class="py-12 px-4 bg-blue">
      <div class="max-w-6xl mx-auto">
        <h3 class="text-2xl font-semibold mb-6 text-neutral">Áreas destacadas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div
            v-for="area in areas"
            :key="area.id"
            class="bg-gray-100 p-4 rounded-lg shadow hover:shadow-md text-center cursor-pointer"
            @click="buscar(area.id)"
          >
            {{ area.area || '--' }}
          </div>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="mt-auto bg-neutral text-black py-6 text-center">
      <p>© 2025 Biblioteca Digital - Todos los derechos reservados</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';

defineProps({
  areas: Array
});
const busqueda = ref('')

function buscar(areaId = '') {
  router.get('/libros', {
    busqueda: busqueda.value ? busqueda.value.trim() : '',
    areaId,
  })
}
</script>
