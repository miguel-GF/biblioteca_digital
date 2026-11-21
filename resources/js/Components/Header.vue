<template>
  <header class="bg-primary text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
      <h1 class="text-xl sm:text-2xl font-bold text-center sm:text-left">
        <Link href="/home">Biblioteca Digital</Link>
      </h1>
      <nav class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm sm:text-base">
        <Link href="/home" class="hover:underline" :class="{ 'font-bold underline': $page.url.startsWith('/home') }">
        Inicio</Link>

        <Link href="/libros" class="hover:underline"
          :class="{ 'font-bold underline': $page.url.startsWith('/libros') && !$page.url.startsWith('/libros-sugeridos') }">Libros</Link>

        <Link href="/libros-sugeridos" class="hover:underline"
          :class="{ 'font-bold underline': $page.url.startsWith('/libros-sugeridos') }">Sugeridos</Link>

        <Link href="/login" class="hover:underline" :class="{ 'font-bold underline': $page.url.startsWith('/login') }">
        Mi cuenta</Link>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
