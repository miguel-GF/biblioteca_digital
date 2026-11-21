<template>
  <AppLayout title="Sugeridos">
    <template #header>
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">Sugeridos</h2>
          <p class="text-sm text-gray-500">
            Selecciona los libros, ordénalos y ajusta su importancia. Los cambios no se guardan hasta que presiones
            <strong>Guardar cambios</strong>.
          </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <a href="/libros-sugeridos" target="_blank"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-50">
            Ver sugeridos en la biblioteca
          </a>
          <button type="button" @click="guardar" :disabled="!dirty || guardando"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white rounded-md text-sm font-semibold hover:bg-blue-900 disabled:opacity-50 disabled:cursor-not-allowed">
            {{ guardando ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </div>
    </template>

    <div class="py-8" :class="{ 'pb-32': dirty }">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

        <!-- Mensajes -->
        <p v-if="successMessage" class="text-green-700 bg-green-100 border border-green-400 p-2 rounded">
          {{ successMessage }}
        </p>
        <p v-if="errorMessage" class="text-red-700 bg-red-100 border border-red-400 p-2 rounded">
          {{ errorMessage }}
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

          <!-- BUSCAR / SELECCIONAR -->
          <section class="bg-white shadow rounded-xl p-4">
            <h3 class="text-lg font-semibold text-gray-800">Buscar libros</h3>
            <p class="text-sm text-gray-500">Busca y usa "Agregar" para pasarlos a la lista de sugeridos.</p>

            <form @submit.prevent="buscar" class="mt-4 flex flex-col sm:flex-row gap-2">
              <input v-model="search" type="text" placeholder="Buscar por título / autor / editorial"
                class="flex-1 px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" />
              <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-900">
                Buscar
              </button>
              <button v-if="busqueda" type="button" @click="limpiar"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Limpiar
              </button>
            </form>

            <div class="mt-4 divide-y divide-gray-100">
              <div v-for="libro in libros.data" :key="libro.id" class="flex items-center gap-3 py-3">
                <div class="min-w-0 flex-1">
                  <div class="font-semibold text-gray-800 line-clamp-2">{{ libro.titulo || '--' }}</div>
                  <div class="text-xs text-gray-500 truncate">
                    {{ libro.autor || 'Sin autor' }} · {{ libro.area || 'Sin área' }}
                  </div>
                </div>

                <span v-if="idsSeleccionados.has(libro.id)"
                  class="shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd" />
                  </svg>
                  Agregado
                </span>

                <button v-else type="button" @click="agregar(libro)"
                  class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-md bg-primary text-white text-sm font-semibold hover:bg-blue-900">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                      clip-rule="evenodd" />
                  </svg>
                  Agregar
                </button>
              </div>

              <p v-if="libros.data.length === 0" class="py-10 text-center text-gray-500">
                No se encontraron libros.
              </p>
            </div>

            <!-- Paginación -->
            <div v-if="libros.links.length > 3" class="mt-4 flex flex-wrap items-center justify-center gap-1">
              <template v-for="link in libros.links" :key="link.label">
                <button v-if="link.url" type="button" @click="irAPagina(link.url)"
                  class="px-3 py-1.5 rounded-md text-sm border"
                  :class="link.active ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                  v-html="link.label" />
                <span v-else class="px-3 py-1.5 rounded-md text-sm border bg-gray-100 text-gray-400"
                  v-html="link.label" />
              </template>
            </div>
          </section>

          <!-- SELECCIONADOS / ORDEN -->
          <section class="bg-white shadow rounded-xl p-4">
            <div class="flex items-center justify-between gap-3">
              <h3 class="text-lg font-semibold text-gray-800">
                Sugeridos
                <span class="ml-1 inline-flex items-center justify-center rounded-full bg-primary/10 text-primary text-xs font-bold px-2 py-0.5">
                  {{ seleccionados.length }}
                </span>
              </h3>
              <button v-if="seleccionados.length" type="button" @click="recalcular"
                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                    clip-rule="evenodd" />
                </svg>
                Recalcular
              </button>
            </div>
            <span class="block text-xs text-gray-500 mt-1">El inicio muestra los primeros 10</span>

            <p class="text-sm text-gray-500 mt-2">
              Arriba = mayor importancia. Ordena los libros y ajusta la importancia si lo necesitas.
            </p>

            <div v-if="seleccionados.length === 0" class="mt-8 py-10 text-center text-gray-500">
              <img src="/images/emptybookshelf.png" alt="Sin sugeridos"
                class="mx-auto w-28 h-28 object-contain mb-4 opacity-70" />
              Todavía no has agregado libros sugeridos.
            </div>

            <ul v-else class="mt-4 space-y-3">
              <li v-for="(libro, index) in seleccionados" :key="libro.id"
                class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                <div class="flex items-start gap-3">
                  <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary text-white text-sm font-bold">
                    {{ index + 1 }}
                  </span>
                  <div class="min-w-0 flex-1">
                    <div class="font-semibold text-gray-800 line-clamp-2">{{ libro.titulo || '--' }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ libro.autor || 'Sin autor' }}</div>
                  </div>
                  <button type="button" @click="quitar(libro)" title="Quitar de sugeridos"
                    class="shrink-0 p-1.5 rounded-full text-red-500 hover:bg-red-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>

                <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-3">
                  <div class="flex items-center gap-1">
                    <button type="button" @click="mover(index, -1)" :disabled="index === 0" title="Subir"
                      class="p-1.5 rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                    <button type="button" @click="mover(index, 1)" :disabled="index === seleccionados.length - 1"
                      title="Bajar"
                      class="p-1.5 rounded-md border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>

                  <label class="flex items-center gap-2 text-sm text-gray-700">
                    Importancia
                    <input type="number" min="1" max="100" v-model.number="libro.importancia"
                      @change="actualizarImportancia(libro)"
                      class="w-20 px-2 py-1 text-center border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                  </label>
                </div>
              </li>
            </ul>
          </section>

        </div>
      </div>
    </div>

    <!-- BARRA DE GUARDADO -->
    <div v-if="dirty"
      class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white shadow-[0_-4px_12px_rgba(0,0,0,0.08)]">
      <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <p class="text-sm text-gray-700">Tienes cambios sin guardar.</p>
        <div class="flex items-center justify-end gap-2">
          <button type="button" @click="descartar" :disabled="guardando"
            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-semibold hover:bg-gray-300 disabled:opacity-50">
            Descartar
          </button>
          <button type="button" @click="guardar" :disabled="guardando"
            class="px-4 py-2 bg-primary text-white rounded-md text-sm font-semibold hover:bg-blue-900 disabled:opacity-50">
            {{ guardando ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  libros: {
    type: Object,
    required: true,
  },
  sugeridos: {
    type: Array,
    default: () => [],
  },
  busqueda: {
    type: String,
    default: '',
  },
});

const page = usePage();
const search = ref(props.busqueda || '');
const seleccionados = ref(props.sugeridos.map((libro) => ({ ...libro })));
const guardando = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const idsSeleccionados = computed(() => new Set(seleccionados.value.map((libro) => libro.id)));

const dirty = computed(() => {
  if (seleccionados.value.length !== props.sugeridos.length) return true;

  const originales = new Map(props.sugeridos.map((libro) => [libro.id, Number(libro.importancia) || 0]));

  return seleccionados.value.some((libro) => originales.get(libro.id) !== (Number(libro.importancia) || 0));
});

watch(
  () => props.sugeridos,
  (sugeridos) => {
    seleccionados.value = sugeridos.map((libro) => ({ ...libro }));
  },
  { deep: true }
);

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      successMessage.value = flash.success;
      setTimeout(() => (successMessage.value = ''), 3500);
    }
    if (flash?.error) {
      errorMessage.value = flash.error;
      setTimeout(() => (errorMessage.value = ''), 3500);
    }
  },
  { immediate: true, deep: true }
);

