<template>
  <div class="flex flex-col min-h-0">
    <div class="flex-1 min-h-0 overflow-y-auto pr-1">
      <p v-if="!sugeridos.length" class="text-sm text-gray-500 text-center py-6">
        Aún no hay libros sugeridos.
      </p>

      <template v-else>
        <p v-if="totalSugeridos > sugeridos.length" class="text-xs text-gray-500 mb-2">
          Mostrando los {{ sugeridos.length }} primeros de {{ totalSugeridos }}.
        </p>

        <ol class="divide-y divide-gray-100">
        <li v-for="(libro, index) in sugeridos" :key="libro.id">
          <button type="button" @click="abrirLibro(libro)" class="group flex w-full items-start gap-3 py-3 text-left">
            <span
              class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary">
              {{ index + 1 }}
            </span>
            <span class="min-w-0 flex-1">
              <span class="block text-sm font-semibold text-gray-800 line-clamp-2 group-hover:text-primary">
                {{ libro.titulo || '--' }}
              </span>
              <span class="mt-0.5 block text-xs text-gray-500 truncate">
                {{ libro.autor || 'Sin autor' }}
              </span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-300 group-hover:text-primary"
              viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z"
                clip-rule="evenodd" />
              <path fill-rule="evenodd"
                d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z"
                clip-rule="evenodd" />
            </svg>
          </button>
        </li>
        </ol>
      </template>
    </div>

    <Link v-if="sugeridos.length" href="/libros-sugeridos"
      class="mt-4 shrink-0 flex items-center justify-center gap-2 w-full px-4 py-2 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-blue-900 transition">
      Ver todos
      <span v-if="totalSugeridos" class="text-xs bg-white/20 rounded-full px-2 py-0.5">{{ totalSugeridos }}</span>
    </Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  sugeridos: {
    type: Array,
    default: () => [],
  },
  totalSugeridos: {
    type: Number,
    default: 0,
  },
});

function abrirLibro(libro) {
  if (libro?.archivo_url) {
    window.open(libro.archivo_url, '_blank');
  }
}
</script>
