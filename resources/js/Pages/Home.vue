<template>
  <div class="min-h-screen flex flex-col">
    <!-- HEADER -->
    <header class="bg-primary text-red shadow-md">
      <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Biblioteca Digital</h1>
        <nav class="space-x-4">
          <a href="#" class="hover:underline">Inicio</a>
          <a href="#" class="hover:underline">Categorías</a>
          <a href="/login" class="hover:underline">Mi cuenta</a>
        </nav>
      </div>
    </header>

    <!-- HERO -->
    <section class="bg-secondary py-16 px-4 text-center">
      <div class="max-w-2xl mx-auto">
        <h2 class="text-4xl font-bold mb-4 text-neutral">Bienvenido a la Biblioteca Digital</h2>
        <p class="text-lg text-gray-700 mb-6">
          Encuentra y consulta libros digitales desde cualquier lugar con tu matrícula escolar.
        </p>
        <input
          type="text"
          placeholder="Buscar libros..."
          class="w-full md:w-2/3 px-4 py-3 border rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-accent"
        />
      </div>
    </section>

    <!-- CATEGORÍAS -->
    <section class="py-12 px-4 bg-blue">
      <div class="max-w-6xl mx-auto">
        <h3 class="text-2xl font-semibold mb-6 text-neutral">Categorías destacadas</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div
            v-for="categoria in categorias"
            :key="categoria.id"
            class="bg-gray-100 p-4 rounded-lg shadow hover:shadow-md text-center"
          >
            {{ categoria.nombre }}
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
defineProps({
  categorias: Array
});
</script>