function confirmarDescarte() {
  if (!dirty.value) return true;
  return window.confirm('Tienes cambios sin guardar. Si continúas se perderán. ¿Quieres continuar?');
}

function buscar() {
  if (!confirmarDescarte()) return;

  router.get(route('admin.libros.sugeridos'), {
    busqueda: search.value.trim(),
  }, { preserveScroll: true });
}

function limpiar() {
  if (!confirmarDescarte()) return;

  search.value = '';
  router.get(route('admin.libros.sugeridos'), {}, { preserveScroll: true });
}

function irAPagina(url) {
  if (!url || !confirmarDescarte()) return;
  router.visit(url, { preserveScroll: true });
}

function ordenarSeleccionados() {
  seleccionados.value = [...seleccionados.value].sort((a, b) => {
    const diferencia = (Number(b.importancia) || 0) - (Number(a.importancia) || 0);
    if (diferencia !== 0) return diferencia;
    return String(a.titulo || '').localeCompare(String(b.titulo || ''), 'es', { sensitivity: 'base' });
  });
}

function siguienteImportancia() {
  if (!seleccionados.value.length) return 100;

  const minimo = Math.min(...seleccionados.value.map((libro) => Number(libro.importancia) || 1));
  return Math.max(1, minimo - 1);
}

function agregar(libro) {
  if (idsSeleccionados.value.has(libro.id)) return;

  seleccionados.value.push({
    ...libro,
    importancia: siguienteImportancia(),
    sugerido: true,
  });
  ordenarSeleccionados();
}

function quitar(libro) {
  seleccionados.value = seleccionados.value.filter((item) => item.id !== libro.id);
}

function actualizarImportancia(libro) {
  libro.importancia = Math.min(100, Math.max(1, Number(libro.importancia) || 1));
  ordenarSeleccionados();
}

function reasignarImportancias(lista) {
  const base = Math.max(100, lista.length);

  return lista.map((libro, index) => ({
    ...libro,
    importancia: base - index,
  }));
}

function mover(index, delta) {
  const destino = index + delta;
  if (destino < 0 || destino >= seleccionados.value.length) return;

  const copia = [...seleccionados.value];
  [copia[index], copia[destino]] = [copia[destino], copia[index]];
  seleccionados.value = reasignarImportancias(copia);
}

function recalcular() {
  if (!seleccionados.value.length) return;
  seleccionados.value = reasignarImportancias(seleccionados.value);
}

function descartar() {
  seleccionados.value = props.sugeridos.map((libro) => ({ ...libro }));
}

function guardar() {
  if (!dirty.value || guardando.value) return;

  guardando.value = true;

  router.put(route('libro.sugeridos.guardar'), {
    sugeridos: seleccionados.value.map((libro) => ({
      id: libro.id,
      importancia: Number(libro.importancia) || 1,
    })),
  }, {
    preserveScroll: true,
    preserveState: true,
    onFinish: () => {
      guardando.value = false;
    },
  });
}
</script>
