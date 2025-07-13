<template>
  <header class="bg-primary text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">
        <Link href="/home">Biblioteca Digital</Link>
      </h1>
      <nav class="space-x-4">
        <Link href="/home" class="hover:underline" :class="{ 'font-bold underline': $page.url.startsWith('/home') }">
        Inicio</Link>

        <Link href="/libros" class="hover:underline"
          :class="{ 'font-bold underline': $page.url.startsWith('/libros') }">Libros</Link>

        <Link href="/login" class="hover:underline" :class="{ 'font-bold underline': $page.url.startsWith('/login') }">
        Mi cuenta</Link>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

// usePage te da acceso a $page.url para comparar la ruta actual
</script>
