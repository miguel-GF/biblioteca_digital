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
const error = ref('')
let libroId = null

function abrir(id) {
  libroId = id
  matricula.value = ''
  error.value = ''
  visible.value = true
}

function cerrar() {
  visible.value = false
}

async function aceptar() {
  error.value = ''
  if (!matricula.value) {
    error.value = 'Por favor ingresa la matrícula.'
    return
  }

  try {
    const response = await axios.get(`/descargar-libro/${libroId}`, {
      params: { matricula: matricula.value },
      responseType: 'blob', // 👈 Para manejar archivos
    })

    // Crear URL y descargar archivo
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `libro-${libroId}.pdf`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

    cerrar()
  } catch (err) {
    if (err.response && err.response.status === 400) {
      error.value = err.response.data.message || 'Matrícula inválida.'
    } else if (err.response && err.response.status === 404) {
      error.value = 'El archivo no fue encontrado.'
    } else {
      error.value = 'Ocurrió un error inesperado.'
    }
  }
}

// Exponemos el método abrir al padre
defineExpose({ abrir })
</script>
