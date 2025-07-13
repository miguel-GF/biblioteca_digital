<template>
  <div v-if="visible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Ingrese su matrícula</h2>
      <input v-model="matricula" type="number" placeholder="Matrícula"
        class="w-full px-4 py-2 border rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-primary" />
      <p v-if="error" class="text-sm text-red-600 mb-2">{{ error }}</p>
      <div class="flex justify-end space-x-2">
        <button @click="cerrar" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cerrar</button>
        <button @click="aceptar" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark">Aceptar</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue'
import axios from 'axios'

const visible = ref(false)
const matricula = ref('')
const tituloSeleccionado = ref('')
const error = ref('')
let libroId = null

function abrir(id, titulo) {
  libroId = id
  error.value = ''

  // ✅ Verificamos si ya hay cookie
  const cookie = document.cookie
    .split('; ')
    .find(row => row.startsWith('libro_sesion='));

  if (cookie) {
    // 👇 Si existe, descargamos directo
    const parsed = JSON.parse(decodeURIComponent(cookie.split('=')[1]));

    axios.get(`/descargar-libro/${id}`, {
      params: { matricula: parsed.matricula },
      responseType: 'blob',
    }).then(response => {
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', `${titulo}`)
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }).catch(err => {
      error.value = 'Error al descargar con sesión activa. Intenta de nuevo.'
      visible.value = true
    })

    return
  }

    // Si no hay cookie → mostrar modal
  tituloSeleccionado.value = titulo
  matricula.value = ''
  visible.value = true
}

function cerrar() {
  visible.value = false
}

async function aceptar() {
  error.value = ''
  const mat = String(matricula.value).trim()

  if (!mat) {
    error.value = 'Por favor ingresa la matrícula.'
    return
  }

  try {
    // Paso 1: validar matrícula
    const { data } = await axios.get('/validar-matricula', {
      params: { matricula: mat }
    })

    if (!data.existe) {
      error.value = 'Matrícula no existe.'
      return
    }

    // Paso 2: guardar cookie por 30 min
    document.cookie = `libro_sesion=${encodeURIComponent(JSON.stringify({
      id: data.id,
      matricula: mat
    }))}; path=/; max-age=1800`

    // Paso 3: descargar
    const file = await axios.get(`/descargar-libro/${libroId}`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([file.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${tituloSeleccionado.value}`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

    cerrar()
  } catch (err) {
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Error inesperado al validar matrícula o descargar archivo.'
    }
  }
}

// METODO GLOBAL QUE SE UTILZARA DESPUES
async function descargarLibro(libroId, nombreArchivo) {
  const file = await axios.get(`/descargar-libro/${libroId}`, {
      responseType: 'blob'
    })

    const url = window.URL.createObjectURL(new Blob([file.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `${nombreArchivo}`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
}

// Exponemos el método abrir al padre
defineExpose({ abrir, descargarLibro })
</script>
